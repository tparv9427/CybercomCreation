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
        return response()->json(SavedView::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'config' => 'required|array',
        ]);

        $view = SavedView::create($data);

        AuditLog::log('save_view', ['view_name' => $view->name, 'view_id' => $view->id]);

        return response()->json([
            'id'      => $view->id,
            'message' => 'View saved successfully',
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        // Only Admin can delete views
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $view = SavedView::find($id);

        if (!$view) {
            return response()->json(['message' => 'View not found'], 404);
        }

        $view_name = $view->name;
        $view->delete();

        AuditLog::log('delete_view', ['view_name' => $view_name, 'view_id' => $id]);

        return response()->json(['message' => 'View deleted successfully']);
    }
}