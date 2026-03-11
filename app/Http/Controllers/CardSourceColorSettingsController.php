<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardSourceColorSettingsController extends Controller
{
    private const SOURCE_LABELS = [
        'linkedin' => 'LinkedIn',
        'indeed' => 'Indeed',
        'glassdoor' => 'Glassdoor',
        'gupy' => 'Gupy',
        'catho' => 'Catho',
        'infojobs' => 'InfoJobs',
        'vagas' => 'Vagas.com',
        'greenhouse' => 'Greenhouse',
        'lever' => 'Lever',
        'workday' => 'Workday',
        'other' => 'Other sources',
    ];

    private const DEFAULT_COLORS = [
        'linkedin' => '#dbeafe',
        'indeed' => '#fef3c7',
        'glassdoor' => '#dcfce7',
        'gupy' => '#ede9fe',
        'catho' => '#fae8ff',
        'infojobs' => '#ffedd5',
        'vagas' => '#fef9c3',
        'greenhouse' => '#d1fae5',
        'lever' => '#e0f2fe',
        'workday' => '#fee2e2',
        'other' => '#f3f4f6',
    ];

    public function edit(Request $request): View
    {
        $userColors = is_array($request->user()->card_source_colors)
            ? $request->user()->card_source_colors
            : [];

        $sourceColors = array_merge(self::DEFAULT_COLORS, $userColors);

        return view('settings.card-source-colors', [
            'sourceLabels' => self::SOURCE_LABELS,
            'sourceColors' => $sourceColors,
            'defaultColors' => self::DEFAULT_COLORS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (array_keys(self::SOURCE_LABELS) as $source) {
            $rules["colors.$source"] = ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'];
        }

        $validated = $request->validate($rules);

        $request->user()->update([
            'card_source_colors' => $validated['colors'],
        ]);

        return redirect()
            ->route('settings.card-source-colors.edit')
            ->with('status', 'Card colors updated successfully.');
    }
}
