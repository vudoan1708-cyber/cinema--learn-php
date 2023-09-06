<?php
namespace cinema\modules;

// Database
use cinema\database\DBFactory;
// Modules
use cinema\modules\ActorModule;
use cinema\modules\MovieModule;
// Utilities
use cinema\utilities\Exception;

class ActorsMoviesModule {
  public static $actorModule;
  public static $movieModule;
  public static $dbFactory;

  public function __construct(DBFactory $dbf) {
    if (!isset(self::$actorModule)) {
      self::$actorModule = new ActorModule($dbf);
    }
    if (!isset(self::$movieModule)) {
      self::$movieModule = new MovieModule($dbf);
    }
    self::$dbFactory = $dbf;
  }

  # ActorsMovies' own methods
  /**
   * Find ActorsMovies rows by a movie ID
   * @param string $movieId
   */
  private function findManyByMovieId($movieId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':movieId' => $movieId,
    ]);
    $result = $dbFactoryWithParams
      ->select('*')
      ->from('ActorsMovies')
      ->where('movieId')
      ->fetchAll();
    $response['body'] = $result;
    return $response['body'];
  }
  /**
   * Find ActorsMovies rows by an actor ID
   * @param string $actorId
   */
  private function findManyByActorId($actorId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':actorId' => $actorId,
    ]);
    $result = $dbFactoryWithParams
      ->select('*')
      ->from('ActorsMovies')
      ->where('actorId')
      ->fetchAll();
    $response['body'] = $result;
    return $response['body'];
  }

  private function create($actorId, $movieId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':actorId' => $actorId,
      ':movieId' => $movieId,
    ]);
    $result = $dbFactoryWithParams
      ->insert(
          'ActorsMovies',
          [
            'actorId',
            'movieId',
          ]
        )
      ->fetchLastInserted('ActorsMovies');
    $response['body'] = $result;
    return $response['body'];
  }

  # Search self via self's id
  public function findActorByActorId(string $actorId) {
    $result = self::$actorModule->find($actorId);
    if (json_encode($result, JSON_PRETTY_PRINT) === 'false') {
      Exception::handleException(new \Exception('Cannot find an actor from the provided ID', 400));
    }
    return self::$actorModule->find($actorId);
  }

  public function findMovieByMovieId(string $movieId) {
    return self::$movieModule->find($movieId);
  }

  # Cross data search
  public function findActorsByMovieId(string $movieId) {
    $actorsMovies = $this->findManyByMovieId($movieId);

    if (count($actorsMovies) === 0) {
      Exception::handleException(new \Exception('Cannot find one or more actors from the provided movieId', 400));
    }
    return self::$actorModule->findManyByIds(array_map(function ($item) {
      return $item['actorId'];
    }, $actorsMovies));
  }

  public function findMoviesByActorId(string $actorId) {
    $actorsMovies = $this->findManyByActorId($actorId);
    return self::$movieModule->findManyByIds(array_map(function ($item) {
      return $item['movieId'];
    }, $actorsMovies));
  }

  public function findMovies() {
    return self::$movieModule->findAll();
  }

  public function createMovie(
    $name,
    $description = null,
    $yearOfRelease = null,
    $thumbnail = null,
    $price = null,
    $currency = null,
    $actorIds = []
  ) {
    // Find relevant actors
    $existingActors = self::$actorModule->findManyByIds($actorIds);
    // If any one that is not found, throw exception
    if (count($existingActors) !== count($actorIds)) {
      Exception::handleException(new \Exception('At least one actor is not found from the payload', 400));
    }

    $result = self::$movieModule->create(
      $name,
      $description,
      $yearOfRelease,
      $thumbnail,
      $price,
      $currency
    );

    // Subsequenntly, create more rows from ActorsMovies table
    foreach ($actorIds as $id) {
      $this->create($id, $result['id']);
    }
    $result['actorsInMovie'] = $existingActors;
    // Return the created movie and the created actors-movies rows
    return $result;
  }

  public function findActors() {
    return self::$actorModule->findAll();
  }
  public function createActor(
    $name,
    $description = null
  ) {
    $result = self::$actorModule->create(
      $name,
      $description
    );
    // Return the created actor
    return $result;
  }
  // public function updateActors()
}
