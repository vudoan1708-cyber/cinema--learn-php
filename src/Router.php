<?php
namespace cinema;

class Router extends \AltoRouter
{
  public $allowedMethods = [];
  private $methods       = [
    'GET',
    'POST',
    'PATCH',
    'PUT',
    'DELETE'
  ];

  /**
   * Constructor for API routes.
   */
  public function __construct() {
    // This file will automatically become a class
    require $_SERVER['DOCUMENT_ROOT'] . "/routes/v1.php";
  }
  
  /**
   * Intercept the AltoRouter match function to handle CORS OPTIONS requests
   * Match a given Request Url against stored routes
   *
   * @param string $requestUrl    Optionally set the request URL instead of setting automatically later.
   * @param string $requestMethod Optionally set the request Method instead of setting automatically later.
   *
   * @return array
   */
  public function matchRoute(string $requestUrl = NULL, string $requestMethod = NULL): array {
    $originalRequestMethod = $requestMethod ?? $_SERVER['REQUEST_METHOD'] ?? "";
    if ($originalRequestMethod == 'OPTIONS') {
      $requestMethod = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $originalRequestMethod;
    }
    foreach ($this->methods as $method) {
      if (parent::match($requestUrl, $method) && !in_array($method, $this->allowedMethods)) {
        $this->allowedMethods[] = $method;
      }
    }
    $match = parent::match($requestUrl, $requestMethod);
    if ($match) {
      $match['request_method'] = $originalRequestMethod;
    }
    return $match ? $match : [];
  }
}
