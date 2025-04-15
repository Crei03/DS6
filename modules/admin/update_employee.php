<?php
/**
 * Actualiza los datos de un empleado desde la vista de administrador
 */
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../components/modal_result.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Inicializar el DBHandler
$dbHandler = new DBHandler();

// Inicializar variables para mensajes y resultados
$mensaje = '';
$tipo_mensaje = '';
$proceso_completado = false;
$pagina_destino = '';
$mostrar_modal = false;

// Verificar si se han enviado datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar los datos recibidos
    $post_data = sanitizarDatosFormulario($_POST);
    
    // Obtener ID del empleado
    $id_empleado = isset($post_data['employee_id']) ? $post_data['employee_id'] : null;
    
    if (!$id_empleado) {
        $mensaje = "Error: ID de empleado no proporcionado.";
        $tipo_mensaje = "error";
        $mostrar_modal = true;
        $pagina_destino = "list_table.php";
    } else {
        // Obtener y validar datos del formulario
        $cedula = isset($post_data['cedula']) ? trim($post_data['cedula']) : '';
        $prefijo = isset($post_data['prefijo']) ? trim($post_data['prefijo']) : '';
        $tomo = isset($post_data['tomo']) ? trim($post_data['tomo']) : '';
        $asiento = isset($post_data['asiento']) ? trim($post_data['asiento']) : '';
        $nombre1 = isset($post_data['nombre1']) ? trim($post_data['nombre1']) : '';
        $nombre2 = isset($post_data['nombre2']) ? trim($post_data['nombre2']) : '';
        $apellido1 = isset($post_data['apellido1']) ? trim($post_data['apellido1']) : '';
        $apellido2 = isset($post_data['apellido2']) ? trim($post_data['apellido2']) : '';
        $apellidoc = isset($post_data['apellidoc']) ? trim($post_data['apellidoc']) : '';
        $usa_ac = isset($post_data['usa_ac']) ? $post_data['usa_ac'] : '0';
        $genero = isset($post_data['genero']) ? $post_data['genero'] : '';
        $estado_civil = isset($post_data['estado_civil']) ? $post_data['estado_civil'] : '';
        $tipo_sangre = isset($post_data['tipo_sangre']) ? $post_data['tipo_sangre'] : '';
        $f_nacimiento = isset($post_data['f_nacimiento']) ? $post_data['f_nacimiento'] : '';
        $nacionalidad = isset($post_data['nacionalidad']) ? $post_data['nacionalidad'] : '';
        $celular = isset($post_data['celular']) ? trim($post_data['celular']) : '';
        $telefono = isset($post_data['telefono']) ? trim($post_data['telefono']) : '';
        $correo = isset($post_data['correo']) ? trim($post_data['correo']) : '';
        $contraseña = isset($post_data['contraseña']) ? trim($post_data['contraseña']) : '';
        $provincia = isset($post_data['provincia']) ? $post_data['provincia'] : '';
        $distrito = isset($post_data['distrito']) ? $post_data['distrito'] : '';
        $corregimiento = isset($post_data['corregimiento']) ? $post_data['corregimiento'] : '';
        $calle = isset($post_data['calle']) ? trim($post_data['calle']) : '';
        $casa = isset($post_data['casa']) ? trim($post_data['casa']) : '';
        $comunidad = isset($post_data['comunidad']) ? trim($post_data['comunidad']) : '';
        $cargo = isset($post_data['cargo']) ? $post_data['cargo'] : '';
        $departamento = isset($post_data['departamento']) ? $post_data['departamento'] : '';
        $f_contra = isset($post_data['f_contra']) ? $post_data['f_contra'] : date('Y-m-d');
        $estado = isset($post_data['estado']) ? $post_data['estado'] : '1';
        
        // Realizar validaciones básicas
        $datos_validos = true;
        
        // Validar apellido de casada cuando está activado
        if ($usa_ac === '1' && empty($apellidoc)) {
            $datos_validos = false;
            $mensaje = "Si usa apellido de casada, debe proporcionarlo.";
            $tipo_mensaje = "error";
        }
        
        // Si los datos son válidos, actualizar el empleado
        if ($datos_validos) {
            // Preparar columnas y valores para actualizar
            $columns = [
                'cedula', 'prefijo', 'tomo', 'asiento', 'nombre1', 'nombre2', 
                'apellido1', 'apellido2', 'apellidoc', 'usa_ac', 'genero', 
                'estado_civil', 'tipo_sangre', 'f_nacimiento', 'nacionalidad',
                'celular', 'telefono', 'correo', 'contraseña', 'provincia', 'distrito', 
                'corregimiento', 'calle', 'casa', 'comunidad', 'cargo', 
                'departamento', 'f_contra', 'estado'
            ];
            
            $values = [
                $cedula, $prefijo, $tomo, $asiento, $nombre1, $nombre2,
                $apellido1, $apellido2, $apellidoc, $usa_ac, $genero,
                $estado_civil, $tipo_sangre, $f_nacimiento, $nacionalidad,
                $celular, $telefono, $correo, $contraseña, $provincia, $distrito,
                $corregimiento, $calle, $casa, $comunidad, $cargo,
                $departamento, $f_contra, $estado
            ];
            
            // Actualizar empleado
            $result = $dbHandler->update('empleados', $columns, $values, 'cedula', $id_empleado);
            
            if ($result['status'] === 'ok') {
                $mensaje = "Empleado actualizado correctamente.";
                $tipo_mensaje = "success";
                $proceso_completado = true;
            } else {
                $mensaje = "Error al actualizar: " . $result['message'];
                $tipo_mensaje = "error";
            }
        }
        
        $pagina_destino = "employee_details.php?id=" . $id_empleado;
        $mostrar_modal = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Empleado</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
</head>
<body>
    <?php 
    if ($mostrar_modal) {
        $titulo = $proceso_completado ? 'Empleado Actualizado' : 'Error en la Actualización';
        
        // Definir opciones para la modal: Lista de empleados o volver a detalles
        $opciones_modal = [
            [
                'url' => 'list_table.php',
                'texto' => 'Ver Lista de Empleados',
                'clase' => 'btn-secondary'
            ],
            [
                'url' => $pagina_destino,
                'texto' => 'Volver a Detalles',
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