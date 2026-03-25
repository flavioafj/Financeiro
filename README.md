# Sistema de Controle Financeiro

Um sistema web completo para controle de finanças pessoais, desenvolvido com tecnologias modernas e práticas de desenvolvimento.

## 🚀 Tecnologias Utilizadas

### Frontend
- **HTML5** - Estrutura da aplicação
- **CSS3** - Estilização com Bootstrap 5
- **JavaScript ES6+** - Lógica do frontend
- **Bootstrap 5** - Framework CSS
- **Chart.js** - Gráficos e visualizações
- **SheetJS (xlsx.js)** - Exportação para Excel

### Backend
- **PHP 8+** - Lógica do servidor
- **MySQL** - Banco de dados
- **PDO** - Conexão com banco de dados

### Segurança
- **JWT (JSON Web Tokens)** - Autenticação
- **bcrypt** - Criptografia de senhas
- **Validação de entrada** - Proteção contra SQL Injection e XSS

## 📁 Estrutura do Projeto

```
Financas/
├── api/                    # Backend PHP
│   ├── config/            # Configurações
│   ├── controllers/       # Controladores
│   ├── models/           # Modelos de dados
│   ├── routes/           # Rotas da API
│   └── utils/            # Utilitários
├── frontend/             # Frontend
│   ├── css/              # Estilos
│   ├── js/               # JavaScript
│   └── pages/            # Páginas HTML
├── database/             # Scripts de banco de dados
├── testes/               # Testes
└── docs/                 # Documentação
```

## 🛠️ Instalação

### Requisitos
- PHP 8.0+
- MySQL 5.7+
- Apache/Nginx
- Composer (opcional)

### Passo a Passo

1. **Clone o repositório**
   ```bash
   git clone https://github.com/seu-usuario/Financeiro.git
   cd Financeiro
   ```

2. **Configure o banco de dados**
   - Importe o script `database/schema.sql` para criar as tabelas
   - Configure as credenciais no arquivo `api/config/database.php`

3. **Configure o ambiente**
   - Copie `.env.example` para `.env` e ajuste as configurações
   - Configure o virtual host do Apache/Nginx

4. **Permissões**
   ```bash
   chmod -R 755 api/
   chmod -R 755 frontend/
   ```

## 🔧 Configuração do Banco de Dados

### Crie o banco de dados
```sql
CREATE DATABASE controle_financeiro;
```

### Importe o schema
```bash
mysql -u usuario -p controle_financeiro < database/schema.sql
```

### Configuração do .htaccess
O arquivo `.htaccess` já está configurado para reescrever URLs e permitir CORS.

## 🚀 Uso

### Iniciar o servidor
```bash
# Apache (XAMPP/WAMP)
# Acesse: http://localhost/Financas/frontend/pages/login.html

# PHP Built-in Server
cd Financas
php -S localhost:8000
```

### Páginas principais
- **Login**: `/frontend/pages/login.html`
- **Cadastro**: `/frontend/pages/register.html`
- **Dashboard**: `/frontend/pages/dashboard.html`
- **Transações**: `/frontend/pages/transacoes.html`
- **Categorias**: `/frontend/pages/categorias.html`
- **Relatórios**: `/frontend/pages/relatorios.html`

## 📊 Funcionalidades

### Autenticação
- Cadastro de usuários
- Login seguro com JWT
- Logout

### Controle Financeiro
- Cadastro de categorias
- Registro de transações (receitas e despesas)
- Dashboard com resumo financeiro
- Gráficos de análise

### Relatórios
- Filtros por período e tipo
- Exportação para Excel
- Distribuição por categoria
- Comparativo de receitas vs despesas

### Segurança
- Validação de entrada
- Criptografia de senhas
- Proteção CSRF
- CORS configurado

## 🧪 Testes

### Testes de API
```bash
# Teste de endpoints
php testes/test_api.php

# Teste de segurança
php testes/test_security.php
```

### Testes de Frontend
```bash
# Teste de funcionalidades
node testes/test_frontend.js
```

## 📡 API Endpoints

### Autenticação

#### Registro de Usuário
```http
POST /api/routes/index.php/auth/register
Content-Type: application/json

{
    "email": "usuario@exemplo.com",
    "senha": "senha123",
    "nome": "Nome do Usuário"
}
```

**Resposta de sucesso:**
```json
{
    "status": "success",
    "message": "Usuário registrado com sucesso",
    "data": {
        "id": 1,
        "email": "usuario@exemplo.com",
        "nome": "Nome do Usuário"
    }
}
```

#### Login de Usuário
```http
POST /api/routes/index.php/auth/login
Content-Type: application/json

{
    "email": "usuario@exemplo.com",
    "senha": "senha123"
}
```

**Resposta de sucesso:**
```json
{
    "status": "success",
    "message": "Login realizado com sucesso",
    "data": {
        "id": 1,
        "email": "usuario@exemplo.com",
        "nome": "Nome do Usuário",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

### Categorias

#### Listar Categorias
```http
GET /api/routes/index.php/categories?user_id=1
Authorization: Bearer seu_token_jwt
```

**Resposta:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "nome": "Alimentação",
            "tipo": "expense",
            "user_id": 1
        },
        {
            "id": 2,
            "nome": "Salário",
            "tipo": "income",
            "user_id": 1
        }
    ]
}
```

### Transações

#### Registrar Transação
```http
POST /api/routes/index.php/transactions?user_id=1
Authorization: Bearer seu_token_jwt
Content-Type: application/json

{
    "categoria_id": 1,
    "valor": 50.00,
    "tipo": "expense",
    "data": "2024-01-15",
    "descricao": "Almoço no restaurante"
}
```

**Resposta de sucesso:**
```json
{
    "status": "success",
    "message": "Transação registrada com sucesso",
    "data": {
        "id": 123,
        "categoria_id": 1,
        "valor": 50.00,
        "tipo": "expense",
        "data": "2024-01-15",
        "descricao": "Almoço no restaurante",
        "user_id": 1
    }
}
```

#### Listar Transações
```http
GET /api/routes/index.php/transactions?user_id=1&limit=10&offset=0
Authorization: Bearer seu_token_jwt
```

**Resposta:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 123,
            "categoria_id": 1,
            "categoria_nome": "Alimentação",
            "categoria_tipo": "expense",
            "valor": 50.00,
            "tipo": "expense",
            "data": "2024-01-15",
            "descricao": "Almoço no restaurante",
            "user_id": 1
        }
    ]
}
```

#### Atualizar Transação
```http
PUT /api/routes/index.php/transactions/123?user_id=1
Authorization: Bearer seu_token_jwt
Content-Type: application/json

{
    "categoria_id": 2,
    "valor": 75.00,
    "tipo": "expense",
    "data": "2024-01-15",
    "descricao": "Jantar no restaurante"
}
```

**Resposta de sucesso:**
```json
{
    "status": "success",
    "message": "Transação atualizada com sucesso"
}
```

#### Excluir Transação
```http
DELETE /api/routes/index.php/transactions/123?user_id=1
Authorization: Bearer seu_token_jwt
```

**Resposta de sucesso:**
```json
{
    "status": "success",
    "message": "Transação excluída com sucesso"
}
```

### Exemplos de Uso com cURL

#### Registro de Usuário
```bash
curl -X POST http://localhost/Financas/api/routes/index.php/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@exemplo.com",
    "senha": "senha123",
    "nome": "Nome do Usuário"
  }'
```

#### Login
```bash
curl -X POST http://localhost/Financas/api/routes/index.php/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@exemplo.com",
    "senha": "senha123"
  }'
```

#### Registro de Transação (após login)
```bash
curl -X POST http://localhost/Financas/api/routes/index.php/transactions?user_id=1 \
  -H "Authorization: Bearer seu_token_jwt" \
  -H "Content-Type: application/json" \
  -d '{
    "categoria_id": 1,
    "valor": 100.00,
    "tipo": "income",
    "data": "2024-01-15",
    "descricao": "Salário"
  }'
```

#### Listar Transações
```bash
curl -X GET http://localhost/Financas/api/routes/index.php/transactions?user_id=1 \
  -H "Authorization: Bearer seu_token_jwt"
```

## 🔧 Como Testar a API

### 1. Teste de Registro e Login
```bash
# 1. Registre um novo usuário
curl -X POST http://localhost/Financas/api/routes/index.php/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email": "test@test.com", "senha": "123456", "nome": "Teste"}'

# 2. Faça login para obter o token
curl -X POST http://localhost/Financas/api/routes/index.php/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@test.com", "senha": "123456"}'

# 3. Use o token retornado para registrar transações
```

### 2. Teste de Transações
```bash
# 1. Registre categorias primeiro (ou use categorias existentes)
# 2. Registre transações usando o token obtido no login
# 3. Consulte as transações registradas
```

### 3. Teste de Segurança
- **Tokens JWT**: Sempre inclua o token nas requisições que exigem autenticação
- **Validação de entrada**: A API valida todos os campos enviados
- **Proteção CSRF**: Implementada nas operações críticas

### 4. Ferramentas de Teste
- **Postman**: Importe a coleção de endpoints
- **Insomnia**: Alternativa ao Postman
- **cURL**: Para testes rápidos via linha de comando
- **JavaScript Fetch**: Para testes no frontend

## 📊 Estrutura de Dados

### Usuário
```json
{
    "id": 1,
    "email": "usuario@exemplo.com",
    "nome": "Nome do Usuário",
    "senha": "senha_criptografada",
    "created_at": "2024-01-01 00:00:00"
}
```

### Categoria
```json
{
    "id": 1,
    "nome": "Alimentação",
    "tipo": "expense",
    "user_id": 1
}
```

### Transação
```json
{
    "id": 123,
    "categoria_id": 1,
    "valor": 50.00,
    "tipo": "expense",
    "data": "2024-01-15",
    "descricao": "Almoço no restaurante",
    "user_id": 1
}
```

## 🔒 Segurança

### Medidas implementadas
- **Validação de entrada** em todos os campos
- **Criptografia bcrypt** para senhas
- **JWT** para autenticação segura
- **Proteção XSS** com escape de saída
- **CORS** configurado para origem específica

### Boas práticas
- Senhas nunca são armazenadas em texto puro
- Tokens JWT com expiração
- Validação server-side em todas as operações
- Tratamento de erros adequado

## 📈 Performance

### Otimizações
- **Cache de consultas** no backend
- **Minificação** de CSS/JS (pode ser adicionada)
- **Lazy loading** de imagens
- **Indexação** no banco de dados

### Monitoramento
- Logs de erro configurados
- Métricas de performance no dashboard
- Monitoramento de uso de recursos

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nome-da-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona feature X'`)
4. Push para a branch (`git push origin feature/nome-da-feature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🙏 Agradecimentos

- Comunidade PHP
- Bootstrap Team
- Chart.js Team
- SheetJS Team

## 📞 Contato

Para suporte ou dúvidas:
- Email: seu-email@example.com
- Issues do GitHub

---

**Desenvolvido com ❤️ para controle financeiro eficiente!**