<?php
  namespace cinema\database;

  // Reference: https://www.sourcecodester.com/tutorial/php/16035/load-environment-variables-env-file-using-php-tutorial

  class Env {
    /**
     * Construct a new Env instance
    */
    public function __construct() {
      $dirPath = realpath(__DIR__ . "/../..");
      $filePath = realpath($dirPath . "/.env");

      // Check .envenvironment file exists
      if(!file_exists($filePath)) {
        throw new \ErrorException("Environment File is Missing.");
      }
      // Check .envenvironment file is readable
      if(!is_readable($filePath)) {
        throw new \ErrorException("Permission Denied for reading the ".($filePath).".");
      }

      if (class_exists('Dotenv\Dotenv')) {
        $dotenv = \Dotenv\Dotenv::createImmutable($dirPath);
        $dotenv->load();
      }
    }

    public static function get(string $name): string {
      return $_ENV[$name];
    }
  }
?>
