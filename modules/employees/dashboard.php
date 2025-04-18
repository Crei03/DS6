<?php
require_once '../../components/sidebar_menu.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';

$cedula = $sesion->getCedula();
$db = new DBHandler();

$empleado = null;
$res = $db->selectOne('empleados', 'cedula', $cedula);
if ($res['status'] === 'ok') {
    $empleado = $res['data'];
}
$db->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Empleado</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/employees/dashboard.css">
    <link rel="stylesheet" href="../../assets/components/employee_metrics.css">
    <link rel="stylesheet" href="../../assets/components/employee_profile_summary.css">
    <link rel="stylesheet" href="../../assets/components/employee_quick_actions.css">
    <link rel="stylesheet" href="../../assets/components/employee_attendance_chart.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <button class="sidebar-toggle" id="sidebar-toggle">
        <span class="material-icons">menu</span>
    </button>
    <div class="sidebar-blur" id="sidebar-blur"></div>
    <?php renderSidebar('dashboard'); ?>
    <div class="main-content">
        <div class="dashboard-header">
            <div class="dashboard-title">Panel de Empleado</div>
            <div class="dashboard-subtitle">Bienvenido a tu panel personal</div>
        </div>
        <?php include '../../components/employees/employee_quick_actions.php'; ?>
        <?php include '../../components/employees/employee_metrics.php'; ?>
        <?php include '../../components/employees/employee_profile_summary.php'; ?>
        <?php include '../../components/employees/employee_attendance_chart.php'; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarBlur = document.getElementById('sidebar-blur');
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarBlur.classList.toggle('active');
            });
            sidebarBlur.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarBlur.classList.remove('active');
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth > 480) {
                    sidebarBlur.classList.remove('active');
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
