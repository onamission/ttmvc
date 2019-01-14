<?php
    // Load Config
    require_once 'config/config.php';
  
    // Load Helpers
    require_once APPROOT .'/../src/helpers/session_helper.php';
    require_once APPROOT .'/../src/helpers/url_helper.php';
    require_once APPROOT .'/../src/helpers/array_helper.php';

    // Autoload Core Classes
    spl_autoload_register(function ($className) {
        if (file_exists(APPROOT .'/../src/libraries/'. $className . '.php')){
          require_once APPROOT . '/../src/libraries/'. $className . '.php';
        }
    });
