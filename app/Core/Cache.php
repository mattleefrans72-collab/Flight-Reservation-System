<?php

namespace App\Core;

class Cache {
  protected string $path = __DIR__ . "/../../cache/";
  protected int $defaultExpiry = 360000;
  protected bool $debug = false;

  public function get($key) {
    $file = $this->keyToFile($key);

    if (!file_exists($file)) {
      return null;
    }

    if (time() - filemtime($file) > $this->defaultExpiry) {
      return null;
    }

    $content = file_get_contents($file);
    if ($content === false) {
      return null;
    }

    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : null;
  }

  public function set($key, array $data) {
    $file = $this->keyToFile($key);

    if (!is_dir($this->path)) {
      mkdir($this->path, 0777, true);
    }

    return file_put_contents(
      $file,
      json_encode($data, JSON_PRETTY_PRINT),
    );
  }

  public function clear($key) {
    $file = $this->keyToFile($key);

    if (file_exists($file)) {
      unlink($file);
    }
  }

  public function encodeKey($params) {
    $normalized = $this->normalizeFlightsParams($params);
    return "flights_" . json_encode($normalized, JSON_UNESCAPED_SLASHES);
  }

  public function encodeOnlyStopKey($params) {
    $normalized = $this->normalizeFlightsParams($params);

    unset($normalized["airlines"]);

    return "flights_" . json_encode($normalized, JSON_UNESCAPED_SLASHES);
  }

  private function normalizeFlightsParams($params, $allAirlineKeys = []) {
    $normalized = $params;
    // Remove empty stops
    if (!isset($normalized["stops"]) || $normalized["stops"] == "ANY") {
      unset($normalized["stops"]);
    } 

    // Remove empty airlines
    if (!isset($normalized["airlines"]) || $normalized["airlines"] === []) {
      unset($normalized["airlines"]);
    } else {
      sort($normalized["airlines"]);
      sort($allAirlineKeys);
      
      if (!empty($allAirlineKeys) && $normalized["airlines"] === $allAirlineKeys) {
        unset($normalized['airlines']);
      } 
    }

    ksort($normalized);

    return $normalized;
  }

  private function keyToFile($key) {
    $name = $this->debug ? $this->sanitize($key) : md5($key);
    return $this->path . $name . ".json";
  }

  private function sanitize($key) {
    return preg_replace("/[^a-zA-Z0-9_-]/", "_", $key);
  }
}
