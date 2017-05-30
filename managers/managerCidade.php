<?php
require_once "../DAOs/DAOCidade.php";
require_once "../managers/managerEstado.php";
require_once "../Erros/Erros.php";
class ManagerCidade
{
/*************************************************************/
    public static function delete($idCidade)
    {
        try
        {
            if(!self::existeId($idCidade)){
                throw new IdNaoExiste('Id de cidade não existe!');
            }
            return DAOCidade::delete($idCidade);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getAll()
    {
        try
        {
            return DAOCidade::getAll();
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getById($idCidade)
    {
        try
        {
            if(!self::existeId($idCidade)){
                throw new IdNaoExiste('Id de cidade não existe!');
            }
            return DAOCidade::getById($idCidade);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getNomeById($idCidade)
    {
        try {
            if(!self::existeId($idCidade)){
                throw new IdNaoExiste('Id de cidade não existe!');
            }
            return DAOCidade::getNomeById($idCidade);
        }
        catch (PDOException $ex) {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getCidadesByEstado($idEstado)
    {
        try {
            if(!managerEstado::existeId($idEstado)){
                throw new IdNaoExiste('Id de estado não existe!');
            }
            return DAOCidade::getCidadesByEstado($idEstado);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function existeId($idCidade){
        try {
            if(DAOCidade::getById($idCidade) != null)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
}
/*************************************************************/
?>