<?php

namespace App\Livewire;

use App\Concerns\DetectsApplicationColumns;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplicationsBoard extends Component
{
    use DetectsApplicationColumns;

    private const BOARD_STATUSES = ['applied', 'waiting', 'interview', 'rejected', 'offer'];
    private const BOARD_CHUNK_SIZE = 20;

    public const LEGACY_STAGE_MAP = [
        'aplicada' => 'applied',
        'aguardando' => 'waiting',
        'entrevista' => 'interview',
        'rejeitada' => 'rejected',
        'oferta' => 'offer',
    ];

    public $isCreateFormOpen = false;
    public $showFavoritesOnly = false;
    public $showArchivedSection = false;
    public $kanbanOrientation = 'horizontal';
    public $searchQuery = '';
    public $isSearching = false;
    public $statusFilters = [];
    public $showDuplicates = false;
    public $isBoardPage = false;
    public $columnVisibleLimits = [];

    public $company;
    public $position;
    public $city;
    public $location;
    public $applied_at;
    public $job_url;
    public $personal_score;
    public $salary_offered;
    public $salary_expected;

    public $editingApplicationId = null;
    public $isEditModalOpen = false;
    public $isInterviewModalOpen = false;
    public $pendingInterviewApplicationId = null;

    public $editCompany;
    public $editPosition;
    public $editCity;
    public $editLocation;
    public $editAppliedAt;
    public $editJobUrl;
    public $editPersonalScore;
    public $editSalaryOffered;
    public $editSalaryExpected;
    public $editNotes;
    public $editInterviewDate;
    public $editInterviewTime;
    public $editInterviewLocation;
    public $editInterviewIsRemote = false;
    public $editInterviewPlatform;
    public $editInterviewAddress;
    public $editingIsInterview = false;
    public $editingCurrentStatus = null;

    public $interviewDate;
    public $interviewTime;
    public $interviewLocation;
    public $interviewIsRemote = false;
    public $interviewPlatform;
    public $interviewAddress;

    protected ApplicationService $service;

    public function mount(): void
    {
        $this->isBoardPage = request()->routeIs('board');

        $orientation = session('kanban_orientation', 'horizontal');
        $this->kanbanOrientation = in_array($orientation, ['horizontal', 'vertical'], true)
            ? $orientation
            : 'horizontal';
        
        // Initialize status filters - all statuses selected by default
        $this->statusFilters = self::BOARD_STATUSES;

        foreach (self::BOARD_STATUSES as $status) {
            $this->columnVisibleLimits[$status] = self::BOARD_CHUNK_SIZE;
        }
    }

    /** Inject the service via Livewire's boot hook (not serialised between requests). */
    public function boot(ApplicationService $service): void
    {
        $this->service = $service;
    }

    // =========================================================================
    // Create
    // =========================================================================

    public function openCreateForm(): void
    {
        $this->resetCreateForm();
        $this->isCreateFormOpen = true;
    }

    public function closeCreateForm(): void
    {
        $this->resetCreateForm();
    }

    public function saveApplication(): void
    {
        $data = $this->validate($this->createValidationRules());

        $this->service->create($data);

        session()->flash('status', 'Application created successfully.');
        $this->resetCreateForm();
    }

    // =========================================================================
    // Edit
    // =========================================================================

    public function editApplication(int $id): void
    {
        $application = $this->service->findForUser($id, Auth::id());

        if (!$application) {
            return;
        }

        $this->fillEditForm($application);
    }

    public function updateApplication(): void
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $application = $this->service->findForUser($this->editingApplicationId, Auth::id());

        if (!$application) {
            return;
        }

        $data = $this->validate($this->updateValidationRules());

        $this->service->update($application, $this->mapEditFormToServiceData($data));

        session()->flash('status', 'Application updated successfully.');
        $this->closeEditModal();
    }

    public function closeEditModal(): void
    {
        $this->resetEditForm();
    }

    public function moveEditingApplicationToNextStage(): void
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $application = $this->service->findForUser($this->editingApplicationId, Auth::id());

        if (!$application) {
            return;
        }

        $currentStatus = $this->resolveStatus($application);

        $nextStageMap = [
            'applied' => 'waiting',
            'waiting' => 'interview',
            'interview' => 'offer',
        ];

        $nextStatus = $nextStageMap[$currentStatus] ?? null;

        if (!$nextStatus) {
            session()->flash('status', 'This application is already in the final stage.');
            return;
        }

        // For interview transitions, always collect date/time through interview modal.
        if ($nextStatus === 'interview') {
            $applicationId = $application->id;
            $this->closeEditModal();
            $this->prepareInterviewMove($applicationId);
            return;
        }

        $this->service->move($application, $nextStatus);

        session()->flash('status', 'Application moved to next stage successfully.');
        $this->closeEditModal();
    }

    public function moveEditingApplicationToPreviousStage(): void
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $application = $this->service->findForUser($this->editingApplicationId, Auth::id());

        if (!$application) {
            return;
        }

        $currentStatus = $this->resolveStatus($application);

        $previousStageMap = [
            'waiting' => 'applied',
            'interview' => 'waiting',
            'offer' => 'interview',
        ];

        $previousStatus = $previousStageMap[$currentStatus] ?? null;

        if (!$previousStatus) {
            session()->flash('status', 'This application cannot move back a stage.');
            return;
        }

        // For interview transitions, always collect date/time through interview modal.
        if ($previousStatus === 'interview') {
            $applicationId = $application->id;
            $this->closeEditModal();
            $this->prepareInterviewMove($applicationId);
            return;
        }

        $this->service->move($application, $previousStatus);

        session()->flash('status', 'Application moved to previous stage successfully.');
        $this->closeEditModal();
    }

    public function archiveEditingApplication(): void
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $application = $this->service->findForUser($this->editingApplicationId, Auth::id());

        if (!$application) {
            return;
        }

        $this->service->move($application, 'archived');

        session()->flash('status', 'Application archived successfully.');
        $this->closeEditModal();
    }

    // =========================================================================
    // Delete
    // =========================================================================

    public function deleteApplication(int $id): void
    {
        $application = $this->service->findForUser($id, Auth::id());

        if (!$application) {
            return;
        }

        $this->service->delete($application);

        session()->flash('status', 'Application deleted successfully.');

        if ($this->editingApplicationId === $id) {
            $this->closeEditModal();
        }
    }

    // =========================================================================
    // Interview scheduling
    // =========================================================================

    #[On('prepareInterviewMove')]
    public function prepareInterviewMove(?int $id = null): void
    {
        if (!$id) {
            return;
        }

        $application = $this->service->findForUser($id, Auth::id());

        if (!$application) {
            return;
        }

        $this->pendingInterviewApplicationId = $application->id;
        $this->isInterviewModalOpen = true;
        $this->fillInterviewForm($application);
    }

    public function saveInterviewMove(): void
    {
        if (!$this->pendingInterviewApplicationId) {
            return;
        }

        $application = $this->service->findForUser($this->pendingInterviewApplicationId, Auth::id());

        if (!$application) {
            return;
        }

        $data = $this->validate($this->interviewValidationRules());

        $this->service->scheduleInterview($application, $this->mapInterviewFormToServiceData($data));

        session()->flash('status', 'Interview scheduled successfully.');
        $this->resetInterviewForm();
    }

    public function closeInterviewModal(): void
    {
        $this->resetInterviewForm();
    }

    // =========================================================================
    // Kanban drag-and-drop
    // =========================================================================

    #[On('moveApplication')]
    public function moveApplication(?int $id = null, ?string $status = null, array $orderedIds = []): void
    {
        if (!$id || !$status) {
            return;
        }

        $application = $this->service->findForUser($id, Auth::id());

        if (!$application) {
            return;
        }

        // Normalise legacy Portuguese stage values to English.
        $status = self::LEGACY_STAGE_MAP[$status] ?? $status;

        if (!in_array($status, ['applied', 'waiting', 'interview', 'rejected', 'offer'], true)) {
            return;
        }

        $previousStatus = $application->status;

        $this->service->move($application, $status);

        if (!empty($orderedIds)) {
            $this->service->reorderForUserStatus(Auth::id(), $status, $orderedIds);
        }

        if ($previousStatus !== $status) {
            session()->flash('status', 'Application moved successfully.');
        }
    }

    // =========================================================================
    // Favourites
    // =========================================================================

    public function toggleFavorite(int $id): void
    {
        $application = $this->service->findForUser($id, Auth::id());

        if (!$application) {
            return;
        }

        $this->service->toggleFavorite($application);
    }

    public function toggleFavoritesFilter(): void
    {
        $this->showFavoritesOnly = !$this->showFavoritesOnly;
    }

    public function clearFavoritesFilter(): void
    {
        $this->showFavoritesOnly = false;
    }

    // =========================================================================
    // Archive
    // =========================================================================

    public function toggleArchivedSection(): void
    {
        $this->showArchivedSection = !$this->showArchivedSection;
    }

    public function toggleKanbanOrientation(): void
    {
        $this->kanbanOrientation = $this->kanbanOrientation === 'horizontal' ? 'vertical' : 'horizontal';
        session(['kanban_orientation' => $this->kanbanOrientation]);
    }

    // =========================================================================
    // Search
    // =========================================================================

    public function updatedSearchQuery($value): void
    {
        $this->isSearching = strlen(trim($value)) > 0;
    }

    public function clearSearch(): void
    {
        $this->searchQuery = '';
        $this->isSearching = false;
    }

    private function matchesSearch(Application $app, string $query): bool
    {
        $lowerQuery = strtolower($query);

        return str_contains(strtolower($app->company ?? ''), $lowerQuery)
            || str_contains(strtolower($app->position ?? ''), $lowerQuery)
            || str_contains(strtolower($app->city ?? ''), $lowerQuery)
            || str_contains(strtolower($app->location ?? ''), $lowerQuery)
            || str_contains(strtolower($app->job_url ?? ''), $lowerQuery)
            || str_contains(strtolower($app->notes ?? ''), $lowerQuery);
    }

    private function filterBySearch($collection): \Illuminate\Support\Collection
    {
        if (!$this->isSearching || empty(trim($this->searchQuery))) {
            return $collection;
        }

        return $collection->filter(fn (Application $app) => $this->matchesSearch($app, $this->searchQuery));
    }

    // =========================================================================
    // Status Filters
    // =========================================================================

    public function toggleStatusFilter(string $status): void
    {
        if (in_array($status, $this->statusFilters, true)) {
            $this->statusFilters = array_filter($this->statusFilters, fn ($s) => $s !== $status);
        } else {
            $this->statusFilters[] = $status;
            sort($this->statusFilters);
        }
    }

    public function resetStatusFilters(): void
    {
        $this->statusFilters = ['applied', 'waiting', 'interview', 'rejected', 'offer'];
    }

    public function updateStatusFilters(string $value): void
    {
        if (empty($value)) {
            $this->resetStatusFilters();
        } else {
            $this->statusFilters = [$value];
        }
    }

    public function loadMore(string $status): void
    {
        if (!in_array($status, self::BOARD_STATUSES, true)) {
            return;
        }

        $current = (int) ($this->columnVisibleLimits[$status] ?? self::BOARD_CHUNK_SIZE);
        $this->columnVisibleLimits[$status] = $current + self::BOARD_CHUNK_SIZE;
    }

    public function toggleDuplicatesFilter(): void
    {
        $this->showDuplicates = !$this->showDuplicates;
    }

    public function clearDuplicatesFilter(): void
    {
        $this->showDuplicates = false;
    }

    private function filterByStatus($collection): \Illuminate\Support\Collection
    {
        if (empty($this->statusFilters)) {
            return $collection;
        }

        $statusField = $this->hasStageColumn() ? 'stage' : 'status';
        return $collection->filter(fn (Application $app) => in_array($app->{$statusField}, $this->statusFilters, true));
    }

    // =========================================================================
    // Duplicate Detection
    // =========================================================================

    private function duplicateKey(Application $app): string
    {
        return strtolower(trim($app->company ?? '')) . '|' . strtolower(trim($app->position ?? ''));
    }

    private function findDuplicateGroups($collection): \Illuminate\Support\Collection
    {
        return $collection
            ->groupBy(fn (Application $app) => $this->duplicateKey($app))
            ->filter(fn ($group, $key) => $key !== '|' && $group->count() > 1);
    }

    private function getDuplicateReasons($duplicateGroups): array
    {
        $reasons = [];

        foreach ($duplicateGroups as $group) {
            $first = $group->first();

            $fields = [
                'company' => 'company',
                'position' => 'position',
                'city' => 'city',
                'location' => 'location',
                'job_url' => 'job URL',
            ];

            $sameFields = [];

            foreach ($fields as $field => $label) {
                $baseValue = strtolower(trim((string) ($first->{$field} ?? '')));

                if ($baseValue === '') {
                    continue;
                }

                $allMatch = $group->every(function (Application $app) use ($field, $baseValue) {
                    return strtolower(trim((string) ($app->{$field} ?? ''))) === $baseValue;
                });

                if ($allMatch) {
                    $sameFields[] = $label;
                }
            }

            $reason = empty($sameFields)
                ? 'Possible duplicate by very similar information.'
                : 'Duplicate because these fields are equal: ' . implode(', ', $sameFields) . '.';

            foreach ($group as $app) {
                $reasons[$app->id] = $reason;
            }
        }

        return $reasons;
    }

    private function buildCalendarApplications($applications): array
    {
        return $applications
            ->filter(fn (Application $app) => !empty($app->applied_at))
            ->sortByDesc('applied_at')
            ->values()
            ->map(fn (Application $app) => [
                'id' => $app->id,
                'company' => (string) ($app->company ?? ''),
                'position' => (string) ($app->position ?? ''),
                'status' => $this->resolveStatus($app),
                'city' => (string) ($app->city ?? ''),
                'location' => (string) ($app->location ?? ''),
                'applied_at' => $app->applied_at?->format('Y-m-d'),
                'applied_label' => $app->applied_at?->format('d/m/Y'),
            ])
            ->all();
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        $userId          = Auth::id();
        $statusField     = $this->hasStageColumn()   ? 'stage' : 'status';
        $hasFavoriteColumn = $this->hasFavoriteColumn();

        $this->service->archiveExpiredIfDue($userId);

        $apps       = $this->service->allForUser($userId);
        $archived   = $apps->where($statusField, 'archived');
        $activeApps = $this->resolveActiveApps($apps, $statusField, $hasFavoriteColumn);

        // Apply status filters
        $filteredApps = $this->filterByStatus($activeApps);

        // On board page, search should filter kanban cards directly.
        if ($this->isBoardPage && $this->isSearching) {
            $filteredApps = $this->filterBySearch($filteredApps);
        }

        // Detect duplicate groups in active apps
        $duplicateGroups = $this->findDuplicateGroups($filteredApps);
        $duplicateIds = $duplicateGroups->flatten()->pluck('id')->all();
        $duplicateReasons = $this->getDuplicateReasons($duplicateGroups);
        $duplicateCount = $duplicateGroups->count();

        if ($this->showDuplicates) {
            $filteredApps = $filteredApps->filter(
                fn (Application $app) => in_array($app->id, $duplicateIds, true)
            );
        }

        $columnTotals = [];
        $columnRemaining = [];
        $columnItems = [];

        foreach (self::BOARD_STATUSES as $status) {
            $allForStatus = $filteredApps->where($statusField, $status)->values();
            $totalCount = $allForStatus->count();
            $limit = (int) ($this->columnVisibleLimits[$status] ?? self::BOARD_CHUNK_SIZE);

            $columnTotals[$status] = $totalCount;
            $columnRemaining[$status] = max(0, $totalCount - $limit);
            $columnItems[$status] = $this->isBoardPage
                ? $allForStatus->take($limit)->values()
                : $allForStatus;
        }

        // Apply search filter if searching
        if ($this->isSearching && !$this->isBoardPage) {
            $allApps = $filteredApps->merge($archived);
            $searchResults = $this->filterBySearch($allApps);
            $calendarApplications = $this->isBoardPage ? [] : $this->buildCalendarApplications($filteredApps);

            return view('livewire.applications-board', [
                'applied'           => collect(),
                'waiting'           => collect(),
                'interview'         => collect(),
                'rejected'          => collect(),
                'offer'             => collect(),
                'columnTotals'      => array_fill_keys(self::BOARD_STATUSES, 0),
                'columnRemaining'   => array_fill_keys(self::BOARD_STATUSES, 0),
                'archived'          => collect(),
                'searchResults'     => $searchResults,
                'total'             => $filteredApps->count(),
                'interviews'        => $filteredApps->where($statusField, 'interview')->count(),
                'offers'            => $filteredApps->where($statusField, 'offer')->count(),
                'archivedCount'     => $archived->count(),
                'favorites'         => $hasFavoriteColumn ? $filteredApps->where('is_favorite', true)->count() : 0,
                'duplicateCount'    => $duplicateCount,
                'duplicateIds'      => $duplicateIds,
                'duplicateReasons'  => $duplicateReasons,
                'showDuplicates'    => $this->showDuplicates,
                'showFavoritesOnly' => $this->showFavoritesOnly,
                'showArchivedSection' => $this->showArchivedSection,
                'hasFavoriteColumn' => $hasFavoriteColumn,
                'kanbanOrientation' => $this->kanbanOrientation,
                'isSearching'       => $this->isSearching,
                'searchQuery'       => $this->searchQuery,
                'statusFilters'     => $this->statusFilters,
                'calendarApplications' => $calendarApplications,
            ]);
        }

        $calendarApplications = $this->isBoardPage ? [] : $this->buildCalendarApplications($filteredApps);

        return view('livewire.applications-board', [
            'applied'           => $columnItems['applied'],
            'waiting'           => $columnItems['waiting'],
            'interview'         => $columnItems['interview'],
            'rejected'          => $columnItems['rejected'],
            'offer'             => $columnItems['offer'],
            'columnTotals'      => $columnTotals,
            'columnRemaining'   => $columnRemaining,
            'archived'          => $archived,
            'searchResults'     => collect(),
            'total'             => $filteredApps->count(),
            'interviews'        => $filteredApps->where($statusField, 'interview')->count(),
            'offers'            => $filteredApps->where($statusField, 'offer')->count(),
            'archivedCount'     => $archived->count(),
            'favorites'         => $hasFavoriteColumn ? $filteredApps->where('is_favorite', true)->count() : 0,
            'duplicateCount'    => $duplicateCount,
            'duplicateIds'      => $duplicateIds,
            'duplicateReasons'  => $duplicateReasons,
            'showDuplicates'    => $this->showDuplicates,
            'showFavoritesOnly' => $this->showFavoritesOnly,
            'showArchivedSection' => $this->showArchivedSection,
            'hasFavoriteColumn' => $hasFavoriteColumn,
            'kanbanOrientation' => $this->kanbanOrientation,
            'isSearching'       => $this->isSearching,
            'searchQuery'       => $this->searchQuery,
            'statusFilters'     => $this->statusFilters,
            'calendarApplications' => $calendarApplications,
        ]);
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    private function resolveActiveApps($apps, string $statusField, bool $hasFavoriteColumn)
    {
        $activeApps = $apps->where($statusField, '!=', 'archived');

        if ($this->hasStageColumn()) {
            $this->normalizeLegacyStages($activeApps);
        }

        if ($this->showFavoritesOnly && $hasFavoriteColumn) {
            return $activeApps->where('is_favorite', true);
        }

        return $activeApps;
    }

    private function normalizeLegacyStages($applications): void
    {
        $applications->each(function (Application $application) {
            if (!$application->stage) {
                $application->stage = $application->status ?: 'applied';
                return;
            }

            // Translate old Portuguese stage values to their English equivalents.
            if (isset(self::LEGACY_STAGE_MAP[$application->stage])) {
                $application->stage = self::LEGACY_STAGE_MAP[$application->stage];
            }
        });
    }

    private function resolveStatus(Application $application): string
    {
        $status = $this->hasStageColumn()
            ? ($application->stage ?: $application->status)
            : $application->status;

        return self::LEGACY_STAGE_MAP[$status] ?? $status;
    }

    private function fillEditForm(Application $application): void
    {
        $this->editingApplicationId   = $application->id;
        $this->isEditModalOpen        = true;
        $this->editCompany            = $application->company;
        $this->editPosition           = $application->position;
        $this->editCity               = $application->city;
        $this->editLocation           = $application->location;
        $this->editAppliedAt          = optional($application->applied_at)->format('Y-m-d');
        $this->editJobUrl             = $application->job_url;
        $this->editPersonalScore      = $application->personal_score;
        $this->editSalaryOffered      = $application->salary_offered;
        $this->editSalaryExpected     = $application->salary_expected;
        $this->editNotes              = $application->notes;
        $this->editingIsInterview     = ($application->stage ?? $application->status) === 'interview';
        $this->editInterviewDate      = $application->interview_date?->format('Y-m-d');
        $this->editInterviewTime      = $application->interview_time;
        $this->editInterviewLocation  = $application->interview_location;
        $this->editInterviewIsRemote  = (bool) $application->interview_is_remote;
        $this->editInterviewPlatform  = $application->interview_platform;
        $this->editInterviewAddress   = $application->interview_address;
        $this->editingCurrentStatus   = $this->resolveStatus($application);
    }

    private function fillInterviewForm(Application $application): void
    {
        $this->interviewDate      = $application->interview_date?->format('Y-m-d');
        $this->interviewTime      = $application->interview_time;
        $this->interviewLocation  = $application->interview_location;
        $this->interviewIsRemote  = (bool) $application->interview_is_remote;
        $this->interviewPlatform  = $application->interview_platform;
        $this->interviewAddress   = $application->interview_address;
    }

    /** Maps validated edit-form fields to the shape expected by UpdateApplication. */
    private function mapEditFormToServiceData(array $validated): array
    {
        return [
            'company'              => $validated['editCompany'],
            'position'             => $validated['editPosition'],
            'city'                 => $validated['editCity']          ?? null,
            'location'             => $validated['editLocation']      ?? null,
            'applied_at'           => $validated['editAppliedAt'],
            'job_url'              => $validated['editJobUrl']        ?? null,
            'personal_score'       => $validated['editPersonalScore'] ?? null,
            'salary_offered'       => $validated['editSalaryOffered'] ?? null,
            'salary_expected'      => $validated['editSalaryExpected'] ?? null,
            'notes'                => $validated['editNotes']         ?? null,
            'is_interview'         => $this->editingIsInterview,
            'interview_date'       => $validated['editInterviewDate']      ?? null,
            'interview_time'       => $validated['editInterviewTime']      ?? null,
            'interview_location'   => $validated['editInterviewLocation']  ?? null,
            'interview_is_remote'  => $validated['editInterviewIsRemote']  ?? false,
            'interview_platform'   => $validated['editInterviewPlatform']  ?? null,
            'interview_address'    => $validated['editInterviewAddress']   ?? null,
        ];
    }

    /** Maps validated interview-form fields to the shape expected by ScheduleInterview. */
    private function mapInterviewFormToServiceData(array $validated): array
    {
        return [
            'interview_date'      => $validated['interviewDate'],
            'interview_time'      => $validated['interviewTime'],
            'interview_location'  => $validated['interviewLocation'] ?? null,
            'interview_is_remote' => $validated['interviewIsRemote'] ?? false,
            'interview_platform'  => $validated['interviewPlatform'] ?? null,
            'interview_address'   => $validated['interviewAddress']  ?? null,
        ];
    }

    // =========================================================================
    // Validation rules
    // =========================================================================

    private function createValidationRules(): array
    {
        return [
            'company'          => ['required', 'string', 'max:255'],
            'position'         => ['required', 'string', 'max:255'],
            'city'             => [$this->hasCityColumn()     ? 'required' : 'nullable', 'string', 'max:255'],
            'location'         => [$this->hasLocationColumn() ? 'required' : 'nullable', 'string', 'max:255'],
            'applied_at'       => ['required', 'date'],
            'job_url'          => ['nullable', 'string', 'max:255'],
            'personal_score'   => ['nullable', 'numeric', 'between:0,10'],
            'salary_offered'   => ['nullable', 'numeric', 'min:0'],
            'salary_expected'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    private function updateValidationRules(): array
    {
        return [
            'editCompany'          => ['required', 'string', 'max:255'],
            'editPosition'         => ['required', 'string', 'max:255'],
            'editCity'             => [$this->hasCityColumn()     ? 'required' : 'nullable', 'string', 'max:255'],
            'editLocation'         => [$this->hasLocationColumn() ? 'required' : 'nullable', 'string', 'max:255'],
            'editAppliedAt'        => ['required', 'date'],
            'editJobUrl'           => ['nullable', 'string', 'max:255'],
            'editPersonalScore'    => ['nullable', 'numeric', 'between:0,10'],
            'editSalaryOffered'    => ['nullable', 'numeric', 'min:0'],
            'editSalaryExpected'   => ['nullable', 'numeric', 'min:0'],
            'editNotes'            => ['nullable', 'string'],
            'editInterviewDate'    => [$this->editingIsInterview ? 'required' : 'nullable', 'date'],
            'editInterviewTime'    => [$this->editingIsInterview ? 'required' : 'nullable'],
            'editInterviewLocation'  => ['nullable', 'string', 'max:255'],
            'editInterviewIsRemote'  => ['boolean'],
            'editInterviewPlatform'  => ['nullable', 'string', 'max:255'],
            'editInterviewAddress'   => ['nullable', 'string', 'max:255'],
        ];
    }

    private function interviewValidationRules(): array
    {
        return [
            'interviewDate'      => ['required', 'date'],
            'interviewTime'      => ['required'],
            'interviewLocation'  => ['nullable', 'string', 'max:255'],
            'interviewIsRemote'  => ['boolean'],
            'interviewPlatform'  => ['nullable', 'string', 'max:255'],
            'interviewAddress'   => ['nullable', 'string', 'max:255'],
        ];
    }

    // =========================================================================
    // Form resets
    // =========================================================================

    protected function resetCreateForm(): void
    {
        $this->reset([
            'isCreateFormOpen',
            'company', 'position', 'city', 'location',
            'applied_at', 'job_url', 'personal_score',
            'salary_offered', 'salary_expected',
        ]);

        $this->resetValidation();
    }

    protected function resetEditForm(): void
    {
        $this->reset([
            'editingApplicationId', 'isEditModalOpen',
            'editCompany', 'editPosition', 'editCity', 'editLocation',
            'editAppliedAt', 'editJobUrl', 'editPersonalScore',
            'editSalaryOffered', 'editSalaryExpected', 'editNotes',
            'editInterviewDate', 'editInterviewTime', 'editInterviewLocation',
            'editInterviewIsRemote', 'editInterviewPlatform', 'editInterviewAddress',
            'editingIsInterview', 'editingCurrentStatus',
        ]);

        $this->editInterviewIsRemote = false;
        $this->resetValidation();
    }

    protected function resetInterviewForm(): void
    {
        $this->reset([
            'isInterviewModalOpen', 'pendingInterviewApplicationId',
            'interviewDate', 'interviewTime', 'interviewLocation',
            'interviewIsRemote', 'interviewPlatform', 'interviewAddress',
        ]);

        $this->interviewIsRemote = false;
        $this->resetValidation();
    }
}
