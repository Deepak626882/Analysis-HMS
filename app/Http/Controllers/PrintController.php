<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\ResHelper;
use App\Helpers\UpdateRepeat;
use App\Models\ACGroup;
use App\Models\Bookings;
use App\Models\BookinPlanDetail;
use App\Models\ChannelEnviro;
use App\Models\ChannelPushes;
use App\Models\Cities;
use App\Models\PlanMast;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CompanyLog;
use App\Models\Companyreg;
use App\Models\Countries;
use App\Models\UserModule;
use App\Models\MenuHelp;
use App\Models\Paycharge;
use App\Models\UserPermission;
use App\Models\Items;
use App\Models\ItemMast;
use App\Models\ItemRate;
use App\Models\ItemCatMast;
use App\Models\ItemGrp;
use App\Models\Guestfolio;
use App\Models\Kot;
use App\Models\Revmast;
use App\Models\RoomMast;
use App\Models\GuestProf;
use App\Models\Sale1;
use App\Models\SubGroup;
use App\Models\Depart;
use App\Models\EnviroFom;
use App\Models\EnviroGeneral;
use App\Models\EnviroPos;
use App\Models\GrpBookinDetail;
use App\Models\GuestFolioProfDetail;
use App\Models\NightAuditLog;
use App\Models\PlanDetail;
use App\Models\PrintingSetup;
use App\Models\RoomBlockout;
use App\Models\RoomCat;
use App\Models\Sagar;
use App\Models\Stock;
use App\Models\RoomOcc;
use App\Models\States;
use App\Models\SundryMast;
use App\Models\SundryTypeFix;
use App\Models\Suntran;
use App\Models\TaxStructure;
use App\Models\User;
use App\Models\VoucherPrefix;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Facades\Hash;
use Psr\Http\Client\NetworkExceptionInterface;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumper;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class PrintController extends Controller
{
    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $compcode;
    protected $ncurdate;
    protected $datemanage;

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
            $this->compcode = Companyreg::where('propertyid', Auth::user()->propertyid)->value('comp_code');
            $this->ncurdate = DB::table('enviro_general')->where('propertyid', Auth::user()->propertyid)->value('ncur');
            $this->propertyid = $propertydata->propertyid;
            $this->ptlngth = strlen($this->propertyid);
            date_default_timezone_set('Asia/Kolkata');
            $this->currenttime = date('Y-m-d H:i:s');
            $this->datemanage = DateHelper::calculateDateRanges($this->ncurdate);
            return $next($request);
        });
    }

    public function revokeopen($code)
    {
        $value = Menuhelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('code', $code)->first();
        return $value;
    }

    public function ExportTable()
    {
        echo '<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">';
        echo '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">';
        echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
        echo '<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>';
    }
    # Warning: Abandon hope, all who enter here. 😱
    public function DownloadTable($tableName, $title, $columnsToExport, $columnToSearch)
    {
        $exportColumnsJS = json_encode($columnsToExport);
        $searchColumnsJS = json_encode($columnToSearch);

        echo "<script>
        $(document).ready(function() {
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
                    // Apply column-specific search
                    let searchColumns = $searchColumnsJS;
                    this.api().columns(searchColumns).every(function() {
                        let column = this;
                        let title = column.header().textContent;
                        let input = document.createElement('input');
                        input.placeholder = 'Search ' + title;
                        $(input).appendTo($(column.footer()).empty());
                        $(input).on('keyup', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                }
            });
        });
        </script>";
    }

    public function printwalkin(Request $request, $docid)
    {
        $permission = revokeopen(141113);
        if (is_null($permission) || $permission->print == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $data = GuestFolio::select([
            'roomocc.name as guest_name',
            'guestfolio.add1',
            'guestfolio.add2',
            'cities.cityname',
            'guestprof.nationality',
            'guestprof.mobile_no',
            'guestprof.email_id',
            'guestprof.dob',
            'guestprof.anniversary',
            'guestfolio.arrfrom',
            'guestfolio.destination',
            'guestfolio.folio_no',
            'roomocc.roomno',
            'room_cat.name as room_type',
            'roomocc.adult',
            'roomocc.children',
            'roomocc.roomrate',
            'roomocc.planamt',
            'roomocc.rrtaxinc',
            'plan_mast.name as plan_name',
            'roomocc.chkindate',
            'roomocc.chkintime',
            'roomocc.depdate',
            'roomocc.deptime',
            'guestfolio.travelmode',
            'guestprof.id_proof as id_proof',
            'guestprof.idproof_no as idproof_no ',
            'guestprof.paymentMethod',
            'subgroup.name as company',
            'ST.Name as travel_agent',
            'busssource.name as business_source',
            'booking.BookedBy',
            'booking.RefBookNo',
            'guestprof.pic_path',
            'guestprof.guestsign',
            'roomocc.roomno AS group_rooms',
            'guestprof.u_name',
            'guestfolio.propertyid'
        ])
            ->leftJoin('roomocc', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('guestprof', 'guestfolio.guestprof', '=', 'guestprof.guestcode')
            ->leftJoin('cities', 'guestfolio.city', '=', 'cities.city_code')
            ->leftJoin('room_cat', 'roomocc.roomcat', '=', 'room_cat.cat_code')
            ->leftJoin('plan_mast', 'roomocc.plancode', '=', 'plan_mast.pcode')
            ->leftJoin('busssource', 'guestfolio.busssource', '=', 'busssource.bcode')
            ->leftJoin('subgroup', 'guestfolio.company', '=', 'subgroup.sub_code')
            ->leftJoin('subgroup as ST', 'guestfolio.travelagent', '=', 'ST.sub_code')
            ->leftJoin('booking', 'booking.docid', '=', 'guestfolio.bookingdocid')
            ->where('guestfolio.docid', $docid)
            ->first();

        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');

        return view('property.grcprintpage', [
            'data' => $data,
            'comp' => $company,
            'statename' => $statename
        ]);
    }

    public function printpurchbill(Request $request, $docid)
    {
        // return $docid;
        $purchaseData = DB::table('purch2 as P2')
            ->select([
                'P2.docid',
                'P2.vno',
                'P2.vdate',
                'P2.vtype as V_Type',
                // DB::raw('CASE WHEN P2.recdqty = 0 THEN P2.issqty ELSE P2.recdqty END as Qty'),
                'P2.qtyrec as Qty',
                // DB::raw("CASE WHEN IFNULL(P2.recdunit, '') = '' THEN P2.issuunit ELSE P2.recdunit END as Unit"),
                'unitmast.name as Unit',
                'P2.itemrate',
                'P2.amount',
                'SG1.name as PartyName',
                'P1.partybillno',
                'P1.partybilldt',
                'P1.total',
                'P1.discper',
                'P1.discamt',
                'P1.addamt',
                'P1.dedamt',
                'P1.roundoff',
                'P1.netamt',
                'P1.partybilldt',
                'I.name as ItemName',
                'G.name as GodName',
                'P2.contradocid',
                'V.description',
                'SG.name as SaleAcName',
                'P1.gstno',
                DB::raw("IFNULL(SG1.conprefix, '') as ConPrefix"),
                DB::raw("IFNULL(SG1.conperSon, '') as ConPerSon"),
                DB::raw("CONCAT(IFNULL(LTRIM(RTRIM(SG1.address)), ''), ', ', IFNULL(cities.cityname, '')) as PartyAddress"),
                'P2.rate',
                DB::raw("IFNULL(P1.invoicetype, '') as InvoiceType"),
                DB::raw("IFNULL(P1.invoiceno, 0) as InvoiceNo"),
                'TS.Name as TaxStruct',
                'P1.payable',
                DB::raw("IFNULL(P1.remark, '') as Remark"),
                'I.LPurRate',
                'P2.mrno'
            ])
            ->leftJoin('purch1 as P1', 'P2.docid', '=', 'P1.docid')
            ->leftJoin('godown_mast as G', 'P2.godcode', '=', 'G.scode')
            ->leftJoin('itemmast as I', 'P2.item', '=', 'I.Code')
            ->leftJoin(DB::raw("(SELECT str_code, MAX(Name) as Name FROM taxstru GROUP BY str_code) as TS"), 'TS.str_code', '=', 'P2.taxstru')
            ->leftJoin('voucher_type as V', function ($join) {
                $join->on('P2.vtype', '=', 'V.v_type')
                    ->where('P2.propertyid', '=', $this->propertyid);
            })

            ->leftJoin('subgroup as SG', 'SG.sub_code', '=', 'P2.accode')
            ->leftJoin('unitmast', 'unitmast.ucode', '=', 'P2.unit')
            ->leftJoin('subgroup as SG1', 'SG1.sub_code', '=', 'P1.Party')
            ->leftJoin('cities', 'cities.city_code', '=', 'SG1.citycode')
            ->where('V.propertyid', $this->propertyid)
            ->where('P2.docid', $docid)
            // ->where('I.ItemType', 'Store')
            ->groupBy('P2.item')
            ->get();

        // var_dump($purchaseData);
        // exit;

        $suntranData = DB::table('suntran')
            ->where('docid', $docid)
            ->where('propertyid', $this->propertyid)
            ->whereNot('amount', '0.00')
            ->orderBy('sno')
            ->get();

        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');

        return view('property.printpurchbill', [
            'purchaseData' => $purchaseData,
            'suntranData' => $suntranData,
            'comp' => $company,
            'statename' => $statename
        ]);
    }

    public function mrprinting(Request $request, $docid)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->print == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ginData = DB::table('gin as G')
            ->select([
                'G.docid',
                'G.vno as MRNo',
                'G.vdate as Date',
                'G.vtype',
                'P.name as PartyName',
                'G.remark',
                'G.porddocid',
                'G.porddate',
                'G.chalno as ChalanNo',
                'G.chaldate as ChakanDate',
                'G.meminvno as MemoInvNo',
                'G.meminvdate as InvoiceDate',
                'G.inspectedby as InspBy',
                'G.approvedby as ApprBy',
                'S.sno as Sno',
                'S.qtyiss',
                'S.qtyrec',
                'S.recdunit as Unit',
                'S.chalqty as ChalanQty',
                'S.rate as Rate',
                'S.amount as Amount',
                'S.recdqty as RecdQty',
                'S.rejqty as RejQty',
                'S.qtyrec as sStkQtyRec',
                'S.qtyiss as sStkQtyIss',
                'S.remarks as Remark',
                'I.name as ItemName',
                'V.description as Type',
            ])
            ->join('stock as S', 'S.docid', '=', 'G.docid')
            ->join('itemmast as I', 'S.item', '=', 'I.Code')
            ->join('subgroup as P', 'S.partycode', '=', 'P.sub_code')
            ->join('voucher_type as V', function ($join) {
                $join->on('G.vtype', '=', 'V.v_type')
                    ->where('V.propertyid', '=', $this->propertyid);
            })
            ->where('G.docid', $docid)
            ->where('G.propertyid', $this->propertyid)
            ->get();

        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)
            ->where('state_code', $company->state_code)
            ->value('name');

        return view('property.mrprinting', [
            'ginData' => $ginData,
            'comp' => $company,
            'statename' => $statename,
        ]);
    }

    public function stockregister(Request $request)
    {
        $permission = revokeopen(161211);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');

        $godown = Depart::where('propertyid', $this->propertyid)->where('dcode', "PURC$this->propertyid")->get();

        $itemgrp = ItemGrp::where('property_id', $this->propertyid)->where('restcode', "PURC$this->propertyid")->where('activeyn', 'Y')->orderBy('name')->get();

        // return $itemgrp;

        return view('property.stockregister', [
            'ncurdate' => $this->ncurdate,
            'company' => $company,
            'statename' => $statename,
            'godown' => $godown,
            'itemgrp' => $itemgrp
        ]);
    }

    public function getItemsAndGroups(Request $request)
    {
        $itemType = $request->input('item_type');

        $types = ($itemType === 'All' || empty($itemType))
            ? ['Raw Material', 'Finish', 'Semi-Finish', 'Consumable', 'Store Item']
            : [$itemType];

        $groupIds = DB::table('itemgrp')
            ->where('property_id', $this->propertyid)
            ->where('restcode', 'PURC' . $this->propertyid)
            ->whereIn('type', $types)
            ->pluck('code')
            ->toArray();

        $groups = DB::table('itemgrp')
            ->whereIn('code', $groupIds)
            ->select('code as id', 'name')
            ->orderBy('name')
            ->get();

        $items = DB::table('itemmast')
            ->where('Property_ID', $this->propertyid)
            ->where('RestCode', 'PURC' . $this->propertyid)
            ->whereIn('ItemGroup', $groupIds)
            ->select('Code as id', 'Name as iname', 'ItemGroup as group_id')
            ->get();

        return response()->json([
            'groups' => $groups,
            'items' => $items
        ]);
    }

    public function getActualData(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $allitems = $request->input('items');
        // 1. Fetch all distinct items with unit names
        $ditems = DB::table('stock as S')
            ->distinct()
            ->select([
                'S.item',
                'I.Name as ItemName',
                'u.name as unitname',
                'ui.name as issueunitname'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->leftJoin('unitmast as u', function ($join) {
                $join->on('u.ucode', '=', 'I.Unit')
                    ->where('u.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('unitmast as ui', function ($join) {
                $join->on('ui.ucode', '=', 'I.IssueUnit')
                    ->where('ui.propertyid', '=', $this->propertyid);
            })
            ->where('S.propertyid', $this->propertyid)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('I.Code', $allitems)
            ->whereIn('VT.ncat', [
                'PBC',
                'PBR',
                'PRR',
                'PRC',
                'STOP',
                'MRE',
                'RQI',
                'RQR',
                'BKREC',
                'BKISS',
                'KSREC',
                'KSISS',
                'KMREC',
                'KMISS'
            ])
            ->orderBy('I.Name')
            ->get();

        // 2. Initialize reportdata with items
        $reportdata = [];
        foreach ($ditems as $row) {
            $reportdata[$row->item] = [
                'item'        => $row->item,
                'itemname'    => $row->ItemName,
                'unitname'    => trim(($row->unitname ?? '') . ' / ' . ($row->issueunitname ?? '')),
                'opqty'       => 0,
                'opamt'       => 0,
                'opissuedqty' => 0,
                'opissuedamt' => 0,
                'transactions' => []
            ];
        }

        // 3. Opening Received
        $openingReceived = DB::table('stock as S')
            ->select([
                DB::raw('SUM(S.recdqty) as OpQty'),
                DB::raw('SUM(S.amount) as OpAmt'),
                'S.item'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->where('S.propertyid', $this->propertyid)
            ->where('S.vdate', '<', $fromdate)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('VT.ncat', ['PBC', 'PBR', 'STOP', 'MRE', 'BKREC', 'KSREC', 'KMREC', 'RQI'])
            ->where('S.recdqty', '>', 0)
            ->whereIn('S.item', $allitems)
            ->groupBy('S.item')
            ->havingRaw('SUM(S.recdqty) > 0')
            ->get();

        foreach ($openingReceived as $row) {
            if (!isset($reportdata[$row->item])) {
                $reportdata[$row->item] = [
                    'item' => $row->item,
                    'itemname' => '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }
            $reportdata[$row->item]['opqty'] = $row->OpQty;
            $reportdata[$row->item]['opamt'] = $row->OpAmt;
        }

        // 4. Opening Issued
        $openingIssued = DB::table('stock as S')
            ->select([
                DB::raw('SUM(S.issqty) as OpQty'),
                DB::raw('SUM(S.amount) as OpAmt'),
                'S.item',
                'I.Name'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->where('S.propertyid', $this->propertyid)
            ->where('S.vdate', '<', $fromdate)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('VT.ncat', ['PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS'])
            ->where('S.issqty', '>', 0)
            ->whereIn('S.item', $allitems)
            ->groupBy('S.item', 'I.Name')
            ->havingRaw('SUM(S.issqty) > 0')
            ->get();

        foreach ($openingIssued as $row) {
            if (!isset($reportdata[$row->item])) {
                $reportdata[$row->item] = [
                    'item' => $row->item,
                    'itemname' => $row->Name ?? '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }
            $reportdata[$row->item]['opissuedqty'] = $row->OpQty;
            $reportdata[$row->item]['opissuedamt'] = $row->OpAmt;
        }

        // 5. Transactions
        $transactions = DB::table('stock as S')
            ->select([
                'S.vdate',
                'S.vtype',
                'S.vno',
                'S.amount',
                'S.item',
                'I.Name',
                DB::raw("
                CASE 
                    WHEN VT.ncat IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC') 
                    THEN S.recdqty ELSE 0 
                END as QtyRec
            "),
                DB::raw("
                CASE 
                    WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') 
                    THEN S.issqty ELSE 0 
                END as QtyIss
            "),
                DB::raw("
                CASE 
                    WHEN VT.ncat IN ('PBC', 'PBR', 'PRR', 'PRC', 'MRE') 
                    THEN SG.name 
                    ELSE D.name 
                END as Particulars
            "),
                DB::raw("
                CASE 
                    WHEN VT.ncat IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC') 
                    THEN 'A' 
                    WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') 
                    THEN 'B' 
                    ELSE 'C' 
                END as SeqNo
            ")
            ])
            ->leftJoin('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->leftJoin('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->leftJoin('subgroup as SG', 'S.partycode', '=', 'SG.sub_code')
            ->leftJoin('stock as S1', function ($join) {
                $join->on('S.contradocid', '=', 'S1.docid')
                    ->on('S.contrasno', '=', 'S1.sno');
            })
            ->leftJoin('godown_mast as D', 'S1.godowncode', '=', 'D.scode')
            ->where('S.propertyid', $this->propertyid)
            ->whereBetween('S.vdate', [$fromdate, $todate])
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->where('I.ItemType', 'Store')
            ->whereIn('I.Code', $allitems)
            ->orderBy('S.item')
            ->orderBy('S.vdate')
            ->orderBy('SeqNo')
            ->orderBy('S.vtype')
            ->orderBy('S.vno')
            ->get();

        foreach ($transactions as $txn) {
            $itemcode = $txn->item;
            if (!isset($reportdata[$itemcode])) {
                $reportdata[$itemcode] = [
                    'item' => $itemcode,
                    'itemname' => $txn->Name ?? '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }

            $reportdata[$itemcode]['transactions'][] = [
                'vdate'      => $txn->vdate,
                'vtype'      => $txn->vtype,
                'vno'        => $txn->vno,
                'amount'     => (float) $txn->amount,
                'qtyrec'     => (float) $txn->QtyRec,
                'qtyiss'     => (float) $txn->QtyIss,
                'particular' => $txn->Particulars,
                'seqno'      => $txn->SeqNo
            ];
        }

        return response()->json([
            'reportdata' => array_values($reportdata)
        ]);
    }

    public function getLprData(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        // 1. Fetch all distinct items with unit names
        $ditems = DB::table('stock as S')
            ->distinct()
            ->select([
                'S.item',
                'I.Name as ItemName',
                'u.name as unitname',
                'ui.name as issueunitname'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->leftJoin('unitmast as u', function ($join) {
                $join->on('u.ucode', '=', 'I.Unit')
                    ->where('u.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('unitmast as ui', function ($join) {
                $join->on('ui.ucode', '=', 'I.IssueUnit')
                    ->where('ui.propertyid', '=', $this->propertyid);
            })
            ->where('S.propertyid', $this->propertyid)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('VT.ncat', [
                'PBC',
                'PBR',
                'PRR',
                'PRC',
                'STOP',
                'MRE',
                'RQI',
                'RQR',
                'BKREC',
                'BKISS',
                'KSREC',
                'KSISS',
                'KMREC',
                'KMISS'
            ])
            ->orderBy('I.Name')
            ->get();

        // 2. Initialize reportdata with items
        $reportdata = [];
        foreach ($ditems as $row) {
            $reportdata[$row->item] = [
                'item'        => $row->item,
                'itemname'    => $row->ItemName,
                'unitname'    => trim(($row->unitname ?? '') . ' / ' . ($row->issueunitname ?? '')),
                'opqty'       => 0,
                'opamt'       => 0,
                'opissuedqty' => 0,
                'opissuedamt' => 0,
                'transactions' => []
            ];
        }

        // 3. Opening Received (using LPurRate)
        $openingReceived = DB::table('stock as S')
            ->select([
                DB::raw('SUM(S.recdqty) as OpQty'),
                DB::raw('SUM(S.recdqty * I.LPurRate) as OpAmt'),
                'S.item'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->where('S.propertyid', $this->propertyid)
            ->where('S.vdate', '<', $fromdate)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('VT.ncat', ['PBC', 'PBR', 'STOP', 'MRE', 'BKREC', 'KSREC', 'KMREC', 'RQI'])
            ->where('S.recdqty', '>', 0)
            ->groupBy('S.item')
            ->havingRaw('SUM(S.recdqty) > 0')
            ->get();

        foreach ($openingReceived as $row) {
            if (!isset($reportdata[$row->item])) {
                $reportdata[$row->item] = [
                    'item' => $row->item,
                    'itemname' => '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }
            $reportdata[$row->item]['opqty'] = $row->OpQty;
            $reportdata[$row->item]['opamt'] = $row->OpAmt;
        }

        // 4. Opening Issued (using LPurRate)
        $openingIssued = DB::table('stock as S')
            ->select([
                DB::raw('SUM(S.issqty) as OpQty'),
                DB::raw('SUM(S.issqty * I.LPurRate) as OpAmt'),
                'S.item',
                'I.Name'
            ])
            ->join('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->join('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->where('S.propertyid', $this->propertyid)
            ->where('S.vdate', '<', $fromdate)
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->whereIn('VT.ncat', ['PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS'])
            ->where('S.issqty', '>', 0)
            ->groupBy('S.item', 'I.Name')
            ->havingRaw('SUM(S.issqty) > 0')
            ->get();

        foreach ($openingIssued as $row) {
            if (!isset($reportdata[$row->item])) {
                $reportdata[$row->item] = [
                    'item' => $row->item,
                    'itemname' => $row->Name ?? '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }
            $reportdata[$row->item]['opissuedqty'] = $row->OpQty;
            $reportdata[$row->item]['opissuedamt'] = $row->OpAmt;
        }

        // 5. Transactions (using LPurRate)
        $transactions = DB::table('stock as S')
            ->select([
                'S.vdate',
                'S.vtype',
                'S.vno',
                DB::raw("
                    CASE 
                        WHEN VT.ncat IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC') 
                        THEN S.recdqty * I.LPurRate
                        WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') 
                        THEN S.issqty * I.LPurRate
                        ELSE 0 
                    END as amount
                "),
                'S.item',
                'I.Name',
                DB::raw("
                    CASE 
                        WHEN VT.ncat IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC') 
                        THEN S.recdqty ELSE 0 
                    END as QtyRec
                "),
                DB::raw("
                    CASE 
                        WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') 
                        THEN S.issqty ELSE 0 
                    END as QtyIss
                "),
                DB::raw("
                    CASE 
                        WHEN VT.ncat IN ('PBC', 'PBR', 'PRR', 'PRC', 'MRE') 
                        THEN SG.name 
                        ELSE D.name 
                    END as Particulars
                "),
                DB::raw("
                    CASE 
                        WHEN VT.ncat IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC') 
                        THEN 'A' 
                        WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') 
                        THEN 'B' 
                        ELSE 'C' 
                    END as SeqNo
                ")
            ])
            ->leftJoin('itemmast as I', function ($join) {
                $join->on('S.item', '=', 'I.Code')
                    ->where('I.ItemType', '=', 'Store');
            })
            ->leftJoin('voucher_type as VT', function ($join) {
                $join->on('S.vtype', '=', 'VT.v_type')
                    ->on('S.propertyid', '=', 'VT.propertyid');
            })
            ->leftJoin('subgroup as SG', 'S.partycode', '=', 'SG.sub_code')
            ->leftJoin('stock as S1', function ($join) {
                $join->on('S.contradocid', '=', 'S1.docid')
                    ->on('S.contrasno', '=', 'S1.sno');
            })
            ->leftJoin('godown_mast as D', 'S1.godowncode', '=', 'D.scode')
            ->where('S.propertyid', $this->propertyid)
            ->whereBetween('S.vdate', [$fromdate, $todate])
            ->whereIn('S.godowncode', ['PURC' . $this->propertyid])
            ->where('I.ItemType', 'Store')
            ->orderBy('S.item')
            ->orderBy('S.vdate')
            ->orderBy('SeqNo')
            ->orderBy('S.vtype')
            ->orderBy('S.vno')
            ->get();

        foreach ($transactions as $txn) {
            $itemcode = $txn->item;
            if (!isset($reportdata[$itemcode])) {
                $reportdata[$itemcode] = [
                    'item' => $itemcode,
                    'itemname' => $txn->Name ?? '',
                    'unitname' => '',
                    'opqty' => 0,
                    'opamt' => 0,
                    'opissuedqty' => 0,
                    'opissuedamt' => 0,
                    'transactions' => []
                ];
            }

            $reportdata[$itemcode]['transactions'][] = [
                'vdate'      => $txn->vdate,
                'vtype'      => $txn->vtype,
                'vno'        => $txn->vno,
                'amount'     => (float) $txn->amount,
                'qtyrec'     => (float) $txn->QtyRec,
                'qtyiss'     => (float) $txn->QtyIss,
                'particular' => $txn->Particulars,
                'seqno'      => $txn->SeqNo
            ];
        }

        return response()->json([
            'reportdata' => array_values($reportdata)
        ]);
    }

    public function fetchValuationData(Request $request)
    {
        $valuation = $request->input('valuation');

        if ($valuation == 'Actual') {
            return $this->getActualData($request);
        } elseif ($valuation == 'LastPurchaseRate') {
            return $this->getLprData($request);
        } else {
            return response()->json(['error' => 'Invalid valuation valuation'], 400);
        }
    }

    public function openbanquetmast()
    {
        $permission = revokeopen(121811);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('server_mast', 'Banquet Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('functiontype')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.waiter2', ['data' => $data]);
    }
    public function getnctypenames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('nctype_mast')
            ->where('nctype', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        if ($data->count() > 0) {
            $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
            foreach ($data as $list) {
                $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->nctype . '</a></li>';
            }
            $output .= '</ul>';
            return $output;
        } else {
            return '';
        }
    }

    public function submitbanquetmaster(Request $request)
    {
        $permission = revokeopen(121811);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'functiontype';
        $data = $request->except('_token');
        $code = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('code');

        if ($code == null) {
            $code = 1;
        } else {
            $code = intval(substr($code, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Banquet Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'code' => $code . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Banquet Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Banquet Master!' . $e->getMessage());
        }
    }

    public function deletebanquetmast(Request $request, $sn, $code)
    {
        $permission = revokeopen(121811);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('functiontype')
                ->where('propertyid', $this->propertyid)
                ->where('code', $code)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Banquet Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Banquet Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateBanquetmaststore(Request $request)
    {
        $permission = revokeopen(121811);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'functiontype';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('code', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Banquet Master Name Already Exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                //'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('code', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Banquet Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function openvenuefeatures()
    {
        $permission = revokeopen(121812);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('venuefeatures', 'Venue Features Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('venuefeatures')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.venuefeatures', ['data' => $data]);
    }
    public function submitvenuefeatures(Request $request)
    {
        $permission = revokeopen(121812);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'venuefeatures';
        $data = $request->except('_token');
        $code = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('code');

        if ($code == null) {
            $code = 1;
        } else {
            $code = intval(substr($code, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Venue Feature Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'code' => $code . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Venue Features Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Venue Features!' . $e->getMessage());
        }
    }

    public function deletevenuefeatures(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121812);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('venuefeatures')
                ->where('propertyid', $this->propertyid)
                ->where('code', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Venue Features Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Venue Features!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatevenuefeaturesstore(Request $request)
    {
        $permission = revokeopen(121812);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'venuefeatures';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('code', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Venue Features Name Already Exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                //'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('code', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Venue Features Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function openitemgroups(Request $request)
    {
        $permission = revokeopen(121815);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('itemgrp', 'Menu Group Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $menugroupdata = DB::table('itemgrp')
            ->select('itemgrp.*', 'depart.name as departname', 'depart.dcode')
            ->join('depart', 'depart.dcode', '=', 'itemgrp.restcode')
            ->where('itemgrp.property_id', $this->propertyid)
            ->where('itemgrp.restcode', 'BANQ' . $this->propertyid)
            ->orderBy('itemgrp.name', 'ASC')
            ->get();

        $departdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        return view('property.igroups', ['data' => $menugroupdata, 'departdata' => $departdata]);
    }

    function submititemgroups(Request $request)
    {
        $permission = revokeopen(121815);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'name' => 'required',
            'type' => 'required',
        ];
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', 'BANQ' . $this->propertyid)
            ->where('name', $request->input('name'))
            ->where('property_id', $this->propertyid)
            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Group already exists!');
        }

        $groupcode = DB::table($tableName)->where('property_id', $this->propertyid)->max('code');
        $groupcode = substr($groupcode, 0, -$this->ptlngth);
        if (empty($groupcode)) {
            $groupcode = 1 . $this->propertyid;
        } else {
            $groupcode = $groupcode + 1 . $this->propertyid;
        }

        try {
            $insertdata = [
                'code' => $groupcode,
                'name' => $request->input('name'),
                'property_id' => $this->propertyid,
                'restcode' => 'BANQ' . $this->propertyid,
                'type' => $request->type,
                'cattype' => $request->categorytype,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'activeyn' => $request->input('activeyn'),
            ];

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Item Group Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item Group!' . $e->getMessage());
        }
    }

    public function updateitemgroups(Request $request)
    {
        $permission = revokeopen(121815);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', 'BANQ' . $this->propertyid)
            ->where('name', $request->input('upname'))
            ->where('property_id', $this->propertyid)
            ->where('code', '!=', $request->input('upcode'))
            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Group already exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('upname'),
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'activeyn' => $request->input('upactiveyn'),
            ];

            DB::table($tableName)
                ->where('property_id', $this->propertyid)
                ->where('code', $request->input('upcode'))
                ->update($updatedata);

            return back()->with('success', 'Item Group Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item Group!');
        }
    }

    public function deletemenugroup(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121815);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $chkitemmast = ItemMast::where('Property_ID', $this->propertyid)->where('ItemGroup', base64_decode($request->input('ucode')))->first();
            if (!is_null($chkitemmast)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Group used in Menu Item'
                ]);
            }
            $jaldiwahasehato📢 = DB::table('itemgrp')
                ->where('property_id', $this->propertyid)
                ->where('code', $ucode)
                ->where('sn', $sn)
                ->delete();

            if ($jaldiwahasehato📢) {
                return response()->json(['message' => 'Menu Group Deleted Successfully']);
            } else {
                return response()->json(['message' => 'Unable to Delete Menu Group!'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Delete Menu Group!'], 500);
        }
    }

    public function openvenuemaster()
    {
        $permission = revokeopen(121813);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('venuemast', 'Venue Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('venuemast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.venuemaster', ['data' => $data]);
    }
    public function submitvenuemaster(Request $request)
    {
        $permission = revokeopen(121813);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'venuemast';
        $code = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('code');

        if ($code == null) {
            $code = 1;
        } else {
            $code = intval(substr($code, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $request->name)
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Venue Master Name already exists!');
        }

        if (!empty($request->file('picpath'))) {
            $itempic = $request->file('picpath');
            $itempicture = 'Venue Picture' . $this->propertyid . '.' . $itempic->getClientOriginalExtension();
            $folderPathp = 'public/property/venuepicture';
            Storage::makeDirectory($folderPathp);
            Storage::putFileAs($folderPathp, $itempic, $itempicture);
        } else {
            $itempicture = null;
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'code' => $code . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'name' => $request->name,
                'shortname' => $request->input('shortname') ?? '',
                'dimension' => $request->input('dimension') ?? '',
                'activeYN' => $request->activeYN,
                'picpath' => $itempicture ?? '',
            ];

            DB::table($tableName)->insert($insertdata);
            return back()->with('success',  'Venue Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Venue master!' . $e->getMessage(), 500);
        }
    }

    public function deletevenuemaster(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121813);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('venuemast')
                ->where('propertyid', $this->propertyid)
                ->where('code', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Venue Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Venue master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatevenuemasterstore(Request $request)
    {
        $permission = revokeopen(121813);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'venuemast';

        // return $request->input('updatecode');
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('code', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        $existingshortname = DB::table($tableName)
            ->where('shortname', $request->input('updateshortname'))
            ->whereNot('code', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Venue Master Name Already Exists!');
        }

        if ($existingshortname) {
            return back()->with('error', 'Venue Master Short Name Already Exists!');
        }

        if (!empty($request->file('uppicpath'))) {
            $itempic = $request->file('uppicpath');
            $itempicture = 'Venue Picture' . $this->propertyid . '.' . $itempic->getClientOriginalExtension();
            $folderPathp = 'public/property/venuepicture';
            Storage::makeDirectory($folderPathp);
            Storage::putFileAs($folderPathp, $itempic, $itempicture);
        } else {
            $itempicture = $request->input('olditemimage');
        }

        // return $request->input('updatecode');
        // return $request->input('updatename');

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                //'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
                'shortname' => $request->input('updateshortname') ?? '',
                'dimension' => $request->input('updatedimension') ?? '',
                'picpath' => $itempicture ?? '',
            ];
            DB::table($tableName)
                ->where('code', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Venue Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage(), 500);
        }
    }


    public function openbanqsundrysetting(Request $request)
    {
        $permission = revokeopen(121818);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vtypes = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->first();
        $data = DB::table('sundrytype')
            ->select('sundrytype.*', 'depart.name AS departname')
            ->leftJoin('depart', 'depart.dcode', '=', 'sundrytype.vtype')
            ->where('sundrytype.propertyid', '=', $this->propertyid)
            ->where('sundrytype.vtype', 'BANQ' . $this->propertyid)
            ->groupBy('sundrytype.vtype')
            ->get();

        return view('property.banqsundrysetting', [
            'vtypes' => $vtypes,
            'data' => $data
        ]);
    }

    public function banqsundrysettingsubmit(Request $request)
    {
        $permission = revokeopen(121818);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vtype' => 'required',
            'applicablefrom' => 'required',
            'sundryname1' => 'required',
            'dispname1' => 'required',
        ]);

        $check = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', 'BANQ' . $this->propertyid)->first();
        if ($check) {
            DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', 'BANQ' . $this->propertyid)->delete();
        }

        $prefixes = array('sundryname', 'dispname', 'calcformula', 'peroramt', 'vals', 'boldyn', 'revenuecharge', 'automan');
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $count = 0;

        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'sundryname') === 0) {
                $count++;
            }
        }
        $sno1 = 1;
        for ($i = 1; $i <= $count; $i++) {
            $data = [];
            $isEmptyRow = true;
            $sundryfix = SundryMast::where('propertyid', $this->propertyid)->where('sundry_code', $request->input('sundryname' . $i))->first();

            foreach ($prefixes as $prefix) {
                $value = $request->input($prefix . $i);
                $sundrydata = [
                    'propertyid' => $this->propertyid,
                    'sno' => $sno1,
                    'sundry_code' => $request->input('sundryname' . $i) ?? '',
                    'disp_name' => $request->input('dispname' . $i) ?? '',
                    'calcformula' => $request->input('calcformula' . $i) ?? '',
                    'peroramt' => $request->input('peroramt' . $i) ?? 'A',
                    'svalue' => $request->input('vals' . $i),
                    'bold' => $request->input('boldyn' . $i) == 'Yes' ? 'Y' : 'N',
                    'revcode' => $request->input('revenuecharge' . $i) ?? '',
                    'automanual' => $request->input('automan' . $i) ?? 'Manual',
                    'vtype' => 'BANQ' . $this->propertyid,
                    'appdate' => $request->input('applicablefrom'),
                    'nature' => $sundryfix->nature ?? '',
                    'calcsign' => $sundryfix->calcsign ?? '',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'postyn' => $request->input('postyn' . $i) == 'Yes' ? 'Y' : 'N',
                ];

                if (!empty($value)) {
                    $data[$prefix] = $value;
                    $isEmptyRow = false;
                }
            }


            if (!$isEmptyRow) {
                DB::table('sundrytype')->insert($sundrydata);
            }
            $sno1++;
        }
        return back()->with('message', 'Banquet Sundry Setting Submitted!');
    }

    public function updatebanquetsundrysetting(Request $request)
    {
        $permission = revokeopen(121818);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vtype = base64_decode($request->input('vtype'));
        $data = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $vtype)->get();
        $revmast = DB::table('revmast')->where('propertyid', $this->propertyid)->where('Desk_code', $vtype)->where('field_type', 'C')
            ->union(
                DB::table('revmast')
                    ->where('propertyid', $this->propertyid)
                    ->where('field_type', 'T')
            )->orderBy('sn')->get();
        $sundrynames = DB::table('sundrymast')->where('propertyid', $this->propertyid)->orderBy('name')->get();
        $sundrytype = DB::table('sundrytypefix')->where('propertyid', $this->propertyid)->orderBy('sn')->get();
        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $vtype)->first();
        return view('property.banquetsundrysettingupdate', [
            'data' => $data,
            'revmast' => $revmast,
            'sundrynames' => $sundrynames,
            'sundrytype' => $sundrytype,
            'depart' => $depart
        ]);
    }

    public function updatebanqsundry(Request $request)
    {
        $permission = revokeopen(121818);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vtype' => 'required',
            'appdate' => 'required',
            'sundryname1' => 'required',
            'dispname1' => 'required',
        ]);

        $check = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $request->input('vtype'))->first();
        if ($check) {
            DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $request->input('vtype'))->delete();
        }

        $prefixes = array('sundryname', 'dispname', 'calcformula', 'peroramt', 'vals', 'boldyn', 'revenuecharge', 'automan');
        $count = 0;

        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'sundryname') === 0) {
                $count++;
            }
        }
        $sno1 = 1;
        for ($i = 1; $i <= $count; $i++) {
            $data = [];
            $isEmptyRow = true;
            $sundryfix = SundryMast::where('propertyid', $this->propertyid)->where('sundry_code', $request->input('sundryname' . $i))->first();

            foreach ($prefixes as $prefix) {
                $value = $request->input($prefix . $i);
                $sundrydata = [
                    'propertyid' => $this->propertyid,
                    'sno' => $sno1,
                    'sundry_code' => $request->input('sundryname' . $i) ?? '',
                    'disp_name' => $request->input('dispname' . $i) ?? '',
                    'calcformula' => $request->input('calcformula' . $i) ?? '',
                    'peroramt' => $request->input('peroramt' . $i) ?? 'A',
                    'svalue' => $request->input('vals' . $i),
                    'bold' => $request->input('boldyn' . $i) == 'Yes' ? 'Y' : 'N',
                    'revcode' => $request->input('revenuecharge' . $i) ?? '',
                    'automanual' => $request->input('automan' . $i) ?? 'Manual',
                    'vtype' => $request->input('oldvtype'),
                    'appdate' => $request->input('appdate'),
                    'nature' => $sundryfix->nature ?? '',
                    'calcsign' => $sundryfix->calcsign ?? '',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'postyn' => $request->input('postyn' . $i) == 'Yes' ? 'Y' : 'N',
                ];

                if (!empty($value)) {
                    $data[$prefix] = $value;
                    $isEmptyRow = false;
                }
            }


            if (!$isEmptyRow) {
                DB::table('sundrytype')->insert($sundrydata);
            }
            $sno1++;
        }
        return redirect('banquetbillsundrysetting')->with('success', 'Banquet Sundry Setting Updated!');
    }

    public function openmenuitems(Request $request)
    {
        $permission = revokeopen(121817);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $itemmast = ItemMast::select(
            'itemmast.Name as itemname',
            'itemmast.Code',
            'itemmast.sn',
            'itemmast.DispCode',
            'itemmast.Property_ID',
            'itemmast.HSNCode',
            'itemmast.DiscApp',
            'itemmast.RateEdit',
            'itemmast.ActiveYN',
            'unitmast.name as unitname',
            'itemgrp.Name as itemgrpname',
            'itemcatmast.Name As itemcatname',
            'itemmast.Dispcode',
            'depart_r.Name as Restaurant',
            'depart_r.dcode',
            'itemrate.Rate',
            'itemmast.ActiveYN',
            'itemmast.NType',
            'itemmast.Specification',
            'itemmast.RestCode',
            'depart_k.name as kitchenname'
        )
            ->leftJoin('itemgrp', function ($join) {
                $join->on('itemgrp.Code', '=', 'itemmast.ItemGroup')
                    ->where('itemgrp.property_id', '=', $this->propertyid);
            })
            ->leftJoin('unitmast', function ($join) {
                $join->on('unitmast.ucode', '=', 'itemmast.Unit')
                    ->where('unitmast.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('depart as depart_r', function ($join) {
                $join->on('depart_r.dcode', '=', 'itemmast.RestCode')
                    ->where('depart_r.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('depart as depart_k', function ($join) {
                $join->on('depart_k.dcode', '=', 'itemmast.Kitchen')
                    ->where('depart_k.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('itemrate', function ($join) {
                $join->on('itemrate.ItemCode', '=', 'itemmast.Code')
                    ->where('itemrate.Property_ID', '=', $this->propertyid);
            })
            ->where('itemmast.Property_ID', '=', $this->propertyid)
            ->where('itemmast.RestCode', '=', 'BANQ' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->groupBy('itemmast.RestCode')
            ->get();


        $itemrate = DB::table('itemrate')
            ->where('Property_ID', $this->propertyid)
            ->where('itemrate.RestCode', '=', 'BANQ' . $this->propertyid)
            ->orderBy('ItemCode', 'ASC')
            ->get();

        $itemgrp = DB::table('itemgrp')->where('restcode', 'BANQ' . $this->propertyid)->where('property_id', $this->propertyid)->orderBy('name', 'ASC')->get();
        // $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $itemnames = DB::table('items')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $unit = DB::table('unitmast')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $itemcatmast = DB::table('itemcatmast')->where('RestCode', 'BANQ' . $this->propertyid)->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $kitchen = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'Kitchen')->orderBy('name', 'ASC')->get();
        return view('property.menuitems', [
            'itemmast' => $itemmast,
            'itemrate' => $itemrate,
            'kitchen' => $kitchen,
            //'restaurentdata' => $restaurentdata,
            'itemgrp' => $itemgrp,
            'itemnames' => $itemnames,
            'unit' => $unit,
            'itemcatmast' => $itemcatmast
        ]);
    }
    public function getcurfinyear()
    {
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
        $formatted_currfinancial = date('Y-m-d', strtotime($currfinancial . '-01-04'));
        return json_encode($formatted_currfinancial);
    }

    public function getitemdata(Request $request)
    {
        $itemdata = DB::table('items')
            ->where('propertyid', $this->propertyid)
            ->where('icode', $request->input('icode'))
            ->first();
        return json_encode($itemdata);
    }

    public function getupdatemenuitems(Request $request)
    {
        $permission = revokeopen(121817);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $itemdata = DB::table('itemmast')
            ->select('itemmast.*', 'itemrate.Rate', 'itemrate.AppDate')
            ->join('itemrate', 'itemrate.ItemCode', '=', 'itemmast.Code')
            ->where('itemmast.property_id', $this->propertyid)
            ->where('itemmast.Code', $request->input('code'))
            ->where('itemmast.RestCode', '=', 'BANQ' . $this->propertyid)
            // ->where('itemmast.RestCode', $request->input('restcode'))
            ->first();
        // return $itemdata;
        // $itemgrp = $itemdata->ItemGroup;
        // $restcode = $itemdata->RestCode;
        $restcode = 'BANQ' . $this->propertyid;
        $itemgrps = ItemGrp::where('property_id', $this->propertyid)->where('restcode', $restcode)->orderBy('name')->get();
        $itemcats = ItemCatMast::where('propertyid', $this->propertyid)->where('RestCode', $restcode)->orderBy('Name')->get();

        $data = [
            'itemgrps' => $itemgrps,
            'itemdata' => $itemdata,
            'itemcats' => $itemcats,
        ];
        return json_encode($data);
    }


    public function restxhr(Request $request)
    {
        $restcode = 'BANQ' . $this->propertyid;
        $itemgrps = ItemGrp::where('property_id', $this->propertyid)->where('restcode', $restcode)->orderBy('name')->get();
        $itemcats = ItemCatMast::where('propertyid', $this->propertyid)->where('RestCode', $restcode)->orderBy('Name')->get();

        $data = [
            'itemgrps' => $itemgrps,
            'itemcats' => $itemcats,
        ];
        return json_encode($data);
    }

    public function submitmenuitems(Request $request)
    {
        $permission = revokeopen(121817);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'name' => 'required',
            'restcode' => 'required',
            'icode' => 'required',
            'unit' => 'required',
            'itemcatmast' => 'required',
            'itemgrp' => 'required',
            'kitchen' => 'required',
            'rateedit' => 'required',
        ];
        $tableName = 'itemmast';

        // $existingcode = DB::table($tableName)
        //     ->where('Property_ID', $this->propertyid)
        //     ->where('DispCode', $request->input('itemcode'))
        //     ->where('RestCode', $request->input('restcode'))
        //     ->first();
        // $maxcode = DB::table($tableName)->where('property_id', $this->propertyid)->max('Code');
        // $code = ($maxcode === null) ? $this->propertyid . '1' : ($code = $this->propertyid . substr($maxcode, $this->ptlngth) + 1);

        // if ($existingcode) {
        //     return response()->json(['message' => 'Item Code already exists!'], 500);
        // }

        $existingname = DB::table($tableName)
            ->where('Property_ID', $this->propertyid)
            ->where('Code', $request->input('itemname'))
            //->where('RestCode', $request->input('restcode'))
            ->where('RestCode', '=', 'BANQ' .  $this->propertyid)

            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Name already exists!');
        }


        $itemname = DB::table('items')->where('propertyid', $this->propertyid)->where('icode', $request->input('itemname'))->first();
        $restcode = 'BANQ' . $this->propertyid;
        try {
            $insertdata = [
                'Code' => $request->input('itemname'),
                'Name' => $itemname->name,
                'property_id' => $this->propertyid,
                'RestCode' => $restcode,
                'ItemGroup' => $request->input('itemgrp'),
                'dishtype' => '',
                'favourite' => '',
                'PurchRate' => '0',
                'MinStock' => '0',
                'MaxStock' => '0',
                'ReStock' => '0',
                'LPurRate' => '0',
                'LPurDate' => null,
                'DispCode' => $request->input('itemcode'),
                'ConvRatio' => '0',
                'IssueUnit' => '',
                'Specification' => $request->input('specification') ?? '',
                'LabelName' => '',
                'LabelQty' => '',
                'LabelRemark1' => '',
                'LabelRemark2' => '',
                'LabelRemark3' => '',
                'LabelRemark4' => '',
                'ItemType' => '',
                'NType' => '',
                'iempic' => $request->input('itempic') ?? '',
                'Unit' => $request->input('unit'),
                'RateEdit' => $request->input('rateedit'),
                'ItemCatCode' => $request->input('itemcatmast'),
                'BarCode' => '',
                'Type' => 'Finish',
                'HSNCode' => $request->input('hsncode') ?? '',
                'DiscApp' => $request->input('discappl'),
                'SChrgApp' => '',
                'RateIncTax' => '',
                'Kitchen' => $request->input('kitchen'),
                'U_EntDt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'a',
                'ActiveYN' => $request->input('activeyn'),
            ];

            DB::table($tableName)->insert($insertdata);

            $itemratedata = [
                'Property_ID' => $this->propertyid,
                'ItemCode' => $request->input('itemname'),
                'RestCode' => $restcode,
                //'AppDate' => $request->input('applicabldate'),
                'Rate' => $request->input('salerate'),
                'Party' => '',
                'U_EntDt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'a',
            ];

            DB::table('itemrate')->insert($itemratedata);

            return back()->with('success', 'Item Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item!' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function updatemenuitems(Request $request)
    {
        $permission = revokeopen(121817);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'upname' => 'required',
            'uprestcode' => 'required',
            'upicode' => 'required',
            'upunit' => 'required',
            'upitemcatmast' => 'required',
            'upitemgrp' => 'required',
            'upkitchen' => 'required',
            'uprateedit' => 'required',
        ];
        $tableName = 'itemmast';

        // $existingname = DB::table($tableName)
        //     ->where('Property_ID', $this->propertyid)
        //     ->where('itemcode', $request->input('upitemname'))
        //     ->where('Code', '!=', $request->input('upcode'))
        //     ->where('RestCode', $request->input('uprestcode'))
        //     ->first();

        // if ($existingname) {
        //     return response()->json(['message' => 'Item Name already exists!'], 500);
        // }

        // $itemname = DB::table('items')->where('propertyid', $this->propertyid)->where('icode', $request->input('upcode'))->first();

        try {
            $updatedata = [
                // 'Name' => $itemname->name,
                // 'itemcode' => $request->input('upitemname'),
                'RestCode' => $request->input('uprestcode'),
                'ItemGroup' => $request->input('upitemgrp'),
                'Unit' => $request->input('upunit'),
                'RateEdit' => $request->input('uprateedit'),
                'dishtype' => $request->input('updishtype'),
                'Specification' => $request->input('upspecification') ?? '',
                'favourite' => '',
                'ItemCatCode' => $request->input('upitemcatmast'),
                //'BarCode' => $request->input('upbarcode'),
                'HSNCode' => $request->input('uphsncode') ?? '',
                'DiscApp' => $request->input('updiscappl'),
                'SChrgApp' => $request->input('upservicecharge'),
                //'RateIncTax' => $request->input('uprateinctax'),
                'PurchRate' => $request->upsalerate,
                'Kitchen' => $request->input('upkitchen'),
                'u_updaedt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'e',
                'ActiveYN' => $request->input('upactiveyn'),
            ];

            // return $request->input('upcode');

            DB::table($tableName)
                ->where('Property_ID', $this->propertyid)
                ->where('Code', $request->input('upcode'))
                ->where('RestCode', $request->input('uprestcode'))
                ->update($updatedata);

            $itemratedata = [
                'Property_ID' => $this->propertyid,
                'RestCode' => $request->input('uprestcode'),
                'AppDate' => $request->input('upapplicabldate'),
                'Rate' => $request->input('upsalerate'),
                'Party' => '',
                'U_updatedt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'e',
            ];

            DB::table('itemrate')
                ->where('Property_ID', $this->propertyid)
                ->where('ItemCode', $request->input('upcode'))
                ->where('RestCode', $request->input('uprestcode'))
                ->update($itemratedata);

            return back()->with('success', 'Item Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item!' . $e);
        }
    }

    public function deletemenuitems(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121817);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {

            $chkkot = Kot::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkkot)) {
                return back()->with(
                    'status',
                    'info',
                    'message',
                    'Item used in KOT'
                );
            }

            $chkstock = Stock::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkstock)) {
                return back()->with(
                    'status',
                    'info',
                    'error',
                    'Item used in stock'
                );
            }

            $delete1 = DB::table('itemmast')
                ->where('Property_ID', $this->propertyid)
                ->where('Code', base64_decode($request->input('ucode')))
                ->delete();

            $delete2 = DB::table('itemrate')
                ->where('Property_ID', $this->propertyid)
                ->where('ItemCode', base64_decode($request->input('ucode')))
                ->delete();

            if ($delete1) {
                return response()->json(['message' => 'Item Deleted Successfully']);
            } else {
                return response()->json(['message' => 'Unable to Delete Item!'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Delete Item!'], 500);
        }
    }


    public function getmaxitemcode(Request $request)
    {
        $maxcode = DB::table('itemmast')->where('Property_ID', $this->propertyid)->max('DispCode');
        $code = ($maxcode === null) ? '1' : ($code = $maxcode + 1);
        return json_encode($code);
    }

    function submitmenugroup(Request $request)
    {
        $validate = [
            'name' => 'required',
            'type' => 'required',
        ];
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            //->where('restcode', $request->input('restcode'))
            ->where('restCode', '=', 'BANQ' . $this->propertyid)
            ->where('name', $request->input('name'))
            ->where('property_id', $this->propertyid)
            ->first();

        if ($existingname) {
            return response()->json(['message' => 'Menu Group already exists!'], 500);
        }

        $groupcode = DB::table($tableName)->where('property_id', $this->propertyid)->max('code');
        $groupcode = substr($groupcode, 0, -$this->ptlngth);
        if (empty($groupcode)) {
            $groupcode = 1 . $this->propertyid;
        } else {
            $groupcode = $groupcode + 1 . $this->propertyid;
        }

        // $paydata = Paycharge::select('paycharge.*', 'roomocc.chkintime', 'roomocc.chkindate', '')

        try {
            $insertdata = [
                'code' => $groupcode,
                'name' => $request->input('name'),
                'property_id' => $this->propertyid,
                'restcode' => $request->input('restcode'),
                'type' => 'Finish',
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'activeyn' => $request->input('activeyn'),
            ];

            DB::table($tableName)->insert($insertdata);

            return response()->json(['message' => 'Menu Group Inserted successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Insert Menu Group!' . $e->getMessage()], 500);
        }
    }

    public function openmenucat(Request $request)
    {
        $permission = revokeopen(121816);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('menucategory', 'Menu Category Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $itemcatmast = DB::table('itemcatmast')
            ->select('itemcatmast.*', 'depart.name as departname', 'taxstru.name as taxstruname', 'subgroup.name as subgrpname')
            ->leftJoin('depart', 'depart.dcode', '=', 'itemcatmast.restcode')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'itemcatmast.AcCode')
            ->where('itemcatmast.propertyid', $this->propertyid)
            ->where('itemcatmast.RestCode', 'BANQ' . $this->propertyid)
            ->groupBy('itemcatmast.Code')
            ->orderBy('itemcatmast.name', 'ASC')
            ->get();
        $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $subgroupdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('group_code', ['11' . $this->propertyid, '15' . $this->propertyid, '25' . $this->propertyid,])->orderBy('name', 'ASC')->get();
        // $subgroupdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('nature', ['Sale'])->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')->where('propertyid', $this->propertyid)
            ->distinct()
            ->get();

        //dd($itemcatmast); // check if this has data


        return view('property.menucat', [
            'data' => $itemcatmast,
            'restaurentdata' => $restaurentdata,
            'subgroupdata' => $subgroupdata,
            'taxstrudata' => $taxstrudata
        ]);
    }

    public function submitmenucat(Request $request)
    {
        $permission = revokeopen(121816);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'name' => 'required',
            'taxstru' => 'required',
        ]);

        $tableName = 'itemcatmast';
        $existingname = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->where('Name', $request->input('name'))
            ->where('RestCode', 'BANQ' . $this->propertyid)
            ->first();
        if ($existingname) {
            return back()->with('error', 'Menu Category Name already exists!');
        }
        function skipfirsti($string, $numToSkip)
        {
            return substr($string, $numToSkip) + 1;
        }
        $prefix = 'MT' . $this->propertyid;

        $latestCode = DB::table('itemcatmast')
            ->where('propertyid', $this->propertyid)
            ->where('Code', 'like', $prefix . '%')
            ->orderByDesc(DB::raw("CAST(SUBSTRING(Code, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"))
            ->value('Code');

        $newNumber = $latestCode ? ((int)substr($latestCode, strlen($prefix))) + 1 : 1;
        $code = $prefix . $newNumber;

        // Safety check to prevent duplication
        $exists = DB::table('itemcatmast')
            ->where('propertyid', $this->propertyid)
            ->where('Code', $code)
            ->where('RestCode', 'BANQ' . $this->propertyid)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Duplicate Menu Category Code exists!');
        }


        // if ($request->input('flag') == 'Charge') {
        //     $deskcode = $request->input('restcode');
        //     $field_type = 'C';
        // } else {
        //     $deskcode = '';
        //     $field_type = '';
        // }

        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';

        try {
            $insertdata = [
                'rev_code' => $code,
                'name' => $shortname . ' - ' . $request->input('name'),
                'short_name' => $shortname,
                'ac_code' => $request->input('AcCode'),
                'tax_stru' => $request->input('taxstru'),
                'type' => $request->input('flag') == 'Category' ? 'Dr' : $request->input('type'),
                'flag_type' => 'BAN',
                'Desk_code' => 'BANQ' . $this->propertyid,
                'field_type' => 'C',
                'u_entdt' => $this->currenttime,
                'propertyid' => $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'SysYN' => 'N',
            ];
            $itemcatmastdata = [
                'Code' => $code,
                'Name' => $request->input('name'),
                'RestCode' => 'BANQ' . $this->propertyid,
                'TaxStru' => $request->input('taxstru'),
                'AcCode' => $request->input('AcCode'),
                'OutletYN' => $outyn,
                'Flag' => $request->input('flag'),
                'RoundOff' => 'No',
                'CatType' => $request->input('type'),
                'cattyper' => '',
                'DrCr' => $request->input('flag') == 'Category' ? 'Dr' : 'Cr',
                'RevCode' => $code,
                'U_EntDt' => $this->currenttime,
                'propertyid' => $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'ActiveYN' => 'Y',
            ];
            DB::table('revmast')->insert($insertdata);
            DB::table($tableName)->insert($itemcatmastdata);
            return back()->with('success', 'Menu Category Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Menu Category!' . $e);
        }
    }

    public function updatemenucat(Request $request)
    {
        $permission = revokeopen(121816);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // $validate = $request->validate([
        //     'upname' => 'required',
        //     'uptaxstru' => 'required',
        // ]);
        $tableName = 'itemcatmast';
        $existingname = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->where('Name', $request->input('name'))
            ->where('Code', '!=', $request->input('upcode'))
            ->first();
        if ($existingname) {
            return back()->with('error', 'Category Name already exists!');
        }
        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';
        try {
            $updatedata = [
                'name' => $shortname . ' - ' . $request->input('upname'),
                'short_name' => $shortname,
                'ac_code' => $request->input('upAcCode'),
                'tax_stru' => $request->input('uptaxstru'),
                'type' => $request->input('upflag') == 'Category' ? 'Dr' : $request->input('uptype'),
                'flag_type' => 'BAN',
                'Desk_code' => 'BANQ' . $this->propertyid,
                'field_type' => 'C',
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'SysYN' => 'N',
            ];
            $itemcatmastdata = [
                'Name' => $request->input('upname'),
                'TaxStru' => $request->input('uptaxstru'),
                'AcCode' => $request->input('upAcCode'),
                'OutletYN' => $outyn,
                'Flag' => $request->input('upflag'),
                'RoundOff' => 'No',
                'CatType' => $request->input('uptype'),
                'cattyper' => '',
                'DrCr' => $request->input('upflag') == 'Category' ? 'Dr' : 'Cr',
                'U_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'ActiveYN' => 'Y',
            ];

            // return $request->input('uprestcode');
            DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $request->input('upcode'))->where('Desk_code', $request->input('uprestcode'))->update($updatedata);
            DB::table($tableName)->where('propertyid', $this->propertyid)->where('Code', $request->input('upcode'))->where('RestCode', $request->input('uprestcode'))->update($itemcatmastdata);
            return back()->with('success', 'Menu Category Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Menu Category!' . $e);
        }
    }
    public function deletemenucategory(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121816);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {

            $chkitemmast = ItemMast::where('Property_ID', $this->propertyid)->where('ItemCatCode', $ucode)->first();
            if (!is_null($chkitemmast)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Category used in Menu Item'
                ]);
            }
            $jaldiwahasehato📢 = DB::table('itemcatmast')
                ->where('propertyid', $this->propertyid)
                ->where('Code', $ucode)
                ->delete();

            $jaldiwahasehato2📢 = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where('rev_code', $ucode)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Menu Category Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Menu Category!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Menu Category!');
        }
    }

    public function openprintfp(Request $request, $docid)
    {
        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
        $hallbookData = DB::table('hallbook as HB')
            ->select([
                'HB.*',
                DB::raw('0 AS SNo'),
                DB::raw("'' AS Item"),
                DB::raw('0 AS QtyIss'),
                DB::raw('0 AS Rate'),
                DB::raw('0 AS LineAmt'),
                DB::raw("'' AS Unit"),
                DB::raw('0 AS LineTaxPer'),
                DB::raw('0 AS TaxAmt'),
                DB::raw('0 AS LineDiscP'),
                DB::raw('0 AS LineDiscA'),
                DB::raw("'' AS Remarks"),
                DB::raw('0 AS LineTotal'),
                DB::raw("'' AS IName"),
                DB::raw("'' AS ItemGrpName")
            ])
            ->where('HB.docid', $docid)
            ->first();


        $venueData = DB::table('venueocc as VC')
            ->select([
                'VC.*',
                'D.name as VenuName'
            ])
            ->leftJoin('venuemast as D', 'VC.venucode', '=', 'D.code')
            ->where('VC.fpdocid', $docid)
            ->get();

        $advanceData = DB::table('paychargeh')
            ->select([
                DB::raw('(amtcr - amtdr) AS Adv'),
                'paytype',
                'vno',
                'vdate'
            ])
            ->whereIn('vtype', ['AD', 'AR'])
            ->where('contradocid', $docid)
            ->get();
        return view('property.printfp', [
            'ncurdate' => $this->ncurdate,
            'company' => $company,
            'statename' => $statename,
            'hallbookData' => $hallbookData,
            'venueData' => $venueData,
            'advanceData' => $advanceData
        ]);
    }

    public function opensalesregister(Request $request)
    {
        $comp = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
        return view('property.salesregister', [
            'comp' => $comp,
            'statename' => $statename,
            'fromdate' => $this->ncurdate,
            'todate' => $this->ncurdate
        ]);
    }
    public function fetchsalesregister(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $data = DB::table('hallsale1 as H')
            ->select([
                'H.vno',
                'H.vdate',
                'H.party',
                'H.noofpax',
                'H.rateperpax',
                DB::raw('H.total as TotalPerCover'),
                'H.discamt',
                'H.taxable',
                'H.nontaxable',
                'H.roundoff',
                DB::raw('H.netamt as Amount')
            ])
            ->where('H.restcode', 'BANQ' . $this->propertyid)
            ->whereBetween('H.vdate', [$fromdate, $todate])
            ->orderBy('H.vno')
            ->orderBy('H.vdate')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function banqsettlementsummary(Request $request)
    {
        $ncurdate = $this->ncurdate;
        $fromdate = $request->input('fromdate', $ncurdate);
        $todate = $request->input('todate', $ncurdate);
        $comp = Companyreg::where('propertyid', $this->propertyid)->first();
        $company = SubGroup::where('propertyid', $this->propertyid)->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name')->groupBy('sub_code')->get();
        //$departs = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->groupBy('dcode')->orderBy('name', 'ASC')->get();

        $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
        $users = User::where('propertyid', $this->propertyid)->get();
        $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();

        return view('property.banq_settlementsummary', [
            'fromdate' => $ncurdate,
            'comp' => $comp,
            'company' => $company,
            //'departs' => $departs,
            'todate',
            'statename' => $statename,
            'users' => $users,
            'revheading' => $revheading
        ]);
    }
}
