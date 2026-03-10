<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
    public $company;
    public $position;
    public $city;
    public $location;
    public $applied_at;
    public $job_url;

    public $editingApplicationId = null;
    public $isEditModalOpen = false;
    public $editCompany;
    public $editPosition;
    public $editCity;
    public $editLocation;
    public $editAppliedAt;
    public $editJobUrl;
    public $editNotes;

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

    public function saveApplication()
    {
        $hasCityColumn = $this->hasCityColumn();
        $hasLocationColumn = $this->hasLocationColumn();

        $data = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => [$hasCityColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'location' => [$hasLocationColumn ? 'required' : 'nullable', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
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
        $this->editNotes = $application->notes;
    }

    public function updateApplication()
    {
        if (!$this->editingApplicationId) {
            return;
        }

        $hasCityColumn = $this->hasCityColumn();
        $hasLocationColumn = $this->hasLocationColumn();

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
            'editNotes',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $hasStageColumn = $this->hasStageColumn();

        $apps = Application::where('user_id', Auth::id())
            ->latest('updated_at')
            ->get();

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