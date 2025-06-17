<?php

require_once __DIR__ . '/Jobs.php';

echo "Worker iniciado, aguardando jobs...\n";

// Loop infinito para processar os jobs conforme forem enfileirados
while (true) {
    // Tenta retirar um job da fila (agora bloqueante, aguardando novos jobs)
    $email = RedisQueue::pop();

    if ($email) {
        echo "Processando job...\n";
        sleep(2); // simulação de processamento
    
        // Exibe o conteúdo do job
        echo "Job processado com sucesso!\n";
        echo "Job: " . $email . "\n";
    }else{
        echo "Falha na conexão ou nenhum job encontrado. Tentando novamente em 5 segundos...\n";
        sleep(5); 
    }
}
?>
