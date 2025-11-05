<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Grupo;

class AdminController {
    
    public static function index(Router $router) {
        // Verificar que el usuario esté logueado y sea administrador
        session_start();
        self::verificarAdmin();
        
        // Solo pasamos datos básicos, el resto se carga vía API
        $router->render('admin/index', [
            'title' => 'Panel de Administración'
        ]);
    }

    public static function crearUsuario(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $usuario = new Usuario();
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();
            
            if (empty($alertas)) {
                $usuario->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/usuario/crear', [
            'title' => 'Crear Usuario',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    public static function editarUsuario(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /admin');
        }
        
        $usuario = Usuario::find($id);
        $alertas = [];
        
        if (!$usuario) {
            header('Location: /admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();
            
            if (empty($alertas)) {
                $usuario->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/usuario/editar', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminarUsuario() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id) {
                $usuario = Usuario::find($id);
                if ($usuario) {
                    $usuario->eliminar();
                }
            }
        }
        
        header('Location: /admin');
    }

    public static function crearGrupo(Router $router) {
        // Lógica para crear un grupo
        session_start();
        self::verificarAdmin();

        $grupo = new Grupo();
        $alertas = [];

        // Asignar el admin_id automáticamente
        $grupo->admin_id = $_SESSION['id'];
        $admin_nombre = $_SESSION['nombre'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grupo->sincronizar($_POST);
            $alertas = $grupo->validar();

            if (empty($alertas)) {
                $grupo->guardar();
                header('Location: /admin');
            }
        }

        $router->render('admin/grupo/crear', [
            'title' => 'Crear Grupo',
            'grupo' => $grupo,
            'alertas' => $alertas,
            'admin_nombre' => $admin_nombre
        ]);
    }

    public static function editarGrupo(Router $router) {
        // Lógica para editar un grupo
        session_start();
        self::verificarAdmin();

        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /admin');
        }

        $grupo = Grupo::find($id);
        $alertas = [];

        if (!$grupo) {
            header('Location: /admin');
        }

        // Obtener el nombre del administrador del grupo
        $admin = Usuario::find($grupo->admin_id);
        $admin_nombre = $admin ? $admin->nombre : 'Desconocido';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grupo->sincronizar($_POST);
            $alertas = $grupo->validar();

            if (empty($alertas)) {
                $grupo->guardar();
                header('Location: /admin');
            }
        }

        $router->render('admin/grupo/editar', [
            'title' => 'Editar Grupo',
            'grupo' => $grupo,
            'alertas' => $alertas,
            'admin_nombre' => $admin_nombre
        ]);
    }

    public static function apiDashboard() {
        session_start();
        self::verificarAdmin();

        try {
            // Obtener todos los usuarios y grupos
            $usuarios = Usuario::all();
            $grupos = Grupo::all();
            
            // Contar manualmente para verificar
            $total_usuarios = count($usuarios);
            $total_grupos = count($grupos);

            // Crear un array para almacenar el conteo de usuarios por grupo
            $usuarios_por_grupo = [];
            foreach ($usuarios as $usuario) {
                if (!empty($usuario->grupo_id)) {
                    if (!isset($usuarios_por_grupo[$usuario->grupo_id])) {
                        $usuarios_por_grupo[$usuario->grupo_id] = 0;
                    }
                    $usuarios_por_grupo[$usuario->grupo_id]++;
                }
            }
            
            // Preparar la respuesta con conteos y detalles
            $responseData = [
                'success' => true,
                'data' => [
                    'estadisticas' => [
                        'total_usuarios' => $total_usuarios,
                        'total_grupos' => $total_grupos
                    ],
                    'usuarios' => array_map(function($usuario) use ($grupos) {
                        // Obtener información del grupo si el usuario tiene uno asignado
                        $grupo_info = null;
                        if (!empty($usuario->grupo_id)) {
                            foreach ($grupos as $grupo) {
                                if ($grupo->id == $usuario->grupo_id) {
                                    $grupo_info = $grupo;
                                    break;
                                }
                            }
                        }

                        return [
                            'id' => $usuario->id,
                            'nombre' => $usuario->nombre,
                            'email' => $usuario->email,
                            'rol' => $usuario->rol,
                            'estado' => $usuario->estado,
                            'grupo_id' => $usuario->grupo_id,
                            'grupo_nombre' => $grupo_info ? $grupo_info->nombre : 'Sin grupo'
                        ];
                    }, $usuarios),
                    'grupos' => array_map(function($grupo) use ($usuarios_por_grupo, $usuarios) {
                        // Buscar el nombre del administrador
                        $admin = Usuario::find($grupo->admin_id);
                        
                        return [
                            'id' => $grupo->id,
                            'nombre' => $grupo->nombre,
                            'admin_id' => $grupo->admin_id,
                            'admin_nombre' => $admin ? $admin->nombre : 'Desconocido',
                            'peso' => $grupo->peso,
                            'total_usuarios' => $usuarios_por_grupo[$grupo->id] ?? 0
                        ];
                    }, $grupos)
                ]
            ];
            
            header('Content-Type: application/json');
            echo json_encode($responseData);
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del dashboard: ' . $e->getMessage()
            ]);
        }
    }

    public static function gestionarExtensiones(Router $router) {
        session_start();
        self::verificarAdmin();

        $extensiones = \Model\ExtensionProhibida::all();

        $router->render('admin/extensiones/index', [
            'title' => 'Gestionar Extensiones Prohibidas',
            'extensiones' => $extensiones
        ]);
    }

    public static function agregarExtension(Router $router) {
        session_start();
        self::verificarAdmin();

        $extension = new \Model\ExtensionProhibida();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $extension->sincronizar($_POST);
            $alertas = $extension->validar();

            if (empty($alertas)) {
                $extension->guardar();
                header('Location: /admin/extensiones');
            }
        }

        $router->render('admin/extensiones/crear', [
            'title' => 'Agregar Extensión Prohibida',
            'extension' => $extension,
            'alertas' => $alertas
        ]);
    }

    public static function eliminarExtension() {
        session_start();
        self::verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $extension = \Model\ExtensionProhibida::find($id);
                if ($extension) {
                    $extension->eliminar();
                }
            }
        }

        header('Location: /admin/extensiones');
    }

    public static function apiExtensionesProhibidas() {
        session_start();
        
        try {
            $extensiones = \Model\ExtensionProhibida::obtenerListaExtensiones();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'extensiones' => $extensiones
                ]
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener extensiones: ' . $e->getMessage()
            ]);
        }
    }

    private static function verificarAdmin() {
        if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: /login');
            exit;
        }
    }
}
?>