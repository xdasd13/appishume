<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"><i class="fas fa-edit mr-2"></i>Editar Entrega</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= base_url('/') ?>">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('/entregas') ?>">Entregas</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <span>Editar Entrega #<?= $entrega['identregable'] ?></span>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Editar Entrega #<?= $entrega['identregable'] ?></h4>
                            <a href="<?= base_url('/entregas') ?>" class="btn btn-secondary btn-round ml-3">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Error de validación:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('/entregas/actualizar/' . $entrega['identregable']) ?>" method="post" class="form-validate">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idserviciocontratado" class="form-label">
                                            <i class="fas fa-briefcase mr-2 text-primary"></i>Servicio Contratado *
                                        </label>
                                        <select class="form-control select2" id="idserviciocontratado" name="idserviciocontratado" required>
                                            <option value="">Seleccione un servicio</option>
                                            <?php foreach ($servicios as $servicio): ?>
                                                <option value="<?= $servicio['idserviciocontratado'] ?>" <?= old('idserviciocontratado', $entrega['idserviciocontratado']) == $servicio['idserviciocontratado'] ? 'selected' : '' ?>>
                                                    Servicio #<?= $servicio['idserviciocontratado'] ?> - 
                                                    <?= $servicio['servicio_nombre'] ?? 'Servicio' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Seleccione el servicio que se entregará</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idpersona" class="form-label">
                                            <i class="fas fa-user-tie mr-2 text-info"></i>Persona que Entrega *
                                        </label>
                                        <select class="form-control select2" id="idpersona" name="idpersona" required>
                                            <option value="">Seleccione una persona</option>
                                            <?php foreach ($personas as $persona): ?>
                                                <option value="<?= $persona['idpersona'] ?>" <?= old('idpersona', $entrega['idpersona']) == $persona['idpersona'] ? 'selected' : '' ?>>
                                                    <?= $persona['nombres'] . ' ' . $persona['apellidos'] ?> 
                                                    (<?= $persona['numerodoc'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Seleccione quien realizará la entrega</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fechahoraentrega" class="form-label">
                                            <i class="fas fa-calendar-alt mr-2 text-success"></i>Fecha y Hora de Entrega *
                                        </label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fechahoraentrega" name="fechahoraentrega" 
                                                   value="<?= old('fechahoraentrega', date('Y-m-d\TH:i', strtotime($entrega['fechahoraentrega']))) ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Seleccione la fecha y hora programada para la entrega</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observaciones" class="form-label">
                                            <i class="fas fa-sticky-note mr-2 text-warning"></i>Observaciones
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales sobre la entrega"><?= old('observaciones') ?></textarea>
                                        <small class="form-text text-muted">Información adicional sobre la entrega (opcional)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="confirmacion" required>
                                    <label class="custom-control-label" for="confirmacion">
                                        Confirmo que la información proporcionada es correcta
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Actualizar Entrega
                                </button>
                                <a href="<?= base_url('/entregas/ver/' . $entrega['identregable']) ?>" class="btn btn-outline-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación adicional de fechas
function validarFechaRealista(fecha) {
    const fechaEntrega = new Date(fecha);
    const fechaActual = new Date();
    const fechaMaxima = new Date();
    fechaMaxima.setFullYear(fechaMaxima.getFullYear() + 2); // 2 años en el futuro
    
    return fechaEntrega > fechaActual && fechaEntrega <= fechaMaxima;
}

$('#fechahoraentrega').on('change', function() {
    if (!validarFechaRealista(this.value)) {
        alert('La fecha de entrega debe ser posterior a la fecha actual y no mayor a 2 años');
        this.value = '';
    }
});
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap',
            placeholder: 'Seleccione una opción',
            allowClear: true
        });

        // Validación del formulario
        $('.form-validate').validate({
            rules: {
                idserviciocontratado: {
                    required: true
                },
                idpersona: {
                    required: true
                },
                fechahoraentrega: {
                    required: true,
                    date: true
                }
            },
            messages: {
                idserviciocontratado: {
                    required: "Por favor seleccione un servicio"
                },
                idpersona: {
                    required: "Por favor seleccione una persona"
                },
                fechahoraentrega: {
                    required: "Por favor ingrese la fecha y hora",
                    date: "Por favor ingrese una fecha válida"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>

<style>
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    .select2-container--bootstrap .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
    }
    .form-validate .form-control {
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    .form-validate .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ddd;
    }
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 6px;
    }
    .custom-checkbox {
        margin-top: 1rem;
    }
</style>

<?= $footer; ?>