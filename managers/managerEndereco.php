<?php
require_once "../Erros/Erros.php";
require_once "../DAOs/DAOEndereco.php";
require_once "managerCidade.php";

class managerEndereco
{
    public static function add($cep, $idCidade,$logradouro = null, $complemento = null)
    {
        try{
            if(strlen($cep)!= 8)
                throw new PreencIncorreto('O CEP deve possuir exatamente 8 digitos!');
            if($idCidade == null)
                throw new IdNulo('O Id da cidade não pode ser nulo');
            if(!managerCidade::existeId($idCidade))
                throw new IdNaoExiste('Cidade com id ' . $idCidade . ' não existe no sistema!');
            $endereco = new Endereco($cep, $idCidade,$logradouro, $complemento);
            return DAOEndereco::add($endereco);
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function update($cep, $idCidade, $logradouro,  $complemento, $id)
    {
        try
        {
            $endereco = new Endereco($cep, $idCidade, $logradouro, $complemento, $id);
            if(strlen($cep)!= 8)
                throw new PreencIncorreto('O CEP deve possuir exatamente 8 digitos!');
            if($idCidade == null)
                throw new IdNulo('O Id da cidade não pode ser nulo');
            if(!managerCidade::existeId($idCidade))
                throw new IdNaoExiste('Cidade com id ' . $idCidade . ' não existe no sistema!');
            if(!self::existeEndereco($id)){
                throw new IdNaoExiste('Id de endereço não existe');
            }
            return DAOEndereco::update($endereco);

        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function delete($id)
    {
        try{
            if(!self::existeEndereco($id)){
                throw new IdNaoExiste('Id de endereço não existe');
            }
            return DAOEndereco::delete($id);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function getAll()
    {
        try
        {
            return DAOEndereco::getAll();
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getById($id)
    {
        try
        {
            if(!self::existeEndereco($id)){
                throw new IdNaoExiste('Id de endereço não existe');
            }
            return DAOEndereco::getById($id);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function existeEndereco($idEndereco){
        try{
            if(DAOEndereco::getById($idEndereco) != null)
                return true;
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
/*************************************************************/
/*************************************************************/
}
?>