<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;

class NotificationController extends Controller
{
    public function getUserNoticationCount($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data = [
            'count' => $user->notify_count,
            'user_notifications' => Notification::where('user_id', $user_id)->get(),
            'admin_notifications' => Notification::all(), // You can filter if needed
        ];
        return response()->json($data);
    }
}
