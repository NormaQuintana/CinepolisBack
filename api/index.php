<?php

// Permitir peticiones desde cualquier origen (o puedes usar tu dominio específico)
header("Access-Control-Allow-Origin: *");

// Permitir métodos usados por tu frontend
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Permitir los encabezados necesarios (por ejemplo, Content-Type)
header("Access-Control-Allow-Headers: Content-Type");

// Si es una preflight request (con método OPTIONS), responder sin procesar más
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}



$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = trim(str_replace('/api/cinepolis', '', $request_uri), '/');
$segments = explode('/', $path);

header('Content-Type: application/xml'); 

switch ($segments[0]) {
    case 'peliculas':
        include '../src/peliculas/index.php';
        break;
    case 'cartelera':
        include '../src/cartelera/index.php';
        break;
    case 'boletos':
        include '../src/boletos/index.php';
        break;
    case 'generos':
        include '../src/genero/index.php';
        break;
    case 'clasificaciones':
        include '../src/clasificación/index.php';
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>';
        echo '<message>Ruta no encontrada</message>';
        echo '</error>';
        break;
}
?>