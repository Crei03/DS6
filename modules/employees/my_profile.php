<?php
/**
 * Página de perfil del empleado
 * 
 * Esta página permite al empleado visualizar y editar su información personal.
 */

// Incluir la configuración y validación
require_once '../../config/config.php';
require_once '../../config/validation.php';
require_once '../../class/session.php';

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
    private $employeeData;
    private $activeTab;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        global $conn, $sesion;
        
        $this->db = $conn;
        
        // Obtener el ID del empleado de la sesión
        $this->empleadoId = $sesion->getCedula();
        
        // Cargar los datos del empleado
        $this->loadEmployeeData();
        
        // Inicializar la pestaña activa
        $this->activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'personal';
    }
    
    /**
     * Cargar los datos del empleado desde la base de datos
     */
    private function loadEmployeeData() {
        // Aquí se cargarían los datos reales del empleado desde la base de datos
        // Por ahora, usamos datos de ejemplo
        $this->employeeData = [
            'id' => 1,
            'prefijo' => '8',
            'tomo' => '123',
            'asiento' => '4567',
            'nombre1' => 'Juan',
            'nombre2' => 'Carlos',
            'apellido1' => 'Pérez',
            'apellido2' => 'González',
            'apellidoc' => 'Rodríguez',
            'usa_ac' => 0,
            'genero' => 'M',
            'estado_civil' => 'S',
            'tipo_sangre' => 'O+',
            'f_nacimiento' => '1990-01-01',
            'nacionalidad' => 'PA',
            'celular' => '65432109',
            'telefono' => '2123456',
            'correo' => 'juan.perez@ejemplo.com',
            'provincia' => '8',
            'distrito' => '8-1',
            'corregimiento' => '8-1-1',
            'calle' => 'Calle Principal',
            'casa' => '123',
            'comunidad' => 'Bella Vista',
            'departamento' => 'IT',
            'cargo' => 'DEV',
            'f_contra' => '2020-01-15',
            'estado' => 'A'
        ];
    }

    /**
     * Generar las opciones para los selects
     */
    public function getOptions($tabla, $valor_campo, $texto_campo) {
        // En un entorno real, estas opciones vendrían de la base de datos
        // Por ahora usamos datos de ejemplo
        
        $opciones = [];
        
        switch ($tabla) {
            case 'provincia':
                $opciones = [
                    ['codigo_provincia' => '1', 'nombre_provincia' => 'Bocas del Toro'],
                    ['codigo_provincia' => '2', 'nombre_provincia' => 'Coclé'],
                    ['codigo_provincia' => '3', 'nombre_provincia' => 'Colón'],
                    ['codigo_provincia' => '4', 'nombre_provincia' => 'Chiriquí'],
                    ['codigo_provincia' => '5', 'nombre_provincia' => 'Darién'],
                    ['codigo_provincia' => '6', 'nombre_provincia' => 'Herrera'],
                    ['codigo_provincia' => '7', 'nombre_provincia' => 'Los Santos'],
                    ['codigo_provincia' => '8', 'nombre_provincia' => 'Panamá'],
                    ['codigo_provincia' => '9', 'nombre_provincia' => 'Veraguas'],
                    ['codigo_provincia' => '10', 'nombre_provincia' => 'Panamá Oeste'],
                    ['codigo_provincia' => '11', 'nombre_provincia' => 'Emberá Wounaan'],
                    ['codigo_provincia' => '12', 'nombre_provincia' => 'Guna Yala'],
                ];
                break;
                
            case 'distrito':
                $opciones = [
                    ['codigo_distrito' => '8-1', 'nombre_distrito' => 'Panamá'],
                    ['codigo_distrito' => '8-2', 'nombre_distrito' => 'San Miguelito'],
                    ['codigo_distrito' => '8-3', 'nombre_distrito' => 'Balboa'],
                    ['codigo_distrito' => '8-4', 'nombre_distrito' => 'Taboga'],
                ];
                break;
                
            case 'corregimiento':
                $opciones = [
                    ['codigo_corregimiento' => '8-1-1', 'nombre_corregimiento' => 'San Felipe'],
                    ['codigo_corregimiento' => '8-1-2', 'nombre_corregimiento' => 'El Chorrillo'],
                    ['codigo_corregimiento' => '8-1-3', 'nombre_corregimiento' => 'Santa Ana'],
                    ['codigo_corregimiento' => '8-1-4', 'nombre_corregimiento' => 'Calidonia'],
                    ['codigo_corregimiento' => '8-1-5', 'nombre_corregimiento' => 'Curundú'],
                ];
                break;
                
            case 'nacionalidad':
                $opciones = [
                    ['codigo' => 'PA', 'pais' => 'Panamá'],
                    ['codigo' => 'CO', 'pais' => 'Colombia'],
                    ['codigo' => 'CR', 'pais' => 'Costa Rica'],
                    ['codigo' => 'US', 'pais' => 'Estados Unidos'],
                    ['codigo' => 'VE', 'pais' => 'Venezuela'],
                ];
                break;
                
            case 'departamento':
                $opciones = [
                    ['codigo' => 'IT', 'nombre' => 'Tecnología de la Información'],
                    ['codigo' => 'HR', 'nombre' => 'Recursos Humanos'],
                    ['codigo' => 'FIN', 'nombre' => 'Finanzas'],
                    ['codigo' => 'MKT', 'nombre' => 'Marketing'],
                ];
                break;
                
            case 'cargo':
                $opciones = [
                    ['codigo' => 'CEO', 'nombre' => 'Director Ejecutivo'],
                    ['codigo' => 'CTO', 'nombre' => 'Director Técnico'],
                    ['codigo' => 'PM', 'nombre' => 'Gerente de Proyecto'],
                    ['codigo' => 'DEV', 'nombre' => 'Desarrollador'],
                    ['codigo' => 'DBA', 'nombre' => 'Administrador de Base de Datos'],
                ];
                break;
        }
        
        return $opciones;
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
        $selected = $this->employeeData['genero'];
        $options = [
            ['value' => 'M', 'text' => 'Masculino'],
            ['value' => 'F', 'text' => 'Femenino']
        ];
        
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
        $selected = $this->employeeData['estado_civil'];
        $options = [
            ['value' => 'S', 'text' => 'Soltero/a'],
            ['value' => 'C', 'text' => 'Casado/a'],
            ['value' => 'D', 'text' => 'Divorciado/a'],
            ['value' => 'V', 'text' => 'Viudo/a'],
        ];
        
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
        $selected = $this->employeeData['tipo_sangre'];
        $options = [
            ['value' => 'A+', 'text' => 'A+'],
            ['value' => 'A-', 'text' => 'A-'],
            ['value' => 'B+', 'text' => 'B+'],
            ['value' => 'B-', 'text' => 'B-'],
            ['value' => 'AB+', 'text' => 'AB+'],
            ['value' => 'AB-', 'text' => 'AB-'],
            ['value' => 'O+', 'text' => 'O+'],
            ['value' => 'O-', 'text' => 'O-']
        ];
        
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
        $selected = $this->employeeData['usa_ac'];
        $options = [
            ['value' => '0', 'text' => 'No'],
            ['value' => '1', 'text' => 'Sí']
        ];
        
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
        $selected = $this->employeeData['estado'];
        $options = [
            ['value' => 'A', 'text' => 'Activo'],
            ['value' => 'I', 'text' => 'Inactivo'],
            ['value' => 'V', 'text' => 'Vacaciones'],
            ['value' => 'P', 'text' => 'Permiso']
        ];
        
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
        $profileActions = new ProfileActions($this->employeeData, $this->activeTab);
        $personalInfo = new EmployeePersonalInfo($this->employeeData, $this);
        $contactInfo = new EmployeeContactInfo($this->employeeData);
        $addressInfo = new EmployeeAddressInfo($this->employeeData, $this);
        $workInfo = new EmployeeWorkInfo($this->employeeData, $this);
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
                        <input type="hidden" name="employee_id" value="<?php echo $this->employeeData['id']; ?>">
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