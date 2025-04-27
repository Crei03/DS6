<?php
/**
 * Configuración general de la aplicación
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'ds6');
define('DB_PASS', '123');
define('DB_NAME', 'ds6');

// Rutas de la aplicación
define('BASE_URL', '/DS6/');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . BASE_URL);

// Conexión a la base de datos
function conectarBD() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión a la base de datos: " . $conexion->connect_error);
    }
    
    return $conexion;
}

// Cerrar la conexión a la base de datos
function cerrarConexion($conexion) {
    if ($conexion) {
        $conexion->close();
    }
}

// Función para redirigir a otra página
function redirigir($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Función para iniciar sesión
function iniciarSesion($usuario) {
    session_start();
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['cedula'] = $usuario['cedula'];
    $_SESSION['rol'] = $usuario['rol'] ?? 'empleado'; // Valor por defecto: empleado
}

// Función para verificar si el usuario está autenticado
function estaAutenticado() {
    session_start();
    return isset($_SESSION['usuario_id']);
}