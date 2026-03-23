<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\SolrClient;

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
    private SolrClient $solr;

    // DLQ config
    private string $dlqTopic;

    public function handle(): int
    {
        $this->broker   = env('KAFKA_BROKER', 'kafka:9092');
        $this->topic    = env('KAFKA_TOPIC',  'report_data_topic');
        $this->groupId  = 'reporting-consumer-group';
        $this->dlqTopic = $this->topic . '_dlq';
        $this->solr = app(SolrClient::class);

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
        $conf->set('enable.auto.commit',   'false');
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
                            $success = $this->flushToSolr($batch, $dlqKafkaTopic, $batchCount);
                            if ($success) {
                                $consumer->commitAsync();
                                $batchCount++;
                            }
                            $this->line("Indexed batch {$batchCount} — Total: {$total}");
                            $batch = [];
                        }
                    }
                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // No new messages — wait for idle timeout then flush remaining
                    if ((time() - $lastMsgTs) > $idleTimeout && !empty($batch)) {
                        $success = $this->flushToSolr($batch, $dlqKafkaTopic, $batchCount);
                        if ($success) {
                            $consumer->commitAsync(); // Commit all processed up to now
                            $batchCount++;
                        }
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
        try {
            // Using SolrClient with commitWithin to ensure visibility within 5s
            $result = $this->solr->add($docs, false, 5000);

            if (($result['responseHeader']['status'] ?? 1) !== 0) {
                throw new \Exception("Solr indexing failed: " . json_encode($result));
            }

            return 1;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            $this->error("Solr indexing failed: " . $errorMsg);
            Log::error('KafkaConsumer: Solr batch failed.', [
                'batch' => $currentBatch + 1,
                'error' => $errorMsg,
            ]);

            // Route failed batch to Dead Letter Queue
            foreach ($docs as $doc) {
                $dlqKafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($doc));
            }
            $dlqProducer = $dlqKafkaTopic->getProducer(); // Keep reference to producer if needed
            $dlqKafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, "BATCH_FAILED: " . $errorMsg);

            $this->warn("Failed batch sent to DLQ: {$this->dlqTopic}");
            return 0;
        }
    }

    /**
     * Issue a final hard commit to Solr to ensure all data is visible.
     */
    private function finalSolrCommit(): void
    {
        $this->solr->add([], true); // Send dummy data or just commit=true
        $this->info("Final Solr commit issued.");
        Log::info('KafkaConsumer: Final Solr commit issued.');
    }
}
