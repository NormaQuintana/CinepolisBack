<?php

require_once __DIR__ . '/../controllers/peliculaController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET' && $request_uri === '/api/cinepolis/peliculas/obtenerPeliculas') {
    PeliculaController::index();
} else if ($request_method === 'GET' && preg_match('/\/api\/cinepolis\/peliculas\/obtenerPelicula\/(\d+)/', $request_uri, $matches)) {
    $id_pelicula = $matches[1];
    PeliculaController::show($id_pelicula);
} else if ($request_method === 'GET' && preg_match('/\/api\/cinepolis\/peliculas\/obtenerPeliculasPorNombre\/(.+)/', $request_uri, $matches)) {
    $nombre = $matches[1];
    PeliculaController::showByName($nombre);
} else if ($request_method === 'POST' && $request_uri === '/api/cinepolis/peliculas/crearPelicula') {
    PeliculaController::create();
} else if ($request_method === 'PUT' && preg_match('/\/api\/cinepolis\/peliculas\/editarPelicula\/(\d+)/', $request_uri, $matches)) {
    $id_pelicula = $matches[1];
    PeliculaController::update($id_pelicula);
}
 else if ($request_method === 'DELETE' && preg_match('/\/api\/cinepolis\/peliculas\/eliminarPelicula\/(\d+)/', $request_uri, $matches)) {
    $id_pelicula = $matches[1];
    PeliculaController::delete($id_pelicula);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
