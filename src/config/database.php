<?php
/**
 * Configuração do Banco de Dados
 */

// Verifica se a extensão PDO está carregada
if (!extension_loaded('pdo')) {
    die('Erro: Extensão PDO não está habilitada no PHP.');
}

// Configurações
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'cakee_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];

try {
    // Cria a conexão PDO
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);

    // Testa a conexão
    $db->query("SELECT 1")->fetch();

} catch (PDOException $e) {
    // Log detalhado
    error_log('Erro de conexão PDO: ' . $e->getMessage());
    
    // Mensagem amigável
    die('Não foi possível conectar ao banco de dados. Por favor, tente novamente mais tarde.');
}

// Retorna a conexão
return $db;