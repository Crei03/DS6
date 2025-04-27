<?php
/**
 * Lista de empleados inactivos
 * 
 * Muestra los empleados que están inactivos (estado = 0) y permite activarlos
 */

// Incluir archivos de configuración y clases necesarias
require_once '../../config/config.php';
require_once '../../class/session.php';
require_once '../../components/sidebar_menu.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Empleados Inactivos</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/list_table.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
    <button class="sidebar-toggle" id="sidebar-toggle">
        <span class="material-icons">menu</span>
    </button>
    <div class="sidebar-blur" id="sidebar-blur"></div>
    <?php renderSidebar('empleados'); ?>
    <div class="main-content">
        <div class="table-header">
            <div class="table-title">Empleados Inactivos</div>
            <div class="table-subtitle">Gestione y active los empleados que están en estado inactivo</div>
            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="Buscar por cédula, nombre o apellido...">
                <button class="search-button"><span class="material-icons">search</span></button>
                <a href="list_table.php" class="back-button"><span class="material-icons">arrow_back</span> Volver a Empleados</a>
            </div>
        </div>
        <div id="alert-container"></div>
        <table class="employee-table">
            <thead>
                <tr>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nacionalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="inactivosTableBody">
                <!-- Las filas se llenarán por JS -->
            </tbody>
        </table>
        <div class="empty-state" id="emptyState">
            <span class="material-icons">info</span>
            <p>No hay empleados inactivos para mostrar.</p>
        </div>
    </div>
    <script>
    function mostrarAlerta(mensaje, tipo = 'success') {
        const alertDiv = document.getElementById('alert-container');
        alertDiv.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
        setTimeout(() => { alertDiv.innerHTML = ''; }, 3000);
    }
    async function cargarEmpleadosInactivos() {
        const response = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'readAll', table: 'empleados' })
        });
        const result = await response.json();
        if (result.status === 'ok' && Array.isArray(result.data)) {
            return result.data.filter(emp => emp.estado == 0);
        }
        return [];
    }
    function renderTablaInactivos(empleados) {
        const tbody = document.getElementById('inactivosTableBody');
        const emptyState = document.getElementById('emptyState');
        tbody.innerHTML = '';
        if (empleados.length === 0) {
            emptyState.style.display = '';
            return;
        } else {
            emptyState.style.display = 'none';
        }
        empleados.forEach(empleado => {
            const tr = document.createElement('tr');
            tr.className = 'employee-row';
            tr.innerHTML = `
                <td>${empleado.cedula}</td>
                <td>${empleado.nombre1}</td>
                <td>${empleado.apellido1}</td>
                <td>${empleado.nacionalidad || ''}</td>
                <td>
                    <button type="button" class="activate-button" data-cedula="${empleado.cedula}">
                        <span class="material-icons">check_circle</span> Activar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        inicializarEventosActivar();
    }
    async function activarEmpleado(cedula) {
        if (!confirm('¿Está seguro que desea activar este empleado?')) return;
        const response = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update',
                table: 'empleados',
                id: cedula,
                data: { estado: 1 }
            })
        });
        const result = await response.json();
        if (result.status === 'ok') {
            mostrarAlerta('Empleado activado correctamente', 'success');
            const inactivos = await cargarEmpleadosInactivos();
            renderTablaInactivos(inactivos);
        } else {
            mostrarAlerta('Error al activar el empleado', 'error');
        }
    }
    function inicializarEventosActivar() {
        document.querySelectorAll('.activate-button').forEach(btn => {
            btn.addEventListener('click', function() {
                activarEmpleado(this.dataset.cedula);
            });
        });
    }
    document.addEventListener('DOMContentLoaded', async function() {
        const inactivos = await cargarEmpleadosInactivos();
        renderTablaInactivos(inactivos);
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            cargarEmpleadosInactivos().then(empleados => {
                const filtrados = empleados.filter(emp =>
                    emp.cedula.toLowerCase().includes(searchTerm) ||
                    (emp.nombre1 && emp.nombre1.toLowerCase().includes(searchTerm)) ||
                    (emp.apellido1 && emp.apellido1.toLowerCase().includes(searchTerm))
                );
                renderTablaInactivos(filtrados);
            });
        });
    });
    </script>
</body>
</html>