<?php
/**
 * Componente para mostrar la información personal del empleado
 * 
 * @param array $employeeData Datos del empleado
 * @param object $parent Referencia a la clase padre para acceder a los métodos
 */
class EmployeePersonalInfo {
    private $employeeData;
    private $parent;
    
    /**
     * Constructor de la clase
     */
    public function __construct($employeeData, $parent) {
        $this->employeeData = $employeeData;
        $this->parent = $parent;
    }
    
    /**
     * Renderizar el componente de información personal
     */
    public function render() {
        ?>
        <div class="form-section">
            <h2>Información Personal</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" value="<?php echo $this->employeeData['cedula']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="prefijo">Prefijo:</label>
                    <input type="text" id="prefijo" name="prefijo" value="<?php echo $this->employeeData['prefijo']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="tomo">Tomo:</label>
                    <input type="text" id="tomo" name="tomo" value="<?php echo $this->employeeData['tomo']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="asiento">Asiento:</label>
                    <input type="text" id="asiento" name="asiento" value="<?php echo $this->employeeData['asiento']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre1">Primer Nombre:</label>
                    <input type="text" id="nombre1" name="nombre1" value="<?php echo $this->employeeData['nombre1']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="nombre2">Segundo Nombre:</label>
                    <input type="text" id="nombre2" name="nombre2" value="<?php echo $this->employeeData['nombre2']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="apellido1">Primer Apellido:</label>
                    <input type="text" id="apellido1" name="apellido1" value="<?php echo $this->employeeData['apellido1']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="apellido2">Segundo Apellido:</label>
                    <input type="text" id="apellido2" name="apellido2" value="<?php echo $this->employeeData['apellido2']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="apellidoc">Apellido de Casada:</label>
                    <input type="text" id="apellidoc" name="apellidoc" value="<?php echo $this->employeeData['apellidoc']; ?>">
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
        <?php
    }
}
?>