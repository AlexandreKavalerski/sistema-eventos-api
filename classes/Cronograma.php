<?php
require_once '../managers/managerEvento.php';
class Cronograma
{
    public $DataLimiteInscricao;
    public $DataLimiteSubmissao;
    public $DataLimiteRevisao;
    public $IdEvento;

    public function __construct($DataLimiteInscricao, $DataLimiteSubmissao, $DataLimiteRevisao, $IdEvento)
    {
        $this->DataLimiteInscricao = $DataLimiteInscricao;
        $this->DataLimiteSubmissao = $DataLimiteSubmissao;
        $this->DataLimiteRevisao = $DataLimiteRevisao;
        $this->IdEvento = $IdEvento;
    }

    function __toString()
    {
        return 'Data limite de Inscrição: ' . $this->DataLimiteInscricao . ', 
        Data limite de Submissão: ' . $this->DataLimiteSubmissao . ', Data limite de Revisão: ' . $this->DataLimiteRevisao .
        ', Evento: ' . managerEvento::getNomeById($this->IdEvento);
    }
}