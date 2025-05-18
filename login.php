<!-- public/login.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login API</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            
            <div class="text-center mb-4">
                <img src="/src/img/cine1.png" alt="Cine Halconez" class="rounded-circle border" style="max-width: 150px; width: 100%; height: auto;">
            </div>
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" id="correo" class="form-control" placeholder="Correo" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" id="contrasena" class="form-control" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
            <div id="mensaje" class="mt-3 text-center"></div>
        </div>
    </div>
    <!-- Bootstrap JS (opcional, solo si usas componentes JS de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const correo = document.getElementById('correo').value;
            const contrasena = document.getElementById('contrasena').value;

            const res = await fetch('/src/auth/login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ correo, contrasena })
            });

            const data = await res.json();
            document.getElementById('mensaje').textContent = data.message;

            if (data.status === 'success') {
                window.location.href = ""; // Redirigir a la página principal
            }
        });
    </script>
</body>
</html>
