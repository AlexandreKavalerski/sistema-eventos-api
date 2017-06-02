<?php
require_once "vendor/autoload.php";
require_once "../classes/auth/JWTGenerator.php";
require_once '../Erros/Erros.php';
require_once "../managers/managerUsuario.php";
require_once "../managers/managerEvento.php";
require_once "../managers/managerUsersRules.php";
require_once "../managers/managerPapelUsuarioEvento.php";
require_once "../managers/managerAdministradorGeral.php";
require_once "../managers/managerCronograma.php";
require_once "../managers/managerComite.php";

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

date_default_timezone_set("America/Sao_Paulo");
$app = new Application();
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), array(
    "cors.allowOrigin" => "*",
    "cors.allowMethods" => "GET, HEAD, OPTIONS, POST, PUT, DELETE"
));
$app->after($app["cors"]);
#$app['debug'] = true;

/*
 * middleware para fazer o controle de acesso às rotas
*/

$app->before(function(Request $request) use($app){
    $route = $request->get('_route');
    $test = $request->getClientIp();
    $url = $request->getUri();

    if($route != 'POST_authentication'
	and $route != 'POST_account'
	and $route != 'GET_'
	and $route != 'POST_file-upload'
    and strpos($route, 'OPTIONS')=== false){

        $authorization = $request->headers->get('token-authorization');
        list($jwt) = sscanf($authorization, '%s');
        if($jwt){
            try{
                $app['jwt'] = JWTGenerator::decode($jwt);
                $rotasLivres = managerUsersRules::getRotasLivres();
                    //se usarmos php 7, esse array pode ser definido apenas uma vez utilizando define(). Diminuindo a quantidade de acessos ao banco, ou nem armazenar em banoo
                if(!in_array($route, $rotasLivres)){
                    if (!$app['jwt']->data->isAdmGeral) {
                        if (!managerUsersRules::temPermissao($app['jwt']->data, $route, $url)){
                            return $app->json(['message'=>'Usuário não possui permissão'], 403);
                        }
                    }
                }
            }
            catch(Exception $ex){
                return $app->json(['message'=>'Não autorizado: ' . $ex->getMessage()], 403);
            }
        }
        else{
            return $app->json(['message'=>'Token não informado'], 401);
        }
    }
});


$app->get('/', function() use ($app) {
	return $app->json([
		'name' => 'Sistema Eventos REST API',
		'version' => '0.1.0',
	]);
});


/*************************************************************/
/*
* #R1: Criar conta no sistema
*/
$app->post('account', function(Request $request) use ($app){
    try {
        $dados = json_decode($request->getContent(), true);
        $nome = $dados['nome'];
        $email = $dados['email'];
        $senha = $dados['senha'];
        $sexo = $dados['sexo'];
        $dtNascimento = $dados['dataDeNascimento'];
        $cepEndereco = $dados['cepEndereco'];
        $logradouroEndereco = $dados['logradouroEndereco'];
        $complementoEndereco = $dados['complementoEndereco'];
        $idCidade = $dados['idCidade'];
        $imagem = $dados['imagem'];
        $resultManager = managerUsuario::add($nome, $email, $senha, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade, $sexo, $dtNascimento, $imagem);
        if ($resultManager) {
            return $app->json(["data"=>['UsuarioAdicionado'=>managerUsuario::getUsuarioById($resultManager)], "message" => "Usuário cadastrado com sucesso!"]);
        }
        else {
            return $app->json(['message'=> 'Erro ao criar conta! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);

        }
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (JaExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (IdNulo $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});

$app->post('file-upload', function(Request $request) use ($app) {
	$path = __DIR__. '/../uploads/';
	$file = $request->files->get('file');
	$originalFileName = $file->getClientOriginalName();
	$info = pathinfo($originalFileName);
	$ext = $info['extension'];
	$basename = basename($originalFileName, '.' . $ext);
	$filename = uniqid($basename . '-') . '.' . $ext;
	$file->move($path, $filename);

	return $app->json(array(
		'filename' => $filename,
		'original_filename' => $originalFileName,
		'info' => $info,
		'_files' => $_FILES
	));
});

/*************************************************************/
$app->post('authentication', function (Request $request) use ($app){
    try {
        $dados = json_decode($request->getContent(), true);
        $usuario = managerUsuario::loginCadastrado($dados['email'], $dados['password']);
        if ($usuario instanceof Usuario) {
            $isAdmGeral = managerAdministradorGeral::isAdmGeral($usuario->Id);
            $jwt = JWTGenerator::encode([
                "expiration_sec" => 14400,
                "iss" => "fabrica.ulbra-to.br/sistema-eventos/backend/api/index.php",
                "userdata" => [
                    "id" => $usuario->Id,
                    "name" => $usuario->Nome,
                    "email" => $usuario->Email,
                    "isAdmGeral" => $isAdmGeral,
                    "events" => managerPapelUsuarioEvento::getEventosEPapeis($usuario->Id)
                ],

            ]);
            return $app->json([
                'login' => true,
                'isAdmGeral' => $isAdmGeral,
                'id' => $usuario->Id,
                'name' => $usuario->Nome,
                "isAdmEvento" => managerPapelUsuarioEvento::isAdministradorDeEvento($usuario->Id),
                'access_token' => $jwt
            ]);
        } else {
            return $app->json(['message' => 'Login não cadastrado!'], 401);
        }
    }
    catch (SenhaIncorreta $ex){
        return $app->json(['message' => $ex->getMessage()], 400);
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message' => $ex->getMessage()], 400);
    }
    catch (PDOException $ex){
        return $app->json(['message' => $ex->getMessage()], 500);
    }
    /*return $app->json([
        'login' => 'false',
        'message' => 'Login Inválido',
    ]);*/
});
/*************************************************************/
$app->get('/home-user', function() use($app) {
    return $app->json(['message'=>'Oi, senhor '. $app['jwt']->data->name]);
});

/*
 **************** CRUD USUARIO *************
*/
/*
#R2:  Gerenciar usuários (Administrador geral)
*/
$app->post("users", function(Request $request) use($app){
    try {
        $dados = json_decode($request->getContent(), true);
        $nome = $dados['nome'];
        $email = $dados['email'];
        $senha = $dados['senha'];
        $sexo = $dados['sexo'];
        $dtNascimento = $dados['dataDeNascimento'];
        $cepEndereco = $dados['cepEndereco'];
        $logradouroEndereco = $dados['logradouroEndereco'];
        $complementoEndereco = $dados['complementoEndereco'];
        $idCidade = $dados['idCidade'];
        $imagem = $dados['imagem'];
        $isAdmGeral = filter_var($dados['isAdmGeral'], FILTER_VALIDATE_BOOLEAN);
        $resultManager = managerUsuario::add($nome, $email, $senha, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade, $sexo, $dtNascimento, $imagem,$isAdmGeral);
        if ($resultManager){
            return $app->json(["data"=>['UsuarioAdicionado'=>managerUsuario::getUsuarioById($resultManager)], "message" => "Usuário cadastrado com sucesso!"]);
        }
        else{
            return $app->json(['message'=>'Erro ao adicionar usuário! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (JaExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (IdNulo $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->get("users", function() use($app){
    try {
        $objResult = managerUsuario::getAll();
        if(is_array($objResult)) {
            return $app->json(['data'=>$objResult, 'message'=>'Usuários buscados com sucesso!']);
        }
        else
            return $app->json(['message'=>'Erro ao buscar usuários! Desculpe o transtorno, trabalharemos para identificar tal erro'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->get("users/{idUser}", function($idUser) use($app){
    try {
        $objResult = managerUsuario::getUsuarioById($idUser);
        if($objResult instanceof Usuario)
            return $app->json(['data'=>$objResult, 'message'=>'Usuário buscado com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar usuário! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*CONTINUANDO*/


/*************************************************************/
/*
#R3: Alterar os dados da conta de usuário (Usuário)
*/
$app->patch("users/{idUser}/profile", function($idUser, Request $request) use($app){ #Testar quando insere email que ja existe
    try {
        $dados = json_decode($request->getContent(), true);
        $nome = $dados['nome'];
        $email = $dados['email'];
        $sexo = $dados['sexo'];
        $dtNascimento = $dados['dataDeNascimento'];
        $cepEndereco = $dados['cepEndereco'];
        $logradouroEndereco = $dados['logradouroEndereco'];
        $complementoEndereco = $dados['complementoEndereco'];
        $idCidade = $dados['idCidade'];
        $objResult = managerUsuario::updateProfile($nome, $email, $sexo, $dtNascimento, $idUser, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade);
        if($objResult)
            return $app->json(['data'=>['UsuarioEditado'=>managerUsuario::getUsuarioById($idUser)], 'message'=>'Perfil do usuário editado com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao editar perfil! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);

    }
    catch(PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch(PDOException $ex){
        if(strpos($ex->getMessage(), '23000')){
            return $app->json(['message'=>'O email informado já pertence a um usuário! Por favor, modifique o email!'], 500);
        }
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->patch("users/{idUser}/password", function($idUser, Request $request) use($app){
    try {
        $dados = json_decode($request->getContent(), true);
        $senha = $dados['senha'];
        $objResult = managerUsuario::updateSenha($idUser, $senha);
        if ($objResult)
            return $app->json(['data'=>['UsuarioEditado'=>managerUsuario::getUsuarioById($idUser)], 'message' => 'Senha alterada com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao alterar senha! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
    /*
     senha:123456
     */
});
/*************************************************************/
$app->delete("users/{idUser}", function($idUser) use($app){
    try {
        $objResult = managerUsuario::delete($idUser);
        if ($objResult)
            return $app->json(['data'=>['IdUsuarioDeletado'=>$idUser], 'message' => 'Usuário deletado com sucesso!']);
        else
            return $app->json(['message' => 'Erro ao excluir usuário! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex){
        return $app->json(['message' => $ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message' => $ex->getMessage()], 500);
    }
});
/*
 **************** CRUD EVENTO *************
*/
/*
 * Como usuário eu quero cadastrar um evento
 * Quando o usuário cria (cadastra) o evento ele se torna o "administrador do evento".
 * */
$app->post("events", function(Request $request) use($app){ #Arrumar
    try{
        $dados = json_decode($request->getContent(), true);
        $idUsuario = $app['jwt']->data->id;
        $nome = $dados['nome'];
        $dtIni = $dados['dtIni'];
        $dtFim = $dados['dtFim'];
        $sigla = $dados['sigla'];
        $descricao = $dados['descricao'];
        $cepEndereco = $dados['cepEndereco'];
        $logradouroEndereco = $dados['logradouroEndereco'];
        $complementoEndereco = $dados['complementoEndereco'];
        $idCidade = $dados['idCidade'];
        $objResult = managerEvento::add($nome, $dtIni, $dtFim, $sigla, $descricao, $idUsuario, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade);
        if($objResult) {
            $jwt = JWTGenerator::refresh([
                "iat" => $app['jwt']->iat,
                "iss" => $app['jwt']->iss,
                "exp" => $app['jwt']->exp,
                "userdata" => [
                    "id" => $app['jwt']->data->id,
                    "name" => $app['jwt']->data->name,
                    "email" => $app['jwt']->data->email,
                    "isAdmGeral" => $app['jwt']->data->isAdmGeral,
                    "events" => managerPapelUsuarioEvento::getEventosEPapeis($app['jwt']->data->id)
                ]
            ]);
            return $app->json(['data'=>['new_token'=>$jwt, 'idEventoCriado'=>$objResult], 'message' => 'Evento criado com sucesso!']);
        }
        else {
            return $app->json(['message' => 'Erro ao adicionar evento! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (IdNulo $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message' => $ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * #R5: Participar de um evento (Usuário)
*/
$app->post("events/{idEvent}/join", function($idEvent) use ($app){
    try{
        $objResult = managerEvento::addParticipante($app['jwt']->data->id, $idEvent);
        if($objResult){
            $jwt = JWTGenerator::refresh([
                "iat" => $app['jwt']->iat,
                "iss" => $app['jwt']->iss,
                "exp" => $app['jwt']->exp,
                "userdata" => [
                    "id" => $app['jwt']->data->id,
                    "name" => $app['jwt']->data->name,
                    "email" => $app['jwt']->data->email,
                    "isAdmGeral" => $app['jwt']->data->isAdmGeral,
                    "events" => managerPapelUsuarioEvento::getEventosEPapeis($app['jwt']->data->id)
                ]
            ]);
            return $app->json(['data'=>['new_token'=>$jwt, 'joined'=>true], 'message'=>'Incrição realizada com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao realizar incrição no evento! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (JaExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * Como administrador geral eu quero ver a lista de todos os eventos
 * */
$app->get("events", function() use($app){
    try {
        $objResult = managerEvento::getAll();
        if(is_array($objResult))
            return $app->json(['data'=> $objResult, 'message'=>'Eventos buscados com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar eventos! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * Como administrador de evento eu quero ver a lista dos eventos que administro
 * O sistema deve retornar a lista dos eventos que o usuário administra.
 */
$app->get("events/adm-by-me", function() use($app){
    try {
        $objResult = managerEvento::getAllAdmByMe($app['jwt']->data->id);
        if(is_array($objResult))
            return $app->json(['data'=> $objResult,'message'=>'Eventos buscados com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar eventos! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/

$app->get("events/{idEvent}", function($idEvent) use($app){
    try {
        $objResult = managerEvento::getById($idEvent);
        if($objResult instanceof Evento)
            return $app->json(['data'=> $objResult, 'message'=>'Evento buscado com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar eventos! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * Como administrador de evento eu quero alterar os dados de um evento que administro
 * Como administrador geral eu quero alterar os dados de um evento
*/
$app->put("events/{idEvent}", function($idEvent, Request $request) use($app){
    try {
        $dados = json_decode($request->getContent(), true);
        $nome = $dados['nome'];
        $dtInicio = $dados['dtIni'];
        $dtFim = $dados['dtFim'];
        $sigla = $dados['sigla'];
        $descricao = $dados['descricao'];
        $cepEndereco = $dados['cepEndereco'];
        $logradouroEndereco = $dados['logradouroEndereco'];
        $complementoEndereco = $dados['complementoEndereco'];
        $idCidade = $dados['idCidade'];

        $objResult = managerEvento::update($nome, $dtInicio , $dtFim , $idEvent, $cepEndereco, $logradouroEndereco, $complementoEndereco, $idCidade, $sigla, $descricao);
        if($objResult)
            return $app->json(['data'=>['EventoEditado'=>managerEvento::getById($idEvent)], 'message'=>'Evento alterado com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao editar Evento! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (IdNulo $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * Como administrador de evento eu quero excluir um evento que administro
 * Como administrador geral eu quero excluir um evento
 *
 * O sistema deve excluir o evento desde que não haja "relacionamentos".
 *
*/
$app->delete("events/{idEvent}", function($idEvent) use($app){
    try {
        $objResult = managerEvento::delete($idEvent,$app['jwt']->data->id);
        if($objResult)
            return $app->json(['data'=>['IdEventoDeletado'=>$idEvent], 'message'=>'Evento deletado com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao deletar Evento! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch (HaRelacionamentos $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch(IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * ESTADOS
*/
$app->get("states", function(Application $app){
    try {
        $objResult = managerEstado::getAll();
        if(is_array($objResult))
            return $app->json(['data'=>$objResult, 'message'=>'Estados buscados com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar Estados! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});

/*************************************************************/
$app->get("states/{idState}", function($idState, Application $app){
    try {
        $objResult = managerEstado::getById($idState);
        if ($objResult instanceof Estado){
            return $app->json(["data"=>$objResult, 'message' => 'Estado buscado com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao buscar Estado! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
/*
 * CIDADES
*/
/*************************************************************/
$app->get("cities", function(Application $app){
    try {
        $objResult = managerCidade::getAll();
        if(is_array($objResult))
            return $app->json(['data'=>$objResult, 'message'=>'Cidades buscadas com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar Cidades! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->get("cities/{idCity}", function($idCity, Application $app){
    try {
        $objResult = managerCidade::getById($idCity);
        if($objResult instanceof Cidade)
            return $app->json(['data'=> $objResult, 'message'=>'Cidade buscada com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar Cidades! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});

/*************************************************************/
$app->get("states/{idState}/cities", function($idState, Application $app){
    try {
        $objResult = managerCidade::getCidadesByEstado($idState);
        if (is_array($objResult))
            return $app->json(['data' => $objResult, 'message' => 'Cidades buscadas com sucesso!']);
        else
            return $app->json(['message'=>'Erro ao buscar Cidades! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});

/*
 * ENDEREÇOS
*/
/*************************************************************/
$app->get("addresses", function() use($app){
    try {
        $enderecos = managerEndereco::getAll();
        if (is_array($enderecos))
            return $app->json(["data" => managerEndereco::getAll(), "message" => "Endereços buscados com sucesso!"]);
        else
            return $app->json(['message'=>'Erro ao buscar Endereços! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->get("addresses/{idAddress}", function($idAddress) use ($app){
    try{
        $objResult = managerEndereco::getById($idAddress);
        if($objResult instanceof Endereco){
            return $app->json(['data'=>$objResult, 'message'=>"Endereço buscado com sucesso!"]);
        }
        else {
            return $app->json(['message'=>'Erro ao buscar Endereço! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch(IdNaoExiste $ex) {
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch(PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});

/*************************************************************/
/*SPRINT 3*/
/*
 * Cronograma
*/
$app->post('events/{idEvent}/schedule', function ($idEvent, Request $request) use ($app){
    try{
        $dados = json_decode($request->getContent(), true);
        $dataLimiteInscricao = $dados['dataLimiteInscricao'];
        $dataLimiteSubmissao = $dados['dataLimiteSubmissao'];
        $dataLimiteRevisao = $dados['dataLimiteRevisao'];
        $result = managerCronograma::add($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvent);
        if($result){
            return $app->json(['data'=>['CronogramaAdicionado'=>managerCronograma::getByEvento($idEvent)], 'message'=>'Cronograma adicionado com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao adicionar Cronograma! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        if(strpos($ex->getMessage(), '23000')){
            return $app->json(['message'=>'Este evento já possui cronograma'], 500);
        }
        else{
            return $app->json(['message'=>$ex->getMessage()], 500);
        }
    }
/*
{
"dataLimiteInscricao":"2016-09-10",
"dataLimiteSubmissao":"2016-10-10",
"dataLimiteRevisao":"2016-11-10"
}
 */

});

/*************************************************************/
$app->put('events/{idEvent}/schedule', function ($idEvent, Request $request) use ($app){
    try{
        $dados = json_decode($request->getContent(), true);
        $dataLimiteInscricao = $dados['dataLimiteInscricao'];
        $dataLimiteSubmissao = $dados['dataLimiteSubmissao'];
        $dataLimiteRevisao = $dados['dataLimiteRevisao'];
        $result = managerCronograma::update($dataLimiteInscricao, $dataLimiteSubmissao, $dataLimiteRevisao, $idEvent);
        if($result){
            return $app->json(['data'=>['CronogramaEditado'=>managerCronograma::getByEvento($idEvent)], 'message'=>'Cronograma alterado com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao editar Cronograma! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*
{
"dataLimiteSubmissao":"2015-01-11",
"dataLimiteRevisao":"2015-02-11",
"dataLimiteInscricao":"2015-03-11"
}
 * */
/*************************************************************/
$app->delete('events/{idEvent}/schedule/date-register', function ($idEvent) use ($app){
    try{
        $result = managerCronograma::deleteDataInscricao($idEvent);
        if($result){
            return $app->json(['data'=>['CronogramaAtual'=>managerCronograma::getByEvento($idEvent)], 'message'=>'Data Limite para inscrição excluída com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao deletar data limite para inscrição! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->delete('events/{idEvent}/schedule/date-submission', function ($idEvent) use ($app){
    try{
        $result = managerCronograma::deleteDataSubmissao($idEvent);
        if($result){
            return $app->json(['data'=>['CronogramaAtual'=>managerCronograma::getByEvento($idEvent)], 'message'=>'Data Limite para submissão excluída com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao deletar data limite para submissão! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->delete('events/{idEvent}/schedule/date-revision', function ($idEvent) use ($app){
    try{
        $result = managerCronograma::deleteDataRevisao($idEvent);
        if($result){
            return $app->json(['data'=>['CronogramaAtual'=>managerCronograma::getByEvento($idEvent)], 'message'=>'Data Limite para revisão excluída com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao deletar data limite para revisão! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->get('events/{idEvent}/schedule', function ($idEvent) use ($app){
    try{
        $crono = managerCronograma::getByEvento($idEvent);
        if($crono){
            return $app->json(['data'=>$crono, 'message'=>'Cronograma buscado com sucesso!']);
        }
        else{
            return $app->json(['message'=>'Erro ao buscar cronograma! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->post('events/{idEvent}/committee', function ($idEvent, Request $request) use ($app){
    try{
        $dados = json_decode($request->getContent(), true);
        $emailMembro = $dados['emailMembro'];
        $tipoComite = $dados['tipoComite'];
        $result = managerComite::add($emailMembro, $idEvent, $tipoComite);
        if($result) {
            $nomeUsuario = managerUsuario::getUsuarioByEmail($emailMembro)->Nome;
            return $app->json(['data'=>['EmailMembro'=>$emailMembro, 'TipoComite'=>$tipoComite, 'IdEvento'=>$idEvent], 'message'=>'Usuário ' . $nomeUsuario . ' adicionado ao comitê ' . $tipoComite . ' do evento!']);
        }
        else{
            return $app->json(['message'=>'Erro ao adicionar membro ao comitê! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (JaExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (NaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*************************************************************/
$app->delete('events/{idEvent}/committee', function($idEvent, Request $request) use ($app){
    try{
        $dados = json_decode($request->getContent(), true);
        $emailUsuario = $dados['emailMembro'];
        $tipoComite = $dados['tipoComite'];
        if(managerComite::delete($emailUsuario, $tipoComite, $idEvent)) {
            return $app->json(['data'=>['EmailMembroRemovido'=>$emailUsuario, 'TipoComite'=>$tipoComite], 'message'=>'Usuário removido do comitê do evento!']);
        }
        else{
            return $app->json(['message'=>'Erro ao remover membro do comitê! Desculpe o transtorno, trabalharemos para identificar tal erro.'], 400);
        }
    }
    catch (PreencIncorreto $ex){
        return $app->json(['message'=>$ex->getMessage()], 400);
    }
    catch (NaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (IdNaoExiste $ex){
        return $app->json(['message'=>$ex->getMessage()], 404);
    }
    catch (PDOException $ex){
        return $app->json(['message'=>$ex->getMessage()], 500);
    }
});
/*
{
"emailMembro":"email1",
"tipoComite":"Organizacional"
}
 */
/*************************************************************/
/*************************************************************/
$app->run();
?>