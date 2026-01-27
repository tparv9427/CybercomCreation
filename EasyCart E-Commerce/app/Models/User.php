<?php

namespace EasyCart\Models;

/**
 * User Model
 * 
 * Represents a user/customer entity.
 */
class User
{
    public $id;
    public $email;
    public $password;
    public $name;
    public $created_at;

    /**
     * Verify password against stored hash
     * 
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Convert model to array
     * 
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'name' => $this->name,
            'created_at' => $this->created_at
        ];
    }

    /**
     * Create model from array
     * 
     * @param array $data
     * @return User
     */
    public static function fromArray($data)
    {
        $user = new self();
        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }
        return $user;
    }
}
