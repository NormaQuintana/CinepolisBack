<?php
require_once __DIR__ . '/../services/generoService.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';

class GeneroController {

    public static function obtenerGeneros() {
        $genero = GeneroService::obtenerGeneros();
        header('Content-Type: application/xml');

        echo XmlHandler::generarXml($genero, 'generos', 'genero');
    }
}
?>