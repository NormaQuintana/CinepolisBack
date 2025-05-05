<?php
require_once __DIR__ . '/../controllers/carteleraController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];


if ($request_method === 'GET' && $request_uri === '/api/cinepolis/cartelera/obtenerCarteleras') {
    CarteleraController::index();
} 
else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}

?>