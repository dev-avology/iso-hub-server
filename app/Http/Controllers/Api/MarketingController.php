<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ApiResponseService; // Import API response service
use App\Models\MarketingCat;
use App\Models\MarketingItems;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
{
    protected $DashboardService;

    public function __construct(DashboardService $DashboardService)
    {
        $this->DashboardService = $DashboardService;
    }

    // Create a category
    public function createCategory(Request $request)
    {
        $permission = 'marketing';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = MarketingCat::create([
            'name' => $request->name,
            'user_id' => $request->user_id
        ]);
        return ApiResponseService::success('Category created successfully', $category);
    }

    // Create an item
    public function createItem(Request $request)
    {
        $permission = 'marketing';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $categoryId = $request->category_id;
        $items = $request->items;

        $validator = Validator::make($request->all(), [
            'category_id'       => 'required|exists:marketing_categories,id',
            'items'             => 'required|array|min:1',
            'items.*.title'     => 'required|string|max:255',
            'items.*.description'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $createdItems = [];

        foreach ($items as $item) {
            $createdItems[] = MarketingItems::create([
                'category_id' => $categoryId,
                'title'       => $item['title'] ?? null,
                'description'       => $item['description'] ?? null,
            ]);
        }
        return ApiResponseService::success('Category items created successfully', $createdItems);
    }

    // public function updateCategory(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return ApiResponseService::error('Validation error', $validator->errors(), 422);
    //     }

    //     $category = MarketingCat::find($id);

    //     if (!$category) {
    //         return ApiResponseService::error('Category not found', [], 404);
    //     }

    //     $category->update([
    //         'name' => $request->name,
    //     ]);

    //     return ApiResponseService::success('Category updated successfully', $category);
    // }

    public function updateItem(Request $request)
    {
        $permission = 'marketing';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'description' => 'required',
            'id'        => 'required|exists:marketing_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category_item = MarketingItems::find($request->id);

        if (!$category_item) {
            return ApiResponseService::error('Item not found', 400);
        }

        $category_item->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ApiResponseService::success('Items updated successfully', $category_item);
    }

    public function getCatWithItem(Request $request)
    {
        $permission = 'marketing.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        if(Auth::user()->role_id  == '5'){
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if(Auth::user()->id != $request->user_id){
                return ApiResponseService::error('Data cant fetch, Wrong user id!', 400);  
            }
        }

        $query = MarketingCat::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $cat = $query->with('items')->get();

        return ApiResponseService::success('Category fetched successfully', $cat);
    }


    public function getItemDetails($id)
    {
        $permission = 'marketing.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $items = MarketingItems::where('id', $id)->first();
        return ApiResponseService::success('Category item fetched successfully', $items);
    }

    public function removeItem($id)
    {
        $permission = 'marketing.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $item = MarketingItems::find($id);
        $item->delete();
        return ApiResponseService::success('Item deleted successfully', []);
    }

    public function removeCategory($id)
    {
        $permission = 'marketing.view';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }
        
        $category = MarketingCat::with('items')->find($id);

        if (!$category) {
            return ApiResponseService::error('Category not found', 404);
        }

        // Delete all related items first
        foreach ($category->items as $item) {
            $item->delete();
        }

        // Then delete the category
        $category->delete();

        return ApiResponseService::success('Category and its items deleted successfully', []);
    }
}
