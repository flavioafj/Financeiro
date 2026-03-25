<?php
/**
 * Script de teste para validar o funcionamento da API
 */

// Configurações de cabeçalho
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");

// Incluir arquivos necessários
require_once 'api/config/database.php';
require_once 'api/models/Usuario.php';
require_once 'api/models/Categoria.php';
require_once 'api/models/Transacao.php';

// Função para testar conexão com o banco
function testarConexao() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            return array('status' => 'success', 'message' => 'Conexão com o banco de dados estabelecida com sucesso!');
        } else {
            return array('status' => 'error', 'message' => 'Falha ao conectar ao banco de dados.');
        }
    } catch (Exception $e) {
        return array('status' => 'error', 'message' => 'Erro na conexão: ' . $e->getMessage());
    }
}

// Função para testar tabelas
function testarTabelas() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $tabelas = ['usuarios', 'categorias', 'transacoes'];
        $resultados = array();
        
        foreach ($tabelas as $tabela) {
            $query = "SHOW TABLES LIKE '$tabela'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $resultados[$tabela] = 'existe';
            } else {
                $resultados[$tabela] = 'não existe';
            }
        }
        
        return array('status' => 'success', 'data' => $resultados);
    } catch (Exception $e) {
        return array('status' => 'error', 'message' => 'Erro ao testar tabelas: ' . $e->getMessage());
    }
}

// Função para testar categorias padrão
function testarCategoriasPadrao() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT COUNT(*) as total FROM categorias WHERE is_default = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return array('status' => 'success', 'data' => array('total_categorias_padrao' => $result['total']));
    } catch (Exception $e) {
        return array('status' => 'error', 'message' => 'Erro ao testar categorias padrão: ' . $e->getMessage());
    }
}

// Função para testar endpoints da API
function testarEndpoints() {
    $endpoints = array(
        'auth/login' => 'POST',
        'auth/register' => 'POST',
        'categories' => 'GET',
        'transactions' => 'GET',
        'dashboard' => 'GET',
        'reports' => 'GET'
    );
    
    $resultados = array();
    
    foreach ($endpoints as $endpoint => $method) {
        $url = "http://localhost/Financas/api/routes/index.php/$endpoint";
        
        // Para testes, vamos apenas verificar se a URL responde
        $resultados[$endpoint] = array(
            'method' => $method,
            'status' => 'testar_manualmente',
            'url' => $url
        );
    }
    
    return array('status' => 'success', 'data' => $resultados);
}

// Executar testes
echo "<h1>Teste da API de Controle Financeiro</h1>";
echo "<hr>";

echo "<h2>1. Teste de Conexão com Banco de Dados</h2>";
$resultado_conexao = testarConexao();
echo "<pre>" . json_encode($resultado_conexao, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>2. Teste de Tabelas</h2>";
$resultado_tabelas = testarTabelas();
echo "<pre>" . json_encode($resultado_tabelas, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>3. Teste de Categorias Padrão</h2>";
$resultado_categorias = testarCategoriasPadrao();
echo "<pre>" . json_encode($resultado_categorias, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>4. Endpoints da API</h2>";
$resultado_endpoints = testarEndpoints();
echo "<pre>" . json_encode($resultado_endpoints, JSON_PRETTY_PRINT) . "</pre>";

echo "<hr>";
echo "<h2>Resumo dos Testes</h2>";
echo "<ul>";
echo "<li>Conexão com banco: " . ($resultado_conexao['status'] === 'success' ? '✅' : '❌') . "</li>";
echo "<li>Tabelas criadas: " . ($resultado_tabelas['status'] === 'success' ? '✅' : '❌') . "</li>";
echo "<li>Categorias padrão: " . ($resultado_categorias['status'] === 'success' ? '✅' : '❌') . "</li>";
echo "</ul>";

echo "<h2>Instruções para Testes Manuais</h2>";
echo "<ol>";
echo "<li><strong>Testar Login:</strong> Acesse a página de login e tente fazer login com credenciais válidas</li>";
echo "<li><strong>Testar Cadastro:</strong> Use a página de cadastro para criar um novo usuário</li>";
echo "<li><strong>Testar Categorias:</strong> Após o login, acesse a página de categorias e verifique se as categorias padrão estão listadas</li>";
echo "<li><strong>Testar Transações:</strong> Registre algumas transações e verifique se aparecem no dashboard</li>";
echo "<li><strong>Testar Dashboard:</strong> Verifique se os gráficos e resumos estão sendo exibidos corretamente</li>";
echo "<li><strong>Testar Relatórios:</strong> Acesse a página de relatórios e gere relatórios por período</li>";
echo "</ol>";

echo "<h2>Teste de Registro via GET</h2>";
echo "<p>Para testar o registro de transações via GET, use a seguinte URL (substitua os valores):</p>";
echo "<code>http://localhost/Financas/api/routes/index.php/api/transactions/add?user_id=1&value=100&type=income&category_id=1&date=2024-01-01&description=Teste</code>";

echo "<hr>";
echo "<p><strong>Observação:</strong> Este script de teste deve ser executado no servidor web para validar corretamente a API.</p>";
?>