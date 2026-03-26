<?php
namespace App\Repositories;
use App\Core\App;
class UserRepository {
  protected $db;

  public function __construct() {
    $this->db = App::resolve("Core\Database");
  }

  public function findByEmail(string $email) {

  }
}
