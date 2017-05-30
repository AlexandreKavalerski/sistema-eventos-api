<?php
require_once '../DAOs/DAOCronograma.php';
require_once '../classes/Cronograma.php';
require_once '../Erros/Erros.php';
require_once 'managerEvento.php';

class managerCronograma
{
    public static function add($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvento){ #Verificar se datas estão corretas
        try{
            if(managerEvento::existeId($idEvento)) {
                return DAOCronograma::add(new Cronograma($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvento));
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function getByEvento($idEvento){
        try{
            if(managerEvento::existeId($idEvento)){
                return DAOCronograma::getByEvento($idEvento);
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function update($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvento){
        try{
            if(managerEvento::existeId($idEvento)) {
                return DAOCronograma::update(new Cronograma($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvento));
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteDataInscricao($idEvento){
        try{
            if(managerEvento::existeId($idEvento)) {
                return DAOCronograma::deleteDataInscricao($idEvento);
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteDataSubmissao($idEvento){
        try{
            if(managerEvento::existeId($idEvento)) {
                return DAOCronograma::deleteDataSubmissao($idEvento);
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteDataRevisao($idEvento){
        try{
            if(managerEvento::existeId($idEvento)) {
                return DAOCronograma::deleteDataRevisao($idEvento);
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
    public static function deleteCronograma($idEvento){
        try{
            if(managerEvento::existeId($idEvento)) {
                DAOCronograma::deleteCronograma($idEvento);
            }
            else{
                throw new IdNaoExiste('Id de evento não existe!');
            }
        }
        catch (PDOException $ex){
            throw new PDOException($ex);
        }
    }
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
}