<?php
require_once __DIR__ . '/../controllers/carteleraController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Obtener carteleras
if ($request_method === 'GET' && $request_uri === '/api/cinepolis/cartelera/obtenerCarteleras') {
    CarteleraController::index();
}
// Crear cartelera
else if ($request_method === 'POST' && $request_uri === '/api/cinepolis/cartelera/crearCartelera') {
    CarteleraController::crear();
}
// Actualizar cartelera
else if ($request_method === 'PUT' && $request_uri === '/api/cinepolis/cartelera/actualizarCartelera') {
    CarteleraController::actualizar();
}
else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
?>