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

// Função para obter usuário logado
function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// Função para obter categorias
async function getCategorias(user_id) {
    try {
        const result = await apiRequest(`/Financas/api/routes/index.php/categories?user_id=${user_id}`);
        
        if (result.status === 'success') {
            return result.data;
        } else {
            throw new Error(result.message || 'Erro ao obter categorias');
        }
    } catch (error) {
        console.error('Erro ao obter categorias:', error);
        throw error;
    }
}

// Função para carregar categorias no select
async function loadCategoriasSelect(selectElement, user_id) {
    try {
        const categorias = await getCategorias(user_id);
        selectElement.innerHTML = '<option value="">Selecione uma categoria</option>';
        
        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.id;
            option.textContent = categoria.nome;
            selectElement.appendChild(option);
        });
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
        selectElement.innerHTML = '<option value="">Erro ao carregar categorias</option>';
    }
}

// Função para formatar moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
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

// Função de registro de transação (compatível com formulário)
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

// Função de criação de transação (compatível com chamada direta)
async function createTransacao(user_id, categoria_id, valor, tipo, data, descricao = '') {
    if (!user_id || !categoria_id || !valor || !tipo || !data) {
        throw new Error('Dados incompletos. É necessário user_id, categoria_id, valor, tipo e data.');
    }

    if (valor <= 0) {
        throw new Error('Valor deve ser maior que zero.');
    }

    if (!['income', 'expense'].includes(tipo)) {
        throw new Error('Tipo de transação inválido.');
    }

    const dataObj = {
        categoria_id: categoria_id,
        valor: parseFloat(valor),
        tipo: tipo,
        data: data,
        descricao: descricao
    };

    try {
        const result = await apiRequest(`/Financas/api/routes/index.php/transactions?user_id=${user_id}`, 'POST', dataObj);
        
        if (result.status === 'success') {
            return true;
        } else {
            throw new Error(result.message || 'Erro ao criar transação');
        }
    } catch (error) {
        console.error('Erro ao criar transação:', error);
        throw error;
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

// Função de obtenção de transações (compatível com chamada direta)
async function getTransacoes(user_id, limit = 1000, offset = 0) {
    try {
        const result = await apiRequest(`/Financas/api/routes/index.php/transactions?user_id=${user_id}&limit=${limit}&offset=${offset}`);
        
        if (result.status === 'success') {
            return result.data;
        } else {
            throw new Error(result.message || 'Erro ao obter transações');
        }
    } catch (error) {
        console.error('Erro ao obter transações:', error);
        throw error;
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

// Função para carregar tabela de transações (compatível com transacoes.html)
function loadTransacoesTable(transacoesFiltradas) {
    const tbody = document.getElementById('transacoes-table').querySelector('tbody');
    tbody.innerHTML = '';

    if (transacoesFiltradas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhuma transação encontrada</td></tr>';
        return;
    }

    // Paginação
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const transacoesPaginadas = transacoesFiltradas.slice(startIndex, endIndex);

    transacoesPaginadas.forEach(transacao => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${transacao.data}</td>
            <td>${transacao.categoria_nome}</td>
            <td>
                <span class="badge ${transacao.tipo === 'income' ? 'bg-success' : 'bg-danger'}">
                    ${transacao.tipo === 'income' ? 'Receita' : 'Despesa'}
                </span>
            </td>
            <td class="${transacao.tipo === 'income' ? 'text-success' : 'text-danger'}">
                ${transacao.tipo === 'income' ? '+' : '-'} ${formatCurrency(Math.abs(transacao.valor))}
            </td>
            <td>${transacao.descricao || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editarTransacao(${transacao.id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="excluirTransacao(${transacao.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Função para editar transação
async function editarTransacao(id) {
    try {
        const user = getUser();
        if (!user) {
            mostrarFeedback('Faça login para editar transações', 'warning');
            return;
        }

        // Buscar detalhes da transação
        const result = await apiRequest(`/Financas/api/routes/index.php/transactions/${id}?user_id=${user.id}`);
        
        if (result.status === 'success') {
            const transacao = result.data;
            
            // Trocar para o formulário de edição
            document.getElementById('form-transacao-criar').style.display = 'none';
            document.getElementById('form-transacao-editar').style.display = 'block';
            
            // Preencher o formulário de edição com os dados da transação
            document.getElementById('transacao-tipo-editar').value = transacao.tipo;
            document.getElementById('transacao-categoria-editar').value = transacao.categoria_id;
            document.getElementById('transacao-valor-editar').value = transacao.valor;
            document.getElementById('transacao-data-editar').value = transacao.data;
            document.getElementById('transacao-descricao-editar').value = transacao.descricao || '';
            
            // Alterar o título do modal
            const modalTitle = document.querySelector('#modal-transacao .modal-title');
            modalTitle.innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Transação';
            
            // Alterar o texto do botão salvar
            const btnSalvar = document.getElementById('btn-salvar-transacao');
            btnSalvar.innerHTML = '<i class="bi bi-save me-2"></i>Atualizar';
            btnSalvar.className = 'btn btn-success';
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modal-transacao'));
            modal.show();
            
            // Salvar alterações - usar onclick personalizado para edição
            btnSalvar.onclick = async function(e) {
                e.preventDefault(); // Prevenir comportamento padrão do botão
                
                const tipo = document.getElementById('transacao-tipo-editar').value;
                const categoriaId = document.getElementById('transacao-categoria-editar').value;
                const valor = document.getElementById('transacao-valor-editar').value;
                const data = document.getElementById('transacao-data-editar').value;
                const descricao = document.getElementById('transacao-descricao-editar').value;
                
                if (!tipo || !categoriaId || !valor || !data) {
                    mostrarFeedback('Por favor, preencha todos os campos obrigatórios.', 'warning');
                    return;
                }
                
                if (parseFloat(valor) <= 0) {
                    mostrarFeedback('O valor deve ser maior que zero.', 'warning');
                    return;
                }
                
                try {
                    const updateData = {
                        categoria_id: categoriaId,
                        valor: parseFloat(valor),
                        tipo: tipo,
                        data: data,
                        descricao: descricao
                    };
                    
                    const updateResult = await apiRequest(`/Financas/api/routes/index.php/transactions/${id}?user_id=${user.id}`, 'PUT', updateData);
                    
                    if (updateResult.status === 'success') {
                        mostrarFeedback('Transação atualizada com sucesso!', 'success');
                        modal.hide();
                        
                        // Recarregar transações
                        transacoes = await getTransacoes(user.id, 1000, 0);
                        aplicarFiltros();
                        
                        // Restaurar o modal para nova transação
                        restaurarModalNovaTransacao();
                    } else {
                        mostrarFeedback(updateResult.message || 'Erro ao atualizar transação', 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao atualizar transação:', error);
                    mostrarFeedback('Erro ao atualizar transação. Tente novamente.', 'danger');
                }
            };
        } else {
            mostrarFeedback(result.message || 'Erro ao buscar transação', 'danger');
        }
    } catch (error) {
        console.error('Erro ao editar transação:', error);
        mostrarFeedback('Erro ao editar transação. Tente novamente.', 'danger');
    }
}

// Função para excluir transação
async function excluirTransacao(id) {
    if (!confirm('Tem certeza que deseja excluir esta transação?')) {
        return;
    }

    try {
        const user = getUser();
        if (!user) {
            mostrarFeedback('Faça login para excluir transações', 'warning');
            return;
        }

        const result = await apiRequest(`/Financas/api/routes/index.php/transactions/${id}?user_id=${user.id}`, 'DELETE');
        
        if (result.status === 'success') {
            mostrarFeedback('Transação excluída com sucesso!', 'success');
            // Recarregar transações
            transacoes = await getTransacoes(user.id, 1000, 0);
            aplicarFiltros();
        } else {
            mostrarFeedback(result.message || 'Erro ao excluir transação', 'danger');
        }
    } catch (error) {
        console.error('Erro ao excluir transação:', error);
        mostrarFeedback('Erro ao excluir transação. Tente novamente.', 'danger');
    }
}

// Função para restaurar o modal para nova transação
function restaurarModalNovaTransacao() {
    // Trocar para o formulário de criação
    document.getElementById('form-transacao-criar').style.display = 'block';
    document.getElementById('form-transacao-editar').style.display = 'none';
    
    // Restaurar o título do modal
    const modalTitle = document.querySelector('#modal-transacao .modal-title');
    modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nova Transação';
    
    // Restaurar o texto e classe do botão salvar
    const btnSalvar = document.getElementById('btn-salvar-transacao');
    btnSalvar.innerHTML = '<i class="bi bi-save me-2"></i>Salvar';
    btnSalvar.className = 'btn btn-primary';
    
    // Limpar ambos os formulários
    document.getElementById('form-transacao-criar').reset();
    document.getElementById('form-transacao-editar').reset();
    
    // Remover o onclick personalizado
    btnSalvar.onclick = null;
}

// Função para atualizar dashboard
async function updateDashboard(user_id) {
    try {
        const transacoes = await getTransacoes(user_id, 1000, 0);
        
        // Calcular totais
        const totalReceitas = transacoes
            .filter(t => t.tipo === 'income')
            .reduce((sum, t) => sum + parseFloat(t.valor), 0);
        
        const totalDespesas = transacoes
            .filter(t => t.tipo === 'expense')
            .reduce((sum, t) => sum + parseFloat(t.valor), 0);
        
        const saldo = totalReceitas - totalDespesas;
        
        // Atualizar cards de resumo
        const totalReceitasElement = document.getElementById('total-receitas');
        const totalDespesasElement = document.getElementById('total-despesas');
        const saldoElement = document.getElementById('saldo-atual');
        const totalTransacoesElement = document.getElementById('total-transacoes');
        
        if (totalReceitasElement) {
            totalReceitasElement.textContent = formatCurrency(totalReceitas);
        }
        
        if (totalDespesasElement) {
            totalDespesasElement.textContent = formatCurrency(totalDespesas);
        }
        
        if (saldoElement) {
            saldoElement.textContent = formatCurrency(saldo);
            // Atualizar classe CSS baseado no saldo
            saldoElement.className = 'dashboard-value ' + (saldo >= 0 ? 'text-success' : 'text-danger');
        }
        
        if (totalTransacoesElement) {
            totalTransacoesElement.textContent = transacoes.length;
        }
        
        return {
            totalReceitas,
            totalDespesas,
            saldo,
            totalTransacoes: transacoes.length
        };
    } catch (error) {
        console.error('Erro ao atualizar dashboard:', error);
        throw error;
    }
}

// Função para gerar gráfico de categorias
async function generateChart(user_id) {
    try {
        const transacoes = await getTransacoes(user_id, 1000, 0);
        
        // Agrupar por categoria
        const categorias = {};
        transacoes.forEach(t => {
            if (!categorias[t.categoria_nome]) {
                categorias[t.categoria_nome] = {
                    nome: t.categoria_nome,
                    valor: 0,
                    tipo: t.categoria_tipo
                };
            }
            categorias[t.categoria_nome].valor += parseFloat(t.valor);
        });
        
        const labels = Object.keys(categorias);
        const data = Object.values(categorias).map(c => c.valor);
        const colors = Object.values(categorias).map(c => 
            c.tipo === 'income' ? '#28a745' : '#dc3545'
        );
        
        const ctx = document.getElementById('categoriaChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erro ao gerar gráfico:', error);
    }
}

// Função para formatar data
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

// Função de logout
async function logout() {
    try {
        // Chamar endpoint de logout na API (para consistência)
        await apiRequest('/Financas/api/routes/index.php/auth/logout', 'POST');
    } catch (error) {
        // Ignorar erros de logout na API, pois o logout principal é feito no frontend
        console.log('Logout na API não foi necessário ou falhou:', error);
    }
    
    // Limpar dados do usuário no frontend
    localStorage.removeItem('user');
    
    // Mostrar feedback e redirecionar para login
    mostrarFeedback('Logout realizado com sucesso!', 'success');
    setTimeout(() => {
        window.location.href = 'login.html';
    }, 2000);
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
            
            // Verificar se está no modo edição
            const isEditMode = document.querySelector('.modal-title') && 
                              document.querySelector('.modal-title').innerHTML.includes('Editar');
            
            if (isEditMode) {
                // No modo edição, o onclick do botão já cuida da edição
                // Não fazer nada aqui para evitar duplicidade
                return;
            } else {
                // No modo criação, executar a criação da transação
                registrarTransacao();
            }
        });
    }

    // Event listener para botão de logout
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }

    // Carregar transações na página de transações (já é feito no transacoes.html)
    // Removido para evitar chamadas duplicadas
});
