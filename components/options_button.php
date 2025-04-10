<?php
/**
 * Componente Button Option para el sidebar
 * 
 * Este componente genera un botón de opción para el menú lateral
 * 
 * @param string $icon Nombre del icono de Material Icons
 * @param string $text Texto del botón
 * @param string $link Enlace al que redirecciona
 * @param boolean $active Indica si la opción está activa
 */

function renderOptionButton($icon, $text, $link, $active = false) {
    $activeClass = $active ? 'active' : '';
    
    echo '
    <a href="' . $link . '" class="menu-option ' . $activeClass . '">
        <div class="option-icon">
            <span class="material-icons">' . $icon . '</span>
        </div>
        <div class="option-text">
            ' . $text . '
        </div>
    </a>
    ';
}
?>