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

// pegando os dados do usuário
$usuario = UsuarioRepository::getByEmail($data['email']);
if (!$usuario) {
    // Se o usuário não existe, retorna um erro
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Usuário não encontrado']);
    exit;
}

// Verifica a senha
if (!password_verify($data['senha'], $usuario->senha)) {
    // Se a senha estiver incorreta, retorna um erro
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Senha incorreta']);
    exit;
}

// Cria o token de autenticação
$token = bin2hex(random_bytes(16));
$usuario->tokens()->create(['token' => $token]);

// Retorna o usuário com o token
unset($usuario->senha); 
$data = $usuario->toArray();
$data['token'] = $token; 


// Retorna apenas os dados solicitados pela documentação (id, nome, email, token)
$response = [
    'id' => $data['id'],
    'nome' => $data['nome'],
    'email' => $data['email'],
    'token' => $data['token']
];

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);





