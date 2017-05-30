<?php
require_once "../classes/Connection.php";
require_once "../classes/AdmGeral.php";
class DAOAdministradorGeral
{
    public static function add($idUsuario){
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_admgeral`(`idUsuario`)
               VALUES ('$idUsuario')";
            return $conn->exec($sql);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }
/*************************************************************/
    public static function getAll(){
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_admgeral";
            $objPDO = $conn->query($sql);
            return self::returnAdministradores($objPDO);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }

    }
/*************************************************************/
    public static function getById($id){
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_admgeral WHERE idUsuario='$id'";
            $objPDO = $conn->query($sql);
            return self::returnAdministradores($objPDO)[0];
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }

    }

/*************************************************************/
    public static function delete($idUsuario){
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_admgeral` WHERE idUsuario='.$idUsuario'";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    private static function returnAdministradores($objResult){
        try {
            $administradores = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0)
                    return null;
                else {
                    foreach ($objResult as $linha) {
                        $adm = self::fillObject($linha);
                        array_push($administradores, $adm);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $adm = self::fillObject($objResult[$i]);
                        array_push($administradores, $adm);
                    }
                }
            }
            return $administradores;
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }
/*************************************************************/
    private static function fillObject($obj){
        try {
            return new AdmGeral($obj["idUsuario"]);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }
}
?>