<?php
/**
 * Componente para mostrar la información de contacto del empleado
 * 
 * @param array $employeeData Datos del empleado
 */
?>
<link rel="stylesheet" href="assets/admin/employee_details.css">
<?php
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
                    <label for="celular">Prefijo:</label>
                    <input id="pref_telfno" name="pref_telfno" value="+507" readonly>
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input id="celular" name="celular" value="<?php echo $this->employeeData['celular']; ?>" oninput="this.value = validarSoloNumeros(this.value)" required>
                </div>
                
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="celular">Prefijo:</label>
                    <input id="pref_telfno" name="pref_telfno" value="+507" readonly>
                </div>
                <div class="form-group">
                    <label for="telefono">Telefono:</label>
                    <input id="telefono" name="telefono" value="<?php echo $this->employeeData['telefono']; ?>" oninput="this.value = validarSoloNumeros(this.value)" required>
                </div>
                
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo $this->employeeData['correo']; ?>" required>
                </div>
            </div>
        </div>
        <?php
    }
}
?>