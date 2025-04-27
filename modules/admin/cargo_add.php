<?php
// Incluir archivos de configuración
require_once '../../config/config.php';
require_once '../../class/session.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->cerrarSesion();
    $sesion->redirigir('../../modules/auth/login.php');
}

// Obtener la cédula del administrador
$cedula = $sesion->getCedula();

require_once '../../components/sidebar_menu.php';


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Cargos</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/components/sidebar_menu.css">
    <link rel="stylesheet" href="../../assets/admin/cargo_add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('cargo_add'); 
    ?>
    <div class="container">
        
        <div class="main-content">
            <h1>Administrar Cargos</h1>
            
            <div class="card">
                <h2>Agregar Nuevo Cargo</h2>
                <form id="positionForm">
                    <div class="form-group">
                        <label for="dep_codigo">Departamento:</label>
                        <select id="dep_codigo" name="dep_codigo" required>
                            <option value="">Seleccione un departamento</option>
                            <!-- Las opciones se cargarán desde JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre del Cargo:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <button type="submit" class="btn-submit">Guardar Cargo</button>
                </form>
                <div id="result-message" class="result-message"></div>
            </div>
            
            <div class="card">
                <h2>Lista de Cargos</h2>
                <div class="table-container">
                    <table id="positionTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Departamento</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán desde JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar departamentos para el select
            loadDepartments();
            
            // Cargar cargos al iniciar
            loadPositions();
            
            // Configurar el formulario
            document.getElementById('positionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                savePosition();
            });
        });
        
        // Función para cargar departamentos en el select
        function loadDepartments() {
            fetch('../../config/controlador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'readAll',
                    table: 'departamento'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    const select = document.getElementById('dep_codigo');
                    // Mantener la opción por defecto
                    select.innerHTML = '<option value="">Seleccione un departamento</option>';
                    
                    data.data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.codigo;
                        option.textContent = dept.nombre;
                        select.appendChild(option);
                    });
                } else {
                    showMessage('Error al cargar departamentos: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
            });
        }
        
        // Función para cargar la lista de cargos
        function loadPositions() {
            fetch('../../config/controlador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'readAll',
                    table: 'cargo'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    const tbody = document.querySelector('#positionTable tbody');
                    tbody.innerHTML = '';
                    
                    // Obtener los nombres de los departamentos
                    fetch('../../config/controlador.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'readAll',
                            table: 'departamento'
                        })
                    })
                    .then(response => response.json())
                    .then(deptData => {
                        if (deptData.status === 'ok') {
                            // Crear un mapa de códigos a nombres de departamento
                            const deptMap = {};
                            deptData.data.forEach(dept => {
                                deptMap[dept.codigo] = dept.nombre;
                            });
                            
                            // Ahora mostrar los cargos con nombres de departamento
                            data.data.forEach(pos => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${pos.codigo}</td>
                                    <td>${deptMap[pos.dep_codigo] || pos.dep_codigo}</td>
                                    <td>${pos.nombre}</td>
                                    <td>
                                        <button class="btn-delete" onclick="deletePosition('${pos.codigo}')">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                        }
                    });
                } else {
                    showMessage('Error al cargar cargos: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
            });
        }
        
        // Función para obtener el siguiente código de cargo para un departamento
        function getNextPositionCode(depCodigo) {
            return fetch('../../config/controlador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'readAll',
                    table: 'cargo'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Filtrar cargos por departamento
                    const deptPositions = data.data.filter(pos => pos.dep_codigo === depCodigo);
                    
                    // Encontrar el código más alto para este departamento
                    let maxCode = 0;
                    deptPositions.forEach(pos => {
                        // Extraer solo la parte del código de cargo (últimos 2 dígitos)
                        const code = parseInt(pos.codigo.substring(2));
                        if (code > maxCode) {
                            maxCode = code;
                        }
                    });
                    
                    // Formatear el nuevo código: [dep_codigo][siguiente código de 2 dígitos]
                    const nextCode = depCodigo + (maxCode + 1).toString().padStart(2, '0');
                    return nextCode;
                } else {
                    showMessage('Error al obtener códigos: ' + data.message, 'error');
                    return null;
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
                return null;
            });
        }
        
        // Función para guardar un nuevo cargo
        function savePosition() {
            const depCodigo = document.getElementById('dep_codigo').value.trim();
            const nombre = document.getElementById('nombre').value.trim();
            
            if (!depCodigo) {
                showMessage('Debe seleccionar un departamento', 'error');
                return;
            }
            
            if (!nombre) {
                showMessage('Debe ingresar un nombre para el cargo', 'error');
                return;
            }
            
            // Obtener el siguiente código
            getNextPositionCode(depCodigo).then(nextCode => {
                if (!nextCode) return;
                
                // Enviar datos al servidor
                fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'create',
                        table: 'cargo',
                        data: {
                            codigo: nextCode,
                            dep_codigo: depCodigo,
                            nombre: nombre
                        }
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showMessage('Cargo guardado correctamente', 'success');
                        document.getElementById('nombre').value = '';
                        loadPositions(); // Recargar la lista
                    } else {
                        showMessage('Error al guardar: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error de conexión: ' + error, 'error');
                });
            });
        }
        
        // Función para eliminar un cargo
        function deletePosition(codigo) {
            if (confirm('¿Está seguro que desea eliminar este cargo?')) {
                fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        table: 'cargo',
                        id: codigo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showMessage('Cargo eliminado correctamente', 'success');
                        loadPositions(); // Recargar la lista
                    } else {
                        showMessage('Error al eliminar: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error de conexión: ' + error, 'error');
                });
            }
        }
        
        // Función para mostrar mensajes
        function showMessage(message, type) {
            const messageDiv = document.getElementById('result-message');
            messageDiv.textContent = message;
            messageDiv.className = 'result-message ' + type;
            
            // Desaparecer después de 5 segundos
            setTimeout(() => {
                messageDiv.textContent = '';
                messageDiv.className = 'result-message';
            }, 5000);
        }
    </script>
</body>
</html>