<?php
use Firebase\JWT\JWT;

class JWTGenerator
{
    const KEY = '123'; //chave
    /**
     * Geração de um novo token jwt
    */
    public static function encode(array $options){
        $issuedAt = time(); //emitido em (quando o token foi emitido
        $expire = $issuedAt + $options ['expiration_sec']; //tempo de expiração do token

        $tokenParam = [
            'iat' => $issuedAt, //timestamp de geração do token
            'iss' => $options['iss'], //dominio, pode ser usado para descartar tokens de outros dominios
            'exp' => $expire, //expiração do token
            'nbf' => $issuedAt - 1, //token não é válido antes de (do horário em que ele foi emitido, nesse caso)
            'data' => $options['userdata'] // Dados do usuário logado
        ];
        return JWT::encode($tokenParam, self::KEY);
    }
/*************************************************************/
    public static function decode($jwt){
        return JWT::decode($jwt, self::KEY, ['HS256']);
    }
/*************************************************************/
    public static function refresh(array $options){
        $issuedAt = $options['iat'];
        $tokenParam = [
            'iat' => $issuedAt, //timestamp de geração do token
            'iss' => $options['iss'], //dominio, pode ser usado para descartar tokens de outros dominios
            'exp' => $options['exp'], //expiração do token
            'nbf' => $issuedAt-1, //token não é válido antes de (do horário em que ele foi emitido, nesse caso)
            'data' => $options['userdata'] // Dados do usuário logado
        ];
        return JWT::encode($tokenParam, self::KEY);
    }
/*************************************************************/
/*************************************************************/
}