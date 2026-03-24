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
        protected string $fileId,
        protected ?string $tenantId = null
    ) {}

    public function handle(KafkaService $kafka): void
    {
        $produced = 0;

        foreach ($this->rows as $index => $row) {
            $doc = $this->transform($row, $index);
            if (!empty($doc)) {
                $kafka->produce($doc, $doc['id'], $this->fileId);
                $produced++;
            }
        }

        $kafka->flush();

        Log::info("ProcessCsvBatch: Produced {$produced} messages for file [{$this->fileId}] (Tenant: {$this->tenantId}).");
    }

    /**
     * Transform a raw CSV row into a Solr-ready document.
     */
    private function transform(array $row, int $index): array
    {
        $doc = [
            'id'          => $this->generateId($row, $index),
            'source_s'    => ['set' => $this->fileId],
            'tenant_id_s' => ['set' => $this->tenantId ?? 'global'],
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

            if (str_ends_with($targetKey, '_i'))       $doc[$targetKey] = ['set' => (int) $value];
            elseif (str_ends_with($targetKey, '_f'))   $doc[$targetKey] = ['set' => (float) str_replace(',', '', $value)];
            elseif (str_ends_with($targetKey, '_b'))   $doc[$targetKey] = ['set' => filter_var($value, FILTER_VALIDATE_BOOLEAN)];
            elseif (str_ends_with($targetKey, '_dt'))  $doc[$targetKey] = ['set' => date('Y-m-d\TH:i:s\Z', strtotime($value))];
            else                                        $doc[$targetKey] = ['set' => (string) $value];
        }

        return $doc;
    }

    /**
     * Generate a deterministic ID based on row content or primary key.
     */
    private function generateId(array $row, int $index): string
    {
        $pkCandidates = ['sku', 'id', 'part_number', 'upc', 'uuid', 'vin'];
        
        foreach ($row as $col => $val) {
            $cleaned = strtolower(str_replace([' ', '_', '-'], '', $col));
            if (in_array($cleaned, $pkCandidates) && trim($val) !== '') {
                // If we found a unique identifier, hash it.
                return md5(trim($val));
            }
        }

        // If no primary key is found, hash the whole row.
        // We include the fileId if you want to keep data isolated by file, 
        // OR exclude it if you want to deduplicate identical rows across multiple files.
        // Defaulting to cross-file deduplication for cleaner data.
        return md5(json_encode($row));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessCsvBatch FAILED for file [{$this->fileId}]: " . $exception->getMessage());
    }
}
