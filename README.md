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