<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AdminGlobalNotification;

class NotificationController extends Controller
{
    public function getUserNoticationCount($user_id)
    {
        $user = User::find((int)$user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data = [
            'count' => $user->notify_count,
            'user_notifications' => Notification::where('user_id', $user_id)->orderBy('created_at', 'desc')->get(),
            'admin_notifications' => Notification::orderBy('created_at', 'desc')->get(), // Corrected here
            'status' => 'success'
        ];
        return response()->json($data);
    }

    public function removeNotification(Request $request)
    {
        $user = User::find((int)$request->user_id);
        if (!$user) {
            return ApiResponseService::error('User not found.', 400);
        }
        $user->update(['notify_count' => 0]);
        return ApiResponseService::success('Notification count reset successfully.', []);
    }

    public function deleteNotification(Request $request)
    {
        $noti = Notification::find((int) $request->id);
     
        if (!$noti) {
           return ApiResponseService::error('Notification cant found.', 400);
        }

        $noti->delete();

        return ApiResponseService::success('Notification deleted successfully.', []);
    }

    public function deleteAllNotification(Request $request)
    {
        Notification::where('user_id', $request->user_id)->delete();
        return ApiResponseService::success('All notifications deleted successfully.', []);
    }

    public function sendToAllUsers(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'msg' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Fetch users (you can uncomment to notify all users)
            // $users = User::all();
            $users = User::all();

            foreach ($users as $user) {
                $user->notify(new AdminGlobalNotification($request->msg));
            }

            return ApiResponseService::success('Notifications have been successfully sent to all users.', []);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ], 500);
        }
    }

}
