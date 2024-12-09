<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\SchemeCategory;

class AdminSchemeCategoryController extends Controller
{
    use ValidationTrait;

    public function schemeCategoryListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $schemeCategory = SchemeCategory::where('status',1)->get();
            return response()->json([
                'status' => 'success',
                'schemeCategory' => $schemeCategory,
                'message' => 'Scheme Category Listing',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function addSchemeCategory(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $schemeCategory = new SchemeCategory();
            $schemeCategory->title = $request->title;
            $schemeCategory->save();
            return response()->json([
                'status' => 'success',
                'schemeCategory' => $schemeCategory,
                'message' => 'Scheme Category Added',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function updateSchemeCategory(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $schemeCategory = SchemeCategory::find($request->id);
            $schemeCategory->title = $request->title;
            $schemeCategory->save();
            return response()->json([
                'status' => 'success',
                'schemeCategory' => $schemeCategory,
                'message' => 'Scheme Category Updated',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function deleteSchemeCategory(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $schemeCategory = SchemeCategory::find($request->id);
            if($schemeCategory->status == 1){
                $schemeCategory->status = 0;
                $message = 'Scheme Category Deleted';
            }
            else{
                $schemeCategory->status = 1;
                $message = 'Scheme Category Restored';
            }
            $schemeCategory->save();
            return response()->json([
                'status' => 'success',
                'schemeCategory' => $schemeCategory,
                'message' => $message,
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
