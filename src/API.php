<?php
namespace cinema;

// Database
use cinema\database\DBFactory;

class API {
  public static $dbFactory;
  public static $router;

  public function __construct() {
    self::$dbFactory = new DBFactory();
    self::$router = new Router();
    $this->runAPI();
  }

  private function runAPI() {
    $response = null;
    $match = self::$router->match();
    // call closure or throw 404 status
    if(is_array($match) && is_callable($match['target'])) {
      $response = call_user_func_array($match['target'], $match['params']);
    } else {
      // no route was matched
      header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    }
    $this->sendResponse($response);
  }

  /**
     * Sends the response to the client.
     *
     * @param string $response The stringified response.
     *
     * @return void
     */
  private function sendResponse($response): void {
    $output = "";
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: *");
    if ($response !== NULL) {
      $output = json_encode($response, JSON_PRETTY_PRINT);
    }
    // header($_SERVER["SERVER_PROTOCOL"] . " {$response['header']}");
    if ($response) {
      echo $output;
    }
  }
}
