<?php
require_once "../classes/Estado.php";
require_once "../classes/Connection.php";

class DAOEstado
{
    public static function add($estado)
    {
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_estado`(`id`,`nome`,`uf`)
               VALUES ('$estado->Id', '$estado->Nome', '$estado->UF')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    public static function update($estado)
    {
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_estado` SET `nome`='$estado->Nome', `uf`='$estado->UF' WHERE `id`=$estado->Id";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function delete($idEstado){
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_estado` WHERE id=" . $idEstado;
            return $conn->exec($sql);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }


/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_estado";
            $objPDO = $conn->query($sql);
            return DAOEstado::returnEstados($objPDO);
        }
        catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/ 
    public static function getById($idEstado)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_estado WHERE id=" . $idEstado;
            $objPDOStatement = $conn->query($sql);
            return DAOEstado::returnEstados($objPDOStatement)[0];
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    public static function getNomeById($idEstado)
    {
        try{
            $conn = Connection::Open();
            $sql = "SELECT nome FROM tb_estado WHERE id='$idEstado'";
            $objPDOStatement = $conn->query($sql);
            return self::returnNome($objPDOStatement);
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    private function returnNome($obj){
        try{
            if ($obj instanceof PDOStatement) {
                if ($obj->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($obj as $linha) {
                        return $linha['nome'];
                    }
                }
            }
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }

/*************************************************************/ 
    private function returnEstados($objResult)
    {
        try {
            $estados = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($objResult as $linha) {
                        $estado = DAOEstado::fillObject($linha);
                        array_push($estados, $estado);
                    }
                }
            }
            else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $estado = DAOEstado::fillObject($objResult[$i]);
                        array_push($estados, $estado);
                    }
                }
            }
            return $estados;
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/ 
    private static function fillObject($obj)
    {
        try {
            return new Estado($obj["nome"], $obj["uf"], $obj["id"]);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }
}
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
?>

