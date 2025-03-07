<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\DashboardService;
use App\Models\User;
use App\Services\ApiResponseService; // Import API response service

class DashboardController extends Controller
{
    protected $DashboardService;
    protected $ApiResponseService;

    public function __construct(DashboardService $DashboardService)
    {
        $this->DashboardService = $DashboardService;
    }

    public function index()
    {
        $permission = 'dashboard.view'; 

        $userPermission = $this->DashboardService->checkPermission($permission);

        if(isset($userPermission) && !empty($userPermission)){
            return $userPermission;
        }

        $dashboardData = $this->DashboardService->getDashboardData();

        return ApiResponseService::success('Dashboard data retrieved successfully', $dashboardData);
    }
}
