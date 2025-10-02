// Script simple para manejar el registro de personal
$(document).ready(function() {
    console.log('Script de registro cargado');
    
    // Manejar clic del botón
    $('#btnCrearPersonal').on('click', function(e) {
        e.preventDefault();
        console.log('Botón de crear personal clickeado');
        
        const $form = $('#formNuevo');
        const $button = $(this);
        const originalText = $button.html();
        
        // Validar contraseña
        const password = $('#password_nuevo').val();
        if (password.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'Contraseña débil',
                text: 'La contraseña debe tener al menos 8 caracteres.',
                confirmButtonColor: '#4e73df'
            });
            return;
        }
        
        // Mostrar confirmación
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
            if (result.isConfirmed) {
                // Mostrar loading
                $button.prop('disabled', true);
                $button.html('<i class="fas fa-spinner fa-spin me-1"></i> Creando...');
                
                // Enviar formulario
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Personal Registrado',
                                text: 'El personal ha sido registrado exitosamente.',
                                confirmButtonText: 'Ver Listado',
                                confirmButtonColor: '#4e73df'
                            }).then(() => {
                                // Redirigir al listado de usuarios
                                window.location.href = '/usuarios';
                            });
                        } else {
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión al crear el usuario',
                            confirmButtonColor: '#4e73df'
                        });
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                        $button.html(originalText);
                    }
                });
            }
        });
    });
    
    console.log('Manejador de clic configurado');
});
