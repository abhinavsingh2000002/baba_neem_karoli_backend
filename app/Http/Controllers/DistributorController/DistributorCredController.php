<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;


class DistributorCredController extends Controller
{
    public function index()
    {
        return view('Distributor.Cred.cred_listing');
    }

    public function listing()
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Driver Name',
            2 => 'Driver Image',
            3 => 'Cred Date & Time',
            4 => 'Cred In',
            5 => 'Cred Out',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (driver.name LIKE '%$searchValue%'
                       OR distributor.name LIKE '%$searchValue%'
                    OR creds.date LIKE '%$searchValue%'
                     OR creds.time LIKE '%$searchValue%')";
        }
        $userId = Auth::user()->id;
        $sql = "SELECT creds.*, users.name as driver_name, users.image_path as driver_image
        FROM creds
        LEFT JOIN users ON creds.driver_id = users.id
        WHERE users.status = 1
        AND creds.user_id = $userId $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY creds.id DESC LIMIT " . $params['start'] . ", " . $params['length'];
        $result = DB::select($sqlRec);
        // dd($result);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $key=> $obj) {
            $id = $obj->id;
            $sno=$key+1;
            $driverName=$obj->driver_name;
            $driverImage=$obj->driver_image;
            $credDateTime=$obj->date.' '.$obj->time;
            $credIn=$obj->cred_in;
            $credOut=$obj->cred_out;

            if (!empty($driverImage) && file_exists(public_path($driverImage))) {
                $imageUrlDriver = asset($driverImage); // Path to user's image
            } else {
                $imageUrlDriver = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtmlDriver = '<img src="' . $imageUrlDriver . '" alt="User Image" width="70" height="70" />';


            $data[] = array(
                $sno,
                $driverName,
                $imageHtmlDriver,
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
}
