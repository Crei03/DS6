<?php
/**
 * Componente para mostrar una modal con el resultado de operaciones CRUD
 * 
 * @param string $tipo_mensaje Tipo de mensaje (success o error)
 * @param string $mensaje Texto del mensaje
 * @param string $pagina_destino URL a la que redireccionar si el usuario confirma
 * @param string $titulo_modal Título de la modal
 * @param array $opciones_navegacion Opciones de navegación personalizadas
 */
class ModalResult {
    private $tipo_mensaje;
    private $mensaje;
    private $pagina_destino;
    private $titulo_modal;
    private $opciones_navegacion;
    
    /**
     * Constructor de la clase
     * 
     * @param string $tipo_mensaje Tipo de mensaje (success o error)
     * @param string $mensaje Texto del mensaje
     * @param string $pagina_destino URL predeterminada para redirección
     * @param string $titulo_modal Título de la modal
     * @param array $opciones_navegacion Lista de opciones de navegación (url, texto, clase)
     */
    public function __construct($tipo_mensaje, $mensaje, $pagina_destino, $titulo_modal = 'Resultado de la operación', $opciones_navegacion = []) {
        $this->tipo_mensaje = $tipo_mensaje;
        $this->mensaje = $mensaje;
        $this->pagina_destino = $pagina_destino;
        $this->titulo_modal = $titulo_modal;
        
        // Si no se especifican opciones, crear una opción predeterminada
        if (empty($opciones_navegacion)) {
            $this->opciones_navegacion = [
                [
                    'url' => $pagina_destino,
                    'texto' => 'Aceptar',
                    'clase' => 'btn-primary'
                ]
            ];
        } else {
            $this->opciones_navegacion = $opciones_navegacion;
        }
    }
    
    /**
     * Renderiza la modal
     */
    public function render() {
        // Identificador único para la modal
        $modal_id = 'modal_' . uniqid();
        
        // Código HTML de la modal
        ?>
        <!-- Estilos de la modal -->
        <style>
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }
            
            .modal-content {
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                width: 90%;
                max-width: 500px;
                padding: 0;
                animation: modalFadeIn 0.3s ease-out;
            }
            
            .modal-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header h3 {
                margin: 0;
                font-size: 18px;
                color: #333;
            }
            
            .modal-body {
                padding: 20px;
                text-align: center;
            }
            
            .modal-footer {
                padding: 15px 20px;
                border-top: 1px solid #dee2e6;
                display: flex;
                justify-content: <?php echo count($this->opciones_navegacion) > 1 ? 'space-between' : 'center'; ?>;
                border-bottom-left-radius: 8px;
                border-bottom-right-radius: 8px;
            }
            
            .modal-message {
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 4px;
            }
            
            .modal-message.success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            
            .modal-message.error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            
            .btn-modal {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 4px;
                text-decoration: none;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s, transform 0.1s;
                border: none;
                font-size: 14px;
                margin: 0 5px;
            }
            
            .btn-modal:hover {
                transform: translateY(-1px);
            }
            
            .btn-modal:active {
                transform: translateY(1px);
            }
            
            .btn-primary {
                background-color: #007bff;
                color: white;
            }
            
            .btn-primary:hover {
                background-color: #0069d9;
            }
            
            .btn-secondary {
                background-color: #6c757d;
                color: white;
            }
            
            .btn-secondary:hover {
                background-color: #5a6268;
            }
            
            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        
        <!-- Contenedor de la modal -->
        <div id="<?php echo $modal_id; ?>_backdrop" class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php echo $this->titulo_modal; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="modal-message <?php echo $this->tipo_mensaje; ?>">
                        <?php echo $this->mensaje; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php foreach($this->opciones_navegacion as $opcion): ?>
                    <a href="<?php echo $opcion['url']; ?>" class="btn-modal <?php echo $opcion['clase']; ?>"><?php echo $opcion['texto']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <script>
            // Mostrar la modal automáticamente
            document.getElementById('<?php echo $modal_id; ?>_backdrop').style.display = 'flex';
            
            // Evitar que se cierre la modal al hacer clic fuera de ella
            document.getElementById('<?php echo $modal_id; ?>_backdrop').addEventListener('click', function(e) {
                if (e.target === this) {
                    e.stopPropagation(); // No hacer nada (mantener la modal abierta)
                }
            });
        </script>
        <?php
    }
}
?>