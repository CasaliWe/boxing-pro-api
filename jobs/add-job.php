<?php

require_once __DIR__ . '/Jobs.php';


// Cria o job com os dados do e-mail
$job = [
    'email' => $_GET['email'], 
    'assunto' => $_GET['assunto'], 
    'mensagem' => $_GET['mensagem']
];

// Adiciona o job à fila 'email_queue'
RedisQueue::push(json_encode($job));

// Informa que o job foi enfileirado
echo "Job de e-mail Criado com sucesso!.\n";