<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Inicio de Sesión</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/auth/login.css">
    <style>
        #error-message { display: none; }
        #error-message.active { display: block; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="company-name">FormAntro</div>
        <div id="error-message" class="error-container"></div>
        <form id="loginForm">
            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="solo-numeros" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Entrar</button>
        </form>
    </div>

    <script type="module">
        import { validarCedulaPanama } from '../../config/validation.js';

        document.addEventListener('DOMContentLoaded', () => {
            const cedulaInput = document.getElementById('cedula');
            const passwordInput = document.getElementById('password');
            const errorDiv = document.getElementById('error-message');
            const form = document.getElementById('loginForm');

            cedulaInput.addEventListener('input', () => validarCedulaPanama(cedulaInput));

            function mostrarError(msg) {
                errorDiv.textContent = msg;
                errorDiv.classList.add('active');
            }
            function ocultarError() {
                errorDiv.textContent = '';
                errorDiv.classList.remove('active');
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                ocultarError();

                const cedula = cedulaInput.value.trim();
                const password = passwordInput.value;

                if (!cedula || !password) {
                    mostrarError('Por favor ingresa cédula y contraseña.');
                    return;
                }

                try {
                    const response = await fetch('../../config/controlador.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'authenticate', cedula, password })
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        window.location.href = '../../' + result.redirect;
                    } else {
                        mostrarError(result.message || 'Credenciales incorrectas.');
                    }
                } catch (err) {
                    console.error('Error en autenticación:', err);
                    mostrarError('Error al comunicarse con el servidor.');
                }
            });
        });
    </script>
</body>
</html>