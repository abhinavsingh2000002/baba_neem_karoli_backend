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

    // public function updateOrderProducts(Request $request)
    // {
    //     $user = $this->validate_user($request->connection_id, $request->auth_code);
    //     if(!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'User not authenticated',
    //         ], 401);
    //     }

    //     try {
    //         \DB::beginTransaction();
    //         $order = Order::findOrFail($request->order_id);
    //         $totalAmount = 0;

    //         // Handle Delete Products
    //         if ($request->has('products_to_delete') && !empty($request->products_to_delete)) {
    //             $productsToDelete = OrderDetail::where('order_id', $request->order_id)
    //                 ->whereIn('id', $request->products_to_delete)
    //                 ->get();
                
    //             if (count($productsToDelete) !== count($request->products_to_delete)) {
    //                 throw new \Exception('Invalid product IDs in deletion request');
    //             }
                
    //             OrderDetail::whereIn('id', $request->products_to_delete)->delete();
    //         }

    //         // Handle Update Existing Products
    //         if ($request->has('products_to_update') && !empty($request->products_to_update)) {
    //             foreach ($request->products_to_update as $product) {
    //                 $orderDetail = OrderDetail::find($product['order_detail_id']);
    //                 if ($orderDetail) {
    //                     $productPrice = MapProductPrice::select('map_product_prices.price', 'products.*')
    //                         ->join('products', 'map_product_prices.product_id', '=', 'products.id')
    //                         ->where('map_product_prices.user_id', $order->user_id)
    //                         ->where('products.id', $orderDetail->product_id)
    //                         ->first();

    //                     $orderDetail->quantity = $product['quantity'];
    //                     $orderDetail->price = $productPrice->price;
    //                     $orderDetail->total = $product['quantity'] * $productPrice->price;
    //                     $orderDetail->save();
    //                 }
    //             }
    //         }

    //         // Handle Add New Products
    //         if ($request->has('products_to_add') && !empty($request->products_to_add)) {
    //             foreach ($request->products_to_add as $product) {
    //                 $productPrice = MapProductPrice::select('map_product_prices.price', 'products.*')
    //                     ->join('products', 'map_product_prices.product_id', '=', 'products.id')
    //                     ->where('map_product_prices.user_id', $order->user_id)
    //                     ->where('products.status', '=', 1)
    //                     ->where('products.id', $product['product_id'])
    //                     ->first();

    //                 if ($productPrice) {
    //                     $orderDetail = new OrderDetail();
    //                     $orderDetail->order_id = $request->order_id;
    //                     $orderDetail->user_id = $order->user_id;
    //                     $orderDetail->product_id = $product['product_id'];
    //                     $orderDetail->order_no = $order->order_no;
    //                     $orderDetail->product_no = $productPrice->product_no;
    //                     $orderDetail->product_name = $productPrice->product_name;
    //                     $orderDetail->company_name = $productPrice->company_name;
    //                     $orderDetail->product_image = $productPrice->product_image;
    //                     $orderDetail->product_description = $productPrice->product_description;
    //                     $orderDetail->product_weight = $productPrice->product_quantity;
    //                     $orderDetail->quantity = $product['quantity'];
    //                     $orderDetail->price = $productPrice->price;
    //                     $orderDetail->total = $product['quantity'] * $productPrice->price;
    //                     $orderDetail->item_per_cred = $productPrice->item_per_cred;
    //                     $orderDetail->save();
    //                 }
    //             }
    //         }

    //         // Recalculate total amount
    //         $totalAmount = OrderDetail::where('order_id', $request->order_id)->sum('total');
    //         $order->total_amount = $totalAmount;
    //         $order->save();

    //         \DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => [
    //                 'order' => $order,
    //                 'order_details' => OrderDetail::where('order_id', $request->order_id)->get()
    //             ],
    //             'message' => 'Order products updated successfully',
    //         ]);

    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to update order products: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


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
}
