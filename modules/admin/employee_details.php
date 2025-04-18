<?php
// Incluir archivos de configuración
require_once '../../config/config.php';
require_once '../../config/BdHandler.php';
require_once '../../config/validation.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';

// --- INICIO: Manejo de solicitudes AJAX ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Acción no válida'];
    $dbHandler = new DBHandler();

    if ($_GET['ajax'] === 'get_distritos' && isset($_GET['provincia_id'])) {
        $provincia_id = $_GET['provincia_id'];
        $result = $dbHandler->getDistritosByProvincia($provincia_id);
        if ($result['status'] === 'ok') {
            $response = ['status' => 'ok', 'data' => $result['data']];
        } else {
            $response['message'] = $result['message'];
        }
    } elseif ($_GET['ajax'] === 'get_corregimientos' && isset($_GET['distrito_id'])) {
        $distrito_id = $_GET['distrito_id'];
        $result = $dbHandler->getCorregimientosByDistrito($distrito_id);
        if ($result['status'] === 'ok') {
            $response = ['status' => 'ok', 'data' => $result['data']];
        } else {
            $response['message'] = $result['message'];
        }
    } elseif ($_GET['ajax'] === 'get_cargos' && isset($_GET['departamento_id'])) {
        $departamento_id = $_GET['departamento_id'];
        $result = $dbHandler->getCargosByDepartamento($departamento_id);
        if ($result['status'] === 'ok') {
            $response = ['status' => 'ok', 'data' => $result['data']];
        } else {
            $response['message'] = $result['message'];
        }
    }

    echo json_encode($response);
    exit;
}
// --- FIN: Manejo de solicitudes AJAX ---

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Incluir el componente del sidebar
require_once '../../components/sidebar_menu.php';

// Incluir los componentes necesarios
require_once '../../components/employees/employee_personal_info.php';
require_once '../../components/employees/employee_contact_info.php';
require_once '../../components/employees/employee_address_info.php';
require_once '../../components/employees/employee_work_info.php';

/**
 * Clase para gestionar los detalles de empleados
 */
class EmployeeDetails {
    private $employee;
    private $employeeId;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        // Obtener el ID del empleado desde la URL
        if(isset($_GET['id'])) {
            $this->employeeId = $_GET['id'];
            // Crear una instancia de la clase Employee
            $this->employee = new Employee($this->employeeId);
        } else {
            echo "ID de empleado no proporcionado.";
            exit;
        }
    }
    
    /**
     * Obtener las opciones de distritos para la provincia seleccionada
     */
    public function getDistritosForSelectedProvincia() {
        $provincia_id = $this->employee->get('provincia');
        return $this->employee->getDistritos($provincia_id);
    }
    
    /**
     * Obtener las opciones de corregimientos para el distrito seleccionado
     */
    public function getCorregimientosForSelectedDistrito() {
        $distrito_id = $this->employee->get('distrito');
        return $this->employee->getCorregimientos($distrito_id);
    }
    
    /**
     * Obtener las opciones para los campos de selección
     */
    public function getOptions($tabla, $valor_campo, $texto_campo) {
        switch ($tabla) {
            case 'provincia':
                return $this->employee->getProvincias();
                
            case 'distrito':
                return $this->getDistritosForSelectedProvincia();
                
            case 'corregimiento':
                return $this->getCorregimientosForSelectedDistrito();
                
            case 'nacionalidad':
                return $this->employee->getNacionalidades();
                
            case 'departamento':
                return $this->employee->getDepartamentos();
                
            case 'cargo':
                return $this->employee->getCargos();
                
            default:
                $opciones = $this->employee->getOptions($tabla, $valor_campo, $texto_campo);
                $resultado = [];
                foreach ($opciones as $opcion) {
                    $resultado[] = [
                        'value' => $opcion[$valor_campo],
                        'text' => $opcion[$texto_campo]
                    ];
                }
                return $resultado;
        }
    }
    
    /**
     * Generar opciones para selects con el valor seleccionado
     */
    public function generateSelectOptions($options, $selected_value) {
        $html = '<option value="">Seleccionar</option>';
        foreach($options as $option) {
            $value = $option['value'];
            $text = $option['text'];
            $selected = ($value == $selected_value) ? 'selected' : '';
            $html .= "<option value='$value' $selected>$text</option>";
        }
        return $html;
    }
    
    /**
     * Generar opciones para género
     */
    public function getGenderOptions() {
        $selected = $this->employee->get('genero');
        $options = $this->employee->getGenderOptions();
        return $this->generateSelectOptions($options, $selected);
    }
    
    /**
     * Generar opciones para estado civil
     */
    public function getCivilStatusOptions() {
        $selected = $this->employee->get('estado_civil');
        $options = $this->employee->getCivilStatusOptions();
        return $this->generateSelectOptions($options, $selected);
    }
    
    /**
     * Generar opciones para tipo de sangre
     */
    public function getBloodTypeOptions() {
        $selected = $this->employee->get('tipo_sangre');
        $options = $this->employee->getBloodTypeOptions();
        return $this->generateSelectOptions($options, $selected);
    }
    
    /**
     * Generar opciones para usa apellido de casada
     */
    public function getUsaAcOptions() {
        $selected = $this->employee->get('usa_ac');
        $options = $this->employee->getUsaAcOptions();
        return $this->generateSelectOptions($options, $selected);
    }
    
    /**
     * Generar opciones para estado de empleado
     */
    public function getStatusOptions() {
        $selected = $this->employee->get('estado');
        $options = $this->employee->getStatusOptions();
        return $this->generateSelectOptions($options, $selected);
    }
    
    /**
     * Renderizar el formulario de detalles de empleado
     */
    public function renderForm() {
        $personalInfo = new EmployeePersonalInfo($this->employee->getData(), $this);
        $contactInfo = new EmployeeContactInfo($this->employee->getData());
        $addressInfo = new EmployeeAddressInfo($this->employee->getData(), $this);
        $workInfo = new EmployeeWorkInfo($this->employee->getData(), $this);
        
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
            <button class="sidebar-toggle" id="sidebar-toggle">
                <span class="material-icons">menu</span>
            </button>

            <div class="sidebar-blur" id="sidebar-blur"></div>
        
            <?php 
            renderSidebar('empleados'); 
            ?>
            
            <div class="container">
                <main class="main-content">
                    <div class="card employee-card">
                        <h1 class="text-center">Detalles del Empleado</h1>
                        
                        <form id="employeeForm" method="POST" action="update_employee.php">
                            <input type="hidden" name="employee_id" value="<?php echo $this->employeeId; ?>">
                            
                            <?php 
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
            document.addEventListener('DOMContentLoaded', function() {
                // Selectores de elementos
                const provinciaSelect = document.getElementById('provincia');
                const distritoSelect = document.getElementById('distrito');
                const corregimientoSelect = document.getElementById('corregimiento');
                const departamentoSelect = document.getElementById('departamento');
                const cargoSelect = document.getElementById('cargo');
                const employeeId = document.querySelector('input[name="employee_id"]').value;
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebar = document.querySelector('.sidebar');
                const sidebarBlur = document.getElementById('sidebar-blur');

                /**
                 * Popula un elemento select con opciones.
                 * Detecta automáticamente los campos de valor y texto.
                 */
                function populateSelect(selectElement, options, defaultOptionText) {
                    selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
                    
                    if (!options || options.length === 0) {
                        selectElement.innerHTML = `<option value="">${defaultOptionText.replace('Seleccione', 'No hay')}</option>`;
                        return;
                    }
                    
                    const firstOption = options[0];
                    let valueField, textField;
                    
                    if (firstOption.codigo_distrito !== undefined) {
                        valueField = 'codigo_distrito'; textField = 'nombre_distrito';
                    } else if (firstOption.codigo_corregimiento !== undefined) {
                        valueField = 'codigo_corregimiento'; textField = 'nombre_corregimiento';
                    } else if (firstOption.codigo !== undefined) {
                        valueField = 'codigo'; textField = 'nombre';
                    } else {
                        // Fallback genérico (aunque no se usa en los casos actuales de fetch)
                        valueField = Object.keys(firstOption).find(key => key.includes('id') || key.includes('codigo')) || 'value';
                        textField = Object.keys(firstOption).find(key => key.includes('nombre') || key.includes('text')) || 'text';
                    }
                    
                    options.forEach(option => {
                        const value = option[valueField];
                        const text = option[textField];
                        const optionElement = document.createElement('option');
                        optionElement.value = value;
                        optionElement.textContent = text;
                        selectElement.appendChild(optionElement);
                    });
                }

                /**
                 * Realiza una llamada fetch para obtener datos y poblar un select.
                 */
                function fetchAndPopulate(targetSelect, ajaxAction, paramName, paramValue, defaultOptionText) {
                    targetSelect.innerHTML = '<option value="">Cargando...</option>';

                    if (!paramValue) {
                        targetSelect.innerHTML = `<option value="">${defaultOptionText}</option>`;
                        // Si es el select de distrito, también limpiar corregimiento
                        if (targetSelect === distritoSelect) {
                            corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                        }
                        return;
                    }

                    fetch(`employee_details.php?id=${employeeId}&ajax=${ajaxAction}&${paramName}=${paramValue}`)
                        .then(response => {
                            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'ok') {
                                populateSelect(targetSelect, data.data, defaultOptionText);
                            } else {
                                targetSelect.innerHTML = '<option value="">Error al cargar</option>';
                                console.error('Error en la respuesta AJAX:', data.message);
                            }
                        })
                        .catch(error => {
                            targetSelect.innerHTML = '<option value="">Error de red</option>';
                            console.error('Error en fetch:', error);
                        });
                }

                // --- Event Listeners para selects dependientes ---

                provinciaSelect.addEventListener('change', function() {
                    fetchAndPopulate(distritoSelect, 'get_distritos', 'provincia_id', this.value, 'Seleccione un distrito');
                    // Limpiar corregimiento al cambiar provincia
                    corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                });

                distritoSelect.addEventListener('change', function() {
                    fetchAndPopulate(corregimientoSelect, 'get_corregimientos', 'distrito_id', this.value, 'Seleccione un corregimiento');
                });
                
                departamentoSelect.addEventListener('change', function() {
                    fetchAndPopulate(cargoSelect, 'get_cargos', 'departamento_id', this.value, 'Seleccione un cargo');
                });

                // --- Lógica del Sidebar ---
                
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarBlur.classList.toggle('active');
                });
                
                sidebarBlur.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarBlur.classList.remove('active');
                });
                
                // Ajustar sidebar en resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 480) {
                        sidebarBlur.classList.remove('active'); // Ocultar blur en pantallas más grandes
                        if (window.innerWidth > 768) {
                            sidebar.classList.add('active'); // Mostrar sidebar en pantallas grandes
                        } else {
                            sidebar.classList.remove('active'); // Ocultar sidebar en tablets
                        }
                    } else {
                         // En pantallas pequeñas (<480), no forzar estado, dejar que el toggle funcione
                    }
                });
                
                // Estado inicial del sidebar basado en el tamaño de la ventana al cargar
                 if (window.innerWidth > 768) {
                     sidebar.classList.add('active');
                 } else {
                     sidebar.classList.remove('active');
                 }

            });
            </script>
        </body>
        </html>
        <?php
    }
}

// Crear instancia de EmployeeDetails y renderizar el formulario
if (isset($_GET['id'])) {
    $employeeDetails = new EmployeeDetails();
    $employeeDetails->renderForm();
} else {
    echo "ID de empleado no proporcionado.";
}
?>