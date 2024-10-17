<?php

namespace App\Http\Controllers\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DriverTask;
use App\Models\Order;
use DB;
use Carbon\Carbon;

class DriverTaskController extends Controller
{
    public function index()
    {
        return view('Driver.Task.driver_task_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Order Numer',
            2 => 'Distributor Name',
            3 => 'Distributor Image',
            4 => 'Task Alloted Date&time',
            5 => 'Task Completed Date&time',
            6 => 'Status',
            7 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                        OR driver_tasks.status LIKE '%$searchValue%'
                    OR driver_tasks.order_no LIKE '%$searchValue%')";
        }

            $sql = "SELECT driver_tasks.*,users.name,users.image_path
            FROM driver_tasks
            JOIN users ON driver_tasks.user_id = users.id
            WHERE driver_tasks.driver_id = " . Auth::user()->id . "
            AND users.status = 1 $filter $where";


        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY driver_tasks.id DESC LIMIT " . $params['start'] . ", " . $params['length'];
        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $key=> $obj) {
            $id = $obj->id;
            $sno=$key+1;
            $orderNo=$obj->order_no;
            $distributorName=$obj->name;
            $distributorImage=$obj->image_path;
            $actionButtons ='';
            if ($obj->status == 0) {
                $status = '<span style="color:orange;">Pending</span>';
                $actionButtons .= '
                 <a class="btn btn-sm btn-info" href="' . route('driver_task.detailListing', $id) . '" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a class="btn btn-sm btn-success" href="javascript:void(0);"
                 title="Approve"
                 onclick="toggleTaskApproval(' . $id . ')">
                 <i class="fas fa-check"></i>
                 </a>';
            } else {
                $status = '<span style="color:green;">Approved</span>';
            }
            $taskAllotedDatetime=$obj->task_alloted_date .' '.$obj->task_alloted_time;
            $taskCompletedDatetime=$obj->task_completed_date .' '.$obj->task_completed_time;


            if (!empty($distributorImage) && file_exists(public_path($distributorImage))) {
                $imageUrlDistributor = asset($distributorImage); // Path to user's image
            } else {
                $imageUrlDistributor = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtmlDistributor = '<img src="' . $imageUrlDistributor . '" alt="User Image" width="70" height="70" />';


            $data[] = array(
                $sno,
                $orderNo,
                $distributorName,
                $imageHtmlDistributor,
                $taskAllotedDatetime,
                $taskCompletedDatetime,
                $status,
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

    public function approve(Request $request)
    {
        // dd($request);
        $driver_task=DriverTask::findOrFail($request->task_id);
        // dd($driver_task);
        if($driver_task)
        {
            $driver_task->status=1;
            $driver_task->save();
            $order=Order::findOrFail($driver_task->order_id);
            $order->order_status=2;
            $order->order_deliverd_date=Carbon::now()->toDateString();
            $order->order_deliverd_time=Carbon::now()->toTImeString();
            $order->save();

            return response()->json(['success'=>$order]);
        }
    }

    public function detailListing(Request $request,$id)
    {
        $all_orders=DriverTask::join('order_details','driver_tasks.order_id','=','order_details.order_id')->where('driver_tasks.id',$id)->get();
        return view('Driver.Task.driver_task_detail_listing')->with(['driver_task'=>$all_orders]);
    }

    // public function detailListing($id)
    // {

        // dd($all_orders);
    // }
}
