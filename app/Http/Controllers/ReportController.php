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
use App\Models\HallSale1;
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

        if ($type == 3) {
            // Sort $data by fpno (vno) so all same fpno rows are together
            $data = $data->sortBy('vno')->values();
            $fpnoMap = [];
        }

        foreach ($data as $row) {
            if ($type == 3) {
                $currentFpno = $row->vno;
                if (!isset($fpnoMap[$currentFpno])) {
                    $fpnoMap[$currentFpno] = $sno++;
                    $showSno = $fpnoMap[$currentFpno];
                } else {
                    $showSno = '';
                }
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
                    'advance'       => $row->Advance ?? '',
                    'type'          => $row->Adv_Type ?? '',
                    'rect_no'       => $row->Adv_No ?? '',
                    'rect_date'     => $row->Adv_Date ?? '',
                ];
            } else {
                $currentFpno = $row->vno;
                $showSno = ($currentFpno !== $lastFpno) ? $sno++ : '';
                $lastFpno = $currentFpno;
                $advances = $this->getAdvanceDetails($row->docid, $propertyid);

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
                    ->where('restcode', 'BANQ' . $propertyid)
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
                'PH.amtcr as Advance',
                'PH.paytype as Adv_Type',
                'PH.vno as Adv_No',
                'PH.vdate as Adv_Date',
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
            ->where('PH.sno', 1)
            ->whereBetween('VO.fromdate', [
                DB::raw("STR_TO_DATE('$fromdate', '%Y-%m-%d')"),
                DB::raw("STR_TO_DATE('$todate', '%Y-%m-%d')")
            ])
            // ->orderBy('VO.fromdate')
            // ->orderBy('VO.dromtime');
            ->orderBy('PH.vdate')
            ->orderBy('PH.vno')
            ->orderBy('PH.sno');

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

    public function outStandingreport(Request $request)
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
        return view('property.outstandingreport', [
            'fromdate' => $fromdate,
            'statename' => $statename,
            'distinctuname' => $distinctuname,
            'company' => $company,
            'revheading' => $revheading
        ]);
    }

    public function outStandingreportData(Request $request)
    {

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $propertyid = $this->propertyid;


        $query = DB::table('hallsale1 as H')
            ->select([
                'H.docId',
                'H.vdate as BookDate',
                'V.fromdate as FuncStartDate',
                'V.todate as FuncEndDate',
                'V.dromtime as FuncStartTime',
                'V.totime as FuncEndTime',
                'F.name as FuncName',
                'H.party as PartyName',
                'S.vno as BillNo',
                'SH.vdate as BillDate',
                DB::raw('(IFNULL(SH.Amount,0) + IFNULL(S.Amount,0)) as Amount'),
                'H.BookDocID'
            ])
            ->leftJoin('paychargeh as P', 'H.DocId', '=', 'P.DocId')
            ->leftJoin('suntran as S', 'H.DocId', '=', 'S.DocId')
            ->leftJoin('suntranh as SH', 'H.DocId', '=', 'SH.DocId')
            ->leftJoin('hallbook as HB', 'H.bookdocid', '=', 'HB.docid')
            ->leftJoin('functiontype as F', 'HB.func_name', '=', 'F.code')
            ->leftJoin('venueocc as V', 'HB.DocId', '=', 'V.FPDocId')
            ->where(function ($q) {
                $q->where('S.suncode', 10103)
                    ->orWhere('SH.suncode', 10103);
            })
            ->where('H.propertyid', $propertyid)
            ->where('H.restcode', 'BANQ' . $propertyid)
            ->whereBetween('H.vdate', [$fromdate, $todate])
            ->whereRaw("((IFNULL(SH.Amount,0) + IFNULL(S.Amount,0)) - (IFNULL(P.AmtCr,0) + IFNULL(H.Advance,0))) > 0")
            ->groupBy('H.docId')
            ->distinct();

        // DataTables search
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('H.party', 'like', "%{$search}%")
                    ->orWhere('F.name', 'like', "%{$search}%")
                    ->orWhere('S.vno', 'like', "%{$search}%")
                    ->orWhere('SH.vdate', 'like', "%{$search}%")
                    ->orWhere('V.fromdate', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $data = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $result = [];
        $sno = $start + 1;
        foreach ($data as $row) {
            $advance = $this->getAdvanceByContraDoc($row->BookDocID, $propertyid);
            $totalReceipt = $this->getTotalReceiptByDoc($row->docId);
            $balance = ($row->Amount) - (($advance->Advance ?? 0) + ($totalReceipt->TotRect ?? 0));

            $result[] = [
                'sno' => $sno++,
                'book_date' => $row->BookDate,
                'function_date' => $row->FuncStartDate,
                'for_time' => $row->FuncStartTime,
                'function_type' => $row->FuncName,
                'party_name' => $row->PartyName,
                'fpno' => $row->BillNo,
                'rect_date' => $row->BillDate,
                'amount' => $row->Amount,
                'advance' => $advance->Advance ?? 0,
                'rect_no' => $totalReceipt->TotRect ?? 0,
                'balance' => $balance,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $result
        ]);
    }


    private function getAdvanceByContraDoc($contraDocId, $propertyId = 103)
    {
        return DB::table('paychargeh')
            ->select(DB::raw('SUM(AmtCr) as Advance'))
            ->where('VType', 'AD')
            ->where('propertyid', $propertyId)
            ->where('ContraDocID', $contraDocId)
            ->first();
    }

    private function getTotalReceiptByDoc($docId)
    {
        return DB::table('paychargeh')
            ->select(DB::raw('IFNULL(SUM(AmtCr),0) as TotRect'))
            ->where('DocId', $docId)
            ->first();
    }


    public function companyWiseSaleReport(Request $request)
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
        $partyName = HallSale1::where('propertyid', $this->propertyid)->distinct('party')->get(['party']);
        return view('property.companywisesalereport', [
            'fromdate' => $fromdate,
            'statename' => $statename,
            'distinctuname' => $distinctuname,
            'company' => $company,
            'partyName' => $partyName,
            'revheading' => $revheading
        ]);
    }



    // DataTables-compatible Banquet Balance fetch (pagination, search, multi-party)
    public function companyWiseSaleReportData(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $partyNames = $request->input('party_name'); // comma-separated or 'all'

        // Convert partyNames to array if not 'all'
        if ($partyNames === 'all' || empty($partyNames)) {
            $partyList = DB::table('hallsale1')->distinct()->pluck('Party')->toArray();
        } else {
            $partyList = array_filter(array_map('trim', explode(',', $partyNames)));
        }

        // If $partyList is empty, return no data
        if (empty($partyList)) {
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $advanceSub = DB::table('paychargeh')
            ->selectRaw('IFNULL(SUM(amtcr),0)')
            ->whereColumn('ContraDocID', 'H.BookDocID');
        $totRectSub = DB::table('paychargeh')
            ->selectRaw('IFNULL(SUM(amtcr),0)')
            ->whereColumn('DocId', 'H.DocId');

        $baseQuery = DB::table('hallsale1 as H')
            ->leftJoin('paychargeh as P', 'H.DocId', '=', 'P.DocId')
            ->leftJoin('hallbook as HB', 'H.BookDocID', '=', 'HB.DocID')
            ->leftJoin('functiontype as F', 'HB.Func_Name', '=', 'F.Code')
            ->leftJoin('venueocc as V', 'HB.DocId', '=', 'V.FPDocId')
            ->select([
                'H.DocID',
                'HB.VDate as BookDate',
                'V.dromtime as FuncStartDate',
                'V.ToDate as FuncEndDate',
                'V.dromtime as FuncStartTime',
                'V.ToTime as FuncEndTime',
                'F.Name as FuncName',
                'H.Party as PartyName',
                'H.Vno as BillNo',
                'H.VDate as BillDate',
                'H.NetAmt as Amount',
                // ✅ Subquery columns
                DB::raw("({$advanceSub->toSql()}) as Advance"),
                DB::raw("(H.NetAmt - ({$advanceSub->toSql()})) as Balance"),
                DB::raw("({$totRectSub->toSql()}) as TotRect"),
                'H.BookDocID',
            ])
            ->mergeBindings($advanceSub)
            ->mergeBindings($totRectSub)
            ->where('H.RestCode', 'BANQ103')
            ->where('H.propertyid', 103)
            ->whereBetween('H.VDate', [$fromdate, $todate])
            ->whereIn('H.Party', $partyList)
            ->havingRaw('Balance > 0');

        // DataTables search
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('H.Party', 'like', "%{$search}%")
                    ->orWhere('F.Name', 'like', "%{$search}%")
                    ->orWhere('H.Vno', 'like', "%{$search}%");
            });
        }

        $total = $baseQuery->count();

        // Pagination
        $data = $baseQuery
            ->orderBy('H.Party')
            ->orderBy('H.Vno')
            ->offset($start)
            ->limit($length)
            ->get();

        // Format for DataTables
        $result = [];
        $sno = $start + 1;
        foreach ($data as $row) {
            $result[] = [
                'sno' => $sno++,
                'book_date' => $row->BookDate,
                'function_date' => $row->FuncStartDate,
                'for_time' => $row->FuncStartTime,
                'function_type' => $row->FuncName,
                'party_name' => $row->PartyName,
                'fpno' => $row->BillNo,
                'rect_date' => $row->BillDate,
                'rect_no' => $row->TotRect ?? 0.00,
                'amount' => $row->Amount ?? 0.00,
                'advance' => $row->Advance ?? 0.00,
                'balance' => $row->Balance ?? 0.00
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $result,
        ]);
    }
}
