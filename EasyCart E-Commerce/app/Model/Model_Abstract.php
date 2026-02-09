<?php

namespace EasyCart\Model;

/**
 * Model_Abstract
 * 
 * Base class for all Model entities.
 * Models contain entity-specific business logic.
 */
abstract class Model_Abstract
{
    /**
     * Entity data array
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     * @param array $data Initial data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get a data value by key
     * @param string $key
     * @return mixed|null
     */
    public function getData($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set a data value by key
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get all data as array
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Populate model from array
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get entity ID
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id') ?? $this->getData('entity_id');
    }
}
