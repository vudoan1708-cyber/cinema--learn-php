<?php
  namespace cinema\modules;

  use cinema\database\DBFactory;
  // Utilities
  use cinema\utilities\Exception;

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
      if (json_encode($result, JSON_PRETTY_PRINT) === 'false') {
        Exception::handleException(new \Exception('Cannot find a user from the provided userId', 404));
      }
      $response['body'] = $result;
      return $response['body'];
    }

    public function findAll() {
      $result = self::$dbFactory->select('*')->from('Users')->fetchAll();
      if (count($result) === 0) {
        Exception::handleException(new \Exception('No user is found', 404));
      }
      $response['body'] = $result;
      return $response['body'];
    }

    public function create(
      $firstName,
      $lastName,
      $address = null,
      $email = null,
      $job = null
    ) {
      // Add parameters
      $dbFactoryWithParams = self::$dbFactory->addParameters([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':address' => $address,
        ':email' => $email,
        ':job' => $job,
      ]);
      $result = $dbFactoryWithParams
        ->insert('Users', [ 'firstName', 'lastName', 'address', 'email', 'job' ])
        ->fetchLastInserted('Users');
      $response['body'] = $result;
      return $response['body'];
    }

    public function update(
      $id,
      $firstName = null,
      $lastName = null,
      $address = null,
      $email = null,
      $job = null
    ) {
      $params = [ ':id' => $id ];

      if (\is_string($firstName)) $params[':firstName'] = $firstName;
      if (\is_string($lastName)) $params[':lastName'] = $lastName;
      if (\is_string($address)) $params[':address'] = $address;
      if (\is_string($email)) $params[':email'] = $email;
      if (\is_string($job)) $params[':job'] = $job;

      // Add parameters
      $dbFactoryWithParams = self::$dbFactory->addParameters($params);
      $dbFactoryWithParams
        ->update(
            'Users',
            array_map(function (string $key) {
              return explode(':', $key)[1];
            }, array_keys($params))
          )
        ->where('id')
        ->fetchById('Users', $id);
      http_response_code(204);
    }

    public function delete($id) {
      // Add parameters
      $dbFactoryWithParams = self::$dbFactory->addParameters([
        ':id' => $id,
      ]);
      $dbFactoryWithParams
        ->delete('Users')
        ->where('id')
        ->fetchById('Users', $id);
      http_response_code(204);
    }
  }
