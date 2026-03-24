<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get platform-wide statistics for the Admin Dashboard.
     */
    public function getStats(Request $request)
    {
        // Simple security check (could use Spatie roles middleware instead)
        if (!$request->user() || !$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 1. Top 10 Users by Action Volume (Search, Export, Save)
        $topUsers = AuditLog::select('user_id', DB::raw('count(*) as total'))
            ->join('users', 'users.id', '=', 'audit_logs.user_id')
            ->select('users.name', 'users.email', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'users.name', 'users.email')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 2. Feature Engagement (Distribution of actions)
        $actions = AuditLog::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->get();

        // 3. Automation Throughput (Daily Email Sends)
        $sentReports = AuditLog::where('action', 'send_scheduled_report')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->limit(30)
            ->get();

        return response()->json([
            'topUsers'    => $topUsers,
            'actions'     => $actions,
            'sentReports' => $sentReports,
            'counts'      => [
                'totalUsers' => User::count(),
                'totalLogs'  => AuditLog::count(),
            ]
        ]);
    }
}
