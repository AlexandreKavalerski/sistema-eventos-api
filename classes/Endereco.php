<?php
require_once "../managers/managerCidade.php";
class Endereco
{
    public $Id;
    public $Cep;
    public $Logradouro;
    public $Complemento;
    public $IdCidade;
    public function __construct($cep, $idCidade, $logradouro = null, $complemento = null, $id = null)
    {
        $this->Cep = $cep;
        $this->IdCidade = $idCidade;
        $this->Logradouro = $logradouro;
        $this->Complemento = $complemento;
        $this->Id = $id;
    }
    function __toString()
    {
            return "CEP: " . $this -> Cep . ", Logradouro: " . $this -> Logradouro . ", Complemento: " . $this -> Complemento . ", Cidade: " . managerCidade::getNomeById($this-> IdCidade);
    }


}
?>