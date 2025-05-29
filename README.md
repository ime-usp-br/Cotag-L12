# CotaG L12 - Sistema de Controle de Cotas Gráficas (Versão Laravel 12)

**Versão:** 0.1.0-alpha (Em Desenvolvimento Ativo - Migração em Andamento)
**Data:** 2025-05-29 (Início da Migração)

[![Status da Build](https://github.com/ime-usp-br/Cotag-L12/actions/workflows/laravel.yml/badge.svg)](https://github.com/ime-usp-br/Cotag-L12/actions/workflows/laravel.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 1. Introdução

O **CotaG L12** é a nova versão do sistema de controle de cotas de impressão da gráfica do IME-USP, reconstruído sobre a plataforma PHP/Laravel 12. Este projeto visa migrar as funcionalidades essenciais do sistema legado "cotag" (Java EE/JSF) para uma arquitetura mais atual, aproveitando os recursos do **Laravel 12 USP Starter Kit**.

**Propósito e Justificativa:** A migração busca modernizar a base tecnológica do CotaG, melhorar sua manutenibilidade, escalabilidade e experiência do usuário. O objetivo é fornecer um sistema robusto, seguro e alinhado com as práticas de desenvolvimento web contemporâneas, mantendo a lógica de negócio central do sistema original de controle de cotas.

## 2. Público-Alvo

Este sistema destina-se a:

*   **Usuários Finais:** Alunos, docentes e servidores do IME-USP que utilizam os serviços de impressão da gráfica e precisam consultar seus saldos e extratos de cota.
*   **Operadores da Gráfica:** Responsáveis por registrar os débitos (uso de impressões) e créditos (recargas) nas cotas dos usuários.
*   **Administradores do Sistema:** Responsáveis por configurar cotas padrão, gerenciar cotas especiais, administrar usuários e papéis, e monitorar a atividade do sistema.

## 3. Principais Funcionalidades (Pós-Migração)

O CotaG L12, após a migração completa, incluirá:

*   **Base Laravel 12:** Estrutura moderna e eficiente.
*   **Autenticação Dupla:**
    *   **Senha Única USP:** Login integrado para usuários USP via `uspdev/senhaunica-socialite`.
    *   **Autenticação Local (Breeze TALL Stack):** Sistema de Login/Registro/Reset de Senha/Verificação de Email para usuários externos ou locais, com validação de dados USP (Nº USP + email) via `uspdev/replicado` durante o registro.
*   **Gerenciamento de Cotas:**
    *   **Cotas Regulares:** Definição de cotas de impressão mensais padrão baseadas no vínculo USP do usuário (ex: Aluno de Graduação, Servidor).
    *   **Cotas Especiais:** Atribuição de cotas personalizadas para usuários específicos, que podem sobrescrever ou complementar a cota regular.
*   **Lançamentos de Cota:**
    *   Registro de débitos (uso de impressões) e créditos (recargas manuais) nas cotas dos usuários pelos operadores/administradores.
    *   Cálculo automático de saldo disponível.
*   **Extrato de Lançamentos:**
    *   Usuários podem visualizar seu próprio histórico de lançamentos (débitos e créditos).
    *   Administradores/Operadores podem visualizar extratos de qualquer usuário ou extratos consolidados com filtros.
*   **Gerenciamento de Usuários e Papéis (Admin):**
    *   Administradores podem listar usuários e gerenciar seus papéis no sistema (`Admin`, `Operator`, `User`, `usp_user`, `external_user`) utilizando `spatie/laravel-permission`.
    *   Atribuição de papéis para controlar o acesso às funcionalidades.
*   **Log de Atividades (Admin):**
    *   Registro de ações importantes no sistema (logins, alterações de cotas, lançamentos) utilizando `spatie/laravel-activitylog` para auditoria.
*   **Interface do Usuário (UI) Moderna:**
    *   Frontend responsivo construído com a stack TALL (Tailwind CSS, Alpine.js, Livewire, Volt).
    *   Componentes Blade reutilizáveis, incluindo o cabeçalho padrão USP.
*   **Ferramentas de Qualidade e Testes:**
    *   Formatação de código com Laravel Pint, análise estática com Larastan.
    *   Testes automatizados (PHPUnit e Laravel Dusk) para garantir a robustez.
*   **Integração Contínua:** Workflows de CI/CD via GitHub Actions.

## 4. Stack Tecnológica (Pós-Migração)

*   **Framework:** Laravel 12
*   **Linguagem:** PHP >= 8.2
*   **Frontend (Stack TALL):** Livewire 3 (Class API), Alpine.js 3, Tailwind CSS 4, Vite.
*   **Banco de Dados:** Suporte padrão do Laravel (MySQL/MariaDB recomendado para produção).
*   **Integrações USP:** `uspdev/senhaunica-socialite`, `uspdev/replicado`.
*   **Autorização:** `spatie/laravel-permission`, `spatie/laravel-activitylog`.
*   **Testes:** PHPUnit, Laravel Dusk.
*   **Qualidade:** Laravel Pint, Larastan.

## 5. Instalação e Uso (Pós-Migração)

*(Esta seção será detalhada conforme a migração avança. Inicialmente, seguirá o padrão do Laravel 12 USP Starter Kit.)*

1.  **Pré-requisitos:** PHP, Composer, Node.js, NPM, Git, Banco de Dados (MySQL/MariaDB), Google Chrome (para Dusk).
2.  **Clonar o Repositório:** `git clone https://github.com/ime-usp-br/cotag_l12.git seu-projeto` (URL hipotética).
3.  **Instalar Dependências:** `composer install`, `npm install`.
4.  **Configurar Ambiente:** Copiar `.env.example` para `.env`, gerar chave (`php artisan key:generate`), configurar `APP_URL`, `DB_*`, `MAIL_*`, e as credenciais para Senha Única e Replicado (conforme documentação do CotaG L12 / Starter Kit).
5.  **Banco de Dados:** `php artisan migrate --seed`.
6.  **Compilar Assets:** `npm run build` (ou `npm run dev` para desenvolvimento).
7.  **Acessar:** Iniciar servidor (`php artisan serve`) e acessar via `APP_URL`.

## 6. Roteiro da Migração (Alto Nível)

O projeto de migração seguirá as seguintes fases principais:

1.  **Fase 1: Configuração Central e Modelo de Dados:**
    *   Mapeamento e migração do esquema de banco de dados e modelos Eloquent.
    *   Refinamento da autenticação dupla (Senha Única + Local Breeze) e configuração de papéis base com Spatie.
2.  **Fase 2: Migração de Funcionalidades Core (Iterativa):**
    *   Gerenciamento de Cotas (Regulares e Especiais).
    *   Lançamentos de Cota (Débito/Crédito).
    *   Extrato de Lançamentos.
    *   Gerenciamento de Usuários e Papéis (Admin).
    *   Implementação de Logs de Atividade.
3.  **Fase 3: Integrações USP e Utilitários:**
    *   Implementação completa do `ReplicadoService`.
    *   Migração/Substituição de utilitários e configurações do sistema Java.
4.  **Fase 4: Refinamento, Testes Finais e Documentação:**
    *   Suíte de testes abrangente (PHPUnit, Dusk).
    *   Revisão da UI/UX.
    *   Atualização completa da documentação (README, Wiki).
    *   Preparação para implantação.

*(Consulte o Plano de Ação de Migração detalhado na Wiki do projeto para mais informações sobre cada fase e tarefa específica.)*

## 7. Como Contribuir (Pós-Lançamento da Migração)

*(Esta seção será relevante após a conclusão da migração e o sistema estar estável. Por enquanto, o foco é na migração pela equipe principal.)*

Contribuições para melhorias e novas funcionalidades serão bem-vindas após o lançamento da versão migrada. Siga o fluxo descrito no **[Guia de Estratégia de Desenvolvimento do Laravel 12 USP Starter Kit](https://github.com/ime-usp-br/laravel_12_starter_kit/blob/main/docs/guia_de_desenvolvimento.md)** (ou o guia específico do CotaG L12, a ser criado).

## 8. Documentação

A documentação detalhada do CotaG L12, incluindo guias de configuração, arquitetura, como estender e contribuir, estará disponível na **[Wiki do GitHub deste projeto](https://github.com/ime-usp-br/cotag_l12/wiki)** (URL hipotética, a ser criada).

## 9. Licença

Este projeto (CotaG L12) é licenciado sob a **Licença MIT**. Veja o arquivo [LICENSE](./LICENSE) para mais detalhes.
