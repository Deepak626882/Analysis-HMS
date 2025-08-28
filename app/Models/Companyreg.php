<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Exception;

class Companyreg extends Model
{
    use Sortable;
    protected $currenttime;

    protected $table = 'company';
    protected $table2 = 'users';
    protected $primaryKey = 'propertyid';
    public $timestamps = false;
    protected $fillable = [
        'comp_code', 'sn_num', 'comp_name', 'start_dt', 'end_dt', 'address1', 'address2', 'country', 'state', 'city', 'state_code', 'mobile',
        'email', 'password', 'acname', 'acnum', 'ifsccode', 'bankname', 'branchname', 'cfyear', 'pfyear', 'pin', 'u_name', 'u_entdt', 'pan_no', 'nationality', 'gstin', 'division_code', 'legal_name',
        'trade_name', 'logo'
    ];

    public function insertUser()
    {
        if (!empty($this->fillable)) {
            $insertdata = [
                'u_name' => $this->u_name,
                'name' => $this->u_name,
                'propertyid' => $this->propertyid,
                'email' => $this->email,
                'password' => $this->password,
                'role' => 2,
                'created_at' => now(),
            ];

            try {
                DB::table('users')->insert($insertdata);
            } catch (Exception $e) {
                echo ('Error inserting data into users table: ' . $e->getMessage());
            }
        } else {
            echo "The fillable property is empty or not set correctly.";
        }
    }
    // public function insertuserpermission()
    // {
    //     if (!empty($this->fillable)) {
    //         $insertdata = [
    //             'u_name' => $this->u_name,
    //             'propertyid' => $this->propertyid,
    //             'role' => 'Property',
    //             'u_entdt' => now(),
    //             'u_ae' => 'a',
    //         ];

    //         try {
    //             DB::table('userpermission')->insert($insertdata);
    //         } catch (Exception $e) {
    //             echo ('Error inserting data into userpermission table: ' . $e->getMessage());
    //         }
    //     } else {
    //         echo "The fillable property is empty or not set correctly.";
    //     }
    // }

    public function insertcrud()
    {
        $path = storage_path('app/public/menu.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($jsonData as $data) {
                    $menuname = $data['Menu']['Name'];
                    $menucode = $data['Menu']['Code'];
                    $updatecrud = [
                        'u_name' => $this->u_name,
                        'propertyid' => $this->propertyid,
                        'u_entdt' => now(),
                        'role' => 'Property',
                        'menuid' => $menucode,
                        'menuname' => $menuname,
                        'u_ae' => 'a',
                    ];
                    DB::table('usercrudperm')->insert($updatecrud);
                }
            } else {
                return back()->with('error', 'JSON parsing error: ' . json_last_error_msg());
            }
        } else {
            return back()->with('error', 'File not found: ' . $path);
        }
    }
    public function insertEnvironment()
    {
        if (!empty($this->fillable)) {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $currentdate = date('Y-m-d');
            $insertdata = [
                'u_name' => $this->u_name,
                'propertyid' => $this->propertyid,
                'ncur' => $currentdate,
                'u_entdt' => $currenttime,
            ];

            try {
                DB::table('enviro_general')->insert($insertdata);
            } catch (Exception $e) {
                echo ('Error inserting data into enviro_general: ' . $e->getMessage());
            }
            $insertdata2 = [
                'u_name' => $this->u_name,
                'propertyid' => $this->propertyid,
                'u_entdt' => $currenttime,
                'u_ae' => 'a',
            ];

            $insert3 = [
                'u_name' => $this->u_name,
                'propertyid' => $this->propertyid,
                'u_entdt' => $currenttime,
                'u_ae' => 'a',
            ];

            $insertchannelenviro = [
                'u_name' => $this->u_name,
                'propertyid' => $this->propertyid,
                'checkyn' => 'N',
                'username' => '',
                'password' => '',
                'apikey' => '',
                'u_entdt' => $currenttime,
                'u_ae' => 'a',
            ];

            try {
                ChannelEnviro::insert($insertchannelenviro);
                DB::table('enviro_form')->insert($insertdata2);
                DB::table('permission')->insert($insertdata2);
                DB::table('enviro_pos')->insert($insert3);
            } catch (Exception $e) {
                echo ('Error inserting data into enviro_form: ' . $e->getMessage());
            }
        } else {
            echo "The fillable property is empty or not set correctly.";
        }
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            $sis = 'ANA';
            $maxSid = DB::table('company')->max('comp_code');
            $maxSidNum = intval(substr($maxSid, strlen($sis)));
            $sidNum = $maxSidNum + 1;
            $sidNumStr = str_pad($sidNum, 3, '0', STR_PAD_LEFT);
            $company->comp_code = $sis . $sidNumStr;
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $company->u_entdt = $currenttime;

            $currentYear = date('Y');
            $nextYear = date('Y') + 1;
            if (date('n') < 4) {
                $currentYear -= 1;
                $nextYear -= 1;
            }
            $company->cfyear = $currentYear . '-' . substr($nextYear, -2);
            $company->pfyear = ($currentYear - 1) . '-' . substr($currentYear, -2);

            $company->password = bcrypt($company->password);

            $maxpropertyid = DB::table('company')->max('propertyid');
            if (is_null($maxpropertyid)) {
                $propertyidnumber = '101';
            } else {
                $propertyidnumber = $maxpropertyid + 1;
            }
            $company->propertyid = $propertyidnumber;
            $company->insertUser();
            $company->insertEnvironment();
            // $company->insertuserpermission();
            $company->insertcrud();
        });
    }

    public static function updateCompany($property_id, $updateData)
    {
        $company = self::where('propertyid', $property_id)->first();
        $emaildata = DB::table('company')->where('propertyid', $property_id)->first();

        if ($company) {
            try {
                date_default_timezone_set('Asia/Kolkata');
                $currenttime = date('Y-m-d H:i:s');
                $company->update($updateData);
                DB::table('users')
                    ->where('propertyid', $property_id)
                    ->update([
                        'name' => $updateData['u_name'],
                        'u_name' => $updateData['u_name'],
                        'email' => $updateData['email'],
                        'updated_at' => $currenttime,
                        'u_ae' => 'e',
                    ]);
                DB::table('company')
                    ->where('propertyid', $property_id)
                    ->update([
                        'u_updatedt' => now(),
                        'u_ae' => 'e',
                    ]);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    public static function InsertCity($property_id, $u_name, $data)
    {
        $tableName = 'tbl_city';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'propertyid' => $property_id,
            'u_name' => $u_name,
            'u_entdt' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }


    public static function InsertCountry($property_id, $u_name, $data)
    {

        $tableName = 'tbl_country';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'propertyid' => $property_id,
            'u_name' => $u_name,
            'u_entdt' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }

    public static function InsertState($property_id, $u_name, $data)
    {

        $tableName = 'tbl_state';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'propertyid' => $property_id,
            'u_name' => $u_name,
            'u_entdt' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }

    /**
     * Summary of update_country
     * @param mixed $country_code
     * @param mixed $data
     * @return bool
     */
    public static function update_country($country_code, $data)
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $update = DB::table('tbl_country')->where('country_code', $country_code)->update(['propertyid' => '1', 'name' => $data['name'], 'nationality' => $data['nationality'], 'u_name' => $data['u_name'], 'u_updatedt' => $currenttime, 'u_ae' => 'e']);

            if ($update) {
                return true;
            } else {
                throw new Exception("Country update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function update_state($state_code, $data)
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $update = DB::table('tbl_state')->where('state_code', $state_code)->update(['propertyid' => '1', 'name' => $data['name'], 'country' => $data['country'], 'u_name' => $data['u_name'], 'u_updatedt' => $currenttime, 'u_ae' => 'e']);

            if ($update) {
                return true;
            } else {
                throw new Exception("State update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function update_city($city_code, $data)
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $update = DB::table('tbl_city')->where('city_code', $city_code)->update(['propertyid' => '1', 'cityname' => $data['cityname'], 'country' => $data['country'], 'state' => $data['state'], 'zipcode' => $data['zipcode'], 'u_name' => $data['u_name'], 'u_updatedt' => $currenttime, 'u_ae' => 'e']);

            if ($update) {
                return true;
            } else {
                throw new Exception("City update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function InsertUsermaster($data)
    {

        $tableName = 'users';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'created_at' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }

    public static function update_usermaster($userid, $data)
    {
        try {
            $tableName = 'users';
            $timezoneHandler = new CompanyLog();
            $update = DB::table($tableName)->where('id', $userid)->update(['propertyid' => $data['propertyid'], 'name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'], 'u_name' => $data['u_name'], 'updated_at' => $timezoneHandler->getCurrentTime(), 'u_ae' => 'e']);
            if ($update) {
                return true;
            } else {
                throw new Exception("User update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
