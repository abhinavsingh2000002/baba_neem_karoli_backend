<?php

namespace App\Api\Traits;
use App\Models\ConnectionRequest;
use Illuminate\Support\Str;
use App\Models\User;


trait ValidationTrait
{
    public function getConnectionId($api_key)
    {
        if ($api_key == "y9O2fffDuVFFWgynkYwP") {
            $random_string = Str::random(20);
            $numeric_string = '';
            for ($i = 0; $i < strlen($random_string); $i++) {
                $numeric_string .= ord($random_string[$i]);
            }
            if (strlen($numeric_string) > 20) {
                $numeric_string = substr($numeric_string, 0, 20);
            } elseif (strlen($numeric_string) < 20) {
                $numeric_string .= str_repeat('0', 20 - strlen($numeric_string));
            }
            try {
                $insert_connection_id = ConnectionRequest::create(['connection_id' => $numeric_string]);
            } catch (\Exception $e) {
                return $e;
            }
            if ($insert_connection_id) {
                return $numeric_string;
            } else {
                return response()->json(['Connection Id' => null, 'Message' => 'Could not get Connection Id.']);
            }
        } else {
            return response()->json(['Connection Id' => null, 'Message' => 'API_key not matched.']);
        }
    }

    public function validate_connection_id($key)
    {
        $connection = ConnectionRequest::where('connection_id', $key)->first();
        if ($connection) {
            return true;
        } else {
            return false;
        }
    }


    public function validate_user($connection_id, $auth_code)
    {
        $find_user = ConnectionRequest::where('connection_id', $connection_id)->where('auth_code', $auth_code)->first();
        if ($find_user) {
            return $find_user->user_id;
        } else {
            return false;
        }
    }


    public function validate_admin($user_id)
    {
        $find_user_role = User::where('id',$user_id)->first();
        if($find_user_role->role_id==1){
            return $find_user_role->id;
        }
        else {
            return false;
        }
    }



    // public function validate_connection_id($key)
    // {
    //     $connection=ConnectionRequest::where('connection_id',$key)->first();
    //     if($connection)
    //     {
    //         return true;
    //     }
    //     else
    //     {
    //         return false;
    //     }
    // }

    // public function validate_user($connection_id,$auth_code)
    // {
    //     $findUser = ConnectionRequest::where('connection_id',$connection_id)->where('auth_code',$auth_code)->first();
    //     if($findUser)
    //     {
    //         return $findUser->user_id;
    //     }
    //     else
    //     {
    //         return false;
    //     }
    // }

}
