<?php

namespace App\Livewire;

use App\Models\Application;
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
    public $isLocationRemote = false;
    public $applied_at;
    public $job_url;
    public $personal_score;
    public $salary_offered;
    public $salary_expected;
    public $job_summary;

    public $editingApplicationId = null;
    public $isEditModalOpen = false;
    public $isInterviewModalOpen = false;
    public $pendingInterviewApplicationId = null;

    public $editCompany;
    public $editPosition;
    public $editCity;
    public $editLocation;
    public $isEditLocationRemote = false;
    public $editAppliedAt;
    public $editJobUrl;
    public $editPersonalScore;
    public $editSalaryOffered;
    public $editSalaryExpected;
    public $editJobSummary;
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

    protected function hasSalaryOfferedColumn(): bool
    {
        return Schema::hasColumn('applications', 'salary_offered');
    }

    protected function hasSalaryExpectedColumn(): bool
    {
        return Schema::hasColumn('applications', 'salary_expected');
    }

    protected function hasJobSummaryColumn(): bool
    {
        return Schema::hasColumn('applications', 'job_summary');
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
        $hasSalaryOfferedColumn = $this->hasSalaryOfferedColumn();
        $hasSalaryExpectedColumn = $this->hasSalaryExpectedColumn();
        $hasJobSummaryColumn = $this->hasJobSummaryColumn();

        if ($this->isLocationRemote) {
            $this->location = 'remote';
        }

        $data = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => [$hasCityColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'location' => [$hasLocationColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
            'personal_score' => ['nullable', 'integer', 'between:0,10'],
            'salary_offered' => ['nullable', 'numeric', 'min:0'],
            'salary_expected' => ['nullable', 'numeric', 'min:0'],
            'job_summary' => ['nullable', 'string', 'max:1500'],
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

        if ($hasSalaryOfferedColumn) {
            $createData['salary_offered'] = $data['salary_offered'] ?? null;
        }

        if ($hasSalaryExpectedColumn) {
            $createData['salary_expected'] = $data['salary_expected'] ?? null;
        }

        if ($hasJobSummaryColumn) {
            $createData['job_summary'] = $data['job_summary'] ?? null;
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

    public function updatedIsLocationRemote($value): void
    {
        if ($value) {
            $this->location = 'remote';
            return;
        }

        if (strtolower((string) $this->location) === 'remote') {
            $this->location = null;
        }
    }

    public function updatedLocation($value): void
    {
        $this->isLocationRemote = strtolower(trim((string) $value)) === 'remote';
    }

    public function updatedIsEditLocationRemote($value): void
    {
        if ($value) {
            $this->editLocation = 'remote';
            return;
        }

        if (strtolower((string) $this->editLocation) === 'remote') {
            $this->editLocation = null;
        }
    }

    public function updatedEditLocation($value): void
    {
        $this->isEditLocationRemote = strtolower(trim((string) $value)) === 'remote';
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
        $this->isEditLocationRemote = strtolower((string) $application->location) === 'remote';
        $this->editAppliedAt = optional($application->applied_at)->format('Y-m-d');
        $this->editJobUrl = $application->job_url;
        $this->editPersonalScore = $application->personal_score;
        $this->editSalaryOffered = $application->salary_offered;
        $this->editSalaryExpected = $application->salary_expected;
        $this->editJobSummary = $application->job_summary;
        $this->editNotes = $application->notes;
        $this->editingIsInterview = ($application->stage ?? $application->status) === 'interview';

        if ($this->hasInterviewFields()) {
            $this->editInterviewDate = $application->interview_date?->format('Y-m-d');
            $this->editInterviewTime = $application->interview_time;
            $this->editInterviewLocation = $application->interview_location;
            $this->editInterviewIsRemote = (bool) $application->interview_is_remote;
            $this->editInterviewPlatform = $application->interview_platform;
            $this->editInterviewAddress = $application->interview_address;
        }
    }

    public function updateApplication()
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $hasCityColumn = $this->hasCityColumn();
        $hasLocationColumn = $this->hasLocationColumn();
        $hasPersonalScoreColumn = $this->hasPersonalScoreColumn();
        $hasSalaryOfferedColumn = $this->hasSalaryOfferedColumn();
        $hasSalaryExpectedColumn = $this->hasSalaryExpectedColumn();
        $hasJobSummaryColumn = $this->hasJobSummaryColumn();
        $hasInterviewFields = $this->hasInterviewFields();

        if ($this->isEditLocationRemote) {
            $this->editLocation = 'remote';
        }

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
            'editSalaryOffered' => ['nullable', 'numeric', 'min:0'],
            'editSalaryExpected' => ['nullable', 'numeric', 'min:0'],
            'editJobSummary' => ['nullable', 'string', 'max:1500'],
            'editNotes' => ['nullable', 'string'],
            'editInterviewDate' => [$this->editingIsInterview ? 'required' : 'nullable', 'date'],
            'editInterviewTime' => [$this->editingIsInterview ? 'required' : 'nullable'],
            'editInterviewLocation' => ['nullable', 'string', 'max:255'],
            'editInterviewIsRemote' => ['boolean'],
            'editInterviewPlatform' => ['nullable', 'string', 'max:255'],
            'editInterviewAddress' => ['nullable', 'string', 'max:255'],
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

        if ($hasSalaryOfferedColumn) {
            $updateData['salary_offered'] = $data['editSalaryOffered'] ?? null;
        }

        if ($hasSalaryExpectedColumn) {
            $updateData['salary_expected'] = $data['editSalaryExpected'] ?? null;
        }

        if ($hasJobSummaryColumn) {
            $updateData['job_summary'] = $data['editJobSummary'] ?? null;
        }

        if ($hasInterviewFields) {
            $updateData['interview_date'] = $this->editingIsInterview ? ($data['editInterviewDate'] ?? null) : null;
            $updateData['interview_time'] = $this->editingIsInterview ? ($data['editInterviewTime'] ?? null) : null;
            $updateData['interview_location'] = $this->editingIsInterview ? ($data['editInterviewLocation'] ?? null) : null;
            $updateData['interview_is_remote'] = $this->editingIsInterview ? (bool) ($data['editInterviewIsRemote'] ?? false) : false;
            $updateData['interview_platform'] = $this->editingIsInterview && ($data['editInterviewIsRemote'] ?? false)
                ? ($data['editInterviewPlatform'] ?? null)
                : null;
            $updateData['interview_address'] = $this->editingIsInterview && !($data['editInterviewIsRemote'] ?? false)
                ? ($data['editInterviewAddress'] ?? null)
                : null;
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
            'interviewAddress' => ['nullable', 'string', 'max:255'],
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
            'isLocationRemote',
            'applied_at',
            'job_url',
            'personal_score',
            'salary_offered',
            'salary_expected',
            'job_summary',
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
            'isEditLocationRemote',
            'editAppliedAt',
            'editJobUrl',
            'editPersonalScore',
            'editSalaryOffered',
            'editSalaryExpected',
            'editJobSummary',
            'editNotes',
            'editInterviewDate',
            'editInterviewTime',
            'editInterviewLocation',
            'editInterviewIsRemote',
            'editInterviewPlatform',
            'editInterviewAddress',
            'editingIsInterview',
        ]);

        $this->editInterviewIsRemote = false;

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