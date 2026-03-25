<?php
/**
 * Script de Limpeza de Cache e Cache Busting
 * 
 * Este script resolve o problema de cache do navegador
 * e garante que os arquivos corretos sejam carregados.
 */

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Limpeza de Cache - Sistema Financeiro</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }";
echo ".container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }";
echo "h2 { color: #34495e; margin-top: 30px; }";
echo ".step { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3498db; }";
echo ".success { background: #d4edda; border-left-color: #28a745; }";
echo ".warning { background: #fff3cd; border-left-color: #ffc107; }";
echo ".error { background: #f8d7da; border-left-color: #dc3545; }";
echo "button { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px; }";
echo "button:hover { background: #2980b9; }";
echo "button.success { background: #28a745; }";
echo "button.success:hover { background: #218838; }";
echo "a { color: #3498db; text-decoration: none; }";
echo "a:hover { text-decoration: underline; }";
echo "code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-family: monospace; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🔧 Limpeza de Cache - Sistema Financeiro</h1>";

// Verificar se os arquivos corretos foram substituídos
$arquivos_verificados = [];

// Verificar arquivos PHP
$arquivos_php = [
    'api/models/Usuario.php' => 'filter_var',
    'api/models/Transacao.php' => 'validarValor',
    'api/controllers/TransacaoController.php' => 'validarTipoTransacao'
];

foreach ($arquivos_php as $arquivo => $termo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        $conteudo = file_get_contents($caminho);
        if (strpos($conteudo, $termo) !== false) {
            $arquivos_verificados[] = "<div class='step success'><strong>✅ $arquivo</strong> - Arquivo corrigido encontrado</div>";
        } else {
            $arquivos_verificados[] = "<div class='step error'><strong>❌ $arquivo</strong> - Arquivo não contém correções</div>";
        }
    } else {
        $arquivos_verificados[] = "<div class='step error'><strong>❌ $arquivo</strong> - Arquivo não encontrado</div>";
    }
}

// Verificar arquivo JavaScript
$arquivo_js = __DIR__ . '/frontend/js/app.js';
if (file_exists($arquivo_js)) {
    $conteudo_js = file_get_contents($arquivo_js);
    if (strpos($conteudo_js, 'validarFormulario') !== false) {
        $arquivos_verificados[] = "<div class='step success'><strong>✅ frontend/js/app.js</strong> - Arquivo corrigido encontrado</div>";
    } else {
        $arquivos_verificados[] = "<div class='step error'><strong>❌ frontend/js/app.js</strong> - Arquivo não contém correções</div>";
    }
} else {
    $arquivos_verificados[] = "<div class='step error'><strong>❌ frontend/js/app.js</strong> - Arquivo não encontrado</div>";
}

echo "<h2>📋 Verificação de Arquivos</h2>";
foreach ($arquivos_verificados as $verificacao) {
    echo $verificacao;
}

echo "<h2>🚀 Soluções para o Problema de Cache</h2>";

echo "<div class='step'>";
echo "<h3>1. Limpeza Imediata de Cache (Recomendado)</h3>";
echo "<p><strong>Para Chrome/Edge:</strong> Pressione <code>Ctrl + Shift + R</code></p>";
echo "<p><strong>Para Firefox:</strong> Pressione <code>Ctrl + Shift + R</code></p>";
echo "<p><strong>Para Safari:</strong> Pressione <code>Cmd + Shift + R</code></p>";
echo "<p><strong>Para limpar todo o cache:</strong> Pressione <code>Ctrl + Shift + Delete</code> e selecione 'Cache'</p>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>2. Desativar Cache no Navegador (Temporário)</h3>";
echo "<p><strong>Chrome/Edge:</strong> Abra o DevTools (F12) → Network → Marque 'Disable cache'</p>";
echo "<p><strong>Firefox:</strong> Abra o DevTools (F12) → Network → Marque 'Disable cache'</p>";
echo "</div>";

echo "<div class='step warning'>";
echo "<h3>3. Cache Busting Automático (Solução Definitiva)</h3>";
echo "<p>Este script criará uma versão com cache busting do seu JavaScript.</p>";
echo "<button class='success' onclick='aplicarCacheBusting()'>Aplicar Cache Busting</button>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>4. Acessar Páginas Específicas</h3>";
echo "<p><a href='frontend/pages/register.html' target='_blank'>Cadastro de Usuário</a></p>";
echo "<p><a href='frontend/pages/login.html' target='_blank'>Login</a></p>";
echo "<p><a href='frontend/pages/transacoes.html' target='_blank'>Transações</a></p>";
echo "</div>";

echo "<h2>✅ Próximos Passos</h2>";
echo "<ol>";
echo "<li><strong>Limpe o cache do navegador</strong> usando as teclas acima</li>";
echo "<li><strong>Feche e reabra o navegador</strong> para garantir a limpeza completa</li>";
echo "<li><strong>Acesse as páginas</strong> e verifique se as correções estão funcionando</li>";
echo "<li><strong>Teste as validações</strong> - tente registrar transações com valores negativos, emails inválidos, etc.</li>";
echo "</ol>";

echo "<h2>🔍 Testes de Segurança</h2>";
echo "<p><a href='testes/test_security.php' target='_blank'>Executar Testes de Segurança</a></p>";
echo "<p><a href='testes/test_api.php' target='_blank'>Testar API</a></p>";

echo "</div>";

echo "<script>";
echo "function aplicarCacheBusting() {";
echo "    // Criar um timestamp único";
echo "    const timestamp = new Date().getTime();";
echo "    ";
echo "    // Modificar o link do CSS e JS para incluir o timestamp";
echo "    const links = document.querySelectorAll('link[rel=\"stylesheet\"]');";
echo "    const scripts = document.querySelectorAll('script[src]');";
echo "    ";
echo "    links.forEach(link => {";
echo "        if (link.href.includes('.css')) {";
echo "            link.href = link.href.split('?')[0] + '?v=' + timestamp;";
echo "        }";
echo "    });";
echo "    ";
echo "    scripts.forEach(script => {";
echo "        if (script.src.includes('.js')) {";
echo "            script.src = script.src.split('?')[0] + '?v=' + timestamp;";
echo "        }";
echo "    });";
echo "    ";
echo "    alert('Cache busting aplicado! Recarregue a página para ver as mudanças.');";
echo "}";
echo "</script>";

echo "</body>";
echo "</html>";
?>