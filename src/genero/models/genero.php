<?php
require_once __DIR__ . '/../../config/database.php';

class Genero {
    public static function obtenerGeneros(){
        global $conn;
        $sql = "SELECT id_genero, nombre FROM genero";
        $result = $conn->query($sql);
        $generos = [];
        while ($row = $result->fetch_assoc()) {
            $generos[] = $row;
        }
        return $generos;
    }
}
?>