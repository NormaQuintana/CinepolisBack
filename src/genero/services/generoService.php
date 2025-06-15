<?php
require_once __DIR__ . '/../models/genero.php';

class GeneroService {
    public static function obtenerGeneros() {
        return Genero::obtenerGeneros();
    }
}
?>