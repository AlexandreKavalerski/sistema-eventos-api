<?php
require_once "../classes/PapelUsuarioEvento.php";
require_once "../DAOs/DAOPapelUsuarioEvento.php";
require_once "../managers/managerEvento.php";
require_once "../Erros/Erros.php";

class managerPapelUsuarioEvento
{

    public static function add($idUsuario, $idPapel, $idEvento){
        try {
            if (managerEvento::existeId($idEvento)){
                return DAOPapelUsuarioEvento::add(new PapelUsuarioEvento($idUsuario, $idPapel, $idEvento));
            }
            throw new IdNaoExiste('Id de evento não existe!');
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function existeRelacao($idUsuario, $idPapel, $idEvento){
        try{
            $result = DAOPapelUsuarioEvento::existeRelacao(new PapelUsuarioEvento($idUsuario, $idPapel, $idEvento));
            if(is_array($result)){
                return true;
            }
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getAll(){
        try{
            return DAOPapelUsuarioEvento::getAll();
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getByEvento($idEvento){
        try {
            if(managerEvento::existeId($idEvento))
                return DAOPapelUsuarioEvento::getByEvento($idEvento);
            else{
                throw new IdNaoExiste('Evento não existe');
            }
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getEventosEPapeis($idUsuario){
        try{
            return DAOPapelUsuarioEvento::getEventosEPapeis($idUsuario);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getPapeisUsuEmEvento($idUsuario, $idEvento){
        //Pega todos os papeis de determinado usuario em determinado evento
        try {
            if (managerEvento::existeId($idEvento)) {
                if (managerUsuario::existeId($idUsuario)){
                    return DAOPapelUsuarioEvento::getPapeisUsuEmEvento($idUsuario, $idEvento);
                }
                else
                    throw new IdNaoExiste('Id de Usuario não existe!');
            }
            else
                throw new IdNaoExiste('Id de Evento não existe!');
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function isAdministradorDeEvento($idUsuario){
        try{
            return DAOPapelUsuarioEvento::isAdministradorDeEvento($idUsuario) ? true:false;
            /*if(DAOPapelUsuarioEvento::isAdministradorDeEvento($idUsuario))
                return true;
            else
                return false;*/
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
    public static function deleteRelacao($idUsuario, $idEvento, $idPapel){
        try {
            return DAOPapelUsuarioEvento::deleteRelacao(new PapelUsuarioEvento($idUsuario, $idPapel, $idEvento));
        }catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
}
?>
