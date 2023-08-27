<?php
  namespace cinema\modules;

  use cinema\database\DBFactory;

  class UserModule {
    private static $dbFactory;

    public function __construct(DBFactory $dbf) {
      self::$dbFactory = $dbf;
    }

    public function find(string $id) {
      $result = self::$dbFactory
        ->addParameters([ ':id' => $id ])
        ->select('*')
        ->from('Users')
        ->where('id')
        ->fetchOne();
      $response['body'] = $result;
      return $response['body'];
    }

    public function findAll() {
      $result = self::$dbFactory->select('*')->from('Users')->fetchAll();
      $response['body'] = $result;
      return $response['body'];
    }

    public function add($firstName, $lastName, $address = null, $job = null) {
      // Add parameters
      $dbFactoryWithParams = self::$dbFactory->addParameters([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':address' => $address,
        ':job' => $job,
      ]);
      $result = $dbFactoryWithParams
        ->insert('Users', [ 'firstName', 'lastName', 'address', 'job' ])
        ->fetchLastInserted('Users');
      $response['body'] = $result;
      return $response['body'];
    }
  }
?>
