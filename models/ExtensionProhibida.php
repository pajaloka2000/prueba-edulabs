<?php

namespace Model;

class ExtensionProhibida extends ActiveRecord {
    protected static $tabla = 'extensiones_prohibidas';
    protected static $columnasDB = ['id', 'extension', 'descripcion'];

    public $id;
    public $extension;
    public $descripcion;
    public $created_at;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->extension = $args['extension'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->created_at = $args['created_at'] ?? null;
    }

    public static function obtenerExtensiones() {
        return self::all();
    }

    public static function estaProhibida($extension) {
        $extension = strtolower(trim($extension));
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE LOWER(extension) = '" . self::$db->escape_string($extension) . "'";
        $resultado = self::consultarSQL($query);
        return $resultado[0]->total > 0;
    }

    public static function obtenerListaExtensiones() {
        $extensiones = self::all();
        return array_map(function($ext) {
            return strtolower($ext->extension);
        }, $extensiones);
    }

    public function validar() {
        if(!$this->extension) {
            self::$alertas['error'][] = 'La extensión es obligatoria';
        }
        
        if($this->extension && strlen($this->extension) > 10) {
            self::$alertas['error'][] = 'La extensión no puede tener más de 10 caracteres';
        }
        
        return self::$alertas;
    }
}
