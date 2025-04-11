<?php
/**
 * Componente para mostrar la información de dirección del empleado
 * 
 * @param array $employeeData Datos del empleado
 * @param object $parent Referencia a la clase padre para acceder a los métodos
 */
class EmployeeAddressInfo {
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
     * Renderizar el componente de información de dirección
     */
    public function render() {
        // Obtener las opciones para los selects
        $provincias = $this->parent->getOptions('provincia', 'codigo_provincia', 'nombre_provincia');
        $distritos = $this->parent->getOptions('distrito', 'codigo_distrito', 'nombre_distrito');
        $corregimientos = $this->parent->getOptions('corregimiento', 'codigo_corregimiento', 'nombre_corregimiento');
        
        ?>
        <div class="form-section">
            <h2>Dirección</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <select id="provincia" name="provincia">
                        <?php echo $this->parent->generateSelectOptions($provincias, $this->employeeData['provincia']); ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="distrito">Distrito:</label>
                    <select id="distrito" name="distrito">
                        <?php echo $this->parent->generateSelectOptions($distritos, $this->employeeData['distrito']); ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="corregimiento">Corregimiento:</label>
                    <select id="corregimiento" name="corregimiento">
                        <?php echo $this->parent->generateSelectOptions($corregimientos, $this->employeeData['corregimiento']); ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" value="<?php echo $this->employeeData['calle']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="casa">Casa:</label>
                    <input type="text" id="casa" name="casa" value="<?php echo $this->employeeData['casa']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="comunidad">Comunidad:</label>
                    <input type="text" id="comunidad" name="comunidad" value="<?php echo $this->employeeData['comunidad']; ?>">
                </div>
            </div>
        </div>
        <?php
    }
}
?>