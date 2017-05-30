<?php
require_once "../classes/Papel.php";
require_once "../DAOs/DAOPapel.php";
require_once "../Erros/Erros.php";
class managerPapel
{
/*************************************************************/

    public static function getAll()
    {
        try
        {
            return DAOPapel::getAll();
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/

    public static function getById($idPapel)
    {
        try
        {
            if(!self::existeId($idPapel))
                throw new IdNaoExiste('Id de Papel não existe!');
            return DAOPapel::getById($idPapel);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/

    public static function existeId($idPapel){
        try{
            if(DAOPapel::getById($idPapel) != null)
                return true;
            return false;
        }
        catch(Exception $ex){
            throw new Exception($ex);
        }
    }
/*************************************************************/
/*************************************************************/
    public static function delete($idPapel)
    {
        try
        {
            if(!self::existeId($idPapel))
                throw new IdNaoExiste('Id de Papel não existe!');
            return DAOPapel::delete($idPapel);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
}
?>