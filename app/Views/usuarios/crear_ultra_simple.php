<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - ISHUME</title>
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
                        <h3 class="text-center">Crear Usuario Ultra Simple</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-usuario-ultra-simple" method="POST" action="<?= base_url() ?>usuarios-simple/guardar">
                            <div class="mb-3">
                                <label for="nombreusuario" class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nombreusuario" name="nombreusuario" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
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
            console.log('✅ Formulario ultra simple cargado');
            
            $('#form-usuario-ultra-simple').submit(function(e) {
                e.preventDefault();
                console.log('📝 Enviando formulario ultra simple...');
                
                var formData = {
                    nombreusuario: $('#nombreusuario').val(),
                    email: $('#email').val(),
                    password: $('#password').val()
                };
                
                console.log('Datos a enviar:', formData);
                
                $.post('<?= base_url() ?>usuarios-simple/guardar', formData)
                    .done(function(response) {
                        console.log('✅ Respuesta:', response);
                        
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                console.log('Error parsing JSON:', e);
                                alert('Usuario creado exitosamente');
                                $('#form-usuario-ultra-simple')[0].reset();
                                return;
                            }
                        }
                        
                        if (response.success) {
                            alert('✅ ' + response.message);
                            $('#form-usuario-ultra-simple')[0].reset();
                        } else {
                            alert('❌ ' + response.message);
                        }
                    })
                    .fail(function(xhr, status, error) {
                        console.log('❌ Error:', error);
                        console.log('Status:', status);
                        console.log('Response:', xhr.responseText);
                        alert('Error: ' + error);
                    });
            });
        });
    </script>
</body>
</html>
