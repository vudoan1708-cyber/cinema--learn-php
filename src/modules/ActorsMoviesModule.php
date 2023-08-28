<?php
namespace cinema\modules;

// Database
use cinema\database\DBFactory;
// Modules
use cinema\modules\ActorModule;
use cinema\modules\MovieModule;

class ActorsMoviesModule {
  public static $actorModule;
  public static $movieModule;

  public function __construct(DBFactory $dbf) {
    if (!isset(self::$actorModule)) {
      self::$actorModule = new ActorModule($dbf);
    }
    if (!isset(self::$movieModule)) {
      self::$movieModule = new MovieModule($dbf);
    }
  }

  public function findActorByActorId(string $actorId) {

  }

  public function findMovieByMovieId(string $movieId) {
    return self::$movieModule->find($movieId);
  }

  // Cross data search
  public function findActorsByMovieId(string $movieId) {

  }

  public function findMoviesByActorId(string $actorId) {

  }

  public function createMovie(
    $name,
    $description = null,
    $yearOfRelease = null,
    $thumbnail = null,
    $price = null,
    $currency = null,
    $actors = []
  ) {
    $result = self::$movieModule->create(
      $name,
      $description,
      $yearOfRelease,
      $thumbnail,
      $price,
      $currency
    );

    // Find relevant actors
    // self::$actorModule->find($actorId);
    // If not found, create them from the Actors and ActorsMovies tables

    // Otherwise, create another row from ActorsMovies table

    // Return the created movie
    return $result;
  }
  // public function updateActors()
}
