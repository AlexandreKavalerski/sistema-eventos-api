<?php

class Comite
{
    public $IdTipo;
    public $IdMembro;
    public $IdEvento;
    public function __construct($idTipo, $idMembro, $idEvento)
    {
        $this->IdTipo = $idTipo;
        $this->IdMembro = $idMembro;
        $this->IdEvento = $idEvento;
    }

    function __toString()
    {
        return 'Id do Usuario: ' . $this->IdMembro . ', Id do Evento: ' . $this->IdEvento . ', Id do Tipo: ' . $this->IdTipo;
    }


}