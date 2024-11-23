<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cred;
use App\Api\Traits\ValidationTrait;

class DistributorCredController extends Controller
{
    use ValidationTrait;

    public function credListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if ($user) {
            // Initialize the query for bills
            $query = Cred::select('users.name as driverName','creds.date','creds.time','creds.cred_in','creds.cred_out')->join('users','creds.driver_id','=','users.id')->where('user_id', '=', $user);

            // Check if a date is provided in the request
            if ($request->has('start_date') && $request->has('end_date')) {
                // Validate and format the date if needed
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                // Assuming bill_date is stored as a date
                $query->whereBetween('date', [$start_date, $end_date]);
            }
            $total_cred_in = $query->sum('cred_in');
            $total_cred_out = $query->sum('cred_out');
            $total_cred_balance = $total_cred_in - $total_cred_out; 
            // Retrieve the bills based on the query
            $creds = $query->get();
            return response()->json([
                'status' => 'success',
                'creds' => $creds,
                'total_cred_in' => $total_cred_in,
                'total_cred_out' => $total_cred_out,
                'total_cred_balance' => $total_cred_balance,
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
