<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\ResHelper;
use App\Helpers\UpdateRepeat;
use App\Helpers\WhatsappSend;
use App\Models\ACGroup;
use App\Models\Bookings;
use App\Models\BookinPlanDetail;
use App\Models\ChannelEnviro;
use App\Models\ChannelPushes;
use App\Models\Cities;
use App\Models\CompanyDiscount;
use App\Models\FomBillDetail;
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
use App\Models\Depart1;
use App\Models\EnviroFom;
use App\Models\EnviroGeneral;
use App\Models\EnviroPos;
use App\Models\EnviroWhatsapp;
use App\Models\GrpBookinDetail;
use App\Models\GuestFolioProfDetail;
use App\Models\Ledger;
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
use App\Models\Kot as KotModal;
use App\Models\Sundrytype;
use App\Services\AccountPosting;
use Illuminate\Support\Facades\Log;

use function App\Helpers\endsWith;
use function App\Helpers\removeSuffixIfExists;
use function PHPUnit\Framework\isNull;

class CompanyController extends Controller
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
    # Warning: Abandon hope, all who enter here. 😱

    public function ncurfetch()
    {
        $ncurdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        return $ncurdate;
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

    public function opencountry(Request $request)
    {
        $permission = revokeopen(122015);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('countrytable', 'Country Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $countrydata = DB::table('countries')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.countryform', ['countrydata' => $countrydata]);
    }

    public function openstate(Request $request)
    {
        $permission = revokeopen(122016);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('statetable', 'State Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $data['country'] = DB::table('countries')->get();
        $state_data = States::select(
            'states.*',
            'countries.name as countryname'
        )
            ->leftJoin('countries', 'countries.country_code', '=', 'states.country')
            ->where('states.propertyid', $this->propertyid)
            ->orderBy('states.name', 'ASC')->get();

        return view('property.stateform', ['state_data' => $state_data], $data);
    }

    public function opencity(Request $request)
    {
        $permission = revokeopen(122017);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('cityformmain', 'City Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $data['country'] = DB::table('countries')->get();
        $city_data = DB::table('cities')
            ->select(
                'cities.cityname',
                'cities.propertyid',
                'states.name as statename',
                'countries.name as countryname',
                'cities.u_name',
                'cities.city_code'
            )
            ->join('states', 'cities.state', '=', 'states.state_code')
            ->join('countries', 'cities.country', '=', 'countries.country_code')
            ->where('cities.propertyid', '=', $this->propertyid)
            ->orderBy('cities.cityname', 'asc')
            ->distinct()
            ->get();
        return view('property.cityform', ['city_data' => $city_data], $data);
    }

    public function opentaxmaster()
    {
        $permission = revokeopen(121111);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('taxmaster', 'Tax Master Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $taxdata = DB::table('revmast')
            ->select('revmast.name as taxname', 'subgroup.name as subname', 'sundrymast.name as sundryname', 'revmast.*')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'revmast.ac_code')
            ->leftJoin('sundrymast', 'sundrymast.sundry_code', '=', 'revmast.sundry')
            ->where('revmast.propertyid', $this->propertyid)
            ->where('field_type', 'T')
            ->orderBy('taxname', 'ASC')
            ->get();

        $sundrymast = SundryMast::where('propertyid', $this->propertyid)->orderBy('name')->get();

        $ledgerdata = DB::table('subgroup')->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.taxmaster', [
            'taxdata' => $taxdata,
            'sundrymast' => $sundrymast,
            'ledgerdata' => $ledgerdata
        ]);
    }

    public function openbusinesssource()
    {
        $permission = revokeopen(121212);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('businesssource', 'Business Source Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.businesssource', ['data' => $data]);
    }

    public function openunitmast()
    {
        $permission = revokeopen(122021);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('unitmast', 'Unit Master Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $data = DB::table('unitmast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.unitmaster', ['data' => $data]);
    }

    public function opennctypemast()
    {
        $permission = revokeopen(121320);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('nctype_mast', 'NC Type Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('nctype_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('nctype', 'ASC')
            ->get();
        return view('property.nctype', ['data' => $data]);
    }

    public function openpaytypemast()
    {

        $permission = revokeopen(121113);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('revmast', 'Pay Type Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('revmast')
            ->select('revmast.name as taxname', 'taxstru.name as taxstruname', 'subgroup.name as subname', 'sundrymast.name as sundryname', 'revmast.*')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'revmast.ac_code')
            ->leftJoin('sundrymast', 'sundrymast.sundry_code', '=', 'revmast.sundry')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'revmast.tax_stru')
            ->where('revmast.propertyid', $this->propertyid)
            ->where('revmast.field_type', 'P')
            ->orderBy('taxname', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.paymaster', ['data' => $data, 'ledgerdata' => $ledgerdata, 'taxstrudata' => $taxstrudata]);
    }

    public function loadledger(Request $request)
    {
        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $options = [];
        if ($ledgerdata) {
            foreach ($ledgerdata as $row) {
                $options[] = [
                    'value' => $row->sub_code,
                    'text' => $row->name,
                ];
            }
        }
        return response()->json($options);
    }

    public function deleteguestledger(Request $request)
    {
        $dataid = $request->input('dataid');
        $datavalue = $request->input('datavalue');
        $reason = $request->input('reason');
        if (empty($reason)) {
            return json_encode('Please Enter Reason');
        }
        $savelogyn = DB::table('enviro_form')->where('propertyid', $this->propertyid)->value('guestchargesdeletelog');

        if ($savelogyn == 'Y') {
            $existingrowsdata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('vno', $dataid)->get();
            foreach ($existingrowsdata as $existingrows) {
                $loginsertdata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $existingrows->docid,
                    'vno' => $existingrows->vno,
                    'vtype' => $existingrows->vtype,
                    'sno' => $existingrows->sno,
                    'vdate' => $existingrows->vdate,
                    'vtime' => $existingrows->vtime,
                    'vprefix' => $existingrows->vprefix,
                    'paycode' => $existingrows->paycode,
                    'comments' => $existingrows->comments,
                    'guestprof' => $existingrows->guestprof,
                    'roomno' => $existingrows->roomno,
                    'amtdr' => $existingrows->amtdr,
                    'roomtype' => $existingrows->roomtype,
                    'roomcat' => $existingrows->roomcat,
                    'foliono' => $existingrows->foliono,
                    'restcode' => $existingrows->restcode,
                    'remarks' => $reason,
                    'billamount' => $existingrows->billamount,
                    'taxper' => $existingrows->taxper,
                    'onamt' => $existingrows->onamt,
                    'folionodocid' => $existingrows->folionodocid,
                    'taxcondamt' => $existingrows->taxcondamt,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'e',
                ];
                $insertintopaychargelog = DB::table('paychargelog')->insert($loginsertdata);
            }
        }

        try {
            $jaldiwahasehato📢 = DB::table('paycharge')
                ->where('propertyid', $this->propertyid)
                ->where('vno', $dataid)
                ->where('vtype', $datavalue)
                ->delete();
            if ($jaldiwahasehato📢) {
                return true;
            } else {
                return response()->json(['message' => 'Unable to Delete Guest Ledger!'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getcompdt(Request $request)
    {
        $company = Companyreg::where('propertyid', $this->propertyid)->first();
        $user = Auth::user();
        $datemanage = $this->datemanage;
        $data  = [
            'user' => $user,
            'company' => $company,
            'datemanage' => $datemanage
        ];

        return json_encode($data);
    }

    public function loadoutlets(Request $request)
    {
        $ledgerdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $options = [];
        if ($ledgerdata) {
            foreach ($ledgerdata as $row) {
                $options[] = [
                    'value' => $row->dcode,
                    'text' => $row->name,
                ];
            }
        }
        return response()->json($options);
    }

    public function openservermast()
    {

        $permission = revokeopen(121313);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('server_mast', 'Server Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('server_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.waiter', ['data' => $data]);
    }

    public function opentablemast()
    {
        $permission = revokeopen(121314);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('table_mast', 'Table Master Data Analysis HMS', [0, 1, 2], [1, 2, 3]);
        $data = DB::table('room_mast')
            ->select('room_mast.*', 'depart.name as departname', 'depart.dcode')
            ->Join('depart', 'depart.dcode', '=', 'room_mast.rest_code')
            ->where('room_mast.propertyid', $this->propertyid)
            ->where('room_mast.type', 'TB')
            ->orderBy('room_mast.name', 'ASC')
            ->distinct()
            ->get();
        $departdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->where('rest_type', 'Outlet')
            ->get();

        return view('property.tablemaster', ['data' => $data, 'departdata' => $departdata]);
    }

    public function opensetupoutlet()
    {
        $permission = revokeopen(121311);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $data = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereiN('rest_type', ['Outlet', 'ROOM SERVICE'])
            ->get();
        return view('property.outletsetup', ['data' => $data]);
    }

    public function opennsessionmast()
    {
        $permission = revokeopen(121319);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('session_mast', 'Session Master Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $data = DB::table('session_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.sessionmast', ['data' => $data]);
    }

    public function openroomfeatures()
    {
        $permission = revokeopen(121216);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // $this->ExportTable();
        // $this->DownloadTable('roomfeatures', 'Room Features Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('roomfeature')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.roomfeature', ['data' => $data]);
    }

    public function opengueststatus()
    {
        $permission = revokeopen(121213);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('gueststats', 'Guest Status Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('gueststats')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return view('property.gueststatus', ['data' => $data]);
    }

    public function openchargemaster()
    {
        $permission = revokeopen(121214);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $this->ExportTable();
        $this->DownloadTable('chargemaster', 'Charge Master Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('revmast')
            ->select('revmast.name as taxname', 'taxstru.name as taxstruname', 'subgroup.name as subname', 'sundrymast.name as sundryname', 'revmast.*')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'revmast.ac_code')
            ->leftJoin('sundrymast', 'sundrymast.sundry_code', '=', 'revmast.sundry')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'revmast.tax_stru')
            ->where('revmast.propertyid', $this->propertyid)
            ->where('field_type', 'C')
            ->where('Desk_code', '=', 'FOM' . $this->propertyid)
            ->distinct()
            ->orderBy('name', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.chargemaster', [
            'data' => $data,
            'ledgerdata' => $ledgerdata,
            'taxstrudata' => $taxstrudata,
            'update' => false
        ]);
    }

    public function openroomcat()
    {
        $permission = revokeopen(121217);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('room_cat', 'Room Category Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('room_cat')
            ->select('revmast.name as taxname', 'room_cat.*')
            ->leftJoin('revmast', function ($join) {
                $join->on('revmast.rev_code', '=', 'room_cat.rev_code')
                    ->where('room_cat.propertyid', '=', $this->propertyid);
            })
            ->where('room_cat.propertyid', $this->propertyid)
            ->orderBy('revmast.name', 'ASC')
            ->get();

        $revmastdata = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('field_type', 'C')
            ->where('Desk_code', 'FOM' . $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $envirodata = DB::table('enviro_form')
            ->where('propertyid', $this->propertyid)
            ->first();
        return view('property.roomcategory', ['data' => $data, 'revmastdata' => $revmastdata, 'envirodata' => $envirodata]);
    }

    public function openroommaster()
    {
        $permission = revokeopen(121218);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('room_mast', 'Room Master Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('room_mast')
            ->select('room_cat.name as catname', 'room_mast.*')
            ->leftJoin('room_cat', 'room_mast.room_cat', '=', 'room_cat.cat_code')
            ->where('room_mast.propertyid', $this->propertyid)
            ->where('room_mast.type', 'RO')
            ->orderBy('room_mast.rcode', 'ASC')
            ->get();
        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $envirodata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        return view('property.roommaster', ['data' => $data, 'roomcat' => $roomcat, 'envirodata' => $envirodata]);
    }

    public function openplanaster()
    {
        $permission = revokeopen(121215);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('plan_mast', 'Plan Master Data Analysis HMS', [0, 1, 2, 3, 4], [1, 2, 3]);
        $data = DB::table('plan_mast')
            ->select('room_cat.name as catname', 'plan_mast.*')
            ->leftJoin('room_cat', 'plan_mast.room_cat', '=', 'room_cat.cat_code')
            ->where('plan_mast.propertyid', $this->propertyid)
            ->orderBy('plan_mast.name', 'ASC')
            ->get();
        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')
            ->select('name', 'str_code')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $chargedata = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('field_type', 'C')
            ->where('Desk_code', 'FOM' . $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.planmaster', [
            'data' => $data,
            'roomcat' => $roomcat,
            'taxstrudata' => $taxstrudata,
            'chargedata' => $chargedata
        ]);
    }

    public function openwalkin()
    {
        $permission = revokeopen(141112);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $planmaster = DB::table('plan_mast')
            ->select('name', 'pcode')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        // $roommast = DB::table('room_mast')
        //     ->where('propertyid', $this->propertyid)
        //     ->where('type', 'RO')
        //     ->where('inclcount', 'Y')
        //     ->orderBy('name', 'ASC')->get();

        $checkoutdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        $chkoutdate = date('Y-m-d', strtotime($checkoutdate . ' +1 day'));
        $bsource = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();
        $travelagent = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('activeyn', '1')
            ->orderBy('cityname', 'ASC')->get();
        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();

        $enviro_formdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();

        return view('property.walkin', [
            'roomcat' => $roomcat,
            'planmaster' => $planmaster,
            'checkoutdate' => $chkoutdate,
            'bsource' => $bsource,
            'company' => $company,
            'travel_agent' => $travelagent,
            'citydata' => $citydata,
            'countrydata' => $countrydata,
            'nationalitydata' => $nationalitydata,
            'gueststatus' => $gueststatus,
            'enviro_formdata' => $enviro_formdata
        ]);
    }

    public function openprefilledwalkin(Request $request)
    {
        $docid = $request->input('docid');
        $sno = $request->input('sno');

        $maxsno = GrpBookinDetail::where('BookingDocid', $docid)->where('Property_ID', $this->propertyid)->max('Sno');

        $advance = Paycharge::where('propertyid', $this->propertyid)->where('sno', 1)->where('sno1', $maxsno)->where('refdocid', $docid)->get() ?? '';
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();

        $updatedata = DB::table('grpbookingdetails')->select(
            DB::raw("CASE 
                WHEN bookingplandetails.rev_code IS NULL THEN 'N' 
                ELSE 'Y' 
                END AS planedit"),
            'bookingplandetails.rev_code as brev_code',
            'bookingplandetails.taxinc as btaxinc',
            'bookingplandetails.taxstru as btaxstru',
            'bookingplandetails.fixrate as bfixrate',
            'bookingplandetails.planper as bplanper',
            'bookingplandetails.amount as bamount',
            'bookingplandetails.netplanamt as bnetplanamt',
            'bookingplandetails.room_rate_before_tax as broom_rate_before_tax',
            'bookingplandetails.total_rate as btotal_rate',
            'revmast.name as chargename',
            'grpbookingdetails.*',
            'grpbookingdetails.GuestName as clientname',
            'guestprof.*',
            'booking.GuestProf',
            'booking.advdeposit',
            'booking.BookedBy',
            'booking.TravelMode',
            'booking.ResStatus',
            'grpbookingdetails.NoDays',
            'booking.NoofRooms',
            'booking.Company',
            'booking.MarketSeg',
            'guestprof.complimentry',
            'booking.BussSource',
            'booking.TravelAgency',
            'guestprof.pic_path',
            'plan_mast.pcode',
            'plan_mast.name as planname',
            'plan_mast.room_per as room_perplan',
            'room_mast.rcode',
            'room_mast.name as roomname',
            'guestprof.city',
            'guestprof.add1',
            'guestprof.add2',
            'cities.cityname as nameofcity',
            'cities.zipcode as cityzipcode',
            'guestprof.country_code',
            'guestprof.state_code',
            'states.name as nameofstate',
            'countries.name as nameofcountry',
            'countries.nationality as nameofnationality',
            'booking.ArrFrom',
            'booking.Destination',
            'booking.TravelMode',
            'booking.purpofvisit',
            'booking.RDisc',
            'booking.RSDisc',
            'booking.vehiclenum',
            'booking.RefBookNo',
            'booking.Remarks',
            'booking.pickupdrop'
        )
            ->leftJoin('guestprof', 'grpbookingdetails.BookingDocid', '=', 'guestprof.docid')->where('grpbookingdetails.Property_ID', $this->propertyid)
            ->leftJoin('booking', 'grpbookingdetails.BookingDocid', '=', 'booking.DocId')
            ->leftJoin('plan_mast', 'grpbookingdetails.Plan_Code', '=', 'plan_mast.pcode')
            ->leftJoin('room_mast', 'grpbookingdetails.RoomNo', '=', 'room_mast.rcode')
            ->leftJoin('cities', 'guestprof.city', '=', 'cities.city_code')
            ->leftJoin('countries', 'guestprof.country_code', '=', 'countries.country_code')
            ->leftJoin('states', 'guestprof.state_code', '=', 'states.state_code')
            ->leftJoin('bookingplandetails', function ($join) {
                $join->on('bookingplandetails.docid', '=', 'grpbookingdetails.BookingDocid')
                    ->on('bookingplandetails.sno1', '=', 'grpbookingdetails.Sno');
            })
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'bookingplandetails.rev_code')
            ->where('room_mast.propertyid', $this->propertyid)
            ->where('grpbookingdetails.BookingDocid', $docid)->groupBy('grpbookingdetails.Sno')->get();


        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $planmaster = DB::table('plan_mast')
            ->select('name', 'pcode')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $roommast = DB::table('room_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $checkoutdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        $chkoutdate = date('Y-m-d', strtotime($checkoutdate . ' +1 day'));
        $bsource = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();
        $travelagent = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)
            ->orderBy('cityname', 'ASC')->get();
        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();

        $enviro_formdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        return view('property.walkinprefilled', [
            'companydata' => $companydata,
            'advance' => $advance,
            'data' => $updatedata,
            'roomcat' => $roomcat,
            'planmaster' => $planmaster,
            'roommast' => $roommast,
            'checkoutdate' => $chkoutdate,
            'bsource' => $bsource,
            'company' => $company,
            'travel_agent' => $travelagent,
            'citydata' => $citydata,
            'countrydata' => $countrydata,
            'nationalitydata' => $nationalitydata,
            'gueststatus' => $gueststatus,
            'enviro_formdata' => $enviro_formdata,
            'ncurdate' => $ncurdate
        ]);
    }

    public function openupdatewalkin(Request $request)
    {
        $permission = revokeopen(141113);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $updatedata = DB::table('roomocc')->select(
            DB::raw("CASE 
            WHEN plandetails.rev_code IS NULL THEN 'N' 
            ELSE 'Y' 
            END AS planedit"),
            'plandetails.rev_code as brev_code',
            'plandetails.taxinc as btaxinc',
            'plandetails.taxstru as btaxstru',
            'plandetails.fixrate as bfixrate',
            'plandetails.planper as bplanper',
            'plandetails.amount as bamount',
            'plandetails.netplanamt as bnetplanamt',
            'plandetails.room_rate_before_tax as broom_rate_before_tax',
            'plandetails.total_rate as btotal_rate',
            'revmast.name as chargename',
            'roomocc.*',
            'roomocc.docid as udocid',
            'roomocc.name as clientname',
            'guestprof.*',
            'guestfolio.guestprof',
            'guestfolio.nodays',
            'guestfolio.roomcount',
            'guestfolio.company',
            'guestfolio.booking_source',
            'guestprof.complimentry',
            'guestfolio.busssource',
            'guestfolio.travelagent',
            'guestprof.pic_path',
            'plan_mast.pcode',
            'plan_mast.name as planname',
            'plan_mast.room_per as room_perplan',
            'room_mast.rcode',
            'room_mast.name as roomname',
            'guestprof.city',
            'guestprof.add1',
            'guestprof.add2',
            'cities.cityname as nameofcity',
            'cities.zipcode as cityzipcode',
            'guestprof.country_code',
            'guestprof.state_code',
            'states.name as nameofstate',
            'countries.name as nameofcountry',
            'countries.nationality as nameofnationality',
            'guestfolio.arrfrom',
            'guestfolio.destination',
            'guestfolio.travelmode',
            'guestfolio.purvisit',
            'guestfolio.rodisc',
            'guestfolio.rsdisc',
            'guestfolio.vehiclenum',
            'guestfolio.remarks',
            'guestfolio.pickupdrop'
        )
            ->leftJoin('plandetails', function ($join) {
                $join->on('plandetails.docid', '=', 'roomocc.docid')
                    ->on('plandetails.sno1', '=', 'roomocc.sno1');
            })
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'plandetails.rev_code')
            ->leftJoin('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
            ->leftJoin('guestfolio', 'roomocc.docid', '=', 'guestfolio.docid')
            ->leftJoin('plan_mast', 'roomocc.plancode', '=', 'plan_mast.pcode')
            ->leftJoin('room_mast', 'roomocc.roomno', '=', 'room_mast.rcode')
            ->leftJoin('cities', 'guestprof.city', '=', 'cities.city_code')
            ->leftJoin('countries', 'guestprof.country_code', '=', 'countries.country_code')
            ->leftJoin('states', 'guestprof.state_code', '=', 'states.state_code')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.docid', $request->input('docid'))->groupBy('roomocc.sno1')->get();

        // return $updatedata;

        foreach ($updatedata as $row) {
            $plans = PlanMast::where('room_cat', $row->roomcat)
                ->where('propertyid', $this->propertyid)
                ->get();
        }

        foreach ($updatedata as $row) {
            $checkindate = $row->chkindate;
            $previousdate = date('Y-m-d', strtotime('-1 day', strtotime($checkindate)));

            $rooms = DB::table('room_mast')
                ->select('rcode')
                ->whereNotIn('rcode', function ($query) use ($checkindate, $previousdate) {
                    $query->select('roomno')
                        ->from('roomocc')
                        ->whereNull('chkoutdate')
                        ->whereBetween('chkindate', [$checkindate, $checkindate])
                        ->where('propertyid', $this->propertyid);
                })
                ->where('type', 'RO')
                ->where('inclcount', 'Y')
                ->whereNotIn('rcode', function ($query) use ($checkindate, $previousdate) {
                    $query->select('RoomNo')
                        ->from('grpbookingdetails')
                        ->whereBetween('Arrdate', [$checkindate, $checkindate])
                        ->where('Property_ID', $this->propertyid);
                })
                ->where('propertyid', $this->propertyid)
                ->where('room_cat', $row->roomcat)
                ->get();
        }

        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $planmaster = DB::table('plan_mast')
            ->select('name', 'pcode')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $roommast = DB::table('room_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $checkoutdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        $chkoutdate = date('Y-m-d', strtotime($checkoutdate . ' +1 day'));
        $bsource = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();
        $travelagent = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('activeyn', '1')
            ->orderBy('cityname', 'ASC')->get();
        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();

        $enviro_formdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();

        $checkcharge = Paycharge::where('folionodocid', $request->input('docid'))->where('billno', '!=', '0')->first();

        $leaderc = '1';

        if ($checkcharge) {
            $leaderc = '0';
        }

        return view('property.updatewalkin', [
            'rooms' => $rooms,
            'plans' => $plans,
            'leaderc' => $leaderc,
            'data' => $updatedata,
            'roomcat' => $roomcat,
            'planmaster' => $planmaster,
            'roommast' => $roommast,
            'checkoutdate' => $chkoutdate,
            'bsource' => $bsource,
            'company' => $company,
            'travel_agent' => $travelagent,
            'citydata' => $citydata,
            'countrydata' => $countrydata,
            'nationalitydata' => $nationalitydata,
            'gueststatus' => $gueststatus,
            'enviro_formdata' => $enviro_formdata
        ]);
    }

    public function openblankgrc(Request $request)
    {
        $permission = revokeopen(141111);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vtype = "CHK";
        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $this->ncurdate)
            ->whereDate('date_to', '>=', $this->ncurdate)
            ->first();
        if ($chkvpf === null || $chkvpf === '0') {
            return response()->json([
                'redirecturl' => '',
                'status' => 'error',
                'message' => 'You are not eligible to checkin for this date: ' . date('d-m-Y', strtotime($this->ncurdate)),
            ]);
        }

        $fom = EnviroFom::where('propertyid', $this->propertyid)->first();
        $start_srl_no = $chkvpf->start_srl_no + 1;
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        return view('property.blankgrc', [
            'srlno' => $start_srl_no,
            'company' => $companydata,
            'fom' => $fom,
            'ncur' => $this->ncurdate,
        ]);
    }

    public function openupdatereservation(Request $request)
    {
        $permission = revokeopen(131111);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $chkcheckin = DB::table('guestfolio')->where('propertyid', $this->propertyid)
            ->where('bookingdocid', base64_decode($request->input('DocId')))->first();
        if ($chkcheckin) {
            return back()->with('error', 'Guest already checked in can not edit Reservation🏡!');
        }

        $advance = Paycharge::where('propertyid', $this->propertyid)->where('sno', 1)->where('refdocid', base64_decode($request->input('DocId')))->get() ?? '';

        // echo base64_decode($request->input('sno'));
        // var_dump($advance);
        // exit;

        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $updatedata = DB::table('grpbookingdetails')->select(
            DB::raw("CASE 
                WHEN bookingplandetails.rev_code IS NULL THEN 'N' 
                ELSE 'Y' 
                END AS planedit"),
            'bookingplandetails.rev_code as brev_code',
            'bookingplandetails.taxinc as btaxinc',
            'bookingplandetails.taxstru as btaxstru',
            'bookingplandetails.fixrate as bfixrate',
            'bookingplandetails.planper as bplanper',
            'bookingplandetails.amount as bamount',
            'bookingplandetails.netplanamt as bnetplanamt',
            'bookingplandetails.room_rate_before_tax as broom_rate_before_tax',
            'bookingplandetails.total_rate as btotal_rate',
            'revmast.name as chargename',
            'grpbookingdetails.*',
            'grpbookingdetails.GuestName as clientname',
            'guestprof.*',
            'booking.GuestProf',
            'booking.vdate',
            'booking.Remarks',
            'booking.pickupdrop',
            'booking.BookNo',
            'booking.advdeposit',
            'booking.BookedBy',
            'booking.TravelMode',
            'booking.ResStatus',
            'grpbookingdetails.NoDays',
            'booking.NoofRooms',
            'booking.Company',
            'booking.MarketSeg',
            'guestprof.complimentry',
            'booking.BussSource',
            'booking.TravelAgency',
            'guestprof.pic_path',
            'plan_mast.pcode',
            'plan_mast.name as planname',
            'plan_mast.room_per as room_perplan',
            'room_mast.rcode',
            'room_mast.name as roomname',
            'guestprof.city',
            'guestprof.add1',
            'guestprof.add2',
            'cities.cityname as nameofcity',
            'cities.zipcode as cityzipcode',
            'guestprof.country_code',
            'guestprof.state_code',
            'states.name as nameofstate',
            'countries.name as nameofcountry',
            'countries.nationality as nameofnationality',
            'booking.ArrFrom',
            'booking.Destination',
            'booking.TravelMode',
            'booking.purpofvisit',
            'booking.RDisc',
            'booking.RSDisc',
            'booking.vehiclenum',
            'booking.RefBookNo'
        )
            ->leftJoin('guestprof', 'grpbookingdetails.BookingDocid', '=', 'guestprof.docid')->where('grpbookingdetails.Property_ID', $this->propertyid)
            ->leftJoin('booking', 'grpbookingdetails.BookingDocid', '=', 'booking.DocId')
            ->leftJoin('plan_mast', 'grpbookingdetails.Plan_Code', '=', 'plan_mast.pcode')
            ->leftJoin('room_mast', 'grpbookingdetails.RoomNo', '=', 'room_mast.rcode')
            ->leftJoin('cities', 'guestprof.city', '=', 'cities.city_code')
            ->leftJoin('countries', 'guestprof.country_code', '=', 'countries.country_code')
            ->leftJoin('states', 'guestprof.state_code', '=', 'states.state_code')
            ->leftJoin('bookingplandetails', function ($join) {
                $join->on('bookingplandetails.docid', '=', 'grpbookingdetails.BookingDocid')
                    ->on('bookingplandetails.sno1', '=', 'grpbookingdetails.Sno');
            })
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'bookingplandetails.rev_code')
            ->where('grpbookingdetails.BookingDocid', base64_decode($request->input('DocId')))->groupBy('grpbookingdetails.Sno')
            ->get();

        // var_dump($updatedata);
        // exit;

        foreach ($updatedata as $row) {
            $checkindate = $row->ArrDate;
            $previousdate = date('Y-m-d', strtotime('-1 day', strtotime($checkindate)));

            $rooms = DB::table('room_mast')
                ->select('rcode')
                ->whereNotIn('rcode', function ($query) use ($checkindate, $previousdate) {
                    $query->select('roomno')
                        ->from('roomocc')
                        ->whereNull('chkoutdate')
                        ->whereBetween('chkindate', [$checkindate, $checkindate])
                        ->where('propertyid', $this->propertyid);
                })
                ->where('type', 'RO')
                ->where('inclcount', 'Y')
                ->whereNotIn('rcode', function ($query) use ($checkindate, $previousdate) {
                    $query->select('RoomNo')
                        ->from('grpbookingdetails')
                        ->whereBetween('Arrdate', [$checkindate, $checkindate])
                        ->where('Property_ID', $this->propertyid);
                })
                ->where('propertyid', $this->propertyid)
                ->where('room_cat', $row->RoomCat)
                ->get();
        }

        foreach ($updatedata as $row) {
            $plans = PlanMast::where('room_cat', $row->RoomCat)
                ->where('propertyid', $this->propertyid)
                ->groupBy('pcode')
                ->get();
        }


        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();

        $planmaster = DB::table('plan_mast')
            ->select('name', 'pcode', 'tarrif')
            ->where('propertyid', $this->propertyid)
            // ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $roommast = DB::table('room_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $checkoutdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        $chkoutdate = date('Y-m-d', strtotime($checkoutdate . ' +1 day'));
        $bsource = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();
        $travelagent = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('activeyn', '1')
            ->orderBy('cityname', 'ASC')->get();
        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();

        $enviro_formdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');

        return view('property.updatereservation', [
            'plans' => $plans,
            'rooms' => $rooms,
            'companydata' => $companydata,
            'advance' => $advance,
            'data' => $updatedata,
            'roomcat' => $roomcat,
            'planmaster' => $planmaster,
            'roommast' => $roommast,
            'checkoutdate' => $chkoutdate,
            'bsource' => $bsource,
            'company' => $company,
            'travel_agent' => $travelagent,
            'citydata' => $citydata,
            'countrydata' => $countrydata,
            'nationalitydata' => $nationalitydata,
            'gueststatus' => $gueststatus,
            'enviro_formdata' => $enviro_formdata,
            'ncurdate' => $ncurdate
        ]);
    }

    public function opencheckinlist()
    {
        $permission = revokeopen(141113);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $datar = GuestFolio::select([
            'guestfolio.docid',
            'guestfolio.folio_no AS Folio_No',
            DB::raw("COALESCE(paycharge.billno, '') AS Bill_No"),
            'roomocc.roomno AS Room_No',
            'roomocc.chkindate',
            'guestfolio.vdate AS CheckIn_Date',
            'roomocc.sno1',
            'roomocc.chkintime AS CheckIn_Time',
            DB::raw("COALESCE(roomocc.chkoutdate, '') AS Dep_Date"),
            'roomocc.chkouttime AS deptime',
            'guestfolio.Name AS Guest_Name',
            'roomocc.type',
            'guestprof.mobile_no',
            'cities.cityname AS City',
            'guestfolio.Remark',
            'roomocc.rackrate AS Rate',
            'roomocc.rrtaxinc AS Tax_Inc',
            'subcom.name AS compname',
            'travelcom.name AS travelagent'
        ])
            ->leftJoin('roomocc', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('guestprof', 'guestprof.guestcode', '=', 'roomocc.guestprof')
            ->leftJoin('cities', 'cities.city_code', '=', 'guestfolio.city')
            ->leftJoin('subgroup AS subcom', 'subcom.sub_code', '=', 'guestfolio.company')
            ->leftJoin('subgroup AS travelcom', 'travelcom.sub_code', '=', 'guestfolio.travelagent')
            ->leftJoin('paycharge', function ($join) {
                $join->on('paycharge.folionodocid', '=', 'roomocc.docid')
                    ->on('paycharge.sno1', '=', 'roomocc.sno1');
            })
            ->where('guestfolio.propertyid', $this->propertyid)
            ->orderByDesc('roomocc.vprefix')
            ->orderByDesc('roomocc.folioNo')
            ->orderByDesc('roomocc.u_entdt')
            ->groupBy([
                'guestfolio.docid',
                'guestfolio.folio_no',
                'roomocc.roomno',
                'roomocc.chkindate',
                'guestfolio.vdate',
                'roomocc.sno1',
                'roomocc.chkintime',
                'roomocc.chkoutdate',
                'roomocc.chkouttime',
                'guestfolio.Name',
                'roomocc.type',
                'guestprof.mobile_no',
                'cities.cityname',
                'guestfolio.Remark',
                'roomocc.rackrate',
                'roomocc.rrtaxinc',
                'subcom.name',
                'travelcom.name'
            ]);

        if (Auth::user()->superwiser == 1) {
            $data = $datar->get();
        } else {
            $data = $datar->where('roomocc.chkindate', $this->ncurdate)->get();
        }

        return view('property.checkinlist', compact('data'));
    }

    public function deletewalkin(Request $request)
    {
        $permission = revokeopen(141113);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = base64_decode($request->input('docid'));
        $sno1 = base64_decode($request->input('sno1'));
        $roomoccdata = DB::table('roomocc')->where('docid', $docid)->where('propertyid', $this->propertyid)->get();
        foreach ($roomoccdata as $data) {
            $sno1fetched = $data->sno1;
            $checkinrbooking = DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('ContraDocId', $docid)->where('ContraSno', $sno1fetched)->first();
            if ($checkinrbooking) {
                $blankpanel = [
                    'ContraDocId' => null,
                    'ContraSno' => null,
                ];
                $update = DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $checkinrbooking->BookingDocid)->where('Sno', $sno1fetched)->update($blankpanel);
            }
        }
        $checkinpaycharge = DB::table('paycharge')->where('folionodocid', $docid)->where('propertyid', $this->propertyid)->first();
        if (!empty($checkinpaycharge)) {
            return back()->with('error', 'Related Records existing cannot delete!');
        }
        $checkroomnotchanged = DB::table('roomocc')->where('docid', $docid)->where('propertyid', $this->propertyid)->where('sno', '1')->where('sno1', '1')->value('type');
        if (!empty($checkroomnotchanged)) {
            return back()->with('error', 'Room Has Been Changed Unable TO Delete It!');
        }

        $profileimage = DB::table('guestprof')->where('docid', $docid)->where('propertyid', $this->propertyid)->value('pic_path');
        $guestsign = DB::table('guestprof')->where('docid', $docid)->where('propertyid', $this->propertyid)->value('guestsign');
        $identityimage = DB::table('guestprof')->where('docid', $docid)->where('propertyid', $this->propertyid)->value('idpic_path');
        if (!empty($profileimage)) {
            $folderPathp = storage_path('app/public/walkin/profileimage/' . $profileimage);
            if (file_exists($folderPathp)) {
                unlink($folderPathp);
            }
        }
        if (!empty($guestsign)) {
            $folderPathp = storage_path('app/public/walkin/signature/' . $guestsign);
            if (file_exists($folderPathp)) {
                unlink($folderPathp);
            }
        }
        if (!empty($identityimage)) {
            $folderPathi = storage_path('app/public/walkin/identityimage/' . $identityimage);
            if (file_exists($folderPathi)) {
                unlink($folderPathi);
            }
        }
        try {
            $roomocc = DB::table('roomocc')->where('docid', $docid)->where('propertyid', $this->propertyid)->delete();
            $guestproftable = DB::table('guestprof')->where('docid', $docid)->where('propertyid', $this->propertyid)->delete();
            $guestfolio = DB::table('guestfolio')->where('docid', $docid)->where('propertyid', $this->propertyid)->delete();
            $guestfolioprofdetail = DB::table('guestfolioprofdetail')->where('doc_id', $docid)->where('propertyid', $this->propertyid)->delete();
            return back()->with('success', 'Walkin Deleted Successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error! - ' . $e->getMessage());
        }
    }

    public function deletereservation(Request $request)
    {
        $permission = revokeopen(131111);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $DocId = base64_decode($request->input('DocId'));
        $advancepayment = DB::table('paycharge')->where('refdocid', $DocId)->where('propertyid', $this->propertyid)->first();
        if (isset($advancepayment)) {
            return back()->with('error', 'Error! - Advance already deposited');
        }
        $profileimage = DB::table('guestprof')->where('docid', $DocId)->where('propertyid', $this->propertyid)->value('pic_path');
        $identityimage = DB::table('guestprof')->where('docid', $DocId)->where('propertyid', $this->propertyid)->value('idpic_path');
        if (!empty($profileimage)) {
            $folderPathp = storage_path('app/public/walkin/reservationprofilepic/' . $profileimage);
            if (file_exists($folderPathp)) {
                unlink($folderPathp);
            }
        }
        if (!empty($identityimage)) {
            $folderPathi = storage_path('app/public/walkin/reservationidentitypic/' . $identityimage);
            if (file_exists($folderPathi)) {
                unlink($folderPathi);
            }
        }
        try {
            $roomocc = DB::table('booking')->where('DocId', $DocId)->where('Property_ID', $this->propertyid)->delete();
            $guestproftable = DB::table('guestprof')->where('docid', $DocId)->where('propertyid', $this->propertyid)->delete();
            $grpbookingdetails = DB::table('grpbookingdetails')->where('BookingDocid', $DocId)->where('Property_ID', $this->propertyid)->delete();
            $bookingplandetails = DB::table('bookingplandetails')->where('docid', $DocId)->where('propertyid', $this->propertyid)->delete();
            return back()->with('success', 'Reservation Deleted Successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error! - ' . $e->getMessage());
        }
    }

    public function checkeditarrival(Request $request)
    {
        $data = DB::table('enviro_form')
            ->join('enviro_general', 'enviro_form.propertyid', '=', 'enviro_general.propertyid')
            ->select('enviro_form.*', 'enviro_general.*')
            ->where('enviro_form.propertyid', $this->propertyid)
            ->first();
        return response()->json($data);
    }

    public function openupdatebsource(Request $request)
    {
        $permission = revokeopen(121212);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('busssource')
            ->where('bcode', base64_decode($request->input('bcode')))
            ->where('propertyid', $this->propertyid)
            ->first();
        return view('property.updatebusinesssource', ['data' => $data]);
    }

    public function openupdateroomfeature(Request $request)
    {
        $permission = revokeopen(121216);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('roomfeature')
            ->where('rcode', base64_decode($request->input('rcode')))
            ->where('propertyid', $this->propertyid)
            ->first();
        return view('property.updateroomfeature', ['data' => $data]);
    }

    public function openupdategueststatus(Request $request)
    {
        $permission = revokeopen(121213);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('gueststats')
            ->where('gcode', base64_decode($request->input('gcode')))
            ->where('propertyid', $this->propertyid)
            ->first();
        return view('property.updategueststatus', ['data' => $data]);
    }

    public function openupdatechargemaster(Request $request)
    {
        $permission = revokeopen(121214);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('revmast')
            ->select('revmast.name as taxname', 'taxstru.name as taxstruname', 'subgroup.name as subname', 'revmast.*')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'revmast.ac_code')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'revmast.tax_stru')
            ->where('revmast.propertyid', $this->propertyid)
            ->where('revmast.field_type', 'C')
            ->where('revmast.Desk_code', 'FOM' . $this->propertyid)
            ->where('revmast.rev_code', base64_decode($request->input('rev_code')))
            ->where('revmast.sn', base64_decode($request->input('sn')))
            ->first();

        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $ledgerdatasub = Ledger::where('subcode', base64_decode($request->input('rev_code')))->where('propertyid', $this->propertyid)->where('vtype', 'F_AO')->orderBy('vsno')->get();

        return view('property.updatechargemaster', [
            'data' => $data,
            'ledgerdata' => $ledgerdata,
            'taxstrudata' => $taxstrudata,
            'update' => true,
            'ledgerdatasub' => $ledgerdatasub
        ]);
    }

    public function openupdateroomcat(Request $request)
    {
        $permission = revokeopen(121217);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('room_cat')
            ->select('revmast.name as taxname', 'room_cat.*')
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'room_cat.rev_code')
            ->where('revmast.propertyid', $this->propertyid)
            ->where('room_cat.sn', base64_decode($request->input('sn')))
            ->first();

        $revmastdata = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('field_type', 'C')
            ->where('Desk_code', 'FOM' . $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $ratelistdata = DB::table('rate_list')
            ->where('propertyid', $this->propertyid)
            ->where('room_cat', base64_decode($request->input('cat_code')))
            ->get();
        return view('property.updateroomcategory', ['data' => $data, 'revmastdata' => $revmastdata, 'ratelistdata' => $ratelistdata]);
    }

    public function openupdateroommast(Request $request)
    {
        $permission = revokeopen(121218);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('room_mast')
            ->select('room_cat.name as catname', 'room_mast.*')
            ->leftJoin('room_cat', 'room_mast.room_cat', '=', 'room_cat.cat_code')
            ->where('room_mast.propertyid', $this->propertyid)
            ->where('room_mast.sno', base64_decode($request->input('sno')))
            ->first();

        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $ratelistdata = DB::table('rate_list')
            ->where('propertyid', $this->propertyid)
            ->where('room_cat', base64_decode($request->input('cat_code')))
            ->where('roomno', base64_decode($request->input('roomno')))
            ->orderBy('sn')
            ->get();
        $envirodata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        return view('property.updateroommaster', [
            'data' => $data,
            'roomcat' => $roomcat,
            'ratelistdata' => $ratelistdata,
            'envirodata' => $envirodata
        ]);
    }

    public function openupdateplanmast(Request $request)
    {
        $permission = revokeopen(121215);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('plan_mast')
            ->select('room_cat.name as catname', 'taxstru.name as taxstruname', 'plan_mast.*', 'plan1.*')
            ->leftJoin('plan1', 'plan_mast.pcode', '=', 'plan1.pcode')
            ->leftJoin('room_cat', 'plan_mast.room_cat', '=', 'room_cat.cat_code')
            ->leftJoin('taxstru', 'plan_mast.room_tax_stru', '=', 'taxstru.str_code')
            ->where('plan_mast.propertyid', $this->propertyid)
            ->where('plan_mast.sn', base64_decode($request->input('sn')))
            ->first();

        $plan1data = DB::table('plan1')
            ->select('revmast.name as chargingname', 'plan1.*')
            ->leftJoin('revmast', 'plan1.rev_code', '=', 'revmast.rev_code')
            ->where('plan1.propertyid', $this->propertyid)
            ->where('plan1.pcode', base64_decode($request->input('pcode')))
            ->orderBy('sno')
            ->get();

        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')
            ->select('name', 'str_code')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $chargedata = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('field_type', 'C')
            ->where('Desk_code', 'FOM' . $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        return view('property.updateplanmaster', [
            'data' => $data,
            'roomcat' => $roomcat,
            'taxstrudata' => $taxstrudata,
            'chargedata' => $chargedata,
            'plan1data' => $plan1data
        ]);
    }
    // Future developer, you owe me a coffee for deciphering this. ☕😅


    public function opentaxstructure()
    {
        $permission = revokeopen(121112);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $taxdata = DB::table('taxstru')
            ->select('name')
            ->where('propertyid', $this->propertyid)
            ->distinct()
            ->orderBy('name', 'ASC')
            ->get();

        $taxdatamain = DB::table('revmast')->where('field_type', 'T')->where('propertyid', $this->propertyid)->get();
        return view('property.taxstructure', ['taxdata' => $taxdata, 'propertyid' => $this->propertyid, 'taxdatamain' => $taxdatamain]);
    }

    public function openledgeraccount()
    {
        $permission = revokeopen(122020);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $ledgerdatamain = DB::table('acgroup')->where('propertyid', $this->propertyid)->get();

        return view('property.ledgeraccount', [
            'taxdata' => $ledgerdata,
            'ledgerdatamain' => $ledgerdatamain,
            'update' => false
        ]);
    }

    public function opencompanymaster()
    {
        $permission = revokeopen(122018);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $comp_mastdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->whereNotNull('comp_type')
            ->orderBy('name', 'ASC')->get();
        $subgroupdata = DB::table('acgroup')
            ->where('group_name', 'SUNDRY DEBTORS')
            ->where('propertyid', $this->propertyid)->get();
        $citydata = DB::table('cities')->where('activeyn', '1')->where('propertyid', $this->propertyid)->orderBy('cityname', 'ASC')->get();
        return view('property.companymaster', [
            'comp_mastdata' => $comp_mastdata,
            'subgroupdata' => $subgroupdata,
            'citydata' => $citydata,
            'update' => false
        ]);
    }

    public function openupdatecompmaster(Request $request)
    {
        $permission = revokeopen(122018);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $sn = base64_decode($request->input('sn'));
        $subcode = base64_decode($request->input('comp_code'));

        $comp_mastdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)->where('sn', $sn)
            ->first();
        $subgroupdata = DB::table('acgroup')
            ->where('group_name', 'SUNDRY DEBTORS')
            ->where('propertyid', $this->propertyid)->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->get();
        $result = DB::table('acgroup')
            ->join('cities', 'acgroup.propertyid', '=', 'cities.propertyid')
            ->where('acgroup.propertyid', $this->propertyid)
            ->where('acgroup.group_code', $comp_mastdata->group_code)
            ->where('cities.city_code', $comp_mastdata->citycode)
            ->select('acgroup.group_name as groupname', 'cities.cityname as cityname')
            ->first();
        $groupname = $result->groupname ?? '';
        $cityname = $result->cityname ?? '';

        $ledgerdatasub = Ledger::where('subcode', $subcode)->where('propertyid', $this->propertyid)->where('vtype', 'F_AO')->orderBy('vsno')->get();

        $roomcat = RoomCat::where('propertyid', $this->propertyid)->where('type', 'RO')->where('inclcount', 'Y')->orderBy('name')->get();

        $compdiscount = CompanyDiscount::where('propertyid', $this->propertyid)->where('compcode', $subcode)->orderBy('sno')->get();

        return view('property.updatecompanymaster', [
            'comp_mastdata' => $comp_mastdata,
            'subgroupdata' => $subgroupdata,
            'citydata' => $citydata,
            'groupname' => $groupname,
            'cityname' => $cityname,
            'ledgerdatasub' => $ledgerdatasub,
            'update' => true,
            'roomcat' => $roomcat,
            'compdiscount' => $compdiscount
        ]);
    }

    public function openupdateledgeraccount(Request $request)
    {
        $permission = revokeopen(122020);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('sub_code', base64_decode($request->input('sub_code')))
            ->first();
        $groupname = DB::table('acgroup')->where('group_code', $ledgerdata->group_code)->first();
        $ledgerdatamain = DB::table('acgroup')->where('propertyid', $this->propertyid)->get();

        $ledgerdatasub = Ledger::where('subcode', base64_decode($request->input('sub_code')))->where('propertyid', $this->propertyid)->where('vtype', 'F_AO')->orderBy('vsno')->get();

        return view('property.updateledgeraccounts', [
            'ledgerdata' => $ledgerdata,
            'ledgerdatamain' => $ledgerdatamain,
            'groupname' => $groupname,
            'ledgerdatasub' => $ledgerdatasub,
            'update' => true
        ]);
    }


    public function openusermaster()
    {
        $permission = revokeopen(122011);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('usermastertable', 'User Master Data Analysis HMS', [0, 1, 2, 3], []);
        $userdata = DB::table('users')
            ->select('users.*', 'company.role as comprole')
            ->leftJoin('company', function ($join) {
                $join->on('company.u_name', 'users.u_name')
                    ->where('company.propertyid', $this->propertyid);
            })
            ->where('users.propertyid', $this->propertyid)
            ->get();

        // var_dump($userdata);
        // exit;

        $path = storage_path('app/public/menu.json');
        $jsonData = file_get_contents($path);
        $menuItems = json_decode($jsonData, true);

        $outlets = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereIn('rest_type', ['Outlet', 'ROOM SERVICE'])
            ->get();

        return view('property.usermaster', compact('userdata', 'menuItems', 'outlets'));
    }


    public function openfomparamter()
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $ledgerdatamain = DB::table('subgroup')->where('propertyid', $this->propertyid)->get();
        $paramdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        try {
            $cancellationac = DB::table('subgroup')->where('propertyid', $this->propertyid)
                ->where('sub_code', $paramdata->cancellationac)->pluck('name')
                ->first();
            $advanceroomrentac = DB::table('subgroup')->where('propertyid', $this->propertyid)
                ->where('sub_code', $paramdata->advanceroomrentac)->pluck('name')
                ->first();
            $roomchrgdueac = DB::table('subgroup')->where('propertyid', $this->propertyid)
                ->where('sub_code', $paramdata->roomchrgdueac)->pluck('name')
                ->first();
            $enviro_general = EnviroGeneral::where('propertyid', $this->propertyid)->first();
        } catch (Exception $e) {
            return back()->with('error', 'An Error Occured!');
        }
        return view('property.fomparameter', [
            'taxdata' => $ledgerdata,
            'ledgerdatamain' => $ledgerdatamain,
            'fomparamdata' => $paramdata,
            'cancellationac' => $cancellationac,
            'advanceroomrentac' => $advanceroomrentac,
            'roomchrgdueac' => $roomchrgdueac,
            'enviro_general' => $enviro_general
        ]);
    }

    public function getState2(Request $request)
    {
        $cid = $request->post('cid');
        $state = DB::table('states')->where('country', $cid)->where('propertyid', $this->propertyid)->orderBy('name', 'asc')->get();
        $html = '<option value="">Select State</option>';
        foreach ($state as $list) {
            $html .= '<option value="' . $list->state_code . '">' . $list->name . '</option>';
        }
        echo $html;
    }

    public function geRate(Request $request)
    {
        $data = json_decode($request->post('data'));
        $value = DB::table('plan_mast')
            ->where('room_cat', $data[0])
            ->where('pcode', $data[1])
            ->where('propertyid', $this->propertyid)
            ->where('adults', $data[2])
            ->pluck('package_amount')
            ->first();
        echo $value;
    }

    public function geRate2(Request $request)
    {
        $data = json_decode($request->post('data'));
        $value = DB::table('plan_mast')
            ->where('room_cat', $data[0])
            ->where('pcode', $data[1])
            ->where('propertyid', $this->propertyid)
            ->where('adults', $data[2])
            ->where('childs', $data[3])
            ->pluck('package_amount')
            ->first();
        echo $value;
    }

    public function geRate3(Request $request)
    {
        $data = json_decode($request->post('data'));
        if ($data[2] == 1) {
            $type = 'singleuser';
            $value = DB::table('rate_list')
                ->where('room_cat', $data[0])
                ->where('roomno', $data[1])
                ->where('propertyid', $this->propertyid)
                ->where('occtype', $type)
                ->pluck('rate2')
                ->first();
        } elseif ($data[2] == 2) {
            $type = 'multiuser';
            $value = DB::table('rate_list')
                ->where('room_cat', $data[0])
                ->where('roomno', $data[1])
                ->where('propertyid', $this->propertyid)
                ->where('occtype', $type)
                ->pluck('rate2')
                ->first();
        } elseif ($data[2] == 3) {
            $singleuserRate = DB::table('rate_list')
                ->where('room_cat', $data[0])
                ->where('roomno', $data[1])
                ->where('propertyid', $this->propertyid)
                ->where('occtype', 'singleuser')
                ->pluck('rate2')
                ->first();

            $multiuserRate = DB::table('rate_list')
                ->where('room_cat', $data[0])
                ->where('roomno', $data[1])
                ->where('propertyid', $this->propertyid)
                ->where('occtype', 'multiuser')
                ->pluck('rate2')
                ->first();

            $value = $singleuserRate + $multiuserRate;
        } elseif ($data[2] > 3) {
            $type = 'extrauser';
            $value = DB::table('rate_list')
                ->where('room_cat', $data[0])
                ->where('roomno', $data[1])
                ->where('propertyid', $this->propertyid)
                ->where('occtype', $type)
                ->pluck('rate2')
                ->first();
        } else {
            $value = 0;
        }

        return $value;
    }

    public function walkinglocdata(Request $request)
    {
        $citycode = $request->input('citycode');
        $citydata = DB::table('cities')
            ->where('city_code', $citycode)
            ->where('propertyid', $this->propertyid)
            ->first();

        $statedata = DB::table('states')
            ->where('state_code', $citydata->state)
            ->where('propertyid', $this->propertyid)
            ->first();

        $countrydata = DB::table('countries')
            ->where('country_code', $statedata->country)
            ->where('propertyid', $this->propertyid)
            ->first();

        $zipcodereturn = $citydata->zipcode;
        $response = [
            'states' => [
                [
                    'state_code' => $statedata->state_code,
                    'name' => $statedata->name,
                ],
            ],
            'countries' => [
                [
                    'country_code' => $countrydata->country_code,
                    'country_name' => $countrydata->name,
                    'nationality' => $countrydata->nationality,
                ],
            ],
            'zipcode' => $zipcodereturn,
        ];

        return response()->json($response);
    }

    public function getsundrynames(Request $request)
    {
        $sundryname = $request->post('cid');
        $listsundry = DB::table('sundrymast')->where('name', 'LIKE', "%$sundryname%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($listsundry as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getledgernames(Request $request)
    {
        $ledgernames = $request->post('cid');
        $listsundry = DB::table('subgroup')->where('name', 'LIKE', "%$ledgernames%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($listsundry as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function gettaxnames(Request $request)
    {
        $taxnames = $request->post('cid');
        $data = DB::table('revmast')->where('name', 'LIKE', "%$taxnames%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getbnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('busssource')->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getunitnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('unitmast')
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        if ($data->count() > 0) {
            $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
            foreach ($data as $list) {
                $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
            }
            $output .= '</ul>';
            return $output;
        } else {

            return '';
        }
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

    public function gettablenames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('room_mast')
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->where('type', 'TB')
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

    public function getoutletnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('depart')
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->where('rest_type', 'Outlet')
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

    public function getpaytypenames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('revmast')
            ->where('name', 'LIKE', "%$names%")
            ->where('field_type', 'P')
            ->where('propertyid', $this->propertyid)
            ->get();
        if ($data->count() > 0) {
            $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
            foreach ($data as $list) {
                $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
            }
            $output .= '</ul>';
            return $output;
        } else {
            return '';
        }
    }

    public function getcheckboxes(Request $request)
    {
        $data = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereIn('rest_type', ['Outlet', 'FOM', 'ROOM SERVICE'])
            ->get();

        return $data;
    }

    public function getperfectcheckrows(Request $request)
    {
        $data = DB::table('depart_pay')
            ->where('propertyid', $this->propertyid)
            ->where('rest_code', $request->post('cid2'))
            ->where('pay_code', $request->post('revmoti'))
            ->get();
        return $data;
    }

    public function getcheckeddatadppay(Request $request)
    {
        $data = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereIn('rest_type', ['Outlet', 'FOM', 'ROOM SERVICE'])
            ->get();
        return $data;
    }

    public function getsessionnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('session_mast')
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        if ($data->count() > 0) {
            $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
            foreach ($data as $list) {
                $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
            }
            $output .= '</ul>';
            return $output;
        } else {
            return '';
        }
    }

    public function getrnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('roomfeature')->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getgnames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('gueststats')->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto">';
        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getchargeames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('revmast')
            ->where('field_type', 'C')
            ->where('Desk_code', 'FOM' . $this->propertyid)
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();
        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto;">';
        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getplannames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('plan_mast')
            ->where('name', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();

        if ($data->isEmpty()) {
            return null;
        }

        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto;">';

        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->name . '</a></li>';
        }

        $output .= '</ul>';
        return $output;
    }

    public function getreasons(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('roomocc')
            ->select('reason')
            ->where('reason', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->distinct()
            ->get();

        if ($data->isEmpty()) {
            return null;
        }

        $output = '<ul class="dropdown-menu" style="display:block; position:absolute; width:auto;">';

        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->reason . '</a></li>';
        }

        $output .= '</ul>';
        return $output;
    }

    public function getcitynames(Request $request)
    {
        $names = $request->post('cid');
        $data = DB::table('cities')
            ->where('cityname', 'LIKE', "%$names%")
            ->where('propertyid', $this->propertyid)
            ->get();

        if ($data->isEmpty()) {
            return null;
        }

        $output = '<ul class="dropdown-menu" style="display:block; position:relative; width:auto;">';

        foreach ($data as $list) {
            $output .= '<li class=""><a class="dropdown-item" href="#">' . $list->cityname . '</a></li>';
        }

        $output .= '</ul>';
        return $output;
    }

    public function submitcountry(Request $request)
    {
        $permission = revokeopen(122015);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'countryname' => 'required',
            'nationality' => 'required',
            'country_code' => 'required',
        ]);
        $countryname = $request->input('countryname');
        $country_code = $request->input('country_code');
        $nationality = $request->input('nationality');

        $existingName = DB::table('countries')
            ->where('propertyid', $this->propertyid)
            ->where('name', $countryname)
            ->first();

        $existingCountryCode = DB::table('countries')
            ->where('propertyid', $this->propertyid)
            ->where('country_code', $country_code)
            ->first();

        $existingNationality = DB::table('countries')
            ->where('propertyid', $this->propertyid)
            ->where('nationality', $nationality)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Country name already exists!');
        } elseif ($existingCountryCode) {
            return back()->with('error', 'Country Code already exists!');
        } elseif ($existingNationality) {
            return back()->with('error', 'Nationality already exists!');
        }

        $data = [
            'u_name' => Auth::user()->name,
            'propertyid' => $this->propertyid,
            'name' => $request->input('countryname'),
            'nationality' => $request->input('nationality'),
            'country_code' => $request->input('country_code'),
        ];

        CompanyLog::InsertCountry($data);
        return back()->with('success', 'Country Inserted successfully!');
    }

    public function deletecountry(Request $request)
    {
        $permission = revokeopen(122015);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $country_code = base64_decode($request->input('country_code'));
            $existsStates = DB::table('states')->where('propertyid', $this->propertyid)->where('country', $country_code)->first();
            $existsCities = DB::table('cities')->where('propertyid', $this->propertyid)->where('country', $country_code)->first();
            if ($existsStates || $existsCities) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Cannot Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('countries')->where('country_code', $country_code)->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Country Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Country');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitstate(Request $request)
    {
        $permission = revokeopen(122016);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'country_select' => 'required',
            'state_name' => 'required',
            'state_code' => 'required',
        ]);

        $stateName = $request->input('state_name');
        $stateCode = $request->input('state_code');

        $existingStateName = DB::table('states')
            ->where('propertyid', $this->propertyid)
            ->where('name', $stateName)
            ->first();

        $existingStateCode = DB::table('states')
            ->where('propertyid', $this->propertyid)
            ->where('state_code', $stateCode)
            ->first();

        if ($existingStateName) {
            return back()->with('error', 'State name already exists!');
        } elseif ($existingStateCode) {
            return back()->with('error', 'State Code already exists!');
        }

        $data = [
            'u_name' => Auth::user()->name,
            'propertyid' => $this->propertyid,
            'country' => $request->input('country_select'),
            'name' => $request->input('state_name'),
            'state_code' => $request->input('state_code'),
        ];

        CompanyLog::InsertState($data);
        return back()->with('success', 'State Inserted successfully!');
    }

    public function deletestate(Request $request)
    {
        $permission = revokeopen(122016);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $state_code = base64_decode($request->input('state_code'));
            $exists = DB::table('cities')->where('propertyid', $this->propertyid)->where('state', $state_code)->first();
            if ($exists) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Cannot Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('states')->where('state_code', $state_code)->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'State Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete State');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitcity(Request $request)
    {
        $permission = revokeopen(122017);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'country' => 'required',
            'cityname' => 'required',
            'state' => 'required',
        ]);

        $cityname = $request->input('cityname');
        $zipcode = $request->input('zipcode');

        $existingCityname = DB::table('cities')
            ->where('propertyid', $this->propertyid)
            ->where('cityname', $cityname)
            ->first();

        $existingZipcode = DB::table('cities')
            ->where('propertyid', $this->propertyid)
            ->where('zipcode', $zipcode)
            ->first();

        if ($existingCityname) {
            return back()->with('error', 'City name already exists!');
        }

        $maxcitycode = DB::table('cities')->where('propertyid', $this->propertyid)->max('city_code');

        $data = [
            'city_code' => $maxcitycode + 1,
            'u_name' => Auth::user()->name,
            'propertyid' => $this->propertyid,
            'country' => $request->input('country'),
            'cityname' => $request->input('cityname'),
            'zipcode' => $request->input('zipcode'),
            'state' => $request->input('state'),
        ];

        CompanyLog::InsertCity($data);
        return back()->with('success', 'City Inserted successfully!');
    }

    public function deletecity(Request $request)
    {
        $permission = revokeopen(122017);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $city_code = base64_decode($request->input('city_code'));
            $exists = DB::table('subgroup')->where('propertyid', $this->propertyid)->where('citycode', $city_code)->first();
            if ($exists) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Cannot Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('cities')->where('city_code', $city_code)->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'City Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete City');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatecountry(Request $request)
    {
        $permission = revokeopen(122015);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $country_code = base64_decode($request->input('country_code'));
        $country_data = DB::table('countries')->where('country_code', $country_code)->first();
        return view('property.updatecountryform', ['country_data' => $country_data]);
    }

    public function update_countrystore(Request $request)
    {
        $permission = revokeopen(122015);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $request->validate(
            [
                'countryname' => 'required',
                'country_code' => 'required',
                'nationality' => 'required',
            ]
        );
        $country_code_first = $request->input('country_code');
        $username = Auth::user()->name;
        $data = [
            'name' => $request->input('countryname'),
            'country_code' => $request->input('country_code'),
            'nationality' => $request->input('nationality'),
            'u_name' => $username,
            'propertyid' => $this->propertyid,
        ];

        $update = CompanyLog::update_country($country_code_first, $data);
        if ($update == true) {
            return back()->with('success', 'Country Updated Successfully');
        } else {
            return back()->with('error', 'Unable to Update Country');
        }
    }

    public function updatestate(Request $request)
    {
        $permission = revokeopen(122016);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $state_code = base64_decode($request->input('state_code'));
        $data['country'] = DB::table('countries')->get();
        $state_data = DB::table('states')->where('state_code', $state_code)->first();
        return view('property.updatestateform', ['state_data' => $state_data], $data);
    }

    public function update_statestore(Request $request)
    {
        $permission = revokeopen(122016);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $request->validate(
            [
                'country_select' => 'required',
                'state_name' => 'required',
                'state_code' => 'required',
            ]
        );

        $state_code = $request->input('state_code');
        $username = Auth::user()->name;

        $exists = DB::table('states')->where('propertyid', $this->propertyid)->whereNot('state_code', $state_code)->where('name', $request->input('state_name'))->first();
        if ($exists) {
            return back()->with('error', "State Name Already Exists");
        }
        $data = [
            'country' => $request->input('country_select'),
            'name' => $request->input('state_name'),
            'u_name' => $username,
        ];

        $update = CompanyLog::update_state($state_code, $data);
        if ($update == true) {
            return back()->with('success', 'State Updated Successfully');
        } else {
            return back()->with('error', 'Unable to Update State');
        }
    }

    public function updatecity(Request $request)
    {
        $permission = revokeopen(122017);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $city_code = base64_decode($request->input('city_code'));
        $data['country'] = DB::table('countries')->get();

        $city_data = DB::table('cities')
            ->select('cities.*', 'states.name as statename')
            ->leftJoin('states', 'states.state_code', '=', 'cities.state')
            ->where('cities.propertyid', $this->propertyid)
            ->where('cities.city_code', $city_code)
            ->first();

        return view('property.updatecityform', ['city_data' => $city_data] + $data);
    }


    public function citystoreupdate(Request $request)
    {
        $request->validate(
            [
                'cityname' => 'required',
                'country' => 'required',
                'state' => 'required',
            ]
        );

        $city_code = $request->input('city_code');
        $username = Auth::user()->name;

        $exists = DB::table('cities')->where('propertyid', $this->propertyid)->whereNot('city_code', $city_code)->where('cityname', $request->input('cityname'))->first();
        if ($exists) {
            return back()->with('error', "City Name Already Exists");
        }
        $data = [
            'cityname' => $request->input('cityname'),
            'country' => $request->input('country'),
            'state' => $request->input('state'),
            'zipcode' => $request->input('zipcode'),
            'activeyn' => $request->input('activeyn'),
            'u_name' => $username,
            'u_updatedt' => $this->currenttime
        ];

        $update = Cities::where('propertyid', $this->propertyid)->where('city_code', $city_code)->update($data);

        // $update = CompanyLog::update_city($city_code, $data);
        if ($update == true) {
            return back()->with('success', 'City Updated Successfully');
        } else {
            return back()->with('error', 'Unable to Update City');
        }
    }


    public function submitusermaster(Request $request)
    {
        $permission = revokeopen(122011);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $property_id = $this->propertyid;
        $request->validate([
            'fullname' => 'required',
            'email' => 'required',
            'designation' => 'required',
            'password' => 'required',
        ]);

        $inputUsername = $request->input('fullname');
        $existingusername = DB::table('users')->where('name', $inputUsername)->where('propertyid', $this->propertyid)->where('email', $this->email)->first();

        if ($existingusername) {
            return back()->with('error', 'Username already exists!');
        }

        $datauu = [
            'u_name' => $request->input('fullname'),
            'propertyid' => $this->propertyid,
            'name' => $request->input('fullname'),
            'email' => $request->input('email'),
            'role' => 2,
            'superwiser' => $request->input('designation'),
            'backdate' => $request->backdate,
            'password' => Hash::make($request->input('password')),
        ];

        $datapos = [
            'username' => $request->input('fullname'),
            'propertyid' => $this->propertyid,
            'u_entdt' => $this->currenttime,
            'u_ae' => 'a',
        ];

        UserPermission::insert($datapos);

        // $compdata = DB::table('company')->where('propertyid', $this->propertyid)->where('email', $this->email)->first();

        // $upcompdata = [
        //     'comp_code' => $compdata->comp_code,
        //     'comp_name' => $compdata->comp_name,
        //     'sn_num' => $compdata->sn_num,
        //     'start_dt' => $compdata->start_dt,
        //     'end_dt' => $compdata->end_dt,
        //     'address1' => $compdata->address1,
        //     'address2' => $compdata->address2,
        //     'country' => $compdata->country,
        //     'state' => $compdata->state,
        //     'city' => $compdata->city,
        //     'state_code' => $compdata->state_code,
        //     'mobile' => $compdata->mobile,
        //     'cfyear' => $compdata->cfyear,
        //     'pfyear' => $compdata->pfyear,
        //     'pin' => $compdata->pin,
        //     'pan_no' => $compdata->pan_no,
        //     'nationality' => $compdata->nationality,
        //     'gstin' => $compdata->gstin,
        //     'division_code' => $compdata->division_code,
        //     'trade_name' => $compdata->trade_name,
        //     'logo' => $compdata->logo,
        //     'status' => 1,
        //     'u_name' => $request->input('fullname'),
        //     'propertyid' => $property_id,
        //     'legal_name' => $request->input('fullname'),
        //     'email' => $request->input('email'),
        //     'role' => 'User',
        //     'password' => Hash::make($request->input('password')),
        //     'u_entdt' => $this->currenttime,
        //     'u_ae' => 'a',
        // ];
        // DB::table('company')->insert($upcompdata);
        CompanyLog::InsertUsermaster($datauu);
        return back()->with('success', 'User Inserted successfully!');
    }

    public function disableusermaster(Request $request)
    {
        try {
            $user_id = base64_decode($request->input('userId'));
            $udata = User::where('id', $user_id)->first();
            $jaldiwahasehato📢 = DB::table('users')->where('id', $user_id)->update(['status' => 0]);
            $jaldiwahasehato1📢 = DB::table('company')->where('email', $udata->email)->where('propertyid', $this->propertyid)
                ->where('role', 'User')->where('u_name', $udata->u_name)
                ->update(['status' => 0]);

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'User InActive Successfully');
            } else {
                return back()->with('error', 'Unable to Find User Id');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function enableusermaster(Request $request)
    {
        try {
            $user_id = base64_decode($request->input('userId'));
            $udata = User::where('id', $user_id)->first();
            $jaldiwahasehato📢 = DB::table('users')->where('id', $user_id)->update(['status' => 1]);
            $jaldiwahasehato1📢 = DB::table('company')->where('email', $udata->email)->where('propertyid', $this->propertyid)
                ->where('role', 'User')->where('u_name', $udata->u_name)
                ->update(['status' => 1]);
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'User Active Successfully');
            } else {
                return back()->with('error', 'Unable to Find User Id');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateusermaster(Request $request)
    {
        $permission = revokeopen(122011);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $userid = base64_decode($request->input('u_name'));
        $userdata = DB::table('users')
            ->select('users.*', 'company.role as roleman')
            ->leftjoin('company', 'company.u_name', '=', 'users.u_name')
            ->where('users.u_name', $userid)->first();
        $path = storage_path('app/public/menu.json');
        $jsonData = file_get_contents($path);
        $menuItems = json_decode($jsonData, true);
        // $permdata = DB::table('userpermission')->where('u_name', $userid)->where('propertyid', $this->propertyid)->first();
        return view('property.updateusermaster', ['userdata' => $userdata, 'menuItems' => $menuItems]);
    }

    public function update_usermasterstore(Request $request)
    {
        $request->validate(
            [
                'fullname' => 'required',
                'email' => 'required',
                'designation' => 'required',
            ]
        );
        $userid = $request->input('userid');

        $compdata = DB::table('company')->where('propertyid', $this->propertyid)->where('email', $this->email)->first();

        // $upcompdata = [
        //     'comp_code' => $compdata->comp_code,
        //     'comp_name' => $compdata->comp_name,
        //     'sn_num' => $compdata->sn_num,
        //     'start_dt' => $compdata->start_dt,
        //     'end_dt' => $compdata->end_dt,
        //     'address1' => $compdata->address1,
        //     'address2' => $compdata->address2,
        //     'country' => $compdata->country,
        //     'state' => $compdata->state,
        //     'city' => $compdata->city,
        //     'state_code' => $compdata->state_code,
        //     'mobile' => $compdata->mobile,
        //     'cfyear' => $compdata->cfyear,
        //     'pfyear' => $compdata->pfyear,
        //     'pin' => $compdata->pin,
        //     'pan_no' => $compdata->pan_no,
        //     'nationality' => $compdata->nationality,
        //     'gstin' => $compdata->gstin,
        //     'division_code' => $compdata->division_code,
        //     'trade_name' => $compdata->trade_name,
        //     'logo' => $compdata->logo,
        //     'status' => 1,
        //     'u_name' => $request->input('fullname'),
        //     'propertyid' => $this->propertyid,
        //     'legal_name' => $request->input('fullname'),
        //     'email' => strtolower($request->input('email')),
        //     'u_updatedt' => $this->currenttime,
        //     'u_ae' => 'e',
        // ];

        $dataup = [
            'u_name' => $request->input('fullname'),
            'propertyid' => $this->propertyid,
            'name' => $request->input('fullname'),
            'email' => strtolower($request->input('email')),
            'updated_at' => $this->currenttime,
            'role' => 2,
            'superwiser' => $request->input('designation'),
            'backdate' => $request->backdate,
            'u_ae' => 'e',
        ];

        DB::table('users')->where('propertyid', $this->propertyid)->where('u_name', $userid)->update($dataup);
        // DB::table('company')->where('propertyid', $this->propertyid)->where('u_name', $userid)->update($upcompdata);
        return redirect('usermaster')->with('success', 'User Updated Successfully!');
    }

    public function changecompanydetails(Request $request)
    {

        $request->validate([
            'legal_name' => 'required',
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'email']
        ]);

        $data = [
            'legal_name' => $request->input('legal_name'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'u_name' => $this->username
        ];

        $update = CompanyLog::UpdateCompanyDetail($this->propertyid, $data);

        if ($update == true) {
            return back()->with('success', 'Record Updated Successfully');
        } else {
            return back()->with('error', 'Unable to Update Record');
        }
    }

    public function Utilityoepn()
    {
        return view('property.utility');
    }

    public function inconsistency()
    {
        $permission = revokeopen(122014);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        return view('property.inconsistency');
    }

    public function accountupdate()
    {
        $path = storage_path('app/public/groupac.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::Insertacgroup($propertyid, $u_name, $jsonData);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Group Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Group already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function subgroupupdate()
    {
        $checkaccount = DB::table('acgroup')->where('propertyid', $this->propertyid)->first();
        if (!$checkaccount) {
            return response()->json(['message' => 'Please add Account Group First'], 500);
        }
        $count = DB::table('acgroup')->where('propertyid', $this->propertyid)->count();
        if ($count < 30) {
            return response()->json(['message' => 'Account Group should be equal to or greater than 30'], 500);
        }

        $path = storage_path('app/public/subgroupac.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::Insertsubgroup($propertyid, $u_name, $jsonData);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Sub Group Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Sub Group already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function countryloadupdate()
    {
        $propertyid = $this->propertyid;
        $u_name = $this->username;

        $data = [
            'propertyid' => $propertyid,
            'u_name' => $u_name
        ];

        $inserts = CompanyLog::InsertCountryLoad($data);

        if ($inserts == true) {
            return response()->json(['message' => $inserts . ' Country Inserted Successfully']);
        } elseif ($inserts == false) {
            return response()->json(['message' => 'Country already exists'], 500);
        }
    }


    public function stateloadupdate()
    {
        $checkaccount = DB::table('countries')->where('propertyid', $this->propertyid)->first();

        if (!$checkaccount) {
            return response()->json(['message' => 'Please add Country First'], 500);
        }

        $propertyid = $this->propertyid;
        $u_name = $this->username;

        $data = [
            'propertyid' => $propertyid,
            'u_name' => $u_name
        ];

        $inserts = CompanyLog::InsertStateLoad($data);

        if ($inserts == true) {
            return response()->json(['message' => $inserts . ' State Inserted Successfully']);
        } elseif ($inserts == false) {
            return response()->json(['message' => 'State already exists'], 500);
        }
    }

    public function cityloadupdate()
    {
        $checkStates = DB::table('states')->where('propertyid', $this->propertyid)->first();
        if (!$checkStates) {
            return response()->json(['message' => 'Please add states First'], 500);
        }

        $checkCountries = DB::table('countries')->where('propertyid', $this->propertyid)->first();
        if (!$checkCountries) {
            return response()->json(['message' => 'Please add Country First'], 500);
        }

        $propertyid = $this->propertyid;
        $u_name = $this->username;

        $data = [
            'propertyid' => $propertyid,
            'u_name' => $u_name
        ];
        // This code is so simple, even my cat could understand it. 🐱

        $inserts = CompanyLog::InsertCityLoad($data);

        if ($inserts == true) {
            return response()->json(['message' => $inserts . ' City Inserted Successfully']);
        } elseif ($inserts == false) {
            return response()->json(['message' => 'City already exists'], 500);
        }
    }

    public function sundrymasterloadupdate()
    {
        $path = storage_path('app/public/sundrymaster.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertSundryMaster($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Sundry Master Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Sundry Master already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function sundrytypeloadupdate()
    {
        $checkCountries = DB::table('sundrymast')->where('propertyid', $this->propertyid)->first();
        if (!$checkCountries) {
            return response()->json(['message' => 'Please add Sundry Master First'], 500);
        }
        $count = DB::table('sundrymast')->where('propertyid', $this->propertyid)->count();
        if ($count < 17) {
            return response()->json(['message' => 'Sundry Master should be equal to or greater than 17'], 500);
        }

        $path = storage_path('app/public/sundrytype.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertSundryType($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Sundry Type Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Sundry Type already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function unitmasterloadupdate()
    {
        $path = storage_path('app/public/unitmaster.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertUnitMaster($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Unit Master Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Unit Master already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function housekeepingloadup()
    {
        $path = storage_path('app/public/housekeeping.json');
        $path2 = storage_path('app/public/depart.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            $jsonData2 = json_decode(file_get_contents($path2), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertHousekeep($propertyid, $u_name, $jsonData);
                    $inserts2 = CompanyLog::InsertHousekeep2($propertyid, $u_name, $jsonData2);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' House Keeping Inserted Successfully!']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'House Keeping already exists!'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => "File not found: $path"], 500);
        }
    }

    public function storeloadup()
    {
        $path = storage_path('app/public/housekeeping3.json');
        $path2 = storage_path('app/public/depart3.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            $jsonData2 = json_decode(file_get_contents($path2), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertStore($propertyid, $u_name, $jsonData);
                    $inserts2 = CompanyLog::InsertStore2($propertyid, $u_name, $jsonData2);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Store Inserted Successfully!']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Store already exists!'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => "File not found: $path"], 500);
        }
    }

    public function banquetload()
    {
        $path = storage_path('app/public/depart5.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertHall($propertyid, $u_name, $jsonData);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Hall Inserted Successfully!']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Hall already exists!'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => "File not found: $path"], 500);
        }
    }

    public function loadroomservice()
    {

        $checks = [
            'acgroup' => 'Please add Account Group First',
            'subgroup' => 'Please add Sub Group First',
            'voucher_prefix' => 'Please add Voucher Prefix First',
            'voucher_type' => 'Please add Voucher Type First',
            'revmast' => 'Please add Tax Master First',
        ];

        foreach ($checks as $table => $message) {
            $check = DB::table($table)->where('propertyid', $this->propertyid)->first();
            if (!$check) {
                return response()->json(['message' => $message], 500);
            }
        }

        $path = storage_path('app/public/revmast2.json');
        $path2 = storage_path('app/public/depart4.json');
        $path3 = storage_path('app/public/subgroupac2.json');
        $path4 = storage_path('app/public/voucherprefix2.json');
        $path5 = storage_path('app/public/vouchertype2.json');
        $path6 = storage_path('app/public/itemcatmast.json');
        $path7 = storage_path('app/public/usermodule.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            $jsonData2 = json_decode(file_get_contents($path2), true);
            $jsonData3 = json_decode(file_get_contents($path3), true);
            $jsonData4 = json_decode(file_get_contents($path4), true);
            $jsonData5 = json_decode(file_get_contents($path5), true);
            $jsonData6 = json_decode(file_get_contents($path6), true);
            $jsonData7 = json_decode(file_get_contents($path7), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                $compcode = Companyreg::where('propertyid', Auth::user()->propertyid)->value('comp_code');
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertRoomS1($propertyid, $u_name, $jsonData);
                    $inserts2 = CompanyLog::InsertRoomS2($propertyid, $u_name, $jsonData2);
                    $inserts3 = CompanyLog::InsertRoomS3($propertyid, $u_name, $jsonData3);
                    $inserts4 = CompanyLog::InsertRoomS4($propertyid, $u_name, $jsonData4);
                    $inserts5 = CompanyLog::InsertRoomS5($propertyid, $u_name, $jsonData5);
                    $inserts6 = CompanyLog::InsertRoomS6($propertyid, $u_name, $jsonData6);
                    $inserts7 = CompanyLog::InsertRoomS7($propertyid, $u_name, $jsonData7);
                    $inserts8 = CompanyLog::InsertRoomS8($propertyid, $compcode, $u_name, $jsonData7);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Room Service Inserted Successfully!']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Room Service already exists!'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => "File not found: $path"], 500);
        }
    }

    public function taxloadupdate()
    {
        $accountGroupCount = DB::table('acgroup')->where('propertyid', $this->propertyid)->count();
        $subGroupCount = DB::table('subgroup')->where('propertyid', $this->propertyid)->count();

        if (!$accountGroupCount) {
            return response()->json(['message' => 'Please add Account Group First'], 500);
        } elseif (!$subGroupCount) {
            return response()->json(['message' => 'Please add Sub Group First'], 500);
        } elseif ($accountGroupCount < 30) {
            return response()->json(['message' => 'Account Group should be equal to or greater than 30'], 500);
        } elseif ($subGroupCount < 19) {
            return response()->json(['message' => 'Sub Group should be equal to or greater than 19'], 500);
        }

        $path = storage_path('app/public/revmast.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertTaxLoad($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Tax Inserted Successfully']);
                    } else {
                        return response()->json(['message' => 'Tax already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function taxloadstructureupdate()
    {
        $accountGroupCount = DB::table('acgroup')->where('propertyid', $this->propertyid)->count();
        $subGroupCount = DB::table('subgroup')->where('propertyid', $this->propertyid)->count();
        $taxCount = DB::table('revmast')->where('propertyid', $this->propertyid)->count();

        if (!$accountGroupCount) {
            return response()->json(['message' => 'Please add Account Group First'], 500);
        } elseif (!$subGroupCount) {
            return response()->json(['message' => 'Please add Sub Group First'], 500);
        } elseif ($accountGroupCount < 30) {
            return response()->json(['message' => 'Account Group should be equal to or greater than 30'], 500);
        } elseif ($subGroupCount < 19) {
            return response()->json(['message' => 'Sub Group should be equal to or greater than 19'], 500);
        } elseif (!$taxCount) {
            return response()->json(['message' => 'Please add Tax First'], 500);
        } elseif ($taxCount < 7) {
            return response()->json(['message' => 'Taxes should be equal to or greater than 7'], 500);
        }

        $path = storage_path('app/public/taxstructure.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertTaxLoad2($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Tax Structure Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Tax Structure already exists'], 500);
                    } else {
                        return response()->json(['message' => 'Tax Structure already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function submittax(Request $request)
    {
        $permission = revokeopen(121111);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $username = Auth::user()->name;
        $property_id = $this->propertyid;

        $validatedData = $request->validate([
            'taxname' => 'required',
            'sundryname' => 'required',
            'ledgeraccount' => 'required',
            'activeyn' => 'required',
        ]);
        $taxname = $request->input('taxname');

        try {
            $existingName = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where('name', $taxname)
                ->first();
            if ($existingName) {
                return back()->with('error', 'Tax Name already exists!');
            }

            $shortname = $request->input('taxname');
            $firstCharacter = substr($shortname, 0, 2);
            $lastchar = substr($shortname, -2);
            $rev_code = $this->propertyid . $firstCharacter . $lastchar;

            $data = [
                'rev_code' => $rev_code,
                'u_name' => Auth::user()->name,
                'propertyid' => $this->propertyid,
                'name' => $request->input('taxname'),
                'sundry' => $request->sundryname,
                'ac_code' => $request->ledgeraccount,
                'payable_ac' => $request->payableaccount,
                'unregistered_ac' => $request->unregaccount,
                'field_type' => 'T',
                'active' => $request->input('activeyn'),
                'u_entdt' => $this->currenttime
            ];

            Revmast::insert($data);
            return back()->with('success', 'Tax Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to insert Tax!');
        }
    }

    public function deletetax(Request $request)
    {
        $permission = revokeopen(121111);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $ac_code = base64_decode($request->input('ac_code'));
            $exists = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('tax_code', base64_decode($request->input('rev_code')))->first();
            if ($exists) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Can Not Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('revmast')->where('rev_code', base64_decode($request->input('rev_code')))
                ->where('sn', base64_decode($request->input('sn')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Tax Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Tax');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function openupdatetax(Request $request)
    {
        $permission = revokeopen(121111);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $sn = base64_decode($request->input('sn'));
        $taxdata = DB::table('revmast')->where('sn', $sn)->first();
        $subgroup = SubGroup::where('propertyid', $this->propertyid)->orderBy('name')->get();
        $sundrymast = SundryMast::where('propertyid', $this->propertyid)->orderBy('name')->get();
        return view('property.updatetaxform', [
            'taxdata' => $taxdata,
            'subgroup' => $subgroup,
            'sundrymast' => $sundrymast
        ]);
    }

    public function namelistfetch(Request $request)
    {
        $docid = $request->input('docid');
        $namelists = RoomOcc::select(
            'roomocc.name',
            'roomocc.roomno',
            'paycharge.taxper',
            'paycharge.amtdr',
            'paycharge.amtdr',
            'paycharge.amtcr'
        )
            ->leftJoin('paycharge', 'paycharge.folionodocid', '=', 'roomocc.docid')
            ->where('roomocc.docid', $docid)
            ->first();

        return json_encode($namelists);
    }

    public function taxstoreupdate(Request $request)
    {

        $validatedData = $request->validate([
            'taxname' => 'required',
            'activeyn' => 'required',
        ]);

        try {
            $taxname = $request->input('taxname');
            $existingName = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->whereNot('sn', $request->input('sn'))
                ->where('name', $taxname)
                ->first();
            if ($existingName) {
                return back()->with('error', 'Tax Name already exists!');
            }

            $data = [
                'u_name' => Auth::user()->name,
                'propertyid' => $this->propertyid,
                'name' => $request->input('taxname'),
                'sundry' => $request->sundryname,
                'ac_code' => $request->ledgeraccount,
                'payable_ac' => $request->payableaccount,
                'unregistered_ac' => $request->unregaccount,
                'active' => $request->input('activeyn'),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e'
            ];

            Revmast::where('propertyid', $this->propertyid)->where('sn', $request->sn)->update($data);
            return redirect('taxmaster')->with('success', 'Tax Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Tax!');
            // echo $e->getMessage();
        }
    }

    public function submittaxstructure(Request $request)
    {
        $permission = revokeopen(121112);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'stru_name' => 'required',
            'tax_code1' => 'required',
        ]);

        $existingName = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->where('name', $request->input('stru_name'))
            ->first();
        if ($existingName) {
            return back()->with('error', 'Tax Structure Name already exists!');
        }

        $insertData = array(
            'name' => $request->input('stru_name'),
            'tax_code' => null,
            'rate' => null,
            'nature' => null,
            'limits' => null,
            'comp_operator' => null,
            'condapp' => null,
            'limit1' => null
        );
        // Don't ask why this works. It just does. 🤷‍♂️
        foreach ($request->input() as $key => $value) {
            if (preg_match('/^tax_code(\d+)$/', $key, $matches)) {
                $sno = $matches[1];
                $insertData['tax_code'] = $value;
                $insertData['rate'] = $request->input('rate' . $sno);
                $insertData['nature'] = $request->input('applyon' . $sno);
                $insertData['limits'] = $request->input('limits' . $sno);
                $insertData['comp_operator'] = $request->input('comparison' . $sno);
                $insertData['condapp'] = $request->input('condition' . $sno);
                $insertData['limit1'] = $request->input('limit' . $sno);
                $insertData['sno'] = $sno;
                $inserts = CompanyLog::InsertTaxStructure($insertData, $this->propertyid, Auth::user()->name);
            }
        }


        if ($inserts == 'success') {
            return back()->with('success', 'Tax Structure Inserted successfully!');
        } else {
            return back()->with('error', 'Unable to insert Tax Structure!' . $inserts);
        }
    }

    public function openupdatetaxstru(Request $request)
    {
        $permission = revokeopen(121112);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $taxdata = DB::table('taxstru')
            ->join('revmast', 'taxstru.tax_code', '=', 'revmast.rev_code')
            ->select('revmast.rev_code', 'revmast.name as revname', 'taxstru.*')
            ->where('taxstru.name', base64_decode($request->input('name')))
            ->where('taxstru.propertyid', base64_decode($request->input('propertyid')))
            ->get();

        $taxname = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('rev_code', base64_decode($request->input('tax_code')))
            ->pluck('name')
            ->first();

        $taxdatamain = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->get();

        return view('property.updatetaxstructure', [
            'taxdata' => $taxdata,
            'taxname' => $taxname,
            'taxdatamain' => $taxdatamain
        ]);
    }


    public function taxstructurestoreupdate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $existingName = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->where('name', $request->input('name'))
            ->whereNot('name', $request->input('oldtaxstruname'))
            ->first();

        if ($existingName) {
            return back()->with('error', 'Tax Structure Name already exists!');
        }

        $snolist = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->where('name', $request->input('oldtaxstruname'))
            ->get();
        $maxSn = $snolist->max('sno');

        $sno = 0;
        foreach ($request->input() as $key => $value) {
            if (preg_match('/^tax_code(\d+)$/', $key, $matches)) {
                $sno = $matches[1];
                $insertData['tax_code'] = $value;
                $insertData['rate'] = $request->input('rate' . $sno);
                $insertData['nature'] = $request->input('applyon' . $sno);
                $insertData['limits'] = $request->input('limits' . $sno);
                $insertData['comp_operator'] = $request->input('comparison' . $sno);
                $insertData['condapp'] = $request->input('condition' . $sno);
                $insertData['limit1'] = $request->input('limit' . $sno);
                $insertData['sno'] = $sno;
            }
        }

        if ($sno > $maxSn) {
            $insertData = array(
                'name' => $request->input('name'),
                'tax_code' => null,
                'rate' => null,
                'nature' => null,
                'limits' => null,
                'comp_operator' => null,
                'condapp' => null,
                'limit1' => null,
                'sysYN' => 'N',
            );
            foreach ($request->input() as $key => $value) {
                if (preg_match('/^tax_code(\d+)$/', $key, $matches)) {
                    $sno = $matches[1];
                    $insertData['tax_code'] = $value;
                    $insertData['rate'] = $request->input('rate' . $sno);
                    $insertData['nature'] = $request->input('applyon' . $sno);
                    $insertData['limits'] = $request->input('limits' . $sno);
                    $insertData['comp_operator'] = $request->input('comparison' . $sno);
                    $insertData['condapp'] = $request->input('condition' . $sno);
                    $insertData['limit1'] = $request->input('limit' . $sno);
                    $insertData['sno'] = $sno;
                    // Good luck understanding this masterpiece! 🤯
                    $shortname = $insertData['name'];
                    $firstCharacter = substr($shortname, 0, 2);
                    $lastchar = substr($shortname, -2);
                    $str_code = $request->input('oldstr_code');
                    $insertData = [
                        'propertyid' => $this->propertyid,
                        'u_name' => Auth::user()->name,
                        'str_code' => $str_code,
                        'u_entdt' => $this->currenttime,
                        'sysYN' => 'N',
                    ] + $insertData;
                    DB::table('taxstru')
                        ->where('propertyid', $this->propertyid)
                        ->where('name', $request->input('oldtaxstruname'))
                        ->where('u_entdt', '<', $this->currenttime)
                        ->delete();
                    DB::table('taxstru')->insert($insertData);
                }
            }
            return back()->with('success', 'Tax Structure Updated and New Rows Inserted Successfully');
        } else if ($sno == $maxSn) {
            foreach ($snolist as $list) {
                $shortname = $request->input('name');
                $firstCharacter = substr($shortname, 0, 2);
                $lastchar = substr($shortname, -2);
                $str_code = $request->input('oldstr_code');
                $data = [
                    'name' => $request->input('name'),
                    'str_code' => $str_code,
                    "tax_code" => $request->input("tax_code{$list->sno}"),
                    "rate" => $request->input("rate{$list->sno}"),
                    "nature" => $request->input("applyon{$list->sno}"),
                    "limits" => $request->input("limits{$list->sno}"),
                    "comp_operator" => $request->input("comparison{$list->sno}"),
                    "limit1" => $request->input("limit{$list->sno}"),
                    "condapp" => $request->input("condition{$list->sno}"),
                    "u_updatedt" => $this->currenttime,
                    'propertyid' => $this->propertyid,
                    'u_name' => Auth::user()->name,
                    'u_ae' => 'e',
                    'sysYN' => 'N',
                ];

                $update = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('name', $request->input('oldtaxstruname'))
                    ->where('sno', $list->sno)
                    ->update($data);
            }
            return back()->with('success', 'Tax Structure Updated Successfully');
        }
        exit;
    }

    public function deletetaxstructure(Request $request)
    {
        $permission = revokeopen(121112);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $name = base64_decode($request->input('name'));
            $propertyid = base64_decode($request->input('propertyid'));
            $jaldiwahasehato📢 = DB::table('taxstru')->where('name', $name)
                ->where('propertyid', $propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Tax Structure Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Tax Structure');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    // Coded by astrogeeksagar
    public function submitledger(Request $request)
    {
        $permission = revokeopen(122020);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'group_code' => 'required',
        ]);

        $existingName = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('name', $request->input('name'))
            ->first();
        if ($existingName) {
            return back()->with('error', 'Ledger Name already exists!');
        }

        $nature = DB::table('acgroup')->where('propertyid', $this->propertyid)
            ->where('group_code', $request->input('group_code'))->pluck('nature')->first();

        $sub_code = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->count() + 1;

        $data = collect($request->except('_token'))->filter(function ($value, $key) {
            return !preg_match('/^(refdate|narration|amount|openingbalance|totalrows|crdr)/i', $key);
        })->toArray();

        try {
            $insertdata = [
                'sub_code' => $sub_code . $this->propertyid,
                'nature' => $nature,
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'subyn' => 1,
            ] + $data;

            DB::table('subgroup')->insert($insertdata);

            if (!empty($request->refdate1)) {
                $vtype = "F_AO";
                $ncurdate = $this->ncurdate;
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $ncurdate)
                    ->whereDate('date_to', '>=', $ncurdate)
                    ->first();
                if ($chkvpf === null || $chkvpf === '0') {
                    return response()->json([
                        'redirecturl' => '',
                        'status' => 'error',
                        'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                    ]);
                }

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $start_srl_no,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $sub_code . $this->propertyid,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');
            }

            return back()->with('success', 'Ledger inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Ledger!');
        }
    }

    public function submitgeneralparam(Request $request)
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        if (!empty($request->input('fombillcopies'))) {
            $validatedData = $request->validate([
                'fombillcopies' => 'required|integer',
            ]);
        }

        $tableName = 'enviro_form';
        $data = $request->except(['_token', 'cashpurcheffect']);

        $envgen = EnviroGeneral::where('propertyid', $this->propertyid)->first();
        $envgen->cashpurcheffect = $request->cashpurcheffect;
        $envgen->save();

        try {
            $updateData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_ae' => 'e',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
            ] + $data;
            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->update($updateData);

            return back()->with('success', 'General Parameter Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update General Parameter!');
        }
    }

    public function submitcheckoutparams(Request $request)
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'enviro_form';
        $data = $request->all();
        unset($data['_token']);

        try {
            $updateData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->update($updateData);

            return back()->with('success', 'Checkout Parameter Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Checkout Parameter!');
        }
    }

    public function submitpostingparams(Request $request)
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'enviro_form';
        $data = $request->all();
        unset($data['_token']);

        try {
            $updateData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->update($updateData);

            return back()->with('success', 'Posting Parameter Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Posting Parameter!');
        }
    }

    public function submitrateparams(Request $request)
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'enviro_form';
        $data = $request->all();
        unset($data['_token']);

        try {
            $updateData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->update($updateData);

            return back()->with('success', 'Rate Parameter Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Rate Parameter!');
        }
    }

    public function submitrateinstructionparamstore(Request $request)
    {
        $permission = revokeopen(121211);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'enviro_form';
        $data = $request->all();
        unset($data['_token']);

        try {
            $updateData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->update($updateData);

            return back()->with('success', 'Instructions Parameter Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Instructions Parameter!');
        }
    }

    public function deleteledger(Request $request)
    {
        $permission = revokeopen(122020);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $sub_code = base64_decode($request->input('sub_code'));
            $exists = DB::table('subgroup')->where('propertyid', $this->propertyid)->where('group_code', $sub_code)->first();
            $exists_tax = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where(function ($query) use ($sub_code) {
                    $query->where('ac_code', $sub_code)
                        ->orWhere('payable_ac', $sub_code)
                        ->orWhere('unregistered_ac', $sub_code);
                })
                ->first();
            if ($exists || $exists_tax) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Can Not Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('subgroup')->where('sub_code', $sub_code)->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Ledger Account Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Ledger Account');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateledgerstore(Request $request)
    {
        $permission = revokeopen(122020);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'group_code' => 'required',
        ]);
        $tableName = 'subgroup';

        try {
            $existingName = DB::table('subgroup')
                ->where('propertyid', $this->propertyid)
                ->whereNot('sub_code', $request->input('sub_code'))
                ->where('name', $request->input('name'))
                ->first();
            if ($existingName) {
                return back()->with('error', 'Ledger Name already exists!');
            }

            $data = collect($request->except('_token'))->filter(function ($value, $key) {
                return !preg_match('/^(refdate|narration|amount|openingbalance|totalrows|crdr)/i', $key);
            })->toArray();

            $currenttime = (new CompanyLog())->getCurrentTime();

            $nature = DB::table('acgroup')->where('group_code', $data['group_code'])->pluck('nature')->first();
            $insertData = [
                'u_updatedt' => $currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'nature' => $nature,
            ] + $data;
            DB::table($tableName)->where('sub_code', $request->sub_code)
                ->where('propertyid', $this->propertyid)
                ->update($insertData);

            if (!empty($request->refdate1)) {

                $ledgerdata = Ledger::where('subcode', $request->sub_code)
                    ->where('propertyid', $this->propertyid)
                    ->where('vtype', 'F_AO')
                    ->first();

                if (is_null($ledgerdata)) {
                    $vtype = "F_AO";
                    $ncurdate = $this->ncurdate;
                    $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->whereDate('date_from', '<=', $ncurdate)
                        ->whereDate('date_to', '>=', $ncurdate)
                        ->first();
                    if ($chkvpf === null || $chkvpf === '0') {
                        return response()->json([
                            'redirecturl' => '',
                            'status' => 'error',
                            'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                        ]);
                    }

                    $vno = $chkvpf->start_srl_no + 1;
                    $vprefix = $chkvpf->prefix;

                    $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                    VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->where('prefix', $vprefix)
                        ->increment('start_srl_no');
                } else {

                    $docid = $ledgerdata->docid;
                    $vno = $ledgerdata->vno;
                    $vprefix = $ledgerdata->vprefix;

                    Ledger::where('subcode', $request->sub_code)
                        ->where('propertyid', $this->propertyid)
                        ->where('vtype', 'F_AO')
                        ->delete();
                }

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $vno,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => 'F_AO',
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $request->sub_code,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }
            }

            return back()->with('success', 'Ledger Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function submitcomp_master(Request $request)
    {
        $permission = revokeopen(122018);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'group_code' => 'required',
        ]);

        try {

            $existingName = DB::table('subgroup')
                ->where('propertyid', $this->propertyid)
                ->where('name', $request->input('name'))
                ->whereNotNull('sub_code')
                ->first();

            if ($existingName) {
                return back()->with('error', 'Company Master Name already exists!');
            }

            $maxSubCodeResult = DB::table('subgroup')
                ->select(DB::raw('MAX(CAST(sub_code AS SIGNED)) AS max_sub_code'))
                ->where('propertyid', $this->propertyid)
                ->first();
            $maxSubCode = $maxSubCodeResult->max_sub_code;
            $plength = $this->ptlngth;
            if ($maxSubCode == null) {
                $comp_code = 1;
            } else {
                $comp_code = substr($maxSubCode, 0, $plength) + 1;
            }

            $nature = DB::table('acgroup')->where('group_code', $request->group_code)->pluck('nature')->first();
            $insertData = [
                'sub_code' => $comp_code . $this->propertyid,
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'nature' => $nature,
                'subyn' => 1,
                'name' => $request->name,
                'group_code' => $request->group_code,
                'comp_type' => $request->comp_type,
                'allow_credit' => $request->allow_credit,
                'mapcode' => $request->mapcode,
                'conperson' => $request->conperson,
                'discounttype' => $request->discounttype,
                'address' => $request->address,
                'citycode' => $request->citycode,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'panno' => $request->panno,
                'gstin' => $request->gstin,
                'activeyn' => $request->activeyn,
            ];

            $inserts = DB::table('subgroup')->insert($insertData);

            if (!empty($request->refdate1)) {
                $vtype = "F_AO";
                $ncurdate = $this->ncurdate;
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $ncurdate)
                    ->whereDate('date_to', '>=', $ncurdate)
                    ->first();
                if ($chkvpf === null || $chkvpf === '0') {
                    return response()->json([
                        'redirecturl' => '',
                        'status' => 'error',
                        'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                    ]);
                }

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $start_srl_no,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $comp_code . $this->propertyid,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }
            }

            return back()->with('success', 'Company Master inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function deletecomp_mast(Request $request)
    {
        $permission = revokeopen(122018);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $comp_code = base64_decode($request->input('comp_code'));
            $jaldiwahasehato📢 = DB::table('subgroup')->where('sub_code', $comp_code)
                ->where('sn', base64_decode($request->input('sn')))->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Company Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Company Master');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update_compmaster(Request $request)
    {
        $permission = revokeopen(122018);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'group_code' => 'required',
        ]);

        try {
            $pcount = $request->pcount;
            if ($pcount > 0) {
                CompanyDiscount::where('propertyid', $this->propertyid)->where('compcode', $request->sub_code)->delete();
                for ($i = 1; $i <= $pcount; $i++) {
                    // return $request->input("planamt2");
                    // return $request->input("roomcat$i");
                    if (!empty($request->input("roomcat$i"))) {
                        $compdiscount = new CompanyDiscount;
                        $compdiscount->propertyid = $this->propertyid;
                        $compdiscount->compcode = $request->sub_code;
                        $compdiscount->sno = $i;
                        $compdiscount->roomcatcode = $request->input("roomcat$i");
                        $compdiscount->adult = $request->input("adult$i");
                        $compdiscount->fixrate = $request->input("rate$i") ?? '';
                        $compdiscount->plan = $request->input("plan$i") ?? '';
                        $compdiscount->planamount = $request->input("planamt$i") ?? '';
                        $compdiscount->taxinc = $request->input("taxinc$i") ?? 'N';
                        $compdiscount->save();
                    }
                }
            }

            // return;

            $existingName = DB::table('subgroup')
                ->where('propertyid', $this->propertyid)
                ->whereNot('sub_code', $request->input('sub_code'))
                ->where('name', $request->input('name'))
                ->first();
            if ($existingName) {
                return back()->with('error', 'Company Master Name Already Exists!');
            }

            $nature = DB::table('acgroup')->where('group_code', $request->group_code)->pluck('nature')->first();
            $insertData = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'nature' => $nature,
                'u_ae' => 'e',
                'name' => $request->name,
                'group_code' => $request->group_code,
                'comp_type' => $request->comp_type,
                'allow_credit' => $request->allow_credit,
                'mapcode' => $request->mapcode,
                'conperson' => $request->conperson,
                'discounttype' => $request->discounttype,
                'address' => $request->address,
                'citycode' => $request->citycode,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'panno' => $request->panno,
                'gstin' => $request->gstin,
                'activeyn' => $request->activeyn,
            ];

            DB::table('subgroup')->where('sub_code', $request->sub_code)
                ->where('sn', $request->sn)
                ->where('propertyid', $this->propertyid)
                ->update($insertData);

            if (!empty($request->refdate1)) {

                $ledgerdata = Ledger::where('subcode', $request->sub_code)
                    ->where('propertyid', $this->propertyid)
                    ->where('vtype', 'F_AO')
                    ->first();

                if (is_null($ledgerdata)) {
                    $vtype = "F_AO";
                    $ncurdate = $this->ncurdate;
                    $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->whereDate('date_from', '<=', $ncurdate)
                        ->whereDate('date_to', '>=', $ncurdate)
                        ->first();
                    if ($chkvpf === null || $chkvpf === '0') {
                        return response()->json([
                            'redirecturl' => '',
                            'status' => 'error',
                            'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                        ]);
                    }

                    $start_srl_no = $chkvpf->start_srl_no + 1;
                    $vprefix = $chkvpf->prefix;

                    $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;
                } else {

                    $docid = $ledgerdata->docid;
                    $vno = $ledgerdata->vno;
                    $vprefix = $ledgerdata->vprefix;

                    Ledger::where('subcode', $request->sub_code)
                        ->where('propertyid', $this->propertyid)
                        ->where('vtype', 'F_AO')
                        ->delete();
                }

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $vno,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => 'F_AO',
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $request->sub_code,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }
            }

            return redirect('companymaster')->with('success', 'Company Master Updated successfully!');
        } catch (Exception $e) {
            // return $e->getMessage() . ' On Line: ' . $e->getLine();
            return back()->with('error', 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function voucherprefixloadupdate()
    {
        $path = storage_path('app/public/voucherprefix.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                $inserts = CompanyLog::InsertVoucherPrefix($propertyid, $u_name, $jsonData);

                if ($inserts > 0) {
                    return response()->json(['message' => $inserts . ' Voucher Prefixes Inserted Successfully']);
                } else {
                    return response()->json(['message' => 'Voucher Prefixes already exist'], 500);
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }
    public function vouchertypeloadupdate()
    {
        $path = storage_path('app/public/vouchertype.json');

        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;

                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertVoucherType($propertyid, $u_name, $jsonData);

                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Voucher Type Inserted Successfully']);
                    } elseif ($inserts == false) {
                        return response()->json(['message' => 'Voucher Type already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function settlementload()
    {
        $subGroupCount = DB::table('subgroup')->where('propertyid', $this->propertyid)->count();
        if (!$subGroupCount) {
            return response()->json(['message' => 'Please add Sub Group First'], 500);
        } elseif ($subGroupCount < 19) {
            return response()->json(['message' => 'Sub Group should be equal to or greater than 19'], 500);
        }

        $path = storage_path('app/public/settlement.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertSettlement($propertyid, $u_name, $jsonData);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Settlement Inserted Successfully']);
                    } else {
                        return response()->json(['message' => 'Settlement already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function travelagentload()
    {
        $accGroupCount = DB::table('acgroup')->where('propertyid', $this->propertyid)->count();
        if (!$accGroupCount) {
            return response()->json(['message' => 'Please add Account Group First'], 500);
        } elseif ($accGroupCount < 19) {
            return response()->json(['message' => 'Account Group should be equal to or greater than 19'], 500);
        }

        $path = storage_path('app/public/travelagent.json');
        if (file_exists($path)) {
            $data = file_get_contents($path);
            $jsonData = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertTravelAgent($propertyid, $u_name, $jsonData);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Travel Agent Inserted Successfully']);
                    } else {
                        return response()->json(['message' => 'Travel Agent already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => 'JSON parsing error: ' . json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found: ' . $path], 500);
        }
    }

    public function fixchargesload()
    {
        $subGroupCount = DB::table('subgroup')->where('propertyid', $this->propertyid)->count();
        $taxstruCount = DB::table('taxstru')->where('propertyid', $this->propertyid)->count();
        if (!$subGroupCount) {
            return response()->json(['message' => 'Please add Sub Group First'], 500);
        } elseif (!$taxstruCount) {
            return response()->json(['message' => 'Please add Tax Structure First'], 500);
        } elseif ($subGroupCount < 19) {
            return response()->json(['message' => 'Sub Group should be equal to or greater than 19'], 500);
        } elseif ($taxstruCount < 7) {
            return response()->json(['message' => 'Tax Structure should be equal to or greater than 7'], 500);
        }

        $path = storage_path('app/public/fixcharges.json');
        if (file_exists($path)) {
            $path2 = storage_path('app/public/busssource.json');
            $path3 = storage_path('app/public/gueststats.json');
            $path4 = storage_path('app/public/roomfeature.json');
            $data = file_get_contents($path);
            $data2 = file_get_contents($path2);
            $data3 = file_get_contents($path3);
            $data4 = file_get_contents($path4);
            $jsonData = json_decode($data, true);
            $jsonData2 = json_decode($data2, true);
            $jsonData3 = json_decode($data3, true);
            $jsonData4 = json_decode($data4, true);
            $jsonData5 = json_decode(file_get_contents(storage_path('app/public/depart2.json')), true);
            $jsonData6 = json_decode(file_get_contents(storage_path('app/public/housekeeping2.json')), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $propertyid = $this->propertyid;
                $u_name = $this->username;
                foreach ($jsonData as $data) {
                    $inserts = CompanyLog::InsertFixcharges($propertyid, $u_name, $jsonData);
                    $inserts2 = CompanyLog::Insertbussource($propertyid, $u_name, $jsonData2);
                    $inserts3 = CompanyLog::Insertgueststats($propertyid, $u_name, $jsonData3);
                    $inserts4 = CompanyLog::Insertroomfeature($propertyid, $u_name, $jsonData4);
                    $inserts5 = CompanyLog::Insertdepart2($propertyid, $u_name, $jsonData5);
                    $inserts5 = CompanyLog::InsertHouseup2($propertyid, $u_name, $jsonData6);
                    if ($inserts == true) {
                        return response()->json(['message' => $inserts . ' Fix Charges Inserted Successfully']);
                    } else {
                        return response()->json(['message' => 'Fix Charges already exists'], 500);
                    }
                }
            } else {
                return response()->json(['message' => json_last_error_msg()], 500);
            }
        } else {
            return response()->json(['message' => 'File not found:' . $path], 500);
        }
    }

    public function submitbsourcestore(Request $request)
    {
        $permission = revokeopen(121212);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'busssource';
        $data = $request->except('_token');
        $bcodemax = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('bcode');

        $bcode = substr($bcodemax, 0, -$this->ptlngth) + 1 . $this->propertyid;

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Business Source Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'bcode' => $bcode,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;
            DB::table($tableName)->insert($insertdata);
            return back()->with('success', 'Business Source Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Business Source!: ' . $e->getMessage());
        }
    }

    public function updatebsourcestore(Request $request)
    {
        $permission = revokeopen(121212);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'busssource';
        $data = $request->except('_token');

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->whereNot('bcode', $request->input('bcode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Business Source Name already exists!');
        }

        try {
            $updatedata = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('bcode', $request->input('bcode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Business Source Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Business Source!');
        }
    }

    public function deletebsource(Request $request)
    {
        $permission = revokeopen(121212);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $bcode = base64_decode($request->input('bcode'));
            $jaldiwahasehato📢 = DB::table('busssource')
                ->where('propertyid', $this->propertyid)
                ->where('bcode', $bcode)->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Business Source Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Business Source');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitgueststatusstore(Request $request)
    {
        $permission = revokeopen(121213);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'gueststats';
        $data = $request->except('_token');
        $gcode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->count() + 1;

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Guest Status Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'gcode' => $gcode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;
            DB::table($tableName)->insert($insertdata);
            return back()->with('success', 'Guest Status Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Guest Status!');
        }
    }

    public function updategueststatusstore(Request $request)
    {
        $permission = revokeopen(121213);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'gueststats';
        $data = $request->except('_token');
        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->whereNot('gcode', $request->input('gcode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Guest Status Name already exists!');
        }

        try {
            $updatedata = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('gcode', $request->input('gcode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Guest Status Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Guest Status!');
        }
    }

    public function deletegueststatus(Request $request)
    {
        $permission = revokeopen(121213);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $gcode = base64_decode($request->input('gcode'));
            $jaldiwahasehato📢 = DB::table('gueststats')
                ->where('propertyid', $this->propertyid)
                ->where('gcode', $gcode)->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Guest Status Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Guest Status');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitchargemaster(Request $request)
    {
        $permission = revokeopen(121214);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // echo $request->totalrows;
        // exit;
        $tableName = 'revmast';
        $data = collect($request->except('_token'))->filter(function ($value, $key) {
            return !preg_match('/^(refdate|narration|amount|openingbalance|totalrows|crdr)/i', $key);
        })->toArray();
        $existingName = DB::table($tableName)
            ->where('name', $request->input('name'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Charge Master already exists!');
        }

        $shortname = $request->input('name');
        $firstCharacter = substr($shortname, 0, 2);
        $lastchar = substr($shortname, -2);
        $rev_code = $firstCharacter . $lastchar . $this->propertyid;

        try {
            $insertdata = [
                'rev_code' => $rev_code,
                'seq_no' => $request->input('seq_no'),
                'u_entdt' => $this->currenttime,
                'flag_type' => 'FOM',
                'field_type' => 'C',
                'sysYN' => 'N',
                'Desk_code' => 'FOM' . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;
            DB::table($tableName)->insert($insertdata);

            if (!empty($request->refdate1)) {
                $vtype = "F_AO";
                $ncurdate = $this->ncurdate;
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $ncurdate)
                    ->whereDate('date_to', '>=', $ncurdate)
                    ->first();
                if ($chkvpf === null || $chkvpf === '0') {
                    return response()->json([
                        'redirecturl' => '',
                        'status' => 'error',
                        'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                    ]);
                }

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $start_srl_no,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $rev_code,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');
            }

            return back()->with('success', 'Charge Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
            // echo 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine();
        }
    }

    public function updatechargemasterstore(Request $request)
    {
        $permission = revokeopen(121214);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'revmast';
        $data = collect($request->except('_token'))->filter(function ($value, $key) {
            return !preg_match('/^(refdate|narration|amount|openingbalance|totalrows|crdr)/i', $key);
        })->toArray();

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->whereNot('sn', $request->input('sn'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Charge Master Name already exists!');
        }

        try {
            $updatedata = [
                'seq_no' => $request->input('seq_no'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('sn', $request->input('sn'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);

            if (!empty($request->refdate1)) {

                $ledgerdata = Ledger::where('subcode', $request->rev_code)
                    ->where('propertyid', $this->propertyid)
                    ->where('vtype', 'F_AO')
                    ->first();

                if (is_null($ledgerdata)) {
                    $vtype = "F_AO";
                    $ncurdate = $this->ncurdate;
                    $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->whereDate('date_from', '<=', $ncurdate)
                        ->whereDate('date_to', '>=', $ncurdate)
                        ->first();
                    if ($chkvpf === null || $chkvpf === '0') {
                        return response()->json([
                            'redirecturl' => '',
                            'status' => 'error',
                            'message' => 'You are not eligible to submit: ' . date('d-m-Y', strtotime($ncurdate)),
                        ]);
                    }

                    $vno = $chkvpf->start_srl_no + 1;
                    $vprefix = $chkvpf->prefix;

                    $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                    VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->where('prefix', $vprefix)
                        ->increment('start_srl_no');
                } else {

                    $docid = $ledgerdata->docid;
                    $vno = $ledgerdata->vno;
                    $vprefix = $ledgerdata->vprefix;

                    Ledger::where('subcode', $request->rev_code)
                        ->where('propertyid', $this->propertyid)
                        ->where('vtype', 'F_AO')
                        ->delete();
                }

                $totalrow = $request->totalrows;

                for ($i = 1; $i <= $totalrow; $i++) {

                    if ($request->input('crdr' . $i) == 'Cr') {
                        $amtcr = $request->input('amount' . $i);
                        $amtdr = '0.00';
                    } else {
                        $amtdr = $request->input('amount' . $i);
                        $amtcr = '0.00';
                    }

                    $ledgerpost = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $i,
                        'vno' => $vno,
                        'vdate' => $request->input('refdate' . $i),
                        'vtype' => 'F_AO',
                        'vprefix' => $vprefix,
                        'narration' => $request->input('narration' . $i),
                        'contrasub' => '',
                        'subcode' => $request->rev_code,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => null,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($ledgerpost);
                }
            }
            return redirect('chargemaster')->with('success', 'Charge Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function deletechargemaster(Request $request)
    {
        $permission = revokeopen(121214);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $ac_code = base64_decode($request->input('ac_code'));
            $exists = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('tax_code', base64_decode($request->input('rev_code')))->first();
            if ($exists) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Can Not Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('revmast')->where('ac_code', $ac_code)
                ->where('rev_code', base64_decode($request->input('rev_code')))
                ->where('sn', base64_decode($request->input('sn')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Charge Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Charge Master');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitroomfeaturetore(Request $request)
    {
        $permission = revokeopen(121216);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'roomfeature';
        $data = $request->except('_token');
        $rcode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->count() + 1;

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Room Feature Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'rcode' => $rcode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;
            DB::table($tableName)->insert($insertdata);
            return back()->with('success', 'Room Feature Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Room Feature!');
        }
    }

    public function updateroomfeaturetore(Request $request)
    {
        $permission = revokeopen(121216);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'roomfeature';
        $data = $request->except('_token');

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->whereNot('rcode', $request->input('rcode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Room Feature Name already exists!');
        }

        try {
            $updatedata = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;
            DB::table($tableName)
                ->where('rcode', $request->input('rcode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Room Feature Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Room Feature!');
        }
    }

    public function deleteroomfeature(Request $request)
    {
        $permission = revokeopen(121216);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $rcode = base64_decode($request->input('rcode'));
            $jaldiwahasehato📢 = DB::table('roomfeature')
                ->where('propertyid', $this->propertyid)
                ->where('rcode', $rcode)->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Room Feature Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Room Feature');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitroomcat(Request $request)
    {
        $permission = revokeopen(121217);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'type' => 'required',
            'shortname' => 'required',
            'rev_code' => 'required',
            'multiper' => 'required|integer',
            'norooms' => 'required|integer',
        ]);

        $tableName = 'room_cat';

        $existingName = DB::table($tableName)
            ->where('name', $request->input('type'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Room Category already exists!');
        }

        $cat_codemax = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('cat_code');

        if ($cat_codemax) {
            $cat_codem = substr($cat_codemax, 0, -$this->ptlngth) + 1;
        } else {
            $cat_codem = 1;
        }

        function insertRate($propertyid, $occtype, $data, $cat_Code)
        {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $rateData = [
                'rate1' => $data['highrate'],
                'rate2' => $data['rackrate'],
                'rate3' => $data['diskrate1'],
                'rate4' => $data['diskrate2'],
                'rate5' => $data['diskrate3'],
                'u_entdt' => $currenttime,
                'room_cat' => $cat_Code . $propertyid,
                'roomno' => '*****',
                'occtype' => $occtype,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $propertyid,
                'u_ae' => 'a',
            ];

            DB::table('rate_list')->insert($rateData);
        }

        insertRate($this->propertyid, 'singleuser', [
            'highrate' => $request->input('singleuser_highrate'),
            'rackrate' => $request->input('singleuser_rackrate'),
            'diskrate1' => $request->input('singleuser_diskrate1'),
            'diskrate2' => $request->input('singleuser_diskrate2'),
            'diskrate3' => $request->input('singleuser_diskrate3'),
        ], $cat_codem);

        insertRate($this->propertyid, 'multiuser', [
            'highrate' => $request->input('multiuser_highrate'),
            'rackrate' => $request->input('multiuser_rackrate'),
            'diskrate1' => $request->input('multiuser_diskrate1'),
            'diskrate2' => $request->input('multiuser_diskrate2'),
            'diskrate3' => $request->input('multiuser_diskrate3'),
        ], $cat_codem);

        insertRate($this->propertyid, 'extrauser', [
            'highrate' => $request->input('extrauser_highrate'),
            'rackrate' => $request->input('extrauser_rackrate'),
            'diskrate1' => $request->input('extrauser_diskrate1'),
            'diskrate2' => $request->input('extrauser_diskrate2'),
            'diskrate3' => $request->input('extrauser_diskrate3'),
        ], $cat_codem);

        insertRate($this->propertyid, 'weekend', [
            'highrate' => $request->input('weekend_highrate'),
            'rackrate' => $request->input('weekend_rackrate'),
            'diskrate1' => $request->input('weekend_diskrate1'),
            'diskrate2' => $request->input('weekend_diskrate2'),
            'diskrate3' => $request->input('weekend_diskrate3'),
        ], $cat_codem);

        $data = [
            'type' => 'RO',
            'name' => $request->input('type'),
            'shortname' => $request->input('shortname'),
            'rev_code' => $request->input('rev_code'),
            'multiper' => $request->input('multiper'),
            'norooms' => $request->input('norooms'),
            'inclcount' => $request->input('inclcount'),
        ];

        try {
            $catCode = $cat_codem . $this->propertyid;

            $insertdata = [
                'cat_code' => $catCode,
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Room Category Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Room Category! ' . $e->getMessage());
        }
    }

    public function updateroomcat(Request $request)
    {
        $permission = revokeopen(121217);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'type' => 'required',
            'shortname' => 'required',
            'rev_code' => 'required',
            'multiper' => 'required|integer',
            'norooms' => 'required|integer',
            'inclcount' => 'required',
        ]);

        $tableName = 'room_cat';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('type'))
            ->whereNot('cat_code', $request->input('cat_code'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Room Category already exists!');
        }
        $propertyid = $this->propertyid;

        function updateOrInsertRate($propertyid, $catCode, $occtype, $data)
        {
            date_default_timezone_set('Asia/Kolkata');
            $currentime = date('Y-m-d H:i:s');
            $check = DB::table('rate_list')
                ->where('propertyid', $propertyid)
                ->where('room_cat', $catCode)
                ->where('occtype', $occtype)
                ->count();

            $rateData = [
                'rate1' => $data['highrate'],
                'rate2' => $data['rackrate'],
                'rate3' => $data['diskrate1'],
                'rate4' => $data['diskrate2'],
                'rate5' => $data['diskrate3'],
                'u_name' => Auth::user()->u_name,
                'propertyid' => $propertyid,
            ];

            if ($check > 0) {
                $rateData['u_updatedt'] = $currentime;
                $rateData['u_ae'] = 'e';
                DB::table('rate_list')
                    ->where('propertyid', $propertyid)
                    ->where('room_cat', $catCode)
                    ->where('occtype', $occtype)
                    ->update($rateData);
            } else {
                $rateData['u_entdt'] = $currentime;
                $rateData['room_cat'] = $catCode;
                $rateData['roomno'] = '*****';
                $rateData['occtype'] = $occtype;
                $rateData['sysYN'] = 'N';
                $rateData['u_ae'] = 'a';
                DB::table('rate_list')
                    ->insert($rateData);
            }
        }

        updateOrInsertRate($this->propertyid, $request->input('cat_code'), 'singleuser', [
            'highrate' => $request->input('singleuser_highrate'),
            'rackrate' => $request->input('singleuser_rackrate'),
            'diskrate1' => $request->input('singleuser_diskrate1'),
            'diskrate2' => $request->input('singleuser_diskrate2'),
            'diskrate3' => $request->input('singleuser_diskrate3'),
        ]);

        updateOrInsertRate($this->propertyid, $request->input('cat_code'), 'multiuser', [
            'highrate' => $request->input('multiuser_highrate'),
            'rackrate' => $request->input('multiuser_rackrate'),
            'diskrate1' => $request->input('multiuser_diskrate1'),
            'diskrate2' => $request->input('multiuser_diskrate2'),
            'diskrate3' => $request->input('multiuser_diskrate3'),
        ]);

        updateOrInsertRate($this->propertyid, $request->input('cat_code'), 'extrauser', [
            'highrate' => $request->input('extrauser_highrate'),
            'rackrate' => $request->input('extrauser_rackrate'),
            'diskrate1' => $request->input('extrauser_diskrate1'),
            'diskrate2' => $request->input('extrauser_diskrate2'),
            'diskrate3' => $request->input('extrauser_diskrate3'),
        ]);

        updateOrInsertRate($this->propertyid, $request->input('cat_code'), 'weekend', [
            'highrate' => $request->input('weekend_highrate'),
            'rackrate' => $request->input('weekend_rackrate'),
            'diskrate1' => $request->input('weekend_diskrate1'),
            'diskrate2' => $request->input('weekend_diskrate2'),
            'diskrate3' => $request->input('weekend_diskrate3'),
        ]);

        $data = [
            'type' => 'RO',
            'name' => $request->input('type'),
            'shortname' => $request->input('shortname'),
            'rev_code' => $request->input('rev_code'),
            'multiper' => $request->input('multiper'),
            'norooms' => $request->input('norooms'),
            'inclcount' => $request->input('inclcount'),
        ];

        try {
            $updatedata = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;

            DB::table($tableName)
                ->where('cat_code', $request->input('cat_code'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);

            return back()->with('success', 'Room Category Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Room Category!');
        }
    }

    public function deleteroomcat(Request $request)
    {
        $permission = revokeopen(121217);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $cat_code = base64_decode($request->input('cat_code'));
            $jaldiwahasehato2📢 = DB::table('rate_list')
                ->where('roomno', '*****')
                ->where('room_cat', base64_decode($request->input('cat_code')))
                ->where('propertyid', $this->propertyid)
                ->delete();
            $jaldiwahasehato📢 = DB::table('room_cat')->where('cat_code', $cat_code)
                ->where('sn', base64_decode($request->input('sn')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Room Category Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Room Category!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitroommast(Request $request)
    {
        $permission = revokeopen(121218);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'roomname' => 'required',
            'room_cat' => 'required',
            'multiper' => 'required|integer',
            'rcode' => 'required|string',
        ]);

        $tableName = 'room_mast';

        $existingName = DB::table($tableName)
            ->where('name', $request->input('roomname'))
            ->where('propertyid', $this->propertyid)
            ->where('type', 'RO')
            ->first();
        $existingCode = DB::table($tableName)
            ->where('rcode', $request->input('rcode'))
            ->where('propertyid', $this->propertyid)
            ->where('type', 'RO')
            ->first();

        if ($existingCode) {
            return back()->with('error', 'Room No. already exists!');
        }

        if ($existingName) {
            return back()->with('error', 'Room Master already exists!');
        }

        $cat_code = $request->input('room_cat');
        $roomno = $request->input('rcode');

        function insertRate2($propertyid, $occtype, $data, $cat_code, $roomno)
        {
            date_default_timezone_set('Asia/Kolkata');
            $currenttime = date('Y-m-d H:i:s');
            $rateData = [
                'rate1' => $data['highrate'],
                'rate2' => $data['rackrate'],
                'rate3' => $data['diskrate1'],
                'rate4' => $data['diskrate2'],
                'rate5' => $data['diskrate3'],
                'u_entdt' => $currenttime,
                'room_cat' => $cat_code,
                'roomno' => $roomno,
                'occtype' => $occtype,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $propertyid,
                'u_ae' => 'a',
            ];

            DB::table('rate_list')->insert($rateData);
        }

        insertRate2($this->propertyid, 'singleuser', [
            'highrate' => $request->input('singleuser_highrate'),
            'rackrate' => $request->input('singleuser_rackrate'),
            'diskrate1' => $request->input('singleuser_diskrate1'),
            'diskrate2' => $request->input('singleuser_diskrate2'),
            'diskrate3' => $request->input('singleuser_diskrate3'),
        ], $cat_code, $roomno);

        insertRate2(
            $this->propertyid,
            'multiuser',
            [
                'highrate' => $request->input('multiuser_highrate'),
                'rackrate' => $request->input('multiuser_rackrate'),
                'diskrate1' => $request->input('multiuser_diskrate1'),
                'diskrate2' => $request->input('multiuser_diskrate2'),
                'diskrate3' => $request->input('multiuser_diskrate3'),
            ],
            $cat_code,
            $roomno
        );

        insertRate2(
            $this->propertyid,
            'extrauser',
            [
                'highrate' => $request->input('extrauser_highrate'),
                'rackrate' => $request->input('extrauser_rackrate'),
                'diskrate1' => $request->input('extrauser_diskrate1'),
                'diskrate2' => $request->input('extrauser_diskrate2'),
                'diskrate3' => $request->input('extrauser_diskrate3'),
            ],
            $cat_code,
            $roomno
        );

        insertRate2(
            $this->propertyid,
            'weekend',
            [
                'highrate' => $request->input('weekend_highrate'),
                'rackrate' => $request->input('weekend_rackrate'),
                'diskrate1' => $request->input('weekend_diskrate1'),
                'diskrate2' => $request->input('weekend_diskrate2'),
                'diskrate3' => $request->input('weekend_diskrate3'),
            ],
            $cat_code,
            $roomno
        );

        $data = [
            'type' => 'RO',
            'name' => $request->input('roomname'),
            'room_cat' => $request->input('room_cat'),
            'rcode' => $request->input('rcode'),
            'rest_code' => 'ROOM',
            'room_stat' => 'C',
            'multiper' => $request->input('multiper'),
            'maid_station' => $request->input('maid_station'),
            'inclcount' => $request->input('inclcount'),
        ];

        try {
            if (!empty($request->file('pic_path'))) {
                $roompic = $request->file('pic_path');
                $roompicture = $request->input('name') . $this->propertyid . time() . '.' . $roompic->getClientOriginalExtension();
                $folderPath = 'public/property/roomimages';
                Storage::makeDirectory($folderPath);
                $filePath = Storage::putFileAs($folderPath, $roompic, $roompicture);
            }
            $roompicture = '';
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'pic_path' => $roompicture,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Room Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Room Master!');
        }
    }

    public function updateroommaster(Request $request)
    {
        $permission = revokeopen(121218);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'roomname' => 'required',
            'room_cat' => 'required',
            'multiper' => 'required|integer',
            'rcode' => 'required|string',
        ]);

        $tableName = 'room_mast';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('roomname'))
            ->whereNot('sno', $request->input('sno'))
            ->where('type', 'RO')
            ->where('propertyid', $this->propertyid)
            ->first();
        $existingCode = DB::table($tableName)
            ->where('rcode', $request->input('rcode'))
            ->whereNot('sno', $request->input('sno'))
            ->where('type', 'RO')
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Room Master already exists!');
        }
        if ($existingCode) {
            return back()->with('error', 'Room No. already exists!');
        }

        $propertyid = $this->propertyid;

        function updateOrInsertRate2($propertyid, $catCode, $occtype, $data, $roomno)
        {
            date_default_timezone_set('Asia/Kolkata');
            $currentime = date('Y-m-d H:i:s');
            $check = DB::table('rate_list')
                ->where('propertyid', $propertyid)
                ->where('room_cat', $catCode)
                ->where('occtype', $occtype)
                ->count();

            $rateData = [
                'rate1' => $data['highrate'],
                'rate2' => $data['rackrate'],
                'rate3' => $data['diskrate1'],
                'rate4' => $data['diskrate2'],
                'rate5' => $data['diskrate3'],
                'u_name' => Auth::user()->u_name,
                'propertyid' => $propertyid,
            ];

            if ($check > 0) {
                $rateData['u_updatedt'] = $currentime;
                $rateData['u_ae'] = 'e';
                DB::table('rate_list')
                    ->where('propertyid', $propertyid)
                    ->where('room_cat', $catCode)
                    ->where('occtype', $occtype)
                    ->where('roomno', $roomno)
                    ->update($rateData);
            } else {
                $rateData['u_entdt'] = $currentime;
                $rateData['room_cat'] = $catCode;
                $rateData['roomno'] = $roomno;
                $rateData['occtype'] = $occtype;
                $rateData['sysYN'] = 'N';
                $rateData['u_ae'] = 'a';
                DB::table('rate_list')
                    ->insert($rateData);
            }
        }

        updateOrInsertRate2($this->propertyid, $request->input('room_cat'), 'singleuser', [
            'highrate' => $request->input('singleuser_highrate'),
            'rackrate' => $request->input('singleuser_rackrate'),
            'diskrate1' => $request->input('singleuser_diskrate1'),
            'diskrate2' => $request->input('singleuser_diskrate2'),
            'diskrate3' => $request->input('singleuser_diskrate3'),
        ], $request->input('roomno'));

        updateOrInsertRate2($this->propertyid, $request->input('room_cat'), 'multiuser', [
            'highrate' => $request->input('multiuser_highrate'),
            'rackrate' => $request->input('multiuser_rackrate'),
            'diskrate1' => $request->input('multiuser_diskrate1'),
            'diskrate2' => $request->input('multiuser_diskrate2'),
            'diskrate3' => $request->input('multiuser_diskrate3'),
        ], $request->input('roomno'));

        updateOrInsertRate2(
            $this->propertyid,
            $request->input('room_cat'),
            'extrauser',
            [
                'highrate' => $request->input('extrauser_highrate'),
                'rackrate' => $request->input('extrauser_rackrate'),
                'diskrate1' => $request->input('extrauser_diskrate1'),
                'diskrate2' => $request->input('extrauser_diskrate2'),
                'diskrate3' => $request->input('extrauser_diskrate3'),
            ],
            $request->input('roomno')
        );

        updateOrInsertRate2($this->propertyid, $request->input('room_cat'), 'weekend', [
            'highrate' => $request->input('weekend_highrate'),
            'rackrate' => $request->input('weekend_rackrate'),
            'diskrate1' => $request->input('weekend_diskrate1'),
            'diskrate2' => $request->input('weekend_diskrate2'),
            'diskrate3' => $request->input('weekend_diskrate3'),
        ], $request->input('roomno'));

        $data = [
            'type' => 'RO',
            'name' => $request->input('roomname'),
            'room_cat' => $request->input('room_cat'),
            'rcode' => $request->input('roomno'),
            'rest_code' => 'ROOM',
            'room_stat' => 'C',
            'multiper' => $request->input('multiper'),
            'maid_station' => $request->input('maid_station'),
            'inclcount' => $request->input('inclcount'),
        ];

        try {
            $roompic = $request->file('pic_path');
            if ($roompic && file_exists($roompic)) {
                $roompicture = $request->input('name') . $this->propertyid . time() . '.' . $roompic->getClientOriginalExtension();
                $folderPath = 'public/property/roomimages';
                Storage::makeDirectory($folderPath);
                $filePath = Storage::putFileAs($folderPath, $roompic, $roompicture);
            } else {
                $roompicture = $request->input('old_photo');
            }
            $updatedata = [
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'pic_path' => $roompicture,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ] + $data;

            DB::table($tableName)
                ->where('sno', $request->input('sno'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Room Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Room Master!');
        }
    }

    public function deleteroommaster(Request $request)
    {
        $permission = revokeopen(121218);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $rcode = base64_decode($request->input('rcode'));
            $image = DB::table('room_mast')
                ->where('propertyid', $this->propertyid)
                ->where('rcode', base64_decode($request->input('roomno')))
                ->where('sno', base64_decode($request->input('sno')))
                ->value('pic_path');
            if ($image) {
                $folderPath = storage_path('app/public/property/roomimages/' . $image);
                if (file_exists($folderPath)) {
                    unlink($folderPath);
                }
            }
            $jaldiwahasehato2📢 = DB::table('rate_list')
                ->where('roomno', base64_decode($request->input('roomno')))
                ->where('room_cat', base64_decode($request->input('cat_code')))
                ->where('propertyid', $this->propertyid)
                ->delete();
            $jaldiwahasehato📢 = DB::table('room_mast')
                ->where('rcode', base64_decode($request->input('roomno')))
                ->where('sno', base64_decode($request->input('sno')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Room Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Room Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function deletplanmast(Request $request)
    {
        $permission = revokeopen(121215);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato2📢 = DB::table('plan1')
                ->where('pcode', base64_decode($request->input('pcode')))
                ->where('propertyid', $this->propertyid)
                ->delete();
            $jaldiwahasehato📢 = DB::table('plan_mast')
                ->where('pcode', base64_decode($request->input('pcode')))
                ->where('sn', base64_decode($request->input('sn')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Plan Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Plan Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitplanmaster(Request $request)
    {
        $permission = revokeopen(121215);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'plan_mast';
        $data = [
            'name' => $request->input('planname'),
            'tarrif' => $request->input('tarrif'),
            'room_cat' => $request->input('room_cat'),
            'room_tax_stru' => $request->input('room_tax_stru'),
            'adults' => $request->input('adults'),
            'childs' => $request->input('childs'),
            'room_rate' => $request->input('room_rate'),
            'package_amount' => $request->input('package_amount'),
            'disc_appYN' => $request->input('disc_appYN'),
            'disc_appON' => $request->input('disc_appON'),
            'rrinc_tax' => $request->input('rrinc_tax'),
            'activeYN' => $request->input('activeYN'),
            'room_per' => $request->input('room_per'),
        ];
        $maxpcode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('pcode');

        if ($maxpcode === null) {
            $pcode = '1' . $this->propertyid;
        } else {
            $pcode = substr($maxpcode, 0, -3) + 1 . $this->propertyid;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('room_cat', $data['room_cat'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Plan Master Name already exists!');
        }

        try {
            $insertdata = [
                'total' => $request->input('lasttotal'),
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'pcode' => $pcode,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;
            $insertData2 = [];
            foreach ($request->input() as $key => $value) {
                if (preg_match('/^rev_code(\d+)$/', $key, $matches)) {
                    $sno = $matches[1];
                    $revmast = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $request->input('rev_code' . $sno))->first();
                    $rowData = [
                        'pcode' => $pcode,
                        'tax_stru' => $revmast->tax_stru,
                        'rev_code' => $request->input('rev_code' . $sno),
                        'fix_rate' => $request->input('fix_rate' . $sno),
                        'tax_inc' => $request->input('tax_inc' . $sno),
                        'adult' => $request->input('adultprice' . $sno),
                        'child' => $request->input('childprice' . $sno),
                        'plan_per' => $request->input('plan_per' . $sno),
                        'net_amount' => $request->input('net_amount' . $sno),
                        'u_entdt' => $this->currenttime,
                        'sysYN' => 'N',
                        'u_name' => Auth::user()->u_name,
                        'propertyid' => $this->propertyid,
                        'u_ae' => 'a',
                        'sno' => $sno,
                    ];
                    $insertData2[] = $rowData;
                    DB::table('plan1')->insert($rowData);
                }
            }
            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Plan Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Plan Master:' . $e->getMessage());
        }
    }

    public function ncurdateget(Request $request)
    {
        $data = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->pluck('ncur')
            ->first();
        return response()->json(['data' => $data]);
    }

    public function yearmanage()
    {
        return $this->datemanage;
    }

    public function checkouttimeget(Request $request)
    {
        $data = substr(DB::table('enviro_form')
            ->where('propertyid', $this->propertyid)
            ->pluck('checkout')
            ->first(), 0, -3);
        return response()->json(['data' => $data]);
    }

    public function updateplanmaster(Request $request)
    {
        $permission = revokeopen(121215);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validatedData = $request->validate([
            'planname' => 'required',
            'tarrif' => 'required',
        ]);

        $existingName = DB::table('plan_mast')
            ->where('propertyid', $this->propertyid)
            ->where('name', $request->input('planname'))
            ->where('room_cat', $request->input('room_cat'))
            ->whereNot('pcode', $request->input('pcode'))
            ->first();

        if ($existingName) {
            return back()->with('error', 'Plan Master Name already exists!');
        }

        $snolist = DB::table('plan1')
            ->where('propertyid', $this->propertyid)
            ->where('pcode', $request->input('pcode'))
            ->get();
        $maxSn = $snolist->max('sno');

        $sno = 0;
        foreach ($request->input() as $key => $value) {
            if (preg_match('/^rev_code(\d+)$/', $key, $matches)) {
                $sno = $matches[1];
                $insertData['rev_code'] = $value;
                $insertData['tax_inc'] = $request->input('tax_inc' . $sno);
                $insertData['fix_rate'] = $request->input('fix_rate' . $sno);
                $insertData['adult'] = $request->input('adult' . $sno);
                $insertData['child'] = $request->input('child' . $sno);
                $insertData['plan_per'] = $request->input('plan_per' . $sno);
                $insertData['net_amount'] = $request->input('net_amount' . $sno);
                $insertData['sno'] = $sno;
            }
        }
        if ($sno > $maxSn) {
            $insertData = array(
                'pcode' => null,
                'rev_code' => null,
                'tax_stru' => null,
                'tax_inc' => null,
                'fix_rate' => null,
                'adult' => null,
                'child' => null,
                'plan_per' => null,
                'net_amount' => null,
                'sysYN' => 'N',
            );
            foreach ($request->input() as $key => $value) {
                if (preg_match('/^rev_code(\d+)$/', $key, $matches)) {
                    $revmast = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $value)->first();
                    $sno = $matches[1];
                    $insertData['rev_code'] = $value;
                    $insertData['tax_stru'] = $revmast->tax_stru;
                    $insertData['fix_rate'] = $request->input('applyon' . $sno);
                    $insertData['tax_inc'] = $request->input('tax_inc' . $sno);
                    $insertData['adult'] = $request->input('adult' . $sno);
                    $insertData['child'] = $request->input('child' . $sno);
                    $insertData['plan_per'] = $request->input('plan_per' . $sno);
                    $insertData['net_amount'] = $request->input('net_amount' . $sno);
                    $insertData['sno'] = $sno;

                    $insertData = [
                        'pcode' => $request->input('pcode'),
                        'propertyid' => $this->propertyid,
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'sysYN' => 'N',
                    ] + $insertData;
                    DB::table('plan1')
                        ->where('propertyid', $this->propertyid)
                        ->where('pcode', $request->input('pcode'))
                        ->where('u_entdt', '<', $this->currenttime)
                        ->delete();
                    DB::table('plan1')->insert($insertData);
                }
            }
            return back()->with('success', 'Plan Master Updated and New Rows Inserted Successfully');
        } else if ($sno == $maxSn) {
            foreach ($snolist as $list) {
                $revmast = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $request->input("rev_code{$list->sno}"))->first();
                $data = [
                    "rev_code" => $request->input("rev_code{$list->sno}"),
                    "tax_stru" => $revmast->tax_stru,
                    "tax_inc" => $request->input("tax_inc{$list->sno}"),
                    "fix_rate" => $request->input("fix_rate{$list->sno}"),
                    "adult" => $request->input("adultprice{$list->sno}"),
                    "child" => $request->input("childprice{$list->sno}"),
                    "plan_per" => $request->input("plan_per{$list->sno}"),
                    "net_amount" => $request->input("net_amount{$list->sno}"),
                    "u_updatedt" => $this->currenttime,
                    'propertyid' => $this->propertyid,
                    'u_name' => Auth::user()->name,
                    'u_ae' => 'e',
                    'sysYN' => 'N',
                ];

                $plan_data = [
                    'name' => $request->input('planname'),
                    'tarrif' => $request->input('tarrif'),
                    'room_cat' => $request->input('room_cat'),
                    'room_tax_stru' => $request->input('room_tax_stru'),
                    'adults' => $request->input('adults'),
                    'childs' => $request->input('childs'),
                    'room_rate' => $request->input('room_rate'),
                    'package_amount' => $request->input('package_amount'),
                    'disc_appYN' => $request->input('disc_appYN'),
                    'disc_appON' => $request->input('disc_appON'),
                    'rrinc_tax' => $request->input('rrinc_tax'),
                    'activeYN' => $request->input('activeYN'),
                    'room_per' => $request->input('room_per'),
                    "u_updatedt" => $this->currenttime,
                    'propertyid' => $this->propertyid,
                    'u_name' => Auth::user()->name,
                    'u_ae' => 'e',
                    'sysYN' => 'N',
                ];

                $update = DB::table('plan1')
                    ->where('propertyid', $this->propertyid)
                    ->where('pcode', $request->input('pcode'))
                    ->where('sno', $list->sno)
                    ->update($data);
                $update2 = DB::table('plan_mast')
                    ->where('propertyid', $this->propertyid)
                    ->where('pcode', $request->input('pcode'))
                    ->update($plan_data);
            }

            return back()->with('success', 'Plan Master Updated Successfully');
        }
    }

    public function submitbunitmaster(Request $request)
    {
        $permission = revokeopen(122021);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'unitmast';
        $data = $request->except('_token');
        $ucode = DB::table('unitmast')->where('propertyid', $this->propertyid)->max('ucode');
        if ($ucode === null) {
            $bcode = 1;
        } else {
            $ucode = intval(substr($ucode, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Unit Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'ucode' => $ucode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Unit Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Unit Master!');
        }
    }

    public function deleteunitmast(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(122021);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('unitmast')
                ->where('propertyid', $this->propertyid)
                ->where('ucode', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Unit Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Unit Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateunitmaststore(Request $request)
    {
        $permission = revokeopen(122021);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'unitmast';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('ucode', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Name Already Exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('ucode', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Unit Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitbnctypemaster(Request $request)
    {
        $permission = revokeopen(121320);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'nctype_mast';
        $data = $request->except('_token');
        $ncode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('ncode');
        $ncode = DB::table('nctype_mast')->where('propertyid', $this->propertyid)->max('ncode');
        if ($ncode === null) {
            $ncode = 1;
        } else {
            $ncode = intval(substr($ncode, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('nctype', $data['nctype'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with(['message' => 'NC Type Master Name already exists!'], 422);
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'ncode' => $ncode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with(['message' => 'NC Type Master Inserted successfully!']);
        } catch (Exception $e) {
            return back()->with(['message' => 'Unable to Insert NC Type Master!'], 500);
        }
    }

    public function updatenctypemaststore(Request $request)
    {
        $permission = revokeopen(121320);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'nctype_mast';
        $existingName = DB::table($tableName)
            ->where('nctype', $request->input('updatename'))
            ->whereNot('ncode', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'NC Type Already Exists!');
        }

        try {
            $updatedata = [
                'nctype' => $request->input('updatename'),
                'ncper' => $request->input('ncper'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('ncode', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'NC Type Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deletenctypemast(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121320);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('nctype_mast')
                ->where('propertyid', $this->propertyid)
                ->where('ncode', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'NC Type Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete NC Type Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function submitsessionmaster(Request $request)
    {
        $permission = revokeopen(121319);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'session_mast';
        $data = $request->except('_token');
        $scode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('scode');
        $scode = DB::table($tableName)->where('propertyid', $this->propertyid)->max('scode');
        if ($scode === null) {
            $scode = 1;
        } else {
            $scode = intval(substr($scode, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Session Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'scode' => $scode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Session Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Session Master!');
        }
    }

    public function updatesessionmaststore(Request $request)
    {
        $permission = revokeopen(121319);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'session_mast';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('scode', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Session Master Name Already Exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'from_time' => $request->input('from_timeup'),
                'to_time' => $request->input('to_timeup'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('scode', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Session Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deletesessionmast(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121319);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('session_mast')
                ->where('propertyid', $this->propertyid)
                ->where('scode', $ucode)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Session Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Session Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitservermaster(Request $request)
    {

        $permission = revokeopen(121313);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'server_mast';
        $data = $request->except('_token');
        $scode = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->max('scode');
        $scode = DB::table('server_mast')->where('propertyid', $this->propertyid)->max('scode');
        if ($scode === null) {
            $scode = 1;
        } else {
            $scode = intval(substr($scode, 0, -3)) + 1;
        }

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Server Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'scode' => $scode . $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('error', 'Server Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Server Master!');
        }
    }

    public function deleteservermast(Request $request, $sn, $ucode)
    {

        $permission = revokeopen(121313);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('server_mast')
                ->where('propertyid', $this->propertyid)
                ->where('scode', $ucode)
                ->where('sn', $sn)
                //->where('scode', base64_decode($request->input('ucode')))
                //->where('sn', base64_decode($request->input('sn')))
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Server Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Server Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function opensundrysetting(Request $request)
    {
        $permission = revokeopen(121312);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vtypes = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Outlet', 'ROOM SERVICE'])->get();
        $data = DB::table('sundrytype')
            ->select('sundrytype.*', 'depart.name AS departname')
            ->leftJoin('depart', 'depart.dcode', '=', 'sundrytype.vtype')
            ->where('sundrytype.propertyid', '=', $this->propertyid)
            ->whereNotIn('sundrytype.vtype', ['BANQ' . $this->propertyid, 'PURC' . $this->propertyid])
            ->groupBy('sundrytype.vtype')
            ->groupBy('sundrytype.appdate')
            ->get();

        return view('property.sundrysetting', [
            'vtypes' => $vtypes,
            'data' => $data
        ]);
    }

    public function fetchsundrytype(Request $request)
    {
        $dcode = $request->input('dcode');
        $sundrytype = DB::table('sundrytypefix')->where('propertyid', $this->propertyid)->orderBy('sn')->get();
        $revmast = DB::table('revmast')->where('propertyid', $this->propertyid)->where('Desk_code', $dcode)->where('field_type', 'C')
            ->union(
                DB::table('revmast')
                    ->where('propertyid', $this->propertyid)
                    ->where('field_type', 'T')
            )->orderBy('sn')->get();

        $sundrynames = DB::table('sundrymast')->where('propertyid', $this->propertyid)->orderBy('name')->get();
        $data = [
            'sundrytype' => $sundrytype,
            'revmast' => $revmast,
            'sundrynames' => $sundrynames,
        ];

        return json_encode($data);
    }

    public function fetchsundrytype2(Request $request)
    {
        $dcode = $request->input('dcode');
        $appdate = $request->input('appdate');
        $sundrytype = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $dcode)->where('appdate', $appdate)->orderBy('sno')->get();
        $revmast = DB::table('revmast')->where('propertyid', $this->propertyid)->where('Desk_code', $dcode)
            ->union(
                DB::table('revmast')
                    ->where('propertyid', $this->propertyid)
                    ->where('field_type', 'T')
            )->orderBy('sn')->get();

        $sundrynames = DB::table('sundrymast')->where('propertyid', $this->propertyid)->orderBy('name')->get();
        $data = [
            'sundrytype' => $sundrytype,
            'revmast' => $revmast,
            'sundrynames' => $sundrynames,
        ];
        return json_encode($data);
    }

    public function sundrysettingsubmit(Request $request)
    {
        $permission = revokeopen(121312);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vtype' => 'required',
            'applicablefrom' => 'required',
            'sundryname1' => 'required',
            'dispname1' => 'required',
        ]);

        // $check = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $request->input('vtype'))->first();
        // if ($check) {
        //     DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $request->input('vtype'))->delete();
        // }

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
                    'vtype' => $request->input('vtype'),
                    'appdate' => $request->input('applicablefrom'),
                    'nature' => $sundryfix->nature ?? '',
                    'calcsign' => $sundryfix->calcsign ?? '',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'postyn' => '',
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
        return response()->json(['message' => 'Sundry Setting Submitted!']);
    }

    public function updatesundry(Request $request)
    {
        $permission = revokeopen(121312);

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
                    'postyn' => '',
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
        return redirect('sundrysetting')->with('success', 'Sundry Setting Updated!');
    }

    public function openupdatesundrysetting(Request $request)
    {
        $permission = revokeopen(121312);

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
        return view('property.sundrysettingupdate', [
            'data' => $data,
            'revmast' => $revmast,
            'sundrynames' => $sundrynames,
            'sundrytype' => $sundrytype,
            'depart' => $depart
        ]);
    }

    public function openpurcsundrysetting(Request $request)
    {
        $permission = revokeopen(121617);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $vtypes = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'PURC' . $this->propertyid)->first();
        $data = DB::table('sundrytype')
            ->select('sundrytype.*', 'depart.name AS departname')
            ->leftJoin('depart', 'depart.dcode', '=', 'sundrytype.vtype')
            ->where('sundrytype.propertyid', '=', $this->propertyid)
            ->where('sundrytype.vtype', 'PURC' . $this->propertyid)
            ->groupBy('sundrytype.vtype')
            ->get();

        return view('property.purchsundrysetting', [
            'vtypes' => $vtypes,
            'data' => $data
        ]);
    }

    public function purcsundrysettingsubmit(Request $request)
    {
        $permission = revokeopen(121617);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vtype' => 'required',
            'applicablefrom' => 'required',
            'sundryname1' => 'required',
            'dispname1' => 'required',
        ]);

        $check = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)->first();
        if ($check) {
            DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)->delete();
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
                    'vtype' => 'PURC' . $this->propertyid,
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
        return back()->with('success', 'Purchase Sundry Setting Submitted!');
    }

    public function updatepurchasesundrysetting(Request $request)
    {
        $permission = revokeopen(121617);
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
        return view('property.purchasesundrysettingupdate', [
            'data' => $data,
            'revmast' => $revmast,
            'sundrynames' => $sundrynames,
            'sundrytype' => $sundrytype,
            'depart' => $depart
        ]);
    }

    public function updatepurcsundry(Request $request)
    {
        $permission = revokeopen(121617);
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
        return redirect('purchsundry')->with('success', 'Purchase Sundry Setting Updated!');
    }

    public function updateservermaststore(Request $request)
    {

        $permission = revokeopen(121313);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'server_mast';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('scode', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Server Master Name already exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'activeYN' => $request->input('upactiveYN'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('scode', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Server Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitbpaytypemaster(Request $request)
    {

        $permission = revokeopen(121113);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'revmast';
        $data = $request->except('_token');

        $existingName = DB::table($tableName)
            ->where('name', $data['name'])
            ->where('propertyid', $this->propertyid)
            ->where('field_type', 'P')
            ->first();

        if ($existingName) {
            return back()->with('error', 'Pay Type Master Name already exists!');
        }

        try {
            $insertdata = [
                'u_entdt' => $this->currenttime,
                'rev_code' => substr($data['name'], 0, 2) . substr($data['name'], -2) . $this->propertyid,
                'sysYN' => 'N',
                'field_type' => 'P',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
            ] + $data;

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Pay Type Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Pay Type Master!');
        }
    }

    public function updatepaytypemaststore(Request $request)
    {

        $permission = revokeopen(121113);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'revmast';
        $existingName = DB::table($tableName)
            ->where('name', $request->input('updatename'))
            ->whereNot('sn', $request->input('updatecode'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingName) {
            return back()->with('error', 'Pay Type Already Exists!');
        }

        $checkboxes = $request->input('departpay');
        $revcode = $request->input('revcodeup');

        $checked_data = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereIn('rest_type', ['Outlet', 'FOM', 'ROOM SERVICE'])
            ->get();



        if (!empty($checkboxes)) {
            foreach ($checkboxes as $key => $value) {
                $existingRecord = DB::table('depart_pay')
                    ->where('pay_code', $revcode)
                    ->where('rest_code', $value)
                    ->first();

                foreach ($checked_data as $row) {
                }

                $allcol = DB::table('depart')
                    ->where('propertyid', $this->propertyid)
                    ->whereIn('rest_type', ['Outlet', 'FOM', 'ROOM SERVICE'])
                    ->get('dcode');

                if ($existingRecord) {
                    DB::table('depart_pay')
                        ->where('pay_code', $revcode)
                        ->where('rest_code', $value)
                        ->update([
                            'u_updatedt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'e',
                            'is_checked' => 'Y',
                        ]);
                } else {
                    $depart_paydata = [
                        'u_entdt' => $this->currenttime,
                        'rest_code' => $value,
                        'pay_code' => $revcode,
                        'u_name' => Auth::user()->u_name,
                        'propertyid' => $this->propertyid,
                        'u_ae' => 'a',
                        'is_checked' => 'Y',
                    ];
                    DB::table('depart_pay')->insert($depart_paydata);
                }
            }

            DB::table('depart_pay')
                ->where('pay_code', $revcode)
                ->whereNotIn('rest_code', $checkboxes)
                ->delete();
        } else {
            DB::table('depart_pay')
                ->where('propertyid', $this->propertyid)
                ->where('pay_code', $revcode)
                ->delete();
        }

        try {
            $updatedata = [
                'name' => $request->input('updatename'),
                'ac_code' => $request->input('upac_code'),
                'ac_posting' => $request->input('upac_posting'),
                'nature' => $request->input('upnature'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('sn', $request->input('updatecode'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return back()->with('success', 'Pay Type Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deletepaytype(Request $request, $sn, $code)
    {
        $permission = revokeopen(121113);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where('rev_code', $code)
                ->where('sn', $sn)
                ->delete();
            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Pay Type Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Pay Type Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submittablemaster(Request $request)
    {
        $permission = revokeopen(121314);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'room_mast';
        $data = $request->except('_token');

        $existingcode = DB::table($tableName)
            ->where('rcode', $data['rcode'])
            ->where('propertyid', $this->propertyid)
            ->where('rest_code', $request->rest_code)
            ->where('type', 'TB')
            ->first();

        if ($existingcode) {
            return response()->json(['message' => 'Table Master Code already exists!'], 422);
        }
        try {
            $insertdata = [
                'rcode' => $request->rcode,
                'rest_code' => $request->rest_code,
                'name' => $request->tablename,
                'u_entdt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'type' => 'TB',
                'room_cat' => 'TABLE',
                'inclcount' => 'N',
            ];

            $posdispcat = [
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'occupied' => '#f86f5d',
                'vacant' => '#f9f6c3',
                'billed' => '#a2c3bf',
            ];

            DB::table('posdispcat')->updateOrInsert(
                ['propertyid' => $this->propertyid],
                $posdispcat
            );

            $colora = [
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'colorcode' => '#f9f6c3',
                'rcode' => $request->input('rcode'),
            ];

            DB::table($tableName)->insert($insertdata);
            DB::table('colora')->insert($colora);

            return response()->json(['message' => 'Table Master Inserted successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Insert Table Master!'], 500);
        }
    }

    public function deletetablemast(Request $request)
    {
        $permission = revokeopen(121314);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('room_mast')
                ->where('propertyid', $this->propertyid)
                ->where('rcode', base64_decode($request->input('rcode')))
                ->where('sno', base64_decode($request->input('sno')))
                ->delete();

            // return base64_decode($request->input('rcode'));

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Table Master Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Table Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatetablemaststore(Request $request)
    {
        $permission = revokeopen(121314);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'room_mast';
        $existingCode = DB::table($tableName)
            ->where('rcode', $request->input('uprcode'))
            ->where('type', 'TB')
            ->whereNot('sno', $request->input('upsn'))
            ->where('propertyid', $this->propertyid)
            ->first();
        if ($existingCode) {
            return response()->json(['message' => 'Table Master Code Already Exists!'], 500);
        }

        try {
            $updatedata = [
                'name' => $request->input('upname'),
                'rest_code' => $request->input('uprest_code'),
                'rcode' => $request->input('uprcode'),
                'u_updatedt' => $this->currenttime,
                'sysYN' => 'N',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'e',
            ];
            DB::table($tableName)
                ->where('sno', $request->input('upsn'))
                ->where('propertyid', $this->propertyid)
                ->update($updatedata);
            return response()->json(['message' => 'Table Master Updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // public function sidemenuperm(Request $request)
    // {
    //     $columnName = $request->input('column');
    //     $check = DB::table('userpermission')
    //         ->where('u_name', Auth::user()->u_name)
    //         ->first($columnName);
    //     return $check;
    // }

    public function getPrefix()
    {
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $currentYear = date('Y', strtotime($ncurdate));
        $nextYear = $currentYear + 1;
        $previousYear = $currentYear - 1;
        if (date('m') < 4) {
            $date_from = $previousYear . '-04-01';
            $date_to = $currentYear . '-03-31';
            $prefix = substr($date_from, 0, 4);
        } else {
            $date_from = $currentYear . '-04-01';
            $date_to = $nextYear . '-03-31';
            $prefix = substr($date_from, 0, 4);
        }

        return $prefix;
    }

    public function submitoutlet(Request $request)
    {
        $permission = revokeopen(121311);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'depart';
        $existingcode = DB::table($tableName)
            ->where('short_name', $request->input('short_name'))
            ->where('propertyid', $this->propertyid)
            ->where('rest_type', 'Outlet')
            ->first();

        if ($existingcode) {
            return back()->with('error', 'Short Name already exists!');
        }

        // Outlet Details
        $outletname = $request->input('name');
        $outletnature = $request->input('outletNature');
        $short_name = $request->input('short_name');
        $mobileno = $request->input('mobileNo');
        $kot = $request->input('kot');
        $splitbill = $request->input('splitBill');
        $orderbooking = $request->input('orderBooking');
        $barcodeapp = $request->input('barCodeApp');
        $labelprinting = $request->input('labelPrinting');

        // KOT Printing Information
        $orderbookingtokenprint = $request->input('orderBookingTokenPrint');
        $printingtype = $request->input('printingType');
        $printingpathtypetxt = $request->input('printingPathTypeTxt');
        $nofkot = $request->input('NOfKOT');
        $currenttokennokot = $request->input('currentTokenNo');

        // Sale Bill Setup
        $partyname = $request->input('partyName');
        $memberinfo = $request->input('memberInfo');
        $customerinfo = $request->input('customerInfo');
        $freeitemapp = $request->input('freeItemApp');
        $cover = $request->input('cover');
        $autosettlement = $request->input('autoSettlement');
        $printonsave = $request->input('printOnSave');
        $autoresettoken = $request->input('autoResetToken');
        $mobileno_select = $request->input('mobileNoyn');
        $currenttokenno_sale = $request->input('currentTokenNosale');

        // Sale Bill Printing Information
        $comptitle = $request->input('compTitle');
        $outlettitle = $request->input('outletTitle');
        $nofbills = $request->input('NOfBills');
        $discountpercentprint = $request->input('discountPercentPrint');
        $printtokenbefore = $request->input('printTokenBefore');
        $printtokenafter = $request->input('printTokenAfter');
        $printtokenno = $request->input('printTokenNo');
        $groupdiscount = $request->input('groupDiscount');
        $header1 = $request->input('header1');
        $header2 = $request->input('header2');
        $header3 = $request->input('header3');
        $header4 = $request->input('header4');
        $slogan1 = $request->input('slogan1');
        $slogan2 = $request->input('slogan2');
        $tokenheader = $request->input('tokenHeader');

        // Order Booking Printing Information
        $firstcopyremark = $request->input('firstCopyRemark');
        $secondcopyremark = $request->input('secondCopyRemark');

        // Scheme Details
        $schemename = $request->input('schemename');
        $discountscheme = $request->input('discscheme');
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $currentYear = date('Y', strtotime($ncurdate));
        $previousYear = $currentYear - 1;
        $nextYear = $currentYear + 1;
        if (date('m') < 4) {
            $date_from = $previousYear . '-04-01';
            $date_to = $currentYear . '-03-31';
            $prefix = substr($date_from, 0, 4);
        } else {
            $date_from = $currentYear . '-04-01';
            $date_to = $nextYear . '-03-31';
            $prefix = substr($date_from, 0, 4);
        }

        $category = ['RSKOT', 'RSBIL', 'RSTKN', 'RSKOT', 'ORDER', 'POSADVANCE', 'POSPAYMENT'];
        $ncat = ['RSKOT', 'RSBIL', 'RSTKN', 'RSKOT', 'ORDER', 'PADV', 'PAMT'];
        $v_type = ['K', 'B', 'T', 'N', 'O', 'A', 'P'];
        $shortname = ['K', 'B', 'T', 'N', 'O', 'A', 'P'];
        $contra_type = ['', 'K', '', '', '', '', ''];
        $description = [' KOT Entry', ' Memo Entry', ' TOKEN Entry', ' N.C. KOT Entry', ' Booking Entry', ' Advance Entry', ' Payment Receive'];
        $description_help = [' KOT Entry', ' Memo Entry', ' TOKEN Entry', ' N.C. KOT Entry', ' Booking Entry', ' Advance Entry', ' Payment Receive'];
        $number_method = ['Automatic', 'Automatic', 'Automatic', 'Automatic', 'Automatic', 'Automatic', 'Automatic'];
        $rest_code = $request->input('short_name') . $this->propertyid;

        for ($i = 0; $i < count($category); $i++) {
            DB::table('voucher_type')->insert([
                'category' => $category[$i],
                'ncat' => $ncat[$i],
                'v_type' => $v_type[$i] . $short_name,
                'short_name' => $shortname[$i] . $short_name,
                'contratype' => $contra_type[$i],
                'description' => $short_name . '' . $description[$i],
                'description_help' => $short_name . '' . $description_help[$i],
                'number_method' => $number_method[$i],
                'restcode' => $rest_code,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'u_entdt' => $this->currenttime,
                'separate_narr' => 'N',
                'common_narr' => 'Y',
                'chqno' => 'N',
                'clgdt' => 'N',
            ]);
        }

        for ($i = 0; $i < count($category); $i++) {
            DB::table('voucher_prefix')->insert([
                'v_type' => $v_type[$i] . $short_name,
                'short_name' => $short_name,
                'prefix' => $prefix,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'start_srl_no' => '0',
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'u_entdt' => $this->currenttime,
            ]);
        }

        $revmast = [
            'type' => 'Cr',
            'rev_code' => $short_name . $this->propertyid,
            'Desk_code' => $rest_code,
            'flag_type' => 'POS',
            'name' => $outletname,
            'short_name' => $short_name,
            'u_name' => Auth::user()->u_name,
            'propertyid' => $this->propertyid,
            'u_ae' => 'a',
            'u_entdt' => $this->currenttime,
            'SysYN' => 'N',
        ];

        function revdiscroundoff($short_name, $alias, $propertyid, $rest_code, $chargename, $currenttime, $accode, $field_type)
        {
            $revdiscroundoff = [
                'type' => 'Cr',
                'rev_code' => $short_name . $alias . $propertyid,
                'ac_code' => $accode,
                'Desk_code' => $rest_code,
                'flag_type' => 'POS',
                'field_type' => $field_type,
                'name' => $chargename,
                'short_name' => $short_name,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $propertyid,
                'u_ae' => 'a',
                'round_off' => 'No',
                'u_entdt' => $currenttime,
                'SysYN' => 'N',
            ];

            Revmast::insert($revdiscroundoff);
        }

        $revdiscount = revdiscroundoff($short_name, 'DC', $this->propertyid, $request->input('short_name') . $this->propertyid, $request->input('short_name') . ' - DISCOUNT', $this->currenttime, '5' . $this->propertyid, 'C');
        $revroundoff = revdiscroundoff($short_name, 'RO', $this->propertyid, $request->input('short_name') . $this->propertyid, $request->input('short_name') . ' - ROUND-OFF', $this->currenttime, '6' . $this->propertyid, 'C');

        DB::table('revmast')->insert($revmast);

        $companylogo = '';

        if ($request->hasFile('companylogo')) {
            $companypic = $request->file('companylogo');
            $companylogo = $request->input('name') . $this->propertyid . 'OT' . $request->input('short_name') . $this->propertyid . '.' . $companypic->getClientOriginalExtension();
            $folderPathp = 'public/admin/property_logo';
            Storage::makeDirectory($folderPathp);
            $filePath = Storage::putFileAs($folderPathp, $companypic, $companylogo);
        } else {
            $companylogo = '';
        }

        // try {
        $insertData = [
            'height' => $request->input('height'),
            'uborderspace' => $request->input('borderspace'),
            'fontsize' => $request->input('font_size'),
            'col' => $request->input('col'),
            'u_entdt' => $this->currenttime,
            'dcode' => $request->input('short_name') . $this->propertyid,
            'sysYN' => 'N',
            'u_name' => Auth::user()->u_name,
            'propertyid' => $this->propertyid,
            'u_ae' => 'a',
            'rest_type' => $request->input('outletNature'),
            'pos' => 'Y',
            'outlet_yn' => 'Y',
            'name' => $outletname,
            'nature' => $outletnature,
            'short_name' => $short_name,
            'mobile_no' => $mobileno,
            'kot_yn' => $kot,
            'companyname' => $request->input('companyname') ?? '',
            'logo' => $companylogo,
            'gstin' => $request->input('companygstin') ?? '',
            'header1' => $header1,
            'header2' => $header2,
            'header3' => $header3,
            'header4' => $header4,
            'slogan1' => $slogan1,
            'slogan2' => $slogan2,
            'company_title' => $comptitle,
            'outlet_title' => $outlettitle,
            'token_print' => $orderbookingtokenprint,
            'print_type' => $printingtype,
            'order_booking' => $orderbooking,
            'member_info' => $memberinfo,
            'party_name' => $partyname,
            'split_bill' => $splitbill,
            'cust_info' => $customerinfo,
            'ckot_print_path' => $printingpathtypetxt,
            'cur_token_no' => $currenttokenno_sale,
            'no_of_kot' => $nofkot,
            'no_of_bill' => $nofbills,
            'token_print_after' => $printtokenafter,
            'token_print_before' => $printtokenbefore,
            'print_on_save' => $printonsave,
            'print_token_no' => $printtokenno,
            'auto_settlement' => $autosettlement,
            'token_header' => $tokenheader,
            'barcode_app' => $barcodeapp,
            'auto_reset_token' => $autoresettoken,
            'cur_token_no_kot' => $currenttokennokot,
            'dis_print' => $discountpercentprint,
            'grp_disc_app' => $groupdiscount,
            'label_printing' => $labelprinting,
            'free_item_app' => $freeitemapp,
            'cover_mandatory' => $cover,
            'mobile_no_mandatory' => $mobileno_select,
            'divcode' => $request->divcode
        ];

        DB::table($tableName)->insert($insertData);

        // User Module Insert

        function createUser($opt1, $opt2, $opt3, $route, $module, $module_name, $flag, $currentTime, $outletcode)
        {
            $usermodule = new UserModule();
            $usermodule->propertyid = Auth::user()->propertyid;
            $usermodule->opt1 = $opt1;
            $usermodule->opt2 = $opt2;
            $usermodule->opt3 = $opt3;
            $usermodule->code = sprintf("%02d%02d%02d", $opt1, $opt2, $opt3);
            $usermodule->route = $route;
            $usermodule->module = $module;
            $usermodule->module_name = $module_name;
            $usermodule->flag = $flag;
            $usermodule->outletcode = $outletcode;
            $usermodule->u_entdt = $currentTime;
            $usermodule->u_updatedt = null;
            $usermodule->save();
        }

        function createMenuHelp($compcode, $opt1, $opt2, $opt3, $route, $module, $module_name, $ins, $edit, $del, $print, $flag, $currentTime, $outletcode)
        {
            $menuhelp = new MenuHelp();
            $menuhelp->propertyid = Auth::user()->propertyid;
            $menuhelp->username = Auth::user()->name;
            $menuhelp->compcode = $compcode;
            $menuhelp->opt1 = $opt1;
            $menuhelp->opt2 = $opt2;
            $menuhelp->opt3 = $opt3;
            $menuhelp->code = sprintf("%02d%02d%02d", $opt1, $opt2, $opt3);
            $menuhelp->route = $route;
            $menuhelp->module = $module;
            $menuhelp->module_name = $module_name;
            $menuhelp->ins = $ins;
            $menuhelp->edit = $edit;
            $menuhelp->del = $del;
            $menuhelp->print = $print;
            $menuhelp->flag = $flag;
            $menuhelp->outletcode = $outletcode;
            $menuhelp->u_name = Auth::user()->name;
            $menuhelp->u_entdt = $currentTime;
            $menuhelp->u_updatedt = null;
            $menuhelp->save();
        }
        $dcode = $request->input('short_name') . $this->propertyid;
        $opt1 = 17;
        $maxopt2 = UserModule::where('propertyid', $this->propertyid)->where('opt1', $opt1)->max('opt2');
        $kotname = 'KOT';
        $salename = 'Sale Bill Entry';
        $posname = 'POS';
        $rt = true;
        $modulename = 'Pointofsale';

        if (strtolower($outletname) == 'laundry') {
            $opt1 = 15;
            $maxopt2 = 13;
            $kotname = 'LOT';
            $salename = 'Laundry Memo';
            $posname = 'Memo';
            $rt = false;
            $modulename = 'Housekeeping';
        }

        if (strtolower($outletname) == 'minibar' || strtolower($outletname) == 'mini bar') {
            $opt1 = 15;
            $maxopt2 = 14;
            $rt = false;
            $modulename = 'Housekeeping';
        }
        createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 0, 'javascript:void()', $outletname, $modulename, 1, 1, 1, 1, 'N', $this->currenttime, $dcode);
        createUser($opt1, $maxopt2 + 1, 0, 'javascript:void()', $outletname, $modulename, 'N', $this->currenttime, $dcode);
        createUser($opt1, $maxopt2 + 1, 11, 'salebillentry?dcode=' . $dcode, $salename, $modulename, 'E', $this->currenttime, $dcode);
        createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 11, 'salebillentry?dcode=' . $dcode, $salename, $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        createUser($opt1, $maxopt2 + 1, 12, 'posbillentry?dcode=' . $dcode, 'POS Bill Reprint', $modulename, 'E', $this->currenttime, $dcode);
        createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 12, 'posbillentry?dcode=' . $dcode, $posname . ' Bill Reprint', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        createUser($opt1, $maxopt2 + 1, 13, 'settlemententry?dcode=' . $dcode, 'Settlement Entry', $modulename, 'E', $this->currenttime, $dcode);
        createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 13, 'settlemententry?dcode=' . $dcode, 'Settlement Entry', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        if ($kot == 'Y' && $request->input('outletNature') == 'Outlet') {
            createUser($opt1, $maxopt2 + 1, 14, 'kotentry?dcode=' . $dcode, $kotname . ' Entry', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 14, 'kotentry?dcode=' . $dcode, $kotname . ' Entry', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
            if ($rt == true) {
                createUser($opt1, $maxopt2 + 1, 15, 'tablechangeentry?dcode=' . $dcode, 'Table Change Entry', $modulename, 'E', $this->currenttime, $dcode);
                createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 15, 'tablechangeentry?dcode=' . $dcode, 'Table Change Entry', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
                createUser($opt1, $maxopt2 + 1, 16, 'tablebooking?dcode=' . $dcode, 'Table Booking', $modulename, 'E', $this->currenttime, $dcode);
                createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 16, 'tablebooking?dcode=' . $dcode, 'Table Booking', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
                createUser($opt1, $maxopt2 + 1, $opt1, 'billlockup?dcode=' . $dcode, 'Bill Look Up', $modulename, 'E', $this->currenttime, $dcode);
                createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, $opt1, 'billlockup?dcode=' . $dcode, 'Bill Look Up', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
                createUser($opt1, $maxopt2 + 1, 18, 'displaytable?dcode=' . $dcode, 'Display Table', $modulename, 'E', $this->currenttime, $dcode);
                createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 18, 'displaytable?dcode=' . $dcode, 'Display Table', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
                createUser($opt1, $maxopt2 + 1, 20, 'paymentreceived?dcode=' . $dcode, 'Payment Received', $modulename, 'E', $this->currenttime, $dcode);
                createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 20, 'paymentreceived?dcode=' . $dcode, 'Payment Received', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
            }
            createUser($opt1, $maxopt2 + 1, 19, 'kottransfer?dcode=' . $dcode, $kotname . ' Transfer', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 19, 'kottransfer?dcode=' . $dcode, $kotname . ' Transfer', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        } elseif ($kot == 'Y' && in_array($request->input('outletNature'), ['ROOM SERVICE', 'Outlet'])) {
            createUser($opt1, $maxopt2 + 1, 14, 'kotentry?dcode=' . $dcode, $kotname . ' Entry', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 14, 'kotentry?dcode=' . $dcode, $kotname . ' Entry', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
            createUser($opt1, $maxopt2 + 1, 18, 'displaytable?dcode=' . $dcode, 'Display Table', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 18, 'displaytable?dcode=' . $dcode, 'Display Table', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
            createUser($opt1, $maxopt2 + 1, 19, 'kottransfer?dcode=' . $dcode, $kotname . ' Transfer', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 19, 'kottransfer?dcode=' . $dcode, $kotname . ' Transfer', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        } elseif ($request->input('outletNature') != 'ROOM SERVICE') {
            createUser($opt1, $maxopt2 + 1, 21, 'splitbill?dcode=' . $dcode, 'Split Bill', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 21, 'splitbill?dcode=' . $dcode, 'Split Bill', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        } elseif ($request->input('orderBooking') == 'Y') {
            createUser($opt1, $maxopt2 + 1, 22, 'orderbooking?dcode=' . $dcode, 'Order Booking', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 22, 'orderbooking?dcode=' . $dcode, 'Order Booking', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
            createUser($opt1, $maxopt2 + 1, 23, 'orderbookingadvance?dcode=' . $dcode, 'Order Booking Advance', $modulename, 'E', $this->currenttime, $dcode);
            createMenuHelp($this->compcode, $opt1, $maxopt2 + 1, 23, 'orderbookingadvance?dcode=' . $dcode, 'Order Booking Advance', $modulename, 1, 1, 1, 1, 'E', $this->currenttime, $dcode);
        }

        return back()->with('success', 'Outlet Setup Inserted successfully!');
        // } catch (Exception $e) {
        //     return response()->json(['message' => 'Unable to Insert Outlet Setup!'], 500);
        // }
    }

    public function deleteoutlet(Request $request, $sn, $short_name, $dcode)
    {
        $permission = revokeopen(121311);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('depart')
                ->where('propertyid', $this->propertyid)
                ->where('short_name', $short_name)
                ->where('sn', $sn)
                ->delete();
            $jaldiwahasehato2📢 = DB::table('voucher_type')
                ->where('propertyid', $this->propertyid)
                ->where('restcode', $short_name . $this->propertyid)
                ->delete();
            $jaldiwahasehato3📢 = DB::table('voucher_prefix')
                ->where('propertyid', $this->propertyid)
                ->where('short_name', $short_name)
                ->delete();
            $jaldiwahasehato4📢 = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where('short_name', $short_name)
                ->delete();

            $jaldiwahasehato5📢 = DB::table('usermodule')
                ->where('propertyid', $this->propertyid)
                ->where('outletcode', $dcode)
                ->delete();

            $jaldiwahasehato6📢 = DB::table('menuhelp')
                ->where('propertyid', $this->propertyid)
                ->where('outletcode', $dcode)
                ->delete();

            # This code is so beautiful, it brings a tear to my eye. 😢💻

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Outlet Setup Deleted successfully!');
            } else {
                return back()->with('error', 'Unable to Delete Outlet Setup!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function getupdateoutlet(Request $request)
    {
        $cid = $request->input('cid');
        $data = DB::table('depart')
            ->where('depart.sn', $cid)
            ->first();
        return json_encode($data);
    }

    public function outletsetupupdate(Request $request)
    {
        $permission = revokeopen(121311);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'depart';

        // Outlet Details
        $outletnature = $request->input('uoutletNature');
        $mobileno = $request->input('umobileNo');
        $splitbill = $request->input('usplitBill');
        $orderbooking = $request->input('uorderBooking');
        $barcodeapp = $request->input('ubarCodeApp');
        $labelprinting = $request->input('ulabelPrinting');

        // KOT Printing Information
        $orderbookingtokenprint = $request->input('uorderBookingTokenPrint');
        $printingtype = $request->input('uprintingType');
        $printingpathtypetxt = $request->input('uprintingPathTypeTxt');
        $nofkot = $request->input('uNOfKOT');
        $currenttokennokot = $request->input('ucurrentTokenNo');

        // Sale Bill Setup
        $partyname = $request->input('upartyName');
        $memberinfo = $request->input('umemberInfo');
        $customerinfo = $request->input('ucustomerInfo');
        $freeitemapp = $request->input('ufreeItemApp');
        $cover = $request->input('ucover');
        $autosettlement = $request->input('uautoSettlement');
        $printonsave = $request->input('uprintOnSave');
        $autoresettoken = $request->input('uautoResetToken');
        $mobileno_select = $request->input('umobileNoyn');
        $currenttokenno_sale = $request->input('ucurrentTokenNosale');

        // Sale Bill Printing Information
        $comptitle = $request->input('ucompTitle');
        $outlettitle = $request->input('uoutletTitle');
        $nofbills = $request->input('uNOfBills');
        $discountpercentprint = $request->input('udiscountPercentPrint');
        $printtokenbefore = $request->input('uprintTokenBefore');
        $printtokenafter = $request->input('uprintTokenAfter');
        $printtokenno = $request->input('uprintTokenNo');
        $groupdiscount = $request->input('ugroupDiscount');
        $header1 = $request->input('uheader1');
        $header2 = $request->input('uheader2');
        $header3 = $request->input('uheader3');
        $header4 = $request->input('uheader4');
        $slogan1 = $request->input('uslogan1');
        $slogan2 = $request->input('uslogan2');
        $tokenheader = $request->input('utokenHeader');

        // Order Booking Printing Information
        $firstcopyremark = $request->input('ufirstCopyRemark');
        $secondcopyremark = $request->input('usecondCopyRemark');

        // Scheme Details
        $schemename = $request->input('uschemename');
        $discountscheme = $request->input('udiscscheme');

        $companylogo = $request->input('oldcompanylogo', '');

        if ($request->hasFile('upcompanylogo')) {
            $companypic = $request->file('upcompanylogo');
            $companylogo = $request->input('upoutletname') . $this->propertyid . 'OT' . $request->input('short_name') . $this->propertyid . '.' . $companypic->getClientOriginalExtension();
            $folderpath = 'public/admin/property_logo';
            Storage::makeDirectory($folderpath);
            if (!empty($request->input('oldcompanylogo')) && Storage::exists($folderpath . '/' . $request->input('oldcompanylogo'))) {
                Storage::delete($folderpath . '/' . $request->input('oldcompanylogo'));
            }
            $filepath = Storage::putFileAs($folderpath, $companypic, $companylogo);
        }


        // try {
        $updatedata = [
            'u_updatedt' => $this->currenttime,
            'height' => $request->input('uheight'),
            'uborderspace' => $request->input('uborderspace'),
            'fontsize' => $request->input('ufont_size'),
            'col' => $request->input('ucol'),
            'sysYN' => 'N',
            'u_name' => Auth::user()->u_name,
            'propertyid' => $this->propertyid,
            'u_ae' => 'e',
            'rest_type' => $request->input('rest_type'),
            'pos' => 'Y',
            'outlet_yn' => 'Y',
            'nature' => $outletnature,
            'companyname' => $request->input('upcompanyname') ?? '',
            'logo' => $companylogo,
            'gstin' => $request->input('upcompanygstin') ?? '',
            'mobile_no' => $mobileno,
            'header1' => $header1,
            'header2' => $header2,
            'header3' => $header3,
            'header4' => $header4,
            'slogan1' => $slogan1,
            'slogan2' => $slogan2,
            'company_title' => $comptitle,
            'outlet_title' => $outlettitle,
            'token_print' => $orderbookingtokenprint,
            'print_type' => $printingtype,
            'order_booking' => $orderbooking,
            'member_info' => $memberinfo,
            'party_name' => $partyname,
            'split_bill' => $splitbill,
            'cust_info' => $customerinfo,
            'ckot_print_path' => $printingpathtypetxt,
            'cur_token_no' => $currenttokenno_sale,
            'no_of_kot' => $nofkot,
            'no_of_bill' => $nofbills,
            'token_print_after' => $printtokenafter,
            'token_print_before' => $printtokenbefore,
            'print_on_save' => $printonsave,
            'print_token_no' => $printtokenno,
            'auto_settlement' => $autosettlement,
            'token_header' => $tokenheader,
            'barcode_app' => $barcodeapp,
            'auto_reset_token' => $autoresettoken,
            'cur_token_no_kot' => $currenttokennokot,
            'dis_print' => $discountpercentprint,
            'grp_disc_app' => $groupdiscount,
            'label_printing' => $labelprinting,
            'free_item_app' => $freeitemapp,
            'cover_mandatory' => $cover,
            'mobile_no_mandatory' => $mobileno_select,
            'divcode' => $request->updivcode
        ];
        DB::table($tableName)
            ->where('sn', $request->input('snnum'))
            ->where('propertyid', $this->propertyid)
            ->update($updatedata);
        return back()->with('success', 'Outlet Setup Updated successfully!');
        // } catch (Exception $e) {
        //     return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        // }
    }

    public function saveSignature(Request $request)
    {
        $imageData = $request->input('image');

        $encodedImage = str_replace('data:image/png;base64,', '', $imageData);
        $decodedImage = base64_decode($encodedImage);

        $filename = 'signature_' . Str::random(10) . '.png';

        $folder = 'walkin/signature';
        $path = storage_path('app/public/' . $folder . '/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $decodedImage);

        return response()->json(['path' => Storage::url($folder . '/' . $filename)]);
    }

    public function submitwalkin(Request $request)
    {
        $permission = revokeopen(141112);
        if (is_null($permission) || $permission->ins == 0) {
            return response()->json([
                'redirecturl' => '',
                'status' => 'error',
                'message' => 'You have no permission to execute this functionality!'
            ]);
        }
        try {
            $validate = $request->validate([
                'name' => 'required|string',
                'cityname' => 'required|string',
                'checkindate' => 'required|date',
                'checkoutdate' => 'required|date|after_or_equal:checkindate',
                'checkintime' => 'required',
                'checkouttime' => 'required',
                'totalrooms' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();
            $vtype = "CHK";
            $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $request->checkindate)
                ->whereDate('date_to', '>=', $request->checkindate)
                ->first();
            if ($chkvpf === null || $chkvpf === '0') {
                DB::rollBack();
                return response()->json([
                    'redirecturl' => '',
                    'status' => 'error',
                    'message' => 'You are not eligible to checkin for this date: ' . date('d-m-Y', strtotime($request->checkindate)),
                ]);
            }

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;

            $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('country'))->first();
            $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('cityname'))->first();
            if (!empty($request->input('issuingcity'))) {
                $issuingcityname = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('issuingcity'))->first();
                $issuingcountryname = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('issuingcountry'))->first();
            }
            $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $request->input('state'))->first();

            $dob = $request->input('birthDate');
            $age = Carbon::parse($dob)->age;

            $profilepicture = null;
            $identitypicture = null;

            if (!empty($request->file('profileimage'))) {
                $profilepic = $request->file('profileimage');
                $profilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $profilepic->getClientOriginalExtension();
                $folderPathp = 'public/walkin/profileimage';
                Storage::makeDirectory($folderPathp);
                Storage::putFileAs($folderPathp, $profilepic, $profilepicture);
            } else {
                $existingProfileImage = $request->input('existing_profileimage');
                if ($existingProfileImage != '') {
                    $folderPathp = 'public/walkin/profileimage';
                    $existingFilePath = $folderPathp . '/' . $existingProfileImage;

                    $newProfilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . pathinfo($existingProfileImage, PATHINFO_EXTENSION);
                    $newFilePath = $folderPathp . '/' . $newProfilepicture;

                    if (Storage::exists($existingFilePath)) {
                        Storage::copy($existingFilePath, $newFilePath);
                        $profilepicture = $newProfilepicture;
                    } else {
                        $profilepicture = null;
                    }
                } else {
                    $profilepicture = null;
                }
            }

            if (!empty($request->file('identityimage'))) {
                $identitypic = $request->file('identityimage');
                $identitypicture = $request->input('guestmobile') . $request->input('guestname') . 'ID' . $this->propertyid . time() . '.' . $identitypic->getClientOriginalExtension();
                $folderpathi = 'public/walkin/identityimage';
                Storage::makeDirectory($folderpathi);
                Storage::putFileAs($folderpathi, $identitypic, $identitypicture);
            } else {
                $existingIdentityImage = $request->input('existing_identityimage');
                if ($existingIdentityImage != '') {
                    $folderpathi = 'public/walkin/identityimage';
                    $existingFilePath = $folderpathi . '/' . $existingIdentityImage;
                    $newIdentitypicture = $request->input('guestmobile') . $request->input('guestname') . 'ID' . $this->propertyid . time() . '.' . pathinfo($existingIdentityImage, PATHINFO_EXTENSION);
                    $newFilePath = $folderpathi . '/' . $newIdentitypicture;

                    if (Storage::exists($existingFilePath)) {
                        Storage::copy($existingFilePath, $newFilePath);
                        $identitypicture = $newIdentitypicture;
                    } else {
                        $identitypicture = null;
                    }
                } else {
                    $identitypicture = null;
                }
            }

            $signfilename = '';
            if (!empty($request->input('signimage'))) {
                $imageData = $request->input('signimage');

                $encodedImage = str_replace('data:image/png;base64,', '', $imageData);
                $decodedImage = base64_decode($encodedImage);

                $signfilename = $request->input('guestmobile') . $request->input('guestname') . 'signature_' . time() . '.png';

                $folder = 'walkin/signature';
                $path = storage_path('app/public/' . $folder . '/' . $signfilename);

                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }

                file_put_contents($path, $decodedImage);
            }

            $roomrate = $request->input('rate1');

            if ($request->input('complimentry') == 'on') {
                $complimentry = 'Y';
                $roomrate = 0;
            } else {
                $complimentry = 'N';
            }

            $maxguestprof = GuestProf::where('propertyid', $this->propertyid)->max('guestcode');
            $guestprof = ($maxguestprof === null) ? $this->propertyid . '10001' : ($guestprof = $this->propertyid . substr($maxguestprof, $this->ptlngth) + 1);

            $guestprofflag = false;
            if ($request->input('guestfetch') == 'Y') {
                $guestprofflag = true;
                $guestfetchdocid = $request->input('guestfetchdocid');
                $findexist = GuestProf::where('propertyid', $this->propertyid)->where('docid', $guestfetchdocid)->first();
                $guestprof = $findexist->guestcode;
            }

            $docid = $this->propertyid . 'CHK' . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

            $count = $request->totalrooms;
            $sno1 = 1;
            $bookingsno = '';
            $maxsno = GrpBookinDetail::where('BookingDocid', $request->input('docid'))->where('Property_ID', $this->propertyid)->max('Sno');

            $advcheck = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('refdocid', $request->input('docid'))->where('sno', '1')->where('sno1', $maxsno)->get();
            $planrowscount = 0;
            $inyn = 1;
            $leaders = [];
            for ($i = 1; $i <= $count; $i++) {
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = round($request->input('rate' . $i));
                $totalroomrate = 0.00;
                $totalrateaftertax = 0.00;

                // Check repeatsame room same date
                $checkrepeat = RoomOcc::where('propertyid', $this->propertyid)
                    ->where('roomno', $request->input('roommast' . $i))
                    ->where('roomcat', $request->input('cat_code' . $i))
                    ->where('chkindate', $request->input('checkindate'))
                    ->where(function ($query) use ($request, $i) {
                        $query->where('depdate', $request->input('checkoutdate'))
                            ->whereBetween('depdate', [$request->input('checkindate'), $request->input('checkoutdate')]);
                    })
                    ->whereNull('type')
                    ->first();
                if (!is_null($checkrepeat)) {
                    DB::rollBack();
                    // return $checkrepeat;
                    $inyn = 0;
                    RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                    PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                    Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();
                    if (!empty($request->input('docid'))) {
                        UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
                    }
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Room No. ' . $request->input('roommast' . $i) . ' Already Booked for Date: ' . date('d-m-Y', strtotime($request->input('checkindate'))) . ' to ' . date('d-m-Y', strtotime($request->input('checkoutdate'))) . ' Please Select Another Room or Date',
                    ]);
                }
                $ratenew = 0;
                $ratenewreverse = 0;
                $postamount = 0;
                $pamount = $request->input('rowdamount' . $i);
                $ptaxstru = TaxStructure::where('propertyid', $this->propertyid)->where('str_code', $request->input('rowstax_stru' . $i))->sum('rate');

                if ($request->input('tax_inc' . $i) == 'Y') {

                    $postamount = ($pamount * 100) / ($ptaxstru + 100);

                    foreach ($fetchtaxstru as $taxstru) {
                        $limitstart = $taxstru->limits;
                        $limitend = $taxstru->limit1;
                        $rate = $taxstru->rate;
                        $comp_operator = $taxstru->comp_operator;
                        if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                            $ratenew += $rate;
                            $fixedrate = $ratenew + 100;
                        } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                            $ratenew += $rate;
                            $fixedrate = $ratenew + 100;
                        }
                    }
                    $calcedamttmp = $roomrate * 100 / $fixedrate;
                    $calcedamt = number_format($calcedamttmp, 2);

                    $ratenewreverse = 0;

                    foreach ($fetchtaxstru as $taxstrurow) {
                        $limitstart2 = floatval(trim($taxstrurow->limits));
                        $limitend2 = $taxstrurow->limit1 !== null ? floatval(trim($taxstrurow->limit1)) : null;
                        $rate2 = floatval($taxstrurow->rate);
                        $comp_operator2 = trim($taxstrurow->comp_operator);
                        $roundedAmt = round($calcedamttmp);

                        if (!is_null($limitend2) && $comp_operator2 === 'Between') {
                            if ($roundedAmt >= $limitstart2 && $roundedAmt <= $limitend2) {
                                $ratenewreverse += $rate2;
                            }
                        } elseif ($comp_operator2 !== 'Between') {
                            if ($roundedAmt >= $limitstart2) {
                                $ratenewreverse += $rate2;
                            }
                        }
                    }

                    $fixedratereverse = $ratenewreverse + 100;

                    $calcedamtreversetmp = ($calcedamttmp * $fixedratereverse) / 100;
                    $calcedamtreverse = number_format($calcedamtreversetmp, 2);

                    if ($roomrate != round($calcedamtreversetmp)) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            // 'message' => 'Tax Slab Calculation Mismatch Amount: ' . $roomrate . ' and after taxsum: ' . $calcedamtreverse
                            'message' => 'Invalid Room Tarrif'
                        ], 500);
                    }
                } else {
                    $calcedamt = $roomrate;
                }

                if ($request->input('planedit' . $i) == 'Y') {
                    $planamount = $request->input('plankaamount' . $i);
                } else {
                    $planamount = $roomrate;
                }
                $roomoccdata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'name' => $request->input('name'),
                    'sno' => 1,
                    'sno1' => $sno1,
                    'folioNo' => $start_srl_no,
                    'vtype' => $vtype,
                    'vprefix' => $vprefix,
                    'guestprof' => $guestprof,
                    'roomcat' => $request->input('cat_code' . $i),
                    'roomtype' => 'RO',
                    'roomno' => $request->input('roommast' . $i),
                    'ratecode' => 2,
                    'depdate' => $request->input('checkoutdate'),
                    'deptime' => $request->input('checkouttime'),
                    'nodays' => $request->input('stay_days'),
                    'rrservicechrg' => '',
                    'chngdate' => $request->input('checkindate'),
                    'roomtaxstru' => $rtaxstru,
                    'roomrate' => str_replace(',', '', $calcedamt),
                    'rackrate' => $roomrate,
                    'chkindate' => $request->input('checkindate'),
                    'chkintime' => $request->input('checkintime'),
                    'adult' => $request->input('adult' . $i),
                    'children' => $request->input('child' . $i),
                    'chkoutdate' => null,
                    'chkouttime' => null,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'rodisc' => $request->input('rodisc'),
                    'rsdisc' => $request->input('rsdisc'),
                    'plancode' => $request->input('planmaster' . $i),
                    'planamt' => $planamount,
                    'rrtaxinc' => $request->input('tax_inc' . $i),
                    'leaderyn' => $request->input('leader' . $i) == 'on' ? 'Y' : 'N',
                    'reasonrchange' => ''
                ];

                $plandetails = [
                    'propertyid' => $this->propertyid,
                    'foliono' => $start_srl_no,
                    'docid' => $docid,
                    'sno' => 1,
                    'sno1' => $sno1,
                    'roomno' => $request->input('roommast' . $i),
                    'room_rate_before_tax' => $request->input('roomrate' . $i),
                    'total_rate' => $request->input('plansumrate' . $i),
                    'pcode' => $request->input('planmaster' . $i),
                    'noofdays' => $request->input('stay_days'),
                    'rev_code' => $request->input('rowsrev_code' . $i),
                    'fixrate' => $request->input('rowdplanfixrate' . $i),
                    'planper' => $request->input('rowdplan_per' . $i),
                    'amount' => $postamount,
                    'netplanamt' => $request->input('plankaamount' . $i),
                    'taxinc' => $request->input('taxincplanroomrate' . $i),
                    'taxstru' => $request->input('rowstax_stru' . $i),
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                $roomcat = RoomCat::where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->first();

                if ($inyn === 1) {

                    $totalroomrate += $roomrate;
                    $totalrateaftertax += str_replace(',', '', $calcedamt);

                    RoomOcc::insert($roomoccdata);
                    if ($request->input('planedit' . $i) == 'Y') {
                        PlanDetail::insert($plandetails);
                        $planrowscount++;
                    }

                    $leaderinserted = false;

                    if (!empty($request->input('docid'))) {
                        $grp = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $request->input('docid'))->first();
                        $bookingsno = $grp->Sno;
                        $fillamttmp = 0.00;
                        if ($advcheck !== null) {
                            $roomno = $request->input('roommast' . $i);
                            $roomcat = $request->input('cat_code' . $i);
                            $n = 1;
                            foreach ($advcheck as $row) {
                                $fillamttmp = $row->amtcr;
                                $paycode = $row->paycode;
                                $comments = $row->comments;
                                $billamount = $row->billamount;
                                $leadercheck = $request->input('leader' . $i) == 'on' ? 'Y' : 'N';
                                $leaders[] = $leadercheck;

                                if ($leadercheck == 'N') {
                                    $fillamt = $fillamttmp / $count;
                                    $paychargedata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'folionodocid' => $docid,
                                        'refdocid' => $request->input('docid'),
                                        'foliono' => $start_srl_no,
                                        'sno' => $n,
                                        'sno1' => $sno1,
                                        'vno' => $sno1,
                                        'vtype' => $vtype,
                                        'vprefix' => $vprefix,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'paycode' => $paycode,
                                        'paytype' => $row->paytype,
                                        'comments' => $comments,
                                        'guestprof' => $guestprof,
                                        'comp_code' => '',
                                        'travel_agent' => '',
                                        'roomno' => $roomno,
                                        'amtcr' => $fillamt,
                                        'roomcat' => $roomcat,
                                        'roomtype' => 'RO',
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $billamount,
                                        'taxper' => 0,
                                        'onamt' => 0,
                                        'taxstru' => '',
                                        'taxcondamt' => 0,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];
                                    Paycharge::insert($paychargedata);
                                    $n++;
                                } else if ($leadercheck == 'Y') {
                                    if (!$leaderinserted) {
                                        $paychargedata = [
                                            'propertyid' => $this->propertyid,
                                            'docid' => $docid,
                                            'folionodocid' => $docid,
                                            'refdocid' => $request->input('docid'),
                                            'foliono' => $start_srl_no,
                                            'sno' => $n,
                                            'sno1' => $sno1,
                                            'msno1' => $sno1,
                                            'vno' => $sno1,
                                            'vtype' => $vtype,
                                            'vprefix' => $vprefix,
                                            'vdate' => $ncurdate,
                                            'vtime' => date('H:i:s'),
                                            'paycode' => $paycode,
                                            'paytype' => $row->paytype,
                                            'comments' => $comments,
                                            'guestprof' => $guestprof,
                                            'comp_code' => '',
                                            'travel_agent' => '',
                                            'roomno' => $roomno,
                                            'amtcr' => $fillamttmp,
                                            'roomcat' => $roomcat,
                                            'roomtype' => 'RO',
                                            'restcode' => 'FOM' . $this->propertyid,
                                            'billamount' => $billamount,
                                            'taxper' => 0,
                                            'onamt' => 0,
                                            'taxstru' => '',
                                            'taxcondamt' => 0,
                                            'u_entdt' => $this->currenttime,
                                            'u_name' => Auth::user()->u_name,
                                            'u_ae' => 'a',
                                        ];
                                        DB::table('paycharge')->insert($paychargedata);
                                        $n++;
                                    }
                                }
                            }
                        }
                        $upnew = [
                            'ContraDocId' => $docid,
                            'ContraSno' => $sno1,
                        ];
                        DB::table('grpbookingdetails')
                            ->where('Property_ID', $this->propertyid)
                            ->where('BookingDocid', $request->input('docid'))
                            ->where('Sno', $i ?? 1)
                            ->update($upnew);
                    }
                }

                $sno1++;
            }

            if (in_array('Y', $leaders)) {
                Paycharge::where('refdocid', $request->input('docid'))->where('msno1', '0')
                    ->where('docid', $docid)->delete();
            }

            $guestfolio = [
                'propertyid' => $this->propertyid,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'remarks' => $request->remarkmain ?? '',
                'pickupdrop' => $request->pickupdrop ?? '',
                'add1' => $request->input('address1') ?? '',
                'add2' => $request->input('address2') ?? '',
                'docid' => $docid,
                'folio_no' => $start_srl_no,
                'vtype' => $vtype,
                'vdate' => $ncurdate,
                'bookingdocid' => $request->input('docid') ?? '',
                'bookingsno' => $bookingsno,
                'vprefix' => $vprefix,
                'booking_source' => $request->input('booking_source') ?? '',
                'guestprof' => $guestprof,
                'travelagent' => $request->input('travel_agent'),
                'name' => $request->input('name'),
                'city' => $request->input('cityname'),
                'nodays' => $request->input('stay_days'),
                'roomcount' => $request->input('rooms') ?? '1',
                'purvisit' => $request->input('purpofvisit'),
                'company' => $request->input('company'),
                'arrfrom' => $request->input('arrfrom'),
                'vehiclenum' => $request->input('vehiclenum'),
                'destination' => $request->input('destination'),
                'travelmode' => $request->input('travelmode'),
                'rodisc' => $request->input('rodisc'),
                'rsdisc' => $request->input('rsdisc'),
                'busssource' => $request->input('bsource'),
                'depdate' => $request->input('checkoutdate'),
            ];

            if ($guestprofflag == false) {

                $guestproft = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'folio_no' => $start_srl_no,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                    'complimentry' => $complimentry,
                    'guestcode' => $guestprof,
                    'name' => $request->input('name'),
                    'state_code' => $request->input('state'),
                    'country_code' => $request->input('country'),
                    'add1' => $request->input('address1'),
                    'add2' => $request->input('address2'),
                    'city' => $request->input('cityname'),
                    'type' => $countrydata->Type,
                    'mobile_no' => $request->input('mobile'),
                    'email_id' => $request->input('email'),
                    'nationality' => $countrydata->nationality ?? null,
                    'anniversary' => $request->input('weddingAnniversary'),
                    'guest_status' => $request->input('vipStatus'),
                    'comments1' => null,
                    'comments2' => null,
                    'comments3' => null,
                    'city_name' => $citydata->cityname,
                    'state_name' => $statedata->name,
                    'country_name' => $countrydata->name,
                    'gender' => $request->input('genderguest'),
                    'marital_status' => $request->input('marital_status'),
                    'zip_code' => $citydata->zipcode,
                    'con_prefix' => $request->input('greetings'),
                    'dob' => $dob,
                    'age' => $age,
                    'pic_path' => $profilepicture ?? '',
                    'guestsign' => $signfilename ?? '',
                    'id_proof' => $request->input('idType'),
                    'idproof_no' => $request->input('idNumber'),
                    'issuingcitycode' => $request->input('issuingcity') ?? null,
                    'issuingcityname' => $issuingcityname->cityname ?? null,
                    'issuingcountrycode' => $request->input('issuingcountry') ?? null,
                    'issuingcountryname' => $issuingcountryname->name ?? null,
                    'expiryDate' => $request->input('expiryDate'),
                    'vipStatus' => $request->input('vipStatus'),
                    'paymentMethod' => $request->input('paymentMethod'),
                    'billingAccount' => $request->input('billingAccount'),
                    'idpic_path' => $identitypicture,
                    'm_prof' => $guestprof,
                    'father_name' => null,
                    'fom' => 1,
                    'pos' => 0,
                ];
                DB::table('guestprof')->insert($guestproft);
            } else {
                $guestproft = [
                    'u_updatedt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'e',
                    'complimentry' => $complimentry,
                    'guestcode' => $guestprof,
                    'name' => $request->input('name'),
                    'state_code' => $request->input('state'),
                    'country_code' => $request->input('country'),
                    'add1' => $request->input('address1'),
                    'add2' => $request->input('address2'),
                    'city' => $request->input('cityname'),
                    'type' => $countrydata->Type,
                    'mobile_no' => $request->input('mobile'),
                    'email_id' => $request->input('email'),
                    'nationality' => $countrydata->nationality ?? null,
                    'anniversary' => $request->input('weddingAnniversary'),
                    'guest_status' => $request->input('vipStatus'),
                    'comments1' => null,
                    'comments2' => null,
                    'comments3' => null,
                    'city_name' => $citydata->cityname,
                    'state_name' => $statedata->name,
                    'country_name' => $countrydata->name,
                    'gender' => $request->input('genderguest'),
                    'marital_status' => $request->input('marital_status'),
                    'zip_code' => $citydata->zipcode,
                    'con_prefix' => $request->input('greetings'),
                    'dob' => $dob,
                    'age' => $age,
                    'pic_path' => $profilepicture,
                    'guestsign' => $signfilename,
                    'id_proof' => $request->input('idType'),
                    'idproof_no' => $request->input('idNumber'),
                    'issuingcitycode' => $request->input('issuingcity') ?? null,
                    'issuingcityname' => $issuingcityname->cityname ?? null,
                    'issuingcountrycode' => $request->input('issuingcountry') ?? null,
                    'issuingcountryname' => $issuingcountryname->name ?? null,
                    'expiryDate' => $request->input('expiryDate'),
                    'vipStatus' => $request->input('vipStatus'),
                    'paymentMethod' => $request->input('paymentMethod'),
                    'billingAccount' => $request->input('billingAccount'),
                    'idpic_path' => $identitypicture,
                    'm_prof' => $guestprof,
                    'father_name' => null,
                    'fom' => 1,
                    'pos' => 0,
                ];

                GuestProf::where('guestcode', $guestprof)->where('propertyid', $this->propertyid)->update($guestproft);
            }

            $guestfolioprofdetail = [
                'propertyid' => $this->propertyid,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'doc_id' => $docid,
                'folio_no' => $start_srl_no,
                'guest_prof' => $guestprof,
                'mprof' => $guestprof,
            ];

            DB::table('guestfolio')->insert($guestfolio);
            DB::table('guestfolioprofdetail')->insert($guestfolioprofdetail);

            $chkgfolio = Guestfolio::where('guestprof', $guestprof)->where('propertyid', $this->propertyid)->first();
            $chkgprof = GuestProf::where('guestcode', $guestprof)->where('propertyid', $this->propertyid)->first();
            $chkgproffolio = GuestFolioProfDetail::where('guest_prof', $guestprof)->where('propertyid', $this->propertyid)->first();

            if (!$chkgprof) {
                RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Guestfolio::where('propertyid', $this->propertyid)->where('guestprof', $guestprof)->delete();
                GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof)->delete();
                GuestFolioProfDetail::where('propertyid', $this->propertyid)->where('guest_prof', $guestprof)->delete();
                PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();
                if (!empty($request->input('docid'))) {
                    UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
                }
                return response()->json([
                    'redirecturl' => 'walkincheckin',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Guest Profile',
                ]);
            }

            if (!$chkgproffolio) {
                RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Guestfolio::where('propertyid', $this->propertyid)->where('guestprof', $guestprof)->delete();
                GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof)->delete();
                GuestFolioProfDetail::where('propertyid', $this->propertyid)->where('guest_prof', $guestprof)->delete();
                PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();
                if (!empty($request->input('docid'))) {
                    UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
                }
                return response()->json([
                    'redirecturl' => 'walkincheckin',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Guest Profile Folio',
                ]);
            }

            if (!$chkgfolio) {
                RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Guestfolio::where('propertyid', $this->propertyid)->where('guestprof', $guestprof)->delete();
                GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof)->delete();
                GuestFolioProfDetail::where('propertyid', $this->propertyid)->where('guest_prof', $guestprof)->delete();
                PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();
                if (!empty($request->input('docid'))) {
                    UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
                }
                return response()->json([
                    'redirecturl' => 'walkincheckin',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Guest Folio',
                ]);
            }

            $plandtcount = PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->count();

            if ($planrowscount != $plandtcount) {
                RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Guestfolio::where('propertyid', $this->propertyid)->where('guestprof', $guestprof)->delete();
                GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof)->delete();
                GuestFolioProfDetail::where('propertyid', $this->propertyid)->where('guest_prof', $guestprof)->delete();
                PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();
                if (!empty($request->input('docid'))) {
                    UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
                }
                return response()->json([
                    'redirecturl' => 'walkincheckin',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Plan Detail',
                ]);
            }

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();

            if ($wpenv != null) {
                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->checkinmsg != '' &&
                    $wpenv->checkinmsgarray != '' &&
                    $wpenv->checkintemplate != '' &&
                    $request->mobile != ''
                ) {
                    $checkinmsgarray = json_decode($wpenv->checkinmsgarray, true);

                    $msgdata = [];
                    foreach ($checkinmsgarray as $row) {
                        [$colname, $table] = $row;
                        $value = DB::table($table)->where('propertyid', $this->propertyid)->where('docid', $docid)->value($colname);
                        $mob = GuestProf::where('propertyid', $this->propertyid)->where('docid', $docid)->value('mobile_no');
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $mob, 'Checkin', 'checkintemplate');
                }

                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->checkinmsgadmin != '' &&
                    $wpenv->checkinmsgadminarray != '' &&
                    $wpenv->checkinmsgadmintemplate != '' &&
                    $wpenv->managementmob != ''
                ) {
                    $checkinmsgadminarray = json_decode($wpenv->checkinmsgadminarray, true);

                    $msgdata = [];
                    foreach ($checkinmsgadminarray as $row) {
                        [$colname, $table] = $row;
                        $value = DB::table($table)->where('propertyid', $this->propertyid)->where('docid', $docid)->value($colname);
                        $mob = GuestProf::where('propertyid', $this->propertyid)->where('docid', $docid)->value('mobile_no');
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $wpenv->managementmob, 'Checkin Admin', 'checkinmsgadmintemplate');
                }
            }
            DB::commit();
            return response()->json([
                'redirecturl' => fomparameter()->pageopenwalkin,
                'status' => 'success',
                'message' => 'Walk-in submission successful.',
            ]);
        } catch (Exception $e) {
            RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Guestfolio::where('propertyid', $this->propertyid)->where('guestprof', $guestprof)->delete();
            GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $guestprof)->delete();
            PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->delete();

            if (!empty($request->input('docid'))) {
                UpdateRepeat::emptygrpcontra($request->input('docid'), $request->bookingsno, $this->propertyid);
            }

            DB::rollBack();

            return response()->json([
                'redirecturl' => '',
                'status' => 'error',
                'message' => 'Unknown Error Occured: ' . $e->getMessage() . ' On Line: ' . $e->getLine(),
            ]);
        }
    }


    public function submitroomchange(Request $request)
    {
        $docid = $request->input('docid');
        $sno = $request->input('sno');
        $sno1 = $request->input('sno1');
        $olddata = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno', $sno)->where('sno1', $request->input('sno1'))->first();
        $chkplanrow = PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno', $sno)->where('sno1', $request->input('sno1'))->first();

        $ncurdate = $this->ncurdate;
        $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $sno1))->value('rev_code');
        $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
        $fetchtaxstru = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->where('str_code', $rtaxstru)
            ->get();
        $roomrate = round($request->input('rate' . $sno1));
        $ratenew = 0;
        $postamount = 0;
        $pamount = $request->input('rowdamount' . $sno1);
        $ptaxstru = TaxStructure::where('propertyid', $this->propertyid)->where('str_code', $request->input('rowstax_stru' . $sno1))->sum('rate');
        if ($request->input('tax_inc' . $sno1) == 'Y') {

            $postamount = ($pamount * 100) / ($ptaxstru + 100);
            foreach ($fetchtaxstru as $taxstru) {
                $limitstart = $taxstru->limits;
                $limitend = $taxstru->limit1;
                $rate = $taxstru->rate;
                $comp_operator = $taxstru->comp_operator;
                if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                    $ratenew += $rate;
                    $fixedrate = $ratenew + 100;
                } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                    $ratenew += $rate;
                    $fixedrate = $ratenew + 100;
                }
            }
            $calcedamt = round($roomrate * 100 / $fixedrate);
        } else {
            $calcedamt = $roomrate;
        }

        $insertnewdata = [
            'propertyid' => $this->propertyid,
            'docid' => $docid,
            'name' => $olddata->name,
            'sno' => $request->input('sno') + 1,
            'sno1' => $olddata->sno1,
            'folioNo' => $olddata->folioNo,
            'vtype' => $olddata->vtype,
            'vprefix' => $olddata->vprefix,
            'guestprof' => $olddata->guestprof,
            'roomcat' => $request->input('cat_code' . $sno1),
            'roomtype' => 'RO',
            'roomno' => $request->input('roommast' . $sno1),
            'ratecode' => 2,
            'depdate' => $olddata->depdate,
            'deptime' => $olddata->deptime,
            'nodays' => $olddata->nodays,
            'rrservicechrg' => '',
            'chngdate' => $ncurdate,
            'roomtaxstru' => $rtaxstru,
            'rackrate' => $olddata->rackrate,
            'roomrate' => round($calcedamt),
            'chkindate' => $request->input('checkindate'),
            'chkintime' => $request->input('checkintime'),
            'adult' => $request->input('adult' . $sno1),
            'children' => $request->input('child' . $sno1),
            'u_entdt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'a',
            'rodisc' => $request->input('rodisc'),
            'rsdisc' => $request->input('rsdisc'),
            'plancode' => $request->input('planmaster' . $sno1),
            'rrtaxinc' => $request->input('tax_inc' . $sno1),
            'reasonrchange' => $request->reason
        ];

        if ($chkplanrow) {
            $plandetails = [
                'propertyid' => $this->propertyid,
                'foliono' => $olddata->folioNo,
                'docid' => $docid,
                'sno' => $olddata->sno + 1,
                'sno1' => $sno1,
                'roomno' => $request->input('roommast' . $sno1),
                'room_rate_before_tax' => $request->input('roomrate' . $sno1),
                'total_rate' => $request->input('plansumrate' . $sno1),
                'pcode' => $request->input('planmaster' . $sno1),
                'noofdays' => $chkplanrow->noofdays,
                'rev_code' => $request->input('rowsrev_code' . $sno1),
                'fixrate' => $request->input('rowdplanfixrate' . $sno1),
                'planper' => $request->input('rowdplan_per' . $sno1),
                'amount' => $postamount,
                'netplanamt' => $request->input('plankaamount' . $sno1),
                'taxinc' => $request->input('taxincplanroomrate' . $sno1),
                'taxstru' => $request->input('rowstax_stru' . $sno1),
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            PlanDetail::insert($plandetails);
        }

        $updateguestfolio = [
            'rodisc' => $request->input('rodisc'),
            'rsdisc' => $request->input('rsdisc'),
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];

        $kotchk = Kot::where('propertyid', $this->propertyid)->where('roomno', $olddata->roomno)->where('pending', 'Y')->first();

        if (!is_null($kotchk)) {
            $uproomkot = [
                'roomno' => $request->input('roommast' . $sno1),
                'u_updatedt' => $this->currenttime,
                'u_ae' => 'e'
            ];
            Kot::where('propertyid', $this->propertyid)->where('roomno', $olddata->roomno)->where('pending', 'Y')
                ->update($uproomkot);
        }

        $updatinexistingrow = [
            'chkoutdate' => $ncurdate,
            'chkouttime' => date('H:i:s'),
            'type' => 'C',
            'newroomno' => $request->input('roommast' . $sno1),
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];
        $oldroomno = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno', $request->input('sno'))->where('sno1', $request->input('sno1'))->first();
        RoomMast::where('propertyid', $this->propertyid)->where('rcode', $oldroomno->roomno)->where('type', 'RO')->where('inclcount', 'Y')
            ->update(['room_stat' => 'D']);

        try {
            RoomOcc::insert($insertnewdata);
            DB::table('guestfolio')->where('propertyid', $this->propertyid)->where('docid', $docid)->update($updateguestfolio);
            RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno', $request->input('sno'))->where('sno1', $request->input('sno1'))->update($updatinexistingrow);
            return redirect('autorefreshmain');
        } catch (Exception $exception) {
            return response()->json(['message' => 'Unable To Change Room' . $exception], 500);
        }
    }

    public function walkinupdate(Request $request)
    {
        // $validate = $request->validate([
        //     'guestname' => 'required',
        // ]);
        // echo $request->input('name');
        $docid = $request->input('docid');

        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('countryguest'))->first();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('cityguest'))->first();
        if (!empty($request->input('issuingcity'))) {
            $issuingcityname = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('issuingcity'))->first();
            $issuingcountryname = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('issuingcountry'))->first();
        }
        $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $request->input('stateguest'))->first();

        $dob = $request->input('birthDate');
        $age = Carbon::parse($dob)->age;

        $profilepicture = $request->input('profileimagehidden');
        $identitypicture = $request->input('identityimagehidden');

        if (!empty($request->file('profileimage'))) {
            $profilepic = $request->file('profileimage');
            $profilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $profilepic->getClientOriginalExtension();
            $folderPathp = 'public/walkin/profileimage';
            Storage::makeDirectory($folderPathp);
            $filePath = Storage::putFileAs($folderPathp, $profilepic, $profilepicture);
            if (!empty($request->input('profileimagehidden'))) {
                if (file_exists('storage/walkin/profileimage' . '/' . $request->input('profileimagehidden'))) {
                    unlink('storage/walkin/profileimage' . '/' . $request->input('profileimagehidden'));
                }
            }
        }

        if (!empty($request->file('identityimage'))) {
            $identitypic = $request->file('identityimage');
            $identitypicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $identitypic->getClientOriginalExtension();
            $folderpathi = 'public/walkin/identityimage';
            Storage::makeDirectory($folderpathi);
            $filePath = Storage::putFileAs($folderpathi, $identitypic, $identitypicture);
            if (!empty($request->input('identityimagehidden'))) {
                if (file_exists('storage/walkin/identityimage' . '/' . $request->input('identityimagehidden'))) {
                    unlink('storage/walkin/identityimage' . '/' . $request->input('identityimagehidden'));
                }
            }
        }

        $signfilename = $request->input('oldsignimage');
        if (!empty($request->input('signimage')) && $signfilename != $request->input('signimage')) {
            $imageData = $request->input('signimage');

            $encodedImage = str_replace('data:image/png;base64,', '', $imageData);
            $decodedImage = base64_decode($encodedImage);

            $signfilename = $request->input('guestmobile') . $request->input('guestname') . 'signature_' . time() . '.png';

            $folder = 'walkin/signature';
            $path = storage_path('app/public/' . $folder . '/' . $signfilename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $decodedImage);
            $oldSignImage = $request->input('oldsignimage');

            if (!empty($oldSignImage) && file_exists('storage/walkin/signature' . '/' . $oldSignImage) && !is_dir('storage/walkin/signature' . '/' . $oldSignImage)) {
                unlink('storage/walkin/signature' . '/' . $oldSignImage);
            }
        }

        $roomoccdata = [
            'propertyid' => $this->propertyid,
            'name' => $request->input('guestname'),
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];

        // echo $request->input('greetingsguest');
        // exit;

        $guestfolio = [
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'name' => $request->input('guestname'),
            'city' => $request->input('cityguest'),
            'purvisit' => $request->input('purpofvisit'),
            'arrfrom' => $request->input('arrfrom'),
            'vehiclenum' => $request->input('vehiclenum'),
            'destination' => $request->input('destination'),
            'travelmode' => $request->input('travelmode'),
            'rodisc' => $request->input('rodisc'),
            'rsdisc' => $request->input('rsdisc'),
            'busssource' => $request->input('bsource'),
        ];

        $guestproft = [
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'name' => $request->input('guestname'),
            'state_code' => $request->input('stateguest'),
            'country_code' => $request->input('countryguest'),
            'city' => $request->input('cityguest'),
            'type' => $countrydata->Type,
            'mobile_no' => $request->input('guestmobile'),
            'email_id' => $request->input('guestemail'),
            'nationality' => $countrydata->nationality,
            'anniversary' => $request->input('weddingAnniversary'),
            'guest_status' => $request->input('vipStatus'),
            'city_name' => $citydata->cityname,
            'state_name' => $statedata->name,
            'country_name' => $countrydata->name,
            'gender' => $request->input('genderguest'),
            'marital_status' => $request->input('marital_status'),
            'zip_code' => $citydata->zipcode,
            'con_prefix' => $request->input('greetingsguest'),
            'dob' => $dob,
            'age' => $age,
            'pic_path' => $profilepicture,
            'guestsign' => $signfilename,
            'id_proof' => $request->input('idType'),
            'idproof_no' => $request->input('idNumber'),
            'issuingcitycode' => $request->input('issuingcity') ?? null,
            'issuingcityname' => $issuingcityname->cityname ?? null,
            'issuingcountrycode' => $request->input('issuingcountry') ?? null,
            'issuingcountryname' => $issuingcountryname->name ?? null,
            'expiryDate' => $request->input('expiryDate'),
            'paymentMethod' => $request->input('paymentMethod'),
            'idpic_path' => $identitypicture,
        ];

        DB::table('roomocc')->where('docid', $docid)->update($roomoccdata);
        DB::table('guestfolio')->where('docid', $docid)->update($guestfolio);
        DB::table('guestprof')->where('docid', $docid)->update($guestproft);


        return redirect('autorefreshmain');
    }


    public function updatewalkin(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'cityname' => 'required',
            'checkindate' => 'required',
            'checkoutdate' => 'required',
            'checkintime' => 'required',
            'checkouttime' => 'required',
        ]);

        // return $request->company;

        // exit;

        $docid = $request->input('docid');
        // return $docid;

        // echo $docid;
        // exit;

        $ncurdate = $this->ncurdate;
        // $currentYear = date('Y', strtotime($ncurdate));
        // $nextYear = $currentYear + 1;
        // if (date('m') < 4) {
        //     $date_from = ($previousYear = $currentYear - 1) . '-04-01';
        //     $date_to = $currentYear . '-03-31';
        //     $currfinancial = $previousYear;
        // } else {
        //     $date_from = $currentYear . '-04-01';
        //     $date_to = $nextYear . '-03-31';
        //     $currfinancial = $currentYear;
        // }
        $vtype = 'CHK';

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $ncurdate)
            ->whereDate('date_to', '>=', $ncurdate)
            ->first();
        $vprefixyr = $chkvpf->prefix;
        // $fdata = DB::table('voucher_prefix')->where('propertyid', $this->propertyid)->where('v_type', $vtype)->first();

        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('country'))->first();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('cityname'))->first();
        if (!empty($request->input('issuingcity'))) {
            $issuingcityname = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('issuingcity'))->first();
            $issuingcountryname = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('issuingcountry'))->first();
        }
        $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $request->input('state'))->first();

        $dob = $request->input('birthDate');
        $age = Carbon::parse($dob)->age;

        $profilepicture = $request->input('profileimagehidden');
        $identitypicture = $request->input('identityimagehidden');
        if (!empty($request->file('profileimage'))) {
            $profilepic = $request->file('profileimage');
            $profilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $profilepic->getClientOriginalExtension();
            $folderPathp = 'public/walkin/profileimage';
            Storage::makeDirectory($folderPathp);
            $filePath = Storage::putFileAs($folderPathp, $profilepic, $profilepicture);
            echo $folderPathp . '/' . $request->input('profileimagehidden');
            if (file_exists('storage/walkin/profileimage' . '/' . $request->input('profileimagehidden'))) {
                unlink('storage/walkin/profileimage' . '/' . $request->input('profileimagehidden'));
            }
        }

        if (!empty($request->file('identityimage'))) {
            $identitypic = $request->file('identityimage');
            $identitypicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $identitypic->getClientOriginalExtension();
            $folderpathi = 'public/walkin/identityimage';
            Storage::makeDirectory($folderpathi);
            $filePath = Storage::putFileAs($folderpathi, $identitypic, $identitypicture);
            if (file_exists('storage/walkin/identityimage' . '/' . $request->input('identityimagehidden'))) {
                unlink('storage/walkin/identityimage' . '/' . $request->input('identityimagehidden'));
            }
        }

        $roomrate = $request->input('rate1');

        if ($request->input('complimentry') == 'on') {
            $complimentry = 'Y';
            $roomrate = 0;
        } else {
            $complimentry = 'N';
        }

        $prefixes = array('cat_code', 'planedit', 'planmaster', 'roommast', 'adult', 'child', 'rate', 'tax_inc');

        $maxsno1 = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->max('sno1');

        $count = 0;
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'cat_code') === 0) {
                $count++;
            }
        }
        PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

        $guestcodep = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        if ($maxsno1 === $count) {
            for ($i = 1; $i <= $count; $i++) {
                $data = [];
                $isEmptyRow = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = $request->input('rate' . $i);

                foreach ($prefixes as $prefix) {
                    $ratenew = 0;
                    if ($request->input('tax_inc' . $i) == 'Y') {
                        foreach ($fetchtaxstru as $taxstru) {
                            $limitstart = $taxstru->limits;
                            $limitend = $taxstru->limit1;
                            $rate = $taxstru->rate;
                            $comp_operator = $taxstru->comp_operator;
                            if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            }
                        }
                        $calcedamttmp = $roomrate * 100 / $fixedrate;
                        $calcedamt = number_format($calcedamttmp, 2);
                    } else {
                        $calcedamt = $roomrate;
                    }
                    $value = $request->input($prefix . $i);

                    $roomoccdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'name' => $request->input('name'),
                        'vprefix' => $vprefixyr,
                        'roomcat' => $request->input('cat_code' . $i),
                        'roomtype' => 'RO',
                        'roomno' => $request->input('roommast' . $i),
                        'ratecode' => 2,
                        'depdate' => $request->input('checkoutdate'),
                        'deptime' => $request->input('checkouttime'),
                        'rrservicechrg' => '',
                        'chngdate' => $request->input('checkindate'),
                        'roomtaxstru' => $rtaxstru,
                        'rackrate' => $request->input('rate' . $i),
                        'roomrate' => str_replace(',', '', $calcedamt),
                        'chkindate' => $request->input('checkindate'),
                        'nodays' => $request->input('stay_days'),
                        'roomcount' => $request->input('rooms') ?? '1',
                        'chkintime' => $request->input('checkintime'),
                        'adult' => $request->input('adult' . $i),
                        'children' => $request->input('child' . $i),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                        'plancode' => $request->input('planmaster' . $i),
                        'rodisc' => $request->input('rodisc'),
                        'rsdisc' => $request->input('rsdisc'),
                        'rrtaxinc' => $request->input('tax_inc' . $i),
                        'leaderyn' => $request->input('leader' . $i) == 'on' ? 'Y' : 'N',
                        'reasonrchange' => ''
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $i,
                        'roomno' => $request->input('roommast' . $i),
                        'room_rate_before_tax' => $request->input('roomrate' . $i),
                        'total_rate' => $request->input('plansumrate' . $i),
                        'pcode' => $request->input('planmaster' . $i),
                        'noofdays' => $request->input('stay_days'),
                        'rev_code' => $request->input('rowsrev_code' . $i),
                        'fixrate' => $request->input('rowdplanfixrate' . $i),
                        'planper' => $request->input('rowdplan_per' . $i),
                        'amount' => $request->input('rowdamount' . $i),
                        'netplanamt' => $request->input('plankaamount' . $i),
                        'taxinc' => $request->input('taxincplanroomrate' . $i),
                        'taxstru' => $request->input('rowstax_stru' . $i),
                        'u_entdt' => $this->currenttime,
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
                    DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', $i)->update($roomoccdata);
                    if ($request->input('planedit' . $i) == 'Y') {
                        PlanDetail::insert($plandetails);
                    }
                }
            }
        } elseif ($maxsno1 < $count) {

            for ($j = 1; $j <= $count; $j++) {
                $datas = [];
                $isEmptyRow2 = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $j))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = $request->input('rate' . $j);
                foreach ($prefixes as $prefix) {
                    $ratenew = 0;
                    if ($request->input('tax_inc' . $j) == 'Y') {
                        foreach ($fetchtaxstru as $taxstru) {
                            $limitstart = $taxstru->limits;
                            $limitend = $taxstru->limit1;
                            $rate = $taxstru->rate;
                            $comp_operator = $taxstru->comp_operator;
                            if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            }
                        }
                        $calcedamttmp = $roomrate * 100 / $fixedrate;
                        $calcedamt = number_format($calcedamttmp, 2);
                    } else {
                        $calcedamt = $roomrate;
                    }
                    $value = $request->input($prefix . $j);

                    $roomoccdata2 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'name' => $request->input('name'),
                        'vprefix' => $vprefixyr,
                        'roomcat' => $request->input('cat_code' . $j),
                        'roomtype' => 'RO',
                        'roomno' => $request->input('roommast' . $j),
                        'ratecode' => 2,
                        'depdate' => $request->input('checkoutdate'),
                        'deptime' => $request->input('checkouttime'),
                        'rrservicechrg' => '',
                        'chngdate' => $request->input('checkindate'),
                        'roomtaxstru' => $rtaxstru,
                        'rackrate' => $request->input('rate' . $j),
                        'roomrate' => str_replace(',', '', $calcedamt),
                        'chkindate' => $request->input('checkindate'),
                        'nodays' => $request->input('stay_days'),
                        'roomcount' => $request->input('rooms') ?? '1',
                        'chkintime' => $request->input('checkintime'),
                        'adult' => $request->input('adult' . $j),
                        'children' => $request->input('child' . $j),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                        'plancode' => $request->input('planmaster' . $j),
                        'rodisc' => $request->input('rodisc'),
                        'rsdisc' => $request->input('rsdisc'),
                        'rrtaxinc' => $request->input('tax_inc' . $j),
                        'leaderyn' => $request->input('leader' . $j) == 'on' ? 'Y' : 'N',
                        'reasonrchange' => ''
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $j,
                        'roomno' => $request->input('roommast' . $j),
                        'room_rate_before_tax' => $request->input('roomrate' . $j),
                        'total_rate' => $request->input('plansumrate' . $j),
                        'pcode' => $request->input('planmaster' . $j),
                        'noofdays' => $request->input('stay_days'),
                        'rev_code' => $request->input('rowsrev_code' . $j),
                        'fixrate' => $request->input('rowdplanfixrate' . $j),
                        'planper' => $request->input('rowdplan_per' . $j),
                        'amount' => $request->input('rowdamount' . $j),
                        'netplanamt' => $request->input('plankaamount' . $j),
                        'taxinc' => $request->input('taxincplanroomrate' . $j),
                        'taxstru' => $request->input('rowstax_stru' . $j),
                        'u_entdt' => $this->currenttime,
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];


                    if (!empty($value)) {
                        $datas[$prefix] = $value;
                        $isEmptyRow2 = false;
                    }
                }

                if (!$isEmptyRow2) {
                    DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', $j)->update($roomoccdata2);
                    if ($request->input('planedit' . $j) == 'Y') {
                        PlanDetail::insert($plandetails);
                    }
                }
            }

            $sno1 = $maxsno1 + 1;
            $fixcount = $count - $maxsno1;
            for ($i = 1; $i <= $fixcount; $i++) {
                $data = [];
                $isEmptyRow = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = $request->input('rate' . $i);
                // This code is like a delicate soufflé: touch it too much, and it collapses.
                foreach ($prefixes as $prefix) {
                    $ratenew = 0;
                    if ($request->input('tax_inc' . $j) == 'Y') {
                        foreach ($fetchtaxstru as $taxstru) {
                            $limitstart = $taxstru->limits;
                            $limitend = $taxstru->limit1;
                            $rate = $taxstru->rate;
                            $comp_operator = $taxstru->comp_operator;
                            if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            }
                        }
                        $calcedamttmp = $roomrate * 100 / $fixedrate;
                        $calcedamt = number_format($calcedamttmp, 2);
                    } else {
                        $calcedamt = $roomrate;
                    }
                    $value = $request->input($prefix . $i);

                    $roomoccdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'name' => $request->input('name'),
                        'sno' => 1,
                        'sno1' => $sno1,
                        'folioNo' => $request->input('folioNo'),
                        'vtype' => $vtype,
                        'vprefix' => $request->input('vprefix'),
                        'guestprof' => $request->input('guestprof'),
                        'roomcat' => $request->input('cat_code' . $sno1),
                        'roomtype' => 'RO',
                        'roomno' => $request->input('roommast' . $sno1),
                        'ratecode' => 2,
                        'depdate' => $request->input('checkoutdate'),
                        'deptime' => $request->input('checkouttime'),
                        'rrservicechrg' => '',
                        'chngdate' => $request->input('checkindate'),
                        'roomtaxstru' => $rtaxstru,
                        'rackrate' => $request->input('rate' . $i),
                        'roomrate' => str_replace(',', '', $calcedamt),
                        'chkindate' => $request->input('checkindate'),
                        'nodays' => $request->input('stay_days'),
                        'roomcount' => $request->input('rooms') ?? '1',
                        'chkintime' => $request->input('checkintime'),
                        'adult' => $request->input('adult' . $sno1),
                        'children' => $request->input('child' . $sno1),
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                        'plancode' => $request->input('planmaster' . $sno1),
                        'rodisc' => $request->input('rodisc'),
                        'rsdisc' => $request->input('rsdisc'),
                        'rrtaxinc' => $request->input('tax_inc' . $sno1),
                        'leaderyn' => $request->input('leader' . $sno1) == 'on' ? 'Y' : 'N',
                        'reasonrchange' => ''
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $i,
                        'roomno' => $request->input('roommast' . $i),
                        'room_rate_before_tax' => $request->input('roomrate' . $i),
                        'total_rate' => $request->input('plansumrate' . $i),
                        'pcode' => $request->input('planmaster' . $i),
                        'noofdays' => $request->input('stay_days'),
                        'rev_code' => $request->input('rowsrev_code' . $i),
                        'fixrate' => $request->input('rowdplanfixrate' . $i),
                        'planper' => $request->input('rowdplan_per' . $i),
                        'amount' => $request->input('rowdamount' . $i),
                        'netplanamt' => $request->input('plankaamount' . $i),
                        'taxinc' => $request->input('taxincplanroomrate' . $i),
                        'taxstru' => $request->input('rowstax_stru' . $i),
                        'u_entdt' => $this->currenttime,
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
                    DB::table('roomocc')->insert($roomoccdata);
                    if ($request->input('planedit' . $i) == 'Y') {
                        PlanDetail::insert($plandetails);
                    }
                }
                $sno1++;
            }
        } elseif ($maxsno1 > $count) {
            DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', '>', $count)->delete();
            PlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', '>', $count)->delete();

            for ($j = 1; $j <= $count; $j++) {
                $datas = [];
                $isEmptyRow2 = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $j))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = $request->input('rate' . $j);

                foreach ($prefixes as $prefix) {
                    $ratenew = 0;
                    if ($request->input('tax_inc' . $j) == 'Y') {
                        foreach ($fetchtaxstru as $taxstru) {
                            $limitstart = $taxstru->limits;
                            $limitend = $taxstru->limit1;
                            $rate = $taxstru->rate;
                            $comp_operator = $taxstru->comp_operator;
                            if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            }
                        }
                        $calcedamttmp = $roomrate * 100 / $fixedrate;
                        $calcedamt = number_format($calcedamttmp, 2);
                    } else {
                        $calcedamt = $roomrate;
                    }
                    $value = $request->input($prefix . $j);

                    $roomoccdata2 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'name' => $request->input('name'),
                        'vprefix' => $vprefixyr,
                        'roomcat' => $request->input('cat_code' . $j),
                        'roomtype' => 'RO',
                        'roomno' => $request->input('roommast' . $j),
                        'ratecode' => 2,
                        'depdate' => $request->input('checkoutdate'),
                        'deptime' => $request->input('checkouttime'),
                        'rrservicechrg' => '',
                        'chngdate' => $request->input('checkindate'),
                        'roomtaxstru' => $rtaxstru,
                        'rackrate' => $request->input('rate' . $j),
                        'roomrate' => str_replace(',', '', $calcedamt),
                        'chkindate' => $request->input('checkindate'),
                        'nodays' => $request->input('stay_days'),
                        'roomcount' => $request->input('rooms') ?? '1',
                        'chkintime' => $request->input('checkintime'),
                        'adult' => $request->input('adult' . $j),
                        'children' => $request->input('child' . $j),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                        'plancode' => $request->input('planmaster' . $j),
                        'rodisc' => $request->input('rodisc'),
                        'rsdisc' => $request->input('rsdisc'),
                        'rrtaxinc' => $request->input('tax_inc' . $j),
                        'leaderyn' => $request->input('leader' . $j) == 'on' ? 'Y' : 'N',
                        'reasonrchange' => ''
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $j,
                        'roomno' => $request->input('roommast' . $j),
                        'room_rate_before_tax' => $request->input('roomrate' . $j),
                        'total_rate' => $request->input('plansumrate' . $j),
                        'pcode' => $request->input('planmaster' . $j),
                        'noofdays' => $request->input('stay_days'),
                        'rev_code' => $request->input('rowsrev_code' . $j),
                        'fixrate' => $request->input('rowdplanfixrate' . $j),
                        'planper' => $request->input('rowdplan_per' . $j),
                        'amount' => $request->input('rowdamount' . $j),
                        'netplanamt' => $request->input('plankaamount' . $j),
                        'taxinc' => $request->input('taxincplanroomrate' . $j),
                        'taxstru' => $request->input('rowstax_stru' . $j),
                        'u_entdt' => $this->currenttime,
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $datas[$prefix] = $value;
                        $isEmptyRow2 = false;
                    }
                }

                if (!$isEmptyRow2) {
                    DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', $j)->update($roomoccdata2);
                    if ($request->input('planedit' . $j) == 'Y') {
                        PlanDetail::insert($plandetails);
                    }
                }
            }

            $sno1 = $maxsno1 + 1;
            $fixcount = $count - $maxsno1;
            for ($i = 1; $i <= $fixcount; $i++) {
                $data = [];
                $isEmptyRow = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                $fetchtaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $rtaxstru)
                    ->get();
                $roomrate = $request->input('rate' . $i);
                // This code is like a delicate soufflé: touch it too much, and it collapses.
                foreach ($prefixes as $prefix) {
                    $ratenew = 0;
                    if ($request->input('tax_inc' . $i) == 'Y') {
                        foreach ($fetchtaxstru as $taxstru) {
                            $limitstart = $taxstru->limits;
                            $limitend = $taxstru->limit1;
                            $rate = $taxstru->rate;
                            $comp_operator = $taxstru->comp_operator;
                            if ($roomrate >= $limitstart && $roomrate <= $limitend) {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            } else if ($roomrate >= $limitstart && $comp_operator != 'Between') {
                                $ratenew += $rate;
                                $fixedrate = $ratenew + 100;
                            }
                        }
                        $calcedamttmp = $roomrate * 100 / $fixedrate;
                        $calcedamt = number_format($calcedamttmp, 2);
                    } else {
                        $calcedamt = $roomrate;
                    }
                    $value = $request->input($prefix . $i);

                    $roomoccdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'name' => $request->input('name'),
                        'sno' => 1,
                        'sno1' => $sno1,
                        'folioNo' => $request->input('folioNo'),
                        'vtype' => $vtype,
                        'vprefix' => $request->input('vprefix'),
                        'guestprof' => $request->input('guestprof'),
                        'roomcat' => $request->input('cat_code' . $sno1),
                        'roomtype' => 'RO',
                        'roomno' => $request->input('roommast' . $sno1),
                        'ratecode' => 2,
                        'depdate' => $request->input('checkoutdate'),
                        'deptime' => $request->input('checkouttime'),
                        'rrservicechrg' => '',
                        'chngdate' => $request->input('checkindate'),
                        'roomtaxstru' => null,
                        'rackrate' => $request->input('rate' . $i),
                        'roomrate' => str_replace(',', '', $calcedamt),
                        'chkindate' => $request->input('checkindate'),
                        'nodays' => $request->input('stay_days'),
                        'roomcount' => $request->input('rooms') ?? '1',
                        'chkintime' => $request->input('checkintime'),
                        'adult' => $request->input('adult' . $sno1),
                        'children' => $request->input('child' . $sno1),
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                        'plancode' => $request->input('planmaster' . $sno1),
                        'rodisc' => $request->input('rodisc'),
                        'rsdisc' => $request->input('rsdisc'),
                        'rrtaxinc' => $request->input('tax_inc' . $sno1),
                        'leaderyn' => $request->input('leader' . $sno1) == 'on' ? 'Y' : 'N',
                        'reasonrchange' => ''
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $i,
                        'roomno' => $request->input('roommast' . $i),
                        'room_rate_before_tax' => $request->input('roomrate' . $i),
                        'total_rate' => $request->input('plansumrate' . $i),
                        'pcode' => $request->input('planmaster' . $i),
                        'noofdays' => $request->input('stay_days'),
                        'rev_code' => $request->input('rowsrev_code' . $i),
                        'fixrate' => $request->input('rowdplanfixrate' . $i),
                        'planper' => $request->input('rowdplan_per' . $i),
                        'amount' => $request->input('rowdamount' . $i),
                        'netplanamt' => $request->input('plankaamount' . $i),
                        'taxinc' => $request->input('taxincplanroomrate' . $i),
                        'taxstru' => $request->input('rowstax_stru' . $i),
                        'u_entdt' => $this->currenttime,
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
                    DB::table('roomocc')->insert($roomoccdata);
                    if ($request->input('planedit' . $i) == 'Y') {
                        PlanDetail::insert($plandetails);
                    }
                }
                $sno1++;
            }
        }

        $guestfolio = [
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'add1' => $request->input('address1'),
            'add2' => $request->input('address2'),
            'booking_source' => $request->input('booking_source') ?? '',
            'travelagent' => $request->input('travel_agent'),
            'name' => $request->input('name'),
            'city' => $request->input('cityname'),
            'nodays' => $request->input('stay_days'),
            'roomcount' => $request->input('rooms') ?? '1',
            'vdate' => $request->input('checkindate'),
            'purvisit' => $request->input('purpofvisit'),
            'company' => $request->input('company'),
            'arrfrom' => $request->input('arrfrom'),
            'vehiclenum' => $request->input('vehiclenum'),
            'destination' => $request->input('destination'),
            'travelmode' => $request->input('travelmode'),
            'rodisc' => $request->input('rodisc'),
            'rsdisc' => $request->input('rsdisc'),
            'busssource' => $request->input('bsource'),
            'depdate' => $request->input('checkoutdate'),
            'remarks' => $request->remarkmain ?? '',
            'pickupdrop' => $request->pickupdrop ?? '',
        ];

        $guestproft = [
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'complimentry' => $complimentry,
            'name' => $request->input('name'),
            'state_code' => $request->input('state'),
            'country_code' => $request->input('country'),
            'add1' => $request->input('address1'),
            'add2' => $request->input('address2'),
            'city' => $request->input('cityname'),
            'type' => $countrydata->Type,
            'mobile_no' => $request->input('mobile'),
            'email_id' => $request->input('email'),
            'nationality' => $countrydata->nationality ?? null,
            'anniversary' => $request->input('weddingAnniversary'),
            'guest_status' => $request->input('vipStatus'),
            'comments1' => null,
            'comments2' => null,
            'comments3' => null,
            'city_name' => $citydata->cityname,
            'state_name' => $statedata->name,
            'country_name' => $countrydata->name,
            'gender' => $request->input('genderguest'),
            'marital_status' => $request->input('marital_status'),
            'zip_code' => $citydata->zipcode,
            'con_prefix' => $request->input('greetingsguest'),
            'dob' => $dob,
            'age' => $age,
            'pic_path' => $profilepicture,
            'id_proof' => $request->input('idType'),
            'idproof_no' => $request->input('idNumber'),
            'issuingcitycode' => $request->input('issuingcity') ?? null,
            'issuingcityname' => $issuingcityname->cityname ?? null,
            'issuingcountrycode' => $request->input('issuingcountry') ?? null,
            'issuingcountryname' => $issuingcountryname->name ?? null,
            'expiryDate' => $request->input('expiryDate'),
            'paymentMethod' => $request->input('paymentMethod'),
            'idpic_path' => $identitypicture,
            'father_name' => null,
            'fom' => 1,
            'pos' => 0,
        ];

        $guestfolioprofdetail = [
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];

        Db::table('guestfolio')->where('propertyid', $this->propertyid)->where('docid', $docid)->update($guestfolio);
        Db::table('guestprof')->where('propertyid', $this->propertyid)->where('guestcode', $guestcodep->guestprof)->update($guestproft);
        Db::table('guestfolioprofdetail')->where('propertyid', $this->propertyid)->where('doc_id', $docid)->update($guestfolioprofdetail);

        $finpay = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        if ($finpay) {
            $updata = [
                'msno1' => $finpay->sno1
            ];
            Paycharge::where('folionodocid', $docid)->where('propertyid', $this->propertyid)->update($updata);
        }

        return response()->json([
            'redirecturl' => 'roomstatus',
            'status' => 'success',
            'message' => 'Walkin Guest Updated successfully!',
        ]);
    }

    public function updatereservation(Request $request)
    {
        $permission = revokeopen(131111);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // $validate = $request->validate([
        //     'name' => 'required',
        //     'cityname' => 'required',
        //     'arrivaldate1' => 'required',
        //     'checkoutdate1' => 'required',
        //     'arrivaltime1' => 'required',
        //     'checkouttime1' => 'required',
        // ]);
        $advdepositcheckbox = $request->input('advdeposit');
        if ($advdepositcheckbox == 'on') {
            $advdeposit = 'Y';
        } else {
            $advdeposit = 'N';
        }
        $docid = $request->input('docid');
        $oldres = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->first();

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
        $vtype = 'RES';

        $fdata = DB::table('voucher_prefix')->where('propertyid', $this->propertyid)->where('v_type', $vtype)->first();

        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('country'))->first();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('cityname'))->first();
        if (!empty($request->input('issuingcity'))) {
            $issuingcityname = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('issuingcity'))->first();
            $issuingcountryname = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('issuingcountry'))->first();
        }
        $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $request->input('state'))->first();

        $dob = $request->input('birthDate');
        $age = Carbon::parse($dob)->age;

        $profilepicture = $request->input('profileimagehidden');
        $identitypicture = $request->input('identityimagehidden');

        if (!empty($request->file('profileimage'))) {
            $profilepic = $request->file('profileimage');
            $profilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $profilepic->getClientOriginalExtension();
            $folderPathp = 'public/walkin/reservationprofilepic';
            Storage::makeDirectory($folderPathp);
            $filePath = Storage::putFileAs($folderPathp, $profilepic, $profilepicture);
            if (file_exists($folderPathp . '/' . $request->input('profileimagehidden'))) {
                unlink($folderPathp . '/' . $request->input('profileimagehidden'));
            }
        }

        if (!empty($request->file('identityimage'))) {
            $identitypic = $request->file('identityimage');
            $identitypicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $identitypic->getClientOriginalExtension();
            $folderpathi = 'public/walkin/reservationidentitypic';
            Storage::makeDirectory($folderpathi);
            $filePath = Storage::putFileAs($folderpathi, $identitypic, $identitypicture);
            if (file_exists($folderpathi . '/' . $request->input('identityimagehidden'))) {
                unlink($folderpathi . '/' . $request->input('identityimagehidden'));
            }
        }

        if ($request->input('complimentry') == 'on') {
            $complimentry = 'Y';
            $roomrate = 0;
        } else {
            $complimentry = 'N';
        }

        $prefixes = array('cat_code', 'planedit', 'planmaster', 'roomcount', 'roommast', 'adult', 'child', 'rate', 'tax_inc');

        $maxsno1 = DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->max('Sno');

        $count = 0;
        $p = $count;
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'cat_code') === 0) {
                $count++;
            }
        }
        $sns = $request->input('sns');

        if (!empty($sns) && is_string($sns)) {
            $sns = explode(',', $sns);
            DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->whereIn('Sno', $sns)->delete();
            $fetchedgrp = GrpBookinDetail::where('Property_ID', $this->propertyid)
                ->where('BookingDocid', $docid)
                ->orderBy('RoomNo', 'ASC')
                ->orderBy('Plan_Code', 'ASC')
                ->orderBy('sn', 'ASC')
                ->get();

            $counter = 1;
            foreach ($fetchedgrp as $grp) {
                $grp->update(['Sno' => $counter]);
                $counter++;
            }
        }

        // echo $maxsno1;
        // var_dump($sns);
        // exit;

        BookinPlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

        if ($maxsno1 == $count) {
            for ($i = 1; $i <= $count; $i++) {
                $data = [];
                $isEmptyRow = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                foreach ($prefixes as $prefix) {
                    $value = $request->input($prefix . $i);
                    $grpbookingdetails = [
                        'GuestName' => $request->input('name'),
                        'RoomCat' => $request->input('cat_code' . $i),
                        'RoomNo' => $request->input('roommast' . $i),
                        'RateCode' => 2,
                        'NoDays' => $request->input('stay_days' . $i),
                        'DepDate' => $request->input('checkoutdate' . $i),
                        'DepTime' => $request->input('checkouttime' . $i),
                        'RoomTaxStru' => $rtaxstru,
                        'Tarrif' => $request->input('rate' . $i),
                        'ArrDate' => $request->input('arrivaldate' . $i),
                        'ArrTime' => $request->input('arrivaltime' . $i),
                        'Adults' => $request->input('adult' . $i),
                        'Childs' => $request->input('child' . $i),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'U_AE' => 'e',
                        'Plan_Code' => $request->input('planmaster' . $i) ?? '',
                        'IncTax' => $request->input('tax_inc' . $i),
                        'ContraDocId' => '',
                        'ContraSno' => '',
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $i,
                        'roomno' => $request->input('roommast' . $i) ?? '0',
                        'room_rate_before_tax' => $request->input('roomrate' . $i) ?? '0',
                        'total_rate' => $request->input('plansumrate' . $i),
                        'pcode' => $request->input('planmaster' . $i),
                        'noofdays' => $request->input('stay_days' . $i),
                        'rev_code' => $request->input('rowsrev_code' . $i) ?? '',
                        'fixrate' => $request->input('rowdplanfixrate' . $i),
                        'planper' => $request->input('rowdplan_per' . $i),
                        'amount' => $request->input('rowdamount' . $i),
                        'netplanamt' => $request->input('plankaamount' . $i),
                        'taxinc' => $request->input('taxincplanroomrate' . $i),
                        'taxstru' => $request->input('rowstax_stru' . $i),
                        'u_entdt' => $this->currenttime,
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
                    DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->where('Sno', $i)->update($grpbookingdetails);
                    if ($request->input('planedit' . $i) == 'Y') {
                        BookinPlanDetail::insert($plandetails);
                    }
                }
            }
        } elseif ($maxsno1 < $count) {

            for ($j = 1; $j <= $count; $j++) {
                $datas = [];
                $isEmptyRow2 = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $j))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');

                foreach ($prefixes as $prefix) {
                    $value = $request->input($prefix . $j);

                    $grpbookingdetails2 = [
                        'GuestName' => $request->input('name'),
                        'RoomCat' => $request->input('cat_code' . $j),
                        'RoomNo' => $request->input('roommast' . $j),
                        'RateCode' => 2,
                        'NoDays' => $request->input('stay_days' . $j),
                        'DepDate' => $request->input('checkoutdate' . $j),
                        'DepTime' => $request->input('checkouttime' . $j),
                        'RoomTaxStru' => $rtaxstru,
                        'Tarrif' => $request->input('rate' . $j),
                        'ArrDate' => $request->input('arrivaldate' . $j),
                        'ArrTime' => $request->input('arrivaltime' . $j),
                        'Adults' => $request->input('adult' . $j),
                        'Childs' => $request->input('child' . $j),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'U_AE' => 'e',
                        'Plan_Code' => $request->input('planmaster' . $j) ?? '',
                        'IncTax' => $request->input('tax_inc' . $j),
                        'ContraDocId' => '',
                        'ContraSno' => '',
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $j,
                        'roomno' => $request->input('roommast' . $j),
                        'room_rate_before_tax' => $request->input('roomrate' . $j),
                        'total_rate' => $request->input('plansumrate' . $j),
                        'pcode' => $request->input('planmaster' . $j),
                        'noofdays' => $request->input('stay_days' . $j),
                        'rev_code' => $request->input('rowsrev_code' . $j),
                        'fixrate' => $request->input('rowdplanfixrate' . $j),
                        'planper' => $request->input('rowdplan_per' . $j),
                        'amount' => $request->input('rowdamount' . $j),
                        'netplanamt' => $request->input('plankaamount' . $j),
                        'taxinc' => $request->input('taxincplanroomrate' . $j),
                        'taxstru' => $request->input('rowstax_stru' . $j),
                        'u_entdt' => $this->currenttime,
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $datas[$prefix] = $value;
                        $isEmptyRow2 = false;
                    }
                }

                if (!$isEmptyRow2) {
                    DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->where('Sno', $j)->update($grpbookingdetails2);
                    if ($request->input('planedit' . $j) == 'Y') {
                        BookinPlanDetail::insert($plandetails);
                    }
                }
            }

            $sno1 = $maxsno1 + 1;
            $fixcount = $count - $maxsno1;
            for ($i = 1; $i <= $fixcount; $i++) {
                $data = [];
                $isEmptyRow = true;
                // This code is like a delicate soufflé: touch it too much, and it collapses.
                foreach ($prefixes as $prefix) {
                    $value = $request->input($prefix . $i);

                    $grpbookingdetails = [
                        'Property_ID' => $this->propertyid,
                        'BookingDocid' => $docid,
                        'Sno' => $sno1,
                        'RoomDet' => '1',
                        'Cancel' => 'N',
                        'Bookno' => $request->input('folioNo'),
                        'Guestprof' => $request->input('guestprof'),
                        'GuestName' => $request->input('name'),
                        'RoomCat' => $request->input('cat_code' . $i),
                        'RoomNo' => $request->input('roommast' . $i),
                        'RateCode' => 2,
                        'NoDays' => $request->input('stay_days' . $i),
                        'DepDate' => $request->input('checkoutdate' . $i),
                        'DepTime' => $request->input('checkouttime' . $i),
                        'RoomTaxStru' => $rtaxstru,
                        'Tarrif' => $request->input('rate' . $i),
                        'ArrDate' => $request->input('arrivaldate' . $i),
                        'ArrTime' => $request->input('arrivaltime' . $i),
                        'Adults' => $request->input('adult' . $i),
                        'Childs' => $request->input('child' . $i),
                        'U_EntDt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'U_AE' => 'a',
                        'Plan_Code' => $request->input('planmaster' . $i) ?? '',
                        'IncTax' => $request->input('tax_inc' . $i),
                        'ContraDocId' => '',
                        'ContraSno' => '',
                    ];

                    $plandetails = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $i,
                        'roomno' => $request->input('roommast' . $i),
                        'room_rate_before_tax' => $request->input('roomrate' . $i),
                        'total_rate' => $request->input('plansumrate' . $i),
                        'pcode' => $request->input('planmaster' . $i),
                        'noofdays' => $request->input('stay_days' . $i),
                        'rev_code' => $request->input('rowsrev_code' . $i),
                        'fixrate' => $request->input('rowdplanfixrate' . $i),
                        'planper' => $request->input('rowdplan_per' . $i),
                        'amount' => $request->input('rowdamount' . $i),
                        'netplanamt' => $request->input('plankaamount' . $i),
                        'taxinc' => $request->input('taxincplanroomrate' . $i),
                        'taxstru' => $request->input('rowstax_stru' . $i),
                        'u_entdt' => $this->currenttime,
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
                    DB::table('grpbookingdetails')->insert($grpbookingdetails);
                    if ($request->input('planedit' . $i) == 'Y') {
                        BookinPlanDetail::insert($plandetails);
                    }
                }
                $sno1++;
            }
        } elseif ($maxsno1 > $count) {

            for ($j = 1; $j <= $count; $j++) {
                $datas = [];
                $isEmptyRow2 = true;
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $j))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');
                foreach ($prefixes as $prefix) {
                    $value = $request->input($prefix . $j);

                    $grpbookingdetails2 = [
                        'GuestName' => $request->input('name'),
                        'RoomCat' => $request->input('cat_code' . $j),
                        'RoomNo' => $request->input('roommast' . $j) ?? '',
                        'RateCode' => 2,
                        'NoDays' => $request->input('stay_days' . $j),
                        'DepDate' => $request->input('checkoutdate' . $j),
                        'DepTime' => $request->input('checkouttime' . $j),
                        'RoomTaxStru' => $rtaxstru,
                        'Tarrif' => $request->input('rate' . $j),
                        'ArrDate' => $request->input('arrivaldate' . $j),
                        'ArrTime' => $request->input('arrivaltime' . $j),
                        'Adults' => $request->input('adult' . $j),
                        'Childs' => $request->input('child' . $j),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'U_AE' => 'e',
                        'Plan_Code' => $request->input('planmaster' . $j) ?? '',
                        'IncTax' => $request->input('tax_inc' . $j),
                        'ContraDocId' => '',
                        'ContraSno' => '',
                    ];

                    $plandetailsb = [
                        'propertyid' => $this->propertyid,
                        'foliono' => $request->input('folioNo'),
                        'docid' => $docid,
                        'sno' => 1,
                        'sno1' => $j,
                        'roomno' => $request->input('roommast' . $j) ?? '',
                        'room_rate_before_tax' => $request->input('roomrate' . $j),
                        'total_rate' => $request->input('plansumrate' . $j),
                        'pcode' => $request->input('planmaster' . $j),
                        'noofdays' => $request->input('stay_days' . $j),
                        'rev_code' => $request->input('rowsrev_code' . $j),
                        'fixrate' => $request->input('rowdplanfixrate' . $j),
                        'planper' => $request->input('rowdplan_per' . $j),
                        'amount' => $request->input('rowdamount' . $j),
                        'netplanamt' => $request->input('plankaamount' . $j),
                        'taxinc' => $request->input('taxincplanroomrate' . $j),
                        'taxstru' => $request->input('rowstax_stru' . $j),
                        'u_entdt' => $this->currenttime,
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'e',
                    ];

                    if (!empty($value)) {
                        $datas[$prefix] = $value;
                        $isEmptyRow2 = false;
                    }
                }

                if (!$isEmptyRow2) {
                    // echo $j . '-' . $request->input('planedit' . $j) . '-' . $request->input('cat_code' . $j) . '-' . $this->currenttime . '</br>';
                    DB::table('grpbookingdetails')->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->where('Sno', $j)->update($grpbookingdetails2);
                    if ($request->input('planedit' . $j) == 'Y') {
                        BookinPlanDetail::insert($plandetailsb);
                    }
                }
            }

            $sno1 = $maxsno1 + 1;
            $fixcount = $count - $maxsno1;
            for ($i = 1; $i <= $fixcount; $i++) {
                $data = [];
                $isEmptyRow = true;
                // This code is like a delicate soufflé: touch it too much, and it collapses.
                foreach ($prefixes as $prefix) {
                    $value = $request->input($prefix . $i);

                    $grpbookingdetails = [
                        'GuestName' => $request->input('name'),
                        'RoomCat' => $request->input('cat_code' . $i),
                        'RoomNo' => $request->input('roommast' . $i),
                        'RateCode' => 2,
                        'NoDays' => $request->input('stay_days' . $i),
                        'DepDate' => $request->input('checkoutdate' . $i),
                        'DepTime' => $request->input('checkouttime' . $i),
                        'RoomTaxStru' => $rtaxstru,
                        'Tarrif' => $request->input('rate' . $i),
                        'ArrDate' => $request->input('arrivaldate' . $i),
                        'ArrTime' => $request->input('arrivaltime' . $i),
                        'Adults' => $request->input('adult' . $i),
                        'Childs' => $request->input('child' . $i),
                        'u_updatedt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'U_AE' => 'e',
                        'Plan_Code' => $request->input('planmaster' . $i) ?? '',
                        'IncTax' => $request->input('tax_inc' . $i),
                        'ContraDocId' => '',
                        'ContraSno' => '',
                    ];
                    if (!empty($value)) {
                        $data[$prefix] = $value;
                        $isEmptyRow = false;
                    }
                }

                if (!$isEmptyRow) {
                    DB::table('grpbookingdetails')->insert($grpbookingdetails);
                }
                $sno1++;
            }
        }

        $incount = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->count();

        $bookingdata = [
            'GuestName' => $request->input('name') ?? '',
            'Vtype' => $vtype,
            // 'vdate' => $ncurdate,
            'Remarks' => $request->input('remarkmain') ?? '',
            'pickupdrop' => $request->pickupdrop ?? '',
            'advdeposit' => $advdeposit,
            'vehiclenum' => $request->input('vehiclenum') ?? '',
            'TravelAgency' => $request->input('travel_agent') ?? '',
            'purpofvisit' => $request->input('purposeofvisit') ?? '',
            'BussSource' => $request->input('bsource') ?? '',
            'MarketSeg' => $request->input('booking_source') ?? '',
            'RRServiceChrg' => '',
            'BookedBy' => $request->input('booked_by') ?? '',
            'ResStatus' => $request->input('reservation_status') ?? '',
            'ResMode' => '',
            'TravelMode' => $request->input('travelmode') ?? '',
            'CancelDate' => null,
            'Cancel' => 'N',
            'Company' => $request->input('company') ?? '',
            'ArrFrom' => $request->input('arrfrom') ?? '',
            'Destination' => $request->input('destination') ?? '',
            'u_updatedt' => $this->currenttime,
            'U_Name' => Auth::user()->u_name,
            'U_AE' => 'e',
            'NoofRooms' => $incount,
            'Authorization' => '',
            'Verified' => '',
            'CancelUName' => '',
            'MobNo' => $request->input('mobile') ?? '',
            'Email' => $request->input('email') ?? '',
            'RRTaxInc' => $request->input('tax_inc1') ?? '',
            'RDisc' => $request->input('rodisc') ?? '0',
            'RSDisc' => $request->input('rsdisc') ?? '0',
            'AdvDueDate' => null,
            'RefCode' => '',
            'RefBookNo' => $request->input('ref_booking_id') ?? ''
        ];

        $guestproft = [
            'propertyid' => $this->propertyid,
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'complimentry' => $complimentry,
            'name' => $request->input('name'),
            'state_code' => $request->input('state'),
            'country_code' => $request->input('country'),
            'add1' => $request->input('address1'),
            'add2' => $request->input('address2'),
            'city' => $request->input('cityname'),
            'type' => $countrydata->Type,
            'mobile_no' => $request->input('mobile'),
            'email_id' => $request->input('email'),
            'nationality' => $countrydata->nationality ?? null,
            'anniversary' => $request->input('weddingAnniversary'),
            'guest_status' => $request->input('vipStatus'),
            'comments1' => null,
            'comments2' => null,
            'comments3' => null,
            'city_name' => $citydata->cityname,
            'state_name' => $statedata->name,
            'country_name' => $countrydata->name,
            'gender' => $request->input('genderguest'),
            'marital_status' => $request->input('marital_status'),
            'zip_code' => $citydata->zipcode,
            'con_prefix' => $request->input('greetings'),
            'dob' => $dob,
            'age' => $age,
            'pic_path' => $profilepicture,
            'id_proof' => $request->input('idType'),
            'idproof_no' => $request->input('idNumber'),
            'issuingcitycode' => $request->input('issuingcity') ?? null,
            'issuingcityname' => $issuingcityname->cityname ?? null,
            'issuingcountrycode' => $request->input('issuingcountry') ?? null,
            'issuingcountryname' => $issuingcountryname->name ?? null,
            'expiryDate' => $request->input('expiryDate'),
            'paymentMethod' => $request->input('paymentMethod'),
            'idpic_path' => $identitypicture,
            'father_name' => null,
            'fom' => 1,
            'pos' => 0,
        ];

        Db::table('booking')->where('Property_ID', $this->propertyid)->where('DocId', $docid)->update($bookingdata);
        Db::table('guestprof')->where('propertyid', $this->propertyid)->where('docid', $docid)->update($guestproft);

        // exit;

        // if ($advdepositcheckbox == 'on') {
        //     $coded = base64_encode($docid);
        //     return response()->json([
        //         'redirecturl' => 'advancedeposit?docid=' . $coded,
        //         'status' => 'success',
        //         'message' => 'Reservation Updated successfully!',
        //     ]);
        // } else {
        //     return response()->json([
        //         'redirecturl' => 'reservationlist',
        //         'status' => 'success',
        //         'message' => 'Reservation Updated successfully!',
        //     ]);
        // }

        if ($advdepositcheckbox == 'on') {
            $coded = base64_encode($docid);
            return redirect('advancedeposit?docid=' . $coded)
                ->with('status', 'success')
                ->with('message', 'Reservation Updated successfully!');
        } else {
            return redirect('reservationlist')
                ->with('status', 'success')
                ->with('message', 'Reservation Updated successfully!');
        }
    }


    public function getoutletlist(Request $request)
    {
        $data = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->whereIn('rest_type', ['Outlet', 'ROOM SERVICE'])
            ->get();

        return json_encode($data);
    }

    public function salebillentry(Request $request)
    {
        //$permission = revokeopen(151411);
        //if (is_null($permission) || $permission->view == 0) { 
        //    return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        // }
        $dcode = $request->query('dcode');
        $roomnoone = $request->query('roomno');
        $departdata = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();

        if ($departdata) {

            if ($departdata) {
                $associatedrestcode = Depart1::where('propertyid', $this->propertyid)
                    ->where('departcode', $departdata->dcode)
                    ->pluck('associatedrestcode')
                    ->toArray();

                $restcodes = array_merge([$departdata->dcode], $associatedrestcode);
            }

            $outletname = Depart::where('propertyid', $this->propertyid)->whereIn('dcode', $restcodes)->orderByDesc('dcode')->get();

            $menugroup = DB::table('itemgrp')
                ->where('property_id', $this->propertyid)
                ->whereIn('restcode', $restcodes)
                ->where('activeyn', 'Y')
                ->orderBy('name', 'ASC')
                ->get();

            $short_name = $departdata->short_name;

            if (strtolower($departdata->nature) == 'room service') {
                if ($departdata->kot_yn == 'N') {
                    $roomno = DB::table('roomocc')
                        ->leftJoin('paycharge', function ($join) use ($short_name) {
                            $join->on('paycharge.roomno', '=', 'roomocc.roomno')
                                ->on('paycharge.sno1', '=', 'roomocc.sno1')
                                ->where(function ($query) use ($short_name) {
                                    $query->where('paycharge.vtype', '=', 'B' . $short_name)
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
                    $label = 'Room No';
                    $roomno = DB::table('kot')->where('propertyid', $this->propertyid)->where('restcode', $dcode)->where('voidyn', 'N')
                        ->where('pending', 'Y')->where('nckot', 'N')->groupBy('roomno')->get();
                }
            } else {
                $label = 'Table No';
                $kotyn = Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->where('nature', 'Outlet')->first();

                if ($kotyn->kot_yn == 'Y') {
                    $roomno = DB::table('kot')->where('propertyid', $this->propertyid)->whereIn('restcode', $restcodes)->where('voidyn', 'N')
                        ->where('pending', 'Y')->where('nckot', 'N')->groupBy('roomno')->get();
                } else {
                    $roomno = RoomMast::select('rcode as roomno')->whereIn('rest_code', $restcodes)->where('propertyid', $this->propertyid)->where('type', 'TB')->orderBy('rcode', 'ASC')->get();
                }
            }
        }

        $nctype = DB::table('nctype_mast')->where('propertyid', $this->propertyid)->get();
        $server_mast = DB::table('server_mast')->where('propertyid', $this->propertyid)->get();
        $outletdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Outlet', 'ROOM SERVICE'])->get();

        $sundrytype1 = DB::table('sundrytype')
            ->where('propertyid', $this->propertyid)
            ->where('vtype', $restcodes[0])
            ->orderBy('sno', 'ASC')
            ->get();

        // return $sundrytype1;

        $sundrytype2 = [];
        if (count($restcodes) > 1) {
            $sundrytype2 = DB::table('sundrytype')
                ->where('propertyid', $this->propertyid)
                ->where('vtype', $restcodes[1])
                ->orderBy('sno', 'ASC')
                ->get();
        }

        $sundrycount = count($sundrytype1);

        $superwiser = Auth::user()->superwiser;

        $query = ($superwiser == '1') ? null : $this->ncurdate;

        $oldroomno = Sale1::select('sale1.waiter', 'stock.*', 'itemmast.Name as itemname', 'server_mast.name as waitername')
            ->leftJoin('stock', 'stock.docid', '=', 'sale1.docid')
            ->leftJoin('itemmast', 'itemmast.Code', '=', 'stock.item')
            ->leftJoin('server_mast', 'server_mast.scode', '=', 'sale1.waiter')
            ->where('stock.propertyid', $this->propertyid)
            ->where('stock.restcode', $dcode)
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('sale1.vdate', $query);
            })
            ->groupBy('stock.vno', 'stock.vdate')
            ->orderBy('stock.vdate', 'DESC')
            ->orderBy('stock.vno', 'DESC')
            ->get();

        // return $dcode;

        $company = SubGroup::where('propertyid', $this->propertyid)->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name')->groupBy('sub_code')->get();
        $envpos = EnviroPos::where('propertyid', $this->propertyid)->first();
        $curusername = $this->username;
        $adminuname = Companyreg::where('propertyid', $this->propertyid)->orderBy('sn', 'ASC')->first();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)
            ->orderBy('cityname', 'ASC')->get();

        $printsetup = PrintingSetup::where('propertyid', $this->propertyid)->where('restcode', $departdata->dcode)->where('module', 'POS')->first();

        if (!isset($printsetup)) {
            return back()->with('error', 'Please Fill Printing Setup First');
        }

        return view('property.salebillentry', [
            'outletname' => $outletname,
            'menudata' => $menugroup,
            'roomno' => $roomno,
            'oldroomno' => $oldroomno,
            'sundrycount' => $sundrycount,
            'depart' => $departdata,
            'nctype' => $nctype,
            'servermast' => $server_mast,
            'outletdata' => $outletdata,
            'label' => $label,
            'sundrytype1' => $sundrytype1,
            'sundrytype2' => $sundrytype2,
            'company' => $company,
            'roomnoone' => $roomnoone,
            'envpos' => $envpos,
            'curusername' => $curusername,
            'adminuname' => $adminuname,
            'citydata' => $citydata,
            'printsetup' =>  $printsetup
        ]);
    }

    public function tablebooking(Request $request)
    {
        echo "<h1 style='text-align:center;color:red;'>" . $request->query('dcode') . "</h1>";
    }

    public function paymentreceived(Request $request)
    {
        echo "<h1 style='text-align:center;color:red;'>" . $request->query('dcode') . "</h1>";
    }
    public function splitbill(Request $request)
    {
        $permission = revokeopen(151421);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        echo "<h1 style='text-align:center;color:red;'>" . $request->query('dcode') . "</h1>";
    }
    public function orderbooking(Request $request)
    {
        echo "<h1 style='text-align:center;color:red;'>" . $request->query('dcode') . "</h1>";
    }
    public function orderbookingadvance(Request $request)
    {
        echo "<h1 style='text-align:center;color:red;'>" . $request->query('dcode') . "</h1>";
    }

    public function posdiplayhandle(Request $request)
    {
        //  $permission = revokeopen(172014);
        // if (is_null($permission) || $permission->ins == 0) {
        //     return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        // }
        $posdispcatdata = [
            'occupied' => $request->input('occupied'),
            'vacant' => $request->input('vacant'),
            'billed' => $request->input('billed'),
        ];

        $updata = [
            'occupied' => $request->input('occupied'),
            'vacant' => $request->input('vacant'),
            'billed' => $request->input('billed')
        ];
        $dcode = $request->input('dcode');

        Depart::where('propertyid', $this->propertyid)->where('dcode', $dcode)->update($updata);

        DB::table('posdispcat')->where('propertyid', $this->propertyid)->update($posdispcatdata);

        return 'success';
    }

    public function fetchitemnames(Request $request)
    {
        $grpid = $request->input('grpid');

        $nameinput = $request->input('name');
        $barcodeinput = $request->input('barcodeinput');
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

        $query = ItemMast::select('itemmast.*', 'itemrate.Rate as rateofitem')
            ->leftJoin('itemrate', function ($join) use ($restcodes) {
                $join->on('itemrate.ItemCode', '=', 'itemmast.Code')
                    ->whereIn('itemrate.restcode', $restcodes);
            })
            ->where('itemmast.Property_ID', $this->propertyid)->where('itemmast.ActiveYN', 'Y')
            ->whereIn('itemmast.RestCode', $restcodes)->orderBy('itemmast.Name', 'ASC');

        if ($grpid != 'favourite' && !empty($grpid)) {
            $query->where('ItemGroup', $grpid);
        } elseif ($grpid == 'favourite') {
            $query->where('favourite', '1');
        } elseif (!empty($nameinput)) {
            $query->where('Name', 'like', "%$nameinput%");
        } elseif (!empty($barcodeinput)) {
            $query->where('DispCode', $barcodeinput);
        }

        $data = $query->get();
        return response()->json($data);
    }

    public function fetchmenunames(Request $request)
    {
        $dcode = $request->input('dcode');
        $data = DB::table('itemgrp')->where('property_id', $this->propertyid)->where('restcode', $dcode)->orderBy('name', 'ASC')->get();
        return json_encode($data);
    }

    public function departnamefetch(Request $request)
    {
        $dcode = $request->input('dcode');
        $data = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $dcode)->first();
        return json_encode($data);
    }

    public function guestdtfetch(Request $request)
    {
        $roomno = $request->input('roomno');
        $addeddocid = $request->input('addeddocid') ?? '';
        // $rdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('roomno', $roomno)->whereNull('type')->first();
        $rdata = Roomocc::select(
            'roomocc.roomno',
            'roomocc.docid',
            'roomocc.name',
            'guestfolio.city AS guestcitycode',
            'guestcities.cityname AS guestcityname',
            'guestfolio.add1',
            'guestfolio.add2',
            'guestprof.mobile_no AS guestmobile',
            'roomocc.adult',
            'guestfolio.company',
            'sgrp.name as companyname',
            'sgrp.gstin',
            'sgrp.citycode AS compcitycode',
            'sgrpcities.cityname AS compcityname',
            'sgrpcities.state AS compstatecode',
            'states.name AS compstatename',
            'roomocc.plancode'
        )
            ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('guestprof', 'guestprof.docid', '=', 'roomocc.docid')
            ->leftJoin('subgroup AS sgrp', 'sgrp.sub_code', '=', 'guestfolio.company')
            ->leftJoin('cities AS sgrpcities', 'sgrpcities.city_code', '=', 'sgrp.citycode')
            ->leftJoin('cities AS guestcities', 'guestcities.city_code', '=', 'guestfolio.city')
            ->leftJoin('states', 'states.state_code', '=', 'sgrpcities.state')
            ->where('roomocc.roomno', $roomno)
            ->where('roomocc.docid', $addeddocid)
            ->where('roomocc.propertyid', $this->propertyid)
            // ->whereNull('roomocc.type')
            ->first();

        $pax = 1;
        if ($rdata) {
            $planname = 'EP';
            $pax = $rdata->adult;
            if (!empty($rdata->plancode)) {
                $planname = DB::table('plan_mast')->where('propertyid', $this->propertyid)->where('pcode', $rdata->plancode)->value('name');
            }
            $concat = 'Name: ' . $rdata->name . ', Plan: ' . $planname;
        } else {
            $concat = '';
        }


        $data = [
            'concat' => $concat,
            'pax' => $pax,
            'guestdetails' => $rdata
        ];

        return response()->json($data);
    }

    public function guestdtfetchkot(Request $request)
    {
        $roomno = $request->input('roomno');
        $rdata = Roomocc::select(
            'roomocc.roomno',
            'roomocc.docid',
            'roomocc.name',
            'guestfolio.city AS guestcitycode',
            'guestcities.cityname AS guestcityname',
            'guestfolio.add1',
            'guestfolio.add2',
            'guestprof.mobile_no AS guestmobile',
            'roomocc.adult',
            'guestfolio.company',
            'sgrp.name as companyname',
            'sgrp.gstin',
            'sgrp.citycode AS compcitycode',
            'sgrpcities.cityname AS compcityname',
            'sgrpcities.state AS compstatecode',
            'states.name AS compstatename',
            'roomocc.plancode',
            'guestfolio.remarks',
            'guestfolio.pickupdrop'
        )
            ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('guestprof', 'guestprof.docid', '=', 'roomocc.docid')
            ->leftJoin('subgroup AS sgrp', 'sgrp.sub_code', '=', 'guestfolio.company')
            ->leftJoin('cities AS sgrpcities', 'sgrpcities.city_code', '=', 'sgrp.citycode')
            ->leftJoin('cities AS guestcities', 'guestcities.city_code', '=', 'guestfolio.city')
            ->leftJoin('states', 'states.state_code', '=', 'sgrpcities.state')
            ->where('roomocc.roomno', $roomno)
            ->where('roomocc.propertyid', $this->propertyid)
            ->whereNull('roomocc.type')
            ->first();

        $pax = 1;
        if ($rdata) {
            $planname = 'EP';
            $pax = $rdata->adult;
            if (!empty($rdata->plancode)) {
                $planname = DB::table('plan_mast')->where('propertyid', $this->propertyid)->where('pcode', $rdata->plancode)->value('name');
            }
            $concat = 'Name: ' . $rdata->name . ', Plan: ' . $planname . ($rdata->remarks != '' ? ', Remarks: ' . $rdata->remarks : '');
        } else {
            $concat = '';
        }


        $data = [
            'concat' => $concat,
            'pax' => $pax,
            'guestdetails' => $rdata
        ];

        return response()->json($data);
    }

    public function fetchitemdetails(Request $request)
    {
        $itemcode = $request->input('itemcode');
        $itemrestcode = $request->input('itemrestcode');
        $data = DB::table('itemmast')
            ->select(
                'itemmast.*',
                'itemrate.Rate',
                'unitmast.name as unitname',
                DB::raw('COALESCE(taxstru.tax_code, "") AS tax_code'),
                DB::raw('COALESCE(taxstru.tax_name, "") AS tax_name'),
                DB::raw('COALESCE(taxstru.tax_rate, 0) AS tax_rate'),
                'itemcatmast.TaxStru'
            )
            ->join('itemrate', 'itemrate.ItemCode', '=', 'itemmast.Code')
            ->join('unitmast', 'unitmast.ucode', '=', 'itemmast.Unit')
            ->leftJoin('itemcatmast', 'itemcatmast.Code', '=', 'itemmast.ItemCatCode')
            ->leftJoin(DB::raw('(SELECT str_code, GROUP_CONCAT(name) AS tax_name, GROUP_CONCAT(tax_code) AS tax_code, SUM(rate) AS tax_rate FROM taxstru GROUP BY str_code) AS taxstru'), 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.Code', $itemcode)
            ->where('itemmast.RestCode', $itemrestcode)
            ->first();
        return json_encode($data);
    }

    public function fetchitempreviousnc(Request $request)
    {
        $docid = $request->docid;
        $chkmerged = KotModal::where('docid', $docid)->where('propertyid', $this->propertyid)->value('mergedwith');
        $short_name = Depart::where('propertyid', $this->propertyid)->where('dcode', $request->dcode)->value('short_name');

        $datatmp = DB::table('itemmast')
            // ->leftJoin('kot', 'kot.item', '=', 'itemmast.Code')
            ->join('kot', function ($join) {
                $join->on('itemmast.Code', '=', 'kot.item')
                    ->on('itemmast.RestCode', '=', 'kot.restcode');
            })
            ->select('itemmast.Name', 'kot.description', 'kot.qty', 'kot.rate', 'kot.voidyn', 'kot.item', 'kot.vno', 'kot.sno', 'kot.roomno', 'kot.waiter', 'kot.remarks', 'kot.pax', 'kot.docid', 'kot.vtype', 'kot.nctype')
            ->where('kot.propertyid', $this->propertyid)
            // ->where('vtype', 'N' . $short_name)
            ->orderBy('kot.sno');

        if (!empty($chkmerged)) {
            $mergedocid = $chkmerged;
            $data = $datatmp->whereIn('kot.docid', explode(',', $mergedocid))->get();
        } else {
            $data = $datatmp->where('kot.docid', $docid)->get();
        }

        return json_encode($data);
    }

    public function fetchitemroomchange(Request $request)
    {

        $roomno = $request->input('roomno');
        $dcode = $request->input('dcode');
        $departdata = DB::table('depart')
            ->where('propertyid', $this->propertyid)
            ->where('dcode', $dcode)
            ->first();
        if (strtolower($departdata->nature) == 'outlet' && $departdata->kot_yn == 'N') {
            $items = '';
            $waitername = '';
            $amount = '';
        } else {

            $items = DB::table('kot')
                ->select(
                    'itemmast.Name',
                    'itemmast.RateEdit',
                    'itemmast.DiscApp',
                    'itemmast.RateIncTax',
                    'itemmast.SChrgApp',
                    'kot.description',
                    'kot.qty',
                    'kot.amount',
                    'kot.rate',
                    'kot.voidyn',
                    'kot.item',
                    'kot.vno',
                    'kot.vdate',
                    'kot.sno',
                    'kot.roomno',
                    'kot.waiter',
                    'kot.remarks',
                    'kot.pax',
                    'kot.docid',
                    'kot.vtype',
                    'kot.nctype',
                    'kot.restcode',
                    'kot.mergedwith',
                    DB::raw('COALESCE(taxstru.tax_code, \'\') AS tax_code'),
                    DB::raw('COALESCE(taxstru.tax_name, \'\') AS tax_name'),
                    DB::raw('COALESCE(taxstru.tax_rate, 0) AS tax_rate'),
                    'itemcatmast.TaxStru',
                    DB::raw('SUM(COALESCE(taxstru.tax_rate, 0)) AS taxrate_sum'),
                    DB::raw("CASE WHEN itemmast.RateIncTax = 'Y' THEN kot.rate * (COALESCE(taxstru.tax_rate, 0) / 100) 
              ELSE 0.00 END AS taxamt,
              CASE WHEN itemmast.RateIncTax = 'Y' THEN kot.rate *100/ (100+(COALESCE(taxstru.tax_rate, 0))) 
              ELSE 0.00 END AS taxedrate,
              CASE WHEN itemmast.RateIncTax = 'Y' THEN kot.rate *100/ (100+(COALESCE(taxstru.tax_rate, 0)))  * kot.qty
              ELSE 0.00 END AS fixamount")
                )
                ->leftJoin('itemmast', 'kot.item', '=', 'itemmast.Code')
                ->leftJoin('itemcatmast', 'itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                ->leftJoin(DB::raw('(SELECT str_code, GROUP_CONCAT(name) AS tax_name, GROUP_CONCAT(tax_code) AS tax_code, SUM(rate) AS tax_rate FROM taxstru GROUP BY str_code) AS taxstru'), function ($join) {
                    $join->on('taxstru.str_code', '=', 'itemcatmast.TaxStru');
                })
                ->where('kot.propertyid', $this->propertyid)
                ->where('kot.roomno', $roomno)
                ->where('kot.pending', 'Y')
                ->where('kot.voidyn', 'N')
                ->where('kot.nckot', 'N')
                ->groupBy('kot.sn')
                ->orderBy('kot.vno', 'ASC')
                ->get();

            $outlet1 = null;
            $outlet2 = null;

            $restcodes = $items->pluck('restcode')->unique()->values();
            $mergedcodes = $items->pluck('mergedwith')->filter()->unique()->values();

            if ($mergedcodes->isNotEmpty() && $restcodes->count() > 0) {
                $outlet1 = $restcodes[0];
                $outlet2 = $restcodes[1];
            }

            $amount = 0;
            foreach ($items as $item) {
                $amount += $item->amount;
            }
            if (count($items) > 0) {
                $waitername = DB::table('server_mast')->where('propertyid', $this->propertyid)->where('scode', $items[0]->waiter)->value('name');
            }
        }

        $sundrytype = DB::table('sundrytype')->where('propertyid', $this->propertyid)->where('vtype', $dcode)->orderBy('sno', 'ASC')->get();
        $sale1 = Sale1::where('propertyid', $this->propertyid)->where('roomno', $roomno)->first();

        $data = [
            'items' => $items,
            'sundrytype' => $sundrytype,
            'amount' => $amount,
            'sale1' => $sale1,
            'waitername' => $waitername ?? '',
            'outlet1code' => $outlet1,
            'outlet2code' => $outlet2
        ];

        return json_encode($data);
    }

    public function fetchtaxstruitem(Request $request)
    {
        $taxcode = $request->input('taxcode');
        $data = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $taxcode)->orderBy('sno', 'ASC')->get();
        return json_encode($data);
    }

    public function openroomstatus(Request $request)
    {
        $permission = revokeopen(141114);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $roomcategorydata = DB::table('room_cat')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $housekeepingdata = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'HOUSE KEEPING')->orderBy('name', 'ASC')->get();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        return view('property.roomstatus', ['roomcategorydata' => $roomcategorydata, 'housekeepingdata' => $housekeepingdata, 'ncurdate' => $ncurdate]);
    }

    public function openlookuproom(Request $request)
    {
        $permission = revokeopen(131113);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $roomcategorydata = DB::table('room_cat')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $housekeepingdata = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'HOUSE KEEPING')->orderBy('name', 'ASC')->get();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        return view('property.lookuprooms', ['roomcategorydata' => $roomcategorydata, 'housekeepingdata' => $housekeepingdata, 'ncurdate' => $ncurdate]);
    }

    public function housekeepget(Request $request)
    {
        $housekeepingdata = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'HOUSE KEEPING')->orderBy('name', 'ASC')->get();
        return json_encode($housekeepingdata);
    }

    public function roomcategoryget(Request $request)
    {
        $roomcategorydata = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        return json_encode($roomcategorydata);
    }

    public function allroomcountget()
    {
        try {
            $roomCounts = RoomMast::select('room_cat', DB::raw('COUNT(rcode) as rcode_count'))
                ->groupBy('room_cat')
                ->pluck('rcode_count', 'room_cat');

            return response()->json($roomCounts, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function roomget(Request $request)
    {
        $room_cat = $request->input('categoryCode');

        $roomdata = DB::table('room_mast')
            ->where('type', 'RO')
            ->where('propertyid', $this->propertyid)
            ->where('room_cat', $room_cat)
            ->orderBy('rcode', 'ASC')
            ->get();

        return json_encode($roomdata);
    }

    public function roomcountget(Request $request)
    {
        $roomcount = DB::table('room_cat')->where('cat_code', $request->input('categoryCode'))->where('propertyid', $this->propertyid)->value('norooms');
        $checkbookedrooms = DB::table('grpbookingdetails')
            ->where('RoomCat', $request->input('categoryCode'))
            ->where('ArrDate', '=', $request->input('checkindate'))
            ->where('Property_ID', $this->propertyid)
            ->where('Cancel', 'N')
            ->count();
        $totalbookedroom = DB::table('grpbookingdetails')->where('RoomCat', $request->input('categoryCode'))->where('Property_ID', $this->propertyid)->where('Cancel', 'N')->count();
        $checkindate = DB::table('grpbookingdetails')
            ->where('RoomCat', $request->input('categoryCode'))
            ->where('Property_ID', $this->propertyid)->value('ArrDate');
        $totalroomcount = $roomcount - $checkbookedrooms;

        return json_encode($totalroomcount);
    }

    public function backendroomcategory(Request $request)
    {
        $roomdata = DB::table('room_cat')->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->get();
        return response()->json($roomdata);
    }

    public function backend_reservations(Request $request)
    {
        $start = $request->input('start');
        echo $start;
        // $end = $request->input('end');
        // $reservationdata = DB::table('reservations')
        //     ->whereNot('end', '<=', $start)
        //     ->orWhereNot('start', '>=', $end)
        //     ->get();
        // return response()->json($reservationdata);
    }

    public function backendreservationcreate(Request $request)
    {
        $roomData = $request->all();
        // var_dump($roomData);
        $roomnum = $roomData['resource'];
        $start = $roomData['start'];
        $end = $roomData['end'];
        $title = $roomData['text'];
        $roomdata = [
            'name' => $title,
            'start' => $start,
            'end' => $end,
            'room_id' => $roomnum,
            'status' => 'new',
        ];
        DB::table('reservations')->insert($roomdata);
        return response()->json($roomdata);
    }

    public function bookedroomget(Request $request)
    {
        $bookedroomdata = RoomOcc::select([
            'roomocc.docid',
            'roomocc.folioNo',
            'roomocc.sno1',
            'roomocc.sno',
            'roomocc.roomno',
            'roomocc.roomcat',
            'roomocc.plancode',
            'roomocc.guestprof',
            'roomocc.name as name',
            'roomocc.chkindate',
            'roomocc.depdate',
            'roomocc.leaderyn',
            'roomocc.propertyid',
            'roomocc.roomrate',
            'roomocc.adult',
            'roomocc.children',
            'booking.BookedBy',
            DB::raw('DATE_SUB(roomocc.depdate, INTERVAL 1 DAY) as depdate_minus_one'),
            DB::raw('COALESCE(paycharge.billno, 0) as billno'),
            'enviro_form.checkout as envcheck',
            'room_cat.cat_code',
            'room_cat.name as roomcatname',
            'guestprof.con_prefix',
            'guestprof.mobile_no',
            'guestprof.guestcode',
            'plan_mast.pcode',
            'guestfolio.company',
            'guestfolio.pickupdrop',
            'guestfolio.remarks',
            'plan_mast.name as planname',
            'sc.name as companyname',
            'st.name as travelname'
        ])
            ->where('roomocc.propertyid', $this->propertyid)
            ->whereNull('roomocc.type')

            ->leftJoin('guestprof', function ($join) {
                $join->on('guestprof.guestcode', '=', 'roomocc.guestprof')
                    ->where('guestprof.propertyid', '=', $this->propertyid);
            })

            ->leftJoin('guestfolio', function ($join) {
                $join->on('guestfolio.docid', '=', 'roomocc.docid')
                    ->on('guestfolio.guestprof', '=', 'roomocc.guestprof');
            })

            ->leftJoin('grpbookingdetails', function ($join) {
                $join->on('grpbookingdetails.ContraDocId', '=', 'roomocc.docid')
                    ->where('grpbookingdetails.Property_ID', '=', $this->propertyid);
            })

            ->leftJoin('booking', function ($join) {
                $join->on('booking.DocId', '=', 'grpbookingdetails.BookingDocid');
            })

            ->join('room_cat', 'roomocc.roomcat', '=', 'room_cat.cat_code')

            ->leftJoin('plan_mast', 'roomocc.plancode', '=', 'plan_mast.pcode')

            ->leftJoin('enviro_form', 'enviro_form.propertyid', '=', 'roomocc.propertyid')

            ->leftJoin('subgroup as sc', 'sc.sub_code', '=', 'guestfolio.company')

            ->leftJoin('subgroup as st', 'st.sub_code', '=', 'guestfolio.travelagent')

            ->leftJoin('paycharge', function ($join) {
                $join->on('paycharge.folionodocid', '=', 'roomocc.docid')
                    ->on('paycharge.sno1', '=', 'roomocc.sno1')
                    ->whereIn('paycharge.vtype', ['RC', 'REV']);
            })

            ->groupBy([
                'roomocc.docid',
                'roomocc.sno1',
                'roomocc.sno',
                'roomocc.roomno',
                'roomocc.roomcat',
                'roomocc.plancode',
                'roomocc.guestprof',
                'roomocc.name',
                'roomocc.chkindate',
                'roomocc.depdate',
                'roomocc.leaderyn',
                'roomocc.propertyid',
                'booking.BookedBy',
                'enviro_form.checkout',
                'room_cat.cat_code',
                'room_cat.name',
                'guestprof.con_prefix',
                'guestprof.mobile_no',
                'guestprof.guestcode',
                'plan_mast.pcode',
                'guestfolio.company',
                'guestfolio.pickupdrop',
                'guestfolio.remarks',
                'plan_mast.name',
                'sc.name',
                'st.name'
            ])
            ->orderBy('roomocc.roomno')
            ->get();

        $amountdetails = RoomOcc::select([
            'roomocc.name as guestname',
            'roomocc.docid',
            'roomocc.sno1',
            'roomocc.sno',
            'roomocc.leaderyn',
            'roomocc.roomno',
            DB::raw('COALESCE(MAX(paycharge.msno1), 0) AS msno1'),
            DB::raw('COALESCE(SUM(CASE WHEN paycharge.amtdr IS NOT NULL THEN paycharge.amtdr ELSE 0 END), 0.00) AS totalamt'),
            DB::raw('COALESCE(SUM(CASE WHEN paycharge.amtcr IS NOT NULL THEN paycharge.amtcr ELSE 0 END), 0.00) AS paidamt'),
            DB::raw('COALESCE(SUM(CASE WHEN paycharge.amtdr IS NOT NULL THEN paycharge.amtdr ELSE 0 END) - SUM(CASE WHEN paycharge.amtcr IS NOT NULL THEN paycharge.amtcr ELSE 0 END), 0.00) as balance'),
            DB::raw('COALESCE(MAX(paycharge.billno), 0) AS billno')
        ])
            ->leftJoin('paycharge', function ($join) {
                $join->on('paycharge.folionodocid', '=', 'roomocc.docid')
                    ->on('paycharge.sno1', '=', 'roomocc.sno1');
            })
            ->where('roomocc.propertyid', $this->propertyid)
            ->whereNotNull('roomocc.docid')
            ->whereNull('roomocc.type')
            ->groupBy(['roomocc.docid', 'roomocc.sno1', 'roomocc.name', 'roomocc.sno', 'roomocc.leaderyn', 'roomocc.roomno'])
            ->orderBy('roomocc.roomno')
            ->get();

        $roomblockout = RoomBlockout::select(['roomcode', 'fromdate', 'reasons', 'propertyid'])
            ->where('propertyid', $this->propertyid)
            ->whereNull('cleardate')
            ->orderBy('roomcode')
            ->get();

        $data = [
            'bookedroomdata' => $bookedroomdata,
            'amountdetails' => $amountdetails,
            'roomblockout' => $roomblockout
        ];

        return response()->json($data);
    }

    public function reservedroomget(Request $request)
    {
        $bookedroomdata = DB::table('grpbookingdetails')
            ->select(
                'booking.BookedBy',
                'booking.Remarks',
                'booking.pickupdrop',
                'grpbookingdetails.*',
                DB::raw('DATE_SUB(grpbookingdetails.DepDate, INTERVAL 1 DAY) as depdate_minus_one'),
                'room_cat.cat_code',
                'room_cat.name as roomcatname',
                'guestprof.con_prefix',
                'guestprof.mobile_no',
                'guestprof.guestcode',
                'grpbookingdetails.GuestProf',
                'plan_mast.pcode',
                'plan_mast.name as planname',
                'bookingplandetails.sno1 as bsno1',
                'bookingplandetails.netplanamt as plannetamt'
            )
            ->join('guestprof', 'guestprof.guestcode', '=', 'grpbookingdetails.GuestProf')
            ->join('room_cat', 'grpbookingdetails.RoomCat', '=', 'room_cat.cat_code')
            ->leftJoin('plan_mast', 'grpbookingdetails.Plan_Code', '=', 'plan_mast.pcode')
            ->leftJoin('bookingplandetails', function ($join) {
                $join->on('bookingplandetails.docid', '=', 'grpbookingdetails.BookingDocid')
                    ->on('bookingplandetails.sno1', '=', 'grpbookingdetails.Sno');
            })
            ->leftJoin('booking', function ($query) {
                $query->on('booking.DocId', '=', 'grpbookingdetails.BookingDocid')
                    ->where('booking.Property_ID', $this->propertyid);
            })
            ->where('grpbookingdetails.Property_ID', $this->propertyid)
            ->where('grpbookingdetails.Cancel', 'N')
            ->where(function ($query) {
                $query->whereNotNull('grpbookingdetails.Plan_Code')
                    ->orWhereNull('grpbookingdetails.Plan_Code');
            })
            ->where(function ($query) {
                $query->where('grpbookingdetails.ContraDocId', '')
                    ->orWhereNull('grpbookingdetails.ContraDocId');
            })
            ->get();

        foreach ($bookedroomdata as $row) {
            $advance = Paycharge::where('propertyid', $this->propertyid)->where('sno', 1)->where('refdocid', $row->BookingDocid)->get() ?? '';
            $row->advance = $advance;
        }

        $emptycategory = GrpBookinDetail::select(
            'RoomCat as room_cat',
            'BookingDocid',
            'ArrDate',
            'DepDate',
            DB::raw('COUNT(*) as emptycategory')
        )
            ->where('RoomNo', '=', 0)
            ->where('Property_ID', '=', $this->propertyid)
            ->groupBy('RoomCat')
            ->get();

        $emptyrooms = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('RoomNo', '=', '0')
            ->groupBy('BookingDocid')
            ->get();

        $data = [
            'bookedroomdata' => $bookedroomdata,
            'emptycategory' => $emptycategory,
            'emptyrooms' => $emptyrooms
        ];

        return response()->json($data);
    }


    public function openchargeposting()
    {
        $permission = revokeopen(191111);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        return view('property.chargesposting', ['ncurdate' => $ncurdate]);
    }

    public function chargesposting(Request $request)
    {
        $permission = revokeopen(191111);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $checkdatec = VoucherPrefix::where('propertyid', $this->propertyid)
            ->whereDate('date_from', '<=', $request->input('charge_date'))
            ->whereDate('date_to', '>=', $request->input('charge_date'))
            ->first();

        if ($checkdatec === null || $checkdatec === '0') {
            return back()->with('error', 'You are not eligible to post charges for this date: ' . date('d-m-Y', strtotime($request->input('charge_date'))));
        }

        Paycharge::where('vdate', $request->input('charge_date'))->whereIn('vtype', ['PPOS', 'IPOS'])->where('propertyid', $this->propertyid)->delete();
        $roomchrgdueac = EnviroFom::where('propertyid', $this->propertyid)->first();
        if ($roomchrgdueac->roomchrgdueac == '') {
            return back()->with('error', 'First Fill Enviro Setting Related to Daily Posting');
        }
        $ppostpost = Suntran::select([
            DB::raw('SUM(suntran.amount) AS RevAmt'),
            'suntran.revcode',
            'suntran.restcode',
            'suntran.vdate',
            DB::raw('MAX(depart.name) AS Outlet'),
            DB::raw('MAX(depart.short_name) AS DShortName'),
            DB::raw('MAX(revmast.name) AS Revenue'),
            DB::raw('MAX(suntran.suncode) AS SundryCode')
        ])
            ->leftJoin('revmast', 'suntran.revcode', '=', 'revmast.rev_code')
            ->leftJoin('depart', 'suntran.restcode', '=', 'depart.dcode')
            ->where('suntran.propertyid', $this->propertyid)
            ->where('suntran.vdate', $request->input('charge_date'))
            ->whereNotNull('suntran.revcode')
            ->where('suntran.revcode', '!=', '')
            ->where('suntran.suncode', '!=', $this->propertyid . '101')
            ->whereIn('depart.rest_type', ['Outlet', 'Room Service'])
            ->where('suntran.delflag', '!=', 'Y')
            ->groupBy('suntran.restcode', 'suntran.revcode', 'suntran.vdate')
            ->orderBy('suntran.restcode')
            ->get();

        foreach ($ppostpost as $row) {
            $vtypeac = 'PPOS';

            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtypeac)
                ->whereDate('date_from', '<=', $request->input('charge_date'))
                ->whereDate('date_to', '>=', $request->input('charge_date'))
                ->first();

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtypeac)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            $docid = $this->propertyid . $vtypeac . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

            $indata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $start_srl_no,
                'vdate' => $request->input('charge_date'),
                'sno' => '1',
                'sno1' => '1',
                'vtype' => $vtypeac,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'comments' => $row->revenue . 'Bill No: ' . $start_srl_no,
                'paycode' => $row->revcode,
                'amtcr' => '0.00',
                'amtdr' => $row->RevAmt,
                'restcode' => $row->restcode,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->name,
                'u_ae' => 'a',
            ];
            Paycharge::insert($indata);
        }

        $ipospost = Stock::selectRaw('
                SUM(stock.amount) - SUM(stock.discamt) AS ItemAmt,
                GROUP_CONCAT(DISTINCT stock.vno ORDER BY stock.vno ASC) AS vno_group,
                itemcatmast.RevCode,
                stock.restcode,
                stock.vdate,
                itemcatmast.AcCode,
                MAX(depart.short_name) AS DShortName
            ')
            ->leftJoin('itemmast', function ($join) {
                $join->on('stock.item', '=', 'itemmast.Code')
                    ->where('itemmast.Property_ID', $this->propertyid)
                    ->on('stock.itemrestcode', '=', 'itemmast.RestCode');
            })
            ->leftJoin('itemcatmast', function ($query) {
                $query->on('itemmast.ItemCatCode', '=', 'itemcatmast.Code')
                    ->where('itemcatmast.propertyid', $this->propertyid)
                    ->on('itemcatmast.RestCode', '=', 'itemmast.RestCode');
            })
            ->leftJoin('depart', function ($query) {
                $query->on('stock.restcode', '=', 'depart.dcode')
                    ->where('depart.propertyid', $this->propertyid);
            })
            ->where('stock.vdate', $request->input('charge_date'))
            ->where('stock.propertyid', $this->propertyid)
            ->where('stock.delflag', '<>', 'Y')
            ->whereRaw("stock.vtype = CONCAT('B', COALESCE(depart.short_name, ''))")
            ->whereIn('depart.rest_type', ['Outlet', 'Room Service'])
            ->groupBy('stock.restcode', 'stock.vdate', 'itemcatmast.RevCode', 'itemcatmast.AcCode')
            ->get();

        // echo '<pre>';
        // print_r($ipospost);
        // echo '</pre>';
        // exit;

        if ($ipospost->isNotEmpty()) {
            foreach ($ipospost as $row) {
                $vnos = explode(',', $row->vno_group);
                $billNoRange = DateHelper::generateBillNoRange($vnos);

                $comment = $row->DShortName . ' Bill No: ' . $billNoRange;
                $vtypeipos = 'IPOS';

                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtypeipos)
                    ->whereDate('date_from', '<=', $request->input('charge_date'))
                    ->whereDate('date_to', '>=', $request->input('charge_date'))
                    ->first();

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtypeipos)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');

                $docid = $this->propertyid . $vtypeipos . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

                $iposin = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $start_srl_no,
                    'vdate' => $request->input('charge_date'),
                    'sno' => '1',
                    'sno1' => '1',
                    'vtype' => $vtypeipos,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'comments' => $comment,
                    'paycode' => $row->RevCode,
                    'amtcr' => '0.00',
                    'amtdr' => $row->ItemAmt,
                    'restcode' => $row->restcode,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->name,
                    'u_ae' => 'a',
                ];
                Paycharge::insert($iposin);
            }
        }

        // exit;

        $tablename = 'paycharge';
        $ncurdate = $request->input('charge_date');
        $envirofom = EnviroFom::where('propertyid', $this->propertyid)->first();

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->whereDate('date_from', '<=', $request->input('charge_date'))
            ->whereDate('date_to', '>=', $request->input('charge_date'))
            ->first();

        $start_srl_no = $chkvpf->start_srl_no;
        $vprefix = $chkvpf->prefix;

        $nullroomocc = DB::table('roomocc')
            ->where('propertyid', $this->propertyid)
            ->whereNull('type')
            ->pluck('docid');

        $searchpay = DB::table('paycharge')
            ->where('propertyid', $this->propertyid)
            ->whereIn('folionodocid', $nullroomocc)
            ->whereNot('billno', '0')
            ->whereNull('settledate')
            ->groupBy('folionodocid')
            ->get(['roomno']);

        // if ($searchpay->isNotEmpty()) {
        //     $totalroom = $searchpay->pluck('roomno')->implode(', ');
        //     return back()->with('error', 'There are some unsettled guest bill, First Settle them Rooms: ' . $totalroom);

        // }

        if ($envirofom->plancalc == 'Y') {
            $vtype = 'REV';
            $results = PlanDetail::select(
                'plandetails.*',
                'roomocc.name',
                'roomocc.roomtype',
                'roomocc.roomcat',
                'guestfolio.company as Comp_Code',
                'guestfolio.guestprof',
                'guestfolio.travelagent',
                'revmast.name as chargename',
                'revmast.pay_type'
            )->leftJoin('paycharge', function ($join) use ($ncurdate, $vtype) {
                $join->on('paycharge.plancode', '=', 'plandetails.pcode')
                    ->on('paycharge.paycode', '=', 'plandetails.rev_code')
                    ->on('paycharge.folionodocid', '=', 'plandetails.docid')
                    ->on('paycharge.sno1', '=', 'plandetails.sno1')
                    ->where('paycharge.vdate', '=', $ncurdate)
                    ->where('paycharge.vtype', '=', $vtype);
            })
                ->leftJoin('roomocc', function ($join) {
                    $join->on('roomocc.docid', '=', 'plandetails.docid')
                        ->on('roomocc.sno1', '=', 'plandetails.sno1');
                })
                ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'plandetails.docid')
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'plandetails.rev_code')
                ->whereNull('paycharge.plancode')
                ->where('plandetails.propertyid', $this->propertyid)
                ->where('roomocc.chkindate', '<=', $ncurdate)
                ->whereNull('roomocc.type')
                ->where('roomocc.propertyid', $this->propertyid)
                ->get();

            foreach ($results as $result) {
                $planchargeamount = $result->amount;
                if ($planchargeamount != 0) {
                    $checktaxstru = TaxStructure::where('propertyid', $this->propertyid)
                        ->where('str_code', $result->taxstru)
                        ->get();
                    $getdocroomoc = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $result->docid)->where('leaderyn', 'Y')->first();
                    if ($getdocroomoc) {
                        $msno1 = $getdocroomoc->sno1;
                    } else {
                        $msno1 = 0;
                    }

                    $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->whereDate('date_from', '<=', $request->input('charge_date'))
                        ->whereDate('date_to', '>=', $request->input('charge_date'))
                        ->first();

                    $start_srl_no = $chkvpf->start_srl_no + 1;
                    $vprefix = $chkvpf->prefix;

                    VoucherPrefix::where('propertyid', $this->propertyid)
                        ->where('v_type', $vtype)
                        ->where('prefix', $vprefix)
                        ->increment('start_srl_no');
                    $docid = $this->propertyid . $vtype . ' ‎ ‎' . $vprefix . ' ‎ ‎ ‎ ' . $start_srl_no;
                    $chargeamt = $result->amount;
                    $insertdefaultdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vno' => $start_srl_no,
                        'vtype' => $vtype,
                        'sno' => 1,
                        'sno1' => $result->sno1,
                        'msno1' => $msno1,
                        'vdate' => $ncurdate,
                        'vtime' => date('H:i:s'),
                        'vprefix' => $vprefix,
                        'paycode' => $result->rev_code,
                        'paytype' => $result->pay_type,
                        'comments' => $result->chargename . ' For Room No. :' . $result->roomno,
                        'guestprof' => $result->guestprof,
                        'comp_code' => $result->Comp_Code,
                        'travel_agent' => $result->travelagent,
                        'roomno' => $result->roomno,
                        'amtdr' => $result->amount,
                        'roomtype' => $result->roomtype,
                        'roomcat' => $result->roomcat,
                        'foliono' => $result->foliono,
                        'restcode' => 'FOM' . $this->propertyid,
                        'billamount' => $result->netplanamt,
                        'taxper' => 0,
                        'onamt' => $result->netplanamt,
                        'folionodocid' => $result->docid,
                        'plancode' => $result->pcode,
                        'fixedchargecode' => $result->rev_code,
                        'plancharge' => $result->netplanamt,
                        'taxstru' => $result->taxstru,
                        'taxcondamt' => 0,
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    Paycharge::insert($insertdefaultdata);

                    foreach ($checktaxstru as $taxstru) {
                        $rates = $taxstru->rate;
                        $lowerlimit = $taxstru->limits;
                        $upperlimit = $taxstru->limit1;
                        $comp_operator = $taxstru->comp_operator;

                        if ($comp_operator == 'Between') {
                            if ($planchargeamount >= $lowerlimit && $planchargeamount <= $upperlimit) {
                                $taxamt = $planchargeamount * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travelagent,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->foliono,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $planchargeamount,
                                    'taxper' => $rates,
                                    'taxstru' => $result->taxstru,
                                    'onamt' => $planchargeamount,
                                    'folionodocid' => $result->docid,
                                    'plancode' => $result->pcode,
                                    'fixedchargecode' => $result->rev_code,
                                    'plancharge' => $result->netplanamt,
                                    'taxcondamt' => $planchargeamount,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } else {
                            if ($comp_operator == '<=') {
                                if ($planchargeamount >= $lowerlimit) {
                                    $taxamt = $planchargeamount * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travelagent,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->foliono,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $planchargeamount,
                                        'taxper' => $rates,
                                        'taxstru' => $result->taxstru,
                                        'onamt' => $planchargeamount,
                                        'folionodocid' => $result->docid,
                                        'plancode' => $result->pcode,
                                        'fixedchargecode' => $result->rev_code,
                                        'plancharge' => $result->netplanamt,
                                        'taxcondamt' => $planchargeamount,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '>=') {
                                if ($planchargeamount <= $lowerlimit) {
                                    $taxamt = $planchargeamount * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travelagent,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->foliono,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $planchargeamount,
                                        'taxper' => $rates,
                                        'taxstru' => $result->taxstru,
                                        'onamt' => $planchargeamount,
                                        'folionodocid' => $result->docid,
                                        'plancode' => $result->pcode,
                                        'fixedchargecode' => $result->rev_code,
                                        'plancharge' => $result->netplanamt,
                                        'taxcondamt' => $planchargeamount,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '=') {
                                if ($planchargeamount == $lowerlimit) {
                                    $taxamt = $planchargeamount * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travelagent,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->foliono,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $planchargeamount,
                                        'taxper' => $rates,
                                        'taxstru' => $result->taxstru,
                                        'onamt' => $planchargeamount,
                                        'folionodocid' => $result->docid,
                                        'plancode' => $result->pcode,
                                        'fixedchargecode' => $result->rev_code,
                                        'plancharge' => $result->netplanamt,
                                        'taxcondamt' => $planchargeamount,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '>') {
                                if ($planchargeamount > $lowerlimit) {
                                    $taxamt = $planchargeamount * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travelagent,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->foliono,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $planchargeamount,
                                        'taxper' => $rates,
                                        'taxstru' => $result->taxstru,
                                        'onamt' => $planchargeamount,
                                        'folionodocid' => $result->docid,
                                        'plancode' => $result->pcode,
                                        'fixedchargecode' => $result->rev_code,
                                        'plancharge' => $result->netplanamt,
                                        'taxcondamt' => $planchargeamount,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '<') {
                                if ($planchargeamount < $lowerlimit) {
                                    $taxamt = $planchargeamount * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travelagent,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->foliono,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $planchargeamount,
                                        'taxper' => $rates,
                                        'taxstru' => $result->taxstru,
                                        'onamt' => $planchargeamount,
                                        'folionodocid' => $result->docid,
                                        'plancode' => $result->pcode,
                                        'fixedchargecode' => $result->rev_code,
                                        'plancharge' => $result->netplanamt,
                                        'taxcondamt' => $planchargeamount,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } else {
                                $taxamt = $planchargeamount * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travelagent,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->foliono,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $planchargeamount,
                                    'taxper' => $rates,
                                    'taxstru' => $result->taxstru,
                                    'onamt' => $planchargeamount,
                                    'folionodocid' => $result->docid,
                                    'plancode' => $result->pcode,
                                    'fixedchargecode' => $result->rev_code,
                                    'plancharge' => $result->netplanamt,
                                    'taxcondamt' => $planchargeamount,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        }
                    }
                }
            }
        }

        // exit;

        $results = DB::table('roomocc')
            ->select(
                'roomocc.*',
                'revmast.ac_code AS RoomChargeAc',
                'revmast.rev_code AS PayCode',
                'revmast.tax_stru AS TaxStru',
                'guestfolio.company as Comp_Code',
                'guestfolio.travelagent as travel_code',
                'guestfolio.rodisc',
                'guestfolio.company',
                'guestfolio.mfoliono',
                'guestfolio.mfolionodocid'
            )
            ->leftJoin('room_cat', 'roomocc.roomcat', '=', 'room_cat.cat_code')
            ->leftJoin('revmast', 'room_cat.rev_code', '=', 'revmast.rev_code')
            ->leftJoin('guestfolio', 'roomocc.docid', '=', 'guestfolio.docid')
            ->whereNull('roomocc.chkoutdate')
            ->where('roomocc.chkindate', '<=', $ncurdate)
            // ->where('roomocc.userchkoutdate', '>', $ncurdate)
            ->whereNull('roomocc.type')
            ->where('roomocc.propertyid', $this->propertyid)
            ->whereNotIn('roomocc.docid', function ($query) use ($ncurdate) {
                $query->select(DB::raw('DISTINCT folionodocid'))
                    ->from('paycharge')
                    ->where('vdate', $ncurdate)
                    ->whereColumn('paycharge.sno1', 'roomocc.sno1')
                    ->where('vtype', 'RC');
            })
            ->get();

        $paycode = DB::table('revmast')->where('propertyid', $this->propertyid)->where('name', 'ROOM CHARGE')->value('rev_code');

        foreach ($results as $result) {

            $getdocroomoc = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $result->docid)->where('leaderyn', 'Y')->first();

            if ($getdocroomoc) {
                $msno1 = $getdocroomoc->sno1;
            } else {
                $msno1 = 0;
            }
            $vtype = 'RC';
            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $request->input('charge_date'))
                ->whereDate('date_to', '>=', $request->input('charge_date'))
                ->first();

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;

            $docid = $this->propertyid . 'RC' . ' ‎ ‎' . $vprefix . ' ‎ ‎ ‎ ' . $start_srl_no;
            $roombookamt = $result->roomrate;
            if ($roombookamt != 0) {

                $checktaxstru = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $result->TaxStru)
                    ->get();

                $comment1 = 'ROOM CHARGE, ROOM No: ' . $result->roomno;
                $insertdefaultdata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $start_srl_no,
                    'vtype' => $vtype,
                    'sno' => 1,
                    'sno1' => $result->sno1,
                    'msno1' => $msno1,
                    'vdate' => $ncurdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'paycode' => $paycode,
                    'comments' => $comment1,
                    'guestprof' => $result->guestprof,
                    'comp_code' => $result->Comp_Code,
                    'travel_agent' => $result->travel_code,
                    'roomno' => $result->roomno,
                    'amtdr' => $result->roomrate,
                    'roomtype' => $result->roomtype,
                    'roomcat' => $result->roomcat,
                    'foliono' => $result->folioNo,
                    'restcode' => 'FOM' . $this->propertyid,
                    'billamount' => $result->roomrate,
                    'taxper' => 0,
                    'onamt' => $result->roomrate,
                    'folionodocid' => $result->docid,
                    'taxcondamt' => 0,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                DB::table($tablename)->insert($insertdefaultdata);

                foreach ($checktaxstru as $taxstru) {
                    $rates = $taxstru->rate;
                    $lowerlimit = $taxstru->limits;
                    $upperlimit = $taxstru->limit1;
                    $comp_operator = $taxstru->comp_operator;

                    if ($comp_operator == 'Between') {
                        if ($roombookamt >= $lowerlimit && $roombookamt <= $upperlimit) {
                            $taxamt = $roombookamt * $rates / 100;

                            $taxname = DB::table('revmast')
                                ->where('propertyid', $this->propertyid)
                                ->where('rev_code', $taxstru->tax_code)
                                ->value('name');

                            $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                            $insertdata = [
                                'propertyid' => $this->propertyid,
                                'docid' => $docid,
                                'vno' => $start_srl_no,
                                'vtype' => $vtype,
                                'sno' => $taxstru->sno + 1,
                                'sno1' => $result->sno1,
                                'msno1' => $msno1,
                                'vdate' => $ncurdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $vprefix,
                                'paycode' => $taxstru->tax_code,
                                'comments' => $comments,
                                'guestprof' => $result->guestprof,
                                'comp_code' => $result->Comp_Code,
                                'travel_agent' => $result->travel_code,
                                'roomno' => $result->roomno,
                                'amtdr' => $taxamt,
                                'roomtype' => $result->roomtype,
                                'roomcat' => $result->roomcat,
                                'foliono' => $result->folioNo,
                                'restcode' => 'FOM' . $this->propertyid,
                                'billamount' => $roombookamt,
                                'taxper' => $rates,
                                'taxstru' => $result->TaxStru,
                                'onamt' => $roombookamt,
                                'folionodocid' => $result->docid,
                                'taxcondamt' => $roombookamt,
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];

                            DB::table($tablename)->insert($insertdata);
                        }
                    } else {
                        if ($comp_operator == '<=') {
                            if ($roombookamt >= $lowerlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } elseif ($comp_operator == '>=') {
                            if ($roombookamt <= $lowerlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } elseif ($comp_operator == '=') {
                            if ($roombookamt == $lowerlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } elseif ($comp_operator == '>') {
                            if ($roombookamt > $lowerlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } elseif ($comp_operator == '<') {
                            if ($roombookamt < $lowerlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        }
                    }
                }
            }
            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');
        }
        return back()->with('success', 'Room Charge Posted Successfully!');
    }

    public function submitadvcahrge(Request $request)
    {
        $validate = $request->validate([
            'charge' => 'required',
            'amount' => 'required',
        ]);

        $ncurdate = $this->ncurdate;
        // echo $request->docid;
        // exit;
        if ($request->charge == 'RMCH' . $this->propertyid) {
            $results = DB::table('roomocc')
                ->select(
                    'roomocc.*',
                    'revmast.ac_code AS RoomChargeAc',
                    'revmast.rev_code AS PayCode',
                    'revmast.tax_stru AS TaxStru',
                    'guestfolio.company as Comp_Code',
                    'guestfolio.travelagent as travel_code',
                    'guestfolio.rodisc',
                    'guestfolio.company',
                    'guestfolio.mfoliono',
                    'guestfolio.mfolionodocid'
                )
                ->leftJoin('room_cat', 'roomocc.roomcat', '=', 'room_cat.cat_code')
                ->leftJoin('revmast', 'room_cat.rev_code', '=', 'revmast.rev_code')
                ->leftJoin('guestfolio', 'roomocc.docid', '=', 'guestfolio.docid')
                ->whereNull('roomocc.chkoutdate')
                ->where('roomocc.chkindate', '<=', $this->ncurdate)
                ->whereNull('roomocc.type')
                ->where('roomocc.docid', $request->docid)
                ->where('roomocc.sno1', $request->sno1)
                ->where('roomocc.sno', $request->sno)
                ->where('roomocc.propertyid', $this->propertyid)
                // ->whereNotIn('roomocc.docid', function ($query) use ($ncurdate) {
                //     $query->select(DB::raw('DISTINCT folionodocid'))
                //         ->from('paycharge')
                //         ->where('vdate', $ncurdate)
                //         ->whereColumn('paycharge.sno1', 'roomocc.sno1')
                //         ->where('vtype', 'RC');
                // })
                ->get();

            // var_dump($results);

            $paycode = DB::table('revmast')->where('propertyid', $this->propertyid)->where('name', 'ROOM CHARGE')->value('rev_code');
            $tablename = 'paycharge';

            foreach ($results as $result) {

                $getdocroomoc = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $result->docid)->where('leaderyn', 'Y')->first();

                if ($getdocroomoc) {
                    $msno1 = $getdocroomoc->sno1;
                } else {
                    $msno1 = 0;
                }

                $vtype = 'RC';
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $ncurdate)
                    ->whereDate('date_to', '>=', $ncurdate)
                    ->first();

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                $docid = $this->propertyid . 'RC' . ' ‎ ‎' . $vprefix . ' ‎ ‎ ‎ ' . $start_srl_no;
                // $roombookamt = $result->roomrate;
                $roombookamt = $request->input('amount');;
                if ($roombookamt != 0) {

                    $checktaxstru = DB::table('taxstru')
                        ->where('propertyid', $this->propertyid)
                        ->where('str_code', $result->TaxStru)
                        ->get();

                    $comment1 = 'ROOM CHARGE, ROOM No: ' . $result->roomno;
                    $insertdefaultdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vno' => $start_srl_no,
                        'vtype' => $vtype,
                        'sno' => 1,
                        'sno1' => $result->sno1,
                        'msno1' => $msno1,
                        'vdate' => $ncurdate,
                        'vtime' => date('H:i:s'),
                        'vprefix' => $vprefix,
                        'paycode' => $paycode,
                        'comments' => $comment1,
                        'guestprof' => $result->guestprof,
                        'comp_code' => $result->Comp_Code,
                        'travel_agent' => $result->travel_code,
                        'roomno' => $result->roomno,
                        'amtdr' => $roombookamt,
                        'roomtype' => $result->roomtype,
                        'roomcat' => $result->roomcat,
                        'foliono' => $result->folioNo,
                        'restcode' => 'FOM' . $this->propertyid,
                        'billamount' => $roombookamt,
                        'taxper' => 0,
                        'onamt' => $roombookamt,
                        'folionodocid' => $result->docid,
                        'taxcondamt' => 0,
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    DB::table($tablename)->insert($insertdefaultdata);

                    foreach ($checktaxstru as $taxstru) {
                        $rates = $taxstru->rate;
                        $lowerlimit = $taxstru->limits;
                        $upperlimit = $taxstru->limit1;
                        $comp_operator = $taxstru->comp_operator;

                        if ($comp_operator == 'Between') {
                            if ($roombookamt >= $lowerlimit && $roombookamt <= $upperlimit) {
                                $taxamt = $roombookamt * $rates / 100;

                                $taxname = DB::table('revmast')
                                    ->where('propertyid', $this->propertyid)
                                    ->where('rev_code', $taxstru->tax_code)
                                    ->value('name');

                                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                $insertdata = [
                                    'propertyid' => $this->propertyid,
                                    'docid' => $docid,
                                    'vno' => $start_srl_no,
                                    'vtype' => $vtype,
                                    'sno' => $taxstru->sno + 1,
                                    'sno1' => $result->sno1,
                                    'msno1' => $msno1,
                                    'vdate' => $ncurdate,
                                    'vtime' => date('H:i:s'),
                                    'vprefix' => $vprefix,
                                    'paycode' => $taxstru->tax_code,
                                    'comments' => $comments,
                                    'guestprof' => $result->guestprof,
                                    'comp_code' => $result->Comp_Code,
                                    'travel_agent' => $result->travel_code,
                                    'roomno' => $result->roomno,
                                    'amtdr' => $taxamt,
                                    'roomtype' => $result->roomtype,
                                    'roomcat' => $result->roomcat,
                                    'foliono' => $result->folioNo,
                                    'restcode' => 'FOM' . $this->propertyid,
                                    'billamount' => $roombookamt,
                                    'taxper' => $rates,
                                    'taxstru' => $result->TaxStru,
                                    'onamt' => $roombookamt,
                                    'folionodocid' => $result->docid,
                                    'taxcondamt' => $roombookamt,
                                    'u_entdt' => $this->currenttime,
                                    'u_name' => Auth::user()->u_name,
                                    'u_ae' => 'a',
                                ];

                                DB::table($tablename)->insert($insertdata);
                            }
                        } else {
                            if ($comp_operator == '<=') {
                                if ($roombookamt >= $lowerlimit) {
                                    $taxamt = $roombookamt * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travel_code,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->folioNo,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $roombookamt,
                                        'taxper' => $rates,
                                        'taxstru' => $result->TaxStru,
                                        'onamt' => $roombookamt,
                                        'folionodocid' => $result->docid,
                                        'taxcondamt' => $roombookamt,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '>=') {
                                if ($roombookamt <= $lowerlimit) {
                                    $taxamt = $roombookamt * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travel_code,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->folioNo,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $roombookamt,
                                        'taxper' => $rates,
                                        'taxstru' => $result->TaxStru,
                                        'onamt' => $roombookamt,
                                        'folionodocid' => $result->docid,
                                        'taxcondamt' => $roombookamt,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '=') {
                                if ($roombookamt == $lowerlimit) {
                                    $taxamt = $roombookamt * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travel_code,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->folioNo,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $roombookamt,
                                        'taxper' => $rates,
                                        'taxstru' => $result->TaxStru,
                                        'onamt' => $roombookamt,
                                        'folionodocid' => $result->docid,
                                        'taxcondamt' => $roombookamt,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '>') {
                                if ($roombookamt > $lowerlimit) {
                                    $taxamt = $roombookamt * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travel_code,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->folioNo,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $roombookamt,
                                        'taxper' => $rates,
                                        'taxstru' => $result->TaxStru,
                                        'onamt' => $roombookamt,
                                        'folionodocid' => $result->docid,
                                        'taxcondamt' => $roombookamt,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            } elseif ($comp_operator == '<') {
                                if ($roombookamt < $lowerlimit) {
                                    $taxamt = $roombookamt * $rates / 100;

                                    $taxname = DB::table('revmast')
                                        ->where('propertyid', $this->propertyid)
                                        ->where('rev_code', $taxstru->tax_code)
                                        ->value('name');

                                    $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;

                                    $insertdata = [
                                        'propertyid' => $this->propertyid,
                                        'docid' => $docid,
                                        'vno' => $start_srl_no,
                                        'vtype' => $vtype,
                                        'sno' => $taxstru->sno + 1,
                                        'sno1' => $result->sno1,
                                        'msno1' => $msno1,
                                        'vdate' => $ncurdate,
                                        'vtime' => date('H:i:s'),
                                        'vprefix' => $vprefix,
                                        'paycode' => $taxstru->tax_code,
                                        'comments' => $comments,
                                        'guestprof' => $result->guestprof,
                                        'comp_code' => $result->Comp_Code,
                                        'travel_agent' => $result->travel_code,
                                        'roomno' => $result->roomno,
                                        'amtdr' => $taxamt,
                                        'roomtype' => $result->roomtype,
                                        'roomcat' => $result->roomcat,
                                        'foliono' => $result->folioNo,
                                        'restcode' => 'FOM' . $this->propertyid,
                                        'billamount' => $roombookamt,
                                        'taxper' => $rates,
                                        'taxstru' => $result->TaxStru,
                                        'onamt' => $roombookamt,
                                        'folionodocid' => $result->docid,
                                        'taxcondamt' => $roombookamt,
                                        'u_entdt' => $this->currenttime,
                                        'u_name' => Auth::user()->u_name,
                                        'u_ae' => 'a',
                                    ];

                                    DB::table($tablename)->insert($insertdata);
                                }
                            }
                        }
                    }
                }
                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');
            }
            return redirect('autorefreshmain');
        }

        // exit;

        $guestfolio = Guestfolio::where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))->first();

        $compcodetmp = '';
        if (!is_null($guestfolio)) {
            $compcodetmp = $guestfolio->company ?? '';
        }

        $revdata = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $request->input('charge'))->first();
        $roombookamt = $request->input('amount');

        $checktaxstru = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->where('str_code', $revdata->tax_stru)
            ->get();

        $taxrates = 0;
        if ($revdata->tax_inc == 'Y') {
            $taxrates = 0;
            foreach ($checktaxstru as $tax) {
                $taxrates += $tax->rate;
            }
            if ($taxrates > 0 && !is_null($taxrates)) {
                $valuenew = str_replace(',', '', number_format(($roombookamt * 100) / (100 + $taxrates), 2));
                $roombookamt = $valuenew;
            }
        }

        if (strtolower($revdata->field_type) == 'c' && strtolower($revdata->type) == 'dr') {
            $amtdr = null;
            $amtcr = $roombookamt;
            $vtype = 'REV';
            $compcode = $compcodetmp;
        } else if (strtolower($revdata->field_type) == 'c' && strtolower($revdata->type) == 'cr') {
            $amtdr = $roombookamt;
            $amtcr = null;
            $vtype = 'REV';
            $compcode = $compcodetmp;
        }

        if (strtolower($revdata->field_type) == 'p' && $roombookamt < 0) {
            $amtdr = abs($roombookamt);
            $amtcr = null;
            $vtype = 'REV';
            $compcode = '';
        } else if (strtolower($revdata->field_type) == 'p' && $roombookamt > 0) {
            $amtdr = null;
            $amtcr = $roombookamt;
            $vtype = 'REC';
            $compcode = '';
        }

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $this->ncurdate)
            ->whereDate('date_to', '>=', $this->ncurdate)
            ->first();

        $start_srl_no = $chkvpf->start_srl_no + 1;
        $vprefix = $chkvpf->prefix;

        $vno = $start_srl_no;

        $result = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))->where('sno1', $request->input('sno1'))->first();
        $docid = $this->propertyid . $vtype . ' ‎ ‎' . $vprefix . ' ‎ ‎ ‎ ' . $vno;

        $rtaxstru = $revdata->tax_stru;

        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))->where('leaderyn', 'Y')->first();

        $insertdata = [
            'propertyid' => $this->propertyid,
            'docid' => $docid,
            'comp_code' => $compcode,
            'vno' => $vno,
            'vtype' => $vtype,
            'sno' => 1,
            'sno1' => $request->input('sno1'),
            'msno1' => $rocc->sno1 ?? 0,
            'chqno' => $request->input('checkno') ? $request->input('checkno') : $request->input('referencenoupi'),
            'cardno' => $request->input('crnumber'),
            'cardholder' => $request->input('holdername'),
            'expdate' => $request->input('expdatecr'),
            'bookno' => $request->input('batchno'),
            'vdate' => $this->ncurdate,
            'vtime' => date('H:i:s'),
            'vprefix' => $vprefix,
            'paycode' => $request->input('charge'),
            'paytype' => $revdata->pay_type ?? '',
            'comments' => $request->input('narration'),
            'guestprof' => $result->guestprof,
            'roomno' => $result->roomno,
            'amtdr' => $amtdr ?? '0.00',
            'amtcr' => $amtcr ?? '0.00',
            'roomtype' => $result->roomtype,
            'roomcat' => $result->roomcat,
            'foliono' => $result->folioNo,
            'restcode' => 'FOM' . $this->propertyid,
            'billamount' => $result->rackrate,
            'taxper' => $taxrates,
            'onamt' => $result->rackrate,
            'folionodocid' => $result->docid,
            'taxcondamt' => 0,
            'taxstru' => $rtaxstru,
            'u_entdt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'a',
        ];

        DB::table('paycharge')->insert($insertdata);

        foreach ($checktaxstru as $taxstru) {
            $rates = $taxstru->rate;
            $lowerlimit = $taxstru->limits;
            $upperlimit = $taxstru->limit1;
            $comp_operator = $taxstru->comp_operator;

            $taxamt = $roombookamt * $rates / 100;

            if ($taxamt > 0) {
                if (strtolower($revdata->field_type) == 'c') {
                    $amtdr = $taxamt;
                    $amtcr = null;
                    $vtype = 'REV';
                }

                $taxname = DB::table('revmast')
                    ->where('propertyid', $this->propertyid)
                    ->where('rev_code', $taxstru->tax_code)
                    ->value('name');

                $comments = $taxname . ', ' . 'Room No: ' . $result->roomno;
                $insertdata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $vno,
                    'vtype' => $vtype,
                    'sno' => $taxstru->sno + 1,
                    'sno1' => $request->input('sno1'),
                    'msno1' => $rocc->sno1 ?? 0,
                    'chqno' => $request->input('checkno') ? $request->input('checkno') : $request->input('referencenoupi'),
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'paycode' => $taxstru->tax_code,
                    'comments' => $comments,
                    'guestprof' => $result->guestprof,
                    'roomno' => $result->roomno,
                    'amtdr' => abs($amtdr) ?? '0.00',
                    'amtcr' => abs($amtcr) ?? '0.00',
                    'roomtype' => $result->roomtype,
                    'roomcat' => $result->roomcat,
                    'foliono' => $result->folioNo,
                    'restcode' => 'FOM' . $this->propertyid,
                    'billamount' => $roombookamt,
                    'taxper' => $rates,
                    'taxstru' => $rtaxstru,
                    'onamt' => $roombookamt,
                    'folionodocid' => $result->docid,
                    'taxcondamt' => $roombookamt,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                DB::table('paycharge')->insert($insertdata);
            }
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->where('prefix', $vprefix)
            ->increment('start_srl_no');

        return redirect('autorefreshmain');
    }

    public function openitemlist(Request $request)
    {
        $permission = revokeopen(121315);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('itemlistmast', 'Item List Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $itemlistdata = Db::table('items')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $maxicode = DB::table('items')->where('propertyid', $this->propertyid)->max('icode');
        if (empty($maxicode)) {
            $maxicode = 0;
        }
        return view('property.itemlists', [
            'data' => $itemlistdata,
            'maxicode' => $maxicode,
            'idlength' => $this->ptlngth
        ]);
    }

    public function submititemlist(Request $request)
    {
        $permission = revokeopen(121315);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'barcode' => 'required',
            'name' => 'required',
        ];
        $tableName = 'items';

        $existingcode = DB::table($tableName)
            ->where('icode', $request->input('barcode') . $this->propertyid)
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingcode) {
            return back()->with('error', 'Item Code already exists!');
        }

        $existingname = DB::table($tableName)
            ->where('name', $request->input('name'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Name already exists!');
        }

        if (!empty($request->file('itempicture'))) {
            $itempic = $request->file('itempicture');
            $itempicture = $request->input('barcode') . '_' . $this->propertyid . '.' . $itempic->getClientOriginalExtension();
            $folderPathp = 'public/property/itempicture';
            Storage::makeDirectory($folderPathp);
            Storage::putFileAs($folderPathp, $itempic, $itempicture);
        } else {
            $itempicture = null;
        }


        try {
            $insertdata = [
                'propertyid' => $this->propertyid,
                'icode' => $request->input('barcode') . $this->propertyid,
                'barcode' => $request->input('barcode') . $this->propertyid,
                'name' => $request->input('name'),
                'itempic' => $itempicture,
                'hsncode' => $request->input('hsncode'),
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Item Master Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item Master!' . $e->getMessage());
        }
    }

    public function updateitemlist(Request $request)
    {
        $permission = revokeopen(121315);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'items';

        $existingbarcode = DB::table($tableName)
            ->where('barcode', $request->input('upbarcode') . $this->propertyid)
            ->whereNot('sn', $request->input('upsn'))
            ->where('propertyid', $this->propertyid)
            ->first();

        if ($existingbarcode) {
            return back()->with('error', 'Barcode already exists for item: ' . $request->input('upname'));
        }

        if (!empty($request->file('upitemimage'))) {
            $itempic = $request->file('upitemimage');
            $itempicture = $request->input('upbarcode') . '_' . $this->propertyid . '.' . $itempic->getClientOriginalExtension();
            $folderPathp = 'public/property/itempicture';
            Storage::makeDirectory($folderPathp);
            Storage::putFileAs($folderPathp, $itempic, $itempicture);
        } else {
            $itempicture = $request->input('olditemimage');
        }

        try {
            $updatedata = [
                'name' => $request->input('upname'),
                'itempic' => $itempicture,
                'hsncode' => $request->input('uphsncode'),
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];

            $checkitemmast = DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('upicode'))->first();
            if ($checkitemmast) {
                $upitemmast = [
                    'Name' => $request->input('upname'),
                    'HSNCode' => $request->input('uphsncode'),
                    'iempic' => $itempicture,
                    'u_updaedt' => $this->currenttime,
                    'U_AE' => 'e',
                ];
                DB::table('itemmast')->where('Property_ID', $this->propertyid)->where('Code', $request->input('upicode'))->update($upitemmast);
            }

            DB::table($tableName)
                ->where('propertyid', $this->propertyid)
                ->where('sn', $request->input('upsn'))
                ->update($updatedata);

            return back()->with('success', 'Item Master Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item Master!' . $e->getMessage());
        }
    }

    public function deleteitemlist(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121315);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $image = DB::table('items')
                ->where('propertyid', $this->propertyid)
                ->where('icode', $ucode)
                ->where('sn', $sn)
                ->value('itempic');
            if ($image) {
                $folderPath = storage_path('app/public/property/itempicture/' . $image);
                if (file_exists($folderPath)) {
                    unlink($folderPath);
                }
            }
            $jaldiwahasehato📢 = DB::table('items')
                ->where('icode', base64_decode($request->input('ucode')))
                ->where('sn', base64_decode($request->input('sn')))
                ->where('propertyid', $this->propertyid)
                ->delete();

            if ($jaldiwahasehato📢) {
                return response()->json(['message' => 'Item Master Deleted Successfully']);
            } else {
                return response()->json(['message' => 'Unable to Delete Item Master!'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Delete Item Master!'], 500);
        }
    }

    public function opennightaudit()
    {
        $permission = revokeopen(191112);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ncurdate = $this->ncurdate;
        $envpos = EnviroPos::where('propertyid', $this->propertyid)->first();
        return view('property.nightaudit', ['ncurdate' => $ncurdate, 'envpos' => $envpos]);
    }

    public function submitnightaudit(Request $request)
    {
        try {
            DB::begintransaction();
            $permission = revokeopen(191112);
            if (is_null($permission) || $permission->ins == 0) {
                return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
            }
            $ncurdate = Carbon::parse($request->input('ncurdate'))->addDays(1)->format('Y-m-d');
            $ncurdateorg = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');

            if ($this->ncurdate >  date('Y-m-d')) {
                return back()->with('error', 'Invalid date process');
            }

            $enviro_pos = EnviroPos::where('propertyid', $this->propertyid)->first();

            if ($enviro_pos->kotatnightaudit == 'Y') {
                $chkkotpending = Kot::where('propertyid', $this->propertyid)
                    ->where('pending', 'Y')
                    ->where('vdate', $ncurdateorg)
                    ->where('voidyn', 'N')
                    ->groupBy('vno')
                    ->get();

                $departmentBills = [];

                foreach ($chkkotpending as $item) {
                    $departname = Depart::where('propertyid', $this->propertyid)
                        ->where('dcode', $item->restcode)
                        ->first();

                    if (!isset($departmentBills[$departname->name])) {
                        $departmentBills[$departname->name] = [];
                    }

                    $departmentBills[$departname->name][] = $item->vno;
                }

                $msgParts = [];
                foreach ($departmentBills as $departName => $bills) {
                    // $msgParts[] = "Bill no. " . implode(', ', $bills) . " pending in " . $departName;
                    $msgParts[] =  $departName;
                }

                if (count($chkkotpending) > 0) {
                    $msg = "You have some pending KOTs in: " . implode(" and ", $msgParts);
                    return back()->with('nightinfo', ['message' => $msg, 'bills' => json_encode($bills), 'row' => 1]);
                }
            }

            if ($enviro_pos->posbillatnightaudit == 'Y') {
                $pendingBills = [];
                $checksalepending = Sale1::select(
                    'sale1.docid',
                    'depart.name as departname',
                    'sale1.vno',
                    DB::raw("CASE WHEN paycharge.docid IS NULL THEN 'Bill Left' ELSE 'Billed' END AS status")
                )
                    ->leftJoin('paycharge', 'paycharge.docid', '=', 'sale1.docid')
                    ->leftJoin('depart', 'depart.dcode', '=', 'sale1.restcode')
                    ->where('sale1.propertyid', $this->propertyid)
                    ->where('sale1.vdate', $ncurdateorg)
                    ->whereNull('paycharge.docid')
                    ->where('sale1.delflag', 'N')
                    ->groupBy('sale1.vno')
                    ->get();

                if ($checksalepending) {
                    foreach ($checksalepending as $item) {
                        if ($item->status != 'Billed') {
                            if (!isset($pendingBills[$item->departname])) {
                                $pendingBills[$item->departname] = [];
                            }
                            $pendingBills[$item->departname][] = $item->vno;
                        }
                    }

                    $summaryString = "";
                    foreach ($pendingBills as $departname => $bills) {
                        // $summaryString .= $departname . ": Bill No. " . implode(", ", $bills) . "; ";
                        $summaryString .= $departname . ", ";
                    }

                    $summaryString = rtrim($summaryString, "; ");

                    if (!empty($summaryString)) {
                        // $msg = "You have some unsettled Sale Bills: " . $summaryString;
                        $msg = "You have some unsettled Bills in: " . $summaryString;
                        return back()->with('nightinfo',  ['message' => $msg, 'bills' => json_encode($bills), 'row' => 2]);
                    }
                }
            }


            $chknotcharged = DB::table('roomocc')
                ->select('roomocc.*', 'guestfolio.mfoliono', 'guestfolio.comp')
                ->leftJoin('guestfolio', 'guestfolio.Docid', '=', 'roomocc.DocId')
                ->where('roomocc.propertyid', $this->propertyid)
                ->whereNull('roomocc.chkoutdate')
                ->where('roomocc.chkindate', '<=', $ncurdateorg)
                ->get();

            $roomsNotFound = [];

            foreach ($chknotcharged as $row) {
                $founduncharged = DB::table('paycharge')
                    ->where('propertyid', $this->propertyid)
                    ->where('vdate', $ncurdateorg)
                    ->where('vtype', 'RC')
                    ->where('folionodocid', $row->docid)
                    ->get();
                if ($founduncharged->isEmpty()) {
                    $roomsNotFound[] = $row->roomno;
                }
            }


            if (!empty($roomsNotFound)) {
                $errorMessage = 'Please Charge Posting For Rooms: ' . implode(', ', $roomsNotFound);
                return back()->with('error', 'Please Charge Posting For Rooms: ' . implode(', ', $roomsNotFound));
                // echo 'hy';
                // exit;
            }

            $nullroomocc = DB::table('roomocc')
                ->where('propertyid', $this->propertyid)
                ->whereNull('type')
                ->pluck('docid');

            $searchpay = DB::table('paycharge')
                ->where('propertyid', $this->propertyid)
                ->whereIn('folionodocid', $nullroomocc)
                ->whereNot('billno', '0')
                ->whereNull('settledate')
                ->groupBy('folionodocid')
                ->get(['roomno']);

            if ($searchpay->isNotEmpty()) {
                $totalroom = $searchpay->pluck('roomno')->implode(', ');
                return back()->with('error', 'Please Settle Bill For Rooms: ' . $totalroom);
            }


            $todayscheckout = DB::table('roomocc')
                ->select('roomocc.*', 'enviro_general.ncur')
                ->leftJoin('enviro_general', 'enviro_general.propertyid', '=', 'roomocc.propertyid')
                ->where('roomocc.depdate', DB::raw('enviro_general.ncur'))
                ->whereNull('roomocc.type')
                ->where('roomocc.propertyid', $this->propertyid)
                ->get();

            foreach ($todayscheckout as $row) {
                $updatedep = Carbon::parse($request->input('ncurdate'))->addDays(1)->format('Y-m-d');
                $uproomocc = [
                    'depdate' => $updatedep,
                ];
                DB::table('roomocc')->where('propertyid', $this->propertyid)->update($uproomocc);
            }

            $updateData = [
                'Cancel' => 'Y',
                'Canceldate' => $ncurdateorg,
                'U_Name' => 'NOSHOW',
                'u_updatedt' => $this->currenttime,
            ];

            $updateQuery = DB::table('grpbookingdetails')
                ->where('Cancel', 'N')
                ->where('ArrDate', $ncurdateorg)
                ->where('ContraDocId', '')
                ->whereNotExists(function ($query) use ($ncurdateorg) {
                    $query->select(DB::raw(1))
                        ->from('guestfolio')
                        ->whereColumn('grpbookingdetails.BookingDocId', 'guestfolio.docid')
                        ->where('Vdate', $ncurdateorg);
                })
                ->update($updateData);

            $service = app(AccountPosting::class);
            $service->accountpoststore($ncurdateorg, $ncurdateorg);

            $checkedrooms = RoomOcc::where('propertyid', $this->propertyid)->whereNull('type')->get();
            if ($checkedrooms) {
                foreach ($checkedrooms as $row) {
                    RoomMast::where('propertyid', $this->propertyid)->where('rcode', $row->roomno)->where('type', 'RO')->where('inclcount', 'Y')
                        ->update(['room_stat' => 'D']);
                }
            }

            if (date('m-d', strtotime($this->ncurdate)) == '03-31') {
                DB::commit();
                return back()->with('success', 'Night Audit Completed');
            }

            $nlog = new NightAuditLog();
            $nlog->propertyid = $this->propertyid;
            $nlog->ncurdate = $this->ncurdate;
            $nlog->narration = 'Night Audit';
            $nlog->u_name = Auth::user()->u_name;
            $nlog->u_entdt = $this->currenttime;
            $nlog->save();

            DB::table('enviro_general')
                ->where('propertyid', $this->propertyid)
                ->update([
                    'ncur' => $ncurdate,
                    'u_updatedt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'e',
                ]);
            DB::commit();
            return redirect()->route('logout');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unable to Update Night Audit!');
        }
    }

    public function opennightaudit2()
    {
        $permission = revokeopen(191113);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        return view('property.nightaudit2', ['ncurdate' => $ncurdate]);
    }

    public function submitnightaudit2(Request $request)
    {
        $permission = revokeopen(191113);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ncurdate = Carbon::parse($request->input('ncurdate'))->subDays(1)->format('Y-m-d');
        try {

            $nlog = new NightAuditLog();
            $nlog->propertyid = $this->propertyid;
            $nlog->ncurdate = $this->ncurdate;
            $nlog->narration = 'Reverse Night Audit';
            $nlog->u_name = Auth::user()->u_name;
            $nlog->u_entdt = $this->currenttime;
            $nlog->save();

            DB::table('enviro_general')
                ->where('propertyid', $this->propertyid)
                ->update([
                    'ncur' => $ncurdate,
                    'u_updatedt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'e',
                ]);
            return redirect()->route('logout');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Night Audit!');
        }
    }

    public function openchangeprofile(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');

        $roomocc = RoomOcc::where('docid', $docid)->first();

        $guestprofdata = DB::table('guestprof')
            ->select('guestprof.*', 'guestprof.city as citycode', 'guestfolio.*')
            ->join('guestfolio', 'guestfolio.guestprof', '=', 'guestprof.guestcode')
            ->where('guestprof.guestcode', $roomocc->guestprof)
            ->where('guestfolio.guestprof', $roomocc->guestprof)
            ->where('guestprof.propertyid', $this->propertyid)
            ->first();

        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)
            ->orderBy('cityname', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();

        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $billingAccount = DB::table('subgroup')->where('propertyid', $this->propertyid)->where('sub_code', $guestprofdata->billingAccount)->first();
        return view('property.changeprofile', [
            'data' => $guestprofdata,
            'citydata' => $citydata,
            'nationalitydata' => $nationalitydata,
            'countrydata' => $countrydata,
            'gueststatus' => $gueststatus,
            'company' => $company,
            'billingAccount' => $billingAccount
        ]);
    }

    public function openammendstay(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $sno = $request->query('sno');
        $roomoccdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)
            ->where('sno1', $sno1)->where('sno', $sno)
            ->first();
        $depdate = $roomoccdata->depdate;
        $nextdate = date('Y-m-d', strtotime($depdate . ' +1 day'));
        return view('property.ammendstay', [
            'data' => $roomoccdata,
            'nextdate' => $nextdate,
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function updateammendstay(Request $request)
    {
        $updatedata = [
            'depdate' => $request->input('departuredate'),
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];
        $chckindate = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))
            ->where('sno1', $request->input('sno1'))->where('sno', $request->input('sno'))
            ->value('chkindate');
        if ($updatedata['depdate'] <= $chckindate) {
            return response()->json(['message' => 'Departure Date can not be earlier than checkin date!'], 500);
        } else {
            DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $request->input('docid'))
                ->where('sno1', $request->input('sno1'))->where('sno', $request->input('sno'))
                ->update($updatedata);
            return redirect('autorefreshmain');
        }
    }

    public function openguestledger(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $guestname = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->value('name');
        $this->ExportTable();
        $this->DownloadTable('guestledger', 'Guest Ledger Data For ' . $guestname . ' Analysis HMS', [0, 1, 2, 3, 4, 5, 6], [1, 2, 3, 4, 5]);
        $fetchmaxsno = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', $sno1)->max('sno');
        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        if ($rocc) {
            $paychargedata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)
                ->orderBy('vdate', 'ASC')->orderBy('vno', 'ASC')->orderBy('vtype', 'ASC')->orderBy('sno', 'ASC')->get();
        } else {
            $paychargedata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('sno1', $sno1)
                ->orderBy('vdate', 'ASC')->orderBy('vno', 'ASC')->orderBy('vtype', 'ASC')->orderBy('sno', 'ASC')->get();
        }

        return view('property.guestledger', [
            'data' => $paychargedata
        ]);
    }


    public function openbillprint(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $sno = $request->query('sno');
        // echo $sno;
        // exit;

        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();

        if ($rocc) {
            $paychargedata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)
                ->orderBy('vdate', 'ASC')->orderBy('vno', 'ASC')->orderBy('sno', 'ASC')->get();
        } else {
            $paychargedata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('sno1', $sno1)
                ->orderBy('vdate', 'ASC')->orderBy('vno', 'ASC')->orderBy('sno', 'ASC')->get();
        }

        if ($paychargedata->isEmpty()) {
            return;
        }

        // return $docid;

        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $roomoccdata = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('docid', $docid)->where('sno1', $sno1)
            ->where('sno', $sno)->first();
        $guestprof = GuestProf::where('propertyid', $this->propertyid)->where('guestcode', $roomoccdata->guestprof)->first();

        $totaldebit = 0;
        $totalcredit = 0;
        foreach ($paychargedata as $data) {
            $totaldebit += $data->amtdr;
            $totalcredit += $data->amtcr;
        }
        $onamt = $paychargedata[0]->onamt;
        $billamt = str_replace(',', '', number_format($totaldebit - $totalcredit, 2));
        $enviro_form = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();

        return view('property.billprint', [
            'data' => $paychargedata,
            'docid' => $docid,
            'sno1' => $sno1,
            'sno' => $sno,
            'company' => $companydata,
            'roomoccdata' => $roomoccdata,
            'guestprof' => $guestprof,
            'billamt' => $billamt,
            'onamt' => $onamt,
            'enviro_form' => $enviro_form,
        ]);
    }

    public function openbillreprint(Request $request)
    {
        $permission = revokeopen(141115);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->where('role', 'Property')->first();

        if ($request->billno != '') {
            $latestbillno = $request->billno;
        } else {
            $latestbillno = Paycharge::where('propertyid', $this->propertyid)->where('vprefix', date('Y', strtotime($this->ncurdate)))
                ->whereNull('modeset')
                ->max('billno');
        }

        $enviro_form = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        $years = DateHelper::Uniqueyears($this->propertyid);

        return view('property.billreprint', [
            'company' => $companydata,
            'latestbillno' => $latestbillno,
            'enviro_form' => $enviro_form,
            'years' => $years,
            'year' => $request->year ?? ''
        ]);
    }

    public function submitbillprint(Request $request)
    {
        $validate = $request->validate([
            'sno1' => 'required',
            'docid' => 'required',
        ]);

        $sno1 = $request->input('sno1');
        $sno = $request->sno;
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
            ->where('roomocc.sno', $sno)
            ->first();

        // $paycharger = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $folionodocid)->where('sno', $sno)
        //     ->where('sno1', $sno1)->first();

        $paycharger = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $folionodocid)
            ->where('sno1', $sno1)->first();

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
                        ->whereNot('roomocc.type', 'O')
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

    // public function submitbillprint(Request $request)
    // {
    //     $validate = $request->validate([
    //         'sno1' => 'required',
    //         'docid' => 'required',
    //     ]);
    //     $count = 50;
    //     for ($i = 1; $i <= $count; $i++) {
    //         $roomcharge = $request->input('room_charge_' . $i);
    //         $paydocid = $request->input('paydocid' . $i);
    //         $paysno = $request->input('paysno' . $i);
    //         $paysnoone = $request->input('paysnoone' . $i);
    //         if ($roomcharge !== null) {
    //             $updata = [
    //                 'amtdr' => $request->input('room_charge_' . $i),
    //                 'onamt' => $request->input('payonamt' . $i),
    //                 'billamount' => $request->input('paybillamt' . $i),
    //                 'u_updatedt' => $this->currenttime,
    //             ];

    //             Paycharge::where('propertyid', $this->propertyid)->where('docid', $paydocid)->where('sno', $paysno)
    //                 ->where('sno1', $paysnoone)->update($updata);
    //         }
    //     }
    // }

    public function submitbillreprint(Request $request)
    {
        $validate = $request->validate([
            'sno1' => 'required',
            'docid' => 'required',
        ]);
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $billno = $request->input('billno');
        $updata = [
            'billno' => $billno,
            'split' => $request->input('split'),
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
        ];

        $updatedata = [
            'u_updatedt' => $this->currenttime,
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
            'guestname' => $request->input('name'),
            'foliono' => $request->input('folioNo'),
            'billdate' => $ncurdate,
            'billno' => $billno,
            'billamt' => $request->input('billamt'),
            'status' => 'Settle',
        ];
        try {
            $updateing = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $request->input('docid'))->where('sno1', $request->input('sno1'))->update($updata);
            DB::table('fombilldetails')->where('propertyid', $this->propertyid)->where('folionodocid', $request->input('docid'))->update($updatedata);
            return redirect('company')->with('success', 'Bill Reprint Successfully');
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable To Update Bill Reprint!'], 500);
        }
    }

    public function billcancel(Request $request)
    {
        try {
            $isAjax = $request->ajax();

            $docid = $request->input('docid');
            $sno1 = $request->input('sno1');
            $reason = $request->input('cancelreason') ?? '';

            $rocc = Roomocc::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->where('leaderyn', 'Y')
                ->first();

            // return $rocc;

            $fomupdata = [
                'cancelremark' => $reason,
                'status' => 'Cancel',
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];

            $payupdatedata = [
                'billno' => '0',
                'split' => 1,
            ];

            $roundid = 'ROFF' . $this->propertyid;

            if ($rocc) {
                $fetchbillno = DB::table('paycharge')
                    ->where('propertyid', $this->propertyid)
                    ->where('folionodocid', $rocc->docid)
                    ->where('msno1', $rocc->sno1)
                    ->value('billno');

                DB::table('paycharge')->where('msno1', $rocc->sno1)
                    ->where('folionodocid', $rocc->docid)
                    ->where('paycode', $roundid)->delete();

                DB::table('fombilldetails')
                    ->where('propertyid', $this->propertyid)
                    ->where('folionodocid', $rocc->docid)
                    ->where('billno', $fetchbillno)
                    ->update($fomupdata);

                DB::table('paycharge')
                    ->where('propertyid', $this->propertyid)
                    ->where('folionodocid', $rocc->docid)
                    ->where('msno1', $rocc->sno1)
                    ->update($payupdatedata);

                return back()->with('success', 'Bill Cancel Successfully');
            } else {
                // $fetchbillno = DB::table('paycharge')
                //     ->where('propertyid', $this->propertyid)
                //     ->where('folionodocid', $request->input('docid'))
                //     ->where('sno1', $request->input('sno1'))
                //     ->value('billno');

                $fetchbillno = FomBillDetail::where('propertyid', $this->propertyid)->where('folionodocid', $request->input('docid'))->where('sno1', $request->input('sno1'))->value('billno');

                $fomupdata = [
                    'cancelremark' => $request->input('cancelreason') ?? '',
                    'status' => 'Cancel',
                    'u_updatedt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'e',
                ];

                $payupdatedata = [
                    'billno' => '0',
                    'split' => 1,
                ];

                // return $fetchbillno;

                $roundid = 'ROFF' . $this->propertyid;
                $delpaychargeround = DB::table('paycharge')->where('sno1', $request->input('sno1'))->where('folionodocid', $request->input('docid'))->where('paycode', $roundid)->delete();
                $fombilldetailsupdate = DB::table('fombilldetails')
                    ->where('propertyid', $this->propertyid)
                    ->where('folionodocid', $request->input('docid'))
                    ->where('billno', $fetchbillno)
                    ->update($fomupdata);

                $updatepaycharge = DB::table('paycharge')
                    ->where('propertyid', $this->propertyid)
                    ->where('folionodocid', $request->input('docid'))
                    ->where('sno1', $request->input('sno1'))
                    ->update($payupdatedata);

                return back()->with('success', 'Bill Cancel Successfully');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while cancelling the bill');
        }
    }
    public function getroomoccdata(Request $request)
    {
        $docid = $request->input('docid');
        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        $sno1 = $request->input('sno1');

        if ($rocc) {
            $adult = RoomOcc::where('docid', $docid)
                ->where('propertyid', $this->propertyid)
                ->sum('adult');
            $children = RoomOcc::where('docid', $docid)
                ->where('propertyid', $this->propertyid)
                ->sum('children');
        } else {
            $adult = RoomOcc::where('docid', $docid)
                ->where('propertyid', $this->propertyid)
                ->value('adult');
            $children = RoomOcc::where('docid', $docid)
                ->where('propertyid', $this->propertyid)
                ->value('children');
        }

        $roomocc = DB::table('roomocc')
            ->select(
                'roomocc.*',
                DB::raw('SUM(roomocc.adult) as adultsum'),
                'paycharge.*',
                'guestfolio.company as companycode',
                'guestfolio.travelagent as guesttravel',
                'roomocc.roomno as roomkanam',
                'room_cat.name as categname',
                'guestprof.nationality',
                'guestprof.city_name',
                'guestprof.mobile_no',
                'guestprof.state_name',
                'plan_mast.name as plankanam',
                'guestfolio.add1',
                'guestfolio.add2'
            )
            ->join('paycharge', 'paycharge.folionodocid', '=', 'roomocc.docid')
            ->join('room_cat', 'room_cat.cat_code', '=', 'roomocc.roomcat')
            ->join('guestprof', 'guestprof.guestcode', '=', 'roomocc.guestprof')
            ->join('guestfolio', 'guestfolio.docid', '=', 'roomocc.docid')
            ->leftJoin('plan_mast', 'plan_mast.pcode', '=', 'roomocc.plancode')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'paycharge.comp_code')
            ->where(function ($myquery) {
                $myquery->whereNotNull('paycharge.comp_code')
                    ->orWhereNull('paycharge.comp_code');
            })
            ->where('roomocc.docid', $docid)
            ->where('roomocc.sno1', $sno1)
            ->where('roomocc.propertyid', $this->propertyid)
            ->where(function ($query) {
                $query->whereNotNull('roomocc.plancode')
                    ->orWhereNull('roomocc.plancode');
            })->where(function ($querys) {
                $querys->whereNull('roomocc.type')
                    ->orWhere('roomocc.type', 'O');
            })
            ->first();

        $data = [
            'roomocc' => $roomocc,
            'adult' => $adult,
            'children' => $children,
        ];

        return json_encode($data);
    }

    public function getsubgroupdata(Request $request)
    {
        $comp_code = $request->input('comp_code');
        $subgroupdata = DB::table('subgroup')
            ->select('subgroup.name as subname', 'subgroup.citycode', 'subgroup.address as subaddress', 'subgroup.gstin as subgstin', 'cities.cityname', 'cities.state as substatecode', 'states.name as substatename')
            ->leftJoin('cities', 'cities.city_code', '=', 'subgroup.citycode')
            ->leftJoin('states', 'states.state_code', '=', 'cities.state')
            ->where('subgroup.sub_code', $comp_code)
            ->where('subgroup.propertyid', $this->propertyid)
            ->first();
        return json_encode($subgroupdata);
    }

    public function gettraveldata(Request $request)
    {
        $travelcode = $request->input('travelcode');
        $subgroupdata = DB::table('subgroup')
            ->select('subgroup.name as travelname', 'subgroup.citycode', 'subgroup.address as traveladdress', 'subgroup.gstin as travelgstin', 'cities.cityname', 'cities.state as travelstatecode', 'states.name as travelstatename')
            ->leftJoin('cities', 'cities.city_code', '=', 'subgroup.citycode')
            ->leftJoin('states', 'states.state_code', '=', 'cities.state')
            ->where('subgroup.sub_code', $travelcode)
            ->where('subgroup.propertyid', $this->propertyid)
            ->first();
        return json_encode($subgroupdata);
    }

    public function getamountfetch(Request $request)
    {
        DB::beginTransaction();
        try {
            $docid = $request->input('docid');
            $sno1 = $request->input('sno1');
            $sno = $request->input('sno');
            $splitval = $request->input('splitval');
            $totalsumdebit = str_replace(',', '', $request->input('totalsumdebit'));
            $totalroomcharge = str_replace(',', '', $request->input('totalroomcharge'));
            $onamttotals = str_replace(',', '', $request->input('onamttotals'));
            $billamount = $request->input('billamount');

            $rooms = RoomOcc::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->groupBy('roomno')
                ->get();

            $data = DB::table('paycharge')
                ->select(
                    'revmast.name',
                    DB::raw('SUM(paycharge.taxper) AS total_taxper'),
                    DB::raw('SUM(paycharge.amtcr) AS total_amtcr')
                )
                ->join('revmast', 'paycharge.paycode', '=', 'revmast.rev_code')
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.propertyid', $this->propertyid)
                ->where('paycharge.sno1', $sno1)
                ->where('paycharge.taxcondamt', '!=', 0)
                ->groupBy('revmast.name')
                ->get();

            $paydata = DB::table('paycharge')
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->first();

            $chkvpfb = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', 'BCNT')
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->lockForUpdate()
                ->first();

            if (!$chkvpfb) {
                throw new Exception('No valid voucher prefix found');
            }

            $vprefixb = $chkvpfb->prefix;
            $bcntno = $chkvpfb->start_srl_no + 1;

            $year = date('Y', strtotime($this->ncurdate));
            $nextyear = $year + 1;
            $divcode = DB::table('company')
                ->where('propertyid', $this->propertyid)
                ->value('division_code');

            $invoiceno = $divcode
                ? $divcode . '/' . $year . '-' . substr($nextyear, -2) . '/' . $bcntno
                : 'BCNT/' . $year . '-' . substr($nextyear, -2) . '/' . $bcntno;

            $rocc = Roomocc::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->where('leaderyn', 'Y')
                ->first();

            $updata = [
                'billno' => $bcntno,
                'split' => '1',
                'u_updatedt' => $this->currenttime,
            ];

            if ($rocc) {
                Paycharge::where('propertyid', $this->propertyid)
                    ->where('folionodocid', $docid)
                    ->where('msno1', $rocc->sno1)
                    ->update($updata);

                $data2 = DB::table('paycharge')
                    ->select('revmast.name', DB::raw('SUM(paycharge.amtdr) as taxsum'))
                    ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                    ->where('paycharge.folionodocid', $docid)
                    ->where('paycharge.msno1', $rocc->sno1)
                    ->where('paycharge.split', $splitval)
                    ->where('revmast.field_type', 'T')
                    ->groupBy('revmast.name')
                    ->get();
                $msno1 = $rocc->sno1;
            } else {
                Paycharge::where('propertyid', $this->propertyid)
                    ->where('folionodocid', $docid)
                    ->where('sno1', $sno1)
                    ->update($updata);

                $data2 = DB::table('paycharge')
                    ->select('revmast.name', DB::raw('SUM(paycharge.amtdr) as taxsum'))
                    ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                    ->where('paycharge.folionodocid', $docid)
                    ->where('paycharge.sno1', $sno1)
                    ->where('paycharge.split', $splitval)
                    ->where('revmast.field_type', 'T')
                    ->groupBy('revmast.name')
                    ->get();
                $msno1 = 0;
            }

            DB::table('paycharge')
                ->where('propertyid', $this->propertyid)
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->where('modeset', 'S')
                ->update(['billno' => 0]);

            $roomocc = RoomOcc::where('propertyid', $this->propertyid)
                ->where('sno1', $sno1)
                ->where('docid', $docid)
                ->first();

            $insertdatafom = [
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'propertyid' => $this->propertyid,
                'u_ae' => 'a',
                'guestname' => $roomocc->name,
                'foliono' => $roomocc->folioNo,
                'folionodocid' => $docid,
                'billdate' => $this->ncurdate,
                'billno' => $bcntno,
                'sno1' => $sno1,
                'billamt' => $billamount ?? '0.00',
                'status' => 'settle',
            ];
            DB::table('fombilldetails')->insert($insertdatafom);

            $sumfieldc = DB::table('paycharge')
                ->join('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.sno1', $sno1)
                ->where('revmast.field_type', 'C')
                ->whereNot('paycharge.paycode', 'RMCH' . $this->propertyid)
                ->whereNot('paycharge.paycode', 'ROFF' . $this->propertyid)
                ->sum('paycharge.amtdr');

            $creditsum = DB::table('paycharge')
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->whereNull('modeset')
                ->sum('amtcr');

            $taxnames = $data2->pluck('name')->toArray();
            $totaltax = $data2->pluck('taxsum')->toArray();

            $totalcredit = $data->sum('total_amtcr');
            $betotal = $onamttotals;
            $toalaftertaxadd = floatval($betotal) + array_sum($totaltax);
            $difference = $toalaftertaxadd - $creditsum;

            $envfom = EnviroFom::where('propertyid', Auth::user()->propertyid)->first();
            $datacc = calculateRoundOff($difference, $envfom->roundofftype);
            // LOG::info('roundoff: ' . json_encode($datacc));
            // $netamount = str_replace(',', '', number_format($difference, 2));


            // $fixnum = (substr($netamount, -2) == 00 ? '0.00' : 100 - substr($netamount, -2));
            // $roundoff = is_int($fixnum) ? '0.' . sprintf('%02d', $fixnum) : $fixnum;

            $rev_codechk = 'ROFF' . $this->propertyid;
            $chkexistpay = DB::table('paycharge')
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->where('paycode', $rev_codechk)
                ->first();

            if (isset($datacc['roundoff']) && !$chkexistpay) {
                $vtype = 'REV';
                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->whereDate('date_from', '<=', $this->ncurdate)
                    ->whereDate('date_to', '>=', $this->ncurdate)
                    ->lockForUpdate()
                    ->first();

                $start_srl_no = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                $sno = DB::table('paycharge')
                    ->where('folionodocid', $docid)
                    ->where('sno1', $sno1)
                    ->max('sno');

                $rev_code = 'ROFF' . $this->propertyid;
                $revmast = DB::table('revmast')
                    ->where('propertyid', $this->propertyid)
                    ->where('rev_code', $rev_code)
                    ->first();

                $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

                $insertpaydata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'sno' => $sno + 1,
                    'sno1' => $paydata->sno1,
                    'msno1' => $msno1,
                    'vtype' => $vtype,
                    'vno' => $start_srl_no,
                    'vprefix' => $vprefix,
                    'vdate' => $this->ncurdate,
                    'vtime' => date('H:i:s'),
                    'guestprof' => $paydata->guestprof,
                    'comp_code' => $paydata->comp_code,
                    'travel_agent' => $paydata->travel_agent,
                    'comments' => $revmast->name,
                    'paycode' => $revmast->rev_code,
                    'amtcr' => 0.00,
                    'amtdr' => $datacc['roundoff'],
                    'tipamt' => $paydata->tipamt,
                    'roomcat' => $paydata->roomcat,
                    'roomtype' => $paydata->roomtype,
                    'roomno' => $paydata->roomno,
                    'foliono' => $paydata->foliono,
                    'cardno' => $paydata->cardno,
                    'cardholder' => $paydata->cardholder,
                    'chqno' => $paydata->chqno,
                    'chqdate' => $paydata->chqdate,
                    'expdate' => $paydata->expdate,
                    'bookno' => $paydata->bookno,
                    'restcode' => $paydata->restcode,
                    'billamount' => $datacc['billamt'] ?? '0.00',
                    'contraid' => $paydata->contraid,
                    'dbtchkin' => $paydata->dbtchkin,
                    'taxper' => 0,
                    'onamt' => 0.00,
                    'split' => $paydata->split,
                    'modeset' => 'S',
                    'billno' => $paydata->billno,
                    'settledate' => $paydata->settledate,
                    'batchno' => $paydata->batchno,
                    'plancharge' => $paydata->plancharge,
                    'fixedchargecode' => $paydata->fixedchargecode,
                    'relatdfoliono' => $paydata->relatdfoliono,
                    'folionodocid' => $paydata->folionodocid,
                    'refno' => $paydata->refno,
                    'plancode' => $paydata->plancode,
                    'seqno' => $paydata->seqno,
                    'relatedfolionodocid' => $paydata->relatedfolionodocid,
                    'refdocid' => $paydata->refdocid,
                    'remarks' => $paydata->remarks,
                    'au_name' => $paydata->au_name,
                    'au_updatedt' => $paydata->au_updatedt,
                    'taxcondamt' => 0.00,
                    'taxstru' => $paydata->taxstru,
                    'agac' => $paydata->agac,
                    'txnno' => $paydata->txnno,
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                DB::table('paycharge')->insert($insertpaydata);
                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtype)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');
            }

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', 'BCNT')
                ->where('prefix', $vprefixb)
                ->increment('start_srl_no');

            $retdata = [
                'sumfieldc' => $sumfieldc,
                'totalroomcharge' => $totalroomcharge,
                'taxname' => $taxnames,
                'taxedamount' => $totaltax,
                'toalaftertaxadd' => str_replace(',', '', number_format($toalaftertaxadd, 2)),
                'totalcredit' => str_replace(',', '', number_format($totalcredit, 2)),
                'netamount' => $datacc['billamt'],
                'betotal' => $betotal,
                'invoiceno' => $invoiceno,
                'roundoff' => $datacc['roundoff'],
                'creditsum' => $creditsum,
                'totalsumdebit' => $totalsumdebit,
                'rooms' => $rooms
            ];

            DB::commit();
            return response()->json($retdata);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function getamountfetch2(Request $request)
    {
        $docid = $request->input('docid');
        $sno1 = $request->input('sno1');
        $billno = $request->input('billno');
        $username = Paycharge::where('propertyid', $this->propertyid)->where('billno', $billno)->first();
        $splitval = $request->input('splitval');
        $totalsumdebit = str_replace(',', '', $request->input('totalsumdebit'));
        $totalbalance = str_replace(',', '', $request->input('totalbalance'));
        $totalroomcharge = str_replace(',', '', $request->input('totalroomcharge'));

        $rooms = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->groupBy('roomno')->get();
        $rocc = RoomOcc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        $payments = Paycharge::where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('modeset', 'S')
            ->whereNot('paycode', 'ROFF' . $this->propertyid)->get();
        $pays = [];
        foreach ($payments as $pay) {
            $pays[] = [
                'name' => $pay->paytype,
                'amt' => $pay->amtcr,
            ];
        }

        $igncode = [];
        $revmasttax = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'T')->where('type', 'Cr')->get();
        foreach ($revmasttax as $row) {
            $igncode[] = $row->rev_code;
        }

        if ($rocc) {
            $taxes = Paycharge::select(
                'revmast.name',
                'paycharge.paycode',
                'paycharge.taxper',
                DB::raw('SUM(paycharge.amtdr) as amtdr'),
                DB::raw('SUM(paycharge.onamt) as onamt')
            )
                ->leftJoin('revmast', function ($join) {
                    $join->on('revmast.rev_code', '=', 'paycharge.paycode')
                        ->where('revmast.propertyid', $this->propertyid);
                })
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.split', $splitval)
                ->where('paycharge.msno1', $rocc->sno1)
                ->whereIn('paycharge.paycode', $igncode)
                ->groupBy('paycharge.taxper')
                ->groupBy('paycharge.paycode')
                ->get();

            $data2 = DB::table('paycharge')
                ->select('revmast.name', DB::raw('SUM(paycharge.amtdr) as taxsum'))
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.msno1', $rocc->sno1)
                ->where('paycharge.split', $splitval)
                ->where('revmast.field_type', 'T')
                ->groupBy('revmast.name')
                ->get();

            $creditsum = DB::table('paycharge')
                ->where('folionodocid', $docid)
                ->where('msno1', $rocc->sno1)
                ->where('billno', $billno)
                ->sum('amtcr');
        } else {
            $taxes = Paycharge::select(
                'revmast.name',
                'paycharge.paycode',
                'paycharge.taxper',
                DB::raw('SUM(paycharge.amtdr) as amtdr'),
                DB::raw('SUM(paycharge.onamt) as onamt')
            )
                ->leftJoin('revmast', function ($join) {
                    $join->on('revmast.rev_code', '=', 'paycharge.paycode')
                        ->where('revmast.propertyid', $this->propertyid);
                })
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.split', $splitval)
                ->where('paycharge.sno1', $sno1)
                ->whereIn('paycharge.paycode', $igncode)
                ->groupBy('paycharge.taxper')
                ->groupBy('paycharge.paycode')
                ->get();

            $data2 = DB::table('paycharge')
                ->select(
                    'revmast.name',
                    DB::raw('SUM(paycharge.amtdr) as taxsum'),
                    DB::raw('SUM(paycharge.taxper) as taxpersum')
                )
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.sno1', $sno1)
                ->where('paycharge.split', $splitval)
                ->where('revmast.field_type', 'T')
                ->groupBy('revmast.name')
                ->get();

            $creditsum = DB::table('paycharge')
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->where('billno', $billno)
                ->where('split', $splitval)
                ->sum('amtcr');
            $msno1 = 0;
        }


        $betotal = $totalroomcharge;

        $taxnames = [];
        $totaltax = [];
        foreach ($data2 as $row) {
            $taxnames[] = $row->name;
            $totaltax[] = $row->taxsum;
        }

        $toalaftertaxadd = floatval($betotal) + array_sum($totaltax);
        $difference = $toalaftertaxadd - $creditsum;
        $formatted_difference = number_format($difference, 2);
        // $netamount = str_replace(',', '', $formatted_difference);
        // $fixnum = (substr($netamount, -2) == 00 ? '0.00' : 100 - substr($netamount, -2));
        // if (is_int($fixnum)) {
        //     $roundoff = '0.' . $fixnum;
        // } else {
        //     $roundoff = $fixnum;
        // }

        $envfom = EnviroFom::where('propertyid', Auth::user()->propertyid)->first();
        $datacc = calculateRoundOff($difference, $envfom->roundofftype);
        // LOG::info('roundoff: ' . json_encode($datacc));
        // $netamount = str_replace(',', '', number_format($difference, 2));


        // $fixnum = (substr($netamount, -2) == 00 ? '0.00' : 100 - substr($netamount, -2));
        // $roundoff = is_int($fixnum) ? '0.' . sprintf('%02d', $fixnum) : $fixnum;
        $rofid = 'ROFF' . $this->propertyid;
        $fetchfombill = DB::table('fombilldetails')->where('propertyid', $this->propertyid)->where('billno', $billno)
            ->where('folionodocid', $docid)->where('status', 'settle')->first();
        $billamt = $fetchfombill->billamt;
        $fetchifexist = DB::table('paycharge')->where('folionodocid', $docid)->where('sno1', $rocc->sno1 ?? $sno1)->where('paycode', $rofid)->first();
        if ($fetchifexist) {
            $amtdrr = $fetchifexist->amtdr;
        }
        if ($totalbalance != $billamt && isset($datacc['roundoff'])) {
            DB::table('paycharge')->where('folionodocid', $docid)->where('sno1', $rocc->sno1 ?? $sno1)->where('paycode', $rofid)->delete();
            $vtype = 'REV';
            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->first();

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;
            $paydata = DB::table('paycharge')->where('folionodocid', $docid)->where('sno1', $rocc->sno1 ?? $sno1)->first();
            $sno = DB::table('paycharge')->where('folionodocid', $docid)->where('sno1', $rocc->sno1 ?? $sno1)->max('sno');
            $rev_code = 'ROFF' . $this->propertyid;
            $revmast = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $rev_code)->first();
            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;
            $insertpaydata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'sno' => $sno + 1,
                'sno1' => $paydata->sno1,
                'msno1' => $rocc->sno1 ?? 0,
                'vtype' => $vtype,
                'vno' => $start_srl_no,
                'vprefix' => $vprefix,
                'vdate' => $this->ncurdate,
                'vtime' => date('H:i:s'),
                'guestprof' => $paydata->guestprof,
                'comp_code' => $paydata->comp_code,
                'travel_agent' => $paydata->travel_agent,
                'comments' => $revmast->name,
                'paycode' => $revmast->rev_code,
                'amtcr' => 0.00,
                'amtdr' => $datacc['roundoff'],
                'tipamt' => $paydata->tipamt,
                'roomcat' => $paydata->roomcat,
                'roomtype' => $paydata->roomtype,
                'roomno' => $paydata->roomno,
                'foliono' => $paydata->foliono,
                'cardno' => $paydata->cardno,
                'cardholder' => $paydata->cardholder,
                'chqno' => $paydata->chqno,
                'chqdate' => $paydata->chqdate,
                'expdate' => $paydata->expdate,
                'bookno' => $paydata->bookno,
                'restcode' => $paydata->restcode,
                'billno' => $billno,
                'billamount' => $datacc['billamt'],
                'modeset' => 'S',
                'contraid' => $paydata->contraid,
                'dbtchkin' => $paydata->dbtchkin,
                'taxper' => 0,
                'onamt' => 0.00,
                'split' => $paydata->split,
                'settledate' => $paydata->settledate,
                'batchno' => $paydata->batchno,
                'plancharge' => $paydata->plancharge,
                'fixedchargecode' => $paydata->fixedchargecode,
                'relatdfoliono' => $paydata->relatdfoliono,
                'folionodocid' => $paydata->folionodocid,
                'refno' => $paydata->refno,
                'plancode' => $paydata->plancode,
                'seqno' => $paydata->seqno,
                'relatedfolionodocid' => $paydata->relatedfolionodocid,
                'refdocid' => $paydata->refdocid,
                'remarks' => $paydata->remarks,
                'au_name' => $paydata->au_name,
                'au_updatedt' => $paydata->au_updatedt,
                'taxcondamt' => 0.00,
                'taxstru' => $paydata->taxstru,
                'agac' => $paydata->agac,
                'txnno' => $paydata->txnno,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];
            DB::table('paycharge')->insert($insertpaydata);
            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');
        }

        $retdata = [
            'taxname' => $taxnames,
            'taxedamount' => $totaltax,
            'toalaftertaxadd' => str_replace(',', '', number_format($toalaftertaxadd, 2)),
            'totalroomcharge' => $totalroomcharge,
            'netamount' => $datacc['billamt'],
            'roundoff' => $datacc['roundoff'],
            'creditsum' => $creditsum,
            'betotal' => $betotal,
            'u_name' => $username->u_name,
            'paymentname' => $pays,
            'rooms' => $rooms,
            'taxes' => $taxes,
            'igncode' => $igncode
        ];

        return response()->json($retdata);
    }

    public function postsplit(Request $request)
    {
        $docid = $request->input('docid');
        $sno1 = $request->input('sno1');
        $sno = $request->input('sno');
        $split = $request->input('split');

        $updata = [
            'split' => $split,
            'u_updatedt' => $this->currenttime,
            'u_ae' => 'e'
        ];

        Paycharge::where('docid', $docid)->where('sno1', $sno1)->update($updata);
        return response()->json(['message' => 'Split Updated']);
    }

    public function fetchbilldataledger(Request $request)
    {
        $docid = $request->input('docid');
        $sno1 = $request->input('sno1');
        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        if ($rocc) {
            $paychargedata = Paycharge::select('paycharge.*', 'revmast.field_type', 'revmast.nature as revnature')->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.propertyid', $this->propertyid)
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.msno1', $rocc->sno1)
                ->whereNull('paycharge.modeset')
                ->orderBy('paycharge.vdate', 'ASC')
                ->orderBy('paycharge.vno', 'ASC')
                ->orderBy('paycharge.sno1', 'ASC')
                ->orderBy('paycharge.sno', 'ASC')
                ->orderBy('paycharge.roomno', 'ASC')
                ->get();
        } else {
            $paychargedata = Paycharge::select('paycharge.*', 'revmast.field_type', 'revmast.nature as revnature')->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.propertyid', $this->propertyid)
                ->where('paycharge.folionodocid', $docid)
                ->where('paycharge.sno1', $sno1)
                ->whereNull('paycharge.modeset')
                ->orderBy('paycharge.vdate', 'ASC')
                ->orderBy('paycharge.vno', 'ASC')
                ->orderBy('paycharge.sno1', 'ASC')
                ->orderBy('paycharge.sno', 'ASC')
                ->orderBy('paycharge.roomno', 'ASC')
                ->get();
        }
        return json_encode($paychargedata);
    }

    public function billprintview(Request $request)
    {
        $propertyid = $this->propertyid;
        $folionodocid = $request->query('folionodocid');
        $sno1 = $request->query('sno1');
        $json = $request->query('arrdata');
        $tbody = $request->query('tbody');
        $updatingstartsrl = DB::table('voucher_prefix')
            ->where('propertyid', $this->propertyid)
            ->where('v_type', 'BCNT')
            ->update(['start_srl_no' => DB::raw('start_srl_no + 1')]);
        $data = DB::table('voucher_prefix')->where('v_type', 'BCNT')->where('propertyid', $this->propertyid)->max('start_srl_no');
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $year = date('Y', strtotime($ncurdate));
        $nextyear = $year + 1;
        $invoiceno = 'BCNT/' . $year . '-' . substr($nextyear, -2) . '/' . $data;
        $sumfieldc = DB::table('paycharge')
            ->join('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('paycharge.folionodocid', $folionodocid)
            ->where('revmast.field_type', 'C')
            ->where('paycharge.vtype', 'REV')
            ->sum('paycharge.amtdr');
        $arrdata = json_decode($json, true);
        $betotal = str_replace(',', '', number_format($arrdata['onamt'] + $sumfieldc, 2));
        $arrdata['betotal'] = $betotal;

        return view('property.billprintpdf', [
            'propertyid' => $propertyid,
            'invoiceno' => $invoiceno,
            'folionodocid' => $folionodocid,
            'sno1' => $sno1,
            'tbody' => $tbody,
            'arrdata' => $arrdata
        ]);
    }

    public function billreprintview(Request $request)
    {
        $propertyid = $this->propertyid;
        $folionodocid = $request->query('folionodocid');
        $sno1 = $request->query('sno1');
        $tbody = $request->query('tbody');
        $json = $request->query('arrdata');
        $arrdata = json_decode($json, true);
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $year = date('Y', strtotime($ncurdate));
        $nextyear = $year + 1;
        $invoiceno = 'BCNT/' . $year . '-' . substr($nextyear, -2) . '/' . $arrdata['billno'];
        $sumfieldc = DB::table('paycharge')
            ->join('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
            ->where('paycharge.folionodocid', $folionodocid)
            ->where('revmast.field_type', 'C')
            ->where('paycharge.vtype', 'REV')
            ->sum('paycharge.amtdr');
        $betotal = str_replace(',', '', number_format($arrdata['onamt'] + $sumfieldc, 2));
        $arrdata['betotal'] = $betotal;

        return view('property.billprintpdf', [
            'propertyid' => $propertyid,
            'invoiceno' => $invoiceno,
            'folionodocid' => $folionodocid,
            'sno1' => $sno1,
            'tbody' => $tbody,
            'arrdata' => $arrdata
        ]);
    }

    public function getcompdetails(Request $request)
    {
        $propertyid = $request->input('propertyid');
        $data = DB::table('company')
            ->select('company.*', 'enviro_form.logoyn', 'enviro_form.emailyn', 'enviro_form.websiteyn',)
            ->leftJoin('enviro_form', 'enviro_form.propertyid', '=', 'company.propertyid')
            ->where('company.propertyid', $this->propertyid)->first();
        return json_encode($data);
    }

    public function getmaxvoucherbill(Request $request)
    {
        $updatingstartsrl = DB::table('voucher_prefix')
            ->where('propertyid', $this->propertyid)
            ->where('v_type', 'BCNT')
            ->update(['start_srl_no' => DB::raw('start_srl_no + 1')]);
        $data = DB::table('voucher_prefix')->where('v_type', 'BCNT')->where('propertyid', $this->propertyid)->max('start_srl_no');
        return json_encode($data);
    }

    public function getmaxvtype(Request $request)
    {
        $vtype = $request->input('vtype');
        $data = DB::table('voucher_prefix')->where('v_type', $vtype)->where('propertyid', $this->propertyid)->max('start_srl_no') + 1;
        return json_encode($data);
    }

    public function openchangeroom(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $sno = $request->query('sno');
        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $maxsno = DB::table('roomocc')->where('propertyid', $this->propertyid)->where('sno1', $sno1)->where('docid', $docid)->max('sno');

        $roomoccdata = DB::table('roomocc')->select(
            DB::raw("CASE 
            WHEN plandetails.rev_code IS NULL THEN 'N' 
            ELSE 'Y' 
            END AS planedit"),
            'plandetails.rev_code as brev_code',
            'plandetails.taxinc as btaxinc',
            'plandetails.taxstru as btaxstru',
            'plandetails.fixrate as bfixrate',
            'plandetails.planper as bplanper',
            'plandetails.amount as bamount',
            'plandetails.netplanamt as bnetplanamt',
            'plandetails.room_rate_before_tax as broom_rate_before_tax',
            'plandetails.total_rate as btotal_rate',
            'revmast.name as chargename',
            'roomocc.*',
            'roomocc.docid as rodocid',
            'roomocc.name as clientname',
            'guestprof.*',
            'guestfolio.guestprof',
            'guestfolio.nodays',
            'guestfolio.roomcount',
            'guestfolio.company',
            'guestfolio.booking_source',
            'guestprof.complimentry',
            'guestfolio.busssource',
            'guestfolio.travelagent',
            'guestprof.pic_path',
            'plan_mast.pcode',
            'plan_mast.name as planname',
            'plan_mast.room_per as room_perplan',
            'room_mast.rcode',
            'room_mast.name as roomname',
            'guestprof.city',
            'guestprof.add1',
            'guestprof.add2',
            'cities.cityname as nameofcity',
            'cities.zipcode as cityzipcode',
            'guestprof.country_code',
            'guestprof.state_code',
            'states.name as nameofstate',
            'countries.name as nameofcountry',
            'countries.nationality as nameofnationality',
            'guestfolio.arrfrom',
            'guestfolio.destination',
            'guestfolio.travelmode',
            'guestfolio.purvisit',
            'guestfolio.rodisc',
            'guestfolio.rsdisc',
            'guestfolio.vehiclenum',
            'guestfolio.remarks',
            'guestfolio.pickupdrop'
        )
            ->leftJoin('plandetails', function ($join) {
                $join->on('plandetails.docid', '=', 'roomocc.docid')
                    ->on('plandetails.sno1', '=', 'roomocc.sno1');
            })
            ->leftJoin('revmast', 'revmast.rev_code', '=', 'plandetails.rev_code')
            ->leftJoin('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
            ->leftJoin('guestfolio', 'roomocc.docid', '=', 'guestfolio.docid')
            ->leftJoin('plan_mast', 'roomocc.plancode', '=', 'plan_mast.pcode')
            ->leftJoin('room_mast', 'roomocc.roomno', '=', 'room_mast.rcode')
            ->leftJoin('cities', 'guestprof.city', '=', 'cities.city_code')
            ->leftJoin('countries', 'guestprof.country_code', '=', 'countries.country_code')
            ->leftJoin('states', 'guestprof.state_code', '=', 'states.state_code')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.docid', $docid)
            ->where('roomocc.sno1', $sno1)
            ->where('roomocc.sno', $sno)
            ->where('roomocc.sno', $maxsno)
            ->first();

        $checkindate = $roomoccdata->chkindate;
        $checkoutdate = $roomoccdata->depdate;
        $propertyid = $this->propertyid;

        // $availrooms = RoomMast::select('room_mast.*')
        //     ->whereNotIn('rcode', function ($query) use ($chkindate, $propertyid) {
        //         $query->select('roomno')
        //             ->from('roomocc')
        //             ->whereNull('chkoutdate')
        //             ->where('propertyid', $propertyid)
        //             ->whereRaw("? >= chkindate AND ? < depdate", [$chkindate, $chkindate]);
        //     })
        //     ->whereNotIn('rcode', function ($query) use ($chkindate, $propertyid) {
        //         $query->select('RoomNo')
        //             ->from('grpbookingdetails')
        //             ->where('Cancel', 'N')
        //             ->where('ContraDocId', '')
        //             ->where('Property_ID', $propertyid)
        //             ->whereRaw("? >= ArrDate AND ? < DepDate", [$chkindate, $chkindate]);
        //     })
        //     ->where('type', 'RO')
        //     ->whereNot('room_stat', 'O')
        //     ->where('inclcount', 'Y')
        //     ->where('propertyid', $propertyid)
        //     ->where('room_cat', $roomoccdata->roomcat)
        //     ->get();
        $cid = $roomoccdata->roomcat;
        $availrooms = DB::table('room_mast as rm')
            ->select('rm.rcode', 'rm.room_cat')
            ->where('rm.propertyid', $propertyid)
            ->where('rm.room_cat', $cid)
            ->whereNotIn('rm.rcode', function ($query) use ($propertyid, $cid, $checkindate, $checkoutdate) {
                $query->select('ro.roomno')
                    ->from('roomocc as ro')
                    ->where('ro.propertyid', $propertyid)
                    ->whereNull('ro.type')
                    ->where('ro.roomcat', $cid)
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
            ->get();

        $plans = PlanMast::where('room_cat', $roomoccdata->roomcat)
            ->where('propertyid', $this->propertyid)
            ->get();

        return view('property.changeroom', [
            'data' => $roomoccdata,
            'availrooms' => $availrooms,
            'roomcat' => $roomcat,
            'plans' => $plans
        ]);
    }

    public function openadvancecharge(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $sno = $request->query('sno');
        // echo $sno1 . ' - ' . $docid;
        // exit;
        $roomoccdata = DB::table('roomocc')
            ->select('roomocc.*', 'guestprof.con_prefix')
            ->join('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.docid', $docid)->where('roomocc.sno1', $sno1)->where('roomocc.sno', $sno)
            ->first();

        $records = DB::table('revmast')
            ->select('revmast.name', 'revmast.nature', 'revmast.rev_code', 'revmast.field_type', 'revmast.flag_type')
            ->selectRaw("CASE WHEN revmast.field_type = 'C' THEN NULL ELSE depart_pay.pay_code END AS pay_code")
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where(function ($query) {
                $query->where('revmast.field_type', '=', 'P')
                    ->orWhere(function ($query) {
                        $query->where('revmast.field_type', '=', 'C')
                            ->where('revmast.flag_type', '=', 'FOM');
                    });
            })
            ->where('revmast.propertyid', '=', $this->propertyid)
            ->where('revmast.active', 'Y')
            ->distinct()
            ->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name', 'ASC')->get();
        $restrooms = DB::table('roomocc')->where('propertyid', $this->propertyid)->whereNot('roomno', $roomoccdata->roomno)->where('type', null)->get();

        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        return view('property.advancecharge', [
            'revdata' => $records,
            'data' => $roomoccdata,
            'restroooms' => $restrooms,
            'roomoccdata' => $roomoccdata,
            'ncurdate' => $ncurdate,
            'company' => $company,
            'companydata' => $companydata
        ]);
    }

    public function fetchadvamt(Request $request)
    {
        $revcode = $request->input('rev_code');
        $amount = DB::table('revmast')->where('propertyid', $this->propertyid)->where('field_type', 'C')->where('rev_code', $revcode)->value('sales_rate');
        $narration = DB::table('revmast')->where('propertyid', $this->propertyid)->where('field_type', 'C')->where('rev_code', $revcode)->value('name');
        $data = [
            'amount' => $amount,
            'narration' => $narration,
        ];
        return json_encode($data);
    }

    public function fetchadvamtpay(Request $request)
    {
        $revcode = $request->input('rev_code');
        $docid = $request->input('docid');
        $sno1 = $request->input('sno1');

        $paydata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)->where('sno1', $sno1)->get();
        $debitamt = 0;
        $creditamt = 0;
        foreach ($paydata as $data) {
            $debitamt += $data->amtdr;
            $creditamt += $data->amtcr;
        }
        $fxdebitamt = str_replace(',', '', number_format($debitamt, 2));
        $fxcreditamt = str_replace(',', '', number_format($creditamt, 2));
        $sum = $fxdebitamt - $fxcreditamt;
        $data = [
            'sum' => round($sum, 2),
        ];
        return json_encode($data);
    }

    public function fetchrevnature(Request $request)
    {
        $revcode = $request->input('rev_code');
        $nature = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $revcode)->value('nature');
        $fieldtype = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $revcode)->value('field_type');
        $name = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $revcode)->value('name');
        $data = [
            'nature' => $nature,
            'fieldtype' => $fieldtype,
            'name' => $name,
        ];
        return json_encode($data);
    }

    public function openroomsettlement(Request $request)
    {
        $docid = $request->query('docid');
        $sno1 = $request->query('sno1');
        $sno = $request->query('sno');
        $roomoccdata = DB::table('roomocc')
            ->select('roomocc.*', 'guestprof.con_prefix')
            ->join('guestprof', 'roomocc.guestprof', '=', 'guestprof.guestcode')
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('roomocc.sno', $sno)
            ->where('roomocc.sno1', $sno1)
            ->where('roomocc.docid', $docid)->first();

        $records = DB::table('revmast')
            ->select('revmast.name', 'revmast.rev_code', 'revmast.nature', 'revmast.field_type', 'revmast.flag_type', 'depart_pay.pay_code')
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where('revmast.field_type', '=', 'P')
            ->where('revmast.propertyid', $this->propertyid)
            ->get();

        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name', 'ASC')->get();
        $restrooms = DB::table('roomocc')->where('propertyid', $this->propertyid)->whereNot('roomno', $roomoccdata->roomno)->where('type', null)->get();

        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $rocc = Roomocc::where('propertyid', $this->propertyid)->where('docid', $docid)->where('leaderyn', 'Y')->first();
        if ($rocc) {
            $tbl = DB::table('paycharge')
                ->select(DB::raw('SUM(amtdr) as amtdr'), DB::raw('SUM(amtcr) as amtcr'), DB::raw('(SUM(amtdr) - SUM(amtcr)) as balance'))
                ->where('folionodocid', $docid)
                ->where('msno1', $rocc->sno1)
                ->first();
        } else {
            $tbl = DB::table('paycharge')
                ->select(DB::raw('SUM(amtdr) as amtdr'), DB::raw('SUM(amtcr) as amtcr'), DB::raw('(SUM(amtdr) - SUM(amtcr)) as balance'))
                ->where('folionodocid', $docid)
                ->where('sno1', $sno1)
                ->first();
        }
        return view('property.roomsettlement', [
            'revdata' => $records,
            'data' => $roomoccdata,
            'restroooms' => $restrooms,
            'roomoccdata' => $roomoccdata,
            'ncurdate' => $ncurdate,
            'company' => $company,
            'companydata' => $companydata,
            'sno1' => $sno1,
            'tbl' => $tbl,
            'money' => '0'
        ]);
    }

    public function submitRoomSettle(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'charge' => 'required',
            'amount' => 'required',
        ]);

        // Constants and frequently used values
        $propertyId = $this->propertyid;
        $docId = $request->input('docid');
        $sno = $request->input('sno');
        $sno1Main = $request->input('sno1main');
        $amount = $request->input('amount');
        $voucherType = 'REC';
        $currentDate = $this->ncurdate;
        $currentTime = $this->currenttime;
        $userName = Auth::user()->u_name;
        $currentHour = date('H:i');

        // Begin transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Get voucher prefix information
            $voucherPrefix = VoucherPrefix::where('propertyid', $propertyId)
                ->where('v_type', $voucherType)
                ->whereDate('date_from', '<=', $currentDate)
                ->whereDate('date_to', '>=', $currentDate)
                ->first();

            if (!$voucherPrefix) {
                throw new \Exception('Voucher prefix not found');
            }

            $voucherNumber = $voucherPrefix->start_srl_no + 1;
            $prefix = $voucherPrefix->prefix;
            $generatedDocId = $propertyId . $voucherType . ' ‎ ‎' . $prefix . ' ‎ ‎ ‎ ' . $voucherNumber;

            // Get room occupancy information
            $roomOccupancy = DB::table('roomocc')
                ->where('propertyid', $propertyId)
                ->where('docid', $docId)
                ->where('sno', $sno)
                ->where('sno1', $sno1Main)
                ->first();

            if (!$roomOccupancy) {
                throw new Exception('Room occupancy record not found');
            }

            // Common update arrays
            $payChargeUpdate = [
                'settledate' => $currentDate,
                'u_updatedt' => $currentTime,
            ];

            $roomOccUpdate = [
                'userchkoutdate' => $currentDate,
                'chkoutuser' => $userName,
                'type' => 'O',
                'chkoutdate' => $currentDate,
                'u_ae' => 'e',
                'chkouttime' => $currentHour,
                'u_updatedt' => $currentTime,
            ];

            $grpBookingUpdate = [
                'chkoutyn' => 'Y',
                'U_AE' => 'e',
                'u_updatedt' => $currentTime,
            ];

            // Process leader room or individual room
            $leaderId = null;
            $billNumber = null;

            $leaderRoomOcc = Roomocc::where('propertyid', $propertyId)
                ->where('docid', $docId)
                ->where('leaderyn', 'Y')
                ->first();

            if ($leaderRoomOcc) {
                $leaderId = $leaderRoomOcc->sno1;
                // echo 'leader';
                // echo $propertyId . ' - ' . $leaderRoomOcc->docid . ' - ' . $leaderId;
                // exit;
                $chkrelatedgroup1 = Paycharge::where('propertyid', $this->propertyid)
                    ->where('folionodocid', $leaderRoomOcc->docid)
                    ->where('msno1', $leaderId)
                    ->groupBy('relatedfolionodocid')
                    ->get();

                $chkrelatedgroup = Paycharge::where('propertyid', $this->propertyid)
                    ->where('folionodocid', $leaderRoomOcc->docid)
                    ->where('msno1', $leaderId)
                    ->whereNotNull('relatedfolionodocid')
                    ->where('relatedfolionodocid', '!=', '')
                    ->groupBy('relatedfolionodocid')
                    ->first();
                $tbl = DB::table('paycharge')
                    ->select(DB::raw('SUM(amtdr) as amtdr'), DB::raw('SUM(amtcr) as amtcr'), DB::raw('(SUM(amtdr) - SUM(amtcr)) as balance'))
                    ->where('folionodocid', $request->input('docid'))
                    ->where('msno1', $leaderRoomOcc->sno1)
                    ->first();
                // var_dump($chkrelatedgroup);
                // exit;

                if (isNull($chkrelatedgroup)) {
                    // echo 'leaderempty';
                    // exit;
                    RoomOcc::where('propertyid', $propertyId)
                        ->where('docid', $leaderRoomOcc->docid)
                        ->update($roomOccUpdate);

                    GrpBookinDetail::where('Property_ID', $propertyId)
                        ->where('ContraDocId', $leaderRoomOcc->docid)
                        ->update($grpBookingUpdate);
                } else {
                    // echo 'leadernotempty';
                    // exit;
                    $relatedDocIds = $chkrelatedgroup1->pluck('relatedfolionodocid');

                    RoomOcc::where('propertyid', $propertyId)
                        ->whereIn('docid', $relatedDocIds)
                        ->update($roomOccUpdate);

                    GrpBookinDetail::where('Property_ID', $propertyId)
                        ->whereIn('ContraDocId', $relatedDocIds)
                        ->update($grpBookingUpdate);
                }

                // exit;

                $billNumber = Paycharge::where('folionodocid', $leaderRoomOcc->docid)
                    ->where('msno1', $leaderId)
                    ->value('billno');

                Paycharge::where('propertyid', $propertyId)
                    ->where('folionodocid', $leaderRoomOcc->docid)
                    ->where('msno1', $leaderId)
                    ->update($payChargeUpdate);

                $rooms = DB::table('roomocc')
                    ->where('propertyid', $propertyId)
                    ->where('docid', $leaderRoomOcc->docid)
                    ->get();

                foreach ($rooms as $row) {
                    RoomMast::where('propertyid', $this->propertyid)->where('rcode', $row->roomno)->where('type', 'RO')->where('inclcount', 'Y')
                        ->update(['room_stat' => 'D']);
                }
            } else {
                // echo 'nonleader';
                // exit;
                $tbl = DB::table('paycharge')
                    ->select(DB::raw('SUM(amtdr) as amtdr'), DB::raw('SUM(amtcr) as amtcr'), DB::raw('(SUM(amtdr) - SUM(amtcr)) as balance'))
                    ->where('folionodocid', $request->input('docid'))
                    ->where('sno1', $request->input('sno1main'))
                    ->first();
                $billNumber = DB::table('paycharge')
                    ->where('folionodocid', $docId)
                    ->where('sno1', $sno1Main)
                    ->value('billno');

                DB::table('paycharge')
                    ->where('propertyid', $propertyId)
                    ->where('folionodocid', $docId)
                    ->where('sno1', $sno1Main)
                    ->update($payChargeUpdate);

                DB::table('roomocc')
                    ->where('propertyid', $propertyId)
                    ->where('docid', $docId)
                    ->where('sno1', $sno1Main)
                    ->where('sno', $sno)
                    ->update($roomOccUpdate);

                GrpBookinDetail::where('Property_ID', $propertyId)
                    ->where('ContraDocId', $docId)
                    ->where('ContraSno', $sno1Main)
                    ->update($grpBookingUpdate);

                $rooms = DB::table('roomocc')
                    ->where('propertyid', $propertyId)
                    ->where('docid', $docId)
                    ->where('sno1', $sno1Main)
                    ->where('sno', $sno)
                    ->get();

                foreach ($rooms as $row) {
                    RoomMast::where('propertyid', $this->propertyid)->where('rcode', $row->roomno)->where('type', 'RO')->where('inclcount', 'Y')
                        ->update(['room_stat' => 'D']);
                }
            }

            // exit;

            // Update bill details
            DB::table('fombilldetails')
                ->where('folionodocid', $docId)
                ->where('billno', $billNumber)
                ->update(['settamt' => $amount]);

            // Process payment charges
            $chargeCount = 0;
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'chargecode') === 0) {
                    $chargeCount++;
                }
            }

            $serialNumber = 1;
            $chargeEntries = [];

            for ($i = 1; $i <= $chargeCount; $i++) {
                $chargeCode = $request->input('chargecode' . $i);
                $chargeAmount = $request->input('amtrow' . $i);

                // Skip empty rows
                if (empty($chargeCode) || empty($chargeAmount) || $chargeAmount == 0) {
                    continue;
                }

                $payCodeInfo = Revmast::where('propertyid', $propertyId)
                    ->where('rev_code', $chargeCode)
                    ->first();

                if (!$payCodeInfo) {
                    continue;
                }

                $chargeEntries[] = [
                    'propertyid' => $propertyId,
                    'docid' => $generatedDocId,
                    'vno' => $voucherNumber,
                    'vtype' => $voucherType,
                    'sno' => $serialNumber,
                    'sno1' => $sno1Main,
                    'msno1' => $leaderId ?? 0,
                    'chqno' => $request->input('checkno') ?: $request->input('referencenoupi'),
                    'cardno' => $request->input('crnumber'),
                    'cardholder' => $request->input('holdername'),
                    'expdate' => $request->input('expdatecr'),
                    'bookno' => $request->input('batchno'),
                    'vdate' => $currentDate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $prefix,
                    'comp_code' => $request->input('compcode' . $i) ?? '',
                    'paycode' => $chargeCode,
                    'paytype' => $payCodeInfo->pay_type ?? '',
                    'comments' => $request->input('chargenarration' . $i),
                    'guestprof' => $roomOccupancy->guestprof,
                    'roomno' => $request->input('rooomoccroomno') ?? $roomOccupancy->roomno,
                    'amtcr' => $chargeAmount,
                    'roomtype' => $roomOccupancy->roomtype,
                    'roomcat' => $roomOccupancy->roomcat,
                    'foliono' => $roomOccupancy->folioNo,
                    'restcode' => 'FOM' . $propertyId,
                    'billamount' => 0.00,
                    'taxper' => 0,
                    'onamt' => 0.00,
                    'folionodocid' => $roomOccupancy->docid,
                    'taxcondamt' => 0,
                    'taxstru' => '',
                    'u_entdt' => $currentTime,
                    'settledate' => $currentDate,
                    'u_name' => $userName,
                    'u_ae' => 'a',
                    'modeset' => 'S',
                ];

                $serialNumber++;
            }

            // Bulk insert charge entries for better performance
            if (!empty($chargeEntries)) {
                DB::table('paycharge')->insert($chargeEntries);
            }

            // Verify inserted records match expected count
            $expectedRows = $request->input('countrows');
            $actualRows = Paycharge::select('paycharge.*', 'revmast.name as revname')
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.propertyid', $propertyId)
                ->where('paycharge.folionodocid', $roomOccupancy->docid)
                ->where('vtype', $voucherType)
                ->whereNotNull('paycharge.paycode')
                ->whereNotNull('paycharge.paytype')
                ->whereNotNull('paycharge.modeset')
                ->where('sno1', $sno1Main)
                ->whereNot('paycharge.amtcr', 0)
                ->count();

            // if ($expectedRows != $actualRows) {
            //     // Clean up incomplete records
            //     Paycharge::where('propertyid', $propertyId)
            //         ->where('vtype', $voucherType)
            //         ->whereNotNull('paycharge.paycode')
            //         ->whereNotNull('paycharge.paytype')
            //         ->whereNotNull('paycharge.modeset')
            //         ->where('folionodocid', $roomOccupancy->docid)
            //         ->where('billno', 0)
            //         ->where('sno1', $sno1Main)
            //         ->delete();

            //     throw new Exception('Row count mismatch');
            // }
            // return 'sagar2';

            // Update voucher prefix
            VoucherPrefix::where('propertyid', $propertyId)
                ->where('v_type', $voucherType)
                ->where('prefix', $prefix)
                ->increment('start_srl_no');

            $guestprof = GuestProf::where('propertyid', $propertyId)
                ->where('docid', $docId)->first();


            // if ($wpenv != null) {
            //     if ($wpenv->checkyn == 'Y' && $wpenv->checkoutmsg != '' && $wpenv->checkouttemplate != '' && $guestprof->mobile_no != '') {
            //         $whatsapp = new WhatsappSend();
            //         $whatsapp->CheckoutSend($tbl->balance, $roomOccupancy->roomno, $roomOccupancy->name, $guestprof->mobile_no);
            //     }
            // }



            // exit;
            DB::commit();
            $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();

            if ($wpenv != null) {
                $mob = GuestProf::where('propertyid', $this->propertyid)->where('docid', $roomOccupancy->docid)->value('mobile_no');
                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->checkoutmsg != '' &&
                    $wpenv->checkoutmsgarray != '' &&
                    $wpenv->checkouttemplate != '' &&
                    $mob != ''
                ) {
                    $checkoutmsgarray = json_decode($wpenv->checkoutmsgarray, true);

                    $msgdata = [];
                    foreach ($checkoutmsgarray as $row) {
                        [$colname, $table] = $row;
                        if (endsWith($colname, 'billamount')) {
                            $value = $tbl->balance;
                        } else {
                            $value = DB::table($table)->where('sno', $sno)->where('sno1', $sno1Main)->where('propertyid', $this->propertyid)->where('docid', $roomOccupancy->docid)->value($colname);
                        }
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $mob, 'Checkout', 'checkouttemplate');
                }

                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->checkoutmsgadmin != '' &&
                    $wpenv->checkoutmsgadminarray != '' &&
                    $wpenv->checkoutmsgadmintemplate != '' &&
                    $wpenv->managementmob != ''
                ) {
                    $checkoutmsgadminarray = json_decode($wpenv->checkoutmsgadminarray, true);

                    $msgdata = [];
                    foreach ($checkoutmsgadminarray as $row) {
                        [$colname, $table] = $row;
                        if (endsWith($colname, 'billamount')) {
                            $value = $tbl->balance;
                        } else {
                            if ($table == 'paycharge') {
                                $value = DB::table($table)->where('vtype', 'REC')->where('sno', $sno)->where('sno1', $sno1Main)->where('propertyid', $this->propertyid)->where('folionodocid', $roomOccupancy->docid)->value($colname);
                            } else {
                                $value = DB::table($table)->where('sno', $sno)->where('sno1', $sno1Main)->where('propertyid', $this->propertyid)->where('docid', $roomOccupancy->docid)->value($colname);
                            }
                        }
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $wpenv->managementmob, 'Checkout Admin', 'checkoutmsgadmintemplate');
                }
            }

            return redirect('autorefreshmain');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Unable To Submit Room Re Settlement: ' . $e->getMessage());
        }
    }

    public function openbillresettlement(Request $request)
    {
        $permission = revokeopen(141116);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $year = date('Y', strtotime($ncurdate));
        $nextyear = $year + 1;
        $latestbillno = Paycharge::where('propertyid', $this->propertyid)->where('vprefix', date('Y', strtotime($this->ncurdate)))
            ->whereNull('modeset')
            ->max('billno');
        $enviro_form = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        $years = DateHelper::Uniqueyears($this->propertyid);
        $records = DB::table('revmast')
            ->select('revmast.name', 'revmast.rev_code', 'revmast.nature', 'revmast.field_type', 'revmast.flag_type', 'depart_pay.pay_code')
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where('revmast.field_type', '=', 'P')
            ->where('revmast.propertyid', $this->propertyid)
            ->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->whereIn('comp_type', ['Corporate', 'Travel Agency'])
            ->orderBy('name', 'ASC')->get();
        return view('property.roomresettlement', [
            'companydata' => $companydata,
            'latestbillno' => $latestbillno,
            'revdata' => $records,
            'enviro_form' => $enviro_form,
            'ncurdate' => $ncurdate,
            'subgroup' => $company,
            'years' => $years
        ]);
    }

    public function updateRoomSettle(Request $request)
    {
        $permission = revokeopen(141116);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $propertyId = $this->propertyid;
        $docId = $request->input('docid');
        $sno1 = $request->input('sno1');
        $amount = $request->input('amount');
        $voucherType = 'REC';
        $currentDate = $this->ncurdate;
        $currentTime = $this->currenttime;
        $userName = Auth::user()->u_name;
        $oldvdate = $request->oldvdate;

        // Begin transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Determine leader status and handle MSno1
            $leaderRoomOcc = Roomocc::where('propertyid', $propertyId)
                ->where('docid', $docId)
                ->where('leaderyn', 'Y')
                ->first();

            $msno1 = 0;

            // Delete existing settlement records based on leader status
            if ($leaderRoomOcc) {
                $msno1 = $leaderRoomOcc->sno1;
                Paycharge::where('propertyid', $propertyId)
                    ->where('msno1', $msno1)
                    ->where('folionodocid', $docId)
                    ->where('billno', 0)
                    ->where('modeset', 'S')
                    ->delete();
            } else {
                Paycharge::where('propertyid', $propertyId)
                    ->where('sno1', $sno1)
                    ->where('folionodocid', $docId)
                    ->where('billno', 0)
                    ->where('modeset', 'S')
                    ->delete();
            }

            // Get voucher prefix information
            $voucherPrefix = VoucherPrefix::where('propertyid', $propertyId)
                ->where('v_type', $voucherType)
                ->whereDate('date_from', '<=', $currentDate)
                ->whereDate('date_to', '>=', $currentDate)
                ->first();

            if (!$voucherPrefix) {
                throw new \Exception('Voucher prefix not found');
            }

            $voucherNumber = $voucherPrefix->start_srl_no + 1;
            $prefix = $voucherPrefix->prefix;
            $generatedDocId = $propertyId . $voucherType . ' ‎ ‎' . $prefix . ' ‎ ‎ ‎ ' . $voucherNumber;

            // Get room occupancy details
            $roomOccupancy = DB::table('roomocc')
                ->where('propertyid', $propertyId)
                ->where('docid', $docId)
                ->where('sno', $request->input('roomoccsno'))
                ->where('sno1', $request->input('roomoccsno1'))
                ->first();

            if (!$roomOccupancy) {
                throw new \Exception('Room occupancy record not found');
            }

            // Update folio bill details
            $billNumber = DB::table('paycharge')
                ->where('folionodocid', $docId)
                ->where('sno1', $sno1)
                ->value('billno');

            DB::table('fombilldetails')
                ->where('folionodocid', $docId)
                ->where('billno', $billNumber)
                ->update(['settamt' => $amount]);

            // Process payment charges
            $chargeCount = 0;
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'chargecode') === 0) {
                    $chargeCount++;
                }
            }

            // Batch prepare payment charge entries
            $serialNumber = 1;
            $chargeEntries = [];

            for ($i = 1; $i <= $chargeCount; $i++) {
                $chargeCode = $request->input('chargecode' . $i);
                $chargeAmount = $request->input('amtrow' . $i);

                // Skip empty or zero-amount rows
                if (empty($chargeCode) || empty($chargeAmount) || (float)$chargeAmount == 0) {
                    continue;
                }

                $payCodeInfo = Revmast::where('propertyid', $propertyId)
                    ->where('rev_code', $chargeCode)
                    ->first();

                if (!$payCodeInfo) {
                    continue;
                }

                // Prepare data for this charge entry
                $chargeEntries[] = [
                    'propertyid' => $propertyId,
                    'docid' => $generatedDocId,
                    'vno' => $voucherNumber,
                    'vtype' => $voucherType,
                    'sno' => $serialNumber,
                    'sno1' => $sno1,
                    'msno1' => $msno1,
                    'chqno' => $request->input('checkno') ?: $request->input('referencenoupi'),
                    'cardno' => $request->input('crnumber'),
                    'cardholder' => $request->input('holdername'),
                    'expdate' => $request->input('expdatecr'),
                    'bookno' => $request->input('batchno'),
                    'vdate' => $oldvdate,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $prefix,
                    'comp_code' => $request->input('compcode' . $i) ?? '',
                    'paycode' => $chargeCode,
                    'paytype' => $payCodeInfo->pay_type ?? '',
                    'comments' => $request->input('chargenarration' . $i),
                    'guestprof' => $roomOccupancy->guestprof,
                    'roomno' => $request->input('roomoccroomno') ?? $roomOccupancy->roomno,
                    'amtcr' => $chargeAmount,
                    'roomtype' => $roomOccupancy->roomtype,
                    'roomcat' => $roomOccupancy->roomcat,
                    'foliono' => $roomOccupancy->folioNo,
                    'restcode' => 'FOM' . $propertyId,
                    'billamount' => 0.00,
                    'taxper' => 0,
                    'onamt' => 0.00,
                    'folionodocid' => $roomOccupancy->docid,
                    'taxcondamt' => 0,
                    'taxstru' => '',
                    'u_entdt' => $currentTime,
                    'settledate' => $oldvdate,
                    'u_name' => $userName,
                    'u_ae' => 'a',
                    'modeset' => 'S',
                ];

                $serialNumber++;
            }

            // Bulk insert all valid charge entries
            if (!empty($chargeEntries)) {
                DB::table('paycharge')->insert($chargeEntries);
            }

            // Update voucher prefix sequence number
            VoucherPrefix::where('propertyid', $propertyId)
                ->where('v_type', $voucherType)
                ->where('prefix', $prefix)
                ->increment('start_srl_no');

            DB::commit();

            return back()->with('success', 'Room Settlement Updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Unable To Update Room Settlement: ' . $e->getMessage());
        }
    }

    public function openreservations(Request $request)
    {
        $permission = revokeopen(131111);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $roomcat = DB::table('room_cat')
            ->where('propertyid', $this->propertyid)
            ->where('inclcount', 'Y')
            ->orderBy('name', 'ASC')->get();
        $planmaster = DB::table('plan_mast')
            ->select('name', 'pcode')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')
            ->distinct()
            ->get();
        $roommast = DB::table('room_mast')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $totalroom = 0;
        foreach ($roommast as $row) {
            if ($row->type == 'RO') {
                $totalroom++;
            }
        }
        $checkoutdate = DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->value('ncur');
        $chkoutdate = date('Y-m-d', strtotime($checkoutdate . ' +1 day'));
        $bsource = DB::table('busssource')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $company = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Corporate')
            ->orderBy('name', 'ASC')->get();
        $travelagent = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('comp_type', 'Travel Agency')
            ->orderBy('name', 'ASC')->get();
        $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('activeyn', '1')
            ->orderBy('cityname', 'ASC')->get();
        $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $gueststatus = DB::table('gueststats')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $nationalitydata = DB::table('countries')->where('propertyid', $this->propertyid)
            ->orderBy('nationality', 'ASC')->get();

        $enviro_formdata = DB::table('enviro_form')->where('propertyid', $this->propertyid)->first();
        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $channelenviro = ChannelEnviro::where('propertyid', $this->propertyid)->first() ?? '';

        return view('property.reservation', [
            'totalroom' => $totalroom,
            'roomcat' => $roomcat,
            'channelenviro' => $channelenviro,
            'planmaster' => $planmaster,
            'roommast' => $roommast,
            'checkoutdate' => $chkoutdate,
            'bsource' => $bsource,
            'company' => $company,
            'travel_agent' => $travelagent,
            'citydata' => $citydata,
            'countrydata' => $countrydata,
            'nationalitydata' => $nationalitydata,
            'gueststatus' => $gueststatus,
            'enviro_formdata' => $enviro_formdata,
            'ncurdate' => $ncurdate
        ]);
    }

    public function reservationsubmit(Request $request)
    {
        $permission = revokeopen(131111);
        // return $permission;
        if (is_null($permission) || $permission->ins == 0) {
            return response()->json([
                'redirecturl' => '',
                'status' => 'error',
                'message' => 'You have no permission to execute this functionality!'
            ]);
        }

        try {

            $validate = $request->validate([
                'name' => 'required',
                'cityname' => 'required',
                'arrivaldate1' => 'required',
                'checkoutdate1' => 'required',
                'arrivaltime1' => 'required',
                'checkouttime1' => 'required',
            ]);

            DB::beginTransaction();

            $channelenviro = ChannelEnviro::where('propertyid', $this->propertyid)->first();
            $envirofom = EnviroFom::where('propertyid', $this->propertyid)->first();

            $advdepositcheckbox = $request->input('advdeposit');

            if ($advdepositcheckbox == 'on') {
                $advdeposit = 'Y';
            } else {
                $advdeposit = 'N';
            }

            $vtype = 'RES';

            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->first();

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefixyr = $chkvpf->prefix;

            $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('country'))->first();
            $citydata = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('cityname'))->first();
            if (!empty($request->input('issuingcity'))) {
                $issuingcityname = DB::table('cities')->where('propertyid', $this->propertyid)->where('city_code', $request->input('issuingcity'))->first();
                $issuingcountryname = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $request->input('issuingcountry'))->first();
            }
            $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $request->input('state'))->first();

            $dob = $request->input('birthDate');
            $age = Carbon::parse($dob)->age;

            $profilepicture = null;
            $identitypicture = null;

            if (!empty($request->file('profileimage'))) {
                $profilepic = $request->file('profileimage');
                $profilepicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $profilepic->getClientOriginalExtension();
                $folderPathp = 'public/walkin/reservationprofilepic';
                Storage::makeDirectory($folderPathp);
                Storage::putFileAs($folderPathp, $profilepic, $profilepicture);
            }

            if (!empty($request->file('identityimage'))) {
                $identitypic = $request->file('identityimage');
                $identitypicture = $request->input('guestmobile') . $request->input('guestname') . 'PR' . $this->propertyid . time() . '.' . $identitypic->getClientOriginalExtension();
                $folderpathi = 'public/walkin/reservationidentitypic';
                Storage::makeDirectory($folderpathi);
                Storage::putFileAs($folderpathi, $identitypic, $identitypicture);
            }

            if ($request->input('complimentry') == 'on') {
                $complimentry = 'Y';
            } else {
                $complimentry = 'N';
            }

            $maxguestprof = GuestProf::where('propertyid', $this->propertyid)->max('guestcode');
            if ($maxguestprof == null) {
                $guestprof = $this->propertyid . 10001;
            } else {
                $guestprof = $this->propertyid . substr($maxguestprof, $this->ptlngth) + 1;
            }

            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefixyr . '‎ ‎ ‎ ‎ ' . $start_srl_no;

            $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
            $count = $request->totalrooms;
            $sno = 1;
            $postdataeglobearray = [];
            $sumtotalamt = 0.00;
            $sumtotalamtaftertax = 0.00;
            $planrowcount = 0;
            for ($i = 1; $i <= $count; $i++) {
                $roomcattaxstructure = DB::table('room_cat')->where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->value('rev_code');
                $rtaxstru = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $roomcattaxstructure)->value('tax_stru');

                $cid = $request->input('cat_code' . $i);
                $checkindate = $request->input('arrivaldate' . $i);
                $checkoutdate = $request->input('checkoutdate' . $i);
                $propertyid = $this->propertyid;

                $emptrooms = '';
                if ($envirofom->autofillroomres == 'Y') {
                    // $rooms = DB::table('room_mast')
                    //     ->whereNotIn('rcode', function ($query) use ($checkindate, $propertyid) {
                    //         $query->select('roomno')
                    //             ->from('roomocc')
                    //             ->whereNull('chkoutdate')
                    //             ->where('propertyid', $propertyid)
                    //             ->whereRaw("? >= chkindate AND ? < depdate", [$checkindate, $checkindate]);
                    //     })
                    //     ->whereNotIn('rcode', function ($query) use ($checkindate, $propertyid) {
                    //         $query->select('RoomNo')
                    //             ->from('grpbookingdetails')
                    //             ->where('Cancel', 'N')
                    //             ->where('ContraDocId', '')
                    //             ->where('Property_ID', $propertyid)
                    //             ->whereRaw("? >= ArrDate AND ? < DepDate", [$checkindate, $checkindate]);
                    //     })
                    //     ->where('type', 'RO')
                    //     ->whereNot('room_stat', 'O')
                    //     ->where('inclcount', 'Y')
                    //     ->where('propertyid', $propertyid)
                    //     ->where('room_cat', $cid)
                    //     ->first();
                    // $rooms = DB::table('room_mast as rm')
                    //     ->select([
                    //         'rm.rcode as SearchCode',
                    //         'rm.rcode as rcode',
                    //         'rm.Name as Quot',
                    //         'rm.room_cat',
                    //         'rv.tax_stru as RoomTaxStru',
                    //     ])
                    //     ->leftJoin('room_cat as rc', 'rc.cat_code', '=', 'rm.room_cat')
                    //     ->leftJoin('revmast as rv', 'rc.rev_code', '=', 'rv.rev_code')
                    //     ->where('rm.propertyid', $channelenviro->propertyid)
                    //     ->where('rm.type', 'RO')
                    //     ->where('rm.InclCount', 'Y')
                    //     ->where('rm.room_cat', $cid)
                    //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $checkoutdate, $propertyid) {
                    //         $query->select('ro.roomno')
                    //             ->from('roomocc as ro')
                    //             ->where('ro.propertyid', $propertyid)
                    //             ->whereNull('ro.type')
                    //             ->where('ro.chkindate', '>=', $checkindate)
                    //             ->where('ro.depdate', '<=', $checkoutdate);
                    //     })
                    //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $propertyid) {
                    //         $query->select('rb.RoomCode')
                    //             ->from('roomblockout as rb')
                    //             ->whereIn('rb.Type', ['O', 'M'])
                    //             ->where('rb.propertyid', $propertyid)
                    //             ->whereRaw('? BETWEEN rb.Fromdate AND rb.ToDate', [$checkindate]);
                    //     })
                    //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $checkoutdate, $propertyid) {
                    //         $query->select('gbd.RoomNo')
                    //             ->from('grpbookingdetails as gbd')
                    //             ->whereNotExists(function ($subquery) {
                    //                 $subquery->select(DB::raw(1))
                    //                     ->from('guestfolio as gf')
                    //                     ->whereColumn('gf.BookingDocId', 'gbd.BookingDocId')
                    //                     ->whereColumn('gf.BookingSno', 'gbd.Sno');
                    //             })
                    //             ->where('gbd.Property_ID', $propertyid)
                    //             ->where('gbd.Cancel', 'N')
                    //             ->where('gbd.ArrDate', '>=', $checkindate)
                    //             ->where('gbd.DepDate', '<=', $checkoutdate)
                    //             ->where('gbd.chkoutyn', 'N');
                    //     })
                    //     ->orderBy('rm.rcode')
                    //     ->first();
                    $rooms = DB::table('room_mast as rm')
                        ->select('rm.rcode', 'rm.room_cat')
                        ->where('rm.propertyid', $propertyid)
                        ->where('rm.room_cat', $cid)
                        ->whereNotIn('rm.rcode', function ($query) use ($propertyid, $cid, $checkindate, $checkoutdate) {
                            $query->select('ro.roomno')
                                ->from('roomocc as ro')
                                ->where('ro.propertyid', $propertyid)
                                ->whereNull('ro.type')
                                ->where('ro.roomcat', $cid)
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
                        ->first();
                    $emptrooms = $rooms->rcode ?? '';

                    if ((empty($emptrooms) || $emptrooms == '') && $envirofom->emptyroomyn == 'N') {
                        DB::rollBack();
                        return response()->json([
                            'redirecturl' => 'Reservation',
                            'status' => 'error',
                            'message' => 'Empty Rooms cannot be assigned.',
                        ]);
                    }
                }

                $grpbookingdetails = [
                    'Property_ID' => $this->propertyid,
                    'BookingDocid' => $docid,
                    'Sno' => $sno,
                    'BookNo' => $start_srl_no,
                    'RoomDet' => '1',
                    'CancelUName' => '',
                    'GuestProf' => $guestprof,
                    'GuestName' => $request->input('name') ?? '',
                    'RoomCat' => $request->input('cat_code' . $i) ?? '',
                    'Plan_Code' => $request->input('planmaster' . $i) ?? '',
                    'ServiceChrg' => 'No',
                    'RoomNo' => $request->input('roommast' . $i) ?? $emptrooms,
                    'RateCode' => 2,
                    'NoDays' => $request->input('stay_days' . $i) ?? '',
                    'DepDate' => $request->input('checkoutdate' . $i) ?? '',
                    'DepTime' => $request->input('checkouttime' . $i) ?? '',
                    'RoomTaxStru' => $rtaxstru ?? '',
                    'CancelDate' => null,
                    'Cancel' => 'N',
                    'IncTax' => $request->input('tax_inc' . $i) ?? '',
                    'Tarrif' => $request->input('rate' . $i) ?? '',
                    'ArrDate' => $request->input('arrivaldate' . $i) ?? '',
                    'ArrTime' => $request->input('arrivaltime' . $i) ?? '',
                    'Adults' => $request->input('adult' . $i) ?? '',
                    'Childs' => $request->input('child' . $i) ?? '',
                    'U_EntDt' => $this->currenttime,
                    'U_Name' => Auth::user()->u_name,
                    'U_AE' => 'a',
                    'ContraDocId' => '',
                    'ContraSno' => '',
                ];

                $plandetails = [
                    'propertyid' => $this->propertyid,
                    'foliono' => $start_srl_no,
                    'docid' => $docid,
                    'sno' => 1,
                    'sno1' => $sno,
                    'roomno' => $request->input('roommast' . $i) ?? $emptrooms,
                    'room_rate_before_tax' => $request->input('roomrate' . $i) ?? '0',
                    'total_rate' => $request->input('plansumrate' . $i) ?? '0',
                    'pcode' => $request->input('planmaster' . $i),
                    'noofdays' => $request->input('stay_days' . $i),
                    'rev_code' => $request->input('rowsrev_code' . $i) ?? '',
                    'fixrate' => $request->input('rowdplanfixrate' . $i),
                    'planper' => $request->input('rowdplan_per' . $i),
                    'amount' => $request->input('rowdamount' . $i),
                    'netplanamt' => $request->input('plankaamount' . $i),
                    'taxinc' => $request->input('taxincplanroomrate' . $i) ?? 'Y',
                    'taxstru' => $request->input('rowstax_stru' . $i),
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                $roomcat = RoomCat::where('propertyid', $this->propertyid)->where('cat_code', $request->input('cat_code' . $i))->first();
                $plandata = PlanMast::where('propertyid', $this->propertyid)->where('pcode', $request->input('planmaster' . $i))->first();

                if ($channelenviro->checkyn == 'Y') {
                    $planedit = $request->input('planedit' . $i);
                    $taxinc = $request->input('tax_inc' . $i);

                    $croomrate = 0.00;

                    if ($planedit == 'Y') {
                        $croomrate = $request->input('plankaamount' . $i);
                    } else {
                        $croomrate = $request->input('rate' . $i);
                    }

                    if ($croomrate < 7500) {
                        $txpr = 12;
                    } else {
                        $txpr = 18;
                    }

                    if ($taxinc == 'Y') {
                        $ct = $croomrate * 100;
                        $amountbeforetax = (str_replace(',', '', number_format(($ct / (100 + $txpr)), 2)));
                        $amountvifergation = (str_replace(',', '', number_format($amountbeforetax, 2)) * $txpr) / 100;
                        $amountaftertax = str_replace(',', '', number_format($amountbeforetax, 2)) + $amountvifergation;
                    } else {
                        $amountbeforetax = $croomrate;
                        $amountaftertax = ($croomrate * $txpr) / 100;
                    }

                    $arrdate = new DateTime($request->input('arrivaldate' . $i));
                    $depsdate = new DateTime($request->input('checkoutdate' . $i));

                    $interval = $arrdate->diff($depsdate);

                    $diffcount = $interval->days;
                    $amountbeforesum = 0.00;
                    $amountaftersum = 0.00;

                    $tmparrdate = clone $arrdate->modify("-1 day");

                    $rowscount = $request->input('roomcount' . $i);

                    $nightwise = [];
                    for ($l = 1; $l <= $diffcount; $l++) {
                        $amountbeforesum += str_replace(',', '', number_format($amountbeforetax, 2));
                        $amountaftersum += str_replace(',', '', number_format($amountaftertax, 2));
                        $effectivedate = clone $tmparrdate;
                        $effectivedate->modify("+$l day");
                        $nightwise[] = [
                            "Base" => [
                                "AmountBeforeTax" => str_replace(',', '', number_format($amountbeforetax, 2)),
                                "AmountAfterTax" => str_replace(',', '', number_format($amountaftertax, 2))
                            ],
                            "EffectiveDate" => $effectivedate->format('Y-m-d')
                        ];
                    }

                    for ($m = 1; $m <= $rowscount; $m++) {
                        $sumtotalamt += str_replace(',', '', number_format($amountbeforesum, 2));
                        $sumtotalamtaftertax += str_replace(',', '', number_format($amountaftersum, 2));
                        $postdataeglobearray[] = [
                            "RoomTypes" => [
                                [
                                    "RoomDescription" => [
                                        "Name" => $roomcat->name
                                    ],
                                    "NumberOfUnits" => 1,
                                    "RoomTypeCode" => $roomcat->map_code
                                ]
                            ],
                            "RatePlans" => [
                                [
                                    "RatePlanCode" => "$plandata->map_code",
                                    "RatePlanName" => $plandata->name
                                ]
                            ],
                            "GuestCounts" => [
                                [
                                    "AgeQualifyingCode" => "10",
                                    "Count" => $i
                                ],
                                [
                                    "AgeQualifyingCode" => "8",
                                    "Count" => 0
                                ]
                            ],
                            "TimeSpan" => [
                                "Start" => $request->input('arrivaldate' . $i),
                                "End" => $request->input('checkoutdate' . $i)
                            ],
                            "RoomRates" => $nightwise,
                            "Total" => [
                                "AmountBeforeTax" => str_replace(',', '', number_format($amountbeforesum, 2)),
                                "AmountAfterTax" => str_replace(',', '', number_format($amountaftersum, 2)),
                            ]
                        ];
                    }
                }

                GrpBookinDetail::insert($grpbookingdetails);
                if ($request->input('planedit' . $i) == 'Y') {
                    $planrowcount++;
                    BookinPlanDetail::insert($plandetails);
                }
                $rcount = $request->input('roomcount' . $i);
                $l = $i;
                if ($request->input('roomcount' . $i) > 1) {
                    for ($j = 1; $j < $rcount; $j++) {

                        $emptrooms = '';
                        if ($envirofom->autofillroomres == 'Y') {
                            $cid = $request->input('cat_code' . $i);
                            $checkindate = $request->input('arrivaldate' . $i);
                            $checkoutdate = $request->input('checkoutdate' . $i);
                            $propertyid = $this->propertyid;

                            // $rooms = DB::table('room_mast')
                            //     ->whereNotIn('rcode', function ($query) use ($checkindate, $propertyid) {
                            //         $query->select('roomno')
                            //             ->from('roomocc')
                            //             ->whereNull('chkoutdate')
                            //             ->where('propertyid', $propertyid)
                            //             ->whereRaw("? >= chkindate AND ? < depdate", [$checkindate, $checkindate]);
                            //     })
                            //     ->whereNotIn('rcode', function ($query) use ($checkindate, $propertyid) {
                            //         $query->select('RoomNo')
                            //             ->from('grpbookingdetails')
                            //             ->where('Cancel', 'N')
                            //             ->where('ContraDocId', '')
                            //             ->where('Property_ID', $propertyid)
                            //             ->whereRaw("? >= ArrDate AND ? < DepDate", [$checkindate, $checkindate]);
                            //     })
                            //     ->where('type', 'RO')
                            //     ->where('inclcount', 'Y')
                            //     ->whereNot('room_stat', 'O')
                            //     ->where('propertyid', $propertyid)
                            //     ->where('room_cat', $cid)
                            //     ->first();
                            // $rooms = DB::table('room_mast as rm')
                            //     ->select([
                            //         'rm.rcode as SearchCode',
                            //         'rm.rcode as rcode',
                            //         'rm.Name as Quot',
                            //         'rm.room_cat',
                            //         'rv.tax_stru as RoomTaxStru',
                            //     ])
                            //     ->leftJoin('room_cat as rc', 'rc.cat_code', '=', 'rm.room_cat')
                            //     ->leftJoin('revmast as rv', 'rc.rev_code', '=', 'rv.rev_code')
                            //     ->where('rm.propertyid', $channelenviro->propertyid)
                            //     ->where('rm.type', 'RO')
                            //     ->where('rm.InclCount', 'Y')
                            //     ->where('rm.room_cat', $cid)
                            //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $checkoutdate, $propertyid) {
                            //         $query->select('ro.roomno')
                            //             ->from('roomocc as ro')
                            //             ->where('ro.propertyid', $propertyid)
                            //             ->whereNull('ro.type')
                            //             ->where('ro.chkindate', '>=', $checkindate)
                            //             ->where('ro.depdate', '<=', $checkoutdate);
                            //     })
                            //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $propertyid) {
                            //         $query->select('rb.RoomCode')
                            //             ->from('roomblockout as rb')
                            //             ->whereIn('rb.Type', ['O', 'M'])
                            //             ->where('rb.propertyid', $propertyid)
                            //             ->whereRaw('? BETWEEN rb.Fromdate AND rb.ToDate', [$checkindate]);
                            //     })
                            //     ->whereNotIn('rm.rcode', function ($query) use ($checkindate, $checkoutdate, $propertyid) {
                            //         $query->select('gbd.RoomNo')
                            //             ->from('grpbookingdetails as gbd')
                            //             ->whereNotExists(function ($subquery) {
                            //                 $subquery->select(DB::raw(1))
                            //                     ->from('guestfolio as gf')
                            //                     ->whereColumn('gf.BookingDocId', 'gbd.BookingDocId')
                            //                     ->whereColumn('gf.BookingSno', 'gbd.Sno');
                            //             })
                            //             ->where('gbd.Property_ID', $propertyid)
                            //             ->where('gbd.Cancel', 'N')
                            //             ->where('gbd.ArrDate', '>=', $checkindate)
                            //             ->where('gbd.DepDate', '<=', $checkoutdate)
                            //             ->where('gbd.chkoutyn', 'N');
                            //     })
                            //     ->orderBy('rm.rcode')
                            //     ->first();
                            $rooms = DB::table('room_mast as rm')
                                ->select('rm.rcode', 'rm.room_cat')
                                ->where('rm.propertyid', $propertyid)
                                ->where('rm.room_cat', $cid)
                                ->whereNotIn('rm.rcode', function ($query) use ($propertyid, $cid, $checkindate, $checkoutdate) {
                                    $query->select('ro.roomno')
                                        ->from('roomocc as ro')
                                        ->where('ro.propertyid', $propertyid)
                                        ->whereNull('ro.type')
                                        ->where('ro.roomcat', $cid)
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
                                ->first();
                            $emptrooms = $rooms->rcode ?? '';

                            if ((empty($emptrooms) || $emptrooms == '') && $envirofom->emptyroomyn == 'N') {
                                DB::rollBack();
                                return response()->json([
                                    'redirecturl' => 'Reservation',
                                    'status' => 'error',
                                    'message' => 'Empty Rooms cannot be assigned.',
                                ]);
                            }
                        }

                        $grpexcept = [
                            'Property_ID' => $this->propertyid,
                            'BookingDocid' => $docid,
                            'Sno' => ++$sno,
                            'BookNo' => $start_srl_no,
                            'RoomDet' => '1',
                            'CancelUName' => '',
                            'GuestProf' => $guestprof,
                            'GuestName' => $request->input('name') ?? '',
                            'RoomCat' => $request->input('cat_code' . $i) ?? '',
                            'Plan_Code' => $request->input('planmaster' . $i) ?? '',
                            'ServiceChrg' => 'No',
                            'RoomNo' => $request->input('roommast' . $j) ?? $emptrooms,
                            'RateCode' => 2,
                            'NoDays' => $request->input('stay_days' . $i) ?? '',
                            'DepDate' => $request->input('checkoutdate' . $i) ?? '',
                            'DepTime' => $request->input('checkouttime' . $i) ?? '',
                            'RoomTaxStru' => $rtaxstru ?? '',
                            'CancelDate' => null,
                            'Cancel' => 'N',
                            'IncTax' => $request->input('tax_inc' . $i) ?? '',
                            'Tarrif' => $request->input('rate' . $i) ?? '',
                            'ArrDate' => $request->input('arrivaldate' . $i) ?? '',
                            'ArrTime' => $request->input('arrivaltime' . $i) ?? '',
                            'Adults' => $request->input('adult' . $i) ?? '',
                            'Childs' => $request->input('child' . $i) ?? '',
                            'U_EntDt' => $this->currenttime,
                            'U_Name' => Auth::user()->u_name,
                            'U_AE' => 'a',
                            'ContraDocId' => '',
                            'ContraSno' => '',
                        ];

                        $plandetailsexcept = [
                            'propertyid' => $this->propertyid,
                            'foliono' => $start_srl_no,
                            'docid' => $docid,
                            'sno' => 1,
                            'sno1' => ++$sno,
                            'roomno' =>  $request->input('roommast' . $j) ?? $emptrooms,
                            'room_rate_before_tax' => $request->input('roomrate' . $i) ?? '0',
                            'total_rate' => $request->input('plansumrate' . $i) ?? '0',
                            'pcode' => $request->input('planmaster' . $i),
                            'noofdays' => $request->input('stay_days' . $i),
                            'rev_code' => $request->input('rowsrev_code' . $i) ?? '',
                            'fixrate' => $request->input('rowdplanfixrate' . $i),
                            'planper' => $request->input('rowdplan_per' . $i),
                            'amount' => $request->input('rowdamount' . $i),
                            'netplanamt' => $request->input('plankaamount' . $i),
                            'taxinc' => $request->input('taxincplanroomrate' . $i) ?? 'Y',
                            'taxstru' => $request->input('rowstax_stru' . $i),
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                        ];

                        if ($request->input('planedit' . $i) == 'Y') {
                            $planrowcount++;
                            BookinPlanDetail::insert($plandetailsexcept);
                        }

                        GrpBookinDetail::insert($grpexcept);
                    }
                }
                $sno++;
            }

            $incount = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->count();

            $bookingdata = [
                'Property_ID' => $this->propertyid,
                'DocId' => $docid,
                'GuestName' => $request->input('name') ?? '',
                'BookNo' => $start_srl_no,
                'Vtype' => $vtype,
                'advdeposit' => $advdeposit,
                'Vprefix' => $vprefixyr,
                'vdate' => $ncurdate,
                'GuestProf' => $guestprof,
                'vehiclenum' => $request->input('vehiclenum') ?? '',
                'TravelAgency' => $request->input('travel_agent') ?? '',
                'purpofvisit' => $request->input('purposeofvisit') ?? '',
                'BussSource' => $request->input('bsource') ?? '',
                'MarketSeg' => $request->input('booking_source') ?? '',
                'RRServiceChrg' => '',
                'BookedBy' => $request->input('booked_by') ?? '',
                'ResStatus' => $request->input('reservation_status') ?? '',
                'ResMode' => '',
                'TravelMode' => $request->input('travelmode') ?? '',
                'CancelDate' => null,
                'Cancel' => 'N',
                'Company' => $request->input('company') ?? '',
                'ArrFrom' => $request->input('arrfrom') ?? '',
                'Destination' => $request->input('destination') ?? '',
                'U_EntDt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'a',
                'NoofRooms' => $incount,
                'Remarks' => $request->input('remarkmain') ?? '',
                'pickupdrop' => $request->pickupdrop ?? '',
                'Authorization' => '',
                'Verified' => '',
                'CancelUName' => '',
                'MobNo' => $request->input('mobile') ?? '',
                'Email' => $request->input('email') ?? '',
                'RRTaxInc' => $request->input('tax_inc' . $i) ?? '',
                'RDisc' => $request->input('rodisc') ?? '0',
                'RSDisc' => $request->input('rsdisc') ?? '0',
                'AdvDueDate' => null,
                'RefCode' => '',
                'RefBookNo' => $request->input('ref_booking_id') ?? '',
            ];

            $guestproft = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'folio_no' => $start_srl_no,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'complimentry' => $complimentry,
                'guestcode' => $guestprof,
                'name' => $request->input('name'),
                'state_code' => $request->input('state'),
                'country_code' => $request->input('country'),
                'add1' => $request->input('address1'),
                'add2' => $request->input('address2'),
                'city' => $request->input('cityname'),
                'type' => $countrydata->Type,
                'mobile_no' => $request->input('mobile'),
                'email_id' => $request->input('email'),
                'nationality' => $countrydata->nationality ?? null,
                'anniversary' => $request->input('weddingAnniversary'),
                'guest_status' => $request->input('vipStatus'),
                'comments1' => null,
                'comments2' => null,
                'comments3' => null,
                'city_name' => $citydata->cityname,
                'state_name' => $statedata->name,
                'country_name' => $countrydata->name,
                'gender' => $request->input('genderguest'),
                'marital_status' => $request->input('marital_status'),
                'zip_code' => $citydata->zipcode,
                'con_prefix' => $request->input('greetings'),
                'dob' => $dob,
                'age' => $age,
                'pic_path' => $profilepicture,
                'id_proof' => $request->input('idType'),
                'idproof_no' => $request->input('idNumber'),
                'issuingcitycode' => $request->input('issuingcity') ?? null,
                'issuingcityname' => $issuingcityname->cityname ?? null,
                'issuingcountrycode' => $request->input('issuingcountry') ?? null,
                'issuingcountryname' => $issuingcountryname->name ?? null,
                'expiryDate' => $request->input('expiryDate'),
                'paymentMethod' => $request->input('paymentMethod'),
                'idpic_path' => $identitypicture,
                'm_prof' => $guestprof,
                'father_name' => null,
                'fom' => 1,
                'pos' => 0,
            ];

            if ($channelenviro->checkyn == 'Y') {
                $compdt = Companyreg::where('propertyid', $this->propertyid)->where('role', 'Property')->first();
                $citydata = Cities::where('propertyid', $this->propertyid)->where('city_code', $request->input('cityname'))->first();
                $statedata = States::where('propertyid', $this->propertyid)->where('state_code', $citydata->state)->first();
                $countries = Countries::where('propertyid', $this->propertyid)->where('country_code', $statedata->country)->first();

                $ut = date('Y-m-d H:i:s');
                $date = new DateTime($ut);
                $formatted_date = $date->format('Y-m-d\TH:i:s');

                if ($channelenviro->url == 'https://www.eglobe-solutions.com') {
                    $postdata = [
                        "RoomStays" => $postdataeglobearray,
                        "ResGuests" => [
                            [
                                "Customer" => [
                                    "PersonName" => [
                                        "NamePrefix" => $request->input('greetingsguest'),
                                        "GivenName" => $request->input('name'),
                                        "Surname" => ""
                                    ],
                                    "Telephone" => [
                                        "PhoneNumber" => $request->input('mobile'),
                                    ],
                                    "Email" => $request->input('email'),
                                    "Address" => [
                                        "AddressLine" => [
                                            $request->input('address1') ?? '',
                                            $request->input('address2') ?? ''
                                        ],
                                        "CityName" => $citydata->cityname,
                                        "PostalCode" => $citydata->zipcode,
                                        "StateProv" => $statedata->name,
                                        "CountryName" => $countries->name
                                    ]
                                ],
                                "PrimaryIndicator" => "1"
                            ]
                        ],
                        "ResGlobalInfo" => [
                            "UniqueID" => [
                                "ID" => $guestprof
                            ],
                            "BasicPropertyInfo" => [
                                "HotelCode" => $channelenviro->eglobepropertyid,
                                "HotelName" => $compdt->comp_name
                            ],
                            "Source" => [
                                "RequestorID" => [
                                    "ID" => "EXT_PMS_CODE",
                                    "Type" => "ChannelManager"
                                ],
                                "BookingChannel" => [
                                    "Type" => "OTA",
                                    "CompanyName" => "EXT PMS NAME",
                                    "CompanyCode" => ""
                                ]
                            ],
                            "CreateDateTime" => $formatted_date,
                            "ResStatus" => "Commit",
                            "TimeSpan" => [
                                "Start" => $request->input('arrivaldate1'),
                                "End" => $request->input('checkoutdate1')
                            ],
                            "GuestCounts" => [
                                [
                                    "AgeQualifyingCode" => "10",
                                    "Count" => 1
                                ],
                                [
                                    "AgeQualifyingCode" => "8",
                                    "Count" => 0
                                ]
                            ],
                            "Total" => [
                                "OtherCharges" => [
                                    [
                                        "ChargeDesc" => "Airport Pickup",
                                        "AmountBeforeTax" => 0,
                                        "AmountAfterTax" => 0
                                    ],
                                    [
                                        "ChargeDesc" => "Airport Drop",
                                        "AmountBeforeTax" => 0,
                                        "AmountAfterTax" => 0
                                    ]
                                ],
                                "Taxes" => [
                                    "Tax" => [
                                        "Amount" => str_replace(',', '', number_format($sumtotalamtaftertax - $sumtotalamt, 2)),
                                    ]
                                ],
                                "AmountBeforeTax" => str_replace(',', '', number_format($sumtotalamt, 2)),
                                "AmountAfterTax" => str_replace(',', '', number_format($sumtotalamtaftertax, 2)),
                                "CurrencyCode" => "INR"
                            ],
                            "PaymentTypeInfo" => [
                                "PaymentType" => "PayAtHotel",
                                "PartialPaymentAmount" => 0.00
                            ],
                            "SpecialRequests" => [""]
                        ]
                    ];

                    // echo json_encode($postdata);

                    // echo '<pre>';pp
                    // print_r($postdata);
                    // echo '</pre>';git 

                    // exit;

                    $apiurl = "$channelenviro->url/webapichannelmanager/extpms/bookings/notif";
                    $eglobecurl = curl_init($apiurl);
                    curl_setopt($eglobecurl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($eglobecurl, CURLOPT_POST, true);
                    curl_setopt($eglobecurl, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: $channelenviro->authorization",
                        "ProviderCode: $channelenviro->providercode"
                    ]);
                    curl_setopt($eglobecurl, CURLOPT_POSTFIELDS, json_encode($postdata));
                    $response = curl_exec($eglobecurl);
                    $httpcode = curl_getinfo($eglobecurl, CURLINFO_HTTP_CODE);

                    $datas = [
                        'apiurl' => $apiurl,
                        'response' => $response,
                        'httpcode' => $httpcode
                    ];

                    $channelpushes = [
                        'propertyid' => $this->propertyid,
                        'eglobepropertyid' => $channelenviro->eglobepropertyid,
                        'name' => $channelenviro->name,
                        'url' => $channelenviro->url,
                        'username' => $channelenviro->username,
                        'password' => $channelenviro->password,
                        'apikey' => $channelenviro->apikey,
                        'authorization' => $channelenviro->authorization,
                        'providercode' => $channelenviro->providercode,
                        'checkyn' => $channelenviro->checkyn,
                        'postdata' => json_encode($postdata),
                        'response' => $response,
                        'httpcode' => $httpcode,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                        'u_name' => Auth::user()->name
                    ];

                    ChannelPushes::insert($channelpushes);
                }
            }
            // DB::commit();
            // exit;

            DB::table('booking')->insert($bookingdata);
            DB::table('guestprof')->insert($guestproft);

            // Fetch records sorted properly
            $fetchedgrp = GrpBookinDetail::where('Property_ID', $this->propertyid)
                ->where('BookingDocid', $docid)
                ->orderBy('RoomNo', 'ASC')
                ->orderBy('Plan_Code', 'ASC')
                ->orderBy('sn', 'ASC')
                ->get();

            foreach ($fetchedgrp as $grp) {
                $grp->update(['Sno' => 100000 + $grp->Sno]);
            }

            $counter = 1;
            foreach ($fetchedgrp as $grp) {
                $grp->update(['Sno' => $counter]);
                $counter++;
            }

            $fetchedplan = BookinPlanDetail::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->orderBy('roomno', 'ASC')
                ->orderBy('pcode', 'ASC')
                ->orderBy('sn', 'ASC')
                ->get();

            foreach ($fetchedplan as $gplan) {
                $gplan->update(['sno1' => 100000 + $gplan->sno1]);
            }

            $counterp = 1;
            foreach ($fetchedplan as $gplan) {
                $gplan->update(['sno1' => $counterp]);
                $counterp++;
            }

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefixyr)
                ->increment('start_srl_no');




            DB::commit();

            $chkgprof = GuestProf::where('guestcode', $guestprof)->where('propertyid', $this->propertyid)->first();
            $chkbooking = Bookings::where('DocId', $docid)->where('Property_ID', $this->propertyid)->first();

            if (!$chkgprof) {
                BookinPlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->delete();
                Bookings::where('Property_ID', $this->propertyid)->where('DocId', $docid)->delete();
                DB::rollBack();
                return response()->json([
                    'redirecturl' => 'Reservation',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Guest Profile Please Try Again',
                ]);
            }

            if (!$chkbooking) {
                BookinPlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->delete();
                GuestProf::where('guestcode', $guestprof)->where('propertyid', $this->propertyid)->delete();
                DB::rollBack();
                return response()->json([
                    'redirecturl' => 'Reservation',
                    'status' => 'error',
                    'message' => 'Unable to insert data in Booking Please Try Again',
                ]);
            }

            if ($planrowcount > 0) {
                $insertedplanb = BookinPlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->count();

                if ($insertedplanb < $planrowcount) {
                    BookinPlanDetail::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
                    GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->delete();
                    Bookings::where('Property_ID', $this->propertyid)->where('DocId', $docid)->delete();
                    GuestProf::where('guestcode', $guestprof)->where('propertyid', $this->propertyid)->delete();
                    DB::rollBack();
                    return response()->json([
                        'redirecturl' => 'Reservation',
                        'status' => 'error',
                        'message' => 'Unable to insert data in Booking Plan Details Please Try Again',
                    ]);
                }
            }

            $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();
            // if ($wpenv != null) {
            //     if ($wpenv->checkyn == 'Y' && $wpenv->reservation != '' && $wpenv->reservationtemplate != '' && $request->mobile != '') {
            //         $whatsapp = new WhatsappSend();
            //         $whatsapp->ReservationSend($request->input('name'), date('d-M-Y', strtotime($request->arrivaldate1)), date('H:i', strtotime($request->arrivaltime1)), $start_srl_no, '0.00', $request->mobile);
            //     }
            // }

            if ($wpenv != null) {
                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->reservation != '' &&
                    $wpenv->reservationarray != '' &&
                    $wpenv->reservationtemplate != '' &&
                    $request->mobile != ''
                ) {
                    $reservationarray = json_decode($wpenv->reservationarray, true);

                    $msgdata = [];
                    foreach ($reservationarray as $row) {
                        [$colname, $table] = $row;
                        if (endsWith($colname, 'sum')) {
                            $value = DB::table($table)->where('propertyid', $this->propertyid)->where('refdocid', $docid)->sum(removeSuffixIfExists($colname, 'sum'));
                        } else {
                            $value = DB::table($table)->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->value($colname);
                        }
                        $mob = GuestProf::where('propertyid', $this->propertyid)->where('docid', $docid)->value('mobile_no');
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $mob, 'Reservation', 'reservationtemplate');
                }

                if (
                    $wpenv->checkyn == 'Y' &&
                    $wpenv->adminreservation != '' &&
                    $wpenv->adminreservationarray != '' &&
                    $wpenv->adminreservationtemplate != '' &&
                    $wpenv->managementmob != ''
                ) {
                    $adminreservationarray = json_decode($wpenv->adminreservationarray, true);

                    $msgdata = [];
                    foreach ($adminreservationarray as $row) {
                        [$colname, $table] = $row;
                        if (endsWith($colname, 'sum')) {
                            $value = DB::table($table)->where('propertyid', $this->propertyid)->where('refdocid', $docid)->sum(removeSuffixIfExists($colname, 'sum'));
                        } else {
                            $value = DB::table($table)->where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->value($colname);
                        }
                        $mob = GuestProf::where('propertyid', $this->propertyid)->where('docid', $docid)->value('mobile_no');
                        $msgdata[] = $value;
                    }

                    $whatsapp = new WhatsappSend();
                    $whatsapp->MuzzTech($msgdata, $wpenv->managementmob, 'Reservation Admin', 'adminreservationtemplate');
                }
            }

            if ($advdepositcheckbox == 'on') {
                $coded = base64_encode($docid);
                return response()->json([
                    'redirecturl' => 'advancedeposit?docid=' . $coded,
                    'status' => 'success',
                    'message' => 'Reservation Added successfully!',
                ]);
            } else {
                return response()->json([
                    'redirecturl' => 'reservationlist',
                    'status' => 'success',
                    'message' => 'Reservation Added successfully!',
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'redirecturl' => '',
                'status' => 'error',
                'message' => 'Unknown error occurred: ' . $e->getMessage() . ' On Line: ' . $e->getLine(),
            ]);
        }
    }

    public function openadvancedeposit(Request $request)
    {
        $docid = base64_decode($request->query('docid'));

        if (empty($docid)) {
            $docid = base64_decode($request->input('DocId'));
        }

        $sno = base64_decode($request->input('Sno'));

        if ($sno == '') {
            $sno = GrpBookinDetail::where('Property_ID', $this->propertyid)->where('BookingDocid', $docid)->max('Sno');
        }

        $ncurdate = DB::table('enviro_general')->where('propertyid', $this->propertyid)->value('ncur');
        $data = DB::table('booking')
            ->select(
                'booking.*',
                'grpbookingdetails.GuestName',
                'grpbookingdetails.Sno',
                'grpbookingdetails.ArrDate',
                'grpbookingdetails.DepDate',
                'grpbookingdetails.RoomNo'
            )
            ->leftJoin('grpbookingdetails', 'grpbookingdetails.BookingDocid', '=', 'booking.DocId')
            ->where('booking.DocId', $docid)
            ->where('grpbookingdetails.Sno', $sno)
            ->where('booking.Property_ID', $this->propertyid)
            ->first();

        $guestnamedata = DB::table('grpbookingdetails')->select('grpbookingdetails.GuestName', 'grpbookingdetails.BookNo')
            ->where('Property_ID', $this->propertyid)
            ->groupBy('BookNo')->get();

        $revdata = DB::table('revmast')
            ->select('revmast.name', 'revmast.rev_code', 'revmast.nature', 'revmast.field_type', 'revmast.flag_type', 'depart_pay.pay_code')
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where('revmast.field_type', '=', 'P')
            ->where('revmast.propertyid', $this->propertyid)
            ->get();

        $taxstrudata = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->groupBy('name')->get();
        $companydata = DB::table('company')->where('propertyid', $this->propertyid)->first();
        return view('property.advancedeposit', [
            'data' => $data,
            'ncurdate' => $ncurdate,
            'names' => $guestnamedata,
            'revdata' => $revdata,
            'taxstrudata' => $taxstrudata,
            'companydata' => $companydata
        ]);
    }

    public function getmaxadresno(Request $request)
    {
        $vtype = $request->input('vtype');
        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $this->ncurdate)
            ->whereDate('date_to', '>=', $this->ncurdate)
            ->first();

        $start_srl_no = $chkvpf->start_srl_no + 1;
        return json_encode($start_srl_no);
    }

    public function deleteadvancedeposit($docid, $vno)
    {
        $chk = Paycharge::where('docid', $docid)->where('vno', $vno)->first();

        if (is_null($chk)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Vno'
            ]);
        }

        Paycharge::where('docid', $docid)->where('vno', $vno)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Advance Deleted successfully'
        ]);
    }

    public function submitadvdeposit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'advancetype' => 'required',
                'rectno' => 'required',
                'guestname' => 'required',
                'paytype' => 'required',
                'narration' => 'required',
                'amount' => 'required|numeric',
            ]);

            DB::beginTransaction();

            $tablename = 'paycharge';
            $bookingDetails = DB::table('grpbookingdetails')
                ->where('BookingDocid', $request->input('docid'))
                ->where('BookNo', $request->input('bookno'))
                ->where('Property_ID', $this->propertyid)
                ->where('Sno', $request->input('Sno'))
                ->first();

            if (!$bookingDetails) {
                DB::rollBack();
                return redirect('reservationlist')->with('error', 'Booking details not found');
            }

            $vtype = $request->input('prevtype');
            $voucherPrefix = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->first();

            if (!$voucherPrefix) {
                DB::rollBack();
                return redirect('reservationlist')->with('error', 'Voucher prefix not found');
            }

            $vno = $voucherPrefix->start_srl_no + 1;
            $vprefix = $voucherPrefix->prefix;
            $docid = $this->propertyid . $vtype . ' ‎ ‎' . $vprefix . ' ‎ ‎ ‎ ' . $vno;

            $advtype = $request->input('advancetype');
            $amount = $request->input('amount');
            $amtdr = ($advtype == 'Refund') ? $amount : 0.00;
            $amtcr = ($advtype == 'Refund') ? 0.00 : $amount;

            $paytype = Revmast::where('propertyid', $this->propertyid)
                ->where('rev_code', $request->input('paytype'))
                ->first();

            if (!$paytype) {
                DB::rollBack();
                return redirect('reservationlist')->with('error', 'Payment type not found');
            }

            $maxsno = GrpBookinDetail::where('BookingDocid', $request->input('docid'))
                ->where('Property_ID', $this->propertyid)
                ->max('Sno');

            $mainEntryData = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $vno,
                'vtype' => $vtype,
                'sno' => 1,
                'sno1' => $maxsno,
                'vdate' => $this->ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'paycode' => $request->input('paytype'),
                'paytype' => $paytype->pay_type,
                'comments' => $request->input('narration'),
                'guestprof' => $request->input('guestprof'),
                'comp_code' => '',
                'travel_agent' => '',
                'roomno' => $bookingDetails->RoomNo,
                'amtdr' => $amtdr,
                'amtcr' => $amtcr,
                'roomcat' => $bookingDetails->RoomCat,
                'restcode' => 'FOM' . $this->propertyid,
                'billamount' => $amount,
                'taxper' => 0,
                'onamt' => 0,
                'taxstru' => $request->input('tax_stru') ?? '',
                'refdocid' => $request->input('docid'),
                'foliono' => $bookingDetails->BookNo,
                'taxcondamt' => 0,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            DB::table($tablename)->insert($mainEntryData);

            $taxStru = $request->input('tax_stru');
            if (!empty($taxStru)) {
                $taxStructures = DB::table('taxstru')
                    ->where('propertyid', $this->propertyid)
                    ->where('str_code', $taxStru)
                    ->get();

                if (!$taxStructures->isEmpty()) {
                    foreach ($taxStructures as $tax) {
                        $rate = $tax->rate;
                        if ($rate != null) {
                            $taxAmount = $amount * $rate / 100;
                            $amtdrTaxed = ($advtype == 'Refund') ? $taxAmount : 0.00;
                            $amtcrTaxed = ($advtype == 'Refund') ? 0.00 : $taxAmount;

                            $taxName = DB::table('revmast')
                                ->where('propertyid', $this->propertyid)
                                ->where('rev_code', $tax->tax_code)
                                ->value('name');

                            if (!$taxName) {
                                DB::rollBack();
                                return redirect('reservationlist')->with('error', 'Tax name not found');
                            }

                            $comments = $taxName . ', ' . 'Room No: ' . $bookingDetails->RoomNo;

                            $taxEntryData = [
                                'propertyid' => $this->propertyid,
                                'docid' => $docid,
                                'vno' => $vno,
                                'vtype' => $vtype,
                                'sno' => $tax->sno + 1,
                                'sno1' => $bookingDetails->Sno,
                                'vdate' => $this->ncurdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $vprefix,
                                'paycode' => $tax->tax_code,
                                'comments' => $comments,
                                'guestprof' => $request->input('guestprof'),
                                'roomno' => $bookingDetails->RoomNo,
                                'amtcr' => $amtcrTaxed,
                                'amtdr' => $amtdrTaxed,
                                'roomcat' => $bookingDetails->RoomCat,
                                'restcode' => 'FOM' . $this->propertyid,
                                'billamount' => 0.00,
                                'taxper' => $rate,
                                'taxstru' => $taxStru,
                                'onamt' => $amount,
                                'refdocid' => $request->input('docid'),
                                'foliono' => $bookingDetails->BookNo,
                                'taxcondamt' => 0.00,
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];

                            DB::table($tablename)->insert($taxEntryData);
                        }
                    }
                }
            }

            $updatedRows = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            if (!$updatedRows) {
                DB::rollBack();
                return redirect('reservationlist')->with('error', 'Failed to update voucher prefix');
            }

            DB::commit();
            return redirect('reservationlist')->with('success', 'Advance Deposit Successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('reservationlist')->with('error', 'Failed to process advance deposit: ' . $e->getMessage());
        }
    }


    public function revcancel(Request $request)
    {
        $DocId = base64_decode($request->input('DocId'));

        try {
            $updatebooking = DB::table('booking')
                ->where('Property_ID', $this->propertyid)
                ->where('DocId', $DocId)
                ->update([
                    'Cancel' => 'N',
                    'CancelUName' => '',
                    'ResStatus' => 'Confirm'
                ]);

            $updategrpbookingdetails = DB::table('grpbookingdetails')
                ->where('Property_ID', $this->propertyid)
                ->where('BookingDocid', $DocId)
                ->update([
                    'Cancel' => 'N',
                    'CancelUName' => '',
                    'CancelDate' => null,
                ]);
            return back()->with('success', 'Reservation Cancelled successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Cancel Reservation!');
        }
    }

    function openmenugroup(Request $request)
    {
        $permission = revokeopen(121316);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $menugroupdata = DB::table('itemgrp')
            ->select('itemgrp.*', 'depart.name as departname', 'depart.dcode')
            ->join('depart', 'depart.dcode', '=', 'itemgrp.restcode')
            ->where('itemgrp.property_id', $this->propertyid)
            ->whereNot('itemgrp.restcode', 'PURC' . $this->propertyid)
            ->orderBy('itemgrp.name', 'ASC')
            ->get();


        $departdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        return view('property.menugroup', ['data' => $menugroupdata, 'departdata' => $departdata]);
    }

    public function submittsundrymast(Request $request)
    {
        $permission = revokeopen(122013);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'sundryname' => 'required',
            'nature' => 'required',
        ]);
        $tablename = 'sundrymast';
        $existingname = DB::table($tablename)->where('propertyid', $this->propertyid)->where('name', $request->input('sundryname'))->first();
        if ($existingname) {
            return response()->json(['message' => 'Sundry Name already exists!'], 500);
        }
        $maxid = DB::table($tablename)->where('propertyid', $this->propertyid)->max('sundry_code');
        $code = ($maxid == null) ? '1' . $this->propertyid : substr($maxid, 0, -$this->ptlngth) + 1 . $this->propertyid;
        $data = [
            'name' => $request->input('sundryname'),
            'nature' => $request->input('nature'),
            'calcsign' => $request->input('calcsign'),
            'u_entdt' => $this->currenttime,
            'sysYN' => 'N',
            'sundry_code' => $code,
            'u_name' => Auth::user()->u_name,
            'propertyid' => $this->propertyid,
            'u_ae' => 'a',
        ];
        DB::table($tablename)->insert($data);
        return back()->with('msuccess', 'Sundry Master added successfully');
    }

    public function updatesundrymast(Request $request)
    {
        $permission = revokeopen(122013);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'upsundryname' => 'required',
            'upnature' => 'required',
        ]);
        $tablename = 'sundrymast';
        $existingname = DB::table($tablename)->where('propertyid', $this->propertyid)->where('name', $request->input('upsundryname'))->whereNot('sn', $request->input('upsn'))->first();
        if ($existingname) {
            return back()->with('error', 'Sundry Name already exists!');
        }

        $data = [
            'name' => $request->input('upsundryname'),
            'nature' => $request->input('upnature'),
            'calcsign' => $request->input('upcalcsign'),
            'u_updatedt' => $this->currenttime,
            'sysYN' => 'N',
            'u_name' => Auth::user()->u_name,
            'propertyid' => $this->propertyid,
            'u_ae' => 'e',
        ];

        DB::table($tablename)->where('sn', $request->input('upsn'))->update($data);

        return back()->with('success', 'Sundry Master updated successfully');
    }

    public function opensundrymaster(Request $request)
    {
        $permission = revokeopen(122013);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('sundrymast', 'Sundry Master Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $sundrydata = DB::table('sundrymast')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        return view('property.sundrymaster', ['data' => $sundrydata]);
    }

    function openmenuitem(Request $request)
    {
        $permission = revokeopen(121318);

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
            'itemmast.RestCode'
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
            ->leftJoin('itemrate', function ($join) {
                $join->on('itemrate.ItemCode', '=', 'itemmast.Code')
                    ->where('itemrate.Property_ID', '=', $this->propertyid);
            })
            ->where('itemmast.Property_ID', '=', $this->propertyid)
            ->groupBy('itemmast.Code')
            ->groupBy('itemmast.RestCode')
            ->get();

        $itemrate = DB::table('itemrate')
            ->where('Property_ID', $this->propertyid)
            ->orderBy('ItemCode', 'ASC')
            ->get();
        $itemgrp = DB::table('itemgrp')->where('property_id', $this->propertyid)->orderBy('name', 'ASC')->get();
        $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $itemnames = DB::table('items')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $unit = DB::table('unitmast')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $itemcatmast = DB::table('itemcatmast')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $kitchen = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'Kitchen')->orderBy('name', 'ASC')->get();
        return view('property.menuitem', [
            'itemmast' => $itemmast,
            'itemrate' => $itemrate,
            'kitchen' => $kitchen,
            'restaurentdata' => $restaurentdata,
            'itemgrp' => $itemgrp,
            'itemnames' => $itemnames,
            'unit' => $unit,
            'itemcatmast' => $itemcatmast
        ]);
    }

    public function openmenucategory(Request $request)
    {
        $permission = revokeopen(121317);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $itemcatmast = DB::table('itemcatmast')
            ->select('itemcatmast.*', 'depart.name as departname', 'taxstru.name as taxstruname', 'subgroup.name as subgrpname')
            ->leftJoin('depart', 'depart.dcode', '=', 'itemcatmast.restcode')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'itemcatmast.AcCode')
            ->where('itemcatmast.propertyid', $this->propertyid)
            ->groupBy('itemcatmast.Code', 'itemcatmast.RestCode')
            ->orderBy('itemcatmast.name', 'ASC')
            ->get();
        $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $subgroupdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->where('nature', 'Sale')->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')->where('propertyid', $this->propertyid)
            ->distinct()
            ->get();

        return view('property.menucategory', [
            'data' => $itemcatmast,
            'restaurentdata' => $restaurentdata,
            'subgroupdata' => $subgroupdata,
            'taxstrudata' => $taxstrudata
        ]);
    }

    public function submitmenucategory(Request $request)
    {
        $permission = revokeopen(121317);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'name' => 'required',
            'restcode' => 'required',
            'taxstru' => 'required',
        ]);
        $tableName = 'itemcatmast';
        $existingname = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->where('Name', $request->input('name'))
            ->where('RestCode', $request->input('restcode'))
            ->first();
        if ($existingname) {
            return back()->with('error', 'Category Name already exists!');
        }
        function skipfirst($string, $numToSkip)
        {
            return substr($string, $numToSkip) + 1;
        }

        $maxcode = DB::table('revmast')
            ->where('propertyid', $this->propertyid)
            ->where('rev_code', 'like', 'mt%')
            ->orderByRaw('CAST(SUBSTRING(rev_code, 3) AS UNSIGNED) DESC')
            ->value('rev_code');

        if (substr($maxcode, 0, 2) != 'MT') {
            $code = 'MT' . $this->propertyid . '1';
        } else {
            $codebe = skipfirst($maxcode, $this->ptlngth + 2);
            $code = 'MT' . $this->propertyid . $codebe;
        }

        if ($request->input('flag') == 'Charge') {
            $deskcode = $request->input('restcode');
            $field_type = 'C';
        } else {
            $deskcode = '';
            $field_type = '';
        }

        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $request->input('restcode'))->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $request->input('restcode'))->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';

        try {
            $insertdata = [
                'rev_code' => $code,
                'name' => $shortname . ' - ' . $request->input('name'),
                'short_name' => $shortname,
                'ac_code' => $request->input('AcCode'),
                'tax_stru' => $request->input('taxstru'),
                'type' => $request->input('flag') == 'Category' ? 'Dr' : $request->input('type'),
                'flag_type' => $request->input('flag'),
                'Desk_code' => $deskcode,
                'field_type' => $field_type,
                'u_entdt' => $this->currenttime,
                'propertyid' => $this->propertyid,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
                'SysYN' => 'N',
            ];
            $itemcatmastdata = [
                'Code' => $code,
                'Name' => $request->input('name'),
                'RestCode' => $request->input('restcode'),
                'TaxStru' => $request->input('taxstru'),
                'AcCode' => $request->input('AcCode'),
                'OutletYN' => $outyn,
                'Flag' => $request->input('flag'),
                'RoundOff' => 'No',
                'CatType' => $request->input('type'),
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
            return back()->with('success', 'Item Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item!' . $e->getMessage() . 'On Line: ' . $e->getLine());
        }
    }

    public function updatemenucategory(Request $request)
    {
        $permission = revokeopen(121317);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'upname' => 'required',
            'uprestcode' => 'required',
            'uptaxstru' => 'required',
        ]);
        $tableName = 'itemcatmast';
        $existingname = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->where('Name', $request->input('name'))
            ->where('Code', '!=', $request->input('upcode'))
            ->first();
        if ($existingname) {
            return back()->with('error', 'Category Name already exists!');
        }
        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $request->input('uprestcode'))->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $request->input('uprestcode'))->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';
        try {
            $updatedata = [
                'name' => $shortname . ' - ' . $request->input('upname'),
                'short_name' => $shortname,
                'ac_code' => $request->input('upAcCode'),
                'tax_stru' => $request->input('uptaxstru'),
                'type' => $request->input('upflag') == 'Category' ? 'Dr' : $request->input('uptype'),
                'flag_type' => $request->input('upflag'),
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'SysYN' => 'N',
            ];
            $itemcatmastdata = [
                'Name' => $request->input('upname'),
                'RestCode' => $request->input('uprestcode'),
                'TaxStru' => $request->input('uptaxstru'),
                'AcCode' => $request->input('upAcCode'),
                'OutletYN' => $outyn,
                'Flag' => $request->input('upflag'),
                'RoundOff' => 'No',
                'CatType' => $request->input('uptype'),
                'DrCr' => $request->input('upflag') == 'Category' ? 'Dr' : 'Cr',
                'U_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'ActiveYN' => 'Y',
            ];
            DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $request->input('upcode'))->update($updatedata);
            DB::table($tableName)->where('propertyid', $this->propertyid)->where('RestCode', $request->input('uprestcode'))->where('Code', $request->input('upcode'))->update($itemcatmastdata);
            return back()->with('success', 'Item Category Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item Category!' . $e);
        }
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


    public function submitmenuitem(Request $request)
    {
        $permission = revokeopen(121318);

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
            ->where('RestCode', $request->input('restcode'))
            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Name already exists!');
        }


        $itemname = DB::table('items')->where('propertyid', $this->propertyid)->where('icode', $request->input('itemname'))->first();

        try {
            $insertdata = [
                'Code' => $request->input('itemname'),
                'Name' => $itemname->name,
                'property_id' => $this->propertyid,
                'RestCode' => $request->input('restcode'),
                'ItemGroup' => $request->input('itemgrp'),
                'dishtype' => $request->input('dishtype'),
                'favourite' => $request->input('favourite'),
                'PurchRate' => '0',
                'MinStock' => '0',
                'MaxStock' => '0',
                'ReStock' => '0',
                'LPurRate' => '0',
                'LPurDate' => null,
                'DispCode' => $request->input('itemcode'),
                'ConvRatio' => '0',
                'IssueUnit' => '',
                'Specification' => '',
                'LabelName' => '',
                'LabelQty' => '',
                'LabelRemark1' => '',
                'LabelRemark2' => '',
                'LabelRemark3' => '',
                'LabelRemark4' => '',
                'ItemType' => '',
                'NType' => $request->input('type'),
                'iempic' => $request->input('itempic') ?? '',
                'Unit' => $request->input('unit'),
                'RateEdit' => $request->input('rateedit'),
                'ItemCatCode' => $request->input('itemcatmast'),
                'BarCode' => $request->input('barcode'),
                'Type' => 'Finish',
                'HSNCode' => $request->input('hsncode') ?? '',
                'DiscApp' => $request->input('discappl'),
                'SChrgApp' => $request->input('servicecharge'),
                'RateIncTax' => $request->input('rateinctax'),
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
                'RestCode' => $request->input('restcode'),
                'AppDate' => $request->input('applicabldate'),
                'Rate' => $request->input('salerate'),
                'Party' => '',
                'U_EntDt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'a',
            ];

            DB::table('itemrate')->insert($itemratedata);

            return back()->with('sucess', 'Item Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item!' . $e . ' On Line: ' . $e->getLine());
        }
    }

    public function updatemenuitem(Request $request)
    {
        $permission = revokeopen(121318);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // $validate = [
        //     'upname' => 'required',
        //     'uprestcode' => 'required',
        //     'upicode' => 'required',
        //     'upunit' => 'required',
        //     'upitemcatmast' => 'required',
        //     'upitemgrp' => 'required',
        //     'upkitchen' => 'required',
        //     'uprateedit' => 'required',
        // ];
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
                'favourite' => $request->input('upfavourite'),
                'ItemCatCode' => $request->input('upitemcatmast'),
                'BarCode' => $request->input('upbarcode'),
                'HSNCode' => $request->input('uphsncode') ?? '',
                'DiscApp' => $request->input('updiscappl'),
                'SChrgApp' => $request->input('upservicecharge'),
                'RateIncTax' => $request->input('uprateinctax'),
                'Kitchen' => $request->input('upkitchen'),
                'u_updaedt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'e',
                'ActiveYN' => $request->input('upactiveyn'),
            ];

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

    public function deletemenuitem(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121318);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {

            $chkkot = Kot::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkkot)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Item used in KOT'
                ]);
            }

            $chkstock = Stock::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkstock)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Item used in stock'
                ]);
            }

            $delete1 = DB::table('itemmast')
                ->where('Property_ID', $this->propertyid)
                ->where('Code', $ucode)
                ->delete();

            $delete2 = DB::table('itemrate')
                ->where('Property_ID', $this->propertyid)
                ->where('ItemCode', $ucode)
                ->delete();

            if ($delete1) {
                return back()->with('success', 'Item Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Item!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Item!');
        }
    }

    public function deletemenucategory(Request $request, $ucode)
    {
        $permission = revokeopen(121317);

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
                return back()->with('success', 'Item Category Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Item Category!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Item Category!');
        }
    }

    public function getitemdata(Request $request)
    {
        $itemdata = DB::table('items')
            ->where('propertyid', $this->propertyid)
            ->where('icode', $request->input('icode'))
            ->first();
        return json_encode($itemdata);
    }

    public function getupdatemenuitem(Request $request)
    {
        $itemdata = DB::table('itemmast')
            ->select('itemmast.*', 'itemrate.Rate', 'itemrate.AppDate')
            ->join('itemrate', 'itemrate.ItemCode', '=', 'itemmast.Code')
            ->where('itemmast.property_id', $this->propertyid)
            ->where('itemmast.Code', $request->input('code'))
            ->where('itemmast.RestCode', $request->input('restcode'))
            ->first();
        // return $itemdata;
        // $itemgrp = $itemdata->ItemGroup;
        $restcode = $itemdata->RestCode;
        $itemgrps = ItemGrp::where('property_id', $this->propertyid)->where('restcode', $restcode)->orderBy('name')->get();
        $itemcats = ItemCatMast::where('propertyid', $this->propertyid)->where('RestCode', $restcode)->orderBy('Name')->get();

        $data = [
            'itemgrps' => $itemgrps,
            'itemdata' => $itemdata,
            'itemcats' => $itemcats,
        ];
        return json_encode($data);
    }

    public function getupdatemenucategory(Request $request)
    {
        $itemcatmast = DB::table('itemcatmast')
            ->where('propertyid', $this->propertyid)
            ->where('Code', $request->input('code'))
            ->where('RestCode', $request->input('restcode'))
            ->first();
        return json_encode($itemcatmast);
    }

    public function getmaxitemcode(Request $request)
    {
        $maxcode = DB::table('itemmast')->where('Property_ID', $this->propertyid)->max('DispCode');
        $code = ($maxcode === null) ? '1' : ($code = $maxcode + 1);
        return json_encode($code);
    }

    function submitmenugroup(Request $request)
    {
        $permission = revokeopen(121316);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'name' => 'required',
            'type' => 'required',
        ];
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', $request->input('restcode'))
            ->where('name', $request->input('name'))
            ->where('property_id', $this->propertyid)
            ->first();

        if ($existingname) {
            return back()->with('error', 'Menu Group already exists!');
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

            return back()->with('success', 'Menu Group Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Menu Group!' . $e->getMessage());
        }
    }

    public function updatemenugroup(Request $request)
    {
        $permission = revokeopen(121316);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', $request->input('uprestcode'))
            ->where('name', $request->input('upname'))
            ->where('property_id', $this->propertyid)
            ->where('code', '!=', $request->input('upcode'))
            ->first();

        if ($existingname) {
            return back()->with('error', 'Menu Group already exists!');
        }

        try {
            $updatedata = [
                'name' => $request->input('upname'),
                'restcode' => $request->input('uprestcode'),
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'activeyn' => $request->input('upactiveyn'),
            ];

            DB::table($tableName)
                ->where('property_id', $this->propertyid)
                ->where('code', $request->input('upcode'))
                ->update($updatedata);

            return back()->with('success', 'Menu Group Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Menu Group!');
        }
    }

    public function deletemenugroup(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121316);

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
                return back()->with('success', 'Menu Group Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Menu Group!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Menu Group!');
        }
    }

    public function deletesundrymast(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(122013);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $jaldiwahasehato📢 = DB::table('sundrymast')
                ->where('propertyid', $this->propertyid)
                ->where('sundry_code', $ucode)
                ->where('sn', $sn)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('msucess', 'Sundry Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Sundry Master!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Sundry Master!' . $e);
        }
    }

    public function partymaster()
    {
        $permission = revokeopen(121612);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('partymaster', 'Party Data Analysis HMS', [0, 1, 2, 3, 4, 5, 6, 7, 8], []);
        $partydata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('group_code', '27' . $this->propertyid)
            ->orderBy('name', 'ASC')->get();
        $partydatamain = DB::table('acgroup')->where('propertyid', $this->propertyid)->get();
        $under_group = '27' . $this->propertyid;
        $acname = ACGroup::where('group_code', $under_group)->where('propertyid', $this->propertyid)->first();
        return view('property.partymaster', [
            'taxdata' => $partydata,
            'partydatamain' => $partydatamain,
            'acname' => $acname,
            'update' => false
        ]);
    }

    public function updatepartymaster(Request $request)
    {
        $permission = revokeopen(121612);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $ledgerdata = DB::table('subgroup')
            ->where('propertyid', $this->propertyid)
            ->where('sub_code', base64_decode($request->input('sub_code')))
            ->first();
        $groupname = DB::table('acgroup')->where('group_code', $ledgerdata->group_code)->first();
        $ledgerdatamain = DB::table('acgroup')->where('propertyid', $this->propertyid)->get();

        $ledgerdatasub = Ledger::where('subcode', base64_decode($request->input('sub_code')))->where('propertyid', $this->propertyid)->where('vtype', 'F_AO')->orderBy('vsno')->get();
        return view('property.updatepartymaster', [
            'ledgerdata' => $ledgerdata,
            'ledgerdatamain' => $ledgerdatamain,
            'groupname' => $groupname,
            'ledgerdatasub' => $ledgerdatasub,
            'update' => true
        ]);
    }

    public function openitemgroup(Request $request)
    {
        $permission = revokeopen(121613);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('itemgrp', 'Menu Group Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $menugroupdata = DB::table('itemgrp')
            ->select('itemgrp.*', 'depart.name as departname', 'depart.dcode')
            ->join('depart', 'depart.dcode', '=', 'itemgrp.restcode')
            ->where('itemgrp.property_id', $this->propertyid)
            ->where('itemgrp.restcode', 'PURC' . $this->propertyid)
            ->orderBy('itemgrp.name', 'ASC')
            ->get();

        $departdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        return view('property.itemgroup', ['data' => $menugroupdata, 'departdata' => $departdata]);
    }

    function submititemgroup(Request $request)
    {
        $permission = revokeopen(121613);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'name' => 'required',
            'type' => 'required',
        ];
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', 'PURC' . $this->propertyid)
            ->where('name', $request->input('name'))
            ->where('property_id', $this->propertyid)
            ->first();

        if ($existingname) {
            return back()->with('message', 'Item Group already exists!');
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
                'restcode' => 'PURC' . $this->propertyid,
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

    public function updateitemgroup(Request $request)
    {
        $permission = revokeopen(121613);

        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $tableName = 'itemgrp';

        $existingname = DB::table($tableName)
            ->where('restcode', 'PURC' . $this->propertyid)
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
                'type' => $request->uptype,
                'cattype' => $request->upcategorytype,
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

    public function openitemcategory(Request $request)
    {
        $permission = revokeopen(121614);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $this->ExportTable();
        $this->DownloadTable('itemcategory', 'Item Category Data Analysis HMS', [0, 1, 2, 3], [1, 2, 3]);
        $itemcatmast = DB::table('itemcatmast')
            ->select('itemcatmast.*', 'depart.name as departname', 'taxstru.name as taxstruname', 'subgroup.name as subgrpname')
            ->leftJoin('depart', 'depart.dcode', '=', 'itemcatmast.restcode')
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'itemcatmast.AcCode')
            ->where('itemcatmast.propertyid', $this->propertyid)
            ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid)
            ->groupBy('itemcatmast.Code')
            ->orderBy('itemcatmast.name', 'ASC')
            ->get();
        $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('rest_type', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $subgroupdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('group_code', ['23' . $this->propertyid, '10' . $this->propertyid, '14' . $this->propertyid,])->orderBy('name', 'ASC')->get();
        $taxstrudata = DB::table('taxstru')->where('propertyid', $this->propertyid)
            ->distinct()
            ->get();

        return view('property.itemcategory', [
            'data' => $itemcatmast,
            'restaurentdata' => $restaurentdata,
            'subgroupdata' => $subgroupdata,
            'taxstrudata' => $taxstrudata
        ]);
    }

    public function submititemcategory(Request $request)
    {
        $permission = revokeopen(121614);
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
            ->where('RestCode', 'PURC' . $this->propertyid)
            ->first();
        if ($existingname) {
            return back()->with('error', 'Item Category Name already exists!');
        }
        function skipfirsti($string, $numToSkip)
        {
            return substr($string, $numToSkip) + 1;
        }
        $maxcodeRow = DB::table('revmast')
            ->select('rev_code')
            ->where('propertyid', $this->propertyid)
            ->where('rev_code', 'like', 'MT%')
            ->orderByRaw("CAST(SUBSTRING(rev_code, " . (strlen('MT' . $this->propertyid) + 1) . ", LENGTH(rev_code)) AS UNSIGNED) DESC")
            ->first();

        if (!$maxcodeRow) {
            $code = 'MT' . $this->propertyid . '1';
        } else {
            $numericPart = (int) substr($maxcodeRow->rev_code, strlen('MT' . $this->propertyid));
            $code = 'MT' . $this->propertyid . ($numericPart + 1);
        }


        // if ($request->input('flag') == 'Charge') {
        //     $deskcode = $request->input('restcode');
        //     $field_type = 'C';
        // } else {
        //     $deskcode = '';
        //     $field_type = '';
        // }

        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'PURC' . $this->propertyid)->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'PURC' . $this->propertyid)->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';

        try {
            $insertdata = [
                'rev_code' => $code,
                'name' => $shortname . ' - ' . $request->input('name'),
                'short_name' => $shortname,
                'ac_code' => $request->input('AcCode'),
                'tax_stru' => $request->input('taxstru'),
                'type' => $request->input('flag') == 'Category' ? 'Dr' : $request->input('type'),
                'flag_type' => 'PUR',
                'Desk_code' => 'PURC' . $this->propertyid,
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
                'RestCode' => 'PURC' . $this->propertyid,
                'TaxStru' => $request->input('taxstru'),
                'AcCode' => $request->input('AcCode'),
                'OutletYN' => $outyn,
                'Flag' => $request->input('flag'),
                'RoundOff' => 'No',
                'CatType' => $request->input('type'),
                'cattyper' => $request->input('cattyper'),
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
            return back()->with('success', 'Item Category Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item Category!' . $e);
        }
    }

    public function updateitemcategory(Request $request)
    {
        $permission = revokeopen(121614);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'upname' => 'required',
            'uptaxstru' => 'required',
        ]);
        $tableName = 'itemcatmast';
        $existingname = DB::table($tableName)
            ->where('propertyid', $this->propertyid)
            ->where('Name', $request->input('name'))
            ->where('Code', '!=', $request->input('upcode'))
            ->first();
        if ($existingname) {
            return back()->with('error', 'Category Name already exists!');
        }
        $shortname = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'PURC' . $this->propertyid)->value('short_name');
        $outletyn = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', 'PURC' . $this->propertyid)->value('rest_type');
        $outyn = $outletyn == 'Outlet' ? 'Y' : 'N';
        try {
            $updatedata = [
                'name' => $shortname . ' - ' . $request->input('upname'),
                'short_name' => $shortname,
                'ac_code' => $request->input('upAcCode'),
                'tax_stru' => $request->input('uptaxstru'),
                'type' => $request->input('upflag') == 'Category' ? 'Dr' : $request->input('uptype'),
                'flag_type' => 'PUR',
                'Desk_code' => 'PURC' . $this->propertyid,
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
                'cattyper' => $request->input('upcattyper'),
                'DrCr' => $request->input('upflag') == 'Category' ? 'Dr' : 'Cr',
                'U_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
                'ActiveYN' => 'Y',
            ];
            DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $request->input('upcode'))->update($updatedata);
            DB::table($tableName)->where('propertyid', $this->propertyid)->where('Code', $request->input('upcode'))->update($itemcatmastdata);
            return back()->with('success', 'Item Category Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item Category!' . $e);
        }
    }

    function openitementry(Request $request)
    {
        $permission = revokeopen(121616);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        // $this->ExportTable();
        // $this->DownloadTable('menuitem', 'Menu Item Data Analysis HMS', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], [1, 2, 3]);
        $itemmast = ItemMast::select(
            'itemmast.Name as itemname',
            'itemmast.Code',
            'itemmast.sn',
            'itemmast.PurchRate',
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
            'itemmast.RestCode'
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
            ->leftJoin('itemrate', function ($join) {
                $join->on('itemrate.ItemCode', '=', 'itemmast.Code')
                    ->where('itemrate.Property_ID', '=', $this->propertyid);
            })
            ->where('itemmast.Property_ID', '=', $this->propertyid)
            ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->get();

        $itemrate = DB::table('itemrate')
            ->where('Property_ID', $this->propertyid)
            ->orderBy('ItemCode', 'ASC')
            ->get();
        $itemgrp = DB::table('itemgrp')->where('restcode', 'PURC' . $this->propertyid)->where('property_id', $this->propertyid)->orderBy('name', 'ASC')->get();
        $restaurentdata = DB::table('depart')->where('propertyid', $this->propertyid)->whereIn('nature', ['Room Service', 'Outlet'])->orderBy('name', 'ASC')->get();
        $itemnames = DB::table('items')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $unit = DB::table('unitmast')->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $itemcatmast = DB::table('itemcatmast')->where('RestCode', 'PURC' . $this->propertyid)->where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $kitchen = DB::table('depart')->where('propertyid', $this->propertyid)->where('rest_type', 'Kitchen')->orderBy('name', 'ASC')->get();
        return view('property.itementry', [
            'itemmast' => $itemmast,
            'itemrate' => $itemrate,
            'kitchen' => $kitchen,
            'restaurentdata' => $restaurentdata,
            'itemgrp' => $itemgrp,
            'itemnames' => $itemnames,
            'unit' => $unit,
            'itemcatmast' => $itemcatmast
        ]);
    }

    public function itementrysubmit(Request $request)
    {
        $permission = revokeopen(121616);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = [
            'itemname' => 'required',
            'unit' => 'required',
            'itemcatmast' => 'required',
            'itemgrp' => 'required',
            'salerate' => 'required',
        ];
        $tableName = 'itemmast';

        $existingcode = DB::table($tableName)
            ->where('Property_ID', $this->propertyid)
            ->where('DispCode', $request->input('itemname'))
            ->where('RestCode', 'PURC' . $this->propertyid)
            ->first();
        $maxcode = DB::table($tableName)->where('property_id', $this->propertyid)->max('Code');
        $code = ($maxcode === null) ? $this->propertyid . '1' : ($code = $this->propertyid . substr($maxcode, $this->ptlngth) + 1);

        if ($existingcode) {
            return back()->with('error', 'Item Code already exists!');
        }

        $existingname = DB::table($tableName)
            ->where('Property_ID', $this->propertyid)
            ->where('Code', $request->input('itemname'))
            ->where('RestCode', 'PURC' . $this->propertyid)
            ->first();

        if ($existingname) {
            return back()->with('error', 'Item Name already exists!');
        }

        $itemname = DB::table('items')->where('propertyid', $this->propertyid)->where('icode', $request->input('itemname'))->first();

        try {
            $insertdata = [
                'Code' => $request->input('itemname'),
                'Name' => $itemname->name,
                // 'itemcode' => $request->input('itemname'),
                'property_id' => $this->propertyid,
                'RestCode' => 'PURC' . $this->propertyid,
                'ItemGroup' => $request->input('itemgrp'),
                'dishtype' => '',
                'favourite' => '0',
                'PurchRate' => $request->input('salerate') ?? '0.00',
                'MinStock' => $request->input('minstock') ?? '0.000',
                'MaxStock' => $request->input('maxstock') ?? '0.000',
                'ReStock' => $request->input('recordstock') ?? '0.000',
                'LPurRate' => '0',
                'LPurDate' => null,
                'DispCode' => '',
                'ConvRatio' => $request->input('convratio') ?? '0.000',
                'IssueUnit' => $request->input('wtunit') ?? '',
                'Specification' => '',
                'LabelName' => '',
                'LabelQty' => '',
                'LabelRemark1' => '',
                'LabelRemark2' => '',
                'LabelRemark3' => '',
                'LabelRemark4' => '',
                'ItemType' => 'Store',
                'NType' => '',
                'iempic' => $request->input('itempic') ?? '',
                'Unit' => $request->input('unit'),
                'RateEdit' => '',
                'ItemCatCode' => $request->input('itemcatmast'),
                'BarCode' => $request->input('barcode'),
                'Type' => $request->input('grouptype'),
                'HSNCode' => $request->input('hsncode') ?? '',
                'DiscApp' => '',
                'SChrgApp' => '',
                'RateIncTax' => '',
                'Kitchen' => '',
                'U_EntDt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'a',
                'ActiveYN' => $request->input('activeyn'),
            ];

            DB::table($tableName)->insert($insertdata);

            return back()->with('success', 'Item Inserted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Insert Item!' . $e);
        }
    }

    public function getupdateitemcategory(Request $request)
    {
        $permission = revokeopen(121616);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $itemdata = DB::table('itemmast')
            ->select('itemmast.*')
            ->where('itemmast.property_id', $this->propertyid)
            ->where('itemmast.Code', $request->input('code'))
            ->where('itemmast.RestCode', $request->input('restcode'))
            ->first();
        // $itemgrp = $itemdata->ItemGroup;
        $restcode = $itemdata->RestCode;
        $itemgrps = ItemGrp::where('property_id', $this->propertyid)->where('restcode', $restcode)->orderBy('name')->get();
        $itemcats = ItemCatMast::where('propertyid', $this->propertyid)->where('RestCode', $restcode)->orderBy('Name')->get();

        $data = [
            'itemgrps' => $itemgrps,
            'itemdata' => $itemdata,
            'itemcats' => $itemcats,
        ];
        return json_encode($data);
    }

    public function updateitementry(Request $request)
    {
        $permission = revokeopen(121616);
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
        ];
        $tableName = 'itemmast';

        try {
            $updatedata = [
                'ItemGroup' => $request->input('upitemgrp'),
                'Unit' => $request->input('upunit'),
                'PurchRate' => $request->input('upsalerate') ?? '0.00',
                'MinStock' => $request->input('upminstock') ?? '0.000',
                'MaxStock' => $request->input('upmaxstock') ?? '0.000',
                'ReStock' => $request->input('uprecordstock') ?? '0.000',
                'IssueUnit' => $request->input('upwtunit'),
                'ItemCatCode' => $request->input('upitemcatmast'),
                'BarCode' => $request->input('upbarcode'),
                'HSNCode' => $request->input('uphsncode') ?? '',
                'u_updaedt' => $this->currenttime,
                'U_Name' => Auth::user()->u_name,
                'U_AE' => 'e',
                'ActiveYN' => $request->input('upactiveyn'),
            ];

            DB::table($tableName)
                ->where('Property_ID', $this->propertyid)
                ->where('Code', $request->input('upcode'))
                ->where('RestCode', 'PURC' . $this->propertyid)
                ->update($updatedata);

            return back()->with('success', 'Item Updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Update Item!' . $e);
        }
    }

    public function opengrcprinting()
    {
        if ($this->revokeopen(141113)->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $data = GuestFolio::select([
            'roomocc.docid',
            'roomocc.name',
            'guestfolio.add1',
            'guestfolio.add2',
            'guestfolio.Name AS Guest_Name',
            'cities.cityname AS City',
            'guestprof.nationality',
            'guestprof.mobile_no',
            'guestprof.email_id',
            'guestprof.dob',
            'guestprof.anniversary',
            'guestfolio.arrfrom',
            'guestfolio.destination',
            'guestfolio.folio_no as Folio_No',
            'room_cat.name as room_category',
            'roomocc.adult',
            'roomocc.children',
            'roomocc.roomrate as Rate',
            'roomocc.planamt',
            'roomocc.rrtaxinc as Tax_Inc',
            'plan_mast.name as plan_name',
            'roomocc.chkindate as CheckIn_Date',
            'roomocc.chkintime as CheckIn_Time',
            'roomocc.depdate as Dep_Date',
            'roomocc.deptime as deptime',
            'guestfolio.travelmode',
            'guestprof.id_proof',
            'guestprof.idproof_no',
            'guestprof.paymentMethod',
            'subgroup.name as compname',
            'ST.name as travelagent',
            'busssource.name as business_source',
            'booking.BookedBy',
            'booking.RefBookNo',
            'guestprof.pic_path',
            'guestprof.guestsign',
            'roomocc.roomno as Room_No',
            'guestprof.u_name'
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
            ->where('roomocc.type', '!=', 'C')
            ->whereNotNull('guestfolio.folio_no')
            ->where('guestfolio.folio_no', '!=', '')
            ->where('guestfolio.propertyid', $this->propertyid)
            ->orderBy('guestfolio.docid')
            ->get();

        return view('property.grcprinting', compact('data'));
    }

    public function deletepartymaster(Request $request, $sn, $sub_code)
    {
        $permission = revokeopen(121612);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $sub_code = base64_decode($request->input('sub_code'));
            $exists = DB::table('subgroup')->where('propertyid', $this->propertyid)->where('group_code', $sub_code)->first();
            $exists_tax = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where(function ($query) use ($sub_code) {
                    $query->where('ac_code', $sub_code)
                        ->orWhere('payable_ac', $sub_code)
                        ->orWhere('unregistered_ac', $sub_code);
                })
                ->first();
            if ($exists || $exists_tax) {
                return back()->with('error', "This Entity Has Been Used for Some Items, So It Can Not Be Deleted. Please Delete Its Usages First.");
            }
            $jaldiwahasehato📢 = DB::table('subgroup')->where('sub_code', $sub_code)->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Party Master Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Party Master');
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deleteitemgroup(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121613);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $chkitemmast = ItemMast::where('Property_ID', $this->propertyid)->where('ItemGroup', $ucode)->first();
            if (!is_null($chkitemmast)) {
                return back()->with(
                    'status',
                    'info',
                    'message',
                    'Group used in Menu Item'
                );
            }
            $jaldiwahasehato📢 = DB::table('itemgrp')
                ->where('property_id', $this->propertyid)
                ->where('code', $ucode)
                ->where('sn', $sn)
                ->delete();

            if ($jaldiwahasehato📢) {
                return back()->with('success', 'Menu Group Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Menu Group!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Menu Group!');
        }
    }

    public function deleteitemcategory(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121614);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {

            $chkitemmast = ItemMast::where('Property_ID', $this->propertyid)->where('ItemCatCode', $ucode)->first();
            if (!is_null($chkitemmast)) {
                return back()->with(
                    'status',
                    'info',
                    'message',
                    'Category used in Menu Item'
                );
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
                return back()->with('success', 'Item Category Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Item Category!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Item Category!');
        }
    }
    public function deletemenuentry(Request $request, $sn, $ucode)
    {
        $permission = revokeopen(121318);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {

            $chkkot = Kot::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkkot)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Item used in KOT'
                ]);
            }

            $chkstock = Stock::where('propertyid', $this->propertyid)->where('item', $ucode)->first();
            if (!is_null($chkstock)) {
                return back()->with(
                    'status',
                    'info',
                    'message',
                    'Item used in stock'
                );
            }

            $delete1 = DB::table('itemmast')
                ->where('Property_ID', $this->propertyid)
                ->where('Code', $ucode)
                ->delete();

            $delete2 = DB::table('itemrate')
                ->where('Property_ID', $this->propertyid)
                ->where('ItemCode', $ucode)
                ->delete();

            if ($delete1) {
                return back()->with('success', 'Item Deleted Successfully');
            } else {
                return back()->with('error', 'Unable to Delete Item!');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Unable to Delete Item!');
        }
    }
}
