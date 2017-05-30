<?php
require_once '../classes/Cronograma.php';
require_once '../classes/Connection.php';

class DAOCronograma
{
    public static function add($cronograma){
        try{
            $conn = Connection::Open();
            $sql = "INSERT INTO tb_cronograma (dataLimiteInscricao, dataLimiteSubmissao, dataLimiteRevisao, idEvento)
            VALUES ('$cronograma->DataLimiteInscricao', '$cronograma->DataLimiteSubmissao', '$cronograma->DataLimiteRevisao', 
            '$cronograma->IdEvento')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function update($cronograma){
        try{
            $conn = Connection::Open();
            $sql = "UPDATE `tb_cronograma` SET `dataLimiteInscricao`='$cronograma->DataLimiteInscricao', 
            `dataLimiteSubmissao`='$cronograma->DataLimiteSubmissao',
            `dataLimiteRevisao`='$cronograma->DataLimiteRevisao' WHERE idEvento = '$cronograma->IdEvento'";
            return $conn->exec($sql);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteDataInscricao($idEvento){
        $conn = Connection::Open();
        $sql = "UPDATE tb_cronograma SET dataLimiteInscricao = NULL WHERE idEvento='$idEvento'";
        return $conn->exec($sql);
    }
/*************************************************************/
    public static function deleteDataSubmissao($idEvento){
        $conn = Connection::Open();
        $sql = "UPDATE tb_cronograma SET dataLimiteSubmissao=NULL WHERE idEvento='$idEvento'";
        return $conn->exec($sql);
    }
/*************************************************************/
    public static function deleteDataRevisao($idEvento){
        $conn = Connection::Open();
        $sql = "UPDATE tb_cronograma SET dataLimiteRevisao=NULL WHERE idEvento='$idEvento'";
        return $conn->exec($sql);
    }


    public static function deleteCronograma($idEvento){
        $conn = Connection::Open();
        $sql = "DELETE  FROM tb_cronograma WHERE idEvento='$idEvento'";
        return $conn->exec($sql);
    }
/*************************************************************/
    public static function getByEvento($idEvento){
        $conn = Connection::Open();
        $sql = "SELECT * FROM tb_cronograma WHERE idEvento ='$idEvento'";
        $objPDO = $conn->query($sql);
        return self::returnObj($objPDO);
    }

/*************************************************************/
    private function returnObj($objPDO){
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
/*************************************************************/
    private static function fillObj($obj){
        return new Cronograma($obj['dataLimiteInscricao'], $obj['dataLimiteSubmissao'], $obj['dataLimiteRevisao'], $obj['idEvento']);
    }
/*************************************************************/
/*************************************************************/
}