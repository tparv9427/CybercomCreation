<?php

namespace EasyCart\Model;

/**
 * Model_User
 * 
 * User entity with business logic.
 */
class Model_User extends Model_Abstract
{
    public function getName(): string
    {
        return $this->getData('name') ?? '';
    }

    public function getEmail(): string
    {
        return $this->getData('email') ?? '';
    }

    public function isActive(): bool
    {
        return (bool) ($this->getData('is_active') ?? true);
    }

    public function getRole(): string
    {
        return $this->getData('role') ?? 'customer';
    }

    public function isAdmin(): bool
    {
        return $this->getRole() === 'admin';
    }

    public function verifyPassword(string $password): bool
    {
        $hash = $this->getData('password');
        return $hash && password_verify($password, $hash);
    }
}
