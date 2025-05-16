<?php

$host = 'localhost';
$user = 'root';
$password = '1234567890';
$database = 'Cinepolis';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>