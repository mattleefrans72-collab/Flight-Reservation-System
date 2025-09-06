<?php

namespace App\Core;

class Storage {
  public static function storeData($data, $name) {
    $_SESSION[$name]["data"] = $data;
  }
  public static function getData($name) {
    return $_SESSION[$name]["data"];
  }
  public static function deleteData($name) {
    unset($_SESSION[$name]);
  }
  public static function storeWithExpiry($name, $data, $ttl = 3600) {
    $_SESSION[$name] = [
        'data' => $data,
        'expires_at' => time() + $ttl
    ];
  }
  public static function getWithExpiry($name, $default = null) {

    if (!isset($_SESSION[$name])) return $default;

    $entry = $_SESSION[$name];

    if (!is_array($entry) || !isset($entry['expires_at'])) {
        return $entry; // not using expiry
    }

    if (time() > $entry['expires_at']) {
        unset($_SESSION[$name]);
        return $default;
    }

    return $entry['data'];
  }
}