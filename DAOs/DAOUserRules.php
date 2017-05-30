<?php
require_once "../classes/Connection.php";
class DAOUserRules
{

    public static function getRotasLivres(){
        try{
            $conn = Connection::Open();
            $sql = "SELECT r.rota AS rota FROM tb_rota r INNER JOIN tb_rotalivre l ON r.id = l.idRota";
            $objResult = $conn->query($sql);
            return self::returnRotas($objResult);
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function papeisQueAcessam($rota){
        try {
            $conn = Connection::Open();
            $sql = "SELECT p.nome as nomePapel FROM tb_papel p INNER JOIN tb_acessa a ON p.id = a.idPapel INNER JOIN tb_rota r ON r.id = a.idRota WHERE r.rota = '$rota'";
            $objResult = $conn->query($sql);
            return self::returnPapeis($objResult);
        }
        catch (PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    private static function returnPapeis($obj)
    {
        try {
            $papeis = array();
            if ($obj instanceof PDOStatement) {
                if ($obj->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($obj as $linha) {
                        array_push($papeis, $linha['nomePapel']);
                    }
                }
            }
            return $papeis;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private static function returnRotas($obj)
    {
        try {
            $rotas = array();
            if ($obj instanceof PDOStatement) {
                if ($obj->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($obj as $linha) {
                        array_push($rotas, $linha['rota']);
                    }
                }
            }
            return $rotas;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
}