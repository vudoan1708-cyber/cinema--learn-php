<?php
  namespace cinema\modules;

  use cinema\database\DBFactory;

  class MovieModule {
    private static $dbFactory;

    public function __construct(DBFactory $dbf) {
      self::$dbFactory = $dbf;
    }

    public function find(string $id) {
      return self::$dbFactory
        ->addParameters([ ':id' => $id ])
        ->select('*')
        ->from('Movies')
        ->where('id')
        ->fetchOne();
    }

    /**
     * Find movies by IDs
     * @param string[] $ids an array of movie IDs
     * @example - findManyByIds([ 1, 2, 3, 4, 5 ]);
     */
    public function findManyByIds($ids) {
      $params = array_map(function($id) {
        return ":id{$id}"; // [ :id1, :id2, :id3, ... ]
      }, $ids);
      return self::$dbFactory
        ->addParameters($params)
        ->select('*')
        ->from('Movies')
        ->whereIn('id', $params)
        ->fetchAll();;
    }

    public function findAll() {
      return self::$dbFactory->select('*')->from('Movies')->fetchAll();
    }

    /**
     * Create a new movie
     */
    public function create(
      $name,
      $description = null,
      $yearOfRelease = null,
      $thumbnail = null,
      $price = null,
      $currency = null
    ) {
      // Add parameters
      $dbFactoryWithParams = self::$dbFactory->addParameters([
        ':name' => $name,
        ':description' => $description,
        ':yearOfRelease' => $yearOfRelease,
        ':thumbnail' => $thumbnail,
        ':price' => $price,
        ':currency' => $currency,
      ]);
      return $dbFactoryWithParams
        ->insert(
            'Movies',
            [
              'name',
              'description',
              'yearOfRelease',
              'thumbnail',
              'price',
              'currency'
            ]
          )
        ->fetchLastInserted('Movies');
    }
  }
