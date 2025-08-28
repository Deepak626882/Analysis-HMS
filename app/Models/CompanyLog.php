<?php

namespace App\Models;

use Illuminate\Console\Events\ScheduledBackgroundTaskFinished;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Psr\SimpleCache\InvalidArgumentException;

class CompanyLog extends Model
{

    protected $currenttime;

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->currenttime = date('Y-m-d H:i:s');
    }

    public function getCurrentTime()
    {
        return $this->currenttime;
    }

    public static function InsertCity($data)
    {

        $tableName = 'cities';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'u_entdt' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }

    public static function InsertCountry($data)
    {

        $tableName = 'countries';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
            'u_entdt' => $currenttime,
        ] + $data;

        DB::table($tableName)->insert($insertData);
    }

    public static function InsertState($data)
    {

        $tableName = 'states';
        date_default_timezone_set('Asia/Kolkata');
        $currenttime = date('Y-m-d H:i:s');
        $insertData = [
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
            $timezoneHandler = new CompanyLog();
            $update = DB::table('countries')->where('country_code', $country_code)->update(['propertyid' => $data['propertyid'], 'name' => $data['name'], 'nationality' => $data['nationality'], 'u_name' => $data['u_name'], 'u_updatedt' => $timezoneHandler->getCurrentTime(), 'u_ae' => 'e']);

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
            $timezoneHandler = new CompanyLog();
            $update = DB::table('states')->where('state_code', $state_code)->update(['propertyid' => '1', 'name' => $data['name'], 'country' => $data['country'], 'u_name' => $data['u_name'], 'u_updatedt' => $timezoneHandler->getCurrentTime(), 'u_ae' => 'e']);

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
            $timezoneHandler = new CompanyLog();
            $update = DB::table('cities')->where('city_code', $city_code)->update(['propertyid' => '1', 'cityname' => $data['cityname'], 'country' => $data['country'], 'state' => $data['state'], 'zipcode' => $data['zipcode'], 'u_name' => $data['u_name'], 'u_updatedt' => $timezoneHandler->getCurrentTime(), 'u_ae' => 'e']);

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
            $update = DB::table($tableName)->where('u_name', $userid)->update(['propertyid' => $data['propertyid'], 'name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'], 'u_name' => $data['u_name'], 'updated_at' => $data['u_updtedt'], 'u_ae' => 'e']);
            if ($update) {
                return true;
            } else {
                throw new Exception("User update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function UpdateCompanyDetail($propertyid, $data)
    {
        try {
            $tableName = 'company';
            $timezoneHandler = new CompanyLog();
            $update2 = DB::table('users')->where('u_name', $data['u_name'])->update([
                'email' => $data['email'],
                'updated_at' => $timezoneHandler->getCurrentTime(),
                'u_ae' => 'e'
            ]);
            $update = DB::table($tableName)->where('propertyid', $propertyid)->update([
                'legal_name' => $data['legal_name'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'u_updatedt' => $timezoneHandler->getCurrentTime(),
                'u_ae' => 'e'
            ]);
            if ($update && $update2) {
                return true;
            } else {
                throw new Exception("Record update failed.");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function Insertacgroup($propertyid, $u_name, $jsonData)
    {
        $tablename = 'acgroup';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('group_code')->toArray();

        $recordsInsertedCount = 0;

        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $code = $account['Code'];
                $nature = $account['Nature'];

                $group_code = $code . $propertyid;

                if (!in_array($group_code, $existingCodes)) {
                    $insertData = [
                        'group_code' => $group_code,
                        'group_name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sys_group' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'nature' => $nature,
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }


    public static function Insertsubgroup($propertyid, $u_name, $jsonData)
    {
        $tablename = 'subgroup';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingNames = $existingRecords->pluck('name')->toArray();

        $recordsInsertedCount = 0;

        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $subcode = $account['SubCode'];
                $code = $account['GroupCode'];
                $nature = $account['Nature'];

                $group_code = $code . $propertyid;
                $sub_code = $subcode . $propertyid;

                if (!in_array($category, $existingNames)) {
                    $insertData = [
                        'group_code' => $group_code,
                        'sub_code' => $sub_code,
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'nature' => $nature,
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertCountryLoad($data)
    {
        $tableName = 'countries';
        $currenttime = new CompanyLog();
        $tblCountryData = DB::table('tbl_country')->get();
        $recordsInsertedCount = 0;
        foreach ($tblCountryData as $country) {
            $existingCountry = DB::table('countries')
                ->where('name', $country->name)
                ->where('propertyid', $data['propertyid'])
                ->first();
            if (!$existingCountry) {
                $insertData = [
                    'name' => $country->name,
                    'country_code' => $country->country_code,
                    'nationality' => $country->nationality,
                    'u_entdt' => $currenttime->getCurrentTime(),
                ] + $data;
                DB::table($tableName)->insert($insertData);
                $recordsInsertedCount++;
            }
        }
        return $recordsInsertedCount;
    }

    public static function InsertStateLoad($data)
    {
        $tableName = 'states';
        $currenttime = new CompanyLog();
        $tblStateData = DB::table('tbl_state')->get();
        $recordsInsertedCount = 0;
        foreach ($tblStateData as $state) {
            $existingstates = DB::table('states')
                ->where('name', $state->name)
                ->where('propertyid', $data['propertyid'])
                ->first();
            if (!$existingstates) {
                $insertData = [
                    'name' => $state->name,
                    'country' => $state->country,
                    'state_code' => $state->state_code,
                    'u_entdt' => $currenttime->getCurrentTime(),
                ] + $data;
                DB::table($tableName)->insert($insertData);
                $recordsInsertedCount++;
            }
        }
        return $recordsInsertedCount;
    }

    public static function InsertCityLoad($data)
    {
        $tableName = 'cities';
        $currenttime = new CompanyLog();
        $tblCityData = DB::table('tbl_city')->get();
        $recordsInsertedCount = 0;
        $citycode = 1;
        foreach ($tblCityData as $city) {
            $existingcity = DB::table('cities')
                ->where('cityname', $city->cityname)
                ->where('propertyid', $data['propertyid'])
                ->first();
            if (!$existingcity) {
                $insertData = [
                    'city_code' => $data['propertyid'] . $citycode,
                    'cityname' => $city->cityname,
                    'country' => $city->country,
                    'zipcode' => $city->zipcode,
                    'state' => $city->state,
                    'u_entdt' => $currenttime->getCurrentTime(),
                ] + $data;
                DB::table($tableName)->insert($insertData);
                $recordsInsertedCount++;
                $citycode++;
            }
        }
        return $recordsInsertedCount;
    }

    public static function InsertSundryMaster($propertyid, $u_name, $jsonData)
    {
        $tablename = 'sundrymast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('sundry_code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $sundrycode = $account['SundryCode'];
                $nature = $account['Nature'];
                $calcsign = $account['CalcSign'];
                $sundry_code = $sundrycode . $propertyid;

                if (!in_array($sundry_code, $existingCodes)) {
                    $insertData = [
                        'sundry_code' => $sundry_code,
                        'name' => $category,
                        'calcsign' => $calcsign,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'nature' => $nature,
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertSundryType($propertyid, $u_name, $jsonData)
    {
        $tablename = 'sundrytypefix';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('sundry_code')->toArray();

        $recordsInsertedCount = 0;

        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $sundrycode = $account['SundryCode'];
                $nature = $account['Nature'];
                $calcsign = $account['CalcSign'];
                $sundry_code = $sundrycode . $propertyid;

                if (!in_array($sundry_code, $existingCodes)) {
                    $insertData = [
                        'sundry_code' => $sundry_code,
                        'disp_name' => $category,
                        'calcsign' => $calcsign,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'nature' => $nature,
                        'calcformula' => $account['CalcFormula'],
                        'peroramt' => $account['PerOrAmt'],
                        'roundoff' => $account['RoundOff'],
                        'vals' => $account['Value'],
                        'limits' => $account['Limit'],
                        'postac' => $account['PostAc'],
                        'grp' => $account['Grp'],
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertUnitMaster($propertyid, $u_name, $jsonData)
    {
        $tablename = 'unitmast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('ucode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'ucode' => $ucode,
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'activeYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertHousekeep($propertyid, $u_name, $jsonData)
    {
        $tablename = 'godown_mast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('scode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $shortname = $account['Short Name'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'scode' => $ucode,
                        'short_name' => $shortname,
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertHousekeep2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'depart';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('scode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['CODE'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'dcode' => $account['D CODE'] . $propertyid,
                        'kot_yn' => $account['KOT YN'],
                        'pos' => $account['POS'],
                        'rest_type' => $account['REST TYPE'],
                        'back_color' => $account['BACK COLOR'],
                        'outlet_yn' => $account['OUTLET YN'],
                        'disc_app' => $account['DISC APP'],
                        'short_name' => $account['SHORT NAME'],
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertTaxLoad($propertyid, $u_name, $jsonData)
    {
        $tablename = 'revmast';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('rev_code')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $RevCode = $account['Rev_Code'];
                $rev_code = $RevCode . $propertyid;
                $accode = $account['Ac_Code'];
                $ac_code = $accode . $propertyid;
                $sundrycode = $account['SundryCode'] != '' ? $account['SundryCode'] . $propertyid : '';

                if (!in_array($rev_code, $existingCodes)) {
                    $insertData = [
                        'rev_code' => $rev_code,
                        'name' => $category,
                        'ac_code' => $ac_code,
                        'sundry' => $sundrycode,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'field_type' => 'T',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertTaxLoad2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'taxstru';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('str_code')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $strcode = $account['StrCode'];
                $str_code = $strcode . $propertyid;
                $taxcode = $account['TaxCode'];
                $nature = $account['Nature'];
                $tax_code = $taxcode . $propertyid;

                // Check if a record with the same propertyid and category name exists
                $alreadyExists = DB::table($tablename)
                    ->where('propertyid', $propertyid)
                    ->where('name', $category)
                    ->first();

                if (!$alreadyExists && !in_array($str_code, $existingCodes)) {
                    $insertData = [
                        'str_code' => $str_code,
                        'name' => $category,
                        'tax_code' => $tax_code,
                        'nature' => $nature,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'sno' => 1,
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertTaxStructure($data, $propertyid, $u_name)
    {
        try {
            $tableName = 'taxstru';
            $currenttime = (new CompanyLog())->getCurrentTime();

            // Generate a base code using property ID
            $baseStrCode = 'TAX' . $propertyid;
            $str_code = $baseStrCode;
            $counter = 1;

            // Check for uniqueness and increment suffix if needed
            while (DB::table($tableName)->where('str_code', $str_code)->exists()) {
                $str_code = $baseStrCode . '_' . $counter;
                $counter++;
            }

            $insertData = [
                'propertyid' => $propertyid,
                'u_name' => $u_name,
                'str_code' => $str_code,
                'u_entdt' => $currenttime,
                'sysYN' => 'N',
            ] + $data;
            DB::table($tableName)->insert($insertData);
            return 'success';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function UpdateTaxStructure($data, $propertyid, $u_name, $strunameold)
    {
        try {
            $tableName = 'taxstru';

            $currenttime = (new CompanyLog())->getCurrentTime();
            $shortname = $data['name'];
            $firstCharacter = substr($shortname, 0, 2);
            $lastchar = substr($shortname, -2);
            $str_code = $propertyid . $firstCharacter . $lastchar;

            $updateData = [
                'propertyid' => $propertyid,
                'u_name' => $u_name,
                'str_code' => $str_code,
                'u_updatedt' => $currenttime,
                'sysYN' => 'N',
                'u_ae' => 'e',
            ] + $data;

            $snolist = DB::table('taxstru')->where('propertyid', $propertyid)
                ->where('name', $strunameold)->get();
            foreach ($snolist as $list) {
                DB::table('taxstru')
                    ->where('propertyid', $propertyid)
                    ->where('sno', $list->sno)
                    ->update($updateData);
            }

            return $updateData;
            // return 'success';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function InserComp_mast($data, $propertyid, $u_name)
    {
        try {
            $tableName = 'comp_mast';
            $currenttime = (new CompanyLog())->getCurrentTime();
            $maxSubCode = DB::table('comp_mast')->where('propertyid', $propertyid)->max('comp_code');
            if ($maxSubCode === null) {
                $comp_code = 1;
            } else {
                $comp_code = intval(substr($maxSubCode, 0, -3)) + 1;
            }

            $nature = DB::table('acgroup')->where('group_code', $data['group_code'])->pluck('nature')->first();
            $insertData = [
                'comp_code' => $comp_code . $propertyid,
                'u_entdt' => $currenttime,
                'sysYN' => 'N',
                'u_name' => $u_name,
                'propertyid' => $propertyid,
                'nature' => $nature,
            ] + $data;
            DB::table($tableName)->insert($insertData);
            return 'success';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function Updatecompmast($data, $propertyid, $u_name)
    {
        try {
            $tableName = 'comp_mast';
            $currenttime = (new CompanyLog())->getCurrentTime();

            $nature = DB::table('acgroup')->where('group_code', $data['group_code'])->pluck('nature')->first();
            $insertData = [
                'u_updatedt' => $currenttime,
                'sysYN' => 'N',
                'u_name' => $u_name,
                'propertyid' => $propertyid,
                'nature' => $nature,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)->where('comp_code', $data['comp_code'])
                ->where('propertyid', $propertyid)
                ->update($insertData);
            return 'success';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function InsertVoucherPrefix($propertyid, $u_name, $jsonData)
    {
        $tablename = 'voucher_prefix';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('v_type')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $account) {
            $previousYear = date('Y') - 1;
            $currentYear = date('Y');
            $nextYear = date('Y') + 1;
            if (date('m') < 4) {
                $date_from = $previousYear . '-04-01';
                $date_to = $currentYear . '-03-31';
                $prefix = substr($date_from, 0, 4);
            } else {
                $date_from = $currentYear . '-04-01';
                $date_to = $nextYear . '-03-31';
                $prefix = substr($date_from, 0, 4);
            }
            foreach ($account['V_TYPE'] as $voucher_prefix) {
                if (!in_array($voucher_prefix, $existingCodes)) {
                    $insertData = [
                        'v_type' => $voucher_prefix,
                        'propertyid' => $propertyid,
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                        'prefix' => $prefix,
                        'start_srl_no' => 0,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertVoucherType($propertyid, $u_name, $jsonData)
    {
        $tablename = 'voucher_type';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('v_type')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $record) {
            foreach ($record as $key => $data) {
                if (!in_array($key, $existingCodes)) {
                    $insertData = [
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'SysYn' => 'Y',
                        'v_type' => $key,
                        'category' => $data['Category'],
                        'ncat' => $data['NCat'],
                        'contratype' => $data['Contratype'],
                        'description' => $data['Description'],
                        'description_help' => $data['Description_Help'],
                        'number_method' => $data['Number_Method'],
                        'start_no' => $data['Start_No'],
                        'last_ent_date' => $data['Last_Ent_Date'],
                        'separate_narr' => $data['Separate_Narr'],
                        'common_narr' => $data['Common_Narr'],
                        'narration' => $data['Narration'],
                        'chqno' => $data['ChqNo'],
                        'chqdt' => $data['ChqDt'],
                        'clgdt' => $data['ClgDt'],
                        'restcode' => $data['RestCode'],
                        'defaultcrac' => $data['DefaultCrAc'],
                        'defaultdrac' => $data['DefaultDrAc'],
                        'firstdrcr' => $data['FirstDrCr'],
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertSettlement($propertyid, $u_name, $jsonData)
    {
        $tablename = 'revmast';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('rev_code')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $category => $account) {
            $RevCode = $account['Rev_Code'];
            $rev_code = $RevCode . $propertyid;
            $accode = $account['Ac_Code'];
            $ac_code = $accode . $propertyid;
            $ac_code = $accode == '' ? '' : $ac_code;

            if (!in_array($rev_code, $existingCodes)) {
                $insertData = [
                    'rev_code' => $rev_code,
                    'name' => $category,
                    'tax_stru' => $account['TaxStru'],
                    'sales_rate' => $account['SaleRate'],
                    'type' => $account['Type'],
                    'flag_type' => $account['FlagType'],
                    'Desk_code' => $account['DeskCode'],
                    'pay_type' => $account['PAYTYPE'],
                    'field_type' => $account['FieldType'],
                    'ac_posting' => $account['ACPosting'],
                    'sundry' => $account['Sundry'],
                    'seq_no' => $account['SeqNo'],
                    'nature' => $account['Nature'],
                    'active' => $account['Active'],
                    'tax_inc' => $account['TaxInc'],
                    'payable_ac' => $account['PayableAc'],
                    'unregistered_ac' => $account['UnregisteredAc'],
                    'hsn_code' => $account['HSNCode'],
                    'map_code' => $account['MapCode'],
                    'ac_code' => $ac_code,
                    'propertyid' => $propertyid,
                    'u_name' => $u_name,
                    'sysYN' => 'Y',
                    'u_entdt' => $currenttime->getCurrentTime(),
                ];

                DB::table($tablename)->insert($insertData);
                $recordsInsertedCount++;
            }
        }
        return $recordsInsertedCount;
    }

    public static function InsertTravelAgent($propertyid, $u_name, $jsonData)
    {
        $tablename = 'subgroup';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('sub_code')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $category => $account) {
            $SubCode = $account['Subcode'];
            $sub_code = $SubCode . $propertyid;
            $group_code = '28' . $propertyid;

            if (!in_array($sub_code, $existingCodes)) {
                $insertData = [
                    'sub_code' => $sub_code,
                    'name' => $category,
                    'nature' => $account['Nature'],
                    'activeyn' => 'Y',
                    'comp_type' => $account['Comp_Type'],
                    'group_code' => $group_code,
                    'propertyid' => $propertyid,
                    'allow_credit' => '1',
                    'u_name' => $u_name,
                    'sysYN' => 'Y',
                    'u_entdt' => $currenttime->getCurrentTime(),
                ];

                DB::table($tablename)->insert($insertData);
                $recordsInsertedCount++;
            }
        }
        return $recordsInsertedCount;
    }

    public static function InsertFixcharges($propertyid, $u_name, $jsonData)
    {
        $tablename = 'revmast';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('rev_code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $RevCode = $account['Rev_Code'];
                $rev_code = $RevCode . $propertyid;
                $accode = $account['Ac_Code'];
                $ac_code = $accode . $propertyid;
                $ac_code = $accode == '' ? '' : $ac_code;
                $tax_stru = $account['TaxStru'] == '' ? '' : $account['TaxStru'] . $propertyid;

                if (!in_array($rev_code, $existingCodes)) {
                    $insertData = [
                        'rev_code' => $rev_code,
                        'name' => $category,
                        'tax_stru' => $tax_stru,
                        'sales_rate' => $account['SaleRate'],
                        'type' => $account['Type'],
                        'flag_type' => $account['FlagType'],
                        'Desk_code' => $account['DeskCode'] . $propertyid,
                        'pay_type' => $account['PAYTYPE'],
                        'field_type' => $account['FieldType'],
                        'ac_posting' => $account['ACPosting'],
                        'sundry' => $account['Sundry'],
                        'seq_no' => $account['SeqNo'],
                        'nature' => $account['Nature'],
                        'active' => $account['Active'],
                        'tax_inc' => $account['TaxInc'],
                        'payable_ac' => $account['PayableAc'],
                        'unregistered_ac' => $account['UnregisteredAc'],
                        'hsn_code' => $account['HSNCode'],
                        'map_code' => $account['MapCode'],
                        'ac_code' => $ac_code,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function Insertbussource($propertyid, $u_name, $jsonData)
    {
        $tablename = 'busssource';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('bcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $bcode = $account['Code'];
                $b_code = $bcode . $propertyid;
                if (!in_array($b_code, $existingCodes)) {
                    $insertData = [
                        'bcode' => $b_code,
                        'name' => $category,
                        'activeYN' => 'Y',
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function Insertgueststats($propertyid, $u_name, $jsonData)
    {
        $tablename = 'gueststats';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('gcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $gcode = $account['GCODE'];
                $g_code = $gcode . $propertyid;
                if (!in_array($g_code, $existingCodes)) {
                    $insertData = [
                        'gcode' => $g_code,
                        'name' => $category,
                        'activeYN' => $account['ACTIVE'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function Insertroomfeature($propertyid, $u_name, $jsonData)
    {
        $tablename = 'roomfeature';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('rcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $RCode = $account['Code'];
                $r_code = $RCode . $propertyid;
                if (!in_array($r_code, $existingCodes)) {
                    $insertData = [
                        'rcode' => $r_code,
                        'name' => $category,
                        'activeYN' => $account['ACTIVE'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function Insertdepart2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'depart';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('dcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $RCode = $account['Code'];
                $r_code = $RCode . $propertyid;
                if (!in_array($r_code, $existingCodes)) {
                    $insertData = [
                        'dcode' => $r_code,
                        'name' => $category,
                        'kot_yn' => $account['kot_yn'],
                        'pos' => $account['pos'],
                        'rest_type' => $account['rest_type'],
                        'back_color' => $account['back_color'],
                        'outlet_yn' => $account['outlet_yn'],
                        'short_name' => $account['short_name'],
                        'disc_app' => $account['disc_app'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertHouseup2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'godown_mast';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('scode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $RCode = $account['Code'];
                $r_code = $RCode . $propertyid;
                if (!in_array($r_code, $existingCodes)) {
                    $insertData = [
                        'scode' => $r_code,
                        'name' => $category,
                        'short_name' => $account['short_name'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertStore($propertyid, $u_name, $jsonData)
    {
        $tablename = 'godown_mast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('scode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $shortname = $account['short_name'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'scode' => $ucode,
                        'short_name' => $shortname,
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertHall($propertyid, $u_name, $jsonData)
    {
        $tablename = 'depart';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();

        $existingCodes = $existingRecords->pluck('dcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $record) {
            foreach ($record as $category => $account) {
                $RCode = $account['Code'];
                $r_code = $RCode . $propertyid;
                if (!in_array($r_code, $existingCodes)) {
                    $insertData = [
                        'dcode' => $r_code,
                        'name' => $category,
                        'kot_yn' => $account['kot_yn'],
                        'pos' => $account['pos'],
                        'rest_type' => $account['rest_type'],
                        'back_color' => $account['back_color'],
                        'outlet_yn' => $account['outlet_yn'],
                        'short_name' => $account['short_name'],
                        'disc_app' => $account['disc_app'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertStore2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'depart';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('dcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'dcode' => $account['Code'] . $propertyid,
                        'kot_yn' => $account['kot_yn'],
                        'pos' => $account['pos'],
                        'rest_type' => $account['rest_type'],
                        'back_color' => $account['back_color'],
                        'outlet_yn' => $account['outlet_yn'],
                        'short_name' => $account['short_name'],
                        'disc_app' => $account['disc_app'],
                        'name' => $category,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS1($propertyid, $u_name, $jsonData)
    {
        $tablename = 'revmast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('rev_code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'rev_code' => $ucode,
                        'name' => $category,
                        'ac_code' => $account['ac_code'] . $propertyid,
                        'tax_stru' => $account['tax_stru'],
                        'type' => $account['type'],
                        'flag_type' => $account['flag_type'],
                        'Desk_code' => $account['Desk_code'] . $propertyid,
                        'field_type' => $account['field_type'],
                        'round_off' => $account['round_off'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS2($propertyid, $u_name, $jsonData)
    {
        $tablename = 'depart';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('dcode')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'dcode' => $ucode,
                        'name' => $category,
                        'nature' => $account['nature'],
                        'kot_yn' => $account['kot_yn'],
                        'company_title' => $account['company_title'],
                        'pos' => $account['pos'],
                        'rest_type' => $account['rest_type'],
                        'outlet_yn' => $account['outlet_yn'],
                        'short_name' => $account['short_name'],
                        'disc_app' => $account['disc_app'],
                        'token_print' => $account['token_print'],
                        'order_booking' => $account['order_booking'],
                        'outlet_title' => $account['outlet_title'],
                        'member_info' => $account['member_info'],
                        'party_name' => $account['party_name'],
                        'split_bill' => $account['split_bill'],
                        'cust_info' => $account['cust_info'],
                        'ckot_print_yn' => $account['ckot_print_yn'],
                        'no_of_kot' => $account['no_of_kot'],
                        'no_of_bill' => $account['no_of_bill'],
                        'token_print_after' => $account['token_print_after'],
                        'print_on_save' => $account['print_on_save'],
                        'print_token_no' => $account['print_token_no'],
                        'auto_settlement' => $account['auto_settlement'],
                        'barcode_app' => $account['barcode_app'],
                        'auto_reset_token' => $account['auto_reset_token'],
                        'cur_token_no_kot' => $account['cur_token_no_kot'],
                        'barcode_partition_app_on' => $account['barcode_partition_app_on'],
                        'dis_print' => $account['dis_print'],
                        'grp_disc_app' => $account['grp_disc_app'],
                        'label_printing' => $account['label_printing'],
                        'free_item_app' => $account['free_item_app'],
                        'cover_mandatory' => $account['cover_mandatory'],
                        'mobile_no_mandatory' => $account['mobile_no_mandatory'],
                        'open_item_yn' => $account['open_item_yn'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS3($propertyid, $u_name, $jsonData)
    {
        $tablename = 'subgroup';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('sub_code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'sub_code' => $ucode,
                        'name' => $category,
                        'group_code' => $account['group_code'] . $propertyid,
                        'nature' => $account['nature'],
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS4($propertyid, $u_name, $jsonData)
    {
        $tablename = 'voucher_prefix';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('v_type')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $account) {
            $previousYear = date('Y') - 1;
            $currentYear = date('Y');
            $nextYear = date('Y') + 1;
            if (date('m') < 4) {
                $date_from = $previousYear . '-04-01';
                $date_to = $currentYear . '-03-31';
                $prefix = substr($date_from, 0, 4);
            } else {
                $date_from = $currentYear . '-04-01';
                $date_to = $nextYear . '-03-31';
                $prefix = substr($date_from, 0, 4);
            }
            foreach ($account['V_TYPE'] as $voucher_prefix) {
                if (!in_array($voucher_prefix, $existingCodes)) {
                    $insertData = [
                        'v_type' => $voucher_prefix,
                        'propertyid' => $propertyid,
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                        'prefix' => $prefix,
                        'start_srl_no' => 0,
                        'u_name' => $u_name,
                        'sysYN' => 'Y',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS5($propertyid, $u_name, $jsonData)
    {
        $tablename = 'voucher_type';
        $currenttime = new CompanyLog();
        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('v_type')->toArray();
        $recordsInsertedCount = 0;

        foreach ($jsonData as $record) {
            foreach ($record as $key => $data) {
                if (!in_array($key, $existingCodes)) {
                    $insertData = [
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'SysYn' => 'Y',
                        'v_type' => $key,
                        'category' => $data['category'],
                        'ncat' => $data['ncat'],
                        'contratype' => $data['contratype'],
                        'description' => $data['description'],
                        'description_help' => $data['description_help'],
                        'number_method' => $data['number_method'],
                        'start_no' => $data['start_no'],
                        'last_ent_date' => $data['last_ent_date'],
                        'separate_narr' => $data['separate_narr'],
                        'chqno' => $data['chqno'],
                        'chqdt' => $data['chqdt'],
                        'clgdt' => $data['clgdt'],
                        'restcode' => $data['restcode'] . $propertyid,
                    ];
                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS6($propertyid, $u_name, $jsonData)
    {
        $tablename = 'itemcatmast';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('Code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['Code'];
                $ucode = $unitcode . $propertyid;

                if (!in_array($ucode, $existingCodes)) {
                    $insertData = [
                        'Code' => $ucode,
                        'Name' => $category,
                        'AcCode' => $account['acname'] . $propertyid,
                        'TaxStru' => $account['taxstru'] . $propertyid,
                        'OutletYN' => 'Y',
                        'RestCode' => 'RS' . $propertyid,
                        'Flag' => 'Category',
                        'CatType' => $account['cattype'],
                        'RevCode' => $account['revcode'] . $propertyid,
                        'RoundOff' => 'No',
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'ActiveYN' => 'Y',
                        'DrCr' => 'Dr',
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS7($propertyid, $u_name, $jsonData)
    {
        $tablename = 'usermodule';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['code'];

                if (!in_array($unitcode, $existingCodes)) {
                    $insertData = [
                        'code' => $unitcode,
                        'module' => $category,
                        'module_name' => 'Pointofsale',
                        'route' => $account['route'] . $propertyid,
                        'outletcode' => 'RS' . $propertyid,
                        'flag' => $account['flag'],
                        'opt1' => $account['opt1'],
                        'opt2' => $account['opt2'],
                        'opt3' => $account['opt3'],
                        'propertyid' => $propertyid,
                        'u_entdt' => $currenttime->getCurrentTime(),
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }

    public static function InsertRoomS8($propertyid, $compcode, $u_name, $jsonData)
    {
        $tablename = 'menuhelp';
        $currenttime = new CompanyLog();

        $existingRecords = DB::table($tablename)
            ->where('propertyid', $propertyid)
            ->get();
        $existingCodes = $existingRecords->pluck('code')->toArray();
        $recordsInsertedCount = 0;
        foreach ($jsonData as $data) {
            foreach ($data as $category => $account) {
                $unitcode = $account['code'];

                if (!in_array($unitcode, $existingCodes)) {
                    $insertData = [
                        'compcode' => $compcode,
                        'code' => $unitcode,
                        'module' => $category,
                        'module_name' => 'Pointofsale',
                        'route' => $account['route'] . $propertyid,
                        'outletcode' => 'RS' . $propertyid,
                        'flag' => $account['flag'],
                        'opt1' => $account['opt1'],
                        'opt2' => $account['opt2'],
                        'opt3' => $account['opt3'],
                        'ins' => 1,
                        'edit' => 1,
                        'del' => 1,
                        'print' => 1,
                        'propertyid' => $propertyid,
                        'u_name' => $u_name,
                        'u_entdt' => $currenttime->getCurrentTime(),
                        'username' => $u_name,
                    ];

                    DB::table($tablename)->insert($insertData);
                    $recordsInsertedCount++;
                }
            }
        }

        return $recordsInsertedCount;
    }
}
