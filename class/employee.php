<?php
/**
 * Clase Employee
 * 
 * Esta clase maneja todas las operaciones relacionadas con los empleados:
 * - Obtener datos del empleado
 * - Actualizar información personal
 * - Actualizar información de contacto
 * - Actualizar información de dirección
 */
class Employee {
    private $db;
    private $conn;
    private $data;
    private $cedula;

    /**
     * Constructor de la clase Employee
     * 
     * @param string $cedula Cédula del empleado
     */
    public function __construct($cedula = null) {
        global $conn;
        
        $this->conn = $conn;
        $this->db = new DBHandler($conn);
        $this->cedula = $cedula;
        
        if ($cedula) {
            $this->loadEmployeeData();
        }
    }
    
    /**
     * Carga los datos del empleado desde la base de datos
     * 
     * @return bool True si se encontraron datos, false en caso contrario
     */
    private function loadEmployeeData() {
        // Obtener los datos del empleado desde la base de datos
        $result = $this->db->selectOne('empleados', 'cedula', $this->cedula);
        
        if ($result['status'] === 'ok' && isset($result['data'])) {
            $this->data = $result['data'];
            return true;
        }
        
        // Si no hay datos, crear una estructura vacía
        $this->data = [
            'id' => null,
            'prefijo' => '',
            'tomo' => '',
            'asiento' => '',
            'nombre1' => '',
            'nombre2' => '',
            'apellido1' => '',
            'apellido2' => '',
            'apellidoc' => '',
            'usa_ac' => 0,
            'genero' => '',
            'estado_civil' => '',
            'tipo_sangre' => '',
            'f_nacimiento' => '',
            'nacionalidad' => '',
            'celular' => '',
            'telefono' => '',
            'correo' => '',
            'provincia' => '',
            'distrito' => '',
            'corregimiento' => '',
            'calle' => '',
            'casa' => '',
            'comunidad' => '',
            'departamento' => '',
            'cargo' => '',
            'f_contra' => '',
            'estado' => ''
        ];
        
        return false;
    }
    
    /**
     * Obtiene todos los datos del empleado
     * 
     * @return array Datos del empleado
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Obtiene un dato específico del empleado
     * 
     * @param string $campo Nombre del campo
     * @return mixed Valor del campo o null si no existe
     */
    public function get($campo) {
        return $this->data[$campo] ?? null;
    }
    
    /**
     * Obtiene los datos personales del empleado
     * 
     * @return array Datos personales del empleado
     */
    public function getPersonalInfo() {
        return [
            'cedula' => $this->cedula['cedula'],
            'prefijo' => $this->data['prefijo'],
            'tomo' => $this->data['tomo'],
            'asiento' => $this->data['asiento'],
            'nombre1' => $this->data['nombre1'],
            'nombre2' => $this->data['nombre2'],
            'apellido1' => $this->data['apellido1'],
            'apellido2' => $this->data['apellido2'],
            'apellidoc' => $this->data['apellidoc'],
            'usa_ac' => $this->data['usa_ac'],
            'genero' => $this->data['genero'],
            'estado_civil' => $this->data['estado_civil'],
            'tipo_sangre' => $this->data['tipo_sangre'],
            'f_nacimiento' => $this->data['f_nacimiento'],
            'nacionalidad' => $this->data['nacionalidad']
        ];
    }
    
    /**
     * Obtiene los datos de contacto del empleado
     * 
     * @return array Datos de contacto del empleado
     */
    public function getContactInfo() {
        return [
            'celular' => $this->data['celular'],
            'telefono' => $this->data['telefono'],
            'correo' => $this->data['correo']
        ];
    }
    
    /**
     * Obtiene los datos de dirección del empleado
     * 
     * @return array Datos de dirección del empleado
     */
    public function getAddressInfo() {
        return [
            'provincia' => $this->data['provincia'],
            'distrito' => $this->data['distrito'],
            'corregimiento' => $this->data['corregimiento'],
            'calle' => $this->data['calle'],
            'casa' => $this->data['casa'],
            'comunidad' => $this->data['comunidad']
        ];
    }
    
    /**
     * Obtiene los datos laborales del empleado
     * 
     * @return array Datos laborales del empleado
     */
    public function getWorkInfo() {
        return [
            'departamento' => $this->data['departamento'],
            'cargo' => $this->data['cargo'],
            'f_contra' => $this->data['f_contra'],
            'estado' => $this->data['estado']
        ];
    }
    
    /**
     * Actualiza la información personal del empleado
     * 
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function updatePersonalInfo($data) {
        $columns = ['nombre1', 'nombre2', 'apellido1', 'apellido2', 'apellidoc', 'usa_ac', 'estado_civil', 'tipo_sangre'];
        $values = [
            $data['nombre1'],
            $data['nombre2'] ?? '',
            $data['apellido1'],
            $data['apellido2'] ?? '',
            $data['apellidoc'] ?? '',
            $data['usa_ac'] ?? 0,
            $data['estado_civil'],
            $data['tipo_sangre']
        ];
        
        $result = $this->db->update('empleados', $columns, $values, 'cedula', $this->cedula);
        
        if ($result['status'] === 'ok') {
            // Actualizar los datos en memoria
            foreach ($data as $key => $value) {
                if (isset($this->data[$key])) {
                    $this->data[$key] = $value;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Actualiza la información de contacto del empleado
     * 
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function updateContactInfo($data) {
        $columns = ['celular', 'telefono', 'correo'];
        $values = [
            $data['celular'],
            $data['telefono'] ?? '',
            $data['correo']
        ];
        
        $result = $this->db->update('empleados', $columns, $values, 'cedula', $this->cedula);
        
        if ($result['status'] === 'ok') {
            // Actualizar los datos en memoria
            foreach ($data as $key => $value) {
                if (isset($this->data[$key])) {
                    $this->data[$key] = $value;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Actualiza la información de dirección del empleado
     * 
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function updateAddressInfo($data) {
        $columns = ['provincia', 'distrito', 'corregimiento', 'calle', 'casa', 'comunidad'];
        $values = [
            $data['provincia'],
            $data['distrito'],
            $data['corregimiento'],
            $data['calle'] ?? '',
            $data['casa'] ?? '',
            $data['comunidad'] ?? ''
        ];
        
        $result = $this->db->update('empleados', $columns, $values, 'cedula', $this->cedula);
        
        if ($result['status'] === 'ok') {
            // Actualizar los datos en memoria
            foreach ($data as $key => $value) {
                if (isset($this->data[$key])) {
                    $this->data[$key] = $value;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Obtiene opciones para selects desde la base de datos
     * 
     * @param string $tabla Nombre de la tabla
     * @param string $valor_campo Campo para el valor de la opción
     * @param string $texto_campo Campo para el texto de la opción
     * @return array Opciones para el select
     */
    public function getOptions($tabla, $valor_campo, $texto_campo) {
        $result = $this->db->selectAll($tabla);
        
        if ($result['status'] === 'ok') {
            return $result['data'];
        }
        
        return [];
    }

    /**
     * Función especializada para obtener datos de provincias desde la BD
     * @return array Array con datos de provincias
     */
    public function getProvincias() {
        $db = new DBHandler();
        $result = $db->selectAll('provincia');
        
        $provincias = [];
        if ($result['status'] === 'ok') {
            foreach ($result['data'] as $provincia) {
                $provincias[] = [
                    'value' => $provincia['codigo_provincia'],
                    'text' => $provincia['nombre_provincia']
                ];
            }
        }
        
        return $provincias;
    }

    /**
     * Función especializada para obtener datos de distritos desde la BD
     * @param string $provincia_codigo Código de provincia (opcional para filtrar)
     * @return array Array con datos de distritos
     */
    public function getDistritos($provincia_codigo = null) {
        // Usar la instancia $this->db que ya tiene la conexión
        if ($provincia_codigo) {
            $result = $this->db->getDistritosByProvincia($provincia_codigo);
            $resultData = ($result['status'] === 'ok') ? $result['data'] : [];
        } else {
            // Si no se filtra, obtener todos (ajustar si es necesario o eliminar esta lógica si siempre se filtra)
            $result = $this->db->selectAll('distrito');
            $resultData = ($result['status'] === 'ok') ? $result['data'] : [];
        }

        $distritos = [];
        foreach ($resultData as $distrito) {
            $distritos[] = [
                // Asegúrate que las claves coincidan con las devueltas por DBHandler
                'value' => $distrito['codigo_distrito'],
                'text' => $distrito['nombre_distrito']
            ];
        }

        return $distritos;
    }

    /**
     * Función especializada para obtener datos de corregimientos desde la BD
     * @param string $distrito_codigo Código de distrito (opcional para filtrar)
     * @return array Array con datos de corregimientos
     */
    public function getCorregimientos($distrito_codigo = null) {
        // Usar la instancia $this->db que ya tiene la conexión
        if ($distrito_codigo) {
            $result = $this->db->getCorregimientosByDistrito($distrito_codigo);
             $resultData = ($result['status'] === 'ok') ? $result['data'] : [];
        } else {
            // Si no se filtra, obtener todos (ajustar si es necesario o eliminar esta lógica si siempre se filtra)
            $result = $this->db->selectAll('corregimiento');
             $resultData = ($result['status'] === 'ok') ? $result['data'] : [];
        }

        $corregimientos = [];
        foreach ($resultData as $corregimiento) {
            $corregimientos[] = [
                 // Asegúrate que las claves coincidan con las devueltas por DBHandler
                'value' => $corregimiento['codigo_corregimiento'],
                'text' => $corregimiento['nombre_corregimiento']
            ];
        }

        return $corregimientos;
    }

    /**
     * Función especializada para obtener datos de nacionalidades desde la BD
     * @return array Array con datos de nacionalidades
     */
    public function getNacionalidades() {
        $db = new DBHandler();
        $result = $db->selectAll('nacionalidad');
        
        $nacionalidades = [];
        if ($result['status'] === 'ok') {
            foreach ($result['data'] as $nacionalidad) {
                $nacionalidades[] = [
                    'value' => $nacionalidad['codigo'],
                    'text' => $nacionalidad['pais']
                ];
            }
        }
        
        return $nacionalidades;
    }

    /**
     * Función especializada para obtener datos de departamentos desde la BD
     * @return array Array con datos de departamentos
     */
    public function getDepartamentos() {
        $db = new DBHandler();
        $result = $db->selectAll('departamento');
        
        $departamentos = [];
        if ($result['status'] === 'ok') {
            foreach ($result['data'] as $depto) {
                $departamentos[] = [
                    'value' => $depto['codigo'],
                    'text' => $depto['nombre']
                ];
            }
        }
        
        return $departamentos;
    }

    /**
     * Función especializada para obtener datos de cargos desde la BD
     * @param string $departamento_codigo Código de departamento (opcional para filtrar)
     * @return array Array con datos de cargos
     */
    public function getCargos($departamento_codigo = null) {
        $db = new DBHandler();
        
        if ($departamento_codigo) {
            // Aquí asumo que tienes una columna departamento_codigo en la tabla cargo
            // Ajusta según la estructura real de tu BD
            $stmt = $db->conn->prepare("SELECT * FROM cargo WHERE departamento_codigo = ?");
            $stmt->bind_param("s", $departamento_codigo);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $result = $db->selectAll('cargo');
            $result = $result['status'] === 'ok' ? $result['data'] : [];
        }
        
        $cargos = [];
        foreach ($result as $cargo) {
            $cargos[] = [
                'value' => $cargo['codigo'],
                'text' => $cargo['nombre']
            ];
        }
        
        return $cargos;
    }

    /**
     * Obtiene las opciones para el género
     * @return array Array con opciones de género
     */
    public function getGenderOptions() {
        $options = [
            ['value' => '0', 'text' => 'Masculino'],
            ['value' => '1', 'text' => 'Femenino']
        ];
        
        return $options;
    }

    /**
     * Obtiene las opciones para el estado civil
     * @return array Array con opciones de estado civil
     */
    public function getCivilStatusOptions() {
        $options = [
            ['value' => '0', 'text' => 'Soltero/a'],
            ['value' => '1', 'text' => 'Casado/a'],
            ['value' => '2', 'text' => 'Divorciado/a'],
            ['value' => '3', 'text' => 'Viudo/a']
        ];
        
        return $options;
    }

    /**
     * Obtiene las opciones para el tipo de sangre
     * @return array Array con opciones de tipo de sangre
     */
    public function getBloodTypeOptions() {
        $options = [
            ['value' => 'Desconocido', 'text' => 'Desconocido'],
            ['value' => 'A', 'text' => 'A'],
            ['value' => 'B', 'text' => 'B'],
            ['value' => 'AB', 'text' => 'AB'],
            ['value' => 'O', 'text' => 'O'],
            ['value' => 'A+', 'text' => 'A+'],
            ['value' => 'A-', 'text' => 'A-'],
            ['value' => 'B+', 'text' => 'B+'],
            ['value' => 'B-', 'text' => 'B-'],
            ['value' => 'AB+', 'text' => 'AB+'],
            ['value' => 'AB-', 'text' => 'AB-'],
            ['value' => 'O+', 'text' => 'O+'],
            ['value' => 'O-', 'text' => 'O-']
        ];
        
        return $options;
    }

    /**
     * Obtiene las opciones para el uso de apellido de casada
     * @return array Array con opciones de uso de apellido de casada
     */
    public function getUsaAcOptions() {
        $options = [
            ['value' => '0', 'text' => 'No'],
            ['value' => '1', 'text' => 'Sí']
        ];
        
        return $options;
    }

    /**
     * Obtiene las opciones para el estado del empleado
     * @return array Array con opciones de estado del empleado
     */
    public function getStatusOptions() {
        $options = [
            ['value' => '0', 'text' => 'Inactivo'],
            ['value' => '1', 'text' => 'Activo']
        ];
        
        return $options;
    }
}
?>