<?php
// boleto/controller/BoletoController.php
require_once __DIR__ . '/../services/boletoService.php';
require_once __DIR__ . '/../../../handler/XmlHandler.php'; // Asegúrate de que esta ruta sea correcta

class BoletoController {

    // --- Endpoint para Apartar Boletos ---
    public static function apartarBoletos() {
        // Establecer el encabezado Content-Type para XML
        header('Content-Type: application/xml');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo XmlHandler::generarXml(['error' => 'Método no permitido. Solo se acepta POST.'], 'respuesta');
            return;
        }

        $data = file_get_contents('php://input'); // Lee el cuerpo de la petición XML
        error_log("DEBUG (Controller Apartar): Raw XML input: " . $data);
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);

        if ($xml === false) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }
            libxml_clear_errors();
            http_response_code(400);
            echo XmlHandler::generarXml(['error' => 'Error al procesar la solicitud XML: ' . implode(', ', $errors)], 'respuesta');
            return;
        }

        // Extraer y castear los datos del XML
        // Es importante verificar si los nodos existen antes de intentar castearlos,
        // especialmente si el XML puede variar.
        $idCartelera = isset($xml->id_cartelera) ? (int)$xml->id_cartelera : 0;
        $idSala = isset($xml->id_sala) ? (int)$xml->id_sala : 0;
        $cantidad = isset($xml->cantidad) ? (int)$xml->cantidad : 0;
        $metodoPago = isset($xml->metodo_pago) ? (string)$xml->metodo_pago : '';
        $precioTotal = isset($xml->precio_total) ? (float)$xml->precio_total : 0.0;

        $numAsientos = [];
        if (isset($xml->num_asientos->asiento)) {
            foreach ($xml->num_asientos->asiento as $asiento) {
                $numAsientos[] = (string)$asiento;
            }
        }

        // Logs de depuración (estos están bien)
        error_log("DEBUG (Controller Apartar): idCartelera extraído: " . $idCartelera);
        error_log("DEBUG (Controller Apartar): idSala extraído: " . $idSala);
        error_log("DEBUG (Controller Apartar): Cantidad extraída: " . $cantidad);
        error_log("DEBUG (Controller Apartar): MetodoPago extraído: " . $metodoPago);
        error_log("DEBUG (Controller Apartar): PrecioTotal extraído: " . $precioTotal);
        error_log("DEBUG (Controller Apartar): numAsientos extraído (ARRAY): " . json_encode($numAsientos));

        // Validar que los parámetros esenciales no estén vacíos o sean inválidos
        // Se mejoró la validación de idCartelera, idSala, cantidad y precioTotal
        if ($idCartelera <= 0 || $idSala <= 0 || $cantidad <= 0 || empty($metodoPago) || $precioTotal <= 0) {
            error_log("DEBUG (Controller Apartar): Fallo de validación: Parámetros esenciales faltantes o inválidos.");
            http_response_code(400);
            echo XmlHandler::generarXml(['error' => 'Faltan parámetros requeridos o son inválidos en la solicitud.'], 'respuesta');
            return;
        }

        // Si se seleccionaron asientos, verificar su disponibilidad
        if (!empty($numAsientos)) {
            error_log("DEBUG (Controller Apartar): Llamando a verificarDisponibilidadAsientos con idCartelera: {$idCartelera}, idSala: {$idSala}, numAsientos: " . json_encode($numAsientos));
            if (!BoletoService::verificarDisponibilidadAsientos($idCartelera, $idSala, $numAsientos)) {
                error_log("DEBUG (Controller Apartar): verificarDisponibilidadAsientos retornó FALSE. Enviando error al front.");
                http_response_code(409); // Conflict, indicando que la solicitud no puede ser completada debido a un conflicto con el estado actual del recurso.
                echo XmlHandler::generarXml(['error' => 'Alguno de los asientos seleccionados no está disponible o no existe.'], 'respuesta');
                return;
            }
        } else {
            // Si no se seleccionaron asientos, pero la cantidad es > 0, esto podría ser un error de lógica
            // o un caso donde no se requiere selección de asientos (ej. boletos de pie, pero no aplicaría aquí)
            // Para boletos de cine, usualmente se requiere seleccionar asientos si cantidad > 0.
            // Considera si esta validación es necesaria para tu caso de uso.
            if ($cantidad > 0) {
                error_log("DEBUG (Controller Apartar): Cantidad de boletos > 0 pero no se seleccionaron asientos.");
                http_response_code(400);
                echo XmlHandler::generarXml(['error' => 'Se especificó una cantidad de boletos pero no se seleccionaron asientos.'], 'respuesta');
                return;
            }
        }


        // Llamar al servicio para apartar los boletos
        // Recordatorio: el idUsuario se pasa como null según tu configuración actual del modelo,
        // donde se usa un valor de prueba si es null o se ignora si la columna no existe.
        $boletoId = BoletoService::apartarBoletos($idCartelera, $idSala, $cantidad, $numAsientos, $metodoPago, $precioTotal, null);

        if ($boletoId) {
            error_log("DEBUG (Controller Apartar): Boletos apartados exitosamente, ID: " . $boletoId);
            http_response_code(201); // 201 Created es más apropiado para una creación exitosa
            echo XmlHandler::generarXml([
                'success' => 'true', // O '1' si prefieres booleanos como strings en XML
                'message' => 'Boletos apartados exitosamente.',
                'id_boleto' => $boletoId,
                'asientos_apartados' => implode(',', $numAsientos) // Para XML, mejor un string separado por comas
            ], 'respuesta');
        } else {
            error_log("DEBUG (Controller Apartar): BoletoService::apartarBoletos retornó FALSE. Error al apartar.");
            http_response_code(500); // Internal Server Error
            echo XmlHandler::generarXml(['error' => 'Error interno al apartar los boletos. Por favor, intente de nuevo.'], 'respuesta');
        }
    }

    // --- Nuevo Endpoint para Obtener el Estado de los Asientos ---
    // Este endpoint permitirá a tu frontend cargar el mapa de asientos y ver cuáles están ocupados.
    // Ejemplo de URL para este endpoint: /api/cinepolis/boletos/sala/{id_sala}/cartelera/{id_cartelera}/asientos
    public static function obtenerEstadoAsientos($idSala, $idCartelera) {
    header('Content-Type: application/xml'); // La respuesta será XML

    // Solo procesamos solicitudes GET para obtener información
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Método no permitido
        echo XmlHandler::generarXml(['error' => 'Método no permitido. Solo se acepta GET para obtener el estado de asientos.'], 'respuesta');
        return;
    }

    if (empty($idSala) || empty($idCartelera)) {
        http_response_code(400);
        echo XmlHandler::generarXml(['error' => 'Faltan parámetros de sala o cartelera.'], 'respuesta');
        return;
    }

    // Obtener todos los asientos de la sala
    $todosAsientos = BoletoService::obtenerAsientosPorSala($idSala);
    error_log("DEBUG (Controller): \$todosAsientos recibido del servicio: " . json_encode($todosAsientos));

    // Obtener los IDs de los asientos que ya están ocupados para esta cartelera
    $nombresAsientosOcupados = BoletoService::obtenerAsientosOcupadosParaCartelera($idCartelera, $idSala);
    error_log("DEBUG (Controller): \$nombresAsientosOcupados recibido del servicio: " . json_encode($nombresAsientosOcupados));

    $mapaOcupados = array_flip($nombresAsientosOcupados);

    $asientosConEstado = [];
    foreach ($todosAsientos as $asiento) {
        // *** ESTO ES LO QUE ESTABA FALTANDO O COMENTADO ***
        $nombreAsiento = $asiento['fila'] . $asiento['numero'];
        $asientosConEstado[] = [
            'id_asiento' => $asiento['id_asiento'],
            'fila' => $asiento['fila'],
            'numero' => $asiento['numero'],
            'tipo' => $asiento['tipo'],
            'nombre_asiento' => $nombreAsiento,
            'estado' => isset($mapaOcupados[$nombreAsiento]) ? 'ocupado' : 'disponible'
        ];
        // **************************************************
    }
    error_log("DEBUG (Controller): \$asientosConEstado final antes de generar XML: " . json_encode($asientosConEstado));

    http_response_code(200);
    echo XmlHandler::generarXml($asientosConEstado, 'asientos_sala', 'asiento');
    }
}
?>