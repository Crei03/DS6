<?php
/**
 * Utilidades para comunicación con el controlador API
 */
class ApiUtils {
    /**
     * Realiza una petición al controlador
     * 
     * @param string $action Acción a realizar
     * @param string $table Tabla a consultar
     * @param array $data Datos adicionales
     * @return array Respuesta del controlador
     */
    public static function fetchFromController($action, $table = '', $data = []) {
        $url = $_SERVER['DOCUMENT_ROOT'] . '/DS6/config/controlador.php';        
        
        $payload = [
            'action' => $action,
            'data' => $data
        ];
        
        if (!empty($table)) {
            $payload['table'] = $table;
        }
        
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($payload)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === FALSE) {
            return ['status' => 'error', 'message' => 'Error al comunicarse con el controlador'];
        }
        
        return json_decode($result, true);
    }
    
    /**
     * Genera opciones para select a partir de un array
     */
    public static function generateSelectOptions($options, $selected_value = '') {
        $html = '<option value="">Seleccionar</option>';
        foreach($options as $option) {
            $value = $option['value'] ?? '';
            $text = $option['text'] ?? '';
            $selected = ($value == $selected_value) ? 'selected' : '';
            $html .= "<option value='$value' $selected>$text</option>";
        }
        return $html;
    }
}
?>