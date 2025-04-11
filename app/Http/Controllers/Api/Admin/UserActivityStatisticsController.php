<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserActivityStatisticsController extends Controller
{
    /**
     * Get user activity statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Get current time
        $now = Carbon::now();
        
        // Get active users in different time periods
        $activeToday = $this->getActiveUsersCount($now->copy()->startOfDay());
        $activeThisWeek = $this->getActiveUsersCount($now->copy()->startOfWeek());
        $activeThisMonth = $this->getActiveUsersCount($now->copy()->startOfMonth());
        
        // Get total users count
        $totalUsers = User::count();
        
        // Get most active users (top 10)
        $mostActiveUsers = User::whereNotNull('last_active')
            ->orderBy('last_active', 'desc')
            ->take(10)
            ->get(['id', 'username', 'email', 'last_active']);
            
        // Get user activity by role
        $usersByRole = $this->getUsersByRole();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'active_today' => $activeToday,
                'active_this_week' => $activeThisWeek,
                'active_this_month' => $activeThisMonth,
                'active_percentage_today' => $totalUsers > 0 ? round(($activeToday / $totalUsers) * 100, 2) : 0,
                'active_percentage_week' => $totalUsers > 0 ? round(($activeThisWeek / $totalUsers) * 100, 2) : 0,
                'active_percentage_month' => $totalUsers > 0 ? round(($activeThisMonth / $totalUsers) * 100, 2) : 0,
                'most_active_users' => $mostActiveUsers,
                'users_by_role' => $usersByRole
            ]
        ]);
    }
    
    /**
     * Get active users count since a specific date
     *
     * @param Carbon $since
     * @return int
     */
    private function getActiveUsersCount(Carbon $since)
    {
        return User::where('last_active', '>=', $since)->count();
    }
    
    /**
     * Get user activity statistics by role
     *
     * @return array
     */
    private function getUsersByRole()
    {
        $now = Carbon::now();
        $startOfDay = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        
        $roles = User::select('role')->distinct()->pluck('role');
        $result = [];
        
        foreach ($roles as $role) {
            $totalInRole = User::where('role', $role)->count();
            $activeTodayInRole = User::where('role', $role)
                ->where('last_active', '>=', $startOfDay)
                ->count();
            $activeThisWeekInRole = User::where('role', $role)
                ->where('last_active', '>=', $startOfWeek)
                ->count();
            $activeThisMonthInRole = User::where('role', $role)
                ->where('last_active', '>=', $startOfMonth)
                ->count();
                
            $result[] = [
                'role' => $role,
                'total' => $totalInRole,
                'active_today' => $activeTodayInRole,
                'active_this_week' => $activeThisWeekInRole,
                'active_this_month' => $activeThisMonthInRole,
                'active_percentage_today' => $totalInRole > 0 ? round(($activeTodayInRole / $totalInRole) * 100, 2) : 0,
                'active_percentage_week' => $totalInRole > 0 ? round(($activeThisWeekInRole / $totalInRole) * 100, 2) : 0,
                'active_percentage_month' => $totalInRole > 0 ? round(($activeThisMonthInRole / $totalInRole) * 100, 2) : 0,
            ];
        }
        
        return $result;
    }
}
