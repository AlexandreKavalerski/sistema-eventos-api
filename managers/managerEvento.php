<?php
require_once "../DAOs/DAOEvento.php";
require_once "../classes/Evento.php";
require_once '../managers/managerEndereco.php';
require_once '../managers/managerPapelUsuarioEvento.php';

class managerEvento
{
    public static function add($nomeEvento, $dtInicio, $dtFim, $sigla, $descricao, $idUsuario, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade)
    {
        try {
            if ($nomeEvento == null){
                throw new PreencIncorreto('Nome do Evento não pode ser nulo');
            }
            if( $dtInicio == null or $dtFim == null) {
                throw new PreencIncorreto('Datas não podem ser nulas!');
            }
            $idEndereco = managerEndereco::add($cepEndereco, $idCidade, $logradouroEndereco, $complementoEndereco);
            if($idEndereco){
                $idEvento = DAOEvento::add(new Evento($nomeEvento, $idEndereco, $dtInicio, $dtFim, $sigla, $descricao));
                if($idEvento){
                   if(managerPapelUsuarioEvento::add($idUsuario, 1, $idEvento))
                       return $idEvento;
                }
            }
            return false;
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function addParticipante($idUsuario, $idEvento)
    {
        try{
            if(!self::existeId($idEvento)){
                throw new IdNaoExiste('Id de evento não existe!');
            }
            if(!managerPapelUsuarioEvento::existeRelacao($idUsuario, 4, $idEvento)){
                return managerPapelUsuarioEvento::add($idUsuario, 4, $idEvento);
            }
            else{
                throw new JaExiste('Usuário já é participante deste evento!');
            }
        }
        catch(PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function update($nome, $dtInicio , $dtFim , $id, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade, $sigla, $descricao)
    {
        try{
            if(!self::existeId($id)){
                throw new IdNaoExiste('Id de evento não existe!');
            }
            if($nome == null){
                throw new PreencIncorreto('Nome do evento não pode ser nulo!');
            }
            if( $dtInicio == null or $dtFim == null) {
                throw new PreencIncorreto('Datas não podem ser nulas!');
            }
            $evento = self::getById($id);
            managerEndereco::update($cepEndereco, $idCidade, $logradouroEndereco, $complementoEndereco, $evento->IdEndereco);
            return DAOEvento::update(new Evento($nome, $evento->IdEndereco, $dtInicio, $dtFim, $sigla, $descricao, $id));

        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function delete($idEvento, $idUsuario)
    {
        try
        {
            if(!self::existeId($idEvento)){
                throw new IdNaoExiste('Id de evento não existe!');
            }
            $result = DAOPapelUsuarioEvento::getByEvento($idEvento);
            if(count($result) == 1) {
                DAOPapelUsuarioEvento::deleteRelacao(new PapelUsuarioEvento($idUsuario, 1, $idEvento));
                return DAOEvento::delete($idEvento);
            }
            else{
                throw new HaRelacionamentos('Evento não pode ser deletado pois há outros usuários vinculados a ele!');
            }
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
            return DAOEvento::getAll();
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getAllAdmByMe($idUsuario)
    {
        try
        {
            return DAOEvento::getEventosEuAdm($idUsuario);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }
/*************************************************************/
    public static function getById($idEvento)
    {
        try
        {
            if(!self::existeId($idEvento)){
                throw new IdNaoExiste('Id  de evento não existe!');
            }
            return DAOEvento::getById($idEvento);
        }
        catch(PDOException $ex)
        {
            throw new PDOException ($ex);
        }
    }

    public static function getNomeById($idEvento){
        try{
            if(!self::existeId($idEvento)){
                throw new IdNaoExiste('Id de Evento não existe!');
            }
            $ev = DAOEvento::getById($idEvento);
            return $ev->Nome;
        }
        catch(PDOException $ex){
            throw new PDOException ($ex);
        }
    }

/*************************************************************/
    public static function existeId($idEvento){
        try{
            if(DAOEvento::getById($idEvento) != null)
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