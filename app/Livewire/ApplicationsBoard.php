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

    public $editingApplicationId = null;
    public $isFormOpen = false;
    public $company;
    public $position;
    public $city;
    public $location;
    public $applied_at;
    public $job_url;
    public $notes;

    protected function hasStageColumn(): bool
    {
        return Schema::hasColumn('applications', 'stage');
    }

    public function saveApplication()
    {
        $data = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->editingApplicationId) {
            $application = Application::where('user_id', Auth::id())
                ->find($this->editingApplicationId);

            if (!$application) {
                return;
            }

            $application->update($data);

            session()->flash('status', 'Application updated successfully.');
            $this->resetForm();

            return;
        }

        Application::create([
            ...collect($data)->except('notes')->all(),
            'user_id' => Auth::id(),
            ...($this->hasStageColumn() ? ['stage' => 'applied'] : []),
            'status' => 'applied',
        ]);

        session()->flash('status', 'Application created successfully.');
        $this->resetForm();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function editApplication($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $this->editingApplicationId = $application->id;
        $this->isFormOpen = true;
        $this->company = $application->company;
        $this->position = $application->position;
        $this->city = $application->city;
        $this->location = $application->location;
        $this->applied_at = optional($application->applied_at)->format('Y-m-d');
        $this->job_url = $application->job_url;
        $this->notes = $application->notes;
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
            $this->resetForm();
        }
    }

    public function cancelEditing()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingApplicationId',
            'isFormOpen',
            'company',
            'position',
            'city',
            'location',
            'applied_at',
            'job_url',
            'notes',
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