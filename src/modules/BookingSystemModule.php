<?php
namespace cinema\modules;

// Database
use cinema\database\DBFactory;
// Modules
use cinema\modules\MovieModule;

class BookingSystem {
  public static $movieModule;
  public static $dbFactory;

  public function __construct(DBFactory $dbf) {
    if (!isset($movieModule)) {
      self::$movieModule = new MovieModule($dbf);
    }

    self::$dbFactory = $dbf;
  }

  # Own's methods
  public function findManyByMovieIdAndUserId($movieId, $userId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':movieId' => $movieId,
      ':userId' => $userId,
    ]);

    $result = $dbFactoryWithParams
      ->select('*')
      ->whereAnd([ 'movieId' => ':movieId', 'userId' => ':userId' ])
      ->fetchAll();
    $response['body'] = $result;
    return $response['body'];
  }
}
