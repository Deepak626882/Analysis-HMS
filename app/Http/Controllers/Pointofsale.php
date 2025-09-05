<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\WhatsappSend;
use App\Models\Billprintthermal;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Companyreg;
use App\Models\Guestfolio;
use App\Models\Suntran;
use App\Models\RoomMast;
use App\Models\Sale1;
use App\Models\Sale2;
use App\Models\Stock;
use App\Models\Paycharge;
use App\Models\MenuHelp;
use App\Models\Depart;
use App\Models\Revmast;
use App\Models\ServerMast;
use App\Models\EnviroPos;
use App\Models\GuestProf;
use App\Models\PrintingSetup;
use App\Models\UserPermission;
use App\Models\RoomOcc;
use App\Models\GuestReward;
use App\Models\Cities;
use App\Models\Depart1;
use App\Models\EnviroWhatsapp;
use App\Models\SubGroup;
use App\Models\Sale1log;
use App\Models\Sale2log;
use App\Models\Stocklog;
use App\Models\Suntranlog;
use App\Models\Kot as KoTModal;
use App\Models\VoucherPrefix;
use App\Models\VoucherType;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Finder\Iterator\VcsIgnoredFilterIterator;

use function App\Helpers\endsWith;
use function App\Helpers\removeSuffixIfExists;
use function App\Helpers\splitByJoin;

class Pointofsale extends Controller
{
    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $ncurdate;

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
    # Warning: Abandon hope, all who enter here. 😱

    public function ncurfetch()
    {
        $ncurdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        return $ncurdate;
    }

    public function ExportTable()
    {
        echo '<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />';
        echo '<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" />';
        echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
        echo '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>';
    }

    public function DownloadTable($tableName, $title, $columnsToExport, $columnToSearch)
    {
        $exportColumnsJS = json_encode($columnsToExport);
        $searchColumnsJS = json_encode($columnToSearch);

        echo "<script>$(document).ready(function() {
        let table = $('#$tableName').DataTable({
            dom: 'Bfrtip',
            pageLength: 15,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel <i class=\"fa fa-file-excel-o\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: 'Csv <i class=\"fa-solid fa-file-csv\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Pdf <i class=\"fa fa-file-pdf-o\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                },
                {
                    extend: 'print',
                    text: 'Print <i class=\"fa-solid fa-print\"></i>',
                    title: '$title',
                    filename: '$title',
                    exportOptions: {
                        columns: $exportColumnsJS
                    }
                }
            ],
            initComplete: function() {
                // Configure column-specific search inputs based on the specified columns
                let searchColumns = $searchColumnsJS;
                this.api().columns(searchColumns).every(function() {
                    let column = this;
                    let title = column.header().textContent;
                    let input = document.createElement('input');
                    input.placeholder = title;
                    $(input).appendTo($(column.footer()).empty()); // Use jQuery for better compatibility
                    $(input).on('keyup', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
            }
        });
    });</script>";
    }
    public function revokeopen($code)
    {
        $value = Menuhelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('code', $code)->first();
        return $value;
    }

    public function fetchcompdetail(Request $request)
    {
        $sub_code = $request->input('sub_code');
        $gstin = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $sub_code)->value('gstin');
        return json_encode($gstin);
    }

    public function discountmaxxhr(Request $request)
    {
        $userpermission = UserPermission::where('propertyid', $this->propertyid)->where('username', $this->username)->first();
        if ($userpermission) {
            return response()->json([$userpermission]);
        } else {
            return response()->json(['message' => 'This is property admin']);
        }
    }

    public function updatedelflagxhr(Request $request)
    {
        $docid = $request->input('docid');
        $reason = $request->input('reason');
        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        $mergedwith = !empty($sale1->mergedwith) ? explode(',', $sale1->mergedwith) : [$sale1->docid];

        $update = [
            'delflag' => 'Y',
            'delremark' => $reason
        ];
        Sale1::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->update($update);
        Sale2::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->update($update);
        Stock::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->update($update);
        Suntran::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->update($update);
        $msg = "Sale Bill Entry Updated";
        return json_encode($msg);
    }

    public function fetchgguestprof(Request $request)
    {
        $sale1docid = $request->input('sale1docid');
        $guestprof = GuestProf::select('guestprof.*', 'cities.cityname as nameofcity')
            ->leftJoin('cities', 'cities.city_code', '=', 'guestprof.city')
            ->where('guestprof.propertyid', $this->propertyid)->where('guestprof.docid', $sale1docid)->first();

        return response()->json(['guestprof' => $guestprof]);
    }


    public function fetchitemoldroomno(Request $request)
    {
        $billno = $request->input('billno');
        $dcode = $request->input('dcode');
        $vprefix = $request->vprefix;
        $dep = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();

        $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
            ->where('departcode', $dep->dcode)
            ->pluck('associatedrestcode')
            ->toArray();

        // $restcodes = array_merge([$dep->dcode], $associatedrestcode);

        $sale1 = Sale1::where('vno', $billno)->where('restcode', $dep->dcode)->where('vprefix', $vprefix)->where('propertyid', $this->propertyid)
            ->first();

        if (!$sale1) {
            return response()->json(['message' => 'Invalid Vno'], 204);
        }

        $chkguestprof = GuestProf::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)->first();
        if (!empty($sale1)) {
            $itemstmp = DB::table('stock')
                ->select(
                    'sale1.docid as sale1docid',
                    'sale1.mergedwith',
                    'stock.itemrate as actualrate',
                    'itemmast.RateIncTax',
                    'itemmast.Name',
                    'depart.kot_yn',
                    'stock.kotdocid',
                    'stock.description',
                    'itemmast.RateEdit',
                    'itemmast.DiscApp as discapp',
                    'stock.qtyiss',
                    'stock.amount',
                    'stock.rate',
                    'stock.item',
                    'stock.vno',
                    'stock.kotsno',
                    'stock.roomno',
                    'sale1.waiter',
                    'sale1.guaratt',
                    'stock.docid',
                    'stock.vtype',
                    'stock.taxper',
                    'stock.restcode',
                    DB::raw('COALESCE(taxstru.tax_code, "") 
                AS tax_code'),
                    DB::raw('COALESCE(taxstru.tax_name, "") AS tax_name'),
                    DB::raw('COALESCE(taxstru.tax_rate, 0) AS tax_rate'),
                    'itemcatmast.TaxStru',
                    'kot.vno as kotvno'
                )
                ->leftJoin('itemmast', function ($join) {
                    $join->on('stock.item', '=', 'itemmast.Code')
                        ->on('stock.restcode', '=', 'itemmast.RestCode');
                })
                ->leftJoin('sale1', 'sale1.docid', '=', 'stock.docid')
                ->leftJoin('depart', 'depart.dcode', '=', 'stock.restcode')
                ->leftJoin('itemcatmast', function ($join) {
                    $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                        ->on('itemcatmast.RestCode', '=', 'itemmast.RestCode');
                })
                ->leftJoin('kot', function ($query) {
                    $query->on('kot.docid', '=', 'stock.kotdocid')
                        ->where('kot.nckot', 'N');
                })
                ->leftJoin(DB::raw('(SELECT str_code, GROUP_CONCAT(name) AS tax_name, GROUP_CONCAT(tax_code) AS tax_code, SUM(rate) AS tax_rate FROM taxstru GROUP BY str_code) AS taxstru'), function ($join) {
                    $join->on('taxstru.str_code', '=', 'itemcatmast.TaxStru');
                })
                ->where('stock.propertyid', $this->propertyid)
                ->where('stock.vprefix', $vprefix)
                ->groupBy('stock.vno')
                ->groupBy('stock.sno')
                ->groupBy('stock.item')
                ->orderBy('stock.sno', 'ASC');

            if (!empty($sale1->mergedwith)) {
                $mergedString = Sale1::where('propertyid', $this->propertyid)
                    ->where('docid', $sale1->docid)
                    ->value('mergedwith');

                $mergedDocids = array_map('trim', explode(',', $mergedString));

                $items = $itemstmp->whereIn('stock.docid', $mergedDocids)->get();
                $suntrandata = Suntran::select(
                    'suntran.*',
                    'sundrytype.bold',
                    'sundrytype.peroramt',
                    'sundrytype.nature',
                    'depart.name as restname',
                    'sundrytype.automanual'
                )
                    ->leftJoin('sundrytype', function ($join) {
                        $join->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                            ->on('sundrytype.vtype', '=', 'suntran.restcode')
                            ->on('sundrytype.sno', '=', 'suntran.sno');
                    })
                    ->leftJoin('depart', 'depart.dcode', '=', 'suntran.restcode')
                    ->whereIn('suntran.docid', $mergedDocids)->where('suntran.propertyid', $this->propertyid)
                    ->groupBy('suntran.sno')
                    ->groupBy('suntran.docid')
                    ->get();
            } else {
                $items = $itemstmp->whereIn('stock.docid', [$sale1->docid])->get();

                $suntrandata = Suntran::select(
                    'suntran.*',
                    'sundrytype.bold',
                    'sundrytype.peroramt',
                    'sundrytype.nature',
                    'depart.name as restname',
                    'sundrytype.automanual'
                )
                    ->leftJoin('sundrytype', function ($join) {
                        $join->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                            ->on('sundrytype.vtype', '=', 'suntran.restcode')
                            ->on('sundrytype.sno', '=', 'suntran.sno');
                    })
                    ->leftJoin('depart', 'depart.dcode', '=', 'suntran.restcode')
                    ->where('suntran.docid', $sale1->docid)
                    ->where('suntran.propertyid', $this->propertyid)
                    ->groupBy('suntran.sno')
                    ->groupBy('suntran.docid')
                    ->get();
            }

            $outlet1 = null;
            $outlet2 = null;

            $mergedString = Sale1::where('propertyid', $this->propertyid)
                ->where('docid', $sale1->docid)
                ->value('mergedwith');

            $mergedDocids = array_map('trim', explode(',', $mergedString));
            $sale1full = Sale1::where('propertyid', $this->propertyid)
                ->whereIn('docid', $mergedDocids)
                ->get();

            $mergedDocids = array_map('trim', explode(',', $mergedString));
            $restcodesp = $sale1full->pluck('restcode')->unique()->values();
            $mergedcodes = $sale1full->pluck('mergedwith')->filter()->unique()->values();

            if ($mergedcodes->isNotEmpty() && $restcodesp->count() > 0) {
                $outlet1 = $restcodesp[0];
                $outlet2 = $restcodesp[1];
            }

            $amount = 0;
            foreach ($items as $item) {
                $docid = $item->docid;
            }

            $sundrytype = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $dcode)->orderBy('sno', 'ASC')->get();

            // return $suntrandata;

            $amount = $suntrandata[0]->amount;

            $roomno = $sale1->roomno;
            $guestdt = Guestfolio::select('guestfolio.*', 'roomocc.plancode', 'roomocc.sno1', 'subgroup.name as subname', 'subgroup.gstin')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'guestfolio.company')
                ->leftJoin('roomocc', 'roomocc.docid', '=', 'guestfolio.docid')
                ->where('roomocc.roomno', $roomno)
                ->where('guestfolio.propertyid', $this->propertyid)->where('guestfolio.docid', $sale1->folionodocid)->first();
            // return $guestdt;
            if ($guestdt) {
                $planname = 'EP';
                if (!empty($guestdt->plancode)) {
                    $planname = DB::table('plan_mast')->where('propertyid', $this->propertyid)->where('pcode', $guestdt->plancode)->value('name');
                }
                // $concat = 'Name: ' . $guestdt->name . ', Plan: ' . $planname;
                $concat = 'Name: ' . $guestdt->name . ', Plan: ' . $planname . ($guestdt->remarks != '' ? ', Remarks: ' . $guestdt->remarks : '');
            } else {
                $concat = '';
            }
            $waitername = DB::table('server_mast')->where('propertyid', $this->propertyid)->where('scode', $sale1->waiter)->value('name');
            $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $sale1->restcode)->first();
            if (strtolower($depart->nature) == 'room service') {
                $chkoutpaycharge = RoomOcc::select('roomocc.type', 'paycharge.roomno')
                    ->leftJoin('paycharge', function ($join) {
                        $join->on('paycharge.roomno', '=', 'roomocc.roomno')
                            ->on('paycharge.folionodocid', '=', 'roomocc.docid')
                            ->on('paycharge.propertyid', '=', 'roomocc.propertyid');
                    })
                    ->leftJoin('sale1', 'sale1.docid', '=', 'paycharge.docid')
                    ->where('roomocc.docid', $guestdt->docid)
                    ->where('roomocc.sno1', $guestdt->sno1)
                    ->where('paycharge.paycode', 'TOUT' . $this->propertyid)
                    ->where('roomocc.type', 'O')
                    ->where('sale1.docid', $sale1->docid)
                    ->groupBy('roomocc.roomno')
                    ->get();
                $chkoutrowcount = count($chkoutpaycharge);
                $label = 'Room No';
            } else {
                $chkoutpaycharge = Paycharge::where('docid', $sale1->docid)->where('paycode', 'TOUT' . $this->propertyid)->where('sno1', $sale1->sno1)->get();
                $chkoutrowcount = count($chkoutpaycharge);
                $label = 'Table No';
            }

            if (strtolower($dep->nature) == 'room service') {
                $compcode = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $guestdt->docid)
                    ->where('comp_code', '!=', '')
                    ->where('sno1', $guestdt->sno1)->value('comp_code');
                if (!$compcode) {
                    $compcode = $guestdt->company;
                }
            } else if ($dep->nature == 'Outlet') {
                $compcode = Paycharge::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)
                    ->where('comp_code', '!=', '')
                    ->value('comp_code');
            }

            $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $sale1->party)->first();

            $paychargerows = Paycharge::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)->count();
            $paychargerowsd = Paycharge::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)
                ->whereNot('paycode', 'TOUT' . $this->propertyid)
                ->get();

            $toutrow = Paycharge::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)
                ->where('paycode', 'TOUT' . $this->propertyid)
                ->first();

            $data = [
                'toutrow' => $toutrow,
                'items' => $items,
                'suntransdata' => $suntrandata,
                'sundrytype' => $sundrytype,
                'amount' => $amount,
                'roomno' => $roomno,
                'sale1' => $sale1,
                'waitername' => $waitername,
                'guestdt' => $guestdt,
                'docid' => $docid,
                'concat' => $concat,
                'chkoutrowcount' => $chkoutrowcount,
                'dep' => $depart->nature,
                'subgroup' => $subgroup,
                'compcode' => $compcode,
                'label' => $label,
                'paychargerows' => $paychargerows,
                'paychargerowsd' => $paychargerowsd,
                'chkguestprof' => $chkguestprof,
                'outlet1code' => $outlet1,
                'outlet2code' => $outlet2
            ];
            return json_encode($data);
        } else {
            return json_encode('false');
        }
    }

    public function fetchcompdt(Request $request)
    {

        $billno = $request->input('billno');
        $vtype = $request->input('vtype');

        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('vno', $billno)->where('vtype', $vtype)->first();
        $dep = Depart::where('propertyid', $this->propertyid)->where('dcode', $sale1->restcode)->first();
        $upsale = [
            'printed' => 'Y'
        ];
        Sale1::where('propertyid', $this->propertyid)->where('restcode', $sale1->restcode)->where('docid', $sale1->docid)->update($upsale);
        // $compcode = null;
        // if ($dep->nature == 'Room Service') {
        //     $guestdt = Guestfolio::select('guestfolio.*', 'roomocc.plancode', 'roomocc.sno1', 'subgroup.name as subname', 'subgroup.gstin')
        //         ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'guestfolio.company')
        //         ->leftJoin('roomocc', 'roomocc.docid', '=', 'guestfolio.docid')
        //         ->where('roomocc.roomno', $sale1->roomno)
        //         ->where('guestfolio.propertyid', $this->propertyid)->where('guestfolio.docid', $sale1->folionodocid)->first();
        //     $compcode = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $guestdt->docid)
        //         ->where('comp_code', '!=', '')
        //         ->where('sno1', $guestdt->sno1)->value('comp_code');
        //     if (!$compcode) {
        //         $compcode = $guestdt->company;
        //     }
        // } else if ($dep->nature == 'Outlet') {
        //     $compcode = Paycharge::where('propertyid', $this->propertyid)->where('docid', $sale1->docid)
        //         ->where('comp_code', '!=', '')
        //         ->value('comp_code');
        // }

        $subgroup = SubGroup::select(
            'subgroup.name as compname',
            'subgroup.gstin',
            'cities.state as compstatecode',
            'states.name AS compstatename',
            'cities.cityname AS compcityname',
            'subgroup.citycode AS compcitycode',
            'subgroup.address'
        )
            ->leftJoin('cities', 'cities.city_code', '=', 'subgroup.citycode')
            ->leftJoin('states', 'states.state_code', '=', 'cities.state')
            ->where('subgroup.propertyid', $this->propertyid)->where('subgroup.sub_code', $sale1->party)->first();

        if ($subgroup) {
            return json_encode($subgroup);
        } else {
            return json_encode(null);
        }

        // return json_encode($subgroup);
    }

    public function phonefindxhr(Request $request)
    {
        $mobile_no = $request->input('phoneno');
        $find = GuestProf::where('propertyid', $this->propertyid)->where('mobile_no', $mobile_no)->first();
        if ($find != null) {
            $results = GuestProf::select([
                'itemmast.Name AS itemname',
                'stock.qtyiss',
                'stock.rate',
                'stock.amount',
                'stock.roomno',
                'guestprof.name AS customername',
                'guestprof.city',
                DB::raw("CONCAT(DATE_FORMAT(stock.vdate, '%d-%m-%Y'), ' ', DATE_FORMAT(stock.vtime, '%H:%i')) AS visittime"),
                'guestprof.likes',
                'guestprof.dislikes',
                DB::raw("DATE_FORMAT(guestprof.dob, '%d-%m-%Y') as dob"),
                DB::raw("DATE_FORMAT(guestprof.anniversary, '%d-%m-%Y') as anniversary"),
                'guestprof.add1'
            ])
                ->leftJoin('stock', 'stock.docid', '=', 'guestprof.docid')
                ->leftJoin('itemmast', 'itemmast.Code', '=', 'stock.item')
                ->where('guestprof.mobile_no', $find->mobile_no)
                ->where('guestprof.propertyid', $this->propertyid)
                ->orderBy('stock.vdate', 'DESC')
                ->orderBy('stock.vtime', 'DESC')
                ->get();
            return response()->json(['data' => $results, 'mobile_no' => $mobile_no], 200);
        } else {
            return response()->json(['Not Found'], 200);
        }
    }

    public function salebillsubmit(Request $request)
    {

        $validate = $request->validate([
            'roomno' => 'required',
            'pax' => 'required',
            'itemcode1' => 'required',
            'rate1' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $docidInput = $request->input('kotdocid');

            $docidsArray = array_filter(explode(',', $docidInput));

            $kotvno = KoTModal::where('propertyid', $this->propertyid)->whereIn('docid', $docidsArray)->get();

            $uniquevno = [];
            foreach ($kotvno as $item) {
                $vno = $item->vno;
                if (!in_array($vno, $uniquevno)) {
                    $uniquevno[] = $vno;
                }
            }

            $uniquevno = implode(",", $uniquevno);
            $totalitems = $request->input('totalitems');

            $table1 = 'sale1';
            $table2 = 'sale2';
            $table3 = 'stock';
            $table4 = 'suntran';
            $table5 = 'paycharge';
            $posroomno = $request->input('posroomno');

            $restcode = $request->input('fixrestcode');
            $totalamt = $request->input('totalamt');
            $servicehargeamt = $request->input('servicechargeamount') ?? '0.00';
            $servicechargefix = $request->input('servicechargefix') ?? '0.00';
            $discper = $request->input('discountfix');
            $discper = floatval(str_replace(',', '', number_format($discper, 2)));
            $discamt = $request->input('discountsundry');
            $taxable = $request->input('taxableamt');
            $inputr = $request->input('roundoffamount');
            $cleanedInput = str_replace('.', '', $inputr);
            $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();

            if (strlen($cleanedInput) > 1) {
                $floatValue = substr($cleanedInput, 0, 1) . '.' . substr($cleanedInput, 1);
            } else {
                $floatValue = $cleanedInput;
            }

            $roundoff = number_format((float)$floatValue, 2, '.', '');

            $netamount = $request->input('netamount');
            $cgst = $request->input('cgstamount');
            $sgst = $request->input('sgstamount');
            $igst = $request->input('igstamt');
            $vtype = $request->input('vtype');

            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->first();

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;

            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;
            $roomno = $request->input('roomno');
            $roomdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('roomno', $roomno)->whereNull('type')->first();
            $msno1 = 0;
            $wpnum = '';
            if (isset($roomdata)) {
                $gprof = GuestProf::where('propertyid', $this->propertyid)->where('docid', $roomdata->docid)->first();
                $wpnum = $gprof->mobile_no ?? '';
                $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $roomdata->docid)->where('leaderyn', 'Y')->first();
                if ($rocc) {
                    $msno1 = $rocc->sno1;
                }
            }

            $roommast = RoomMast::where('propertyid', $this->propertyid)->where('rcode', $roomno)->first();

            $nontaxable = $totalamt - $taxable;

            $sundrycount = $request->input('sundrycount');

            for ($i = 1; $i <= $sundrycount; $i++) {
                $fstype = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $restcode)->where('sno', $i)->first();
                $svalue = $fstype->svalue;
                $baseamount = 0.00;
                $amount = 0.00;
                if ($fstype->disp_name == 'Discount') {
                    $amount = $discamt;
                    $svalue = $discper;
                }
                if ($fstype->disp_name == 'Service Charge') {
                    $amount = $servicehargeamt;
                    $svalue = $servicechargefix;
                }
                if ($fstype->disp_name == 'Amount') {
                    $amount = $totalamt;
                }
                if ($fstype->disp_name == 'CGST') {
                    $amount = $cgst;
                }
                if ($fstype->disp_name == 'SGST') {
                    $amount = $sgst;
                }
                if ($fstype->disp_name == 'Round Off') {
                    $amount = $roundoff;
                    $baseamount = $roundoff + $netamount;
                }
                if ($fstype->disp_name == 'Net Amount') {
                    $amount = $netamount;
                }

                $suntrandata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'sno' => $i,
                    'vno' => $start_srl_no,
                    'vtype' => $vtype,
                    'vdate' => $this->ncurdate,
                    'dispname' => $fstype->disp_name,
                    'suncode' => $fstype->sundry_code,
                    'calcformula' => $fstype->calcformula,
                    'svalue' => $svalue,
                    'amount' => $amount,
                    'baseamount' => $baseamount,
                    'revcode' => $fstype->revcode,
                    'restcode' => $restcode,
                    'sunappdate' => $this->ncurdate,
                    'delflag' => 'N',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                DB::table($table4)->insert($suntrandata);
            }

            if ($roundoff > 0) {
                $roundyn = 'Y';
            } else {
                $roundyn = 'N';
            }

            if ($request->input('phoneno') != '') {
                $wpnum = $request->input('phoneno');
                $findexist = GuestProf::where('propertyid', $this->propertyid)->where('mobile_no', $request->input('phoneno'))->first();

                if ($findexist != null) {
                    $guestprof = $findexist->guestcode;
                } else {
                    $maxguestprof = GuestProf::where('propertyid', $this->propertyid)->max('guestcode');
                    $guestprof = ($maxguestprof === null) ? $this->propertyid . '10001' : ($guestprof = $this->propertyid . substr($maxguestprof, $this->ptlngth) + 1);
                }

                $citycode = $request->input('customercity');
                $citydata = '';
                $statedata = '';
                $countrydata = '';
                if (!empty($citycode)) {
                    $citydata = Cities::where('propertyid', $this->propertyid)->where('city_code', $citycode)->first();
                    $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $citydata->state)->first();
                    $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $citydata->country)->first();
                }

                $guestreward = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'custcode' => $guestprof,
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'restcode' => $restcode,
                    'departname' => $depart->name,
                    'billno' => $start_srl_no,
                    'total' => $totalamt,
                    'billamt' => $netamount,
                    'rewardpoint' => 0.00,
                    'redeempoint' => 0.00,
                    'mobileno' => $request->input('phoneno'),
                    'discamt' => $discamt ?? 0.00,
                    'schemecode' => '',
                    'saleupto' => 0.00,
                    'rppointonamt' => 0.00,
                    'rewardvalue' => 0.00,
                    'reedemvalue' => 0.00,
                    'regid' => '',
                    'discper' => $discper ?? 0.00,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                $dob = $request->input('birthdate');
                $age = Carbon::parse($dob)->age;

                $guestproft = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'folio_no' => '0',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'complimentry' => '',
                    'guestcode' => $guestprof,
                    'name' => $request->input('customername'),
                    'state_code' => $citydata->state ?? '',
                    'country_code' => $citydata->country ?? '',
                    'add1' => $request->input('address') ?? '',
                    'add2' => '',
                    'city' => $citycode ?? '',
                    'type' => $countrydata->Type ?? '',
                    'mobile_no' => $request->input('phoneno'),
                    'email_id' => '',
                    'nationality' => $countrydata->nationality ?? '',
                    'anniversary' => $request->input('anniversary') ?? null,
                    'guest_status' => '',
                    'comments1' => '',
                    'comments2' => '',
                    'comments3' => '',
                    'city_name' => $citydata->cityname ?? '',
                    'state_name' => $statedata->name ?? '',
                    'country_name' => $countrydata->name ?? '',
                    'gender' => '',
                    'marital_status' => $request->input('anniversary') != '' ? 'Married' : 'Single',
                    'zip_code' => $citydata->zipcode ?? '',
                    'con_prefix' => '',
                    'dob' => $dob ?? null,
                    'age' => $age ?? '',
                    'pic_path' => '',
                    'id_proof' => '',
                    'idproof_no' => '',
                    'issuingcitycode' => '',
                    'issuingcityname' => '',
                    'issuingcountrycode' => '',
                    'issuingcountryname' => '',
                    'expiryDate' => null,
                    'vipStatus' => '',
                    'paymentMethod' => '',
                    'billingAccount' => '',
                    'idpic_path' => '',
                    'm_prof' => $guestprof,
                    'father_name' => '',
                    'likes' => $request->input('like') ?? '',
                    'dislikes' => $request->input('dislike') ?? '',
                    'fom' => 0,
                    'pos' => 1,
                ];
                GuestReward::insert($guestreward);
                GuestProf::insert($guestproft);
            }

            $sale1 = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $start_srl_no,
                'vtype' => $vtype,
                'vdate' => $this->ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'restcode' => $restcode,
                'party' => $request->input('company') ?? '',
                'roomno' => $roomno,
                'roomcat' => $roommast->room_cat,
                'roomtype' => $roommast->type,
                'foliono' => $roomdata->folioNo ?? '',
                'sno1' => $roomdata->sno1 ?? 1,
                'total' => $totalamt,
                'nontaxable' => $nontaxable ?? 0.00,
                'taxable' => $totalamt - ($discamt + $nontaxable) ?? 0.00,
                'discper' => $discper ?? 0.00,
                'discamt' => $discamt ?? 0.00,
                'servicecharge' => 0.00,
                'addamt' => 0.00,
                'dedamt' => 0.00,
                'roundoff' => $roundoff ?? 0.00,
                'netamt' => $netamount,
                'remark' => '',
                'waiter' => $request->input('waiter') ?? '',
                'kotno' => $uniquevno,
                'tokenno' => 0,
                'delflag' => 'N',
                'guaratt' => $request->input('pax'),
                'printed' => '',
                'deliveredyn' => '',
                'custname' => '',
                'phoneno' => '',
                'add' => '',
                'city' => '',
                'cashrecd' => 0.00,
                'folionodocid' => $roomdata->docid ?? '',
                'au_name' => '',
                'au_entdt' => null,
                'discremark' => '',
                'sgst' => $sgst ?? 0.00,
                'cgst' => $cgst ?? 0.00,
                'igst' => $igst ?? 0.00,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            $chkroomserv = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();
            if (strtolower($chkroomserv->rest_type) == 'room service') {

                $paycode1 = 'ROOM' . $this->propertyid;
                $revdata1 = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $paycode1)->first();

                $paycharge1 = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $start_srl_no,
                    'vtype' => $vtype,
                    'comp_code' => $request->input('company'),
                    'sno' => 1,
                    'sno1' => $roomdata->sno1,
                    'msno1' => $msno1,
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'paycode' => $paycode1,
                    'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $start_srl_no,
                    'paytype' => $revdata1->pay_type,
                    'roomcat' => 'REST',
                    'restcode' => $restcode,
                    'roomno' => $roomno,
                    'amtcr' => $netamount,
                    'roomtype' => 'RO',
                    'foliono' => 0,
                    'billamount' => $netamount,
                    'taxcondamt' => 0,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                $paycode2 = 'TOUT' . $this->propertyid;
                $paycharge2 = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $start_srl_no,
                    'vtype' => $vtype,
                    'comp_code' => $request->input('company'),
                    'sno' => 2,
                    'sno1' => $roomdata->sno1 ?? '',
                    'msno1' => $msno1,
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'paycode' => $paycode2,
                    'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $start_srl_no,
                    'paytype' => $revdata1->pay_type,
                    'folionodocid' => $roomdata->docid ?? '',
                    'restcode' => $restcode,
                    'roomno' => $roomno,
                    'roomcat' => $roommast->room_cat,
                    'amtdr' => $netamount,
                    'roomtype' => $roommast->type,
                    'foliono' => $roomdata->folioNo ?? '',
                    'guestprof' => $roomdata->guestprof ?? '',
                    'billamount' => $netamount,
                    'taxcondamt' => 0,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];
                DB::table($table5)->insert($paycharge1);
                DB::table($table5)->insert($paycharge2);
            }

            $sale2 = [];

            for ($i = 1; $i <= $totalitems; $i++) {
                $sno = $request->input('itemnumber' . $i);

                $taxratepos = $request->input('taxrate_sum' . $i);
                $tax_rate = $request->input('tax_rate' . $i);
                $itemqty = $request->input('quantity' . $i);
                $itemratetmp = $request->input('taxedrate' . $i);
                $itemrate = floor($itemratetmp * 100) / 100;
                $itemtruerate = $request->input('rate' . $i);
                $itemcamttmp = $request->input('fixamount' . $i);
                $itemcamt = floor($itemcamttmp * 100) / 100;
                $taxamt = ($itemcamt * $taxratepos) / 100;
                $itemamt = $itemqty * $itemrate;
                if ($discper != 0) {
                    $discamt = $itemamt * $discper / 100;
                } else {
                    $discamt = 0.00;
                }

                $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->first();
                if ($taxratepos != 0) {
                    $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->value('ItemCatCode');
                    $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                    $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                    foreach ($fetchtaxes as $taxesrow) {
                        $titemqty = $request->input('quantity' . $i);
                        $titemratetmp = $request->input('taxedrate' . $i);
                        $titemrate = floor($titemratetmp * 100) / 100;
                        $titemamt = $titemqty * $titemrate;
                        $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                        if ($discper != 0) {
                            $titemamt = $titemamt - ($titemamt * $discper) / 100;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                        }
                        $sale2[] = [
                            'propertyid' => $this->propertyid,
                            'docid' => $docid,
                            'sno' => $sno,
                            'sno1' => $taxesrow->sno,
                            'vno' => $start_srl_no,
                            'vtype' => $vtype,
                            'vdate' => $this->ncurdate,
                            'vtime' => date('H:i:s'),
                            'vprefix' => $vprefix,
                            'restcode' => $restcode,
                            'taxcode' => $taxesrow->tax_code,
                            'basevalue' => $titemamt,
                            'taxper' => $taxesrow->rate,
                            'taxamt' => $ttaxamt,
                            'delflag' => 'N',
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                        ];
                    }
                }

                if ($itemmast->RestCode == '' || empty($itemmast->RestCode) || is_null($itemmast->RestCode)) {
                    DB::rollBack();
                    return 'Unable To Submit Sale Bill Please Try Again!';
                }

                $stock = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'sno' => $sno,
                    'vno' => $start_srl_no,
                    'vtype' => $vtype,
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'restcode' => $restcode,
                    'roomno' => $roomno,
                    'roomcat' => $roommast->room_cat,
                    'roomtype' => $roommast->type,
                    'contradocid' => '',
                    'contrasno' => '',
                    'item' => $request->input('itemcode' . $i),
                    'qtyiss' => $request->input('quantity' . $i),
                    'qtyrec' => '0',
                    'unit' => $itemmast->Unit ?? '',
                    'rate' => $itemrate,
                    'amount' => $itemcamt,
                    'taxper' => $tax_rate,
                    'taxamt' => $taxamt,
                    'discper' => $discper ?? 0.00,
                    'discamt' => $discamt,
                    'description' => $request->input('description' . $i) ?? '',
                    'voidyn' => '',
                    'remarks' => '',
                    'kotdocid' => $request->input('kotsdocid' . $i) ?? $request->input('kotdocidfix') ?? '',
                    'kotsno' => $request->input('kotsno' . $i) ?? '0',
                    'total' => $netamount ?? '0',
                    'discapp' => $request->input('discapp' . $i) ?? '',
                    'roundoff' => $roundyn,
                    'departcode' => $itemmast->Kitchen ?? '',
                    'godowncode' => $itemmast->Kitchen ?? '',
                    'chalqty' => '',
                    'recdqty' => '',
                    'accqty' => '',
                    'rejqty' => '',
                    'recdunit' => '',
                    'specification' => '',
                    'itemrate' => $itemtruerate,
                    'delflag' => 'N',
                    'landval' => 0.00,
                    'convratio' => 0.00,
                    'indentdocid' => '',
                    'indentsno' => 0,
                    'issqty' => 0.00,
                    'issueunit' => '',
                    'freesno' => 0,
                    'schemecode' => '',
                    'seqno' => 0,
                    'company' => '',
                    'itemrestcode' => $itemmast->RestCode ?? '',
                    'schrgapp' => '',
                    'schrgper' => 0.00,
                    'schrgamt' => 0.00,
                    'refdocid' => '',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                $kotupdate = [
                    'u_updatedt' => $this->currenttime,
                    'u_ae' => 'e',
                ];

                foreach ($sale2 as $row) {
                    $existingRow = DB::table($table2)->where('sno', $row['sno'])->where('sno1', $row['sno1'])->where('docid', $row['docid'])->where('propertyid', $row['propertyid'])->first();
                    if (!$existingRow) {
                        DB::table($table2)->insert($row);
                    }
                }

                DB::table($table3)->insert($stock);
                $docidInput = $request->input('kotdocid');

                $docidsArray = array_filter(explode(',', $docidInput));

                $kotupdate = [
                    'pending' => 'N',
                    'contradocid' => $docid,
                    'contrsno' => $start_srl_no,
                ];

                DB::table('kot')
                    ->where('propertyid', $this->propertyid)
                    ->whereIn('docid', $docidsArray)
                    ->update($kotupdate);
            }
            DB::table($table1)->insert($sale1);

            $sale1print = Sale1::select(
                'sale1.vno',
                'sale1.vdate',
                'sale1.waiter',
                'sale1.roomno',
                'sale1.restcode',
                'sale1.vtype',
                'sale1.kotno',
                'depart.name as departname',
                'depart.nature as departnature',
                'server_mast.name as waitername'
            )
                ->leftJoin('server_mast', 'server_mast.scode', '=', 'sale1.waiter')
                ->leftJoin('depart', 'depart.dcode', '=', 'sale1.restcode')
                ->where('sale1.docid', $docid)->where('sale1.propertyid', $this->propertyid)->where('sale1.restcode', $restcode)->first();

            $printsetup = PrintingSetup::where('propertyid', $this->propertyid)->where('restcode', $restcode)->where('module', 'POS')->first();

            $printdata = [
                'roomno' => $sale1print->roomno,
                'vdate' => $sale1print->vdate,
                'vtype' => $sale1print->vtype,
                'waiter' => $sale1print->waitername,
                'billno' => $sale1print->vno,
                'departname' => $sale1print->departname,
                'kotno' => $sale1print->kotno,
                'outletcode' => $sale1print->restcode,
                'departnature' => $sale1print->departnature,
                'printsetup' => $printsetup
            ];

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            DB::commit();

            $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();

            if ($wpenv != null) {
                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->billmsgguest != '' &&
                    $wpenv->billmsgguestarray != '' &&
                    $wpenv->billmsgguesttemplate != '' &&
                    $wpnum != ''
                ) {
                    $billmsgguestarray = json_decode($wpenv->billmsgguestarray, true);
                    $msgdata = [];

                    foreach ($billmsgguestarray as $row) {
                        [$colname, $tbl] = $row;

                        if (endsWith($colname, 'sum')) {
                            $value = DB::table($tbl)
                                ->where('propertyid', $this->propertyid)
                                ->where('docid', $docid)
                                ->sum(removeSuffixIfExists($colname, 'sum'));
                        } elseif ($res = splitByJoin($colname)) {
                            $left = $res['left'];   // e.g., 'item'
                            $right = $res['right']; // e.g., 'itemmast'

                            $value = DB::table($tbl)
                                ->leftJoin($right, function ($join) use ($tbl, $right, $left) {
                                    $join->on("$tbl.$left", '=', "$right.Code")
                                        ->whereColumn("$right.RestCode", "$tbl.restcode");
                                })
                                ->where("$tbl.propertyid", $this->propertyid)
                                ->where("$tbl.docid", $docid)
                                ->pluck("$right.Name")
                                ->implode(', ');
                        } else {
                            $value = DB::table($tbl)
                                ->where('propertyid', $this->propertyid)
                                ->where('docid', $docid)
                                ->value($colname);
                        }

                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $wpnum, 'Bill Message', 'billmsgguesttemplate');
                }
            }

            $billprinty = $request->input('billprinty');

            if ($billprinty == 'Y' && empty($posroomno)) {
                $page = 'salebillentry';
                return view('property.layouts.prints', [
                    'printdata' => $printdata,
                    'page' => $page
                ]);
            } else if ($billprinty == '' && !empty($posroomno)) {
                return redirect('displaytable?dcode=' . $request->input('fixrestcode'))->with('infosale', [
                    'title' => 'Success',
                    'text' => 'Sale Bill Submitted Successfully Do You Want To Print Bill ?',
                    'printdata' => json_encode($printdata)
                ]);
            } else if (empty($posroomno) && empty($billprinty)) {
                return back()->with('infosale', [
                    'title' => 'Success',
                    'text' => 'Sale Bill Submitted Successfully Do You Want To Print Bill ?',
                    'printdata' => json_encode($printdata)
                ]);
            } else {
                $page = 'displaytable';
                return view('property.layouts.prints', [
                    'printdata' => $printdata,
                    'page' => $page
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        // if (!empty($posroomno)) {
        //     return redirect('displaytable?dcode=' . $request->input('fixrestcode'))->with('infosale', [
        //         'title' => 'Success',
        //         'text' => 'Sale Bill Submitted Successfully Do You Want To Print Bill Also',
        //         'printdata' => json_encode($printdata)
        //     ]);
        // } else {
        //     return back()->with('infosale', [
        //         'title' => 'Success',
        //         'text' => 'Sale Bill Submitted Successfully Do You Want To Print Bill Also',
        //         'printdata' => json_encode($printdata)
        //     ]);
        // }
    }

    public function salebillprint(Request $request)
    {
        return view('property.salebillprint');
    }

    public function salebillprint2(Request $request)
    {
        return view('property.salebillprint2');
    }

    public function getcompdetail(Request $request)
    {
        $comp = Companyreg::where('propertyid', $this->propertyid)->first();
        $menuhelp = MenuHelp::where('propertyid', $this->propertyid)->get();
        $data = [
            'comp' => $comp,
            'menuhelp' => $menuhelp
        ];
        return json_encode($data);
    }

    public function salebillprintitems(Request $request)
    {
        $billno = $request->input('billno');
        $vtype = $request->input('vtype');
        $vdate = $request->vdate;

        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('vno', $billno)->where('vdate', $vdate)->where('vtype', $vtype)->first();
        $items = Stock::selectRaw('
        MAX(stock.sno) AS srno,
        MAX(stock.taxper) AS taxper,
        SUM(stock.taxamt) AS taxamt,
        stock.rate,
        stock.itemrate,
        stock.remarks,
        MAX(stock.itemrate) AS itemrate,
        SUM(stock.qtyiss) AS qty,
        SUM(stock.amount) AS amt,
        MAX(i.name) AS itemname,
        MAX(i.hsncode) AS hsncode,
        MAX(i.dispcode) As dispcode,
        CASE
            WHEN SUM(stock.taxamt) = 0 THEN SUM(stock.amount)
            ELSE 0
        END AS nontaxable,
        CASE
            WHEN SUM(stock.taxamt) <> 0 THEN SUM(stock.amount)
            ELSE 0
        END AS taxable,
        unitmast.name AS unitname
    ')
            ->leftJoin('itemmast as i', function ($join) {
                $join->on('i.Code', '=', 'stock.item')
                    ->whereColumn('stock.itemrestcode', '=', 'i.restcode');
            })
            ->leftJoin('unitmast', 'unitmast.ucode', '=', 'stock.unit')
            ->where('stock.docid', $sale1->docid)
            ->groupBy('stock.item', 'stock.rate', 'stock.remarks')
            ->orderByRaw('MAX(i.name)')
            ->get();

        $taxes = Sale2::select(
            'revmast.name as taxname',
            'revmast.rev_code',
            'sale2.taxper',
            DB::raw('SUM(taxamt) as taxamt'),
            DB::raw('SUM(basevalue) as taxableamt')
        )
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'sale2.taxcode')
            ->where('sale2.docid', $sale1->docid)
            ->groupBy('revmast.rev_code', 'sale2.taxper', 'revmast.name')
            ->orderBy('sale2.taxper')
            ->get();

        $suntran = Suntran::select('suntran.*', 'depart.dis_print', 'depart.outlet_title', 'depart.company_title')
            ->leftJoin('depart', 'depart.dcode', '=', 'suntran.restcode')
            ->where('suntran.propertyid', $this->propertyid)->where('suntran.docid', $sale1->docid)->get();

        $waitername = ServerMast::where('propertyid', $this->propertyid)->where('scode', $sale1->waiter)->first();

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $sale1->restcode)->first();

        $tbro = $depart->nature == 'Outlet' ? 'Table No.' : 'Room No.';

        $data = [
            'items' => $items,
            'taxes' => $taxes,
            'sale1' => $sale1,
            'suntran' =>  $suntran,
            'waitername' => $waitername,
            'tbro' => $tbro,
            'depart' => $depart
        ];

        return json_encode($data);
    }

    public function getoutletdetails(Request $request)
    {
        $dcode = $request->input('dcode');
        $data = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();
        return json_encode($data);
    }

    public function salebillupdate(Request $request)
    {
        $validate = $request->validate([
            'pax' => 'required',
        ]);

        if (!$request->input('itemcode1')) {
            return back()->with('error', 'You can not delete all the items!');
        }

        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $currentYear = date('Y', strtotime($ncurdate));
        $nextYear = $currentYear + 1;
        if (date('m') < 4) {
            $date_from = ($previousYear = $currentYear - 1) . '-04-01';
            $date_to = $currentYear . '-03-31';
            $currfinancial = $previousYear;
        } else {
            $date_from = $currentYear . '-04-01';
            $date_to = $nextYear . '-03-31';
            $currfinancial = $currentYear;
        }

        $vtype = $request->input('vtype');
        $vno = $request->input('vnostock');
        $stockdocid = $request->input('stockdocid');
        $restcode = $request->input('fixrestcode');
        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();

        $enviro_pos = EnviroPos::where('propertyid', $this->propertyid)->first();

        if ($enviro_pos->possalebillauditlog == 'Y') {
            $sale1log = new Sale1log();
            $oldsale1data = Sale1::where('propertyid', $this->propertyid)
                ->where('docid', $stockdocid)
                ->where('restcode', $restcode)
                ->first();

            if ($oldsale1data) {
                $sale1log->fill($oldsale1data->toArray());
                $sale1log->save();
            }
            $oldsale2data = Sale2::where('propertyid', $this->propertyid)
                ->where('docid', $stockdocid)
                ->where('restcode', $restcode)
                ->get();

            if ($oldsale2data->isNotEmpty()) {
                foreach ($oldsale2data as $sale2record) {
                    $sale2log = new Sale2log();
                    $sale2log->fill($sale2record->toArray());
                    $sale2log->save();
                }
            }

            $oldstockdata = Stock::where('propertyid', $this->propertyid)
                ->where('docid', $stockdocid)
                ->where('restcode', $restcode)
                ->get();

            if ($oldstockdata->isNotEmpty()) {
                foreach ($oldstockdata as $oldstockrow) {
                    $stocklog = new Stocklog();
                    $stocklog->fill($oldstockrow->toArray());
                    $stocklog->save();
                }
            }

            $oldsuntrandata = Suntran::where('propertyid', $this->propertyid)
                ->where('docid', $stockdocid)
                ->where('restcode', $restcode)
                ->get();


            if ($oldsuntrandata->isNotEmpty()) {
                foreach ($oldsuntrandata as $oldsuntranrow) {
                    $suntranlog = new Suntranlog();
                    $suntranlog->fill($oldsuntranrow->toArray());
                    $suntranlog->save();
                }
            }
        }

        $roomno = $request->input('roomno') ?? $request->input('previousroomno');
        $roomdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('roomno', $roomno)->whereNull('type')->first();
        $totalamt = $request->input('totalamt');
        $discper = $request->input('discountfix');
        $discper = floatval(str_replace(',', '', number_format($discper, 2)));
        $discamt = $request->input('discountsundry');
        $taxable = $request->input('taxableamt');
        $servicehargeamt = $request->input('servicechargeamount') ?? '0.00';
        $servicechargefix = $request->input('servicechargefix') ?? '0.00';
        $netamount = $request->input('netamount');
        $cgst = $request->input('cgstamount');
        $sgst = $request->input('sgstamount');
        $igst = $request->input('igstamt');
        $nontaxable = $totalamt - $taxable;
        $inputr = $request->input('roundoffamount');
        $cleanedInput = str_replace('.', '', $inputr);

        if (strlen($cleanedInput) > 1) {
            $floatValue = substr($cleanedInput, 0, 1) . '.' . substr($cleanedInput, 1);
        } else {
            $floatValue = $cleanedInput;
        }

        $roundoff = number_format((float)$floatValue, 2, '.', '');

        $sundrycount = $request->input('sundrycount');

        for ($i = 1; $i <= $sundrycount; $i++) {
            $fstype = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $restcode)->where('sno', $i)->first();
            $svalue = $fstype->svalue;
            $baseamount = 0.00;
            $amount = 0.00;
            if ($fstype->disp_name == 'Discount') {
                $amount = $discamt;
                $svalue = $discper;
            }
            if ($fstype->disp_name == 'Service Charge') {
                $amount = $servicehargeamt;
                $svalue = $servicechargefix;
            }
            if ($fstype->disp_name == 'Amount') {
                $amount = $totalamt;
            }
            if ($fstype->disp_name == 'CGST') {
                $amount = $cgst;
            }
            if ($fstype->disp_name == 'SGST') {
                $amount = $sgst;
            }
            if ($fstype->disp_name == 'Round Off') {
                $amount = $roundoff;
                $baseamount = $roundoff + $netamount;
            }
            if ($fstype->disp_name == 'Net Amount') {
                $amount = $netamount;
            }

            $suntrandata = [
                'propertyid' => $this->propertyid,
                'docid' => $stockdocid,
                'svalue' => $svalue,
                'amount' => $amount,
                'baseamount' => $baseamount,
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];

            Suntran::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $i)->update($suntrandata);
        }

        if ($roundoff > 0) {
            $roundyn = 'Y';
        } else {
            $roundyn = 'N';
        }

        $sale1 = [
            'total' => $totalamt,
            'nontaxable' => $nontaxable ?? 0.00,
            'taxable' => $totalamt - ($discamt + $nontaxable) ?? 0.00,
            'party' => $request->input('company') ?? '',
            'discper' => $discper ?? 0.00,
            'discamt' => $discamt ?? 0.00,
            'roundoff' => $roundoff ?? 0.00,
            'netamt' => $netamount,
            'waiter' => $request->input('waiter'),
            'guaratt' => $request->input('pax'),
            'sgst' => $sgst ?? 0.00,
            'cgst' => $cgst ?? 0.00,
            'igst' => $igst ?? 0.00,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];

        Sale1::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->update($sale1);

        $guestprof = Guestprof::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->first();

        if ($guestprof) {
            $citycode = $request->input('customercity');
            $citydata = '';
            $statedata = '';
            $countrydata = '';
            if (!empty($citycode)) {
                $citydata = Cities::where('propertyid', $this->propertyid)->where('city_code', $citycode)->first();
                $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $citydata->state)->first();
                $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $citydata->country)->first();
            }

            $guestreward = [
                'total' => $totalamt,
                'billamt' => $netamount,
                'mobileno' => $request->input('phoneno'),
                'discamt' => $discamt ?? 0.00,
                'discper' => $discper ?? 0.00,
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];

            $dob = $request->input('birthdate');
            $age = Carbon::parse($dob)->age;

            $guestproft = [
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'name' => $request->input('customername'),
                'state_code' => $citydata->state ?? '',
                'country_code' => $citydata->country ?? '',
                'add1' => $request->input('address') ?? '',
                'city' => $citycode ?? '',
                'type' => $countrydata->Type ?? '',
                'mobile_no' => $request->input('phoneno'),
                'nationality' => $countrydata->nationality ?? '',
                'anniversary' => $request->input('anniversary') ?? null,
                'city_name' => $citydata->cityname ?? '',
                'state_name' => $statedata->name ?? '',
                'country_name' => $countrydata->name ?? '',
                'marital_status' => $request->input('anniversary') != '' ? 'Married' : 'Single',
                'zip_code' => $citydata->zipcode ?? '',
                'dob' => $dob ?? null,
                'age' => $age ?? '',
                'm_prof' => $guestprof,
                'likes' => $request->input('like') ?? '',
                'dislikes' => $request->input('dislike') ?? '',
            ];
            GuestReward::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->update($guestreward);
            GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof->guestcode)->update($guestproft);
        } else if ($request->input('phoneno') != '') {
            $findexist = GuestProf::where('propertyid', $this->propertyid)->where('mobile_no', $request->input('phoneno'))->first();

            if ($findexist != null) {
                $guestprof = $findexist->guestcode;
            } else {
                $maxguestprof = GuestProf::where('propertyid', $this->propertyid)->max('guestcode');
                $guestprof = ($maxguestprof === null) ? $this->propertyid . '10001' : ($guestprof = $this->propertyid . substr($maxguestprof, $this->ptlngth) + 1);
            }

            $citycode = $request->input('customercity');
            $citydata = '';
            $statedata = '';
            $countrydata = '';
            if (!empty($citycode)) {
                $citydata = Cities::where('propertyid', $this->propertyid)->where('city_code', $citycode)->first();
                $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $citydata->state)->first();
                $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $citydata->country)->first();
            }

            $guestreward = [
                'propertyid' => $this->propertyid,
                'docid' => $stockdocid,
                'custcode' => $guestprof,
                'vdate' => $ncurdate,
                'vtime' => date('H:i:s'),
                'restcode' => $restcode,
                'departname' => $depart->name,
                'billno' => $vno,
                'total' => $totalamt,
                'billamt' => $netamount,
                'rewardpoint' => 0.00,
                'redeempoint' => 0.00,
                'mobileno' => $request->input('phoneno'),
                'discamt' => $discamt ?? 0.00,
                'schemecode' => '',
                'saleupto' => 0.00,
                'rppointonamt' => 0.00,
                'rewardvalue' => 0.00,
                'reedemvalue' => 0.00,
                'regid' => '',
                'discper' => $discper ?? 0.00,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            $dob = $request->input('birthdate');
            $age = Carbon::parse($dob)->age;

            $guestproft = [
                'propertyid' => $this->propertyid,
                'docid' => $stockdocid,
                'folio_no' => $vno,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'complimentry' => '',
                'guestcode' => $guestprof,
                'name' => $request->input('customername'),
                'state_code' => $citydata->state ?? '',
                'country_code' => $citydata->country ?? '',
                'add1' => $request->input('address') ?? '',
                'add2' => '',
                'city' => $citycode ?? '',
                'type' => $countrydata->Type ?? '',
                'mobile_no' => $request->input('phoneno'),
                'email_id' => '',
                'nationality' => $countrydata->nationality ?? '',
                'anniversary' => $request->input('anniversary') ?? null,
                'guest_status' => '',
                'comments1' => '',
                'comments2' => '',
                'comments3' => '',
                'city_name' => $citydata->cityname ?? '',
                'state_name' => $statedata->name ?? '',
                'country_name' => $countrydata->name ?? '',
                'gender' => '',
                'marital_status' => $request->input('anniversary') != '' ? 'Married' : 'Single',
                'zip_code' => $citydata->zipcode ?? '',
                'con_prefix' => '',
                'dob' => $dob ?? null,
                'age' => $age ?? '',
                'pic_path' => '',
                'id_proof' => '',
                'idproof_no' => '',
                'issuingcitycode' => '',
                'issuingcityname' => '',
                'issuingcountrycode' => '',
                'issuingcountryname' => '',
                'expiryDate' => null,
                'vipStatus' => '',
                'paymentMethod' => '',
                'billingAccount' => '',
                'idpic_path' => '',
                'm_prof' => $guestprof,
                'father_name' => '',
                'likes' => $request->input('like') ?? '',
                'dislikes' => $request->input('dislike') ?? '',
                'fom' => 0,
                'pos' => 1,
            ];
            GuestReward::insert($guestreward);
            GuestProf::insert($guestproft);
        }

        $chkroomserv = Depart::where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();
        if (strtolower($chkroomserv->rest_type) == 'room service') {
            $paycode1 = 'ROOM' . $this->propertyid;
            $revdata1 = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $paycode1)->first();
            $paycharge1 = [
                'propertyid' => $this->propertyid,
                'docid' => $stockdocid,
                'vno' => $vno,
                'vtype' => $vtype,
                'comp_code' => $request->input('company'),
                'sno' => 1,
                'sno1' => 1,
                'vdate' => $ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $currfinancial,
                'paycode' => $paycode1,
                'comments' => '(RS) BILL NO.- ' . $vno,
                'paytype' => $revdata1->pay_type,
                'roomcat' => 'REST',
                'restcode' => $restcode,
                'roomno' => $roomno,
                'amtcr' => $netamount,
                'roomtype' => 'RO',
                'foliono' => 0,
                'billamount' => $netamount,
                'taxcondamt' => 0,
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];
            $paycode2 = 'TOUT' . $this->propertyid;
            $revdata2 = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $paycode2)->first();
            $paycharge2 = [
                'propertyid' => $this->propertyid,
                'docid' => $stockdocid,
                'vno' => $vno,
                'vtype' => $vtype,
                'comp_code' => $request->input('company'),
                'sno' => 2,
                'sno1' => 1,
                'vdate' => $ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $currfinancial,
                'paycode' => $paycode2,
                'comments' => '(RS) BILL NO.- ' . $vno,
                'paytype' => $revdata2->pay_type,
                'folionodocid' => $roomdata->docid,
                'restcode' => $restcode,
                'roomno' => $roomno,
                'roomcat' => $roomdata->roomcat,
                'amtdr' => $netamount,
                'roomtype' => $roomdata->roomtype,
                'foliono' => $roomdata->folioNo,
                'guestprof' => $roomdata->guestprof,
                'billamount' => $netamount,
                'taxcondamt' => 0,
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];
            // Paycharge::where('docid', $stockdocid)->where('sno', 1)->update($paycharge1);
            // Paycharge::where('docid', $stockdocid)->where('sno', 2)->update($paycharge2);
        }

        // Sale1::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->update($sale1);

        $prefixes = ['itemcode', 'itemnumber', 'itemname', 'discapp', 'description', 'quantity', 'rate', 'amount', 'fixamount', 'tax_rate'];
        $count = 0;
        $sale2 = [];
        $totalitemsum = $request->input('totalitemsum');
        $countitemstock = Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->count();
        // echo $totalitemsum . ' - ' . $countitemstock;
        // exit;
        if ($totalitemsum == $countitemstock) {
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'itemcode') === 0) {
                    $count++;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                $data = [];
                $isEmptyRow = true;

                foreach ($prefixes as $prefix) {
                    $sno = $request->input('itemnumber' . $i);
                    $value = $request->input($prefix . $i);
                    $taxratepos = $request->input('taxrate_sum' . $i);
                    $tax_rate = $request->input('tax_rate' . $i);
                    $itemqty = $request->input('quantity' . $i);
                    $itemratetmp = $request->input('taxedrate' . $i);
                    $itemrate = floor($itemratetmp * 100) / 100;
                    $itemtruerate = $request->input('rate' . $i);
                    $itemcamttmp = $request->input('fixamount' . $i);
                    $itemcamt = floor($itemcamttmp * 100) / 100;
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $itemamt = $itemqty * $itemrate;
                    if ($discper != 0) {
                        $discamt = $itemamt * $discper / 100;
                    } else {
                        $discamt = 0.00;
                    }

                    $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->first();

                    if ($taxratepos != 0) {
                        $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->value('ItemCatCode');
                        $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                        $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                        foreach ($fetchtaxes as $taxesrow) {
                            $titemqty = $request->input('quantity' . $i);
                            $titemrate = $request->input('rate' . $i);
                            $titemamt = $titemqty * $titemrate;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            if ($discper != 0) {
                                $titemamt = $titemamt - ($titemamt * $discper) / 100;
                                $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            }
                            $sale2[] = [
                                'sno' => $sno,
                                'sno1' => $taxesrow->sno,
                                'basevalue' => $titemamt,
                                'taxper' => $taxesrow->rate,
                                'taxamt' => $ttaxamt,
                                'u_updatedt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'e',
                            ];
                        }
                    }

                    $stock = [
                        'item' => $request->input('itemcode' . $i),
                        'qtyiss' => $request->input('quantity' . $i),
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $itemrate,
                        'amount' => $itemcamt,
                        'taxper' => $tax_rate,
                        'taxamt' => $taxamt,
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('description' . $i) ?? '',
                        'total' => $netamount,
                        'discapp' => $request->input('discapp' . $i) ?? '',
                        'roundoff' => $roundyn,
                        'departcode' => $itemmast->Kitchen ?? '',
                        'godowncode' => $itemmast->Kitchen ?? '',
                        'itemrate' => $itemtruerate,
                        'itemrestcode' => $itemmast->RestCode ?? '',
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    foreach ($sale2 as $row) {
                        Sale2::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $row['sno'])->where('sno1', $row['sno1'])->update($row);
                    }
                    // echo $i . ' - ' . $request->input('itemcode' . $i) . ' - ' . $request->input('description' . $i) . '</br>';
                    Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $sno)->where('item', $request->input('itemcode' . $i))->update($stock);
                }
            }
        } elseif ($totalitemsum > $countitemstock) {
            $sale2innew = [];
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'itemcode') === 0) {
                    $count++;
                }
            }
            for ($j = 1; $j <= $count; $j++) {
                $data = [];
                $isEmptyRow = true;

                foreach ($prefixes as $prefix) {
                    $sno = $request->input('itemnumber' . $j);
                    $value = $request->input($prefix . $j);
                    $taxratepos = $request->input('taxrate_sum' . $j);
                    $itemqty = $request->input('quantity' . $j);
                    $itemrate = $request->input('rate' . $j);
                    $itemcamt = $request->input('fixamount' . $j);
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $itemamt = $itemqty * $itemrate;
                    if ($discper != 0) {
                        $discamt = $itemamt * $discper / 100;
                    } else {
                        $discamt = 0.00;
                    }

                    $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $j))->first();

                    if ($taxratepos != 0) {
                        $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $j))->value('ItemCatCode');
                        $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                        $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                        foreach ($fetchtaxes as $taxesrow) {
                            $titemqty = $request->input('quantity' . $j);
                            $titemrate = $request->input('rate' . $j);
                            $titemamt = $titemqty * $titemrate;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            if ($discper != 0) {
                                $titemamt = $titemamt - ($titemamt * $discper) / 100;
                                $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            }
                            $sale2[] = [
                                'sno' => $sno,
                                'sno1' => $taxesrow->sno,
                                'basevalue' => $titemamt,
                                'taxper' => $taxesrow->rate,
                                'taxamt' => $ttaxamt,
                                'u_updatedt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'e',
                            ];
                        }
                    }

                    $stock = [
                        'item' => $request->input('itemcode' . $j),
                        'qtyiss' => $request->input('quantity' . $j),
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('rate' . $j),
                        'amount' => $itemamt,
                        'taxper' => $taxratepos,
                        'taxamt' => $taxamt,
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('description' . $j) ?? '',
                        'total' => $netamount,
                        'discapp' => $request->input('discapp' . $j) ?? '',
                        'roundoff' => $roundyn,
                        'departcode' => $itemmast->RestCode ?? '',
                        'godowncode' => $itemmast->Kitchen ?? '',
                        'itemrate' => $itemrate,
                        'itemrestcode' => $itemmast->RestCode ?? '',
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    foreach ($sale2 as $row) {
                        Sale2::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $row['sno'])->where('sno1', $row['sno1'])->update($row);
                    }
                    Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $sno)->update($stock);
                }
            }
            // Insering New Item
            $count2 = Stock::where('docid', $stockdocid)->where('propertyid', $this->propertyid)->count();
            $newcount = $count2 + 1;

            $k = 1;
            for ($i = $newcount; $i <= $totalitemsum; $i++) {
                echo 'coming in loop </br>';
                $data = [];
                $isEmptyRow = true;
                // $sno = $request->input('itemnumber' . $i);
                $maxsno = Stock::where('docid', $stockdocid)->where('propertyid', $this->propertyid)->max('sno');
                foreach ($prefixes as $prefix) {
                    $sno = $maxsno + $k;
                    $k = $maxsno + 1;
                    // echo $k . '</br>';
                    $value = $request->input($prefix . $i);
                    $taxratepos = $request->input('taxrate_sum' . $i);
                    $itemqty = $request->input('quantity' . $i);
                    $itemrate = $request->input('rate' . $i);
                    $itemcamt = $request->input('fixamount' . $i);
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $itemamt = $itemqty * $itemrate;
                    if ($discper != 0) {
                        $discamt = $itemamt * $discper / 100;
                    } else {
                        $discamt = 0.00;
                    }

                    $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->first();

                    if ($taxratepos != 0) {
                        $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->value('ItemCatCode');
                        $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                        $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                        foreach ($fetchtaxes as $taxesrow) {
                            $titemqty = $request->input('quantity' . $i);
                            $titemrate = $request->input('rate' . $i);
                            $titemamt = $titemqty * $titemrate;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            if ($discper != 0) {
                                $titemamt = $titemamt - ($titemamt * $discper) / 100;
                                $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            }
                            $sale2innew[] = [
                                'propertyid' => $this->propertyid,
                                'docid' => $stockdocid,
                                'sno' => $sno,
                                'sno1' => $taxesrow->sno,
                                'vno' => $vno,
                                'vtype' => $vtype,
                                'vdate' => $ncurdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $currfinancial,
                                'restcode' => $restcode,
                                'taxcode' => $taxesrow->tax_code,
                                'basevalue' => $titemamt,
                                'taxper' => $taxesrow->rate,
                                'taxamt' => $ttaxamt,
                                'delflag' => 'N',
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];
                        }
                    }

                    $stock = [
                        'propertyid' => $this->propertyid,
                        'docid' => $stockdocid,
                        'sno' => $k,
                        'vno' => $vno,
                        'vtype' => $vtype,
                        'vdate' => $ncurdate,
                        'vtime' => date('H:i:s'),
                        'vprefix' => $currfinancial,
                        'restcode' => $restcode,
                        'roomno' => $roomno,
                        'roomcat' => $roomdata->roomcat ?? 'TABLE',
                        'roomtype' => $roomdata->roomtype ?? 'TB',
                        'contradocid' => '',
                        'contrasno' => '',
                        'item' => $request->input('itemcode' . $i),
                        'qtyiss' => $request->input('quantity' . $i),
                        'qtyrec' => '0',
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('rate' . $i),
                        'amount' => $itemamt,
                        'taxper' => $taxratepos,
                        'taxamt' => $taxamt,
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('description' . $i) ?? '',
                        'voidyn' => '',
                        'remarks' => '',
                        'kotdocid' => $request->input('kotdocid') ?? '',
                        'kotsno' => $request->input('kotsno' . $i) ?? '0',
                        'total' => $netamount,
                        'discapp' => $request->input('discapp' . $i) ?? '',
                        'roundoff' => $roundyn,
                        'departcode' => $itemmast->Kitchen ?? '',
                        'godowncode' => $itemmast->Kitchen ?? '',
                        'chalqty' => '',
                        'recdqty' => '',
                        'accqty' => '',
                        'rejqty' => '',
                        'recdunit' => '',
                        'specification' => '',
                        'itemrate' => $itemrate,
                        'delflag' => 'N',
                        'landval' => 0.00,
                        'convratio' => 0.00,
                        'indentdocid' => '',
                        'indentsno' => 0,
                        'issqty' => 0.00,
                        'issueunit' => '',
                        'freesno' => 0,
                        'schemecode' => '',
                        'seqno' => 0,
                        'company' => '',
                        'itemrestcode' => $itemmast->RestCode ?? '',
                        'schrgapp' => '',
                        'schrgper' => 0.00,
                        'schrgamt' => 0.00,
                        'refdocid' => '',
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    foreach ($sale2innew as $row) {
                        $existingRow = Sale2::where('sno', $row['sno'])->where('sno1', $row['sno1'])->where('docid', $row['docid'])->where('propertyid', $row['propertyid'])->first();
                        if (!$existingRow) {
                            Sale2::insert($row);
                        }
                    }
                    Stock::insert($stock);
                }
                // exit;
            }
        } elseif ($totalitemsum < $countitemstock) {
            $sale2innew = [];
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'itemcode') === 0) {
                    $count++;
                }
            }
            $delid = [];
            for ($j = 1; $j <= $count; $j++) {
                $data = [];
                $isEmptyRow = true;

                foreach ($prefixes as $prefix) {
                    $sno = $request->input('itemnumber' . $j);
                    $delid[] = $sno;
                    $value = $request->input($prefix . $j);
                    $taxratepos = $request->input('taxrate_sum' . $j);
                    $itemqty = $request->input('quantity' . $j);
                    $itemrate = $request->input('rate' . $j);
                    $itemcamt = $request->input('fixamount' . $j);
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $itemamt = $itemqty * $itemrate;
                    if ($discper != 0) {
                        $discamt = $itemamt * $discper / 100;
                    } else {
                        $discamt = 0.00;
                    }

                    $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->first();

                    if ($taxratepos != 0) {
                        $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->value('ItemCatCode');
                        $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                        $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                        foreach ($fetchtaxes as $taxesrow) {
                            $titemqty = $request->input('quantity' . $j);
                            $titemrate = $request->input('rate' . $j);
                            $titemamt = $titemqty * $titemrate;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            if ($discper != 0) {
                                $titemamt = $titemamt - ($titemamt * $discper) / 100;
                                $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            }
                            $sale2[] = [
                                'sno' => $sno,
                                'sno1' => $taxesrow->sno,
                                'basevalue' => $titemamt,
                                'taxper' => $taxesrow->rate,
                                'taxamt' => $ttaxamt,
                                'u_updatedt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'e',
                            ];
                        }
                    }

                    $stock = [
                        'item' => $request->input('itemcode' . $j),
                        'qtyiss' => $request->input('quantity' . $j),
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('rate' . $j),
                        'amount' => $itemamt,
                        'taxper' => $taxratepos,
                        'taxamt' => $taxamt,
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('description' . $j) ?? '',
                        'total' => $netamount,
                        'discapp' => $request->input('discapp' . $j) ?? '',
                        'roundoff' => $roundyn,
                        'departcode' => $itemmast->Kitchen ?? '',
                        'godowncode' => $itemmast->Kitchen ?? '',
                        'itemrate' => $itemrate,
                        'itemrestcode' => $itemmast->RestCode ?? '',
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    foreach ($sale2 as $row) {
                        Sale2::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $row['sno'])->where('sno1', $row['sno1'])->update($row);
                    }
                    Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('sno', $sno)->update($stock);
                }
            }
            Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->whereNotIn('sno', $delid)->delete();
            Sale2::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->whereNotIn('sno', $delid)->delete();

            $count2 = $countitemstock + 1;
            for ($i = $count2; $i <= $totalitemsum; $i++) {
                $data = [];
                $isEmptyRow = true;

                foreach ($prefixes as $prefix) {
                    $sno = $request->input('itemnumber' . $i);
                    $value = $request->input($prefix . $i);
                    $taxratepos = $request->input('taxrate_sum' . $i);
                    $itemqty = $request->input('quantity' . $i);
                    $itemrate = $request->input('rate' . $i);
                    $itemcamt = $request->input('fixamount' . $i);
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $itemamt = $itemqty * $itemrate;
                    if ($discper != 0) {
                        $discamt = $itemamt * $discper / 100;
                    } else {
                        $discamt = 0.00;
                    }

                    $itemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->first();

                    if ($taxratepos != 0) {
                        $itemcat = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('itemcode' . $i))->value('ItemCatCode');
                        $cattax = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->where('Code', $itemcat)->value('TaxStru');
                        $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $cattax)->get();
                        foreach ($fetchtaxes as $taxesrow) {
                            $titemqty = $request->input('quantity' . $i);
                            $titemrate = $request->input('rate' . $i);
                            $titemamt = $titemqty * $titemrate;
                            $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            if ($discper != 0) {
                                $titemamt = $titemamt - ($titemamt * $discper) / 100;
                                $ttaxamt = ($titemamt * $taxesrow->rate) / 100;
                            }
                            $sale2innew[] = [
                                'propertyid' => $this->propertyid,
                                'docid' => $stockdocid,
                                'sno' => $sno,
                                'sno1' => $taxesrow->sno,
                                'vno' => $vno,
                                'vtype' => $vtype,
                                'vdate' => $ncurdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $currfinancial,
                                'restcode' => $restcode,
                                'taxcode' => $taxesrow->tax_code,
                                'basevalue' => $titemamt,
                                'taxper' => $taxesrow->rate,
                                'taxamt' => $ttaxamt,
                                'delflag' => 'N',
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];
                        }
                    }

                    $stock = [
                        'propertyid' => $this->propertyid,
                        'docid' => $stockdocid,
                        'sno' => $sno,
                        'vno' => $vno,
                        'vtype' => $vtype,
                        'vdate' => $ncurdate,
                        'vtime' => date('H:i:s'),
                        'vprefix' => $currfinancial,
                        'restcode' => $restcode,
                        'roomno' => $roomno,
                        'roomcat' => $roomdata->roomcat ?? '',
                        'roomtype' => $roomdata->roomtype ?? '',
                        'contradocid' => '',
                        'contrasno' => '',
                        'item' => $request->input('itemcode' . $i),
                        'qtyiss' => $request->input('quantity' . $i),
                        'qtyrec' => '0',
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('rate' . $i),
                        'amount' => $itemamt,
                        'taxper' => $taxratepos,
                        'taxamt' => $taxamt,
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('description' . $i) ?? '',
                        'voidyn' => '',
                        'remarks' => '',
                        'kotdocid' => $request->input('kotdocid'),
                        'kotsno' => $request->input('kotsno' . $i) ?? '0',
                        'total' => $netamount,
                        'discapp' => $request->input('discapp' . $i) ?? '',
                        'roundoff' => $roundyn,
                        'departcode' => $itemmast->Kitchen ?? '',
                        'godowncode' => $itemmast->Kitchen ?? '',
                        'chalqty' => '',
                        'recdqty' => '',
                        'accqty' => '',
                        'rejqty' => '',
                        'recdunit' => '',
                        'specification' => '',
                        'itemrate' => $itemrate,
                        'delflag' => 'N',
                        'landval' => 0.00,
                        'convratio' => 0.00,
                        'indentdocid' => '',
                        'indentsno' => 0,
                        'issqty' => 0.00,
                        'issueunit' => '',
                        'freesno' => 0,
                        'schemecode' => '',
                        'seqno' => 0,
                        'company' => '',
                        'itemrestcode' => $itemmast->RestCode ?? '',
                        'schrgapp' => '',
                        'schrgper' => 0.00,
                        'schrgamt' => 0.00,
                        'refdocid' => '',
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    foreach ($sale2innew as $row) {
                        $existingRow = Sale2::where('sno', $row['sno'])->where('sno1', $row['sno1'])->where('docid', $row['docid'])->where('propertyid', $row['propertyid'])->first();
                        if (!$existingRow) {
                            Sale2::insert($row);
                        }
                    }
                    Stock::insert($stock);
                }
            }
        }

        return back()->with('success', 'Sale Bill Updated Successfully!');
    }

    public function posparameter(Request $request)
    {
        $permission = revokeopen(121321);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = EnviroPos::where('propertyid', $this->propertyid)->first();
        $subgroup = SubGroup::where('propertyid', $this->propertyid)->groupBy('sub_code')->get();
        $revmast = Revmast::where('propertyid', $this->propertyid)->where('pay_type', 'Cash')->where('field_type', 'P')->where('nature', 'Cash')->get();
        $billrows = Depart::select('printersetup.restcode', 'printersetup.module', 'printersetup.description', 'printersetup.printerpath', 'depart.*')
            ->leftJoin('printersetup', 'printersetup.restcode', '=', 'depart.dcode')
            ->where('depart.propertyid', $this->propertyid)
            ->where(function ($query) {
                $query->where('depart.rest_type', 'FOM')
                    ->orWhere('depart.pos', 'Y');
            })->get();

        $kotrows = Depart::select('printersetup.restcode', 'printersetup.module', 'printersetup.description', 'printersetup.printerpath', 'depart.*')
            ->leftJoin('printersetup', 'printersetup.restcode', '=', 'depart.dcode')
            ->where('depart.propertyid', $this->propertyid)
            ->where('depart.pos', 'Y')->where('depart.kot_yn', 'Y')->get();
        return view('property.posparameter', [
            'data' => $data,
            'subgroup' => $subgroup,
            'revmast' => $revmast,
            'billrows' => $billrows,
            'kotrows' => $kotrows
        ]);
    }

    public function posgeneralparam(Request $request)
    {

        $permission = revokeopen(121114);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $billrows = Depart::leftJoin('printersetup', function ($join) {
            $join->on('printersetup.restcode', '=', 'depart.dcode')
                ->where('printersetup.mark', '=', 'B');
        })
            ->where('depart.propertyid', $this->propertyid)
            ->where(function ($query) {
                $query->where('depart.rest_type', 'FOM')
                    ->orWhere('depart.pos', 'Y');
            })
            ->select(
                'printersetup.restcode',
                'printersetup.module',
                'printersetup.description',
                'printersetup.printerpath',
                'depart.*'
            )
            ->get();

        $kotrows = Depart::select(
            'printersetup.restcode',
            'printersetup.module',
            'printersetup.description',
            'printersetup.printerpath',
            'printersetup.kitchen',
            'depart.*'
        )
            ->leftJoin('printersetup', function ($join) {
                $join->on('printersetup.restcode', '=', 'depart.dcode')
                    ->where('printersetup.mark', '=', 'K');
            })
            ->where('depart.propertyid', $this->propertyid)
            ->where('depart.pos', 'Y')
            ->where('depart.kot_yn', 'Y')
            ->get();
        $kitchen = Depart::where('propertyid', $this->propertyid)->where('nature', 'Kitchen')->get();
        return view('property.posgeneralparam', [
            'billrows' => $billrows,
            'kotrows' => $kotrows,
            'kitchen' => $kitchen
        ]);
    }

    public function posgeneralsubmit(Request $request)
    {

        $permission = revokeopen(121321);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'kotatnightaudit' => 'required',
            'posbillatnightaudit' => 'required',
            'possalebillauditlog' => 'required',
            'modifyentryinbackdate' => 'required',
        ]);

        try {
            $data = [
                'kotatnightaudit' => $request->input('kotatnightaudit'),
                'posbillatnightaudit' => $request->input('posbillatnightaudit'),
                'possalebillauditlog' => $request->input('possalebillauditlog'),
                'modifyentryinbackdate' => $request->input('modifyentryinbackdate'),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e',
            ];

            EnviroPos::where('propertyid', $this->propertyid)->update($data);
        } catch (\Exception $th) {
            return back()->with('error', 'Unable To Update General Parameter: ' . $th->getMessage());
        }

        return back()->with('success', 'General Parameter Updated Successfully');
    }

    public function posoutletsubmit(Request $request)
    {
        $permission = revokeopen(121321);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'cashpaytype' => 'required',
            'roundofftype' => 'required',
            'reportingonsalebill' => 'required',
            'postposdiscseperately' => 'required',
        ]);

        try {
            $data = [
                'cashpaytype' => $request->input('cashpaytype'),
                'roundofftype' => $request->input('roundofftype'),
                'reportingonsalebill' => $request->input('reportingonsalebill'),
                'postposdiscseperately' => $request->input('postposdiscseperately'),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e',
            ];

            EnviroPos::where('propertyid', $this->propertyid)->update($data);
        } catch (\Exception $th) {
            return back()->with('error', 'Unable To Update Outlet Parameter: ' . $th->getMessage());
        }

        return back()->with('success', 'Outlet Parameter Updated Successfully');
    }

    public function poskotsubmit(Request $request)
    {
        $permission = revokeopen(121321);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'kotoutletselection' => 'required',
            'printeditkot' => 'required',
            'printkot' => 'required'
        ]);

        try {
            $data = [
                'printkot' => $request->input('printkot'),
                'kotoutletselection' => $request->input('kotoutletselection'),
                'printeditkot' => $request->input('printeditkot'),
                'nckot' => $request->input('nckot'),
                'kotheader1' => $request->input('kotheader1'),
                'kotheader2' => $request->input('kotheader2'),
                'kotheader3' => $request->input('kotheader3'),
                'kotheader4' => $request->input('kotheader4'),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e',
            ];

            EnviroPos::where('propertyid', $this->propertyid)->update($data);
        } catch (\Exception $th) {
            return back()->with('error', 'Unable To Update KOT Parameter: ' . $th->getMessage());
        }

        return back()->with('success', 'KOT Parameter Updated Successfully');
    }

    public function posordersubmit(Request $request)
    {
        $permission = revokeopen(121321);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'bookingpartyac' => 'required',
        ]);

        try {
            $data = [
                'bookingpartyac' => $request->input('bookingpartyac'),
                'slipfooter1' => $request->input('slipfooter1'),
                'slipfooter2' => $request->input('slipfooter2'),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e',
            ];

            EnviroPos::where('propertyid', $this->propertyid)->update($data);
        } catch (\Exception $th) {
            return back()->with('error', 'Unable To Update Order Booking Parameter: ' . $th->getMessage());
        }

        return back()->with('success', 'Order Booking Parameter Updated Successfully');
    }

    public function fetchsingledcode(Request $request)
    {
        $dcode = $request->input('dcode');
        $data = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();
        return json_encode($data);
    }

    public function posbillprintsubmit(Request $request)
    {

        $totalrows = $request->input('btotalrows');
        try {
            PrintingSetup::where('mark', 'B')->where('propertyid', $this->propertyid)->delete();
            for ($i = 1; $i <= $totalrows; $i++) {
                $data = new PrintingSetup();
                $data->propertyid = $this->propertyid;
                $data->module =  $request->input('bmodule' . $i);
                $data->restcode =  $request->input('bdcode' . $i);
                $data->description =  $request->input('bdescription' . $i) ?? '';
                $data->printerpath =  $request->input('bprintpath' . $i) ?? '';
                $data->u_entdt =  $this->currenttime;
                $data->u_name =  Auth::user()->u_name;
                $data->u_ae = 'a';
                $data->mark = 'B';
                $data->save();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Unable To Update Bill Print Parameter: ' . $e->getMessage());
        }

        return back()->with('success', 'Bill Printing Parameter Updated Successfully');
    }

    public function poskotprintsubmit(Request $request)
    {
        $permission = revokeopen(121114);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $totalrows = $request->input('ktotalrows');
        try {
            PrintingSetup::where('mark', 'K')->where('propertyid', $this->propertyid)->delete();
            for ($i = 1; $i <= $totalrows; $i++) {

                $data = new PrintingSetup();
                $data->propertyid = $this->propertyid;
                $data->module =  $request->input('kmodule' . $i);
                $data->kitchen = $request->input('kitchen' . $i);
                $data->restcode =  $request->input('kdcode' . $i);
                $data->description =  $request->input('kdescription' . $i) ?? '';
                $data->printerpath =  $request->input('kprintpath' . $i) ?? '';
                $data->u_entdt =  $this->currenttime;
                $data->u_name =  Auth::user()->u_name;
                $data->u_ae =  'a';
                $data->mark =  'K';
                $data->save();
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable To Update Kot Print Parameter: ' . $e->getMessage());
        }

        return back()->with('success', 'KOT Printing Parameter Updated Successfully');
    }

    public function salebillsettle(Request $request)
    {
        $vno = $request->query('vno');
        $sale1docid = $request->query('sale1docid');

        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('docid', $sale1docid)->first();

        $megedchk = !empty($sale1->mergedwith) ? true : false;

        $mergedwith = !empty($sale1->mergedwith) ? explode(',', $sale1->mergedwith) : [$sale1->docid];

        $dcode = $sale1->restcode;
        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();
        $paycharge1 = Paycharge::select('paycharge.settledate', 'paycharge.sno', 'paycharge.sno1', 'paycharge.billamount', 'paycharge.roomno', 'paycharge.restcode', 'paycharge.amtcr', 'revmast.rev_code', 'revmast.name', 'paycharge.amtdr', 'paycharge.vdate')
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->whereNot('paycharge.amtcr', 0.00)
            ->whereIn('paycharge.docid', $mergedwith)->first();
        $checked = 'false';
        $checkroom = Paycharge::where('folionodocid', $sale1->folionodocid)->where('billno', '!=', '0')->first() ?? '';
        if ($checkroom) {
            $checked = 'true';
        }

        $paycharge2 = Paycharge::whereIn('paycharge.docid', $mergedwith)->where('propertyid', $this->propertyid)->orderBy('sno', 'ASC')->get();

        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $records = DB::table('revmast')
            ->select('revmast.name', 'revmast.rev_code', 'revmast.nature', 'revmast.field_type', 'revmast.flag_type', 'depart_pay.pay_code')
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where('revmast.field_type', '=', 'P')
            ->where('revmast.propertyid', $this->propertyid)
            ->get();

        $roomno = RoomOcc::leftJoin('paycharge', function ($join) {
            $join->on('paycharge.roomno', '=', 'roomocc.roomno')
                ->on('paycharge.propertyid', '=', 'roomocc.propertyid');
        })
            ->where('roomocc.propertyid', $this->propertyid)
            ->where(function ($query) {
                $query->where('paycharge.billno', 0)
                    ->orWhereNull('paycharge.billno');
            })
            ->whereNull('roomocc.type')
            ->groupBy('roomocc.roomno')
            ->orderBy('roomocc.roomno')
            ->select('roomocc.roomno')
            ->get();

        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name', 'ASC')->get();

        $totalsalerow =  Sale1::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->get();

        $paidamt = Paycharge::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->whereNot('amtcr', 0.00)->sum('amtcr');
        $balance = $totalsalerow->sum('netamt') - $paidamt;

        // if ($megedchk == true) {
        $paidrows = Paycharge::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->whereNot('amtcr', 0.00)
            // ->groupBy('amtcr')
            ->get();
        // } else {
        // }


        // return $paidrows->sum('amtcr');

        return view('property.salebillsettle', [
            'vno' => $vno,
            'roomnos' => $roomno,
            'company' => $company,
            'paidamt' => $paidamt,
            'balance' => $balance,
            'companydata' => $companydata,
            'revdata' => $records,
            'paidrows' => $paidrows,
            'sale1' => $sale1,
            'totalsalerow' => $totalsalerow,
            'paycharge1' => $paycharge1,
            'paycharge2' => $paycharge2,
            'checkroom' => $checkroom,
            'checked' => $checked,
            'megedchk' => $megedchk
        ]);
    }

    public function nillsettle(Request $request)
    {
        $docid = $request->docid;
        $sno1 = $request->sno1;

        $uppaycharge = [
            'settledate' => $this->ncurdate,
            'u_updatedt' => $this->currenttime,
        ];
        $uproomocc = [
            'userchkoutdate' => $this->ncurdate,
            'chkoutuser' => Auth::user()->u_name,
            'type' => 'O',
            'chkoutdate' => $this->ncurdate,
            'chkouttime' => date('H:i'),
            'u_updatedt' => $this->currenttime,
        ];

        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))->where('leaderyn', 'Y')->first();
        if ($rocc) {
            $fbillno = Paycharge::where('folionodocid', $rocc->docid)->where('msno1', $rocc->sno1)->value('billno');
            Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $rocc->docid)->where('msno1', $rocc->sno1)
                ->update($uppaycharge);
            RoomOcc::where('propertyid', $this->propertyid)->where('docid', $rocc->docid)->update($uproomocc);
        } else {
            $fbillno = DB::table('paycharge')->where('folionodocid', $request->input('docid'))->where('sno1', $request->input('sno1'))->value('billno');
            DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $request->input('docid'))->where('sno1', $request->input('sno1'))
                ->update($uppaycharge);
            DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))->where('sno1', $request->input('sno1'))
                ->where('sno', $request->input('sno'))->update($uproomocc);
        }

        return response()->json([
            'status' => true,
            'message' => 'Settlement Updated'
        ]);
    }

    public function salebillsettlesubmit(Request $request)
    {

        $sale1docid = $request->input('sale1docid');
        $rowcount = $request->input('rowcount') + 1;

        // return $rowcount;

        for ($i = 1; $i <= $rowcount; $i++) {
            $chargetype[] = $request->input('chargetype' . $i);
        }

        $string = ['ROOM SETTLEMENT', 'Room'];

        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('docid', $sale1docid)->first();

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $sale1->restcode)->first();
        $paycode1 = 'ROOM' . $this->propertyid;
        $netamount = $request->input('netamount');
        $revdata1 = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $paycode1)->first();
        $roomno = $request->input('roomno') ?? $request->input('fixroomno');

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $sale1->vtype)
            ->whereDate('date_from', '<=', $this->ncurdate)
            ->whereDate('date_to', '>=', $this->ncurdate)
            ->first();

        $vprefix = $chkvpf->prefix;

        $mergedwith = !empty($sale1->mergedwith) ? explode(',', $sale1->mergedwith) : [$sale1->docid];

        Paycharge::where('propertyid', $this->propertyid)->whereIn('docid', $mergedwith)->delete();
        $msno1 = 0;
        $roomoccdocid = $request->roomoccdocid;
        if ($roomoccdocid) {
            $roomdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $roomoccdocid)->where('roomno', $roomno)->first();
        } else {
            $roomdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('roomno', $roomno)->whereNull('type')->first();
        }
        if ($roomdata) {
            $rocc = RoomOcc::where('docid', $roomdata->docid)->where('leaderyn', 'Y')->first();
            if ($rocc) {
                $msno1 = $rocc->sno1;
            }
        }

        $result = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $sale1->folionodocid)
            ->where('roomno', $roomno)->whereNull('type')->first();

        if (array_intersect($string, $chargetype)) {
            $paycode2 = 'TOUT' . $this->propertyid;
            $paycharge2 = [
                'propertyid' => $this->propertyid,
                'docid' => $sale1docid,
                'vno' => $sale1->vno,
                'vtype' => $sale1->vtype,
                'sno' => 2,
                'sno1' => $roomdata->sno1 ?? '',
                'msno1' => $msno1,
                'vdate' => $sale1->vdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'paycode' => $paycode2,
                'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $sale1->vno,
                'paytype' => $revdata1->pay_type,
                'folionodocid' => $roomdata->docid,
                'restcode' => $sale1->restcode,
                'roomno' => $roomno,
                'roomcat' => $roomdata->roomcat,
                'amtdr' => $netamount,
                'roomtype' => $roomdata->roomtype,
                'foliono' => $roomdata->folioNo,
                'guestprof' => $roomdata->guestprof,
                'billamount' => $netamount,
                'taxcondamt' => 0,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];
            DB::table('paycharge')->insert($paycharge2);
        }

        $snoCache = [];
        $insertRows = [];

        $buildInsertData = function ($sale1row, $amt, $chargeIndex, $sno) use (
            $request,
            $result,
            $vprefix,
            $msno1
        ) {
            $paycode = $request->input('chargecode' . $chargeIndex);
            $paycodes = Revmast::where('propertyid', $sale1row->propertyid)
                ->where('rev_code', $paycode)
                ->first();

            return [
                'propertyid'   => $sale1row->propertyid,
                'docid'        => $sale1row->docid,
                'vno'          => $sale1row->vno,
                'vtype'        => $sale1row->vtype,
                'sno'          => $sno,
                'sno1'         => $result->sno1 ?? $sale1row->sno1,
                'msno1'        => $msno1,
                'chqno'        => $request->input('checkno') ?: $request->input('referencenoupi'),
                'cardno'       => $request->input('crnumber'),
                'cardholder'   => $request->input('holdername'),
                'expdate'      => $request->input('expdatecr'),
                'bookno'       => $request->input('batchno'),
                'vdate'        => $this->ncurdate,
                'vtime'        => date('H:i:s'),
                'vprefix'      => $vprefix,
                'comp_code'    => $request->input('compcode' . $chargeIndex) ?? '',
                'paycode'      => $paycode,
                'paytype'      => $paycodes->pay_type ?? '',
                'comments'     => $request->input('chargenarration' . $chargeIndex),
                'guestprof'    => $result->guestprof ?? '',
                'roomno'       => $sale1row->roomno,
                'amtcr'        => $amt,
                'roomtype'     => $result->roomtype ?? 'TB',
                'roomcat'      => $result->roomcat ?? 'TABLE',
                'foliono'      => 0,
                'restcode'     => $sale1row->restcode,
                'billamount'   => $sale1row->netamount,
                'taxper'       => 0,
                'onamt'        => 0.00,
                'folionodocid' => '',
                'taxcondamt'   => 0,
                'taxstru'      => '',
                'u_entdt'      => $this->currenttime,
                'settledate'   => null,
                'u_name'       => Auth::user()->u_name,
                'u_ae'         => 'a',
                'modeset'      => null,
            ];
        };

        if (!empty($sale1->mergedwith)) {
            $mergedwith = explode(',', $sale1->mergedwith);

            foreach ($mergedwith as $docid) {
                $sale1row = Sale1::where('propertyid', $this->propertyid)
                    ->where('docid', $docid)->first();

                if (!$sale1row) continue;

                if (!isset($snoCache[$docid])) {
                    $snoCache[$docid] = Paycharge::where('propertyid', $this->propertyid)
                        ->where('docid', $docid)->max('sno') ?? 0;
                }

                for ($i = 1; $i <= $rowcount; $i++) {
                    $snoCache[$docid]++;
                    $amtHalf = $sale1row->netamt;
                    $insertRows[] = $buildInsertData($sale1row, $amtHalf, $i, $snoCache[$docid]);
                }
            }
        } else {
            $saledocid = $sale1->docid;

            if (!isset($snoCache[$saledocid])) {
                $snoCache[$saledocid] = Paycharge::where('propertyid', $this->propertyid)
                    ->where('docid', $saledocid)->max('sno') ?? 0;
            }

            for ($i = 1; $i <= $rowcount; $i++) {
                $snoCache[$saledocid]++;
                $amtFull = $request->input('amtrow' . $i);
                $insertRows[] = $buildInsertData($sale1, $amtFull, $i, $snoCache[$saledocid]);
            }
        }

        if (!empty($insertRows)) {
            DB::table('paycharge')->insert($insertRows);
        }

        $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();

        if (!is_null($wpenv)) {
            if (
                $wpenv->checkyn == 'Y' &&
                $wpenv->billmsgadmin != '' &&
                $wpenv->billmsgadminarray != '' &&
                $wpenv->billmsgadmintemplate != '' &&
                $wpenv->managementmob != ''
            ) {
                $billmsgadminarray = json_decode($wpenv->billmsgadminarray, true);

                $msgdata = [];
                foreach ($billmsgadminarray as $row) {
                    [$colname, $tbl] = $row;

                    if (endsWith($colname, 'sum')) {
                        $value = DB::table($tbl)
                            ->where('propertyid', $this->propertyid)
                            ->where('docid', $sale1docid)
                            ->sum(removeSuffixIfExists($colname, 'sum'));
                    } elseif ($res = splitByJoin($colname)) {
                        $left = $res['left'];   // e.g., 'item'
                        $right = $res['right']; // e.g., 'itemmast'

                        $value = DB::table($tbl)
                            ->leftJoin($right, function ($join) use ($tbl, $right, $left) {
                                $join->on("$tbl.$left", '=', "$right.Code")
                                    ->whereColumn("$right.RestCode", "$tbl.restcode");
                            })
                            ->where("$tbl.propertyid", $this->propertyid)
                            ->where("$tbl.docid", $sale1docid)
                            ->pluck("$right.Name")
                            ->implode(', ');
                    } else if ($tbl == 'paycharge') {
                        $value = DB::table($tbl)->where('propertyid', $this->propertyid)->where('restcode', $sale1->restcode)->where('docid', $sale1docid)->pluck($colname)
                            ->implode(', ');
                    } else {
                        $value = DB::table($tbl)->where('propertyid', $this->propertyid)->where('docid', $sale1docid)->value($colname);
                    }

                    $msgdata[] = $value;
                }

                $whatsapp = new WhatsappSend();
                $whatsapp->MuzzTech($msgdata, $wpenv->managementmob, 'Bill Message Admin', 'billmsgadmintemplate');
            }
        }

        return redirect('autorefreshmain');
    }

    public function departxhr(Request $request)
    {
        $dcode = $request->input('dcode');
        $name = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->value('name');
        return json_encode($name);
    }

    public function salebillprintthermal(Request $request)
    {
        $docid = $request->docid;
        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
        $mergedwith = !empty($sale1->mergedwith) ? explode(',', $sale1->mergedwith) : [$sale1->docid];
        $psno = 1;
        foreach ($mergedwith as $docid) {
            $mersale1 = Sale1::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

            $items = Stock::selectRaw('
        MAX(stock.sno) AS srno,
        MAX(stock.taxper) AS taxper,
        SUM(stock.taxamt) AS taxamt,
        stock.rate,
        stock.itemrate,
        stock.remarks,
        MAX(stock.itemrate) AS itemrate,
        SUM(stock.qtyiss) AS qty,
        SUM(stock.amount) AS amt,
        MAX(i.name) AS itemname,
        MAX(i.hsncode) AS hsncode,
        MAX(i.dispcode) As dispcode,
        CASE
            WHEN SUM(stock.taxamt) = 0 THEN SUM(stock.amount)
            ELSE 0
        END AS nontaxable,
        CASE
            WHEN SUM(stock.taxamt) <> 0 THEN SUM(stock.amount)
            ELSE 0
        END AS taxable,
        unitmast.name AS unitname
    ')
                ->leftJoin('itemmast as i', function ($join) {
                    $join->on('i.Code', '=', 'stock.item')
                        ->whereColumn('stock.itemrestcode', '=', 'i.restcode');
                })
                ->leftJoin('unitmast', 'unitmast.ucode', '=', 'stock.unit')
                ->where('stock.docid', $mersale1->docid)
                ->groupBy('stock.item', 'stock.rate', 'stock.remarks')
                ->orderByRaw('MAX(i.name)')
                ->get();

            $taxes = Sale2::select(
                'revmast.name as taxname',
                'revmast.rev_code',
                'sale2.taxper',
                DB::raw('SUM(taxamt) as taxamt'),
                DB::raw('SUM(basevalue) as taxableamt')
            )
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'sale2.taxcode')
                ->where('sale2.docid', $mersale1->docid)
                ->groupBy('revmast.rev_code', 'sale2.taxper', 'revmast.name')
                ->orderBy('sale2.taxper')
                ->get();

            $suntran = Suntran::select('suntran.*', 'sundrytype.nature', 'depart.dis_print', 'depart.outlet_title', 'depart.company_title')
                ->leftJoin('depart', 'depart.dcode', '=', 'suntran.restcode')
                ->leftJoin('sundrytype', function ($join) {
                    $join->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                        ->on('sundrytype.sno', '=', 'suntran.sno');
                })
                ->where('suntran.propertyid', $this->propertyid)
                ->where('suntran.docid', $mersale1->docid)
                ->get()
                ->toArray();

            $waitername = ServerMast::where('propertyid', $this->propertyid)->where('scode', $mersale1->waiter)->first();

            $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $mersale1->restcode)->first();

            $itemdt = [];

            foreach ($items as $item) {
                $itemdt[] = [
                    'itemname' => $item->itemname,
                    'qty'      => $item->qty,
                    'rate'     => $item->rate,
                    'amt'      => $item->amt
                ];
            }

            $yearmanage = DateHelper::calculateDateRanges($this->ncurdate);

            $prefix = $mersale1->vtype;
            $divcode = $depart->divcode;

            if ($divcode != '') {
                $prefix = $divcode;
            }
            if (strtolower($depart->nature) == 'outlet') {
                $str = $prefix . '/' . $yearmanage['hf']['start'] . '-' . $yearmanage['hf']['end'] . '/' . $mersale1->vno;
                $billdisplaytext = 'Table';
            } else if (strtolower($depart->nature) == 'room service') {
                $str = $prefix . '/' . $yearmanage['hf']['start'] . '-' . $yearmanage['hf']['end'] . '/' . $mersale1->vno;
                $billdisplaytext = 'Room';
            }

            $data = [
                'main' => [
                    'billno' => $str,
                    'billdisplaytext' => $billdisplaytext,
                    'vdate' => date('d-m-Y', strtotime($mersale1->vdate)),
                    'curtime' => $mersale1->vtime,
                    'roomno' => $mersale1->roomno,
                    'kotno' => $mersale1->kotno,
                    'netamountinwords' => amountToWords($mersale1->netamt),
                    'waiter' => $waitername,
                    'cashier' => Auth::user()->name
                ],
                'company' => [
                    'name' => companydata()->comp_name ?? '',
                    'mobile' => companydata()->mobile ?? '',
                    'email' => companydata()->email ?? '',
                    'website' => companydata()->website ?? '',
                    'gstin' => companydata()->gstin ?? '',
                    'saccode' => '996332',
                    'address1' => companydata()->address1 ?? '',
                    'address2' => companydata()->address2 ?? ''
                ],
                'items' => $itemdt,
                'suntrans' => $suntran,
                'taxes' => $taxes
            ];

            Log::info(json_encode($data));

            $printsetup = PrintingSetup::where('propertyid', $this->propertyid)->where('module', 'POS')->where('restcode', $mersale1->restcode)->get();

            foreach ($printsetup as $print) {
                if ($print->restcode == $mersale1->restcode) {
                    $bill = new Billprintthermal();
                    $bill->propertyid = $this->propertyid;
                    $bill->docid = $mersale1->docid;
                    $bill->billdata = json_encode($data);
                    $bill->printerpath = $print->printerpath;
                    $bill->psno = $psno;
                    $bill->u_name = Auth::user()->name;
                    $bill->save();
                }
                $psno++;
            }
        }

        return true;
    }
}
