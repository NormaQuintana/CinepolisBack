<?php
require_once __DIR__ . '/../models/cartelera.php';

class CarteleraService {
    public static function obtenerCarteleras() {
        return Cartelera::obtenerCarteleras();
    } 
}

?>