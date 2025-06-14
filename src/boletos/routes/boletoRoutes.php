<?php
// rutas.php (o tu archivo de enrutamiento principal)
require_once __DIR__ . '/../controller/boletoController.php'; 

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Ruta para apartar boletos
if ($request_method === 'POST' && $request_uri === '/api/cinepolis/boletos/apartarBoletos') {
    BoletoController::apartarBoletos();
} 
// Ruta para obtener el estado de los asientos
else if ($request_method === 'GET' && preg_match('/^\/api\/cinepolis\/boletos\/sala\/(\d+)\/cartelera\/(\d+)\/asientos$/', $request_uri, $matches)) {
    $idSala = $matches[1];
    $idCartelera = $matches[2];
    BoletoController::obtenerEstadoAsientos($idSala, $idCartelera);
}
// ... otras rutas
else {
    // Manejar 404 Not Found si ninguna ruta coincide
    http_response_code(404);
    header('Content-Type: application/xml');
    echo XmlHandler::generarXml(['error' => 'Ruta no encontrada.'], 'respuesta');
}
?>