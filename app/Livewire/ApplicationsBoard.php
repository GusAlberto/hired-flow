<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationsBoard extends Component
{
    public $editingApplicationId = null;
    public $company;
    public $position;
    public $city;
    public $location;
    public $applied_at;
    public $job_url;

    public function saveApplication()
    {
        $data = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->editingApplicationId) {
            $application = Application::where('user_id', Auth::id())
                ->find($this->editingApplicationId);

            if (!$application) {
                return;
            }

            $application->update($data);
            $this->resetForm();

            return;
        }

        Application::create([
            ...$data,
            'user_id' => Auth::id(),
            'status' => 'applied',
        ]);

        $this->resetForm();
    }

    public function editApplication($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $this->editingApplicationId = $application->id;
        $this->company = $application->company;
        $this->position = $application->position;
        $this->city = $application->city;
        $this->location = $application->location;
        $this->applied_at = optional($application->applied_at)->format('Y-m-d');
        $this->job_url = $application->job_url;
    }

    public function deleteApplication($id)
    {
        $application = Application::where('user_id', Auth::id())->find($id);

        if (!$application) {
            return;
        }

        $application->delete();

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
            'company',
            'position',
            'city',
            'location',
            'applied_at',
            'job_url',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $apps = Application::where('user_id', Auth::id())
            ->latest('updated_at')
            ->get();

        $total = $apps->count();

        $interviews = $apps->where('status', 'interview')->count();

        $offers = $apps->where('status', 'offer')->count();

        return view('livewire.applications-board', [

            'applied' => $apps->where('status', 'applied'),

            'waiting' => $apps->where('status', 'waiting'),

            'interview' => $apps->where('status', 'interview'),

            'rejected' => $apps->where('status', 'rejected'),

            'offer' => $apps->where('status', 'offer'),

            'total' => $total,

            'interviews' => $interviews,

            'offers' => $offers,

        ]);
    }

    #[On('moveApplication')]
    public function moveApplication($id, $status)
    {
        $app = Application::find($id);

        if (!$app) {
            return;
        }

        if ($app->user_id != Auth::id()) {
            return;
        }

        $app->status = $status;
        $app->save();
    }
}