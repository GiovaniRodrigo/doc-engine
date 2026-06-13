# Lista de Tarefas: Colaboração em Tempo Real

- [X] 1. Criar testes automatizados para validar a colaboração em tempo real e o controle de acesso (`tests/Feature/RealTimeCollaborationTest.php`)
- [X] 2. Registrar rota `/docs/{slug}/collaboration` sob o grupo de middleware de edição (`src/routes.php`)
- [X] 3. Adicionar método `collaboration` no controlador (`src/Http/DocumentationController.php`) manipulando a lógica de presença via Cache e controle de expiração (15s)
- [X] 4. Criar componentes visuais CSS de chips/badges e mensagens de alerta de concorrência (`resources/css/docs/components.css`)
- [X] 5. Inserir a interface de presença, container de aviso e script JS de polling (5s) com tratamento de token CSRF e falhas de autorização (`resources/views/edit.blade.php`)
- [X] 6. Executar suíte de testes completa do PHPUnit (`vendor/bin/phpunit`) para validar a entrega com sucesso e sem regressões
