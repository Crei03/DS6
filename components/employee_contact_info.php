<?php
/**
 * Componente para mostrar la información de contacto del empleado
 * 
 * @param array $employeeData Datos del empleado
 */
class EmployeeContactInfo {
    private $employeeData;
    
    /**
     * Constructor de la clase
     */
    public function __construct($employeeData) {
        $this->employeeData = $employeeData;
    }
    
    /**
     * Renderizar el componente de información de contacto
     */
    public function render() {
        ?>
        <div class="form-section">
            <h2>Información de Contacto</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="tel" id="celular" name="celular" value="<?php echo $this->employeeData['celular']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo $this->employeeData['telefono']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo $this->employeeData['correo']; ?>">
                </div>
            </div>
        </div>
        <?php
    }
}
?>