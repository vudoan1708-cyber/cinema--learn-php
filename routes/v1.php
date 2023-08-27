<?php
// Database
use cinema\API;
// Modules
use cinema\modules\UserModule;

# User endpoints
$this->map(
  'GET',
  '/user/[i:userId]',
  function ($userId) {
    $response = [
    ];
    return (new UserModule(API::$dbFactory))->find($userId);
  },
  'user#get'
);

$this->map(
  'GET',
  '/users',
  function () {
    return (new UserModule(API::$dbFactory))->findAll();
  },
  'users#get'
);

$this->map(
  'POST',
  '/user',
  function () {
    $fn = $_POST["firstName"];
    $ln = $_POST["lastName"];
    $addr = $_POST["address"];
    $job = $_POST["job"];
    if (!isset($fn)) {
      throw [
        "message" => "Bad Request",
        "detail" => 'Unset firstName'
      ];
    }
    if (!isset($ln)) {
      throw [
        "message" => "Bad Request",
        "detail" => 'Unset lastName'
      ];
    }
    return (new UserModule(API::$dbFactory))->add($fn, $ln, $addr, $job);
  }
);
