<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminDistributorController extends Controller
{   
    use ValidationTrait;

    public function distributorListing(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $query = User::where('users.role_id', '=', 2)
                ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
                ->leftJoin('payments', 'users.id', '=', 'payments.user_id')
                ->select(
                    'users.*',
                    \DB::raw('COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = users.id AND orders.order_status IN (2, 3)), 0.00) as total_order_amount'),
                    \DB::raw('COALESCE((SELECT SUM(amount_paid) FROM payments WHERE payments.user_id = users.id), 0.00) as total_paid_amount'),
                    \DB::raw('COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = users.id AND orders.order_status IN (2, 3)), 0.00) - COALESCE((SELECT SUM(amount_paid) FROM payments WHERE payments.user_id = users.id), 0.00) as outstanding_balance')
                )
                ->groupBy(
                    'users.id', 'users.name', 'users.email', 'users.mobile', 'users.dob', 
                    'users.aadhar_number', 'users.pan_number', 'users.image_path', 
                    'users.address', 'users.role_id', 'users.status', 'users.password',
                    'users.created_at', 'users.updated_at'
                );

            // Add search filters
            if($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%")
                      ->orWhere('users.mobile', 'LIKE', "%{$search}%")
                      ->orWhere('users.dob', 'LIKE', "%{$search}%")
                      ->orWhere('users.aadhar_number', 'LIKE', "%{$search}%")
                      ->orWhere('users.pan_number', 'LIKE', "%{$search}%");
                });
            }

            // Add status filter
            if($request->has('status')) {
                $query->where('users.status', $request->status);
            }

            $distributor = $query->orderBy('users.id','desc')->get();
            
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor retrieved successfully',
            ], 200);
        }   
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function distributorDetailListing(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $distributor = User::where('role_id','=',2)->find($request->distributor_id);
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor retrieved successfully',
            ], 200);
        }   
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }
    
    public function addDistributor(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:10|unique:users,mobile',
                'dob' => 'nullable|date_format:Y-m-d',
                'aadhar_number' => 'required|string|max:12',
                'pan_number' => 'nullable|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10',
                'image_path' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'address' => 'required|string',
                'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&) and minimum 8 characters',
                'pan_number.regex' => 'PAN number must be in this format: ABCDE1234F (first 5 capital letters, then 4 numbers, ending with 1 capital letter)'
            ]); 

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            // Handle image upload
            if ($request->hasFile('image_path')) {
                $image = $request->file('image_path');
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $imageName = $request->name . '_' . $request->email . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/distributor'), $imageName);
            }

            $distributor = new User();
            $distributor->name=$request->name;
            $distributor->email=$request->email;
            $distributor->mobile=$request->mobile;  
            $distributor->dob=$request->dob;
            $distributor->aadhar_number=$request->aadhar_number;
            $distributor->pan_number=$request->pan_number;
            $distributor->address=$request->address;
            $distributor->role_id=2;
            $distributor->status=1;
            $distributor->image_path = 'uploads/distributor/' . $imageName;
            $distributor->password=Hash::make($request->password);  
            $distributor->save();
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor added successfully',
            ], 200);    
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function updateDistributor(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $distributor = User::where('role_id', '=', 2)->find($request->distributor_id);
            
            if(!$distributor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Distributor not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,'.$distributor->id,
                'mobile' => 'sometimes|string|max:10|unique:users,mobile,'.$distributor->id,
                'dob' => 'nullable|date_format:Y-m-d',
                'aadhar_number' => 'sometimes|string|max:12',
                'pan_number' => 'nullable|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10',
                'image_path' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'address' => 'sometimes|string',
                'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&) and minimum 8 characters',
                'pan_number.regex' => 'PAN number must be in this format: ABCDE1234F (first 5 capital letters, then 4 numbers, ending with 1 capital letter)'
            ]); 

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            // Handle image upload if new image is provided
            if ($request->hasFile('image_path')) {
                // Delete old image if exists
                if ($distributor->image_path && file_exists(public_path($distributor->image_path))) {
                    unlink(public_path($distributor->image_path));
                }
                
                $image = $request->file('image_path');
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $imageName = $request->name . '_' . $request->email . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/distributor'), $imageName);
                $distributor->image_path = 'uploads/distributor/' . $imageName;
            }

            // Update only the fields that are provided in the request
            if($request->has('name')) $distributor->name = $request->name;
            if($request->has('email')) $distributor->email = $request->email;
            if($request->has('mobile')) $distributor->mobile = $request->mobile;
            if($request->has('dob')) $distributor->dob = $request->dob;
            if($request->has('aadhar_number')) $distributor->aadhar_number = $request->aadhar_number;
            if($request->has('pan_number')) $distributor->pan_number = $request->pan_number;
            if($request->has('address')) $distributor->address = $request->address;
            if($request->has('password')) $distributor->password = Hash::make($request->password);

            $distributor->save();
            
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor updated successfully',
            ], 200);    
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function deleteOrRestoreDistributor(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $distributor = User::where('role_id', '=', 2)->find($request->distributor_id);
            if($distributor->status == 1){
                $distributor->status = 0;
                $message = 'Distributor deleted successfully';
            }
            else{
                $distributor->status = 1;
                $message = 'Distributor restored successfully';
            }
            $distributor->save();
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => $message,
            ], 200);
        }   
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }
}