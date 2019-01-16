# ttmvc
My first attempt at an MVC Framework

I started with the UDEMY course "Object Oriented PHP & MVC" and while working on
it, I saw ways that I could further improve it.

I have added:
 * Automated SQL query building for more automated and consistent query writing
 * A base Model class to inherit methods -- avoiding the need to copy and paste
 * Simple field validation in base Model based on field type
 * View, Model and Controller templates
 * Separation of the MVC Framework from the application
 * Simple logging
 
Future developments I hope to make:
 * Built in Role Base Security
 * CRUD auto creation (similar to zii in the Yii framework)

To use:
 * Download/Checkout to local Document Root folder
 * Create the database structure (tables & fields) in a local DB
 * Open the public/.htaccess file and change the directory in line 4 to match your structure
 * Open the app/config/config.php file
   * Enter Database credentials and specifics
   * Change URLROOT value
   * Change SITENAME value
   * Change DEFAULT_CONTROLLER
   * Change DEFAULT_METHOD (usually 'index')
 * Create your custom Models
   * Navigate to the app/models folder
   * Copy the ModelTemplate.php file and paste it in the same folder
   * Rename the file to be a PascalCase version of the db table name (in singular form)
   * Follow the instructions in the comment at the beginning of the file
   * Change the instructions to a description of the class
 * Create your custom Controllers
   * Navigate to the app/controllers folder
   * Copy the ControllerTemplate.php file and paste it in the same folder
   * Rename the file to be a PascalCase version of the db table name (in plural form)
   * Follow the instructions in the comment at the beginning of the file
   * Change the instructions to a description of the class
 * Create your custom Views
   * Navigate to the app/views folder
   * Copy the viewtempate folder and paste it in the same (app/views) folder
   * Rename the folder to be a lowercase version of the db table name (in singular form)
   * Open each file within that new folder
     * Follow the instructions in the comment at the beginning of the file
     * Erase the instructions to clean up the code
 * Create your custom Test
   * Navigate to the app/tests folder
   * Copy the TestTemplate.php file and paste it in the same folder
   * Rename the file to be a PascalCase version of the db table name (in singular form)
   * Follow the instructions in the comment at the beginning of the file
   * Erase the instructions to clean up the code

