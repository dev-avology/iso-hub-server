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
            'admin_notifications' => Notification::where('user_id', $user_id)->orderBy('created_at', 'desc')->get(), // Corrected here
            'status' => 'success'
        ];
        return response()->json($data);
    }

    // public function getUserNoticationCount($user_id)
    // {
    //     $user = User::find((int)$user_id);

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     // Superadmin: return ALL notifications
    //     if ($user->role_id == 1) {
    //         return response()->json([
    //             'count' => Notification::count(),
    //             'user_notifications' => Notification::orderBy('created_at', 'desc')->get(),
    //             'status' => 'success'
    //         ]);
    //     }

    //     // 🔁 Get user and all their recursive parents (via created_by_id)
    //     $parentUsers = $this->getAllRecursiveParents($user);
    //     $allUserIds = collect($parentUsers)->pluck('id')->push($user->id)->unique();

    //     // 📥 Get all notifications for this user + their parent chain
    //     $notifications = Notification::whereIn('user_id', $allUserIds)
    //                         ->orderBy('created_at', 'desc')
    //                         ->get();

    //     // 📊 Get total notify count sum for this user + parents
    //     $notifyCount = User::whereIn('id', $allUserIds)->sum('notify_count');

    //     return response()->json([
    //         'count' => $notifyCount,
    //         'user_notifications' => $notifications,
    //         'status' => 'success'
    //     ]);
    // }

    // public function getUserNoticationCount($user_id)
    // {
    //     $user = User::find((int)$user_id);

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     // Superadmin: return ALL notifications
    //     if ($user->role_id == 1) {
    //         return response()->json([
    //             'count' => Notification::count(),
    //             'admin_notifications' => Notification::orderBy('created_at', 'desc')->get(),
    //             'status' => 'success'
    //         ]);
    //     }

    //     // ✅ Get user and all their recursive children
    //     $childUserIds = $this->getAllRecursiveChildren($user->id);
    //     $allUserIds = collect($childUserIds)->push($user->id)->unique();

    //     // ✅ Fetch notifications for self + all children
    //     $notifications = Notification::whereIn('user_id', $allUserIds)
    //                         ->orderBy('created_at', 'desc')
    //                         ->get();

    //     // ✅ Count total notify_count for self + all children
    //     $notifyCount = User::whereIn('id', $allUserIds)->sum('notify_count');

    //     return response()->json([
    //         'count' => $notifyCount,
    //         'admin_notifications' => $notifications,
    //         'status' => 'success'
    //     ]);
    // }

    private function getAllRecursiveChildren($userId)
    {
        $allChildIds = [];

        $directChildren = User::where('created_by_id', $userId)->pluck('id')->toArray();

        foreach ($directChildren as $childId) {
            $allChildIds[] = $childId;
            $allChildIds = array_merge($allChildIds, $this->getAllRecursiveChildren($childId));
        }

        return $allChildIds;
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
