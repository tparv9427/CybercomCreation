<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SolrClient
{
    protected string $baseUrl;
    protected string $collection;

    public function __construct()
    {
        $host = config('solr.host', env('SOLR_HOST', 'solr'));
        $port = config('solr.port', env('SOLR_PORT', 8983));
        $this->collection = config('solr.collection', env('SOLR_COLLECTION', 'reports'));
        $this->baseUrl = "http://{$host}:{$port}/solr/{$this->collection}";
    }

    public function query(array $params): array
    {
        $response = Http::get("{$this->baseUrl}/select", $params);
        return $response->json();
    }

    public function getFields(): array
    {
        $response = Http::get("{$this->baseUrl}/schema/fields");
        return $response->json()['fields'] ?? [];
    }

    public function add(array $docs, bool $commit = true): array
    {
        $url = "{$this->baseUrl}/update" . ($commit ? '?commit=true' : '');
        $response = Http::post($url, $docs);
        return $response->json();
    }

    public function delete(string $query = '*:*', bool $commit = true): array
    {
        $url = "{$this->baseUrl}/update" . ($commit ? '?commit=true' : '');
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url, ['delete' => ['query' => $query]]);
        return $response->json();
    }

    public function facet(array $params): array
    {
        $params['facet'] = 'true';
        $params['rows'] = 0; // We only want facet counts
        $response = Http::get("{$this->baseUrl}/select", $params);
        return $response->json();
    }
}