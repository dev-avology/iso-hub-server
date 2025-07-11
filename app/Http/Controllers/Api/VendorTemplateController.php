<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rep;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use App\Models\VendorTemplates;
use Illuminate\Support\Facades\Storage;
use App\Services\ApiResponseService; // Import API response service
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VendorTemplateController extends Controller
{
    public function storeVendorTemplates(Request $request)
    {
        $vendors = json_decode($request->input('vendors'), true);

        if (!is_array($vendors)) {
            return response()->json(['error' => 'Invalid vendors data'], 400);
        }

        $errors = [];

        foreach ($vendors as $index => $vendor) {
            $vendorName = $vendor['vendor_name'] ?? null;

            if (!$vendorName) {
                $errors[] = "Vendor name is required at index $index.";
                continue;
            }

            // Check for uniqueness
            $existingVendor = VendorTemplates::where('vendor_name', $vendorName)->first();

            if ($existingVendor) {
                $errors[] = "Vendor name '{$vendorName}' already exists at index $index.";
                continue;
            }

            $logoPath = null;

            if ($request->hasFile("vendors.$index.logo_url")) {
                $file = $request->file("vendors.$index.logo_url");
                $logoPath = $file->store('vendor_logos', 'public');
            }

            $user = User::find($vendor['user_id']);
            $created_by_id = null;
            if($user){
             $created_by_id = $user->created_by_id;
            }

            $data = [
                'user_id' => $vendor['user_id'] ?? null,
                'created_by_id' => $created_by_id,
                'vendor_type' => $vendor['vendor_type'] ?? null,
                'vendor_name' => $vendorName,
                'vendor_email' => $vendor['vendor_email'] ?? null,
                'vendor_phone' => $vendor['vendor_phone'] ?? null,
                'logo_url' => $logoPath ? asset('storage/' . $logoPath) : parse_url($vendor['logo_url'], PHP_URL_PATH),
                'login_url' => $vendor['login_url'] ?? null,
                'support_info' => $vendor['support_info'] ?? null,
                'notes' => $vendor['notes'] ?? null,
                'rep_name' => $vendor['rep_name'] ?? null,
                'rep_email' => $vendor['rep_email'] ?? null,
                'rep_phone' => $vendor['rep_phone'] ?? null,
                'description' => $vendor['description'] ?? null,
            ];

            VendorTemplates::create($data);
        }

        if (!empty($errors)) {
            return response()->json(['message' => 'Some vendors were not saved.', 'errors' => $errors], 422);
        }

        return ApiResponseService::success('Vendors saved successfully', []);
    }




    public function showVendorTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required',
            'vendor_type' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $auth = auth()->user();

        $user = User::find($auth->id);

        if (!$user) {
            Log::warning('User not found', ['user_id' => $request->user_id]);
            return ApiResponseService::error('User not found', [], 404);
        }

        $superadmin = User::where('role_id',1)->first();
        $superadmin_id = '';
        if($superadmin){
            $superadmin_id = $superadmin->id;
        }

        $vendor_template = VendorTemplates::with('vendor_user')->where('vendor_name', $request->vendor_name)->where('vendor_type', $request->vendor_type)->whereIn('user_id', [$superadmin_id,$auth->id])->first();

        if (!$vendor_template) {
            return ApiResponseService::error('Template not found', 404);
        }
        return ApiResponseService::success('Template fetched successfully', $vendor_template);
    }

    public function getAdminVendorDropdownData(Request $request)
    {
        $validTypes = ['processors', 'gateways', 'hardware','internal'];

        if (!in_array($request->vendor_type, $validTypes)) {
            return ApiResponseService::error('Invalid vendor type', 404);
        }

        $auth = auth()->user();

        $user = User::find($auth->id);

        if (!$user) {
            Log::warning('User not found', ['user_id' => $request->user_id]);
            return ApiResponseService::error('User not found', [], 404);
        }

        $superadmin = User::where('role_id',1)->first();
        $superadmin_id = '';
        if($superadmin){
            $superadmin_id = $superadmin->id;
        }

        $vendors = VendorTemplates::where('vendor_type', $request->vendor_type)
            ->whereIn('user_id', [$superadmin_id,$user->id])
            ->get();

        return ApiResponseService::success('Template fetched successfully', $vendors);
    }

    // public function getAllVendorsList(Request $request)
    // {
    //     $query = VendorTemplates::query();

    //     // if ($request->has('user_id')) {
    //     //     $query->where('user_id', $request->user_id);
    //     // }

    //     if ($request->has('user_id')) {
    //         $userIds = [$request->user_id, 2]; // Include requested user and user ID 2
    //         $query->whereIn('user_id', $userIds);
    //     }

    //     // $vendors = $query->orderBy('card_order','asc')->get();
    //     // Get vendors grouped and sorted per user to preserve correct card_order
    //     $vendors = $query->get()->groupBy('user_id')->flatMap(function ($userGroup) {
    //         return $userGroup->sortBy('card_order');
    //     });

    //     // Group vendors by type    
    //     $categorizedVendors = $vendors->groupBy('vendor_type')->map(function ($group) {
    //         return $group->map(function ($vendor) {
    //             return [
    //                 'id' => $vendor->id,
    //                 'vendor_name' => $vendor->vendor_name,
    //                 'vendor_email' => $vendor->vendor_email,
    //                 'vendor_phone' => $vendor->vendor_phone,
    //                 'logo_url' => $vendor->logo_url,
    //                 'login_url' => $vendor->login_url,
    //                 'rep_name' => $vendor->rep_name,
    //                 'rep_email' => $vendor->rep_email,
    //                 'rep_phone' => $vendor->rep_phone,
    //                 'notes' => $vendor->notes,
    //                 'support_info' => $vendor->support_info,
    //                 'description' => $vendor->description,
    //                 'vendor_type' => $vendor->vendor_type,
    //                 'created_at' => $vendor->created_at,
    //                 'updated_at' => $vendor->updated_at
    //             ];
    //         });
    //     });

    //     return ApiResponseService::success('Vendors fetched successfully', $categorizedVendors);
    // }

    public function getAllVendorsList(Request $request)
    {
        Log::debug('getAllVendorsList called', ['request' => $request->all()]);

        $auth = auth()->user();

        $user = User::find($auth->id);

        if (!$user) {
            Log::warning('User not found', ['user_id' => $request->user_id]);
            return ApiResponseService::error('User not found', [], 404);
        }

        Log::debug('User found', ['user_id' => $user->id, 'role_id' => $user->role_id]);

        $query = VendorTemplates::query();

        $superadmin = User::where('role_id',1)->first();
        $superadmin_id = '';
        if($superadmin){
            $superadmin_id = $superadmin->id;
        }

        if ($user->role_id == 1) {
            Log::debug('Role is super admin, fetching all vendors');
        } elseif ($user->role_id == 2) {
            $query->whereIn('user_id', [$superadmin_id, $user->id]);
        } else {
            $query->whereIn('user_id', [$superadmin_id, $user->created_by_id]);
        }

        $vendorsRaw = $query->get();

        // Group and sort by card_order
        $vendors = $vendorsRaw->groupBy('user_id')->flatMap(function ($userGroup) {
            return $userGroup->sortBy('card_order');
        });

        // Group by vendor_type and map the fields
        $categorizedVendors = $vendors->groupBy('vendor_type')->map(function ($group) {
            return $group->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'vendor_name' => $vendor->vendor_name,
                    'vendor_email' => $vendor->vendor_email,
                    'vendor_phone' => $vendor->vendor_phone,
                    'logo_url' => $vendor->logo_url,
                    'login_url' => $vendor->login_url,
                    'rep_name' => $vendor->rep_name,
                    'rep_email' => $vendor->rep_email,
                    'rep_phone' => $vendor->rep_phone,
                    'notes' => $vendor->notes,
                    'support_info' => $vendor->support_info,
                    'description' => $vendor->description,
                    'vendor_type' => $vendor->vendor_type,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at
                ];
            });
        });

        return ApiResponseService::success('Vendors fetched successfully', $categorizedVendors);
    }



    public function updateVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_templates,id',
            'vendor_name' => [
                'required',
                Rule::unique('vendor_templates', 'vendor_name')->ignore($request->id),
            ],
            // 'vendor_email' => 'required|email',
            // 'vendor_phone' => 'required',
            // 'logo_url' => 'nullable|file|image',
            // 'login_url' => 'required',
            // 'rep_name' => 'required',
            // 'rep_email' => 'required|email',
            // 'rep_phone' => 'required',
            // 'notes' => 'required',
            // 'support_info' => 'required',
            // 'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422); // <-- Pass a proper status code
        }

        $vendor = VendorTemplates::find($request->id);

        $data = [
            'vendor_name' => $request->vendor_name,
            'vendor_email' => $request->vendor_email,
            'vendor_phone' => $request->vendor_phone,
            'login_url' => $request->login_url,
            'rep_name' => $request->rep_name,
            'rep_email' => $request->rep_email,
            'rep_phone' => $request->rep_phone,
            'notes' => $request->notes,
            'support_info' => $request->support_info,
            'description' => $request->description,
        ];

        if ($request->hasFile("logo_url")) {
            $file = $request->file("logo_url");
            $logoPath = $file->store('vendor_logos', 'public');
            $data['logo_url'] = asset('storage/' . $logoPath);
        }

        $vendor->update($data);

        return ApiResponseService::success('Vendor updated successfully', $vendor);
    }


    public function editVendorDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_templates,id',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::error('Validation error', $validator->errors(), 422);
        }

        $vendor = VendorTemplates::find($request->id);

        if (!$vendor) {
            return ApiResponseService::error('Vendor not found', 404);
        }

        return ApiResponseService::success('Vendor details fetched successfully', $vendor);
    }

    public function deleteVendor(Request $request){
       $vendor_template = VendorTemplates::find($request->id);
       $vendor_template->delete();
       return ApiResponseService::success('Vendor deleted successfully', $vendor_template);
    }

    public function updateCardOrder(Request $request)
    {
        try {
            $request->validate([
                'vendor_ids' => 'required|array',
                'vendor_ids.*' => 'required|integer|exists:vendor_templates,id'
            ]);

            \Log::info($request->all());

            foreach ($request->vendor_ids as $index => $vendorId) {
                VendorTemplates::where('id', $vendorId)
                    ->update(['card_order' => $index]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Card order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
