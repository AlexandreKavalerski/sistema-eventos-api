<?php
class EventosUsuario
{
public $IdEvento;
public $NomeEvento;
public $NomePapeis = array();

    public function __construct($IdEvento, $NomeEvento, array $NomePapeis)
    {
        $this->IdEvento = $IdEvento;
        $this->NomeEvento = $NomeEvento;
        $this->NomePapeis = $NomePapeis;
    }


}