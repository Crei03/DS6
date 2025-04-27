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
    <title>Administrar Departamentos</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/components/sidebar_menu.css">
    <link rel="stylesheet" href="../../assets/admin/department_add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('department_add'); 
    ?>
    <div class="container">
        
        <div class="main-content">
            <h1>Administrar Departamentos</h1>
            
            <div class="card">
                <h2>Agregar Nuevo Departamento</h2>
                <form id="departmentForm">
                    <div class="form-group">
                        <label for="nombre">Nombre del Departamento:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <button type="submit" class="btn-submit">Guardar Departamento</button>
                </form>
                <div id="result-message" class="result-message"></div>
            </div>
            
            <div class="card">
                <h2>Lista de Departamentos</h2>
                <div class="table-container">
                    <table id="departmentTable">
                        <thead>
                            <tr>
                                <th>Código</th>
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
            // Cargar departamentos al iniciar
            loadDepartments();
            
            // Configurar el formulario
            document.getElementById('departmentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveDepartment();
            });
        });
        
        // Función para cargar la lista de departamentos
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
                    const tbody = document.querySelector('#departmentTable tbody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(dept => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${dept.codigo}</td>
                            <td>${dept.nombre}</td>
                            <td>
                                <button class="btn-delete" onclick="deleteDepartment('${dept.codigo}')">
                                    <i class="material-icons">delete</i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    showMessage('Error al cargar departamentos: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
            });
        }
        
        // Función para obtener el siguiente código de departamento
        function getNextDepartmentCode() {
            return fetch('../../config/controlador.php', {
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
                    // Encontrar el código más alto y agregar 1
                    let maxCode = 0;
                    data.data.forEach(dept => {
                        const code = parseInt(dept.codigo);
                        if (code > maxCode) {
                            maxCode = code;
                        }
                    });
                    
                    // Formatear el nuevo código con dos dígitos (01, 02, etc.)
                    const nextCode = (maxCode + 1).toString().padStart(2, '0');
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
        
        // Función para guardar un nuevo departamento
        function saveDepartment() {
            const nombre = document.getElementById('nombre').value.trim();
            
            if (!nombre) {
                showMessage('Debe ingresar un nombre para el departamento', 'error');
                return;
            }
            
            // Obtener el siguiente código
            getNextDepartmentCode().then(nextCode => {
                if (!nextCode) return;
                
                // Enviar datos al servidor
                fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'create',
                        table: 'departamento',
                        data: {
                            codigo: nextCode,
                            nombre: nombre
                        }
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showMessage('Departamento guardado correctamente', 'success');
                        document.getElementById('nombre').value = '';
                        loadDepartments(); // Recargar la lista
                    } else {
                        showMessage('Error al guardar: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error de conexión: ' + error, 'error');
                });
            });
        }
        
        // Función para eliminar un departamento
        function deleteDepartment(codigo) {
            if (confirm('¿Está seguro que desea eliminar este departamento?')) {
                fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        table: 'departamento',
                        id: codigo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showMessage('Departamento eliminado correctamente', 'success');
                        loadDepartments(); // Recargar la lista
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