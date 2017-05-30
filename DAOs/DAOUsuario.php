<?php
require_once "../classes/Connection.php";
require_once "../classes/Usuario.php";
class DAOUsuario
{
    public static function add($user){
        try {
            $conn = Connection::Open();

            $sql = "INSERT INTO `tb_usuario`(`id`,`nome`,`email`,`senha`,`idEndereco`,`sexo`,`dtNascimento`, `imagem`)
               VALUES ('$user->Id', '$user->Nome', '$user->Email', '$user->Senha','$user->IdEndereco', '$user->Sexo','$user->DataDeNascimento', '$user->Imagem')";
            $conn->exec($sql);
            return $conn->lastInsertId();
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function update ($usuario){
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_usuario` SET `nome`='$usuario->Nome',`email`='$usuario->Email',`idEndereco`='$usuario->IdEndereco',`sexo`='$usuario->Sexo',`dtNascimento`='$usuario->DataDeNascimento', `imagem`='$usuario->Imagem'  WHERE `id` = '$usuario->Id'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function updateSenha($idUsuario,$senha)
    {
        try {
            $conn = Connection::Open();
            $sql = "UPDATE `tb_usuario` SET `senha`='$senha' WHERE `id` ='$idUsuario'";
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }


/*************************************************************/
    public static function delete($idUsuario)
    {
        try {
            $conn = Connection::Open();
            $sql = "DELETE FROM `tb_usuario` WHERE id=" . $idUsuario;
            return $conn->exec($sql);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_usuario";
            $objPDO = $conn->query($sql);
            return self::returnUsuarios($objPDO);
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getById($idUsuario)
    {
        try {
            $conn = Connection::Open();
            $sql = "SELECT *FROM tb_usuario Where id=" . $idUsuario;
            $objPDOStatement = $conn->query($sql);
            return self::returnUsuarios($objPDOStatement)[0];
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }

/*************************************************************/
    public static function getUsuarioByEmail($email){
        try{
            $conn = Connection::Open();
            $sql = "SELECT * FROM tb_usuario WHERE email='$email'";
            $objPDOStatement = $conn->query($sql);
            return self::returnUsuarios($objPDOStatement)[0];
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    /*public static function getSenhaById($id){
        try{
            $conn=Connection::Open();
            $sql = "SELECT senha FROM tb_usuario WHERE id=" . $id;
            $objPDOStatement = $conn -> query($sql);
            return self::returnSenha($objPDOStatement);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }*/
/*************************************************************/
    public static function verCredenciais($login, $senha){
        try{
            $conn = Connection::Open();
            $sql = "SELECT * FROM `tb_usuario` WHERE `email`='$login' AND `senha`='$senha'";
            $objPDOStatement = $conn ->query($sql);
            return self::returnUsuarios($objPDOStatement)[0];
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    private function returnUsuarios($objResult)
    {
        try {
            $usuarios = array();
            if ($objResult instanceof PDOStatement) {
                if ($objResult->rowCount() == 0) {
                    return null;
                } else {
                    foreach ($objResult as $linha) {
                        $usuario = self::fillObject($linha);
                        array_push($usuarios, $usuario);
                    }
                }
            } else {
                if (is_array($objResult)) {
                    for ($i = 0; $i < count($objResult); $i++) {
                        $usuario = self::fillObject($objResult[$i]);
                        array_push($usuarios, $usuario);
                    }
                }
            }
            return $usuarios;
        } catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    /*private static function returnSenha($obj){
        try{
            if ($obj instanceof PDOStatement) {
                if ($obj->rowCount() == 0) {
                    return null;
                }
                else {
                    foreach ($obj as $linha) {
                        return $linha['senha'];
                    }
                }
            }
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }*/

/*************************************************************/
    private static function fillObject($obj)
    {
        try {
            return new Usuario($obj["nome"], $obj["email"], null, $obj["idEndereco"], $obj["sexo"], $obj["dtNascimento"], $obj['imagem'],$obj["id"]);
        }
        catch (PDOException $ex) {
            throw new PDOException($ex);
        }
    }
}
?>

