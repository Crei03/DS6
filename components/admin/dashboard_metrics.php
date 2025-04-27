<?php
// Componente: Métricas del dashboard (total empleados, departamentos, nuevos este mes)
require_once '../../config/BdHandler.php';

$db = new DBHandler();

// Total de empleados activos
$resEmpleados = $db->selectAll('empleados');
$totalEmpleados = 0;
$totalInactivos = 0;
if ($resEmpleados['status'] === 'ok') {
    $totalEmpleados = count(array_filter($resEmpleados['data'], function($e) {
        return isset($e['estado']) ? $e['estado'] == 1 : true;
    }));
    $totalInactivos = count(array_filter($resEmpleados['data'], function($e) {
        return isset($e['estado']) ? $e['estado'] == 0 : false;
    }));
}

// Total de empleados eliminados
$resEliminados = $db->selectAll('e_eliminados');
$totalEliminados = $resEliminados['status'] === 'ok' ? count($resEliminados['data']) : 0;

// Nuevos empleados este mes
$nuevosEsteMes = 0;
if ($resEmpleados['status'] === 'ok') {
    $mesActual = date('Y-m');
    $nuevosEsteMes = count(array_filter($resEmpleados['data'], function($e) use ($mesActual) {
        return isset($e['f_contra']) && strpos($e['f_contra'], $mesActual) === 0 && (isset($e['estado']) ? $e['estado'] == 1 : true);
    }));
}
$db->close();
?>
<div class="admin-metrics-container">
    <div class="admin-metrics-row">
        <div class="admin-metric-card total-employees">
            <span class="admin-metric-icon"><span class="material-icons">people</span></span>
            <div class="admin-metric-content">
                <div class="admin-metric-value"><?php echo $totalEmpleados; ?></div>
                <div class="admin-metric-title">Total de Empleados</div>
            </div>
        </div>
        <div class="admin-metric-card inactive-employees">
            <span class="admin-metric-icon"><span class="material-icons">person_off</span></span>
            <div class="admin-metric-content">
                <div class="admin-metric-value"><?php echo $totalInactivos; ?></div>
                <div class="admin-metric-title">Empleados Inactivos</div>
            </div>
        </div>
    </div>
    <div class="admin-metrics-row">
        <div class="admin-metric-card deleted-employees">
            <span class="admin-metric-icon"><span class="material-icons">delete_sweep</span></span>
            <div class="admin-metric-content">
                <div class="admin-metric-value"><?php echo $totalEliminados; ?></div>
                <div class="admin-metric-title">Empleados Eliminados</div>
            </div>
        </div>
        <div class="admin-metric-card new-employees">
            <span class="admin-metric-icon"><span class="material-icons">person_add</span></span>
            <div class="admin-metric-content">
                <div class="admin-metric-value"><?php echo $nuevosEsteMes; ?></div>
                <div class="admin-metric-title">Nuevos este mes</div>
            </div>
        </div>
    </div>
</div>
