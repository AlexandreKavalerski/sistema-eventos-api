<?php
require_once '../classes/Comite.php';
require_once '../DAOs/DAOComite.php';
require_once 'managerUsuario.php';
require_once 'managerEvento.php';
require_once 'managerTipoComite.php';
require_once 'managerUsuario.php';
require_once '../Erros/Erros.php';


class managerComite
{
    public static function add($emailUsuario, $idEvento, $tipoComite){
        try {
            $idTipoComite = managerTipoComite::getIdByTipo($tipoComite);
            $idUsuario = managerUsuario::getIdUsuarioByEmail($emailUsuario);
            if ($idTipoComite) {
                if ($idUsuario) {
                    if (managerEvento::existeId($idEvento)) {
                        $comite = new Comite($idTipoComite, $idUsuario, $idEvento);
                        if(!self::ehMembro($comite)) {
                            return DAOComite::add($comite);
                        }
                        else{
                            throw new JaExiste('O usuário já é membro deste comitê!');
                        }
                    }
                    else {
                        throw new IdNaoExiste('Id de evento não existe!');
                    }
                }
                else {
                    throw new NaoExiste('O email de usuário informado não existe no sistema');
                }
            }
            else {
                throw new PreencIncorreto('O tipo do comitê está incorreto! Deve ser "Cientifico" ou "Organizacional"');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function delete($emailUsuario, $tipoComite, $idEvento){
        try{
            $idMembro = managerUsuario::getIdUsuarioByEmail($emailUsuario);
            $idTipoComite = managerTipoComite::getIdByTipo($tipoComite);
            if(managerEvento::existeId($idEvento)) {
                if ($idMembro) {
                    if ($idTipoComite) {
                        $comite = new Comite($idTipoComite, $idMembro, $idEvento);
                        if(self::ehMembro($comite)) {
                            $result = DAOComite::deleteComite($comite);
                            return $result;
                        }
                        else{
                            throw new NaoExiste('O usuário não é membro deste comitê!');
                        }
                    }
                    else {
                        throw new PreencIncorreto('O tipo do comitê está incorreto. Deve ser "Cientifico" ou "Organizacional"');
                    }
                }
                else {
                    throw new NaoExiste('O email de usuário informado não existe no sistema');
                }
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private static function ehMembro($comite){
        try{
            $result = DAOComite::getComite($comite);
            return $result ? true:false;
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
/*************************************************************/
}