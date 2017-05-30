<?php

require_once '../classes/Connection.php';
require_once '../classes/Comite.php';

class DAOComite
{
    public static function add($comite){
        try{
            $conn = Connection::Open();
            $sql = "INSERT INTO tb_comite (idTipo, idEvento, idMembro)
            VALUES ('$comite->IdTipo', '$comite->IdEvento', '$comite->IdMembro')";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getComite($comite){
        try{
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_comite WHERE idEvento='$comite->IdEvento' AND idMembro='$comite->IdMembro'
            AND idTipo='$comite->IdTipo'";
            $objPDO = $conn->query($sql);
            return self::returnObj($objPDO);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteComite($comite){
        try{
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_comite WHERE idEvento='$comite->IdEvento' AND 
            idMembro='$comite->IdMembro' AND idTipo='$comite->IdTipo'";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteAllFromEvento($idEvento){
        try{
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_comite WHERE idEvento='$idEvento'";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private static function returnObj($objPDO){
        try{
            $objetos = array();
            if($objPDO instanceof PDOStatement){
                if($objPDO->rowCount()==0){
                    return null;
                }
                else{
                    foreach($objPDO as $linha){
                        $obj = self::fillObj($linha);
                        array_push($objetos, $obj);
                    }
                }
            }
            elseif(is_array($objPDO)){
                for($i=0;$i < count($objPDO);$i++){
                    $obj = self::fillObj($objPDO[$i]);
                    array_push($objetos, $obj);
                }
            }
            return $objetos;
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private static function fillObj($obj){
        $comite = new Comite($obj['idTipo'], $obj['idMembro'], $obj['idEvento']);
        return $comite;
    }
/*************************************************************/
/*************************************************************/
/*************************************************************/

}
#DAOComite::add(new Comite(2,1,1));
#print_r(DAOComite::getComite(new Comite(2,1,1)));
#DAOComite::deleteComite(new Comite(2,1,1));