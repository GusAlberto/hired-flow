<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationsBoard extends Component
{
    public $company;
    public $position;
    public $applied_at;
    public $job_url;

    public function addApplication()
    {
        Application::create([
            'user_id' => Auth::id(),
            'company' => $this->company,
            'position' => $this->position,
            'applied_at' => $this->applied_at,
            'job_url' => $this->job_url,
            'status' => 'applied'
        ]);

        $this->reset();
    }

    public function render()
    {
    $apps = Application::where('user_id', Auth::id())->get();

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