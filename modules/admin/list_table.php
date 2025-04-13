<?php
// Incluir archivos de configuración
require_once '../../config/config.php';
require_once '../../class/session.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Incluir el componente del sidebar
require_once '../../components/sidebar_menu.php';

// En una aplicación real, aquí se consultaría la base de datos
// Por ahora, usaremos datos de ejemplo
$empleados = [
    [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '555-1234',
        'pais' => 'Panamá'
    ],
    [
        'nombre' => 'María',
        'apellido' => 'González',
        'telefono' => '555-5678',
        'pais' => 'Colombia'
    ],
    [
        'nombre' => 'Carlos',
        'apellido' => 'Rodríguez',
        'telefono' => '555-9012',
        'pais' => 'México'
    ],
    [
        'nombre' => 'Ana',
        'apellido' => 'Martínez',
        'telefono' => '555-3456',
        'pais' => 'Panamá'
    ],
    [
        'nombre' => 'Pedro',
        'apellido' => 'López',
        'telefono' => '555-7890',
        'pais' => 'Costa Rica'
    ]
];
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
                <input type="text" class="search-input" placeholder="Buscar empleado...">
                <button class="search-button"><span class="material-icons">search</span></button>
                <a href="employee_add.php" class="add-button"><span class="material-icons">add</span> Agregar</a>
            </div>
        </div>
        
        <table class="employee-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Teléfono</th>
                    <th>País</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?php echo $empleado['nombre']; ?></td>
                    <td><?php echo $empleado['apellido']; ?></td>
                    <td><?php echo $empleado['telefono']; ?></td>
                    <td><?php echo $empleado['pais']; ?></td>
                    <td>
                        <a href="employee_details.php?id=1" class="details-button">
                            <span class="material-icons">visibility</span> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="pagination">
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
        });
    </script>
</body>
</html>