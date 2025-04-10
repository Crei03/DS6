<?php
// Incluir los archivos de configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';

// Inicializar variables
$errores = [];
$cedula = '';
$password = '';

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $datos = sanitizarDatosFormulario($_POST);
    $cedula = isset($datos['cedula']) ? $datos['cedula'] : '';
    $password = isset($datos['password']) ? $datos['password'] : '';
    
    // Validar los datos de inicio de sesión
    $errores = validarLogin($cedula, $password);
    
    // Limpiar la cédula para que solo contenga números
    $cedulaLimpia = validarSoloNumeros($cedula);
    
    // Si no hay errores, intentar autenticar al usuario
    if (empty($errores)) {
        $conexion = conectarBD();
        
        // Consultar el usuario en la base de datos
        $stmt = $conexion->prepare("SELECT u.id, u.cedula, u.contraseña FROM usuarios u WHERE u.cedula = ?");
        $stmt->bind_param("s", $cedulaLimpia);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Verificar la contraseña (en un sistema real, deberías usar password_verify)
            if ($password === $usuario['contraseña']) {
                // Iniciar sesión y redirigir
                iniciarSesion($usuario);
                redirigir('modules/dashboard/index.php');
            } else {
                $errores[] = "La contraseña es incorrecta";
            }
        } else {
            $errores[] = "El usuario no existe";
        }
        
        cerrarConexion($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Inicio de Sesión</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/auth/login.css">
</head>
<body>
    <div class="login-container">
        <div class="company-name">FormAntro</div>
        
        <?php if (!empty($errores)): ?>
        <div class="error-container">
            <ul>
                <?php foreach ($errores as $error): ?>
                <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="solo-numeros" 
                      value="<?php echo htmlspecialchars($cedula); ?>" required>
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
        // Script para manejar la validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el campo de cédula
            var cedulaInput = document.getElementById('cedula');
            
            // Añadir evento para validar en tiempo real
            cedulaInput.addEventListener('input', function() {
                var valor = this.value;
                
                // Eliminar caracteres no numéricos
                var soloNumeros = valor.replace(/[^0-9]/g, '');
                
                // Actualizar el valor del campo
                if (valor !== soloNumeros) {
                    this.value = soloNumeros;
                }
            });
            
            // Manejar el envío del formulario
            document.getElementById('loginForm').addEventListener('submit', function(event) {
                var cedula = cedulaInput.value.trim();
                
                // Validación básica antes de enviar
                if (cedula.length < 8 || cedula.length > 13) {
                    event.preventDefault();
                    alert('La cédula debe tener entre 8 y 13 dígitos.');
                }
            });
        });
    </script>
</body>
</html>