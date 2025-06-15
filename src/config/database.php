<?php
$host = 'localhost';
$user = 'root';
$password = 'P@ssw0rd1704';
$database = 'cinepolis';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>