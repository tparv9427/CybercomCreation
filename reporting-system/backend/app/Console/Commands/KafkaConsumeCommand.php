<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class KafkaConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Run via: php artisan kafka:consume
     *          php artisan kafka:consume --batch-size=1000
     */
    protected $signature = 'kafka:consume
                            {--batch-size=500 : Number of messages to collect before indexing into Solr}
                            {--idle-timeout=5 : Seconds of inactivity before flushing the current batch}';

    protected $description = 'Long-running Kafka consumer daemon. Reads from report_data_topic and indexes batches into Solr.';

    // Kafka config
    private string $broker;
    private string $topic;
    private string $groupId;

    // Solr config
    private string $solrUrl;

    // DLQ config
    private string $dlqTopic;

    public function handle(): int
    {
        $this->broker   = env('KAFKA_BROKER', 'kafka:9092');
        $this->topic    = env('KAFKA_TOPIC',  'report_data_topic');
        $this->groupId  = 'reporting-consumer-group';
        $this->dlqTopic = $this->topic . '_dlq';
        $this->solrUrl  = sprintf(
            'http://%s:%s/solr/%s/update',
            env('SOLR_HOST', 'solr'),
            env('SOLR_PORT', '8983'),
            env('SOLR_COLLECTION', 'reports')
        );

        $batchSize   = (int) $this->option('batch-size');
        $idleTimeout = (int) $this->option('idle-timeout');

        $this->info("=== Kafka Consumer Started ===");
        $this->info("Broker : {$this->broker}");
        $this->info("Topic  : {$this->topic}");
        $this->info("Group  : {$this->groupId}");
        $this->info("Batch  : {$batchSize} messages");
        $this->line('');

        Log::info('KafkaConsumer: Starting daemon.', [
            'topic'      => $this->topic,
            'batch_size' => $batchSize,
        ]);

        // ── Build Kafka Consumer ──────────────────────────────────────────────
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', $this->broker);
        $conf->set('group.id',             $this->groupId);
        $conf->set('enable.auto.commit',   'true');
        $conf->set('auto.offset.reset',    'earliest');
        $conf->set('socket.timeout.ms',    '60000');

        $consumer = new \RdKafka\KafkaConsumer($conf);
        $consumer->subscribe([$this->topic]);

        // ── Build DLQ Producer ────────────────────────────────────────────────
        $dlqConf = new \RdKafka\Conf();
        $dlqConf->set('metadata.broker.list', $this->broker);
        $dlqProducer  = new \RdKafka\Producer($dlqConf);
        $dlqKafkaTopic = $dlqProducer->newTopic($this->dlqTopic);

        // ── Main Consume Loop ─────────────────────────────────────────────────
        $batch      = [];
        $total      = 0;
        $batchCount = 0;
        $lastMsgTs  = time();

        $this->info("Consuming messages...");

        while (true) {
            $message = $consumer->consume(1000); // poll timeout 1s

            switch ($message->err) {

                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $doc = json_decode($message->payload, true);

                    if ($doc) {
                        $batch[]   = $doc;
                        $total++;
                        $lastMsgTs = time();

                        // When batch is full → flush to Solr
                        if (count($batch) >= $batchSize) {
                            $batchCount += $this->flushToSolr($batch, $dlqKafkaTopic, $batchCount);
                            $this->line("Indexed batch {$batchCount} — Total: {$total}");
                            $batch = [];
                        }
                    }
                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // No new messages — wait for idle timeout then flush remaining
                    if ((time() - $lastMsgTs) > $idleTimeout && !empty($batch)) {
                        $batchCount += $this->flushToSolr($batch, $dlqKafkaTopic, $batchCount);
                        $this->line("Indexed idle batch {$batchCount} — Total: {$total}");
                        $batch = [];
                    }
                    break;

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    // End of current messages, keep running (daemon mode)
                    break;

                default:
                    $this->error("Kafka error: " . $message->errstr());
                    Log::error('KafkaConsumer error: ' . $message->errstr());
                    break;
            }
        }

        // This point is only reached if the loop is intentionally broken (future SIGTERM handling)
        $consumer->close();
        $this->finalSolrCommit();

        $this->info("\nDone. Total indexed: {$total} documents in {$batchCount} batches.");
        Log::info('KafkaConsumer: Stopped.', ['total' => $total, 'batches' => $batchCount]);

        return Command::SUCCESS;
    }

    /**
     * Send a batch of documents to Solr.
     * On failure, routes failed docs to the DLQ topic.
     * Returns 1 on success, 0 on failure.
     */
    private function flushToSolr(array $docs, $dlqKafkaTopic, int $currentBatch): int
    {
        $url  = $this->solrUrl . '?commitWithin=5000';
        $json = json_encode($docs);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200 || ($result['responseHeader']['status'] ?? 1) !== 0) {
            $this->error("Solr indexing failed (HTTP {$httpCode}): {$response}");
            Log::error('KafkaConsumer: Solr batch failed.', [
                'batch'     => $currentBatch + 1,
                'http_code' => $httpCode,
                'response'  => $response,
            ]);

            // Route failed batch to Dead Letter Queue
            foreach ($docs as $doc) {
                $dlqKafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($doc));
            }
            $dlqKafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, "BATCH_FAILED: HTTP {$httpCode} — {$response}");

            $this->warn("Failed batch sent to DLQ: {$this->dlqTopic}");
            return 0;
        }

        return 1;
    }

    /**
     * Issue a final hard commit to Solr to ensure all data is visible.
     */
    private function finalSolrCommit(): void
    {
        $commitUrl = sprintf(
            'http://%s:%s/solr/%s/update?commit=true',
            env('SOLR_HOST', 'solr'),
            env('SOLR_PORT', '8983'),
            env('SOLR_COLLECTION', 'reports')
        );

        $ch = curl_init($commitUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => '{}',
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        curl_close($ch);

        $this->info("Final Solr commit issued.");
        Log::info('KafkaConsumer: Final Solr commit issued.');
    }
}
