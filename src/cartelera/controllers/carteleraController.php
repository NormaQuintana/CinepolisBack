<?php
require_once __DIR__ . '/../services/carteleraServices.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';

class CarteleraController {
    public static function index() {
        $carteleras = CarteleraService::obtenerCarteleras();
        header('Content-Type: application/xml');
        echo XmlHandler::generarXml($carteleras, 'carteleras', 'cartelera');
    }

    // Crear una nueva cartelera
    public static function crear() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            isset($data['id_cine'], $data['id_pelicula'], $data['fecha'], $data['horario'], $data['sala'])
        ) {
            $result = CarteleraService::crearCartelera(
                $data['id_cine'],
                $data['id_pelicula'],
                $data['fecha'],
                $data['horario'],
                $data['sala']
            );
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['message' => 'Cartelera creada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Error al crear la cartelera']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos']);
        }
    }

    // Actualizar una cartelera existente
    public static function actualizar() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            isset($data['id_cartelera'], $data['id_cine'], $data['id_pelicula'], $data['fecha'], $data['horario'], $data['sala'])
        ) {
            $result = CarteleraService::actualizarCartelera(
                $data['id_cartelera'],
                $data['id_cine'],
                $data['id_pelicula'],
                $data['fecha'],
                $data['horario'],
                $data['sala']
            );
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['message' => 'Cartelera actualizada correctamente']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Error al actualizar la cartelera']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos']);
        }
    }
}
?>