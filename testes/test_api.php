<?php
/**
 * Testes Automatizados da API REST
 * 
 * Este script realiza testes automatizados em todos os endpoints da API
 * para validar funcionalidades e identificar possíveis problemas.
 */

// Configurações de teste
$base_url = 'http://localhost/Financas/api/routes/index.php';
$test_results = [];

echo "<h1>🧪 Testes Automatizados da API REST</h1>";
echo "<hr>";

// Função para fazer requisições HTTP
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $http_code,
        'error' => curl_error($ch)
    ];
}

// Função para registrar resultados
function logTest($test_name, $success, $message = '', $response = null) {
    global $test_results;
    
    $test_results[] = [
        'name' => $test_name,
        'success' => $success,
        'message' => $message,
        'response' => $response
    ];
    
    $status = $success ? '✅' : '❌';
    echo "<p><strong>{$status} {$test_name}:</strong> {$message}</p>";
    
    if ($response && !$success) {
        echo "<pre style='background: #f5f5f5; padding: 10px; border-left: 3px solid red;'>" . htmlspecialchars($response) . "</pre>";
    }
}

// Teste 1: Conexão com a API
echo "<h2>1. Teste de Conexão com a API</h2>";
$response = makeRequest($base_url . '/auth/user?user_id=1');
if ($response['http_code'] == 400) {
    logTest('Conexão com API', true, 'API está respondendo corretamente');
} else {
    logTest('Conexão com API', false, 'API não está respondendo', $response['response']);
}

// Teste 2: Cadastro de Usuário
echo "<h2>2. Teste de Cadastro de Usuário</h2>";
$test_user = [
    'email' => 'test_' . time() . '@example.com',
    'senha' => 'test123',
    'nome' => 'Usuário de Teste'
];

$response = makeRequest($base_url . '/auth/register', 'POST', $test_user);
$result = json_decode($response['response'], true);

if ($response['http_code'] == 201 && isset($result['status']) && $result['status'] == 'success') {
    logTest('Cadastro de Usuário', true, 'Usuário cadastrado com sucesso');
    $user_id = null; // Não temos o ID do usuário retornado
} else {
    logTest('Cadastro de Usuário', false, 'Falha no cadastro', $response['response']);
    $user_id = null;
}

// Teste 3: Login de Usuário
echo "<h2>3. Teste de Login de Usuário</h2>";
if ($user_id === null) {
    // Tentar login com credenciais de teste
    $login_data = [
        'email' => $test_user['email'],
        'senha' => $test_user['senha']
    ];
    
    $response = makeRequest($base_url . '/auth/login', 'POST', $login_data);
    $result = json_decode($response['response'], true);
    
    if ($response['http_code'] == 200 && isset($result['status']) && $result['status'] == 'success') {
        logTest('Login de Usuário', true, 'Login realizado com sucesso');
        $user_id = $result['data']['id'] ?? 1; // Usar ID 1 como fallback
    } else {
        logTest('Login de Usuário', false, 'Falha no login', $response['response']);
        $user_id = 1; // Usar ID 1 como fallback para testes
    }
}

// Teste 4: Listagem de Categorias
echo "<h2>4. Teste de Listagem de Categorias</h2>";
$response = makeRequest($base_url . '/categories?user_id=' . $user_id);
$result = json_decode($response['response'], true);

if ($response['http_code'] == 200 && isset($result['status']) && $result['status'] == 'success') {
    $categorias = $result['data'];
    $total_categorias = count($categorias);
    logTest('Listagem de Categorias', true, "Encontradas {$total_categorias} categorias");
    
    // Verificar categorias padrão
    $categorias_receita = array_filter($categorias, function($cat) {
        return $cat['tipo'] === 'income';
    });
    $categorias_despesa = array_filter($categorias, function($cat) {
        return $cat['tipo'] === 'expense';
    });
    
    logTest('Categorias por Tipo', true, "Receitas: " . count($categorias_receita) . ", Despesas: " . count($categorias_despesa));
} else {
    logTest('Listagem de Categorias', false, 'Falha na listagem', $response['response']);
}

// Teste 5: Registro de Transação
echo "<h2>5. Teste de Registro de Transação</h2>";
if ($user_id) {
    $transacao_data = [
        'categoria_id' => 1,
        'valor' => 100.50,
        'tipo' => 'income',
        'data' => date('Y-m-d'),
        'descricao' => 'Teste de transação'
    ];
    
    $response = makeRequest($base_url . '/transactions?user_id=' . $user_id, 'POST', $transacao_data);
    $result = json_decode($response['response'], true);
    
    if ($response['http_code'] == 201 && isset($result['status']) && $result['status'] == 'success') {
        logTest('Registro de Transação', true, 'Transação registrada com sucesso');
    } else {
        logTest('Registro de Transação', false, 'Falha no registro', $response['response']);
    }
}

// Teste 6: Dashboard (Resumo Financeiro)
echo "<h2>6. Teste de Dashboard</h2>";
$response = makeRequest($base_url . '/dashboard?user_id=' . $user_id);
$result = json_decode($response['response'], true);

if ($response['http_code'] == 200 && isset($result['status']) && $result['status'] == 'success') {
    $resumo = $result['data'];
    logTest('Dashboard', true, "Total Receitas: R$ {$resumo['total_receitas']}, Total Despesas: R$ {$resumo['total_despesas']}");
} else {
    logTest('Dashboard', false, 'Falha no dashboard', $response['response']);
}

// Teste 7: Relatórios
echo "<h2>7. Teste de Relatórios</h2>";
$response = makeRequest($base_url . '/reports?user_id=' . $user_id);
$result = json_decode($response['response'], true);

if ($response['http_code'] == 200 && isset($result['status']) && $result['status'] == 'success') {
    $relatorios = $result['data'];
    $total_categorias = count($relatorios);
    logTest('Relatórios', true, "Relatório gerado para {$total_categorias} categorias");
} else {
    logTest('Relatórios', false, 'Falha nos relatórios', $response['response']);
}

// Resumo dos Testes
echo "<hr><h2>📊 Resumo dos Testes</h2>";
$total_testes = count($test_results);
$testes_sucesso = count(array_filter($test_results, function($test) { return $test['success']; }));
$testes_falha = $total_testes - $testes_sucesso;

echo "<p><strong>Total de Testes:</strong> {$total_testes}</p>";
echo "<p><strong>Testes com Sucesso:</strong> {$testes_sucesso}</p>";
echo "<p><strong>Testes com Falha:</strong> {$testes_falha}</p>";

if ($testes_falha > 0) {
    echo "<h3>❌ Testes com Falhas:</h3>";
    foreach ($test_results as $test) {
        if (!$test['success']) {
            echo "<p><strong>{$test['name']}:</strong> {$test['message']}</p>";
        }
    }
}

echo "<hr><p><em>Testes realizados em " . date('d/m/Y H:i:s') . "</em></p>";
?>