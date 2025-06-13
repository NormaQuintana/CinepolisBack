<?php
require_once __DIR__ . '/../models/cartelera.php';

class CarteleraService {
    public static function obtenerCarteleras() {
        return Cartelera::obtenerCarteleras();
    }

    public static function crearCartelera($id_cine, $id_pelicula, $fecha, $horario, $sala) {
        return Cartelera::crearCartelera($id_cine, $id_pelicula, $fecha, $horario, $sala);
    }

    public static function actualizarCartelera($id_cartelera, $id_cine, $id_pelicula, $fecha, $horario, $sala) {
        return Cartelera::actualizarCartelera($id_cartelera, $id_cine, $id_pelicula, $fecha, $horario, $sala);
    }
}
?>