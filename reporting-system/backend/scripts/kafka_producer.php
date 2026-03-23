<?php
/**
 * ⚠️  DEPRECATED — DO NOT USE IN PRODUCTION
 * ─────────────────────────────────────────────────────────────────────────────
 * This script has been replaced by the Laravel async pipeline:
 *
 *   NEW FLOW:
 *   POST /api/import/upload  →  ImportController
 *                            →  ProcessCsvBatch (Laravel Job, queued via Redis)
 *                            →  KafkaService::produce()
 *                            →  Kafka Topic: report_data_topic
 *                            →  kafka:consume (Artisan Daemon)
 *                            →  Solr
 *
 *   PRODUCTION COMMAND:
 *   curl -X POST http://localhost:9006/api/import/upload \
 *        -F 'csv_file=@your_file.csv'
 *
 *   OR from dashboard: http://localhost:5173
 *
 * This file is kept only as a reference / manual testing fallback.
 * ─────────────────────────────────────────────────────────────────────────────
 */


$input = $argv[1] ?? null;

if (!$input || !file_exists($input)) {
    die("ERROR: Please provide a valid CSV file or directory path.\nUsage: php kafka_producer.php /path/to/input\n");
}

$files = is_dir($input) ? glob("$input/*.csv") : [$input];

if (empty($files)) {
    die("ERROR: No CSV files found.\n");
}

$topic  = 'report_data_topic';
$broker = 'kafka:9092';

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', $broker);
$conf->set('socket.timeout.ms', '10000');
$conf->set('queue.buffering.max.ms', '1000');

$producer   = new RdKafka\Producer($conf);
$kafkaTopic = $producer->newTopic($topic);

function isForceString(string $field): bool
{
    $lower = strtolower($field);
    $keywords = [
        'sku','code','id','ref','num','number','name',
        'title','desc','description','label','tag','type',
        'category','model','part','item','upc','ean',
        'barcode','zip','postal','phone','email','url',
        'slug','handle','key','token'
    ];

    foreach ($keywords as $kw) {
        if (str_contains($lower, $kw)) return true;
    }
    return false;
}

function hasSolrSuffix(string $field): bool
{
    return preg_match('/_(s|i|f|b|dt|t|l)$/', $field) === 1;
}

function detectType(array $samples): string
{
    $nonEmpty = array_filter($samples, fn($v) => $v !== '' && $v !== null);
    if (empty($nonEmpty)) return 's';

    $isInt   = true;
    $isFloat = true;
    $isBool  = true;
    $isDate  = true;

    foreach ($nonEmpty as $val) {
        $val = trim($val);
        if (!preg_match('/^-?\d+$/', $val))                                   $isInt   = false;
        if (!is_numeric(str_replace(',', '', $val)))                           $isFloat = false;
        if (!in_array(strtolower($val), ['true','false','1','0','yes','no']))  $isBool  = false;
        if (strtotime($val) === false)                                         $isDate  = false;
    }

    if ($isBool)  return 'b';
    if ($isInt)   return 'i';
    if ($isFloat) return 'f';
    if ($isDate)  return 'dt';
    return 's';
}

foreach ($files as $csvFile) {
    $fileId = pathinfo($csvFile, PATHINFO_FILENAME);
    echo "=== Processing: $fileId ===\n";
    echo "Pass 1: Sampling rows for type detection...\n";

    $file        = fopen($csvFile, 'r');
    $headers     = fgetcsv($file);
    if (!$headers) {
        fclose($file);
        continue;
    }
    $colCount    = count($headers);
    $samples     = array_fill(0, $colCount, []);
    $totalRows   = 0;
    $sampleLimit = 1000;

    while ($row = fgetcsv($file)) {
        foreach ($row as $i => $val) {
            if (isset($headers[$i]) && count($samples[$i]) < $sampleLimit) {
                $samples[$i][] = $val;
            }
        }
        $totalRows++;
    }

    fclose($file);

    echo "Total rows scanned: $totalRows\n";
    echo "Building field map...\n";

    $fieldMap = [];
    foreach ($headers as $i => $col) {
        if ($col === 'id') {
            $fieldMap[$col] = 'id';
        } elseif (hasSolrSuffix($col)) {
            $fieldMap[$col] = $col;
        } elseif (isForceString($col)) {
            $fieldMap[$col] = $col . '_s';
        } else {
            $type = detectType($samples[$i] ?? []);
            $fieldMap[$col] = $col . '_' . $type;
        }
    }

    echo "Pass 2: Producing to Kafka...\n";

    $file       = fopen($csvFile, 'r');
    fgetcsv($file); // Skip header

    $processed = 0;
    $produced  = 0;
    $skipped   = 0;

    while ($row = fgetcsv($file)) {

        if (count($row) !== $colCount) {
            $processed++;
            $skipped++;
            continue;
        }

        $raw = array_combine($headers, $row);
        $doc = [];

        $doc['id'] = $fileId . '_' . ($processed + 1);
        $doc['source_s'] = $fileId;

        foreach ($raw as $col => $value) {
            if ($col === 'id') continue;

            $newKey = $fieldMap[$col] ?? ($col . '_s');
            $value  = trim($value);

            if ($value === '') continue;

            if (str_ends_with($newKey, '_i'))       $doc[$newKey] = (int) $value;
            elseif (str_ends_with($newKey, '_f'))   $doc[$newKey] = (float) str_replace(',', '', $value);
            elseif (str_ends_with($newKey, '_b'))   $doc[$newKey] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            elseif (str_ends_with($newKey, '_dt'))  $doc[$newKey] = date('Y-m-d\TH:i:s\Z', strtotime($value));
            else                                     $doc[$newKey] = (string) $value;
        }

        $kafkaTopic->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            json_encode($doc),
            $doc['id']
        );

        $produced++;
        $processed++;

        if ($produced % 1000 === 0) {
            $producer->poll(0);
            echo "Produced $produced messages for $fileId...\n";
        }
    }

    fclose($file);
    echo "Finished $fileId. Total produced: $produced\n\n";
}

echo "\nFlushing all remaining messages...\n";
$result = $producer->flush(60000);

if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
    echo "Done.\n";
} else {
    echo "WARNING: Some messages not delivered. Code: $result\n";
}