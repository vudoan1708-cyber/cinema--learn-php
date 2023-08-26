<?php
  namespace mab\database;

  use mab\database\Env;

  class Database {
    private $env;
    private $MYSQL_HOST;
    private $MYSQL_DB;
    private $MYSQL_USER;
    private $MYSQL_PASSWORD;

    /**
     * Construct a new Database instance
     * @param Env $EnvInstance an instance of Env class,
     * needed when instantiating a Database class to retrieve environment variables
     */
    public function __construct(Env $EnvInstance) {
      $this->env = $EnvInstance;
      $this->MYSQL_HOST = $this->env->get("MYSQL_HOST");
      $this->MYSQL_DB = $this->env->get("MYSQL_DB");
      $this->MYSQL_USER = $this->env->get("MYSQL_USER");
      $this->MYSQL_PASSWORD = $this->env->get("MYSQL_PASSWORD");
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
