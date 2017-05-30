<?php
require_once '../managers/managerEstado.php';
class Cidade
{
    public $Id;
    public $Nome;
    public $idEstado;
    public function __construct($nome, $idEstado, $id = null)
    {
        $this->Nome = $nome;
        $this->idEstado = $idEstado;
        $this->Id = $id;
    }

    public function __toString()
    {
        return "Nome: " . $this->Nome . ", Estado: " . managerEstado::getNomeById($this->idEstado);
    }
}

?>