<?php

namespace App\Core;

class Cache {
    protected string $path = __DIR__ . '/../../cache/';
    protected int $defaultExpiry = 3600; // 5 minutes

    public function encode(array $value): string {
    $original = $value;
    unset($value['airlines']);
    unset($value['stops']);
    
    // drop stops if not actually filtering
    if (($original['stops'] ?? 'any') == 'any' || empty($original['stops'])) {
        unset($original['stops']);
    }

    // drop airlines if not actually filtering
    if (empty($original['airlines'])) {
        unset($original['airlines']);
    } else {
        // fetch all available airlines for this search
        $valueFile = 'flights_' . md5(json_encode(value: $value));
        $valueResponse = $this->get($valueFile);
        $allAirlineKeys = array_keys($valueResponse['response']['dictionaries']['original_carriers'] ?? []);

        sort($allAirlineKeys);
        $selected = $original['airlines'];
        sort($selected);

        if ($selected === $allAirlineKeys) {
            unset($original['airlines']); // all airlines selected → same as no filter
        }
    }
    return 'flights_' . md5(json_encode($original));
}
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