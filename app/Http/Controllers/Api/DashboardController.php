<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        // Get authenticated user
        // Get user
        $user = User::find(1);



        // $user = Auth::user(); // No need for guard('api'), Sanctum handles it
        // dd($user->can('dashboard.view'));

        // Check if user has permission
        if (!$user || !$user->can('dashboard.view')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to dashboard!'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'users' => [],
            ]
        ], 200);
    }
}
