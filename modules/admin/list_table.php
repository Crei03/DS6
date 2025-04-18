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

// Procesar la inactivación del empleado si se recibe la solicitud
if (isset($_GET['accion']) && $_GET['accion'] == 'inactivar' && isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    
    // Conexión a la base de datos
    $conn = conectarBD();
    
    // Actualizar estado del empleado a inactivo (0)
    $update_query = "UPDATE empleados SET estado = 0 WHERE cedula = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $mensaje = "Empleado inactivado correctamente";
        $tipo = "success";
    } else {
        $mensaje = "Error al inactivar el empleado";
        $tipo = "error";
    }
    
    // Cerrar conexión
    cerrarConexion($conn);
    
    // Redirigir para evitar reenvío de formulario
    header("Location: list_table.php?mensaje=$mensaje&tipo=$tipo");
    exit();
}

// Obtener listado de empleados
$dbHandler = new DBHandler();
$resultado = $dbHandler->selectAll('empleados');
$empleados_completos = ($resultado['status'] === 'ok') ? $resultado['data'] : [];

// Filtrar solo empleados activos (estado = 1)
$empleados = array_filter($empleados_completos, function($empleado) {
    return $empleado['estado'] == 1;
});

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormAntro - Listado de Empleados</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/list_table.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Estilo para mostrar estado activo */
        .status-active {
            background-color: #2ecc71;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
        }
        
        /* Estilos para los botones */
        .inactive-button {
            background-color: #f39c12;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-right: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        
        .inactive-button:hover {
            background-color: #e67e22;
        }
        
        .delete-button {
            background-color: #e74c3c;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-right: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        
        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
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
                <a href="list_table_inactive.php" class="inactive-button"><span class="material-icons">block</span> Ver Inactivos</a>
                <a href="list_table_delete.php" class="delete-button"><span class="material-icons">delete_sweep</span> Ver Eliminados</a>
            </div>
        </div>
        
        <?php 
        // Mostrar mensaje de resultado si existe
        if (isset($_GET['mensaje']) && isset($_GET['tipo'])) {
            echo '<div class="alert alert-' . $_GET['tipo'] . '">' . $_GET['mensaje'] . '</div>';
        }
        ?>
        
        <table class="employee-table">
            <thead>
                <tr>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Nacionalidad</th>
                    <th>Estado</th>
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
                    <td><span class="status-active">Activo</span></td>
                    <td>
                        <a href="employee_details.php?id=<?php echo $empleado['cedula']; ?>" class="details-button">
                            <span class="material-icons">visibility</span> Ver
                        </a>
                        <a href="list_table.php?accion=inactivar&cedula=<?php echo $empleado['cedula']; ?>" class="inactive-button" onclick="return confirm('¿Estás seguro que deseas inactivar este empleado?');">
                            <span class="material-icons">block</span> Inactivar
                        </a>
                        <a href="delete_employee.php?cedula=<?php echo $empleado['cedula']; ?>" class="delete-button" onclick="return confirm('¿Estás seguro que deseas eliminar este empleado?');">
                            <span class="material-icons">delete</span> Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="pagination" id="paginationContainer">
            <button class="pagination-button" id="firstPage"><span class="material-icons">first_page</span></button>
            <button class="pagination-button" id="prevPage"><span class="material-icons">chevron_left</span></button>
            <div id="pageNumbers" class="page-numbers">
                <!-- Los números de página se generarán dinámicamente con JavaScript -->
            </div>
            <button class="pagination-button" id="nextPage"><span class="material-icons">chevron_right</span></button>
            <button class="pagination-button" id="lastPage"><span class="material-icons">last_page</span></button>
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

            // Funcionalidad de búsqueda y paginación
            const searchInput = document.getElementById('searchInput');
            const employeeRows = document.querySelectorAll('.employee-row');
            const paginationContainer = document.getElementById('paginationContainer');
            const pageNumbersContainer = document.getElementById('pageNumbers');
            const rowsPerPage = 10; // Número de filas por página
            let currentPage = 1; // Página inicial
            let filteredRows = Array.from(employeeRows); // Inicialmente, todas las filas
            
            // Inicializar la paginación
            initPagination();
            
            // Función para filtrar los empleados
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                // Filtrar las filas según el término de búsqueda
                filteredRows = Array.from(employeeRows).filter(row => {
                    const cedula = row.cells[0].textContent.toLowerCase();
                    const nombre = row.cells[1].textContent.toLowerCase();
                    const apellido = row.cells[2].textContent.toLowerCase();
                    
                    return cedula.includes(searchTerm) || nombre.includes(searchTerm) || apellido.includes(searchTerm);
                });
                
                // Resetear a la primera página y actualizar la visualización
                currentPage = 1;
                updatePageNumbers();
                displayRows();
            });
            
            // Función para inicializar la paginación
            function initPagination() {
                // Configurar los botones de navegación
                document.getElementById('firstPage').addEventListener('click', () => {
                    currentPage = 1;
                    updatePageNumbers();
                    displayRows();
                });
                
                document.getElementById('prevPage').addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePageNumbers();
                        displayRows();
                    }
                });
                
                document.getElementById('nextPage').addEventListener('click', () => {
                    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePageNumbers();
                        displayRows();
                    }
                });
                
                document.getElementById('lastPage').addEventListener('click', () => {
                    currentPage = Math.ceil(filteredRows.length / rowsPerPage);
                    updatePageNumbers();
                    displayRows();
                });
                
                // Generar números de página y mostrar las filas
                updatePageNumbers();
                displayRows();
            }
            
            // Función para actualizar los números de página
            function updatePageNumbers() {
                pageNumbersContainer.innerHTML = '';
                const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
                
                // Determinar qué números de página mostrar
                let startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, startPage + 4);
                startPage = Math.max(1, endPage - 4);
                
                // Crear botones para cada número de página
                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = 'pagination-button' + (i === currentPage ? ' active' : '');
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', function() {
                        currentPage = i;
                        updatePageNumbers();
                        displayRows();
                    });
                    pageNumbersContainer.appendChild(pageButton);
                }
                
                // Mostrar u ocultar la paginación según la cantidad de registros
                togglePagination(filteredRows.length);
            }
            
            // Función para mostrar las filas según la página actual
            function displayRows() {
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                
                // Ocultar todas las filas primero
                employeeRows.forEach(row => {
                    row.style.display = 'none';
                });
                
                // Mostrar solo las filas de la página actual
                filteredRows.slice(startIndex, endIndex).forEach(row => {
                    row.style.display = '';
                });
            }
            
            // Función para mostrar u ocultar la paginación
            function togglePagination(totalRows) {
                if (totalRows > rowsPerPage) {
                    paginationContainer.style.display = '';
                } else {
                    paginationContainer.style.display = totalRows === 0 ? 'none' : '';
                }
            }
        });
    </script>
</body>
</html>