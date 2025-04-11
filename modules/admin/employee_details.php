<?php
// Incluir archivos de configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';

require_once '../../components/sidebar_menu.php';


// Incluir los componentes necesarios
require_once '../../components/employee_personal_info.php';
require_once '../../components/employee_contact_info.php';
require_once '../../components/employee_address_info.php';
require_once '../../components/employee_work_info.php';

/**
 * Clase para gestionar los detalles de empleados
 */
class EmployeeDetails {
    private $conn;
    private $employeeId;
    private $employeeData;
    
    /**
     * Constructor de la clase
     */
    public function __construct($connection) {
        $this->conn = $connection;
        
        // Obtener el ID del empleado desde la URL
        if(isset($_GET['id'])) {
            $this->employeeId = $_GET['id'];
            $this->loadEmployeeData();
        }
    }
    
    /**
     * Cargar los datos del empleado desde la base de datos
     */
    private function loadEmployeeData() {
        if(!empty($this->employeeId)) {
            $query = "SELECT e.*, 
                      p.nombre_provincia, 
                      d.nombre_distrito, 
                      c.nombre_corregimiento,
                      n.pais as nombre_nacionalidad,
                      dep.nombre as nombre_departamento,
                      car.nombre as nombre_cargo
                      FROM empleados e
                      LEFT JOIN provincia p ON e.provincia = p.codigo_provincia
                      LEFT JOIN distrito d ON e.distrito = d.codigo_distrito
                      LEFT JOIN corregimiento c ON e.corregimiento = c.codigo_corregimiento
                      LEFT JOIN nacionalidad n ON e.nacionalidad = n.codigo
                      LEFT JOIN departamento dep ON e.departamento = dep.codigo
                      LEFT JOIN cargo car ON e.cargo = car.codigo
                      WHERE e.cedula = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $this->employeeId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                $this->employeeData = $result->fetch_assoc();
            } else {
                echo "No se encontró el empleado.";
                exit;
            }
        }
    }
    
    /**
     * Obtener las opciones para los campos de selección
     */
    public function getOptions($table, $code_field, $name_field) {
        $options = [];
        $query = "SELECT $code_field, $name_field FROM $table ORDER BY $name_field";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $options[$row[$code_field]] = $row[$name_field];
            }
        }
        
        return $options;
    }
    
    /**
     * Generar opciones para selects con el valor seleccionado
     */
    public function generateSelectOptions($options, $selected_value) {
        $html = '';
        foreach($options as $value => $text) {
            $selected = ($value == $selected_value) ? 'selected' : '';
            $html .= "<option value='$value' $selected>$text</option>";
        }
        return $html;
    }
    
    /**
     * Generar opciones para género
     */
    public function getGenderOptions() {
        $options = [
            '1' => 'Masculino',
            '2' => 'Femenino'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['genero']);
    }
    
    /**
     * Generar opciones para estado civil
     */
    public function getCivilStatusOptions() {
        $options = [
            '1' => 'Soltero/a',
            '2' => 'Casado/a',
            '3' => 'Divorciado/a',
            '4' => 'Viudo/a'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['estado_civil']);
    }
    
    /**
     * Generar opciones para tipo de sangre
     */
    public function getBloodTypeOptions() {
        $options = [
            'Desconocido' => 'Desconocido',
            'O+' => 'O+',
            'O-' => 'O-',
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'AB+' => 'AB+',
            'AB-' => 'AB-'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['tipo_sangre']);
    }
    
    /**
     * Generar opciones para usa apellido de casada
     */
    public function getUsaAcOptions() {
        $options = [
            '0' => 'No',
            '1' => 'Sí'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['usa_ac']);
    }
    
    /**
     * Generar opciones para estado de empleado
     */
    public function getStatusOptions() {
        $options = [
            '0' => 'Inactivo',
            '1' => 'Activo'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['estado']);
    }
    
    /**
     * Renderizar el formulario de detalles de empleado
     */
    public function renderForm() {
        // Inicializar los componentes con los datos del empleado
        $personalInfo = new EmployeePersonalInfo($this->employeeData, $this);
        $contactInfo = new EmployeeContactInfo($this->employeeData);
        $addressInfo = new EmployeeAddressInfo($this->employeeData, $this);
        $workInfo = new EmployeeWorkInfo($this->employeeData, $this);
        
        // Renderizar el formulario
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Detalles del Empleado</title>
            <link rel="stylesheet" href="../../assets/global/root.css">
            <link rel="stylesheet" href="../../assets/admin/employee_details.css">
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        </head>
        <body>
        <?php 
        // Renderizar el sidebar indicando la página activa
        renderSidebar('employee_details'); 
        ?>
            <div class="container">
                <?php include_once '../../components/sidebar_menu.php'; ?>
                
                <main class="main-content">
                    <div class="card employee-card">
                        <h1 class="text-center">Detalles del Empleado</h1>
                        
                        <form id="employeeForm" method="POST" action="update_employee.php">
                            <input type="hidden" name="cedula" value="<?php echo $this->employeeData['cedula']; ?>">
                            
                            <?php 
                            // Renderizar cada componente
                            $personalInfo->render();
                            $contactInfo->render();
                            $addressInfo->render();
                            $workInfo->render();
                            ?>
                            
                            <div class="button-group">
                                <button type="submit" class="btn">Guardar</button>
                                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
            
            <script>
            // Script para cargar los distritos y corregimientos de forma dinámica
            document.addEventListener('DOMContentLoaded', function() {
                const provinciaSelect = document.getElementById('provincia');
                const distritoSelect = document.getElementById('distrito');
                const corregimientoSelect = document.getElementById('corregimiento');
                
                // Función para cargar distritos según la provincia seleccionada
                provinciaSelect.addEventListener('change', function() {
                    const provinciaId = this.value;
                    
                    // Limpiar las opciones actuales
                    distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
                    corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                    
                    // Realizar petición AJAX para obtener distritos
                    fetch(`get_distritos.php?provincia=${provinciaId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(distrito => {
                                const option = document.createElement('option');
                                option.value = distrito.codigo_distrito;
                                option.textContent = distrito.nombre_distrito;
                                distritoSelect.appendChild(option);
                            });
                        });
                });
                
                // Función para cargar corregimientos según el distrito seleccionado
                distritoSelect.addEventListener('change', function() {
                    const distritoId = this.value;
                    
                    // Limpiar las opciones actuales
                    corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                    
                    // Realizar petición AJAX para obtener corregimientos
                    fetch(`get_corregimientos.php?distrito=${distritoId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(corregimiento => {
                                const option = document.createElement('option');
                                option.value = corregimiento.codigo_corregimiento;
                                option.textContent = corregimiento.nombre_corregimiento;
                                corregimientoSelect.appendChild(option);
                            });
                        });
                });
            });
            </script>
        </body>
        </html>
        <?php
    }
}

// Iniciar la conexión a la base de datos
// $config = new Config();
// $conn = $config->getConnection();

// // Crear instancia de EmployeeDetails y renderizar el formulario
// $employeeDetails = new EmployeeDetails($conn);

$_GET['id'] = 'V12345678';

class FakeResult {
    private $data;
    private $index = 0;
    public $num_rows;

    public function __construct($data) {
        $this->data = $data;
        $this->num_rows = count($data);
    }

    public function fetch_assoc() {
        if ($this->index < count($this->data)) {
            return $this->data[$this->index++];
        }
        return null;
    }
}

class FakeStmt {
    public function bind_param($types, &$param) {}
    public function execute() {}
    public function get_result() {
        $fakeData = [
            [
                'cedula'       => 'V12345678',
                'prefijo'      => 'Mr.',
                'tomo'         => '001',
                'asiento'      => '010',
                'nombre1'      => 'Juan',
                'nombre2'      => 'Carlos',
                'apellido1'    => 'Perez',
                'apellido2'    => 'Lopez',
                'apellidoc'    => 'Martinez',
                'usa_ac'       => '1',
                'genero'       => '1',
                'estado_civil' => '2',
                'tipo_sangre'  => '',
                'f_nacimiento' => '1985-05-15',
                'nacionalidad' => '1',
                'celular'      => '04141234567',
                'telefono'     => '02121234567',
                'correo'       => 'juan@example.com',
                'provincia'    => '1',
                'distrito'     => '1',
                'corregimiento'=> '1',
                'calle'        => 'Av. Siempre Viva',
                'casa'         => '42',
                'comunidad'    => 'Comunidad1',
                'cargo'        => '1',
                'departamento' => '1',
                'f_contra'     => '2010-03-20',
                'estado'       => '1'
            ]
        ];
        return new FakeResult($fakeData);
    }
}

class FakeDBConnection {
    public function prepare($query) {
        return new FakeStmt();
    }

    public function query($query) {
        if (strpos($query, 'FROM nacionalidad') !== false) {
            $data = [['codigo' => '1', 'pais' => 'Venezolano']];
        } elseif (strpos($query, 'FROM departamento') !== false) {
            $data = [['codigo' => '1', 'nombre' => 'Recursos Humanos']];
        } elseif (strpos($query, 'FROM cargo') !== false) {
            $data = [['codigo' => '1', 'nombre' => 'Gerente']];
        } elseif (strpos($query, 'FROM provincia') !== false) {
            $data = [['codigo_provincia' => '1', 'nombre_provincia' => 'Provincia1']];
        } elseif (strpos($query, 'FROM distrito') !== false) {
            $data = [['codigo_distrito' => '1', 'nombre_distrito' => 'Distrito1']];
        } elseif (strpos($query, 'FROM corregimiento') !== false) {
            $data = [['codigo_corregimiento' => '1', 'nombre_corregimiento' => 'Corregimiento1']];
        } else {
            $data = [];
        }
        return new FakeResult($data);
    }
}

$conn = new FakeDBConnection();

$employee = new EmployeeDetails($conn);
$employeeDetails = $employee;


$employeeDetails->renderForm();
?>