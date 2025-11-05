<?php

namespace Model;

class Grupo extends ActiveRecord {
    protected static $tabla = 'grupos';
    protected static $columnasDB = ['id', 'nombre', 'admin_id', 'peso'];

    public $id;
    public $nombre;
    public $admin_id;
    public $peso;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->admin_id = $args['admin_id'] ?? null;
        $this->peso = $args['peso'] ?? null;
    }

    public static function count() {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado[0]->total ?? 0;
    }

    public function contarUsuarios() {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE grupo_id = " . self::$db->escape_string($this->id);
        $resultado = self::consultarSQL($query);
        return $resultado[0]->total ?? 0;
    }

    public function UsuariosPorGrupo() {
        $query = "SELECT COUNT(*) as total, grupo_id FROM usuarios GROUP BY grupo_id";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    
}