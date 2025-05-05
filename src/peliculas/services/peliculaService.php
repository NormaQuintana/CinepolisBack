<?php

require_once __DIR__ . '/../models/pelicula.php';

class peliculaService {
    public static function obtenerPeliculas() {
        return Pelicula::obtenerPeliculas();
    }

    public static function obtenerPeliculaPorId($id_pelicula) {
        return Pelicula::obtenerPeliculaPorId($id_pelicula);
    }

    public static function obtenerPeliculasPorNombre($nombre) {
        return Pelicula::obtenerPeliculasPorNombre($nombre);
    }

    public static function crearPelicula($id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster) {
        return Pelicula::crearPelicula($id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster);
    }

    public static function editarPelicula($id_pelicula, $id_genero, $id_clasificacion, $titulo, $director, 
        $sinopsis, $duracion, $reparto, $ruta_poster) {
        return Pelicula::editarPelicula($id_pelicula, $id_genero, $id_clasificacion, $titulo, 
        $director, $sinopsis, $duracion, $reparto, $ruta_poster);
    }

    public static function eliminarPelicula($id_pelicula) {
        return Pelicula::eliminarPelicula($id_pelicula);
    }
}

?>