<?php

use App\Models\Cities;
use App\Models\Companyreg;
use App\Models\EnviroBanquet;
use App\Models\EnviroFom;
use App\Models\EnviroGeneral;
use App\Models\FunctionType;
use App\Models\HallBook;
use App\Models\HallSale1;
use App\Models\MenuHelp;
use App\Models\PlanMast;
use App\Models\RoomOcc;
use App\Models\SubGroup;
use App\Models\VenueMast;
use App\Models\VenueOcc;
use App\Models\VoucherPrefix;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('companydata')) {
    function companydata()
    {
        return Companyreg::where('propertyid', Auth::user()->propertyid)->first();
    }
}


if (!function_exists('ncurdate')) {
    function ncurdate()
    {
        return EnviroGeneral::where('propertyid', Auth::user()->propertyid)->value('ncur');
    }
}

if (!function_exists('revokefunction')) {
    function revokeopen($code)
    {
        return MenuHelp::where([
            ['propertyid', '=', Auth::user()->propertyid],
            ['username', '=', Auth::user()->name],
            ['code', '=', $code]
        ])->first();
    }
}

if (!function_exists('getMonthYearCode')) {
    function getMonthYearCode($date)
    {
        $timestamp = strtotime($date);
        return date('mY', $timestamp);
    }
}

if (!function_exists('calculateTax')) {
    function calculateTax($amount, $taxPercent)
    {
        return ($amount * $taxPercent) / 100;
    }
}

if (!function_exists('getDayNameFromDate')) {
    function getDayNameFromDate($date)
    {
        if (!$date) return '';

        try {
            $dateObj = new DateTime($date);
            return $dateObj->format('l');
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('maxvno')) {
    function maxvno($vtype, $tbl)
    {
        $maxvno = DB::table($tbl)->where('vtype', $vtype)->max('vno');

        if (is_null($maxvno)) {
            return 1;
        } else {
            return $maxvno + 1;
        }
    }
}

if (!function_exists('allcities')) {
    function allcities()
    {
        $citydata = Cities::where('propertyid', Auth::user()->propertyid)->where('activeyn', '1')
            ->orderBy('cityname', 'ASC')->get();

        return $citydata;
    }
}

if (!function_exists('travelagents')) {
    function travelagents()
    {
        $travelagent = SubGroup::where('propertyid', Auth::user()->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();

        return $travelagent;
    }
}

if (!function_exists('functiontypes')) {
    function functiontypes()
    {
        $data = FunctionType::where('propertyid', Auth::user()->propertyid)
            ->orderBy('name', 'ASC')->get();

        return $data;
    }
}

if (!function_exists('companiessubgroup')) {
    function companiessubgroup()
    {
        $data = DB::table('subgroup')
            ->where('propertyid', Auth::user()->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();

        return $data;
    }
}

if (!function_exists('subgroup')) {
    function subgroup($sub_code)
    {
        $data = SubGroup::select(
            'subgroup.*',
            'cities.cityname',
            'states.name as statename',
            'states.state_code'
        )
            ->leftJoin('cities', function ($join) {
                $join->on('cities.city_code', '=', 'subgroup.citycode')
                    ->where('cities.propertyid', Auth::user()->propertyid);
            })
            ->leftJoin('states', function ($join) {
                $join->on('states.state_code', '=', 'cities.state')
                    ->where('states.propertyid', Auth::user()->propertyid);
            })
            ->where('subgroup.propertyid', Auth::user()->propertyid)->where('subgroup.sub_code', $sub_code)->first();

        return $data;
    }
}

if (!function_exists('venuemast')) {
    function venuemast()
    {
        $data = VenueMast::where('propertyid', Auth::user()->propertyid)->orderBy('name', 'ASC')->get();

        return $data;
    }
}

if (!function_exists('banquetparameter')) {
    function banquetparameter()
    {
        $data = EnviroBanquet::where('propertyid', Auth::user()->propertyid)->first();

        return $data;
    }
}

if (!function_exists('fomparameter')) {
    function fomparameter()
    {
        $data = EnviroFom::where('propertyid', Auth::user()->propertyid)->first();

        return $data;
    }
}


if (!function_exists('hallbook')) {
    function hallbook()
    {
        $hallbook = HallBook::select(
            'cities.cityname',
            'hallbook.*',
            'venuemast.name as venuename',
            DB::raw("IFNULL(SUM(paychargeh.amtcr), 0) as advancesum")
        )
            ->leftJoin('paychargeh', 'paychargeh.contradocid', '=', 'hallbook.docid')
            ->leftJoin('cities', 'cities.city_code', '=', 'hallbook.city')
            ->leftJoin('venuemast', 'venuemast.code', '=', 'hallbook.func_name')
            ->where('hallbook.propertyid', Auth::user()->propertyid)
            ->groupBy('hallbook.docid')
            ->orderByDesc('hallbook.vno')
            ->get();


        return $hallbook;
    }
}

if (!function_exists('hallbookvenue')) {
    function hallbookvenue()
    {
        $hallbook = VenueOcc::select(
            'cities.cityname',
            'hallbook.*',
            'venuemast.name as venuename',
            'venueocc.*',
            DB::raw("IFNULL(SUM(paychargeh.amtcr), 0) as advancesum")
        )
            ->leftJoin('hallbook', 'hallbook.docid', '=', 'venueocc.fpdocid')
            ->leftJoin('paychargeh', function ($join) {
                $join->on('paychargeh.contradocid', '=', 'hallbook.docid')
                    ->where('paychargeh.sno', '1');
            })
            ->leftJoin('cities', 'cities.city_code', '=', 'hallbook.city')
            ->leftJoin('venuemast', 'venuemast.code', '=', 'venueocc.venucode')
            ->where('hallbook.propertyid', Auth::user()->propertyid)
            ->groupBy('venueocc.fpdocid')
            ->groupBy('venueocc.sno')
            ->orderByDesc('hallbook.vno')
            ->get();


        return $hallbook;
    }
}

if (!function_exists('hallbookbill')) {
    function hallbookbill()
    {
        $hallbook = HallBook::select(
            'cities.cityname',
            'hallbook.*',
            'venuemast.name as venuename',
            DB::raw("IFNULL(SUM(paychargeh.amtcr), 0) as advancesum")
        )
            ->leftJoin('paychargeh', 'paychargeh.contradocid', '=', 'hallbook.docid')
            ->leftJoin('cities', 'cities.city_code', '=', 'hallbook.city')
            ->leftJoin('venuemast', 'venuemast.code', '=', 'hallbook.func_name')
            ->where('hallbook.propertyid', Auth::user()->propertyid)
            ->whereNotIn('hallbook.docid', function ($query) {
                $query->select('bookdocid')->from('hallsale1');
            })
            ->groupBy('hallbook.docid')
            ->orderByDesc('hallbook.vno')
            ->get();

        return $hallbook;
    }
}

if (!function_exists('subgroupall')) {
    function subgroupall()
    {
        $data = DB::table('subgroup')
            ->where('propertyid', Auth::user()->propertyid)->orderBy('name', 'ASC')->get();

        return $data;
    }
}



if (!function_exists('oldbanqutbillnos')) {
    function oldbanqutbillnos()
    {
        $data = HallSale1::where('propertyid', Auth::user()->propertyid)->orderByDesc('vno')->get();

        return $data;
    }
}


if (!function_exists('companydata')) {
    function companydata()
    {
        $companydata = DB::table('company')->where('propertyid', Auth::user()->propertyid)->first();

        return $companydata;
    }
}

if (!function_exists('bookedroomslist')) {
    function bookedroomslist()
    {
        $roomno = RoomOcc::leftJoin('paycharge', function ($join) {
            $join->on('paycharge.roomno', '=', 'roomocc.roomno')
                ->on('paycharge.propertyid', '=', 'roomocc.propertyid');
        })
            ->where('roomocc.propertyid', Auth::user()->propertyid)
            ->where(function ($query) {
                $query->where('paycharge.billno', 0)
                    ->orWhereNull('paycharge.billno');
            })
            ->whereNull('roomocc.type')
            ->groupBy('roomocc.roomno')
            ->orderBy('roomocc.roomno')
            ->select('roomocc.roomno')
            ->get();

        return $roomno;
    }
}


function convertGroup($number, $ones, $tens)
{
    if ($number === 0) {
        return '';
    }

    if ($number < 20) {
        return $ones[$number];
    }

    if ($number < 100) {
        $ten = floor($number / 10);
        $one = $number % 10;
        return $tens[$ten] . ($one > 0 ? '-' . $ones[$one] : '');
    }

    $hundred = floor($number / 100);
    $remainder = $number % 100;
    $parts = [];

    if ($hundred > 0) {
        $parts[] = $ones[$hundred] . ' hundred';
    }

    if ($remainder > 0) {
        $parts[] = convertGroup($remainder, $ones, $tens);
    }

    return implode(' ', $parts);
}

function amountToWords($amount)
{
    // Split number into whole and decimal parts
    $parts = explode('.', (string)$amount);
    $wholeNumber = (int)$parts[0];
    $decimal = isset($parts[1]) ? str_pad($parts[1], 2, '0', STR_PAD_RIGHT) : '00';

    $ones = [
        0 => '',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen'
    ];

    $tens = [
        2 => 'twenty',
        3 => 'thirty',
        4 => 'forty',
        5 => 'fifty',
        6 => 'sixty',
        7 => 'seventy',
        8 => 'eighty',
        9 => 'ninety'
    ];

    $scales = [
        '',
        'thousand',
        'million',
        'billion',
        'trillion'
    ];

    // Handle whole number part
    if ($wholeNumber === 0) {
        $result = 'zero';
    } else {
        $groups = [];
        $numStr = (string)$wholeNumber;
        $padLength = ceil(strlen($numStr) / 3) * 3;
        $numStr = str_pad($numStr, $padLength, '0', STR_PAD_LEFT);
        $groupArray = str_split($numStr, 3);

        foreach ($groupArray as $i => $group) {
            $groupNum = (int)$group;
            if ($groupNum === 0) {
                continue;
            }

            $scaleKey = count($groupArray) - $i - 1;
            $groupWords = convertGroup($groupNum, $ones, $tens);

            if (!empty($groupWords)) {
                $groups[] = $groupWords . ($scaleKey > 0 ? ' ' . $scales[$scaleKey] : '');
            }
        }

        $result = implode(' ', $groups);
    }

    // Handle decimal part
    //if ($decimal !== '00') {
    //$result .= ' and ' . $decimal . '/100';
    //} else {
    //$result .= ' and 00/100';
    //}

    return ucfirst($result);
}


function myproperties()
{
    $sn_num = Companyreg::where('propertyid', Auth::user()->propertyid)->value('sn_num');

    $data = Companyreg::select('company.propertyid', 'company.comp_name', 'users.id as userid', 'users.u_name')
        ->leftJoin('users', function ($join) {
            $join->on('users.propertyid', '=', 'company.propertyid');
            // ->where('users.u_name', 'sa');
        })
        ->where('company.sn_num', $sn_num)
        ->groupBy('company.propertyid')
        ->get();

    return $data;
}


function distinctyear()
{
    return VoucherPrefix::select('prefix')
        ->where('propertyid', Auth::user()->propertyid)
        ->distinct()
        ->orderByDesc('prefix')
        ->get();
}


function envirogeneral()
{
    return EnviroGeneral::where('propertyid', Auth::user()->propertyid)->first();
}


function allproperties()
{
    return Companyreg::groupBy('propertyid')->get();
}

function calculateRoundOff($amount, $mode = 'Standard')
{
    $paise = $amount - floor($amount);

    if ($mode === 'Standard') {
        $rounded = ($paise < 0.50) ? floor($amount) : ceil($amount);
    } elseif ($mode === 'Upper') {
        $rounded = ceil($amount);
    } else {
        $rounded = round($amount); // fallback
    }

    // convert to decimal (rupees instead of paise)
    $roundoff = round($rounded - $amount, 2);

    return [
        'billamt'  => $rounded,
        'roundoff' => $roundoff
    ];
}


function planbasedcategory($catcode)
{
    $plans = PlanMast::where('propertyid', Auth::user()->propertyid)->where('room_cat', $catcode)->where('activeYN', 'Y')->orderBy('name')->get();
    return $plans;
}
