<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Crear Usuario Simple</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-usuario-simple">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="nombreusuario" class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nombreusuario" name="nombreusuario" required>
                                <div class="form-text">Mínimo 5 caracteres</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="<?= base_url() ?>usuarios-simple" class="btn btn-secondary">Ver Usuarios</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            console.log('✅ Formulario simple cargado correctamente');
            
            $('#form-usuario-simple').submit(function(e) {
                e.preventDefault();
                console.log('📝 Enviando formulario simple...');
                
                // Mostrar loading
                Swal.fire({
                    title: 'Creando usuario...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url() ?>usuarios-simple/guardar',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('✅ Respuesta exitosa:', response);
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Limpiar formulario
                                $('#form-usuario-simple')[0].reset();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('❌ Error AJAX:', error);
                        console.log('Status:', status);
                        console.log('Response:', xhr.responseText);
                        
                        Swal.close();
                        Swal.fire({
                            title: 'Error de Conexión',
                            text: 'No se pudo conectar con el servidor: ' + error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
