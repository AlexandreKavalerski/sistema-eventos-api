<?php
class Estado
{
    public $Id;
    public $Nome;
    public $UF;

    public function __construct($Nome, $Uf, $Id = null)
    {
        $this->Nome = $Nome;
        $this->UF = $Uf;
        $this->Id = $Id;

    }
    public function __toString()
    {
        return "Nome: " . $this->Nome . ", UF: " . $this->UF;
    }
}