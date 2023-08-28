<?php
namespace cinema\modules;

use cinema\database\DBFactory;

class ActorModule {
  private static $dbFactory;

  public function __construct(DBFactory $dbf) {
    self::$dbFactory = $dbf;
  }
  
  public function find(string $id) {
    $result = self::$dbFactory
      ->addParameters([ ':id' => $id ])
      ->select('*')
      ->from('Actors')
      ->where('id')
      ->fetchOne();
    $response['body'] = $result;
    return $response['body'];
  }

  /**
   * Find actors by IDs
   * @param string[] $ids an array if actor IDs
   * @example - findManyByIds([ 1, 2, 3, 4, 5 ]);
   */
  public function findManyByIds($ids) {
    $params = array_map(function($id) {
      return ":id{$id}"; // [ :id1, :id2, :id3, ... ]
    }, $ids);
    $result = self::$dbFactory
      ->addParameters($params)
      ->select('*')
      ->from('Actors')
      ->whereIn('id', $params)
      ->fetchOne();;
    $response['body'] = $result;
    return $response['body'];
  }

  public function findAll() {
    $result = self::$dbFactory->select('*')->from('Actors')->fetchAll();
    $response['body'] = $result;
    return $response['body'];
  }

  /**
   * Create a new actor
   */
  public function create(
    $name,
    $description = null
  ) {
    // Add parameters
    $dbFactoryWithParams = self::$dbFactory->addParameters([
      ':name' => $name,
      ':description' => $description,
    ]);
    $result = $dbFactoryWithParams
      ->insert(
          'Actors',
          [
            'name',
            'description',
          ]
        )
      ->fetchLastInserted('Actors');
    $response['body'] = $result;
    return $response['body'];
  }
}
