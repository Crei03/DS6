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

// Total de departamentos
$resDepartamentos = $db->selectAll('departamento');
$totalDepartamentos = $resDepartamentos['status'] === 'ok' ? count($resDepartamentos['data']) : 0;

// Nuevos empleados este mes
$nuevosEsteMes = 0;
if ($resEmpleados['status'] === 'ok') {
    $mesActual = date('Y-m');
    $nuevosEsteMes = count(array_filter($resEmpleados['data'], function($e) use ($mesActual) {
        return isset($e['fecha_ingreso']) && strpos($e['fecha_ingreso'], $mesActual) === 0 && (isset($e['estado']) ? $e['estado'] == 1 : true);
    }));
}
$db->close();
?>
<div class="dashboard-cards dashboard-metrics">
    <div class="card">
        <div class="card-title">Total de Empleados</div>
        <div class="card-value"><?php echo $totalEmpleados; ?></div>
    </div>
    <div class="card">
        <div class="card-title">Empleados Inactivos</div>
        <div class="card-value inactivos"><?php echo $totalInactivos; ?></div>
    </div>
    <div class="card">
        <div class="card-title">Departamentos</div>
        <div class="card-value"><?php echo $totalDepartamentos; ?></div>
    </div>
</div>
