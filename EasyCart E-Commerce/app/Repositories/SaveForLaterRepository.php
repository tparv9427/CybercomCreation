<?php

namespace EasyCart\Repositories;

/**
 * SaveForLaterRepository
 * 
 * Handles storage of items saved for later
 */
class SaveForLaterRepository
{
    private $saveFile;

    public function __construct()
    {
        $this->saveFile = __DIR__ . '/../../data/user_saved_items.json';
    }

    public function get()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            // Logged in user
            return isset($_SESSION['saved_items']) ? $_SESSION['saved_items'] : [];
        } else {
            // Guest
            return isset($_SESSION['guest_saved_items']) ? $_SESSION['guest_saved_items'] : [];
        }
    }

    public function save($data)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
            $_SESSION['saved_items'] = $data;
            $this->saveToDisk($_SESSION['user_id'], $data);
        } else {
            $_SESSION['guest_saved_items'] = $data;
        }
    }

    public function saveToDisk($userId, $data)
    {
        $allData = file_exists($this->saveFile) ? json_decode(file_get_contents($this->saveFile), true) : [];
        $allData[$userId] = $data;
        
        $dir = dirname($this->saveFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($this->saveFile, json_encode($allData, JSON_PRETTY_PRINT));
    }

    public function loadFromDisk($userId)
    {
        if (file_exists($this->saveFile)) {
            $allData = json_decode(file_get_contents($this->saveFile), true);
            return isset($allData[$userId]) ? $allData[$userId] : [];
        }
        return [];
    }
}
