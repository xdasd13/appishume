<?= $header ?>
<div class="container">
    <h2><?= $titulo ?></h2>
    
    <div class="card">
        <div class="card-header">
            <h4>Información del Servicio</h4>
        </div>
        <div class="card-body">
            <p><strong>Servicio:</strong> <?= $servicio->servicio ?></p>
            <p><strong>Cliente:</strong> <?= !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos ?></p>
            <p><strong>Fecha del Evento:</strong> <?= date('d/m/Y', strtotime($servicio->fechaevento)) ?></p>
        </div>
    </div>
    
    <form method="post" action="<?= base_url('equipos/guardar') ?>" class="mt-4">
        <input type="hidden" name="idserviciocontratado" value="<?= $servicio->idserviciocontratado ?>">
        
        <div class="mb-3">
            <label for="idusuario" class="form-label">Usuario/Técnico</label>
            <select class="form-select" id="idusuario" name="idusuario" required>
                <option value="">Seleccionar usuario</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario->idusuario ?>">
                        <?= $usuario->nombres . ' ' . $usuario->apellidos . ' (' . $usuario->cargo . ')' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción del Equipo</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
        </div>
        
        <div class="mb-3">
            <label for="estadoservicio" class="form-label">Estado del Servicio</label>
            <select class="form-select" id="estadoservicio" name="estadoservicio" required>
                <option value="">Seleccionar estado</option>
                <option value="Programado">Programado</option>
                <option value="En Proceso">En Proceso</option>
                <option value="Completado">Completado</option>
                <option value="Pendiente">Pendiente</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Asignar Equipo</button>
        <a href="<?= base_url('equipos/por-servicio/'.$servicio->idserviciocontratado) ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?= $footer ?>