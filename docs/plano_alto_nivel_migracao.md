**Plano de Ação de Migração: Cotag (Java) para CotaG L12 (Laravel)**

**Visão Geral:** A migração será uma reescrita funcional do sistema "cotag" para a plataforma Laravel 12, aproveitando ao máximo a arquitetura moderna e as funcionalidades pré-configuradas do starter kit "CotaG L12". O foco será em replicar a lógica de negócio e as funcionalidades essenciais, adaptando a interface do usuário para a stack TALL (Tailwind, Alpine.js, Livewire, Volt), simplificando funcionalidades de UI complexas onde necessário.

---

**Fase 1: Configuração Central, Modelo de Dados e Autenticação Base**

1.  **Mapeamento e Migração do Modelo de Dados:**
    *   **Analisar Modelos Java:**
        *   `Usuario.java`: Mapeia para `App\Models\User` (Laravel). Campos a adicionar/verificar: `codpes` (já presente no CotaG L12), `nompes` (usar `name`), `email` (padrão). Relação com `Spatie\Permission\Models\Role`.
        *   `Papel.java`: Mapeia para `Spatie\Permission\Models\Role` (Laravel). Campos: `name`.
        *   `Pessoa.java`:
            *   **Decisão (1.C):** Avaliar campo a campo: `codpes` e `nompes` já mapeados para `User.codpes` e `User.name`. `vinculos` serão gerenciados via `uspdev/senhaunica-socialite` e não armazenados diretamente no `User`. Quaisquer outros campos de `Pessoa.java` serão avaliados; os frequentemente usados com autenticação irão para `User`, os menos usados ou muito específicos para um novo modelo `App\Models\UserProfile` (se necessário).
        *   `Cota.java`: Novo modelo Eloquent `App\Models\Cota`. Campos: `id`, `vinculo` (string), `valor` (integer).
        *   `CotaEspecial.java`: Novo modelo Eloquent `App\Models\CotaEspecial`. Campos: `id`, `user_id` (FK para `users.id`), `valor` (integer). Relação One-to-One com `User`.
        *   `Lancamento.java`: Novo modelo Eloquent `App\Models\Lancamento`. Campos: `id`, `user_id` (FK para `users.id` da pessoa da cota), `operador_id` (FK para `users.id` do registrador), `tipo_lancamento` (Enum PHP), `valor` (integer), `data` (datetime).
        *   `Log.java`:
            *   **Decisão (2.B):** Será substituído pela integração do pacote `spatie/laravel-activitylog`. Não será criado um modelo `LogAtividade` customizado.
        *   `RequisicaoSenha.java`: Utilizar o sistema de `password_reset_tokens` e fluxo do Laravel Breeze.
    *   **Criar Migrações Laravel:**
        *   Para `cotas`: `vinculo`, `valor`.
        *   Para `cota_especials`: `user_id` (foreign), `valor`.
        *   Para `lancamentos`: `user_id` (foreign), `operador_id` (foreign), `tipo_lancamento` (string para Enum), `data`.
        *   Instalar e rodar migrações do `spatie/laravel-activitylog`.
        *   Garantir que as migrações do `spatie/laravel-permission` e as básicas do `User` (CotaG L12) sejam executadas.
    *   **Definir Relacionamentos Eloquent:**
        *   `User` `hasOne` `CotaEspecial`.
        *   `User` `hasMany` `Lancamento` (como `pessoaCota`) e `hasMany` `Lancamento` (como `operadorLancamento`).
        *   `Lancamento` `belongsTo` `User` (como `pessoaCota`) e `belongsTo` `User` (como `operadorLancamento`).
        *   `CotaEspecial` `belongsTo` `User`.
    *   **Seeders:**
        *   `RoleSeeder.php` (CotaG L12):
            *   **Decisão (5.A):** Criar papéis Spatie: `Admin`, `Operator`, `User`, além dos já existentes `usp_user` e `external_user`.
        *   `CotaSeeder.php`: Criar para popular cotas padrão.
    *   **Estratégia de Migração de Dados (Alto Nível):**
        *   Exportar dados do MySQL do sistema Java.
        *   Criar Comandos Artisan no Laravel para importar.
        *   **Decisão (3.A):** Para usuários locais Java, forçar um reset de senha para todos, notificando-os por email para definirem uma nova senha no sistema Laravel. Não implementar re-hash de senhas antigas.

2.  **Autenticação e Autorização (Refinar CotaG L12):**
    *   **Autenticação Dupla (CotaG L12 já tem a base):**
        *   **Senha Única:** Utilizar `SocialiteController` e Trait `HasSenhaunica` do CotaG L12.
        *   **Local (Breeze):** Utilizar as views TALL e controllers/Volt components do Breeze.
    *   **Registro Local (Breeze + Replicado):**
        *   A view `register.blade.php` (Volt) do CotaG L12 já possui a lógica condicional para `codpes`.
        *   No controller/component Volt de registro:
            *   **Decisão (4.A):** Chamar `ReplicadoService`. Se a validação Replicado falhar (serviço indisponível ou dados não batem), impedir o registro completamente e mostrar erro claro.
            *   Atribuir role Spatie (`Admin`, `Operator`, `User`, `usp_user`, `external_user`) conforme regras de negócio (ex: primeiro registro pode ser `User`, `usp_user` se validado).
    *   **Autorização (Spatie + SenhaUnica):**
        *   **Decisão (15.A):** Para vínculos USP, confiar nas permissões do guard `senhaunica` (`$user->can('SERVIDOR@senhaunica')`). Não armazenar vínculos diretamente em `User` local.
    *   **Logout Senha Única:**
        *   **Decisão (10.A):** Invalidar apenas a sessão Laravel local. Não tentar redirecionar para logout centralizado da Senha Única.

---

**Fase 2: Migração de Funcionalidades (Iterativa)**
*   **Decisão (13.B):** A ordem de migração dos módulos será definida dinamicamente pela equipe/PO com base no valor de negócio, dependências e complexidade.

Para cada funcionalidade principal identificada no projeto Java "cotag" (ex: gerenciamento de Cotas, CotaEspecial, Lançamentos, Extrato, gerenciamento de Usuário/Papel, Logs):

1.  **Gerenciamento de Cotas (Regulares - `Cota`):**
    *   **Backend:** `CotaController` (Admin), `CotaPolicy`.
    *   **Frontend (Livewire/Volt):** Componentes para CRUD de cotas.
    *   **Testes:** Feature tests para CRUD, Policy tests.

2.  **Gerenciamento de Cotas Especiais (`CotaEspecial`):**
    *   **Backend:** `CotaEspecialController` (Admin).
    *   **Frontend (Livewire/Volt):** Componente de busca de usuário.
        *   **Decisão (6.B):** Permitir busca direta no Replicado (via `ReplicadoService`) e, se o usuário for encontrado mas não existir localmente, criá-lo no `App\Models\User` (com role `usp_user` e email verificado) antes de atribuir a cota especial.
    *   **Testes:** Feature e Policy.

3.  **Lançamentos de Cota (Débito/Crédito - `Lancamento`):**
    *   **Backend:** `LancamentoController` (Operator/Admin), Form Request.
        *   **Decisão (7.A):** Lógica de cálculo de saldo e verificação de limites em `App\Services\CotaService` (ou `LancamentoService`).
        *   **Decisão (17.A):** Usar Enum PHP (`App\Enums\TipoLancamento`) para `tipo_lancamento`.
    *   **Frontend (Livewire/Volt):** Componente `lancamento-form` interativo.
    *   **Testes:** Feature (registrar lançamento), Unit (cálculo de saldo no Service).

4.  **Extrato de Lançamentos:**
    *   **Backend:** `ExtratoController`.
    *   **Frontend (Livewire/Volt):** Componentes `extrato-pessoal` e `extrato-admin`.
        *   **Decisão (8.A):** Tela de admin/operador exibe lançamentos do mês atual para todos por padrão, com filtros.
    *   **Testes:** Feature para visualização com diferentes permissões.

5.  **Gerenciamento de Usuários e Papéis (Admin):**
    *   **Backend:** Adaptar/Usar controllers Breeze/Volt para perfil. `Admin\UserController`, `Admin\RoleController`.
    *   **Frontend (Livewire/Volt):**
        *   **Decisão (9.A):** UI Admin focada apenas na atribuição de papéis Spatie pré-definidos (`Admin`, `Operator`, `User`) aos usuários. Definição de permissões por papel via Seeders/código.
    *   **Testes:** Feature para gerenciamento de usuários e atribuição de papéis.

6.  **Logs de Atividade:**
    *   **Backend:**
        *   **Decisão (2.B):** Utilizar o pacote `spatie/laravel-activitylog` para registrar ações críticas. Configurar quais eventos/modelos logar.
        *   `Admin\ActivityLogController`: Para visualizar logs do Spatie.
    *   **Frontend (Livewire/Volt):** Componente para visualização e filtro dos logs do Spatie.
    *   **Testes:** Verificar se logs são criados para ações chave.

---

**Fase 3: Integrações USP e Utilitários**

1.  **Implementar `ReplicadoService.php`:**
    *   Métodos baseados em `DaoReplicado.java`.
    *   **Decisão (14.A):** Apenas um wrapper fino para `uspdev/replicado`. Caching não será implementado neste Service inicialmente.

2.  **Migrar/Substituir Utilitários Java:**
    *   `DataUtility.java`: Usar Carbon e helpers Laravel/PHP.
    *   `IntegerUtil.isInt()`: Validação numérica Laravel.
    *   `Ordenador*`: Coleções Laravel.
    *   `PasswordGenerator.java`: Desnecessário (Laravel Breeze cuida).
    *   `Recursos.java` (`system.properties`):
        *   **Decisão (20.C - Interpretada):** URLs OAuth (key, secret, endpoints) em `config/services.php` populadas via `.env`. URL de logout centralizado (se aplicável) em `.env` e acessada via `config()`.
    *   `messages.properties`: Migrar para `lang/*.json` (CotaG L12 já tem).
    *   `StringUtils.sgldepToCodset`:
        *   **Decisão (11.B):** Não será migrado, funcionalidade considerada não relevante.

3.  **Substituir Lógica JSF View Helpers:**
    *   Navegação (`PageTransitionBean.java`) por rotas Laravel.
    *   Conversores/Validadores JSF por Form Requests, Regras de Validação Customizadas, lógica Livewire/Blade.
    *   Mensagens de erro/sucesso (`MessageBean.java`):
        *   **Decisão (16.C):** Combinação de Flash (`session()->flash()`) para redirecionamentos tradicionais e mecanismos Livewire (`$this->dispatch()`, validação Livewire) para interações em componentes.

---

**Fase 4: Refinamento, Testes Finais e Considerações de Implantação**

1.  **Testes Abrangentes:** Conforme estrutura do CotaG L12.
2.  **Revisão e Refinamento da UI/UX:**
    *   **Decisão (12.A):** Simplificar componentes de UI complexos do PrimeFaces, focando no essencial com Livewire/Blade. Funcionalidades avançadas podem ser adicionadas depois.
    *   **Decisão (19.A):** Para funcionalidade de impressão, criar CSS específico (`@media print`).
3.  **Documentação:** Atualizar docs do CotaG L12.
4.  **CI/CD:** Usar workflow do CotaG L12.
5.  **Implantação:** Padrão Laravel.

---

**Desafios Potenciais Identificados (Revisados):**

1.  **Migração de Dados:** Reset forçado de senhas simplifica, mas a transformação dos outros dados ainda é um desafio.
2.  **Lógica de Negócio Complexa em EJBs/MBs:** Ainda requer reengenharia cuidadosa.
3.  **UI (Simplificada):** A simplificação de componentes PrimeFaces (Decisão 12.A) precisa ser bem comunicada e gerenciada em termos de expectativas.
4.  **Diferenças no Gerenciamento de Sessão e Estado JSF vs Laravel/Livewire.**