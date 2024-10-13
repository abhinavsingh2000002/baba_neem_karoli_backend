<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\MapProductPrice;
use DB;

class MapProductController extends Controller
{
    public function index()
    {
        return view('Backend.Map Product Price.map_product_price_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1=>  'product_no',
            2 => 'Product Name',
            3 => 'Distributor Name',
            4 => 'Distrubutor Image',
            5 => 'Price',
            6 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                    OR products.product_name LIKE '%$searchValue%'
                    OR map_product_prices.price LIKE '%$searchValue%'
                     OR products.product_no LIKE '%$searchValue%')";
        }

        $sql = "SELECT * FROM map_product_prices,users,products WHERE map_product_prices.user_id=users.id AND map_product_prices.product_id=products.id $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY users.id DESC LIMIT " . $params['start'] . ", " . $params['length'];

        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $obj) {
            $id = $obj->id;
            $actionButtons ='
            <a class="btn btn-sm btn-warning" href="' . route('map_product_price.edit', $id) . '" title="Edit">
                <i class="fas fa-edit"></i>
            </a>';

            if (!empty($obj->image_path) && file_exists(public_path($obj->image_path))) {
                $imageUrl = asset($obj->image_path); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

            $data[] = array(
                $sno,
                $obj->product_no,
                $obj->product_name,
                $obj->name,
                $imageHtml,
                $obj->price,
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
        if ($request->isMethod('post')) {

            $request->validate([
                'distributor' => 'required|exists:users,id',
                'product' => 'required|exists:products,id',
                'price' => 'required|numeric|min:0',
            ]);

            $exists = MapProductPrice::where('user_id', $request->distributor)
                ->where('product_id', $request->product)->where('status',1)
                ->exists();

            if ($exists) {
                session()->flash('delete', 'This distributor has already mapped prices for this product.');
                return redirect()->back();
            }

            $map_price = new MapProductPrice();
            $map_price->user_id = $request->distributor;
            $map_price->product_id = $request->product;
            $map_price->price = $request->price;
            $map_price->save();

            session()->flash('success', 'Price Mapping Successfully');
            return redirect()->route('map_product_price.index');
        }

        $distributors = User::where('role_id', '=', 2)->where('status',1)->get();
        $products = Product::where('status',1)->get();

        return view('Backend.Map Product Price.map_product_price_add')->with([
            'distributors' => $distributors,
            'products' => $products,
        ]);
    }

    public function edit($id)
    {
        $distributors = User::where('role_id', '=', 2)->where('status',1)->get();
        $products = Product::where('status',1)->get();
        $map_product_price=MapProductPrice::findOrFail($id);
        return view('Backend.Map Product Price.map_product_price_edit')->with([
            'distributors'=>$distributors,
            'products'=>$products,
            'map_product_price'=>$map_product_price,
        ]);
    }

    public function update(Request $request,$id)
    {
        if($request->isMethod('post')){
            $map_product_price = MapProductPrice::find($id);

            if($map_product_price) {
                $map_product_price->user_id = $request->distributor;
                $map_product_price->product_id = $request->product;
                $map_product_price->price = $request->price;
                $map_product_price->save();

                session()->flash('success', 'Price Mapping Updated Successfully');
            } else {
                session()->flash('delete', 'Price Mapping not found');
            }
            return redirect()->route('map_product_price.index');
        }

    }

}
