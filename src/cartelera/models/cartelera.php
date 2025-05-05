<?php
require_once __DIR__ . '/../../config/database.php';

class Cartelera {
    public static function obtenerCarteleras(){
        global $conn;
        $sql = "SELECT 
            c.id_cartelera,
            cn.nombre AS cine,
            p.ruta_poster,
            p.titulo AS pelicula,
            c.fecha,
            c.horario
            FROM cartelera c
            INNER JOIN cine cn ON c.id_cine = cn.id_cine
            INNER JOIN pelicula p ON c.id_pelicula = p.id_pelicula
            ORDER BY cn.nombre ASC";

        $result = $conn->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?> 