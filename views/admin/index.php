<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-tachometer-alt"></i>
            Panel de Administración
        </h1>
        <p>Gestiona Grupos y usuarios del sistema</p>
        <div class="admin-info">
            <span class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?>
            </span>
            <div class="admin-actions">
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="admin-stats-container">
        <div class="admin-stat-card">
            <div class="stat-icon grupos">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Grupos Totales</p>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon usuarios">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Usuarios</p>
            </div>
        </div>
    </div>

    <!-- Acceso Rápido -->
    <div class="quick-access-container">
        <a href="/admin/extensiones" class="quick-access-card">
            <div class="quick-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="quick-info">
                <h3>Gestionar Extensiones</h3>
                <p>Administrar tipos de archivo prohibidos</p>
            </div>
        </a>
    </div>

    <!-- Sección de Grupos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Grupos</h2>
            <a href="/admin/grupos/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Grupo
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Administrador</th>
                        <th>Peso por archivo</th>
                        <th>Usuarios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando Grupos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>


    <!-- Sección de Usuarios -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Usuarios</h2>
            <a href="/admin/usuarios/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Grupo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando usuarios...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
