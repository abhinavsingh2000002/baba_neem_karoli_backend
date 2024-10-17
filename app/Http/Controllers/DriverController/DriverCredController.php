<?php

namespace App\Http\Controllers\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cred;
use Carbon\Carbon;
use DB;

class DriverCredController extends Controller
{
    public function index()
    {
        return view('Driver.Cred.cred_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Distributor Name',
            2 => 'Distributor Image',
            3 => 'Cred Date & Time',
            4 => 'Cred In',
            5 => 'Cred Out',
            6 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                        OR creds.date LIKE '%$searchValue%'
                         OR creds.time LIKE '%$searchValue%')";
        }
        $driver_id=Auth::user()->id;
        $sql = "SELECT creds.*,users.name as distributor_name,users.image_path as distributor_image  FROM creds,users
        WHERE creds.user_id=users.id AND users.status=1 AND creds.driver_id=$driver_id $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY creds.id DESC LIMIT " . $params['start'] . ", " . $params['length'];
        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $key=> $obj) {
            $id = $obj->id;
            $sno=$key+1;
            $distributorName=$obj->distributor_name;
            $distributorImage=$obj->distributor_image;
            $credDateTime=$obj->date.' '.$obj->time;
            $credIn=$obj->cred_in;
            $credOut=$obj->cred_out;


            if (!empty($distributorImage) && file_exists(public_path($distributorImage))) {
                $imageUrlDistributor = asset($distributorImage); // Path to user's image
            } else {
                $imageUrlDistributor = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtmlDistributor = '<img src="' . $imageUrlDistributor . '" alt="User Image" width="70" height="70" />';


            $data[] = array(
                $sno,
                $distributorName,
                $imageHtmlDistributor,
                $credDateTime,
                $credIn,
                $credOut,
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
    public function add()
    {
        $user=User::where('role_id',2)->get();
        return view('Driver.Cred.cred_add')->with(['user'=>$user]);
    }

    public function create(Request $request)
    {
        $cred=new Cred();
        $cred->date=Carbon::now()->toDateString();
        $cred->time=Carbon::now()->toTImeString();
        $cred->user_id=$request->distributor_name;
        $cred->driver_id=Auth::user()->id;
        $cred->cred_out=$request->cred_out;
        $cred->save();
        session()->flash('success','Cred Created Successfully');
        return redirect()->route('driver_cred.index');
    }
}
