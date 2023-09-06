<?php
// Database
use cinema\API;
// Modules
use cinema\modules\UserModule;
use cinema\modules\ActorsMoviesModule;
// Utilities
use cinema\utilities\Request;
use cinema\utilities\Exception;

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
      Exception::handleException(new \Exception('firstName not set', 400));
    }
    if (!isset($data['lastName'])) {
      Exception::handleException(new \Exception('lastName not set', 400));
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
      Exception::handleException(new \Exception('User ID not found in the database', 404));
    }

    // Get the PATCH payload.
    $data = Request::parseRequest();

    if (count($data) === 0) {
      Exception::handleException(new \Exception('Payload is empty', 400));
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
      Exception::handleException(new \Exception('User ID not found in the database', 404));
    }

    return $UserModule->delete($userId);
  },
  'user#delete'
);

# Actor endpoints
$this->map(
  'GET',
  '/actorById/[i:actorId]',
  function ($actorId) {
    return (new ActorsMoviesModule(API::$dbFactory))->findActorByActorId($actorId);
  },
  'actor#get'
);

$this->map(
  'GET',
  '/actorsByMovieId/[i:movieId]',
  function ($movieId) {
    return (new ActorsMoviesModule(API::$dbFactory))->findActorsByMovieId($movieId);
  },
  'actors#get#movieId'
);

$this->map(
  'GET',
  '/actors',
  function () {
    return (new ActorsMoviesModule(API::$dbFactory))->findActors();
  },
  'actors#get'
);

$this->map(
  'POST',
  '/actor',
  function () {
    $data = Request::parseRequest();

    if (!isset($data['name'])) {
      Exception::handleException(new \Exception('Movie name is required', 400));
    }
    return (new ActorsMoviesModule(API::$dbFactory))->createActor(
      $data['name'],
      $data['description']
    );
  },
  'actor#post'
);

# Movie endpoints
$this->map(
  'GET',
  '/movieById/[i:movieId]',
  function ($movieId) {
    return (new ActorsMoviesModule(API::$dbFactory))->findMovieByMovieId($movieId);
  },
  'movie#get'
);

$this->map(
  'GET',
  '/moviesByActorId=[i:actorId]',
  function ($actorId) {
    return (new ActorsMoviesModule(API::$dbFactory))->findMoviesByActorId($actorId);
  },
  'movies#get#actorId'
);

$this->map(
  'GET',
  '/movies',
  function () {
    return (new ActorsMoviesModule(API::$dbFactory))->findMovies();
  },
  'movies#get'
);

$this->map(
  'POST',
  '/movie',
  function () {
    $data = Request::parseRequest();

    if (!isset($data['name'])) {
      Exception::handleException(new \Exception('Movie name is required', 400));
    }
    if (!isset($data['actorIds'])) {
      Exception::handleException(new \Exception('actorIds are is required', 400));
    }
    if (!is_array($data['actorIds'])) {
      Exception::handleException(new \Exception('actorIds should be an array of IDs', 400));
    }
    return (new ActorsMoviesModule(API::$dbFactory))->createMovie(
      $data['name'],
      $data['description'],
      $data['yearOfRelease'],
      $data['thumbnail'],
      $data['price'],
      $data['currency'],
      $data['actorIds']
    );
  },
  'movie#post'
);
