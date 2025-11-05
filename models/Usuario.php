<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'rol', 'email', 'password', 'estado', 'grupo_id', 'peso_max', 'usado'];

    public $id;
    public $nombre;
    public $rol;
    public $email;
    public $password;
    public $estado;
    public $grupo_id;
    public $peso_max;
    public $usado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->rol = $args['rol'] ?? 'basico';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->estado = $args['estado'] ?? 'activo';
        $this->grupo_id = $args['grupo_id'] ?? null;
        $this->peso_max = $args['peso_max'] ?? 10;
        $this->usado = $args['usado'] ?? 0;
    }

    public function puedeAcceder() {
        return $this->estado === 'activo';
    }

    public function esAdministrador() {
        return $this->rol === 'administrador';
    }

    public function esBasico() {
        return $this->rol === 'basico';
    }

    public function tienePermisoCRUD($seccion) {
        if ($this->esAdministrador()) {
            return true;
        }

        return false;
    }

    public function tienePermisoConsulta($seccion) {
        return $this->puedeAcceder();
    }

    public function puedeModificarUsuario($usuario_id) {
        if ($this->esAdministrador()) {
            return true;
        }

        return $this->id == $usuario_id;
    }

    public function hashPassword() {
        if ($this->password) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    public function verificarPassword($password) {
        return password_verify($password, $this->password);
    }

    public function guardar() {
        if ($this->password && !password_get_info($this->password)['algo']) {
            $this->hashPassword();
        }
        
        return parent::guardar();
    }

    public static function getRolesDisponibles() {
        return [
            'administrador' => 'Administrador',
            'basico' => 'Básico'
        ];
    }

    public static function getEstadosDisponibles() {
        return [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo'
        ];
    }

    public static function count() {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado[0]->total ?? 0;
    }

    public function validar() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        
        if($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }
        
        if(!$this->password && !$this->id) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        
        if($this->password && strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if(!$this->rol) {
            self::$alertas['error'][] = 'El rol es obligatorio';
        }
        
        if($this->rol && !in_array($this->rol, ['administrador', 'basico'])) {
            self::$alertas['error'][] = 'El rol debe ser administrador o básico';
        }
        
        if(!$this->estado) {
            self::$alertas['error'][] = 'El estado es obligatorio';
        }
        
        if($this->estado && !in_array($this->estado, ['activo', 'inactivo'])) {
            self::$alertas['error'][] = 'El estado debe ser activo o inactivo';
        }
        
        return self::$alertas;
    }
}