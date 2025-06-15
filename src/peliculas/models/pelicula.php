<?php

require_once __DIR__ . '/../../config/database.php';

class Pelicula
{
    public static function obtenerPeliculas()
    {
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

    public static function obtenerPeliculaPorId($id_pelicula)
    {
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

    public static function obtenerPeliculasPorNombre($nombre)
    {
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

    public static function crearPelicula(
        $id_genero,
        $id_clasificacion,
        $titulo,
        $director,
        $sinopsis,
        $duracion,
        $reparto,
        $ruta_poster
    ) {
        global $conn;
        $sql = "INSERT INTO pelicula (id_genero, id_clasificacion, titulo, director, 
        sinopsis, duracion, reparto, ruta_poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisssiss",
            $id_genero,
            $id_clasificacion,
            $titulo,
            $director,
            $sinopsis,
            $duracion,
            $reparto,
            $ruta_poster
        );

        return $stmt->execute();
    }

    public static function editarPelicula(
        $id_pelicula,
        $id_genero,
        $id_clasificacion,
        $titulo,
        $director,
        $sinopsis,
        $duracion,
        $reparto,
        $ruta_poster
    ) {
        global $conn;
        $sql = "UPDATE pelicula SET id_genero = ?, id_clasificacion = ?, titulo = ?, 
        director = ?, sinopsis = ?, duracion = ?, reparto = ?, ruta_poster = ? WHERE id_pelicula = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iissssssi",
            $id_genero,
            $id_clasificacion,
            $titulo,
            $director,
            $sinopsis,
            $duracion,
            $reparto,
            $ruta_poster,
            $id_pelicula
        );

        return $stmt->execute();
    }

    public static function eliminarPelicula($id_pelicula)
    {
        global $conn;

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // PASO 1: Eliminar boleto_asiento
            $sql1 = "
            DELETE FROM boleto_asiento 
            WHERE id_boleto IN (
                SELECT id_boleto FROM boleto 
                WHERE id_cartelera IN (
                    SELECT id_cartelera FROM cartelera WHERE id_pelicula = ?
                )
            )";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("i", $id_pelicula);
            $stmt1->execute();

            // PASO 2: Eliminar boletos
            $sql2 = "
            DELETE FROM boleto 
            WHERE id_cartelera IN (
                SELECT id_cartelera FROM cartelera WHERE id_pelicula = ?
            )";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $id_pelicula);
            $stmt2->execute();

            // PASO 3: Eliminar cartelera
            $sql3 = "DELETE FROM cartelera WHERE id_pelicula = ?";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("i", $id_pelicula);
            $stmt3->execute();

            // PASO 4: Eliminar película
            $sql4 = "DELETE FROM pelicula WHERE id_pelicula = ?";
            $stmt4 = $conn->prepare($sql4);
            $stmt4->bind_param("i", $id_pelicula);
            $stmt4->execute();

            // Confirmar transacción
            $conn->commit();
            return true;
        } catch (Exception $e) {
            // Revertir si hay error
            $conn->rollback();
            error_log("Error al eliminar película: " . $e->getMessage());
            return false;
        }
    }
}
