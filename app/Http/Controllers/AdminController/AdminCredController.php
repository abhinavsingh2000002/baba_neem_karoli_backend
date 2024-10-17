<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class AdminCredController extends Controller
{
    public function index()
    {
        return view('Backend.cred.cred_listing');
    }

    public function listing()
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
            $where .= " AND (driver.name LIKE '%$searchValue%'
                       OR distributor.name LIKE '%$searchValue%'
                    OR creds.date LIKE '%$searchValue%'
                     OR creds.time LIKE '%$searchValue%')";
        }

        $sql = "SELECT creds.*,driver.name as driver_name,driver.image_path as driver_image,
        distributor.name as distributor_name,distributor.image_path as distributor_image FROM creds
        LEFT JOIN users as driver ON creds.driver_id=driver.id
        LEFT JOIN users as distributor ON creds.user_id=distributor.id
        WHERE driver.status=1 AND distributor.status=1  $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY creds.id DESC LIMIT " . $params['start'] . ", " . $params['length'];
        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $key=> $obj) {
            $id = $obj->id;
            $sno=$key+1;
            $driverName=$obj->driver_name;
            $driverImage=$obj->driver_image;
            $distributorName=$obj->distributor_name;
            $distributorImage=$obj->distributor_image;
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





            if (!empty($distributorImage) && file_exists(public_path($distributorImage))) {
                $imageUrlDistributor = asset($distributorImage); // Path to user's image
            } else {
                $imageUrlDistributor = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtmlDistributor = '<img src="' . $imageUrlDistributor . '" alt="User Image" width="70" height="70" />';


            $data[] = array(
                $sno,
                $driverName,
                $imageHtmlDriver,
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
}
