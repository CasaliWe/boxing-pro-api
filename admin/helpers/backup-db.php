<?php
require __DIR__ . '/../../vendor/autoload.php'; 
use Core\Logger;

$databaseFile = __DIR__.'/../../db/db.sqlite';
$backupDir = __DIR__.'/../backups/db/';
$backupFile = $backupDir . 'backup_' . date('d-m-Y') . '.sqlite';

if (copy($databaseFile, $backupFile)) {
    Logger::log("Backup do banco de dados criado com sucesso: $backupDir\n", 'INFO');
    echo "Backup criado com sucesso: $backupFile\n";
} else {
    Logger::log("Falha ao criar o backup.\n", 'ERROR');
    echo "Falha ao criar o backup.\n";
}

