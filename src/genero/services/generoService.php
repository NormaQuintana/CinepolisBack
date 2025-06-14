<?php
require_once __DIR__ . '/../models/genero.php';

class GeneroService {
    public function obtenerGeneros() {
        $generos = Genero::obtenerGeneros();
        return $generos;
    }
}
?>