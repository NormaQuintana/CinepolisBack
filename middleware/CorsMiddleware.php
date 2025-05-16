<?php
class CorsMiddleware {
    public static function handle() {
        header("Access-Control-Allow-Origin: *"); // Para desarrollo. Cambiar a origen específico en producción
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Opcionalmente manejar preflight requests (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
