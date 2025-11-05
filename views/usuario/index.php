<div class="usuario-dashboard">
    <div class="dashboard-header">
        <h1>Panel de Usuario</h1>
        <p>Sube archivos</p>
        <div class="user-info">
            <span class="user-welcome">
                <i class="fas fa-user"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
            </span>
            <div class="user-actions">
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Almacenamiento -->
    <div class="storage-stats-container">
        <div class="storage-card">
            <div class="storage-icon">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="storage-info">
                <h3>Almacenamiento Total</h3>
                <p class="storage-value"><?php echo $almacenamiento_total; ?> MB</p>
                <?php if($grupo): ?>
                    <small>Grupo: <?php echo htmlspecialchars($grupo->nombre); ?></small>
                <?php else: ?>
                    <small>Almacenamiento por defecto</small>
                <?php endif; ?>
            </div>
        </div>

        <div class="storage-card">
            <div class="storage-icon usado">
                <i class="fas fa-database"></i>
            </div>
            <div class="storage-info">
                <h3>Espacio Usado</h3>
                <p class="storage-value"><?php echo $almacenamiento_usado; ?> MB</p>
                <small><?php echo $porcentaje_usado; ?>% utilizado</small>
            </div>
        </div>

        <div class="storage-card">
            <div class="storage-icon disponible">
                <i class="fas fa-cloud"></i>
            </div>
            <div class="storage-info">
                <h3>Espacio Disponible</h3>
                <p class="storage-value"><?php echo $almacenamiento_disponible; ?> MB</p>
                <small>Libre para usar</small>
            </div>
        </div>
    </div>

    <!-- Barra de progreso de almacenamiento -->
    <div class="storage-progress-container">
        <div class="progress-header">
            <span>Uso del Almacenamiento</span>
            <span><?php echo $porcentaje_usado; ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo min($porcentaje_usado, 100); ?>%"></div>
        </div>
    </div>

    <section class="dashboard-section">
        <div class="section-header">
                <h2><i class="fas fa-file"></i> Mis Archivos</h2>
                <a href="/usuario/archivo/subir" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Subir archivo
                </a>
        </div>

        <div id="archivos-container" class="archivos-grid">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Cargando archivos...
            </div>
        </div>

        <!-- Mensaje cuando no hay archivos -->
        <div id="no-archivos" class="no-archivos" style="display: none;">
            <i class="fas fa-folder-open"></i>
            <p>No tienes archivos subidos</p>
            <a href="/usuario/archivo/subir" class="btn btn-primary">Subir tu primer archivo</a>
        </div>
    </section>
</div>
