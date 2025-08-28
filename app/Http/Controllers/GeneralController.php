<?php

namespace App\Http\Controllers;

use App\Models\EnviroBanquet;
use App\Models\EnviroFom;
use App\Models\EnviroInventory;
use App\Models\EnviroPos;
use App\Models\PlanMast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    public function calculateroundoffpos(Request $request)
    {
        $envfom = EnviroPos::where('propertyid', Auth::user()->propertyid)->first();
        $mode = $envfom->roundofftype;

        $results = [];
        foreach ($request->amounts as $row) {
            $res = calculateRoundOff($row['amount'], $mode);
            $results[] = [
                'outlet' => $row['outlet'],
                'billamt' => $res['billamt'],
                'roundoff' => $res['roundoff']
            ];
        }
        return response()->json($results);
    }


    public function calculateroundpurch(Request $request)
    {
        $envfom = EnviroInventory::where('propertyid', Auth::user()->propertyid)->first();
        $amount = $request->amount;

        $data = calculateRoundOff($amount, $envfom->roundofftype);

        return response()->json($data);
    }

    public function calculateroundbanquet(Request $request)
    {
        $envfom = EnviroBanquet::where('propertyid', Auth::user()->propertyid)->first();
        $mode = $envfom->roundofftype;

        $res = calculateRoundOff($request->amount, $mode);
        $results = [
            'restcode' => $request->restcode,
            'billamt' => $res['billamt'],
            'roundoff' => $res['roundoff']
        ];
        return response()->json($results);
    }

    public function fetchallemptyrooms(Request $request)
    {
        $propertyid = Auth::user()->propertyid;
        $checkindate = $request->checkindate;
        $checkoutdate = $request->checkoutdate;

        $roommast = DB::table('room_mast as rm')
            ->select('rm.rcode', 'rm.room_cat')
            ->where('rm.propertyid', $propertyid)
            ->where('rm.type', 'RO')
            ->where('rm.inclcount', 'Y')
            ->whereNotIn('rm.rcode', function ($query) use ($propertyid, $checkindate, $checkoutdate) {
                $query->select('ro.roomno')
                    ->from('roomocc as ro')
                    ->where('ro.propertyid', $propertyid)
                    ->whereNull('ro.type')
                    ->where('ro.chkindate', '<', $checkoutdate)
                    ->where('ro.depdate', '>=', $checkindate);
            })
            ->whereNotIn('rm.rcode', function ($query) use ($propertyid, $checkindate, $checkoutdate) {
                $query->select('gb.RoomNo')
                    ->from('grpbookingdetails as gb')
                    ->where('gb.Property_ID', $propertyid)
                    ->where('gb.ArrDate', '<', $checkoutdate)
                    ->where('gb.DepDate', '>', $checkindate)
                    ->where('gb.chkoutyn', 'N')
                    ->where('gb.Cancel', 'N')
                    ->where('gb.RoomNo', '!=', 0);
            })
            ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $checkoutdate) {
                $query->select('rb.roomcode')
                    ->from('roomblockout as rb')
                    ->where('rb.fromdate', '<', $checkoutdate)
                    ->where('rb.todate', '>', $checkindate)
                    ->where('rb.type', 'O');
            })
            ->groupBy('rm.rcode', 'rm.room_cat')
            ->get();

        return response()->json($roommast);
    }

    public function planfetchbycat(Request $request)
    {
        $roomcat = $request->roomcat;
        $plans = PlanMast::where('propertyid', Auth::user()->propertyid)->where('room_cat', $roomcat)->where('activeYN', 'Y')->orderBy('name')->get();
        return response()->json($plans);
    }
}
