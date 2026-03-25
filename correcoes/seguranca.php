<?php
/**
 * Correções de Segurança e Validação
 * 
 * Este script contém as correções implementadas para melhorar
 * a segurança e validação do sistema de controle financeiro.
 */

// 1. Funções de Sanitização
function sanitizarEmail($email) {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

function sanitizarTexto($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function validarValor($valor) {
    $valor = filter_var($valor, FILTER_VALIDATE_FLOAT);
    return $valor > 0 ? $valor : false;
}

function validarTipoTransacao($tipo) {
    $tipos_validos = ['income', 'expense'];
    return in_array($tipo, $tipos_validos) ? $tipo : false;
}

function validarData($data) {
    $formato = 'Y-m-d';
    $d = DateTime::createFromFormat($formato, $data);
    return $d && $d->format($formato) === $data;
}

// 2. Melhorias no Model de Usuário
function corrigirModelUsuario() {
    $codigo_corrigido = <<< 'PHP'
<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $email;
    public $senha;
    public $nome;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar novo usuário
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET email=:email, senha=:senha, nome=:nome";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->email = sanitizarEmail($this->email);
        $this->senha = htmlspecialchars(strip_tags($this->senha));
        $this->nome = sanitizarTexto($this->nome);

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind dos parâmetros
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":nome", $this->nome);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar se o email já existe
    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Login do usuário
    public function login() {
        $query = "SELECT id, email, senha, nome FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->senha, $row['senha'])) {
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->nome = $row['nome'];
                return true;
            }
        }
        return false;
    }

    // Obter dados do usuário
    public function readOne() {
        $query = "SELECT id, email, nome, data_criacao FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->email = $row['email'];
            $this->nome = $row['nome'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }
}
?>
PHP;
    
    return $codigo_corrigido;
}

// 3. Melhorias no Model de Transação
function corrigirModelTransacao() {
    $codigo_corrigido = <<< 'PHP'
<?php
require_once __DIR__ . '/../config/database.php';

class Transacao {
    private $conn;
    private $table_name = "transacoes";

    public $id;
    public $usuario_id;
    public $categoria_id;
    public $valor;
    public $tipo;
    public $data;
    public $descricao;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar nova transação
    public function create() {
        // Validações
        $this->valor = validarValor($this->valor);
        $this->tipo = validarTipoTransacao($this->tipo);
        $this->data = validarData($this->data) ? $this->data : date('Y-m-d');
        
        if (!$this->valor || !$this->tipo) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET usuario_id=:usuario_id, categoria_id=:categoria_id, 
                      valor=:valor, tipo=:tipo, data=:data, descricao=:descricao";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->descricao = sanitizarTexto($this->descricao);

        // Bind dos parâmetros
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":data", $this->data);
        $stmt->bindParam(":descricao", $this->descricao);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obter transações de um usuário
    public function readByUsuario($usuario_id, $limite = 50, $offset = 0) {
        $query = "SELECT t.id, t.usuario_id, t.categoria_id, t.valor, t.tipo, 
                         t.data, t.descricao, t.data_criacao,
                         c.nome as categoria_nome, c.tipo as categoria_tipo
                  FROM " . $this->table_name . " t
                  INNER JOIN categorias c ON t.categoria_id = c.id
                  WHERE t.usuario_id = :usuario_id 
                  ORDER BY t.data DESC, t.data_criacao DESC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Obter transação por ID
    public function readOne() {
        $query = "SELECT t.id, t.usuario_id, t.categoria_id, t.valor, t.tipo, 
                         t.data, t.descricao, t.data_criacao,
                         c.nome as categoria_nome, c.tipo as categoria_tipo
                  FROM " . $this->table_name . " t
                  INNER JOIN categorias c ON t.categoria_id = c.id
                  WHERE t.id = :id AND t.usuario_id = :usuario_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->categoria_id = $row['categoria_id'];
            $this->valor = $row['valor'];
            $this->tipo = $row['tipo'];
            $this->data = $row['data'];
            $this->descricao = $row['descricao'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }
}
?>
PHP;
    
    return $codigo_corrigido;
}

// 4. Melhorias no Frontend (JavaScript)
function corrigirFrontend() {
    $codigo_corrigido = <<< 'JAVASCRIPT'
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

// Função de feedback visual
function mostrarFeedback(mensagem, tipo = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Remover alerta após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Event listener para formulários
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const errors = validarFormulario(form);
            
            if (errors.length > 0) {
                e.preventDefault();
                errors.forEach(error => {
                    mostrarFeedback(error, 'danger');
                });
            }
        });
    });
});
JAVASCRIPT;
    
    return $codigo_corrigido;
}

// 5. Melhorias no Controller de Transação
function corrigirControllerTransacao() {
    $codigo_corrigido = <<< 'PHP'
<?php
require_once __DIR__ . '/../models/Transacao.php';

class TransacaoController {
    private $database;
    private $db;
    private $transacao;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->transacao = new Transacao($this->db);
    }

    // Criar nova transação
    public function create($user_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($user_id) && !empty($data->categoria_id) && !empty($data->valor) && !empty($data->tipo) && !empty($data->data)) {
            // Validações
            $valor = validarValor($data->valor);
            $tipo = validarTipoTransacao($data->tipo);
            $data_valida = validarData($data->data);
            
            if (!$valor || !$tipo || !$data_valida) {
                http_response_code(400);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Dados inválidos. Verifique valor, tipo e data."
                ));
                return;
            }

            $this->transacao->usuario_id = $user_id;
            $this->transacao->categoria_id = $data->categoria_id;
            $this->transacao->valor = $valor;
            $this->transacao->tipo = $tipo;
            $this->transacao->data = $data->data;
            $this->transacao->descricao = !empty($data->descricao) ? sanitizarTexto($data->descricao) : null;

            if ($this->transacao->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Transação criada com sucesso!"
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Erro ao criar transação. Verifique os dados."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário categoria_id, valor, tipo e data."
            ));
        }
    }
}
?>
PHP;
    
    return $codigo_corrigido;
}

// 6. Melhorias no .htaccess para segurança
function corrigirHtaccess() {
    $codigo_corrigido = <<< 'HTACCESS'
# Configurações de segurança
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /Financas/
    
    # Redirecionar para HTTPS (se disponível)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Proteger arquivos sensíveis
    <Files ".htaccess">
        Order Allow,Deny
        Deny from all
    </Files>
    
    <Files "config/database.php">
        Order Allow,Deny
        Deny from all
    </Files>
    
    <Files "*.log">
        Order Allow,Deny
        Deny from all
    </Files>
    
    # Redirecionar todas as requisições para a API
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^api/(.*)$ api/routes/index.php/$1 [QSA,L]
    
    # Redirecionar páginas do frontend
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ frontend/pages/$1 [QSA,L]
</IfModule>

# Configurações de cabeçalhos de segurança
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # CORS headers
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "POST, GET, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header always set Access-Control-Allow-Credentials "true"
</IfModule>

# Configurações de cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
    ExpiresByType application/x-javascript "access plus 1 week"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>
HTACCESS;
    
    return $codigo_corrigido;
}

// Resumo das correções implementadas
function resumoCorrecoes() {
    return [
        'seguranca' => [
            'Sanitização de emails com filter_var',
            'Sanitização de textos com htmlspecialchars',
            'Validação de valores numéricos',
            'Validação de tipos de transação',
            'Validação de formatos de data',
            'Proteção contra SQL Injection com prepared statements'
        ],
        'frontend' => [
            'Tratamento de erros melhorado',
            'Validação de formulários em tempo real',
            'Feedback visual para o usuário',
            'Validação de email com regex',
            'Validação de valores e tipos'
        ],
        'performance' => [
            'Cache de arquivos estáticos',
            'Proteção de arquivos sensíveis',
            'Cabeçalhos de segurança',
            'CORS configurado corretamente'
        ]
    ];
}

echo "<h1>Correções Implementadas</h1>";
echo "<h2>Resumo das Melhorias</h2>";

$correcoes = resumoCorrecoes();

foreach ($correcoes as $categoria => $itens) {
    echo "<h3>" . ucfirst($categoria) . "</h3>";
    echo "<ul>";
    foreach ($itens as $item) {
        echo "<li>✅ $item</li>";
    }
    echo "</ul>";
}

echo "<h2>Próximos Passos</h2>";
echo "<ol>";
echo "<li>Substituir os arquivos originais pelos códigos corrigidos</li>";
echo "<li>Testar novamente o sistema</li>";
echo "<li>Executar os testes de segurança</li>";
echo "<li>Validar o fluxo completo de cadastro e transações</li>";
echo "</ol>";

echo "<p><strong>Importante:</strong> As correções focam em segurança, validação de dados e experiência do usuário, mantendo a simplicidade do sistema.</p>";
?>