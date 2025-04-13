<?php
/**
 * Clase para gestionar las sesiones de usuario
 * 
 * Esta clase proporciona métodos para:
 * - Iniciar sesión
 * - Verificar si el usuario está autenticado
 * - Verificar el tipo de usuario
 * - Obtener datos del usuario en sesión
 * - Cerrar sesión
 */
class Session {
    /**
     * Constructor - Inicia la sesión si no está iniciada
     */
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Inicia la sesión del usuario
     * 
     * @param string $tipo_usuario Tipo de usuario (admin, empleado, etc)
     * @param string $cedula Cédula del usuario
     * @return bool True si la sesión se inició correctamente
     */
    public function iniciarSesion($tipo_usuario, $cedula) {
        $_SESSION['usuario'] = $cedula; 
        $_SESSION['tipo_usuario'] = $tipo_usuario;
        $_SESSION['cedula'] = $cedula;
        $_SESSION['tiempo_inicio'] = time();
        
        return isset($_SESSION['usuario']);
    }
    
    /**
     * Verifica si el usuario está autenticado
     * 
     * @return bool True si el usuario está autenticado
     */
    public function estaAutenticado() {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }
    
    /**
     * Verifica si el usuario es de tipo admin
     * 
     * @return bool True si el usuario es admin
     */
    public function esAdmin() {
        return $this->estaAutenticado() && $_SESSION['tipo_usuario'] === 'admin';
    }
    
    /**
     * Verifica si el usuario es de tipo empleado
     * 
     * @return bool True si el usuario es empleado
     */
    public function esEmpleado() {
        return $this->estaAutenticado() && $_SESSION['tipo_usuario'] === 'empleado';
    }
    
    /**
     * Obtiene la cédula del usuario en sesión
     * 
     * @return string La cédula del usuario o null si no está en sesión
     */
    public function getCedula() {
        return isset($_SESSION['cedula']) ? $_SESSION['cedula'] : null;
    }
    
    /**
     * Obtiene el nombre de usuario en sesión
     * 
     * @return string El nombre de usuario o null si no está en sesión
     */
    public function getUsuario() {
        return isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
    }
    
    /**
     * Obtiene el tipo de usuario en sesión
     * 
     * @return string El tipo de usuario o null si no está en sesión
     */
    public function getTipoUsuario() {
        return isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;
    }
    
    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     * 
     * @param string $tipo_requerido Tipo de usuario requerido para acceder al módulo
     * @return bool True si el usuario tiene acceso
     */
    public function verificarAcceso($tipo_requerido) {
        if (!$this->estaAutenticado()) {
            return false;
        }
        
        if ($tipo_requerido === 'admin') {
            return $this->esAdmin();
        } elseif ($tipo_requerido === 'empleado') {
            return $this->esEmpleado();
        }
        
        return false;
    }
    
    /**
     * Redirige al usuario a una página específica
     * 
     * @param string $url URL a la que se redirigirá
     */
    public function redirigir($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function cerrarSesion() {
        // Eliminar todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea destruir completamente la sesión, eliminar también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
    }
}
?>