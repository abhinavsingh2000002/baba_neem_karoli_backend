<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Product;
use App\Models\MapProductPrice;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\Bill;
use Carbon\Carbon;
use App\Models\OrderDetail;

class DistributorProductOrderController extends Controller
{
    use ValidationTrait;
    public function productListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            $products = MapProductPrice::select('products.*','map_product_prices.price')
                ->join('products','map_product_prices.product_id','=','products.id')
                ->where('map_product_prices.user_id','=',$user)
                ->where('products.status','=', 1)->get();

            if ($products) {
                return response()->json([
                    'status' => 'success',
                    'products' => $products,
                    'message' => 'Products retrieved successfully',
                ], 200); // HTTP 200 OK
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated',
        ], 401); // HTTP 401 Unauthorized
    }

    public function addToCart(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            $existingProduct = ShoppingCart::where('product_id', $request->product_id)
                                        ->where('user_id', $request->user_id)
                                        ->first();

            if ($existingProduct) {
                $shoppingCart = ShoppingCart::find($existingProduct->id);
                $shoppingCart->quantity = $request->quantity;
                $shoppingCart->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Product quantity updated successfully',
                ], 200); // HTTP 200 OK
            } else {
                $shoppingCart = new ShoppingCart();
                $shoppingCart->quantity = $request->quantity;
                $shoppingCart->user_id = $user;
                $shoppingCart->product_id = $request->product_id;

                if ($shoppingCart->save()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Product added to cart successfully',
                    ], 200); // HTTP 200 OK
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'An error occurred while adding the product to the cart.',
                    ], 500); // HTTP 500 Internal Server Error
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function updateCartProductQuantity(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            $cartProduct=ShoppingCart::find($request->shopping_cart_id);
            $cartProduct->quantity = $request->quantity;
            $cartProduct->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product quantity updated successfully',
            ], 200); //
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function cartProduct(Request $request)
    {
        // Validate user authentication
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Fetch cart products for the authenticated user
            $cartProducts = ShoppingCart::
            select('products.*','map_product_prices.price','shopping_carts.quantity','shopping_carts.id as shopping_cart_id')
            ->join('products','shopping_carts.product_id','=','products.id')
           ->join('map_product_prices','products.id','=','map_product_prices.product_id')->where('products.status','=',1)->where('shopping_carts.user_id', '=', $user)
           ->where('map_product_prices.user_id','=',$user)->where('products.status','=',1)->get();

            return response()->json([
                'status' => 'success',
                'cartProducts' => $cartProducts,
                'message' => 'Cart Products retrieved successfully',
            ], 200); // HTTP 200 OK
        } else {
            // User is not authenticated
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function removeCartProduct(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Find the shopping cart by ID and delete it
            $shoppingCart = ShoppingCart::find($request->shopping_cart_id);

            if ($shoppingCart) {
                $shoppingCart->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Shopping cart Product deleted successfully.'
                ], 200); // HTTP status code 200 OK
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shopping cart Product not Found.'
                ], 404); // HTTP status code 404 Not Found
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.'
            ], 401); // HTTP status code 401 Unauthorized
        }
    }

    // public function oderPlaced(Request $request)
    // {
    //     $user = $this->validate_user($request->connection_id, $request->auth_code);
    //     if($user)
    //     {
    //         // Get current date and time
    //         $currentDate = Carbon::now()->toDateString(); // YYYY-MM-DD
    //         $currentTime = Carbon::now();

    //         // Define allowed time window
    //         $startTime = Carbon::createFromTimeString('06:00');
    //         $endTime = Carbon::createFromTimeString('19:00');

    //         // Check if user already placed an order today
    //         $existingOrder = Order::where('user_id', $user)
    //                             ->where('order_date', $currentDate)
    //                             ->first();

    //         // Restrict order placement if an order already exists today
    //         if ($existingOrder) {
    //             return response()->json(['failed' => 'You have already placed an order today. Please try again tomorrow.']);
    //         }

    //         // Check if the current time is within the allowed time range
    //         if ($currentTime->lt($startTime) || $currentTime->gt($endTime)) {
    //             return response()->json(['failed' => 'Orders can only be placed between 6:00 AM and 7:00 PM.']);
    //         }

    //         $total_order=ShoppingCart::select('shopping_carts.quantity','products.*','map_product_prices.price')->join('products','shopping_carts.product_id','=','products.id')
    //         ->join('map_product_prices','shopping_carts.product_id','map_product_prices.product_id')->where('shopping_carts.user_id',$user)
    //         ->where('map_product_prices.user_id',$user)
    //         ->where('products.status','=',1)
    //         ->where('map_product_prices.status','=',1)->get();
    //         // dd($total_order);
    //         $totalAmount=[];
    //         foreach($total_order as $tot_order)
    //         {
    //             $price = floatval($tot_order->price);
    //             $product_quantity = floatval($tot_order->quantity);
    //             $totalAmount[] = $price * $product_quantity;
    //         }
    //         $totalAmount=array_sum($totalAmount);
    //         if(count($total_order)>0){
    //             $order=new Order();
    //             do {
    //                 $orderNo = mt_rand(1000000000, 9999999999);
    //             } while (Order::where('order_no', $orderNo)->exists());
    //             $order->order_no = $orderNo;
    //             $order->user_id=$user;
    //             $order->total_amount=$totalAmount;
    //             $order->order_date=Carbon::now()->toDateString(); // YYYY-MM-DD format
    //             $order->order_time=Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
    //             $order->save();
    //         }
    //         else{
    //             return response()->json(['failed'=>'Please Add Item to the cart']);
    //         }
    //         if($order){
    //             $bill=new Bill();
    //             do {
    //                 $billNo = mt_rand(1000000000, 9999999999);
    //             } while (Bill::where('bill_no', $billNo)->exists());
    //             $bill->bill_no=$billNo;
    //             $bill->user_id=$user;
    //             $bill->order_id=$order->id;
    //             $bill->order_no=$order->order_no;
    //             $bill->bill_date=Carbon::now()->toDateString(); // YYYY-MM-DD format
    //             $bill->bill_time=Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
    //             $bill->save();
    //         }
    //         if($order){
    //             foreach($total_order as $total_order_one)
    //             {
    //                 // dd($total_order_one);

    //                 $orderDetails=new OrderDetail();
    //                 $orderDetails->order_id=$order->id;
    //                 $orderDetails->order_no=$order->order_no;
    //                 $orderDetails->user_id=$user;
    //                 $orderDetails->product_no=$total_order_one->product_no;
    //                 $orderDetails->product_id=$total_order_one->id;
    //                 $orderDetails->product_name=$total_order_one->product_name;
    //                 $orderDetails->product_no=$total_order_one->product_no;
    //                 $orderDetails->product_name=$total_order_one->product_name;
    //                 $orderDetails->company_name=$total_order_one->company_name;
    //                 $orderDetails->product_image=$total_order_one->product_image;
    //                 $orderDetails->product_description=$total_order_one->product_description;
    //                 $orderDetails->product_weight=$total_order_one->product_quantity;
    //                 $orderDetails->product_quantity=$total_order_one->quantity;
    //                 $orderDetails->item_per_cred=$total_order_one->item_per_cred;
    //                 $price = floatval($total_order_one->price);
    //                 $product_quantity = floatval($total_order_one->quantity);
    //                 $orderDetails->amount = $price * $product_quantity;
    //                 // dd($price,$product_quantity);
    //                 $orderDetails->save();
    //             }
    //         return response()->json(['success'=>'Order Placed Successfully']);
    //         }
    //     }else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'User not authenticated.'
    //         ], 401); // HTTP status code 401 Unauthorized
    //     }
    // }
    public function orderPlaced(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if ($user) {
            // Get current date and time
            $currentDate = Carbon::now()->toDateString(); // YYYY-MM-DD
            $currentTime = Carbon::now();

            // Define allowed time window
            $startTime = Carbon::createFromTimeString('06:00');
            $endTime = Carbon::createFromTimeString('19:00');

            // Check if user already placed an order today
            $existingOrder = Order::where('user_id', $request->distributor_id)
            ->where('order_date', $currentDate)
            ->first();

            // Restrict order placement if an order already exists today
            if ($existingOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already placed an order today. Please try again tomorrow.'
            ], 400); // HTTP status code 400 Bad Request
        }


            // Check if the current time is within the allowed time range
            if ($currentTime->lt($startTime) || $currentTime->gt($endTime)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Orders can only be placed between 6:00 AM to 7:00 PM.'
                ], 400); // HTTP status code 400 Bad Request
            }

            $total_order = ShoppingCart::select('shopping_carts.quantity', 'products.*', 'map_product_prices.price')
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('map_product_prices', 'shopping_carts.product_id', 'map_product_prices.product_id')
                ->where('shopping_carts.user_id', $user)
                ->where('map_product_prices.user_id', $user)
                ->where('products.status', '=', 1)
                ->where('map_product_prices.status', '=', 1)
                ->get();

            $totalAmount = [];
            foreach ($total_order as $tot_order) {
                $price = floatval($tot_order->price);
                $product_quantity = floatval($tot_order->quantity);
                $totalAmount[] = $price * $product_quantity;
            }
            $totalAmount = array_sum($totalAmount);

            if (count($total_order) > 0) {
                $order = new Order();
                do {
                    $orderNo = mt_rand(1000000000, 9999999999);
                } while (Order::where('order_no', $orderNo)->exists());

                $order->order_no = $orderNo;
                $order->user_id = $user;
                $order->total_amount = $totalAmount;
                $order->order_date = Carbon::now()->toDateString(); // YYYY-MM-DD format
                $order->order_time = Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
                $order->save();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please Add Item to the cart'
                ], 400); // HTTP status code 400 Bad Request
            }

            if ($order) {
                $bill = new Bill();
                do {
                    $billNo = mt_rand(1000000000, 9999999999);
                } while (Bill::where('bill_no', $billNo)->exists());

                $bill->bill_no = $billNo;
                $bill->user_id = $user;
                $bill->order_id = $order->id;
                $bill->order_no = $order->order_no;
                $bill->bill_date = Carbon::now()->toDateString(); // YYYY-MM-DD format
                $bill->bill_time = Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
                $bill->save();
            }

            if ($order) {
                foreach ($total_order as $total_order_one) {
                    $orderDetails = new OrderDetail();
                    $orderDetails->order_id = $order->id;
                    $orderDetails->order_no = $order->order_no;
                    $orderDetails->user_id = $user;
                    $orderDetails->product_no = $total_order_one->product_no;
                    $orderDetails->product_id = $total_order_one->id;
                    $orderDetails->product_name = $total_order_one->product_name;
                    $orderDetails->company_name = $total_order_one->company_name;
                    $orderDetails->product_image = $total_order_one->product_image;
                    $orderDetails->product_description = $total_order_one->product_description;
                    $orderDetails->product_weight = $total_order_one->product_quantity;
                    $orderDetails->product_quantity = $total_order_one->quantity;
                    $orderDetails->item_per_cred = $total_order_one->item_per_cred;
                    $price = floatval($total_order_one->price);
                    $product_quantity = floatval($total_order_one->quantity);
                    $orderDetails->amount = $price * $product_quantity;
                    $orderDetails->save();
                }
                return response()->json([
                    'status' => 'success',
                    'order'=>$order,
                    'bill'=>$bill,
                    'orderDetails'=>$orderDetails,
                    'message' => 'Order Placed Successfully'
                ], 200); // HTTP status code 200 Created
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.'
            ], 401); // HTTP status code 401 Unauthorized
        }
    }

}
