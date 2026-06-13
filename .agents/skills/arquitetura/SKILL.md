---
name: arquitetura
description: "Diretrizes, princípios e padrões para design e estruturação de arquitetura de software, incluindo Clean Architecture, Ports and Adapters, desacoplamento e registros de decisões arquiteturais (ADRs)."
---

## 1. Skill Overview and Principles

A arquitetura de software é a fundação sobre a qual os sistemas são construídos. Uma arquitetura bem projetada facilita a manutenção, testabilidade, escalabilidade e evolução do sistema a longo prazo, minimizando o custo de mudanças.

### Princípios de Arquitetura de Software

#### A. Clean Architecture (Arquitetura Limpa)
Popularizada por Robert C. Martin (Uncle Bob), a Clean Architecture foca no isolamento das regras de negócio do software em relação a frameworks, bancos de dados, interfaces de usuário (UI) e quaisquer outros agentes externos.
*   **Regra de Dependência:** O código das camadas internas não deve saber nada sobre as camadas externas. Dependências de código-fonte devem apontar apenas para dentro (em direção às políticas de negócio).
*   **Camadas Comuns:**
    1.  **Entities (Entidades):** Regras de negócio globais/corporativas. Objetos com métodos ou estruturas de dados que contêm regras fundamentais de negócio.
    2.  **Use Cases (Casos de Uso):** Regras de negócio específicas da aplicação. Orquestram o fluxo de dados de e para as entidades.
    3.  **Interface Adapters (Adaptadores de Interface):** Traduzem dados no formato mais conveniente para os casos de uso e entidades para o formato mais conveniente para agentes externos (ex: Controllers, Presenters, Gateways).
    4.  **Frameworks & Drivers (Frameworks e Web/DB):** Ferramentas como bancos de dados, frameworks web, interfaces de usuário.

#### B. Ports and Adapters (Arquitetura Hexagonal)
Criada por Alistair Cockburn, visa permitir que uma aplicação seja igualmente governada por usuários, programas, testes automatizados ou scripts, e desenvolvida e testada de forma isolada de seus eventuais dispositivos de execução e bancos de dados.
*   **Core (Núcleo):** Contém a lógica de negócio principal. Não tem dependências externas.
*   **Ports (Portas):** Interfaces que definem como o mundo externo pode interagir com o Core (Inbound/Driving Ports) ou como o Core pode interagir com o mundo externo (Outbound/Driven Ports).
*   **Adapters (Adaptadores):** Implementações concretas das portas. Por exemplo, um controlador HTTP é um adaptador de entrada (Driving), e uma classe que implementa uma interface de repositório usando PostgreSQL é um adaptador de saída (Driven).

#### C. MVC (Model-View-Controller)
Um padrão clássico de design de interface de usuário que divide a aplicação em três componentes interconectados:
*   **Model:** Gerencia os dados e a lógica de negócio fundamental.
*   **View:** Apresenta os dados ao usuário (Interface de Usuário).
*   **Controller:** Aceita entradas do usuário, converte-as em comandos para o Model ou View.
*   *Nota:* Em sistemas modernos de backend puramente de API, o MVC muitas vezes evolui para camadas de rotas, controladores e serviços (Service Pattern), delegando a "View" para aplicações SPA/Mobile clientes.

#### D. Event-Driven Architecture (Arquitetura Orientada a Eventos)
Padrão arquitetural no qual o fluxo do sistema é determinado por eventos (alterações significativas de estado).
*   **Produtores de Eventos:** Publicam eventos sem saber quem os consumirá.
*   **Canais/Message Brokers:** Gerenciam a entrega dos eventos (ex: Kafka, RabbitMQ, AWS SNS/SQS).
*   **Consumidores de Eventos:** Reagem aos eventos executando lógicas específicas.
*   **Vantagens:** Alto desacoplamento temporal e espacial, escalabilidade horizontal e resiliência.

### Separação de Conceitos (Separation of Concerns - SoC)
O princípio de separar um programa em seções distintas, onde cada seção lida com um assunto específico.
*   Evita "God Classes" (classes que fazem tudo) e arquivos gigantes.
*   Facilita a leitura e navegação no código: se você precisa alterar o acesso ao banco, mexe apenas na camada de persistência.

### Desacoplamento de Camadas, Inversão de Controle (IoC) e Injeção de Dependências (DI)
*   **Inversão de Controle (IoC):** Transfere o controle do fluxo de execução ou da criação de objetos da própria classe para um framework ou container externo.
*   **Injeção de Dependências (DI):** Um padrão de design usado para implementar a IoC. Em vez de uma classe instanciar suas próprias dependências internas, elas são fornecidas ("injetadas") a ela de fora (geralmente via construtor).
*   **Benefício para Testabilidade:** Permite injetar facilmente simulacros (*mocks*, *stubs*) das dependências durante os testes, isolando a unidade sob teste.

---

## 2. Templates / Implementation Patterns

### Estrutura de Pastas Recomendada (Backend - Clean Architecture / Ports & Adapters)

Este é um template padrão para estruturas de projetos modernos de backend (ex: TypeScript, Go, Python):

```text
meu-projeto/
├── src/
│   ├── core/                        # Regras de Negócio Puras
│   │   ├── domain/                  # Entidades e Value Objects
│   │   │   ├── user.entity.ts
│   │   │   └── value-objects/
│   │   ├── use-cases/               # Casos de uso da aplicação (interatores)
│   │   │   ├── create-user.usecase.ts
│   │   │   └── ports/               # Definições de Interfaces (Portas)
│   │   │       ├── user-repository.interface.ts
│   │   │       └── notification-service.interface.ts
│   │
│   ├── infra/                       # Detalhes de Infraestrutura (Adaptadores)
│   │   ├── database/                # Bancos de dados e ORMs
│   │   │   ├── prisma/
│   │   │   └── repositories/        # Implementações dos repositórios
│   │   │       └── prisma-user.repository.ts
│   │   ├── services/                # Serviços de terceiros (envio de email, etc.)
│   │   │   └── ses-email.service.ts
│   │   ├── web/                     # Servidor Web (Express, NestJS, FastAPI)
│   │   │   ├── controllers/
│   │   │   │   └── user.controller.ts
│   │   │   ├── routes/
│   │   │   └── middlewares/
│   │
│   ├── config/                      # Configurações globais de ambiente e DI
│   │   ├── env.ts
│   │   └── di-container.ts          # Configuração da injeção de dependências
│   │
│   └── main.ts                      # Ponto de entrada da aplicação
│
├── tests/                           # Testes de integração, e2e e unitários
├── docs/                            # Documentação técnica e ADRs
│   └── adrs/
├── package.json
└── README.md
```

### Template de Registro de Decisão Arquitetural (ADR)

As ADRs devem ser armazenadas na pasta `/docs/adrs/` usando o formato `ADR-NNN-titulo-curto.md`.

```markdown
# ADR [Número]: [Título Sucinto e Claro]

*   **Status**: [Proposto | Aceito | Rejeitado | Superado por ADR-XXX]
*   **Data**: AAAA-MM-DD
*   **Autores**: [Nome/Contato]
*   **Decisores**: [Lista de decisores/envolvidos]

## Contexto e Declaração do Problema

[Descreva o contexto do problema que estamos tentando resolver. Explique os fatores limitadores, os requisitos técnicos e de negócios, bem como as forças que estão agindo sobre a decisão (por exemplo, custo, tempo de entrega, conhecimento da equipe, performance, escalabilidade).]

## Opções Consideradas

### Opção 1: [Nome da Opção 1]
*   **Prós**:
    *   [Ponto positivo]
*   **Contras**:
    *   [Ponto negativo]

### Opção 2: [Nome da Opção 2]
*   **Prós**:
    *   [Ponto positivo]
*   **Contras**:
    *   [Ponto negativo]

## Decisão Selecionada

Escolhemos a **[Opção X]** porque [justificativa principal baseada nas forças identificadas no contexto]. 

[Forneça detalhes adicionais sobre como a opção será implementada ou os pontos críticos que pesaram na decisão final.]

## Consequências

### Positivas (Benefícios)
*   [O que ganhamos com essa escolha?]

### Negativas (Riscos ou Custos)
*   [O que perdemos ou quais novos desafios/compromissos técnicos foram introduzidos?]
*   [Estratégias de mitigação para as consequências negativas.]
```

### Checklist de Revisão Arquitetural

Antes de submeter alterações de arquitetura ou código estrutural, verifique:
1.  [ ] **Regra de Dependência:** As camadas internas (`core/domain`) possuem importações ou dependências de bibliotecas de infraestrutura externa (`infra/web`, ORMs, frameworks)? (Devem ser zero).
2.  [ ] **Contratos Definidos:** Há interfaces claras para comunicação externa (portas)?
3.  [ ] **Acoplamento:** As classes são facilmente instanciáveis nos testes unitários sem levantar conexões com banco de dados ou APIs reais?
4.  [ ] **Single Responsibility:** Cada classe/função tem uma responsabilidade única no fluxo arquitetural?
5.  [ ] **Acordo de Decisão:** Caso uma nova biblioteca core ou infraestrutura pesada seja adicionada, foi criada uma ADR correspondente?

---

## 3. Step-by-Step Instructions

Quando esta skill for invocada para projetar ou avaliar a arquitetura de um projeto, siga os passos abaixo:

### Passo 1: Análise da Arquitetura Atual
1.  **Mapeamento da Estrutura:** Execute um comando de mapeamento de diretórios (ex: `tree` ou leitura recursiva) para entender a organização atual do repositório.
2.  **Mapeamento de Dependências:** Inspecione os arquivos de configuração de pacotes (ex: `package.json`, `requirements.txt`, `go.mod`, `composer.json`) para identificar as tecnologias core e de infraestrutura.
3.  **Análise de Fluxo:** Abra alguns arquivos chave (como rotas, controladores e persistência) para rastrear o fluxo de uma requisição típica. Identifique se há vazamento de conceitos (ex: SQL/ORM dentro do controlador ou na camada de rotas).

### Passo 2: Alinhamento com o Usuário
Antes de propor mudanças arquiteturais significativas, faça perguntas direcionadas ao usuário:
*   *"Qual é o tamanho e a complexidade esperada para este projeto no médio prazo?"*
*   *"O projeto atual sofre com problemas específicos de testabilidade ou acoplamento?"*
*   *"Existe preferência de framework ou abordagem arquitetural específica (ex: Hexagonal pura, MVC clássico, Clean Architecture com Domain-Driven Design)?"*
*   *"Quais são as principais integrações externas ou fontes de dados previstas?"*

### Passo 3: Implementação / Refatoração Arquitetural
1.  **Criação do Núcleo (Core/Domain):** Comece isolando as regras de negócio puras (funções, classes de domínio, value objects) sem depender de bibliotecas externas.
2.  **Definição das Interfaces (Ports):** Defina os contratos das portas (ex: `IUserRepository`, `IEmailService`) na camada interna.
3.  **Criação dos Adaptadores (Adapters):** Desenvolva a implementação concreta das interfaces na camada de infraestrutura.
4.  **Configuração da Injeção de Dependências:** Configure o ponto de entrada da aplicação (`main.ts` ou container de DI) para amarrar os adaptadores concretos às portas requisitadas pelos use cases.
5.  **Documentação com ADR:** Crie ou atualize o documento de decisão arquitetural (ADR) relevante sob a pasta `docs/adrs/`.

### Passo 4: Verificação e Validação
1.  **Testes de Isolamento:** Escreva testes unitários para os Casos de Uso (`use-cases`) injetando implementações mockadas/fake das portas de saída. Os testes devem passar sem depender de infraestrutura.
2.  **Testes de Integração:** Escreva testes de integração mínimos para validar os Adaptadores com bancos de dados de teste ou simuladores locais.
3.  **Verificação de Regras de Importação:** Revise se nenhuma classe dentro de `core/` importa coisas de `infra/`.
