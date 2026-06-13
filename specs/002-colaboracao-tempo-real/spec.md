# Especificação: Colaboração em Tempo Real com Controle de Acesso

Esta especificação define o comportamento e os requisitos para o sistema de presença e colaboração em tempo real no editor de documentos, respeitando as políticas e middlewares de controle de acesso configurados no pacote.

---

## 1. Objetivo

Permitir que os editores de documentação saibam em tempo real quem mais está editando o mesmo documento no momento. O sistema deve alertar os usuários caso haja concorrência de edições para evitar conflitos (sobrescrever rascunhos), assegurando que apenas usuários com acesso autorizado de edição possam visualizar ou registrar a presença de colaboração.

---

## 2. Cenários de Uso

### Cenário 1: Presença em Tempo Real de Múltiplos Editores
* **Dado que** o Usuário A (autorizado a editar) está na página de edição do documento `guia-instalacao`,
* **Quando** o Usuário B (também autorizado a editar) abre a página de edição do mesmo documento `guia-instalacao`,
* **Então** o Usuário A deve visualizar um indicador visual moderno (ex: chip/avatar) mostrando que o Usuário B está ativo.
* **E** o Usuário B também deve visualizar o Usuário A na lista de editores ativos.

### Cenário 2: Bloqueio de Acesso para Usuários Não Autorizados
* **Dado que** o Usuário C não possui permissão de edição (ou não está autenticado) e tenta enviar uma requisição direta ao endpoint de colaboração de um documento,
* **Quando** a requisição é interceptada pelo middleware de edição (`edit_middleware`),
* **Então** o servidor deve rejeitar a requisição com status HTTP apropriado (`401 Unauthorized` ou `403 Forbidden`).
* **E** a presença do Usuário C não deve ser registrada nem exposta aos outros editores ativos.

### Cenário 3: Alerta de Risco de Conflitos
* **Dado que** o Usuário A e o Usuário B estão visualizando/editando o mesmo documento ao mesmo tempo,
* **Quando** ambos mantêm a página aberta enviando batimentos cardíacos,
* **Então** a interface deve exibir um alerta de aviso visível (Material Design 3 Alert Warning) indicando: *"Há outros colaboradores editando este documento. Cuidado para não sobrescrever rascunhos paralelos."*

### Cenário 4: Saída Automática de Colaborador (Expirado/Timeout)
* **Dado que** o Usuário B fecha o editor de `guia-instalacao` ou perde conexão de internet,
* **Quando** se passam 15 segundos desde o último batimento cardíaco recebido do Usuário B,
* **Então** o Usuário B deve ser automaticamente removido da lista de presença do documento para o Usuário A.

---

## 3. Requisitos Funcionais

### R1. Endpoint de Batimento de Colaboração (Heartbeat API)
* Criar uma rota protegida `POST /docs/{slug}/collaboration` que gerencia a presença do editor atual e retorna a lista de editores ativos.
* A rota deve estar sob o mesmo grupo de middleware que a rota de edição (`edit_middleware` + `docs_middleware`).
* O endpoint deve identificar o usuário logado usando `auth()->user()->name` ou `auth()->user()->email`. Caso o sistema não possua autenticação de usuários ativa (ex.: em testes ou instalações simples), o sistema deve utilizar o ID de sessão (`session()->getId()`) com um nome genérico (ex.: "Editor Anônimo [ID_Curto]") ou permitir a identificação baseada em cabeçalhos/IP.
* O estado de presença deve ser guardado no cache do Laravel (`Cache::get`/`Cache::put`) usando uma chave contendo o slug do documento para isolamento, ex: `documentation-engine:collaboration:{slug}`.
* O tempo de vida (TTL) de presença ativa deve ser de 15 segundos. Batimentos recebidos após esse tempo expiram o usuário na lista.

### R2. Polling no Cliente via JavaScript
* Na página de edição (`edit.blade.php`), adicionar um script que faz requisições `fetch` periódicas de 5 em 5 segundos do tipo POST para o endpoint `/docs/{slug}/collaboration`.
* O script deve enviar o token CSRF (`X-CSRF-TOKEN`) apropriado.
* Se a API retornar um erro de autorização (`401`/`403`), o script deve parar de fazer chamadas periódicas imediatamente e, opcionalmente, desabilitar os controles do editor ou notificar o usuário.

### R3. Interface de Presença (Visual M3)
* Implementar na parte superior do formulário de edição um painel dinâmico que exibe a lista de colaboradores ativos.
* O painel deve conter chips/badges contendo as iniciais ou nome do colaborador com cores de fundo dinâmicas.
* Caso haja mais de 1 colaborador ativo (além de si mesmo), renderizar um alerta de aviso (`.docs-alert-warning` ou semelhante no padrão M3) alertando sobre risco de concorrência.

---

## 4. Critérios de Sucesso

1. Múltiplos editores autorizados visualizam a presença uns dos outros na tela de edição do mesmo documento em tempo real (tempo de atualização de até 5 segundos).
2. Um usuário que fecha o navegador é removido da lista de colaboradores em até 15 segundos.
3. Usuários que não possuem autorização de edição recebem bloqueio HTTP (401/403) ao tentarem acessar o endpoint de colaboração direta ou indiretamente.
4. O alerta de concorrência é exibido somente quando há mais de um editor ativo editando o documento.
5. Todos os testes automatizados da aplicação continuam passando com sucesso.
