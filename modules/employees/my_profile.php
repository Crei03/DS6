<?php
/**
 * Página de perfil del empleado
 * 
 * Esta página permite al empleado visualizar y editar su información personal.
 */

// Incluir la configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../config/BdHandler.php';
require_once '../../class/session.php';
require_once '../../class/employee.php';

// Verificar sesión del usuario
$sesion = new Session();
if (!$sesion->esEmpleado()) {
    $sesion->redirigir('../../modules/auth/login.php');
}

// Incluir los componentes necesarios
require_once '../../components/profile_actions.php';
require_once '../../components/employee_personal_info.php';
require_once '../../components/employee_contact_info.php';
require_once '../../components/employee_address_info.php';
require_once '../../components/employee_work_info.php';
require_once '../../components/sidebar_menu.php';

/**
 * Clase para la página de perfil del empleado
 */
class MyProfile {
    private $db;
    private $empleadoId;
    private $employee;
    private $activeTab;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        global $conn, $sesion;
        
        $this->db = $conn;
        
        // Obtener el ID del empleado de la sesión
        $this->empleadoId = $sesion->getCedula();
        
        // Cargar los datos del empleado utilizando la clase Employee
        $this->employee = new Employee($this->empleadoId);
        
        // Inicializar la pestaña activa
        $this->activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'personal';
    }
    
    /**
     * Generar las opciones para los selects
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
                    $keys = array_keys($opcion);
                    $resultado[] = [
                        'value' => $opcion[$keys[0]],
                        'text' => $opcion[$keys[1]]
                    ];
                }
                
                return $resultado;
        }
    }
    
    /**
     * Generar las opciones para los selects
     */
    public function generateSelectOptions($options, $selectedValue) {
        $html = '<option value="">Seleccione una opción</option>';
        
        foreach ($options as $option) {
            $keys = array_keys($option);
            $value = $option[$keys[0]];
            $text = $option[$keys[1]];
            
            $selected = ($selectedValue == $value) ? 'selected' : '';
            
            $html .= '<option value="' . $value . '" ' . $selected . '>' . $text . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Obtener las opciones para el género
     */
    public function getGenderOptions() {
        $selected = $this->employee->get('genero');
        $options = $this->employee->getGenderOptions();
        
        $html = '<option value="">Seleccione una opción</option>';
        foreach ($options as $option) {
            $selectedAttr = ($selected == $option['value']) ? 'selected' : '';
            $html .= '<option value="' . $option['value'] . '" ' . $selectedAttr . '>' . $option['text'] . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Obtener las opciones para el estado civil
     */
    public function getCivilStatusOptions() {
        $selected = $this->employee->get('estado_civil');
        $options = $this->employee->getCivilStatusOptions();
        
        $html = '<option value="">Seleccione una opción</option>';
        foreach ($options as $option) {
            $selectedAttr = ($selected == $option['value']) ? 'selected' : '';
            $html .= '<option value="' . $option['value'] . '" ' . $selectedAttr . '>' . $option['text'] . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Obtener las opciones para el tipo de sangre
     */
    public function getBloodTypeOptions() {
        $selected = $this->employee->get('tipo_sangre');
        $options = $this->employee->getBloodTypeOptions();
        
        $html = '<option value="">Seleccione una opción</option>';
        foreach ($options as $option) {
            $selectedAttr = ($selected == $option['value']) ? 'selected' : '';
            $html .= '<option value="' . $option['value'] . '" ' . $selectedAttr . '>' . $option['text'] . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Obtener las opciones para el uso de apellido de casada
     */
    public function getUsaAcOptions() {
        $selected = $this->employee->get('usa_ac');
        $options = $this->employee->getUsaAcOptions();
        
        $html = '<option value="">Seleccione una opción</option>';
        foreach ($options as $option) {
            $selectedAttr = ($selected == $option['value']) ? 'selected' : '';
            $html .= '<option value="' . $option['value'] . '" ' . $selectedAttr . '>' . $option['text'] . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Obtener las opciones para el estado del empleado
     */
    public function getStatusOptions() {
        $selected = $this->employee->get('estado');
        $options = $this->employee->getStatusOptions();
        
        $html = '<option value="">Seleccione una opción</option>';
        foreach ($options as $option) {
            $selectedAttr = ($selected == $option['value']) ? 'selected' : '';
            $html .= '<option value="' . $option['value'] . '" ' . $selectedAttr . '>' . $option['text'] . '</option>';
        }
        
        return $html;
    }
    
    /**
     * Renderizar la página
     */
    public function render() {
        $profileActions = new ProfileActions($this->employee->getData(), $this->activeTab);
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
            <title>Mi Perfil</title>
            <link rel="stylesheet" href="../../assets/global/root.css">
            <link rel="stylesheet" href="../../assets/employees/my_profile.css">
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            <script src="../../config/validation.js"></script>
        </head>
        <body>
            <!-- Botón para mostrar/ocultar el sidebar en pantallas pequeñas -->
            <button class="sidebar-toggle" id="sidebar-toggle">
                <span class="material-icons">menu</span>
            </button>

            <!-- Capa semi-transparente para dispositivos móviles -->
            <div class="sidebar-blur" id="sidebar-blur"></div>
            
            <?php renderSidebar('perfil'); ?>
            
            <div class="profile-container">
                <div class="profile-sidebar-container">
                    <?php $profileActions->render(); ?>
                </div>
                
                <div class="profile-content">
                    <form id="profile-form" method="post" action="update_profile.php">
                        <input type="hidden" name="employee_id" value="<?php echo $this->employee->get('cedula'); ?>">
                        <input type="hidden" name="active_tab" value="<?php echo $this->activeTab; ?>">
                        
                        <!-- Información personal -->
                        <div id="container-personal" class="info-container <?php echo $this->activeTab == 'personal' ? 'active' : ''; ?>">
                            <?php $personalInfo->render(); ?>
                            <div class="buttons-container">
                                <button type="button" class="action-button button-cancel" onclick="resetForm('personal')">Cancelar</button>
                                <button type="submit" class="action-button button-save">Guardar cambios</button>
                            </div>
                        </div>
                        
                        <!-- Información de contacto -->
                        <div id="container-contacto" class="info-container <?php echo $this->activeTab == 'contacto' ? 'active' : ''; ?>">
                            <?php $contactInfo->render(); ?>
                            <div class="buttons-container">
                                <button type="button" class="action-button button-cancel" onclick="resetForm('contacto')">Cancelar</button>
                                <button type="submit" class="action-button button-save">Guardar cambios</button>
                            </div>
                        </div>
                        
                        <!-- Información de dirección -->
                        <div id="container-direccion" class="info-container <?php echo $this->activeTab == 'direccion' ? 'active' : ''; ?>">
                            <?php $addressInfo->render(); ?>
                            <div class="buttons-container">
                                <button type="button" class="action-button button-cancel" onclick="resetForm('direccion')">Cancelar</button>
                                <button type="submit" class="action-button button-save">Guardar cambios</button>
                            </div>
                        </div>
                        
                        <!-- Información laboral (solo lectura) -->
                        <div id="container-laboral" class="info-container read-only-section <?php echo $this->activeTab == 'laboral' ? 'active' : ''; ?>">
                            <?php $workInfo->render(); ?>
                            <!-- Sin botones de acción ya que es solo lectura -->
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Función para mostrar la pestaña inicial
                document.addEventListener('DOMContentLoaded', function() {
                    cambiarTab('<?php echo $this->activeTab; ?>');

                    // Desactivar los campos no editables en información personal
                    document.getElementById('genero').setAttribute('disabled', 'disabled');
                    document.getElementById('f_nacimiento').setAttribute('disabled', 'disabled');
                    document.getElementById('nacionalidad').setAttribute('disabled', 'disabled');
                    
                    // Desactivar todos los campos en información laboral
                    var laboralContainer = document.getElementById('container-laboral');
                    var inputs = laboralContainer.querySelectorAll('input, select');
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].setAttribute('disabled', 'disabled');
                    }
                });

                // Función para resetear el formulario
                function resetForm(tabId) {
                    // En un entorno real, recargaríamos la página o los datos originales
                    // Por ahora solo recargamos la página manteniendo la pestaña activa
                    window.location.href = 'my_profile.php?tab=' + tabId;
                }

                // Función para validar solo números
                function validarSoloNumeros(valor) {
                    return valor.replace(/[^0-9]/g, '');
                }

                // Función para validar solo letras
                function validarSoloLetras(valor) {
                    return valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                }

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

// Instanciar y renderizar la página
$page = new MyProfile();
$page->render();
?>