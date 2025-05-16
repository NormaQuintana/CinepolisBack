<?php
require_once __DIR__ . '/../../controllers/LoginController.php';

header("Content-Type: application/json");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['correo']) && isset($data['contrasena'])) {
        LoginController::login($data['correo'], $data['contrasena']);
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Faltan campos requeridos"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
