<?php

class Usuario
{
    public $Id;
    public $Nome;
    public $Email;
    public $Senha;
    public $Sexo;
    public $DataDeNascimento;
    public $IdEndereco;
    public $Imagem;

    public function __construct($nome, $email, $senha,$idEndereco, $sexo = null, $DtNascimento = null, $imagem=null,$id = null)
    {
        $this->Nome = $nome;
        $this->Email = $email;
        $this->Senha = $senha;
        $this->IdEndereco = $idEndereco;
        $this->Sexo = $sexo;
        $this->DataDeNascimento = $DtNascimento;
        $this->Imagem = $imagem;
        $this->Id = $id;
    }

    public function __toString()
    {
        return "Nome: " . $this->Nome . ", Email: " . $this->Email . ", Sexo: " . $this->Sexo . ", Data de Nascimento: " . $this->DataDeNascimento;

    }
}

?>