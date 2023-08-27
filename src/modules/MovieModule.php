<?php

  namespace cinema\modules;

  use cinema\database\DBFactory;


  class MovieModule {
    private static $dbFactory;

    public function __construct(DBFactory $dbf) {
      self::$dbFactory = $dbf;
    }
  }
