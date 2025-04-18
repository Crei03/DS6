<?php
/**
 * Componente para mostrar la información laboral del empleado
 * 
 * @param array $employeeData Datos del empleado
 * @param object $parent Referencia a la clase padre para acceder a los métodos
 */
class EmployeeWorkInfo {
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
     * Renderizar el componente de información laboral
     */
    public function render() {
        // Obtener las opciones para los selects
        $departamentos = $this->parent->getOptions('departamento', 'codigo', 'nombre');
        $cargos = $this->parent->getOptions('cargo', 'codigo', 'nombre');
        
        ?>
        <div class="form-section">
            <h2>Información Laboral</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="departamento">Departamento:</label>
                    <select id="departamento" name="departamento">
                        <?php echo $this->parent->generateSelectOptions($departamentos, $this->employeeData['departamento']); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <select id="cargo" name="cargo">
                        <?php echo $this->parent->generateSelectOptions($cargos, $this->employeeData['cargo']); ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="f_contra">Fecha de Contratación:</label>
                    <input type="date" id="f_contra" name="f_contra" value="<?php echo $this->employeeData['f_contra']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <?php echo $this->parent->getStatusOptions(); ?>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }
}
?>