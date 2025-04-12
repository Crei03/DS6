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
        $html = '<option value="">Seleccionar</option>';
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
            '0' => 'Masculino',
            '1' => 'Femenino'
        ];
        return $this->generateSelectOptions($options, $this->employeeData['genero']);
    }
    
    /**
     * Generar opciones para estado civil
     */
    public function getCivilStatusOptions() {
        $options = [
            '0' => 'Soltero/a',
            '1' => 'Casado/a',
            '2' => 'Divorciado/a',
            '3' => 'Viudo/a'
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
            // Funciones de validación para inputs
            function validarSoloNumeros(valor) {
                // Eliminar cualquier carácter que no sea un número
                return valor.replace(/[^0-9]/g, '');
            }

            function validarSoloLetras(valor) {
                // Eliminar cualquier carácter que no sea una letra o espacio
                return valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
            }
            
            // Validación para prevenir envío de formulario con opciones "Seleccionar" o campos obligatorios vacíos
            document.addEventListener('DOMContentLoaded', function() {
                const employeeForm = document.getElementById('employeeForm');
                
                // Aplicar estilos iniciales a los selects vacíos
                const selectElements = employeeForm.querySelectorAll('select');
                selectElements.forEach(function(select) {
                    if (select.value === '') {
                        select.classList.add('invalid-select');
                    }
                    
                    select.addEventListener('change', function() {
                        if (this.value !== '') {
                            this.classList.remove('invalid-select');
                        } else {
                            this.classList.add('invalid-select');
                        }
                    });
                });
                
                // Campos que deben ser excluidos de la validación obligatoria
                const excludedFields = ['nombre2', 'apellido2', 'casa', 'comunidad'];
                
                // Obtener todos los inputs con atributo 'required'
                let inputElements = Array.from(employeeForm.querySelectorAll('input[required]'));
                
                // Filtrar los campos excluidos
                inputElements = inputElements.filter(input => {
                    return !excludedFields.includes(input.id) && !excludedFields.includes(input.name);
                });
                
                // Manejo especial para el apellido de casada
                const usaAcSelect = document.getElementById('usa_ac');
                const apellidoCasadaField = document.getElementById('apellidoc');
                
                // Función para actualizar la validación del apellido de casada
                function updateApellidoCasadaValidation() {
                    if (usaAcSelect && apellidoCasadaField) {
                        if (usaAcSelect.value === '1') { // Si usa apellido de casada es "Sí"
                            // Agregar al array de inputs a validar si no está
                            const index = inputElements.indexOf(apellidoCasadaField);
                            if (index === -1) {
                                inputElements.push(apellidoCasadaField);
                            }
                            
                            // Verificar estado actual y aplicar estilo si está vacío
                            if (apellidoCasadaField.value.trim() === '') {
                                apellidoCasadaField.classList.add('invalid-input');
                            } else {
                                apellidoCasadaField.classList.remove('invalid-input');
                            }
                        } else {
                            // Si no usa apellido de casada, quitarlo del array de validación
                            const index = inputElements.indexOf(apellidoCasadaField);
                            if (index > -1) {
                                inputElements.splice(index, 1);
                            }
                            // Y quitar cualquier estilo de error
                            apellidoCasadaField.classList.remove('invalid-input');
                        }
                    }
                }
                
                // Configurar evento para el cambio en usa_ac
                if (usaAcSelect) {
                    usaAcSelect.addEventListener('change', updateApellidoCasadaValidation);
                    // Ejecutar una vez al inicio para establecer el estado inicial
                    updateApellidoCasadaValidation();
                }
                
                // Aplicar estilos iniciales a todos los inputs requeridos vacíos
                inputElements.forEach(function(input) {
                    // Verificar estado inicial
                    if (input.value.trim() === '') {
                        input.classList.add('invalid-input');
                    }
                    
                    // Escuchar cambios en tiempo real
                    input.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            this.classList.remove('invalid-input');
                        } else {
                            this.classList.add('invalid-input');
                        }
                    });
                });
                
                // Validación al enviar el formulario
                employeeForm.addEventListener('submit', function(event) {
                    let formValid = true;
                    let firstInvalidField = null;
                    
                    // Validar selects
                    selectElements.forEach(function(select) {
                        if (select.value === '') {
                            formValid = false;
                            select.classList.add('invalid-select');
                            
                            if (!firstInvalidField) {
                                firstInvalidField = select;
                            }
                        } else {
                            select.classList.remove('invalid-select');
                        }
                    });
                    
                    // Validar inputs requeridos
                    inputElements.forEach(function(input) {
                        // Excepción para apellido de casada cuando no se usa
                        if (input.id === 'apellidoc' && usaAcSelect && usaAcSelect.value === '0') {
                            return;
                        }
                        
                        if (input.value.trim() === '') {
                            formValid = false;
                            input.classList.add('invalid-input');
                            
                            if (!firstInvalidField) {
                                firstInvalidField = input;
                            }
                        } else {
                            input.classList.remove('invalid-input');
                        }
                    });
                    
                    // Si hay campos inválidos, prevenir envío y mostrar mensaje
                    if (!formValid) {
                        event.preventDefault();
                        alert('No se permite enviar el formulario con campos obligatorios vacíos. Por favor, complete todos los campos requeridos.');
                        
                        // Hacer scroll al primer campo inválido
                        if (firstInvalidField) {
                            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalidField.focus();
                        }
                    }
                });
            });
            
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
                'nombre1'      => '',
                'nombre2'      => 'Carlos',
                'apellido1'    => 'Perez',
                'apellido2'    => 'Lopez',
                'apellidoc'    => 'Martinez',
                'usa_ac'       => '1',
                'genero'       => '1',
                'estado_civil' => '2',
                'tipo_sangre'  => 'Desconocido',
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