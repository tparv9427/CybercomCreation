<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SavedViewController extends Controller
{
    public function index(): JsonResponse
    {
        $views = DB::table('saved_views')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($view) {
                $view->config = json_decode($view->config, true);
                return $view;
            });

        return response()->json($views);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'config' => 'required|array',
        ]);

        $id = DB::table('saved_views')->insertGetId([
            'name'       => $request->get('name'),
            'config'     => json_encode($request->get('config')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'id'      => $id,
            'message' => 'View saved successfully',
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = DB::table('saved_views')->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'View not found'], 404);
        }

        return response()->json(['message' => 'View deleted successfully']);
    }
}