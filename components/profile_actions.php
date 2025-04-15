<?php
/**
 * Componente para mostrar las acciones de perfil del empleado
 * 
 * @param array $employeeData Datos del empleado
 * @param string $activeTab Pestaña activa por defecto
 */
class ProfileActions {
    private $employeeData;
    private $activeTab;
    
    /**
     * Constructor de la clase
     */
    public function __construct($employeeData, $activeTab = 'personal') {
        $this->employeeData = $employeeData;
        $this->activeTab = $activeTab;
    }
    
    /**
     * Renderizar el componente de acciones de perfil
     */
    public function render() {
        // Construir el nombre completo del empleado
        $nombreCompleto = $this->employeeData['nombre1'];
        if (!empty($this->employeeData['nombre2'])) {
            $nombreCompleto .= ' ' . $this->employeeData['nombre2'];
        }
        $nombreCompleto .= ' ' . $this->employeeData['apellido1'];
        if (!empty($this->employeeData['apellido2'])) {
            $nombreCompleto .= ' ' . $this->employeeData['apellido2'];
        }
        if (!empty($this->employeeData['apellidoc']) && $this->employeeData['usa_ac'] == 1) {
            $nombreCompleto .= ' de ' . $this->employeeData['apellidoc'];
        }
        
        ?>
        <link rel="stylesheet" href="../../assets/employees/profile_actions.css">
        
        <div class="profile-sidebar">
            <div class="profile-image-container">
                <!-- Imagen de perfil o iniciales si no hay imagen -->
                <?php if (isset($this->employeeData['imagen_perfil']) && !empty($this->employeeData['imagen_perfil'])): ?>
                    <img src="<?php echo $this->employeeData['imagen_perfil']; ?>" alt="Foto de perfil">
                <?php else: ?>
                    <div class="profile-initials">
                        <?php echo substr($this->employeeData['nombre1'], 0, 1) . substr($this->employeeData['apellido1'], 0, 1); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-username">
                <?php echo $nombreCompleto; ?>
            </div>
            
            <div class="profile-actions">
                <button class="profile-action-btn <?php echo $this->activeTab == 'personal' ? 'active' : ''; ?>" 
                        data-tab="personal" onclick="cambiarTab('personal')">
                    Informacion Personal
                </button>
                
                <button class="profile-action-btn <?php echo $this->activeTab == 'contacto' ? 'active' : ''; ?>" 
                        data-tab="contacto" onclick="cambiarTab('contacto')">
                    Informacion Contacto
                </button>
                
                <button class="profile-action-btn <?php echo $this->activeTab == 'direccion' ? 'active' : ''; ?>" 
                        data-tab="direccion" onclick="cambiarTab('direccion')">
                    Dirrecion
                </button>
                
                <button class="profile-action-btn <?php echo $this->activeTab == 'laboral' ? 'active' : ''; ?>" 
                        data-tab="laboral" onclick="cambiarTab('laboral')">
                    Cargo
                </button>
            </div>
        </div>

        <script>
            function cambiarTab(tabId) {
                // Ocultar todos los contenedores de información
                document.querySelectorAll('.info-container').forEach(function(container) {
                    container.style.display = 'none';
                });
                
                // Mostrar el contenedor seleccionado
                document.getElementById('container-' + tabId).style.display = 'block';
                
                // Actualizar los botones activos
                document.querySelectorAll('.profile-action-btn').forEach(function(btn) {
                    btn.classList.remove('active');
                });
                
                document.querySelector('.profile-action-btn[data-tab="' + tabId + '"]').classList.add('active');
            }
        </script>
        <?php
    }
}
?>