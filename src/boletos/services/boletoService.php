<?php
// services/BoletoService.php

// Asegúrate de que esta ruta sea correcta para tu configuración
require_once __DIR__ . '/../models/boleto.php';

class BoletoService {

    // Ya no es necesario el constructor ni la propiedad $boleto
    // porque los métodos de la clase Boleto son estáticos.

    public static function obtenerSalaPorId($id_sala) {
        return Boleto::obtenerSalaPorId($id_sala);
    }

    public static function verificarDisponibilidadAsientos($idCartelera, $idSala, $numAsientos) {
        return Boleto::verificarDisponibilidadAsientos($idCartelera, $idSala, $numAsientos);
    }

    // MÉTODO APARTARBOLETOS - MODIFICADO
    // Se eliminó $idUsuario de los parámetros del método y de la llamada a Boleto::apartarBoletos
    public static function apartarBoletos($idCartelera, $idSala, $cantidad, $numAsientos, $metodoPago, $precioTotal) {
        return Boleto::apartarBoletos($idCartelera, $idSala, $cantidad, $numAsientos, $metodoPago, $precioTotal);
    }

    public static function obtenerAsientosOcupadosParaCartelera($idCartelera, $idSala) {
        return Boleto::obtenerAsientosOcupadosParaCartelera($idCartelera, $idSala);
    }

    // --- ¡¡¡ESTE ES EL MÉTODO QUE DEBES AÑADIR/VERIFICAR!!! ---
    public static function obtenerAsientosPorSala($idSala) {
        // Este método delegará la llamada a tu clase Boleto (el modelo de datos real)
        return Boleto::obtenerAsientosPorSala($idSala);
    }
    // --- FIN DEL MÉTODO ---

}
?>