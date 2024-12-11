<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
class AdminProductController extends Controller
{
    use ValidationTrait;

    public function productListing(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $query = Product::query();

            // Add search filters
            if($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('product_name', 'LIKE', "%{$search}%")
                      ->orWhere('company_name', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->orderBy(\DB::raw('ISNULL(products.display_order), products.display_order'), 'asc')->get();

            return response()->json([
                'status' => 'success',
                'products' => $products,
                'message' => 'Products retrieved successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function addProduct(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'product_quantity' => 'required|string',
                'item_per_cred' => 'required|numeric',
                'product_description' => 'nullable|string',
                'product_price' => 'required|numeric',
                'display_order' => 'nullable|numeric|unique:products,display_order',
                'product_image' => 'required|image|mimes:jpeg,png,JPEG,PNG|max:10240',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $product = new Product();
            do {
                $productNo = mt_rand(1000000000, 9999999999);
            } while (Product::where('product_no', $productNo)->exists());
            $product->product_no = $productNo;
            $product->product_name = $request->product_name;
            $product->company_name = $request->company_name;
            $product->product_quantity = $request->product_quantity;
            $product->item_per_cred = $request->item_per_cred;
            $product->product_description = $request->product_description;
            $product->product_price = $request->product_price;
            $product->display_order = $request->display_order;
            if($request->hasFile('product_image')){
                $image = $request->file('product_image');
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $filename = $request->product_name . '_' . $request->product_quantity . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $filename);
                $product->product_image = 'uploads/products/' . $filename;
            }
            $product->save();

            return response()->json([
                'status' => 'success',
                'product' => $product,
                'message' => 'Product added successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function updateProduct(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'product_name' => 'sometimes|string|max:255',
                'company_name' => 'sometimes|string|max:255',
                'product_quantity' => 'sometimes|string',
                'item_per_cred' => 'sometimes|numeric',
                'product_description' => 'nullable|string',
                'product_price' => 'sometimes|numeric',
                'display_order' => 'nullable|numeric|unique:products,display_order,' . $request->product_id,
                'product_image' => 'nullable|image|mimes:jpeg,png,JPEG,PNG|max:10240',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $product = Product::find($request->product_id);

            if ($request->has('product_name')) {
                $product->product_name = $request->product_name;
            }
            if ($request->has('company_name')) {
                $product->company_name = $request->company_name;
            }
            if ($request->has('product_quantity')) {
                $product->product_quantity = $request->product_quantity;
            }
            if ($request->has('item_per_cred')) {
                $product->item_per_cred = $request->item_per_cred;
            }
            if ($request->has('product_description')) {
                $product->product_description = $request->product_description;
            }
            if ($request->has('product_price')) {
                $product->product_price = $request->product_price;
            }
            if ($request->has('display_order')) {
                $product->display_order = $request->display_order;
            }

            if ($request->hasFile('product_image')) {
                // Delete the old image if it exists
                if ($product->product_image && file_exists(public_path($product->product_image))) {
                    unlink(public_path($product->product_image));
                }

                $image = $request->file('product_image');
                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $filename = $request->product_name . '_' . $request->product_quantity . '_' . $currentDateTime . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $filename);
                $product->product_image = 'uploads/products/' . $filename;
            }

            $product->save();

            return response()->json([
                'status' => 'success',
                'product' => $product,
                'message' => 'Product updated successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function deleteOrRestoreProduct(Request $request){
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user){
            $product = Product::find($request->product_id);
            if($product->status == 1){
                $product->status = 0;
                $message = 'Product deleted successfully';
            }
            else {
                $product->status = 1;
                $message = 'Product restored successfully';
            }
            $product->save();

            return response()->json([
                'status' => 'success',
                'product' => $product,
                'message' => $message,
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

}
