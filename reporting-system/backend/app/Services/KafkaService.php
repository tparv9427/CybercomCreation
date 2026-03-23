<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\Topic;

class KafkaService
{
    private ?Producer $producer = null;
    private ?Topic    $topic    = null;

    private string $broker;
    private string $topicName;

    public function __construct()
    {
        $this->broker    = env('KAFKA_BROKER', 'kafka:9092');
        $this->topicName = env('KAFKA_TOPIC',  'report_data_topic');
        $this->initialize();
    }

    private function initialize(): void
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->broker);
        $conf->set('socket.timeout.ms',    '10000');
        $conf->set('queue.buffering.max.ms', '1000');

        $this->producer = new Producer($conf);
        $this->topic    = $this->producer->newTopic($this->topicName);
    }

    /**
     * Produce a single document to Kafka.
     */
    public function produce(array $data, ?string $key = null): void
    {
        if (!$this->topic) {
            $this->initialize();
        }

        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($data), $key);
        $this->producer->poll(0);
    }

    /**
     * Flush all buffered messages to Kafka.
     * Throws on error so the job can retry.
     */
    public function flush(int $timeoutMs = 15000): void
    {
        if (!$this->producer) {
            return;
        }

        $result = $this->producer->flush($timeoutMs);

        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new Exception("Kafka flush failed. Error code: {$result}");
        }

        Log::debug('KafkaService: Flush successful.');
    }
}
