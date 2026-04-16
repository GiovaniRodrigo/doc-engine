# Runbook — Docs não atualizam

## Problema

Documentação não reflete alterações recentes.

## Verificações

1. Verificar commit aplicado
2. Rodar manualmente:

php artisan docs:sync project

3. Verificar fila:

php artisan queue:work

4. Verificar logs

## Possíveis causas

- Falha no Git pull
- Falha no render
- Cache desatualizado