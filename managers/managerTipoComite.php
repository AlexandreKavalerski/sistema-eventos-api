<?php

class managerTipoComite
{
    public static function getIdByTipo($tipo){
        if ($tipo == 'Organizacional')
            return 1;
        else if($tipo == 'Científico' or $tipo == 'Cientifico')
            return 2;
        return false;
    }
}