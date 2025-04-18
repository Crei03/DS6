<?php
// Componente: Métricas personales del empleado
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';

$sesion = new Session();
$cedula = $sesion->getCedula();
$db = new DBHandler();

// Obtener datos del empleado
$empleado = null;
$res = $db->selectOne('empleados', 'cedula', $cedula);
if ($res['status'] === 'ok') {
    $empleado = $res['data'];
}

// Días trabajados (desde fecha de ingreso)
$diasTrabajados = 0;
if ($empleado && isset($empleado['f_contra'])) {
    $fechaIngreso = new DateTime($empleado['f_contra']);
    $hoy = new DateTime();
    $diasTrabajados = $fechaIngreso->diff($hoy)->days;
}

// Días de ausencia (placeholder, requiere tabla de asistencias)
$diasAusencia = '-';
// Si tienes una tabla de asistencias, aquí puedes calcularlo

// Departamento y cargo
$departamento = '-';
$cargo = '-';
if ($empleado) {
    // Obtener nombre del departamento
    $depRes = $db->selectOne('departamento', 'codigo', $empleado['departamento']);
    if ($depRes['status'] === 'ok') $departamento = $depRes['data']['nombre'];
    // Obtener nombre del cargo
    $cargoRes = $db->selectOne('cargo', 'codigo', $empleado['cargo']);
    if ($cargoRes['status'] === 'ok') $cargo = $cargoRes['data']['nombre'];
}

// Estado
$estado = ($empleado && isset($empleado['estado']) && $empleado['estado'] == 1) ? 'Activo' : 'Inactivo';
$db->close();
?>
<div class="employee-metrics-cards">
    <div class="card">
        <div class="card-title">Días trabajados</div>
        <div class="card-value"><?php echo $diasTrabajados; ?></div>
    </div>
    <div class="card">
        <div class="card-title">Departamento</div>
        <div class="card-value"><?php echo htmlspecialchars($departamento); ?></div>
    </div>
    <div class="card">
        <div class="card-title">Cargo</div>
        <div class="card-value"><?php echo htmlspecialchars($cargo); ?></div>
    </div>
    <div class="card">
        <div class="card-title">Estado</div>
        <div class="card-value"><?php echo $estado; ?></div>
    </div>
</div>
