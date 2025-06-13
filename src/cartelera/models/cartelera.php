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

    // Crear una nueva cartelera
    public static function crearCartelera($id_cine, $id_pelicula, $fecha, $horario, $sala) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO cartelera (id_cine, id_pelicula, fecha, horario, sala) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $id_cine, $id_pelicula, $fecha, $horario, $sala);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Actualizar una cartelera existente
    public static function actualizarCartelera($id_cartelera, $id_cine, $id_pelicula, $fecha, $horario, $sala) {
        global $conn;
        $stmt = $conn->prepare("UPDATE cartelera SET id_cine = ?, id_pelicula = ?, fecha = ?, horario = ?, sala = ? WHERE id_cartelera = ?");
        $stmt->bind_param("iisssi", $id_cine, $id_pelicula, $fecha, $horario, $sala, $id_cartelera);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}

?>