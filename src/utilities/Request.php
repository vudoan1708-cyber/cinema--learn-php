<?php

namespace cinema\utilities;

class Request {
  public static function parseRequest() {
    $data = !empty($_POST)
      ? $_POST
      : json_decode(file_get_contents('php://input'), true);
    return $data;
  }
}
