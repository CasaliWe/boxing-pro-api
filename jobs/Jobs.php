<?php

require __DIR__ . '/../vendor/autoload.php'; 

use Predis\Client; 

// Classe para manipular a fila via Redis com métodos estáticos
class RedisQueue {

    // Propriedade estática para armazenar a conexão com o Redis
    protected static $redis = null;
    // Nome padrão da fila
    protected static $queueName = 'email_queue';

    // Método para obter a conexão com o Redis (inicializa se necessário)
    protected static function getRedis() {
        if (self::$redis === null) {
            self::$redis = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
        }
        return self::$redis;
    }

    // Método para adicionar um job ao fim fila
    public static function push($message, $queueName = null) {
        $q = $queueName ? $queueName : self::$queueName;
        return self::getRedis()->rPush($q, $message);
    }

    // Buscando job da fila
    public static function pop($queueName = null) {
        $q = $queueName ? $queueName : self::$queueName;
    
        try {
            $result = self::getRedis()->brPop([$q], 0);
            return $result ? $result[1] : null;
        } catch (\Exception $e) {
            echo "Erro ao conectar no Redis: " . $e->getMessage() . "\n";
            sleep(5);
            return null;
        }
    }

    // Buscar todas as mensagens da fila
    public static function getAll($queueName = null) {
        $q = $queueName ? $queueName : self::$queueName;
        return self::getRedis()->lRange($q, 0, -1);
    }
}

?>
