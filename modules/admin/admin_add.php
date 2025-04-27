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
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/components/sidebar_menu.css">
    <link rel="stylesheet" href="../../assets/admin/admin_add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php 
    // Renderizar el sidebar indicando la página activa
    renderSidebar('admin_add'); 
    ?>
    <div class="container">
        
        <div class="main-content">
            <h1>Administrar Usuarios</h1>
            
            <div class="card">
                <h2>Agregar Nuevo Usuario</h2>
                <form id="userForm">
                    <div class="form-group">
                        <label for="cedula">Cédula:</label>
                        <input type="text" id="cedula" name="cedula" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="contraseña">Contraseña:</label>
                        <input type="password" id="contraseña" name="contraseña" required>
                    </div>
                    <button type="submit" class="btn-submit">Guardar Usuario</button>
                </form>
                <div id="result-message" class="result-message"></div>
            </div>
            
            <div class="card">
                <h2>Lista de Usuarios</h2>
                <div class="table-container">
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Correo</th>
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
            // Cargar usuarios al iniciar
            loadUsers();
            
            // Configurar el formulario
            document.getElementById('userForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveUser();
            });
        });
        
        // Función para cargar la lista de usuarios
        function loadUsers() {
            fetch('../../config/controlador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'readAll',
                    table: 'usuarios'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    const tbody = document.querySelector('#userTable tbody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.cedula}</td>
                            <td>${user.correo}</td>
                            <td>
                                <button class="btn-delete" onclick="deleteUser('${user.cedula}')">
                                    <i class="material-icons">delete</i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    showMessage('Error al cargar usuarios: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
            });
        }
        
        // Función para guardar un nuevo usuario
        function saveUser() {
            const cedula = document.getElementById('cedula').value.trim();
            const correo = document.getElementById('correo').value.trim();
            const contraseña = document.getElementById('contraseña').value;
            
            if (!cedula || !correo || !contraseña) {
                showMessage('Todos los campos son obligatorios', 'error');
                return;
            }
            
            // Validar formato de cédula (puedes personalizar según el formato que necesites)
            if (!/^\d+-\d+-\d+$/.test(cedula) && !/^\d+$/.test(cedula)) {
                showMessage('Formato de cédula inválido. Use el formato correcto (por ejemplo: 8-123-456 o 8123456)', 'error');
                return;
            }
            
            // Validar correo
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                showMessage('Formato de correo electrónico inválido', 'error');
                return;
            }
            
            // Enviar datos al servidor
            fetch('../../config/controlador.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'create',
                    table: 'usuarios',
                    data: {
                        cedula: cedula,
                        correo: correo,
                        contraseña: contraseña
                    }
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    showMessage('Usuario guardado correctamente', 'success');
                    document.getElementById('cedula').value = '';
                    document.getElementById('correo').value = '';
                    document.getElementById('contraseña').value = '';
                    loadUsers(); // Recargar la lista
                } else {
                    showMessage('Error al guardar: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Error de conexión: ' + error, 'error');
            });
        }
        
        // Función para eliminar un usuario
        function deleteUser(cedula) {
            if (confirm('¿Está seguro que desea eliminar este usuario?')) {
                fetch('../../config/controlador.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        table: 'usuarios',
                        id: cedula
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        showMessage('Usuario eliminado correctamente', 'success');
                        loadUsers(); // Recargar la lista
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