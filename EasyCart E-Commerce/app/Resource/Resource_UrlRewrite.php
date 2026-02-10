<?php

namespace EasyCart\Resource;

/**
 * Resource_UrlRewrite — Data layer for SEO URL rewrites
 */
class Resource_UrlRewrite extends Resource_Abstract
{
    protected $table = 'url_rewrite';
    protected $primaryKey = 'url_rewrite_id';
    protected $columns = [
        'url_rewrite_id',
        'request_path',
        'target_path',
        'entity_id',
        'entity_type',
        'redirect_type',
        'metadata',
        'created_at',
        'updated_at'
    ];

    /**
     * Find rewrite by request path
     */
    public function findByRequestPath(string $path): ?array
    {
        return \EasyCart\Database\QueryBuilder::select($this->table)
            ->where('request_path', '=', $path)
            ->fetchOne();
    }

    /**
     * Find rewrite by entity
     */
    public function findByEntity(string $type, int $id): ?array
    {
        return \EasyCart\Database\QueryBuilder::select($this->table)
            ->where('entity_type', '=', $type)
            ->where('entity_id', '=', $id)
            ->where('redirect_type', '=', 0) // Primary rewrite
            ->fetchOne();
    }
}
