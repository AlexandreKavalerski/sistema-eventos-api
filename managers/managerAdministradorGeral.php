<?php
require_once "../DAOs/DAOAdministradorGeral.php";
class managerAdministradorGeral
{
    public static function add($idUsuario)
    {
        try {
            return DAOAdministradorGeral::add($idUsuario);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getAll(){
        try {
            return DAOAdministradorGeral::getAll();
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getById($id){
        try {
            $result = DAOAdministradorGeral::getById(($id));
            if($id != null and $result != null)
                return $result;
            return false;
        }
        catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function existe($id){
        try{
            if(DAOAdministradorGeral::getById($id) != null)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function isAdmGeral($id){
        try{
            $objResult = self::getById($id);
            return ($objResult instanceof AdmGeral)? true :false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function delete($idUsuario){
        try {
            return DAOAdministradorGeral::delete($idUsuario);
        }
        catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
}
?>