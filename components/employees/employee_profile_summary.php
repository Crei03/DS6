<?php
// Componente: Resumen de perfil del empleado
require_once  '../../config/BdHandler.php';
require_once  '../../class/session.php';

$sesion = new Session();
$cedula = $sesion->getCedula();
$db = new DBHandler();

$empleado = null;
$res = $db->selectOne('empleados', 'cedula', $cedula);
if ($res['status'] === 'ok') {
    $empleado = $res['data'];
}
$db->close();
?>
<div class="employee-profile-summary card">
    <div class="profile-row"><span class="profile-label">Nombre:</span> <span><?php echo htmlspecialchars(($empleado['nombre1'] ?? '') . ' ' . ($empleado['apellido'] ?? '')); ?></span></div>
    <div class="profile-row"><span class="profile-label">Cédula:</span> <span><?php echo htmlspecialchars($empleado['cedula'] ?? ''); ?></span></div>
    <div class="profile-row"><span class="profile-label">Correo:</span> <span><?php echo htmlspecialchars($empleado['correo'] ?? '-'); ?></span></div>
    <div class="profile-row"><span class="profile-label">Fecha de ingreso:</span> <span><?php echo htmlspecialchars($empleado['f_contra'] ?? '-'); ?></span></div>
</div>
