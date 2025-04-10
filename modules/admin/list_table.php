<?php
// Incluir archivos de configuración
require_once '../../config/config.php';

// // Verificar si el usuario está autenticado
// if (!isset($_SESSION)) {
//     session_start();
// }

// // Si el usuario no está autenticado, redirigir al login
// if (!isset($_SESSION['usuario_id'])) {
//     redirigir('auth/login.php');
// }

// // Verificar si el usuario es administrador
// if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
//     // Si no es administrador, redirigir al dashboard
//     redirigir('modules/dashboard.php');
// }

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
                <button class="search-button">
                    <span class="material-icons">search</span>
                </button>
                <button class="add-button">
                    <span class="material-icons">add</span> Agregar
                </button>
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
                    <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['pais']); ?></td>
                    <td>
                        <button class="details-button">Más detalles</button>
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
</body>
</html>