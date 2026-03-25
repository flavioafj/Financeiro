/**
 * Testes Automatizados do Frontend
 * 
 * Este script realiza testes automatizados no frontend
 * para validar interface, integração e responsividade.
 */

// Configurações de teste
const BASE_URL = 'http://localhost/Financas';
const API_BASE = 'http://localhost/Financas/api/routes/index.php';

// Resultados dos testes
const testResults = [];

// Função para registrar resultados
function logTest(testName, success, message = '', details = null) {
    testResults.push({
        name: testName,
        success: success,
        message: message,
        details: details,
        timestamp: new Date().toISOString()
    });
    
    const status = success ? '✅' : '❌';
    console.log(`${status} ${testName}: ${message}`);
    
    if (details) {
        console.log('Detalhes:', details);
    }
}

// Teste 1: Carregamento de Páginas
async function testPageLoading() {
    console.log('\n🧪 Teste 1: Carregamento de Páginas');
    
    const pages = [
        { name: 'Login', url: '/frontend/pages/login.html' },
        { name: 'Cadastro', url: '/frontend/pages/register.html' },
        { name: 'Dashboard', url: '/frontend/pages/dashboard.html' },
        { name: 'Transações', url: '/frontend/pages/transacoes.html' },
        { name: 'Categorias', url: '/frontend/pages/categorias.html' },
        { name: 'Relatórios', url: '/frontend/pages/relatorios.html' }
    ];
    
    for (const page of pages) {
        try {
            const response = await fetch(BASE_URL + page.url);
            if (response.ok) {
                logTest(`Carregamento ${page.name}`, true, 'Página carregada com sucesso');
            } else {
                logTest(`Carregamento ${page.name}`, false, `Erro ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            logTest(`Carregamento ${page.name}`, false, 'Falha ao carregar página', error.message);
        }
    }
}

// Teste 2: Validação de Formulários
function testFormValidation() {
    console.log('\n🧪 Teste 2: Validação de Formulários');
    
    // Testar se os formulários existem
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        logTest('Formulário de Login', true, 'Formulário encontrado no DOM');
        
        // Verificar campos obrigatórios
        const emailField = loginForm.querySelector('input[type="email"]');
        const passwordField = loginForm.querySelector('input[type="password"]');
        
        if (emailField && passwordField) {
            logTest('Campos do Login', true, 'Campos de email e senha encontrados');
        } else {
            logTest('Campos do Login', false, 'Campos de email ou senha não encontrados');
        }
    } else {
        logTest('Formulário de Login', false, 'Formulário não encontrado no DOM');
    }
    
    if (registerForm) {
        logTest('Formulário de Cadastro', true, 'Formulário encontrado no DOM');
        
        // Verificar campos obrigatórios
        const nameField = registerForm.querySelector('input[name="nome"]');
        const emailField = registerForm.querySelector('input[name="email"]');
        const passwordField = registerForm.querySelector('input[name="senha"]');
        
        if (nameField && emailField && passwordField) {
            logTest('Campos do Cadastro', true, 'Campos de nome, email e senha encontrados');
        } else {
            logTest('Campos do Cadastro', false, 'Campos obrigatórios não encontrados');
        }
    } else {
        logTest('Formulário de Cadastro', false, 'Formulário não encontrado no DOM');
    }
}

// Teste 3: Comunicação com API
async function testApiCommunication() {
    console.log('\n🧪 Teste 3: Comunicação com API');
    
    // Testar conexão com a API
    try {
        const response = await fetch(API_BASE + '/auth/user?user_id=1', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok || response.status === 400) { // 400 é esperado para user_id inválido
            logTest('Conexão com API', true, 'API está respondendo');
        } else {
            logTest('Conexão com API', false, `Erro ${response.status}: ${response.statusText}`);
        }
    } catch (error) {
        logTest('Conexão com API', false, 'Falha na conexão', error.message);
    }
    
    // Testar CORS
    try {
        const response = await fetch(API_BASE + '/categories?user_id=1', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.headers.get('Access-Control-Allow-Origin')) {
            logTest('CORS Headers', true, 'Headers CORS configurados corretamente');
        } else {
            logTest('CORS Headers', false, 'Headers CORS não encontrados');
        }
    } catch (error) {
        logTest('CORS Headers', false, 'Falha ao testar CORS', error.message);
    }
}

// Teste 4: Responsividade
function testResponsiveness() {
    console.log('\n🧪 Teste 4: Responsividade');
    
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    logTest('Viewport Detection', true, `Largura: ${viewportWidth}px, Altura: ${viewportHeight}px`);
    
    // Testar breakpoints comuns
    if (viewportWidth < 576) {
        logTest('Mobile Breakpoint', true, 'Dispositivo mobile detectado');
    } else if (viewportWidth < 768) {
        logTest('Tablet Breakpoint', true, 'Dispositivo tablet detectado');
    } else {
        logTest('Desktop Breakpoint', true, 'Dispositivo desktop detectado');
    }
    
    // Testar se o Bootstrap está carregado
    if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
        logTest('Bootstrap Loaded', true, 'Bootstrap jQuery functions disponíveis');
    } else {
        logTest('Bootstrap Loaded', false, 'Bootstrap não está carregado corretamente');
    }
}

// Teste 5: Manipulação de DOM
function testDOMManipulation() {
    console.log('\n🧪 Teste 5: Manipulação de DOM');
    
    // Testar se elementos principais existem
    const mainContainer = document.querySelector('.container');
    const navbar = document.querySelector('nav');
    const footer = document.querySelector('footer');
    
    if (mainContainer) {
        logTest('Container Principal', true, 'Container principal encontrado');
    } else {
        logTest('Container Principal', false, 'Container principal não encontrado');
    }
    
    if (navbar) {
        logTest('Navbar', true, 'Navbar encontrado');
    } else {
        logTest('Navbar', false, 'Navbar não encontrado');
    }
    
    if (footer) {
        logTest('Footer', true, 'Footer encontrado');
    } else {
        logTest('Footer', false, 'Footer não encontrado');
    }
}

// Teste 6: Event Listeners
function testEventListeners() {
    console.log('\n🧪 Teste 6: Event Listeners');
    
    // Testar se o app.js está carregado
    if (typeof apiRequest === 'function') {
        logTest('API Functions', true, 'Funções de API carregadas');
    } else {
        logTest('API Functions', false, 'Funções de API não encontradas');
    }
    
    // Testar se há listeners de formulário
    const forms = document.querySelectorAll('form');
    forms.forEach((form, index) => {
        const hasSubmitListener = form.onsubmit !== null;
        logTest(`Form ${index + 1} Listener`, hasSubmitListener, hasSubmitListener ? 'Listener de submit encontrado' : 'Listener de submit não encontrado');
    });
}

// Teste 7: Local Storage
function testLocalStorage() {
    console.log('\n🧪 Teste 7: Local Storage');
    
    try {
        // Testar se o localStorage está disponível
        localStorage.setItem('test', 'test');
        localStorage.removeItem('test');
        logTest('Local Storage', true, 'Local Storage disponível e funcional');
        
        // Testar se há dados de sessão
        const userData = localStorage.getItem('user');
        if (userData) {
            logTest('Session Data', true, 'Dados de sessão encontrados');
        } else {
            logTest('Session Data', false, 'Nenhum dado de sessão encontrado');
        }
    } catch (error) {
        logTest('Local Storage', false, 'Local Storage não disponível', error.message);
    }
}

// Função principal de testes
async function runAllTests() {
    console.log('🚀 Iniciando Testes Automatizados do Frontend');
    console.log('='.repeat(50));
    
    await testPageLoading();
    testFormValidation();
    await testApiCommunication();
    testResponsiveness();
    testDOMManipulation();
    testEventListeners();
    testLocalStorage();
    
    // Resumo dos testes
    console.log('\n📊 Resumo dos Testes');
    console.log('='.repeat(30));
    
    const totalTests = testResults.length;
    const successTests = testResults.filter(test => test.success).length;
    const failedTests = totalTests - successTests;
    
    console.log(`Total de Testes: ${totalTests}`);
    console.log(`Testes com Sucesso: ${successTests}`);
    console.log(`Testes com Falha: ${failedTests}`);
    
    if (failedTests > 0) {
        console.log('\n❌ Testes com Falhas:');
        testResults.filter(test => !test.success).forEach(test => {
            console.log(`- ${test.name}: ${test.message}`);
        });
    }
    
    console.log('\n✨ Testes concluídos!');
    
    // Retornar resultados para possível uso
    return testResults;
}

// Executar testes quando a página estiver carregada
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runAllTests);
} else {
    runAllTests();
}

// Exportar para uso em outros scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { runAllTests, testResults };
}