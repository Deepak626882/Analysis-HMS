<?php

namespace App\Http\Controllers;

use App\Models\Billprintthermal;
use App\Models\Companyreg;
use App\Models\EnviroPos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kot;
use App\Models\PrintDelay;
use App\Models\PrintingSetup;
use App\Models\Sagar;
use App\Models\Sale1;
use App\Models\Stock;
use Exception;

class PythonAuth extends Controller
{

    public function login(Request $request)
    {
        $data = $request->json()->all();

        $request->validate([
            'username' => 'required|string',
            'property_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $data['username'])
            ->where('propertyid', $data['property_id'])
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'Account does not exist',
            ]);
        }

        if ($user->status !== 1) {
            return response()->json([
                'status' => 'error',
                'Account is not active',
            ]);
        }

        if ($user && Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
    }

    public function getproperty(Request $request)
    {
        $data = $request->json()->all();

        $request->validate([
            'property_id' => 'required|string',
            'username' => 'required|string',
        ]);


        $user = User::where('name', $data['username'])
            ->where('propertyid', $data['property_id'])
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account does not exist',
            ]);
        }

        if ($user->status !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account is not active',
            ]);
        }

        try {

            $compdata = Companyreg::where('propertyid', $user->propertyid)
                // ->where('u_name', $user->name)
                // ->whereIn('role', ['Property', 'User'])
                ->first();

            if ($compdata) {
                return response()->json([
                    'status' => 'success',
                    'data' => $compdata,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Company Data Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unknown Error Occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function fetchprintdata(Request $request)
    {
        $request->validate([
            'property_id' => 'required|string'
        ]);
        $data = $request->json()->all();

        $printdelay = PrintDelay::where('propertyid', $data['property_id'])->orderBy('sn', 'DESC')->first();
        if ($printdelay) {
            try {
                $enviropos = EnviroPos::where('propertyid', $printdelay->propertyid)->first();
                $company = Companyreg::where('propertyid', $printdelay->propertyid)->first();
                $printdata = PrintDelay::select('printdelay.*', 'depart.name as kitchenname')
                    ->leftJoin('depart', 'depart.dcode', '=', 'printdelay.kitchen')
                    ->where('printdelay.propertyid', $printdelay->propertyid)->where('printdelay.docid', $printdelay->docid)
                    ->orderBy('printdelay.kitchen', 'ASC')->get();
                $kotdetail = Kot::select('kot.*', 'server_mast.name as waitername', 'depart.name as departname')
                    ->leftJoin('depart', 'depart.dcode', '=', 'kot.restcode')
                    ->leftJoin('server_mast', 'server_mast.scode', '=', 'kot.waiter')
                    ->where('kot.docid', $printdelay->docid)->where('kot.propertyid', $printdelay->propertyid)->first();
                $printerpath = PrintingSetup::where('restcode', $printdelay->restcode)->where('propertyid', $printdelay->propertyid)->get();

                $roomno = $kotdetail->roomno;
                $kotdocid = $kotdetail->docid;

                $chk = Kot::whereNot('docid', $printdelay->docid)->where('propertyid', $data['property_id'])->where('roomno', $roomno)->first();

                $kottype = $kotdetail->nckot == 'Y' ? 'NC KOT' : 'KOT';

                if (is_null($chk)) {
                    $ordertype = 'New Order';
                } else {
                    $ordertype = 'Running Order';
                }

                $ordertype = $kottype == 'NC KOT' ? '' : $ordertype;

                $data = [
                    'kottype' => $kottype,
                    'ordertype' => $ordertype,
                    'printdata' => $printdata,
                    'kotdetail' => $kotdetail,
                    'printerpath' => $printerpath,
                    'enviropos' => $enviropos,
                    'company' => $company
                ];

                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown Error Occurred: ' . $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No data found'
        ]);
    }

    public function deleteprintdata(Request $request)
    {
        $data = $request->json()->all();

        $request->validate([
            'property_id' => 'required|string',
            'docid' => 'required|string'
        ]);

        $kot = Kot::where('propertyid', $data['property_id'])->where('docid', $data['docid'])->first();

        if ($kot) {
            try {
                PrintDelay::where('propertyid', $kot->propertyid)->where('docid', $kot->docid)->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Queue Data Deleted Successfully'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown Error Occurred: ' . $e->getMessage(),
                ]);
            }
        }
    }

    public function deleteprintdatabill(Request $request)
    {
        $data = $request->json()->all();

        $request->validate([
            'property_id' => 'required|string',
            'docid' => 'required|string'
        ]);

        $sale1 = Sale1::where('propertyid', $data['property_id'])->where('docid', $data['docid'])->first();

        if ($sale1) {
            try {
                Billprintthermal::where('propertyid', $sale1->propertyid)->where('docid', $sale1->docid)->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Queue Data Deleted Successfully'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown Error Occurred: ' . $e->getMessage(),
                ]);
            }
        }
    }

    public function fetchprintdatabill(Request $request)
    {
        try {
            $data = $request->json()->all();

            $billprint = Billprintthermal::where('propertyid', $data['property_id'])
                ->orderByDesc('sn')
                ->get();

            if ($billprint) {
                return $billprint;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No data found'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
