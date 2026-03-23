<?php

namespace App\Jobs;

use App\Services\KafkaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCsvBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times to retry this job on failure.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying.
     */
    public int $backoff = 30;

    public function __construct(
        protected array $rows,
        protected array $fieldMap,
        protected string $fileId
    ) {}

    public function handle(KafkaService $kafka): void
    {
        $produced = 0;

        foreach ($this->rows as $index => $row) {
            $doc = $this->transform($row, $index);
            if (!empty($doc)) {
                $kafka->produce($doc, $doc['id']);
                $produced++;
            }
        }

        $kafka->flush();

        Log::info("ProcessCsvBatch: Produced {$produced} messages for file [{$this->fileId}].");
    }

    /**
     * Transform a raw CSV row into a Solr-ready document.
     */
    private function transform(array $row, int $index): array
    {
        $doc = [
            'id'        => $this->fileId . '_' . ($index + 1) . '_' . uniqid(),
            'source_s'  => $this->fileId,
        ];

        foreach ($row as $col => $value) {
            if ($col === 'id' || !isset($this->fieldMap[$col])) {
                continue;
            }

            $targetKey = $this->fieldMap[$col];
            $value     = trim($value);

            if ($value === '') {
                continue;
            }

            if (str_ends_with($targetKey, '_i'))       $doc[$targetKey] = (int) $value;
            elseif (str_ends_with($targetKey, '_f'))   $doc[$targetKey] = (float) str_replace(',', '', $value);
            elseif (str_ends_with($targetKey, '_b'))   $doc[$targetKey] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            elseif (str_ends_with($targetKey, '_dt'))  $doc[$targetKey] = date('Y-m-d\TH:i:s\Z', strtotime($value));
            else                                        $doc[$targetKey] = (string) $value;
        }

        return $doc;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessCsvBatch FAILED for file [{$this->fileId}]: " . $exception->getMessage());
    }
}
