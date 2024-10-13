<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ConnectionRequest;



class AuthController extends Controller
{
    use ValidationTrait;

    public function get_connection_id(Request $request)
    {
        $connection_id = $this->getConnectionId($request->api_key);
        // dd($connection_id);
       if($connection_id)
       {
            return response()->json([
                "status"=> "success",
                "connection_id"=> $connection_id,
                "message"=> "Connection Established Successfully"
            ], 200);
       }else
       {
            return response()->json([
                "status"=> "500",
                "message"=> "failed"
            ], 500);
        }
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'connection_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $findConnectionID = $this->validate_connection_id($request->connection_id);

        if (!$findConnectionID) {
            return response()->json(['message' => 'Invalid connection ID'], 400);
        }

        $credentials = $request->only('email', 'password','role_id');
        $check_role=User::where('email',$credentials['email'])->first();
        if($check_role->role_id!=$credentials['role_id']){
            return response()->json(['message' => 'User are not aurthorised to login'], 400);
        }
        $user = User::where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Create a new token for the user
            $token = Str::random(60);
            $user->remember_token = $token;
            $user->save();

            $authCode = Str::random(20);
            $helo=ConnectionRequest::where('connection_id',$request->connection_id)->update([
                'auth_code' => $authCode,
                'user_id' => $user->id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successful',
                'auth_code' => $authCode,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                // 'phone' => $user->mobile,
                // 'city' => $user->city,
                // 'state' => $user->user_state,
                // 'address' => $user->address,
                // 'image' => $user->pic,
                // 'gender' => $user->gender,
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function validate_user($connection_id, $auth_code)
    {
        $find_user = ConnectionRequest::where('connection_id', $connection_id)->where('auth_code', $auth_code)->first();
        if ($find_user) {
            return $find_user->user_id;
        } else {
            return false;
        }
    }


    public function logout(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if ($user) {
            // Revoke the user's tokens
            $user = User::find($user); // Ensure to get the User instance
            // $user->tokens->each(function ($token) {
            //     $token->revoke();
            // });

            $clear_connection = ConnectionRequest::where('user_id', $user->id)
                ->where('connection_id', $request->connection_id)
                ->where('auth_code', $request->auth_code)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful',
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not authenticated',
            ], 401);
        }
    }



    // public function loginSubmit(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //         'connection_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
    //     }

    //     $findConnectionID = $this->validate_connection_id($request->connection_id);

    //     if (!$findConnectionID) {
    //         return response()->json(['message' => 'Invalid connection ID'], 400);
    //     }

    //     $credentials = $request->only('email', 'password');

    //     if (Auth::guard('api')->attempt($credentials))
    //     {
    //         $user = Auth::guard('api')->user();
    //         if ($user)
    //         {
    //             $userData = DB::table('users')->where('email', $user->email)->first();
    //             $company = ConnectionRequest::where('connection_id', $request->connection_id)->select('company_id')->first();
    //             if($company)
    //             {
    //                 if($userData->company_id != $company->company_id){
    //                     return response()->json(['message' => 'User doesnot belong to this Company. Contact your administrator.'], 404);
    //                 }
    //             }

    //             $employeeDetails = DB::table('employee_details')->leftJoin('designations','designations.id','=','employee_details.designation_id')
    //                                    ->select('designations.name as designation')
    //                                    ->where('employee_details.user_id', $userData->id)->first();

    //             $authCode = Str::random(20);
    //             ConnectionRequest::create([
    //                 'connection_id' => $request->connection_id,
    //                 'auth_code' => $authCode,
    //                 'user_id' => $userData->id,
    //                 'created_at'=> now(),
    //                 'updated_at'=> now(),
    //              ]);

    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'User Login Successful',
    //                 'auth_code' => $authCode,
    //                 // 'id'        => $connectionRequestDetails->id,
    //                 'user_id'   => $userData->id,
    //                 'user_name' => $userData->name,
    //                 'user_email'=> $user->email,
    //                 'user_image'=> '/user-uploads/avatar/' . $userData->image,
    //                 'user_mobile'=> $userData->mobile,
    //                 'user_gender'=> $userData->gender,
    //                 'designation' => $employeeDetails->designation
    //             ], 200);

    //         }
    //         else
    //         {
    //             return response()->json(['message' => 'User not found after authentication'], 404);
    //         }

    //     }
    //     else {
    //         return response()->json(['message' => 'Login not found after authentication'], 404);
    //     }
    // }

    // public function logout(Request $request)
    // {
    //     $user = $this->validate_user($request->connection_id, $request->auth_code);
    //     if($user)
    //     {
    //         $clear_connection=ConnectionRequest::where('user_id',$user)->where('connection_id',$request->connection_id)->delete();
    //         Auth::guard('api')->logout();
    //         return response()->json(['message' => 'Logout successful','status'=>'success'], 200);
    //     }
    //     else
    //     {
    //         return response()->json(['message' => 'User not authenticated','status'=>'failed'], 401);
    //     }
    // }

    // public function validateLogin(Request $request)
    // {
    //     $user_id = $this->validate_user($request->connection_id, $request->auth_code);

    //     if ($user_id) {
    //             // User found, consider this as successful login
    //             return response()->json([
    //             'status' => 'success',
    //             'message' => 'Login successful',
    //             'is_logged_in' => true,
    //         ], 200);
    //         } else
    //         {
    //             // User not found or authentication failed
    //             return response()->json([
    //             'status' => 'success',
    //             'message' => 'User is not logged In',
    //             'is_logged_in' => false,
    //             ], 200);
    //     }
    // }

}
