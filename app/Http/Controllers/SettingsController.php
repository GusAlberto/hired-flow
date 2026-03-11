<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

    public function runArchivingNow(Request $request): RedirectResponse
    {
        $archiveAfterDays = max(1, min(365, (int) ($request->user()->archive_after_days ?: 30)));
        $thresholdDate = Carbon::today()->subDays($archiveAfterDays);
        $hasStageColumn = Schema::hasColumn('applications', 'stage');

        $query = Application::query()
            ->where('user_id', $request->user()->id)
            // Archive only after the configured amount of full days has passed.
            ->whereDate('applied_at', '<', $thresholdDate)
            ->where('status', '!=', 'archived');

        $updateData = ['status' => 'archived'];

        if ($hasStageColumn) {
            $updateData['stage'] = 'archived';
        }

        $archivedCount = $query->update($updateData);

        return redirect()
            ->route('settings.index', ['tab' => 'archiving'])
            ->with('status', "Manual archive completed. {$archivedCount} application(s) archived.");
    }
}
