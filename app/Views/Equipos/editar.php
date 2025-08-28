<?= $header ?>
<div class="container">
    <h2><?= $titulo ?></h2>
    
    <form method="post" action="<?= base_url('equipos/actualizar') ?>">
        <input type="hidden" name="idequipo" value="<?= $equipo->idequipo ?>">
        
        <div class="mb-3">
            <label for="idusuario" class="form-label">Usuario/Técnico</label>
            <select class="form-select" id="idusuario" name="idusuario" required>
                <option value="">Seleccionar usuario</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario->idusuario ?>" <?= $usuario->idusuario == $equipo->idusuario ? 'selected' : '' ?>>
                        <?= $usuario->nombres . ' ' . $usuario->apellidos . ' (' . $usuario->cargo . ')' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción del Equipo</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= $equipo->descripcion ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="estadoservicio" class="form-label">Estado del Servicio</label>
            <select class="form-select" id="estadoservicio" name="estadoservicio" required>
                <option value="Programado" <?= $equipo->estadoservicio == 'Programado' ? 'selected' : '' ?>>Programado</option>
                <option value="En Proceso" <?= $equipo->estadoservicio == 'En Proceso' ? 'selected' : '' ?>>En Proceso</option>
                <option value="Completado" <?= $equipo->estadoservicio == 'Completado' ? 'selected' : '' ?>>Completado</option>
                <option value="Pendiente" <?= $equipo->estadoservicio == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="<?= base_url('equipos') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?= $footer ?>