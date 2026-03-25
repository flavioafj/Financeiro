# Guia para Criar o Repositório no GitHub

## Passo 1: Criar o Repositório no GitHub

1. **Acesse o GitHub**: https://github.com
2. **Faça login** na sua conta
3. **Clique em "New repository"** (novo repositório)
4. **Configure o repositório**:
   - **Nome**: `Financeiro`
   - **Descrição**: `Sistema de Controle Financeiro - Aplicação web completa para gestão de finanças pessoais`
   - **Privacidade**: Public (ou Private, conforme sua preferência)
   - **Initialize this repository with a README**: ❌ (não marcar, já temos o README.md)
   - **Add .gitignore**: ❌ (não marcar, já temos o .gitignore)
   - **Choose a license**: ❌ (não marcar, já temos o LICENSE)

5. **Clique em "Create repository"**

## Passo 2: Conectar o Repositório Local ao Remoto

Após criar o repositório no GitHub, execute os comandos abaixo no terminal:

```bash
# Remova o remote atual (se necessário)
git remote remove origin

# Adicione o remote correto (substitua SEU-USUARIO pelo seu username do GitHub)
git remote add origin https://github.com/SEU-USUARIO/Financeiro.git

# Faça o push inicial
git branch -M main
git push -u origin main
```

## Passo 3: Verificar o Push

Após o push, você pode:
- Verificar no GitHub se todos os arquivos foram enviados corretamente
- Confirmar que o README.md está sendo exibido na página do repositório
- Verificar se todas as pastas e arquivos estão presentes

## Comandos Alternativos (se houver problemas)

Se encontrar problemas de autenticação:

```bash
# Use SSH em vez de HTTPS (recomendado)
git remote set-url origin git@github.com:SEU-USUARIO/Financeiro.git

# Ou configure credenciais para HTTPS
git config --global credential.helper store
```

## Estrutura do Repositório no GitHub

Após o push bem-sucedido, o repositório deverá conter:

```
Financeiro/
├── api/                    # Backend PHP
├── frontend/              # Frontend HTML/CSS/JS
├── database/              # Scripts SQL
├── testes/                # Testes
├── docs/                  # Documentação
├── README.md              # Documentação principal
├── .gitignore             # Arquivos ignorados
└── LICENSE                # Licença do projeto
```

## Próximos Passos

1. **Proteger a branch main**: Configure proteções de branch no GitHub
2. **Adicionar colaboradores**: Se necessário, adicione outros desenvolvedores
3. **Configurar CI/CD**: Adicione workflows de integração contínua
4. **Documentar**: Atualize o README.md com informações específicas do seu deployment

## Dicas Importantes

- **Sempre faça commits frequentes** com mensagens descritivas
- **Use branches** para desenvolvimento de novas funcionalidades
- **Mantenha o .gitignore atualizado** para evitar commits indesejados
- **Documente mudanças** importantes no CHANGELOG.md (criar se necessário)

## Suporte

Se encontrar problemas:
1. Verifique a conexão com o GitHub
2. Confira as credenciais de acesso
3. Verifique se o repositório foi criado corretamente
4. Consulte a documentação do Git: https://git-scm.com/doc