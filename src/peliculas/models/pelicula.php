<?php

require_once __DIR__ . '/../../config/database.php';

class Pelicula{
    public static function obtenerPeliculas(){
        global $conn;
        $sql = "SELECT 
            p.id_pelicula,
            g.nombre AS genero,
            c.nombre AS clasificacion,
            p.titulo,
            p.director,
            p.sinopsis,
            p.duracion,
            p.reparto,
            p.ruta_poster
            FROM pelicula p
            INNER JOIN genero g ON p.id_genero = g.id_genero
            INNER JOIN clasificacion c ON p.id_clasificacion = c.id_clasificacion
            ORDER BY p.id_pelicula ASC";

        $result = $conn->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function obtenerPeliculaPorId($id_pelicula){
        global $conn;
        $sql = "SELECT 
            p.id_pelicula,
            g.nombre AS genero,
            c.nombre AS clasificacion,
            p.titulo,
            p.director,
            p.sinopsis,
            p.duracion,
            p.reparto,
            p.ruta_poster
            FROM pelicula p
            INNER JOIN genero g ON p.id_genero = g.id_genero
            INNER JOIN clasificacion c ON p.id_clasificacion = c.id_clasificacion
            WHERE p.id_pelicula = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pelicula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public static function obtenerPeliculasPorNombre($nombre){
        global $conn;
        $nombre = $conn->real_escape_string($nombre);
        $sql = "SELECT 
            p.id_pelicula,
            g.nombre AS genero,
            c.nombre AS clasificacion,
            p.titulo,
            p.director,
            p.sinopsis,
            p.duracion,
            p.reparto,
            p.ruta_poster
            FROM pelicula p
            INNER JOIN genero g ON p.id_genero = g.id_genero
            INNER JOIN clasificacion c ON p.id_clasificacion = c.id_clasificacion
            WHERE p.titulo LIKE '%$nombre%'";
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $peliculas = [];
            while ($row = $result->fetch_assoc()) {
                $peliculas[] = $row;
            }
            return $peliculas;
        } else {
            return [];  // <-- Regresa array vacío si no encontró
        }
    }

    public static function crearPelicula($id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster){
        global $conn;
        $sql = "INSERT INTO pelicula (id_genero, id_clasificacion, titulo, director, 
        sinopsis, duracion, reparto, ruta_poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssiss", $id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster);
        
        return $stmt->execute();
    }

    public static function editarPelicula($id_pelicula, $id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster){
        global $conn;
        $sql = "UPDATE pelicula SET id_genero = ?, id_clasificacion = ?, titulo = ?, 
        director = ?, sinopsis = ?, duracion = ?, reparto = ?, ruta_poster = ? WHERE id_pelicula = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssssi", $id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster, $id_pelicula);
        
        return $stmt->execute();
    }

    public static function eliminarPelicula($id_pelicula){
        global $conn;
        $sql = "DELETE FROM pelicula WHERE id_pelicula = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pelicula);
        
        return $stmt->execute();
    }
}

?>