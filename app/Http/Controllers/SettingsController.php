<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'archiving');

        if (!in_array($tab, ['archiving'], true)) {
            $tab = 'archiving';
        }

        return view('settings.index', [
            'tab' => $tab,
            'archiveAfterDays' => (int) ($request->user()->archive_after_days ?: 30),
        ]);
    }

    public function updateArchiving(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'archive_after_days' => ['required', 'integer', 'between:1,365'],
        ]);

        $request->user()->update([
            'archive_after_days' => $validated['archive_after_days'],
        ]);

        return redirect()
            ->route('settings.index', ['tab' => 'archiving'])
            ->with('status', 'Archive settings updated successfully.');
    }
}
