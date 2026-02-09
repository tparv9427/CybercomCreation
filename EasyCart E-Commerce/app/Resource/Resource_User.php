<?php

namespace EasyCart\Resource;

/**
 * Resource_User
 * 
 * Database configuration for users table.
 */
class Resource_User extends Resource_Abstract
{
    protected $tableName = 'users';
    protected $primaryKey = 'id';
    protected $columns = [
        'id',
        'email',
        'password',
        'name',
        'role',
        'is_active',
        'created_at'
    ];
}
