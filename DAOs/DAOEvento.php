<?php
require_once "../classes/Evento.php";
require_once "../classes/Connection.php";
class DAOEvento
{
    public static function add($evento)
    {
        try {

               $conn = Connection::Open();
               $sql = "INSERT INTO `tb_evento`(`id`,`nome`, `sigla`, `descricao`, `dtInicio`,`dtFim`, `idEndereco`)
               VALUES ('$evento->Id', '$evento->Nome', '$evento->Sigla', '$evento->Descricao', '$evento->DtInicio', '$evento->DtFinal',   '$evento->IdEndereco')";
               $conn->exec($sql);
                return $conn->lastInsertId();
        }catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function update($evento)
    {
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_evento` SET `id`='$evento->Id',`nome`='$evento->Nome',`sigla`='$evento->Sigla',`descricao`='$evento->Descricao',`dtInicio`='$evento->DtInicio',`dtFim`='$evento->DtFinal' WHERE id = '$evento->Id'";
            return $conn->exec($sql);
        }catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function delete($idEvento){
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_evento` WHERE id=" . $idEvento;
            return $conn->exec($sql);
        }catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_evento";
            $objPDO = $conn->query($sql);
            return self::returnEventos($objPDO);
        } catch (PDOException $ex) {
            throw new Exception($ex); //Joga pra camada de cima
        }
    }
/*************************************************************/
    public static function getById($idEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_evento WHERE id=" . $idEvento;
            $objPDOStatement = $conn->query($sql);
            return DAOEvento::returnEventos($objPDOStatement)[0];
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    public static function getEventosEuAdm($idUsuario){
        try{
            $conn = Connection::Open();
            $sql = "SELECT e.* FROM tb_papelusuarioevento p INNER  JOIN  tb_evento e ON p.idEvento = e.id WHERE p.idPapel = 1 AND p.idUsuario = '$idUsuario'";
            $objPDOStatement = $conn->query($sql);
            return self::returnEventos($objPDOStatement);
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    private function returnEventos($objResult)
    {
        try {
            $eventos = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                } else {
                    foreach ($objResult as $linha) {
                        $evento = DAOEvento::fillObject($linha);
                        array_push($eventos, $evento);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $evento = DAOEvento::fillObject($objResult[$i]);
                        array_push($eventos, $evento);
                    }
                }
            }
            return $eventos;
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    private static function fillObject($obj)
    {
        try {
            $evento = new Evento($obj["nome"], $obj["idEndereco"], $obj["dtInicio"], $obj["dtFim"], $obj["sigla"], $obj["descricao"], $obj["id"]);
            return $evento;
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
}