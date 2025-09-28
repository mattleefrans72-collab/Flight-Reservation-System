<?php 

namespace App\Core;
use PDO;
class Database {
  public $conn;
  protected $statement;
  public function __construct($config, $username = "root", $password = '') {
    $dsn = "mysql:host={$config["host"]};port={$config["port"]};dbname={$config["dbname"]};charset={$config["charset"]}";
    $this->conn = new PDO($dsn, $username , $password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
  }
  public function query($query, $parrams = []) {
  $this->statement = $this->conn->prepare($query);
  $this->statement->execute($parrams);
  return $this;
  }
  public function fetchAll() {
  return $this->statement->fetchAll();
  }

  public function fetch() {
    return $this->statement->fetch();
  }

  public function fetchColumn() {
    return $this->statement->fetchColumn();
  }
  public function fetchOrFail() {
    $result = $this->fetch();

    if (!$result) {
      abort();
    }

    return $result;
  }
}

?>