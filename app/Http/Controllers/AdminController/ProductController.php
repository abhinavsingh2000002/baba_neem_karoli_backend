<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('Backend.Product.product_listing');
    }

    public function listing(Request $request)
    {
        $columns = array(
            0 => 'S No',
            1 => 'product_no',
            2 => 'product_name',
            3 => 'company_name',
            4 => 'product_quantity',
            5 => 'product_image',
            6 => 'status',
            7 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " WHERE (products.product_no LIKE '%$searchValue%'
                    OR products.product_name LIKE '%$searchValue%'
                    OR products.company_name LIKE '%$searchValue%'
                     OR products.product_quantity LIKE '%$searchValue%'
                      OR products.status LIKE '%$searchValue%')";
        }

        $sql = "SELECT * FROM products $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY products.id DESC LIMIT " . $params['start'] . ", " . $params['length'];

        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $obj) {
            $id = $obj->id;
            $actionButtons='';
        if ($obj->status == 1) {
            $actionButtons .= '
            <a class="btn btn-sm btn-info" href="' . route('product.view', $id) . '" title="View">
                <i class="fas fa-eye"></i>
            </a>
            <a class="btn btn-sm btn-warning" href="' . route('product.edit', $id) . '" title="Edit">
                <i class="fas fa-edit"></i>
            </a>';
            $actionButtons .= '
            <a class="btn btn-sm btn-danger" href="' . route('product.delete', $id) . '" title="Delete" onclick="return confirm(\'Are you sure you want to delete this item?\');">
                <i class="fas fa-trash-alt"></i>
            </a>';
        } else {
            $actionButtons .= '
            <a class="btn btn-sm btn-success" href="' . route('product.delete', $id) . '" title="Restore" onclick="return confirm(\'Are you sure you want to restore this item?\');">
                <i class="fas fa-undo-alt"></i>
            </a>';
        }

            // Check if the image path is available, otherwise use a default avatar image
            if (!empty($obj->image_path) && file_exists(public_path($obj->image_path))) {
                $imageUrl = asset($obj->image_path); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

            $data[] = array(
                  'DT_RowClass' => $obj->status == 0 ? 'table-danger' : '',
                $sno,
                $obj->product_no,
                $obj->product_name,
                $obj->company_name,
                $imageHtml,
                $obj->product_quantity,
                $obj->status == 1 ? 'Active' : 'Inactive',
                $actionButtons,
            );
        $sno++;
        }

        $json_data = array(
            "draw" => intval($params['draw']),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data" => $data
        );
        return response()->json($json_data);
    }

    public function add(Request $request)
    {
        if($request->all())
        {
            $rules=[
                'product_name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'product_description' => 'required|string',
                'product_quantity' => 'required|string',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:51200' // Validation for images
            ];
            $validator = Validator::make($request->all(), $rules);
            if (!$validator->fails()) {
                $product = new Product();
                $product->product_no = rand(0, 99999);
                $product->product_name = $request->product_name;
                $product->company_name = $request->company_name;
                $product->product_description = $request->product_description;
                $product->item_per_cred=$request->no_of_item;
                $product->product_quantity = $request->product_quantity;

                $imagePaths = [];

                if ($request->hasfile('images')) {
                    foreach ($request->file('images') as $image) {
                        $filename = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('products', $filename, 'public');
                        $imagePaths[] = $filename;
                    }
                }
                $product->product_image = implode('|', $imagePaths);
                $product->save();
                return response()->json(['success' => true, 'success' => 'Product created successfully.']);

            }
            else {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
        }
        return view('Backend.Product.product_add');
    }

    public function view($id)
    {
        $product=Product::findOrFail($id);
        $imagesArray = explode('|', $product->product_image);
        return view('Backend.Product.product_view')->with(['product'=>$product,'imagesArray'=>$imagesArray]);
    }

    public function edit($id)
    {
        $product=Product::findOrFail($id);
        $imagesArray = explode('|', $product->product_image);
        return view('Backend.Product.product_edit')->with(['product'=>$product,'imagesArray'=>$imagesArray]);
    }

    public function update(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->product_name = $request->product_name;
        $product->company_name = $request->company_name;
        $product->product_description = $request->product_description;
        $product->item_per_cred=$request->no_of_item;
        $product->product_quantity = $request->product_quantity;

        if ($request->hasfile('images')) {
            $imagePaths = [];

            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('products', $filename, 'public');
                $imagePaths[] = $filename;
            }
            $product->product_image = implode('|', $imagePaths);
        }
        $product->save();

        return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        if($product->status==1){
            $product->status=0;
            $product->save();
            session()->flash('delete', 'Product Deleted Successfully');
        }
        else{
            $product->status=1;
            $product->save();
            session()->flash('success', 'Product Restored Successfully');
        }
        return redirect()->route('product.index');
    }


}
