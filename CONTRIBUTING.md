# Contributing Guide — Hired Flow

Guia de referência para adicionar novas funcionalidades ou alterar as existentes de forma organizada e consistente com a arquitetura do projeto.

---

## Arquitetura em camadas

```
app/
├── Actions/              # Operações de negócio atômicas (uma ação = um arquivo)
├── Concerns/             # Traits reutilizáveis entre camadas
├── Http/
│   ├── Controllers/      # Controladores HTTP (páginas, endpoints REST)
│   └── Requests/         # Form Requests (validação de entrada HTTP)
├── Livewire/             # Componentes de UI (apenas estado e validação)
├── Models/               # Eloquent models (dados + casts + relações)
├── Repositories/         # Acesso direto ao banco de dados
├── Services/             # Orquestradores de regra de negócio
└── View/Components/      # Blade Components de layout
```

---

## Quando alterar cada camada

### `Models/`
**Altere quando:**
- Adicionar ou remover uma coluna do `$fillable`
- Adicionar um cast (`$casts`)
- Definir uma nova relação (`hasMany`, `belongsTo`, etc.)
- Adicionar um scope de Query

**Não coloque aqui:**
- Lógica de negócio
- Queries complexas
- Chamadas a outros serviços

**Arquivo principal:** `app/Models/Application.php`

---

### `database/migrations/`
**Altere quando:**
- Adicionar ou remover colunas de uma tabela existente
- Criar uma nova tabela
- Alterar um ENUM

**Regra:** sempre use `Schema::hasColumn()` como guarda antes de `addColumn` em migrations que alteram tabelas existentes — garante que a migration é re-executável sem falhas em ambientes legados.

```php
if (!Schema::hasColumn('applications', 'nova_coluna')) {
    $table->string('nova_coluna')->nullable();
}
```

---

### `Concerns/DetectsApplicationColumns.php`
**Altere quando:**
- Adicionar uma nova coluna opcional na tabela `applications`
- Precisar checar a existência dessa coluna em mais de um lugar (Action, Service, Livewire)

**Exemplo para nova coluna `primazde`:**
```php
public function hasPrioridadeColumn(): bool
{
    return Schema::hasColumn('applications', 'prioridade');
}
```

---

### `Actions/`
**Crie um novo arquivo quando:**
- Precisar realizar **uma operação atômica e bem definida** sobre um model
- A operação for reutilizável em contextos diferentes (Livewire, Controller, Job, etc.)

**Convenção de nomenclatura:** verbo + entidade → `CreateApplication`, `ArchiveApplication`, `SendInterviewReminder`

**Estrutura padrão:**
```php
class MinhaAction
{
    use DetectsApplicationColumns; // se precisar de schema guards

    public function execute(Application $application, array $data): void
    {
        // faz uma única coisa
    }
}
```

**Registre no service** se for chamado pelo `ApplicationService`.

---

### `Repositories/ApplicationRepository.php`
**Altere quando:**
- Precisar de uma nova query de banco de dados
- A query for usada em mais de um lugar

**Não coloque aqui:**
- Lógica de negócio
- Regras condicionais complexas
- Flash messages

**Exemplo:**
```php
public function findByInterviewDate(Carbon $date, int $userId): Collection
{
    return Application::where('user_id', $userId)
        ->whereDate('interview_date', $date)
        ->get();
}
```

---

### `Services/ApplicationService.php`
**Altere quando:**
- Orquestrar múltiplas Actions ou Repositories em sequência
- Adicionar lógica cross-cutting (cache, log, notificação) junto a uma operação

**Não coloque aqui:**
- Queries diretas ao banco (use o Repository)
- Código de UI (Livewire/blade)
- Validação de formulário

**Estrutura ao adicionar um método:**
```php
public function arquivarManualmente(Application $application): void
{
    $this->arquivarAction->execute($application);
    // log, evento, notificação se necessário
}
```

---

### `Livewire/ApplicationsBoard.php`
**Altere quando:**
- Adicionar ou remover campos de formulário públicos (`public $campo`)
- Adicionar novas regras de validação
- Alterar estado de UI (modais, filtros, seções)
- Conectar um novo método do `ApplicationService` à interface

**Não coloque aqui:**
- Queries ao banco
- Regras de negócio
- Lógica de archiving, movimentação, etc.

**Padrão ao adicionar uma nova ação de UI:**
```php
public function minhaAcao(int $id): void
{
    $application = $this->service->findForUser($id, Auth::id());

    if (!$application) {
        return;
    }

    $this->service->minhaAcao($application);

    session()->flash('status', 'Mensagem de feedback.');
}
```

---

### `Http/Controllers/`
**Altere quando:**
- Adicionar uma rota HTTP comum (settings, páginas, API)
- Processar um form POST fora do Livewire

**Arquivo principal hoje:** `app/Http/Controllers/SettingsController.php`

---

### `routes/web.php`
**Altere quando:**
- Adicionar uma nova página ou endpoint
- Registrar uma nova rota para um Controller existente

**Todas as rotas autenticadas ficam dentro do grupo `middleware('auth')`.**

---

## Fluxo típico para uma nova feature

Exemplo: adicionar campo `prioridade` às vagas.

```
1. migration        → adicionar coluna `prioridade` à tabela `applications`
2. Model            → adicionar `prioridade` ao $fillable e $casts
3. Concerns         → adicionar hasPrioridadeColumn() no trait
4. Action           → criar/atualizar CreateApplication e UpdateApplication para incluir o campo
5. Service          → nenhuma mudança (Actions já resolvem)
6. Livewire         → adicionar public $prioridade, regra de validação e prop no modal
7. Blade            → adicionar input no create-modal e edit-modal, exibir no card
8. Seeder           → atualizar ApplicationSeeder com o novo campo fake
```

---

## Convenções de branches e commits

| Tipo | Prefixo | Exemplo |
|------|---------|---------|
| Nova feature | `feat/` | `feat/add-priority-field` |
| Refatoração | `refactor/` | `refactor/clean-functions` |
| Correção | `fix/` | `fix/archive-date-boundary` |
| Configuração | `chore/` | `chore/update-dependencies` |

**Commit messages:**
```
feat: add priority field to application cards
fix: correct archive threshold from <= to <
refactor: extract interview scheduling into ScheduleInterview action
chore: update ApplicationSeeder with priority field
```

---

## Comandos úteis do dia a dia

```bash
# Subir containers
./vendor/bin/sail up -d

# Rodar migrations
sail artisan migrate

# Popular banco com dados fake
sail artisan db:seed

# Popular apenas aplicações
sail artisan db:seed --class=ApplicationSeeder

# Compilar assets (produção)
sail npm run build

# Desenvolver com hot reload
sail npm run dev

# Limpar caches
sail artisan config:clear
sail artisan view:clear
sail artisan cache:clear
```
