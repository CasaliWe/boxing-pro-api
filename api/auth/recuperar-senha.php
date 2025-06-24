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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// verifica se o email existe
$usuario = UsuarioRepository::getByEmail($data['email']);

// cria uma nova senha aleatória
if (!$usuario) {
    // Se o usuário não existe, retorna um erro
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Usuário não encontrado']);
    exit;
}

// Gera uma nova senha aleatória
$novaSenha = bin2hex(random_bytes(4));

// Atualiza a senha do usuário
$usuario->senha = password_hash($novaSenha, PASSWORD_BCRYPT);

if (!$usuario->save()) {
    // Se não conseguir salvar, retorna um erro
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao atualizar a senha']);
    exit;
}

//enviar email
$mail = new PHPMailer(true);

try {
    // Configurações do servidor
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USER'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_SECURE'];
    $mail->Port = $_ENV['MAIL_PORT'];

    // Configuração da codificação de caracteres
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // Destinatários
    $mail->setFrom($_ENV['MAIL_USER'], $_ENV['NOME_SITE']);
    $mail->addAddress($usuario['email'], $_ENV['NOME_SITE']);

    // Cabeçalhos adicionais
    $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
    $mail->addCustomHeader('Precedence', 'bulk');

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Nova senha!';
    $mail->Body = '
            <h1>Recuperação de Senha</h1>
            <p>Olá,</p>
            <p>Você solicitou a recuperação de senha. Sua nova senha é: <strong>' . $novaSenha . '</strong></p>
            <p>Por favor, faça login e altere sua senha o mais rápido possível.</p>
            <p>Atenciosamente,</p>
            <p>' . $_ENV['NOME_SITE'] . '</p>
    ';

    $mail->send();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'mensagem' => 'Email de recuperação enviado com sucesso'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Erro ao enviar o email: ' . $mail->ErrorInfo
    ]);
}


