<?php
namespace cinema\modules;

// Database
use cinema\database\DBFactory;
// Modules
use cinema\modules\MovieModule;

class BookingSystemModule {
  public static $movieModule;
  public static $dbFactory;

  public function __construct(DBFactory $dbf) {
    if (!isset($movieModule)) {
      self::$movieModule = new MovieModule($dbf);
    }

    self::$dbFactory = $dbf;
  }

  # Own's methods
  public function findManyByMovieIdAndUserId(string $movieId, string $userId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':movieId' => $movieId,
      ':userId' => $userId,
    ]);

    return $dbFactoryWithParams
      ->select('*')
      ->whereAnd([ 'movieId' => ':movieId', 'userId' => ':userId' ])
      ->fetchAll();
  }

  public function find($id) {
    return self::$dbFactory
      ->addParameters([ ':id' => $id ])
      ->select('*')
      ->from('BookingSystem')
      ->where('id')
      ->fetchOne();
  }

  public function createBooking(string $movieId, string $userId) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':movieId' => $movieId,
      ':userId' => $userId,
    ]);

    return $dbFactoryWithParams
      ->insert('BookingSystem', [ 'movieId', 'userId' ])
      ->fetchLastInserted('BookingSystem');
  }
}
