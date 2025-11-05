<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AdminController;
use Controllers\LoginController;
use Controllers\UsuarioController;

$router = new Router();

// Rutas de autenticación
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//Vista de administración
$router->get('/admin', [AdminController::class, 'index']);


// Rutas para CRUD de Usuarios
$router->get('/admin/usuarios/crear', [AdminController::class, 'crearUsuario']);
$router->post('/admin/usuarios/crear', [AdminController::class, 'crearUsuario']);
$router->get('/admin/usuarios/editar', [AdminController::class, 'editarUsuario']);
$router->post('/admin/usuarios/editar', [AdminController::class, 'editarUsuario']);
$router->post('/admin/usuarios/eliminar', [AdminController::class, 'eliminarUsuario']);

// Rutas para CRUD de Grupos
$router->get('/admin/grupos/crear', [AdminController::class, 'crearGrupo']);
$router->post('/admin/grupos/crear', [AdminController::class, 'crearGrupo']);
$router->get('/admin/grupos/editar', [AdminController::class, 'editarGrupo']);
$router->post('/admin/grupos/editar', [AdminController::class, 'editarGrupo']);
$router->post('/admin/grupos/eliminar', [AdminController::class, 'eliminarGrupo']);


// Gestión de extensiones prohibidas
$router->get('/admin/extensiones', [AdminController::class, 'gestionarExtensiones']);
$router->get('/admin/extensiones/crear', [AdminController::class, 'agregarExtension']);
$router->post('/admin/extensiones/crear', [AdminController::class, 'agregarExtension']);
$router->post('/admin/extensiones/eliminar', [AdminController::class, 'eliminarExtension']);

// Rutas API admin
$router->get('/api/admin/dashboard', [AdminController::class, 'apiDashboard']);
$router->get('/api/extensiones-prohibidas', [AdminController::class, 'apiExtensionesProhibidas']);

// Vista de Usuario
$router->get('/usuario', [UsuarioController::class, 'index']);

// Subir archivo
$router->get('/usuario/archivo/subir', [UsuarioController::class, 'subirArchivo']);
$router->post('/usuario/archivo/subir', [UsuarioController::class, 'subirArchivo']);

// Rustas API usuario
$router->get('/api/usuario/almacenamiento', [UsuarioController::class, 'apiAlmacenamiento']);
$router->post('/api/usuario/subir-archivo', [UsuarioController::class, 'apiSubirArchivo']);
$router->get('/api/usuario/archivos', [UsuarioController::class, 'apiArchivos']);
$router->post('/api/usuario/eliminar-archivo', [UsuarioController::class, 'apiEliminarArchivo']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();