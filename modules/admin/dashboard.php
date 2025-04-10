<?php
// Incluir archivos de configuración
require_once '../../config/config.php';

// // Verificar si el usuario está autenticado
// if (!isset($_SESSION)) {
//     session_start();
// }

// // Si el usuario no está autenticado, redirigir al login
// if (!isset($_SESSION['usuario_id'])) {
//     redirigir('auth/login.php');
// }

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
</body>
</html>