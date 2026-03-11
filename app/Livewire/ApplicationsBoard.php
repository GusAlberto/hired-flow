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

    public $interviewDate;
    public $interviewTime;
    public $interviewLocation;
    public $interviewIsRemote = false;
    public $interviewPlatform;
    public $interviewAddress;

    protected ApplicationService $service;

    public function mount(): void
    {
        $orientation = session('kanban_orientation', 'horizontal');
        $this->kanbanOrientation = in_array($orientation, ['horizontal', 'vertical'], true)
            ? $orientation
            : 'horizontal';
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
    public function moveApplication(?int $id = null, ?string $status = null): void
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

        $this->service->move($application, $status);

        session()->flash('status', 'Application moved successfully.');
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

        return view('livewire.applications-board', [
            'applied'           => $activeApps->where($statusField, 'applied'),
            'waiting'           => $activeApps->where($statusField, 'waiting'),
            'interview'         => $activeApps->where($statusField, 'interview'),
            'rejected'          => $activeApps->where($statusField, 'rejected'),
            'offer'             => $activeApps->where($statusField, 'offer'),
            'archived'          => $archived,
            'total'             => $activeApps->count(),
            'interviews'        => $activeApps->where($statusField, 'interview')->count(),
            'offers'            => $activeApps->where($statusField, 'offer')->count(),
            'archivedCount'     => $archived->count(),
            'favorites'         => $hasFavoriteColumn ? $activeApps->where('is_favorite', true)->count() : 0,
            'showFavoritesOnly' => $this->showFavoritesOnly,
            'showArchivedSection' => $this->showArchivedSection,
            'hasFavoriteColumn' => $hasFavoriteColumn,
            'kanbanOrientation' => $this->kanbanOrientation,
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
            'personal_score'   => ['nullable', 'integer', 'between:0,10'],
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
            'editPersonalScore'    => ['nullable', 'integer', 'between:0,10'],
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
            'editingIsInterview',
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
