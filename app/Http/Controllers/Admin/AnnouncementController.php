<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Show announcements page
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        
        return view('admin.announcements', [
            'announcements' => $announcements
        ]);
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:general,scholarship,deadline,maintenance',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'is_published' => 'boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:publish_date'
        ]);

        try {
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'priority' => $request->priority,
                'is_published' => $request->has('is_published'),
                'publish_date' => $request->publish_date ?: now(),
                'expiry_date' => $request->expiry_date,
                'created_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            Log::info('Announcement created', [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'created_by' => $announcement->created_by
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating announcement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:general,scholarship,deadline,maintenance',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'is_published' => 'boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:publish_date'
        ]);

        try {
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'priority' => $request->priority,
                'is_published' => $request->has('is_published'),
                'publish_date' => $request->publish_date,
                'expiry_date' => $request->expiry_date,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            Log::info('Announcement updated', [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating announcement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $title = $announcement->title;
            
            $announcement->delete();

            Log::info('Announcement deleted', [
                'announcement_id' => $id,
                'title' => $title,
                'deleted_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting announcement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get announcements data for API
     */
    public function getAnnouncementsData(Request $request)
    {
        $query = Announcement::query();

        // Apply filters
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $announcements = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'type' => $announcement->type,
                    'priority' => $announcement->priority,
                    'is_published' => $announcement->is_published,
                    'publish_date' => $announcement->publish_date ? $announcement->publish_date->format('Y-m-d H:i:s') : null,
                    'expiry_date' => $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d H:i:s') : null,
                    'created_by' => $announcement->created_by,
                    'created_at' => $announcement->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Get published announcements for students
     */
    public function getPublishedAnnouncements()
    {
        $announcements = Announcement::published()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'data' => $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'type' => $announcement->type,
                    'priority' => $announcement->priority,
                    'publish_date' => $announcement->publish_date->format('Y-m-d H:i:s'),
                    'expiry_date' => $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d H:i:s') : null
                ];
            })
        ]);
    }

    /**
     * Toggle announcement publication status
     */
    public function togglePublication($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_published = !$announcement->is_published;
            $announcement->save();

            $status = $announcement->is_published ? 'published' : 'unpublished';

            Log::info('Announcement publication toggled', [
                'announcement_id' => $id,
                'new_status' => $status,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Announcement {$status} successfully",
                'is_published' => $announcement->is_published
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling announcement publication: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get announcement statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Announcement::count(),
            'published' => Announcement::where('is_published', true)->count(),
            'unpublished' => Announcement::where('is_published', false)->count(),
            'expired' => Announcement::where('expiry_date', '<', now())->count(),
            'by_type' => [
                'general' => Announcement::where('type', 'general')->count(),
                'scholarship' => Announcement::where('type', 'scholarship')->count(),
                'deadline' => Announcement::where('type', 'deadline')->count(),
                'maintenance' => Announcement::where('type', 'maintenance')->count(),
            ],
            'by_priority' => [
                'urgent' => Announcement::where('priority', 'urgent')->count(),
                'high' => Announcement::where('priority', 'high')->count(),
                'medium' => Announcement::where('priority', 'medium')->count(),
                'low' => Announcement::where('priority', 'low')->count(),
            ]
        ];

        return response()->json($stats);
    }
}
