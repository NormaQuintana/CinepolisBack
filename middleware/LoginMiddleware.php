<?php
require_once __DIR__ . '/../config/database.php'; 
 
class LoginController {
    public static function login($correo, $contrasena) {
        global $conn;
        session_start();

        $correo = mysqli_real_escape_string($conn, $correo);
        $contrasena = mysqli_real_escape_string($conn, $contrasena);

        $sql = "SELECT * FROM usuario WHERE correo = '$correo'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verificar contraseña (idealmente debería estar hasheada con password_verify)
            if ($usuario['contrasena'] === $contrasena) {
                $_SESSION['usuario'] = [
                    'id' => $usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'correo' => $usuario['correo'],
                ];

                echo json_encode([
                    "status" => "success",
                    "message" => "Inicio de sesión exitoso"
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
        }
    }

    public static function logout() {
        session_start();
        session_destroy();
        echo json_encode(["status" => "success", "message" => "Sesión cerrada"]);
    }
}
