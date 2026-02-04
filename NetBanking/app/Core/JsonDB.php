<?php

namespace App\Core;

class JsonDB
{
    protected $dbPath;

    public function __construct()
    {
        $this->dbPath = DB_PATH;
        if (!is_dir($this->dbPath)) {
            mkdir($this->dbPath, 0777, true);
        }
    }

    protected function getFilePath($table)
    {
        return $this->dbPath . '/' . $table . '.json';
    }

    public function custom_query($table)
    {
        $file = $this->getFilePath($table);
        if (!file_exists($file)) {
            return [];
        }
        $json = file_get_contents($file);
        return json_decode($json, true) ?? [];
    }

    public function insert($table, $data)
    {
        $currentData = $this->custom_query($table);
        // Auto-increment ID
        if (empty($data['id'])) {
            $lastItem = end($currentData);
            $data['id'] = $lastItem ? $lastItem['id'] + 1 : 1;
        }

        $currentData[] = $data;
        return $this->save($table, $currentData);
    }

    public function update($table, $id, $data)
    {
        $currentData = $this->custom_query($table);
        foreach ($currentData as $key => $row) {
            if ($row['id'] == $id) {
                // Merge existing data with new data
                $currentData[$key] = array_merge($row, $data);
                return $this->save($table, $currentData);
            }
        }
        return false;
    }

    public function delete($table, $id)
    {
        $currentData = $this->custom_query($table);
        $newData = array_filter($currentData, function ($row) use ($id) {
            return $row['id'] != $id;
        });

        // Re-index array
        $newData = array_values($newData);
        return $this->save($table, $newData);
    }

    public function find($table, $id)
    {
        $data = $this->custom_query($table);
        foreach ($data as $row) {
            if ($row['id'] == $id) {
                return $row;
            }
        }
        return null;
    }

    public function where($table, $key, $value)
    {
        $data = $this->custom_query($table);
        return array_filter($data, function ($row) use ($key, $value) {
            return isset($row[$key]) && $row[$key] == $value;
        });
    }

    protected function save($table, $data)
    {
        $file = $this->getFilePath($table);
        // Pretty print for readability/debug
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
}
