// Script para sobrescribir el comportamiento del formulario - ejecutado después de jQuery
$(document).ready(function() {
    console.log('Script de sobrescritura cargado');
    
    // Eliminar todos los manejadores de submit existentes
    $('#formExistente, #formNuevo').off('submit');
    
    // Agregar nuevo manejador de submit
    $('#formExistente, #formNuevo').on('submit', function(e) {
        console.log('Formulario interceptado:', $(this).attr('id'));
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Forzar que no se ejecute el submit normal
        console.log('PreventDefault ejecutado, continuando con AJAX...');
        
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonText = $submitButton.html();
        
        console.log('Botón encontrado:', $submitButton.length > 0);
        
        // Validar contraseña antes de enviar
        const passwordField = $form.find('input[type="password"]').first();
        const password = passwordField.val();
        
        console.log('Contraseña:', password, 'Longitud:', password.length);
        
        if (password.length < 8) {
            console.log('Contraseña muy corta');
            Swal.fire({
                icon: 'error',
                title: 'Contraseña débil',
                text: 'La contraseña debe tener al menos 8 caracteres.',
                confirmButtonColor: '#4e73df'
            });
            return false;
        }
        
        console.log('Mostrando SweetAlert...');
        
        // Mostrar confirmación con SweetAlert
        Swal.fire({
            title: '¿Confirmar registro?',
            text: '¿Está seguro de que desea crear este usuario?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear usuario',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            console.log('Resultado SweetAlert:', result);
            if (result.isConfirmed) {
                console.log('Usuario confirmó, enviando formulario...');
                // Mostrar loading en el botón
                $submitButton.prop('disabled', true);
                $submitButton.html('<i class="fas fa-spinner fa-spin me-1"></i> Creando...');
                
                // Enviar formulario
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        if (response.success) {
                            // Mostrar solo el mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: 'Personal Registrado',
                                text: 'El personal ha sido registrado exitosamente.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4e73df'
                            });
                        } else {
                            // Manejar errores de validación
                            let errorMessage = response.message || 'Error al crear el usuario';
                            
                            if (response.errors) {
                                let errorList = '<ul>';
                                for (let field in response.errors) {
                                    errorList += `<li>${response.errors[field]}</li>`;
                                }
                                errorList += '</ul>';
                                errorMessage = `Errores de validación:${errorList}`;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de Validación',
                                html: errorMessage,
                                confirmButtonColor: '#4e73df'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        console.error('Respuesta:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión al crear el usuario',
                            confirmButtonColor: '#4e73df'
                        });
                    },
                    complete: function() {
                        $submitButton.prop('disabled', false);
                        $submitButton.html(originalButtonText);
                    }
                });
            }
        });
        
        // Forzar que no se ejecute el submit normal
        return false;
    });
    
    console.log('Manejador de submit sobrescrito correctamente');
});
