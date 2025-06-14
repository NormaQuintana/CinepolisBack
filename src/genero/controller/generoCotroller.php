<?php
require_once __DIR__ . '/../services/generoService.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';

class GeneroController {

    public function obtenerGeneros() {
        $generoService = new GeneroService();
         header('Content-Type: application/xml');

        echo XmlHandler::generarXml($peliculas, 'peliculas', 'genero');
    }
}
?>