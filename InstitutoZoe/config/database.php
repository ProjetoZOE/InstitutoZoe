<?php
/**
 * Configuração de Banco de Dados
 * Utiliza PDO para conexão segura com MySQL
 */

// Configurações de conexão
define('DB_HOST', 'localhost');
define('DB_NAME', 'teste_institutozoe');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// DSN (Data Source Name)
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// Opções PDO
$pdo_options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
);

// Conectar ao banco de dados
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
} catch (PDOException $e) {
    // Log de erro (em produção, não exibir detalhes)
    error_log('Erro de conexão com banco de dados: ' . $e->getMessage());
    die('Erro ao conectar com o banco de dados. Tente novamente mais tarde.');
}

/**
 * Função auxiliar para executar queries com prepared statements
 * 
 * Aceita dois formatos:
 * - executarQuery($sql, $params) - usa variável global $pdo
 * - executarQuery($pdo, $sql, $params) - usa PDO passado como parâmetro (retrocompatibilidade)
 * 
 * @return PDOStatement|false
 */
function executarQuery($sql, $params = array(), $pdo_param = null) {
    global $pdo;
    
    // Se o primeiro parâmetro é um objeto PDO, então foi usado o formato antigo
    if ($sql instanceof PDO) {
        $pdo_param = $sql;
        $sql = $params;
        $params = isset($pdo_param) ? $pdo_param : array();
        // Corrigir os parâmetros
        $params = func_get_arg(2) ?? array();
    }
    
    // Usar PDO passado ou o global
    $connection = $pdo_param ?? $pdo;
    
    try {
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log('Erro na query: ' . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter uma linha do resultado
 */
function obterUmaLinha($sql, $params = array(), $pdo_param = null) {
    // Se o primeiro parâmetro é um objeto PDO, então foi usado o formato antigo
    if ($sql instanceof PDO) {
        $pdo_param = $sql;
        $sql = $params;
        $params = func_get_arg(2) ?? array();
    }
    
    $stmt = executarQuery($sql, $params, $pdo_param);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Função para obter todas as linhas do resultado
 */
function obterTodas($sql, $params = array(), $pdo_param = null) {
    // Se o primeiro parâmetro é um objeto PDO, então foi usado o formato antigo
    if ($sql instanceof PDO) {
        $pdo_param = $sql;
        $sql = $params;
        $params = func_get_arg(2) ?? array();
    }
    
    $stmt = executarQuery($sql, $params, $pdo_param);
    return $stmt ? $stmt->fetchAll() : array();
}

?>
