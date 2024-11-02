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
