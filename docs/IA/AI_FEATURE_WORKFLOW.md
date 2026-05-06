# Fluxo de Implementação para IA

Este documento define o processo obrigatório para uma IA implementar novas funcionalidades, ferramentas ou alterações relevantes no código deste projeto.

## Regra principal

A IA deve seguir esta ordem:

1. Definir ou localizar a regra de negócio.
2. Criar ou atualizar os testes com base nos padrões do projeto.
3. Implementar a funcionalidade.

Se a regra de negócio não existir e não puder ser inferida com segurança, a IA deve interromper o trabalho com erro claro e pedir definição ao usuário antes de criar testes ou alterar código.

## Etapa 1: regra de negócio

Antes de qualquer alteração técnica, a IA deve identificar:

- qual problema de negócio será resolvido;
- qual comportamento esperado;
- quais entradas são aceitas;
- quais saídas ou efeitos são esperados;
- quais casos inválidos devem ser rejeitados;
- quais limites, exceções ou fallbacks existem.

A IA deve procurar a regra em:

- documentação existente em `docs/`;
- testes existentes em `tests/`;
- código relacionado em `src/Application/`, `src/Domain/` e `src/Infrastructure/`;
- mensagens explícitas do usuário.

Se a regra estiver ausente, ambígua ou contraditória, interrompa com erro:

```text
Erro: regra de negócio ausente ou ambígua. Não é seguro criar testes ou implementar sem definição do comportamento esperado.
```

Depois, peça ao usuário a regra necessária.

## Aprovação da etapa 1

Antes de avançar para testes, a IA deve mostrar ao usuário:

- a regra de negócio encontrada ou proposta;
- os arquivos que provavelmente serão afetados;
- o plano de alteração de código ou documentação;
- os riscos conhecidos.

A IA só deve avançar após aprovação explícita do usuário.

## Etapa 2: testes

Com a regra de negócio aprovada, a IA deve criar ou atualizar testes antes da implementação.

Use os testes em `tests/Feature/` como referência obrigatória para o padrão de testes do projeto.

Os testes devem cobrir:

- caso feliz;
- entradas inválidas;
- limites relevantes;
- fallback ou erro esperado, quando houver;
- regressões relacionadas à regra.

## Aprovação da etapa 2

Antes de implementar, a IA deve mostrar ao usuário:

- quais testes serão criados ou alterados;
- qual regra cada teste valida;
- quais arquivos de teste serão modificados;
- quais comandos serão usados para executar a validação.

A IA só deve avançar para implementação após aprovação explícita do usuário.

## Etapa 3: implementação

Somente depois dos testes aprovados, a IA deve alterar o código da funcionalidade.

A implementação deve:

- seguir os padrões existentes do projeto (PSR-12, tipagem forte);
- manter a alteração no menor escopo possível;
- evitar refatorações não solicitadas;
- preservar compatibilidade com as funcionalidades existentes;
- tratar erros de forma clara para o usuário.

## Aprovação da etapa 3

Antes de editar código de produção, a IA deve mostrar ao usuário:

- plano de alteração por arquivo;
- funções, classes ou módulos que serão alterados;
- comportamento esperado depois da mudança;
- impacto nos testes existentes;
- comandos de verificação planejados.

A IA só deve editar código de produção após aprovação explícita do usuário.

## Verificação final

Após implementar, a IA deve executar os testes aplicáveis.

Comando padrão:

```bash
vendor/bin/phpunit
```

Para executar um teste específico:

```bash
vendor/bin/phpunit tests/Feature/NomeDoTeste.php
```

## Resultado esperado da IA

Ao final, a IA deve informar:

- regra de negócio implementada;
- testes criados ou alterados;
- arquivos de produção alterados;
- comandos executados;
- resultado dos testes;
- qualquer limitação ou risco residual.

## Restrições

A IA não deve:

- implementar funcionalidade sem regra de negócio definida;
- criar testes depois da implementação, salvo correção explícita de teste quebrado;
- alterar código de produção antes da aprovação do plano;
- ampliar escopo sem consultar o usuário;
- remover comportamento existente sem aprovação;
- ignorar falha de teste.
