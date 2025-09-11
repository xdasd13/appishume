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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Lista de Usuarios</h3>
                        <a href="<?= base_url() ?>usuarios-simple/crear" class="btn btn-primary">Crear Usuario</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($usuarios)): ?>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?= is_array($usuario) ? $usuario['idusuario'] : $usuario->idusuario ?></td>
                                                <td><?= is_array($usuario) ? $usuario['nombreusuario'] : $usuario->nombreusuario ?></td>
                                                <td><?= is_array($usuario) ? ($usuario['email'] ?? 'Sin email') : ($usuario->email ?? 'Sin email') ?></td>
                                                <td><?= is_array($usuario) ? ($usuario['tipo_usuario'] ?? 'trabajador') : ($usuario->tipo_usuario ?? 'trabajador') ?></td>
                                                <td>
                                                    <?php 
                                                    $estado = is_array($usuario) ? $usuario['estado'] : $usuario->estado;
                                                    if ($estado == 1): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php $id = is_array($usuario) ? $usuario['idusuario'] : $usuario->idusuario; ?>
                                                    <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?= $id ?>)">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No hay usuarios registrados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function eliminarUsuario(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url() ?>usuarios-simple/eliminar/' + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Eliminado', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudo eliminar el usuario', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
