<?php
// Incluir archivos de configuración
require_once '../../config/config.php';
require_once '../../class/session.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Obtener la cédula del administrador
$cedula = $sesion->getCedula();

// Incluir el componente del sidebar
require_once '../../components/sidebar_menu.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Dashboard</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/dashboard.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
    <button class="sidebar-toggle" id="sidebar-toggle">
        <span class="material-icons">menu</span>
    </button>

    <!-- Capa semi-transparente para dispositivos móviles -->
    <div class="sidebar-blur" id="sidebar-blur"></div>
    
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('dashboard'); 
    ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div class="dashboard-title">Panel de Control</div>
            <div class="dashboard-subtitle">Bienvenido al sistema de gestión de empleados</div>
        </div>
        
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-title">Total de Empleados</div>
                <div class="card-value">
                    <?php
                    // En un sistema real, esto vendría de la base de datos
                    echo '42';
                    ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">Departamentos</div>
                <div class="card-value">
                    <?php
                    // En un sistema real, esto vendría de la base de datos
                    echo '8';
                    ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">Nuevos este mes</div>
                <div class="card-value">
                    <?php
                    // En un sistema real, esto vendría de la base de datos
                    echo '5';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funcionalidad del sidebar responsive
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarBlur = document.getElementById('sidebar-blur');
            
            // Función para mostrar/ocultar el sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarBlur.classList.toggle('active');
            });
            
            // Cerrar el sidebar al hacer clic en el área semi-transparente
            sidebarBlur.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarBlur.classList.remove('active');
            });
            
            // Ajustar la visualización en cambios de tamaño de ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth > 480) {
                    sidebarBlur.classList.remove('active');
                    // En pantallas mayores a 480px, el sidebar siempre es visible
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                    } else {
                        sidebar.classList.add('active');
                    }
                }
            });
        });
    </script>
</body>
</html>