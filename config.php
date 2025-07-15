<?php
function getDBConnection() {
    $host = 'localhost';
    $user = 'root';
    $pass = ''; // default is empty for WAMP
    $dbname = 'crud_app'; // make sure this matches your DB name

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
