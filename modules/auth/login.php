<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Inicio de Sesión</title>
    <link rel="stylesheet" href="../../assets/auth/login.css">
</head>
<body>
    <div class="login-container">
        <div class="company-name">FormAntro</div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Entrar</button>
            
            <a href="#" class="forgot-password">Recuperar contraseña</a>
        </form>
    </div>

    <script>
        // Script para manejar el envío del formulario
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Obtener los valores de los campos
            const cedula = document.getElementById('cedula').value;
            const password = document.getElementById('password').value;
            
            // Aquí se enviarían los datos a un backend para autenticación
            console.log('Cédula:', cedula);
            console.log('Contraseña:', password);
            
            // Estructura preparada para cuando tengamos la ruta al backend
            /*
            fetch('../../api/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cedula: cedula,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirección en caso de éxito
                    window.location.href = '../dashboard/index.php';
                } else {
                    // Mostrar mensaje de error
                    alert(data.message || 'Error de autenticación');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
            */
        });
    </script>
</body>
</html>