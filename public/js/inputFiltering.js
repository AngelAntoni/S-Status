/**
 * Script para el formulario de añadir servidor
 */

// Función para validar URL
function isValidURL(url) {
    const pattern = new RegExp(
        '^(https?:\\/\\/)?' + // protocolo
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // dominio
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // O dirección IP
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // puerto y ruta
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // parámetros de consulta
        '(\\#[-a-z\\d_]*)?$', // fragmento
        'i'
    );
    return pattern.test(url);
}

// Función para validar y mostrar error en campo URL
function validateURLField(inputElement, errorCallback) {
    const value = inputElement.value.trim();
    
    // Verificar si está vacío
    if (!value) {
        errorCallback('Por favor, ingrese la URL del servidor');
        return false;
    }
    
    // Verificar formato de URL
    if (!isValidURL(value)) {
        errorCallback('Por favor, ingrese una URL válida (ejemplo: https://example.com)');
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a los elementos del formulario
    const urlServidor = document.getElementById('urlServidor');
    const nombreServidor = document.getElementById('nombreServidor');
    const tipoServidor = document.getElementById('tipoServidor');
    const descripcionServidor = document.getElementById('descripcionServidor');
    const btnCancelar = document.getElementById('btnCancelar');
    const btnAñadir = document.getElementById('btnAñadir');

    // Evento para el botón Cancelar
    btnCancelar.addEventListener('click', function() {
        window.location.href = '/dashboard';
    });

    // Evento para el botón Añadir
    btnAñadir.addEventListener('click', function() {
        // Usar la función de validación
        if (!validateURLField(urlServidor, function(errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        })) {
            return;
        }

        // Obtener los valores del formulario
        const url = urlServidor.value;
        const nombre = nombreServidor.value || 'Servidor ' + tipoServidor.value;
        const tipo = tipoServidor.value;
        const descripcion = descripcionServidor.value || 'Servidor añadido desde el formulario';

        // Enviar datos al servidor mediante AJAX
        $.ajax({
            url: '/add-server',
            method: 'POST',
            data: {
                name: nombre,
                url: url,
                type: tipo,
                description: descripcion,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Añadido con éxito',
                    text: 'El servidor ha sido añadido correctamente',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '/dashboard';
                });
            },
            error: function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al guardar el servidor'
                });
            }
        });
    });
});