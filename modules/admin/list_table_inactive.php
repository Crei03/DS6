<?php
/**
 * Lista de empleados inactivos
 * 
 * Muestra los empleados que están inactivos (estado = 0) y permite activarlos
 */

// Incluir archivos de configuración y clases necesarias
require_once '../../config/config.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';
require_once '../../config/BdHandler.php';
require_once '../../components/sidebar_menu.php';
require_once '../../components/modal_result.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Conexión a la base de datos
$conn = conectarBD();

// Obtener lista de empleados inactivos
$query = "SELECT e.*, 
          d.nombre AS departamento_nombre, 
          c.nombre AS cargo_nombre,
          n.pais AS nacionalidad_nombre
          FROM empleados e 
          LEFT JOIN departamento d ON e.departamento = d.codigo
          LEFT JOIN cargo c ON e.cargo = c.codigo
          LEFT JOIN nacionalidad n ON e.nacionalidad = n.codigo
          WHERE e.estado = 0
          ORDER BY e.apellido1";
$result = $conn->query($query);
$empleados_inactivos = $result->fetch_all(MYSQLI_ASSOC);

// Procesar la activación de un empleado si se recibe una solicitud
if (isset($_GET['accion']) && $_GET['accion'] == 'activar' && isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    
    // Actualizar estado del empleado a activo (1)
    $update_query = "UPDATE empleados SET estado = 1 WHERE cedula = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $mensaje = "Empleado activado correctamente";
        $tipo = "success";
    } else {
        $mensaje = "Error al activar el empleado";
        $tipo = "error";
    }
    
    // Redirigir para evitar reenvío de formulario
    header("Location: list_table_inactive.php?mensaje=$mensaje&tipo=$tipo");
    exit();
}

// Cerrar conexión
cerrarConexion($conn);
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

    <!-- Capa semi-transparente para dispositivos móviles -->
    <div class="sidebar-blur" id="sidebar-blur"></div>
    
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('empleados'); 
    ?>
    
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
        
        <?php 
        // Mostrar mensaje de resultado si existe
        if (isset($_GET['mensaje']) && isset($_GET['tipo'])) {
            echo '<div class="alert alert-' . $_GET['tipo'] . '">' . $_GET['mensaje'] . '</div>';
        }
        ?>
        
        <?php if (count($empleados_inactivos) > 0): ?>
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
                <?php foreach ($empleados_inactivos as $empleado): ?>
                <tr class="employee-row">    
                    <td><?php echo $empleado['cedula']; ?></td>
                    <td><?php echo $empleado['nombre1']; ?></td>
                    <td><?php echo $empleado['apellido1']; ?></td>
                    <td><?php echo $empleado['cargo_nombre']; ?></td>
                    <td><?php echo $empleado['departamento_nombre']; ?></td>
                    <td><?php echo $empleado['nacionalidad_nombre']; ?></td>
                    <td>
                        <a href="?accion=activar&cedula=<?php echo $empleado['cedula']; ?>" 
                           class="activate-button" 
                           onclick="return confirm('¿Está seguro que desea activar este empleado?');">
                            <span class="material-icons">check_circle</span> Activar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">info</span>
                <p>No hay empleados inactivos para mostrar.</p>
            </div>
        <?php endif; ?>
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
            });
        });
    </script>
</body>
</html>