<?php
  namespace cinema\modules;

  use cinema\database\DBFactory;

  class MovieModule {
    private static $dbFactory;

    public function __construct(DBFactory $dbf) {
      self::$dbFactory = $dbf;
    }

    public function find(string $id) {
      $result = self::$dbFactory
        ->addParameters([ ':id' => $id ])
        ->select('*')
        ->from('Movies')
        ->where('id')
        ->fetchOne();
      $response['body'] = $result;
      return $response['body'];
    }

    public function findAll() {
      $result = self::$dbFactory->select('*')->from('Movies')->fetchAll();
      $response['body'] = $result;
      return $response['body'];
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
      $result = $dbFactoryWithParams
        ->insert(
            'Users',
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
      $response['body'] = $result;
      return $response['body'];
    }
  }
