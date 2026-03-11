<?php

namespace App\Livewire;

use App\Models\Application;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplicationsBoard extends Component
{
    public const LEGACY_STAGE_MAP = [
        'aplicada' => 'applied',
        'aguardando' => 'waiting',
        'entrevista' => 'interview',
        'rejeitada' => 'rejected',
        'oferta' => 'offer',
    ];

    public $isCreateFormOpen = false;
    public $showFavoritesOnly = false;

    public $company;
    public $position;
    public $city;
    public $location;
    public $applied_at;
    public $job_url;
    public $personal_score;

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
    public $editNotes;

    public $interviewDate;
    public $interviewTime;
    public $interviewLocation;
    public $interviewIsRemote = false;
    public $interviewPlatform;
    public $interviewAddress;

    protected function hasStageColumn(): bool
    {
        return Schema::hasColumn('applications', 'stage');
    }

    protected function hasCityColumn(): bool
    {
        return Schema::hasColumn('applications', 'city');
    }

    protected function hasLocationColumn(): bool
    {
        return Schema::hasColumn('applications', 'location');
    }

    protected function hasPersonalScoreColumn(): bool
    {
        return Schema::hasColumn('applications', 'personal_score');
    }

    protected function hasFavoriteColumn(): bool
    {
        return Schema::hasColumn('applications', 'is_favorite');
    }

    protected function hasInterviewFields(): bool
    {
        return Schema::hasColumns('applications', [
            'interview_date',
            'interview_time',
            'interview_location',
            'interview_is_remote',
            'interview_platform',
            'interview_address',
        ]);
    }

    public function saveApplication()
    {
        $hasCityColumn = $this->hasCityColumn();
        $hasLocationColumn = $this->hasLocationColumn();
        $hasPersonalScoreColumn = $this->hasPersonalScoreColumn();

        $data = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => [$hasCityColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'location' => [$hasLocationColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
            'personal_score' => ['nullable', 'integer', 'between:0,10'],
        ]);

        $createData = [
            'company' => $data['company'],
            'position' => $data['position'],
            'applied_at' => $data['applied_at'],
            'job_url' => $data['job_url'] ?? null,
        ];

        if ($hasCityColumn) {
            $createData['city'] = $data['city'] ?? null;
        }

        if ($hasLocationColumn) {
            $createData['location'] = $data['location'] ?? null;
        }

        if ($hasPersonalScoreColumn) {
            $createData['personal_score'] = $data['personal_score'] ?? null;
        }

        if ($this->hasFavoriteColumn()) {
            $createData['is_favorite'] = false;
        }

        Application::create([
            ...$createData,
            'user_id' => Auth::id(),
            ...($this->hasStageColumn() ? ['stage' => 'applied'] : []),
            'status' => 'applied',
        ]);

        session()->flash('status', 'Application created successfully.');
        $this->resetCreateForm();
    }

    public function openCreateForm()
    {
        $this->resetCreateForm();
        $this->isCreateFormOpen = true;
    }

    public function closeCreateForm()
    {
        $this->resetCreateForm();
    }

    public function editApplication($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $this->editingApplicationId = $application->id;
        $this->isEditModalOpen = true;
        $this->editCompany = $application->company;
        $this->editPosition = $application->position;
        $this->editCity = $application->city;
        $this->editLocation = $application->location;
        $this->editAppliedAt = optional($application->applied_at)->format('Y-m-d');
        $this->editJobUrl = $application->job_url;
        $this->editPersonalScore = $application->personal_score;
        $this->editNotes = $application->notes;
    }

    public function updateApplication()
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $hasCityColumn = $this->hasCityColumn();
        $hasLocationColumn = $this->hasLocationColumn();
        $hasPersonalScoreColumn = $this->hasPersonalScoreColumn();

        $application = Application::where('user_id', Auth::id())
            ->find($this->editingApplicationId);

        if (!$application) {
            return;
        }

        $data = $this->validate([
            'editCompany' => ['required', 'string', 'max:255'],
            'editPosition' => ['required', 'string', 'max:255'],
            'editCity' => [$hasCityColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'editLocation' => [$hasLocationColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'editAppliedAt' => ['required', 'date'],
            'editJobUrl' => ['nullable', 'string', 'max:255'],
            'editPersonalScore' => ['nullable', 'integer', 'between:0,10'],
            'editNotes' => ['nullable', 'string'],
        ]);

        $updateData = [
            'company' => $data['editCompany'],
            'position' => $data['editPosition'],
            'applied_at' => $data['editAppliedAt'],
            'job_url' => $data['editJobUrl'],
            'notes' => $data['editNotes'],
        ];

        if ($hasCityColumn) {
            $updateData['city'] = $data['editCity'] ?? null;
        }

        if ($hasLocationColumn) {
            $updateData['location'] = $data['editLocation'] ?? null;
        }

        if ($hasPersonalScoreColumn) {
            $updateData['personal_score'] = $data['editPersonalScore'] ?? null;
        }

        $application->update($updateData);

        session()->flash('status', 'Application updated successfully.');
        $this->closeEditModal();
    }

    public function deleteApplication($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $application->delete();
        session()->flash('status', 'Application deleted successfully.');

        if ($this->editingApplicationId === (int) $id) {
            $this->closeEditModal();
        }
    }

    public function closeEditModal()
    {
        $this->resetEditForm();
    }

    #[On('prepareInterviewMove')]
    public function prepareInterviewMove($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $this->pendingInterviewApplicationId = $application->id;
        $this->isInterviewModalOpen = true;

        if ($this->hasInterviewFields()) {
            $this->interviewDate = $application->interview_date?->format('Y-m-d');
            $this->interviewTime = $application->interview_time;
            $this->interviewLocation = $application->interview_location;
            $this->interviewIsRemote = (bool) $application->interview_is_remote;
            $this->interviewPlatform = $application->interview_platform;
            $this->interviewAddress = $application->interview_address;
        }
    }

    public function closeInterviewModal()
    {
        $this->resetInterviewForm();
    }

    public function saveInterviewMove()
    {
        if (!$this->pendingInterviewApplicationId) {
            return;
        }

        $application = Application::where('user_id', Auth::id())
            ->find($this->pendingInterviewApplicationId);

        if (!$application) {
            return;
        }

        $data = $this->validate([
            'interviewDate' => ['required', 'date'],
            'interviewTime' => ['required'],
            'interviewLocation' => ['nullable', 'string', 'max:255'],
            'interviewIsRemote' => ['boolean'],
            'interviewPlatform' => ['nullable', 'string', 'max:255'],
            'interviewAddress' => [Rule::requiredIf(!$this->interviewIsRemote), 'nullable', 'string', 'max:255'],
        ]);

        if ($this->hasStageColumn()) {
            $application->stage = 'interview';
        }

        $application->status = 'interview';

        if ($this->hasInterviewFields()) {
            $application->interview_date = $data['interviewDate'];
            $application->interview_time = $data['interviewTime'];
            $application->interview_location = $data['interviewLocation'] ?? null;
            $application->interview_is_remote = (bool) $data['interviewIsRemote'];
            $application->interview_platform = $data['interviewIsRemote'] ? ($data['interviewPlatform'] ?? null) : null;
            $application->interview_address = $data['interviewIsRemote'] ? null : ($data['interviewAddress'] ?? null);
        }

        $application->save();

        session()->flash('status', 'Interview scheduled successfully.');
        $this->resetInterviewForm();
    }

    public function toggleFavorite($id)
    {
        if (!$this->hasFavoriteColumn()) {
            return;
        }

        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $application->is_favorite = !$application->is_favorite;
        $application->save();
    }

    public function toggleFavoritesFilter()
    {
        $this->showFavoritesOnly = !$this->showFavoritesOnly;
    }

    public function clearFavoritesFilter()
    {
        $this->showFavoritesOnly = false;
    }

    protected function resetCreateForm()
    {
        $this->reset([
            'isCreateFormOpen',
            'company',
            'position',
            'city',
            'location',
            'applied_at',
            'job_url',
            'personal_score',
        ]);

        $this->resetValidation();
    }

    protected function resetEditForm()
    {
        $this->reset([
            'editingApplicationId',
            'isEditModalOpen',
            'editCompany',
            'editPosition',
            'editCity',
            'editLocation',
            'editAppliedAt',
            'editJobUrl',
            'editPersonalScore',
            'editNotes',
        ]);

        $this->resetValidation();
    }

    protected function resetInterviewForm()
    {
        $this->reset([
            'isInterviewModalOpen',
            'pendingInterviewApplicationId',
            'interviewDate',
            'interviewTime',
            'interviewLocation',
            'interviewIsRemote',
            'interviewPlatform',
            'interviewAddress',
        ]);

        $this->interviewIsRemote = false;
        $this->resetValidation();
    }

    public function render()
    {
        $hasStageColumn = $this->hasStageColumn();
        $hasFavoriteColumn = $this->hasFavoriteColumn();

        $apps = Application::where('user_id', Auth::id())
            ->latest('updated_at')
            ->get();

        $favoritesCount = $hasFavoriteColumn
            ? $apps->where('is_favorite', true)->count()
            : 0;

        if ($this->showFavoritesOnly && $hasFavoriteColumn) {
            $apps = $apps->where('is_favorite', true);
        }

        if ($hasStageColumn) {
            $apps->each(function (Application $application) {
                $stage = $application->stage;

                if (!$stage) {
                    $application->stage = $application->status ?: 'applied';
                    return;
                }

                // Keep backward compatibility with legacy Portuguese stage values.
                if (isset(self::LEGACY_STAGE_MAP[$stage])) {
                    $application->stage = self::LEGACY_STAGE_MAP[$stage];
                }
            });
        }

        $total = $apps->count();

        $interviews = $apps->where($hasStageColumn ? 'stage' : 'status', 'interview')->count();

        $offers = $apps->where($hasStageColumn ? 'stage' : 'status', 'offer')->count();

        return view('livewire.applications-board', [

            'applied' => $apps->where($hasStageColumn ? 'stage' : 'status', 'applied'),

            'waiting' => $apps->where($hasStageColumn ? 'stage' : 'status', 'waiting'),

            'interview' => $apps->where($hasStageColumn ? 'stage' : 'status', 'interview'),

            'rejected' => $apps->where($hasStageColumn ? 'stage' : 'status', 'rejected'),

            'offer' => $apps->where($hasStageColumn ? 'stage' : 'status', 'offer'),

            'total' => $total,

            'interviews' => $interviews,

            'offers' => $offers,

            'favorites' => $favoritesCount,

            'showFavoritesOnly' => $this->showFavoritesOnly,

            'hasFavoriteColumn' => $hasFavoriteColumn,

        ]);
    }

    #[On('moveApplication')]
    public function moveApplication($id, $status)
    {
        $hasStageColumn = $this->hasStageColumn();

        $app = Application::find($id);

        if (!$app) {
            return;
        }

        if ($app->user_id != Auth::id()) {
            return;
        }

        // Accept either the new english values or legacy portuguese ones.
        if (isset(self::LEGACY_STAGE_MAP[$status])) {
            $status = self::LEGACY_STAGE_MAP[$status];
        }

        if (!in_array($status, ['applied', 'waiting', 'interview', 'rejected', 'offer'], true)) {
            return;
        }

        if ($hasStageColumn) {
            $app->stage = $status;
        }

        $app->status = $status;
        $app->save();

        session()->flash('status', 'Application moved successfully.');
    }
}