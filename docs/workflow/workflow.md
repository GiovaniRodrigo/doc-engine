# Workflows

## Publicação de documentação

1. Desenvolvedor faz commit
2. Webhook é disparado
3. Sistema sincroniza repositório
4. Detecta arquivos alterados
5. Cria novas versões
6. Renderiza HTML
7. Publica versão
8. Atualiza cache

## Atualização manual

php artisan docs:sync project-name