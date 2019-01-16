<?php
/**
 * ControllerTemplate
 * This class is a template to build new controllers. It is not designed to be
 * used itself. When creating a new controller:
 *      Copy this file into a new file
 *      Name the file for the model that you want to create. It should be a
 *          PascalCase version of the the corresponding database table name
 *          that is being used to store the data with a .php extention
 *      Name the Model the same as you named the file (without the php extention)
 *      In the __construct() method, change <ModelName> to match the to be used
 *          (PascalCase and singular)
 *      In all other methods, replace <controllername> with a lowercase version
 *          of the controller name (all lowercase and plural)
 *      In all methods, replace <modelname> with a lowercase version
 *          of the controller name (all lowercase and singular)
 *      In all methods, change 'field1' and 'field2' to the actual names of the
 *          fields from your model. Add more fields as desired.
 *      If user data is wanted to be passed to the view, then:
 *          * In the __construct() method, uncomment the section of
 *            code to implement login and exposure to user data
 *          * In the index(), detail(), add() and edit() methods uncomment the
 *            line of code that adds the user data to the data array
 *
 * @author tturnquist
 */
  class ControllerTemplates extends Controller{
    public function __construct(){
        // Load Model
        $this->modelInstance = $this->model('<ModelName>');

        // if login and userdata are needed uncomment and customize this section
        /*
        if(!isset($_SESSION['user_id'])){
          redirect('users/login');
        }
        $this->userModel = $this->model('User');
        $this->userData = $this->userModel->fetchById($_SESSION['user_id']);
         * */
    }

    // Load list of Records
    public function index(){
        $modelData = $this->modelInstance->fetchAll();
        $data = [
            '<controllername>' => $modelData,
            // 'user' => $this->userData
        ];
        $this->view('<controllername>/index', $data);
    }

    // Show Single Record
    public function detail($id){
        $modelData = $this->modelInstance->fetchById($id);

        $data = [
            '<controllername>' => $modelData,
            // 'user' => $this->userData
        ];

      $this->view('<controllername>/detail', $data);
    }

    // Add a Record
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize POST
            $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'field1' => trim($_POST['field1']),
                'field2' => trim($_POST['field2']),
                'user_id' => $_SESSION['user_id'],
                /* Audit columns (uncomment if wanted)
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'updated_date' => date('Y-m-d'),
                */
                'field1_err' => '',
                'filed2_err' => '',
                // 'user' => $this->userData
            ];

            // Validate form (customize and repeats as needed)
            if(empty($data['field1'])){
                $data['field1_err'] = 'Please enter field1';
            }

            // Make sure there are no errors
            if(empty($data['field1_err']) &&
                    empty($data['field2_err'])){
                // Validation passed. Log that fact
                    $msg = 'Validation passed with data: ' . implode(', ', $data);
                    logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'ADD');
                //Execute
                if($this->modelInstance->add($data)){
                    // Redirect to login
                    flash('record_added', 'Record Added');
                    redirect('<controllername>');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('<controllername>/add', $data);
            }
        } else {
            $data = [
                'field1' => '',
                'field2' => '',
                /* Audit columns (uncomment if wanted)
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'updated_date' => date('Y-m-d'),
                */
            ];
            $this->view('<controllername>/add', $data);
        }
    }

    // Edit a Record
    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize POST
            $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'field1' => trim($_POST['field1']),
                'field2' => trim($_POST['field2']),
                'user_id' => $_SESSION['user_id'],
                /* Audit columns (uncomment if wanted)
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'updated_date' => date('Y-m-d'),
                */
                'field1_err' => '',
                'field2_err' => '',
                // 'user' => $this->userData
            ];

            // Validate form (customize and repeats as needed)
            if(empty($data['field1'])){
                $data['field1_err'] = 'Please enter field1';
            }

            // Make sure there are no errors
            if(empty($data['field1_err']) &&
                  empty($data['field2_err'])){
                // Validation passed. Log it
                $msg = 'Validation passed with data: ' . implode(', ', $data);
                logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'EDIT');
                //Execute
                if($this->modelInstance->update($data, '', 'id', '=', $id)){
                    // Redirect to login
                    flash('record_message', 'Record Updated');
                    redirect('<controllername>');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $this->view('<controllername>/edit', $data);
        }

        } else {
            // Get record from model
            $record = $this->modelInstance->fetchById($id);
            // Check for owner
            if($record->created_by != $_SESSION['user_id']){
                redirect('<controllername>');
            }

            $data = [
                'id' => $id,
                'field1' => $record->field1,
                'field2' => $record->field2,
                'user' => $this->user,
                /* Audit columns (uncomment if wanted)
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'updated_date' => date('Y-m-d'),
                */
            ];

          $this->view('<controllername>/edit', $data);
        }
    }

    // Delete a Record
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Get record from model
            $record = $this->modelInstance->fetchById($id);
            // Check for owner
            if($record->created_by != $_SESSION['user_id']){
                $msg = 'Non-owner tried to delete record: ' . __CLASS__ . ":$id";
                logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'DEL');
                redirect('<controllername>');
            }
            //Execute
            if($this->modelInstance->delete($id)){
                // Redirect to login
                flash('record_message', 'Record Removed');
                redirect('<controllername>');
            } else {
                $msg = 'Something went wrong: ' . __CLASS__ . ":$id | $err";
                logThis($msg, 'app', __FILE__. ' line: ' . __LINE__, __FUNCTION__, 'DEL');
                die('Something went wrong');
            }
        } else {
              redirect('<controllername>');
        }
      }
  }