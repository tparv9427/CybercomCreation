<?php

$topic     = 'report_data_topic';
$broker    = 'kafka:9092';
$groupId   = 'reporting-consumer-group';
$solrUrl   = 'http://solr:8983/solr/reports/update';
$batchSize = 5000;

echo "Starting Kafka Consumer (Group Mode)\n";
echo "Broker: $broker\n";
echo "Topic:  $topic\n";
echo "Group:  $groupId\n\n";

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', $broker);
$conf->set('group.id', $groupId);
$conf->set('enable.auto.commit', 'true');
$conf->set('auto.offset.reset', 'earliest');
$conf->set('socket.timeout.ms', '60000');

$consumer = new RdKafka\KafkaConsumer($conf);
$consumer->subscribe([$topic]);

// Setup DLQ Producer
$dlqConf = new RdKafka\Conf();
$dlqConf->set('metadata.broker.list', $broker);
$dlqProducer = new RdKafka\Producer($dlqConf);
$dlqTopic = $dlqProducer->newTopic($topic . '_dlq');

function sendToSolr(array $docs, string $url, $dlqTopic = null): bool
{
    $json = json_encode($docs);

    $ch = curl_init($url . '?commitWithin=5000');
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
        echo "ERROR sending to Solr: $response\n";
        
        // Send to DLQ
        if ($dlqTopic) {
            foreach ($docs as $doc) {
                $dlqTopic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($doc));
            }
            $dlqTopic->produce(RD_KAFKA_PARTITION_UA, 0, "BATCH FAILED: $response");
            echo "Sent failed batch to DLQ.\n";
        }
        return false;
    }

    return true;
}

$batch      = [];
$total      = 0;
$batchCount = 0;
$lastMsgTs  = time();

echo "Consuming messages...\n\n";

while (true) {

    $message = $consumer->consume(1000);

    switch ($message->err) {

        case RD_KAFKA_RESP_ERR_NO_ERROR:

            $doc = json_decode($message->payload, true);

            if ($doc) {
                $batch[] = $doc;
                $total++;
                $lastMsgTs = time();

                if (count($batch) >= $batchSize) {

                    if (sendToSolr($batch, $solrUrl, $dlqTopic)) {
                        $batchCount++;
                        echo "Indexed batch $batchCount — Total: $total\n";
                    }

                    $batch = [];
                }
            }
            break;

        case RD_KAFKA_RESP_ERR__TIMED_OUT:

            // No new messages for a while
            if (time() - $lastMsgTs > 5) {

                if (!empty($batch)) {
                    if (sendToSolr($batch, $solrUrl, $dlqTopic)) {
                        $batchCount++;
                        echo "Indexed final batch — Total: $total\n";
                    }
                    $batch = [];
                }

                echo "\nNo more messages. Exiting.\n";
                goto done;
            }

            break;

        default:
            echo "Error: " . $message->errstr() . "\n";
            break;
    }
}

done:

$consumer->close();

// Final hard commit
$ch = curl_init('http://solr:8983/solr/reports/update?commit=true');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '{}',
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
curl_exec($ch);
curl_close($ch);

echo "\nDone!\n";
echo "Total indexed: $total documents in $batchCount batches.\n";
echo "Verify: http://localhost:9007/solr/reports/select?q=*:*&rows=0\n";