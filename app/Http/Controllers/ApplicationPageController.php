<?php

namespace App\Http\Controllers;

use App\Concerns\DetectsApplicationColumns;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationPageController extends Controller
{
    use DetectsApplicationColumns;

    public function create(): View
    {
        return view('applications.create');
    }

    public function store(Request $request, ApplicationService $service): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $service->create($data);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Application created successfully.');
    }

    private function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'city' => [$this->hasCityColumn() ? 'required' : 'nullable', 'string', 'max:255'],
            'location' => [$this->hasLocationColumn() ? 'required' : 'nullable', 'string', 'max:255'],
            'applied_at' => ['required', 'date'],
            'job_url' => ['nullable', 'string', 'max:255'],
            'personal_score' => ['nullable', 'numeric', 'between:0,10'],
            'salary_offered' => ['nullable', 'numeric', 'min:0'],
            'salary_expected' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
