<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use DB;
use PDF;
use Carbon\Carbon;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Distributor\DistributorLedgerExport;
use Illuminate\Support\Facades\Auth;

class DistributorLedgerController extends Controller
{
    public function index()
    {
        return view('Distributor.Ledger.ledger_listing');
    }

    public function listing(Request $request)
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Distributor Name',
            2 => 'Distributor Image',
            3 => 'Driver Name',
            4 => 'Driver Image',
            5 => 'Cred Date & Time',
            6 => 'Cred In',
            7 => 'Cred Out',
            8 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (bills.bill_no LIKE '%$searchValue%'
                        OR bills.order_no LIKE '%$searchValue%'
                        OR bills.bill_date LIKE '%$searchValue%'
                        OR bills.bill_time LIKE '%$searchValue%'
                        OR users.name LIKE '%$searchValue%'
                         OR orders.order_status LIKE '%$searchValue%'
                        OR orders.total_amount LIKE '%$searchValue%')";
        }
        if ($request->currentMonthYear){
            // Split the 'YYYY-MM' format into year and month
            $explodedDate = explode('-', $request->currentMonthYear);
            // Extract year and month correctly
            $year = intval($explodedDate[0]); // First part is year
            $month = intval($explodedDate[1]); // Second part is month
            // Build the filter for SQL query
            $filter = "AND MONTH(bill_date) = $month AND YEAR(bill_date) = $year";
        }
        if ($request->distributorName && $request->currentMonthYear){
             // Split the 'YYYY-MM' format into year and month
             $explodedDate = explode('-', $request->currentMonthYear);
             // Extract year and month correctly
             $year = intval($explodedDate[0]); // First part is year
             $month = intval($explodedDate[1]); // Second part is month
             $distributorName=intval($request->distributorName);
             // Build the filter for SQL query
            $filter = "AND MONTH(bill_date) = $month AND YEAR(bill_date) = $year AND users.id=$distributorName";
        }
        $User_id=Auth()->user()->id;
        $sql = "SELECT bills.*,users.*,orders.* FROM bills,users,orders
        WHERE bills.user_id=users.id AND bills.order_id=orders.id
        AND users.status=1 AND bills.user_id=".$User_id." $filter $where";
        // dd($sql);
        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY bills.id DESC LIMIT " . $params['start'] . ", " . $params['length'];
        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $key => $obj) {
            $id = $obj->id;
            $sno = $key + 1;
            $billNo = $obj->bill_no;
            $orderNo = $obj->order_no;
            $billDateTime = Carbon::parse($obj->bill_date)->format('d-m-Y') . ' ' . $obj->bill_time; // Assuming bill_time is a separate property
            $distributorName = $obj->name;
            $totalAmount = $obj->total_amount;

            switch ($obj->order_status) {
                case 0:
                    $orderStatus = 'Failed';
                    break;
                case 1:
                    $orderStatus = 'Pending';
                    break;
                case 2:
                    $orderStatus = 'Confirmed';
                    break;
                case 3:
                    $orderStatus = 'Delivered';
                    break;
                default:
                    $orderStatus = 'Unknown Status';
                    break;
            }

            $data[] = array(
                $sno,
                $billNo,
                $orderNo,
                $billDateTime,
                $distributorName,
                $totalAmount,
                $orderStatus,
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

    public function ledgerpdf(Request $request)
    {

            $bills = Bill::select('bills.*', 'users.name', 'orders.total_amount', 'orders.order_status')
            ->join('orders', 'bills.order_id', '=', 'orders.id')
            ->join('users', 'bills.user_id', '=', 'users.id')
            ->where('users.status', '=', 1)->where('orders.order_status','!=',0)
            ->where('users.id','=',Auth::user()->id)
            ->whereMonth('bills.bill_date', '=', $request->month)
            ->whereYear('bills.bill_date', '=', $request->year)
            ->get();
        if(count($bills)>0){
            $data['bills']=$bills;
            $pdf = PDF::loadView('Distributor.Ledger.ledger_pdf', $data)
              ->setOption('defaultFont', 'DejaVu Sans')
              ->setOption('isHtml5ParserEnabled', true)
              ->setOption('isRemoteEnabled', true);
            return $pdf->download('Ledger_' . $request->month . '_'. $request->year.'.pdf');
        }
        else{
            return response()->json(['no_data'=>0]);
        }
    }

    public function ledgerexcel(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        if ($request->has('export')) {
            return $this->exportToExcel($month, $year);
        }
    }

    protected function exportToExcel($month, $year)
    {
        return Excel::download(new DistributorLedgerExport($month, $year), 'Ledger_'. $month . '_' . $year . '.xls');
    }
}
