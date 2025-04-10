<?php
/**
 * Componente Sidebar Menu
 * 
 * Este componente genera el menú lateral de navegación
 * 
 * @param string $activeMenu Identifica la opción activa en el menú
 */

// Incluir el componente de botones de opciones
require_once 'options_button.php';

function renderSidebar($activeMenu = '') {
    // Incluir las hojas de estilo necesarias para el sidebar
    echo '
    
    <link rel="stylesheet" href="../../assets/components/sidebar_menu.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <div class="sidebar">
        <div class="logo-container">
            <div class="company-logo">FormAntro</div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <span class="material-icons">account_circle</span>
            </div>
            <div class="user-details">
                <div class="user-name">';
                
    // Mostrar el nombre del usuario desde la sesión si está disponible
    if (isset($_SESSION['nombre']) && isset($_SESSION['apellido'])) {
        echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido'];
    } else {
        echo 'Usuario';
    }
    
    echo '</div>
                <div class="user-role">';
    
    // Mostrar el rol del usuario desde la sesión si está disponible
    if (isset($_SESSION['rol'])) {
        echo $_SESSION['rol'] === 'admin' ? 'Administrador' : 'Empleado';
    } else {
        echo 'Rol no definido';
    }
    
    echo '</div>
            </div>
        </div>
        
        <div class="menu-container">';
    
    // Definir las opciones del menú según el rol
    $menuOptions = [];
    
    // Opciones comunes para todos los usuarios
    $menuOptions[] = [
        'icon' => 'home',
        'text' => 'Inicio',
        'link' => '../admin/dashboard.php',
        'id' => 'dashboard'
    ];
    
    // Opciones solo para administradores
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        $menuOptions[] = [
            'icon' => 'people',
            'text' => 'Empleados',
            'link' => '../admin/list_table.php',
            'id' => 'empleados'
        ];
        
        $menuOptions[] = [
            'icon' => 'settings',
            'text' => 'Configuración',
            'link' => '../admin/config.php',
            'id' => 'config'
        ];
    }
    
    // Opciones para todos los usuarios
    $menuOptions[] = [
        'icon' => 'person',
        'text' => 'Mi Perfil',
        'link' => '../perfil/index.php',
        'id' => 'perfil'
    ];
    
    // Renderizar cada opción del menú
    foreach ($menuOptions as $option) {
        $isActive = ($activeMenu === $option['id']);
        renderOptionButton($option['icon'], $option['text'], $option['link'], $isActive);
    }
    
    // Opción para cerrar sesión
    echo '
        </div>
        
        <div class="logout-container">';
    renderOptionButton('exit_to_app', 'Cerrar Sesión', '../auth/logout.php');
    echo '
        </div>
    </div>
    
    ';
}
?>