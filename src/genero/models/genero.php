<?php
require_once __DIR__ . '/../../config/database.php';

class Genero {
    public static function obtenerGeneros(){
        global $conn;
        $sql = "SELECT *FROM genero";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>