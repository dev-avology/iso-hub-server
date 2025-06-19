<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\ProspectMail;
use App\Models\JotForm;
use Illuminate\Support\Facades\Auth;
use App\Services\JotFormService;
use App\Mail\DuplicateFormMail;
use App\Mail\SendFormLinkMail;
use App\Services\DashboardService;
use App\Services\FileService;
use Illuminate\Support\Facades\Response;

class JotFromController extends Controller
{
    protected $JotFormService;
    protected $UserService;
    protected $DashboardService;
    protected $FileService;

    public function __construct(JotFormService $JotFormService, DashboardService $DashboardService, FileService $FileService)
    {
        $this->JotFormService = $JotFormService;
        $this->DashboardService = $DashboardService;
        $this->FileService = $FileService;
    }

    public function createForm(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'business_dba' => 'required',
        //     'business_corporate_legal_name' => 'required',
        //     'business_location_address' => 'required',
        //     'business_corporate_address' => 'required',
        //     'business_city' => 'required',
        //     'business_state' => 'required',
        //     'business_zip' => 'required',
        //     'business_phone_number' => 'required',
        //     'business_contact_name' => 'required',
        //     'business_contact_number' => 'required',
        //     'business_start_date' => 'required|date',
        //     'business_tax_id' => 'required',
        //     'business_profile_business_type' => 'nullable|array',

        //     'ownership_owner_name' => 'required',
        //     'ownership_title' => 'required',
        //     'ownership_percent' => 'required',
        //     'ownership_phone_number' => 'required',
        //     'ownership_city' => 'required',
        //     'ownership_state' => 'required',
        //     'ownership_zip' => 'required',
        //     'ownership_email' => 'required',
        //     'ownership_dob' => 'required|date',
        //     'ownership_social_security_number' => 'required',
        //     'ownership_residential_street_address' => 'required',
        //     'ownership_driver_licence_number' => 'required',

        //     'bank_name' => 'required',
        //     'aba_routing' => 'required',
        //     'doa' => 'required',

        //     'business_type' => 'nullable|array',

        //     'terminal' => 'nullable|array',
        //     'processing_services' => 'nullable|array',
        //     'terminal_type_or_model' => 'required',
        //     'mobile_app' => 'nullable|array',
        //     'mobile_app_cardreader_type_model' => 'required',
        //     'pos_point_of_sale' => 'nullable|array',
        //     'system_type_model' => 'required',
        //     'number_of_stations' => 'required',
        //     'pos_other_items' => 'required',
        //     'virtual_terminal' => 'nullable|array',
        //     'signature'       => 'required|string',
        //     'unique_string'   => 'required|string',
        //     'signature_date'  => 'required|date'
        // ]);

        // Return validation errors if any
        // if ($validator->fails()) {
        //     return response()->json([
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors(),
        //     ], 422);
        // }

        $user_id = $request->user_id;

        if (!$user_id) {
            return ApiResponseService::error('Missing data', 400);
        }
        $business_dba = null;
        $message = null;
        // try {
        //     // Decrypt and decode the data from the URL
        //     $decryptedData = json_decode(decrypt(urldecode($queryData)), true);
        //     $userId = $decryptedData['user_id'] ?? null;
        //     $business_dba = $request->business_dba ?? null;

        //     if (isset($decryptedData['is_duplicate']) && ($decryptedData['is_duplicate'] == '1')) {
        //         $message = 'Replicated JotForm submission (' . $business_dba . ').';
        //     } else {
        //         $message = 'New JotForm submission (' . $business_dba . ').';
        //     }
        // } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        //     return ApiResponseService::error('Invalid encrypted data', 400);
        // }

        $business_dba = $request->business_dba ?? null;
        // $message = 'new Pre-Application (' . $business_dba . ').';
        $message = 'new Pre-Application.';

        $form = $this->JotFormService->create($request, $user_id);
        if ($form) {
            $this->FileService->notifyUser($user_id, $message);
        }
        return ApiResponseService::success('Forms Created Successfully', $form);
    }

    public function jotFormcheckUniqueString($user_id)
    {
        if (!$user_id) {
            return ApiResponseService::error('Missing data', 400);
        }

        try {
            // Decrypt and decode the data from the URL
            // $decryptedData = json_decode(decrypt(urldecode($string)), true);

            // Check if the decrypted data is valid
            // if (!is_array($decryptedData) || !isset($decryptedData['user_id'])) {
            //     return ApiResponseService::error('Invalid encrypted data', 400);
            // }

            // // Check if secret verification is required
            // if (!isset($decryptedData['is_duplicate'])) {
            //     if (!isset($decryptedData['secret']) || $decryptedData['secret'] !== 'jotform_URD_!@#9823_secret$%DEC8901') {
            //         return ApiResponseService::error('Invalid encrypted data', 400);
            //     }
            // }

            // $userId = $user_id;
            // Check if user_id exists in the users table
            $user = User::find($user_id);

            if (!$user) {
                return ApiResponseService::error('Invalid data', 400);
            }

            $data = [];

            // if (isset($decryptedData['is_duplicate'])) {
            //     foreach ($decryptedData as $key => $value) {
            //         $data[$key] = $value;
            //     }
            // }
            // Return success with user data if everything is valid
            return ApiResponseService::success('Data verified successfully', $data);
        } catch (\Exception $e) {
            // Handle decryption error or invalid string
            return ApiResponseService::error('Invalid data format', 400);
        }
    }

    public function getFormsList(Request $request)
    {
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $query = JotForm::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $jotforms = $query->with(['get_jotform_details', 'get_jotform_docs', 'get_jotform_owner_docs'])->orderBy('created_at', 'desc')->get();
        return ApiResponseService::success('Jotfrom lists fetched successfully', $jotforms);
    }

    // public function getFromDetails($id)
    // {
    //     $permission = 'jotform.view';
    //     $userPermission = $this->DashboardService->checkPermission($permission);

    //     if (!empty($userPermission)) {
    //         return $userPermission;
    //     }

    //     $query = JotForm::query();

    //     if ($id) {
    //         $query->where('id', $id);
    //     }
    //     $jotforms = $query->orderBy('created_at', 'desc')->get();
    //     return ApiResponseService::success('Jotfrom fetched successfully', $jotforms);
    // }

    public function getFromDetails($id = null)
    {
        // Check if the user has the 'jotform.view' permission
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        // If permission check returns a response (like an error), return it immediately
        if (!empty($userPermission)) {
            return $userPermission;
        }

        // Build the query
        $query = JotForm::query();

        // If an ID is provided, add constraints and eager load relationships
        if ($id) {
            $query->with(['get_jotform_details', 'get_jotform_docs', 'get_jotform_owner_docs'])
                ->where('id', $id);
        }

        // Fetch the records
        $jotforms = $query->orderBy('created_at', 'desc')->get();

        // Return a success response
        return ApiResponseService::success('Jotform fetched successfully', $jotforms);
    }

    public function sendFormDuplicateMail(Request $request)
    {
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);
        if (!empty($userPermission)) {
            return $userPermission;
        }

        $validator = Validator::make($request->all(), [
            'business_dba' => 'required',
            'business_corporate_legal_name' => 'required',
            'business_location_address' => 'required',
            'business_corporate_address' => 'required',
            'business_city' => 'required',
            'business_state' => 'required',
            'business_zip' => 'required',
            'business_phone_number' => 'required',
            'business_contact_name' => 'required',
            'business_contact_number' => 'required',
            'business_start_date' => 'required|date',
            'business_tax_id' => 'required',
            'business_profile_business_type' => 'nullable|array',

            'ownership_owner_name' => 'required',
            'ownership_title' => 'required',
            'ownership_percent' => 'required',
            'ownership_phone_number' => 'required',
            'ownership_city' => 'required',
            'ownership_state' => 'required',
            'ownership_zip' => 'required',
            'ownership_email' => 'required',
            'ownership_dob' => 'required|date',
            'ownership_social_security_number' => 'required',
            'ownership_residential_street_address' => 'required',
            'ownership_driver_licence_number' => 'required',

            'bank_name' => 'required',
            'aba_routing' => 'required',
            'doa' => 'required',

            'business_type' => 'nullable|array',

            'terminal' => 'nullable|array',
            'processing_services' => 'nullable|array',
            'terminal_type_or_model' => 'required',
            'mobile_app' => 'nullable|array',
            'mobile_app_cardreader_type_model' => 'required',
            'pos_point_of_sale' => 'nullable|array',
            'system_type_model' => 'required',
            'number_of_stations' => 'required',
            'pos_other_items' => 'required',
            'virtual_terminal' => 'nullable|array',
            'user_id' => 'required',
            'email' => 'required'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        if ($request->user_id != $userId) {
            return ApiResponseService::error('Unauthorized user.', 401);
        }

        $data = [
            'business_dba' => $request->business_dba,
            'business_corporate_legal_name' => $request->business_corporate_legal_name,
            'business_location_address' => $request->business_location_address,
            'business_corporate_address' => $request->business_corporate_address,
            'business_city' => $request->business_city,
            'business_state' => $request->business_state,
            'business_zip' => $request->business_zip,
            'business_phone_number' => $request->business_phone_number,
            'business_contact_name' => $request->business_contact_name,
            'business_contact_number' => $request->business_contact_number,
            'business_start_date' => $request->business_start_date,
            'business_tax_id' => $request->business_tax_id,
            'business_profile_business_type' => $request->business_profile_business_type,

            'ownership_owner_name' => $request->ownership_owner_name,
            'ownership_title' => $request->ownership_title,
            'ownership_percent' => $request->ownership_percent,
            'ownership_phone_number' => $request->ownership_phone_number,
            'ownership_city' => $request->ownership_city,
            'ownership_state' => $request->ownership_state,
            'ownership_zip' => $request->ownership_zip,
            'ownership_email' => $request->ownership_email,
            'ownership_dob' => $request->ownership_dob,
            'ownership_social_security_number' => $request->ownership_social_security_number,
            'ownership_residential_street_address' => $request->ownership_residential_street_address,
            'ownership_driver_licence_number' => $request->ownership_driver_licence_number,

            'bank_name' => $request->bank_name,
            'aba_routing' => $request->aba_routing,
            'doa' => $request->doa,

            'business_type' => $request->business_type,
            'business_type_other' => $request->business_type_other,

            'terminal' => $request->terminal,
            'terminal_special_features' => $request->terminal_special_features ?? '',
            'processing_services' => $request->processing_services,
            'terminal_type_or_model' => $request->terminal_type_or_model,
            'mobile_app' => $request->mobile_app,
            'mobile_app_cardreader_type_model' => $request->mobile_app_cardreader_type_model,
            'mobile_app_special_features' => $request->mobile_app_special_features ?? '',
            'pos_point_of_sale' => $request->pos_point_of_sale,
            'system_type_model' => $request->system_type_model,
            'number_of_stations' => $request->number_of_stations,
            'pos_other_items' => $request->pos_other_items,
            'pos_special_features' => $request->pos_special_features,
            'virtual_terminal' => $request->virtual_terminal,

            'user_id' => $request->user_id,
            'email' => $request->email,
            'is_duplicate' => '1'
        ];


        Mail::to($request->email)->send(new DuplicateFormMail($data));

        return ApiResponseService::success('Email sent successfully', []);
    }

    public function destroyJotForm($id)
    {
        $permission = 'jotform.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $jotform = JotForm::find($id);
        if (!$jotform) {
            return ApiResponseService::error('Jotform not found', 404);
        }
        $jotform->delete();
        return ApiResponseService::success('Jotform deleted successfully');
    }

    public function generateFormToken(Request $request)
    {
        $user_id = (string)$request->user_id;
        $encryptedData = encrypt(json_encode(['user_id' => $user_id, 'secret' => 'jotform_URD_!@#9823_secret$%DEC8901']));
        return ApiResponseService::success('Jotfrom token fetched successfully', $encryptedData);
    }

    public function getChatHash(Request $request)
    {
        $userId = $request->user_id;
        $secret = env('CHATBASE_SECRET');

        if (!$userId || !$secret) {
            return response()->json(['error' => 'Missing data'], 400);
        }

        $hash = hash_hmac('sha256', $userId, $secret);
        return ApiResponseService::success('Token fetched successfully', $hash);
    }

    public function sendFormLinkMail(Request $request)
    {
        try {
            // Create JotForm entry
            $form = JotForm::create([
                'user_id' => $request->user_id ?? '',
                'email' => $request->email ?? '',
                'phone' => $request->phone ?? '',
                'merchant_name' => $request->merchant_name ?? '',
                'iso_form_status' => 1
            ]);

            // Prepare email data
            $data = [
                'dba' => $request->dba ?? '',
                'merchant_name' => $request->merchant_name ?? '',
                'email' => $request->email ?? '',
                'phone' => $request->phone ?? '',
                'iso_form_link' => $request->iso_form_link ?? '',
                'form_id' => $form->id
            ];

            // Send email
            Mail::to($request->email)->send(new SendFormLinkMail($data));

            return ApiResponseService::success('Email sent successfully', []);
        } catch (\Exception $e) {
            return ApiResponseService::error('Failed to process request: ' . $e->getMessage(), 500);
        }
    }

    public function trackEmailOpen($form_id)
    {
        $form = JotForm::find($form_id);

        $statusUpdated = false;

        if ($form && $form->iso_form_status < 3) {
            $form->iso_form_status = 3; // Opened
            $form->save();
            $statusUpdated = true;
        }

        // Prepare response as JSON part
        $jsonResponse = json_encode([
            'success' => true,
            'message' => $statusUpdated ? 'Form status updated to opened.' : 'Form already opened or not found.',
            'data' => [],
        ]);

        // Transparent GIF (1x1 pixel)
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');

        // Combine image + JSON in multipart response (advanced) — or return just the image as below
        return Response::make($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($pixel),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            // Optional: expose API JSON in header (not visible in email clients)
            'X-API-Response' => $jsonResponse,
        ]);
    }

    public function trackFormClick($form_id)
    {
        $form = JotForm::find($form_id);

        if ($form) {
            // Update status if less than 4
            if ($form->iso_form_status < 4) {
                $form->iso_form_status = 4; // Link Clicked
                $form->save();
            }

            // Redirect to the actual ISO form link
            return redirect()->away($form->iso_form_link);
        }

        return response()->json(['error' => true, 'message' => 'Invalid form ID'], 404);
    }
}
