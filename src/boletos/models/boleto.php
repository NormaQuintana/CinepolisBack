<?php
// C:\xampp\htdocs\Cinepolis\src\boletos\models\Boleto.php

// Asegúrate de que esta ruta sea correcta para tu configuración de base de datos
require_once __DIR__ . '/../../config/database.php';

class Boleto {
    // Los métodos existentes que no usan id_usuario permanecen igual
    public static function obtenerSalaPorId($id_sala){
        global $conn; 
        $sql = "SELECT id_sala, tipo, precio_boleto, num_sala FROM sala WHERE id_sala = ?";
        $stmt = $conn->prepare($sql);
        // Utiliza bind_param correctamente para mysqli, asumiendo que $conn es mysqli
        $stmt->bind_param("i", $id_sala); 
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function obtenerAsientosPorSala($idSala) {
        global $conn; // Accede a la conexión global
        $sql = "SELECT id_asiento, fila, numero, tipo FROM asiento WHERE id_sala = ? ORDER BY fila, numero";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idSala);
        $stmt->execute();
        $result = $stmt->get_result();
        $asientos = [];
        while ($row = $result->fetch_assoc()) {
            $asientos[] = $row;
        }
        
        error_log("DEBUG (BoletoModel): Resultado de obtenerAsientosPorSala para sala " . $idSala . ": " . json_encode($asientos));
        return $asientos; 
    }

    public static function verificarDisponibilidadAsientos($idCartelera, $idSala, $numAsientosSeleccionados){
        global $conn; // Asumiendo que $conn es una instancia de mysqli
        error_log("DEBUG (VerificarDisp): Iniciando verificación de disponibilidad.");
        error_log("DEBUG (VerificarDisp): idCartelera: " . $idCartelera);
        error_log("DEBUG (VerificarDisp): idSala: " . $idSala);
        error_log("DEBUG (VerificarDisp): Asientos a verificar (numAsientosSeleccionados): " . json_encode($numAsientosSeleccionados));

        if (empty($numAsientosSeleccionados)) {
            error_log("DEBUG (VerificarDisp): No se seleccionaron asientos, retornando true (o considera false si es un error).");
            return true; // O false, dependiendo de si consideras esto un estado válido
        }

        // Paso 1: Confirmar que los asientos seleccionados existen para la sala dada
        // Construye el string de placeholders para la cláusula IN
        $asiento_placeholders = implode(',', array_fill(0, count($numAsientosSeleccionados), '?'));
        $sql_get_ids = "SELECT id_asiento FROM asiento WHERE id_sala = ? AND CONCAT(fila, numero) IN ($asiento_placeholders)";
        error_log("DEBUG (VerificarDisp): SQL para obtener IDs de asientos: " . $sql_get_ids);

        $stmt_get_ids = $conn->prepare($sql_get_ids);
        if ($stmt_get_ids === false) {
            error_log("ERROR (VerificarDisp): Fallo en prepare de SQL_get_ids: " . $conn->error);
            return false;
        }

        // Para bind_param, el primer argumento es una string de tipos.
        // 'i' para idSala (integer), 's' para cada nombre de asiento (string).
        $types = "i" . str_repeat("s", count($numAsientosSeleccionados));
        
        // El operador spread (...) funciona en PHP 5.6+ para arrays
        // Los argumentos para bind_param deben ser pasados como referencias si PHP < 8.1
        // MySQLi bind_param requiere que los parámetros estén en variables, no literales de array.
        // Array de parámetros para bind_param, donde el primer elemento es la cadena de tipos
        $bind_params = array_merge([$types, $idSala], $numAsientosSeleccionados);
        // Referencias son obligatorias para mysqli::bind_param
        $refs = [];
        foreach($bind_params as $key => $value) {
            $refs[$key] = &$bind_params[$key];
        }
        call_user_func_array([$stmt_get_ids, 'bind_param'], $refs);


        if (!$stmt_get_ids->execute()) {
            error_log("ERROR (VerificarDisp): Fallo en execute de SQL_get_ids: " . $stmt_get_ids->error);
            return false;
        }

        $result_get_ids = $stmt_get_ids->get_result();
        $idsAsientosParaVerificar = [];
        while ($row = $result_get_ids->fetch_assoc()) {
            $idsAsientosParaVerificar[] = $row['id_asiento'];
        }
        error_log("DEBUG (VerificarDisp): IDs de asientos encontrados en BD (idsAsientosParaVerificar): " . json_encode($idsAsientosParaVerificar));
        error_log("DEBUG (VerificarDisp): Comparación de conteos: Encontrados [" . count($idsAsientosParaVerificar) . "] vs Seleccionados [" . count($numAsientosSeleccionados) . "]");

        if (empty($idsAsientosParaVerificar) || count($idsAsientosParaVerificar) !== count($numAsientosSeleccionados)) {
            error_log("DEBUG (VerificarDisp): CONDICION DE FALLO 1: Asientos seleccionados no existen o no todos fueron encontrados.");
            return false;
        }

        // Paso 2: Verificar si alguno de estos asientos ya está ocupado
        $ids_placeholders = implode(',', array_fill(0, count($idsAsientosParaVerificar), '?'));
        $sql_check_occupied = "SELECT ba.id_asiento FROM boleto_asiento ba
                               INNER JOIN boleto b ON ba.id_boleto = b.id_boleto
                               INNER JOIN asiento a ON ba.id_asiento = a.id_asiento
                               WHERE b.id_cartelera = ? AND ba.id_asiento IN ($ids_placeholders)";
        error_log("DEBUG (VerificarDisp): SQL para verificar ocupados: " . $sql_check_occupied);

        $stmt_check_occupied = $conn->prepare($sql_check_occupied);
        if ($stmt_check_occupied === false) {
            error_log("ERROR (VerificarDisp): Fallo en prepare de SQL_check_occupied: " . $conn->error);
            return false;
        }

        $types_check = "i" . str_repeat("i", count($idsAsientosParaVerificar));
        $bind_params_check = array_merge([$types_check, $idCartelera], $idsAsientosParaVerificar);
        $refs_check = [];
        foreach($bind_params_check as $key => $value) {
            $refs_check[$key] = &$bind_params_check[$key];
        }
        call_user_func_array([$stmt_check_occupied, 'bind_param'], $refs_check);

        if (!$stmt_check_occupied->execute()) {
            error_log("ERROR (VerificarDisp): Fallo en execute de SQL_check_occupied: " . $stmt_check_occupied->error);
            return false;
        }

        $result_check_occupied = $stmt_check_occupied->get_result();
        error_log("DEBUG (VerificarDisp): Filas encontradas para asientos ocupados: " . $result_check_occupied->num_rows);

        $is_available = ($result_check_occupied->num_rows === 0);
        if (!$is_available) {
            error_log("DEBUG (VerificarDisp): CONDICION DE FALLO 2: Algunos asientos seleccionados ya están OCUPADOS.");
        }
        error_log("DEBUG (VerificarDisp): Retornando disponibilidad: " . ($is_available ? "true" : "false"));
        return $is_available;
    }

    // MÉTODO APARTARBOLETOS - COMPLETAMENTE IMPLEMENTADO Y DEPURADO
    // Se corrige el parámetro a $precioPago
    public static function apartarBoletos($idCartelera, $idSala, $cantidad, $numAsientos, $metodoPago, $precioPago, $idUsuario = null) {
        global $conn; // Accede a la conexión global (asumiendo mysqli)

        // Verificación de conexión
        if (!$conn) {
            error_log("CRITICAL ERROR (Boleto::apartarBoletos): La conexión a la base de datos (\$conn) no está disponible.");
            return false;
        }

        // Si idUsuario no se va a usar en la DB, esta parte se puede eliminar o comentar.
        // Por ahora, se mantiene el log para visibilidad durante el desarrollo.
        if ($idUsuario === null) {
            error_log("WARNING (Boleto::apartarBoletos): idUsuario es NULL. No se usará en la inserción.");
        }

        try {
            $conn->begin_transaction(); // Iniciar transacción

            error_log("DEBUG (Boleto::apartarBoletos): Iniciando apartado.");
            error_log("DEBUG (Boleto::apartarBoletos): idCartelera: " . $idCartelera);
            error_log("DEBUG (Boleto::apartarBoletos): idSala: " . $idSala);
            error_log("DEBUG (Boleto::apartarBoletos): Cantidad de boletos a apartar: " . $cantidad);
            error_log("DEBUG (Boleto::apartarBoletos): Asientos seleccionados (numAsientos): " . json_encode($numAsientos));
            error_log("DEBUG (Boleto::apartarBoletos): MetodoPago: " . $metodoPago);
            error_log("DEBUG (Boleto::apartarBoletos): PrecioPago: " . $precioPago);

            $idsAsientosConfirmados = [];
            if (!empty($numAsientos)) {
                $asiento_placeholders = implode(',', array_fill(0, count($numAsientos), '?'));
                $sql_get_ids = "SELECT id_asiento FROM asiento WHERE id_sala = ? AND CONCAT(fila, numero) IN ($asiento_placeholders)";
                error_log("DEBUG (Boleto::apartarBoletos): SQL para obtener IDs de asientos: " . $sql_get_ids);
                
                $stmt_get_ids = $conn->prepare($sql_get_ids);
                if ($stmt_get_ids === false) {
                    error_log("ERROR (Boleto::apartarBoletos): Fallo en prepare de SQL_get_ids (obtener IDs): " . $conn->error);
                    $conn->rollback();
                    return false;
                }
                
                $types = "i" . str_repeat("s", count($numAsientos));
                $bind_params = array_merge([$types, $idSala], $numAsientos);
                $refs = [];
                foreach($bind_params as $key => $value) {
                    $refs[$key] = &$bind_params[$key];
                }
                call_user_func_array([$stmt_get_ids, 'bind_param'], $refs);

                if (!$stmt_get_ids->execute()) {
                    error_log("ERROR (Boleto::apartarBoletos): Fallo en execute de SQL_get_ids (obtener IDs de asientos): " . $stmt_get_ids->error);
                    $conn->rollback();
                    return false;
                }

                $result_get_ids = $stmt_get_ids->get_result();
                while ($row = $result_get_ids->fetch_assoc()) {
                    $idsAsientosConfirmados[] = $row['id_asiento'];
                }
            }
            
            error_log("DEBUG (Boleto::apartarBoletos): IDs de asientos encontrados en BD (idsAsientosConfirmados): " . json_encode($idsAsientosConfirmados));
            error_log("DEBUG (Boleto::apartarBoletos): Comparación: count(idsAsientosConfirmados) [" . count($idsAsientosConfirmados) . "] vs count(numAsientos) [" . count($numAsientos) . "]");

            // Si se seleccionaron asientos y no se encontraron todos los IDs correspondientes, algo está mal.
            if (empty($idsAsientosConfirmados) || count($idsAsientosConfirmados) !== $cantidad) {
                error_log("ERROR (Boleto::apartarBoletos): Discrepancia en la cantidad de asientos. Seleccionados: " . $cantidad . ", Encontrados: " . count($idsAsientosConfirmados));
                $conn->rollback();
                return false; 
            }

            // 1. Insertar en la tabla 'boleto'
            // IMPORTANTE: 'id_usuario' se ha eliminado de esta lista de columnas.
            $sqlInsertBoleto = "INSERT INTO boleto (id_cartelera, id_sala, cantidad, metodo_pago, precio_pago, fecha_compra) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmtInsertBoleto = $conn->prepare($sqlInsertBoleto);

            if ($stmtInsertBoleto === false) {
                error_log("ERROR (Boleto::apartarBoletos): Fallo en prepare de SQL_InsertBoleto: " . $conn->error);
                $conn->rollback();
                return false;
            }

            // La cadena de tipos ahora refleja la eliminación de 'id_usuario'
            // id_cartelera (i), id_sala (i), cantidad (i), metodo_pago (s), precio_pago (d)
           $typesBoleto = "iiisd"; // ¡Ahora 5 tipos para 5 '?' !

            // Asegúrate de que las variables pasadas coincidan con los tipos (no pasas $precioUnitario aquí)
            $stmtInsertBoleto->bind_param($typesBoleto, $idCartelera, $idSala, $cantidad, $metodoPago, $precioPago);

            if (!$stmtInsertBoleto->execute()) {
                error_log("ERROR (Boleto::apartarBoletos): Fallo al insertar en la tabla 'boleto'. SQL Error: " . $stmtInsertBoleto->error);
                error_log("ERROR (Boleto::apartarBoletos): SQL State: " . $stmtInsertBoleto->sqlstate);
                $conn->rollback();
                return false;
            }

            $idBoleto = $conn->insert_id; // Obtener el ID del boleto recién insertado (para mysqli)
            error_log("DEBUG (Boleto::apartarBoletos): Boleto insertado con ID: " . $idBoleto);

            // 2. Insertar en la tabla 'boleto_asiento' para cada asiento
            $sqlInsertBoletoAsiento = "INSERT INTO boleto_asiento (id_boleto, id_asiento) VALUES (?, ?)";
            $stmtInsertBoletoAsiento = $conn->prepare($sqlInsertBoletoAsiento);

            if ($stmtInsertBoletoAsiento === false) {
                error_log("ERROR (Boleto::apartarBoletos): Fallo en prepare de SQL_InsertBoletoAsiento: " . $conn->error);
                $conn->rollback();
                return false;
            }

            foreach ($idsAsientosConfirmados as $idAsiento) {
                $stmtInsertBoletoAsiento->bind_param("ii", $idBoleto, $idAsiento);
                if (!$stmtInsertBoletoAsiento->execute()) {
                    error_log("ERROR (Boleto::apartarBoletos): Fallo al insertar asiento " . $idAsiento . " en 'boleto_asiento'. SQL Error: " . $stmtInsertBoletoAsiento->error);
                    $conn->rollback(); // Revertir todo si un asiento falla
                    return false;
                }
                error_log("DEBUG (Boleto::apartarBoletos): Asiento " . $idAsiento . " asociado al boleto " . $idBoleto);
            }

            $conn->commit(); // Confirmar la transacción si todo fue exitoso
            error_log("DEBUG (Boleto::apartarBoletos): Transacción de apartado de boletos completada exitosamente. ID de Boleto: " . $idBoleto);

            // Retornar el ID del boleto para que el controlador frontend pueda usarlo
            return $idBoleto; 
            
        } catch (mysqli_sql_exception $e) { // Capturar excepciones específicas de MySQLi
            $conn->rollback();
            error_log("ERROR (Boleto::apartarBoletos): mysqli_sql_exception: " . $e->getMessage());
            error_log("ERROR (Boleto::apartarBoletos): Código SQL: " . $e->getCode());
            error_log("ERROR (Boleto::apartarBoletos): Stack Trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) { // Capturar otras excepciones
            $conn->rollback();
            error_log("ERROR (Boleto::apartarBoletos): Excepción general en apartarBoletos: " . $e->getMessage());
            error_log("ERROR (Boleto::apartarBoletos): Stack Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public static function obtenerAsientosOcupadosParaCartelera($idCartelera, $idSala) {
        global $conn; // Asumiendo que $conn es una instancia de mysqli
        $sql = "SELECT CONCAT(a.fila, a.numero) AS nombre_asiento
                FROM boleto_asiento ba
                INNER JOIN boleto b ON ba.id_boleto = b.id_boleto
                INNER JOIN asiento a ON ba.id_asiento = a.id_asiento
                WHERE b.id_cartelera = ? AND b.id_sala = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idCartelera, $idSala);
        $stmt->execute();
        $result = $stmt->get_result();
        $ocupados = [];
        while ($row = $result->fetch_assoc()) {
            $ocupados[] = $row['nombre_asiento'];
        }
        
        error_log("DEBUG (BoletoModel): Resultado de obtenerAsientosOcupadosParaCartelera para cartelera " . $idCartelera . " y sala " . $idSala . ": " . json_encode($ocupados));
        return $ocupados;
    }
}
?>