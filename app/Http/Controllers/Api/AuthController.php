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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;



class AuthController extends Controller
{
    use ValidationTrait;

    public function get_connection_id(Request $request)
    {
        // dd($request);
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

        // First check if user exists
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid user email. Please check your email address.'
            ], 401);
        }

        // Check role
        if ($user->role_id != $credentials['role_id']) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not authorized to login'
            ], 400);
        }

        // Check status
        if (!$user->status) {
            return response()->json([
                'status' => 'error',
                'message' => 'User account is inactive'
            ], 401);
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect password. Please try again.'
            ], 401);
        }

        // If we get here, everything is valid
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        $authCode = Str::random(20);
        ConnectionRequest::where('connection_id', $request->connection_id)->update([
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
            'user_detail' => $user,
        ], 200);
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

    public function editProfile(Request $request)
    {
        $user_id = $this->validate_user($request->connection_id, $request->auth_code);
        if($user_id)
        {
            // dd($request->image_path);
            $user = User::find($user_id);

            // Validation rules with unique check excluding current user
            $validator = Validator::make($request->all(), [
                'name' => $request->has('name') ? 'required|string|max:255' : '',
                'email' => $request->has('email') ? 'required|email' : '',
                'mobile' => $request->has('mobile') ? 'required|string|max:10' : '',
                'dob' => $request->has('dob') ? 'nullable|date_format:Y-m-d' : '',
                'aadhar_number' => $request->has('aadhar_number') ? 'required|string|max:12' : '',
                'pan_number' => $request->has('pan_number') ? 'nullable|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/' : '',
                'image_path' => $request->has('image_path') ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : '',
                'address' => $request->has('address') ? 'required|string' : '',
                'password' => $request->has('password') ? 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/' : '',
            ], [
                'pan_number.regex' => 'PAN number must be in this format: ABCDE1234F (first 5 capital letters, then 4 numbers, ending with 1 capital letter)',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&) and minimum 8 characters'
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
                if ($user->image_path && file_exists(public_path($user->image_path))) {
                    unlink(public_path($user->image_path));
                }

                $image = $request->file('image_path');
                $currentDateTime = now()->format('Y-m-d_H-i-s');

                // Determine upload directory based on role_id
                $uploadDir = match ($user->role_id) {
                    1 => 'uploads/admin',
                    2 => 'uploads/distributor',
                    3 => 'uploads/driver',
                    default => 'uploads/unknown'
                };
                $imageName = $request->name . '_' . $request->email . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path($uploadDir), $imageName);
                $request->merge(['image_path' => $uploadDir . '/' . $imageName]);
                $user->image_path = $uploadDir . '/' . $imageName;
            }
            if($request->has('name'))
            {
                $user->name = $request->name;
            }
            if($request->has('email'))
            {
                $user->email = $request->email;
            }
            if($request->has('mobile'))
            {
                $user->mobile = $request->mobile;
            }
            if($request->has('dob'))
            {
                $user->dob = $request->dob;
            }
            if($request->has('aadhar_number'))
            {
                $user->aadhar_number = $request->aadhar_number;
            }
            if($request->has('pan_number'))
            {
                $user->pan_number = $request->pan_number;
            }
            if($request->has('address'))
            {
                $user->address = $request->address;
            }
            if($request->has('password'))
            {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not authenticated',
            ], 401);
        }
    }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // Check if user exists and is an admin
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role_id !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not an admin.',
            ], 403); // Forbidden, as they are not an admin
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in users table with expiry time (5 minutes from now)
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        // Send OTP via email
        try {
            Mail::send('emails.forgot-password', ['otp' => $otp], function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset OTP');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent to your Registered email address',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again.',
            ], 500);
        }
    }


    // public function forgotPassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors(),
    //         ], 400);
    //     }

    //     // Check if user exists and is an admin
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || $user->role_id !== 1) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'You are not an admin.',
    //         ], 403); // Forbidden, as they are not an admin
    //     }

    //     // Generate OTP
    //     $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    //     // Store OTP in users table with expiry time (5 minutes from now)
    //     $user->otp = $otp;
    //     $user->otp_expires_at = now()->addMinutes(5);
    //     $user->save();

    //     // Send OTP via email
    //     try {
    //         // Sending OTP via email
    //         Mail::send('emails.forgot-password', ['otp' => $otp], function($message) use ($request) {
    //             $message->to($request->email)
    //                     ->subject('Password Reset OTP');
    //         });
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to send OTP to email. Please try again.',
    //         ], 500);
    //     }

    //     // Send OTP via SMS using Twilio
    //     try {
    //         // Ensure the phone number is in the correct format (e.g., +17756287797)
    //         $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

    //         $twilio->messages->create(
    //         '+91' . $user->mobile, // Ensure mobile is in correct format
    //             [
    //                 'from' => '+17756287797', // Twilio phone number in E.164 format
    //                 'body' => 'Your OTP for password reset is: ' . $otp
    //             ]
    //         );

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'OTP has been sent to your registered email and mobile number.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Log the exception message to get more details
    //         Log::error('Twilio SMS Error: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to send OTP to mobile. Please try again.',
    //         ], 500);
    //     }
    // }


    public function verifyOTP(Request $request)
    {
        // dd($request->password,$request->confirm);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                'confirmed', // This will ensure password and confirm password match
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character (@$!%*?&) and must be at least 8 characters long.',
            'password.confirmed' => 'The password and confirm password do not match.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // Validate OTP and reset the password if valid
        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP.',
            ], 400);
        }

        // Reset the user's password
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successful.',
        ], 200);
    }



    // public function resendOTP(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors(),
    //         ], 400);
    //     }

    //     // Generate new OTP
    //     $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    //     // Update OTP in database
    //     $user = User::where('email', $request->email)->first();
    //     $user->otp = $otp;
    //     $user->otp_expires_at = now()->addMinutes(5);
    //     $user->save();

    //     // Resend OTP via email
    //     try {
    //         Mail::send('emails.forgot-password', ['otp' => $otp], function($message) use ($request) {
    //             $message->to($request->email)
    //                     ->subject('Password Reset OTP');
    //         });

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'New OTP has been sent to your email address',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to send OTP. Please try again.',
    //         ], 500);
    //     }
    // }



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
