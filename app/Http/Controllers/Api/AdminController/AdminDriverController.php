<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminDriverController extends Controller
{
    use ValidationTrait;

    public function driverListing(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $query = User::where('role_id', '=', 3);
            
            // Add search filters
            if($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('mobile', 'LIKE', "%{$search}%")
                      ->orWhere('dob', 'LIKE', "%{$search}%")
                      ->orWhere('aadhar_number', 'LIKE', "%{$search}%")
                      ->orWhere('pan_number', 'LIKE', "%{$search}%");
                });
            }

            // Add status filter
            if($request->has('status')) {
                $query->where('status', $request->status);
            }

            $drivers = $query->orderBy('id','desc')->get();
            
            return response()->json([
                'status' => 'success',
                'drivers' => $drivers,
                'message' => 'Driver retrieved successfully',
            ], 200);
        }   
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function driverDetailListing(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $driver = User::where('role_id','=',3)->find($request->driver_id);
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
                'message' => 'Driver retrieved successfully',
            ], 200);
        }   
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function addDriver(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:10|unique:users,mobile',
                'dob' => 'nullable|date_format:Y-m-d',
                'aadhar_number' => 'required|string|max:12|unique:users,aadhar_number',
                'pan_number' => 'nullable|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10|unique:users,pan_number',
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
                $image->move(public_path('uploads/driver'), $imageName);
            }

            $driver = new User();
            $driver->name=$request->name;
            $driver->email=$request->email;
            $driver->mobile=$request->mobile;  
            $driver->dob=$request->dob;
            $driver->aadhar_number=$request->aadhar_number;
            $driver->pan_number=$request->pan_number;
            $driver->address=$request->address;
            $driver->role_id=3;
            $driver->status=1;
            $driver->image_path = 'uploads/driver/' . $imageName;
            $driver->password=Hash::make($request->password);  
            $driver->save();
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
                'message' => 'Driver added successfully',
            ], 200);    
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function updateDriver(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $driver = User::where('role_id', '=', 3)->find($request->driver_id);
            
            if(!$driver) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Driver not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,'.$driver->id,
                'mobile' => 'sometimes|string|max:10|unique:users,mobile,'.$driver->id,
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
                if ($driver->image_path && file_exists(public_path($driver->image_path))) {
                    unlink(public_path($driver->image_path));
                }
                
                $image = $request->file('image_path');
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $imageName = $request->name . '_' . $request->email . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/driver'), $imageName);
                $driver->image_path = 'uploads/driver/' . $imageName;
            }

            // Update only the fields that are provided in the request
            if($request->has('name')) $driver->name = $request->name;
            if($request->has('email')) $driver->email = $request->email;
            if($request->has('mobile')) $driver->mobile = $request->mobile;
            if($request->has('dob')) $driver->dob = $request->dob;
            if($request->has('aadhar_number')) $driver->aadhar_number = $request->aadhar_number;
            if($request->has('pan_number')) $driver->pan_number = $request->pan_number;
            if($request->has('address')) $driver->address = $request->address;
            if($request->has('password')) $driver->password = Hash::make($request->password);

            $driver->save();
            
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
                'message' => 'Driver updated successfully',
            ], 200);    
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function deleteOrRestoreDriver(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $driver = User::where('role_id', '=', 3)->find($request->driver_id);
            if($driver->status == 1){
                $driver->status = 0;
                $message = 'Driver deleted successfully';
            }
            else{
                $driver->status = 1;
                $message = 'Driver restored successfully';
            }
            $driver->save();
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
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
