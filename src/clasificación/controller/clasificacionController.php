<?php
require_once __DIR__ . '/../services/clasificacionService.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';

class ClasificacionController {

    public static function obtenerClasificaciones() {
        $clasificacion = ClasificacionService::obtenerClasificaciones();
        header('Content-Type: application/xml');

        echo XmlHandler::generarXml($clasificacion, 'clasificaciones', 'clasificacion');
    }
}
?>