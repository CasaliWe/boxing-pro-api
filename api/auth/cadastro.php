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

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// hash da senha
$data['senha'] = password_hash($data['senha'], PASSWORD_BCRYPT);

// Verifica se o usuário já existe
$usuarioExistente = UsuarioRepository::getByEmail($data['email']);
if ($usuarioExistente) {
    // Se o usuário já existe, retorna um erro
    http_response_code(409);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Usuário já existe']);
    exit;
}

// Cria o novo usuário
$usuario = UsuarioRepository::create($data);

// Verifica se o usuário foi criado com sucesso
if (!$usuario) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao criar usuário']);
    exit;
}

// Cria o token de autenticação
$token = bin2hex(random_bytes(16)); 
$usuario->tokens()->create(['token' => $token]);

// Retorna o usuário criado com o token
$usuario->token = $token;
unset($usuario->senha); 

http_response_code(201);
header('Content-Type: application/json');
echo json_encode($usuario);





