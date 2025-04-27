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

require_once '../../components/sidebar_menu.php';

// Incluir los componentes necesarios
require_once '../../components/employees/employee_personal_info.php';
require_once '../../components/employees/employee_contact_info.php';
require_once '../../components/employees/employee_address_info.php';
require_once '../../components/employees/employee_work_info.php';

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
        $personalInfo = new EmployeePersonalInfo($this->employeeData, $this, false);
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
            <link rel="stylesheet" href="../../assets/admin/employee_add.css">
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
                        <div style="display: flex; justify-content: flex-start; margin-bottom: 1.5rem;">
                            <button type="button" class="back-button" id="btn-regresar" style="margin-right: 8px;" onclick="window.history.length > 1 ? window.history.back() : window.location.href='list_table.php';">
                                <span class="material-icons">arrow_back</span>Regresar
                            </button>
                        </div>
                        
                        <div id="alert-container"></div>
                        <form id="employeeForm">
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
                            import { validarCedulaPanama } from '../../config/validation.js';

                            document.addEventListener('DOMContentLoaded', async () => {
                                // Elementos de formulario
                                const form = document.getElementById('employeeForm');
                                const cedulaHidden = document.getElementById('cedula_hidden');
                                const prefijo = document.getElementById('prefijo');
                                const tomo = document.getElementById('tomo');
                                const asiento = document.getElementById('asiento');
                                const cedulaInput = document.getElementById('cedula');
                                const provinciaSelect = document.getElementById('provincia');
                                const distritoSelect = document.getElementById('distrito');
                                const corregimientoSelect = document.getElementById('corregimiento');
                                const departamentoSelect = document.getElementById('departamento');
                                const cargoSelect = document.getElementById('cargo');
                                const errorDiv = document.getElementById('alert-container');

                                // Validación de cédula
                                cedulaInput.addEventListener('input', () => validarCedulaPanama(cedulaInput));
                                [prefijo, tomo, asiento].forEach(el => el.addEventListener('input', () => {
                                    cet = `${prefijo.value}-${tomo.value}-${asiento.value}`.replace(/--/g,'-');
                                    cedulaInput.value = cet;
                                    cedulaHidden.value = cet;
                                }));

                                // Cargar catálogos
                                async function fetchCatalogo(table) {
                                    const res = await fetch('../../config/controlador.php', {
                                        method: 'POST', headers:{'Content-Type':'application/json'},
                                        body: JSON.stringify({ action:'readAll', table })
                                    });
                                    const json = await res.json();
                                    return json.status==='ok' && Array.isArray(json.data) ? json.data : [];
                                }
                                const [provincias, distritosAll, corregimientosAll, departamentos, cargosAll] =
                                    await Promise.all([
                                        fetchCatalogo('provincia'),
                                        fetchCatalogo('distrito'),
                                        fetchCatalogo('corregimiento'),
                                        fetchCatalogo('departamento'),
                                        fetchCatalogo('cargo')
                                    ]);

                                function populate(select, data, valField, txtField) {
                                    select.innerHTML = `<option value="">Seleccionar</option>` +
                                        data.map(d => `<option value="${d[valField]}">${d[txtField]}</option>`).join('');
                                    select.disabled = false;
                                }
                                populate(provinciaSelect, provincias, 'codigo_provincia', 'nombre_provincia');
                                provinceChange();
                                provinciaSelect.addEventListener('change', provinceChange);

                                function provinceChange() {
                                    const pid = provinciaSelect.value;
                                    const list = distritosAll.filter(d=>d.codigo_provincia==pid);
                                    populate(distritoSelect, list, 'codigo_distrito', 'nombre_distrito');
                                    corregimientoSelect.innerHTML = `<option>Seleccione distrito primero</option>`;
                                    corregimientoSelect.disabled = true;
                                }
                                distritoSelect.addEventListener('change', () => {
                                    const did = distritoSelect.value;
                                    const list = corregimientosAll.filter(c=>c.codigo_distrito==did);
                                    populate(corregimientoSelect, list, 'codigo_corregimiento', 'nombre_corregimiento');
                                });

                                populate(departamentoSelect, departamentos, 'codigo', 'nombre');
                                departamentoSelect.addEventListener('change', () => {
                                    const dept = departamentoSelect.value;
                                    const list = cargosAll.filter(c=>c.dep_codigo==dept);
                                    populate(cargoSelect, list, 'codigo', 'nombre');
                                });

                                // Manejar envío de formulario
                                form.addEventListener('submit', async e => {
                                    e.preventDefault();
                                    errorDiv.innerHTML = '';
                                    const formData = new FormData(form);
                                    const data = {};
                                    formData.forEach((v,k)=> data[k]=v);
                                    console.log('Datos a enviar:', data);
                                    if (!data.cedula) {
                                        errorDiv.textContent = 'Cédula inválida'; return;
                                    }
                                    // Enviar datos al controlador
                                    const resp = await fetch('../../config/controlador.php', {
                                        method:'POST', headers:{'Content-Type':'application/json'},
                                        body: JSON.stringify({ action:'create', table:'empleados', data })
                                    });
                                    const result = await resp.json();
                                    if (result.status==='ok' || result.insertedId) {
                                        window.location.href = 'list_table.php';
                                    } else {
                                        errorDiv.textContent = result.message || 'Error al guardar';
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

// Crear instancia de EmployeeAdd y renderizar el formulario
$employeeAdd = new EmployeeAdd();
$employeeAdd->renderForm();
?>