<?php
class Papel
{
    public $Id;
    public $Nome;
    public $Descricao;

    public function __construct( $Nome, $Descricao, $IdPapel=null)
    {
        $this->Id = $IdPapel;
        $this->Nome = $Nome;
        $this->Descricao = $Descricao;
    }


}