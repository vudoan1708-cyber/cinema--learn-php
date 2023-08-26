<?php
  // Reference: https://getcomposer.org/doc/01-basic-usage.md#autoloading
  require_once __DIR__ . '/vendor/autoload.php';

  // Database
  require_once __DIR__ . '/src/database/DBFactory.php';
  // Modules
  require_once __DIR__ . '/src/modules/User.php';

  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  // Get the request URL
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $splitURI =  explode('/',  $uri);

  $requestMethod = $_SERVER["REQUEST_METHOD"];

  # API endpoints
  if ($splitURI[1] === 'users' && $requestMethod === 'GET') {
    $userModule = new mab\modules\UserModule(new mab\database\DBFactory());
    $userModule->processRequest($requestMethod);
  }
  if ($splitURI[1] === 'user' && isset($splitURI[2])) {
    $userModule = new mab\modules\UserModule(new mab\database\DBFactory());
    $userModule->processRequest($requestMethod, $splitURI[2]);
  }
