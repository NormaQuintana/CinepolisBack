<?php
require_once __DIR__ . '/../../config/database.php';

class Clasificacion {
    public static function obtenerClasificaciones(){
        global $conn;
        $sql = "SELECT *FROM clasificacion";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>