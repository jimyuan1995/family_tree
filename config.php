<?php 
    define('DB_SERVER', '127.0.0.1');
    define('DB_USERNAME', 'family_tree');
    define('DB_PASSWORD', '123');
    define('DB_DATABASE', 'family_tree');
    $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
?>