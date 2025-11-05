<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-shield-alt"></i>
            Gestión de Extensiones Prohibidas
        </h1>
        <p>Administra los tipos de archivo que no se pueden subir al sistema</p>
    </div>

    <div class="breadcrumb">
        <a href="/admin"><i class="fas fa-tachometer-alt"></i> Panel</a>
        <span class="separator">/</span>
        <span class="current">Extensiones Prohibidas</span>
    </div>

    <!-- Sección de Extensiones -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-ban"></i> Extensiones Prohibidas</h2>
            <a href="/admin/extensiones/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Extensión
            </a>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Las extensiones listadas aquí no podrán ser subidas por los usuarios del sistema por razones de seguridad.</p>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Extensión</th>
                        <th>Descripción</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($extensiones)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                No hay extensiones prohibidas registradas
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($extensiones as $extension): ?>
                            <tr>
                                <td><?php echo $extension->id; ?></td>
                                <td>
                                    <span class="extension-badge">
                                        <i class="fas fa-file"></i>
                                        .<?php echo htmlspecialchars($extension->extension); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($extension->descripcion ?? 'N/A'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($extension->created_at)); ?></td>
                                <td class="acciones">
                                    <form method="POST" action="/admin/extensiones/eliminar" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $extension->id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de eliminar la extensión .<?php echo $extension->extension; ?>?')">
                                            <i class="fas fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="section-actions">
            <a href="/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver al Panel
            </a>
        </div>
    </section>
</div>

<style>
.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.info-box i {
    color: #2196f3;
    font-size: 2rem;
}

.info-box p {
    margin: 0;
    color: #1565c0;
}

.extension-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffebee;
    color: #c62828;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    font-weight: 600;
    font-family: monospace;
}

.extension-badge i {
    font-size: 1rem;
}
</style>