<?php
/**
 * Componente para mostrar la información de contacto del empleado
 * 
 * @param array $employeeData Datos del empleado
 */
?>
<link rel="stylesheet" href="assets/admin/employee_details.css">
<!-- Incluir Google Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                    <input id="celular" name="celular" value="<?php echo $this->employeeData['celular']; ?>" maxlength="8" oninput="this.value = validarSoloNumeros(this.value)" required>
                </div>
                
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="celular">Prefijo:</label>
                    <input id="pref_telfno" name="pref_telfno" value="+507" readonly>
                </div>
                <div class="form-group">
                    <label for="telefono">Telefono:</label>
                    <input id="telefono" name="telefono" value="<?php echo $this->employeeData['telefono']; ?>" maxlength="7" oninput="this.value = validarSoloNumeros(this.value)" required>
                </div>
                
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo $this->employeeData['correo']; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label for="password-input">Contraseña:</label>
                    <div class="password-input-container">
                        <input type="password" id="password-input" name="contraseña" 
                               value="<?php echo isset($this->employeeData['contraseña']) ? $this->employeeData['contraseña'] : ''; ?>"
                               minlength="8" maxlength="16" 
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d|\W).{8,16}$" 
                               title="La contraseña debe tener entre 8 y 16 caracteres, incluir al menos una mayúscula, una minúscula y un número o carácter especial."
                               oninput="validatePassword()" 
                               required>
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                    <div id="password-message" class="password-validation-message"></div>
                </div>
            </div>
        </div>
        <script>
            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password-input'); // Usar el nuevo ID
                const icon = document.querySelector('.toggle-password-btn .material-icons');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.textContent = 'visibility_off'; // Icono Material Icons
                } else {
                    passwordInput.type = 'password';
                    icon.textContent = 'visibility'; // Icono Material Icons
                }
            }

            function validatePassword() {
                const passwordInput = document.getElementById('password-input');
                const messageDiv = document.getElementById('password-message');
                const password = passwordInput.value;
                const minLength = 8;
                const maxLength = 16;
                const hasUpperCase = /[A-Z]/.test(password);
                const hasLowerCase = /[a-z]/.test(password);
                const hasNumberOrSpecial = /[\d\W]/.test(password); // \d para números, \W para no alfanuméricos (especiales)
                const isValidLength = password.length >= minLength && password.length <= maxLength;

                let messages = [];
                if (!isValidLength) {
                    messages.push(`Debe tener entre ${minLength} y ${maxLength} caracteres.`);
                }
                if (!hasUpperCase) {
                    messages.push("Debe contener al menos una mayúscula.");
                }
                if (!hasLowerCase) {
                    messages.push("Debe contener al menos una minúscula.");
                }
                if (!hasNumberOrSpecial) {
                    messages.push("Debe contener al menos un número o carácter especial.");
                }

                if (messages.length === 0) {
                    messageDiv.textContent = "Contraseña válida.";
                    messageDiv.className = 'password-validation-message valid';
                    passwordInput.setCustomValidity(''); // Marcar como válido para la validación del navegador
                } else {
                    messageDiv.innerHTML = messages.join("<br>"); // Usar innerHTML para saltos de línea
                    messageDiv.className = 'password-validation-message invalid';
                    // Establecer un mensaje de validación personalizado para evitar el envío del formulario si es inválido
                    passwordInput.setCustomValidity(messages.join(" ")); 
                }
            }

            function validarSoloNumeros(value) {
                return value.replace(/[^0-9]/g, '');
            }

            // Llamar a validatePassword al cargar la página si ya hay una contraseña (ej. en edición)
            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById('password-input').value) {
                    validatePassword();
                }
            });
        </script>
        <?php
    }
}
?>