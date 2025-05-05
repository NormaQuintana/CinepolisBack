<?php

require_once __DIR__ . '/../services/peliculaService.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php';


class PeliculaController {

    public static function index() {
        $peliculas = PeliculaService::obtenerPeliculas();
        header('Content-Type: application/xml');

        echo XmlHandler::generarXml($peliculas, 'peliculas', 'pelicula');
    }

    public static function show($id_pelicula) {
        $pelicula = PeliculaService::obtenerPeliculaPorId($id_pelicula);
        header('Content-Type: application/xml');

        $xml = new SimpleXMLElement('<pelicula/>');
        foreach ($pelicula as $key => $value) {
            $xml->addChild($key, htmlspecialchars($value));
        }

        echo $xml->asXML();
    }

    public static function showByName($nombre) {
        $peliculas = PeliculaService::obtenerPeliculasPorNombre($nombre);
        header('Content-Type: application/xml');

        if (!empty($peliculas)) {
            $xml = new SimpleXMLElement('<peliculas/>');
            foreach ($peliculas as $pelicula) {
                $peliculaNode = $xml->addChild('pelicula');
                $peliculaNode->addChild('id_pelicula', htmlspecialchars($pelicula['id_pelicula']));
                $peliculaNode->addChild('titulo', htmlspecialchars($pelicula['titulo']));
                $peliculaNode->addChild('director', htmlspecialchars($pelicula['director']));
                $peliculaNode->addChild('sinopsis', htmlspecialchars($pelicula['sinopsis']));
                $peliculaNode->addChild('duracion', htmlspecialchars($pelicula['duracion']));
                $peliculaNode->addChild('reparto', htmlspecialchars($pelicula['reparto']));
                $peliculaNode->addChild('ruta_poster', htmlspecialchars($pelicula['ruta_poster']));
                $peliculaNode->addChild('genero', htmlspecialchars($pelicula['genero']));
                $peliculaNode->addChild('clasificacion', htmlspecialchars($pelicula['clasificacion']));
            }
        } else {
            // Si no hay resultados, también respondemos un XML
            $xml = new SimpleXMLElement('<peliculas/>');
            $xml->addChild('mensaje', 'No se encontró ninguna película con ese nombre');
            echo $xml->asXML();
        }

        echo $xml->asXML();
        
    }

    public static function create() {
        $data = file_get_contents('php://input');
        $xml = simplexml_load_string($data);

        $id_genero = (int)$xml->id_genero;
        $id_clasificacion = (int)$xml->id_clasificacion;
        $titulo = (string)$xml->titulo;
        $director = (string)$xml->director;
        $sinopsis = (string)$xml->sinopsis;
        $duracion = (string)$xml->duracion;
        $reparto = (string)$xml->reparto;
        $ruta_poster = (string)$xml->ruta_poster;

        if (PeliculaService::crearPelicula($id_genero, $id_clasificacion, $titulo, 
            $director, $sinopsis, $duracion, $reparto, $ruta_poster)) {
            http_response_code(201);
            echo json_encode(['message' => 'Pelicula creada exitosamente']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear la pelicula']);
        }
    }

    public static function update() {
        $data = file_get_contents('php://input');
        $xml = simplexml_load_string($data);

        $id_pelicula = (int)$xml->id_pelicula;
        $id_genero = (int)$xml->id_genero;
        $id_clasificacion = (int)$xml->id_clasificacion;
        $titulo = (string)$xml->titulo;
        $director = (string)$xml->director;
        $sinopsis = (string)$xml->sinopsis;
        $duracion = (string)$xml->duracion;
        $reparto = (string)$xml->reparto;
        $ruta_poster = (string)$xml->ruta_poster;

        if (PeliculaService::editarPelicula($id_pelicula, $id_genero, $id_clasificacion, 
            $titulo, $director, $sinopsis, $duracion, $reparto, $ruta_poster)) {
            http_response_code(200);
            echo json_encode(['message' => 'Pelicula editada exitosamente']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al editar la pelicula']);
        }
    }

    public static function delete() {
        $data = file_get_contents('php://input');
        $xml = simplexml_load_string($data);

        $id_pelicula = (int)$xml->id_pelicula;

        if (PeliculaService::obtenerPeliculaPorId($id_pelicula) == null) {
            http_response_code(404);
            echo json_encode(['message' => 'Pelicula no encontrada']);
        } elseif (PeliculaService::eliminarPelicula($id_pelicula)) {
            http_response_code(200);
            echo json_encode(['message' => 'Pelicula eliminada exitosamente']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al eliminar la pelicula']);
        }
    }
}
?>