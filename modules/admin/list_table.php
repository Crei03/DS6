<?php
require_once '../../config/config.php';
require_once '../../class/session.php';

$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}
require_once '../../components/sidebar_menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Listado de Empleados</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/list_table.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <button class="sidebar-toggle" id="sidebar-toggle">
        <span class="material-icons">menu</span>
    </button>
    <div class="sidebar-blur" id="sidebar-blur"></div>
    <?php renderSidebar('empleados'); ?>
    <div class="main-content">
        <div class="table-header">
            <div class="table-title">Listado de Empleados</div>
            <div class="table-subtitle">Gestione la información de los empleados de la empresa</div>
            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="Buscar por cédula, nombre o apellido...">
                <button class="search-button"><span class="material-icons">search</span></button>
                <a href="employee_add.php" class="add-button"><span class="material-icons">add</span> Agregar</a>
                <a href="list_table_inactive.php" class="quick-inactive"><span class="material-icons">block</span> Ver Inactivos</a>
                <a href="list_table_delete.php" class="quick-delete"><span class="material-icons">delete</span> Ver Eliminados</a>
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
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <!-- Las filas se llenarán por JS -->
            </tbody>
        </table>
        <div class="pagination" id="paginationContainer">
            <button class="pagination-button" id="firstPage"><span class="material-icons">first_page</span></button>
            <button class="pagination-button" id="prevPage"><span class="material-icons">chevron_left</span></button>
            <div id="pageNumbers" class="page-numbers"></div>
            <button class="pagination-button" id="nextPage"><span class="material-icons">chevron_right</span></button>
            <button class="pagination-button" id="lastPage"><span class="material-icons">last_page</span></button>
        </div>
    </div>
    <script>
    // Cargar catálogos desde el backend
    async function cargarCatalogo(tabla) {
        const res = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'readAll', table: tabla })
        });
        const json = await res.json();
        return (json.status === 'ok' && Array.isArray(json.data)) ? json.data : [];
    }
    let cargos = [], departamentos = [], nacionalidades = [], empleadosFiltrados = [];
    let paginaActual = 1;
    const empleadosPorPagina = 10;

    function mapTexto(codigo, catalogo, campoTexto) {
        const item = catalogo.find(x => x.codigo == codigo || x.value == codigo);
        return item ? item[campoTexto] : codigo;
    }
    function mostrarAlerta(mensaje, tipo = 'success') {
        const alertDiv = document.getElementById('alert-container');
        alertDiv.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
        setTimeout(() => { alertDiv.innerHTML = ''; }, 3000);
    }
    async function cargarEmpleados() {
        const response = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'readAll', table: 'empleados' })
        });
        const result = await response.json();
        if (result.status === 'ok' && Array.isArray(result.data)) {
            return result.data.filter(emp => emp.estado == 1).map(emp => ({
                cedula: emp.cedula,
                nombre1: emp.nombre1,
                apellido1: emp.apellido1,
                nacionalidad: emp.nacionalidad,
                cargo: emp.cargo,
                departamento: emp.departamento,
                estado: emp.estado
            }));
        }
        return [];
    }
    function renderTabla(empleados) {
        const tbody = document.getElementById('employeeTableBody');
        tbody.innerHTML = '';
        empleados.forEach(empleado => {
            const tr = document.createElement('tr');
            tr.className = 'employee-row';
            tr.innerHTML = `
                <td>${empleado.cedula}</td>
                <td>${empleado.nombre1}</td>
                <td>${empleado.apellido1}</td>
                <td>${mapTexto(empleado.nacionalidad, nacionalidades, 'pais')}</td>
                <td>${mapTexto(empleado.cargo, cargos, 'nombre')}</td>
                <td>${mapTexto(empleado.departamento, departamentos, 'nombre')}</td>
                <td><span class="status-active">Activo</span></td>
                <td>
                    <a href="employee_details.php?id=${empleado.cedula}" class="details-button">
                        <span class="material-icons">visibility</span> Ver
                    </a>
                    <button type="button" class="inactive-button" data-cedula="${empleado.cedula}">
                        <span class="material-icons">block</span> Inactivar
                    </button>
                    <button type="button" class="delete-button" data-cedula="${empleado.cedula}">
                        <span class="material-icons">delete</span> Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
    function renderPaginacion(totalEmpleados) {
        const totalPaginas = Math.ceil(totalEmpleados / empleadosPorPagina);
        const pageNumbersDiv = document.getElementById('pageNumbers');
        pageNumbersDiv.innerHTML = '';
        for (let i = 1; i <= totalPaginas; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = 'pagination-number' + (i === paginaActual ? ' active' : '');
            btn.addEventListener('click', () => {
                paginaActual = i;
                actualizarTabla();
            });
            pageNumbersDiv.appendChild(btn);
        }
    }
    function getEmpleadosPagina() {
        const inicio = (paginaActual - 1) * empleadosPorPagina;
        return empleadosFiltrados.slice(inicio, inicio + empleadosPorPagina);
    }
    async function inactivarEmpleado(cedula) {
        if (!confirm('¿Estás seguro que deseas inactivar este empleado?')) return;
        const response = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update',
                table: 'empleados',
                pk: cedula,
                id: cedula,
                data: { estado: 0 }
            })
        });
        const result = await response.json();
        if (result.status === 'ok') {
            mostrarAlerta('Empleado inactivado correctamente', 'success');
            empleadosFiltrados = await cargarEmpleados();
            actualizarTabla();
        } else {
            mostrarAlerta('Error al inactivar el empleado', 'error');
        }
    }
    async function eliminarEmpleado(cedula) {
        if (!confirm('¿Estás seguro que deseas eliminar este empleado?')) return;
        const response = await fetch('../../config/controlador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete',
                table: 'empleados',
                pk: cedula,
                id: cedula 
            })
        });
        const result = await response.json();
        if (result.status === 'ok') {
            mostrarAlerta('Empleado eliminado correctamente', 'success');
            empleadosFiltrados = await cargarEmpleados();
            actualizarTabla();
        } else {
            mostrarAlerta('Error al eliminar el empleado', 'error');
        }
    }
    async function actualizarTabla() {
        const empleadosPagina = getEmpleadosPagina();
        renderTabla(empleadosPagina);
        renderPaginacion(empleadosFiltrados.length);
        document.querySelectorAll('#employeeTableBody .inactive-button').forEach(btn => {
            btn.addEventListener('click', function() {
                inactivarEmpleado(this.dataset.cedula);
            });
        });
        document.querySelectorAll('#employeeTableBody .delete-button').forEach(btn => {
            btn.addEventListener('click', function() {
                eliminarEmpleado(this.dataset.cedula);
            });
        });
    }
    document.addEventListener('DOMContentLoaded', async () => {
        [cargos, departamentos, nacionalidades] = await Promise.all([
            cargarCatalogo('cargo'),
            cargarCatalogo('departamento'),
            cargarCatalogo('nacionalidad')
        ]);
        empleadosFiltrados = await cargarEmpleados();
        actualizarTabla();
        document.getElementById('firstPage').addEventListener('click', () => { paginaActual = 1; actualizarTabla(); });
        document.getElementById('prevPage').addEventListener('click', () => { if (paginaActual > 1) { paginaActual--; actualizarTabla(); } });
        document.getElementById('nextPage').addEventListener('click', () => {
            const totalPaginas = Math.ceil(empleadosFiltrados.length / empleadosPorPagina);
            if (paginaActual < totalPaginas) { paginaActual++; actualizarTabla(); }
        });
        document.getElementById('lastPage').addEventListener('click', () => {
            paginaActual = Math.ceil(empleadosFiltrados.length / empleadosPorPagina);
            actualizarTabla();
        });
        document.getElementById('searchInput').addEventListener('input', function() {
            const valor = this.value.toLowerCase();
            // Recargar empleados si el input está vacío
            if (valor === '') {
                cargarEmpleados().then(data => {
                    empleadosFiltrados = data;
                    paginaActual = 1;
                    actualizarTabla();
                });
            } else {
                empleadosFiltrados = empleadosFiltrados.filter(emp =>
                    emp.cedula.toLowerCase().includes(valor) ||
                    emp.nombre1.toLowerCase().includes(valor) ||
                    emp.apellido1.toLowerCase().includes(valor)
                );
                paginaActual = 1;
                actualizarTabla();
            }
        });
    });
    </script>
</body>
</html>