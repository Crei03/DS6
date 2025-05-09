<?php
/**
 * Componente para mostrar la información personal del empleado
 * 
 * @param array $employeeData Datos del empleado
 * @param object $parent Referencia a la clase padre para acceder a los métodos
 * @param bool $disableCedulaFields Indica si los campos de cédula (prefijo, tomo, asiento) deben estar deshabilitados
 */
class EmployeePersonalInfo {
    private $employeeData;
    private $parent;
    private $disableCedulaFields;
    
    /**
     * Constructor de la clase
     */
    public function __construct($employeeData, $parent, $disableCedulaFields = false) {
        $this->employeeData = $employeeData;
        $this->parent = $parent;
        $this->disableCedulaFields = $disableCedulaFields;
    }
    
    /**
     * Renderizar el componente de información personal
     */
    public function render() {
        // Construir el valor inicial de la cédula formateada
        $cedula_formateada = '';
        if (!empty($this->employeeData['prefijo']) || !empty($this->employeeData['tomo']) || !empty($this->employeeData['asiento'])) {
            $cedula_formateada = sprintf('%s-%s-%s', 
                $this->employeeData['prefijo'] ?? '', 
                $this->employeeData['tomo'] ?? '', 
                $this->employeeData['asiento'] ?? ''
            );
        }

        // Determinar si los campos deben estar deshabilitados
        $disabledAttr = $this->disableCedulaFields ? 'disabled' : '';
        ?>
        <div class="form-section">
            <h2>Información Personal</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" value="<?php echo $cedula_formateada; ?>" disabled>
                    <input type="hidden" id="cedula_hidden" name="cedula" value="<?php echo $this->employeeData['cedula']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="prefijo">Prefijo:</label>
                    <input type="text" id="prefijo" value="<?php echo $this->employeeData['prefijo']; ?>" maxlength="2" oninput="validarPrefijo(this)" <?php echo $disabledAttr; ?>>
                    <input type="hidden" id="prefijo_hidden" name="prefijo" value="<?php echo $this->employeeData['prefijo']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="tomo">Tomo:</label>
                    <input type="text" id="tomo" value="<?php echo $this->employeeData['tomo']; ?>" maxlength="4" oninput="this.value = validarSoloNumeros(this.value); updateCedula();" <?php echo $disabledAttr; ?>>
                    <input type="hidden" id="tomo_hidden" name="tomo" value="<?php echo $this->employeeData['tomo']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="asiento">Asiento:</label>
                    <input type="text" id="asiento" value="<?php echo $this->employeeData['asiento']; ?>" maxlength="5" oninput="this.value = validarSoloNumeros(this.value); updateCedula();" <?php echo $disabledAttr; ?>>
                    <input type="hidden" id="asiento_hidden" name="asiento" value="<?php echo $this->employeeData['asiento']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre1">Primer Nombre:</label>
                    <input type="text" id="nombre1" name="nombre1" value="<?php echo $this->employeeData['nombre1']; ?>" oninput="this.value = validarSoloLetras(this.value)" required>
                </div>
                
                <div class="form-group">
                    <label for="nombre2">Segundo Nombre:</label>
                    <input type="text" id="nombre2" name="nombre2" value="<?php echo $this->employeeData['nombre2']; ?>" oninput="this.value = validarSoloLetras(this.value)">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="apellido1">Primer Apellido:</label>
                    <input type="text" id="apellido1" name="apellido1" value="<?php echo $this->employeeData['apellido1']; ?>" oninput="this.value = validarSoloLetras(this.value)" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido2">Segundo Apellido:</label>
                    <input type="text" id="apellido2" name="apellido2" value="<?php echo $this->employeeData['apellido2']; ?>" oninput="this.value = validarSoloLetras(this.value)">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="genero">Género:</label>
                    <select id="genero" name="genero">
                        <?php echo $this->parent->getGenderOptions(); ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="estado_civil">Estado Civil:</label>
                    <select id="estado_civil" name="estado_civil">
                        <?php echo $this->parent->getCivilStatusOptions(); ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tipo_sangre">Tipo de Sangre:</label>
                    <select id="tipo_sangre" name="tipo_sangre">
                        <?php echo $this->parent->getBloodTypeOptions(); ?>
                    </select>
                </div>
            </div>
                      
            <div class="form-row">
                <div class="form-group">
                    <label for="apellidoc">Apellido de Casada:</label>
                    <input type="text" id="apellidoc" name="apellidoc" value="<?php echo $this->employeeData['apellidoc']; ?>" oninput="this.value = validarSoloLetras(this.value)">
                </div>
                
                <div class="form-group">
                    <label for="usa_ac">Usa Apellido de Casada:</label>
                    <select id="usa_ac" name="usa_ac">
                        <?php echo $this->parent->getUsaAcOptions(); ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="f_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="f_nacimiento" name="f_nacimiento" value="<?php echo $this->employeeData['f_nacimiento']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="nacionalidad">Nacionalidad:</label>
                    <select id="nacionalidad" name="nacionalidad">
                        <?php 
                        $nacionalidades = $this->parent->getOptions('nacionalidad', 'codigo', 'pais');
                        echo $this->parent->generateSelectOptions($nacionalidades, $this->employeeData['nacionalidad']); 
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <script>
            function validarPrefijo(input) {
                let originalValue = input.value;
                let value = originalValue.replace(/[^0-9]/g, ''); // Permitir solo números
                
                // Si el valor no está vacío y empieza con 0 y tiene más de un dígito, o es solo '0'
                if (value.length > 1 && value.startsWith('0')) {
                     value = value.substring(1); // Eliminar el cero inicial
                } else if (value === '0') {
                     value = ''; // No permitir solo '0'
                }

                let numValue = parseInt(value, 10);

                if (isNaN(numValue)) { // Si después de limpiar no es un número (estaba vacío o era solo '0')
                    value = ''; 
                } else if (numValue < 1) {
                     value = ''; // Si es menor que 1 (esto no debería pasar por la lógica anterior, pero por seguridad)
                } else if (numValue > 13) {
                    // Si es mayor que 13, intentar quitar el último dígito.
                    // Necesitamos el valor *antes* de la validación para quitar el último carácter introducido.
                    value = originalValue.slice(0, -1).replace(/[^0-9]/g, ''); 
                     // Re-validar por si al quitar el último dígito queda un 0 inicial inválido
                     if (value.length > 1 && value.startsWith('0')) {
                         value = value.substring(1);
                     } else if (value === '0') {
                         value = '';
                     }
                     // Asegurarse de que sigue siendo numérico y > 0
                     let recheckNum = parseInt(value, 10);
                     if (isNaN(recheckNum) || recheckNum < 1) {
                         value = '';
                     }
                }
                
                input.value = value;
                updateCedula();
            }

            function validarSoloLetras(value) {
                return value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            }

            function validarSoloNumeros(value) {
                return value.replace(/[^0-9]/g, '');
            }

            function updateCedula() {
                const prefijo = document.getElementById('prefijo').value;
                const tomo = document.getElementById('tomo').value;
                const asiento = document.getElementById('asiento').value;
                const cedulaInput = document.getElementById('cedula');
                const cedulaHiddenInput = document.getElementById('cedula_hidden');

                // Construir la cédula formateada solo si hay algún valor
                if (prefijo || tomo || asiento) {
                    const cedulaValue = `${prefijo}-${tomo}-${asiento}`;
                    cedulaInput.value = cedulaValue;
                    cedulaHiddenInput.value = cedulaValue;
                } else {
                    cedulaInput.value = ''; // Limpiar si todos están vacíos
                    cedulaHiddenInput.value = ''; // Limpiar el campo oculto también
                }
            }

            function toggleApellidoCasada() {
                const genero = document.getElementById('genero').value;
                const estadoCivil = document.getElementById('estado_civil').value;
                const apellidoc = document.getElementById('apellidoc');
                const usaAc = document.getElementById('usa_ac');
                
                // Condición: solo habilitar si es mujer (1) y casada (1) o viuda (3)
                const esMujer = genero === '1';
                const esCasadaOViuda = estadoCivil === '1' || estadoCivil === '3';
                const cumpleCondicionBase = esMujer && esCasadaOViuda;
                
                // Si no cumple la condición base, deshabilitamos ambos campos
                if (!cumpleCondicionBase) {
                    apellidoc.disabled = true;
                    usaAc.disabled = true;
                    apellidoc.classList.add('disabled-field');
                    usaAc.classList.add('disabled-field');
                    apellidoc.value = '';
                    usaAc.value = '0'; // Establecer a "No"
                    return;
                }
                
                // Si cumple condición base, habilitamos el select de "Usa Apellido de Casada"
                usaAc.disabled = false;
                usaAc.classList.remove('disabled-field');
                
                // Verificamos el valor de "Usa Apellido de Casada"
                const usaApellidoCasada = usaAc.value === '1';
                
                // Habilitamos o deshabilitamos el campo de apellido casada según la selección
                apellidoc.disabled = !usaApellidoCasada;
                
                if (usaApellidoCasada) {
                    apellidoc.classList.remove('disabled-field');
                } else {
                    apellidoc.classList.add('disabled-field');
                    apellidoc.value = ''; // Limpiar el valor si está deshabilitado
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateCedula();
                toggleApellidoCasada(); // Ejecutar al cargar la página
                
                // Añadir event listeners para los selectores
                document.getElementById('genero').addEventListener('change', toggleApellidoCasada);
                document.getElementById('estado_civil').addEventListener('change', toggleApellidoCasada);
                document.getElementById('usa_ac').addEventListener('change', toggleApellidoCasada);
            });
        </script>
        <?php
    }
}
?>