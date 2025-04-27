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
<div class="employee-metrics-container">
    <div class="employee-metrics-row">
        <div class="metric-card days-worked">
            <span class="metric-icon"><span class="material-icons">date_range</span></span>
            <div class="metric-content">
                <div class="metric-value"><?php echo $diasTrabajados; ?></div>
                <div class="metric-title">Días trabajados</div>
            </div>
        </div>
        <div class="metric-card department">
            <span class="metric-icon"><span class="material-icons">business</span></span>
            <div class="metric-content">
                <div class="metric-value"><?php echo htmlspecialchars($departamento); ?></div>
                <div class="metric-title">Departamento</div>
            </div>
        </div>
    </div>
    <div class="employee-metrics-row">
        <div class="metric-card role">
            <span class="metric-icon"><span class="material-icons">badge</span></span>
            <div class="metric-content">
                <div class="metric-value"><?php echo htmlspecialchars($cargo); ?></div>
                <div class="metric-title">Cargo</div>
            </div>
        </div>
        <div class="metric-card status">
            <span class="metric-icon"><span class="material-icons">verified_user</span></span>
            <div class="metric-content">
                <div class="metric-value"><?php echo $estado; ?></div>
                <div class="metric-title">Estado</div>
            </div>
        </div>
    </div>
</div>
