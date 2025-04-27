// validation.js

// Validación de cédula panameña: formato XX-XXXX-XXXXX
function validarCedulaPanama(input) {
    let valor = input.value;
    let cursorPos = input.selectionStart;
    let valorOriginal = valor;
    let valorLimpio = valor.replace(/[^0-9\-]/g, '');
    valorLimpio = valorLimpio.replace(/--+/g, '-');
    if (valorLimpio.startsWith('-')) valorLimpio = valorLimpio.substring(1);
    let partes = valorLimpio.split('-');
    let partesValidas = [];
    for (let i = 0; i < partes.length; i++) {
        let parte = partes[i];
        if (i === 0) {
            if (parte.length > 2) parte = parte.substring(0, 2);
            let numPrefijo = parseInt(parte, 10);
            if (parte.length > 0 && (isNaN(numPrefijo) || numPrefijo < 1)) {
                parte = '';
            } else if (numPrefijo > 13) {
                if (parte.startsWith('1')) {
                    parte = '1';
                } else {
                    parte = parte.substring(0, parte.length - 1);
                    numPrefijo = parseInt(parte, 10);
                    if (isNaN(numPrefijo) || numPrefijo < 1) {
                        parte = '';
                    }
                }
            }
        } else if (i === 1) {
            if (parte.length > 4) parte = parte.substring(0, 4);
        } else if (i === 2) {
            if (parte.length > 5) parte = parte.substring(0, 5);
        } else {
            break;
        }
        partesValidas.push(parte);
    }
    valorLimpio = partesValidas.join('-');
    if (input.value !== valorLimpio) {
        input.value = valorLimpio;
        let diff = valorOriginal.length - valorLimpio.length;
        let newCursorPos = cursorPos - diff;
        if (cursorPos === valorOriginal.length && valorLimpio.length >= valorOriginal.length) {
            newCursorPos = valorLimpio.length;
        }
        input.setSelectionRange(Math.max(0, newCursorPos), Math.max(0, newCursorPos));
    }
}

// Validación de prefijo panameño: solo números, sin ceros iniciales, máximo 2 dígitos, entre 1 y 13
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
    if (isNaN(numValue)) {
        value = '';
    } else if (numValue < 1) {
        value = '';
    } else if (numValue > 13) {
        // Si es mayor que 13, intentar quitar el último dígito.
        value = originalValue.slice(0, -1).replace(/[^0-9]/g, '');
        // Re-validar por si al quitar el último dígito queda un 0 inicial inválido
        if (value.length > 1 && value.startsWith('0')) {
            value = value.substring(1);
        } else if (value === '0') {
            value = '';
        }
        let recheckNum = parseInt(value, 10);
        if (isNaN(recheckNum) || recheckNum < 1) {
            value = '';
        }
    }
    input.value = value;
    if (typeof updateCedula === 'function') updateCedula();
}

export { validarCedulaPanama, validarPrefijo };