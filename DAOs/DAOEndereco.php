<?php
require_once "../classes/Connection.php";
require_once "../classes/Endereco.php";
class DAOEndereco
{
    public static function add($endereco) #$endereco
    {
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_endereco`(`id`, `cep`, `logradouro`, `complemento`, `idCidade`)
        VALUES ('$endereco->Id','$endereco->Cep', '$endereco->Logradouro', '$endereco->Complemento','$endereco->IdCidade')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        }
        catch (PDOException $ex){
            throw new Exception($ex);
        }
    }
/*************************************************************/
    public static function update ($endereco){
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_endereco` SET `cep`='$endereco->Cep',`logradouro`= '$endereco->Logradouro',`complemento`= '$endereco->Complemento',`idCidade`='$endereco->IdCidade'  WHERE `id` = '$endereco->Id'";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    public static function delete($idEndereco)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_endereco` WHERE id=" . $idEndereco;
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_endereco";
            $objPDO = $conn->query($sql);
            return DAOEndereco::returnEnderecos($objPDO);
        }
        catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }


/*************************************************************/

    public static function getById($idEndereco)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_endereco Where id='$idEndereco'";
            $objPDOStatement = $conn->query($sql);
            return DAOEndereco::returnEnderecos($objPDOStatement)[0];
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    public static function getEnderecosByCidade($idCidade)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_endereco Where idCidade='$idCidade'";
            $objPDOStatement = $conn->query($sql);
            return DAOEndereco::returnEnderecos($objPDOStatement);
        }
        catch (PDOException $ex){
            throw new Exception ($ex);
        }
    }

/*************************************************************/
    private function returnEnderecos($objResult)
    {
        try {
            $enderecos = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($objResult as $linha) {
                        $endereco = DAOEndereco::fillObject($linha);
                        array_push($enderecos, $endereco);
                    }
                }
            }
            else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $endereco = DAOEndereco::fillObject($objResult[$i]);
                        array_push($enderecos, $endereco);
                    }
                }
            }
            return $enderecos;
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }
/*************************************************************/
    private static function fillObject($obj)
    {
        try {
            return new Endereco($obj["cep"], $obj["idCidade"], $obj["logradouro"], $obj["complemento"], $obj["id"]);
        }
        catch(PDOException $ex){
            throw new Exception ($ex);
        }
    }
}
?>

