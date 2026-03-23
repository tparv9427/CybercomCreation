<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvBatch;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;

class ImportController extends Controller
{
    private const CHUNK_SIZE   = 1000;
    private const MAX_FILE_MB  = 200;

    private static array $forceStringKeywords = [
        'sku','code','id','ref','num','number',
        'label','tag','type','category','model','part','item','upc','ean',
        'barcode','zip','postal','phone','email','url',
        'slug','handle','key','token'
    ];

    private static array $forceTextKeywords = [
        'desc','description','text','content','comment','feedback','review','about','title','name'
    ];

    public function upload(Request $request): JsonResponse
    {
        // 1. Validate the incoming file
        $request->validate([
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:' . (self::MAX_FILE_MB * 1024),
            ],
        ]);

        $file   = $request->file('csv_file');
        $fileId = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path   = $file->getRealPath();

        // 2. Build the field map by sampling the first 1000 rows (Pass 1)
        $fieldMap = $this->buildFieldMap($path);

        if (empty($fieldMap)) {
            return response()->json(['error' => 'Could not parse CSV headers.'], 422);
        }

        // 3. Dispatch batches asynchronously (Pass 2) using LazyCollection
        //    LazyCollection reads one line at a time - never loads full file into memory.
        $totalJobs = 0;

        LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');
            fgetcsv($handle); // skip header row
            while (($row = fgetcsv($handle)) !== false) {
                yield $row;
            }
            fclose($handle);
        })
        ->chunk(self::CHUNK_SIZE)
        ->each(function ($chunk) use ($fieldMap, $fileId, &$totalJobs) {
            // Re-key each row array using original header names
            $headers = array_keys($fieldMap);
            $rows    = $chunk->map(fn($row) => array_combine($headers, array_pad($row, count($headers), '')))->all();

            ProcessCsvBatch::dispatch($rows, $fieldMap, $fileId);
            $totalJobs++;
        });

        AuditLog::log('import_csv', ['file' => $fileId, 'jobs' => $totalJobs]);

        return response()->json([
            'message'    => "Import started successfully.",
            'file'       => $fileId,
            'jobs_queued' => $totalJobs,
            'chunk_size' => self::CHUNK_SIZE,
        ]);
    }

    /**
     * Pass 1: Sample rows to detect column types and build the field map.
     */
    private function buildFieldMap(string $path): array
    {
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            return [];
        }

        $colCount = count($headers);
        $samples  = array_fill(0, $colCount, []);
        $limit    = 1000;

        while (($row = fgetcsv($handle)) !== false && $limit-- > 0) {
            foreach ($row as $i => $val) {
                if (isset($headers[$i]) && count($samples[$i]) < 1000) {
                    $samples[$i][] = $val;
                }
            }
        }
        fclose($handle);

        $fieldMap = [];
        foreach ($headers as $i => $col) {
            $colSamples = $samples[$i] ?? [];
            if ($col === 'id') {
                $fieldMap[$col] = 'id';
            } elseif ($this->hasSolrSuffix($col)) {
                $fieldMap[$col] = $col;
            } elseif ($this->isForceText($col, $colSamples)) {
                $fieldMap[$col] = $col . '_t';
            } elseif ($this->isForceString($col)) {
                $fieldMap[$col] = $col . '_s';
            } else {
                $type = $this->detectType($colSamples);
                $fieldMap[$col] = $col . '_' . $type;
            }
        }

        return $fieldMap;
    }

    private function isForceText(string $field, array $samples): bool
    {
        $lower = strtolower($field);
        foreach (self::$forceTextKeywords as $kw) {
            if (str_contains($lower, $kw)) return true;
        }

        // Auto-detect based on average character length (> 50 chars)
        $nonEmpty = array_filter($samples, fn($v) => $v !== '' && $v !== null);
        if (count($nonEmpty) > 0) {
            $avgLen = array_sum(array_map('strlen', $nonEmpty)) / count($nonEmpty);
            if ($avgLen > 50) return true;
        }

        return false;
    }

    private function isForceString(string $field): bool
    {
        $lower = strtolower($field);
        foreach (self::$forceStringKeywords as $kw) {
            if (str_contains($lower, $kw)) return true;
        }
        return false;
    }

    private function hasSolrSuffix(string $field): bool
    {
        return (bool) preg_match('/_(s|i|f|b|dt|t|l)$/', $field);
    }

    private function detectType(array $samples): string
    {
        $nonEmpty = array_filter($samples, fn($v) => $v !== '' && $v !== null);
        if (empty($nonEmpty)) return 's';

        $isInt = $isFloat = $isBool = $isDate = true;

        foreach ($nonEmpty as $val) {
            $val = trim($val);
            if (!preg_match('/^-?\d+$/', $val))                                  $isInt   = false;
            if (!is_numeric(str_replace(',', '', $val)))                          $isFloat = false;
            if (!in_array(strtolower($val), ['true','false','1','0','yes','no'])) $isBool  = false;
            if (strtotime($val) === false)                                        $isDate  = false;
        }

        if ($isBool)  return 'b';
        if ($isInt)   return 'i';
        if ($isFloat) return 'f';
        if ($isDate)  return 'dt';
        return 's';
    }
}
