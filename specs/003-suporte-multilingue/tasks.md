# Lista de Tarefas: Suporte Multilíngue para Documentação

- [X] 1. Criar testes automatizados para validar a detecção de idiomas, filtro da sidebar, redirecionamento e normalização de caminhos (`tests/Feature/MultilingualSupportTest.php`)
- [X] 2. Atualizar a normalização de slugs e implementar a lógica de presença/tradução de idiomas no controlador (`src/Http/DocumentationController.php`)
- [X] 3. Refatorar o construtor da sidebar (`src/Application/Services/SidebarBuilder.php`) para suportar a filtragem e limpeza do prefixo de idioma selecionado
- [X] 4. Inserir o dropdown seletor de idiomas com o script JS de redirecionamento dinâmico de traduções (`resources/views/layouts/default.blade.php`)
- [X] 5. Adicionar estilos Material Design 3 para o dropdown seletor de idiomas (`resources/css/docs/components.css`)
- [X] 6. Executar toda a suíte de testes com o PHPUnit (`vendor/bin/phpunit`) para garantir que os testes multilíngues e legados passam com 100% de sucesso
