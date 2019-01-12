<?php
/* 
 *  CORE CONTROLLER CLASS
 *  Loads Models & Views
 */
class Controller {
    // Load model from controllers
    public function model($model){
      // Require model file
      require_once '../app/models/' . $model . '.php';
      // Instantiate model
      return new $model();
    }

    // Load view from controllers
    public function view($view, $data = []){
      // Check for view file
      if(file_exists('../app/views/'.$view.'.php')){
        // Require view file
        require_once '../app/views/'.$view.'.php';
      } else {
        // No view exists
        die('View does not exist');
      }
    }
  }