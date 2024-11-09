<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cred;
use App\Api\Traits\ValidationTrait;

class AdminCredController extends Controller
{
    use ValidationTrait;

    public function distributorList(Request $request)
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
        else {
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
        $query = Cred::select('users.name as driverName','u.name as distributorName','creds.date','creds.time','creds.cred_in','creds.cred_out')
        ->join('users','creds.driver_id','=','users.id')
        ->join('users as u','creds.user_id','=','u.id');

        // Check if a date is provided in the request
        if ($request->has('date')) {
            // Validate and format the date if needed
            $date = $request->input('date');
            // Assuming bill_date is stored as a date
            $query->whereDate('date', $date);
        }

         // Apply distributor filter if provided
         if ($request->has('distributor_id')) {
            $query->where('creds.user_id', $request->distributor_id);
        }

        // Retrieve the bills based on the query
        $creds = $query->orderBy('creds.id','desc')->get();
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
