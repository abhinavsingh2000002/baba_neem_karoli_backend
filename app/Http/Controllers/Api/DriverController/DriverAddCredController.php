<?php

namespace App\Http\Controllers\Api\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Cred;
use Carbon\Carbon;

class DriverAddCredController extends Controller
{
    use ValidationTrait;

    public function distributorListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $distributor=User::select('users.id as distributorId','users.name as distributorName')->where('role_id','=',2)->where('status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor retrieved successfully',
            ], 200); // HTTP 200 OK
        }
      
    }

    public function credCreate(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $cred=new Cred();
            $cred->date = Carbon::now()->toDateString();
            $cred->time = Carbon::now()->toTimeString();
            $cred->user_id=$request->user_id;
            $cred->driver_id=$user;
            $cred->cred_out=$request->cred_out;
            $cred->save();
            return response()->json([
                'status' => 'success',
                'cred' => $cred,
                'message' => 'cred created successfully',
            ], 200); // HTTP 200 OK
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function credListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if ($user) {
            // Initialize the query for bills
            $query = Cred::select('users.name as distributorName','creds.date','creds.time','creds.cred_in','creds.cred_out')->join('users','creds.user_id','=','users.id')->where('driver_id', '=', $user);

            // Check if a date is provided in the request
            if ($request->has('date')) {
                // Validate and format the date if needed
                $date = $request->input('date');
                // Assuming bill_date is stored as a date
                $query->whereDate('date', $date);
            }

            // Retrieve the bills based on the query
            $creds = $query->get();
            return response()->json([
                'status' => 'success',
                'creds' => $creds,
                'message' => 'creds retrieved successfully',
            ], 200); // HTTP 200 OK
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
