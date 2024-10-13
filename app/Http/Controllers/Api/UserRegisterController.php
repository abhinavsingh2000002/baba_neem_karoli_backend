<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UserRegisterController extends Controller
{
    use ValidationTrait;

    public function Register(Request $request)
    {
        $user_id=$this->validate_user($request->connection_id,$request->auth_code);
        if($user_id){
            $admin=$this->validate_admin($user_id,$request->check_role);
                if($admin){
                    $rules = [
                        'name' => 'required|string|min:3|max:100',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|string|min:8|max:50',
                        'mobile' => 'required|digits:10|unique:users,mobile',
                        'dob' => 'required|date|before:today',
                        'address' => 'required|string|min:5|max:255',
                        'aadhar_number' => 'required|digits:12|unique:users,aadhar_number',
                        'pan_number' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|unique:users,pan_number',
                        'role_id' => 'required|integer|in:1,2,3',
                        'image_path' => 'required|mimes:jpeg,png|max:10240',

                    ];
                    $validator = Validator::make($request->all(), $rules);
                    if (!$validator->fails()) {

                        $image_file = $request->image_path;
                        $name_email = str_replace(' ', '_', $request->name) . '_' . $request->email;
                        $role = $request->role_id;

                        $folderPath = ($role == '2') ? '/uploads/distributor/' : '/uploads/driver/';


                        if (!is_dir(public_path($folderPath))) {
                            mkdir(public_path($folderPath), 0777, true);
                        }

                        // Get the uploaded file
                        $file = $request->file('image_path');
                        $extension = $file->getClientOriginalExtension();


                        $image_name = $name_email . '_' . time();
                        $filename = $image_name . '.' . $extension;
                        $path = public_path($folderPath . $filename);

                        // Move the uploaded file to the correct folder
                        $file->move(public_path($folderPath), $filename);


                        $user= new User();
                        $user->name=$request->name;
                        $user->email=$request->email;
                        $user->password=Hash::make($request->password);
                        $user->mobile=$request->mobile;
                        $user->dob=$request->dob;
                        $user->address=$request->address;
                        $user->aadhar_number=$request->aadhar_number;
                        $user->pan_number=$request->pan_number;
                        $user->image_path=$folderPath . $filename;
                        $user->role_id=$request->role_id;
                        $user->created_at=now();
                        $user->updated_at=now();
                        $user->save();

                        return response()->json([
                            'status'=>'success',
                            'message'=>'User Registered Successfully',
                            'user'=>$user
                        ],200);
                    }
                    else{
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Validation failed',
                            'errors'  => $validator->errors()
                        ],400);
                    }
                }
                else{
                    return response()->json(['status' => 'error', 'message' => 'You are not aurthorised to register the new user'],400);
                }
        }
        else{
            return response()->json(['status' => 'error', 'message' => 'invalid connection'],400);
        }

    }

    public function Listing(Request $request){

        $user_id=$this->validate_user($request->connection_id,$request->auth_code);
        if($user_id){
            $admin=$this->validate_admin($user_id,$request->check_role);
            if($admin){
                $distributor=User::where('role_id',$request->role_id)->get();
                return response([
                    'status'=>'success',
                    'message'=>'Data Fetched Successfully',
                    'user'=>$distributor
                ]);
            }
            else{
                return response()->json(['status' => 'error', 'message' => 'You are not aurthorised to show distributor'],400);
            }
        }
        else{
            return response()->json(['status' => 'error', 'message' => 'invalid connection'],400);
        }

    }

    public function DetailListing(Request $request){

        $user_id=$this->validate_user($request->connection_id,$request->auth_code);
        if($user_id){
            $admin=$this->validate_admin($user_id,$request->check_role);
            if($admin){
                $distributor=User::where('id',$request->id)->first();
                return response([
                    'status'=>'success',
                    'message'=>'Data Fetched Successfully',
                    'user'=>$distributor
                ]);
            }
            else{
                return response()->json(['status' => 'error', 'message' => 'You are not aurthorised to show distributor'],400);
            }
        }
        else{
            return response()->json(['status' => 'error', 'message' => 'invalid connection'],400);
        }
    }

    public function Edit(Request $request){
        $user_id=$this->validate_user($request->connection_id,$request->auth_code);
        if($user_id){
            $admin=$this->validate_admin($user_id,$request->check_role);
            if($admin){

            }
            else{
                return response()->json(['status' => 'error', 'message' => 'You are not aurthorised to show distributor'],400);
            }
        }
        else{
            return response()->json(['status' => 'error', 'message' => 'invalid connection'],400);
        }
    }

}
