<?php
class User {
    private $id, $name, $rol;
    public function __construct($datos) {
        $this->id = $datos['id'];
        $this->name = $datos['name'];
        $this->rol = $datos['rol'];
    }
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getRol() {
        return $this->rol;
    }
}
?>
