<?php
require_once __DIR__ . '/../services/carteleraServices.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';

class CarteleraController {
    public static function index() {
        $carteleras = CarteleraService::obtenerCarteleras();
        header('Content-Type: application/xml');
        echo XmlHandler::generarXml($carteleras, 'carteleras', 'cartelera');
    } 
}
?>