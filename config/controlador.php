<?php
// api.php - Controlador dinámico para operaciones CRUD

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/BdHandler.php");
require_once(__DIR__ . '/../class/session.php');
header("Content-Type: application/json");

// Usar la función conectarBD definida en config.php
$conn = conectarBD();

$db = new DBHandler($conn);
$input = file_get_contents("php://input");
$request = json_decode($input, true);

// Validaciones
$action = $request['action'] ?? '';
$table  = $request['table'] ?? '';
$data   = $request['data']  ?? [];

if (!$action) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Se requiere 'action'"]);
    exit;
}

// Solo validar 'table' para acciones que lo requieren
$acciones_requieren_tabla = ['create', 'readAll', 'read', 'update', 'delete', 'restore_employee'];
if (in_array($action, $acciones_requieren_tabla) && !$table) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Se requiere 'table' para la acción '$action'"]);
    exit;
}

$filteredData = [];
if (is_array($data)) {
    foreach ($data as $key => $value) {
        $filteredData[$key] = htmlspecialchars(trim($value));
    }
}

function getPrimaryKeyColumn($table) {
    // Agrupar por nombre de clave primaria
    $groupedKeys = [
        "codigo" => ["nacionalidad", "departamento", "cargo"],
        "codigo_provincia" => ["provincia"],
        "codigo_distrito" => ["distrito"],
        "codigo_corregimiento" => ["corregimiento"],
        "cedula" => ["empleados", "usuarios"],
    ];

    foreach ($groupedKeys as $pk => $tables) {
        if (in_array($table, $tables)) {
            return $pk;
        }
    }
    return null;
}

try {
    switch ($action) {
        case "authenticate":
            $cedula   = $request['cedula'] ?? '';
            $password = $request['password'] ?? '';
            $session = new Session();
            $user = $db->selectOne('usuarios', 'cedula', $cedula);
            if ($user && isset($user['data']) && isset($user['data']['contraseña']) && $user['data']['contraseña'] === $password) {
                $session->iniciarSesion('admin', $user['data']['cedula']);
                echo json_encode(['status' => 'success', 'redirect' => 'modules/admin/dashboard.php', 'cedula' => $user['data']['cedula'], 'tipo_usuario' => 'admin']);
                break;
            }
            // Buscar en empleados
            $emp = $db->selectOne('empleados', 'cedula', $cedula);
            if ($emp && isset($emp['data']) && isset($emp['data']['contraseña']) && $emp['data']['contraseña'] === $password) {
                $session->iniciarSesion('empleado', $emp['data']['cedula']);
                echo json_encode(['status' => 'success', 'redirect' => 'modules/employees/dashboard.php', 'cedula' => $emp['data']['cedula'], 'tipo_usuario' => 'empleado']);
                break;
            }
            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas']);
            break;

        case "restore_employee":
            $cedula = $request['cedula'] ?? null;
            if (!$cedula) {
                throw new Exception("Falta la cédula del empleado para restaurar.");
            }
            // Obtener datos del empleado eliminado
            $empleado = $db->selectOne('e_eliminados', 'cedula', $cedula);
            if (!$empleado || !isset($empleado['data'])) {
                throw new Exception("No se encontró el empleado eliminado.");
            }
            $empleadoData = $empleado['data'];
            unset($empleadoData['fecha_eliminacion']); // No insertar la fecha de eliminación en empleados
            // Insertar en empleados
            $columns = array_keys($empleadoData);
            $values = array_values($empleadoData);
            $db->insert('empleados', $columns, $values);
            // Eliminar de e_eliminados
            $db->delete('e_eliminados', 'cedula', $cedula);
            echo json_encode(["status" => "ok", "message" => "Empleado restaurado correctamente"]);
            break;

        case "create":
            // Se espera que 'data' contenga los pares campo => valor
            $columns = array_keys($filteredData);
            $values  = array_values($filteredData);
            $insertedId = $db->insert($table, $columns, $values);
            echo json_encode($insertedId);
            break;

        case "readAll":
            $rows = $db->selectAll($table);
            echo json_encode($rows);
            break;

        case "read":
            $pk = getPrimaryKeyColumn($table);
            $id = $request['id'] ?? null;
            $row = $db->selectOne($table, $pk, $id);
            echo json_encode($row);
            break;

        case "update":
            // Se asume que se envía el identificador en "id"
            $pk = getPrimaryKeyColumn($table);
            $id = $request['id'] ?? null;
            if (!$id) {
                throw new Exception("Falta el identificador para actualizar.");
            }
            $columns = array_keys($filteredData);
            $values  = array_values($filteredData);
            $updated = $db->update($table, $columns, $values, $pk, $id);
            echo json_encode($updated);
            break;

        case "delete":
            $id = $request['id'] ?? null;
            $pk = getPrimaryKeyColumn($table);
            if (!$id) {
                throw new Exception("Falta el identificador para eliminar.");
            }
            // Si es la tabla de empleados, guardar en e_eliminados antes de eliminar
            if ($table === 'empleados') {
                $empleado = $db->selectOne($table, $pk, $id);
                if ($empleado && isset($empleado['data'])) {
                    // Preparar datos para insertar en e_eliminados
                    $empleadoData = $empleado['data'];

                    // Insertar en tabla de eliminados
                    $columns = array_keys($empleadoData);
                    $values = array_values($empleadoData);
                    $db->insert('e_eliminados', $columns, $values);
                }
            }

            $deleted = $db->delete($table, $pk, $id);
            echo json_encode($deleted);
            break;

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Cerrar la conexión
$db->close();
?>