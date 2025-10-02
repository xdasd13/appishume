// Funcionalidad completa del formulario de usuarios - ejecutado después de jQuery
$(document).ready(function() {
    console.log('jQuery disponible, inicializando funcionalidades completas');
    
    // Variables globales para validación DNI
    let dniValidationTimeout;
    let lastValidatedDni = '';
    let dniValidationInProgress = false;
    
    // Función para mostrar alertas con SweetAlert2
    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#4e73df'
        });
    }
    
    // Función para toggle de visibilidad de contraseña
    function setupPasswordToggle(passwordId, toggleId) {
        const passwordInput = document.getElementById(passwordId);
        const toggleButton = document.getElementById(toggleId);
        
        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Cambiar icono
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }
    }
    
    // Configurar toggles para todas las contraseñas
    setupPasswordToggle('password_existente', 'togglePasswordExistente');
    setupPasswordToggle('confirm_password_existente', 'toggleConfirmPasswordExistente');
    setupPasswordToggle('password_nuevo', 'togglePasswordNuevo');
    setupPasswordToggle('confirm_password_nuevo', 'toggleConfirmPasswordNuevo');
    
    // Validar fortaleza de contraseña y actualizar requisitos visualmente
    $('#password_existente, #password_nuevo').on('input', function() {
        const password = $(this).val();
        const formType = $(this).attr('id') === 'password_existente' ? 'existente' : 'nuevo';
        const strengthBar = $(this).closest('.row').find('.password-strength');
        const strength = calcularFortalezaPassword(password);
        
        console.log('Validando contraseña:', password, 'FormType:', formType);
        
        // Actualizar barra de fortaleza
        if (strengthBar.length) {
            strengthBar.css('width', strength.percentage + '%');
            strengthBar.removeClass('bg-danger bg-warning bg-success').addClass(strength.class);
        }
        
        // Actualizar visualización de requisitos
        updatePasswordRequirements(password, formType);
        
        // Validar si cumple requisitos
        if (password.length >= 8 && strength.score >= 3) {
            $(this).get(0).setCustomValidity('');
        } else {
            $(this).get(0).setCustomValidity('La contraseña no cumple con los requisitos de seguridad');
        }
    });
    
    function updatePasswordRequirements(password, formType) {
        console.log('Actualizando requisitos para:', formType, 'Contraseña:', password);
        
        // Longitud
        if (password.length >= 8) {
            $(`#length-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Longitud cumplida');
        } else {
            $(`#length-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Longitud no cumplida');
        }
        
        // Mayúscula
        if (/[A-Z]/.test(password)) {
            $(`#uppercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Mayúscula encontrada');
        } else {
            $(`#uppercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Mayúscula no encontrada');
        }
        
        // Minúscula
        if (/[a-z]/.test(password)) {
            $(`#lowercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Minúscula encontrada');
        } else {
            $(`#lowercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Minúscula no encontrada');
        }
        
        // Número
        if (/[0-9]/.test(password)) {
            $(`#number-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Número encontrado');
        } else {
            $(`#number-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Número no encontrado');
        }
        
        // Símbolo
        if (/[^A-Za-z0-9]/.test(password)) {
            $(`#special-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Símbolo encontrado');
        } else {
            $(`#special-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Símbolo no encontrado');
        }
    }
    
    function calcularFortalezaPassword(password) {
        let score = 0;
        
        // Longitud mínima
        if (password.length >= 8) score++;
        
        // Contiene letra minúscula
        if (/[a-z]/.test(password)) score++;
        
        // Contiene letra mayúscula
        if (/[A-Z]/.test(password)) score++;
        
        // Contiene número
        if (/[0-9]/.test(password)) score++;
        
        // Contiene símbolo
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        // Calcular porcentaje y clase
        let percentage = (score / 5) * 100;
        let strengthClass = 'bg-danger';
        
        if (score >= 4) {
            strengthClass = 'bg-success';
        } else if (score >= 3) {
            strengthClass = 'bg-warning';
        }
        
        return {
            score: score,
            percentage: percentage,
            class: strengthClass
        };
    }
    
    // Validar que las contraseñas coincidan
    $('#confirm_password_existente, #confirm_password_nuevo').on('input', function() {
        const formId = $(this).closest('form').attr('id');
        const passwordField = formId === 'formExistente' ? '#password_existente' : '#password_nuevo';
        const password = $(passwordField).val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword) {
            $(this).get(0).setCustomValidity('Las contraseñas no coinciden');
        } else {
            $(this).get(0).setCustomValidity('');
        }
    });
    
    // Manejo de formularios
    $('#formExistente, #formNuevo').on('submit', function(e) {
        console.log('Formulario enviado:', $(this).attr('id'));
        e.preventDefault();
        
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
            showAlert('error', 'Contraseña débil', 'La contraseña debe tener al menos 8 caracteres.');
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
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message || 'Usuario creado correctamente',
                                confirmButtonColor: '#4e73df'
                            }).then(() => {
                                // Limpiar el formulario
                                $form[0].reset();
                                
                                // Resetear validaciones visuales
                                $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                                $form.find('.requirement-met, .requirement-unmet').removeClass('requirement-met requirement-unmet').addClass('requirement-unmet');
                                
                                // Resetear estado de DNI
                                $('#dni-status').html('');
                                $('#nombres, #apellidos, #email_nuevo').val('').removeClass('is-valid bg-light').attr('readonly', false);
                                $('#privacy-notice').hide();
                                lastValidatedDni = '';
                                
                                // Mostrar mensaje de éxito y opciones
                                mostrarExitoYLimpiar();
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
                        showAlert('error', 'Error', 'Error de conexión al crear el usuario');
                    },
                    complete: function() {
                        $submitButton.prop('disabled', false);
                        $submitButton.html(originalButtonText);
                    }
                });
            }
        });
    });
    
    // Inicializar validación DNI después de que jQuery esté disponible
    initializeDniValidation();
    
    function initializeDniValidation() {
        console.log('Inicializando validación DNI con jQuery disponible');
        
        // Bind eventos de validación DNI
        $('#numerodoc').off('input').on('input', function() {
            const dni = $(this).val().trim();
            const $input = $(this);
            
            // Limpiar timeout anterior
            clearTimeout(dniValidationTimeout);
            
            // Reset visual states
            resetDniValidationState();
            
            // Validar formato básico
            if (dni.length === 0) {
                return;
            }
            
            if (!/^\d{1,8}$/.test(dni)) {
                showDniError('Solo se permiten números');
                return;
            }
            
            if (dni.length < 8) {
                $('#example-numerodoc').text(`Faltan ${8 - dni.length} dígitos`).addClass('text-muted');
                return;
            } else {
                $('#example-numerodoc').text('Ejemplo: 12345678 (8 dígitos)').removeClass('text-muted');
            }
            
            // Si es el mismo DNI ya validado, no revalidar
            if (dni === lastValidatedDni) {
                return;
            }
            
            // Debounce: esperar 500ms sin escribir
            dniValidationTimeout = setTimeout(() => {
                validateDniWithReniec(dni);
            }, 500);
        });

        // Limpiar validación cuando se borra el campo
        $('#numerodoc').off('keyup').on('keyup', function() {
            if ($(this).val().length === 0) {
                resetDniValidationState();
                $('#numerodoc').removeClass('is-valid is-invalid');
                $('#nombres, #apellidos, #email_nuevo').val('').removeClass('is-valid bg-light').attr('readonly', false);
                $('#privacy-notice').hide();
                lastValidatedDni = '';
            }
        });

        // Prevenir envío del formulario si hay validación en progreso
        $('#formNuevo').off('submit').on('submit', function(e) {
            if (dniValidationInProgress) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Validación en Progreso',
                    text: 'Por favor espere a que termine la validación del DNI',
                    timer: 2000
                });
                return false;
            }
        });
    }
    
    // Función para validar DNI con RENIEC
    function validateDniWithReniec(dni) {
        if (dniValidationInProgress) {
            return;
        }
        
        dniValidationInProgress = true;
        showDniLoading();
        
        $.ajax({
            url: '/usuarios/ajax-check-dni',
            type: 'POST',
            data: {
                dni: dni,
                csrf_test_name: $('input[name="csrf_test_name"]').val()
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta RENIEC:', response);
                
                if (response.success) {
                    handleDniSuccess(response.data, dni);
                } else if (response.error === 'dni_exists_active') {
                    handleDniExistsActive(response.data);
                } else if (response.error === 'dni_exists_inactive') {
                    handleDniExistsInactive(response.data);
                } else if (response.error === 'dni_exists_no_user') {
                    handleDniExistsNoUser(response.data);
                } else {
                    handleDniError(response.message || 'Error al validar DNI');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                handleDniError('Error de conexión al validar DNI');
            },
            complete: function() {
                dniValidationInProgress = false;
            }
        });
    }
    
    // Función para manejar éxito de validación DNI
    function handleDniSuccess(data, dni) {
        console.log('DNI válido encontrado:', data);
        
        $('#numerodoc').removeClass('is-invalid').addClass('is-valid');
        $('#nombres').val(data.nombres).addClass('is-valid bg-light').attr('readonly', true);
        $('#apellidos').val(data.apellidos).addClass('is-valid bg-light').attr('readonly', true);
        $('#email_nuevo').val(generateEmail(data.nombres, data.apellidos)).addClass('is-valid bg-light').attr('readonly', true);
        
        showDniSuccess(`DNI válido encontrado en RENIEC`);
        $('#privacy-notice').show();
        
        lastValidatedDni = dni;
    }
    
    // Función para manejar DNI existente activo
    function handleDniExistsActive(data) {
        $('#numerodoc').removeClass('is-valid').addClass('is-invalid');
        showDniError(`El DNI ${data.dni} ya está registrado y activo para ${data.nombre_completo}`);
    }
    
    // Función para manejar DNI existente inactivo
    function handleDniExistsInactive(data) {
        $('#numerodoc').removeClass('is-valid').addClass('is-invalid');
        showDniError(`El DNI ${data.dni} ya está registrado pero inactivo para ${data.nombre_completo}. Contacte al administrador.`);
    }
    
    // Función para manejar DNI existente sin usuario
    function handleDniExistsNoUser(data) {
        $('#numerodoc').removeClass('is-valid').addClass('is-invalid');
        showDniError(`El DNI ${data.dni} ya está registrado para ${data.nombre_completo} pero no tiene credenciales de acceso. Contacte al administrador.`);
    }
    
    // Función para manejar error de validación DNI
    function handleDniError(message) {
        $('#numerodoc').removeClass('is-valid').addClass('is-invalid');
        showDniError(message);
    }
    
    // Función para mostrar estado de carga
    function showDniLoading() {
        $('#dni-status').html('<i class="fas fa-spinner fa-spin text-info"></i> Validando...');
    }
    
    // Función para mostrar éxito
    function showDniSuccess(message) {
        $('#dni-status').html(`<i class="fas fa-check-circle text-success"></i> ${message}`);
    }
    
    // Función para mostrar error
    function showDniError(message) {
        $('#dni-status').html(`<i class="fas fa-times-circle text-danger"></i> ${message}`);
    }
    
    // Función para resetear estado de validación
    function resetDniValidationState() {
        $('#dni-status').html('');
        $('#numerodoc').removeClass('is-valid is-invalid');
    }
    
    // Función para generar email
    function generateEmail(nombres, apellidos) {
        const nombre = nombres.toLowerCase().split(' ')[0];
        const apellido = apellidos.toLowerCase().split(' ')[0];
        return `${nombre}.${apellido}@ishume.com`;
    }
    
    // Función para mostrar mensaje de éxito y limpiar formulario
    function mostrarExitoYLimpiar() {
        console.log('Mostrando mensaje de éxito y limpiando formulario');
        
        // Mostrar mensaje de éxito con opción de ver listado
        Swal.fire({
            icon: 'success',
            title: '¡Usuario Creado Exitosamente!',
            text: 'El usuario ha sido registrado correctamente.',
            showCancelButton: true,
            confirmButtonText: 'Ver Listado de Usuarios',
            cancelButtonText: 'Crear Otro Usuario',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir al listado de usuarios
                window.location.href = '/usuarios';
            } else {
                // Quedarse en el formulario para crear otro usuario
                console.log('Usuario decidió crear otro usuario');
            }
        });
    }
    
    console.log('Todas las funcionalidades inicializadas correctamente');
});
