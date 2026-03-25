/**
 * Funções de API corrigidas
 */

// Função de requisição com tratamento de erros melhorado
async function apiRequest(url, method = 'GET', data = null) {
    try {
        const config = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        if (data) {
            config.body = JSON.stringify(data);
        }

        const response = await fetch(url, config);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Erro na requisição:', error);
        throw error;
    }
}

// Função de validação de formulário
function validarFormulario(form) {
    const errors = [];
    
    // Validar email
    const email = form.querySelector('input[type="email"]');
    if (email && !validarEmail(email.value)) {
        errors.push('Email inválido');
    }
    
    // Validar valor da transação
    const valor = form.querySelector('input[name="valor"]');
    if (valor && !validarValor(parseFloat(valor.value))) {
        errors.push('Valor deve ser maior que zero');
    }
    
    // Validar tipo de transação
    const tipo = form.querySelector('select[name="tipo"]');
    if (tipo && !validarTipoTransacao(tipo.value)) {
        errors.push('Tipo de transação inválido');
    }
    
    // Validar data
    const data = form.querySelector('input[name="data"]');
    if (data && !validarData(data.value)) {
        errors.push('Data inválida');
    }
    
    return errors;
}

// Função de validação de email
function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Função de validação de valor
function validarValor(valor) {
    return !isNaN(valor) && valor > 0;
}

// Função de validação de tipo de transação
function validarTipoTransacao(tipo) {
    return ['income', 'expense'].includes(tipo);
}

// Função de validação de data
function validarData(data) {
    const date = new Date(data);
    return date instanceof Date && !isNaN(date);
}

// Função de feedback visual
function mostrarFeedback(mensagem, tipo = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remover alerta após 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Função de registro de usuário
async function register() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const errors = validarFormulario(form);
    
    if (errors.length > 0) {
        errors.forEach(error => {
            mostrarFeedback(error, 'danger');
        });
        return;
    }

    const formData = new FormData(form);
    const data = {
        email: formData.get('email'),
        senha: formData.get('senha'),
        nome: formData.get('nome')
    };

    try {
        const result = await apiRequest('/Financas/api/routes/index.php/auth/register', 'POST', data);
        
        if (result.status === 'success') {
            mostrarFeedback('Cadastro realizado com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            mostrarFeedback(result.message || 'Erro no cadastro', 'danger');
        }
    } catch (error) {
        mostrarFeedback('Erro de conexão. Tente novamente.', 'danger');
    }
}

// Função de login
async function login() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    const formData = new FormData(form);
    const data = {
        email: formData.get('email'),
        senha: formData.get('senha')
    };

    try {
        const result = await apiRequest('/Financas/api/routes/index.php/auth/login', 'POST', data);
        
        if (result.status === 'success') {
            localStorage.setItem('user', JSON.stringify(result.data));
            mostrarFeedback('Login realizado com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 2000);
        } else {
            mostrarFeedback(result.message || 'Credenciais inválidas', 'danger');
        }
    } catch (error) {
        mostrarFeedback('Erro de conexão. Tente novamente.', 'danger');
    }
}

// Função de registro de transação
async function registrarTransacao() {
    const form = document.getElementById('transacaoForm');
    if (!form) return;

    const errors = validarFormulario(form);
    
    if (errors.length > 0) {
        errors.forEach(error => {
            mostrarFeedback(error, 'danger');
        });
        return;
    }

    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        mostrarFeedback('Faça login para registrar transações', 'warning');
        return;
    }

    const formData = new FormData(form);
    const data = {
        categoria_id: formData.get('categoria_id'),
        valor: parseFloat(formData.get('valor')),
        tipo: formData.get('tipo'),
        data: formData.get('data'),
        descricao: formData.get('descricao')
    };

    try {
        const result = await apiRequest(`/Financas/api/routes/index.php/transactions?user_id=${user.id}`, 'POST', data);
        
        if (result.status === 'success') {
            mostrarFeedback('Transação registrada com sucesso!', 'success');
            form.reset();
            // Atualizar lista de transações
            carregarTransacoes(user.id);
        } else {
            mostrarFeedback(result.message || 'Erro ao registrar transação', 'danger');
        }
    } catch (error) {
        mostrarFeedback('Erro de conexão. Tente novamente.', 'danger');
    }
}

// Função de carregamento de transações
async function carregarTransacoes(usuario_id) {
    try {
        const result = await apiRequest(`/Financas/api/routes/index.php/transactions?user_id=${usuario_id}`);
        
        if (result.status === 'success') {
            exibirTransacoes(result.data);
        } else {
            mostrarFeedback('Erro ao carregar transações', 'danger');
        }
    } catch (error) {
        mostrarFeedback('Erro de conexão. Tente novamente.', 'danger');
    }
}

// Função de exibição de transações
function exibirTransacoes(transacoes) {
    const container = document.getElementById('transacoes-container');
    if (!container) return;

    if (transacoes.length === 0) {
        container.innerHTML = '<p class="text-muted">Nenhuma transação encontrada.</p>';
        return;
    }

    const html = transacoes.map(transacao => `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>${transacao.categoria_nome}</strong>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-${transacao.tipo === 'income' ? 'success' : 'danger'}">
                            ${transacao.tipo === 'income' ? 'Receita' : 'Despesa'}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <strong>R$ ${transacao.valor.toFixed(2)}</strong>
                    </div>
                    <div class="col-md-3">
                        ${transacao.descricao || '-'}
                    </div>
                    <div class="col-md-2">
                        ${transacao.data}
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    container.innerHTML = html;
}

// Event listener para formulários
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners para formulários de cadastro e login
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            register();
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            login();
        });
    }

    // Event listener para formulário de transação
    const transacaoForm = document.getElementById('transacaoForm');
    if (transacaoForm) {
        transacaoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            registrarTransacao();
        });
    }

    // Carregar transações na página de transações
    if (window.location.pathname.includes('transacoes.html')) {
        const user = JSON.parse(localStorage.getItem('user'));
        if (user) {
            carregarTransacoes(user.id);
        }
    }
});