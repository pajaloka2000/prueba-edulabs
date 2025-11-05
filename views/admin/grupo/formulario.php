<div class="formulario-container">
    <div class="breadcrumb">
        <a href="/admin"><i class="fas fa-tachometer-alt"></i>Panel</a>
        <span class="separator">/</span>
        <span class="current">
            <?php echo isset($grupo->id) ? 'Editar Grupo' : 'Crear Grupo'; ?>
        </span>
    </div>

    <h2><?php echo isset($grupo->id) ? 'Editar Grupo' : 'Crear Grupo'; ?></h2>
    
    <a href="/admin" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Panel
    </a>

    <?php foreach($alertas as $key => $mensajes): ?>
        <div class="alerta alerta-<?php echo $key; ?>">
            <?php foreach($mensajes as $mensaje): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?php echo $mensaje; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST">
        <fieldset>
            <legend><i class="fas fa-box"></i> Información del grupo</legend>

            <div class="campo">
                <label for="nombre"><i class="fas fa-tag"></i> Nombre del grupo:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del Grupo" 
                       value="<?php echo s($grupo->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="peso"><i class="fas fa-tag"></i> Peso máximo de los archivos del grupo:</label>
                <input type="text" id="peso" name="peso" placeholder="Peso archivos del Grupo" 
                       value="<?php echo s($grupo->peso); ?>" required>
            </div>

            <div class="campo">
                <label><i class="fas fa-user-shield"></i> Administrador del grupo:</label>
                <div class="admin-info-field">
                    <input type="hidden" name="admin_id" value="<?php echo s($grupo->admin_id); ?>">
                    <div class="admin-display">
                        <i class="fas fa-user"></i>
                        <span><?php echo s($admin_nombre); ?></span>
                    </div>
                </div>
            </div>


        </fieldset>

        <button type="submit" class="boton boton-verde">
            <i class="fas fa-save"></i>
            <?php echo isset($grupo->id) ? 'Actualizar Grupo' : 'Crear Grupo'; ?>
        </button>
    </form>
</div>

<style>
    .admin-info-field {
        margin-top: 0.5rem;
    }

    .admin-display {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #495057;
        font-size: 0.9rem;
    }

    .admin-display i {
        color: #6c757d;
        margin-right: 0.75rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {


});
</script>
