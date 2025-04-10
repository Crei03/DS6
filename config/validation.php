<?php
/**
 * Funciones de validación para formularios
 * 
 * Este archivo contiene las funciones necesarias para validar
 * diferentes tipos de entrada en los formularios de la aplicación
 */

/**
 * Valida que un valor contenga solo números
 * 
 * @param string $valor El valor a validar
 * @return string El valor limpiado (solo números)
 */
function validarSoloNumeros($valor) {
    // Eliminar cualquier carácter que no sea un número
    return preg_replace('/[^0-9]/', '', $valor);
}

/**
 * Valida que un valor contenga solo letras y espacios
 * 
 * @param string $valor El valor a validar
 * @return string El valor limpiado (solo letras y espacios)
 */
function validarSoloLetras($valor) {
    // Eliminar cualquier carácter que no sea una letra o espacio
    return preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/', '', $valor);
}

/**
 * Valida un formato específico de cédula panameña
 * 
 * @param string $cedula La cédula a validar
 * @return bool True si la cédula tiene un formato válido, false en caso contrario
 */
function validarFormatoCedula($cedula) {
    // Formato típico de cédula panameña (ajustar según necesidades)
    $patron = '/^[0-9]{1,2}-[0-9]{1,4}-[0-9]{1,6}$/';
    return preg_match($patron, $cedula);
}

/**
 * Sanitiza todos los datos de entrada de un formulario
 * 
 * @param array $datos Array asociativo con los datos del formulario
 * @return array Datos sanitizados
 */
function sanitizarDatosFormulario($datos) {
    $datosSanitizados = [];
    
    foreach ($datos as $campo => $valor) {
        // Convertir caracteres especiales a entidades HTML
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        
        // Eliminar espacios en blanco al inicio y al final
        $valor = trim($valor);
        
        $datosSanitizados[$campo] = $valor;
    }
    
    return $datosSanitizados;
}

/**
 * Valida los datos de inicio de sesión
 * 
 * @param string $cedula La cédula del usuario
 * @param string $password La contraseña del usuario
 * @return array Array con errores encontrados o vacío si no hay errores
 */
function validarLogin($cedula, $password) {
    $errores = [];
    
    // Validar cédula
    if (empty($cedula)) {
        $errores[] = "La cédula es obligatoria";
    } else {
        $cedulaLimpia = validarSoloNumeros($cedula);
        if (strlen($cedulaLimpia) < 8 || strlen($cedulaLimpia) > 13) {
            $errores[] = "El formato de la cédula es inválido";
        }
    }
    
    // Validar contraseña
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria";
    }
    
    return $errores;
}