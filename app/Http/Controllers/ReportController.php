<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Bookings;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Guestfolio;
use App\Models\Suntran;
use App\Models\Sale1;
use App\Models\Sale2;
use App\Models\Stock;
use App\Models\SubGroup;
use App\Models\MenuHelp;
use App\Models\Paycharge;
use App\Models\Companyreg;
use App\Models\RoomOcc;
use App\Models\FomBillDetail;
use App\Models\BussSource;
use App\Models\EnviroFom;
use App\Models\Depart;
use App\Models\EnviroGeneral;
use App\Models\EnviroInventory;
use App\Models\Focc;
use App\Models\GrpBookinDetail;
use App\Models\ItemCatMast;
use App\Models\ItemMast;
use App\Models\Kot;
use App\Models\PaychargeH;
use App\Models\Revmast;
use App\Models\RoomCat;
use App\Models\RoomMast;
use App\Models\States;
use App\Models\TaxStructure;
use App\Models\VoucherType;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Ui\Presets\React;
use Laravel\Ui\UiCommand;
use Monolog\Formatter\GoogleCloudLoggingFormatter;
use Monolog\Handler\FlowdockHandler;
use Symfony\Component\CssSelector\Parser\Handler\NumberHandler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Transport\Dsn;
use Illuminate\Support\Facades\Log;
use Spatie\FlareClient\Report;

use function PHPSTORM_META\type;
use function Termwind\ask;

class ReportController extends Controller
{


    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $ncurdate;

    ///////////////////////////  Deepak Code Repport //////////////////////////

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!isset(Auth::user()->name)) {
                return redirect('index.php');
            }

            $this->username = Auth::user()->name;
            $this->email = Auth::user()->email;
            $this->prpid = Auth::user()->propertyid;
            $propertydata = DB::table('users')->where('propertyid', $this->prpid)->first();
            $this->ncurdate = DB::table('enviro_general')->where('propertyid', Auth::user()->propertyid)->value('ncur');
            $this->propertyid = $propertydata->propertyid;
            $this->ptlngth = strlen($this->propertyid);
            date_default_timezone_set('Asia/Kolkata');
            $this->currenttime = date('Y-m-d H:i:s');
            return $next($request);
        });
    }
    public function ncurfetch()
    {
        $ncurdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        return $ncurdate;
        $paycharge = Paycharge::$encrypter->value;
    }

    public function dailyFunctionSheet(Request $request)
    {
        $permission = revokeopen(141213);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $fromdate = $this->ncurdate;
        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
        $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
        $distinctuname = Paycharge::where('propertyid', $this->propertyid)->where('modeset', 'S')->distinct('u_name')->get(['u_name']);
        return view('property.dailyfunctionsheet', [
            'fromdate' => $fromdate,
            'statename' => $statename,
            'distinctuname' => $distinctuname,
            'company' => $company,
            'revheading' => $revheading
        ]);
    }

    public function dailyFunctionSheetData(Request $request)
    {

        // try {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $type = $request->input('type');
        $propertyid = $this->propertyid;

        if ($type == 1) {
            $query = $this->getFunctionData($fromdate, $todate, $propertyid);
        } else if ($type == 2) {
            $query = $this->getPendingData($fromdate, $todate, $propertyid);
        } else if ($type == 3) {
            $query = $this->getAdvanceData($fromdate, $todate, $propertyid);
        } else {
            // For type 3 or any other invalid type, return zero records
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }


        // DataTables global search filter
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('S.vno', 'like', "%{$search}%")
                  ->orWhere('VM.name', 'like', "%{$search}%")
                  ->orWhere('VO.fromdate', 'like', "%{$search}%")
                  ->orWhere('VO.dromtime', 'like', "%{$search}%")
                  ->orWhere('VO.totime', 'like', "%{$search}%")
                  ->orWhere('S.guaratt', 'like', "%{$search}%")
                  ->orWhere('S.coverrate', 'like', "%{$search}%")
                  ->orWhere('functiontype.name', 'like', "%{$search}%")
                  ->orWhere('S.partyname', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $data = $query->offset($start)->limit($length)->get();

        $result = [];
        $sno = $start + 1;
        $lastFpno = null;

        foreach ($data as $row) {
            $advances = $this->getAdvanceDetails($row->docid, $propertyid);
            $currentFpno = $row->vno;
            $showSno = ($currentFpno !== $lastFpno) ? $sno++ : '';
            $lastFpno = $currentFpno;
            if ($advances->isNotEmpty()) {
                $first = true;
                foreach ($advances as $advance) {
                    $result[] = [
                        'sno'           => $first ? $showSno : '',
                        'fpno'          => $row->vno,
                        'venue'         => $row->Venue ?? '',
                        'function_date' => $row->fromdate ?? '',
                        'for_time'      => $row->ForTime ?? '',
                        'to_time'       => $row->ToTime ?? '',
                        'pax'           => $row->Pax ?? '',
                        'pax_rate'      => $row->Rate ?? '',
                        'function_type' => $row->FuncType ?? '',
                        'party_name'    => $row->PartyName ?? '',
                        'advance'       => $advance->Advance ?? '',
                        'type'          => $advance->Adv_Type ?? '',
                        'rect_no'       => $advance->Adv_No ?? '',
                        'rect_date'     => $advance->Adv_Date ?? '',
                    ];
                    $first = false;
                }
            } else {
                $result[] = [
                    'sno'           => $showSno,
                    'fpno'          => $row->vno,
                    'venue'         => $row->Venue ?? '',
                    'function_date' => $row->fromdate ?? '',
                    'for_time'      => $row->ForTime ?? '',
                    'to_time'       => $row->ToTime ?? '',
                    'pax'           => $row->Pax ?? '',
                    'pax_rate'      => $row->Rate ?? '',
                    'function_type' => $row->FuncType ?? '',
                    'party_name'    => $row->PartyName ?? '',
                    'advance'       => '',
                    'type'          => '',
                    'rect_no'       => '',
                    'rect_date'     => '',
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $result
        ]);
    }


    private function getAdvanceDetails($contradocid, $propertyid)
    {
        $query = DB::table('paychargeh as PH')
            ->select([
                'PH.sno',
                'PH.vno as Adv_No',
                'PH.vdate as Adv_Date',
                DB::raw("CASE WHEN PH.Vtype = 'AD' THEN PH.AmtCr ELSE -PH.AmtDr END as Advance"),
                'PH.paytype as Adv_Type',
            ])
            ->whereIn('PH.vtype', ['AD', 'AR'])
            ->where('PH.restcode', 'BANQ' . $propertyid)
            ->where('PH.contradocid', $contradocid)
            ->where('PH.sno', 1)
            ->orderBy('PH.vdate')
            ->orderBy('PH.vno')
            ->orderBy('PH.sno');

        return $query->get();
    }


    private function getFunctionData($fromdate, $todate, $propertyid)
    {
        $query = DB::table('hallbook as S')
            ->select([
                'S.docid',
                'S.vno',
                DB::raw("CONCAT(
                IF(TRIM(IFNULL(S.mobileno, '')) <> '', CONCAT(TRIM(S.mobileno), ', '), ''),
                IF(TRIM(IFNULL(S.mobileno1, '')) <> '', CONCAT(TRIM(S.mobileno1), ', '), '')
            ) as ContactNo"),
                'VM.name as Venue',
                'S.guaratt as Pax',
                'S.coverrate as Rate',
                'S.partyname as PartyName',
                'functiontype.name as FuncType',
                'VO.fromdate',
                'VO.dromtime as ForTime',
                'VO.todate',
                'VO.totime as ToTime',
            ])
            ->leftJoin('functiontype', 'S.Func_Name', '=', 'functiontype.code')
            ->join('venueocc as VO', 'S.DocId', '=', 'VO.fpdocid')
            ->join('venuemast as VM', 'VO.VenuCode', '=', 'VM.code')
            ->where('S.restcode', 'BANQ' . $propertyid)
            ->where('S.propertyid', $propertyid)
            ->whereBetween('VO.fromdate', [
                DB::raw("STR_TO_DATE('$fromdate', '%Y-%m-%d')"),
                DB::raw("STR_TO_DATE('$todate', '%Y-%m-%d')")
            ])
            ->orderBy('VO.fromdate')
            ->orderBy('VO.dromtime');

        return $query;
    }

    private function getPendingData($fromdate, $todate, $propertyid)
    {
        $query = DB::table('hallbook as S')
            ->select([
                'S.docid',
                'S.vno',
                DB::raw("CONCAT(
                IF(TRIM(IFNULL(S.mobileno, '')) <> '', CONCAT(TRIM(S.mobileno), ', '), ''),
                IF(TRIM(IFNULL(S.mobileno1, '')) <> '', CONCAT(TRIM(S.mobileno1), ', '), '')
            ) as ContactNo"),
                'VM.name as Venue',
                'S.guaratt as Pax',
                'S.coverrate as Rate',
                'S.partyname as PartyName',
                'functiontype.name as FuncType',
                'VO.fromdate',
                'VO.dromtime as ForTime',
                'VO.todate',
                'VO.totime as ToTime',
            ])
            ->leftJoin('functiontype', 'S.Func_Name', '=', 'functiontype.code')
            ->join('venueocc as VO', 'S.DocId', '=', 'VO.fpdocid')
            ->join('venuemast as VM', 'VO.VenuCode', '=', 'VM.code')
            ->where('S.restcode', 'BANQ' . $propertyid)
            ->where('S.propertyid', $propertyid)
            ->whereNotIn('S.docid', function ($subquery) use ($propertyid) {
                $subquery->select('bookdocid')
                    ->from('hallsale1')
                    ->where('restcode', 'BANQ.' . $propertyid)
                    ->where('propertyid', $propertyid);
            })
            ->whereBetween('VO.fromdate', [
                DB::raw("STR_TO_DATE('$fromdate', '%Y-%m-%d')"),
                DB::raw("STR_TO_DATE('$todate', '%Y-%m-%d')")
            ])
            ->orderBy('VO.fromdate')
            ->orderBy('VO.dromtime');

        return $query;
    }

    private function getAdvanceData($fromdate, $todate, $propertyid)
    {
        $query = DB::table('hallbook as S')
            ->select([
                'S.docid',
                'S.vno',
                DB::raw("CONCAT(
                IF(TRIM(IFNULL(S.mobileno, '')) <> '', CONCAT(TRIM(S.mobileno), ', '), ''),
                IF(TRIM(IFNULL(S.mobileno1, '')) <> '', CONCAT(TRIM(S.mobileno1), ', '), '')
            ) as ContactNo"),
                'VM.name as Venue',
                'VO.fromdate',
                'VO.dromtime as ForTime',
                'VO.todate',
                'VO.totime as ToTime',
                'S.guaratt as Pax',
                'S.coverrate as Rate',
                'S.partyname as PartyName',
                'functiontype.name as FuncType',
                // 'PH.amtcr as Advance',
                // 'PH.paytype as Adv_Type',
                // 'PH.vno as Adv_No',
                // 'PH.vdate as Adv_Date',
            ])
            ->leftJoin('functiontype', 'S.func_name', '=', 'functiontype.Code')
            ->join('venueocc as VO', 'S.DocId', '=', 'VO.fpdocid')
            ->join('venuemast as VM', 'VO.VenuCode', '=', 'VM.code')
            ->leftJoin('paychargeh as PH', function ($join) {
                $join->on('S.DocId', '=', 'PH.contradocid')
                    ->where('PH.VType', '=', 'AD');
            })
            ->where('S.restcode', 'BANQ' . $propertyid)
            ->where('S.propertyid', $propertyid)
            // ->where('PH.sno', 1)
            ->whereBetween('VO.fromdate', [
                DB::raw("STR_TO_DATE('$fromdate', '%Y-%m-%d')"),
                DB::raw("STR_TO_DATE('$todate', '%Y-%m-%d')")
            ])
            ->orderBy('VO.fromdate')
            ->orderBy('VO.dromtime');
            // ->orderBy('PH.vdate')
            // ->orderBy('PH.vno')
            // ->orderBy('PH.sno');

        return $query;
    }


    public function bookingEnquiryDetail(Request $request)
    {
        $permission = revokeopen(141213);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $fromdate = $this->ncurdate;
        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
        $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
        $distinctuname = Paycharge::where('propertyid', $this->propertyid)->where('modeset', 'S')->distinct('u_name')->get(['u_name']);
        return view('property.bookinginquirydetail', [
            'fromdate' => $fromdate,
            'statename' => $statename,
            'distinctuname' => $distinctuname,
            'company' => $company,
            'revheading' => $revheading
        ]);
    }

    public function bookingEnquiryDetailFetch(Request $request)
    {

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fromDate = $request->input('fromdate'); // ex: '2025-03-31'
        $toDate = $request->input('todate');     // ex: '2026-03-31'
        $status = $request->input('status');     // ex: 'Active'
        $propertyId = $this->propertyid;

       // print_r($status);

        $query = DB::table('bookinginquiry as B')
            ->join('bookingdetail as BD', 'B.inqno', '=', 'BD.inqno')
            ->leftJoin('functiontype as F', 'B.functype', '=', 'F.code')
            ->leftJoin('venuemast as V', 'BD.venuecode', '=', 'V.code')
            ->select(
                'B.inqno',
                'BD.fromdate',
                'BD.todate',
                'BD.fromtime',
                'BD.totime',
                'F.Name as FunctionType',
                'BD.venuecode',
                'V.Name as VenueName',
                'B.partyname',
                'B.mobileno',
                'B.conperson',
                'B.bookedby',
                'B.handledby',
                'B.status',
                'B.u_name',
                'B.remark'
            )
            ->where('B.cattype', 'Indoor')
            ->where('B.propertyid', $propertyId)
            ->whereBetween('BD.fromdate', [$fromDate, $toDate]);

        // Status filter logic
        if ($status === 'Active') {
            $query->where('B.status', 'Active');
        } elseif ($status === 'Inactive') {
            $query->where('B.status', 'Inactive');
        } 

        // DataTables server-side pagination, search, and ordering
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('B.inqno', 'like', "%{$search}%")
                    ->orWhere('B.partyname', 'like', "%{$search}%")
                    ->orWhere('B.mobileno', 'like', "%{$search}%")
                    ->orWhere('V.Name', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $data = $query
            ->orderBy('BD.fromdate')
            ->orderBy('BD.fromtime')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }
}
