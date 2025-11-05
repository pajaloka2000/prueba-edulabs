<div class="formulario-container">
    <div class="breadcrumb">
        <a href="/admin"><i class="fas fa-tachometer-alt"></i> Panel</a>
        <span class="separator">/</span>
        <a href="/admin/extensiones">Extensiones Prohibidas</a>
        <span class="separator">/</span>
        <span class="current">Nueva Extensión</span>
    </div>

    <h2><i class="fas fa-plus"></i> Agregar Extensión Prohibida</h2>
    
    <a href="/admin/extensiones" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver a Extensiones
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
            <legend><i class="fas fa-shield-alt"></i> Información de la Extensión</legend>

            <div class="campo">
                <label for="extension"><i class="fas fa-file"></i> Extensión (sin el punto):</label>
                <input type="text" id="extension" name="extension" 
                       placeholder="Ejemplo: exe, bat, php" 
                       value="<?php echo s($extension->extension); ?>" 
                       pattern="[a-zA-Z0-9]{1,10}"
                       required>
                <small>Solo letras y números, máximo 10 caracteres</small>
            </div>

            <div class="campo">
                <label for="descripcion"><i class="fas fa-info-circle"></i> Descripción (opcional):</label>
                <input type="text" id="descripcion" name="descripcion" 
                       placeholder="Ejemplo: Ejecutable de Windows" 
                       value="<?php echo s($extension->descripcion); ?>"
                       maxlength="100">
                <small>Descripción breve del tipo de archivo</small>
            </div>
        </fieldset>

        <div class="warning-box">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Advertencia:</strong>
                <p>Al agregar una extensión a esta lista, los usuarios no podrán subir archivos con esta extensión. Asegúrate de que realmente quieres prohibir este tipo de archivo.</p>
            </div>
        </div>

        <button type="submit" class="boton boton-verde">
            <i class="fas fa-save"></i>
            Agregar Extensión
        </button>
    </form>
</div>

<style>
.warning-box {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1.5rem;
    margin: 2rem 0;
    border-radius: 4px;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.warning-box i {
    color: #f57c00;
    font-size: 2rem;
    flex-shrink: 0;
}

.warning-box strong {
    color: #e65100;
    display: block;
    margin-bottom: 0.5rem;
}

.warning-box p {
    margin: 0;
    color: #795548;
}

.campo small {
    display: block;
    margin-top: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
}
</style>