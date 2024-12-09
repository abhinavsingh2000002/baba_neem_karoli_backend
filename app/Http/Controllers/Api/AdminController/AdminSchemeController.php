<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Scheme;
use App\Models\SchemeCategory;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Order;

class AdminSchemeController extends Controller
{
        use ValidationTrait;

        public function schemeCategoryListing(Request $request)
        {
           $user = $this->validate_user($request->connection_id, $request->auth_code);
           if($user){
            $schemeCategory = SchemeCategory::where('status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'schemeCategory' => $schemeCategory,
                'message' => 'Scheme Category Retrieved Successfully',
            ], 200); // HTTP 200 OK
           }
           else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
           }
        }

        public function distributorListing(Request $request)
        {
            $user = $this->validate_user($request->connection_id, $request->auth_code);
            if($user){
                $distributor = User::where('role_id','=',2)->where('status','=',1)->get();
                return response()->json([
                    'status' => 'success',
                    'distributor' => $distributor,
                    'message' => 'Distributor Retrieved Successfully',
                ], 200); // HTTP 200 OK
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401); // HTTP 401 Unauthorized
            }
        }

    public function schemeListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $query = Scheme::join('scheme_categorys','schemes.scheme_category_id','=','scheme_categorys.id')
                ->join('users','schemes.user_id','=','users.id')
                ->select('scheme_categorys.title as schemeCategoryTitle','users.name as userName','schemes.*');

            // Filter by user_id if provided
            if ($request->has('user_id')) {
                $query->where('schemes.user_id', $request->user_id);
            }

            // Filter by date if provided
            if ($request->has('date')) {
                $query->whereDate('schemes.scheme_applied_date', $request->date);
            }

            $scheme = $query->get();

            return response()->json([
                'status' => 'success',
                'scheme' => $scheme,
                'message' => 'Scheme Retrieved Successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function schemeApply(Request $request)
        {
            $user = $this->validate_user($request->connection_id, $request->auth_code);
            if($user){
                // Check if scheme already exists for this month and user
                $existingScheme = Scheme::where('user_id', $request->user_id)
                    ->whereMonth('scheme_applied_for_month', Carbon::parse($request->scheme_applied_for_month)->month)
                    ->whereYear('scheme_applied_for_month', Carbon::parse($request->scheme_applied_for_month)->year)
                    ->first();

                if ($existingScheme) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Scheme already applied for this month',
                    ], 400);
                }

                $totalAmount = Order::where('user_id', '=', $request->user_id)
                    ->where('order_status', '=', 3)
                    ->whereMonth('order_date', '=', Carbon::parse($request->scheme_applied_for_month)->month)
                    ->whereYear('order_date', '=', Carbon::parse($request->scheme_applied_for_month)->year)
                    ->sum('total_amount');

                // Check if no orders found
                if($totalAmount == 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No orders found for the given month',
                    ], 400);
                }

                // Check if total amount is greater than scheme amount
                if($totalAmount >= $request->scheme_applied_amount){
                    $scheme = new Scheme();
                    $scheme->user_id = $request->user_id;
                    $scheme->scheme_category_id = $request->scheme_category_id;
                    $scheme->scheme_applied_for_month = $request->scheme_applied_for_month;
                    $scheme->before_scheme_applied_amount = $totalAmount;
                    $scheme->scheme_applied_amount = $request->scheme_applied_amount;
                    $scheme->after_scheme_applied_amount = $totalAmount-$request->scheme_applied_amount;
                    $scheme->scheme_applied_date = Carbon::now()->toDateString();
                    $scheme->scheme_applied_time = Carbon::now()->toTimeString();
                    $scheme->save();

                return response()->json([
                    'status' => 'success',
                    'scheme' => $scheme,
                    'message' => 'Scheme Applied Successfully',
                ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Order amount is less than scheme amount. Cannot apply scheme.',
                    ], 400);
                }
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }
    }
}
