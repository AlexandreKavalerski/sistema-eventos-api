<?php
require_once "../classes/Papel.php";
require_once "../classes/Connection.php";
class DAOPapel
{
    public static function add($papel)
    {
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_papel`(id,`nome`,`descricao`)
               VALUES ('$papel->Id', '$papel->Nome', '$papel->Descricao')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function update($papel)
    {
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_papel` SET `nome`='$papel->Nome', `descricao`='$papel->Descricao' WHERE id=$papel->Id";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    public static function delete($idPapel){
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_papel` WHERE id=" . $idPapel;
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papel";
            $objPDO = $conn->query($sql);
            return DAOPapel::returnPapel($objPDO);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getById($idPapel)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_papel WHERE id=" . $idPapel;
            $objPDOStatement = $conn->query($sql);
            return DAOPapel::returnPapel($objPDOStatement)[0];
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    private function returnPapel($objResult)
    {
        try {
            $papeis = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                } else {
                    foreach ($objResult as $linha) {
                        $papel = DAOPapel::fillObject($linha);
                        array_push($papeis, $papel);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $papel = DAOPapel::fillObject($objResult[$i]);
                        array_push($papeis, $papel);
                    }
                }
            }
            return $papeis;
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    private static function fillObject($obj)
    {
        try {
            return new Papel($obj["nome"], $obj["descricao"], $obj["id"]);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
}
