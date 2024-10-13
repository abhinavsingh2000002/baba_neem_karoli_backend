<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class DistributorController extends Controller
{
    public function index()
    {
        return view('Backend.Distributor.distributor_listing');
    }

    public function listing(Request $request)
    {
        $columns = array(
            0 => 'SNo',
            1 => 'Name',
            2 => 'Email',
            3 => 'Mobile',
            4 => 'Image',
            5 => 'dob',
            6 => '',
        );

        $params = $_REQUEST;

        $where = "";
        $filter = '';

        // Handle search input
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $where .= " AND (users.name LIKE '%$searchValue%'
                    OR users.email LIKE '%$searchValue%'
                    OR users.mobile LIKE '%$searchValue%')";
        }

        $sql = "SELECT * FROM users WHERE users.status=1 AND users.role_id = 2 $filter $where";

        $sqlTot = $sql;
        $sqlRec = $sql . " ORDER BY users.id DESC LIMIT " . $params['start'] . ", " . $params['length'];

        $result = DB::select($sqlRec);
        $totalRecords = count(DB::select($sqlTot));
        $sno = $params['start'] + 1;
        $data = [];
        foreach ($result as $obj) {
            $id = $obj->id;
            $actionButtons = '
            <a class="btn btn-sm btn-info" href="' . route('distributor.view', $id) . '" title="View">
                    <i class="fas fa-eye"></i>
            </a>
            <a class="btn btn-sm btn-warning" href="' . route('distributor.edit', $id) . '" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <a class="btn btn-sm btn-danger" href="' . route('distributor.delete', $id) . '" title="Delete" onclick="return confirm(\'Are you sure you want to delete this item?\');">
                <i class="fas fa-trash-alt"></i>
            </a>';

            // Check if the image path is available, otherwise use a default avatar image
            if (!empty($obj->image_path) && file_exists(public_path($obj->image_path))) {
                $imageUrl = asset($obj->image_path); // Path to user's image
            } else {
                $imageUrl = asset('../../../app-assets/images/portrait/small/avatar-s-27.png');
            }

            // Generate the image HTML
            $imageHtml = '<img src="' . $imageUrl . '" alt="User Image" width="70" height="70" />';

            $data[] = array(
                $sno,
                $obj->name,
                $obj->email,
                $obj->mobile,
                $imageHtml,
                $obj->dob,
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
            // dd($request);
            $rules = [
                'name' => 'required|string|min:3|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|max:50',
                'mobile' => 'required|digits:10|unique:users,mobile',
                'dob' => 'required|date|before:today',
                'address' => 'required|string|min:5|max:255',
                'aadhar_number' => 'required|digits:12|unique:users,aadhar_number',
                'pan_number' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|unique:users,pan_number',
                'role_id' => 'required|integer|in:1,2,3',
                'image_path' => 'required|mimes:jpeg,png|max:10240',

            ];
            $validator = Validator::make($request->all(), $rules);
            if (!$validator->fails()) {
                $image_file = $request->image_path;
                $name_email = str_replace(' ', '_', $request->name) . '_' . $request->email;
                $role = $request->role_id;

                $folderPath = ($role == '2') ? '/uploads/distributor/' : '/uploads/driver/';


                if (!is_dir(public_path($folderPath))) {
                    mkdir(public_path($folderPath), 0777, true);
                }

                // Get the uploaded file
                $file = $request->file('image_path');
                $extension = $file->getClientOriginalExtension();


                $image_name = $name_email . '_' . time();
                $filename = $image_name . '.' . $extension;
                $path = public_path($folderPath . $filename);

                // Move the uploaded file to the correct folder
                $file->move(public_path($folderPath), $filename);

                // Create a new user
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->dob = $request->dob;
                $user->role_id = $request->role_id;
                $user->aadhar_number = $request->aadhar_number;
                $user->pan_number = $request->pan_number;
                $user->image_path = $folderPath . $filename;
                $user->password = Hash::make($request->password);
                $user->address = $request->address;
                $user->save();


                session()->flash('success', 'Distributor Registered Successfull');
                return redirect()->route('distributor.index');
            }
            else{
                return redirect()->back()
                ->withErrors($validator) // Pass the validator errors to the session
                ->withInput();
            }
        }
        $role=Role::where('id','=',2)->get();
        return view('Backend.Distributor.distributor_add')->with(['role'=>$role]);

    }

    public function view(Request $request)
    {
        $data=User::where('id',$request->id)->first();
        return view('Backend.Distributor.distributor_view')->with(['data'=>$data]);
    }

    public function edit(Request $request)
    {
        $role=Role::where('id','!=',1)->get();
        $data=User::where('id',$request->id)->first();
        return view('Backend.Distributor.distributor_edit')->with(['data'=>$data,'role'=>$role]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email,' . $id,  // Ignore unique check for current user's email
            'password' => 'nullable|string|min:8|max:50',  // Password update is optional
            'mobile' => 'required|digits:10|unique:users,mobile,' . $id,  // Ignore unique check for current user's mobile
            'dob' => 'required|date|before:today',
            'address' => 'required|string|min:5|max:255',
            'aadhar_number' => 'required|digits:12|unique:users,aadhar_number,' . $id,  // Ignore unique check for current user's aadhar
            'pan_number' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|unique:users,pan_number,' . $id,  // Ignore unique check for current user's PAN
            'role_id' => 'required|integer|in:1,2,3',
            'image_path' => 'nullable|mimes:jpeg,png|max:10240',  // Image upload is optional during update
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if (!$validator->fails()) {
            $user = User::findOrFail($id);
        
            $name_email = str_replace(' ', '_', $request->name) . '_' . $request->email;
            $role = $request->role_id;
        
            $folderPath = ($role == '2') ? '/uploads/distributor/' : '/uploads/driver/';
        
            if (!is_dir(public_path($folderPath))) {
                mkdir(public_path($folderPath), 0777, true);
            }
        
            if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');
                $extension = $file->getClientOriginalExtension();
        
                $image_name = $name_email . '_' . time();
                $filename = $image_name . '.' . $extension;
                $path = public_path($folderPath . $filename);
        
                // Move the uploaded file
                $file->move(public_path($folderPath), $filename);
        
                // Directly assign the image path to the user
                $user->image_path = $folderPath . $filename;
            }
        
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->dob = $request->dob;
            $user->role_id = $request->role_id;
            $user->aadhar_number = $request->aadhar_number;
            $user->pan_number = $request->pan_number;
            $user->address = $request->address;
        
            // Hash password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        
            $user->save();
        
            session()->flash('success', 'Distributor Updated Successfully');
            return redirect()->route('distributor.index');
        } else {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    }        

    public function delete($id)
    {
        $user=User::findOrFail($id);
        $user->status=0;
        $user->save();
        session()->flash('delete', 'Distributor Deleted Successfully');
        return redirect()->route('distributor.index');
    }

}
