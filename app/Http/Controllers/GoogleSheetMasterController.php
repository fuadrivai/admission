<?php

namespace App\Http\Controllers;

use App\Google\SchoolVisitGoogleSheetService;
use App\Models\GoogleSheetMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleSheetMasterController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();

        return view('setting.google-sheet-master', [
            'title' => 'gSheet Master',
            'integrations' => GoogleSheetMaster::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, string $integrationKey)
    {
        $this->authorizeAccess();

        $master = GoogleSheetMaster::where('integration_key', $integrationKey)->firstOrFail();
        $validated = $request->validate([
            'google_sheet_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $master->google_sheet_url = $validated['google_sheet_url'] ?: null;
        $master->save();

        return redirect()->route('setting.google-sheet-master.index')
            ->with('success', $master->name . ' Google Sheet URL saved.');
    }

    public function syncSchoolVisit(SchoolVisitGoogleSheetService $googleSheet)
    {
        $this->authorizeAccess();

        try {
            $url = $googleSheet->sync();
        } catch (\Exception $exception) {
            Log::error('School Visit Google Sheets sync failed.', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('setting.google-sheet-master.index')
                ->withErrors(['google_sheet' => 'School Visit sync failed. Check the application log and Google API authorization.']);
        }

        return redirect()->route('setting.google-sheet-master.index')
            ->with('success', 'School Visit data synced to Google Sheets.')
            ->with('google_sheet_url', $url);
    }

    private function authorizeAccess(): void
    {
        abort_unless(auth()->check() && auth()->user()->role !== 'user', 403);
    }
}
