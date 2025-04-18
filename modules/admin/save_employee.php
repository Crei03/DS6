<?php
/**
 * Archivo para procesar la creación y actualización de empleados
 * 
 * Este archivo maneja tanto la inserción de nuevos empleados como
 * la actualización de empleados existentes, utilizando las funciones
 * insert y update de la clase DBHandler.
 */

// Incluir archivos de configuración y clases necesarias
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';
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
$id_empleado = null;
$proceso_completado = false;
$pagina_destino = '';
$mostrar_modal = false;

// Verificar el método de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar todos los datos recibidos del formulario
    $post_data = sanitizarDatosFormulario($_POST);
    
    // Extraer el ID si estamos actualizando un empleado existente
    $id_empleado = isset($post_data['id_empleado']) ? $post_data['id_empleado'] : null;
    $modo = $id_empleado ? 'actualizar' : 'insertar';
    
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
    
    // Validar la identificación del empleado (Cédula o Tomo/Asiento)
    $id_valido = true;
    if ($prefijo) {
        // Si hay prefijo, estamos usando tomo/asiento
        if (!$tomo || !$asiento) {
            $id_valido = false;
            $mensaje = "Si usa tomo/asiento, debe proporcionar ambos campos.";
            $tipo_mensaje = "error";
        }
    } else if ($modo === 'insertar') {
        // Si no hay prefijo, validamos la cédula normal (solo para inserciones)
        if (!$cedula) {
            $id_valido = false;
            $mensaje = "Debe proporcionar el número de cédula.";
            $tipo_mensaje = "error";
        }
    }
    
    // Si el apellido de casada está habilitado pero vacío
    if ($usa_ac === '1' && empty($apellidoc)) {
        $id_valido = false;
        $mensaje = "Si usa apellido de casada, debe proporcionarlo.";
        $tipo_mensaje = "error";
    }
    
    // Verificar si ya existe el empleado (solo para inserción)
    if ($id_valido && $modo === 'insertar') {
        // Para identificación por cédula
        $existeEmpleado = $dbHandler->selectOne('empleados', 'cedula', $cedula);
        if ($existeEmpleado['status'] === 'ok') {
            $id_valido = false;
            $mensaje = "Ya existe un empleado con esa cédula en el sistema.";
            $tipo_mensaje = "error";
        }
    }
    
    // Si todo es válido, proceder con la operación
    if ($id_valido) {
        // Preparar columnas y valores
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
        
        // Realizar la operación según el modo
        if ($modo === 'insertar') {
            // Insertar nuevo empleado
            $result = $dbHandler->insert('empleados', $columns, $values);
            
            if ($result['status'] === 'ok') {
                $mensaje = "Empleado agregado correctamente.";
                $tipo_mensaje = "success";
                $proceso_completado = true;
                $pagina_destino = "list_table.php";
                $mostrar_modal = true;
            } else {
                $mensaje = "Error al guardar: " . $result['message'];
                $tipo_mensaje = "error";
                $mostrar_modal = true;
            }
        } else {
            // Actualizar empleado existente
            $result = $dbHandler->update('empleados', $columns, $values, 'id', $id_empleado);
            
            if ($result['status'] === 'ok') {
                $mensaje = "Empleado actualizado correctamente.";
                $tipo_mensaje = "success";
                $proceso_completado = true;
                $pagina_destino = "employee_details.php?id=" . $id_empleado;
                $mostrar_modal = true;
            } else {
                $mensaje = "Error al actualizar: " . $result['message'];
                $tipo_mensaje = "error";
                $mostrar_modal = true;
            }
        }
    } else {
        // Si hay errores de validación, mostrar la modal con el mensaje de error
        $mostrar_modal = true;
        $pagina_destino = $modo === 'insertar' ? 'list_table.php' : "employee_details.php?id=" . $id_empleado;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($modo === 'insertar') ? 'Guardar Empleado' : 'Actualizar Empleado'; ?></title>
    <link rel="stylesheet" href="../../assets/global/root.css">
    <link rel="stylesheet" href="../../assets/admin/employee_details.css">
</head>
<body>
    <?php 
    // Si hay un mensaje para mostrar, crear y renderizar la modal
    if ($mostrar_modal) {
        $titulo = $proceso_completado ? 
            ($modo === 'insertar' ? 'Empleado Agregado' : 'Empleado Actualizado') : 
            'Error en el Formulario';
        
        $modal = new ModalResult($tipo_mensaje, $mensaje, $pagina_destino, $titulo);
        $modal->render();
    } else {
        // Si no hay mensaje para mostrar (por ejemplo, acceso directo a este archivo)
        // Redirigir al formulario de agregar empleado
        echo '<script>window.location.href = "employee_add.php";</script>';
    }
    ?>
</body>
</html>

