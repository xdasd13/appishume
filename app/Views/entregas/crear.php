<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <!-- <div class="page-header">
            <h4 class="page-title"><i class="fas fa-plus-circle mr-2"></i>Nueva Entrega</h4>
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
                    <span>Nueva Entrega</span>
                </li>
            </ul>
        </div> -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Registrar Nueva Entrega</h4>
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
                        
                        <form action="<?= base_url('/entregas/guardar') ?>" method="post" class="form-validate">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idserviciocontratado" class="form-label">
                                            <i class="fas fa-briefcase mr-2 text-primary"></i>Servicio Contratado *
                                        </label>
                                        <select class="form-control select2" id="idserviciocontratado" name="idserviciocontratado" required>
                                            <option value="">Seleccione un servicio</option>
                                            <?php foreach ($servicios as $servicio): ?>
                                                <option value="<?= $servicio['idserviciocontratado'] ?>" 
                                                        data-fecha-servicio="<?= $servicio['fechahoraservicio'] ?>"
                                                        <?= old('idserviciocontratado') == $servicio['idserviciocontratado'] ? 'selected' : '' ?>>
                                                    Servicio #<?= $servicio['idserviciocontratado'] ?> - 
                                                    <?= $servicio['servicio_nombre'] ?> 
                                                    (<?= date('d/m/Y', strtotime($servicio['fechahoraservicio'])) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Seleccione el servicio ya realizado para programar la entrega</small>
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
                                                <option value="<?= $persona['idpersona'] ?>" <?= old('idpersona') == $persona['idpersona'] ? 'selected' : '' ?>>
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
                                            <input type="datetime-local" class="form-control" id="fechahoraentrega" name="fechahoraentrega" value="<?= old('fechahoraentrega') ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Máximo 3 semanas después del servicio. Días hábiles: Lunes a Viernes, 8:00 AM - 6:00 PM</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado_entrega" class="form-label">
                                            <i class="fas fa-tasks mr-2 text-warning"></i>Estado de la Entrega *
                                        </label>
                                        <select class="form-control select2" id="estado_entrega" name="estado_entrega" required>
                                            <option value="">Seleccione el estado</option>
                                            <option value="pendiente" <?= old('estado_entrega') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="completada" <?= old('estado_entrega') == 'completada' ? 'selected' : '' ?>>Completada</option>
                                        </select>
                                        <small class="form-text text-muted">Seleccione si la entrega está pendiente o ya se realizó</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observaciones" class="form-label">
                                            <i class="fas fa-sticky-note mr-2 text-info"></i>Formato de Entrega *
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Ej: USB físico, link digital, cuadros 30x40, etc." required><?= old('observaciones') ?></textarea>
                                        <small class="form-text text-muted">Describa el formato de entrega (físico/digital) y especificaciones</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="confirmacion" required>
                                    <label class="custom-control-label" for="confirmacion">
                                        Confirmo que el servicio ya fue realizado y la información de entrega es correcta
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Registrar Entrega
                                </button>
                                <a href="<?= base_url('/entregas') ?>" class="btn btn-outline-secondary btn-lg ml-2">
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
// Validación adicional de fechas para productora audiovisual
function validarFechaEntrega() {
    const fechaEntregaInput = document.getElementById('fechahoraentrega');
    const servicioSelect = document.getElementById('idserviciocontratado');
    const estadoSelect = document.getElementById('estado_entrega');
    const fechaRealInput = document.getElementById('fecha_real_entrega');
    
    if (!servicioSelect.value) {
        alert('Primero debe seleccionar un servicio contratado');
        fechaEntregaInput.value = '';
        return false;
    }
    
    // Obtener la fecha del servicio desde el data attribute
    const optionSeleccionada = servicioSelect.options[servicioSelect.selectedIndex];
    const fechaServicioStr = optionSeleccionada.getAttribute('data-fecha-servicio');
    
    if (!fechaServicioStr) {
        alert('Error: No se pudo obtener la fecha del servicio. Contacte al administrador.');
        return false;
    }
    
    const fechaServicio = new Date(fechaServicioStr);
    const fechaEntrega = new Date(fechaEntregaInput.value);
    const fechaMaxima = new Date(fechaServicio);
    fechaMaxima.setDate(fechaMaxima.getDate() + 21); // 3 semanas después
    
    // Validar que sea día hábil (lunes a viernes)
    const diaEntrega = fechaEntrega.getDay();
    const esFinDeSemana = (diaEntrega === 0 || diaEntrega === 6);
    
    // Validar horario laboral (8am - 6pm)
    const horaEntrega = fechaEntrega.getHours();
    const fueraHorarioLaboral = (horaEntrega < 8 || horaEntrega > 18);
    
    if (fechaEntrega <= fechaServicio) {
        alert('ERROR: La fecha de entrega debe ser POSTERIOR a la fecha del servicio (' + fechaServicio.toLocaleDateString() + ')');
        fechaEntregaInput.value = '';
        return false;
    }
    
    if (fechaEntrega > fechaMaxima) {
        alert('ERROR: La entrega no puede ser más de 3 semanas después del servicio. Máximo permitido: ' + fechaMaxima.toLocaleDateString());
        fechaEntregaInput.value = '';
        return false;
    }
    
    if (esFinDeSemana) {
        if (!confirm('ADVERTENCIA: Está programando una entrega para fin de semana. ¿Está seguro?')) {
            fechaEntregaInput.value = '';
            return false;
        }
    }
    
    if (fueraHorarioLaboral) {
        if (!confirm('ADVERTENCIA: Está programando una entrega fuera del horario laboral (8:00 AM - 6:00 PM). ¿Está seguro?')) {
            fechaEntregaInput.value = '';
            return false;
        }
    }
    
    // Validaciones para estado "completada"
    if (estadoSelect.value === 'completada') {
        const hoy = new Date();
        
        if (fechaEntrega > hoy) {
            if (!confirm('ADVERTENCIA: Marcó la entrega como COMPLETADA pero la fecha programada es futura. ¿Está seguro?')) {
                return false;
            }
        }
        
        // Si hay fecha real de entrega, validar que sea coherente
        if (fechaRealInput.value) {
            const fechaReal = new Date(fechaRealInput.value);
            if (fechaReal > hoy) {
                alert('ERROR: La fecha real de entrega no puede ser futura');
                fechaRealInput.value = '';
                return false;
            }
        }
    }
    
    return true;
}

// Mostrar/ocultar campo de fecha real según estado
function toggleFechaReal() {
    const estadoSelect = document.getElementById('estado_entrega');
    const fechaRealGroup = document.getElementById('fecha_real_entrega').closest('.form-group');
    
    if (estadoSelect.value === 'completada') {
        fechaRealGroup.style.display = 'block';
    } else {
        fechaRealGroup.style.display = 'none';
        document.getElementById('fecha_real_entrega').value = '';
    }
}

$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });

    // Mostrar/ocultar fecha real al cambiar estado
    $('#estado_entrega').on('change', function() {
        toggleFechaReal();
    });

    // Inicializar visibilidad del campo fecha real
    toggleFechaReal();

    // Validar fecha cuando cambia
    $('#fechahoraentrega').on('change', function() {
        validarFechaEntrega();
    });

    // Validar todo el formulario antes de enviar
    $('.form-validate').on('submit', function(e) {
        if (!validarFechaEntrega()) {
            e.preventDefault();
            return false;
        }
        
        // Validación adicional de confirmación
        if (!$('#confirmacion').is(':checked')) {
            alert('Debe confirmar que la información es correcta');
            e.preventDefault();
            return false;
        }
        
        return true;
    });

    // Validación del formulario con jQuery Validate
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
            },
            estado_entrega: {
                required: true
            },
            observaciones: {
                required: true,
                minlength: 10
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
            },
            estado_entrega: {
                required: "Por favor seleccione el estado de la entrega"
            },
            observaciones: {
                required: "Por favor describa el formato de entrega",
                minlength: "Debe describir mínimo 10 caracteres"
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

    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header .btn {
            margin-top: 10px;
            margin-left: 0 !important;
            align-self: flex-end;
        }
    }
</style>

<?= $footer; ?>