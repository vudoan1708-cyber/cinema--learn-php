<?php

namespace cinema\utilities;

class Exception extends \Exception {
  private const MESSAGE = [
    '400' => 'Bad Request',
    '500' => 'Internal Server Error',
    '404' => 'Not Found',
    '403' => 'Forbidden',
    '204' => 'No Content',
    '200' => 'OK',
  ];

  public static function handleException(\Exception $ex) {
    http_response_code($ex->code);
    $exceptionResponse = [
      'code' => $ex->code,
      'message' => self::MESSAGE[$ex->code],
      'detail' => $ex->getMessage(),
    ];
    echo json_encode($exceptionResponse);
    throw $ex;
  }
}
