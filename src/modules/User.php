<?php
  namespace mab\modules;

  use mab\database\DBFactory;

  class UserModule {
    private static $dbFactory;

    public function __construct(DBFactory $dbf) {
      self::$dbFactory = $dbf;
    }

    /**
     * @param GET|POST|PATCH|PUT|DELETE $requestMethod
     * @param string $id
     */
    public function processRequest($requestMethod, ?string $id = null) {
      switch ($requestMethod) {
        case "GET":
          if ($id) {
            $response = $this->find($id);
            break;
          }
          $response = $this->findAll();
          break;
        case "POST":
          $fn = $_POST["firstName"];
          $ln = $_POST["lastName"];
          $addr = $_POST["address"];
          $job = $_POST["job"];
          if (!isset($fn)) {
            throw [
              "message" => "Bad Request",
              "detail" => 'Unset firstName'
            ];
          }
          if (!isset($ln)) {
            throw [
              "message" => "Bad Request",
              "detail" => 'Unset lastName'
            ];
          }
          if (!isset($addr)) {
            throw [
              "message" => "Bad Request",
              "detail" => 'Unset address'
            ];
          }
          if (!isset($job)) {
            throw [
              "message" => "Bad Request",
              "detail" => 'Unset job'
            ];
          }
          $response = $this->add($fn, $ln, $addr, $job);
          break;
        default:
          break;
      }
      header($response["status_code_header"]);
      if ($response["body"]) {
        echo $response["body"];
      }

      return $response;
    }

    public function find(string $id) {
      // $result = $this->personGateway->findAll();
      $result = self::$dbFactory
        ->addParameters([ ':id' => $id ])
        ->select('*')
        ->from('Users')
        ->where('id')
        ->fetchOne();
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);
      return $response;
    }

    public function findAll() {
      // $result = $this->personGateway->findAll();
      $result = self::$dbFactory->select('*')->from('Users')->fetchAll();
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);
      return $response;
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
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);
      return $response;
    }
  }
?>
