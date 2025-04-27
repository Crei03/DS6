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
            <link rel="stylesheet" href="../../assets/admin/employee_add.css">
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
                        <div style="display: flex; justify-content: flex-start; margin-bottom: 1.5rem;">
                            <button type="button" class="back-button" id="btn-regresar" style="margin-right: 8px;" onclick="window.history.length > 1 ? window.history.back() : window.location.href='list_table.php';">
                                <span class="material-icons">arrow_back</span>Regresar
                            </button>
                        </div>
                        <div id="alert-container"></div>
                        <form id="employeeForm">
                            <input type="hidden" name="cedula" id="cedula_hidden" value="<?php echo htmlspecialchars($this->employee->get('cedula')); ?>">
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
                        <script type="module">
                        import { validarCedulaPanama, validarPrefijo } from '../../config/validation.js';
                        document.addEventListener('DOMContentLoaded', async () => {
                            const form = document.getElementById('employeeForm');
                            const cedulaHidden = document.getElementById('cedula_hidden');
                            const prefijo = document.getElementById('prefijo');
                            const tomo  = document.getElementById('tomo');
                            const asiento = document.getElementById('asiento');
                            const provinciaSelect = document.getElementById('provincia');
                            const distritoSelect = document.getElementById('distrito');
                            const corregimientoSelect = document.getElementById('corregimiento');
                            const departamentoSelect = document.getElementById('departamento');
                            const cargoSelect = document.getElementById('cargo');
                            const errorDiv = document.getElementById('alert-container');

                            // Validación de cédula (si el campo existe y es editable)
                            const cedulaInput = document.getElementById('cedula');
                            if (cedulaInput) {
                                cedulaInput.addEventListener('input', () => validarCedulaPanama(cedulaInput));
                            }
                            // Validación de prefijo (si el campo existe y es editable)
                            if (prefijo) {
                                prefijo.addEventListener('input', () => validarPrefijo(prefijo));
                            }

                            // Selects dependientes (AJAX)
                            function fetchAndPopulate(targetSelect, ajaxAction, paramName, paramValue, defaultOptionText) {
                                targetSelect.innerHTML = '<option value="">Cargando...</option>';
                                if (!paramValue) {
                                    targetSelect.innerHTML = `<option value="">${defaultOptionText}</option>`;
                                    if (targetSelect === distritoSelect) {
                                        corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                                    }
                                    return;
                                }
                                fetch(`employee_details.php?id=${cedulaHidden.value}&ajax=${ajaxAction}&${paramName}=${paramValue}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'ok') {
                                            targetSelect.innerHTML = `<option value="">${defaultOptionText}</option>`;
                                            data.data.forEach(option => {
                                                let value = option.codigo_distrito || option.codigo_corregimiento || option.codigo || option.value;
                                                let text = option.nombre_distrito || option.nombre_corregimiento || option.nombre || option.text;
                                                targetSelect.innerHTML += `<option value="${value}">${text}</option>`;
                                            });
                                        } else {
                                            targetSelect.innerHTML = '<option value="">Error al cargar</option>';
                                        }
                                    })
                                    .catch(() => {
                                        targetSelect.innerHTML = '<option value="">Error de red</option>';
                                    });
                            }
                            provinciaSelect.addEventListener('change', function() {
                                fetchAndPopulate(distritoSelect, 'get_distritos', 'provincia_id', this.value, 'Seleccione un distrito');
                                corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                            });
                            distritoSelect.addEventListener('change', function() {
                                fetchAndPopulate(corregimientoSelect, 'get_corregimientos', 'distrito_id', this.value, 'Seleccione un corregimiento');
                            });
                            departamentoSelect.addEventListener('change', function() {
                                fetchAndPopulate(cargoSelect, 'get_cargos', 'departamento_id', this.value, 'Seleccione un cargo');
                            });

                            // Envío del formulario por fetch
                            form.addEventListener('submit', async e => {
                                e.preventDefault();
                                errorDiv.innerHTML = '';
                                const formData = new FormData(form);
                                const data = {};
                                formData.forEach((v,k)=> data[k]=v);
                                if (!data.cedula) {
                                    errorDiv.textContent = 'Cédula inválida'; return;
                                }
                                const resp = await fetch('../../config/controlador.php', {
                                    method:'POST', headers:{'Content-Type':'application/json'},
                                    body: JSON.stringify({ action:'update', table:'empleados', id:data.cedula, data })
                                });
                                const result = await resp.json();
                                if (result.status==='ok' || result.updated) {
                                    errorDiv.innerHTML = '<div style="color:green">Actualizado correctamente</div>';
                                    setTimeout(()=>window.location.href='list_table.php', 1200);
                                } else {
                                    errorDiv.textContent = result.message || 'Error al actualizar';
                                }
                            });
                        });
                        </script>
                    </div>
                </main>
            </div>
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