<?php
// Incluir los archivos de configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../class/session.php';

// Inicializar la clase Session
$sesion = new Session();

// Inicializar variables
$errores = [];
$cedula = '';
$password = '';

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar los datos del formulario
    $datos = sanitizarDatosFormulario($_POST);
    $cedula = isset($datos['cedula']) ? trim($datos['cedula']) : ''; // Trim para quitar espacios
    $password = isset($datos['password']) ? $datos['password'] : '';
    
    // Usar la función validarFormatoCedula de validation.php
    if (!validarFormatoCedula($cedula)) {
         $errores[] = "El formato de la cédula debe ser XX-XXXX-XXXXX.";
    } 
    
    // Validar los datos de inicio de sesión (contraseña, etc.)
    $erroresLogin = validarLogin($cedula, $password);
    $errores = array_merge($errores, $erroresLogin);

    // Si no hay errores *después de todas las validaciones*, intentar autenticar al usuario
    if (empty($errores)) {
        $conexion = conectarBD();
        $encontrado = false;

        // 1. Intentar autenticar como administrador primero (tabla usuarios)
        $stmt = $conexion->prepare("SELECT u.cedula, u.contraseña FROM usuarios u WHERE u.cedula = ?");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Verificar la contraseña del administrador
            if ($password === $usuario['contraseña']) {
                // Iniciar sesión como administrador usando la clase Session
                $sesion->iniciarSesion('admin', $usuario['cedula']);
                $sesion->redirigir('../../modules/admin/dashboard.php');
                $encontrado = true;
            } else {    
                $errores[] = "La contraseña es incorrecta";
            }
        }

        // 2. Si no es admin, intentar autenticar como empleado
        if (!$encontrado) {
            $stmt = $conexion->prepare("SELECT e.cedula, e.contraseña FROM empleados e WHERE e.cedula = ?");
            $stmt->bind_param("s", $cedula);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                $empleado = $resultado->fetch_assoc();

                // Verificar la contraseña del empleado
                if ($password === $empleado['contraseña']) {
                    // Iniciar sesión como empleado usando la clase Session
                    $sesion->iniciarSesion('empleado', $empleado['cedula']);
                    $sesion->redirigir('../../modules/employees/dashboard.php');
                    $encontrado = true;
                } else {
                    $errores[] = "La contraseña es incorrecta";
                }
            }
        }

        // 3. Si no se encontró en ninguna tabla
        if (!$encontrado && empty($errores)) {
            $errores[] = "El usuario no existe. Cédula buscada: " . $cedula;
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
        document.addEventListener('DOMContentLoaded', function() {
            var cedulaInput = document.getElementById('cedula');

            cedulaInput.addEventListener('input', function(e) {
                var input = e.target;
                var valor = input.value;
                var cursorPos = input.selectionStart;
                var valorOriginal = valor;

                // Limpiar caracteres no válidos (permitir solo números y guiones)
                var valorLimpio = valor.replace(/[^0-9\-]/g, '');

                // Evitar guiones consecutivos o al inicio/final
                valorLimpio = valorLimpio.replace(/--+/g, '-');
                if (valorLimpio.startsWith('-')) valorLimpio = valorLimpio.substring(1);

                // Dividir por guiones y validar/limitar longitudes
                var partes = valorLimpio.split('-');
                var partesValidas = [];

                for (let i = 0; i < partes.length; i++) {
                    let parte = partes[i];
                    if (i === 0) { // Primera parte (prefijo)
                        // Limitar longitud a 2 dígitos primero
                        if (parte.length > 2) parte = parte.substring(0, 2);
                        // Validar que sea un número entre 1 y 13
                        let numPrefijo = parseInt(parte, 10);
                        if (parte.length > 0 && (isNaN(numPrefijo) || numPrefijo < 1)) {
                             // Si no es número válido o es 0, limpiar
                             parte = '';
                        } else if (numPrefijo > 13) {
                             // Si es mayor que 13, intentar mantener solo el primer dígito si es '1'
                             if (parte.startsWith('1')) {
                                 parte = '1';
                             } else {
                                 // Si empieza con otro número > 1, quitar el último dígito ingresado
                                 parte = parte.substring(0, parte.length - 1);
                                 // Revalidar por si queda 0
                                 numPrefijo = parseInt(parte, 10);
                                 if (isNaN(numPrefijo) || numPrefijo < 1) {
                                     parte = '';
                                 }
                             }
                        }
                    } else if (i === 1) { // Segunda parte (tomo)
                        if (parte.length > 4) parte = parte.substring(0, 4);
                    } else if (i === 2) { // Tercera parte (asiento)
                        if (parte.length > 5) parte = parte.substring(0, 5);
                    } else { // No permitir más de 3 partes
                        break;
                    }
                    partesValidas.push(parte);
                }

                valorLimpio = partesValidas.join('-');

                // Actualizar el valor del input si cambió
                if (input.value !== valorLimpio) {
                    input.value = valorLimpio;

                    // Ajustar posición del cursor
                    var diff = valorOriginal.length - valorLimpio.length;
                    var newCursorPos = cursorPos - diff;
                    // Intentar mantener el cursor al final si se está añadiendo texto
                    // y el cambio no fue solo por limitación de longitud
                    if (cursorPos === valorOriginal.length && valorLimpio.length >= valorOriginal.length) {
                         newCursorPos = valorLimpio.length;
                    }
                    input.setSelectionRange(Math.max(0, newCursorPos), Math.max(0, newCursorPos));
                }
            });

        });
    </script>
</body>
</html>