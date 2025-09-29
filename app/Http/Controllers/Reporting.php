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

class Reporting extends Controller
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
      $paycharge = Paycharge::$encrypter->value;
   }

   public function showheader()
   {
      $username = Auth::user()->name;
      return view('property.layouts.header', ['data' => $username]);
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

   public function revokeopen($code)
   {
      $value = Menuhelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('code', $code)->first();
      return $value;
   }

   public function report_bulkcharge(Request $request)
   {
      $permission = revokeopen(141212);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      if ($this->revokeopen(141212)->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }

      $fromdate = $this->ncurdate;
      $bsource = DB::table('busssource')
         ->where('propertyid', $this->propertyid)
         ->orderBy('name', 'ASC')->get();
      $todate = date('Y-m-d', strtotime('-1 month', strtotime($this->ncurdate)));
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      $companysub = SubGroup::where('propertyid', $this->propertyid)->whereIn('comp_type', ['Corporate'])
         ->orderBy('name')->groupBy('sub_code')->get();
      $travelagents = SubGroup::Where('propertyid', $this->propertyid)->where('comp_type', 'Travel Agency')->orderBy('name', 'ASC')->get();
      $bussdata = BussSource::where('propertyid', $this->propertyid)->get();

      $uniqpay = Paycharge::where('propertyid', $this->propertyid)->groupBy('paytype')->get();

      $roundoff = 'ROFF' . $this->propertyid;
      $disc = 'DISC' . $this->propertyid;
      $revmast = Revmast::where('revmast.propertyid', $this->propertyid)
         ->where('field_type', 'C')
         ->where('Desk_code', '=', 'FOM' . $this->propertyid)
         ->whereNotIn('revmast.rev_code', [$roundoff, $disc])
         ->whereNot('seq_no', '0')
         ->distinct()
         ->orderBy('seq_no', 'ASC')
         ->get();

      return view('property.report_bulkcharge', [
         'fromdate' => $fromdate,
         'statename' => $statename,
         'company' => $company,
         'bsource' => $bsource,
         'revmast' => $revmast,
         'companysub' => $companysub,
         'travelagents' => $travelagents,
         'bussdata' => $bussdata,
         'uniqpay' => $uniqpay
      ]);
   }

   public function fetchdatabillprint(Request $request)
   {
      $docid = $request->input('docid');
      $sno1 = $request->input('sno1');
      $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
      if ($rocc) {
         $paychargedata = Paycharge::select('paycharge.*', 'revmast.field_type')->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('paycharge.propertyid', $this->propertyid)
            ->where('paycharge.folionodocid', $docid)
            ->whereNull('paycharge.modeset')->orderBy('paycharge.vdate', 'ASC')->orderBy('paycharge.vno', 'ASC')->orderBy('paycharge.sno', 'ASC')->get();
      } else {
         $paychargedata = Paycharge::select('paycharge.*', 'revmast.field_type')->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('paycharge.propertyid', $this->propertyid)
            ->where('paycharge.folionodocid', $docid)
            ->where('paycharge.sno1', $sno1)
            ->whereNull('paycharge.modeset')->orderBy('paycharge.vdate', 'ASC')->orderBy('paycharge.vno', 'ASC')->orderBy('paycharge.sno', 'ASC')->get();
      }

      return json_encode($paychargedata);
   }

   public function fetchbilldata(Request $request)
   {
      $billno = $request->input('billno');
      $guestname = $request->input('guestname');
      $vprefix = $request->vprefix;

      if ($guestname != '') {

         $fetchguestnameroomocc = Roomocc::where('propertyid', $this->propertyid)
            ->where('name', $guestname)
            ->where('vprefix', $vprefix)
            ->first();
         if (!$fetchguestnameroomocc) {
            return json_encode('Invalid');
         }

         if ($fetchguestnameroomocc->chkindate <=  $this->ncurdate && $fetchguestnameroomocc)

            if ($fetchguestnameroomocc->userchkoutdate == null) {
               return json_encode('Invalid');
            }

         $fechbillno = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $fetchguestnameroomocc->docid)
            ->where('vprefix', $vprefix)
            ->where('vtype', 'RC')->first();

         $billno = $fechbillno->billno;
      }

      $chkbilltrue = DB::table('paycharge')
         ->where('propertyid', $this->propertyid)
         ->where('billno', $billno)
         ->where('vprefix', $vprefix)
         ->whereNull('modeset')
         ->where('vtype', 'RC')
         ->limit(1)
         ->first();

      if (!$chkbilltrue) {
         return json_encode('Invalid');
      }

      $paychargedata = DB::table('paycharge')
         ->where('propertyid', $this->propertyid)
         ->where('vprefix', $vprefix)
         ->where('billno', $billno)
         ->whereNull('modeset')
         ->get();

      foreach ($paychargedata as $data) {
         $docid = $data->folionodocid;
         $sno1 = $data->sno1;
         $sno = $data->sno;
      }

      $paymode = Paycharge::select('paycharge.paycode', 'paycharge.comp_code', 'revmast.pay_type')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.vprefix', $vprefix)
         ->where('paycharge.propertyid', $this->propertyid)->where('paycharge.folionodocid', $docid)
         ->where('paycharge.modeset', 'S')->whereNot('paycharge.vtype', 'REV')->get();
      $paymodedata = [];
      foreach ($paymode as $row) {
         $pay_type = $row->pay_type;
         $paydata = null;
         if ($pay_type == 'Company') {
            $paydata = SubGroup::where('propertyid', $this->propertyid)
               ->where('sub_code', $row->comp_code)
               ->first();
         }

         $paymodedata[] = [
            'pay_type' => $pay_type,
            'paycompname' => ($paydata) ? $paydata->name : null
         ];
      }

      $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
      $rocc = Roomocc::where('propertyid', $this->propertyid)->where('vprefix', $vprefix)->where('docid', $docid)->where('leaderyn', 'Y')->first();
      if ($rocc) {
         $roomoccdata = RoomOcc::select(
            'roomocc.*',
            'cities.cityname',
            'states.name as statename',
            'room_cat.name as roomcategory',
            'company.name as companyname',
            'company.gstin as companygst',
            'travelagent.name as travelname',
            'travelagent.gstin as travelgst',
            'guestfolio.add1',
            'guestfolio.add2',
            'guestprof.guestsign'
         )
            ->leftJoin('guestprof', 'guestprof.docid', '=', 'roomocc.docid')
            ->leftJoin('cities', 'cities.city_code', '=', 'guestprof.city')
            ->leftJoin('states', 'states.state_code', '=', 'guestprof.state_code')
            ->leftJoin('room_cat', 'room_cat.cat_code', '=', 'roomocc.roomcat')
            ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('subgroup as company', 'company.sub_code', '=', 'guestfolio.company')
            ->leftJoin('subgroup as travelagent', 'travelagent.sub_code', '=', 'guestfolio.travelagent')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.docid', $docid)
            ->where('roomocc.vprefix', $vprefix)
            ->where(function ($query) {
               $query->whereNull('roomocc.type')
                  ->orWhere('roomocc.type', 'O');
            })
            ->first();
      } else {
         $roomoccdata = RoomOcc::select(
            'roomocc.*',
            'cities.cityname',
            'states.name as statename',
            'room_cat.name as roomcategory',
            'company.name as companyname',
            'company.gstin as companygst',
            'travelagent.name as travelname',
            'travelagent.gstin as travelgst',
            'guestfolio.add1',
            'guestfolio.add2',
            'guestprof.guestsign'
         )
            ->leftJoin('guestprof', 'guestprof.docid', '=', 'roomocc.docid')
            ->leftJoin('cities', 'cities.city_code', '=', 'guestprof.city')
            ->leftJoin('states', 'states.state_code', '=', 'guestprof.state_code')
            ->leftJoin('room_cat', 'room_cat.cat_code', '=', 'roomocc.roomcat')
            ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('subgroup as company', 'company.sub_code', '=', 'guestfolio.company')
            ->leftJoin('subgroup as travelagent', 'travelagent.sub_code', '=', 'guestfolio.travelagent')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.docid', $docid)
            ->where('roomocc.sno1', $sno1)
            ->where('roomocc.vprefix', $vprefix)
            ->where(function ($query) {
               $query->whereNull('roomocc.type')
                  ->orWhere('roomocc.type', 'O');
            })
            ->first();
      }

      $totaldebit = 0;
      $totalcredit = 0;

      foreach ($paychargedata as $data) {
         $totaldebit += $data->amtdr;
         $totalcredit += $data->amtcr;
      }
      $onamt = $paychargedata[0]->onamt;
      $billamt = str_replace(',', '', number_format($totaldebit - $totalcredit, 2));
      $divcode = DB::table('company')->where('propertyid', $this->propertyid)->value('division_code');
      $ranges = DateHelper::calculateDateRanges($this->ncurdate);
      if ($divcode == null) {
         $invoiceno = 'BCNT/' . $ranges['finyear']['current'] . '-' . substr($ranges['finyear']['nextyear'], 2) . '/' . $billno;
      } else {
         $invoiceno = $divcode . '/' . $ranges['finyear']['current'] . '-' . substr($ranges['finyear']['nextyear'], 2) . '/' . $billno;
      }
      $sumfieldc = DB::table('paycharge')
         ->join('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.folionodocid', $docid)
         ->where('revmast.field_type', 'C')
         ->where('paycharge.vtype', 'REV')
         ->where('paycharge.vprefix', $vprefix)
         ->sum('paycharge.amtdr');

      $sumtyperev = DB::table('paycharge')
         ->where('propertyid', $this->propertyid)
         ->where('folionodocid', $docid)
         ->where('paycharge.vprefix', $vprefix)
         ->where('sno', 1)
         ->where('sno1', $sno1)
         ->where('vtype', 'REV')
         ->sum('amtdr');

      $data = [
         'billno' => $billno,
         'paychargedata' => $paychargedata,
         'docid' => $docid,
         'sno1' => $sno1,
         'sumtyperev' => $sumtyperev,
         'companydata' => $companydata,
         'roomoccdata' => $roomoccdata,
         'billamt' => $billamt,
         'sumfieldc' => $sumfieldc,
         'onamt' => $onamt,
         'invoiceno' => $invoiceno,
         'paymodedata' => $paymodedata
      ];
      return json_encode($data);
   }

   public function fetchcompname(Request $request)
   {
      $settlemode = $request->input('settlemode');
      $settlefor = $request->input('settlefor');
      if ($settlefor == 'Company') {
         $data = SubGroup::Where('propertyid', $this->propertyid)->where('comp_type', 'Corporate')->orderBy('name', 'ASC')->get();
      } else if ($settlefor == 'Travel Agent') {
         $data = SubGroup::Where('propertyid', $this->propertyid)->where('comp_type', 'Travel Agency')->orderBy('name', 'ASC')->get();
      }
      return json_encode($data);
   }

   public function fetchpaydata(Request $request)
   {
      $cgst = 'CGSS' . $this->propertyid;
      $sgst = 'SGSS' . $this->propertyid;
      $roundoff = 'ROFF' . $this->propertyid;
      $disc = 'DISC' . $this->propertyid;
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      $allsettlement = $request->input('allsettlement');
      $allcompany = $request->input('allcompany');
      $alltravelagent = $request->input('alltravelagent');
      $allbusssource = $request->input('allbusssource');

      $settleyn = $request->input('settleyn');
      $companyyn = $request->input('companyyn');
      $travelyn = $request->input('travelyn');
      $bussyn = $request->input('bussyn');

      $seqrevcode = [];
      $revmast = Revmast::where('revmast.propertyid', $this->propertyid)
         ->where('field_type', 'C')
         ->where('Desk_code', '=', 'FOM' . $this->propertyid)
         ->whereNot('seq_no', '0')
         ->whereNotIn('revmast.rev_code', [$roundoff, $disc])
         ->distinct()
         ->orderBy('seq_no', 'ASC')
         ->get();

      $skipcode = [$roundoff, $disc];

      foreach ($revmast as $row) {
         $seqrevcode[] = $row->rev_code;
      }

      $selectFields = [
         'billno',
         DB::raw("SUM(CASE WHEN paycharge.sno IS NOT NULL THEN paycharge.amtdr ELSE 0 END) AS billamt"),
         DB::raw("SUM(CASE WHEN paycharge.sno = 1 THEN paycharge.amtdr ELSE 0 END) AS goods1"),
         DB::raw("SUM(CASE WHEN paycode = '{$cgst}' THEN amtdr - amtcr ELSE 0 END) AS cgstsum"),
         DB::raw("SUM(CASE WHEN paycode = '{$sgst}' THEN amtdr - amtcr ELSE 0 END) AS sgstsum"),
         DB::raw("SUM(CASE WHEN paycode = '{$roundoff}' THEN amtdr - amtcr ELSE 0 END) AS roundoff"),
         DB::raw("SUM(CASE WHEN paycode = '{$disc}' THEN amtcr ELSE 0 END) AS discount")
      ];

      $dynamicAliases = [];

      foreach ($seqrevcode as $code) {
         $alias = "sum_" . strtolower(substr($code, 0, 4));
         $selectFields[] = DB::raw("SUM(CASE WHEN paycode = '{$code}' THEN amtdr ELSE 0 END) AS {$alias}");
         $dynamicAliases[] = $alias;
      }

      $mainQuery = DB::table('paycharge')
         ->select([
            'paycharge.sno1',
            'paycharge.settledate',
            'paycharge.vprefix',
            'guestfolio.name as guestname',
            'guestfolio.vdate as checkindate',
            'roomocc.chkintime as checkintime',
            DB::raw("COALESCE(roomocc.chkoutdate, '') as chkoutdate"),
            'roomocc.chkouttime as chkouttime',
            'roomocc.docid as roomdocid',
            'roomocc.sno1 as rocc1',
            'guestfolio.busssource as bcode',
            'busssource.name as busssource',
            'guestprof.mobile_no as mobile_no',
            'guestfolio.company as compcode',
            'guestfolio.docid as folionodocid',
            'guestfolio.travelagent as travelcode',
            'paycharge.foliono',
            'paycharge.billno',
            'paycharge.docid',
            'paycharge.roomno',
            DB::raw('(roomocc.adult + roomocc.children) AS occ'),
            'roomocc.nodays as nights',
            'subcom.name as company',
            'subcom.gstin as compgstin',
            'travelcom.name as travelcompany',
            'travelcom.gstin as travelgstin',
            'booking.DocId as bookingdocid',
            'booking.BookNo AS bookno',
            'booking.RefBookNo AS refbookingid'
         ])
         ->leftJoin('roomocc', 'paycharge.folionodocid', '=', 'roomocc.docid')
         ->leftJoin('guestfolio', 'paycharge.folionodocid', '=', 'guestfolio.docid')
         ->leftJoin('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
         ->leftJoin('subgroup AS subcom', 'guestfolio.company', '=', 'subcom.sub_code')
         ->leftJoin('subgroup AS travelcom', 'guestfolio.travelagent', '=', 'travelcom.sub_code')
         ->leftJoin('busssource', 'busssource.bcode', '=', 'guestfolio.busssource')
         ->leftJoin('booking', 'booking.DocId', '=', 'guestfolio.bookingdocid')
         ->where('paycharge.propertyid', $this->propertyid)
         ->whereBetween('paycharge.settledate', [$fromdate, $todate])
         ->where('paycharge.roomtype', 'RO')
         ->where('paycharge.foliono', '!=', 0)
         ->where('paycharge.billno', '!=', 0)
         ->where('roomocc.type', 'O');

      if ($companyyn == 'Y') {
         $mainQuery->whereIn('guestfolio.company', explode(',', $allcompany));
      }

      if ($travelyn == 'Y') {
         $mainQuery->whereIn('guestfolio.travelagent', explode(',', $alltravelagent));
      }

      if ($bussyn == 'Y') {
         $mainQuery->whereIn('guestfolio.busssource', explode(',', $allbusssource));
      }

      if (strtolower($settleyn) == 'y') {
         $mainQuery->whereIn('paycharge.paytype', explode(',', strtolower($allsettlement)));
      }

      $mainQuery->groupBy('paycharge.billno')
         ->orderBy('paycharge.billno')
         ->orderBy('paycharge.settledate');

      $cgstQuery = DB::table('paycharge')
         ->select($selectFields)
         ->where('propertyid', $this->propertyid)
         ->whereBetween('settledate', [$fromdate, $todate])
         ->groupBy('billno');

      $resultQuery = DB::table(DB::raw("({$mainQuery->toSql()}) AS main_query"))
         ->mergeBindings($mainQuery)
         ->leftJoin(DB::raw("({$cgstQuery->toSql()}) AS cgst"), 'main_query.billno', '=', 'cgst.billno')
         ->mergeBindings($cgstQuery)
         ->select([
            'main_query.sno1',
            'main_query.rocc1',
            'main_query.settledate',
            'main_query.guestname',
            'main_query.checkindate',
            'main_query.checkintime',
            'main_query.roomdocid',
            'main_query.chkoutdate',
            'main_query.chkouttime',
            'main_query.mobile_no',
            'main_query.foliono',
            'main_query.billno',
            'main_query.folionodocid',
            'main_query.bookingdocid',
            'main_query.roomno',
            'main_query.occ',
            'main_query.nights',
            'main_query.vprefix',
            DB::raw('IFNULL(cgst.goods1, 0) AS goods1'),
            DB::raw('IFNULL(cgst.cgstsum, 0) AS cgstsum'),
            DB::raw('IFNULL(cgst.sgstsum, 0) AS sgstsum'),
            DB::raw('IFNULL(cgst.roundoff, 0) AS roundoff'),
            DB::raw('IFNULL(cgst.discount, 0) AS discount'),
            DB::raw('(IFNULL(cgst.cgstsum, 0) + IFNULL(cgst.sgstsum, 0)) AS total_tax'),
            DB::raw('IFNULL(cgst.billamt, 0) AS billamt'),
            'main_query.company',
            'main_query.compgstin',
            'main_query.travelcompany',
            'main_query.travelgstin',
            'main_query.compcode',
            'main_query.travelcode',
            'main_query.bookno',
            'main_query.refbookingid',
            'main_query.busssource',
            'main_query.bcode'
         ]);

      foreach ($dynamicAliases as $alias) {
         $resultQuery = $resultQuery->addSelect(DB::raw("IFNULL(cgst.{$alias}, 0) AS {$alias}"));
      }

      $resulttmp = $resultQuery->get();

      if ($resulttmp->isEmpty()) {
         return json_encode([
            'skipcode' => $skipcode,
            'report' => [],
            'revmast' => $revmast,
            'resultQuery' => []
         ]);
      }

      $roomDocIds = $resulttmp->pluck('roomdocid')->filter()->unique()->values()->toArray();

      $bulkPaymentQuery = DB::table('paycharge')
         ->leftJoin('revmast', function ($join) {
            $join->on('revmast.rev_code', '=', 'paycharge.paycode')
               ->where('revmast.field_type', '=', 'P');
         })
         ->whereIn('paycharge.folionodocid', $roomDocIds)
         ->where('modeset', 'S')
         ->where('paycharge.paycode', '!=', 'ROFF' . $this->propertyid)
         ->select([
            'paycharge.folionodocid',
            'paycharge.paytype',
            DB::raw('SUM(paycharge.amtcr) AS totalamt')
         ])
         ->groupBy('paycharge.folionodocid', 'paycharge.paytype')
         ->havingRaw('SUM(paycharge.amtcr) > 0');


      if (strtolower($settleyn) == 'y') {
         $bulkPaymentQuery->whereIn('paycharge.paytype', explode(',', strtolower($allsettlement)));
      }

      $bulkPaymentData = $bulkPaymentQuery->get()->groupBy('folionodocid');

      $bulkAdvanceData = Paycharge::whereIn('paycharge.folionodocid', $roomDocIds)
         ->where('paycharge.propertyid', $this->propertyid)
         ->whereIn('vtype', ['REC', 'CHK'])
         ->whereNull('modeset')
         ->select([
            'folionodocid',
            'sno1',
            DB::raw('SUM(paycharge.amtcr) AS advance_sum')
         ])
         ->groupBy('folionodocid', 'sno1')
         ->get()
         ->keyBy(function ($item) {
            return $item->folionodocid . '_' . $item->sno1;
         });

      $result = [];

      foreach ($resulttmp as $row) {
         $paymentDataForRoom = $bulkPaymentData->get($row->roomdocid, collect());

         $paytypeStr = $paymentDataForRoom->pluck('paytype')->implode(', ');
         $paymentStr = $paymentDataForRoom->pluck('totalamt')->implode(', ');

         if (strtolower($settleyn) == 'y' && empty(trim($paytypeStr))) {
            continue;
         }

         $advanceKey = $row->roomdocid . '_' . $row->rocc1;
         $advancesum = $bulkAdvanceData->get($advanceKey)->advance_sum ?? 0;

         $row->paytype = $paytypeStr;
         $row->payment = $paymentStr;
         $row->advance = $advancesum;

         $result[] = $row;
      }

      $data = [
         'skipcode' => $skipcode,
         'report' => $result,
         'revmast' => $revmast,
         'resultQuery' => $resultQuery->get()
      ];

      return json_encode($data);
   }

   public function billreprintsubmit(Request $request)
   {
      $validate = $request->validate([
         'billno' => 'required',
         'docid' => 'required',
         'folionodocid' => 'required',
         'sno1' => 'required',
      ]);

      $sno1 = $request->input('sno1');
      $folionodocid = $request->input('folionodocid');
      $count = $request->input('rowcount');
      $totalbalance = 0.00;
      $totalroomcharge = 0.00;
      $billprintingsummerised = $request->input('billprintingsummerised');
      $taxsummary = $request->input('taxsummary');
      $invoiceno = $request->input('invoiceno');

      for ($i = 1; $i <= $count; $i++) {
         $roomcharge = $request->input('room_charge_' . $i);
         $paydocid = $request->input('paydocid' . $i);
         $paysno = $request->input('paysno' . $i);
         $paysnoone = $request->input('paysnoone' . $i);
         if ($roomcharge !== null) {
            $updata = [
               'amtdr' => $request->input('room_charge_' . $i),
               'onamt' => $request->input('payonamt' . $i),
               'billamount' => $request->input('paybillamt' . $i),
               'u_updatedt' => $this->currenttime,
            ];

            Paycharge::where('propertyid', $this->propertyid)->where('docid', $paydocid)->where('sno', $paysno)
               ->where('sno1', $paysnoone)->update($updata);
         }
      }

      $company = Companyreg::where('propertyid', $this->propertyid)->where('role', 'Property')->first();

      $guest = Roomocc::select('roomocc.*', 'guestprof.mobile_no', 'guestprof.guestsign')
         ->leftJoin('guestprof', function ($join) {
            $join->on('guestprof.docid', '=', 'roomocc.docid');
         })
         ->where('roomocc.propertyid', $this->propertyid)
         ->where('roomocc.docid', $folionodocid)
         ->where('roomocc.sno1', $sno1)
         ->first();

      // $paycharger = Paycharge::where('propertyid', $this->propertyid)->where('docid', $paydocid)->where('sno', $paysno)
      //    ->where('sno1', $paysnoone)->first();

      $paycharger = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $folionodocid)
         ->where('sno1', $sno1)->whereNot('billno', '0')->first();

      $chargedt = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $folionodocid)
         ->where('sno1', $sno1)->get();

      $paycode = ['RMCH' . $this->propertyid, 'MEGE' . $this->propertyid];
      foreach ($chargedt as $row) {
         $totalbalance += $row->amtdr;
      }

      $enviro = EnviroFom::where('propertyid', $this->propertyid)->first();
      $paycode = ['RMCH' . $this->propertyid, 'MEGE' . $this->propertyid];

      $igncode = [];
      $settlecodes = [];
      $revmasttax = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'T')->where('type', 'Cr')->get();
      $revmastpay = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->where('type', 'Dr')->get();

      foreach ($revmasttax as $row) {
         $igncode[] = $row->rev_code;
      }

      foreach ($revmastpay as $row) {
         $settlecodes[] = $row->rev_code;
      }

      $charged = [];
      $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $folionodocid)->where('leaderyn', 'Y')->first();
      if ($rocc) {
         $cond = ['paycharge.msno1' => $rocc->sno1];
      } else {
         $cond = ['paycharge.sno1' => $sno1];
      }
      if ($enviro->billprintingsummerised == 'Y') {
         $charged1 = Paycharge::select(
            'paycharge.vdate',
            'paycharge.vtype',
            'paycharge.vno',
            'paycharge.comments',
            'paycharge.roomno',
            DB::raw("SUM(paycharge.amtdr) as amtdr"),
            DB::raw("SUM(paycharge.amtcr) as amtcr"),
            'plan_mast.name as plankanaam',
            'paycharge.split',
            'paycharge.paycode'
         )
            ->leftJoin('roomocc', function ($join) {
               $join->on('roomocc.docid', '=', 'paycharge.folionodocid')
                  ->on('roomocc.sno1', '=', 'paycharge.sno1')
                  ->where('roomocc.type', 'O')
                  ->where('roomocc.propertyid', $this->propertyid);
            })
            ->leftJoin('plan_mast', function ($join) {
               $join->on('roomocc.plancode', '=', 'plan_mast.pcode')
                  ->where('plan_mast.propertyid', $this->propertyid);
            })
            ->where('paycharge.propertyid', $this->propertyid)
            ->where('paycharge.folionodocid', $folionodocid)
            ->whereNull('paycharge.modeset')
            ->where($cond)
            ->whereIn('paycharge.paycode', $paycode)
            ->groupBy('paycharge.roomno', 'paycharge.vdate')
            ->orderBy('paycharge.vdate', 'ASC')
            ->orderBy('paycharge.roomno', 'ASC')
            ->get();

         foreach ($charged1 as $row) {
            $totalroomcharge += $row->amtdr;
            $charged[] = [
               'vdate' => $row->vdate,
               'vtype' => $row->vtype,
               'vno' => $row->vno,
               'comments' => $row->plankanaam . ' For Room ' . $row->roomno,
               'amtdr' => $row->amtdr,
               'amtcr' => $row->amtcr,
               'split' => $row->split,
               'paycode' => $row->paycode
            ];
         }

         $charged2 = Paycharge::select(
            'vdate',
            'vtype',
            'vno',
            'comments',
            'amtdr',
            'amtcr',
            'split',
            'paycode'
         )
            ->where('propertyid', $this->propertyid)
            ->where('folionodocid', $folionodocid)
            ->where($cond)
            ->whereNotIn('paycharge.paycode', $paycode)
            ->whereNot('paycharge.paycode', 'ROFF' . $this->propertyid)
            ->whereNull('paycharge.modeset')
            ->whereNotIn('paycharge.paycode', $igncode)
            ->orderBy('paycharge.vdate', 'ASC')
            ->orderBy('paycharge.roomno', 'ASC')
            ->get();

         foreach ($charged2 as $row2) {
            $totalroomcharge += $row2->amtdr;
            $charged[] = [
               'vdate' => $row2->vdate,
               'vtype' => $row2->vtype,
               'vno' => $row2->vno,
               'comments' => $row2->comments,
               'amtdr' => $row2->amtdr,
               'amtcr' => $row2->amtcr,
               'split' => $row2->split,
               'paycode' => $row2->paycode
            ];
         }
      } else {
         $charged = Paycharge::select(
            'vdate',
            'vtype',
            'vno',
            'comments',
            'amtdr',
            'amtcr',
            'split',
            'paycode'
         )
            ->where('propertyid', $this->propertyid)
            ->where('folionodocid', $folionodocid)
            ->whereNot('paycode', 'ROFF' . $this->propertyid)
            ->whereNull('paycharge.modeset')
            ->where($cond)
            ->orderBy('paycharge.vdate', 'ASC')
            ->orderBy('paycharge.roomno', 'ASC')
            ->get();

         $totalroomcharge = $charged->sum('amtdr');
      }

      return response()->json([
         'company' => $company,
         'guest' => $guest,
         'paycharger' => $paycharger,
         'totalbalance' => $totalbalance,
         'totalroomcharge' => $totalroomcharge,
         'billprintingsummerised' => $billprintingsummerised,
         'charged' => $charged,
         'taxsummary' => $taxsummary,
         'invoiceno' => $invoiceno,
         'igncode' => $igncode
      ]);
   }

   public function checkinreg(Request $request)
   {
      $permission = revokeopen(141211);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;

      $data = DB::table('guestfolio')->where('propertyid', $this->propertyid)->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.checkinreg', [
         'data' => $data,
         'fromdate' => $fromdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }

   public function fetchcheckinregdata(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      // $checkval = Companyreg::where('propertyid', $this->propertyid)->first();
      // if ($fromdate < $checkval->start_dt) {
      //    return json_encode('1');
      // } else if ($todate > $checkval->end_dt) {
      //    return json_encode('2');
      // }
      $guestfolioData = Guestfolio::select(
         'guestfolio.Docid AS FolionoDocid',
         'guestfolio.folio_no',
         DB::raw('CASE WHEN guestfolio.mFoliono = 0 THEN guestfolio.folio_no ELSE guestfolio.mFoliono END AS FolioNo'),
         'guestprof.Name',
         'guestfolio.add1',
         'guestfolio.add2',
         'city_live.cityname AS city',
         'guestprof.nationality',
         'guestprof.mobile_no',
         'roomocc.RoomNo',
         DB::raw('IFNULL(roomocc.adult + roomocc.children, 0) AS TotalGuest'),
         'roomocc.RoomRate',
         'roomocc.planamt',
         'roomocc.ChkinDate',
         'roomocc.ChkinTime',
         'roomocc.chkoutdate',
         'roomocc.chkouttime',
         'city_from.cityname AS arrfrom',
         'city_to.cityname AS destination',
         'guestfolio.PurVisit',
         DB::raw('(SELECT SUM(paycharge.amtcr) FROM paycharge WHERE paycharge.folionodocid = guestfolio.Docid AND paycharge.modeset != "S") AS advance'),
         'guestfolio.U_Name',
         'subgroup.name AS travelagent'
      )
         ->join('roomocc', 'roomocc.Docid', '=', 'guestfolio.Docid')
         ->join('guestprof', 'guestprof.guestcode', '=', 'guestfolio.guestprof')
         ->leftJoin('countries', 'guestprof.Nationality', '=', 'countries.country_code')
         ->leftJoin('cities AS city_live', 'city_live.city_code', '=', 'guestfolio.city')
         ->leftJoin('cities AS city_from', 'city_from.city_code', '=', 'guestfolio.arrfrom')
         ->leftJoin('cities AS city_to', 'city_to.city_code', '=', 'guestfolio.destination')
         ->leftjoin('subgroup', 'subgroup.sub_code', '=', 'guestfolio.travelagent')
         ->whereBetween('guestfolio.vdate', [$fromdate, $todate])
         ->where('roomocc.Sno', 1)
         ->where('guestfolio.propertyid', $this->propertyid)
         ->groupBy('roomocc.docid')
         ->orderBy('roomocc.foliono', 'DESC')
         ->get();

      return json_encode($guestfolioData);
   }

   public function cashierreport(Request $request)
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
      return view('property.cashierreport', [
         'fromdate' => $fromdate,
         'statename' => $statename,
         'distinctuname' => $distinctuname,
         'company' => $company,
         'revheading' => $revheading
      ]);
   }

   public function fetchusersname(Request $request)
   {
      $distinctuname = Paycharge::where('propertyid', $this->propertyid)->where('modeset', 'S')->distinct('u_name')->get(['u_name']);
      return json_encode($distinctuname);
   }

   public function fetchcashierreportdata(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');
      // Making array of revheading names
      $revheadingArray = [];
      $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
      foreach ($revheading as $row) {
         $revheadingArray[] = $row->pay_type;
      }

      $firstQuery = DB::table('paycharge as PC')
         ->leftJoin('revmast as PY', 'PC.PayCode', '=', 'PY.rev_code')
         ->leftJoin('roomocc as RO', 'PC.FOLIONODOCID', '=', 'RO.DOCID')
         ->select(
            'PC.DOCID',
            'PC.SNO',
            'PC.SNO1',
            DB::raw('MAX(PC.FOLIONO) AS FOLIONO'),
            'PC.FOLIONODOCID',
            'RO.roomno',
            'RO.name as GUESTNAME',
            DB::raw('MAX(PC.VDATE) AS VDATE'),
            DB::raw('MAX(PC.VTYPE) AS VTYPE'),
            DB::raw('MAX(PC.VNO) AS VNO'),
            DB::raw('PC.AmtCr - PC.AmtDr AS NetSale'),
            DB::raw('SUM(PC.TipAmt) AS TipAmt1'),
            DB::raw('MAX(PC.PAYCODE) AS PAYCODE'),
            DB::raw('MAX(PC.PayType) AS PType'),
            DB::raw('MAX(PC.U_NAME) AS UNAME'),
            DB::raw('MAX(PC.Comments) AS COMMENT'),
            DB::raw("'PAYMENT RECD.' AS DEPARTNAME"),
            DB::raw('1 AS AA')
         )
         ->where(function ($query) {
            $query->where(function ($query) {
               $query->whereIn('PC.VTYPE', ['ARRES', 'ADRES'])
                  ->where('PC.DbtChkIn', '<>', 'Yes');
            })
               ->orWhere(function ($query) {
                  $query->whereNotIn('PC.VTYPE', ['ARRES', 'ADRES'])
                     ->where(function ($query) {
                        $query->whereNull('PC.refdocid')
                           ->orWhere('PC.refdocid', '=', '');
                     });
               });
         })
         ->where('PC.RESTCODE', 'FOM' . $this->propertyid)
         ->whereIn('PY.field_type', ['P'])
         ->whereNotIn('PC.VTYPE', ['CHK'])
         ->whereBetween('PC.VDate', [$fromdate, $todate])
         ->where('PC.propertyid', $this->propertyid)
         ->whereIn('PC.PAYTYPE', $revheadingArray)
         ->groupBy('PC.DOCID', 'PC.SNO', 'PC.SNO1')
         ->havingRaw('SUM(PC.AmtCr) - SUM(PC.AmtDr) > 0');

      $secondQuery = DB::table('paycharge as PC')
         ->leftJoin('revmast as PY', 'PC.PayCode', '=', 'PY.rev_code')
         ->leftJoin('roomocc as RO', 'PC.FOLIONODOCID', '=', 'RO.DOCID')
         ->select(
            'PC.DOCID',
            'PC.SNO',
            'PC.SNO1',
            DB::raw('MAX(PC.FOLIONO) AS FOLIONO'),
            'PC.FOLIONODOCID',
            'RO.roomno',
            'RO.name as GUESTNAME',
            DB::raw('MAX(PC.VDATE) AS VDATE'),
            DB::raw('MAX(PC.VTYPE) AS VTYPE'),
            DB::raw('MAX(PC.VNO) AS VNO'),
            DB::raw('PC.AmtCr - PC.AmtDr AS NetSale'),
            DB::raw('SUM(PC.TipAmt) AS TipAmt1'),
            DB::raw('MAX(PC.PAYCODE) AS PAYCODE'),
            DB::raw('MAX(PC.PayType) AS PType'),
            DB::raw('MAX(PC.U_NAME) AS UNAME'),
            DB::raw('MAX(PC.Comments) AS COMMENT'),
            DB::raw("'PAYMENT MADE' AS DEPARTNAME"),
            DB::raw('2 AS AA')
         )
         ->where(function ($query) {
            $query->where(function ($query) {
               $query->whereIn('PC.VTYPE', ['ARRES', 'ADRES'])
                  ->where('PC.DbtChkIn', '<>', 'Yes');
            })
               ->orWhere(function ($query) {
                  $query->whereNotIn('PC.VTYPE', ['ARRES', 'ADRES'])
                     ->where(function ($query) {
                        $query->whereNull('PC.refdocid')
                           ->orWhere('PC.refdocid', '=', '');
                     });
               });
         })
         ->where('PC.RESTCODE', 'FOM' . $this->propertyid)
         ->whereIn('PY.field_type', ['P'])
         ->whereNotIn('PC.VTYPE', ['CHK'])
         ->whereBetween('PC.VDate', [$fromdate, $todate])
         ->where('PC.propertyid', $this->propertyid)
         ->whereIn('PC.PAYTYPE', $revheadingArray)
         ->groupBy('PC.DOCID', 'PC.SNO', 'PC.SNO1')
         ->havingRaw('SUM(PC.AmtCr) - SUM(PC.AmtDr) < 0');

      $thirdQuery = DB::table('expsheet as E')
         ->select(
            'E.docid',
            DB::raw('1 AS SNO'),
            DB::raw('2 AS SNO1'),
            DB::raw('"" AS FOLIONO'),
            DB::raw('"" AS FOLIONODOCID'),
            DB::raw('"" AS roomno'),
            DB::raw('"" AS GUESTNAME'),
            'E.vdate',
            'E.vtype',
            'E.vno',
            DB::raw('E.cramt AS NetSale'),
            DB::raw('0 AS TipAmt1'),
            DB::raw('"" AS PAYCODE'),
            DB::raw("'Cash' AS PType"),
            'E.u_name AS UNAME',
            'E.remark AS COMMENT',
            DB::raw("'MISC.PAYMENT' AS DEPARTNAME"),
            DB::raw('3 AS AA')
         )
         ->whereBetween('E.vdate', [$fromdate, $todate])
         ->where('E.vtype', 'HTEXP')
         ->where('E.propertyid', $this->propertyid)
         ->where('E.cramt', '>', 0);

      $fourthQuery = DB::table('expsheet as E')
         ->select(
            'E.docid',
            DB::raw('1 AS SNO'),
            DB::raw('2 AS SNO1'),
            DB::raw('"" AS FOLIONO'),
            DB::raw('"" AS FOLIONODOCID'),
            DB::raw('"" AS roomno'),
            DB::raw('"" AS GUESTNAME'),
            'E.vdate',
            'E.vtype',
            'E.vno',
            'E.cramt AS NetSale',
            DB::raw('0 AS TipAmt1'),
            DB::raw('"" AS PAYCODE'),
            DB::raw("'Cash' AS PType"),
            'E.u_name AS UNAME',
            'E.remark AS COMMENT',
            DB::raw("'MISC.RECEIPT' AS DEPARTNAME"),
            DB::raw('4 AS AA')
         )
         ->whereBetween('E.VDate', [$fromdate, $todate])
         ->where('E.vtype', 'HTSAL')
         ->where('E.propertyid', $this->propertyid)
         ->where('E.cramt', '>', 0);

      $results = $firstQuery->unionAll($secondQuery)
         ->unionAll($thirdQuery)
         ->unionAll($fourthQuery)
         ->orderBy('AA')
         ->orderBy('foliono')
         ->orderBy('VDATE')
         ->orderBy('DOCID')
         ->orderBy('SNO')
         ->get();

      $billnos = Paycharge::where('propertyid', $this->propertyid)
         ->where('billno', '!=', 0)
         ->whereNull('paytype')
         ->get()
         ->keyBy(function ($item) {
            return $item->folionodocid . '_' . $item->sno1;
         });

      $roomnos = RoomOcc::where('propertyid', $this->propertyid)
         ->whereNotNull('type')
         ->get()
         ->keyBy(function ($item) {
            return $item->docid . '_' . $item->sno1;
         });

      foreach ($results as $row) {
         $key = $row->FOLIONODOCID . '_' . $row->SNO1;
         if (isset($roomnos[$key])) {
            $row->roomno = $roomnos[$key]->roomno;
         }
      }

      foreach ($results as $row) {
         $key = $row->FOLIONODOCID . '_' . $row->SNO1;
         if (isset($billnos[$key])) {
            $row->billno = $billnos[$key]->billno;
         } else {
            $row->billno = 'Not Found';
         }
      }

      $paytype = [];
      $distinctpaytypes = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
      foreach ($distinctpaytypes as $row) {
         $paytype[] = $row->pay_type;
      }
      $data = [
         'cashierdata' => $results,
         'paytype' => $paytype,
      ];

      return json_encode($data);
   }

   public function fetchcashierreportdata2(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');
      $usernames = json_decode($request->input('unames'));
      // Making array of revheading names
      $revheadingArray = [];
      $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
      foreach ($revheading as $row) {
         $revheadingArray[] = $row->pay_type;
      }

      $propertyId = $this->propertyid;

      $firstQuery = DB::table('paycharge as PC')
         ->leftJoin('revmast as PY', 'PC.PayCode', '=', 'PY.rev_code')
         ->leftJoin('roomocc as RO', 'PC.FOLIONODOCID', '=', 'RO.DOCID')
         ->select(
            'PC.DOCID',
            'PC.SNO',
            'PC.SNO1',
            DB::raw('MAX(PC.FOLIONO) AS FOLIONO'),
            'PC.FOLIONODOCID',
            'RO.roomno',
            'RO.name as GUESTNAME',
            DB::raw('MAX(PC.VDATE) AS VDATE'),
            DB::raw('MAX(PC.VTYPE) AS VTYPE'),
            DB::raw('MAX(PC.VNO) AS VNO'),
            DB::raw('PC.AmtCr - PC.AmtDr AS NetSale'),
            DB::raw('SUM(PC.TipAmt) AS TipAmt1'),
            DB::raw('MAX(PC.PAYCODE) AS PAYCODE'),
            DB::raw('MAX(PC.PayType) AS PType'),
            DB::raw('MAX(PC.U_NAME) AS UNAME'),
            DB::raw('MAX(PC.Comments) AS COMMENT'),
            DB::raw("'PAYMENT RECD.' AS DEPARTNAME"),
            DB::raw('1 AS AA')
         )
         ->where(function ($query) {
            $query->where(function ($query) {
               $query->whereIn('PC.VTYPE', ['ARRES', 'ADRES'])
                  ->where('PC.DbtChkIn', '<>', 'Yes');
            })
               ->orWhere(function ($query) {
                  $query->whereNotIn('PC.VTYPE', ['ARRES', 'ADRES'])
                     ->where(function ($query) {
                        $query->whereNull('PC.refdocid')
                           ->orWhere('PC.refdocid', '=', '');
                     });
               });
         })
         ->where('PC.RESTCODE', 'FOM' . $this->propertyid)
         ->whereIn('PY.field_type', ['P'])
         ->whereIn('PC.u_name', $usernames)
         ->whereNotIn('PC.VTYPE', ['CHK'])
         ->whereBetween('PC.VDate', [$fromdate, $todate])
         ->where('PC.propertyid', $this->propertyid)
         ->whereIn('PC.PAYTYPE', $revheadingArray)
         ->groupBy('PC.DOCID', 'PC.SNO', 'PC.SNO1')
         ->havingRaw('SUM(PC.AmtCr) - SUM(PC.AmtDr) > 0');

      $secondQuery = DB::table('paycharge as PC')
         ->leftJoin('revmast as PY', 'PC.PayCode', '=', 'PY.rev_code')
         ->leftJoin('roomocc as RO', 'PC.FOLIONODOCID', '=', 'RO.DOCID')
         ->select(
            'PC.DOCID',
            'PC.SNO',
            'PC.SNO1',
            DB::raw('MAX(PC.FOLIONO) AS FOLIONO'),
            'PC.FOLIONODOCID',
            'RO.roomno',
            'RO.name as GUESTNAME',
            DB::raw('MAX(PC.VDATE) AS VDATE'),
            DB::raw('MAX(PC.VTYPE) AS VTYPE'),
            DB::raw('MAX(PC.VNO) AS VNO'),
            DB::raw('PC.AmtCr - PC.AmtDr AS NetSale'),
            DB::raw('SUM(PC.TipAmt) AS TipAmt1'),
            DB::raw('MAX(PC.PAYCODE) AS PAYCODE'),
            DB::raw('MAX(PC.PayType) AS PType'),
            DB::raw('MAX(PC.U_NAME) AS UNAME'),
            DB::raw('MAX(PC.Comments) AS COMMENT'),
            DB::raw("'PAYMENT MADE' AS DEPARTNAME"),
            DB::raw('2 AS AA')
         )
         ->where(function ($query) {
            $query->where(function ($query) {
               $query->whereIn('PC.VTYPE', ['ARRES', 'ADRES'])
                  ->where('PC.DbtChkIn', '<>', 'Yes');
            })
               ->orWhere(function ($query) {
                  $query->whereNotIn('PC.VTYPE', ['ARRES', 'ADRES'])
                     ->where(function ($query) {
                        $query->whereNull('PC.refdocid')
                           ->orWhere('PC.refdocid', '=', '');
                     });
               });
         })
         ->where('PC.RESTCODE', 'FOM' . $this->propertyid)
         ->whereIn('PY.field_type', ['P'])
         ->whereIn('PC.u_name', $usernames)
         ->whereNotIn('PC.VTYPE', ['CHK'])
         ->whereBetween('PC.VDate', [$fromdate, $todate])
         ->where('PC.propertyid', $this->propertyid)
         ->whereIn('PC.PAYTYPE', $revheadingArray)
         ->groupBy('PC.DOCID', 'PC.SNO', 'PC.SNO1')
         ->havingRaw('SUM(PC.AmtCr) - SUM(PC.AmtDr) < 0');

      $thirdQuery = DB::table('expsheet as E')
         ->select(
            'E.DOCID',
            DB::raw('1 AS SNO'),
            DB::raw('2 AS SNO1'),
            DB::raw('"" AS FOLIONO'),
            DB::raw('"" AS FOLIONODOCID'),
            DB::raw('"" AS roomno'),
            DB::raw('"" AS GUESTNAME'),
            'E.VDATE',
            'E.VTYPE',
            'E.VNO',
            DB::raw('-E.Amount AS NetSale'),
            DB::raw('0 AS TipAmt1'),
            DB::raw('"" AS PAYCODE'),
            DB::raw("'Cash' AS PType"),
            'E.U_NAME AS UNAME',
            'E.Remarks AS COMMENT',
            DB::raw("'MISC.PAYMENT' AS DEPARTNAME"),
            DB::raw('3 AS AA')
         )
         ->whereBetween('E.VDate', [$fromdate, $todate])
         ->where('E.VTYPE', 'HTEXP')
         ->where('E.Amount', '>', 0);

      $fourthQuery = DB::table('expsheet as E')
         ->select(
            'E.DOCID',
            DB::raw('1 AS SNO'),
            DB::raw('2 AS SNO1'),
            DB::raw('"" AS FOLIONO'),
            DB::raw('"" AS FOLIONODOCID'),
            DB::raw('"" AS roomno'),
            DB::raw('"" AS GUESTNAME'),
            'E.VDATE',
            'E.VTYPE',
            'E.VNO',
            'E.Amount AS NetSale',
            DB::raw('0 AS TipAmt1'),
            DB::raw('"" AS PAYCODE'),
            DB::raw("'Cash' AS PType"),
            'E.U_NAME AS UNAME',
            'E.Remarks AS COMMENT',
            DB::raw("'MISC.RECEIPT' AS DEPARTNAME"),
            DB::raw('4 AS AA')
         )
         ->whereBetween('E.VDate', [$fromdate, $todate])
         ->where('E.VTYPE', 'HTSAL')
         ->where('E.Amount', '>', 0);

      $results = $firstQuery->unionAll($secondQuery)
         ->unionAll($thirdQuery)
         ->unionAll($fourthQuery)
         ->orderBy('AA')
         ->orderBy('DEPARTNAME')
         ->orderBy('VDATE')
         ->orderBy('DOCID')
         ->orderBy('SNO')
         ->get();

      $billnos = Paycharge::where('propertyid', $this->propertyid)
         ->where('billno', '!=', 0)
         ->whereNull('paytype')
         ->get()
         ->keyBy(function ($item) {
            return $item->folionodocid . '_' . $item->sno1;
         });

      foreach ($results as $row) {
         $key = $row->FOLIONODOCID . '_' . $row->SNO1;
         if (isset($billnos[$key])) {
            $row->billno = $billnos[$key]->billno;
         } else {
            $row->billno = 'Not Found';
         }
      }

      $paytype = [];
      $distinctpaytypes = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
      foreach ($distinctpaytypes as $row) {
         $paytype[] = $row->pay_type;
      }
      $data = [
         'cashierdata' => $results,
         'paytype' => $paytype,
      ];

      return json_encode($data);
   }

   public function cancelbills(Request $request)
   {
      $permission = revokeopen(141214);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.cancelbills', [
         'fromdate' => $fromdate,
         'statename' => $statename,
         'company' => $company
      ]);
   }

   public function fetchcancelbilldata(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      $data = FomBillDetail::where('propertyid', $this->propertyid)->Where('status', 'Cancel')->whereBetween('billdate', [$fromdate, $todate])->orderBy('billdate')->orderBy('billno')->orderBy('u_name')->get();
      return json_encode($data);
   }

   public function fetchbussource(Request $request)
   {
      $bussdata = BussSource::where('propertyid', $this->propertyid)->get();
      return json_encode($bussdata);
   }

   public function fetchroomresettle(Request $request)
   {
      $billno = $request->input('billno');
      $vprefix = $request->vprefix;

      $chkbilltrue = Paycharge::where('propertyid', $this->propertyid)
         ->where('billno', $billno)
         ->where('vprefix', $vprefix)
         ->first();

      if (!$chkbilltrue) {
         return json_encode('Invalid');
      }

      $paychargedata = DB::table('paycharge')
         ->where('propertyid', $this->propertyid)
         ->where('vprefix', $vprefix)
         ->where('billno', $billno)
         ->get();

      foreach ($paychargedata as $data) {
         $docid = $data->folionodocid;
         $sno1 = $data->sno1;
         $sno = $data->sno;
         $msno1 = $data->msno1;
      }

      $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();

      $paymodeQuery = Paycharge::select('paycharge.paycode', 'paycharge.vdate', 'paycharge.comp_code', 'revmast.pay_type')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('paycharge.folionodocid', $docid)
         ->where('paycharge.vprefix', $vprefix)
         ->where('paycharge.modeset', 'S')
         ->whereNot('paycharge.vtype', 'REV');

      if ($rocc) {
         $paymodeQuery->where('msno1', $rocc->sno1);
      } else {
         $paymodeQuery->where('sno1', $sno1);
      }

      $paymode = $paymodeQuery->get();

      $paymodedata = [];
      foreach ($paymode as $row) {
         $pay_type = $row->pay_type;
         $paydate = $row->vdate;
         $paydata = null;
         if ($pay_type == 'Company') {
            $paydata = SubGroup::where('propertyid', $this->propertyid)
               ->where('sub_code', $row->comp_code)
               ->first();
         }

         $paymodedata[] = [
            'pay_type' => $pay_type,
            'paydate' => $paydate,
            'paycompname' => ($paydata) ? $paydata->name : null
         ];
      }

      $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
      $roomoccdata = RoomOcc::select(
         'roomocc.*',
         'cities.cityname',
         'states.name as statename',
         'room_cat.name as roomcategory',
         'company.name as companyname',
         'company.gstin as companygst',
         'travelagent.name as travelname',
         'travelagent.gstin as travelgst'
      )
         ->leftJoin('guestprof', 'guestprof.docid', '=', 'roomocc.docid')
         ->leftJoin('cities', 'cities.city_code', '=', 'guestprof.city')
         ->leftJoin('states', 'states.state_code', '=', 'guestprof.state_code')
         ->leftJoin('room_cat', 'room_cat.cat_code', '=', 'roomocc.roomcat')
         ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
         ->leftJoin('subgroup as company', 'company.sub_code', '=', 'guestfolio.company')
         ->leftJoin('subgroup as travelagent', 'travelagent.sub_code', '=', 'guestfolio.travelagent')
         ->where('roomocc.propertyid', $this->propertyid)
         ->where('roomocc.docid', $docid)
         ->where('roomocc.sno1', $sno1)
         ->first();

      $qry1s = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid);
      $qry2s = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)
         ->whereNull('modeset');
      $qry3s = Paycharge::select('paycharge.*', 'revmast.name as revname')->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)->where('paycharge.folionodocid', $docid)
         ->whereNotNull('paycharge.modeset')->whereNot('paycharge.amtcr', 0)->orderBy('paycharge.sno', 'ASC');
      if ($rocc) {
         $qry1s->where('msno1', $rocc->sno1);
         $qry2s->where('msno1', $rocc->sno1);
         $qry3s->where('paycharge.msno1', $rocc->sno1);
         $payd = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('msno1', $rocc->sno1)
            ->where('modeset', 'S')->first();
      } else {
         $qry1s->where('sno1', $sno1);
         $qry2s->where('sno1', $sno1);
         $qry3s->where('paycharge.sno1', $sno1);
         $payd = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('sno1', $sno1)
            ->where('modeset', 'S')->first();
      }
      $qry1 = $qry1s->sum('amtdr');
      $qry2 = $qry2s->sum('amtcr');
      $qry3 = $qry3s->get();
      $totalamt = str_replace(',', '', number_format($qry1 - $qry2, 2));
      $data = [
         'roomoccdata' => $roomoccdata,
         'paymodedata' => $paymodedata,
         'billno' => $billno,
         'companydata' => $companydata,
         'totalamt' => $totalamt,
         'qry3' => $qry3,
         'sno1' => $sno1,
         'payd' => $payd
      ];

      return json_encode($data);
   }

   public function fomtaxdetail(Request $request)
   {
      $permission = revokeopen(141511);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;
      $taxnames = Paycharge::select('revmast.name', 'paycharge.paycode', 'paycharge.taxper')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('revmast.field_type', 'T')
         ->whereNotNull('paycharge.taxper')
         // ->whereBetween('paycharge.vdate', [$this->ncurdate, $this->ncurdate])
         ->groupBy('paycharge.paycode')
         ->get();
      $data = DB::table('guestfolio')->where('propertyid', $this->propertyid)->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.fomtaxdetail', [
         'data' => $data,
         'fromdate' => $fromdate,
         'company' => $company,
         'statename' => $statename,
         'taxnames' => $taxnames
      ]);
   }

   public function fetchtaxesnames(Request $request)
   {
      $fromdate = $request->input('fromdate') ?? $this->ncurdate;
      $todate = $request->input('todate') ?? $this->ncurdate;

      $taxnames = Paycharge::select('revmast.name', 'paycharge.paycode', 'paycharge.taxper')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('revmast.field_type', 'T')
         ->whereBetween('paycharge.vdate', [$fromdate, $todate])
         ->groupBy('paycharge.paycode')
         ->get();

      return json_encode($taxnames);
   }

   public function fetchfomtaxdata(Request $request)
   {

      $propertyid = $this->propertyid;
      $cgstCode = 'CGSS' . $propertyid;
      $sgstCode = 'SGSS' . $propertyid;
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      $taxData = DB::table('paycharge AS P')
         ->select(
            'P.folionodocid',
            'P.billno',
            DB::raw("SUM(CASE WHEN P.taxper = 6 AND P.paycode = '{$cgstCode}' THEN P.onamt ELSE 0 END) AS BASEVALUE1"),
            DB::raw("SUM(CASE WHEN P.taxper = 6 AND P.paycode = '{$cgstCode}' THEN P.amtdr - P.amtcr ELSE 0 END) AS TAXAMT1"),
            DB::raw("MAX(CASE WHEN P.taxper = 6 AND P.paycode = '{$cgstCode}' THEN P.taxper ELSE 0 END) AS TAXPER1"),

            DB::raw("SUM(CASE WHEN P.taxper = 9 AND P.paycode = '{$cgstCode}' THEN P.onamt ELSE 0 END) AS BASEVALUE2"),
            DB::raw("SUM(CASE WHEN P.taxper = 9 AND P.paycode = '{$cgstCode}' THEN P.amtdr - P.amtcr ELSE 0 END) AS TAXAMT2"),
            DB::raw("MAX(CASE WHEN P.taxper = 9 AND P.paycode = '{$cgstCode}' THEN P.taxper ELSE 0 END) AS TAXPER2"),

            DB::raw("SUM(CASE WHEN P.taxper = 6 AND P.paycode = '{$sgstCode}' THEN P.onamt ELSE 0 END) AS BASEVALUE3"),
            DB::raw("SUM(CASE WHEN P.taxper = 6 AND P.paycode = '{$sgstCode}' THEN P.amtdr - P.amtcr ELSE 0 END) AS TAXAMT3"),
            DB::raw("MAX(CASE WHEN P.taxper = 6 AND P.paycode = '{$sgstCode}' THEN P.taxper ELSE 0 END) AS TAXPER3"),

            DB::raw("SUM(CASE WHEN P.taxper = 9 AND P.paycode = '{$sgstCode}' THEN P.onamt ELSE 0 END) AS BASEVALUE4"),
            DB::raw("SUM(CASE WHEN P.taxper = 9 AND P.paycode = '{$sgstCode}' THEN P.amtdr - P.amtcr ELSE 0 END) AS TAXAMT4"),
            DB::raw("MAX(CASE WHEN P.taxper = 9 AND P.paycode = '{$sgstCode}' THEN P.taxper ELSE 0 END) AS TAXPER4")
         )
         ->where('P.roomtype', '=', 'RO')
         ->whereRaw('P.amtdr - P.amtcr <> 0')
         ->whereNotIn('P.vtype', ['ARRES', 'ADRES'])
         ->where('P.foliono', '<>', 0)
         ->whereBetween('P.settledate', [$fromdate, $todate])
         ->where('P.propertyid', '=', $propertyid)
         ->groupBy('P.folionodocid', 'P.billno');

      $guestData = DB::table('paycharge AS P')
         ->select(
            'P.folionodocid',
            'P.foliono',
            'P.settledate',
            'P.billno',
            'roomocc.name AS GuestName',
            'S.name AS company',
            'S.gstin',
            DB::raw("CASE 
            WHEN EXISTS (
                SELECT 1 FROM roomocc ro 
                WHERE ro.docid = P.folionodocid 
                AND ro.type = 'O' 
                AND ro.leaderyn = 'Y'
            ) 
            THEN (
                SELECT ro.roomno FROM roomocc ro 
                WHERE ro.docid = P.folionodocid 
                AND ro.type = 'O' 
                AND ro.leaderyn = 'Y' 
                LIMIT 1
            )
            ELSE roomocc.roomno 
        END AS RoomNo"),
            DB::raw('P.amtdr - P.amtcr AS AmtDr')
         )
         ->join('roomocc', function ($join) {
            $join->on('P.folionodocid', '=', 'roomocc.docid')
               ->where('roomocc.type', '=', 'O');
         })
         ->join('guestfolio', 'P.folionodocid', '=', 'guestfolio.docid')
         ->leftJoin('subgroup AS S', 'guestfolio.company', '=', 'S.sub_code')
         ->leftJoin('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
         ->where('P.roomtype', '=', 'RO')
         ->whereRaw('P.amtdr - P.amtcr <> 0')
         ->whereNotIn('P.vtype', ['ARRES', 'ADRES'])
         ->where('P.foliono', '<>', 0)
         ->whereBetween('P.settledate', [$fromdate, $todate])
         ->where('P.propertyid', '=', $propertyid)
         ->where(function ($query) {
            $query->whereNotNull('P.billno')
               ->where('P.billno', '<>', 0)
               ->orWhere('P.paycode', '=', 'ROFF101');
         });

      $billAmounts = DB::table('paycharge')
         ->select(
            'folionodocid',
            'billno',
            DB::raw('IFNULL(SUM(amtdr), 0) AS billamount')
         )
         ->whereBetween('settledate', [$fromdate, $todate])
         ->groupBy('folionodocid', 'billno');

      $guestDataSql = $guestData->toSql();
      $guestDataBindings = $guestData->getBindings();

      $taxDataSql = $taxData->toSql();
      $taxDataBindings = $taxData->getBindings();

      $billAmountsSql = $billAmounts->toSql();
      $billAmountsBindings = $billAmounts->getBindings();

      $results = DB::table(DB::raw("({$guestDataSql}) AS GD"))
         ->select(
            'GD.foliono',
            'GD.folionodocid',
            'GD.settledate',
            DB::raw('MAX(GD.billno) AS BILL_NO'),
            DB::raw('MAX(GD.GuestName) AS GuestName'),
            DB::raw('MAX(GD.company) AS companyname'),
            DB::raw('MAX(GD.gstin) AS companygstin'),
            DB::raw('GROUP_CONCAT(DISTINCT GD.RoomNo) AS RoomNo'),
            DB::raw("'' AS RevenueName"),
            DB::raw('IFNULL(SUM(GD.AmtDr), 0) AS AmtDr'),

            'TD.BASEVALUE1',
            'TD.TAXPER1',
            'TD.TAXAMT1',
            'TD.BASEVALUE2',
            'TD.TAXPER2',
            'TD.TAXAMT2',
            'TD.BASEVALUE3',
            'TD.TAXPER3',
            'TD.TAXAMT3',
            'TD.BASEVALUE4',
            'TD.TAXPER4',
            'TD.TAXAMT4',

            DB::raw('TD.BASEVALUE1 + TD.BASEVALUE2 + TD.BASEVALUE3 + TD.BASEVALUE4 AS EBASEVALUE'),
            DB::raw('TD.TAXAMT1 + TD.TAXAMT2 + TD.TAXAMT3 + TD.TAXAMT4 AS ETAXAMT'),

            'BA.billamount'
         )
         ->join(DB::raw("({$taxDataSql}) AS TD"), function ($join) {
            $join->on('GD.folionodocid', '=', 'TD.folionodocid')
               ->on('GD.billno', '=', 'TD.billno');
         })
         ->join(DB::raw("({$billAmountsSql}) AS BA"), function ($join) {
            $join->on('GD.folionodocid', '=', 'BA.folionodocid')
               ->on('GD.billno', '=', 'BA.billno');
         })
         ->groupBy('GD.billno', 'TD.folionodocid')
         ->orderByRaw('CAST(MAX(GD.billno) AS DECIMAL)');

      $allBindings = array_merge(
         $guestDataBindings,
         $taxDataBindings,
         $billAmountsBindings
      );

      foreach ($allBindings as $binding) {
         $results->addBinding($binding, 'join');
      }

      $result = $results->get();

      if ($result->isEmpty()) {
         return response()->json(['taxdetail' => []]);
      }

      return response()->json([
         'taxdetail' => $result,
         'fromdate' => $fromdate,
         'todate' => $todate
      ]);
   }

   public function occupancyreport(Request $request)
   {
      $permission = revokeopen(141215);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $comp = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
      return view('property.occupancyreport', [
         'ncurdate' => $this->ncurdate,
         'comp' => $comp,
         'statename' => $statename
      ]);
   }

   public function fetchoocxhr(Request $request)
   {
      $fordate = $request->input('fordate');
      $sortedby = $request->input('sortedby');
      $printcondition = $request->input('printcondition');

      $occdata = DB::table('roomocc')
         ->select([
            'guestprof.mobile_no as mobileno',
            'guestfolio.docid as folionodocid',
            'roomocc.foliono',
            'guestfolio.guestprof',
            'room_mast.room_cat',
            'room_mast.rcode as roomno',
            'guestprof.con_prefix',
            'guestprof.nationality',
            'roomocc.name as guestname',
            'roomocc.propertyid',
            'guestprof.gender',
            'roomocc.adult',
            'roomocc.children',
            DB::raw('(roomocc.adult + roomocc.children) as pax'),
            'countries.name as CName',
            'gueststats.name as GuestStatus',
            'guestprof.age',
            'guestprof.id_proof',
            'guestprof.idproof_no as IdProofNo',
            'guestfolio.arrfrom',
            'guestfolio.destination',
            'guestfolio.travelmode',
            'guestfolio.purvisit',
            'busssource.name as BusiSrc',
            'guestfolio.remark as Remark',
            'subgroup.name as companyname',
            'sg.name as travelname',
            'roomocc.roomrate',
            'guestfolio.rodisc as roomdisc',
            'plan_mast.name as package',
            'plan_mast.tarrif',
            'roomocc.plancode',
            'roomocc.ratecode',
            'roomocc.chngdate',
            'roomocc.type',
            DB::raw('CONCAT(DATE_FORMAT(guestfolio.vdate, "%d-%m-%Y"), " ", DATE_FORMAT(roomocc.chkintime, "%h:%i")) as chkindate'),
            DB::raw('DATE_FORMAT(roomocc.depdate, "%d-%m-%Y") as depdate'),
            'roomocc.RRTaxInc',
            'guestfolio.add1',
            'guestfolio.add2 as address'
         ])
         ->join('room_mast', function ($query) {
            $query->on('room_mast.rcode', '=', 'roomocc.roomno')
               ->where('room_mast.propertyid', $this->propertyid)
               ->where('room_mast.type', 'RO');
         })
         ->leftJoin('guestfolio', function ($query) {
            $query->on('roomocc.docid', '=', 'guestfolio.docid')
               ->where('guestfolio.propertyid', $this->propertyid);
         })
         ->leftJoin('guestprof', function ($query) {
            $query->on('guestfolio.guestprof', '=', 'guestprof.guestcode')
               ->where('guestprof.propertyid', $this->propertyid);
         })
         ->leftJoin('plan_mast', function ($query) {
            $query->on('roomocc.plancode', '=', 'plan_mast.pcode')
               ->where('plan_mast.propertyid', $this->propertyid);
         })
         ->leftJoin('countries', function ($query) {
            $query->on('guestprof.nationality', '=', 'countries.country_code')
               ->where('countries.propertyid', $this->propertyid);
         })
         ->leftJoin('gueststats', function ($query) {
            $query->on('guestprof.guest_status', '=', 'gueststats.gcode')
               ->where('gueststats.propertyid', $this->propertyid);
         })
         ->leftJoin('subgroup', function ($query) {
            $query->on('guestfolio.company', '=', 'subgroup.sub_code')
               ->where('subgroup.propertyid', $this->propertyid);
         })
         ->leftJoin('subgroup as sg', function ($query) {
            $query->on('guestfolio.travelagent', '=', 'sg.sub_code')
               ->where('sg.propertyid', $this->propertyid);
         })
         ->leftJoin('busssource', function ($query) {
            $query->on('busssource.bcode', '=', 'guestfolio.busssource')
               ->where('busssource.propertyid', $this->propertyid);
         })
         ->where('roomocc.propertyid', $this->propertyid)
         ->whereNull('roomocc.type')
         // ->where('roomocc.chkindate', '<=', $fordate)
         // ->where(function ($query) {
         //    $query->where('roomocc.type', '!=', 'C')
         //       ->orWhereNull('roomocc.type');
         // })
         ->groupBy('roomocc.roomno')
         ->orderBy('room_mast.rcode')
         ->get();

      $dayuse = RoomOcc::where('propertyid', $this->propertyid)->where('chkindate', $fordate)->where('chkoutdate', $fordate)
         ->whereIn('type', ['O'])
         ->count();

      $data = [
         'occdata' => $occdata,
         'dayuse' => $dayuse
      ];
      return response()->json($data);
   }

   public function fetchfomtaxdata2(Request $request)
   {

      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');
      $taxes = json_decode($request->input('taxes'));
      return json_encode($taxes);

      $cgst = 'CGSS' . $this->propertyid;
      $sgst = 'SGSS' . $this->propertyid;

      $data = Paycharge::select(
         'paycharge.vdate',
         'guestfolio.name as guestname',
         'paycharge.folionodocid',
         'paycharge.foliono',
         'paycharge.billno',
         'roomocc.roomno',
         'paycharge.taxper',
         'paycharge.onamt',
         'revmast.rev_code',
         DB::raw("MAX(CASE WHEN revmast.rev_code = '{$cgst}' THEN paycharge.amtdr ELSE NULL END) AS CGST"),
         DB::raw("MAX(CASE WHEN revmast.rev_code = '{$sgst}' THEN paycharge.amtdr ELSE NULL END) AS SGST"),
         'subgroup.name AS company',
         'subgroup.gstin'
      )
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'paycharge.folionodocid')
         ->leftJoin('roomocc', 'roomocc.docid', '=', 'paycharge.folionodocid')
         ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'paycharge.comp_code')
         ->where('revmast.rev_code', $cgst)
         ->orWhere('revmast.rev_code', $sgst)
         ->whereBetween('paycharge.vdate', [$fromdate, $todate])
         ->where('paycharge.propertyid', $this->propertyid)
         ->groupBy('paycharge.sno1', 'paycharge.sno', 'paycharge.foliono')
         ->get();

      return json_encode($data);
   }

   public function enviroform(Request $request)
   {
      $data = EnviroFom::where('propertyid', $this->propertyid)->first();
      return json_encode($data);
   }


   public function itemwisesale(Request $request)
   {
      $permission = revokeopen(171713);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;
      $taxnames = Paycharge::select('revmast.name', 'paycharge.paycode', 'paycharge.taxper')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('revmast.field_type', 'T')
         ->whereNotNull('paycharge.taxper')
         ->groupBy('paycharge.paycode')
         ->get();
      $data = DB::table('guestfolio')->where('propertyid', $this->propertyid)->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      $departs = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->groupBy('dcode')->orderBy('name', 'ASC')->get();
      $items = ItemMast::where('Property_ID', $this->propertyid)->groupBy('Code')->orderBy('Name', 'ASC')->get();
      return view('property.pos_itemwisesale', [
         'data' => $data,
         'fromdate' => $fromdate,
         'company' => $company,
         'statename' => $statename,
         'taxnames' => $taxnames,
         'departs' => $departs,
         'items' => $items,
      ]);
   }

   public function itemwiserepfetch(Request $request)
   {
      try {
         $fromDate = $request->input('fromdate');
         $toDate = $request->input('todate');
         $allOutlets = explode(',', $request->input('alloutlets'));
         $allitemgroups = explode(',', $request->input('allitemgroups'));
         $allitems = explode(',', $request->input('allitems'));
         $propertyId = $this->propertyid;
         $nckot = $request->nckot;

         $depart = Depart::where('propertyid', $propertyId)
            ->whereIn('dcode', $allOutlets)
            ->get();

         $shortname1 = [];
         $shortname2 = [];
         foreach ($depart as $row) {
            $shortname1[] = 'B' . $row->short_name;
            if ($nckot != 'N') {
               $shortname2[] = 'N' . $row->short_name;
            }
         }

         if ($nckot == 'N') {
            $saleQuery = DB::table('sale1 as S')
               ->select([
                  DB::raw('MAX(sk.docid) as DOC'),
                  'I.DispCode',
                  DB::raw('MAX(I.HSNCode) as HSNCODE'),
                  'I.Name as ITEMNAME',
                  DB::raw('MAX(U.name) as UNIT'),
                  DB::raw('0 as NCQTY'),
                  DB::raw('SUM(sk.qtyiss) as QTY'),
                  DB::raw('SUM(sk.amount) as VALUE1'),
                  DB::raw('SUM(sk.discamt) as DISC'),
                  DB::raw('SUM(sk.amount - sk.discapp) as VALUE2'),
                  DB::raw('MAX(IG.name) as ITEMGROUP'),
                  DB::raw('MAX(sk.restcode) as RestCode'),
                  DB::raw('MAX(D.name) as DepartCode'),
               ])
               ->join('stock as sk', 'S.docid', '=', 'sk.docid')
               ->join('itemmast as I', function ($join) {
                  $join->on('sk.item', '=', 'I.Code')
                     ->on('sk.itemrestcode', '=', 'I.RestCode');
               })
               ->join('unitmast as U', function ($join) use ($propertyId) {
                  $join->on('U.ucode', '=', 'I.Unit')
                     ->where('U.propertyid', $propertyId);
               })
               ->join('itemgrp as IG', 'I.ItemGroup', '=', 'IG.code')
               ->join('depart as D', 'S.restcode', '=', 'D.dcode')
               ->whereIn('sk.vtype', $shortname1)
               ->where('S.delflag', 'N')
               ->whereIn('I.ItemGroup', $allitemgroups)
               ->whereIn('I.Code', $allitems)
               ->whereBetween('S.vdate', [$fromDate, $toDate])
               ->whereIn('S.restcode', $allOutlets)
               ->groupBy('ITEMNAME', 'sk.restcode')
               ->orderBy('ITEMNAME')
               ->get();
         } else {
            $saleQuery = DB::table('stock as sk')
               ->select([
                  DB::raw('MAX(sk.docid) as DOC'),
                  'I.DispCode',
                  DB::raw('MAX(I.HSNCode) as HSNCODE'),
                  'I.Name as ITEMNAME',
                  DB::raw('MAX(U.name) as UNIT'),
                  DB::raw('SUM(CASE WHEN sk.vtype IN (' . implode(',', array_map(fn($val) => "'$val'", $shortname1)) . ') THEN sk.qtyiss ELSE 0 END) as QTY'),
                  DB::raw('SUM(CASE WHEN sk.vtype IN (' . implode(',', array_map(fn($val) => "'$val'", $shortname2)) . ') THEN sk.qtyiss ELSE 0 END) as NCQTY'),
                  DB::raw('SUM(sk.amount) as VALUE1'),
                  DB::raw('SUM(sk.discamt) as DISC'),
                  DB::raw('SUM(sk.amount - sk.discapp) as VALUE2'),
                  DB::raw('MAX(IG.name) as ITEMGROUP'),
                  'sk.restcode',
                  DB::raw('MAX(D.name) as DepartCode'),
               ])
               ->join('itemmast as I', function ($join) {
                  $join->on('sk.item', '=', 'I.Code')
                     ->on('sk.itemrestcode', '=', 'I.RestCode');
               })
               ->join('unitmast as U', function ($join) use ($propertyId) {
                  $join->on('U.ucode', '=', 'I.Unit')
                     ->where('U.propertyid', $propertyId);
               })
               ->join('itemgrp as IG', 'I.ItemGroup', '=', 'IG.code')
               ->join('depart as D', 'sk.restcode', '=', 'D.dcode')
               ->whereIn('sk.vtype', array_merge($shortname1, $shortname2))
               ->where('sk.delflag', 'N')
               ->whereIn('I.ItemGroup', $allitemgroups)
               ->whereIn('I.Code', $allitems)
               ->whereBetween('sk.vdate', [$fromDate, $toDate])
               ->whereIn('sk.restcode', $allOutlets)
               ->groupBy('ITEMNAME', 'sk.restcode')
               ->orderBy('ITEMNAME')
               ->get();
         }

         return response()->json([
            'items' => $saleQuery,
            'shortname1' => $shortname1,
            'shortname2' => $shortname2,
            'status' => 'success'
         ]);
      } catch (\Exception $e) {
         return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while fetching the report: ' . $e->getMessage()
         ], 500);
      }
   }


   public function deletedunsettledbill(Request $request)
   {
      $permission = revokeopen(171714);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;
      $taxnames = Paycharge::select('revmast.name', 'paycharge.paycode', 'paycharge.taxper')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('revmast.field_type', 'T')
         ->whereNotNull('paycharge.taxper')
         ->groupBy('paycharge.paycode')
         ->get();
      $data = DB::table('guestfolio')->where('propertyid', $this->propertyid)->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      $departs = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->groupBy('dcode')->orderBy('name', 'ASC')->get();
      $items = ItemMast::where('Property_ID', $this->propertyid)->groupBy('Code')->orderBy('Name', 'ASC')->get();
      return view('property.pos_saledeletereport', [
         'data' => $data,
         'fromdate' => $fromdate,
         'company' => $company,
         'statename' => $statename,
         'taxnames' => $taxnames,
         'departs' => $departs,
         'items' => $items,
      ]);
   }

   public function saledelxhr(Request $request)
   {

      $alloutlets = $request->input('alloutlets');
      $delorunsettle = $request->input('delorunsettle');
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      if ($delorunsettle == 'delete') {
         $query = DB::table('sale1 as S')
            ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
            ->leftJoin('server_mast as W', 'S.waiter', '=', 'W.scode')
            ->select(
               'S.vno',
               'S.vdate',
               'S.roomno',
               'S.netamt',
               'S.guaratt',
               'W.name as Steward',
               'D.name as OutletName',
               'D.dcode as OutletCode',
               'S.delremark',
               'S.u_name',
               'S.delflag'
            )
            ->where('S.propertyid', '=', $this->propertyid)
            ->whereBetween('S.vdate', [$fromdate, $todate])
            ->whereIn('S.restcode', explode(',', $alloutlets))
            ->where('S.delflag', '=', 'Y')
            ->get();
      } else if ($delorunsettle == 'unsettle') {
         $query = Sale1::select(
            'sale1.vno',
            'sale1.vdate',
            'sale1.roomno',
            'sale1.netamt',
            'sale1.guaratt',
            'server_mast.name AS Steward',
            'depart.name AS OutletName',
            'depart.dcode AS OutletCode',
            'sale1.delremark',
            'sale1.u_name',
            'sale1.delflag'
         )
            ->leftJoin('depart', 'sale1.restcode', '=', 'depart.dcode')
            ->leftJoin('server_mast', 'sale1.waiter', '=', 'server_mast.scode')
            ->where('sale1.propertyid', '=', $this->propertyid)
            ->whereBetween('sale1.vdate', [$fromdate, $todate])
            ->whereIn('sale1.restcode', explode(',', $alloutlets))
            ->where('sale1.delflag', 'N')
            ->whereNotIn('sale1.docid', function ($query) {
               $query->select('docid')->distinct()->from('paycharge');
            })
            ->get();
      } else if ($delorunsettle == 'combine') {
         $query = DB::table('sale1 as S')
            ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
            ->leftJoin('server_mast as W', 'S.waiter', '=', 'W.scode')
            ->select(
               'S.vno',
               'S.vdate',
               'S.roomno',
               'S.netamt',
               'S.guaratt',
               'W.name as Steward',
               'D.name as OutletName',
               'D.dcode as OutletCode',
               'S.delremark',
               'S.u_name',
               'S.delflag'
            )
            ->where('S.propertyid',  '=', $this->propertyid)
            ->whereBetween('S.vdate', [$fromdate, $todate])
            ->whereIn('S.restcode', explode(',', $alloutlets))
            ->where(function ($query) {
               $query->where('S.delflag', 'Y') // Delete condition
                  ->orWhere(function ($query) { // Unsettle condition
                     $query->where('S.delflag', 'N')
                        ->whereNotIn('S.docid', function ($subquery) {
                           $subquery->select('docid')
                              ->distinct()
                              ->from('paycharge');
                        });
                  });
            })
            ->get();
      }

      $data = [
         'items' => $query,
      ];

      return json_encode($data);
   }

   public function getindex(Request $request)
   {
      if ($this->revokeopen(141114)->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }

      $startdate = $request->input('startdate');
      $enddate = $request->input('enddate');

      $gueststayduration = RoomOcc::select('chkindate', DB::raw('COUNT(DISTINCT docid) as guest_count'))
         ->where('propertyid', $this->propertyid)
         ->whereBetween('chkindate', [$startdate, $enddate])
         ->groupBy('chkindate')
         ->orderBy('chkindate')
         ->get();

      $depart = Depart::where('propertyid', $this->propertyid)
         ->where('nature', 'Outlet')
         ->where('kot_yn', 'Y')
         ->get();

      $totalamount = 0.00;

      foreach ($depart as $row) {
         $vouchers = VoucherType::where('propertyid', $this->propertyid)
            ->where('restcode', $row->dcode)
            ->where('description', $row->short_name . " Memo Entry")
            ->get();

         foreach ($vouchers as $item) {
            $totalamount += Paycharge::where('propertyid', $this->propertyid)
               ->where('vtype', $item->v_type)
               ->where('vdate', $this->ncurdate)
               ->sum('amtcr');
         }
      }

      $totalamount1 = Paycharge::where('propertyid', $this->propertyid)
         ->where('vtype', 'REC')
         ->where('vdate', $this->ncurdate)
         ->sum('amtcr');

      $combinedTotal = $totalamount + $totalamount1;

      $yesterday = date('Y-m-d', strtotime($this->ncurdate . ' -2 day'));
      $yesterdaytime = date('Y-m-d H:i:s', strtotime($this->ncurdate . ' -2 day'));
      $last7days = date('Y-m-d', strtotime($this->ncurdate . ' -7 day'));
      $yesterdayroomchargamount = Paycharge::where('propertyid', $this->propertyid)
         ->where('vdate', $yesterday)
         ->sum('amtcr');

      $totalkotlast24 = Kot::where('propertyid', $this->propertyid)
         ->where('u_entdt', '>=', $yesterdaytime)
         ->distinct('docid')
         ->count('docid');

      $totalreservation7days = Bookings::where('Property_ID', $this->propertyid)
         ->where('vdate', '>=', $last7days)
         ->distinct('DocId')
         ->count('DocId');

      $totalamountoutletyesterday = 0.00;

      foreach ($depart as $row) {
         $vouchers = VoucherType::where('propertyid', $this->propertyid)
            ->where('restcode', $row->dcode)
            ->where('description', $row->short_name . " Memo Entry")
            ->get();

         foreach ($vouchers as $item) {
            $totalamountoutletyesterday += Paycharge::where('propertyid', $this->propertyid)
               ->where('vtype', $item->v_type)
               ->where('vdate', $yesterday)
               ->sum('amtcr');
         }
      }

      $yesterdaycombinedTotal = $yesterdayroomchargamount + $totalamountoutletyesterday;

      $percentageChange = $yesterdaycombinedTotal > 0
         ? (($combinedTotal - $yesterdaycombinedTotal) / $yesterdaycombinedTotal) * 100
         : 0;


      $totalRooms = RoomMast::where('propertyid', $this->propertyid)
         ->where('type', 'RO')
         ->count();

      $occupiedRoomsCount = DB::table('roomocc')
         ->leftJoin('paycharge', function ($join) {
            $join->on('paycharge.roomno', '=', 'roomocc.roomno')
               ->on('paycharge.sno1', '=', 'roomocc.sno1');
         })
         ->where('roomocc.propertyid', $this->propertyid)
         ->whereNull('roomocc.type')
         ->where(function ($query) {
            $query->where('paycharge.vtype', 'BRS')
               ->orWhereNull('paycharge.vtype');
         })
         ->groupBy('roomocc.roomno', 'roomocc.sno1')
         ->get()
         ->count();

      $occupancyPercentage = 100;
      if ($totalRooms > 0) {
         $occupancyPercentage = ($occupiedRoomsCount / $totalRooms) * 100;
      }

      $data = [
         'occupancyPercentage' => $occupancyPercentage,
         'totalreservation7days' => $totalreservation7days,
         'totalkotlast24' => $totalkotlast24,
         'yesterdaycombinedTotal' => number_format($yesterdaycombinedTotal, 2),
         'yesterday' => $yesterday,
         'combinedTotal' => number_format($combinedTotal, 2),
         'gueststayduration' => $gueststayduration,
         'enddate' => $enddate,
         'percentageChange' => str_replace(',', '', number_format($percentageChange, 2)),
      ];

      return response()->json(['success' => 'Data Found', 'data' => $data]);
   }


   public function salesummary(Request $request)
   {
      $permission = revokeopen(171811);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $fromdate = $this->ncurdate;
      $taxnames = Paycharge::select('revmast.name', 'paycharge.paycode', 'paycharge.taxper')
         ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
         ->where('paycharge.propertyid', $this->propertyid)
         ->where('revmast.field_type', 'T')
         ->whereNotNull('paycharge.taxper')
         ->groupBy('paycharge.paycode')
         ->get();
      $data = DB::table('guestfolio')->where('propertyid', $this->propertyid)->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      $departs = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->groupBy('dcode')->orderBy('name', 'ASC')->get();
      $items = ItemMast::where('Property_ID', $this->propertyid)->groupBy('Code')->orderBy('Name', 'ASC')->get();
      return view('property.pos_salesummary', [
         'data' => $data,
         'fromdate' => $fromdate,
         'company' => $company,
         'statename' => $statename,
         'taxnames' => $taxnames,
         'departs' => $departs,
         'items' => $items,
      ]);
   }


   public function salesummaryrpt(Request $request)
   {
      $alloutlets = $request->input('alloutlets');
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');

      $cgst = 'CGSS' . $this->propertyid;
      $sgst = 'SGSS' . $this->propertyid;

      // Get dynamic tax percentages
      $taxRates = DB::table('sale2')
         ->select('taxper')
         ->distinct()
         ->where('delflag', '<>', 'Y')
         ->whereBetween('vdate', [$fromdate, $todate])
         ->whereIn('restcode', explode(',', $alloutlets))
         ->where('propertyid', $this->propertyid)
         ->pluck('taxper')
         ->toArray();

      // Generate dynamic CGST/SGST columns
      $dynamicColumns = [];
      foreach ($taxRates as $rate) {
         $r = str_replace('.', '_', $rate);

         $dynamicColumns[] = "COALESCE(SUM(CASE WHEN TC.taxcode = '$cgst' AND TC.taxper = $rate THEN TC.basevalue END), 0) AS CGST_BASE_$r";
         $dynamicColumns[] = "COALESCE(SUM(CASE WHEN TC.taxcode = '$cgst' AND TC.taxper = $rate THEN TC.taxamt END), 0) AS CGST_TAXAMT_$r";
         $dynamicColumns[] = "COALESCE(SUM(CASE WHEN TC.taxcode = '$sgst' AND TC.taxper = $rate THEN TC.basevalue END), 0) AS SGST_BASE_$r";
         $dynamicColumns[] = "COALESCE(SUM(CASE WHEN TC.taxcode = '$sgst' AND TC.taxper = $rate THEN TC.taxamt END), 0) AS SGST_TAXAMT_$r";
      }

      $dynamicColumnsSql = implode(",\n  ", $dynamicColumns);

      // Prepare outlet placeholders
      $outletsArray = explode(',', $alloutlets);
      $placeholders = implode(',', array_fill(0, count($outletsArray), '?'));


      $sql = "
        SELECT
            d.name AS DepartName,
            S.vdate,
            MIN(S.vno) AS MinBillNo,
            MAX(S.vno) AS MaxBillNo,
            SUM(DISTINCT S.netamt) AS NetAmt,
            SUM(DISTINCT S.taxable) AS Taxable,
            SUM(DISTINCT S.nontaxable) AS NonTaxable,
            MAX(S.restcode) AS RestCode,
            SUM(DISTINCT S.roundoff) AS RoundOff,
            SUM(DISTINCT S.dedamt) AS DedAmt,
            SUM(DISTINCT S.addamt) AS AddAmt,
            SUM(DISTINCT S.servicecharge) AS ServiceCharge,
            SUM(DISTINCT S.discamt) AS DiscAmt,
            SUM(DISTINCT S.total) AS GoodsAmt,
            $dynamicColumnsSql
        FROM sale1 AS S
        LEFT JOIN (
            SELECT docid, vdate, taxcode, taxper, basevalue, taxamt
            FROM sale2
            WHERE delflag <> 'Y'
              AND vdate BETWEEN ? AND ?
              AND restcode IN ($placeholders)
        ) AS TC ON S.docid = TC.docid AND S.vdate = TC.vdate
        LEFT JOIN depart d ON S.restcode = d.dcode
        WHERE S.delflag <> 'Y'
          AND S.vdate BETWEEN ? AND ?
          AND S.restcode IN ($placeholders)
        GROUP BY d.name, S.vdate
        ORDER BY d.name, S.vdate
    ";

      // Final bindings
      $bindings = [
         $fromdate,
         $todate,
         ...$outletsArray, // for subquery
         $fromdate,
         $todate,
         ...$outletsArray, // for main query
      ];

      $results = DB::select($sql, $bindings);

      return response()->json(['items' => $results]);
   }



   public function arrivallist(Request $request)
   {
      $permission = revokeopen(131211);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $comp = DB::table('company')->where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
      return view('property.arrivallist', [
         'comp' => $comp,
         'statename' => $statename,
         'fromdate' => $this->ncurdate
      ]);
   }

   public function arrivallistfetch(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $todate = $request->input('todate');
      $pendingyn = $request->input('pendingyn');

      $report = GrpBookinDetail::leftJoin('booking', 'booking.DocId', '=', 'grpbookingdetails.BookingDocid')
         ->leftJoin('guestprof', 'guestprof.docid', '=', 'grpbookingdetails.BookingDocid')
         ->leftJoin('plan_mast', 'grpbookingdetails.Plan_Code', '=', 'plan_mast.pcode')
         ->leftJoin('room_cat', 'room_cat.cat_code', '=', 'grpbookingdetails.RoomCat')
         ->leftJoin('subgroup AS S', 'booking.Company', '=', 'S.sub_code')
         ->leftJoin('subgroup AS T', 'booking.TravelAgency', '=', 'T.sub_code')
         ->leftJoin('bookingplandetails', function ($join) {
            $join->on('bookingplandetails.docid', '=', 'grpbookingdetails.BookingDocid')
               ->on('bookingplandetails.sno1', '=', 'grpbookingdetails.Sno');
         })
         ->select([
            DB::raw("CASE WHEN bookingplandetails.docid != '' THEN bookingplandetails.netplanamt ELSE grpbookingdetails.Tarrif 
            END AS tarrifamount"),
            'T.name as travelname',
            'booking.Vtype',
            'booking.DocId',
            'grpbookingdetails.Sno',
            'booking.BookNo AS ResNo',
            'grpbookingdetails.ContraDocId',
            'grpbookingdetails.GuestName AS GuestName',
            'booking.MobNo',
            'S.name AS Company',
            'booking.NoofRooms AS RoomDet',
            'grpbookingdetails.ArrDate',
            'grpbookingdetails.ArrTime',
            'grpbookingdetails.Adults AS Pax',
            'grpbookingdetails.Childs AS Child',
            'grpbookingdetails.DepDate',
            'grpbookingdetails.DepTime',
            'plan_mast.name AS PlanName',
            'grpbookingdetails.RoomNo',
            'room_cat.name AS RoomType',
            'booking.ArrFrom AS ArrDetail',
            'booking.BookedBy',
            'booking.ResStatus',
            'booking.Remarks',
         ])
         ->where('grpbookingdetails.Cancel', '=', 'N')
         ->where('grpbookingdetails.Property_ID', $this->propertyid)
         ->whereBetween('grpbookingdetails.ArrDate', [$fromdate, $todate]);

      if ($pendingyn == 'pending') {
         $report->where('grpbookingdetails.ContraDocId', '');
      } else {
      }

      $report->orderBy('grpbookingdetails.ArrDate')
         ->orderBy('grpbookingdetails.GuestName')
         ->orderBy('booking.BookNo')
         ->orderBy('grpbookingdetails.Sno');

      $data = [
         'data' => $report->get()
      ];

      return response()->json($data);
   }

   public function dailyreport(Request $request)
   {
      $permission = revokeopen(191212);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $ncurdate = $this->ncurdate;
      $comp = Companyreg::where('propertyid', $this->propertyid)->first();
      $company = SubGroup::where('propertyid', $this->propertyid)->whereIn('comp_type', ['Corporate', 'Travel Agency'])
         ->orderBy('name')->groupBy('sub_code')->get();
      $departs = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->groupBy('dcode')->orderBy('name', 'ASC')->get();
      $items = Itemmast::where('Property_ID', $this->propertyid)->groupBy('Code')->orderBy('Name', 'ASC')->get();
      $taxes = [
         1 => 'CGSS' . $this->propertyid . '-CGST (SALES)',
         2 => 'SGSS' . $this->propertyid . '-SGST (SALES)',
         3 => 'NT' . $this->propertyid . '-NO TAX',
      ];

      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');

      return view('property.dailyreport', [
         'fordate' => $ncurdate,
         'comp' => $comp,
         'company' => $company,
         'departs' => $departs,
         'items' => $items,
         'taxes' => $taxes,
         'todate',
         'statename' => $statename
      ]);
   }

   public function dailyreportfetch(Request $request)
   {
      $fordate = $request->fordate;
      $ranges = DateHelper::calculateDateRanges($fordate);

      $revmast = Revmast::select('rev_code', 'Name', 'field_type', 'Nature')
         ->where('propertyid', $this->propertyid)
         ->where('Flag_Type', 'FOM')
         ->where('field_type', 'C')
         ->orderBy('rev_code')
         ->get();

      $departments = Depart::select('dcode', 'name', 'short_name')
         ->where('propertyid', $this->propertyid)
         ->whereIn('nature', ['Room Service', 'Outlet'])
         ->get();

      $categories = ItemCatMast::select('CatType AS NAME')
         ->where('propertyid', $this->propertyid)
         ->whereNotNull('RevCode')
         ->where('RevCode', '<>', '')
         ->whereNotNull('CatType')
         ->where('CatType', '<>', '')
         ->whereNot('RestCode', "BANQ$this->propertyid")
         ->distinct()
         ->orderBy('CatType')
         ->get();

      $reportData = [];

      foreach ($revmast as $row) {
         $today = Paycharge::where('vdate', $fordate)
            ->where('restcode', 'FOM' . $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->where('propertyid', $this->propertyid)
            ->selectRaw('SUM(amtdr) - SUM(amtcr) AS Today')
            ->first();

         $mtd = Paycharge::whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
            ->where('restcode', 'FOM' . $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->where('propertyid', $this->propertyid)
            ->selectRaw('SUM(amtdr) - SUM(amtcr) AS MTD')
            ->first();

         $ftd = Paycharge::whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
            ->where('restcode', 'FOM' . $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->where('propertyid', $this->propertyid)
            ->selectRaw('SUM(amtdr) - SUM(amtcr) AS FTD')
            ->first();

         $ytd = Paycharge::whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
            ->where('restcode', 'FOM' . $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->where('propertyid', $this->propertyid)
            ->selectRaw('SUM(amtdr) - SUM(amtcr) AS YTD')
            ->first();

         $reportData[] = [
            'category' => 'Front Office',
            'rev_code' => $row->rev_code,
            'Name' => $row->Name,
            'field_type' => $row->field_type,
            'Nature' => $row->Nature,
            'Today' => $today ? $today->Today : 0,
            'MTD' => $mtd ? $mtd->MTD : 0,
            'FTD' => $ftd ? $ftd->FTD : 0,
            'YTD' => $ytd ? $ytd->YTD : 0
         ];
      }

      foreach ($departments as $department) {
         foreach ($categories as $category) {
            $today = Paycharge::leftJoin('itemcatmast', function ($query) use ($department, $category) {
               $query->on('itemcatmast.Code', '=', 'paycharge.paycode')
                  ->where('itemcatmast.propertyid', $this->propertyid)
                  ->where('itemcatmast.RestCode', $department->dcode);
            })
               ->where('vdate', $fordate)
               ->where('paycharge.restcode', $department->dcode)
               ->whereIn('paycode', function ($query) use ($category) {
                  $query->select('RevCode')->from('itemcatmast')->where('CatType', $category->NAME);
               })
               ->selectRaw('SUM(amtdr - amtcr) AS Today')
               ->first();

            $mtd = Paycharge::whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
               ->where('paycharge.restcode', $department->dcode)
               ->whereIn('paycode', function ($query) use ($category) {
                  $query->select('RevCode')->from('itemcatmast')->where('CatType', $category->NAME);
               })
               ->selectRaw('SUM(amtdr - amtcr) AS MTD')
               ->first();

            $ytd = Paycharge::whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
               ->where('paycharge.restcode', $department->dcode)
               ->whereIn('paycode', function ($query) use ($category) {
                  $query->select('RevCode')->from('itemcatmast')->where('CatType', $category->NAME);
               })
               ->selectRaw('SUM(amtdr - amtcr) AS YTD')
               ->first();

            $reportData[] = [
               'category' => $department->name,
               'rev_code' => $department->dcode,
               'Name' => $category->NAME,
               'short_name' => $department->short_name,
               'Today' => $today ? $today->Today : 0,
               'MTD' => $mtd ? $mtd->MTD : 0,
               'YTD' => $ytd ? $ytd->YTD : 0
            ];
         }
      }

      $banqsaletoday = PaychargeH::select('paytype as name', DB::raw('SUM(amtcr) as today'))
         ->where('propertyid', $this->propertyid)
         ->where('vdate', $fordate)
         ->whereNotIn('paycode', ["CGSP$this->propertyid", "SGSP$this->propertyid"])
         ->groupBy('paycode', 'paytype')
         ->get();

      $banqsalemtd = PaychargeH::select('paytype as name', DB::raw('SUM(amtcr) as MTD'))
         ->where('propertyid', $this->propertyid)
         ->whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
         ->whereNotIn('paycode', ["CGSP$this->propertyid", "SGSP$this->propertyid"])
         ->groupBy('paycode', 'paytype')
         ->get();

      $banqsaleytd = PaychargeH::select('paytype as name', DB::raw('SUM(amtcr) as YTD'))
         ->where('propertyid', $this->propertyid)
         ->whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
         ->whereNotIn('paycode', ["CGSP$this->propertyid", "SGSP$this->propertyid"])
         ->groupBy('paycode', 'paytype')
         ->get();

      $reportData = [];

      foreach ($banqsaletoday as $today) {
         $mtd = $banqsalemtd->firstWhere('name', $today->name);
         $ytd = $banqsaleytd->firstWhere('name', $today->name);

         $reportData[] = [
            'category' => 'Payment Summary',
            'rev_code' => '',
            'Name' => $today->name,
            'short_name' => '',
            'Today' => $today->today ?? 0,
            'MTD' => $mtd->MTD ?? 0,
            'YTD' => $ytd->YTD ?? 0
         ];
      }

      // 🔹 Today's Data
      $hallToday = DB::table('hallstock')
         ->leftJoin('itemmast', function ($join) {
            $join->on('itemmast.Code', '=', 'hallstock.item')
               ->on('itemmast.RestCode', '=', 'hallstock.restcode');
         })
         ->leftJoin('itemcatmast', function ($join) {
            $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
               ->on('itemcatmast.RestCode', '=', 'itemmast.RestCode');
         })
         ->select(
            'itemmast.ItemCatCode',
            'itemcatmast.Name as categoryname',
            DB::raw('SUM(hallstock.amount) as today')
         )
         ->where('hallstock.propertyid', $this->propertyid)
         ->where('hallstock.restcode', 'BANQ' . $this->propertyid)
         ->whereDate('hallstock.vdate', $fordate)
         ->groupBy('itemmast.ItemCatCode', 'itemcatmast.Name')
         ->get();

      // 🔹 MTD Data
      $hallMTD = DB::table('hallstock')
         ->leftJoin('itemmast', function ($join) {
            $join->on('itemmast.Code', '=', 'hallstock.item')
               ->on('itemmast.RestCode', '=', 'hallstock.restcode');
         })
         ->leftJoin('itemcatmast', function ($join) {
            $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
               ->on('itemcatmast.RestCode', '=', 'itemmast.RestCode');
         })
         ->select(
            'itemmast.ItemCatCode',
            DB::raw('SUM(hallstock.amount) as MTD')
         )
         ->where('hallstock.propertyid', $this->propertyid)
         ->where('hallstock.restcode', 'BANQ' . $this->propertyid)
         ->whereBetween('hallstock.vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
         ->groupBy('itemmast.ItemCatCode')
         ->get();

      // 🔹 YTD Data
      $hallYTD = DB::table('hallstock')
         ->leftJoin('itemmast', function ($join) {
            $join->on('itemmast.Code', '=', 'hallstock.item')
               ->on('itemmast.RestCode', '=', 'hallstock.restcode');
         })
         ->leftJoin('itemcatmast', function ($join) {
            $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
               ->on('itemcatmast.RestCode', '=', 'itemmast.RestCode');
         })
         ->select(
            'itemmast.ItemCatCode',
            DB::raw('SUM(hallstock.amount) as YTD')
         )
         ->where('hallstock.propertyid', $this->propertyid)
         ->where('hallstock.restcode', 'BANQ' . $this->propertyid)
         ->whereBetween('hallstock.vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
         ->groupBy('itemmast.ItemCatCode')
         ->get();

      foreach ($hallToday as $row) {
         $mtd = $hallMTD->firstWhere('ItemCatCode', $row->ItemCatCode);
         $ytd = $hallYTD->firstWhere('ItemCatCode', $row->ItemCatCode);

         $reportData[] = [
            'category' => 'Banquet',
            'rev_code' => $row->ItemCatCode,
            'Name' => $row->categoryname,
            'short_name' => '',
            'Today' => $row->today ?? 0,
            'MTD' => $mtd->MTD ?? 0,
            'YTD' => $ytd->YTD ?? 0
         ];
      }

      // 🔹 Today
      $banquetToday = DB::table('hallsale1')
         ->select(DB::raw("'Banquet Sale' as name"), DB::raw('SUM(totalpercover - discamt) as today'))
         ->where('propertyid', $this->propertyid)
         ->where('restcode', 'BANQ' . $this->propertyid)
         ->whereDate('vdate', $fordate)
         ->first();

      // 🔹 MTD
      $banquetMTD = DB::table('hallsale1')
         ->select(DB::raw('SUM(totalpercover - discamt) as MTD'))
         ->where('propertyid', $this->propertyid)
         ->where('restcode', 'BANQ' . $this->propertyid)
         ->whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
         ->first();

      // 🔹 YTD
      $banquetYTD = DB::table('hallsale1')
         ->select(DB::raw('SUM(totalpercover - discamt) as YTD'))
         ->where('propertyid', $this->propertyid)
         ->where('restcode', 'BANQ' . $this->propertyid)
         ->whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
         ->first();

      $reportData[] = [
         'category' => 'Banquet',
         'rev_code' => '',
         'Name' => $banquetToday->name ?? 'Banquet Sale',
         'short_name' => '',
         'Today' => $banquetToday->today ?? 0,
         'MTD' => $banquetMTD->MTD ?? 0,
         'YTD' => $banquetYTD->YTD ?? 0
      ];

      $taxp = Revmast::select('rev_code', 'name')
         ->where('propertyid', $this->propertyid)
         ->whereIn('field_type', ['T'])
         ->get();

      foreach ($taxp as $row) {
         $today = Paycharge::selectRaw("SUM(amtdr) AS Today")
            ->where('propertyid', $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->where('vdate', $fordate)
            ->first();

         $mtd = Paycharge::selectRaw("SUM(amtdr) AS MTD")
            ->where('propertyid', $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
            ->first();

         $ytd = Paycharge::selectRaw("SUM(amtdr) AS YTD")
            ->where('propertyid', $this->propertyid)
            ->where('paycode', $row->rev_code)
            ->whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
            ->first();

         $reportData[] = [
            'category' => 'Tax Summary',
            'rev_code' => $row->rev_code,
            'Name' => $row->name,
            'short_name' => $row->name,
            'Today' => $today ? $today->Today : 0,
            'MTD' => $mtd ? $mtd->MTD : 0,
            'YTD' => $ytd ? $ytd->YTD : 0
         ];
      }

      $deposit = Revmast::select('rev_code', 'name')->where('propertyid', $this->propertyid)->whereNot('nature', 'Room')
         ->whereIn('field_type', ['P'])->get();
      $depfield = [];
      foreach ($deposit as $row) {
         $depfield[] = $row->name;

         $mtdstart = $ranges['mtd']['start'];
         $mtdend = $ranges['mtd']['end'];
         $ytdstart = $ranges['ftd']['start'];
         $ytdend = $ranges['ftd']['end'];

         $today = Revmast::leftJoin('paycharge', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('revmast.field_type', 'P')
            ->where('paycharge.paycode', $row->rev_code)
            ->where('paycharge.vdate', $fordate)
            ->whereNotIn('paycharge.docid', function ($query) use ($fordate, $depfield) {
               $query->select('docid')
                  ->from('paycharge')
                  ->where('vtype', 'CHK')
                  ->whereIn('paytype', $depfield)
                  ->where('vdate', $fordate);
            })
            ->selectRaw('SUM(paycharge.amtcr) - SUM(paycharge.amtdr) AS Today')
            ->first();

         $mtd = Revmast::leftJoin('paycharge', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('revmast.field_type', 'P')
            ->where('paycharge.paycode', $row->rev_code)
            ->whereBetween('paycharge.vdate', [$mtdstart, $mtdend])
            ->whereNotIn('paycharge.docid', function ($query) use ($mtdstart, $mtdend, $depfield) {
               $query->select('docid')
                  ->from('paycharge')
                  ->where('vtype', 'CHK')
                  ->whereIn('paytype', $depfield)
                  ->whereBetween('paycharge.vdate', [$mtdstart, $mtdend]);
            })
            ->selectRaw('SUM(paycharge.amtcr) - SUM(paycharge.amtdr) AS MTD')
            ->first();

         $ytd = Revmast::leftJoin('paycharge', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('revmast.field_type', 'P')
            ->where('paycharge.paycode', $row->rev_code)
            ->whereBetween('paycharge.vdate', [$ytdstart, $ytdend])
            ->whereNotIn('paycharge.docid', function ($query) use ($ytdstart, $ytdend, $depfield) {
               $query->select('docid')
                  ->from('paycharge')
                  ->where('vtype', 'CHK')
                  ->whereIn('paytype', $depfield)
                  ->whereBetween('paycharge.vdate', [$ytdstart, $ytdend]);
            })
            ->selectRaw('SUM(paycharge.amtcr) - SUM(paycharge.amtdr) AS YTD')
            ->first();

         $reportData[] = [
            'category' => 'Payment Summary',
            'rev_code' => $row->rev_code,
            'Name' => $row->name,
            'short_name' => $row->name,
            'Today' => $today ? $today->Today : 0,
            'MTD' => $mtd ? $mtd->MTD : 0,
            'YTD' => $ytd ? $ytd->YTD : 0
         ];
      }

      $occupancy = RoomCat::select('norooms', 'cat_code AS roomcat', 'name as roomcatname', 'shortname')->where('type', 'RO')->where('propertyid', $this->propertyid)
         ->orderBy('name', 'ASC')->get();

      foreach ($occupancy as $row) {
         $today = Paycharge::selectRaw("count('roomno') AS Today")
            ->where('roomcat', $row->roomcat)
            ->where('vdate', $fordate)
            ->whereIn('paycode', function ($query) {
               $query->select('rev_code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('field_type', 'C')
                  ->where('nature', 'Room Charge');
            })
            ->where('amtdr', '>', 0)
            ->where('propertyid', $this->propertyid)
            ->first();

         $mtd = Paycharge::selectRaw("count('roomno') AS MTD")
            ->where('roomcat', $row->roomcat)
            ->whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
            ->whereIn('paycode', function ($query) {
               $query->select('rev_code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('field_type', 'C')
                  ->where('nature', 'Room Charge');
            })
            ->where('amtdr', '>', 0)
            ->where('propertyid', $this->propertyid)
            ->first();

         $ytd = Paycharge::selectRaw("count('roomno') AS YTD")
            ->where('roomcat', $row->roomcat)
            ->whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
            ->whereIn('paycode', function ($query) {
               $query->select('rev_code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('field_type', 'C')
                  ->where('nature', 'Room Charge');
            })
            ->where('amtdr', '>', 0)
            ->where('propertyid', $this->propertyid)
            ->first();

         $reportData[] = [
            'totalrooms' => $row->norooms,
            'category' => "Room Category",
            'rev_code' => $row->roomcat,
            'Name' => $row->roomcatname,
            'short_name' => $row->roomcatname,
            'Today' => $today ? $today->Today : 0,
            'MTD' => $mtd ? $mtd->MTD : 0,
            'YTD' => $ytd ? $ytd->YTD : 0
         ];
      }

      foreach ($occupancy as $row) {
         $today = Paycharge::selectRaw('COUNT(roomno) as todaycount, SUM(amtdr - amtcr) as Today')
            ->where('roomcat', $row->roomcat)
            ->where('vdate', $fordate)
            ->where('propertyid', $this->propertyid)
            ->where('amtdr', '>', 0)
            ->whereIn('paycode', function ($query) {
               $query->select('rev_Code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('Field_Type', 'C')
                  ->where('Nature', 'Room Charge');
            })
            ->first();

         $mtd = Paycharge::selectRaw('COUNT(roomno) as mtdcount, SUM(amtdr - amtcr) as MTD')
            ->where('roomcat', $row->roomcat)
            ->whereBetween('vdate', [$ranges['mtd']['start'], $ranges['mtd']['end']])
            ->where('propertyid', $this->propertyid)
            ->where('amtdr', '>', 0)
            ->whereIn('paycode', function ($query) {
               $query->select('rev_Code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('Field_Type', 'C')
                  ->where('Nature', 'Room Charge');
            })
            ->first();

         $ytd = Paycharge::selectRaw('COUNT(roomno) as ytdcount, SUM(amtdr - amtcr) as YTD')
            ->where('roomcat', $row->roomcat)
            ->whereBetween('vdate', [$ranges['ftd']['start'], $ranges['ftd']['end']])
            ->where('propertyid', $this->propertyid)
            ->where('amtdr', '>', 0)
            ->whereIn('paycode', function ($query) {
               $query->select('rev_Code')
                  ->from('revmast')
                  ->where('flag_type', 'FOM')
                  ->where('Field_Type', 'C')
                  ->where('Nature', 'Room Charge');
            })
            ->first();

         $reportData[] = [
            'totalrooms' => $row->norooms,
            'category' => "Room Average",
            'rev_code' => $row->roomcat,
            'Name' => $row->roomcatname,
            'short_name' => $row->roomcatname,
            'todaycount' => $today->todaycount ?? 0,
            'mtdcount' => $mtd->mtdcount ?? 0,
            'ytdcount' => $ytd->ytdcount ?? 0,
            'Today' => $today->Today ?? 0,
            'MTD' => $mtd->MTD ?? 0,
            'YTD' => $ytd->YTD ?? 0
         ];
      }

      // Company Query

      $companydatatoday = DB::table('paycharge as P')
         ->leftJoin('revmast as PY', 'P.paycode', '=', 'PY.rev_code')
         ->leftJoin('guestfolio as G', 'P.folionodocid', '=', 'G.docid')
         ->leftJoin('subgroup as S', 'P.comp_code', '=', 'S.sub_code')
         ->leftJoin(DB::raw("(SELECT DISTINCT folionoDocid, billno FROM paycharge WHERE propertyid = $this->propertyid AND amtdr <> 0 AND (modeset IS NULL OR modeset = '')) AS B"), 'P.folionodocid', '=', 'B.folionoDocid')
         ->select([
            'P.docid',
            'P.vtype',
            'P.vno',
            'P.msno1',
            'P.foliono AS foliono',
            'P.folionodocid',
            'S.name AS name',
            'P.paytype AS paycode',
            'P.amtcr AS amount',
            'B.billno'
         ])
         ->where(function ($query) {
            $query->whereIn('P.vtype', ['ARRES', 'ADRES', 'AWRES'])
               ->orWhere(function ($query) {
                  $query->whereNotIn('P.vtype', ['ARRES', 'ADRES', 'AWRES'])
                     ->where(function ($subquery) {
                        $subquery->whereNull('P.contraid')
                           ->orWhere('P.contraid', '=', '');
                     });
               });
         })
         ->where([
            ['P.vdate', '=', $fordate],
            ['P.modeset', '=', 'S'],
            ['P.propertyid', '=', $this->propertyid],
            ['P.restcode', '=', 'FOM' . $this->propertyid],
            ['PY.field_type', '=', 'P'],
            ['P.paytype', '=', 'Company']
         ])
         ->where('P.vtype', '<>', 'CHK')
         ->orderBy('S.name')
         ->orderBy('P.folionodocid')
         ->orderBy('P.foliono')
         ->orderBy('P.vtype')
         ->orderBy('P.vno')
         ->get();

      foreach ($companydatatoday as $row) {
         $reportData[] = [
            'category' => "CompanyData",
            'Name' => $row->name,
            'billno' => 'FOM/' . $row->billno,
            'amount' => $row->amount ?? 0,
         ];
      }

      $poscodes = [];
      $depname = [];
      $companypos = Depart::selectRaw("'' AS opt, name AS outlet, dcode")
         ->where('propertyid', $this->propertyid)
         ->whereIn('rest_type', ['Outlet', 'Room Service'])
         ->whereIn('pos', ['Y'])
         ->orderBy('name')
         ->get();

      foreach ($companypos as $row) {
         $poscodes[] = $row->dcode;
         $depname[] = $row->outlet;
      }

      $companyposa = DB::table('paycharge as P')
         ->leftJoin('subgroup as S', 'P.comp_code', '=', 'S.sub_code')
         ->select([
            'P.vno as billno',
            'P.vtype',
            'P.paytype',
            'S.name',
            'P.restcode',
            DB::raw('(P.amtcr - P.amtdr) AS amount')
         ])
         ->where('P.propertyid', $this->propertyid)
         ->whereIn('P.restcode', $poscodes)
         ->whereDate('P.vdate', $fordate)
         ->where('P.paytype', 'Company')
         ->orderBy('S.name')
         ->get();

      foreach ($companyposa as $row) {
         $key = array_search($row->restcode, $poscodes);
         $departmentName = $key !== false ? $depname[$key] : '';

         $reportData[] = [
            'category' => "CompanyData",
            'Name' => $row->name,
            'billno' =>  $departmentName . '/' . $row->billno,
            'amount' => $row->amount ?? 0,
         ];
      }


      $data = [
         'occupancy' => $occupancy,
         'taxp' => $taxp,
         'ranges' => $ranges,
         'reportData' => $reportData,
         'poscodes' => $poscodes,
      ];

      return json_encode($data);
   }

   public function dailyreportprint(Request $request)
   {
      $permission = revokeopen(191212);
      if (is_null($permission) || $permission->print == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $comp = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');

      return view('property.dailyreportprint', [
         'comp' => $comp,
         'statename' => $statename
      ]);
   }

   public function lookuprromtype(Request $request)
   {
      $permission = revokeopen(131212);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $resstatus = Bookings::where('Property_ID', $this->propertyid)->groupBy('ResStatus')->get();
      $comp = DB::table('company')->where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
      return view('property.lookuprromtype', [
         'comp' => $comp,
         'statename' => $statename,
         'fromdate' => $this->ncurdate,
         'resstatus' => $resstatus
      ]);
   }

   public function lookuproomtypefetch(Request $request)
   {
      $fromdate = $request->input('fromdate');
      $resstatus = $request->input('resstatus');
      $todate = date('Y-m-d', strtotime($fromdate . ' +21 days'));

      $roomcategories = RoomCat::select('cat_code', 'name', 'norooms')
         ->where('propertyid', $this->propertyid)
         ->where('inclcount', 'y')
         ->orderBy('name')
         ->get();

      $totalrooms = RoomCat::where('propertyid', $this->propertyid)
         ->where('inclcount', 'y')
         ->sum('norooms');

      $results = [];

      foreach ($roomcategories as $category) {
         $dailyBusyCounts = [];
         $currentDate = $fromdate;

         while (strtotime($currentDate) <= strtotime($todate)) {
            $norooms = $category->norooms;
            // $busyrooms_grp = GrpBookinDetail::select(DB::raw('sum(roomdet) as troombusy'))
            //     ->leftJoin('guestfolio as gf', function ($join) {
            //         $join->on('gf.bookingdocid', '=', 'grpbookingdetails.ContraDocId')
            //             ->on('gf.bookingsno', '=', 'grpbookingdetails.Sno');
            //     })
            //     ->leftJoin('roomocc as ro', 'ro.docid', '=', 'gf.docid')
            //     ->where('grpbookingdetails.RoomCat', $category->cat_code)
            //     ->where('grpbookingdetails.Property_ID', $this->propertyid)
            //     ->whereDate('grpbookingdetails.ArrDate', '<=', $currentDate)
            //     ->whereDate('grpbookingdetails.DepDate', '>', $currentDate)
            //     ->where('grpbookingdetails.Cancel', 'N')
            //     ->whereNull('ro.chkindate')
            //     ->where('gf.propertyid', $this->propertyid)
            //     ->where('ro.propertyid', $this->propertyid)
            //     ->value('troombusy') ?? 0;


            $busyrooms_grp = GrpBookinDetail::where('Property_ID', $this->propertyid)
               ->whereDate('ArrDate', '<=', $currentDate)
               ->whereDate('DepDate', '>', $currentDate)
               ->where('RoomCat', $category->cat_code)
               ->where('ContraDocId', '')
               ->where('Cancel', 'N')
               ->sum('RoomDet');

            $busyrooms_occ = RoomOcc::where('propertyid', $this->propertyid)
               ->where('roomcat', $category->cat_code)
               ->where('roomtype', 'ro')
               ->whereDate('chkindate', '<=', $currentDate)
               ->whereDate('depdate', '>', $currentDate)
               ->whereNull('type')
               ->count();

            // $dailyBusyCounts[$currentDate] = $busyrooms_grp + $busyrooms_occ;
            $dailyBusyCounts[$currentDate] = $norooms - ($busyrooms_grp + $busyrooms_occ);
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
         }

         $results[] = [
            'category' => $category->name,
            'cat_code' => $category->cat_code,
            'daily_busy_counts' => $dailyBusyCounts,
            'busyrooms_grp' => $busyrooms_grp,
            'busyrooms_occ' => $busyrooms_occ
         ];
      }

      $data = [
         'roomcategories' => $results,
         'totalrooms' => $totalrooms,
      ];

      return response()->json($data);
   }

   public function nckotreport(Request $request)
   {
      $permission = revokeopen(171715);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.nckotreport', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }
   public function nckotreportfetch(Request $request)
   {
      $fromdate = $request->fromdate;

      $todate = $request->todate;
      $kotdata = DB::table('kot AS K')
         ->leftJoin('sale1 AS S', 'K.contradocid', '=', 'S.docid')
         ->leftJoin('depart AS D', 'K.restcode', '=', 'D.dcode')
         ->leftJoin('itemmast AS I', function ($join) {
            $join->on('K.item', '=', 'I.Code')
               ->on('K.itemrestcode', '=', 'I.RestCode');
         })
         ->leftJoin('nctype_mast AS nc', 'K.nctype', '=', 'nc.ncode')
         ->select([
            'K.vno AS KOTNO',
            'K.vdate',
            'K.vtime',
            'nc.ncode',
            'K.restcode',
            'K.roomno',
            'K.qty',
            'K.rate',
            'K.amount',
            'D.name as DEPARTNAME',
            'I.Name as ITEMNAME',
            'K.ncreason AS Reason',
            'K.voidyn AS VoidYN',
            'K.u_name AS UserName',
         ])
         ->where('K.propertyid', $this->propertyid)
         ->where('K.voidyn', 'N')
         ->where('K.nckot', 'Y')
         ->whereBetween('K.VDate', [$fromdate, $todate])
         ->orderBy('K.restcode')
         ->orderBy('K.vdate')
         ->orderBy('K.vno')
         ->orderBy('nc.ncode')
         ->get();

      return json_encode($kotdata);
   }

   public function advresreport(Request $request)
   {
      $permission = revokeopen(131213);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.advresreport', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }

   public function advresreportfetch(Request $request)
   {
      // Validate inputs
      $request->validate([
         'fromdate' => 'required|date',
         'todate' => 'required|date'
      ]);

      $fromdate = $request->fromdate;
      $todate = $request->todate;


      $bookingData = DB::table('booking AS B')
         ->leftJoin('guestprof AS GF', 'B.guestprof', '=', 'GF.guestcode')
         ->leftJoin('grpbookingdetails AS G', 'B.DocId', '=', 'G.BookingDocid')
         ->leftJoin('paycharge AS PC', 'B.DocId', '=', 'PC.refdocid')
         ->leftJoin('revmast AS RM', 'PC.paycode', '=', 'RM.rev_code')
         ->leftJoin('subgroup AS SU', 'B.Company', '=', 'SU.sub_code')
         ->leftJoin('subgroup AS ST', 'B.TravelAgency', '=', 'ST.sub_code')
         ->select([
            'B.DocId',
            'B.BookNo AS ResNo',
            'PC.vno AS Reciptno',
            'B.ResStatus AS Status',
            DB::raw("CASE 
                        WHEN PC.vtype = 'ADRES' THEN 'Advance' 
                        WHEN PC.vtype = 'ARRES' THEN 'Refund' 
                        ELSE 'Other' 
                    END AS PaymentType"),
            'B.vdate as ResDate',
            'GF.name as GuestName',
            'G.arrDate as ArrivalDate',
            'G.DepDate as Depdate',
            'PC.amtcr as Amount',
            'RM.name as PMode',
            'SU.name as Company',
            'ST.Name as TravelAgent',
            'PC.u_name',
         ])
         ->whereExists(function ($query) use ($fromdate, $todate) {
            $query->select(DB::raw(1))
               ->from('paycharge AS PC')
               ->whereColumn('PC.refdocid', 'B.DocId')
               ->whereBetween('PC.vdate', [$fromdate, $todate])
               ->where('PC.propertyid', $this->propertyid)
               ->whereIn('PC.vtype', ['ARRES', 'ADRES']);
         })
         ->groupBy('PC.vno')
         ->orderBy('PC.vdate', 'ASC')
         ->orderBy('PC.vno', 'ASC')
         ->get();

      return response()->json($bookingData);
   }


   public function expectedcheckout(Request $request)
   {
      $permission = revokeopen(141216);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.expectedcheckout', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }
   public function expectedcheckoutfetch(Request $request)
   {
      $fromdate = $request->fromdate;

      $todate = $request->todate;
      $checkoutData =  DB::table('guestfolio')
         ->select([
            'guestfolio.docid',
            'guestfolio.folio_no as FolioNo',
            'roomocc.roomno',
            'guestfolio.name',
            'roomocc.adult as PAX',
            'roomocc.deptime as ExpTime',
            'guestfolio.vdate as ChechINDate',
            DB::raw("CONCAT(roomocc.depdate, ' ', roomocc.deptime) AS Depdate"),
            'subgroup.name as CompanyName'
         ])
         ->join('roomocc', 'guestfolio.docid', '=', 'roomocc.docid')
         ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'guestfolio.Company')
         ->where('guestfolio.propertyid', $this->propertyid)
         ->whereNull('roomocc.chkoutdate')
         ->whereBetween('roomocc.depdate', [$fromdate, $todate])
         ->orderBy('roomocc.roomno')
         ->get();


      return json_encode($checkoutData);
   }

   public function focc_report(Request $request)
   {
      $permission = revokeopen(191211);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.focc_report', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }

   public function foccamount(Request $request)
   {
      $date = $request->date;
      $focc = Focc::where('propertyid', $this->propertyid)->where('vdate', $date)->first();

      return response()->json([$focc]);
   }

   public function foccreportprint(Request $request)
   {
      $permission = revokeopen(191211);
      if (is_null($permission) || $permission->print == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $comp = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');

      return view('property.foccreportprint', [
         'comp' => $comp,
         'statename' => $statename
      ]);
   }


   public function focc_reportfetch(Request $request)
   {
      $fordate = $request->input('fordate');
      $interestamount = $request->input('interestamount');

      $invenv = EnviroGeneral::where('propertyid', $this->propertyid)->first();
      if (!$invenv) {
         return response()->json([
            'status' => 'error',
            'message' => 'Please Define Enviro Inventory First'
         ]);
      }

      if ($interestamount != '0') {
         $chkfocc = Focc::where('propertyid', $this->propertyid)->where('vdate', $fordate)->first();

         if ($chkfocc) {
            $data = [
               'interestamount' => $interestamount,
               'u_updatedt' => $this->currenttime
            ];

            Focc::where('propertyid', $this->propertyid)->where('vdate', $fordate)->update($data);
         } else {
            $data = [
               'vdate' => $fordate,
               'propertyid' => $this->propertyid,
               'interestamount' => $interestamount,
               'u_entdt' => $this->currenttime
            ];

            Focc::insert($data);
         }
      }

      $frontofcsum = 0.00;
      $outletsum = 0.00;
      $banquetsum = 0.00;
      $miscolsum = 0.00;
      $misexpsum = 0.00;
      $compsum = 0.00;
      $othersum = 0.00;

      $fomoffice = DB::table('paycharge AS P')
         ->select([
            'P.docid',
            'G.name AS GuestName',
            'P.vtype',
            'P.vno AS RectNo',
            'FO.billno AS Billno',
            'P.foliono AS FolioNo',
            'P.folionodocid',
            'P.roomno AS Roomno',
            'P.paytype',
            'P.amtcr AS Amount',
         ])
         ->leftJoin('revmast AS PY', function ($join) {
            $join->on('P.paycode', '=', 'PY.rev_code')
               ->where('PY.field_type', '=', 'P')
               ->where('PY.propertyid', $this->propertyid);
         })
         ->leftJoin('guestprof AS G', function ($join) {
            $join->on('P.guestprof', '=', 'G.guestcode')
               ->where('G.propertyid', $this->propertyid);;
         })
         ->leftJoin('fombilldetails AS FO', function ($join) {
            $join->on('P.folionodocid', '=', 'FO.folionodocid')
               ->where('FO.propertyid', $this->propertyid);
         })
         ->whereDate('P.vdate', $fordate)
         ->where('P.propertyid', $this->propertyid)
         ->where('P.restcode', 'FOM' . $this->propertyid)
         ->whereIn('P.paytype', ['Cash', 'Cash In Hand'])
         ->whereIn('P.vtype', ['ADRES', 'REC'])
         ->groupBy([
            'P.vno'
         ])
         ->orderBy('P.folionodocid')
         ->orderBy('P.foliono')
         ->orderBy('P.vtype')
         ->orderBy('P.vno')
         ->get();

      $reportdata = [];

      foreach ($fomoffice as $row) {
         $frontofcsum += $row->Amount;
         $reportdata[] = [
            'frontoffice' => 'Y',
            'guestname' => $row->GuestName,
            'rectno' => $row->RectNo,
            'foliono' => $row->FolioNo,
            'billno' => $row->Billno,
            'roomno' => $row->Roomno,
            'amount' => $row->Amount
         ];
      }

      $depart = Depart::where('propertyid', $this->propertyid)
         ->whereIn('rest_type', ['Room Service', 'Outlet'])
         ->orderBy('name', 'ASC')
         ->get();

      $departCodes = $depart->pluck('dcode')->toArray();

      $pos = DB::table('sale1 as S')
         ->selectRaw('
        MAX(S.docid) AS saledocid,
        SUM(S.netamt) AS TotalNetAmount,
        MAX(PC.paycode) AS MaxPayCode,
        MAX(PC.paytype) AS MaxPayType,
        MAX(D.name) AS DepartName,
        SUM(PC.amtcr) - SUM(PC.amtdr) AS Amount
    ')
         ->leftJoin('paycharge as PC', function ($join) use ($fordate, $departCodes) {
            $join->on('S.docid', '=', 'PC.docid')
               ->where('PC.paytype', '=', 'Cash')
               ->whereNotIn('PC.paycode', ['TOUT' . $this->propertyid])
               ->whereIn('PC.restcode', $departCodes)
               ->where('PC.vdate', '=', $fordate);
         })
         ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
         ->where('S.propertyid', '=', $this->propertyid)
         ->groupBy('S.restcode')
         ->get();

      foreach ($pos as $row) {
         $outletsum += $row->Amount;
         $reportdata[] = [
            'pos' => 'Y',
            'outlet' => $row->DepartName,
            'amount' => $row->Amount,
            'docid' => $row->saledocid
         ];
      }

      $banquet = DB::table('hallsale1 as S')
         ->selectRaw('
         MAX(S.docid) AS banqdocid,
         S.vno as billno,
         S.party,
         SUM(S.netamt) AS TotalNetAmount,
         MAX(PC.paycode) AS MaxPayCode,
         MAX(PC.paytype) AS MaxPayType,
         MAX(D.name) AS DepartName,
         SUM(PC.amtcr) - SUM(PC.amtdr) AS Amount
      ')
         ->leftJoin('paychargeh as PC', function ($join) use ($fordate, $departCodes) {
            $join->on('S.docid', '=', 'PC.docid')
               ->where('PC.paytype', '=', 'Cash')
               ->whereNotIn('PC.paycode', ['TOUT' . $this->propertyid])
               ->where('PC.restcode', "BANQ$this->propertyid")
               ->where('PC.vdate', '=', $fordate);
         })
         ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
         ->where('S.propertyid', '=', $this->propertyid)
         ->groupBy('S.docid')
         ->get();

      foreach ($banquet as $row) {
         $banquetsum += $row->Amount;
         $reportdata[] = [
            'banquet' => 'Y',
            'outlet' => $row->party . ' (Settlement)',
            'amount' => $row->Amount,
            'docid' => $row->banqdocid,
            'billno' => $row->billno
         ];
      }

      $banquet2 = DB::table('hallbook as S')
         ->selectRaw('
         MAX(S.docid) AS banqdocid,
         S.vno as billno,
         S.partyname,
         MAX(PC.paycode) AS MaxPayCode,
         MAX(PC.paytype) AS MaxPayType,
         SUM(PC.amtcr) - SUM(PC.amtdr) AS Amount
      ')
         ->leftJoin('paychargeh as PC', function ($join) use ($fordate, $departCodes) {
            $join->on('S.docid', '=', 'PC.contradocid')
               ->where('PC.paytype', '=', 'Cash')
               ->whereNotIn('PC.paycode', ['TOUT' . $this->propertyid])
               ->where('PC.restcode', "BANQ$this->propertyid")
               ->where('PC.vdate', '=', $fordate);
         })
         ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
         ->where('S.propertyid', '=', $this->propertyid)
         ->groupBy('PC.paycode')
         ->groupBy('PC.amtcr')
         ->get();

      foreach ($banquet2 as $row) {
         $banquetsum += $row->Amount;
         $reportdata[] = [
            'banquet' => 'Y',
            'advance' => 'Y',
            'outlet' => $row->partyname . ' (Advance)',
            'amount' => $row->Amount,
            'docid' => $row->banqdocid,
            'billno' => $row->billno
         ];
      }

      $misccol = DB::table('expsheet')
         ->select([
            'expsheet.vno as Vouncherno',
            'subgroup.name as ACName',
            'expsheet.cramt as Amount',
            'expsheet.remark'
         ])
         ->leftJoin('subgroup', 'expsheet.drac', '=', 'subgroup.sub_code')
         ->where('expsheet.vtype', 'HTSAL')
         ->where('expsheet.VDate', $fordate)
         ->where('subgroup.nature', 'Cash')
         ->whereNot('expsheet.delflag', 'Y')
         ->where('expsheet.propertyid', $this->propertyid)
         ->orderBy('ACName')
         ->get();

      foreach ($misccol as $row) {
         $miscolsum += $row->Amount;
         $reportdata[] = [
            'miscy' => 'Y',
            'acname' => $row->ACName,
            'voucherno' => $row->Vouncherno,
            'amount' => $row->Amount
         ];
      }

      if ($invenv->cashpurcheffect == 'Y') {
         $expsheetData = DB::table('expsheet as e')
            ->leftJoin('subgroup as s', 'e.drac', '=', 's.sub_code')
            ->select(
               'e.vno as Vouncherno',
               's.name as ACName',
               'e.dramt as Amount',
               'e.remark'
            )
            ->where('e.Vtype', 'HTEXP')
            ->whereNot('e.delflag', 'Y')
            ->where('e.vdate', $fordate)
            ->where('e.propertyid', $this->propertyid);

         $purchData = DB::table('purch1 as p')
            ->selectRaw('NULL as Vouncherno, "Cash Purchase" as ACName, SUM(p.NetAmt) as Amount, NULL as Remark')
            ->where('p.vdate', $fordate)
            ->where('p.propertyid', $this->propertyid)
            ->where('p.vtype', 'PBPC');

         $miscexpense = $expsheetData->unionAll($purchData)
            ->orderBy('ACName')
            ->get();
      } else {
         $miscexpense = DB::table('expsheet as e')
            ->leftJoin('subgroup as s', 'e.drac', '=', 's.sub_code')
            ->select(
               'e.vno as Vouncherno',
               's.name as ACName',
               'e.dramt as Amount',
               'e.remark'
            )
            ->where('e.Vtype', 'HTEXP')
            ->where('e.VDate', $fordate)
            ->whereNot('e.delflag', 'Y')
            ->where('e.propertyid', $this->propertyid)
            ->orderBy('s.name')
            ->get();
      }

      foreach ($miscexpense as $row) {
         $misexpsum += $row->Amount;
         $reportdata[] = [
            'miscx' => 'Y',
            'acname' => $row->ACName,
            'voucherno' => $row->Vouncherno,
            'amount' => $row->Amount,
            'remark' => $row->remark
         ];
      }

      $companyrec = DB::table('paycharge as P')
         ->leftJoin('revmast as PY', 'P.PayCode', '=', 'PY.rev_code')
         ->leftJoin('subgroup as S', 'P.comp_code', '=', 'S.Sub_Code')
         ->leftJoin('fombilldetails as FO', 'P.folionodocid', '=', 'FO.folionodocid')
         ->select([
            'P.vno as vno',
            'P.foliono as foliono',
            'FO.billno as billno',
            'S.name as compname',
            'P.amtcr as amount'
         ])
         ->where(function ($query) {
            $query->where(function ($q) {
               $q->whereIn('P.VTYPE', ['ARRES', 'ADRES', 'AWRES'])
                  ->where('P.propertyid', $this->propertyid);
            })
               ->orWhere(function ($q) {
                  $q->whereNotIn('P.VTYPE', ['ARRES', 'ADRES', 'AWRES'])
                     ->where(function ($sub) {
                        $sub->whereNull('P.contraid')
                           ->orWhere('P.contraid', '');
                     });
               });
         })
         ->where('P.Vdate', $fordate)
         ->where('P.modeset', 'S')
         ->where('P.RESTCODE', 'FOM' . $this->propertyid)
         ->where('PY.Field_Type', 'P')
         ->where('P.PayType', 'Company')
         ->where('P.Vtype', '<>', 'CHK')
         ->groupBy('FO.billno')
         ->orderBy('S.Name')
         ->orderBy('P.FOLIONODOCID')
         ->orderBy('P.FOLIONO')
         ->orderBy('P.VTYPE')
         ->orderBy('P.VNO')
         ->get();

      foreach ($companyrec as $row) {
         $compsum += $row->amount;
         $reportdata[] = [
            'comp' => 'Y',
            'compname' => $row->compname,
            'vno' => $row->vno,
            'billno' => $row->billno,
            'foliono' => $row->foliono,
            'amount' => $row->amount
         ];
      }

      $otherpay = DB::table('paycharge as P')
         ->select(
            'P.vno as vno',
            DB::raw('MAX(P.foliono) as foliono'),
            'booking.BookNo as resno',
            'FO.billno as billno',
            'G.name as guestname',
            'P.paytype as paymode',
            'P.restcode',
            'P.vtype',
            'P.vno',
            DB::raw('SUM(P.amtcr) as amount')
         )
         ->leftJoin('revmast as PY', 'P.PayCode', '=', 'PY.rev_code')
         ->leftJoin('booking', 'booking.DocId', '=', 'P.refdocid')
         ->leftJoin('guestprof AS G', function ($join) {
            $join->on('P.guestprof', '=', 'G.guestcode');
         })
         // ->leftJoin('fombilldetails as FO', function ($join) {
         //    $join->on('P.folionodocid', '=', 'FO.folionodocid')
         //       ->on('P.sno1', '=', 'FO.sno1');
         // })
         ->leftJoin('fombilldetails as FO', function ($join) {
            $join->on('P.folionodocid', '=', 'FO.folionodocid')
               ->whereRaw("FO.sno1 = CASE WHEN P.msno1 = 0 THEN P.sno1 ELSE P.msno1 END")
               ->where('FO.status', 'settle');
         })
         ->where(function ($query) {
            $query->where(function ($q) {
               $q->whereIn('P.VTYPE', ['ARRES', 'ADRES', 'AWRES'])
                  ->where('P.propertyid', $this->propertyid);
            })
               ->orWhere(function ($q) {
                  $q->whereNotIn('P.VTYPE', ['ARRES', 'ADRES', 'AWRES'])
                     ->where(function ($sub) {
                        $sub->whereNull('P.contraid')
                           ->orWhere('P.contraid', '');
                     });
               });
         })
         ->whereDate('P.vdate', $fordate)
         ->where('P.propertyid', $this->propertyid)
         ->where('PY.Field_Type', 'P')
         ->whereIn('P.PayType', [
            'UPI',
            'Credit Card',
            'Cheque',
            'Hold',
            'Complementary',
            'Staff',
            'Other'
         ])
         ->where('P.Vtype', '<>', 'CHK')
         ->groupBy('P.folionodocid')
         ->groupBy('P.vno')
         ->groupBy('FO.sno1')
         ->orderBy('P.paytype')
         ->orderBy('FO.billno')
         ->get();

      foreach ($otherpay as $row) {

         if ($row->restcode != 'FOM' . $this->propertyid) {
            $row->billno = $row->vtype . ' / ' . $row->vno;
         }

         $othersum += $row->amount;
         $reportdata[] = [
            'otherpay' => 'Y',
            'guestname' => $row->guestname,
            'vno' => $row->vno,
            'billno' => $row->billno,
            'foliono' => $row->foliono . ' / ' . $row->resno,
            'amount' => $row->amount,
            'paymode' => $row->paymode
         ];
      }

      $misccolotherpay = DB::table('expsheet')
         ->select([
            'expsheet.vno as Vouncherno',
            'subgroup.nature as paymode',
            'subgroup.name as ACName',
            'expsheet.cramt as Amount',
            'expsheet.remark'
         ])
         ->leftJoin('subgroup', 'expsheet.drac', '=', 'subgroup.sub_code')
         ->where('expsheet.vtype', 'HTSAL')
         ->where('expsheet.VDate', $fordate)
         ->whereNot('subgroup.nature', 'Cash')
         ->where('expsheet.propertyid', $this->propertyid)
         ->orderBy('ACName')
         ->get();

      foreach ($misccolotherpay as $row) {
         $othersum += $row->Amount;
         $reportdata[] = [
            'otherpay' => 'Y',
            'paymode' => $row->paymode,
            'vno' => $row->Vouncherno,
            'amount' => $row->Amount,
            'guestname' => $row->ACName,
            'billno' => '',
            'foliono' => ''
         ];
      }

      $otherpaybanq = DB::table('paychargeh as P')
         ->select(
            'P.vno as vno',
            'hallbook.partyname as partyname',
            'hallbook.vno as billno',
            'P.paytype as paymode',
            'P.restcode',
            'P.vtype',
            'P.vno',
            DB::raw('SUM(P.amtcr) as amount')
         )
         ->leftJoin('revmast as PY', 'P.PayCode', '=', 'PY.rev_code')
         ->Join('hallbook', 'hallbook.docid', '=', 'P.contradocid')
         ->whereDate('P.vdate', $fordate)
         ->where('P.propertyid', $this->propertyid)
         ->where('PY.Field_Type', 'P')
         ->whereIn('P.PayType', [
            'UPI',
            'Credit Card',
            'Cheque',
            'Hold',
            'Complementary',
            'Staff',
            'Other'
         ])
         ->groupBy('P.paytype')
         ->groupBy('P.amtcr')
         ->get();

      foreach ($otherpaybanq as $row) {

         $othersum += $row->amount;
         $reportdata[] = [
            'otherpay' => 'Y',
            'banq' => 'Y',
            'guestname' => $row->partyname . ' (Advance)',
            'vno' => $row->vno,
            'billno' => '',
            'foliono' => '',
            'amount' => $row->amount,
            'paymode' => $row->paymode
         ];
      }

      $otherpaybanqsale = DB::table('paychargeh as P')
         ->select(
            'P.vno as vno',
            'hallsale1.party as partyname',
            'hallsale1.vno as billno',
            'P.paytype as paymode',
            'P.restcode',
            'P.vtype',
            'P.vno',
            DB::raw('SUM(P.amtcr) as amount')
         )
         ->leftJoin('revmast as PY', 'P.PayCode', '=', 'PY.rev_code')
         ->Join('hallsale1', 'hallsale1.docid', '=', 'P.docid')
         ->whereDate('P.vdate', $fordate)
         ->where('P.propertyid', $this->propertyid)
         ->where('PY.Field_Type', 'P')
         ->whereIn('P.PayType', [
            'UPI',
            'Credit Card',
            'Cheque',
            'Hold',
            'Complementary',
            'Staff',
            'Other'
         ])
         ->groupBy('P.paytype')
         ->groupBy('P.amtcr')
         ->get();

      foreach ($otherpaybanqsale as $row) {

         $othersum += $row->amount;
         $reportdata[] = [
            'otherpay' => 'Y',
            'banq' => 'Y',
            'guestname' => $row->partyname . ' (Settlement)',
            'vno' => $row->vno,
            'billno' => $row->billno,
            'foliono' => '',
            'amount' => $row->amount,
            'paymode' => $row->paymode
         ];
      }

      $totalamount = 0.00;

      foreach ($reportdata as $row) {
         $totalamount += $row['amount'];
      }

      $data =  [
         'reportdata' => $reportdata,
         'departCodes' => $departCodes,
         'totalamount' => $totalamount,
         'frontofcsum' => $frontofcsum,
         'outletsum' => $outletsum,
         'banquetsum' => $banquetsum,
         'miscolsum' => $miscolsum,
         'misexpsum' => $misexpsum,
         'compsum' => $compsum,
         'othersum' => $othersum
      ];

      return json_encode($data);
   }

   public function pendingkotreport(Request $request)
   {
      $permission = revokeopen(171717);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $propertyId = $request->input('propertyid', $this->propertyid);

      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.pendingkotreport', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename,

         'propertyid' => $propertyId

      ]);
   }
   public function pendingkotreportfetch(Request $request)
   {
      $fromdate = $request->fromdate;

      $todate = $request->todate;

      $propertyId = $request->input('propertyid', $this->propertyid);

      $validOutlets = DB::table('depart')
         ->select('dcode')
         ->where('propertyid', $propertyId)
         ->whereIn('Rest_Type', ['Outlet', 'Room Service'])
         ->where('pos', 'Y')
         ->pluck('dcode');

      // Fetching the merged report based on the valid outlets
      $mergedReport = DB::table('kot AS K')
         ->join('server_mast AS W', 'K.waiter', '=', 'W.scode')
         ->join('itemmast AS I', function ($join) {
            $join->on('K.item', '=', 'I.Code')
               ->on('K.restcode', '=', 'I.RestCode');
         })
         ->join('depart AS D', function ($join) use ($propertyId) {
            $join->on('K.restcode', '=', 'D.dcode')
               ->where('D.propertyid', $propertyId)
               ->whereIn('D.Rest_Type', ['Outlet', 'Room Service'])
               ->where('D.pos', 'Y');
         })
         ->join('voucher_type AS V', 'K.vtype', '=', 'V.v_type')
         ->select(
            'D.name AS Outlet',
            'D.dcode',
            'K.vdate AS Date',
            'K.vtime AS Time',
            'K.vno AS KOTno',
            'W.name AS WAITER',
            'K.roomno AS RoomTableNo',
            'K.u_name AS UserName',
            'I.Name AS ItemName',
            'K.qty AS Qty'
         )
         ->where('K.nckot', '<>', 'Y')
         ->whereBetween('K.vdate', [$fromdate, $todate])
         ->whereIn('V.ncat', ['RSKOT'])
         ->where('K.pending', 'Y')
         ->whereRaw("IFNULL(K.delflag, '') <> 'Y'")
         ->where('K.voidyn', '<>', 'Y')
         ->whereIn('K.restcode', $validOutlets)
         ->where('K.propertyid', $propertyId)
         ->groupBy('K.vno')
         ->groupBy('K.item')
         ->orderBy('K.docid')
         ->orderBy('K.restcode')
         ->orderBy('I.Name')
         ->get();

      return response()->json($mergedReport);
   }

   /*$mergedReport = DB::table('kot AS K')
    ->join('server_mast AS W', 'K.waiter', '=', 'W.scode')
    ->join('itemmast AS I', function ($join) {
        $join->on('K.item', '=', 'I.Code')
             ->on('K.restcode', '=', 'I.RestCode');
    })
    ->join('depart AS D', function ($join) {
        $join->on('K.restcode', '=', 'D.dcode')
             ->where('D.propertyid', $this->propertyid)
             ->whereIn('D.Rest_Type', ['Outlet', 'Room Service'])
             ->where('D.pos', 'Y');
    })
    ->join('voucher_type AS V', 'K.vtype', '=', 'V.v_type')
    ->select(
        'D.name AS Outlet',
        'D.dcode',
        'K.vdate AS Date',
        'K.vtime AS Time',
        'K.vno AS KOTno',
        //'D.name AS DEPARTNAME',
        'W.name AS WAITER',
        'K.roomno AS RoomTableNo',
        'K.u_name AS UserName',
        'I.Name AS ItemName',
        'K.qty AS Qty'
    )
    ->where('K.nckot', '<>', 'Y')
    ->whereBetween('K.vdate', [$fromdate, $todate])
    ->whereIn('V.ncat', ['RSKOT' , $this->propertyid])
    ->where('K.pending', 'Y')
    ->whereRaw("IFNULL(K.delflag, '') <> 'Y'")
    ->where('K.voidyn', '<>', 'Y')
    ->whereIn('K.restcode', ['BER108' ,'RS108'])
    ->where('K.propertyid', $this->propertyid)
    ->orderBy('K.docid')
    ->orderBy('K.restcode')
    ->orderBy('I.Name')
    ->get();


   return json_encode($mergedReport);                               

}*/



   public function kotwisedetail(Request $request)
   {
      $permission = revokeopen(171716);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.kotwisedetail', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename,
         'propertyid' => $this->propertyid
      ]);
   }
   public function kotwisedetailfetch(Request $request)
   {
      $fromdate = $request->fromdate;

      $todate = $request->todate;

      $propertyId = $request->input('propertyid', $this->propertyid);


      // Fetching the valid outlets for the given property ID
      $Outlets = DB::table('depart')
         ->select('dcode')
         ->where('propertyid', $propertyId)
         ->whereIn('Rest_Type', ['Outlet', 'Room Service'])
         ->where('pos', 'Y')
         ->pluck('dcode');

      // Fetching the details based on the selected outlets and date range
      $details = DB::table('kot AS K')
         ->select(
            'D.name AS OutLet',
            'K.vdate AS Date',
            'K.roomno AS TableRoomNo',
            'K.vno AS KOTNO',
            'K.vtime AS KotTime',
            'S.vno AS BILLNO',
            'K.qty AS QTY',
            'K.rate AS Rate',
            'K.amount as kotamount',
            'I.Name AS ITEMNAME',
            DB::raw("CASE K.voidyn WHEN 'N' THEN 'No' WHEN 'Y' THEN 'Yes' ELSE '' END AS VoidYN"),
            'KL.VTime AS EditTime',
            'W.Name AS WAITER',
            'K.u_name AS UserName',
            'K.remarks AS Remarks',
            'K.reasons AS Reason'
         )
         ->leftJoin('sale1 AS S', 'K.contradocid', '=', 'S.DocId')
         ->leftJoin('itemmast AS I', function ($join) {
            $join->on('K.item', '=', 'I.Code')
               ->on('K.itemrestcode', '=', 'I.RestCode');
         })
         ->leftJoin('depart AS D', function ($join) use ($propertyId) {
            $join->on('K.restcode', '=', 'D.dcode')
               ->where('D.propertyid', $propertyId)
               ->whereIn('D.Rest_Type', ['Outlet', 'Room Service'])
               ->where('D.pos', 'Y');
         })
         ->leftJoin(DB::raw('(SELECT DocId, MAX(VTime) AS VTime FROM kotlog GROUP BY DocId) AS KL'), 'KL.DocId', '=', 'K.DocId')
         ->leftJoin('server_mast AS W', 'K.waiter', '=', 'W.scode')
         ->where('K.propertyid', $propertyId)
         ->whereNotIn(DB::raw('IFNULL(S.DELFLAG, "")'), ['D', 'Y'])
         ->where('K.nckot', 'N')
         ->whereBetween('K.vdate', [$fromdate, $todate])
         ->whereIn('K.restcode', $Outlets)
         ->orderBy('K.restcode')
         ->orderBy('K.vdate')
         ->orderBy('K.vno')
         ->get();

      return response()->json($details);
   }






   public function roominventory(Request $request)
   {

      $permission = revokeopen(141311);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.roominventory', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename,


      ]);
   }

   public function roominventoryfetch(Request $request)
   {
      $status = $request->status;

      $allrooms = DB::table('room_mast as RM')
         ->join('room_cat as RC', function ($join) {
            $join->on('RM.room_cat', '=', 'RC.cat_code')
               ->on('RM.type', '=', 'RC.type');
         })
         ->select('RM.rcode as ROOMNO', 'RC.Name as RoomCatName')
         ->where([
            ['RM.Type', 'RO'],
            ['RM.InclCount', 'Y'],
            ['RM.propertyid', $this->propertyid]
         ])
         ->orderBy('RM.rcode')
         ->get();

      $occupiedRooms = DB::table('room_mast as RM')
         ->select([
            'RM.rcode as ROOMNO',
            'RC.name as RoomCatName',
            'RO.foliono',
            'RO.sno1',
            'RO.docid',
            'RO.chkindate',
            'RO.type',
            'RO.depdate',
            'RO.adult',
            'RO.children as Child',
            'PL.name as PlanName',
            'RO.roomrate',
            'RO.planamt',
            'GF.name AS GuestName',
            'B.bookedby',
            'BS.name AS MarketSeg',
            'S.name AS CompanyName',
            DB::raw('MAX(RL.rate2) as RackRate')
         ])
         ->join('room_cat as RC', function ($join) {
            $join->on('RM.room_cat', '=', 'RC.cat_code')
               ->on('RM.type', '=', 'RC.type')
               ->where('RC.propertyid', $this->propertyid);
         })
         ->join('roomocc as RO', function ($join) {
            $join->on('RM.rcode', '=', 'RO.roomno')
               ->where('RO.propertyid', $this->propertyid)
               ->whereNull('RO.chkoutdate');
         })
         ->leftJoin('guestfolio as GF', function ($join) {
            $join->on('RO.foliono', '=', 'GF.folio_no')
               ->where('GF.propertyid', $this->propertyid);
         })
         ->leftJoin('booking as B', 'B.DocId', '=', 'GF.bookingdocid')
         ->leftJoin('busssource as BS', function ($join) {
            $join->on('GF.busssource', '=', 'BS.bcode')
               ->where('BS.propertyid', $this->propertyid);
         })
         ->leftJoin('subgroup as S', function ($join) {
            $join->on('GF.company', '=', 'S.sub_code')
               ->where('S.propertyid', $this->propertyid);
         })
         ->leftJoin('plan_mast as PL', function ($join) {
            $join->on('RO.plancode', '=', 'PL.pcode')
               ->where('PL.propertyid', $this->propertyid);
         })
         ->leftJoin('rate_list as RL', function ($join) {
            $join->on('RM.rcode', '=', 'RL.roomno')
               ->where('RL.propertyid', $this->propertyid)
               ->orOn('RM.room_cat', '=', 'RL.room_cat');
         })
         ->where([
            ['RM.type', 'RO'],
            ['RM.inclcount', 'Y'],
            ['RM.propertyid', $this->propertyid]
         ])
         ->groupBy([
            'RM.rcode',
            'RC.name',
            'RO.foliono',
            'RO.chkindate',
            'RO.type',
            'RO.depdate',
            'RO.adult',
            'RO.children',
            'PL.name',
            'RO.roomrate',
            'RO.planamt',
            'GF.name',
            'B.bookedby',
            'BS.name',
            'S.name'
         ])
         ->orderBy('RO.roomno')
         ->orderBy('RM.rcode')
         ->get();

      foreach ($occupiedRooms as $key => $row) {
         $balance = Paycharge::select('sno1', 'folionodocid', DB::raw('SUM(amtdr) - SUM(amtcr) as balanceamt'))
            ->where([
               ['folionodocid', '=', $row->docid],
               ['sno1', '=', $row->sno1]
            ])
            ->value('balanceamt');

         $advanceamt = Paycharge::select(DB::raw('SUM(amtcr) as Advance'))
            ->where([
               ['folionodocid', '=', $row->docid],
               ['sno1', '=', $row->sno1]
            ])
            ->value('Advance');

         // if ($balance !== null || $advanceamt !== null) {
         $row->Advance = $advanceamt ?? 0;
         $row->balanceamt = $balance ?? 0;
         // } 
         // else {
         //     unset($occupiedRooms[$key]);
         // }
      }


      return response()->json([
         'allrooms' => $allrooms,
         'roomdetails' => $occupiedRooms,
         'status' => $status
      ]);
   }

   public function voidbills(Request $request)
   {
      $permission = revokeopen(171812);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      return view('property.voidbills', [
         'ncurdate' => $this->ncurdate,
         'company' => $company,
         'statename' => $statename
      ]);
   }
   public function voidbillsfetch(Request $request)
   {
      $fromdate = $request->fromdate;
      $todate = $request->todate;

      $Outlets = DB::table('depart')
         ->where('propertyid', $this->propertyid)
         ->whereIn('nature', ['Outlet', 'Room Service'])
         ->where('pos', 'Y')
         ->pluck('dcode')
         ->toArray();

      $sales = DB::table('sale1 as S')
         ->leftJoin('depart as D', 'S.restcode', '=', 'D.dcode')
         ->leftJoin('server_mast as W', 'S.waiter', '=', 'W.scode')
         ->leftJoin('room_mast as RM', function ($join) {
            $join->on('S.restcode', '=', 'RM.rest_code');
         })
         ->select([
            'D.name as DEPARTNAME',
            DB::raw("CONCAT_WS('/', S.vtype, S.VNO) AS BillNo"),
            'S.vdate as Date',
            'S.vtime as Time',
            'W.name as WAITER',
            'S.roomno as TableRoomno',
            'S.netamt as NetSale',
            'S.delremark as Remark',
            'S.u_name as UserName'
         ])
         ->where('S.propertyid', $this->propertyid)
         ->whereBetween('S.vdate', [$fromdate, $todate])
         ->where('S.delflag', 'Y')
         ->whereIn('S.restcode', $Outlets)
         // ->whereRaw("COALESCE(RM.type, '') IN ('TB')")
         ->groupBy('S.docid')
         ->orderBy('D.name')
         ->orderBy('S.vno')
         ->get();

      return response()->json($sales);
   }


   public function fomsalesummary(Request $request)
   {
      $permission = revokeopen(141312);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }

      $fromdate = $this->ncurdate;
      $bsource = DB::table('busssource')
         ->where('propertyid', $this->propertyid)
         ->orderBy('name', 'ASC')->get();
      $company = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $company->state_code)->value('name');
      $outlets = Depart::where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name')->get();
      return view('property.fomsalesummary', [
         'fromdate' => $fromdate,
         'statename' => $statename,
         'company' => $company,
         'outlets' => $outlets
      ]);
   }

   public function fetchfomsalesummary(Request $request)
   {
      $fromdate = $request->fromdate;
      $todate = $request->todate;
      $propertyId = $this->propertyid;
      $from = $fromdate;
      $to = $todate;

      $totalRooms = DB::table('room_mast')
         ->where('propertyid', $propertyId)
         ->where('type', 'RO')
         ->where('inclcount', 'Y')
         ->count();

      $paySummary = DB::table('paycharge')
         ->select(
            'vdate',
            DB::raw("SUM(CASE WHEN paycode = 'RMCH$propertyId' THEN 1 ELSE 0 END) as chargableroom"),
            DB::raw("SUM(CASE WHEN paycode = 'RMCH$propertyId' THEN amtdr ELSE 0 END) as roomcharge"),
            DB::raw("SUM(CASE WHEN paycode = 'MEGE$propertyId' THEN amtdr ELSE 0 END) as mealcharge"),
            DB::raw("SUM(CASE WHEN paycode = 'EXTB$propertyId' THEN amtdr ELSE 0 END) as extrabedcharge"),
            DB::raw("SUM(CASE WHEN paycode = 'CGSS$propertyId' THEN amtdr ELSE 0 END) as cgst"),
            DB::raw("SUM(CASE WHEN paycode = 'SGSS$propertyId' THEN amtdr ELSE 0 END) as sgst")
         )
         ->where('propertyid', $propertyId)
         ->whereBetween('vdate', [$from, $to])
         ->groupBy('vdate');

      $data = DB::table('paycharge as p')
         ->joinSub($paySummary, 'summary', function ($join) {
            $join->on('p.vdate', '=', 'summary.vdate');
         })
         ->leftJoin('roomocc', 'roomocc.chkindate', '=', 'p.vdate')
         ->select([
            'p.vdate',
            'p.folionodocid',
            DB::raw("CONCAT(MIN(p.billno), ' to ', MAX(p.billno)) AS billno_range"),
            DB::raw("$totalRooms as totalrooms"),
            DB::raw("summary.chargableroom"),
            DB::raw("($totalRooms - summary.chargableroom) as balance_room"),
            DB::raw("ROUND((summary.chargableroom / NULLIF($totalRooms, 0)) * 100, 2) as roomoccupancy"),
            DB::raw("summary.roomcharge"),
            DB::raw("summary.mealcharge"),
            DB::raw("summary.extrabedcharge"),
            DB::raw("summary.cgst"),
            DB::raw("summary.sgst"),
         ])
         ->where('p.propertyid', $propertyId)
         // ->where('p.billno', '!=', 0)
         ->whereBetween('p.vdate', [$from, $to])
         ->groupBy('p.vdate')
         ->get();

      $outlets = Depart::where('propertyid', $propertyId)
         ->whereIn('nature', ['Room Service', 'Outlet'])
         ->orderBy('name')
         ->get();
      foreach ($data as $row) {
         $vdate = $row->vdate;

         foreach ($outlets as $outlet) {
            $shortName = strtolower($outlet->short_name);
            $restcode = $outlet->dcode;

            $result = DB::table('sale1')
               ->selectRaw('SUM(total) AS total_sum, SUM(discamt) AS discamt_sum')
               ->where('propertyid', $this->propertyid)
               ->where('restcode', $restcode)
               ->where('vdate', $vdate)
               ->where('delflag', 'N')
               ->first();

            $totalSum = $result->total_sum;
            $discamtSum = $result->discamt_sum;
            $row->$shortName = $totalSum - $discamtSum;
         }

         $paycode = 'RMCH' . $this->propertyid;

         $groupColumn = Paycharge::where('propertyid', $this->propertyid)
            ->where('vdate', $vdate)
            ->where('paycode', $paycode)
            ->groupBy('relatedfolionodocid')
            ->exists() ? 'relatedfolionodocid' : 'folionodocid';

         $folioDocIds = DB::table('paycharge')
            ->where('propertyid', $this->propertyid)
            ->where('vdate', $vdate)
            ->where('paycode', $paycode)
            ->groupBy($groupColumn)
            ->pluck($groupColumn)
            ->toArray();

         $roomOccQuery = DB::table('roomocc')
            ->whereIn('docid', $folioDocIds)
            ->where(function ($query) {
               $query->where('type', 'O')->orWhereNull('type');
            });

         $row->adult = $roomOccQuery->sum('adult') ?? 0;
         $row->children = $roomOccQuery->sum('children') ?? 0;
      }

      return $data;
   }

   public function contributionreport(Request $request)
   {
      $permission = revokeopen(141313);
      if (is_null($permission) || $permission->view == 0) {
         return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
      }

      $ncurdate = $this->ncurdate;
      $comp = Companyreg::where('propertyid', $this->propertyid)->first();
      $statename = States::where('propertyid', $this->propertyid)->where('state_code', $comp->state_code)->value('name');
      $years = DateHelper::Uniqueyears($this->propertyid);
      return view('property.contributionreport', [
         'ncurdate' => $ncurdate,
         'statename' => $statename,
         'comp' => $comp,
         'years' => $years
      ]);
   }

   public function fetchcontribuition(Request $request)
   {
      $year = $request->vprefix;
      $month = $request->formonth;
      $type = $request->type;

      $month = str_pad($month, 2, '0', STR_PAD_LEFT);
      $startdate = "$year-$month-01";
      $enddate = date("Y-m-t", strtotime($startdate));

      $data = DB::table('subgroup as sg')
         ->selectRaw("
        sg.sub_code AS compcode,
        sg.name AS company_name,
        SUM(CASE WHEN DAY(p.vdate) = 1 THEN 1 ELSE 0 END) AS `01`,
        SUM(CASE WHEN DAY(p.vdate) = 2 THEN 1 ELSE 0 END) AS `02`,
        SUM(CASE WHEN DAY(p.vdate) = 3 THEN 1 ELSE 0 END) AS `03`,
        SUM(CASE WHEN DAY(p.vdate) = 4 THEN 1 ELSE 0 END) AS `04`,
        SUM(CASE WHEN DAY(p.vdate) = 5 THEN 1 ELSE 0 END) AS `05`,
        SUM(CASE WHEN DAY(p.vdate) = 6 THEN 1 ELSE 0 END) AS `06`,
        SUM(CASE WHEN DAY(p.vdate) = 7 THEN 1 ELSE 0 END) AS `07`,
        SUM(CASE WHEN DAY(p.vdate) = 8 THEN 1 ELSE 0 END) AS `08`,
        SUM(CASE WHEN DAY(p.vdate) = 9 THEN 1 ELSE 0 END) AS `09`,
        SUM(CASE WHEN DAY(p.vdate) = 10 THEN 1 ELSE 0 END) AS `10`,
        SUM(CASE WHEN DAY(p.vdate) = 11 THEN 1 ELSE 0 END) AS `11`,
        SUM(CASE WHEN DAY(p.vdate) = 12 THEN 1 ELSE 0 END) AS `12`,
        SUM(CASE WHEN DAY(p.vdate) = 13 THEN 1 ELSE 0 END) AS `13`,
        SUM(CASE WHEN DAY(p.vdate) = 14 THEN 1 ELSE 0 END) AS `14`,
        SUM(CASE WHEN DAY(p.vdate) = 15 THEN 1 ELSE 0 END) AS `15`,
        SUM(CASE WHEN DAY(p.vdate) = 16 THEN 1 ELSE 0 END) AS `16`,
        SUM(CASE WHEN DAY(p.vdate) = 17 THEN 1 ELSE 0 END) AS `17`,
        SUM(CASE WHEN DAY(p.vdate) = 18 THEN 1 ELSE 0 END) AS `18`,
        SUM(CASE WHEN DAY(p.vdate) = 19 THEN 1 ELSE 0 END) AS `19`,
        SUM(CASE WHEN DAY(p.vdate) = 20 THEN 1 ELSE 0 END) AS `20`,
        SUM(CASE WHEN DAY(p.vdate) = 21 THEN 1 ELSE 0 END) AS `21`,
        SUM(CASE WHEN DAY(p.vdate) = 22 THEN 1 ELSE 0 END) AS `22`,
        SUM(CASE WHEN DAY(p.vdate) = 23 THEN 1 ELSE 0 END) AS `23`,
        SUM(CASE WHEN DAY(p.vdate) = 24 THEN 1 ELSE 0 END) AS `24`,
        SUM(CASE WHEN DAY(p.vdate) = 25 THEN 1 ELSE 0 END) AS `25`,
        SUM(CASE WHEN DAY(p.vdate) = 26 THEN 1 ELSE 0 END) AS `26`,
        SUM(CASE WHEN DAY(p.vdate) = 27 THEN 1 ELSE 0 END) AS `27`,
        SUM(CASE WHEN DAY(p.vdate) = 28 THEN 1 ELSE 0 END) AS `28`,
        SUM(CASE WHEN DAY(p.vdate) = 29 THEN 1 ELSE 0 END) AS `29`,
        SUM(CASE WHEN DAY(p.vdate) = 30 THEN 1 ELSE 0 END) AS `30`,
        SUM(CASE WHEN DAY(p.vdate) = 31 THEN 1 ELSE 0 END) AS `31`,
        COUNT(p.vdate) AS total_nights,
        IFNULL(SUM(p.amtdr), 0) AS revenue
    ")
         ->leftJoin('paycharge as p', function ($join) use ($startdate, $enddate) {
            $join->on('p.comp_code', '=', 'sg.sub_code')
               ->whereBetween('p.vdate', [$startdate, $enddate])
               ->where('p.propertyid', $this->propertyid)
               ->where('p.sno', '1')
               ->where('p.paycode', 'RMCH' . $this->propertyid)
               ->where('p.vtype', 'RC');
         })
         ->where('sg.propertyid', $this->propertyid)
         ->where('sg.comp_type', $type)
         ->groupBy('sg.name', 'sg.sub_code')
         ->orderBy('sg.name')
         ->get();

      foreach ($data as $row) {
         $amountsum = Paycharge::where('propertyid', $this->propertyid)->where('comp_code', $row->compcode)
            ->whereBetween('vdate', [$startdate, $enddate])
            ->sum('amtdr');
         $row->revenue = $amountsum;
      }

      return json_encode($data);
   }

   ///////////////////////////  Deepak Code Repport //////////////////////////

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

         $total = $query->count();
         $data = $query->offset($start)->limit($length)->get();

         $result = [];
         $sno = $start + 1;

         foreach ($data as $row) {
            $advances = $this->getAdvanceDetails($row->docid, $propertyid);
            $advanceTotal  = $advances->sum('Advance');
            $row->Adv_Date = $advances->isNotEmpty() ? $advances->first()->Adv_Date : null;
            $row->Advance  = $advanceTotal;
            $row->Adv_Type = $advances->isNotEmpty() ? $advances->first()->Adv_Type : null;
            $row->rect_no  = $advances->isNotEmpty() ? $advances->first()->sno : null;

            $result[] = [
               'sno'           => $sno++,
               'fpno'          => $row->vno,
               'venue'         => $row->Venue ?? '',
               'start_date'    => $row->fromdate ?? '',
               'for_time'      => $row->ForTime ?? '',
               'to_time'       => $row->ToTime ?? '',
               'pax'           => $row->Pax ?? '',
               'pax_rate'      => $row->Rate ?? '',
               'function_type' => $row->FuncType ?? '',
               'party_name'    => $row->PartyName ?? '',
               'advance'       => $advanceTotal ?? '',
               'type'          => $row->Adv_Type ?? '',
               'rect_no'       => $row->rect_no ?? '',
               'rect_date'     => $row->Adv_Date ?? '',
            ];
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
         ->orderBy('PH.vdate')
         ->orderBy('PH.vno')
         ->orderBy('PH.sno');

      return $query;
   }
}
