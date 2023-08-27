<?php
// Database
use cinema\API;
// Modules
use cinema\modules\UserModule;
// Utilities
use cinema\utilities\Request;

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
    $data = (new Request())->parseRequest();

    if (!isset($data['firstName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'firstName not found';
      throw $ex;
    }
    if (!isset($data['lastName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'lastName not found';
      throw $ex;
    }
    return (new UserModule(API::$dbFactory))->create($data['firstName'], $data['lastName'], $data['address'] ?? null, $data['job'] ?? null);
  },
  'user#post'
);

$this->map(
  'PATCH',
  '/user/[i:userId]',
  function ($userId) {
    // Get the PATCH payload.
    $data = (new Request())->parseRequest();
    if (!isset($data['firstName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'firstName not found';
      throw $ex;
    }
    if (!isset($data['lastName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'lastName not found';
      throw $ex;
    }
    return (new UserModule(API::$dbFactory))->update($userId, $data['firstName'], $data['lastName'], $data['address'] ?? null, $data['job'] ?? null);
  },
  'user#patch'
);

$this->map(
  'DELETE',
  '/user/[i:userId]',
  function ($userId) {
    // Get the PATCH payload.
    $data = (new Request())->parseRequest();
    if (!isset($data['firstName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'firstName not found';
      throw $ex;
    }
    if (!isset($data['lastName'])) {
      $ex = new \Exception('Bad Request', 400);
      // $ex->detail = 'lastName not found';
      throw $ex;
    }
    return (new UserModule(API::$dbFactory))->delete($userId, $data['firstName'], $data['lastName'], $data['address'] ?? null, $data['job'] ?? null);
  },
  'user#delete'
);
