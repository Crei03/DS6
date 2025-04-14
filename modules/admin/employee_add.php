<?php
// Incluir archivos de configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esAdmin()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// --- INICIO: Manejo de solicitudes AJAX ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Acción no válida'];
    $dbHandler = new DBHandler();
    $action = $_GET['ajax'];
    $id = null;
    $result = null;

    switch ($action) {
        case 'get_distritos':
            if (isset($_GET['provincia_id'])) {
                $id = $_GET['provincia_id'];
                $result = $dbHandler->getDistritosByProvincia($id);
            } else {
                $response['message'] = 'Falta provincia_id';
            }
            break;
        case 'get_corregimientos':
            if (isset($_GET['distrito_id'])) {
                $id = $_GET['distrito_id'];
                $result = $dbHandler->getCorregimientosByDistrito($id);
            } else {
                $response['message'] = 'Falta distrito_id';
            }
            break;
        case 'get_cargos':
            if (isset($_GET['departamento_id'])) {
                $id = $_GET['departamento_id'];
                $result = $dbHandler->getCargosByDepartamento($id);
            } else {
                $response['message'] = 'Falta departamento_id';
            }
            break;
    }

    if ($result !== null) {
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

require_once '../../components/sidebar_menu.php';

// Incluir los componentes necesarios
require_once '../../components/employee_personal_info.php';
require_once '../../components/employee_contact_info.php';
require_once '../../components/employee_address_info.php';
require_once '../../components/employee_work_info.php';

/**
 * Clase para gestionar la adición de nuevos empleados
 */
class EmployeeAdd {
    private $employee;
    private $employeeData;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        // Inicializar una instancia de Employee sin cédula para obtener opciones
        $this->employee = new Employee();
        
        // Inicializar un array vacío para los datos del empleado
        $this->initializeEmptyData();
    }
    
    /**
     * Inicializar un array vacío para los datos del empleado
     */
    private function initializeEmptyData() {
        $this->employeeData = [
            'cedula' => '',
            'prefijo' => '',
            'tomo' => '',
            'asiento' => '',
            'nombre1' => '',
            'nombre2' => '',
            'apellido1' => '',
            'apellido2' => '',
            'apellidoc' => '',
            'usa_ac' => '',
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
            'cargo' => '',
            'departamento' => '',
            'f_contra' => date('Y-m-d'), // Fecha actual como valor por defecto
            'estado' => '1' // Estado activo por defecto
        ];
    }
    
    /**
     * Obtener las opciones para los campos de selección desde la clase Employee
     * Simplificado: Se eliminan los casos para distrito, corregimiento y cargo,
     * ya que se cargan dinámicamente con JavaScript/AJAX.
     */
    public function getOptions($tabla, $valor_campo, $texto_campo) {
        switch ($tabla) {
            case 'provincia':
                return $this->getProvincias();
                
            case 'nacionalidad':
                return $this->getNacionalidades();
                
            case 'departamento':
                return $this->getDepartamentos();
                
            default:
                return $this->employee->getOptions($tabla, $valor_campo, $texto_campo);
        }
    }
    
    /**
     * Generar opciones para selects con el valor seleccionado
     */
    public function generateSelectOptions($options, $selected_value = '') {
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
     * Obtiene las provincias usando la clase Employee
     */
    public function getProvincias() {
        return $this->employee->getProvincias();
    }
    
    /**
     * Obtiene las nacionalidades usando la clase Employee
     */
    public function getNacionalidades() {
        return $this->employee->getNacionalidades();
    }
    
    /**
     * Obtiene los departamentos usando la clase Employee
     */
    public function getDepartamentos() {
        return $this->employee->getDepartamentos();
    }
    
    /**
     * Generar opciones para género
     */
    public function getGenderOptions() {
        $options = $this->employee->getGenderOptions();
        return $this->generateSelectOptions($options, $this->employeeData['genero']);
    }
    
    /**
     * Generar opciones para estado civil
     */
    public function getCivilStatusOptions() {
        $options = $this->employee->getCivilStatusOptions();
        return $this->generateSelectOptions($options, $this->employeeData['estado_civil']);
    }
    
    /**
     * Generar opciones para tipo de sangre
     */
    public function getBloodTypeOptions() {
        $options = $this->employee->getBloodTypeOptions();
        return $this->generateSelectOptions($options, $this->employeeData['tipo_sangre']);
    }
    
    /**
     * Generar opciones para usa apellido de casada
     */
    public function getUsaAcOptions() {
        $options = $this->employee->getUsaAcOptions();
        return $this->generateSelectOptions($options, $this->employeeData['usa_ac']);
    }
    
    /**
     * Generar opciones para estado de empleado
     */
    public function getStatusOptions() {
        $options = $this->employee->getStatusOptions();
        return $this->generateSelectOptions($options, $this->employeeData['estado']);
    }
    
    /**
     * Renderizar el formulario de agregar empleado
     */
    public function renderForm() {
        // Inicializar los componentes con los datos del empleado (vacíos)
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
            <title>Agregar Empleado</title>
            <link rel="stylesheet" href="../../assets/global/root.css">
            <link rel="stylesheet" href="../../assets/admin/employee_details.css">
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        </head>
        <body>
            <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
            <button class="sidebar-toggle" id="sidebar-toggle">
                <span class="material-icons">menu</span>
            </button>

            <!-- Capa semi-transparente para dispositivos móviles -->
            <div class="sidebar-blur" id="sidebar-blur"></div>
        
            <?php 
            // Renderizar el sidebar indicando la página activa
            renderSidebar('employee_add'); 
            ?>
            
            <div class="container">
                <main class="main-content">
                    <div class="card employee-card">
                        <h1 class="text-center">Agregar Nuevo Empleado</h1>
                        
                        <form id="employeeForm" method="POST" action="save_employee.php">
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
                const departamentoSelect = document.getElementById('departamento');
                const cargoSelect = document.getElementById('cargo');
                
                // Function para realizar peticiones AJAX
                function fetchData(url, handleData) {
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Error HTTP: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => handleData(data))
                        .catch(error => {
                            console.error('Error en fetch:', error);
                        });
                }
                
                // Función para poblar un select con opciones
                function populateSelect(selectElement, data, defaultOptionText, valueField, textField) {
                    selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item[valueField];
                            option.textContent = item[textField];
                            selectElement.appendChild(option);
                        });
                    } else {
                        selectElement.innerHTML = `<option value="">No hay opciones disponibles</option>`;
                    }
                }
                
                // Función para limpiar y deshabilitar selects dependientes
                function resetDependentSelects(selects) {
                    selects.forEach(select => {
                        select.innerHTML = `<option value="">Seleccione primero el nivel anterior</option>`;
                        select.disabled = true;
                    });
                }
                
                // Event listener para Provincia -> Distrito
                provinciaSelect.addEventListener('change', function() {
                    const provinciaId = this.value;
                    resetDependentSelects([distritoSelect, corregimientoSelect]);
                    
                    if (provinciaId) {
                        distritoSelect.innerHTML = '<option value="">Cargando...</option>';
                        fetchData(`employee_add.php?ajax=get_distritos&provincia_id=${provinciaId}`, function(response) {
                            if (response.status === 'ok') {
                                populateSelect(distritoSelect, response.data, 'Seleccione un distrito', 'codigo_distrito', 'nombre_distrito');
                                distritoSelect.disabled = false;
                            } else {
                                distritoSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
                                distritoSelect.disabled = true;
                            }
                            corregimientoSelect.innerHTML = '<option value="">Seleccione un distrito primero</option>';
                            corregimientoSelect.disabled = true;
                        });
                    }
                });
                
                // Event listener para Distrito -> Corregimiento
                distritoSelect.addEventListener('change', function() {
                    const distritoId = this.value;
                    resetDependentSelects([corregimientoSelect]);
                    
                    if (distritoId) {
                        corregimientoSelect.innerHTML = '<option value="">Cargando...</option>';
                        fetchData(`employee_add.php?ajax=get_corregimientos&distrito_id=${distritoId}`, function(response) {
                            if (response.status === 'ok') {
                                populateSelect(corregimientoSelect, response.data, 'Seleccione un corregimiento', 'codigo_corregimiento', 'nombre_corregimiento');
                                corregimientoSelect.disabled = false;
                            } else {
                                corregimientoSelect.innerHTML = '<option value="">Error al cargar corregimientos</option>';
                                corregimientoSelect.disabled = true;
                            }
                        });
                    }
                });
                
                // Event listener para Departamento -> Cargo
                departamentoSelect.addEventListener('change', function() {
                    const departamentoId = this.value;
                    resetDependentSelects([cargoSelect]);
                    
                    if (departamentoId) {
                        cargoSelect.innerHTML = '<option value="">Cargando...</option>';
                        fetchData(`employee_add.php?ajax=get_cargos&departamento_id=${departamentoId}`, function(data) {
                            if (data.status === 'ok') {
                                populateSelect(cargoSelect, data.data, 'Seleccione un cargo', 'codigo', 'nombre');
                                cargoSelect.disabled = false;
                            } else {
                                cargoSelect.innerHTML = '<option value="">Error al cargar cargos</option>';
                                cargoSelect.disabled = true;
                            }
                        });
                    }
                });
                
                // Inicializar selects dependientes como deshabilitados
                resetDependentSelects([distritoSelect, corregimientoSelect, cargoSelect]);
                
                // Funcionalidad del sidebar responsive
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebar = document.querySelector('.sidebar');
                const sidebarBlur = document.getElementById('sidebar-blur');
                
                // Función para mostrar/ocultar el sidebar
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarBlur.classList.toggle('active');
                });
                
                // Cerrar el sidebar al hacer clic en el área semi-transparente
                sidebarBlur.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarBlur.classList.remove('active');
                });
                
                // Ajustar la visualización en cambios de tamaño de ventana
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 480) {
                        sidebarBlur.classList.remove('active');
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('active');
                        } else {
                            sidebar.classList.add('active');
                        }
                    }
                });
            });
            </script>
        </body>
        </html>
        <?php
    }
}

// Crear instancia de EmployeeAdd y renderizar el formulario
$employeeAdd = new EmployeeAdd();
$employeeAdd->renderForm();
?>