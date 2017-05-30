<?php
require_once "../DAOs/DAOUserRules.php";

class managerUsersRules
{
        public static function retornaIdRota($rota, $url){
            try {
                $arrRota = explode('_', $rota);
                for ($i = 1; $i < count($arrRota); $i++) {
                    if ($arrRota[$i] == 'idEvent' or $arrRota[$i] == 'idUser') {
                        $pos = $i - 1; //[0] = VERBO  e nn me interessa
                    }
                }
                $arrUrl = explode('index.php/', $url);
                $url = $arrUrl[1]; //tudo depois de 'index.php/'
                $arrUrl = explode('/', $url);
                $id = $arrUrl[$pos];
                return $id;
            }
            catch (Exception $ex){
                throw new Exception($ex);
            }
        }
    public static function temPermissao($dados, $rota, $url){
        try{
            if(strpos($rota, 'events')  !== false){
                $eventosEPapeis = $dados->events;
                return self::permissaoParaEvento($rota, $eventosEPapeis, $url);
            }
            elseif(strpos($rota, 'users') !== false){
                $idUsuario = $dados->id;
                return self::permissaoParaUsuario($rota, $idUsuario, $url);
            }
            else{
                return false;
            }

        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

    public static function permissaoParaEvento($rota, $eventosEPapeis, $url){
        try{
            if(strpos($rota, 'idEvent') !== false) {
                $idEvento = self::retornaIdRota($rota, $url);
                $papeisEmEvento = [];
                for ($i = 0; $i < count($eventosEPapeis); $i++) {
                    if ($eventosEPapeis[$i]->IdEvento == $idEvento){
                        $papeisEmEvento = $eventosEPapeis[$i]-> NomePapeis;
                        break 1; //quebra o for // = break;
                    }
                }
                $papeisComPerm = self::papeisQueAcessam($rota);
                if(count(array_intersect($papeisEmEvento, $papeisComPerm))>0){
                    return true;
                }
            }
            elseif($rota == 'GET_events_adm_by_me'){
                for($i=0;$i<count($eventosEPapeis);$i++){
                    if(in_array('Administrador', $eventosEPapeis[$i]-> NomePapeis)) {
                        return true;
                    }
                }
                return false;
            }
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

    public static function permissaoParaUsuario($rota, $idUsuario, $url){
        try{
            if(strpos($rota, 'idUser') !== false) {
                $idRota = self::retornaIdRota($rota, $url);
                if($idRota == $idUsuario){
                    return true;
                }
            }
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }


    public static function rotaPossuiId($rota){
        try{
            $arrRota = explode('_', $rota);
            for ($i = 1; $i < count($arrRota); $i++) {
                if ($arrRota[$i] == 'id') {
                    return true;
                }
            }
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
    public static function papeisQueAcessam($rota){
        try{
            return DAOUserRules::papeisQueAcessam($rota);
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
    public static function getRotasLivres(){
        try{
            return DAOUserRules::getRotasLivres();
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }

}
?>