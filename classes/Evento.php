<?php
require_once "Endereco.php";
class Evento
{
    public $Id;
    public $Nome;
    public $Sigla;
    public $Descricao;
    public $IdEndereco;
    public $DtInicio;
    public $DtFinal;

    public function __construct($Nome, $IdEndereco, $DtInicio, $DtFinal, $Sigla = null, $Descricao = null, $Id = null)
    {
        $this->Nome = $Nome;
        $this->IdEndereco = $IdEndereco;
        $this->DtInicio = $DtInicio;
        $this->DtFinal = $DtFinal;
        $this->Sigla = $Sigla;
        $this->Descricao = $Descricao;
        $this->Id = $Id;
    }
    function __toString()
    {
        return "Nome: " . $this->Nome . ", Endereço: " . $this->IdEndereco; #Pegar endereco depois
    }


}