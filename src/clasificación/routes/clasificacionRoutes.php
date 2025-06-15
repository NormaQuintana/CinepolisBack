<?php
require_once __DIR__ . '/../controller/clasificacionController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET' && $request_uri === '/api/cinepolis/clasificaciones/obtenerClasificaciones') {
    ClasificacionController::obtenerClasificaciones();
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
} 
?>