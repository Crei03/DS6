<?php
// api.php - Controlador dinámico para operaciones CRUD

require_once(__DIR__ . "/BdHandler.php");
header("Content-Type: application/json");

$host = "localhost";
$usuario = "root";
$pass = "";
$bd = "DS6";
$puerto = 3306;

try{
     $conn = new mysqli($host,$usuario,$pass,$bd,$puerto);
}catch(Exception $e){    
    http_response_code(500);
    
    die(json_encode(["status" => "error", "message" => $e->getMessage()]));
    exit;
}

$db = new DBHandler($conn);
$input = file_get_contents("php://input");

$request = [
     "action" => "create",
     "table"  => "nacionalidad",
     "data"   => [
          "codigo" => 4413,
          "pais" => 1234,
          ]
     ];

// $request = [
//      "action" => "update",
//      "table"  => "nacionalidad",
//      "id"     => "507",
//      "data"   => [
//          "pais" => "Colombia"
//      ]
//  ];

// $request = [
//      "action" => "read",
//      "table"  => "nacionalidad",
//      "id"     => "1"
//  ];

//  $request = [
//      "action" => "readAll",
//      "table"  => "nacionalidad",
//  ];

//  $request = [
//      "action" => "delete",
//      "table"  => "nacionalidad",
//      "id"     => "50"
//  ];
 

// Validaciones
$action = $request['action'] ?? '';
$table  = $request['table'] ?? '';
$data   = $request['data']  ?? [];


if (!$action || !$table) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Se requieren 'action' y 'table'"]);
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
         "cedula" => ["empleados"],
         "id" => ["usuarios"]
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
            $updated = $db->update($table, $columns, $values,$pk, $id);
            echo json_encode($updated);
            break;

        case "delete":
            $id = $request['id'] ?? null;
            $pk = getPrimaryKeyColumn($table);
            if (!$id) {
                throw new Exception("Falta el identificador para eliminar.");
            }
            $deleted = $db->delete($table,$pk, $id);
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