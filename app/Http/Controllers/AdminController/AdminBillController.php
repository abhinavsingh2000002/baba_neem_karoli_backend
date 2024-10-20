<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderDetail;
use DB;
use PDF;

class AdminBillController extends Controller
{
    public function index()
    {
        return view('Backend.bills.bills_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Bill Number',
            2 => 'Order Number',
            3 => 'Distrubutor Name',
            4 => 'Distributor Image',
            5 => 'Total Amount',
            7 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                    OR orders.order_no LIKE '%$searchValue%'
                    OR bills.bill_no LIKE '%$searchValue%'
                      OR orders.total_amount LIKE '%$searchValue%')";
        }

        $sql = "SELECT bills.*,users.name,users.image_path,orders.total_amount FROM bills,users,orders WHERE bills.user_id=users.id AND bills.order_id=orders.id AND users.status=1 $filter $where";
        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY bills.id DESC LIMIT " . $params['start'] . ", " . $params['length'];

        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        // dd($result);
        $sno = $params['start'] + 1;
        $data = [];
       foreach ($result as $key => $obj) {
            $id = $obj->id;
            $Sno = $key + 1;
            $billNo = $obj->bill_no;
            $orderNo = $obj->order_no;
            $distributorName = $obj->name;
            $distributorImage = $obj->image_path;
            $totalAmount = $obj->total_amount;


            if (!empty($distributorImage) && file_exists(public_path($distributorImage))) {
                $imageUrl = asset($distributorImage); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

            $actionButtons = '
                <a class="btn btn-sm btn-info" href="' . route('admin_bills.billDetail', $id) . '" title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
            <a class="btn btn-sm btn-warning" href="' . route('admin_bills.invoicePdf', $obj->order_id) . '" title="Download">
                <i class="fas fa-download"></i>
            </a>';

            $data[] = array(
                $Sno,
                $billNo,
                $orderNo,
                $distributorName,
                $imageHtml,
                $totalAmount,
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

    public function BillDetail($id)
    {
        $bill=Bill::findOrFail($id);
        if($bill)
        {
            $id=$bill->order_id;
            $order=Order::select('orders.*','bills.bill_no','users.name','users.address')->join('bills','orders.id','=','bills.order_id')
            ->join('users','orders.user_id','=','users.id')->where('orders.id',$id)->first();
            if($order){
                $orderDetail=OrderDetail::join('bills','order_details.order_id','bills.order_id')
                ->join('orders','order_details.order_id','=','orders.id')
                ->where('order_details.order_id',$id)->get();
            }
        }
        // dd($order);
        return view('Backend.bills.bills_detail')->with(['orderDetail'=>$orderDetail,'order'=>$order]);
    }

    public function invoicePdf($id)
    {
        $order=Order::select('orders.*','bills.bill_no','users.name','users.address')->join('bills','orders.id','=','bills.order_id')
        ->join('users','orders.user_id','=','users.id')->where('orders.id',$id)->first();
        $orderDetail = OrderDetail::join('bills', 'order_details.order_id', 'bills.order_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.order_id', $id)
            ->get();
        // Generate PDF
        $pdf = PDF::loadView('Backend.bills.invoice_pdf', [
            'order' => $order,
            'orderDetail' => $orderDetail
        ]);

        // Download the PDF file
        return $pdf->download('invoice_' . $order->bill_no . '.pdf');
    }
}
