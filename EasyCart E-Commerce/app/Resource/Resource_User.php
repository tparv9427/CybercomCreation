<?php

namespace EasyCart\Resource;

/**
 * Resource_User — User DB Configuration
 * 
 * Table: customer_entity
 * Primary Key: entity_id
 */
class Resource_User extends Resource_Abstract
{
    protected $table = 'customer_entity';
    protected $primaryKey = 'entity_id';
    protected $columns = [
        'entity_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'created_at',
        'updated_at'
    ];

    /**
     * Find user by email address
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return \EasyCart\Database\QueryBuilder::select($this->table, ['*'])
            ->where('email', '=', $email)
            ->fetchOne();
    }
}
