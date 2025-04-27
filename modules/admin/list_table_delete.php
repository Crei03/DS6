<?php
/**
 * Lista de empleados eliminados
 * 
 * Muestra los empleados que han sido eliminados y permite restaurarlos
 */

// Incluir archivos de configuración y clases necesarias
require_once '../../config/config.php';
require_once '../../class/session.php';
require_once '../../components/sidebar_menu.php';
require_once '../../components/modal_result.php';

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
    <title>FormAntro - Empleados Eliminados</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/list_table.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
    <button class="sidebar-toggle" id="sidebar-toggle">
        <span class="material-icons">menu</span>
    </button>

    <!-- Capa semi-transparente para dispositivos móviles -->
    <div class="sidebar-blur" id="sidebar-blur"></div>
    
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('empleados'); 
    ?>
    
    <div class="main-content">
        <div class="table-header">
            <div class="table-title">Empleados Eliminados</div>
            <div class="table-subtitle">Gestione y restaure los empleados que han sido eliminados</div>
            
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
            <tbody id="eliminadosTableBody">
                <!-- Las filas se llenarán por JS -->
            </tbody>
        </table>
        <div class="empty-state" id="emptyState" style="display:none;">
            <span class="material-icons">info</span>
            <p>No hay empleados eliminados para mostrar.</p>
        </div>
    </div>

    <script>
        function mostrarAlerta(mensaje, tipo = 'success') {
            const alertDiv = document.getElementById('alert-container');
            alertDiv.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
            setTimeout(() => { alertDiv.innerHTML = ''; }, 3000);
        }

        async function cargarEmpleadosEliminados() {
            const response = await fetch('../../config/controlador.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'readAll', table: 'e_eliminados' })
            });
            const result = await response.json();
            if (result.status === 'ok' && Array.isArray(result.data)) {
                return result.data;
            }
            return [];
        }

        function renderTablaEliminados(empleados) {
            const tbody = document.getElementById('eliminadosTableBody');
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
                        <button type="button" class="restore-button" data-cedula="${empleado.cedula}">
                            <span class="material-icons">restore</span> Restaurar
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            inicializarEventosRestaurar();
        }

        async function restaurarEmpleado(cedula) {
            if (!confirm('¿Estás seguro que deseas restaurar este empleado?')) return;
            try {
                const response = await fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'restore_employee',
                        table: 'e_eliminados',
                        cedula: cedula
                    })
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    mostrarAlerta('Empleado restaurado correctamente', 'success');
                    const eliminados = await cargarEmpleadosEliminados();
                    renderTablaEliminados(eliminados);
                } else {
                    mostrarAlerta('Error al restaurar: ' + result.message, 'error');
                }
            } catch (err) {
                console.error('Error en la restauración:', err);
                mostrarAlerta('Error al comunicarse con el servidor', 'error');
            }
        }

        function inicializarEventosRestaurar() {
            document.querySelectorAll('.restore-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    restaurarEmpleado(this.dataset.cedula);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', async function() {
            const eliminados = await cargarEmpleadosEliminados();
            renderTablaEliminados(eliminados);

            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                cargarEmpleadosEliminados().then(empleados => {
                    const filtrados = empleados.filter(emp =>
                        emp.cedula.toLowerCase().includes(searchTerm) ||
                        (emp.nombre1 && emp.nombre1.toLowerCase().includes(searchTerm)) ||
                        (emp.apellido1 && emp.apellido1.toLowerCase().includes(searchTerm))
                    );
                    renderTablaEliminados(filtrados);
                });
            });
        });
    </script>
</body>
</html>