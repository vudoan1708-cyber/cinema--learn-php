<?php
  namespace cinema\database;

  use cinema\database\Env;

  class Database {
    private $MYSQL_HOST;
    private $MYSQL_DB;
    private $MYSQL_USER;
    private $MYSQL_PASSWORD;

    /**
     * Construct a new Database instance
     */
    public function __construct() {
      $this->MYSQL_HOST = Env::get("MYSQL_HOST");
      $this->MYSQL_DB = Env::get("MYSQL_DB");
      $this->MYSQL_USER = Env::get("MYSQL_USER");
      $this->MYSQL_PASSWORD = Env::get("MYSQL_PASSWORD");
    }

    public function connect(): \PDO {
      require_once(dirname(__DIR__) . "/internal/Debugging.php");
      try {
        $db = new \PDO(
          "mysql:host={$this->MYSQL_HOST};dbname={$this->MYSQL_DB};charset=utf8",
          $this->MYSQL_USER,
          $this->MYSQL_PASSWORD,
          [
            \PDO::MYSQL_ATTR_FOUND_ROWS => true,
            \PDO::ATTR_PERSISTENT => false,
          ]);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $db;
      } catch (\PDOException $db) {
        ErrorLog($db->getMessage());
        return null;
      }
    }
  }
