<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Order;
use App\Models\User;
use App\Models\DriverTask;
use Carbon\Carbon;

class AdminDriverTaskController extends Controller
{
    public function index()
    {
        return view('Backend.Driver Task.driver_task_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Order Numer',
            2 => 'Driver Name',
            3 => 'Driver Image',
            4 => 'Distributor Name',
            5 => 'Distributor Image',
            6 => 'Status',
            7 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (driver.name LIKE '%$searchValue%'
                        OR distributor.name LIKE '%$searchValue%'
                    OR driver_tasks.order_no LIKE '%$searchValue%')";
        }

            $sql = "SELECT driver_tasks.*,
            driver.name as driver_name,
            driver.image_path as driver_image,
            distributor.name as distributor_name,
            distributor.image_path as distributor_image
            FROM driver_tasks
            LEFT JOIN users as driver ON driver_tasks.driver_id = driver.id
            LEFT JOIN users as distributor ON driver_tasks.user_id = distributor.id
            WHERE driver.status = 1 AND distributor.status = 1 $filter $where";

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
            $driverName=$obj->driver_name;
            $driverImage=$obj->driver_image;
            $distributorName=$obj->distributor_name;
            $distributorImage=$obj->distributor_image;
            if ($obj->status == 0) {
                $status = '<span style="color:black;">Pending</span>';
            } else {
                $status = '<span style="color:green;">Approved</span>';
            }
            $actionButtons = '
            <a class="btn btn-sm btn-warning" href="' . route('admin_driver_task.edit', $id) . '" title="Edit">
                <i class="fas fa-edit"></i>
            </a>';

            // Check if the image path is available, otherwise use a default avatar image
            if (!empty($driverImage) && file_exists(public_path($driverImage))) {
                $imageUrl = asset($driverImage); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

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
                $driverName,
                $imageHtml,
                $distributorName,
                $imageHtmlDistributor,
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

    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {
            $order=Order::findOrFail($request->order);
            $existingTask = DriverTask::where('order_id', $order->id)->first();
            if($existingTask){
                session()->flash('error', 'The order has already been allotted to another driver.');
                return redirect()->back();
            }
            $driver_task=new DriverTask();
            $driver_task->order_id=$order->id;
            $driver_task->order_no=$order->order_no;
            $driver_task->user_id=$order->user_id;
            $driver_task->driver_id=$request->driver;
            $driver_task->task_alloted_date=carbon::now()->toDateString();
            $driver_task->task_alloted_time=carbon::now()->toTimeString();
            $driver_task->save();

            session()->flash('success','Task Alloted to Driver Successfull');
            return redirect()->route('admin_driver_task.index');
        }
        $all_order=Order::where('order_status',2)->get();
        $all_user=user::where('role_id',3)->where('status',1)->get();
        return view('Backend.Driver Task.driver_task_add')->with([
            'all_user'=>$all_user,
            'all_order'=>$all_order,
         ]);
    }


    public function edit($id)
    {
        $driver_task=DriverTask::findOrFail($id);
        $all_order=Order::where('order_status',2)->get();
        $all_user=user::where('role_id',3)->where('status',1)->get();
        return view('Backend.Driver Task.driver_task_edit')->with([
            'all_user'=>$all_user,
            'all_order'=>$all_order,
            'driver_task'=>$driver_task,
         ]);
    }

    public function update(Request $request,$id)
    {
        $driver_task=DriverTask::findOrFail($id);
        if($driver_task->status==0)
        {
            $driver_task->order_id=$request->order;
            $driver_task->driver_id=$request->driver;
            $driver_task->task_alloted_date=carbon::now()->toDateString();
            $driver_task->task_alloted_time=carbon::now()->toTimeString();
            $driver_task->save();
            session()->flash('success','Task Updated Successfull');
            return redirect()->route('admin_driver_task.index');
        }
        else{
            session()->flash('error','The Task has already been Approved');
            return redirect()->back();
        }
    }

}
