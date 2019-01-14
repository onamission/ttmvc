<?php require APPROOT . '/views/inc/header.php'; ?>
<h1>TTMVC</h1>
<p>This is a simple PHP MVC framework</p>
<h2>Features:</h2>
    <ul>
        <li>URL Rewriting with mod_rewrite</li>
        <li>Full MVC Workflow</li>
        <li>Core PDO Database Library</li>
        <li>Flash Messaging</li>
    </ul>
<h2>To use:</h2>
<ul>
    <li> Download/Checkout from git to local Document Root folder</li>
    <li> Create the database structure (tables & fields) in a local DB</li>
    <li> Open the app/config/config.php file
        <ul>
            <li> Enter Database credentials and specifics</li>
            <li> Change URLROOT value</li>
            <li> Change SITENAME value</li>
            <li> Change DEFAULT_CONTROLLER</li>
            <li> Change DEFAULT_METHOD (usually 'index')</li>
        </ul>
    </li>
    <li> Create your custom Models
        <ul>
            <li> Navigate to the app/models folder</li>
            <li> Copy the ModelTemplate.php file and paste it in the same folder</li>
            <li> Rename the file to be a PascalCase version of the db table name (in singular form)</li>
            <li> Follow the instructions in the comment at the beginning of the file</li>
            <li> Change the instructions to a description of the class</li>
        </ul>
    </li>
    <li> Create your custom Controllers
        <ul>
        <li> Navigate to the app/controllers folder</li>
        <li> Copy the ControllerTemplate.php file and paste it in the same folder</li>
        <li> Rename the file to be a PascalCase version of the db table name (in plural form)</li>
        <li> Follow the instructions in the comment at the beginning of the file</li>
        <li> Change the instructions to a description of the class</li>
        </ul>
    </li>
    <li> Create your custom Views
        <ul>
            <li> Navigate to the app/views folder</li>
            <li> Copy the viewtempate folder and paste it in the same (app/views) folder</li>
            <li> Rename the folder to be a lowercase version of the db table name (in singular form)</li>
            <li> Open each file within that new folder
                <ul>
                    <li> Follow the instructions in the comment at the beginning of the file</li>
                    <li> Erase the instructions to clean up the code</li>
                </ul>
            </li>
        </ul>
    </li>
    <li> Create your custom Test
        <ul>
            <li> Navigate to the app/tests folder</li>
            <li> Copy the TestTemplate.php file and paste it in the same folder</li>
            <li> Rename the file to be a PascalCase version of the db table name (in singular form)</li>
            <li> Follow the instructions in the comment at the beginning of the file</li>
            <li> Erase the instructions to clean up the code</li>
        </ul>
    </li>
</ul>
<br>
<?php require APPROOT . '/views/inc/footer.php'; ?>