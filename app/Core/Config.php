<?php

namespace App\Core;

class Config {
  protected static array $config = [];
  protected static function load() {
      if (empty(self::$config)) {
          self::$config = require base_path('config.php');
      }
  }
  public static function get($key, $default = null) {
      self::load();

      $keys = explode('.', $key);
      $value = self::$config;

      foreach ($keys as $segment) {
          if (!is_array($value) || !array_key_exists($segment, $value)) {
              return $default;
          }
          $value = $value[$segment];
      }

      return $value;
  }
}