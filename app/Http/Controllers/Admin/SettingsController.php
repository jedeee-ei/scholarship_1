<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        // Get current settings
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        return view('admin.settings', [
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear
        ]);
    }

    /**
     * Save settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'current_semester' => 'required|string',
            'current_academic_year' => 'required|string'
        ]);

        try {
            // Update or create settings
            SystemSetting::updateOrCreate(
                ['key' => 'current_semester'],
                ['value' => $request->current_semester]
            );

            SystemSetting::updateOrCreate(
                ['key' => 'current_academic_year'],
                ['value' => $request->current_academic_year]
            );

            Log::info('System settings updated', [
                'semester' => $request->current_semester,
                'academic_year' => $request->current_academic_year
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current semester and year
     */
    public function getCurrentSemesterYear()
    {
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        return response()->json([
            'current_semester' => $currentSemester,
            'current_academic_year' => $currentAcademicYear
        ]);
    }

    /**
     * Update settings (alternative method)
     */
    public function updateSettings(Request $request)
    {
        return $this->saveSettings($request);
    }
}
