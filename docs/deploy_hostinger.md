# Deploy na Hostinger

Este guia detalha o processo de deploy da aplicação de controle financeiro na Hostinger.

## Requisitos

- Conta na Hostinger (plano com PHP e MySQL)
- Acesso ao painel de controle da Hostinger
- Acesso FTP ou File Manager

## Passo a Passo

### 1. Configurar o Banco de Dados

1. **Acesse o painel da Hostinger**
   - Faça login na sua conta
   - Selecione o domínio desejado

2. **Criar Banco de Dados MySQL**
   - Vá até "Banco de Dados MySQL"
   - Clique em "Criar Banco de Dados"
   - Nomeie o banco: `financas_app`
   - Crie um usuário com senha forte
   - Associe o usuário ao banco de dados

3. **Configurar phpMyAdmin**
   - Acesse o phpMyAdmin através do painel
   - Selecione o banco de dados `financas_app`
   - Clique em "Importar"
   - Selecione o arquivo `database/schema.sql`
   - Clique em "Executar"

### 2. Configurar o PHP

1. **Ajustar configurações do PHP**
   - No painel da Hostinger, vá até "Configurações PHP"
   - Defina a versão do PHP para 7.4 ou superior
   - Configure os seguintes parâmetros:
     - `max_execution_time = 300`
     - `memory_limit = 256M`
     - `post_max_size = 10M`
     - `upload_max_filesize = 10M`

### 3. Upload dos Arquivos

1. **Via File Manager (recomendado para iniciantes)**
   - Acesse o File Manager no painel da Hostinger
   - Navegue até o diretório `public_html`
   - Crie a estrutura de pastas conforme o projeto
   - Faça upload de todos os arquivos da pasta `Financas/`

2. **Via FTP (recomendado para grandes projetos)**
   - Configure o cliente FTP com os dados fornecidos pela Hostinger
   - Conecte-se ao servidor
   - Faça upload da pasta `Financas/` para o diretório `public_html`

### 4. Configurar Conexão com Banco de Dados

1. **Editar arquivo de configuração**
   - Acesse o arquivo `api/config/database.php`
   - Atualize as credenciais do banco de dados:

```php
private $host = 'localhost'; // ou o host fornecido pela Hostinger
private $db_name = 'seu_usuario_financas_app'; // geralmente é nome_usuario_banco
private $username = 'seu_usuario'; // usuário do banco
private $password = 'sua_senha'; // senha do usuário
```

### 5. Configurar .htaccess

1. **Verificar regras de reescrita**
   - O arquivo `.htaccess` já está configurado para funcionar na Hostinger
   - Caso haja problemas, verifique se o módulo `mod_rewrite` está habilitado

2. **Configurar domínio**
   - Se estiver usando um subdomínio, ajuste as regras no `.htaccess`
   - Para domínios principais, o arquivo já está configurado

### 6. Testar a Aplicação

1. **Testar conexão**
   - Acesse `seusite.com/teste_api.php`
   - Verifique se todos os testes passam

2. **Testar páginas**
   - Acesse `seusite.com/frontend/pages/login.html`
   - Teste o cadastro e login
   - Verifique todas as funcionalidades

### 7. Configurar Segurança

1. **Permissões de arquivos**
   - Defina permissões adequadas:
     - Pastas: 755
     - Arquivos: 644
     - Arquivos sensíveis: 600

2. **SSL/HTTPS**
   - Ative o SSL gratuito da Hostinger
   - Edite o `.htaccess` para redirecionar HTTP para HTTPS

### 8. Otimizações

1. **Cache**
   - Configure cache no `.htaccess`
   - Ative cache do navegador

2. **Performance**
   - Use CDN para arquivos estáticos (Bootstrap, Chart.js)
   - Otimize imagens

## Solução de Problemas

### Erro 500
- Verifique as permissões dos arquivos
- Cheque o log de erros no painel da Hostinger
- Valide a conexão com o banco de dados

### Erro de Conexão com Banco
- Verifique as credenciais no `database.php`
- Confira se o banco de dados foi criado corretamente
- Teste a conexão via phpMyAdmin

### CORS Errors
- O `.htaccess` já configura CORS
- Verifique se o cabeçalho `Access-Control-Allow-Origin` está presente

### Arquivos não encontrados
- Verifique a estrutura de pastas
- Confira os caminhos nos arquivos HTML e JavaScript

## Monitoramento

### Logs de Erro
- Acesse o painel da Hostinger
- Vá até "Logs de Erro"
- Monitore erros PHP e de servidor

### Estatísticas
- Use as estatísticas do painel da Hostinger
- Monitore tráfego e uso de recursos

## Backup

### Backup Automático
- Configure backup automático no painel da Hostinger
- Defina frequência semanal ou diária

### Backup Manual
- Faça backup do banco de dados via phpMyAdmin
- Salve cópia dos arquivos críticos

## Atualizações

### Atualização de Segurança
- Mantenha o PHP atualizado
- Atualize dependências quando necessário

### Atualização de Conteúdo
- Faça backup antes de atualizar
- Teste alterações em ambiente de staging

## Suporte

### Documentação da Hostinger
- [Hostinger Help Center](https://www.hostinger.com.br/tutoriais)
- [PHP Configuration](https://www.hostinger.com.br/tutoriais/php-configuration-hostinger)

### Suporte Técnico
- Abra ticket no painel da Hostinger
- Consulte a documentação oficial

---

**Importante:** Sempre faça backup antes de realizar alterações críticas no ambiente de produção.