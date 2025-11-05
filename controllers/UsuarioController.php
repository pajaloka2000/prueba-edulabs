<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Grupo;

class UsuarioController {

    public static function index(Router $router) {
        session_start();
        if (!isset($_SESSION['id'])) {
            header('Location: /login');
            exit;
        }

        $usuario = Usuario::find($_SESSION['id']);
        
        $grupo = null;
        $almacenamiento_total = 10;
        
        if ($usuario->grupo_id) {
            $grupo = Grupo::find($usuario->grupo_id);
            if ($grupo) {
                $almacenamiento_total = $grupo->peso;
            }
        } else {
            $almacenamiento_total = $usuario->peso_max ?? 10;
        }
        
        $almacenamiento_usado = $usuario->usado ?? 0;
        $almacenamiento_disponible = $almacenamiento_total - $almacenamiento_usado;
        $porcentaje_usado = $almacenamiento_total > 0 ? ($almacenamiento_usado / $almacenamiento_total) * 100 : 0;

        $router->render('usuario/index', [
            'title' => 'Panel de Usuario',
            'usuario' => $usuario,
            'grupo' => $grupo,
            'almacenamiento_total' => $almacenamiento_total,
            'almacenamiento_usado' => $almacenamiento_usado,
            'almacenamiento_disponible' => $almacenamiento_disponible,
            'porcentaje_usado' => round($porcentaje_usado, 2)
        ]);
    } 

    public static function subirArchivo(Router $router) {
        session_start();
        if (!isset($_SESSION['id'])) {
            header('Location: /login');
            exit;
        }

        $router->render('usuario/subirArchivo', [
            'title' => 'Subir Archivo'
        ]);
    }

    public static function apiAlmacenamiento() {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        try {
            $usuario = Usuario::find($_SESSION['id']);
            
            $almacenamiento_total = 10;
            if ($usuario->grupo_id) {
                $grupo = Grupo::find($usuario->grupo_id);
                if ($grupo) {
                    $almacenamiento_total = $grupo->peso;
                }
            } else {
                $almacenamiento_total = $usuario->peso_max ?? 10;
            }
            
            $almacenamiento_usado = $usuario->usado ?? 0;
            $almacenamiento_disponible = $almacenamiento_total - $almacenamiento_usado;
            $porcentaje_usado = $almacenamiento_total > 0 ? ($almacenamiento_usado / $almacenamiento_total) * 100 : 0;
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'total' => $almacenamiento_total,
                    'usado' => round($almacenamiento_usado, 2),
                    'disponible' => round($almacenamiento_disponible, 2),
                    'porcentaje' => round($porcentaje_usado, 2)
                ]
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener información de almacenamiento: ' . $e->getMessage()
            ]);
        }
    }

    public static function apiSubirArchivo() {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            $usuario = Usuario::find($_SESSION['id']);
            
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Error al subir el archivo');
            }
            
            $archivo = $_FILES['archivo'];
            
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (\Model\ExtensionProhibida::estaProhibida($extension)) {
                throw new \Exception("Error: El tipo de archivo '.$extension' no está permitido por razones de seguridad");
            }
            
            if ($extension === 'zip') {
                $resultadoAnalisis = self::analizarArchivoZip($archivo['tmp_name']);
                if (!$resultadoAnalisis['valido']) {
                    throw new \Exception($resultadoAnalisis['mensaje']);
                }
            }
            
            $pesoMB = $archivo['size'] / (1024 * 1024);
            
            $almacenamiento_total = 10;
            if ($usuario->grupo_id) {
                $grupo = Grupo::find($usuario->grupo_id);
                if ($grupo) {
                    $almacenamiento_total = $grupo->peso;
                }
            } else {
                $almacenamiento_total = $usuario->peso_max ?? 10;
            }
            
            $almacenamiento_usado = $usuario->usado ?? 0;
            $almacenamiento_disponible = $almacenamiento_total - $almacenamiento_usado;
            
            if ($pesoMB > $almacenamiento_disponible) {
                throw new \Exception('No tienes suficiente espacio disponible');
            }
            
            $uploadDir = __DIR__ . '/../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
            $rutaDestino = $uploadDir . $nombreArchivo;
            
            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new \Exception('Error al guardar el archivo');
            }
            
            $almacenamiento = new \Model\Almacenamiento([
                'archivo' => $archivo['name'],
                'peso' => round($pesoMB, 2),
                'usuario_id' => $_SESSION['id']
            ]);
            
            $almacenamiento->guardar();
            
            $usuario->usado = round($almacenamiento_usado + $pesoMB, 2);
            $usuario->guardar();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Archivo subido correctamente',
                'data' => [
                    'archivo' => $archivo['name'],
                    'peso' => round($pesoMB, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public static function apiArchivos() {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        try {
            $query = "SELECT * FROM almacenamiento WHERE usuario_id = " . $_SESSION['id'] . " ORDER BY id DESC";
            $archivos = \Model\Almacenamiento::consultarSQL($query);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'archivos' => $archivos
                ]
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener archivos: ' . $e->getMessage()
            ]);
        }
    }

    public static function apiEliminarArchivo() {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $archivoId = $data['id'] ?? null;
            
            if (!$archivoId) {
                throw new \Exception('ID de archivo no proporcionado');
            }
            
            $archivo = \Model\Almacenamiento::find($archivoId);
            
            if (!$archivo || $archivo->usuario_id != $_SESSION['id']) {
                throw new \Exception('Archivo no encontrado o no tienes permisos');
            }
            
            // Actualizar espacio usado del usuario
            $usuario = Usuario::find($_SESSION['id']);
            $usuario->usado = round($usuario->usado - $archivo->peso, 2);
            if ($usuario->usado < 0) $usuario->usado = 0;
            $usuario->guardar();
            
            // Eliminar archivo
            $archivo->eliminar();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Archivo eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private static function analizarArchivoZip($rutaArchivo) {
        
        if (!class_exists('ZipArchive')) {
            
            error_log('ADVERTENCIA: ZipArchive no disponible. No se pudo analizar el contenido del ZIP.');
            return [
                'valido' => true,
                'mensaje' => 'ZIP aceptado (análisis omitido - ZipArchive no disponible)'
            ];
        }

        $zip = new \ZipArchive();
        $resultado = $zip->open($rutaArchivo);

        if ($resultado !== true) {
            return [
                'valido' => false,
                'mensaje' => 'Error: No se pudo abrir el archivo ZIP para análisis'
            ];
        }

        $extensionesProhibidas = \Model\ExtensionProhibida::obtenerListaExtensiones();

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $nombreArchivo = $stat['name'];

            if (substr($nombreArchivo, -1) === '/') {
                continue;
            }

            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (in_array($extension, $extensionesProhibidas)) {
                $zip->close();
                return [
                    'valido' => false,
                    'mensaje' => "Error: El archivo '{$nombreArchivo}' dentro del .zip no está permitido (extensión '.{$extension}' prohibida)"
                ];
            }
        }

        $zip->close();

        return [
            'valido' => true,
            'mensaje' => 'Archivo ZIP válido'
        ];
    }
    
}
?>
