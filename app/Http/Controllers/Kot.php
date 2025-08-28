<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Kot as KotModal;
use App\Models\Depart;
use App\Models\Paycharge;
use App\Models\RoomMast;
use App\Models\EnviroPos;
use App\Models\Companyreg;
use App\Models\Depart1;
use App\Models\ItemMast;
use App\Models\KotLog;
use App\Models\PrintDelay;
use App\Models\PrintingSetup;
use App\Models\Stock;
use App\Models\Stocklog;
use App\Models\VoucherPrefix;
use App\Models\VoucherType;
use Exception;
use Illuminate\Support\Facades\Log;

class Kot extends Controller
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

    public function showheader()
    {
        $username = Auth::user()->name;
        return view('property.layouts.header', ['data' => $username]);
    }

    public function revokeopen($colname)
    {
        // $value = DB::table('userpermission')->where('propertyid', $this->propertyid)->where('u_name', Auth::user()->name)->value($colname);
        // return $value;
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
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>';
        echo '<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>';
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

    public function kotentry(Request $request)
    {
        // $permission = revokeopen(172011);
        // if (is_null($permission) || $permission->view == 0) {
        //     return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        // }
        $dcode = $request->query('dcode');
        $roomone = $request->query('roomno') ?? '';

        $departdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->where('dcode', $dcode)
            ->first();

        if ($departdata) {
            $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
                ->where('departcode', $departdata->dcode)
                ->pluck('associatedrestcode')
                ->toArray();

            $restcodes = array_merge([$departdata->dcode], $associatedrestcode);
        }

        $menugroup = DB::table('itemgrp')
            ->where('property_id', $this->propertyid)
            ->whereIn('restcode', $restcodes)
            ->where('activeyn', 'Y')
            ->orderBy('name', 'ASC')
            ->get();

        if (strtolower($departdata->nature) == 'room service') {
            $roomno = DB::table('roomocc')
                ->leftJoin('paycharge', function ($join) {
                    $join->on('paycharge.roomno', '=', 'roomocc.roomno')
                        ->on('paycharge.sno1', '=', 'roomocc.sno1')
                        ->where(function ($query) {
                            $query->where('paycharge.vtype', '=', 'BRS')
                                ->orWhereNull('paycharge.vtype');
                        });
                })
                ->select('paycharge.sn', 'roomocc.roomno', 'roomocc.name', 'paycharge.billno')
                ->where('roomocc.propertyid', $this->propertyid)
                ->whereNull('roomocc.type')
                ->groupBy('roomocc.roomno', 'roomocc.sno1')
                ->get();

            $label = 'Room No.';
        } else {
            $roomno = RoomMast::select('rcode as roomno')->where('rest_code', $dcode)->where('propertyid', $this->propertyid)->where('type', 'TB')->orderBy('rcode', 'ASC')->get();
            $label = 'Table No.';
        }

        $nctype = DB::table('nctype_mast')->where('propertyid', $this->propertyid)->get();
        $server_mast = DB::table('server_mast')->where('propertyid', $this->propertyid)->where('activeYN', 'Y')->get();
        $outletdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Outlet', 'ROOM SERVICE'])->get();
        $envpos = EnviroPos::where('propertyid', $this->propertyid)->first();
        $curusername = $this->username;
        $adminuname = Companyreg::where('propertyid', $this->propertyid)->orderBy('sn', 'ASC')->first();

        return view('property.kotentry', [
            'menudata' => $menugroup,
            'roomno' => $roomno,
            'depart' => $departdata,
            'nctype' => $nctype,
            'servermast' => $server_mast,
            'outletdata' => $outletdata,
            'label' => $label,
            'roomone' => $roomone,
            'envpos' => $envpos,
            'curusername' => $curusername,
            'adminuname' => $adminuname
        ]);
    }

    public function getmaxkrsno(Request $request)
    {
        $shortname = $request->input('shortname');
        $data = DB::table('voucher_prefix')->where('v_type', 'K' . $shortname)->where('propertyid', $this->propertyid)->max('start_srl_no') + 1;
        return json_encode($data);
    }

    public function getmaxnrsno(Request $request)
    {
        $shortname = $request->input('shortname');
        $data = DB::table('voucher_prefix')->where('v_type', 'N' . $shortname)->where('propertyid', $this->propertyid)->max('start_srl_no') + 1;
        return json_encode($data);
    }

    public function getsessionmast(Request $request)
    {
        $curtime = $request->input('curtime');
        $fetch = DB::table('session_mast')->where('propertyid', $this->propertyid)->where('from_time', '<=', $curtime)->where('to_time', '>=', $curtime)->value('name') ?? '';
        return json_encode($fetch);
    }

    public function fetchpendingkot(Request $request)
    {
        $dcode = $request->input('dcode');

        $departdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->where('dcode', $dcode)
            ->first();

        if ($departdata) {
            $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
                ->where('departcode', $departdata->dcode)
                ->pluck('associatedrestcode')
                ->toArray();

            $restcodes = array_merge([$departdata->dcode], $associatedrestcode);
        }

        $data = DB::table('kot')
            ->select(
                'kot.vno',
                'kot.docid',
                'kot.vdate',
                'kot.vtime',
                'kot.roomno',
                'kot.qty',
                'server_mast.name AS waiterbhai',
                'itemmast.Name AS itemnaam',
                'kot.voidyn'
            )
            ->join('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'kot.item')
                    ->on('itemmast.RestCode', '=', 'kot.restcode');
            })
            ->LeftJoin('server_mast', 'server_mast.scode', '=', 'kot.waiter')
            ->where('kot.propertyid', '=', $this->propertyid)
            ->whereIn('kot.restcode', $restcodes)
            ->where('kot.nckot', '=', 'N')
            ->where('kot.vdate', $this->ncurdate)
            ->where('kot.pending', 'Y')
            ->orderBy('kot.vno')
            ->get();

        return json_encode($data);
    }

    public function fetchitemdetailsbbyvno(Request $request)
    {
        $docid = $request->docid;

        $chkmerged = KotModal::where('docid', $docid)->where('propertyid', $this->propertyid)->value('mergedwith');

        $datatmp = DB::table('itemmast')
            ->join('kot', function ($join) {
                $join->on('itemmast.Code', '=', 'kot.item')
                    ->on('itemmast.RestCode', '=', 'kot.restcode');
            })
            ->select('itemmast.Name', 'kot.description', 'kot.docid', 'kot.qty', 'kot.rate', 'kot.voidyn', 'kot.item', 'kot.vno', 'kot.sno', 'kot.roomno', 'kot.waiter', 'kot.remarks', 'kot.pax', 'kot.docid', 'kot.vtype')
            ->where('kot.propertyid', $this->propertyid)
            ->orderBy('kot.sno');

        if (!empty($chkmerged)) {
            $mergedocid = $chkmerged;
            $data = $datatmp->whereIn('kot.docid', explode(',', $mergedocid))->get();
        } else {
            $data = $datatmp->where('kot.docid', $docid)->get();
        }


        return json_encode($data);
    }

    public function fetchncpreviouskot(Request $request)
    {
        $dcode = $request->input('dcode');

        $departdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->where('dcode', $dcode)
            ->first();

        if ($departdata) {
            $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
                ->where('departcode', $departdata->dcode)
                ->pluck('associatedrestcode')
                ->toArray();

            $restcodes = array_merge([$departdata->dcode], $associatedrestcode);
        }

        $data = DB::table('kot')
            ->select('kot.vno', 'kot.vdate', 'kot.docid', 'kot.vtime', 'kot.roomno', 'kot.qty', 'server_mast.name AS waiterbhai', 'itemmast.Name AS itemnaam')
            ->join('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'kot.item')
                    ->on('itemmast.RestCode', '=', 'kot.restcode');
            })
            ->join('server_mast', 'server_mast.scode', '=', 'kot.waiter')
            ->where('kot.propertyid', '=', $this->propertyid)
            ->whereIn('kot.restcode', $restcodes)
            ->where('kot.nckot', '=', 'Y')
            // ->where('kot.vtype', 'N' . $departdata->short_name)
            ->where('kot.vdate', $this->ncurdate)
            ->orderBy('kot.vno')
            ->get();
        return json_encode($data);
    }

    public function oldwaitername(Request $request)
    {
        $roomno = $request->input('roomno');
        $restcode = $request->input('restcode');

        $waitercode = KotModal::where('propertyid', $this->propertyid)->where('restcode', $restcode)->where('roomno', $roomno)
            ->where('pending', 'Y')->select('waiter')->first();
        $ordertype = 'New Order';
        if (!is_null($waitercode)) {
            $chk = KotModal::whereNot('docid', $waitercode->docid)->where('propertyid', $this->propertyid)->where('roomno', $roomno)->first();

            if (is_null($chk)) {
                $ordertype = 'New Order';
            } else {
                $ordertype = 'Running Order';
            }
        }

        if ($waitercode) {
            $data = [
                'ordertype' => $ordertype,
                'waiter' => $waitercode
            ];
            return json_encode($data);
        } else {
            $data = [
                'ordertype' => $ordertype,
                'waiter' => ''
            ];
            return json_encode($data);
        }
    }

    public function submitkotentry(Request $request)
    {
        // $permission = revokeopen(172011);
        // if (is_null($permission) || $permission->ins == 0) {
        //     return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        // }
        $validate = $request->validate([
            'roomno' => 'required',
            'pax' => 'required',
            'waiter' => 'required',
            'fixrestcode' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $fixrestcode = $request->input('fixrestcode');
            $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $fixrestcode)->first();
            $totalitems = $request->totalitems;

            $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
                ->where('departcode', $depart->dcode)
                ->pluck('associatedrestcode');

            $associate = false;
            for ($i = 0; $i <= $totalitems; $i++) {
                $itemcode = $request->input('itemcode' . $i);
                $itemmast = ItemMast::where('Code', $itemcode)->where('Property_ID', $this->propertyid)
                    ->whereIn('RestCode', $associatedrestcode)->first();
                if ($itemmast) {
                    $associate = true;
                    break;
                }
            }

            if ($associatedrestcode->isNotEmpty()) {
                $chkroommast = RoomMast::where('propertyid', $this->propertyid)
                    ->whereIn('rest_code', $associatedrestcode)
                    ->where('rcode', $request->roomno)
                    ->first();

                if (is_null($chkroommast) && $associate) {
                    return response()->json([
                        'status' => 'error',
                        'docid' => '',
                        'message' => "Room No.: $request->roomno Not Found for related Outlet!"
                    ]);
                }
            }

            $ncurdate = $this->ncurdate;
            $nckotreason = $request->input('nckotreason') ?? '';
            $roomoccdata = RoomMast::where('propertyid', $this->propertyid)->where('rcode', $request->input('roomno'))->first();
            $olddocidpendingkot = $request->input('olddocidpendingkot');
            if (!empty($olddocidpendingkot)) {

                try {
                    $chkmerged = KotModal::where('docid', $olddocidpendingkot)->where('propertyid', $this->propertyid)->value('mergedwith');
                    $kotlogdatatmp = KotModal::where('propertyid', $this->propertyid)->orderBy('sno', 'ASC');

                    if (!empty($chkmerged)) {
                        $kotlogdata = $kotlogdatatmp->whereIn('docid', explode(',', $chkmerged))->get();
                    } else {
                        $kotlogdata = $kotlogdatatmp->where('docid', $olddocidpendingkot)->get();
                    }

                    // Log::info('Preparin KOT LOG Data: ' . json_encode($kotlogdata));

                    foreach ($kotlogdata as $data) {
                        $kotlogindata = [
                            'propertyid' => $data->propertyid,
                            'docid' => $data->docid,
                            'sno' => $data->sno,
                            'pax' => $data->pax,
                            'vno' => $data->vno,
                            'itemrestcode' => $data->itemrestcode,
                            'item' => $data->item,
                            'description' => $data->description,
                            'qty' => $data->qty,
                            'rate' => $data->rate,
                            'amount' => $data->amount,
                            'voidyn' => $data->voidyn,
                            'vtype' => $data->vtype,
                            'vdate' => $data->vdate,
                            'vtime' => $data->vtime,
                            'vprefix' => $data->vprefix,
                            'roomcat' => $data->roomcat,
                            'restcode' => $data->restcode,
                            'waiter' => $data->waiter,
                            'pending' => $data->pending,
                            'delflag' => $data->delflag,
                            'contradocid' => $data->contradocid,
                            'contrsno' => $data->contrsno,
                            'reasons' => $data->reasons,
                            'ncreason' => $nckotreason,
                            'remarks' => $data->remarks,
                            'roomtype' => $data->roomtype,
                            'roomno' => $data->roomno,
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                            'nckot' => $data->nckot,
                            'nctype' => $data->nctype,
                            'freesno' => $data->freesno,
                            'printed' => $data->printed,
                            'schemecode' => $data->schemecode,
                            'tokenno' => $data->tokenno,
                            'printflag' => $data->printflag,
                            'mergedwith' => $data->mergedwith
                        ];

                        // Log::info('Kot Data Array: ' . json_encode($kotlogindata));
                        $inserted = DB::table('kotlog')->insert($kotlogindata);
                        // Log::info('KOTLOG Insert Result: ' . ($inserted ? 'Success' : 'Failed'));
                    }

                    if (!empty($chkmerged)) {
                        KotModal::where('propertyid', $this->propertyid)->whereIn('docid', explode(',', $chkmerged))->delete();
                    } else {
                        KotModal::where('propertyid', $this->propertyid)->where('docid', $olddocidpendingkot)->delete();
                    }

                    if ($request->input('nctypecheckbox') == 'on') {
                        $chkmergedstock = Stock::where('docid', $olddocidpendingkot)->where('propertyid', $this->propertyid)->value('mergedwith');
                        $stocklogdatatmp = Stock::where('propertyid', $this->propertyid)->orderBy('sno', 'ASC');

                        if (!empty($chkmergedstock)) {
                            $stocklogdata = $stocklogdatatmp->whereIn('stock.docid', explode(',', $chkmerged))->get();
                        } else {
                            $stocklogdata = $stocklogdatatmp->where('stock.docid', $olddocidpendingkot)->get();
                        }

                        foreach ($stocklogdata as $row) {
                            $stockdata = [
                                'propertyid' => $this->propertyid,
                                'docid' => $row->docid,
                                'sno' => $row->sno,
                                'vno' => $row->vno,
                                'itemrestcode' => $row->itemrestcode,
                                'item' => $row->item,
                                'qtyiss' => $row->qtyiss,
                                'qtyrec' => $row->qtyiss,
                                'taxper' => $row->taxper,
                                'taxamt' => $row->taxamt,
                                'discper' => $row->discper,
                                'discamt' => $row->discamt,
                                'kotdocid' => $row->kotdocid,
                                'kotsno' => $row->kotsno,
                                'total' => $row->total,
                                'discapp' => $row->discapp,
                                'roundoff' => $row->roundoff,
                                'partycode' => $row->partycode,
                                'departcode' => $row->departcode,
                                'godowncode' => $row->godowncode,
                                'chalqty' => $row->chalqty,
                                'recdqty' => $row->recdqty,
                                'accqty' => $row->acqty ?? '',
                                'rejqty' => $row->rejqty,
                                'recdunit' => $row->recdunit,
                                'specification' => $row->specification,
                                'itemrate' => $row->itemrate,
                                'delflag' => $row->delflag,
                                'landval' => $row->landval,
                                'convratio' => $row->convratio,
                                'indentdocid' => $row->indentdocid,
                                'indentsno' => $row->indentsno,
                                'issqty' => $row->issqty,
                                'issueunit' => $row->issueunit,
                                'freesno' => $row->freesno,
                                'schemecode' => $row->schemecode,
                                'seqno' => $row->seqno,
                                'company' => $row->company,
                                'schrgapp' => $row->schrgapp,
                                'schrgper' => $row->schrgper,
                                'schrgamt' => $row->schrgamt,
                                'refdocid' => $row->refdocid,
                                'rate' => $row->rate,
                                'amount' => $row->amount,
                                'voidyn' => $row->voidyn,
                                'vtype' => $row->vtype,
                                'vdate' => $row->vdate,
                                'vtime' => $row->vtime,
                                'vprefix' => $row->vprefix,
                                'roomcat' => $row->roomcat,
                                'restcode' => $row->restcode,
                                'contradocid' => $row->contradocid,
                                'contrasno' => $row->contrasno,
                                'remarks' => $row->remarks,
                                'roomtype' => $row->roomtype,
                                'roomno' => $row->roomno,
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];

                            Stocklog::insert($stockdata);
                        }

                        if (!empty($chkmergedstock)) {
                            Stock::where('propertyid', $this->propertyid)->whereIn('docid', explode(',', $chkmergedstock))->delete();
                        } else {
                            Stock::where('propertyid', $this->propertyid)->where('docid', $olddocidpendingkot)->delete();
                        }
                    }

                    $groupedItems = [];
                    $generatedDocIDs = [];

                    for ($i = 1; $i <= $totalitems; $i++) {
                        $itemcode = $request->input('itemcode' . $i);
                        $restcodeitem = DB::table('itemmast')->where('Property_ID', $this->propertyid)
                            ->where('Code', $itemcode)->value('RestCode');
                        $groupedItems[$associatedrestcode->isNotEmpty() ? $restcodeitem : $fixrestcode][] = ['i' => $i, 'itemcode' => $itemcode];
                    }

                    foreach ($groupedItems as $restcode => $items) {

                        $nckotyn = $request->input('nctypecheckbox') == 'on' ? 'Y' : 'N';
                        $nckot = $nckotyn == 'Y' ? $request->input('nctype') : '';
                        $pending = $nckotyn == 'Y' ? 'N' : 'Y';

                        if ($request->input('nctypecheckbox') == 'on') {
                            foreach ($items as $data) {

                                $i = $data['i'];
                                $qty = $request->input('quantity' . $i);
                                $rate = $request->input('rate' . $i);

                                $stocklog = Stocklog::where('propertyid', $this->propertyid)->where('docid', $request->input('kotdocidrow' . $i))->first();

                                $stockdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $stocklog->docid,
                                    'sno' => $i,
                                    'vno' => $stocklog->vno,
                                    'itemrestcode' => $restcode,
                                    'item' => $data['itemcode'],
                                    'qtyiss' => $qty,
                                    'qtyrec' => '',
                                    'taxper' => '',
                                    'taxamt' => '',
                                    'discper' => '',
                                    'discamt' => '',
                                    'kotdocid' => '',
                                    'kotsno' => '',
                                    'total' => '',
                                    'discapp' => '',
                                    'roundoff' => '',
                                    'partycode' => '',
                                    'departcode' => '',
                                    'godowncode' => '',
                                    'chalqty' => '',
                                    'recdqty' => '',
                                    'accqty' => '',
                                    'rejqty' => '',
                                    'recdunit' => '',
                                    'specification' => '',
                                    'itemrate' => '',
                                    'delflag' => 'N',
                                    'landval' => '',
                                    'convratio' => '',
                                    'indentdocid' => '',
                                    'indentsno' => '',
                                    'issqty' => '',
                                    'issueunit' => '',
                                    'freesno' => '',
                                    'schemecode' => '',
                                    'seqno' => '',
                                    'company' => '',
                                    'schrgapp' => '',
                                    'schrgper' => '',
                                    'schrgamt' => '',
                                    'refdocid' => '',
                                    'rate' => $rate,
                                    'amount' => $qty * $rate,
                                    'voidyn' => $request->input('voidyn' . $i),
                                    'vtype' => $stocklog->vtype,
                                    'vdate' => $stocklog->vdate,
                                    'vtime' => $stocklog->vtime,
                                    'vprefix' => $stocklog->vprefix,
                                    'roomcat' => $stocklog->roomcat,
                                    'restcode' => $restcode,
                                    'contradocid' => '',
                                    'contrasno' => '',
                                    'remarks' => $request->input('kotremark') ?? '',
                                    'roomtype' => $roomoccdata->type,
                                    'roomno' => $request->input('roomno'),
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'e',
                                ];

                                Stock::insert($stockdata);
                            }
                        }

                        foreach ($items as $data) {

                            $i = $data['i'];
                            $qty = $request->input('quantity' . $i);
                            $rate = $request->input('rate' . $i);

                            $kotlog = KotLog::where('propertyid', $this->propertyid)->where('docid', $request->input('kotdocidrow' . $i))->first();
                            $generatedDocIDs[] = $kotlog->docid;
                            $kotdata = [
                                'propertyid' => $this->propertyid,
                                'docid' => $kotlog->docid,
                                'sno' => $i,
                                'pax' => $request->input('pax'),
                                'vno' => $kotlog->vno,
                                'itemrestcode' => $restcode,
                                'item' => $data['itemcode'],
                                'description' => $request->input('description' . $i) ?? '',
                                'qty' => $qty,
                                'rate' => $rate,
                                'amount' => $qty * $rate,
                                'voidyn' => $request->input('voidyn' . $i),
                                'vtype' => $kotlog->vtype,
                                'vdate' => $kotlog->vdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $kotlog->vprefix,
                                'roomcat' => $roomoccdata->room_cat,
                                'restcode' => $restcode,
                                'waiter' => $request->input('waiter'),
                                'pending' => $pending,
                                'delflag' => '',
                                'contradocid' => '',
                                'contrsno' => '',
                                'reasons' => $request->input('editingreasons') ?? '',
                                'ncreason' => $nckotreason,
                                'remarks' => $request->input('kotremark') ?? '',
                                'roomtype' => $kotlog->roomtype,
                                'roomno' => $kotlog->roomno,
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'e',
                                'nckot' => $nckotyn,
                                'nctype' => $nckot,
                                'freesno' => $kotlog->freesno,
                                'printed' => $kotlog->printed,
                                'schemecode' => $kotlog->schemecode,
                                'tokenno' => $kotlog->tokenno,
                                'printflag' => $kotlog->printflag,
                                'mergedwith' => $kotlog->mergedwith
                            ];
                            DB::table('kot')->insert($kotdata);
                        }
                    }

                    $uniqueDocIDs = array_unique($generatedDocIDs);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'docid' => $uniqueDocIDs,
                        'message' => 'Kot Entry Updated!'
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'docid' => '',
                        'message' => 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine()
                    ], 500);
                }
            }

            $groupedItems = [];
            $generatedDocIDs = [];

            for ($i = 1; $i <= $totalitems; $i++) {
                $itemcode = $request->input('itemcode' . $i);
                $restcodeitem = DB::table('itemmast')->where('Property_ID', $this->propertyid)
                    ->where('Code', $itemcode)->value('RestCode');
                $groupedItems[$associatedrestcode->isNotEmpty() ? $restcodeitem : $fixrestcode][] = ['i' => $i, 'itemcode' => $itemcode];
            }

            foreach ($groupedItems as $restcode => $items) {
                $dprow = Depart::where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();
                $desc = $dprow->short_name . ($request->input('nctypecheckbox') == 'on' ? ' N.C. KOT Entry' : ' KOT Entry');
                $vtype = VoucherType::where('propertyid', $this->propertyid)->where('restcode', $restcode)->where('description', $desc)->value('v_type');
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $ncurdate)->whereDate('date_to', '>=', $ncurdate)->first();

                $vprefix = $chkvpf->prefix;
                $vno = $chkvpf->start_srl_no + 1;
                $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;
                $generatedDocIDs[] = $docid;

                $nckotyn = $request->input('nctypecheckbox') == 'on' ? 'Y' : 'N';
                $nckot = $nckotyn == 'Y' ? $request->input('nctype') : '';
                $pending = $nckotyn == 'Y' ? 'N' : 'Y';
                $sno1 = 1;
                $snokot = 1;
                if ($request->input('nctypecheckbox') == 'on') {
                    foreach ($items as $data) {
                        $i = $data['i'];
                        $qty = $request->input('quantity' . $i);
                        $rate = $request->input('rate' . $i);
                        $stockdata = [
                            'propertyid' => $this->propertyid,
                            'docid' => $docid,
                            'sno' => $sno1++,
                            'vno' => $vno,
                            'itemrestcode' => $restcode,
                            'item' => $data['itemcode'],
                            'qtyiss' => $qty,
                            'qtyrec' => '',
                            'taxper' => '',
                            'taxamt' => '',
                            'discper' => '',
                            'discamt' => '',
                            'kotdocid' => '',
                            'kotsno' => '',
                            'total' => '',
                            'discapp' => '',
                            'roundoff' => '',
                            'partycode' => '',
                            'departcode' => '',
                            'godowncode' => '',
                            'chalqty' => '',
                            'recdqty' => '',
                            'accqty' => '',
                            'rejqty' => '',
                            'recdunit' => '',
                            'specification' => '',
                            'itemrate' => '',
                            'delflag' => 'N',
                            'landval' => '',
                            'convratio' => '',
                            'indentdocid' => '',
                            'indentsno' => '',
                            'issqty' => '',
                            'issueunit' => '',
                            'freesno' => '',
                            'schemecode' => '',
                            'seqno' => '',
                            'company' => '',
                            'schrgapp' => '',
                            'schrgper' => '',
                            'schrgamt' => '',
                            'refdocid' => '',
                            'rate' => $rate,
                            'amount' => $qty * $rate,
                            'voidyn' => $request->input('voidyn' . $i),
                            'vtype' => $vtype,
                            'vdate' => $ncurdate,
                            'vtime' => date('H:i:s'),
                            'vprefix' => $vprefix,
                            'roomcat' => $roomoccdata->room_cat,
                            'restcode' => $restcode,
                            'contradocid' => '',
                            'contrasno' => '',
                            'remarks' => $request->input('kotremark') ?? '',
                            'roomtype' => $roomoccdata->type,
                            'roomno' => $request->input('roomno'),
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                        ];
                        DB::table('stock')->insert($stockdata);
                    }
                }

                foreach ($items as $data) {
                    $i = $data['i'];
                    $qty = $request->input('quantity' . $i);
                    $rate = $request->input('rate' . $i);
                    $kotdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'sno' => $snokot++,
                        'pax' => $request->input('pax'),
                        'vno' => $vno,
                        'itemrestcode' => $restcode,
                        'item' => $data['itemcode'],
                        'description' => $request->input('description' . $i) ?? '',
                        'qty' => $qty,
                        'rate' => $rate,
                        'amount' => $qty * $rate,
                        'voidyn' => $request->input('voidyn' . $i),
                        'vtype' => $vtype,
                        'vdate' => $ncurdate,
                        'vtime' => date('H:i:s'),
                        'vprefix' => $vprefix,
                        'roomcat' => $roomoccdata->room_cat,
                        'restcode' => $restcode,
                        'waiter' => $request->input('waiter'),
                        'pending' => $pending,
                        'delflag' => '',
                        'contradocid' => '',
                        'contrsno' => '',
                        'reasons' => $request->input('editingreasons') ?? '',
                        'ncreason' => $nckotreason,
                        'remarks' => $request->input('kotremark') ?? '',
                        'roomtype' => $roomoccdata->type,
                        'roomno' => $request->input('roomno'),
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                        'nckot' => $nckotyn,
                        'nctype' => $nckot,
                        'freesno' => '',
                        'printed' => '',
                        'schemecode' => '',
                        'tokenno' => '',
                        'printflag' => '',
                    ];
                    DB::table('kot')->insert($kotdata);
                }

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');
            }

            $uniqueDocIDs = array_unique($generatedDocIDs);

            if (count($uniqueDocIDs) > 1) {
                $merged = implode(',', $uniqueDocIDs);

                DB::table('kot')
                    ->where('propertyid', $this->propertyid)
                    ->whereIn('docid', $uniqueDocIDs)
                    ->update(['mergedwith' => $merged]);

                DB::table('stock')
                    ->where('propertyid', $this->propertyid)
                    ->whereIn('docid', $uniqueDocIDs)
                    ->update(['mergedwith' => $merged]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'docid' => $uniqueDocIDs,
                'message' => 'Kot Entry Submitted!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'docid' => '',
                'message' => 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine()
            ], 500);
        }
    }

    public function kottransfer(Request $request)
    {
        $permission = revokeopen(172015);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $dcode = $request->query('dcode');
        $vnos = KotModal::select('vno')->where('propertyid', $this->propertyid)->where('restcode', $dcode)->where('nckot', 'N')
            ->where('pending', 'Y')->orderBy('vno')->groupBy('vno')->get();

        return view('property.kottransfer', [
            'vnos' => $vnos,
            'dcode' => $dcode
        ]);
    }

    public function vnoxhr(Request $request)
    {
        $vno = $request->input('vno');
        $dcode = $request->input('dcode');
        $restroomno  = KotModal::select('docid', 'roomno')->where('propertyid', $this->propertyid)->where('restcode', $dcode)->where('nckot', 'N')
            ->where('pending', 'Y')->whereNot('vno', $vno)->orderBy('vno')->groupBy('roomno')->get();
        $oneroom = KotModal::select('roomno', 'docid')->where('propertyid', $this->propertyid)->where('restcode', $dcode)->where('nckot', 'N')
            ->where('pending', 'Y')->where('vno', $vno)->orderBy('vno')->first();
        $data = [
            'restrooms' => $restroomno,
            'oneroom' => $oneroom
        ];
        return json_encode($data);
    }

    public function kottransferstore(Request $request)
    {
        $permission = revokeopen(172015);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vno = $request->input('vno');
        $dcode = $request->input('dcode');
        $roomno = $request->input('roomno');
        $docid = $request->input('docid');

        $data = [
            'roomno' => $roomno
        ];

        KotModal::where('propertyid', $this->propertyid)->where('restcode', $dcode)->where('nckot', 'N')->where('pending', 'Y')
            ->where('docid', $docid)->update($data);
        return back()->with('success', 'Kot Transfered Successfully');
    }

public function sendprintdata(Request $request)
{
    $docid = $request->input('docid');
    $kottbl  = KotModal::where('propertyid', $this->propertyid)->whereIn('docid', (array)$docid)->first();
    $printedit = $request->input('printedit');

    $enviropos = EnviroPos::where('propertyid', $this->propertyid)->first();

    // build the $kot collection based on mergedwith / printedit rules (kept same as your code)
    if (!empty($kottbl->mergedwith)) {
        if ($printedit == 'Y' && $enviropos->printeditkot == 'All Items') {
            $kot = KotModal::where('propertyid', $this->propertyid)->where('mergedwith', $kottbl->mergedwith)->get();
        } else if ($printedit == 'Y' && $enviropos->printeditkot == 'Void Items') {
            $kot = KotModal::where('propertyid', $this->propertyid)->where('mergedwith', $kottbl->mergedwith)->where('voidyn', 'Y')->get();
        } else { // printedit == 'N'
            $kot = KotModal::where('propertyid', $this->propertyid)->where('mergedwith', $kottbl->mergedwith)->get();
        }
    } else {
        if ($printedit == 'Y' && $enviropos->printeditkot == 'All Items') {
            $kot = KotModal::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        } else if ($printedit == 'Y' && $enviropos->printeditkot == 'Void Items') {
            $kot = KotModal::where('propertyid', $this->propertyid)->where('docid', $docid)->where('voidyn', 'Y')->get();
        } else { // printedit == 'N'
            $kot = KotModal::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        }
    }

    if ($kot) {
        try {
            $sn = 1; // global item serial
            // caches so we don't query printing setup repeatedly for same restcode
            $printingSetupCache = []; // [restcode] => Collection of PrintingSetup (ordered)
            $psnoMap = []; // [restcode] => array of ['setup' => $setup, 'psno' => n, 'kitchen' => $setup->kitchen]

            foreach ($kot as $row) {
                $restcode = $row->restcode;
                $depart = Depart::where('dcode', $restcode)->first();

                // load itemmast once
                $itemmast = ItemMast::where('Property_ID', $this->propertyid)
                    ->where('Code', $row->item)
                    ->where('RestCode', $restcode)
                    ->first();

                // prepare printing setup map for this restcode if not prepared
                if (!isset($psnoMap[$restcode])) {
                    // IMPORTANT: orderBy('id') used as default ordering. 
                    // If your PrintingSetup table has an explicit 'psno' column use orderBy('psno') instead.
                    $allSetups = PrintingSetup::where('propertyid', $this->propertyid)
                        ->where('module', 'KOT')
                        ->where('restcode', $restcode)
                        ->orderBy('sn')
                        ->get();

                    if ($allSetups->isEmpty()) {
                        return response()->json(['message' => 'Printer Path Not Found for restcode ' . $restcode], 500);
                    }

                    $psnoMap[$restcode] = [];
                    $idx = 1;
                    foreach ($allSetups as $setup) {
                        $psnoMap[$restcode][] = [
                            'setup'   => $setup,
                            'psno'    => $idx,
                            'kitchen' => $setup->kitchen
                        ];
                        $idx++;
                    }
                }

                // choose applicable setups:
                // first try setups that match itemmast->Kitchen, otherwise fallback to all setups for restcode
                $kitchenOfItem = $itemmast->Kitchen ?? null;
                $applicable = array_values(array_filter($psnoMap[$restcode], function ($s) use ($kitchenOfItem) {
                    return $kitchenOfItem !== null && ((string)$s['kitchen'] === (string)$kitchenOfItem);
                }));

                if (empty($applicable)) {
                    // fallback — print to all setups for that restcode (keeping their psno)
                    $applicable = $psnoMap[$restcode];
                }

                // insert a PrintDelay row for each applicable setup, using the setup's psno
                foreach ($applicable as $a) {
                    $setup = $a['setup'];
                    $psno = $a['psno'];
                    $printerpath = $setup->printerpath;
                    $kitchenToUse = $kitchenOfItem ?? $setup->kitchen;

                    $printdata = [
                        'propertyid'     => $this->propertyid,
                        'docid'          => $row->docid,
                        'restaurentname' => $depart->name ?? null,
                        'restcode'       => $restcode,
                        'printerpath'    => $printerpath,
                        'kitchen'        => $kitchenToUse,
                        'itemname'       => $itemmast->Name ?? $row->item,
                        'itemsn'         => $sn++,
                        'psno'           => $psno,
                        'itemprice'      => $row->rate,
                        'quantity'       => $row->qty,
                        'duplicate'      => $printedit,
                        'created_at'     => $this->currenttime
                    ];
                    PrintDelay::insert($printdata);
                }
            }

            return response()->json(['message' => 'Print Successfully, Please Check Your Printer']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unknown Error Occured: ' . $e->getMessage()], 500);
        }
    }

    return response()->json(['message' => 'No KOT found'], 404);
}

}
