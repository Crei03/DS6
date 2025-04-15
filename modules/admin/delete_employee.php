<?php
/**
 * Archivo para procesar la eliminación lógica y restauración de empleados
 * 
 * Este archivo maneja dos acciones:
 * 1. Eliminación: Copia los datos a la tabla e_eliminados, y elimina al empleado de la tabla empleados
 * 2. Restauración: Recupera un empleado de la tabla e_eliminados a la tabla empleados
 */

// Incluir archivos de configuración y clases necesarias
require_once '../../config/config.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../components/modal_result.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Inicializar variables para mensajes y resultados
$mensaje = '';
$tipo_mensaje = '';
$proceso_completado = false;
$pagina_destino = 'list_table.php';
$mostrar_modal = false;
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'eliminar';

// Verificar si se ha proporcionado una cédula
if (isset($_GET['cedula']) || isset($_POST['cedula'])) {
    $cedula = isset($_GET['cedula']) ? $_GET['cedula'] : $_POST['cedula'];
    
    try {
        // Obtener una conexión a la base de datos
        $conn = conectarBD();

        // PROCESO DE ELIMINACIÓN DE EMPLEADO
        if ($accion === 'eliminar') {
            // 1. Obtener todos los datos del empleado
            $stmt = $conn->prepare("SELECT * FROM empleados WHERE cedula = ?");
            $stmt->bind_param("s", $cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            $empleado = $result->fetch_assoc();
            $stmt->close();
            
            if (!$empleado) {
                $mensaje = "Empleado no encontrado.";
                $tipo_mensaje = "error";
            } else {
                // Iniciar transacción para asegurar que todas las operaciones se completen
                $conn->begin_transaction();
                
                try {
                    // 2. Insertar los datos en la tabla e_eliminados
                    // Verificamos si ya existe en la tabla de eliminados
                    $check_stmt = $conn->prepare("SELECT cedula FROM e_eliminados WHERE cedula = ?");
                    $check_stmt->bind_param("s", $cedula);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    $check_stmt->close();
                    
                    // Si no existe en la tabla de eliminados, lo insertamos
                    if ($check_result->num_rows === 0) {
                        // Construir la consulta de inserción
                        $columns = implode(", ", array_keys($empleado));
                        $placeholders = implode(", ", array_fill(0, count($empleado), "?"));
                        $types = str_repeat("s", count($empleado)); // Asumimos que todos son strings por simplicidad
                        
                        $insert_sql = "INSERT INTO e_eliminados ($columns) VALUES ($placeholders)";
                        $stmt = $conn->prepare($insert_sql);
                        
                        // Crear un array con los valores en el orden correcto
                        $values = array_values($empleado);
                        
                        // Binding dinámico de parámetros
                        $stmt->bind_param($types, ...$values);
                        $stmt->execute();
                        $stmt->close();
                    }
                    
                    // 3. Eliminar el empleado de la tabla empleados
                    $stmt = $conn->prepare("DELETE FROM empleados WHERE cedula = ?");
                    $stmt->bind_param("s", $cedula);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Confirmar transacción
                    $conn->commit();
                    
                    $mensaje = "Empleado eliminado correctamente.";
                    $tipo_mensaje = "success";
                    $proceso_completado = true;
                    
                } catch (Exception $e) {
                    // Si hay error, revertir cambios
                    $conn->rollback();
                    $mensaje = "Error al eliminar empleado: " . $e->getMessage();
                    $tipo_mensaje = "error";
                }
            }
        }
        // PROCESO DE RESTAURACIÓN DE EMPLEADO
        else if ($accion === 'restaurar') {
            // 1. Obtener todos los datos del empleado eliminado
            $stmt = $conn->prepare("SELECT * FROM e_eliminados WHERE cedula = ?");
            $stmt->bind_param("s", $cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            $empleado = $result->fetch_assoc();
            $stmt->close();
            
            if (!$empleado) {
                $mensaje = "Empleado eliminado no encontrado.";
                $tipo_mensaje = "error";
            } else {
                // Iniciar transacción para asegurar que todas las operaciones se completen
                $conn->begin_transaction();
                
                try {
                    // 2. Verificar si el empleado ya existe en la tabla principal
                    $check_stmt = $conn->prepare("SELECT cedula FROM empleados WHERE cedula = ?");
                    $check_stmt->bind_param("s", $cedula);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    $check_stmt->close();
                    
                    // Si ya existe, actualizamos el estado a activo (1)
                    if ($check_result->num_rows > 0) {
                        $stmt = $conn->prepare("UPDATE empleados SET estado = 1 WHERE cedula = ?");
                        $stmt->bind_param("s", $cedula);
                        $stmt->execute();
                        $stmt->close();
                    } 
                    // Si no existe, lo insertamos en la tabla empleados
                    else {
                        // Modificar el estado a activo (1)
                        $empleado['estado'] = 1;
                        
                        // Construir la consulta de inserción
                        $columns = implode(", ", array_keys($empleado));
                        $placeholders = implode(", ", array_fill(0, count($empleado), "?"));
                        $types = str_repeat("s", count($empleado)); // Asumimos que todos son strings por simplicidad
                        
                        $insert_sql = "INSERT INTO empleados ($columns) VALUES ($placeholders)";
                        $stmt = $conn->prepare($insert_sql);
                        
                        // Crear un array con los valores en el orden correcto
                        $values = array_values($empleado);
                        
                        // Binding dinámico de parámetros
                        $stmt->bind_param($types, ...$values);
                        $stmt->execute();
                        $stmt->close();
                    }
                    
                    // 3. Eliminar el registro de la tabla e_eliminados (opcional)
                    // Si prefieres mantener el historial, comenta estas líneas
                    $stmt = $conn->prepare("DELETE FROM e_eliminados WHERE cedula = ?");
                    $stmt->bind_param("s", $cedula);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Confirmar transacción
                    $conn->commit();
                    
                    $mensaje = "Empleado restaurado correctamente.";
                    $tipo_mensaje = "success";
                    $proceso_completado = true;
                    
                } catch (Exception $e) {
                    // Si hay error, revertir cambios
                    $conn->rollback();
                    $mensaje = "Error al restaurar empleado: " . $e->getMessage();
                    $tipo_mensaje = "error";
                }
            }
        } else {
            $mensaje = "Acción no válida.";
            $tipo_mensaje = "error";
        }
        
        // Cerrar la conexión
        cerrarConexion($conn);
        
    } catch (Exception $e) {
        $mensaje = "Error al procesar la solicitud: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
    
    $mostrar_modal = true;
} else {
    // No se proporcionó una cédula
    $mensaje = "Error: No se proporcionó la cédula del empleado.";
    $tipo_mensaje = "error";
    $mostrar_modal = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($accion === 'restaurar') ? 'Restaurar Empleado' : 'Eliminar Empleado'; ?></title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php 
    if ($mostrar_modal) {
        $titulo = $proceso_completado ? 
            ($accion === 'restaurar' ? 'Empleado Restaurado' : 'Empleado Eliminado') : 
            ($accion === 'restaurar' ? 'Error en la Restauración' : 'Error en la Eliminación');
        
        // Opciones para la modal
        $opciones_modal = [
            [
                'url' => $pagina_destino,
                'texto' => 'Volver a la Lista',
                'clase' => 'btn-primary'
            ]
        ];
        
        $modal = new ModalResult($tipo_mensaje, $mensaje, $pagina_destino, $titulo, $opciones_modal);
        $modal->render();
    } else {
        // Si no hay datos para procesar, redirigir a la lista de empleados
        echo '<script>window.location.href = "list_table.php";</script>';
    }
    ?>
</body>
</html>