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
require_once '../../class/session.php';

// Inicializar la sesión
$sesion = new Session();

class menuOption {
    public function __construct(
        public string $icon,
        public string $text,
        public string $link,
        public string $id 
    ) {}
}

function renderSidebar($activeMenu = '') {
    global $sesion;
    
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
                
    // Mostrar la cédula del usuario como nombre por ahora
    echo $sesion->getUsuario() ?: 'Usuario';
    
    echo '</div>
                <div class="user-role">';
    
    // Mostrar el rol del usuario desde la sesión
    $tipoUsuario = $sesion->getTipoUsuario();
    echo $tipoUsuario === 'admin' ? 'Administrador' : 'Empleado';
    
    echo '</div>
            </div>
        </div>
        
        <div class="menu-container">';
    
    
    $menuOptions = [];  
    
    // Opciones dependiendo del tipo de usuario
    if ($sesion->esAdmin()) {
        // Opciones para administradores
        $menuOptions[] = new menuOption( 
            icon: 'dashboard',
            text: 'Dashboard',
            link: '../admin/dashboard.php',
            id: 'dashboard'
        );
        
        $menuOptions[] = new menuOption( 
            icon: 'people',
            text: 'Empleados',
            link: '../admin/list_table.php',
            id: 'empleados'
        );

        $menuOptions[] = new menuOption( 
            icon: 'add',
            text: 'Agregar Empleado',
            link: '../admin/employee_add.php',
            id: 'employee_add'      
        );
        
    } elseif ($sesion->esEmpleado()) {
        // Opciones para empleados
        $menuOptions[] = new menuOption(
            icon: 'dashboard',
            text: 'Dashboard',
            link: '../employees/dashboard.php',
            id: 'dashboard'
        );
        
        $menuOptions[] = new menuOption( 
            icon: 'person',
            text: 'Mi Perfil',
            link: '../employees/my_profile.php',
            id: 'my_profile'
        );

        
        // Otras opciones específicas para empleados si son necesarias
    }
    
    
    // Renderizar cada opción del menú
    foreach ($menuOptions as $option) {
        $isActive = ($activeMenu === $option->id); 
        renderOptionButton($option->icon, $option->text, $option->link, $isActive);
    }
    
    // Opción para cerrar sesión
    echo '
        </div>
        
        <div class="logout-container">';
    renderOptionButton('exit_to_app', 'Cerrar Sesión', '../auth/login.php');
    echo '
        </div>
    </div>
    
    ';
}
?>