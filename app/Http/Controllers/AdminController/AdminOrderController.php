<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;

class AdminOrderController extends Controller
{
    public function index()
    {
        return view('Backend.Order.order_listing');
    }

    public function listing(Request $request)
    {

        $columns = array(
            0 => 'SNo',
            1 => 'Order Number',
            2 => 'Order Date&TIme',
            3 => 'Order Confirm Date&TIme',
            4 => 'Order Delivered Date&TIme',
            5 => 'Order Failed Date&TIme',
            6 => 'Distributor Name',
            7 => 'Distributor Profile',
            8 => 'Total Amount',
            9 => 'Order Status',
            10 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';


        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                OR orders.order_no LIKE '%$searchValue%'
                OR orders.order_date LIKE '%$searchValue%'
                OR orders.order_time LIKE '%$searchValue%'
                OR orders.order_confirm_date LIKE '%$searchValue%'
                OR orders.order_confirm_time LIKE '%$searchValue%'
                OR orders.order_deliverd_date LIKE '%$searchValue%'
                OR orders.order_deliverd_time LIKE '%$searchValue%'
                OR orders.order_failed_date LIKE '%$searchValue%'
                OR orders.order_failed_time LIKE '%$searchValue%'
                OR orders.total_amount LIKE '%$searchValue%'
                OR orders.order_status LIKE '%$searchValue%')";

        }
        if (!empty($request->date)) {
            $filter .= " AND DATE(orders.order_date) = '" . date('Y-m-d', strtotime($request->date)) . "'";
        }
        $sql = "SELECT orders.*,users.name, users.image_path FROM orders,users WHERE orders.user_id=users.id AND users.status=1 $filter $where";
        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY orders.id DESC LIMIT " . $params['start'] . ", " . $params['length'];

        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        $statusLabels = [
            0 => 'Failed',
            1 => 'Pending',
            2 => 'Confirmed',
            3 => 'Delivered'
        ];

       foreach ($result as $key => $obj) {
            $id = $obj->id;
            $Sno = $key + 1;
            $orderNo = $obj->order_no;
            $distributorName = $obj->name;
            $distributorImage = $obj->image_path;
            $orderDateTime = $obj->order_date . ' ' . $obj->order_time;
            $orderConfirmDateTime = $obj->order_confirm_date . ' ' . $obj->order_confirm_time;
            $orderDeliveredDateTime = $obj->order_deliverd_date . ' ' . $obj->order_deliverd_time;
            $orderFailedDateTime = $obj->order_failed_date . ' ' . $obj->order_failed_time;
            $totalAmount = $obj->total_amount;
            $orderStatus=$obj->order_status;
            $orderStatus = isset($statusLabels[$orderStatus]) ? $statusLabels[$orderStatus] : 'Unknown';
            $orderStatusChange='';
            $actionButtons='';
            if($obj->order_status=='1'){
                $orderStatusChange .= '
                <a class="btn btn-sm btn-success order-status-change ml-3" data-order_status="2" data-id="'.$id.'" title="Approve Order"><i class="fas fa-check"></i></a>
                <a class="btn btn-sm btn-danger order-status-change" data-order_status="0" data-id="'.$id.'" title="Reject Order">
                    <i class="fas fa-times"></i>
                </a>';
            }

            if (!empty($distributorImage) && file_exists(public_path($distributorImage))) {
                $imageUrl = asset($distributorImage); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

            $actionButtons .= '
                <a class="btn btn-sm btn-info ml-2" href="' . route('admin_order.detailListing', $id) . '" title="View Details">
                    <i class="fas fa-eye"></i>
                </a>';
                if($obj->order_status=='1'){
                    $actionButtons .=
                          '<a class="btn btn-sm btn-warning" href="' . route('admin_order.detailListing', $id) . '" title="Edit Order">
                        <i class="fas fa-edit"></i>
                    </a>';
                }

            $data[] = array(
                $Sno,
                $orderNo,
                $distributorName,
                $imageHtml,
                $orderDateTime,
                $orderConfirmDateTime,
                $orderDeliveredDateTime,
                $orderFailedDateTime,
                $totalAmount,
                $orderStatus,
                $orderStatusChange,
                $actionButtons,
            );
        }


        $json_data = array(
            "draw" => intval($params['draw']),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data" => $data
        );
        return response()->json($json_data);
    }

    public function updateStatus(Request $request)
    {
        // dd($request);
        $order_update_status=Order::findOrFail($request->order_id);
        $order_update_status->order_status=$request->order_status;
        if($request->order_status=='0'){
            $order_update_status->order_failed_date=Carbon::now()->toDateString();
            $order_update_status->order_failed_time=Carbon::now()->toTimeString();
        }
        else if($request->order_status=='1'){
            $order_update_status->order_date=Carbon::now()->toDateString();
            $order_update_status->order_time=Carbon::now()->toTimeString();
        }
        else if($request->order_status=='2'){
            $order_update_status->order_confirm_date=Carbon::now()->toDateString();
            $order_update_status->order_confirm_time=Carbon::now()->toTimeString();
        }
        else if($request->order_status='3'){
            $order_update_status->order_deliverd_date=Carbon::now()->toDateString();
            $order_update_status->order_deliverd_time=Carbon::now()->toTimeString();
        }
        $order_update_status->save();
        return response()->json(['order_update_status'=>$order_update_status]);
    }

    public function detailListing($id)
    {
        $order=Order::select('users.name','users.address','orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$id)
        ->join('users','orders.user_id','=','users.id')->first();
        if($order){
            $orderDetail=OrderDetail::join('bills','order_details.order_id','bills.order_id')
            ->join('orders','order_details.order_id','=','orders.id')
            ->where('order_details.order_id',$id)->get();
        }
            return view('Backend.Order.oder_detail_listing')->with([
                'order'=>$order,
                'orderDetail'=>$orderDetail
            ]);
    }
}
