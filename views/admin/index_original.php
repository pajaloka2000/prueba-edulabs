<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-tachometer-alt"></i>
            Panel de Administración
        </h1>
        <p>Gestiona productos, categorías, subcategorías y usuarios del sistema</p>
        <div class="admin-info">
            <span class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?>
            </span>
            <div class="admin-actions">
                <a href="/admin/productos/crear" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Nuevo Producto
                </a>
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
            <div class="stat-icon productos">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($productos); ?></h3>
                <p>Productos Totales</p>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon categorias">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($categorias); ?></h3>
                <p>Categorías</p>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon subcategorias">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($subcategorias); ?></h3>
                <p>Subcategorías</p>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon usuarios">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($usuarios); ?></h3>
                <p>Usuarios</p>
            </div>
        </div>
    </div>

    <!-- Sección de Productos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Productos</h2>
            <a href="/admin/productos/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>

        <div class="table-container">
            <?php if (!empty($productos)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto->id; ?></td>
                                <td><?php echo htmlspecialchars($producto->nombre); ?></td>
                                <td>
                                    <?php 
                                    // Buscar la categoría correspondiente
                                    $categoria_nombre = 'N/A';
                                    foreach ($categorias as $categoria) {
                                        if ($categoria->id == $producto->categoria_id) {
                                            $categoria_nombre = htmlspecialchars($categoria->nombre);
                                            break;
                                        }
                                    }
                                    echo $categoria_nombre;
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    // Buscar la subcategoría correspondiente
                                    $subcategoria_nombre = 'Sin subcategoría';
                                    if ($producto->subcategoria_id) {
                                        foreach ($subcategorias as $subcategoria) {
                                            if ($subcategoria->id == $producto->subcategoria_id) {
                                                $subcategoria_nombre = htmlspecialchars($subcategoria->nombre);
                                                break;
                                            }
                                        }
                                    }
                                    echo '<span class="subcategoria">' . $subcategoria_nombre . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <span class="estado <?php echo $producto->estado; ?>">
                                        <?php echo ucfirst($producto->estado); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($producto->created_at ?? 'now')); ?></td>
                                <td class="acciones">
                                    <a href="/admin/productos/editar?id=<?php echo $producto->id; ?>" 
                                       class="btn btn-sm btn-warning tooltip">
                                        <i class="fas fa-edit"></i> Editar
                                        <span class="tooltip-text">Editar producto</span>
                                    </a>
                                    <form method="POST" action="/admin/productos/eliminar" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                        <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger tooltip">
                                            <i class="fas fa-trash"></i> Eliminar
                                            <span class="tooltip-text">Eliminar producto</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No hay productos registrados</p>
                    <a href="/admin/productos/crear" class="btn btn-primary">Crear primer producto</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sección de Categorías -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-tags"></i> Categorías</h2>
            <a href="/admin/categorias/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Categoría
            </a>
        </div>

        <div class="table-container">
            <?php if (!empty($categorias)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Subcategorías</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?php echo $categoria->id; ?></td>
                                <td><?php echo htmlspecialchars($categoria->nombre); ?></td>
                                <td>
                                    <span class="estado <?php echo $categoria->estado; ?>">
                                        <?php echo ucfirst($categoria->estado); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $subcats_count = 0;
                                    foreach ($subcategorias as $sub) {
                                        if ($sub->categoria_id == $categoria->id) {
                                            $subcats_count++;
                                        }
                                    }
                                    echo $subcats_count;
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $productos_count = 0;
                                    foreach ($productos as $producto) {
                                        if ($producto->categoria_id == $categoria->id) {
                                            $productos_count++;
                                        }
                                    }
                                    echo $productos_count;
                                    ?>
                                </td>
                                <td class="acciones">
                                    <a href="/admin/categorias/editar?id=<?php echo $categoria->id; ?>" 
                                       class="btn btn-sm btn-warning tooltip">
                                        <i class="fas fa-edit"></i> Editar
                                        <span class="tooltip-text">Editar categoría</span>
                                    </a>
                                    <form method="POST" action="/admin/categorias/estado" style="display: inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $categoria->id; ?>">
                                        <input type="hidden" name="estado" value="<?php echo $categoria->estado === 'activa' ? 'inactiva' : 'activa'; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $categoria->estado === 'activa' ? 'btn-danger' : 'btn-success'; ?> tooltip">
                                            <i class="fas fa-<?php echo $categoria->estado === 'activa' ? 'eye-slash' : 'eye'; ?>"></i>
                                            <?php echo $categoria->estado === 'activa' ? 'Desactivar' : 'Activar'; ?>
                                            <span class="tooltip-text"><?php echo $categoria->estado === 'activa' ? 'Desactivar categoría' : 'Activar categoría'; ?></span>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/categorias/eliminar" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta categoría? Se eliminarán también sus subcategorías y productos asociados.');">
                                        <input type="hidden" name="id" value="<?php echo $categoria->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger tooltip">
                                            <i class="fas fa-trash"></i> Eliminar
                                            <span class="tooltip-text">Eliminar categoría</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <p>No hay categorías registradas</p>
                    <a href="/admin/categorias/crear" class="btn btn-primary">Crear primera categoría</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sección de Subcategorías -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-layer-group"></i> Subcategorías</h2>
            <a href="/admin/subcategorias/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Subcategoría
            </a>
        </div>

        <div class="table-container">
            <?php if (!empty($subcategorias)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría Padre</th>
                            <th>Estado</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subcategorias as $subcategoria): ?>
                            <tr>
                                <td><?php echo $subcategoria->id; ?></td>
                                <td><?php echo htmlspecialchars($subcategoria->nombre); ?></td>
                                <td>
                                    <?php 
                                    $categoria_nombre = 'N/A';
                                    foreach ($categorias as $categoria) {
                                        if ($categoria->id == $subcategoria->categoria_id) {
                                            $categoria_nombre = htmlspecialchars($categoria->nombre);
                                            break;
                                        }
                                    }
                                    echo $categoria_nombre;
                                    ?>
                                </td>
                                <td>
                                    <span class="estado <?php echo $subcategoria->estado; ?>">
                                        <?php echo ucfirst($subcategoria->estado); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo method_exists($subcategoria, 'contarProductos') ? $subcategoria->contarProductos() : '0'; ?>
                                </td>
                                <td class="acciones">
                                    <a href="/admin/subcategorias/editar?id=<?php echo $subcategoria->id; ?>" 
                                       class="btn btn-sm btn-warning tooltip">
                                        <i class="fas fa-edit"></i> Editar
                                        <span class="tooltip-text">Editar subcategoría</span>
                                    </a>
                                    <form method="POST" action="/admin/subcategorias/estado" style="display: inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $subcategoria->id; ?>">
                                        <input type="hidden" name="estado" value="<?php echo $subcategoria->estado === 'activa' ? 'inactiva' : 'activa'; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $subcategoria->estado === 'activa' ? 'btn-danger' : 'btn-success'; ?> tooltip">
                                            <i class="fas fa-<?php echo $subcategoria->estado === 'activa' ? 'eye-slash' : 'eye'; ?>"></i>
                                            <?php echo $subcategoria->estado === 'activa' ? 'Desactivar' : 'Activar'; ?>
                                            <span class="tooltip-text"><?php echo $subcategoria->estado === 'activa' ? 'Desactivar subcategoría' : 'Activar subcategoría'; ?></span>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/subcategorias/eliminar" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta subcategoría? Se eliminarán también los productos asociados.');">
                                        <input type="hidden" name="id" value="<?php echo $subcategoria->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger tooltip">
                                            <i class="fas fa-trash"></i> Eliminar
                                            <span class="tooltip-text">Eliminar subcategoría</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-layer-group"></i>
                    <p>No hay subcategorías registradas</p>
                    <a href="/admin/subcategorias/crear" class="btn btn-primary">Crear primera subcategoría</a>
                </div>
            <?php endif; ?>
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
            <?php if (!empty($usuarios)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario->id; ?></td>
                                <td><?php echo htmlspecialchars($usuario->nombre); ?></td>
                                <td><?php echo htmlspecialchars($usuario->email); ?></td>
                                <td>
                                    <span class="rol <?php echo $usuario->rol; ?>">
                                        <?php echo ucfirst($usuario->rol); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="estado <?php echo $usuario->estado; ?>">
                                        <?php echo ucfirst($usuario->estado); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($usuario->created_at ?? 'now')); ?></td>
                                <td class="acciones">
                                    <a href="/admin/usuarios/editar?id=<?php echo $usuario->id; ?>" 
                                       class="btn btn-sm btn-warning tooltip">
                                        <i class="fas fa-edit"></i> Editar
                                        <span class="tooltip-text">Editar usuario</span>
                                    </a>
                                    <?php if ($usuario->id != $_SESSION['admin']): ?>
                                        <form method="POST" action="/admin/usuarios/eliminar" 
                                              style="display: inline-block;" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger tooltip">
                                                <i class="fas fa-trash"></i> Eliminar
                                                <span class="tooltip-text">Eliminar usuario</span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="btn btn-sm btn-secondary disabled tooltip">
                                            <i class="fas fa-user"></i> Tú
                                            <span class="tooltip-text">No puedes eliminarte</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No hay usuarios registrados</p>
                    <a href="/admin/usuarios/crear" class="btn btn-primary">Crear primer usuario</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>