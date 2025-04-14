<?php
// Incluir archivos de configuración
require_once '../../config/config.php';
require_once '../../class/session.php';
require_once '../../config/BdHandler.php'; // Incluir BdHandler.php
require_once '../../class/employee.php'; // Incluir employee.php

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Obtener listado de empleados
$dbHandler = new DBHandler();
$resultado = $dbHandler->selectAll('empleados');
$empleados = ($resultado['status'] === 'ok') ? $resultado['data'] : [];

// Crear instancia de Employee para obtener departamentos y cargos
$employee = new Employee();
$departamentos = $employee->getDepartamentos();
$cargos = $employee->getCargos();
$nacionalidades = $employee->getNacionalidades();

// Función para obtener nombre del departamento
function getDepartamentoNombre($codigo, $departamentos) {
    foreach ($departamentos as $departamento) {
        if ($departamento['value'] === $codigo) {
            return $departamento['text'];
        }
    }
    return $codigo; // Si no encuentra, devuelve el código
}


// Función para obtener nombre del cargo
function getCargoNombre($codigo, $cargos) {
    foreach ($cargos as $cargo) {
        if ($cargo['value'] === $codigo) {
            return $cargo['text'];
        }
    }
    return $codigo; // Si no encuentra, devuelve el código
}

function getNacionalidad($codigo, $nacionalidades) {
    foreach ($nacionalidades as $nacionalidad) {
        if ($nacionalidad['value'] === $codigo) {
            return $nacionalidad['text'];
        }
    }
    return $codigo; // Si no encuentra, devuelve el código
}

// Incluir el componente del sidebar
require_once '../../components/sidebar_menu.php';

?>

<!DOCTYZPE html>
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
            <div class="table-title">Listado de Empleados</div>
            <div class="table-subtitle">Gestione la información de los empleados de la empresa</div>
            
            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="Buscar por cédula, nombre o apellido...">
                <button class="search-button"><span class="material-icons">search</span></button>
                <a href="employee_add.php" class="add-button"><span class="material-icons">add</span> Agregar</a>
            </div>
        </div>
        
        <table class="employee-table">
            <thead>
                <tr>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Nacionalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                <tr class="employee-row">    
                    <td><?php echo $empleado['cedula']; ?></td>
                    <td><?php echo $empleado['nombre1']; ?></td>
                    <td><?php echo $empleado['apellido1']; ?></td>
                    <td><?php echo getCargoNombre($empleado['cargo'], $cargos); ?></td>
                    <td><?php echo getDepartamentoNombre($empleado['departamento'], $departamentos); ?></td>
                    <td><?php echo getNacionalidad($empleado['nacionalidad'],$nacionalidades); ?></td>
                    <td>
                        <a href="employee_details.php?id=<?php echo $empleado['cedula']; ?>" class="details-button">
                            <span class="material-icons">visibility</span> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="pagination" id="paginationContainer">
            <button class="pagination-button"><span class="material-icons">first_page</span></button>
            <button class="pagination-button"><span class="material-icons">chevron_left</span></button>
            <button class="pagination-button active">1</button>
            <button class="pagination-button">2</button>
            <button class="pagination-button">3</button>
            <button class="pagination-button"><span class="material-icons">chevron_right</span></button>
            <button class="pagination-button"><span class="material-icons">last_page</span></button>
        </div>
    </div>

    <script>
        // Funcionalidad del sidebar responsive
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarBlur = document.getElementById('sidebar-blur');
            
            // Función para mostrar/ocultar el sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarBlur.classList.toggle('active');
            });
            
            // Cerrar el sidebar al hacer clic en el área semi-transparente
            sidebarBlur.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarBlur.classList.remove('active');
            });
            
            // Ajustar la visualización en cambios de tamaño de ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth > 480) {
                    sidebarBlur.classList.remove('active');
                    // En pantallas mayores a 480px, el sidebar siempre es visible
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                    } else {
                        sidebar.classList.add('active');
                    }
                }
            });

            // Funcionalidad de búsqueda
            const searchInput = document.getElementById('searchInput');
            const employeeRows = document.querySelectorAll('.employee-row');
            const paginationContainer = document.getElementById('paginationContainer');
            
            // Función para filtrar los empleados
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleRowCount = 0;
                
                employeeRows.forEach(row => {
                    const cedula = row.cells[0].textContent.toLowerCase();
                    const nombre = row.cells[1].textContent.toLowerCase();
                    const apellido = row.cells[2].textContent.toLowerCase();
                    
                    if (cedula.includes(searchTerm) || nombre.includes(searchTerm) || apellido.includes(searchTerm)) {
                        row.style.display = '';
                        visibleRowCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Mostrar u ocultar la paginación según la cantidad de registros visibles
                togglePagination(visibleRowCount);
            });
            
            // Función para mostrar u ocultar la paginación
            function togglePagination(visibleRowCount) {
                if (visibleRowCount >= 10) {
                    paginationContainer.style.display = '';
                } else {
                    paginationContainer.style.display = 'none';
                }
            }
            
            // Inicializar el estado de la paginación al cargar la página
            togglePagination(employeeRows.length);
        });
    </script>
</body>
</html>