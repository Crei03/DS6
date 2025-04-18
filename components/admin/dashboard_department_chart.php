<?php
// Componente: Gráfico de empleados por departamento (solo conteo, sin JS externo)
require_once '../../config/BdHandler.php';

$db = new DBHandler();
$resEmpleados = $db->selectAll('empleados');
$resDepartamentos = $db->selectAll('departamento');
$db->close();

$departamentos = [];
if ($resDepartamentos['status'] === 'ok') {
    foreach ($resDepartamentos['data'] as $dep) {
        $departamentos[$dep['codigo']] = [
            'nombre' => $dep['nombre'],
            'total' => 0
        ];
    }
}
if ($resEmpleados['status'] === 'ok') {
    foreach ($resEmpleados['data'] as $emp) {
        if ((isset($emp['estado']) ? $emp['estado'] == 1 : true) && isset($departamentos[$emp['departamento']])) {
            $departamentos[$emp['departamento']]['total']++;
        }
    }
}

// Procesar empleados activos para estadísticas de género y edad
$hombres = 0;
$mujeres = 0;
$adulto_joven = 0; // 18-35
$adulto_maduro = 0; // 36-59
$adulto_mayor = 0; // 60+
$hoy = date('Y-m-d');
if ($resEmpleados['status'] === 'ok') {
    foreach ($resEmpleados['data'] as $emp) {
        if (isset($emp['estado']) && $emp['estado'] != 1) continue;
        // Género
        if (isset($emp['genero'])) {
            if ((int)($emp['genero']) === 0) $hombres++;
            elseif ((int)($emp['genero']) === 1) $mujeres++;
        }
        // Edad
        if (isset($emp['f_nacimiento'])) {
            $edad = (int)date_diff(date_create($emp['f_nacimiento']), date_create($hoy))->y;
            if ($edad >= 18 && $edad <= 35) $adulto_joven++;
            elseif ($edad >= 36 && $edad <= 59) $adulto_maduro++;
            elseif ($edad >= 60) $adulto_mayor++;
        }
    }
}
?>
<div class="dashboard-department-chart">
    <div class="chart-bars">
        <div class="chart-title">Empleados por departamento</div>
        <div class="chart-bars">
            <?php foreach ($departamentos as $dep): ?>
                <div class="chart-bar-row">
                    <span class="bar-label"><?php echo htmlspecialchars($dep['nombre']); ?></span>
                    <div class="bar-outer">
                        <div class="bar-inner" style="width: <?php echo ($dep['total'] > 0 ? min($dep['total'] * 18, 220) : 8); ?>px;"></div>
                    </div>
                    <span class="bar-value"><?php echo $dep['total']; ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($departamentos)): ?>
                <div class="text-center">No hay datos de departamentos.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="dashboard-extra-stats">
        <div class="gender-stats-row">
            <div class="gender-stat masculino">
                <span class="material-icons">male</span>
                Hombres
                <span class="extra-value"><?php echo $hombres; ?></span>
            </div>
            <div class="gender-stat femenino">
                <span class="material-icons">female</span>
                Mujeres
                <span class="extra-value"><?php echo $mujeres; ?></span>
            </div>
        </div>
        <div class="yearOld-stats-row">
            <?php $totalEdad = $adulto_joven + $adulto_maduro + $adulto_mayor;
            function percent($val, $total) { return $total > 0 ? round($val * 100 / $total) : 0; }
            ?>
            <div class="yearOld-bar">
                <span class="yearOld-label">Adulto joven (18-35)</span>
                <div class="yearOld-bar-outer">
                    <div class="yearOld-bar-inner" style="width: <?php echo percent($adulto_joven, $totalEdad) * 1.8; ?>px;"></div>
                </div>
                <span class="yearOld-value"><?php echo $adulto_joven; ?> (<?php echo percent($adulto_joven, $totalEdad); ?>%)</span>
            </div>
            <div class="yearOld-bar">
                <span class="yearOld-label">Adulto maduro (36-59)</span>
                <div class="yearOld-bar-outer">
                    <div class="yearOld-bar-inner" style="width: <?php echo percent($adulto_maduro, $totalEdad) * 1.8; ?>px;"></div>
                </div>
                <span class="yearOld-value"><?php echo $adulto_maduro; ?> (<?php echo percent($adulto_maduro, $totalEdad); ?>%)</span>
            </div>
            <div class="yearOld-bar">
                <span class="yearOld-label">Adulto mayor (60+)</span>
                <div class="yearOld-bar-outer">
                    <div class="yearOld-bar-inner" style="width: <?php echo percent($adulto_mayor, $totalEdad) * 1.8; ?>px;"></div>
                </div>
                <span class="yearOld-value"><?php echo $adulto_mayor; ?> (<?php echo percent($adulto_mayor, $totalEdad); ?>%)</span>
            </div>
        </div>
    </div>
</div>
