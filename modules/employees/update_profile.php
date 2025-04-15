<?php
/**
 * Actualiza los datos personales del empleado desde su perfil
 */
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';
require_once '../../components/modal_result.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esEmpleado()) {
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
$activeTab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'personal';

// Verificar si se han enviado datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar los datos recibidos
    $post_data = sanitizarDatosFormulario($_POST);
    
    // Obtener ID del empleado (en este caso usamos la cédula)
    $id_empleado = isset($post_data['employee_id']) ? $post_data['employee_id'] : null;
    
    if (!$id_empleado) {
        $mensaje = "Error: ID de empleado no proporcionado.";
        $tipo_mensaje = "error";
        $mostrar_modal = true;
        $pagina_destino = "my_profile.php";
    } else {
        // Obtener el empleado actual para mantener los campos que no puede cambiar
        $employee = new Employee($id_empleado);
        $empleado_actual = $employee->getData();
        
        // Obtener la cédula como identificador único para la actualización
        $cedula_empleado = $empleado_actual['cedula'];
        
        // Mantener los campos que no se pueden modificar
        $prefijo = $empleado_actual['prefijo'];
        $tomo = $empleado_actual['tomo'];
        $asiento = $empleado_actual['asiento'];
        $cedula = $empleado_actual['cedula']; // No se modifica la cédula
        $genero = $empleado_actual['genero'];
        $f_nacimiento = $empleado_actual['f_nacimiento'];
        $nacionalidad = $empleado_actual['nacionalidad'];
        $cargo = $empleado_actual['cargo'];
        $departamento = $empleado_actual['departamento'];
        $f_contra = $empleado_actual['f_contra'];
        $estado = $empleado_actual['estado'];
        
        // Obtener campos que el empleado puede editar
        $nombre1 = isset($post_data['nombre1']) ? trim($post_data['nombre1']) : $empleado_actual['nombre1'];
        $nombre2 = isset($post_data['nombre2']) ? trim($post_data['nombre2']) : $empleado_actual['nombre2'];
        $apellido1 = isset($post_data['apellido1']) ? trim($post_data['apellido1']) : $empleado_actual['apellido1'];
        $apellido2 = isset($post_data['apellido2']) ? trim($post_data['apellido2']) : $empleado_actual['apellido2'];
        $apellidoc = isset($post_data['apellidoc']) ? trim($post_data['apellidoc']) : $empleado_actual['apellidoc'];
        $usa_ac = isset($post_data['usa_ac']) ? $post_data['usa_ac'] : $empleado_actual['usa_ac'];
        $estado_civil = isset($post_data['estado_civil']) ? $post_data['estado_civil'] : $empleado_actual['estado_civil'];
        $tipo_sangre = isset($post_data['tipo_sangre']) ? $post_data['tipo_sangre'] : $empleado_actual['tipo_sangre'];
        $celular = isset($post_data['celular']) ? trim($post_data['celular']) : $empleado_actual['celular'];
        $telefono = isset($post_data['telefono']) ? trim($post_data['telefono']) : $empleado_actual['telefono'];
        $correo = isset($post_data['correo']) ? trim($post_data['correo']) : $empleado_actual['correo'];
        $contraseña = isset($post_data['contraseña']) ? trim($post_data['contraseña']) : $empleado_actual['contraseña'];
        $provincia = isset($post_data['provincia']) ? $post_data['provincia'] : $empleado_actual['provincia'];
        $distrito = isset($post_data['distrito']) ? $post_data['distrito'] : $empleado_actual['distrito'];
        $corregimiento = isset($post_data['corregimiento']) ? $post_data['corregimiento'] : $empleado_actual['corregimiento'];
        $calle = isset($post_data['calle']) ? trim($post_data['calle']) : $empleado_actual['calle'];
        $casa = isset($post_data['casa']) ? trim($post_data['casa']) : $empleado_actual['casa'];
        $comunidad = isset($post_data['comunidad']) ? trim($post_data['comunidad']) : $empleado_actual['comunidad'];
        
        // Realizar validaciones
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
            
            // Actualizar empleado usando la cédula como identificador
            $result = $dbHandler->update('empleados', $columns, $values, 'cedula', $cedula);
            
            if ($result['status'] === 'ok') {
                $mensaje = "Perfil actualizado correctamente.";
                $tipo_mensaje = "success";
                $proceso_completado = true;
            } else {
                $mensaje = "Error al actualizar: " . $result['message'];
                $tipo_mensaje = "error";
            }
        }
        
        $pagina_destino = "my_profile.php?tab=" . $activeTab;
        $mostrar_modal = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil</title>
    <link rel="stylesheet" href="../../assets/global/root.css">
</head>
<body>
    <?php 
    if ($mostrar_modal) {
        $titulo = $proceso_completado ? 'Perfil Actualizado' : 'Error en la Actualización';
        
        // Opciones para la modal: Solo volver al perfil
        $opciones_modal = [
            [
                'url' => $pagina_destino,
                'texto' => 'Volver al Perfil',
                'clase' => 'btn-primary'
            ]
        ];
        
        $modal = new ModalResult($tipo_mensaje, $mensaje, $pagina_destino, $titulo, $opciones_modal);
        $modal->render();
    } else {
        // Si no hay datos para procesar, redirigir al perfil
        echo '<script>window.location.href = "my_profile.php";</script>';
    }
    ?>
</body>
</html>