<?php

namespace App\Core;

class Cache {
    protected string $path = __DIR__ . '/../../cache/';
    protected int $defaultExpiry = 300; // 5 minutes

    public function get($key) {
        $file = $this->path . md5($key) . '.json';
        
        if (!file_exists($file)) return null;
        if (time() - filemtime($file) > $this->defaultExpiry) return null;

        return json_decode(file_get_contents($file), true);
    }

    public function set($key, $data) {
        $file = $this->path . md5($key) . '.json';
        return file_put_contents($file, json_encode($data)) !== false;
    }

    public function clear($key) {
        $file = $this->path . md5($key) . '.json';
        if (file_exists($file)) unlink($file);
    }
}