<?php

require_once __DIR__ . '/../controllers/peliculaController.php';

$request_uri = $_SERVER['REQUEST_URI']; 
$request_method = $_SERVER['REQUEST_METHOD'];

//obtener todas las peliculas

if ($request_method === 'GET' && $request_uri === '/api/cinepolis/peliculas/obtenerPeliculas') {
    PeliculaController::index();
} 
else if ($request_method === 'GET' && preg_match('/\/api\/cinepolis\/peliculas\/obtenerPelicula\/(\d+)/', $request_uri, $matches)) {
    $id_pelicula = $matches[1];
    PeliculaController::show($id_pelicula);
}
else if( $request_method === 'GET' && preg_match('/\/api\/cinepolis\/peliculas\/obtenerPeliculasPorNombre\/(.+)/', $request_uri, $matches)) {
    $nombre = $matches[1];
    PeliculaController::showByName($nombre);
}
else if ($request_method === 'POST' && $request_uri === '/api/cinepolis/peliculas/crearPelicula') {
    PeliculaController::create();
}
else if ($request_method === 'PUT' && $request_uri === '/api/cinepolis/peliculas/editarPelicula') {
    PeliculaController::update();
}
else if ($request_method === 'DELETE' && $request_uri === '/api/cinepolis/peliculas/eliminarPelicula') {
    PeliculaController::delete();
}
else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
?>