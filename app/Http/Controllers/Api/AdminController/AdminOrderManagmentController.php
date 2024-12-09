<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Api\Traits\ValidationTrait;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use App\Models\MapProductPrice;
use App\Models\ShoppingCart;
use App\Models\Bill;



class AdminOrderManagmentController extends Controller
{
    use ValidationTrait;

    public function orderManagmentListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = Order::select('orders.id', 'orders.order_no', 'orders.order_date', 'orders.order_time', 'orders.order_status', 'orders.total_amount',
                \DB::raw('(SELECT COUNT(*) FROM order_details WHERE order_details.order_id = orders.id) as total_items'),
                'users.name as distributorName'
            )->join('users', 'users.id', '=', 'orders.user_id');
            if($request->has('date'))
            {
                $query->whereDate('orders.order_date', $request->date);
            }
            if($request->has('search'))
            {
                $query->where('orders.order_no', 'like', '%'.$request->search.'%');
                $query->orWhere('users.name', 'like', '%'.$request->search.'%');
            }

            if($request->has('status'))
            {
                $query->where('orders.order_status', $request->status);
            }
            $query->orderBy('orders.order_date', 'desc');

            return response()->json([
                'status' => 'success',
                'data' => $query->get(),
                'message' => 'Order managment listing retrieved successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function orderManagmentDetails(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order = Order::select('user_id','order_no', 'order_date','order_time', 'order_status', 'total_amount',
                \DB::raw('(SELECT COUNT(*) FROM order_details WHERE order_details.order_id = orders.id) as total_items')
            )->find($request->order_id);
            $orderDetails = OrderDetail::where('order_id', $request->order_id)->get();
            $distributor = User::select('name', 'mobile', 'address','image_path')->find($order->user_id);
            if($order && $orderDetails)
            {
                return response()->json([
                    'status' => 'success',
                    'order' => $order,
                    'orderDetails' => $orderDetails,
                    'distributor' => $distributor,
                    'message' => 'Order managment details retrieved successfully',
                ]);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found',
                ], 404);
            }
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order = Order::find($request->order_id);
            $order->order_status = $request->status;
            if ($request->status == 0) {
                $order->order_failed_date = Carbon::now()->toDateString();
                $order->order_failed_time = Carbon::now()->toTimeString();
            } elseif ($request->status == 2) {
                $order->order_confirm_date = Carbon::now()->toDateString();
                $order->order_confirm_time = Carbon::now()->toTimeString();
            }
            $order->save();

            // Custom message based on status
            $message = 'Order status updated successfully';
            if ($request->status == 0) {
                $message = 'Order rejected successfully';
            } elseif ($request->status == 2) {
                $message = 'Order accepted successfully';
            }

            return response()->json([
                'status' => 'success',
                'data' => $order,
                'message' => $message,
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function productListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order=Order::find($request->order_id);
            $products = MapProductPrice::select('products.id as productId','products.product_name','products.product_no','products.product_quantity','products.item_per_cred','map_product_prices.price')->join('products','map_product_prices.product_id','=','products.id')
            ->where('user_id',$order->user_id)->where('products.status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'data' => $products,
                'message' => 'Product listing retrieved successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }


    public function updateOrderProducts(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order = Order::find($request->order_id);
            $totalAmount = 0; // Initialize total amount

            // Loop through existing products to update or delete them
            foreach (json_decode($request->details) as $detailData) {
                // dd($detailData->delete);
                if (isset($detailData->delete) && $detailData->delete) {
                    // Delete the product if marked for removal
                    OrderDetail::find($detailData->id)->delete();
                } else {
                    // Update existing product
                    $orderDetail = OrderDetail::find($detailData->id);
                    // Fetch product price
                    $product_price = MapProductPrice::select('price')
                                    ->where('product_id', $orderDetail->product_id)
                                    ->where('user_id', $orderDetail->user_id)
                                    ->first();

                    // Update product quantity and amount in OrderDetail
                    $orderDetail->product_quantity = $detailData->quantity;
                    $orderDetail->amount = $detailData->quantity * $product_price->price;
                    $orderDetail->save();
                    // dd($orderDetail);
                    // Add the amount of this product to the total order amount
                    $totalAmount += $orderDetail->amount;
                }
            }

            // Handle new products if any
            if ($request->has('new_products')) {
                $newProductIds = json_decode($request->new_products)->product_id; // Get all product IDs
                $newQuantities = json_decode($request->new_products)->quantity; // Get corresponding quantities
                foreach ($newProductIds as $index => $productId) {
                    $quantity = $newQuantities[$index]; // Get the corresponding quantity for this product

                    $product = MapProductPrice::select('map_product_prices.price', 'products.*')
                        ->join('products', 'map_product_prices.product_id', '=', 'products.id')
                        ->where('map_product_prices.user_id', $order->user_id)
                        ->where('products.status', '=', 1)
                        ->where('products.id', '=', $productId)
                        ->first(); // Get the single product
                    if ($product) {
                        $orderDetail = new OrderDetail();
                        $orderDetail->order_id = $order->id;
                        $orderDetail->user_id = $order->user_id;
                        $orderDetail->product_id = $product->id;
                        $orderDetail->order_no = $order->order_no;
                        $orderDetail->product_no = $product->product_no;
                        $orderDetail->product_name = $product->product_name;
                        $orderDetail->company_name = $product->company_name;
                        $orderDetail->product_image = $product->product_image;
                        $orderDetail->product_description = $product->product_description;
                        $orderDetail->product_weight = $product->product_quantity;
                        $orderDetail->product_quantity = $quantity; // Use the correct quantity
                        $orderDetail->item_per_cred = $product->item_per_cred;
                        $orderDetail->amount = $quantity * $product->price; // Calculate amount based on quantity
                        $orderDetail->save();

                        // Add the amount of this new product to the total order amount
                        $totalAmount += $orderDetail->amount;
                    }
                }
            }
            // Update the total amount in the Order table
            $order->total_amount = $totalAmount;
            $order->save();
            return response()->json([
                'status' => 'success',
                'order' => $order,
                'message' => 'Order products updated successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

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
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function productListingForDistributor(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $products = MapProductPrice::select('products.*','map_product_prices.price')->join('products','map_product_prices.product_id','=','products.id')
            ->where('user_id',$request->distributor_id)->where('products.status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'products' => $products,
                'message' => 'Product listing retrieved successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function addToCart(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $cart = ShoppingCart::where('user_id',$request->distributor_id)->where('product_id',$request->product_id)->first();
            if($cart)
            {
                $cart->quantity += $request->quantity;
                $cart->save();
            }
            else {
                $cart = new ShoppingCart();
                $cart->user_id = $request->distributor_id;
                $cart->product_id = $request->product_id;
                $cart->quantity = $request->quantity;
                $cart->save();
            }
            return response()->json([
                'status' => 'success',
                'cart' => $cart,
                'message' => 'Product added to cart successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function cartListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $cart_data=ShoppingCart::select('products.*','map_product_prices.price','shopping_carts.quantity','shopping_carts.id as shopping_cart_id')
            ->join('products','shopping_carts.product_id','=','products.id')
            ->join('map_product_prices','products.id','=','map_product_prices.product_id')
            ->where('map_product_prices.user_id','=',$request->distributor_id)->where('shopping_carts.user_id','=',$request->distributor_id)
            ->where('map_product_prices.status',1)->where('products.status',1)
            ->get();
            return response()->json([
                'status' => 'success',
                'cartProducts' => $cart_data,
                'message' => 'Cart listing retrieved successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
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
            // if ($existingOrder) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'You have already placed an order today. Please try again tomorrow.'
            //     ], 400); // HTTP status code 400 Bad Request
            // }

            // Check if the current time is within the allowed time range
            // if ($currentTime->lt($startTime) || $currentTime->gt($endTime)) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Orders can only be placed between 6:00 AM to 7:00 PM.'
            //     ], 400); // HTTP status code 400 Bad Request
            // }

            $total_order = ShoppingCart::select('shopping_carts.quantity', 'products.*', 'map_product_prices.price')
                ->join('products', 'shopping_carts.product_id', '=', 'products.id')
                ->join('map_product_prices', 'shopping_carts.product_id', 'map_product_prices.product_id')
                ->where('shopping_carts.user_id', $request->distributor_id)
                ->where('map_product_prices.user_id', $request->distributor_id)
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
                $order->user_id = $request->distributor_id;
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
                $bill->user_id = $request->distributor_id;
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
                    $orderDetails->user_id = $request->distributor_id;
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
