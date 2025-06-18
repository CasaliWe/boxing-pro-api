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

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

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

// Retorna os dados completos do perfil do usuário conforme documentação
$response = [
    'id' => $usuario->id,
    'nome' => $usuario->nome,
    'email' => $usuario->email,
    'idade' => $usuario->idade,
    'peso' => (float)$usuario->peso, // Convertendo para garantir que seja um número
    'altura' => (int)$usuario->altura,
    'academia' => $usuario->academia,
    'cidade' => $usuario->cidade,
    'estado' => $usuario->estado,
    'base' => $usuario->base,
    'competidor' => (bool)$usuario->competidor, // Garantindo que seja um booleano
    'iniciouEm' => $usuario->iniciouEm,
    'fotoPerfil' => $usuario->fotoPerfil,
    'assinatura' => $usuario->assinatura
];

// Ajuste para o caminho completo da URL da foto, se necessário
if (!empty($response['fotoPerfil']) && strpos($response['fotoPerfil'], 'http') !== 0) {
    // Adiciona o domínio da API à URL da foto se não for uma URL completa
    $response['fotoPerfil'] = $_ENV['BASE_URL'] . "api/imagens/perfil/" . $response['fotoPerfil'];
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);


