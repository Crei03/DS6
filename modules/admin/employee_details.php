<?php
// Incluir archivos de configuración
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

// Incluir el componente del sidebar
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
            
            // Manejar solicitudes de cargar distritos/corregimientos
            $this->handleLocationRequests();
        } else {
            echo "ID de empleado no proporcionado.";
            exit;
        }
    }
    
    /**
     * Maneja las solicitudes de carga de distritos y corregimientos
     */
    private function handleLocationRequests() {
        if (isset($_GET['load_distritos'])) {
            $provincia_id = $_GET['load_distritos'];
            $distritos = $this->employee->getDistritos($provincia_id);
            
            // Guardar distritos en la sesión para mostrarlos cuando se recargue la página
            $_SESSION['distritos'] = $distritos;
            $_SESSION['provincia_seleccionada'] = $provincia_id;
        }
        
        if (isset($_GET['load_corregimientos'])) {
            $distrito_id = $_GET['load_corregimientos'];
            $corregimientos = $this->employee->getCorregimientos($distrito_id);
            
            // Guardar corregimientos en la sesión para mostrarlos cuando se recargue la página
            $_SESSION['corregimientos'] = $corregimientos;
            $_SESSION['distrito_seleccionado'] = $distrito_id;
        }
        
        // Manejar solicitud para cargar cargos según el departamento seleccionado
        if (isset($_GET['load_cargos'])) {
            $departamento_id = $_GET['load_cargos'];
            $cargos = $this->employee->getCargos($departamento_id);
            
            // Guardar cargos en la sesión para mostrarlos cuando se recargue la página
            $_SESSION['cargos'] = $cargos;
            $_SESSION['departamento_seleccionado'] = $departamento_id;
        }
    }
    
    /**
     * Obtener las opciones de distritos para la provincia seleccionada
     */
    public function getDistritosForSelectedProvincia() {
        $provincia_id = $this->employee->get('provincia');
        
        // Si hay una provincia en la sesión, usarla
        if (isset($_SESSION['provincia_seleccionada'])) {
            $provincia_id = $_SESSION['provincia_seleccionada'];
        }
        
        // Si hay distritos en la sesión, mostrarlos
        if (isset($_SESSION['distritos'])) {
            $distritos = $_SESSION['distritos'];
        } else {
            // Si no, obtener distritos para la provincia actual
            $distritos = $this->employee->getDistritos($provincia_id);
        }
        
        return $distritos;
    }
    
    /**
     * Obtener las opciones de corregimientos para el distrito seleccionado
     */
    public function getCorregimientosForSelectedDistrito() {
        $distrito_id = $this->employee->get('distrito');
        
        // Si hay un distrito en la sesión, usarlo
        if (isset($_SESSION['distrito_seleccionado'])) {
            $distrito_id = $_SESSION['distrito_seleccionado'];
        }
        
        // Si hay corregimientos en la sesión, mostrarlos
        if (isset($_SESSION['corregimientos'])) {
            $corregimientos = $_SESSION['corregimientos'];
        } else {
            // Si no, obtener corregimientos para el distrito actual
            $corregimientos = $this->employee->getCorregimientos($distrito_id);
        }
        
        return $corregimientos;
    }
    
    /**
     * Obtener las opciones para los campos de selección
     */
    public function getOptions($tabla, $valor_campo, $texto_campo) {
        // Utilizar la clase Employee para obtener opciones
        // según el tipo de tabla solicitada
        switch ($tabla) {
            case 'provincia':
                return $this->employee->getProvincias();
                
            case 'distrito':
                return $this->employee->getDistritos();
                
            case 'corregimiento':
                return $this->employee->getCorregimientos();
                
            case 'nacionalidad':
                return $this->employee->getNacionalidades();
                
            case 'departamento':
                return $this->employee->getDepartamentos();
                
            case 'cargo':
                return $this->employee->getCargos();
                
            default:
                // Para otras tablas, usar el método genérico
                $opciones = $this->employee->getOptions($tabla, $valor_campo, $texto_campo);
                
                // Formatear los datos para tener la estructura value/text
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
        // Inicializar los componentes con los datos del empleado
        $personalInfo = new EmployeePersonalInfo($this->employee->getData(), $this);
        $contactInfo = new EmployeeContactInfo($this->employee->getData());
        $addressInfo = new EmployeeAddressInfo($this->employee->getData(), $this);
        $workInfo = new EmployeeWorkInfo($this->employee->getData(), $this);
        
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
            <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
            <button class="sidebar-toggle" id="sidebar-toggle">
                <span class="material-icons">menu</span>
            </button>

            <!-- Capa semi-transparente para dispositivos móviles -->
            <div class="sidebar-blur" id="sidebar-blur"></div>
        
            <?php 
            // Renderizar el sidebar indicando la página activa
            renderSidebar('empleados'); 
            ?>
            
            <div class="container">
                <main class="main-content">
                    <div class="card employee-card">
                        <h1 class="text-center">Detalles del Empleado</h1>
                        
                        <form id="employeeForm" method="POST" action="update_employee.php">
                            <input type="hidden" name="employee_id" value="<?php echo $this->employeeId; ?>">
                            
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
                    
                    // Obtener distritos de la provincia seleccionada
                    if (provinciaId) {
                        // Realizar petición para obtener distritos
                        window.location.href = `${window.location.pathname}?id=<?php echo $this->employeeId; ?>&load_distritos=${provinciaId}`;
                    }
                });
                
                // Función para cargar corregimientos según el distrito seleccionado
                distritoSelect.addEventListener('change', function() {
                    const distritoId = this.value;
                    
                    // Limpiar las opciones actuales
                    corregimientoSelect.innerHTML = '<option value="">Seleccione un corregimiento</option>';
                    
                    // Obtener corregimientos del distrito seleccionado
                    if (distritoId) {
                        // Realizar petición para obtener corregimientos
                        window.location.href = `${window.location.pathname}?id=<?php echo $this->employeeId; ?>&load_corregimientos=${distritoId}`;
                    }
                });
            });
            
            // Funcionalidad del sidebar responsive
            document.addEventListener('DOMContentLoaded', function() {
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
                        // En pantallas mayores a 480px, el sidebar siempre es visible
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

// Crear instancia de EmployeeDetails y renderizar el formulario
if (isset($_GET['id'])) {
    $employeeDetails = new EmployeeDetails();
    $employeeDetails->renderForm();
} else {
    echo "ID de empleado no proporcionado.";
}