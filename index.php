<?php
  // Reference: https://getcomposer.org/doc/01-basic-usage.md#autoloading
  require_once __DIR__ . '/vendor/autoload.php';

  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // Require in the API class
  require __DIR__ . '/src/API.php';
  // Instantiate the class and start the code flow
  new cinema\API();
  exit(0);
