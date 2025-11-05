<?php

namespace Model;

class Almacenamiento extends ActiveRecord {

    protected static $tabla = 'almacenamiento';
    protected static $columnasDB = ['id', 'archivo', 'peso', 'usuario_id'];

    public $id;
    public $archivo;
    public $peso;
    public $usuario_id;
    public $created_at;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->archivo = $args['archivo'] ?? null;
        $this->peso = $args['peso'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->created_at = $args['created_at'] ?? null;
    }
}