<?php
// api/index.php
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = trim(str_replace('/api/cinepolis', '', $request_uri), '/');
$segments = explode('/', $path);

header('Content-Type: application/xml'); // Establecer el tipo de contenido a XML

switch ($segments[0]) {
    case 'peliculas':
        include '../src/peliculas/index.php';
        break;
    case 'cartelera':
        include '../src/cartelera/index.php';
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        // Si también quieres devolver un cuerpo XML para el error 404:
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>';
        echo '<message>Ruta no encontrada</message>';
        echo '</error>';
        break;
}
?>