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
                        <div class="d-flex align-items-center">
                            <h4 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Editar Entrega
                                #<?= $entrega['identregable'] ?></h4>
                            <a href="<?= base_url('/entregas') ?>" class="btn btn-secondary btn-round ml-auto">
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

                        <?php if (session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                <?= session('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/entregas/actualizar/' . $entrega['identregable']) ?>" method="post"
                            class="form-validate" id="formEditarEntrega">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idserviciocontratado" class="form-label">
                                            <i class="fas fa-briefcase mr-2 text-primary"></i>Servicio Contratado *
                                        </label>
                                        <select class="form-control select2" id="idserviciocontratado"
                                            name="idserviciocontratado" required>
                                            <option value="">Seleccione un servicio</option>
                                            <?php foreach ($servicios as $servicio): ?>
                                                <option value="<?= $servicio['idserviciocontratado'] ?>"
                                                    data-fecha-servicio="<?= $servicio['fechahoraservicio'] ?>"
                                                    <?= old('idserviciocontratado', $entrega['idserviciocontratado']) == $servicio['idserviciocontratado'] ? 'selected' : '' ?>>
                                                    Servicio #<?= $servicio['idserviciocontratado'] ?> -
                                                    <?= $servicio['servicio_nombre'] ?? 'Servicio' ?>
                                                    (<?= date('d/m/Y', strtotime($servicio['fechahoraservicio'])) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Servicio ya realizado para programar la
                                            entrega</small>
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
                                        <small class="form-text text-muted">Persona que realizará la entrega</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fechahoraentrega" class="form-label">
                                            <i class="fas fa-calendar-alt mr-2 text-success"></i>Fecha Programada de
                                            Entrega *
                                        </label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fechahoraentrega"
                                                name="fechahoraentrega"
                                                value="<?= old('fechahoraentrega', date('Y-m-d\TH:i', strtotime($entrega['fechahoraentrega']))) ?>"
                                                required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i
                                                        class="fas fa-calendar-check"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Fecha programada para la entrega</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">
                                            <i class="fas fa-tasks mr-2 text-warning"></i>Estado de la Entrega *
                                        </label>
                                        <select class="form-control select2" id="estado" name="estado" required
                                            <?= ($entrega['estado'] ?? 'pendiente') == 'completada' ? 'disabled' : '' ?>>
                                            <option value="pendiente" <?= old('estado', $entrega['estado'] ?? 'pendiente') == 'pendiente' ? 'selected' : '' ?> <?= ($entrega['estado'] ?? 'pendiente') == 'completada' ? 'disabled' : '' ?>>
                                                ⏳ Pendiente
                                            </option>
                                            <option value="completada" <?= old('estado', $entrega['estado'] ?? '') == 'completada' ? 'selected' : '' ?>>
                                                ✅ Completada
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">Estado actual de la entrega</small>

                                        <?php if (($entrega['estado'] ?? 'pendiente') == 'completada'): ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="fas fa-lock mr-2"></i>
                                                Esta entrega ya fue completada y no puede revertirse a pendiente.
                                            </div>
                                            <input type="hidden" name="estado" value="completada">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="grupoFechaReal"
                                        style="display: <?= ($entrega['estado'] ?? 'pendiente') == 'completada' ? 'block' : 'none' ?>;">
                                        <label for="fecha_real_entrega" class="form-label">
                                            <i class="fas fa-calendar-check mr-2 text-primary"></i>Fecha Real de Entrega
                                            *
                                        </label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fecha_real_entrega"
                                                name="fecha_real_entrega"
                                                value="<?= old('fecha_real_entrega', isset($entrega['fecha_real_entrega']) ? date('Y-m-d\TH:i', strtotime($entrega['fecha_real_entrega'])) : date('Y-m-d\TH:i')) ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Fecha en que se realizó realmente la
                                            entrega</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observaciones" class="form-label">
                                            <i class="fas fa-sticky-note mr-2 text-info"></i>Formato de Entrega *
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                                            placeholder="Formato de entrega (físico/digital)"
                                            required><?= old('observaciones', $entrega['observaciones']) ?></textarea>
                                        <small class="form-text text-muted">Describa el formato de entrega y
                                            especificaciones</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de estado actual -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Información actual:</strong><br>
                                • Estado: <span
                                    class="badge badge-<?= ($entrega['estado'] ?? 'pendiente') == 'completada' ? 'success' : 'warning' ?>">
                                    <?= ($entrega['estado'] ?? 'pendiente') == 'completada' ? 'COMPLETADA' : 'PENDIENTE' ?>
                                </span><br>
                                <?php if (isset($entrega['fecha_real_entrega']) && $entrega['fecha_real_entrega']): ?>
                                    • Fecha real de entrega:
                                    <?= date('d/m/Y H:i', strtotime($entrega['fecha_real_entrega'])) ?>
                                <?php endif; ?>
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
                                <a href="<?= base_url('/entregas') ?>"
                                    class="btn btn-outline-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>

                                <?php if (($entrega['estado'] ?? 'pendiente') == 'pendiente'): ?>
                                    <button type="button" class="btn btn-success btn-lg ml-2" id="btnCompletarAhora">
                                        <i class="fas fa-check-circle mr-2"></i>Marcar como Completada Ahora
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar/ocultar campo de fecha real según estado
    function toggleFechaReal() {
        const estadoSelect = document.getElementById('estado');
        const fechaRealGroup = document.getElementById('grupoFechaReal');
        const fechaRealInput = document.getElementById('fecha_real_entrega');

        if (estadoSelect.value === 'completada') {
            fechaRealGroup.style.display = 'block';
            fechaRealInput.setAttribute('required', 'required');

            // Si está vacío, poner fecha y hora actual
            if (!fechaRealInput.value) {
                const now = new Date();
                const localDateTime = now.toISOString().slice(0, 16);
                fechaRealInput.value = localDateTime;
            }
        } else {
            fechaRealGroup.style.display = 'none';
            fechaRealInput.removeAttribute('required');
            fechaRealInput.value = '';
        }
    }

    // Completar entrega automáticamente
    function completarEntregaAhora() {
        if (confirm('¿Está seguro de marcar esta entrega como COMPLETADA? Se usará la fecha y hora actual.')) {
            const now = new Date();
            const localDateTime = now.toISOString().slice(0, 16);

            document.getElementById('estado').value = 'completada';
            document.getElementById('fecha_real_entrega').value = localDateTime;

            // Mostrar campo de fecha real
            toggleFechaReal();

            // Enviar formulario automáticamente
            document.getElementById('formEditarEntrega').submit();
        }
    }

    // Validación adicional de fechas para edición
    function validarFechaEntregaEdicion() {
        const fechaEntregaInput = document.getElementById('fechahoraentrega');
        const servicioSelect = document.getElementById('idserviciocontratado');
        const estadoSelect = document.getElementById('estado');
        const fechaRealInput = document.getElementById('fecha_real_entrega');

        if (!servicioSelect.value) {
            alert('Primero debe seleccionar un servicio contratado');
            fechaEntregaInput.value = '';
            return false;
        }

        // Obtener la fecha del servicio
        const optionSeleccionada = servicioSelect.options[servicioSelect.selectedIndex];
        const fechaServicioStr = optionSeleccionada.getAttribute('data-fecha-servicio');

        if (!fechaServicioStr) {
            alert('Error: No se pudo obtener la fecha del servicio. Contacte al administrador.');
            return false;
        }

        const fechaServicio = new Date(fechaServicioStr);
        const fechaEntrega = new Date(fechaEntregaInput.value);
        const fechaMaxima = new Date(fechaServicio);
        fechaMaxima.setDate(fechaMaxima.getDate() + 21);

        if (fechaEntrega <= fechaServicio) {
            alert('ERROR: La fecha de entrega debe ser POSTERIOR a la fecha del servicio (' + fechaServicio.toLocaleDateString() + ')');
            return false;
        }

        if (fechaEntrega > fechaMaxima) {
            alert('ERROR: La entrega no puede ser más de 3 semanas después del servicio. Máximo permitido: ' + fechaMaxima.toLocaleDateString());
            return false;
        }

        // Validaciones para estado "completada"
        if (estadoSelect.value === 'completada' && fechaRealInput.value) {
            const fechaReal = new Date(fechaRealInput.value);
            const hoy = new Date();

            if (fechaReal > hoy) {
                alert('ERROR: La fecha real de entrega no puede ser futura');
                fechaRealInput.value = '';
                return false;
            }
        }

        return true;
    }

    $(document).ready(function () {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap',
            placeholder: 'Seleccione una opción',
            allowClear: true
        });

        // Mostrar/ocultar fecha real al cambiar estado
        $('#estado').on('change', function () {
            toggleFechaReal();
        });

        // Botón para completar ahora
        $('#btnCompletarAhora').on('click', function () {
            completarEntregaAhora();
        });

        // Inicializar visibilidad del campo fecha real
        toggleFechaReal();

        // Validar fecha cuando cambia
        $('#fechahoraentrega').on('change', function () {
            validarFechaEntregaEdicion();
        });

        // Validar todo el formulario antes de enviar
        $('#formEditarEntrega').on('submit', function (e) {
            if (!validarFechaEntregaEdicion()) {
                e.preventDefault();
                return false;
            }

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
                estado: {
                    required: true
                },
                fecha_real_entrega: {
                    required: function () {
                        return $('#estado').val() === 'completada';
                    }
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
                    required: "Por favor ingrese la fecha programada",
                    date: "Por favor ingrese una fecha válida"
                },
                estado: {
                    required: "Por favor seleccione el estado de la entrega"
                },
                fecha_real_entrega: {
                    required: "La fecha real de entrega es obligatoria para entregas completadas"
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

    #btnCompletarAhora {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
    }

    #btnCompletarAhora:hover {
        background: linear-gradient(45deg, #218838, #1e9e8a);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>

<?= $footer; ?>