<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, Cache-Control, Authorization, Origin');

// Se o método for OPTIONS, só responde com 200 e termina aqui
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../../config/config.php';
use Repositories\UsuarioRepository;

// pegando o token do cabeçalho Authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token de autenticação não fornecido']);
    exit;
}

// Extrair o token do formato "Bearer token"
$authHeader = $headers['Authorization'];
if (strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Formato de token inválido']);
    exit;
}

$token = substr($authHeader, 7); 

// Verifica se o token é válido
$tokenObj = UsuarioRepository::isTokenValid($token);
if (!$tokenObj) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token de autenticação inválido']);
    exit;
}

// Buscar os dados do usuário associado ao token
$usuario = UsuarioRepository::getById($tokenObj->user_id);

if (!$usuario) {
    // Se não encontrar o usuário, retorna um erro
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Usuário não encontrado']);
    exit;
}

// pegando os dados do corpo da requisição em json
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// verificando se a senha do banco é a mesma que veio do corpo da requisição fazendo o teste de hash
if (!password_verify($data['senhaAtual'], $usuario->senha)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Senha incorreta']);
    exit;
}

// Verifica se a nova senha foi fornecida
if (!isset($data['novaSenha']) || empty($data['novaSenha'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Nova senha não fornecida']);
    exit;
}

// Atualiza a senha do usuário
$senha = password_hash($data['novaSenha'], PASSWORD_DEFAULT);

// atualizando senha no banco
$res = UsuarioRepository::updatePassword($usuario->id, $senha);

if (!$res) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao atualizar senha']);
    exit;
}

// Retorna os dados atualizados do perfil do usuário conforme documentação
$response = [
    'mensagem' => 'Senha atualizada com sucesso'
];

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);


