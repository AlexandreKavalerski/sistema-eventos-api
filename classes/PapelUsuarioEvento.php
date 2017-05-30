<?php
class PapelUsuarioEvento
{
    public $IdUsuario;
    public $IdPapel;
    public $IdEvento;

    public function __construct($IdUsuario, $IdPapel, $IdEvento)
    {
        $this->IdUsuario = $IdUsuario;
        $this->IdPapel = $IdPapel;
        $this->IdEvento = $IdEvento;
    }

    function __toString()
    {
        return "idUsuario: " . $this->IdUsuario . ", idPapel: " . $this->IdPapel . ", idEvento: " . $this->IdEvento;
    }


}