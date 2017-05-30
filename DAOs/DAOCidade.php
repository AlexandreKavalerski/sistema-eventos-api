<?php
require_once "../classes/Connection.php";
require_once "../classes/Cidade.php";
class DAOCidade
{
    public static function add($cidade)
    {
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_cidade`(`id`,`nome`,`idEstado`)
               VALUES ('$cidade->Id', '$cidade->Nome', '$cidade->idEstado')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function update ($cidade){
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_cidade` SET `nome`='$cidade->Nome',`idEstado`= $cidade->idEstado  WHERE `id` = $cidade->Id";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }


/*************************************************************/
    public static function delete($idCidade)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_cidade` WHERE id=" . $idCidade;
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_cidade";
            $objPDO = $conn->query($sql);
            return DAOCidade::returnCidades($objPDO);
        }
        catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getById($idCidade)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_cidade WHERE id=" . $idCidade;
            $objPDOStatement = $conn->query($sql);
            return DAOCidade::returnCidades($objPDOStatement)[0];
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function getNomeById($idCidade)
    {
        try{
            $conn = Connection::Open();
            $sql = "SELECT nome FROM tb_cidade WHERE id='$idCidade'";
            $objPDOStatement = $conn->query($sql);
            return self::returnNome($objPDOStatement);
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/

    public static function getCidadesByEstado($idEstado)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_cidade Where idEstado=" . $idEstado;
            $objPDOStatement = $conn->query($sql);
            return self::returnCidades($objPDOStatement);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
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

    private function returnCidades($objResult)
    {
        try {
            $cidades = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                } else {
                    foreach ($objResult as $linha) {
                        $cidade = DAOCidade::fillObject($linha);
                        array_push($cidades, $cidade);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $cidade = DAOCidade::fillObject($objResult[$i]);
                        array_push($cidades, $cidade);
                    }
                }
            }
            return $cidades;
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/

    private static function fillObject($obj)
    {
        try {
            return new Cidade($obj["nome"], $obj["idEstado"], $obj["id"]);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }

    }
}
?>