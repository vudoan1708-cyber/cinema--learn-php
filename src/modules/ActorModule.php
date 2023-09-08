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
   * @param string[] $ids an array of actor IDs
   * @example - findManyByIds([ 1, 2, 3, 4, 5 ]);
   */
  public function findManyByIds($ids) {
    $keys = array_map(function ($id) {
      return ":id{$id}"; // [ :id1, :id2, :id3, ... ]
    }, $ids);
    // [ :id1 => '1', :id2 => 'id2', :id3 => 'id3', ... ]
    $params = array_combine($keys, $ids);

    return self::$dbFactory
      ->addParameters($params)
      ->select('*')
      ->from('Actors')
      ->whereIn('id', $keys)
      ->fetchAll();
  }

  public function findAll() {
    return self::$dbFactory->select('*')->from('Actors')->fetchAll();
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
    return $dbFactoryWithParams
      ->insert(
          'Actors',
          [
            'name',
            'description',
          ]
        )
      ->fetchLastInserted('Actors');
  }
}
