<?php
  // Load Config
  require_once 'config/config.php';
  // Load Helpers
  require_once 'helpers/session_helper.php';
  require_once 'helpers/url_helper.php';

  // Autoload Core Classes
  spl_autoload_register(function ($className) {
      echo APPROOT .'/libraries/'. $className . '.php\n';
      if (file_exists(APPROOT .'/libraries/'. $className . '.php')){
        require_once APPROOT . '/libraries/'. $className . '.php';
      }
  });
