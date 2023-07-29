<?php

namespace App\Http\Controllers\User;

use App\Classes\CompetitionManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Dashboard
 *
 * The dashboard provides a quick overview of the user's standings. This
 * information is mainly for the benefit of the mobile app dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Dashboard
     *
     * Retrieves a summary of information for the dashboard of the currently
     * authenticated user.
     *
     * @responseFile 200 resources/responses/User/Dashboard/dashboard-summary.json
     */
    public function dashboard()
    {
        $response = CompetitionManager::getLotteriesForUserDashboard(Auth::user());
        return response()->success(__('user_dashboard_success'), $response);
    }
}
