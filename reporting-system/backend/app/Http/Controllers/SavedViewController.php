<?php

namespace App\Http\Controllers;

use App\Models\SavedView;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SavedViewController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $views = SavedView::with(['user:id,name', 'schedule'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($views);
    }
    
    public function updateSchedule(Request $request, int $id): JsonResponse
    {
        $view = SavedView::findOrFail($id);
        $user = auth()->user();

        if ($view->user_id !== $user->id && !$user->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $freq = $request->get('frequency'); // 'daily', 'weekly', 'monthly' or empty

        if (empty($freq)) {
            \App\Models\ScheduledReport::where('saved_view_id', $id)->delete();
            return response()->json(['message' => 'Schedule removed']);
        }

        \App\Models\ScheduledReport::updateOrCreate(
            ['saved_view_id' => $id],
            ['user_id' => $user->id, 'frequency' => $freq]
        );

        return response()->json(['message' => 'Schedule updated to ' . $freq]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'config'    => 'required|array',
            'parent_id' => 'nullable|exists:saved_views,id',
            'is_public' => 'boolean',
        ]);

        $data['user_id'] = auth()->id();

        if (!empty($data['parent_id'])) {
            $parent = SavedView::find($data['parent_id']);
            $data['version'] = ($parent->version ?? 1) + 1;
        }

        $view = SavedView::create($data);

        AuditLog::log('save_view', [
            'view_name' => $view->name, 
            'view_id'   => $view->id, 
            'version'   => $view->version,
            'is_public' => $view->is_public
        ]);

        return response()->json([
            'id'      => $view->id,
            'message' => 'View saved successfully',
            'version' => $view->version,
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $view = SavedView::find($id);

        if (!$view) {
            return response()->json(['message' => 'View not found'], 404);
        }

        // Only owner or Admin can delete views
        $user = auth()->user();
        if ($view->user_id !== $user->id && !$user->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized. You do not own this view.'], 403);
        }

        $view_name = $view->name;
        $view->delete();

        AuditLog::log('delete_view', ['view_name' => $view_name, 'view_id' => $id]);

        return response()->json(['message' => 'View deleted successfully']);
    }
}