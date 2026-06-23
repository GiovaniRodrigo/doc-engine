# Lista de Tarefas: Criação de Diagramas UML no Editor

- [x] 1. Criar testes automatizados para validar a presença do menu dropdown de diagramas UML e suas opções no editor (`tests/Feature/FormattingToolsTest.php`)
- [x] 2. Adicionar chaves de tradução para as opções do dropdown (`resources/lang/pt/messages.php` e `resources/lang/en/messages.php`)
- [x] 3. Adicionar estilos CSS do dropdown com o padrão visual M3 (`resources/css/docs/components.css`)
- [x] 4. Implementar a interface do dropdown, controle JS de abertura/fechamento e templates UML retrocompatíveis na função `insertMarkdown` (`resources/views/edit.blade.php`)
- [x] 5. Sincronizar views e arquivos CSS editados com a pasta de paridade do aplicativo de testes (`tests/laravel/`)
- [x] 6. Executar toda a suíte de testes com o PHPUnit (`vendor/bin/phpunit`) para garantir que os novos testes UML e todos os testes legados estão com 100% de sucesso
