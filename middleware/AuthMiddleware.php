<?php
class AuthMiddleware {
    public static function handle() {
        session_start();
        
        // Verificamos si el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Acceso no autorizado"
            ]);
            exit;
        }
    }
}
