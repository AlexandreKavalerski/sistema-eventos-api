<?php
require_once "../DAOs/DAOEstado.php";

class managerEstado
{
    public static function delete($id)
    {
        try{
            if(!self::existeId($id)){
                throw new IdNaoExiste('Id de Estado não existe!');
            }
            return DAOEstado::delete($id);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/

    public static function getAll()
    {
        try
        {
            return DAOEstado::getAll();
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function existeId($idEstado){
        try{
            if(DAOEstado::getById($idEstado) != null)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getById($idEstado)
    {
        try
        {
            if(!self::existeId($idEstado)){
                throw new IdNaoExiste('Id de estado não existe!');
            }
            return DAOEstado::getById($idEstado);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function getNomeById($idEstado)
    {
        try {
            if(!self::existeId($idEstado)){
                throw new IdNaoExiste('Id de Estado não existe!');
            }
            return DAOEstado::getNomeById($idEstado);
        }
        catch (PDOException $ex) {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    
}
?>


