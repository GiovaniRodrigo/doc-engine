---
name: ui-ux
description: "Diretrizes e padrões de projeto para criar interfaces ricas, modernas, acessíveis e responsivas no desenvolvimento de software."
---

## 1. Skill Overview and Principles

Esta skill consolida princípios essenciais de Design Visual e Interação para que qualquer interface desenvolvida seja esteticamente polida, funcionalmente intuitiva, inclusiva e de alta performance.

### A. Princípios Fundamentais de Visual Design (C.A.R.P.)
1. **Contraste (Contrast):** Se dois elementos não são idênticos, diferencie-os drasticamente. Evite contrastes fracos (como cinza claro sobre cinza médio). Use contraste de cor, tamanho, peso tipográfico e espaçamento para guiar a atenção do usuário para os elementos mais importantes (ex: botões de chamada para ação - CTAs).
2. **Alinhamento (Alignment):** Cada elemento deve ter uma conexão visual com outro elemento na página. Nada deve ser colocado de forma arbitrária. Utilize grades (grids) e linhas guias invisíveis. O alinhamento correto cria uma sensação de ordem, limpeza e profissionalismo.
3. **Repetição (Repetition):** Repita aspectos visuais do design em toda a interface. Elementos como cores, formas, texturas, relações espaciais, espessuras de linha, fontes, tamanhos e conceitos gráficos devem ser consistentes para unificar o sistema e melhorar a usabilidade.
4. **Proximidade (Proximity):** Agrupe elementos relacionados para que pareçam uma única unidade visual. A proximidade reduz a desordem visual e fornece uma estrutura de conteúdo clara. Se elementos não estão relacionados, afaste-os.

### B. Estética Rica e Moderna
* **Cores HSL Harmônicas:** Prefira o modelo HSL (`hsl(hue, saturation, lightness)`) ao invés de Hex ou RGB para declarar cores. Ele simplifica a criação de paletas harmônicas, escalas de cinza cromáticas (adicionando um pouco de *saturation* e *hue* do tema principal nos tons de cinza para torná-los menos "mortos") e controle dinâmico de luminosidade para Dark/Light modes.
* **Dark Mode Nativo:** Projete interfaces pensando em ambos os temas desde o início. Use CSS custom properties (variáveis CSS) para trocar a paleta de cores com base nas preferências do sistema ou classes do elemento raiz (HTML).
* **Glassmorphism:** Técnica que simula o vidro fosco usando fundo translúcido, bordas sutis brilhantes e desfoque de fundo (`backdrop-filter: blur()`). Excelente para modais, sidebars e cabeçalhos fixos sobre conteúdos fluidos.
* **Sombras Suaves (Soft Shadows):** Evite sombras pretas padrão e duras. Use sombras em camadas com opacidade reduzida e difusão ampla, utilizando a própria cor de fundo ou um tom escuro cromático correspondente à paleta da página.
* **Tipografia Refinada:** 
  - Limite o uso a no máximo duas famílias de fontes (uma para títulos, outra para corpo).
  - Use alturas de linha adequadas (ex: `line-height: 1.5` para texto corrido, `1.2` para títulos).
  - Ajuste o espaçamento entre letras (`letter-spacing`) — ligeiramente mais fechado em títulos grandes e ligeiramente mais aberto em textos pequenos ou em uppercase.
  - Implemente escala tipográfica proporcional (ex: Major Second ou Major Third para definir os tamanhos das fontes).

### C. Interatividade e Micro-animações
* **Transições Suaves:** Todas as mudanças de estado visuais (`:hover`, `:focus-visible`, `:active`) devem ter transições explícitas com duração (geralmente entre `150ms` e `300ms`) e funções de interpolação suaves (`cubic-bezier` ou `ease-out`).
* **Estados de Interação:**
  - **Hover:** Indica que o elemento é clicável. Use pequenas mudanças de cor de fundo, elevação de sombra ou um sutil deslocamento vertical (`transform: translateY(-2px)`).
  - **Focus-Visible:** Essencial para navegação por teclado. Nunca remova o outline padrão sem fornecer um anel de foco personalizado, vibrante e com alto contraste.
  - **Active:** Feedback imediato do clique. Um sutil encolhimento (`transform: scale(0.98)`) ou escurecimento do botão.
* **Micro-animações:** Use-as para feedbacks de sucesso, carregamento ou transição de estados de dados (ex: skeleton screens para carregamento, checkmarks animados após finalização).

### D. Acessibilidade (a11y) e Responsividade
* **Diretrizes WCAG (Web Content Accessibility Guidelines):**
  - **Contraste Mínimo:** Garantir contraste de no mínimo 4.5:1 para texto normal e 3:1 para texto grande (18pt+/24px+).
  - **Navegação por Teclado:** Toda a interface funcional deve ser operável via teclado (`Tab`, `Enter`, `Space`, setas direcionais).
  - **Uso correto de ARIA:** Tags semânticas são sempre preferíveis. Use atributos ARIA (`aria-expanded`, `aria-hidden`, `aria-invalid`, `aria-live`) para comunicar estados dinâmicos a leitores de tela.
* **Responsividade Mobile-First:**
  - Comece desenhando para a menor tela e adicione complexidade usando breakpoints de media queries para telas maiores.
  - Evite tamanhos de fonte ou larguras rígidas em pixels; use unidades relativas (`rem`, `em`, `%`, `vw`, `vh`).
  - Toques amigáveis em mobile: áreas clicáveis devem ter pelo menos `44px x 44px`.

### E. Design Systems e Componentes Reutilizáveis
* **Tokens de Design (Design Tokens):** Centralize variáveis de espaçamento, cores, raios de borda, sombras e fontes em um único local para garantir consistência.
* **Componentização Semântica:** Crie componentes isolados com APIs claras. Garanta que o estado interno do componente (ex: se está aberto, ativo ou desabilitado) seja refletido tanto visualmente quanto semanticamente para tecnologias assistivas.

### F. Bases e Referências (shadcn/ui & Material Design 3)
Ao projetar e construir interfaces, utilize como principal referência conceitual e estrutural:
* **shadcn/ui (Estilo Moderno, Minimalismo e Acessibilidade Web):**
  - **Princípios:** Total customização por código (copy-paste), separação estrita de comportamento (Radix UI) e estilo (Tailwind CSS/CSS Variables).
  - **Estética:** Bordas limpas, cantos arredondados moderados (`0.5rem` / `8px`), contraste acentuado no escuro/claro, sombras suaves e semântica de cores neutras com acentos fortes.
  - **Acessibilidade:** Foco total em conformidade com WAI-ARIA, controle de foco avançado e componentes sem estilo base para máxima flexibilidade.
* **Material Design 3 - M3 (Sistema de Cores Dinâmicas e Hierarquia de Componentes):**
  - **Princípios:** Cores dinâmicas baseadas em papéis estritos (Primary, Secondary, Tertiary, Container, Surface e Outline) que geram paletas harmônicas a partir de uma cor semente (Seed Color).
  - **Estética:** Cantos bem arredondados (`border-radius: 28px` para FABs e modais, `12px`/`16px` para cards e campos de entrada), elevações baseadas em tonalidades/sombras sutis e tipografia bem estruturada (Display, Headline, Title, Body, Label).
  - **Interação:** Feedback de toque expressivo (efeito Ripple ou equivalentes de estado), transições baseadas em físicas e comportamento de componentes adaptáveis.

---

## 2. Templates / Implementation Patterns

### A. Estrutura de Design Tokens (CSS Variables & HSL)

#### 1. Padrão shadcn/ui (CSS Variables com HSL)
Este padrão é ideal para layouts minimalistas, modernos e limpos.
```css
:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;

  --card: 0 0% 100%;
  --card-foreground: 222.2 84% 4.9%;

  --popover: 0 0% 100%;
  --popover-foreground: 222.2 84% 4.9%;

  --primary: 221.2 83.2% 53.3%;
  --primary-foreground: 210 40% 98%;

  --secondary: 210 40% 96.1%;
  --secondary-foreground: 222.2 47.4% 11.2%;

  --muted: 210 40% 96.1%;
  --muted-foreground: 215.4 16.3% 46.9%;

  --accent: 210 40% 96.1%;
  --accent-foreground: 222.2 47.4% 11.2%;

  --destructive: 0 84.2% 60.2%;
  --destructive-foreground: 210 40% 98%;

  --border: 214.3 31.8% 91.4%;
  --input: 214.3 31.8% 91.4%;
  --ring: 221.2 83.2% 53.3%;

  --radius: 0.5rem;
  
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-normal: 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.dark {
  --background: 222.2 84% 4.9%;
  --foreground: 210 40% 98%;

  --card: 222.2 84% 4.9%;
  --card-foreground: 210 40% 98%;

  --popover: 222.2 84% 4.9%;
  --popover-foreground: 210 40% 98%;

  --primary: 217.2 91.2% 59.8%;
  --primary-foreground: 222.2 47.4% 11.2%;

  --secondary: 217.2 32.6% 17.5%;
  --secondary-foreground: 210 40% 98%;

  --muted: 217.2 32.6% 17.5%;
  --muted-foreground: 215 20.2% 65.1%;

  --accent: 217.2 32.6% 17.5%;
  --accent-foreground: 210 40% 98%;

  --destructive: 0 62.8% 30.6%;
  --destructive-foreground: 210 40% 98%;

  --border: 217.2 32.6% 17.5%;
  --input: 217.2 32.6% 17.5%;
  --ring: 224.3 76.3% 48%;
}
```

#### 2. Padrão Material Design 3 (M3 - CSS Variables com HSL)
Este padrão é focado em papéis de cores dinâmicas e maior expressividade visual.
```css
:root {
  /* Cores de Marca (M3 Roles) - Base Violeta */
  --md-sys-color-primary: hsl(262, 52%, 47%);
  --md-sys-color-on-primary: hsl(0, 0%, 100%);
  --md-sys-color-primary-container: hsl(262, 85%, 90%);
  --md-sys-color-on-primary-container: hsl(262, 60%, 15%);

  --md-sys-color-secondary: hsl(262, 10%, 40%);
  --md-sys-color-on-secondary: hsl(0, 0%, 100%);
  --md-sys-color-secondary-container: hsl(262, 20%, 90%);
  --md-sys-color-on-secondary-container: hsl(262, 25%, 15%);

  --md-sys-color-surface: hsl(262, 15%, 98%);
  --md-sys-color-on-surface: hsl(262, 30%, 10%);
  --md-sys-color-surface-container: hsl(262, 12%, 94%);
  
  --md-sys-color-outline: hsl(262, 10%, 50%);
  --md-sys-color-outline-variant: hsl(262, 12%, 85%);

  /* Border Radii característicos do M3 */
  --md-shape-corner-small: 8px;
  --md-shape-corner-medium: 12px;
  --md-shape-corner-large: 16px;
  --md-shape-corner-extra-large: 28px;
  --md-shape-corner-full: 9999px;
  
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-normal: 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

@media (prefers-color-scheme: dark) {
  :root {
    --md-sys-color-primary: hsl(262, 80%, 75%);
    --md-sys-color-on-primary: hsl(262, 60%, 20%);
    --md-sys-color-primary-container: hsl(262, 50%, 35%);
    --md-sys-color-on-primary-container: hsl(262, 85%, 90%);

    --md-sys-color-secondary: hsl(262, 20%, 75%);
    --md-sys-color-on-secondary: hsl(262, 20%, 20%);
    --md-sys-color-secondary-container: hsl(262, 25%, 35%);
    --md-sys-color-on-secondary-container: hsl(262, 20%, 90%);

    --md-sys-color-surface: hsl(262, 15%, 10%);
    --md-sys-color-on-surface: hsl(262, 10%, 90%);
    --md-sys-color-surface-container: hsl(262, 12%, 14%);
    
    --md-sys-color-outline: hsl(262, 10%, 60%);
    --md-sys-color-outline-variant: hsl(262, 12%, 30%);
  }
}
```

### B. Glassmorphism Moderno (CSS)
```css
.glass-panel {
  background-color: var(--glass-bg);
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
}
```

### C. Componente de Botão Acessível e Interativo
```html
<button class="btn btn-primary" type="button" aria-label="Enviar formulário de cadastro">
  <span>Enviar Cadastro</span>
  <!-- Símbolo opcional / Indicador de carregamento escondido via aria-hidden -->
  <svg class="spinner" aria-hidden="true" viewBox="0 0 24 24">...</svg>
</button>
```
```css
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  font-family: inherit;
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  transition: background-color var(--transition-fast), 
              transform var(--transition-fast), 
              box-shadow var(--transition-fast);
}

.btn-primary {
  background-color: var(--color-primary);
  color: #ffffff; /* Alto contraste garantido */
}

/* Hover State */
.btn-primary:hover {
  background-color: var(--color-primary-hover);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Active State */
.btn-primary:active {
  background-color: var(--color-primary-active);
  transform: translateY(1px);
  box-shadow: var(--shadow-sm);
}

/* Focus State (Acessibilidade) */
.btn-primary:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px var(--color-bg), 
              0 0 0 6px var(--color-primary);
}

/* Redução de movimento respeitando preferências do SO do usuário */
@media (prefers-reduced-motion: reduce) {
  .btn, .glass-panel {
    transition: none !important;
    transform: none !important;
  }
}
```

### D. Checklist de Auditoria de Componente (UI/UX e a11y)
1. [ ] **Semântica:** O elemento HTML correto foi utilizado? (Ex: `<button>` em vez de `<div onclick="...">`).
2. [ ] **Contraste:** A taxa de contraste do texto sobre o fundo atende ao mínimo WCAG (4.5:1)?
3. [ ] **Teclado:** O elemento recebe foco sequencial através da tecla `Tab`? Possui estilo claro de `:focus-visible`?
4. [ ] **Estados:** O elemento tem estilos distintos e transições suaves para Hover, Active e Focus?
5. [ ] **Responsividade:** O componente se comporta corretamente em telas estreitas (320px)? O texto sofre quebras ou overflow inesperado?
6. [ ] **Touch Target:** No mobile, o botão/link possui no mínimo 44x44 pixels de área clicável (incluindo padding)?

---

## 3. Step-by-Step Instructions

Ao ser acionada, esta skill orienta o assistente de IA a executar os seguintes passos sistemáticos para auditoria, redesenho ou implementação de interfaces:

### Passo 1: Análise da Interface e Código Existente
* **Auditoria Visual (C.A.R.P.):** Examine a distribuição dos componentes. O alinhamento é consistente? Há contraste suficiente para criar hierarquia visual? Componentes relacionados estão próximos?
* **Semântica e Acessibilidade:** Verifique a marcação HTML. Há divs com eventos de clique que deveriam ser botões? Faltam atributos ARIA em componentes dinâmicos (como modais, menus suspensos, abas)?
* **Verificação de Responsividade:** Revise o uso de CSS. Existem larguras fixas exageradas em pixels (`width: 600px`) que quebram no mobile?

### Passo 2: Alinhamento com o Usuário
Antes de escrever o código final de UI/UX, faça perguntas-chave para obter contexto:
1. *"Qual é a paleta de cores ou a cor principal da marca (em HSL ou Hex)?"*
2. *"Deseja utilizar como base estética o **shadcn/ui** (design moderno, minimalista, cantos discretos, Tailwind/CSS Variables) ou o **Material Design 3 (M3)** (cantos arredondados, cores dinâmicas com papéis de cores definidos, componentes expressivos)?"*
3. *"Qual a principal biblioteca visual ou framework CSS sendo utilizado (CSS Puro, Tailwind CSS, Bootstrap, Material UI, etc.)?"*
4. *"O projeto já possui suporte a Dark Mode? Deseja que a implementação inclua suporte nativo a Dark Mode?"*
5. *"Existe alguma fonte específica do projeto ou podemos utilizar uma stack de fontes do sistema modernas e refinadas?"*

### Passo 3: Implementação Refinada
* **Configuração de Tokens:** Crie ou adapte os tokens de design (CSS custom properties ou classes de configuração do Tailwind) com base na escolha da base estética (shadcn/ui ou M3) e nas escolhas do usuário.
* **Marcação Semântica e ARIA:** Escreva a estrutura HTML focada em acessibilidade antes do estilo. Garanta que leitores de tela entendam o propósito e o estado do componente.
* **Design Visual com CSS/Tailwind:**
  - Se a base for **shadcn/ui**, aplique o design minimalista, cantos arredondados discretos (`0.5rem`/`radius-md`), sombras bem sutis e transições rápidas.
  - Se a base for **Material Design 3 (M3)**, aplique papéis de cores claros (Primary, Primary Container, Surface, Outline), cantos bem arredondados (`8px` a `28px`), transições suaves (ex: efeito ripple para feedback de toque) e elevação tonal.
  - Garanta que todas as fontes e espaçamentos usem escalas proporcionais baseadas em `rem`.
* **Estados e Transições:** Adicione as propriedades CSS de transição e crie os estados `:hover`, `:active` e `:focus-visible`.
* **Mobile-First Layout:** Use Flexbox/CSS Grid responsivos, configurando media queries para layouts que requeiram colunas múltiplas em desktops.

### Passo 4: Validação e Avaliação do Resultado
Forneça um relatório detalhado das melhorias implementadas, abordando:
1. **Diferenças Antes/Depois:** O que mudou em termos de design visual e usabilidade.
2. **Resultado de Acessibilidade:** Confirmação do suporte a teclado, descrição do foco e dos atributos ARIA adicionados.
3. **Responsividade:** Explicação de como o componente se comporta em diferentes tamanhos de tela (breakpoints).
4. **Desempenho Visual:** Demonstração do uso de transições leves e suporte a `prefers-reduced-motion`.
