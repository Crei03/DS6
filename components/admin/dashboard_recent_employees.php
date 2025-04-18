<?php
// Componente: Últimos empleados agregados
require_once '../../config/BdHandler.php';

$db = new DBHandler();
$resEmpleados = $db->selectAll('empleados');
$empleados = [];
if ($resEmpleados['status'] === 'ok') {
    // Solo empleados activos y ordenados por fecha_ingreso descendente
    $empleados = array_filter($resEmpleados['data'], function($e) {
        return isset($e['estado']) ? $e['estado'] == 1 : true;
    });
    usort($empleados, function($a, $b) {
        return strcmp($b['f_contra'], $a['f_contra']);
    });
    $empleados = array_slice($empleados, 0, 5);
}
// Obtener departamentos para mostrar el nombre
$resDepartamentos = $db->selectAll('departamento');
$departamentos = [];
if ($resDepartamentos['status'] === 'ok') {
    foreach ($resDepartamentos['data'] as $dep) {
        $departamentos[$dep['codigo']] = $dep['nombre'];
    }
}
$db->close();
?>
<div class="dashboard-recent-employees">
    <div class="recent-title">Últimos empleados agregados</div>
    <table class="recent-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Fecha de ingreso</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($empleados as $emp): ?>
            <tr>
                <td><?php echo htmlspecialchars($emp['nombre1'] . ' ' . $emp['apellido1']); ?></td>
                <td><?php echo isset($departamentos[$emp['departamento']]) ? htmlspecialchars($departamentos[$emp['departamento']]) : '-'; ?></td>
                <td><?php echo htmlspecialchars($emp['f_contra']); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($empleados)): ?>
            <tr><td colspan="3" class="text-center">No hay empleados recientes.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
