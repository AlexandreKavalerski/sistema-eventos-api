<?php
require_once "../classes/Usuario.php";
require_once "../DAOs/DAOUsuario.php";
require_once "../managers/managerEndereco.php";
require_once "../managers/managerAdministradorGeral.php";
require_once "../Erros/Erros.php";

class ManagerUsuario
{
/*************************************************************/
    public static function add($nome, $email, $senha, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade, $sexo, $dtNascimento, $imagem, $isAdmGeral = false)
    {
        try {
            if(!self::emailPreencCorreto($email)) {
                throw new PreencIncorreto('Email preenchido de forma incorreta! O email não pode ser nulo e nem conter mais de 50 digitos.');
            }
            if(!self::nomePreencCorreto($nome)) {
                throw new PreencIncorreto('Nome preenchido de forma incorreta! O nome não pode ser nulo e nem conter mais de 200 digitos.');
            }
            if(self::existeEmail($email)){
                throw new JaExiste('Email ' . $email . ' já está cadastrado no sistema!');
            }
            $sexo = strtoupper($sexo);
            if(strlen($sexo)>1 or ($sexo != 'M' and $sexo != 'F')){
                throw new PreencIncorreto('O campo sexo deve conter apenas um dígito! E deve ser "M" ou "F"');
            }
            $idEndereco = managerEndereco::add($cepEndereco, $idCidade, $logradouroEndereco, $complementoEndereco);
            if($idEndereco) {
                $idUsuario = DAOUsuario::add(new Usuario($nome, $email, $senha, $idEndereco, $sexo, $dtNascimento, $imagem));
                if($isAdmGeral){
                    return managerAdministradorGeral::add($idUsuario);
                }
                if ($idUsuario){
                    return $idUsuario;
                }
            }
            return false;
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/

    public static function updateProfile($nome, $email, $sexo, $dtNascimento, $idUsuario, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade)
    {
        try {
            if(!self::existeId($idUsuario))
                throw new IdNaoExiste('Id de usuário não existe');
            if(!self::emailPreencCorreto($email)) {
                throw new PreencIncorreto('Email preenchido de forma incorreta! O email não pode ser nulo e nem conter mais de 50 digitos.');
            }
            if(!self::nomePreencCorreto($nome)) {
                throw new PreencIncorreto('Nome preenchido de forma incorreta! O nome não pode ser nulo e nem conter mais de 200 digitos.');
            }
            if(count($sexo)>1){
                throw new PreencIncorreto('O campo sexo deve conter apenas um dígito! "M" ou "F"');
            }
            /*
             if(self::existeEmail($email) and $idUsuario != self::getIdUsuarioByEmail($email)){
                throw new JaExiste('Email ' . $email . ' já está cadastrado no sistema!');
            }*/
            $usuario = self::getUsuarioById($idUsuario);
            managerEndereco::update($cepEndereco, $idCidade, $logradouroEndereco, $complementoEndereco,$usuario->IdEndereco);
            if(DAOUsuario::update(new Usuario($nome, $email,null, $usuario->IdEndereco, $sexo, $dtNascimento, $idUsuario)))
                return $idUsuario;

        }
        catch (PDOException $ex) {
            throw new PDOException ($ex);
        }
    }

/*************************************************************/

    public static function updateSenha($idUsuario, $senha)
    {
        try{
            if($senha == null) {
                throw new PreencIncorreto('O campo senha não pode ser nulo!');
            }
            if(!self::existeId($idUsuario)) {
                throw new IdNaoExiste('Id de usuário não existe!');
            }
            return DAOUsuario::updateSenha($idUsuario, $senha);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/

    public static function delete($idUsuario)
    {
        try
        {
            if(!self::existeId($idUsuario)){
                throw new IdNaoExiste('Id de usuário não existe!');
            }
            return DAOUsuario::delete($idUsuario);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }

/*************************************************************/

    public static function getAll()
    {
        try{
            return DAOUsuario::getAll();
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getUsuarioById($idUsuario)
    {
        try
        {
            if(!self::existeId($idUsuario))
                throw new IdNaoExiste('Id de usuário não existe!');
            return DAOUsuario::getById($idUsuario);

        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getSenhaById($idUsuario){
        try{
            if(!self::existeId($idUsuario))
                throw new IdNaoExiste('Id de usuário não existe!');
            return DAOUsuario::getSenhaById($idUsuario);
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getUsuarioByEmail($email){
        try{
            $usuario = DAOUsuario::getUsuarioByEmail($email);
            if ($usuario instanceof Usuario){
                return $usuario;
            }
            return false;
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getIdUsuarioByEmail($email){
        try{
            $usuario = DAOUsuario::getUsuarioByEmail($email);
            if ($usuario instanceof Usuario){
                return $usuario->Id;
            }
            return false;
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function loginCadastrado($email, $senha){
        try{
            if(!self::emailPreencCorreto($email)) {
                throw new PreencIncorreto('Email preenchido de forma incorreta! O email não pode ser nulo e nem conter mais de 50 digitos.');
            }
            $usuario = DAOUsuario::verCredenciais($email, $senha);
            if($usuario instanceof Usuario)
                return $usuario;
            else
                throw new SenhaIncorreta('A senha digitada está incorreta!');
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function nomePreencCorreto($nome){
        if ($nome == null or count($nome) > 200) {
            return false;
        }
        return true;
    }
/*************************************************************/
    public static function emailPreencCorreto($email){
        if ($email == null or count($email) > 50)
            return false;
        return true;
    }
/*************************************************************/
    public static function existeEmail($email){
        try {
            $usuario = DAOUsuario::getUsuarioByEmail($email);
            if($usuario instanceof Usuario)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function existeId($idUsuario){
        try{
            if(DAOUsuario::getById($idUsuario) != null)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
/*************************************************************/
}
?>