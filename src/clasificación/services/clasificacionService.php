<?php
require_once __DIR__ . '/../models/clasificacion.php';

class ClasificacionService {
    public static function obtenerClasificaciones() {
        return Clasificacion::obtenerClasificaciones();
    }
}
?>