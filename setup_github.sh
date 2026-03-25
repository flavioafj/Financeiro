#!/bin/bash

# Script de Setup para Repositório GitHub - Sistema Financeiro

echo "🚀 Configurando repositório GitHub para Sistema Financeiro"
echo "=================================================="

# Verifica se o git está instalado
if ! command -v git &> /dev/null; then
    echo "❌ Git não está instalado. Por favor, instale o Git primeiro."
    exit 1
fi

# Verifica se já existe repositório Git
if [ -d ".git" ]; then
    echo "✅ Repositório Git já inicializado"
else
    echo "📦 Inicializando repositório Git..."
    git init
fi

# Verifica se o usuário já configurou o Git
if [ -z "$(git config user.name)" ]; then
    echo "📝 Configurando informações do usuário Git..."
    read -p "Digite seu nome completo: " user_name
    read -p "Digite seu email do GitHub: " user_email
    
    git config user.name "$user_name"
    git config user.email "$user_email"
    echo "✅ Configurações do Git atualizadas"
fi

# Pergunta o username do GitHub
read -p "Digite seu username do GitHub: " github_user

# Cria o repositório remoto
echo "🔗 Configurando repositório remoto..."
git remote remove origin 2>/dev/null || true
git remote add origin "https://github.com/$github_user/Financeiro.git"

echo ""
echo "📋 Resumo da configuração:"
echo "   Username: $github_user"
echo "   Repositório: https://github.com/$github_user/Financeiro.git"
echo "   Branch principal: main"
echo ""

# Pergunta se deseja fazer o push
read -p "Deseja fazer o push para o GitHub agora? (s/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "📤 Fazendo push para o GitHub..."
    
    # Verifica se o repositório remoto já existe
    if git ls-remote origin &> /dev/null; then
        echo "✅ Repositório remoto encontrado"
        
        # Faz o push
        git branch -M main
        if git push -u origin main; then
            echo "✅ Push realizado com sucesso!"
            echo "🌐 Acesse seu repositório em: https://github.com/$github_user/Financeiro"
        else
            echo "❌ Erro ao fazer push. Verifique suas credenciais do GitHub."
            echo "💡 Dica: Configure suas credenciais com:"
            echo "   git config --global credential.helper store"
        fi
    else
        echo "❌ Repositório remoto não encontrado."
        echo "💡 Por favor, crie o repositório no GitHub primeiro:"
        echo "   1. Acesse https://github.com/new"
        echo "   2. Nome: Financeiro"
        echo "   3. Descrição: Sistema de Controle Financeiro"
        echo "   4. Não marque 'Initialize this repository with a README'"
        echo "   5. Clique em 'Create repository'"
        echo ""
        echo "   Depois execute novamente:"
        echo "   git push -u origin main"
    fi
else
    echo "💡 Para fazer o push manualmente, execute:"
    echo "   git branch -M main"
    echo "   git push -u origin main"
fi

echo ""
echo "🎉 Configuração concluída!"
echo "📚 Consulte criar_repositorio_github.md para mais detalhes"