<?php
$host = 'localhost';
$db = 'flameDB';
$username = 'db_admin';
$passwd = 'flamerootpassword';

$conn = new mysqli($host, $username, $passwd, $db);

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
    return false;
}

$conn->set_charset('utf8mb4');
return $conn;
?>