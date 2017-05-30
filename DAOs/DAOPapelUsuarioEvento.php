<?php
require_once("../classes/PapelUsuarioEvento.php");
require_once ("../classes/Connection.php");
require_once("../classes/EventosUsuario.php");
class DAOPapelUsuarioEvento
{
    public static function add($papelUsuarioEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "INSERT INTO `tb_papelusuarioevento`(`idUsuario`,`idPapel`,`idEvento`)
               VALUES ('$papelUsuarioEvento->IdUsuario', '$papelUsuarioEvento->IdPapel', '$papelUsuarioEvento->IdEvento')";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function existeRelacao($papelUsuarioEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idUsuario='$papelUsuarioEvento->IdUsuario'
AND idEvento='$papelUsuarioEvento->IdEvento' AND idPapel='$papelUsuarioEvento->IdPapel'";
             $objResult = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objResult);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento";
            $objPDO = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDO);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    public static function getByEvento($idEvento)
    {
        try {
            #Retorno todas as relações existentes em determinado evento (administradores, autores, participantes, e seus respectivos ids)
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idEvento='$idEvento'";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        }
        catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

    public static function getByUsuario($idUsuario)
    {
        try {
            #Retorno todas as relações existentes para determinado usuario (administradores, autores, participantes, e seus respectivos ids)
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idUsuario=" . $idUsuario;
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getPapeisUsuEmEvento($idUsuario, $idEvento)
    {
        try {
            #Pego todos os papeis do usuario em determinado evento
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idEvento=.'$idEvento' AND idUsuario=.'$idUsuario'";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getAdmEvento($idEvento)
    {
        #Retorna Administrador(es) daquele evento (idPapel=1)
        try {
            $conn = Connection::Open();
            $sql = "SELECT *  FROM tb_papelUsuarioEvento WHERE idEvento='$idEvento' AND idPapel=1";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getAvaliadorEvento($idEvento)
    {
        #Retorna Avaliador(es) daquele evento (idPapel=2)
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idEvento='$idEvento' AND idPapel=2";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getAutorEvento($idEvento)
    {
        #Retorna Autor(es) daquele evento (idPapel=3)
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idEvento='$idEvento' AND idPapel=3";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function getParticipanteEvento($idEvento)
    {
        #Retorna Participante(es) daquele evento (idPapel=4)
        try {
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelUsuarioEvento WHERE idEvento='$idEvento' AND idPapel=4";
            $objPDOStatement = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDOStatement);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    public static function getEventosEPapeis($idUsuario){
        try {
            $conn = Connection::Open();
            $sql = "SELECT e.id AS idEvento, e.nome AS nomeEvento, pa.nome AS nomePapel FROM tb_papelusuarioevento p INNER JOIN tb_evento e ON p.idEvento = e.id INNER JOIN tb_papel pa ON pa.id = p.idPapel WHERE p.idUsuario ='$idUsuario' ORDER BY `idEvento` ASC";
            $arr = $conn->prepare($sql);
            $arr->execute();
            $result = $arr->fetchAll();
            return self::returnArrayEventosPapeis($result);
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function deleteRelacao($papelUsuarioEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idUsuario`='$papelUsuarioEvento->IdUsuario' AND `idPapel`='$papelUsuarioEvento->IdPapel' AND `idEvento`='$papelUsuarioEvento->IdEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteAdministradores($idEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idPapel`=1 AND `idEvento`='$idEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteAvaliadores($idEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idPapel`=2 AND `idEvento`='$idEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteAutores($idEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idPapel`=3 AND `idEvento`='$idEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteParticipantes($idEvento)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idPapel`=4 AND `idEvento`='$idEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteTodaspapelUsuarioEventoDeUsuario($idUsuario)
    {
        #Ao deletar um usuário, apago todas as relações dele com eventos e papéis
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idUsuario`='$idUsuario'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function deleteTodaspapelUsuarioEventoDeEvento($idEvento)
    {
        #Ao deletar um evento, apago todas as relações dele com usuários e papéis
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM tb_papelusuarioevento WHERE `idEvento`='$idEvento'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    public static function isAdministradorDeEvento($idUsuario){
        try{
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_papelusuarioevento WHERE idUsuario='$idUsuario' AND idPapel=1";
            $objPDO = $conn->query($sql);
            return self::returnpapelUsuarioEvento($objPDO);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private function returnpapelUsuarioEvento($objResult)
    {
        try {
            $papelUsuarioEventos = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                } else {
                    foreach ($objResult as $papelUsuarioEvento) {
                        $papelUsuarioEventou = self::fillObject($papelUsuarioEvento);
                        array_push($papelUsuarioEventos, $papelUsuarioEventou);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $papelUsuarioEventou = self::fillObject($objResult[$i]);
                        array_push($papelUsuarioEventos, $papelUsuarioEventou);
                    }
                }
            }
            return $papelUsuarioEventos;
        } catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }

/*************************************************************/
    private function returnArrayEventosPapeis($objResult)
    {
        try {
            $eventos = array();
            $papeis = array();
            if(count($objResult)==0)
                return null;
            elseif(count($objResult)>0){
                for ($i = 0; $i < count($objResult); $i++) {
                    $idEvento = $objResult[$i]['idEvento'];
                    $nomeEvento = $objResult[$i]['nomeEvento'];
                    $nomePapel = $objResult[$i]['nomePapel'];
                    array_push($papeis, $nomePapel);
                    if(!($i >= count($objResult)-1)) {
                        while ($objResult[$i + 1]['idEvento'] == $idEvento) {
                            array_push($papeis, $objResult[$i + 1]['nomePapel']);
                            $i += 1;
                            if ($i >= count($objResult) - 1)
                                break;
                        }
                    }
                    $eventoUsuarios = self::fillObjectEventoUsers($idEvento,$nomeEvento, $papeis);
                    array_push($eventos,$eventoUsuarios);
                    $papeis = array();
                }
            }
            return $eventos;
        }
        catch (PDOException $ex) {
            throw new Exception($ex);
        }
    }
/*************************************************************/
    private static function fillObject($obj)
    {
        try {
            return new PapelUsuarioEvento($obj["idUsuario"], $obj['idPapel'], $obj['idEvento']);
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
    private static function fillObjectEventoUsers($idE, $nomeE, $papeis){
        try {
            return new EventosUsuario($idE, $nomeE, $papeis);
        }catch (Exception $ex){
            throw new Exception($ex);
        }
    }
}