<?php
// Database
use cinema\API;
// Modules
use cinema\modules\UserModule;
use cinema\modules\ActorsMoviesModule;
// Utilities
use cinema\utilities\Request;

# User endpoints
$this->map(
  'GET',
  '/user/[i:userId]',
  function ($userId) {
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
    $data = Request::parseRequest();

    if (!isset($data['firstName'])) {
      http_response_code(400);
      $ex = new \Exception('firstName not set', 400);
      throw $ex;
    }
    if (!isset($data['lastName'])) {
      http_response_code(400);
      $ex = new \Exception('lastName not set', 400);
      throw $ex;
    }
    return (new UserModule(API::$dbFactory))->create(
      $data['firstName'],
      $data['lastName'],
      $data['address'] ?? null,
      $data['email'] ?? null,
      $data['job'] ?? null
    );
  },
  'user#post'
);

$this->map(
  'PATCH',
  '/user/[i:userId]',
  function ($userId) {
    $UserModule = new UserModule(API::$dbFactory);

    // Find all users and then search if the queried ID exists in the DB
    $allUsers = $UserModule->findAll();
    if (array_search($userId, array_column($allUsers, 'id')) === false) {
      http_response_code(404);
      $ex = new \Exception('User ID not found in the database', 404);
      throw $ex;
    }

    // Get the PATCH payload.
    $data = Request::parseRequest();
    echo count($data);
    if (count($data) === 0) {
      http_response_code(400);
      $ex = new \Exception('Payload is empty', 400);
      throw $ex;
    }

    return $UserModule->update(
      $userId,
      $data['firstName'],
      $data['lastName'],
      $data['address'] ?? null,
      $data['email'] ?? null,
      $data['job'] ?? null
    );
  },
  'user#patch'
);

$this->map(
  'DELETE',
  '/user/[i:userId]',
  function ($userId) {
    $UserModule = new UserModule(API::$dbFactory);

    // Find all users and then search if the queried ID exists in the DB
    $allUsers = $UserModule->findAll();
    if (array_search($userId, array_column($allUsers, 'id')) === false) {
      http_response_code(404);
      $ex = new \Exception('User ID not found in the database', 404);
      throw $ex;
    }

    return $UserModule->delete($userId);
  },
  'user#delete'
);

# Movie endpoints
$this->map(
  'GET',
  '/movie/[i:movieId]',
  function ($movieId) {
    return (new ActorsMoviesModule(API::$dbFactory))->findMovieByMovieId($movieId);
  },
  'movie#get'
);
