<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\ACGroup;
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
use App\Models\EnviroInventory;
use App\Models\EnviroFom;
use App\Models\EnviroGeneral;
use App\Models\EnviroPos;
use App\Models\Gin;
use App\Models\GodownMast;
use App\Models\GrpBookinDetail;
use App\Models\Indent;
use App\Models\Indent1;
use App\Models\Ledger;
use App\Models\PlanDetail;
use App\Models\Purch1;
use App\Models\Purch2;
use App\Models\RoomCat;
use App\Models\Sagar;
use App\Models\Stock;
use App\Models\RoomOcc;
use App\Models\Sale2;
use App\Models\States;
use App\Models\Sundrytype;
use App\Models\Suntran;
use App\Models\TaxStructure;
use App\Models\UnitMast;
use App\Models\User;
use App\Models\VoucherPrefix;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Client\NetworkExceptionInterface;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumper;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $compcode;
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
            $this->compcode = Companyreg::where('propertyid', Auth::user()->propertyid)->value('comp_code');
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

    public function revokeopen($code)
    {
        $value = Menuhelp::where('propertyid', $this->propertyid)->where('username', Auth::user()->name)->where('code', $code)->first();
        return $value;
    }

    public function openmrentry(Request $request)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = Gin::select(
            'gin.*',
            'subgroup.name as subname',
            DB::raw('COUNT(stock.docid) as itemcount'),
            DB::raw('stock.contradocid')
        )
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'gin.partycode')
            ->leftJoin('stock', 'gin.docid', '=', 'stock.docid')
            ->where('gin.propertyid', $this->propertyid)
            ->where('gin.delflag', 'N')
            ->groupBy('gin.docid', 'subgroup.name')
            ->get();

        $maxvno = Gin::where('propertyid', $this->propertyid)->max('vno');
        if (isset($maxvno)) {
            $mrno = $maxvno + 1;
        } else {
            $mrno = 1;
        }
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();
        $superwiser = Auth::user()->superwiser;

        return view('property.mrentry', [
            'data' => $data,
            'ncurdate' => $this->ncurdate,
            'mrno' => $mrno,
            'godown' => $godown,
            'items' => $items,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser
        ]);
    }

    public function mrentryparty(Request $request)
    {
        $vtype = $request->input('vtype');
        $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('nature', ['Supplier'])->get();
        $maxvno = Gin::where('propertyid', $this->propertyid)->where('vtype', $vtype)->max('vno');
        if (isset($maxvno)) {
            $mrno = $maxvno + 1;
        } else {
            $mrno = 1;
        }

        $data = [
            'subgroup' => $subgroup,
            'mrno' => $mrno,
            'vtype' => $vtype
        ];

        return json_encode($data);
    }

    public function checkduplicatechalan(Request $request)
    {
        $chalno = $request->input('chalno');
        $partycode = $request->input('partycode');
        $chk = Gin::where('propertyid', $this->propertyid)
            ->where(function ($query) use ($partycode) {
                $query->where('partycode', $partycode)
                    ->orWhere('partyname', $partycode);
            })
            ->where('chalno', $chalno)
            ->first();
        if (isset($chk)) {
            return response([
                'duplicate' => true
            ]);
        } else {
            return response([
                'duplicate' => false
            ]);
        }
    }

    public function checkduplicatememinvno(Request $request)
    {
        $invoiceno = $request->input('invoiceno');
        $partycode = $request->input('partycode');
        $chk = Gin::where('propertyid', $this->propertyid)
            ->where(function ($query) use ($partycode) {
                $query->where('partycode', $partycode)
                    ->orWhere('partyname', $partycode);
            })
            ->where('meminvno', $invoiceno)
            ->first();
        if (isset($chk)) {
            return response([
                'duplicate' => true
            ]);
        } else {
            return response([
                'duplicate' => false
            ]);
        }
    }

    public function mrentrysubmit(Request $request)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'mrno' => 'required',
            'vdate' => 'required',
            'vtype' => 'required',
        ]);

        // $ranges = DateHelper::calculateDateRanges($request->input('vdate'));

        // VoucherPrefix::where('propertyid', $this->propertyid)
        //     ->where('v_type', $vtype)
        //     ->increment('start_srl_no');

        // $voucherPrefix = VoucherPrefix::where('propertyid', $this->propertyid)
        //     ->where('v_type', $vtype)
        //     ->first();
        // $vno = $voucherPrefix->start_srl_no;
        $vtype = $request->input('vtype');
        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        $vno = $chkvpf->start_srl_no + 1;
        $vprefix = $chkvpf->prefix;

        $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

        $chkpartyname = SubGroup::where('sub_code', $request->input('partycode'))->first();
        $partycode = $request->input('partycode');
        $partyname = '';
        if (!isset($chkpartyname)) {
            $partycode = '';
            $partyname = $request->input('partycode');
        }

        $indata = [
            'propertyid' => $this->propertyid,
            'docid' => $docid,
            'vno' => $vno,
            'vtype' => $vtype,
            'vprefix' => $vprefix,
            'ocdocid' => '',
            'ocdate' => null,
            'vdate' => $request->input('vdate'),
            'partycode' => $partycode,
            'partyname' => $partyname,
            'pono' => $request->input('pono') ?? '',
            'chalno' => $request->input('chalno'),
            'chaldate' => $request->input('chaldate'),
            'meminvno' => $request->input('meminvno') ?? '0',
            'meminvdate' => $request->input('meminvdate') ?? null,
            'indentno' => $request->input('indentno') ?? '0',
            'inspectedby' => $request->input('inspectedby'),
            'approvedby' => $request->input('approvedby'),
            'remark' => $request->input('remark') ?? '',
            'delflag' => 'N',
            'u_entdt' => $this->currenttime,
            'u_ae' => 'a',
        ];

        Gin::insert($indata);

        $netamount = 0;
        $totalitem = $request->input('totalitem');
        for ($i = 1; $i <= $totalitem; $i++) {
            $itemmast = ItemMast::where('Property_ID', $this->propertyid)->where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();
            $amount = $request->input('itemrate' . $i) * $request->input('accqty' . $i);
            $netamount += $amount;
            $stock = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $vno,
                'sno' => $i,
                'restcode' => 'PURC' . $this->propertyid,
                'vtype' => $vtype,
                'vdate' => $request->input('vdate'),
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'item' => $request->input('item' . $i),
                'issueunit' => $request->input('wtunit' . $i) ?? '',
                'qtyiss' => 0,
                'qtyrec' => $request->input('accqty' . $i),
                'unit' => $itemmast->Unit ?? '',
                'rate' => $request->input('itemrate' . $i),
                'partycode' => $request->input('partycode'),
                'amount' => $amount,
                'taxper' => '',
                'taxamt' => '',
                'discper' => '',
                'discamt' => '',
                'description' => $request->input('specification' . $i) ?? '',
                'specification' => $request->input('specification' . $i) ?? '',
                'total' => $netamount,
                'discapp' => $itemmast->DiscApp ?? '',
                'roundoff' => '',
                'departcode' => $itemmast->RestCode ?? '',
                'godowncode' => $request->input('godown' . $i),
                'chalqty' => $request->input('chalqty' . $i),
                'recdqty' => $request->input('recdqty' . $i),
                'rejqty' => $request->input('rejqty' . $i),
                'accqty' => $request->input('accqty' . $i),
                'recdunit' => $request->input('wtunit' . $i) ?? '',
                'itemrate' => $itemmast->PurchRate ?? '',
                'itemrestcode' => $itemmast->RestCode ?? '',
                'convratio' => $request->input('convratio' . $i) ?? '',
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];

            Stock::insert($stock);
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->where('prefix', $vprefix)
            ->increment('start_srl_no');

        return back()->with('success', 'Mr Entry Submitted');
    }

    public function updatemrentry(Request $request)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = $request->query('docid');
        $gin = Gin::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
        $stock = Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('nature', ['Supplier'])->get();

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.mrentryupdate', [
            'ncurdate' => $this->ncurdate,
            'godown' => $godown,
            'items' => $items,
            'gin' => $gin,
            'stock' => $stock,
            'subgroup' => $subgroup,
            'units' => $units
        ]);
    }

    public function mrentryupdate(Request $request)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'docid' => 'required',
        ]);

        $ranges = DateHelper::calculateDateRanges($request->input('vdate'));

        $docid = $request->input('docid');
        $gin = Gin::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
        $chkpartyname = SubGroup::where('sub_code', $request->input('partycode'))->first();
        $partycode = $request->input('partycode');
        $partyname = '';
        if (!isset($chkpartyname)) {
            $partycode = '';
            $partyname = $request->input('partycode');
        }

        $updategin = [
            'vdate' => $request->input('vdate'),
            'partycode' => $partycode,
            'partyname' => $partyname,
            'pono' => $request->input('pono') ?? '',
            'chalno' => $request->input('chalno'),
            'chaldate' => $request->input('chaldate'),
            'meminvno' => $request->input('meminvno') ?? '0',
            'meminvdate' => $request->input('meminvdate') ?? null,
            'indentno' => $request->input('indentno') ?? '0',
            'inspectedby' => $request->input('inspectedby'),
            'approvedby' => $request->input('approvedby'),
            'remark' => $request->input('remark') ?? '',
            'delflag' => 'N',
            'u_name' => Auth::user()->u_name,
            'u_ae' => 'e',
        ];

        Gin::where('docid', $docid)->update($updategin);

        Stock::where('docid', $docid)->delete();

        $totalitem = $request->input('totalitem');
        $netamount = 0;


        for ($i = 1; $i <= $totalitem; $i++) {
            $itemmast = ItemMast::where('Property_ID', $this->propertyid)
                ->where('Code', $request->input('item' . $i))
                ->where('RestCode', 'PURC' . $this->propertyid)
                ->first();

            $amount = $request->input('itemrate' . $i) * $request->input('accqty' . $i);
            $netamount += $amount;

            $stockdata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $gin->vno,
                'sno' => $i,
                'restcode' => 'PURC' . $this->propertyid,
                'vtype' => $gin->vtype,
                'vdate' => $gin->vdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $ranges['finyear']['current'],
                'item' => $request->input('item' . $i),
                'issueunit' => $request->input('wtunit' . $i) ?? '',
                'qtyiss' => 0,
                'qtyrec' => $request->input('accqty' . $i),
                'unit' => $itemmast->Unit ?? '',
                'rate' => $request->input('itemrate' . $i),
                'partycode' => $request->input('partycode'),
                'amount' => $amount,
                'taxper' => '',
                'taxamt' => '',
                'discper' => '',
                'discamt' => '',
                'description' => $request->input('specification' . $i) ?? '',
                'specification' => $request->input('specification' . $i) ?? '',
                'total' => $netamount,
                'discapp' => $itemmast->DiscApp ?? '',
                'roundoff' => '',
                'departcode' => $itemmast->RestCode ?? '',
                'godowncode' => $request->input('godown' . $i),
                'chalqty' => $request->input('chalqty' . $i),
                'recdqty' => $request->input('recdqty' . $i),
                'rejqty' => $request->input('rejqty' . $i),
                'accqty' => $request->input('accqty' . $i),
                'recdunit' => $request->input('wtunit' . $i) ?? '',
                'itemrate' => $itemmast->PurchRate ?? '',
                'itemrestcode' => $itemmast->RestCode ?? '',
                'convratio' => $request->input('convratio' . $i) ?? '',
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ];

            Stock::insert($stockdata);
        }

        return redirect('mrentry')->with('success', 'Mr Entry Updated Successfully!');
    }

    public function deletemrentry(Request $request)
    {
        $permission = revokeopen(161114);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = $request->query('docid');

        $updata = [
            'delflag' => 'Y'
        ];
        Gin::where('propertyid', $this->propertyid)->where('docid', $docid)->update($updata);

        return redirect('mrentry')->with('success', 'Mr Entry Del Flag Updated Successfully!');
    }

    public function openpurchasebill(Request $request)
    {

        $permission = revokeopen(161115);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = Purch1::select(
            'purch1.*',
            'subgroup.name as subname',
        )
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'purch1.Party')
            ->where('purch1.propertyid', $this->propertyid)
            ->where('purch1.delflag', 'N')
            ->groupBy('purch1.docid', 'subgroup.name')
            ->get();

        $maxvno = Gin::where('propertyid', $this->propertyid)->max('vno');
        if (isset($maxvno)) {
            $mrno = $maxvno + 1;
        } else {
            $mrno = 1;
        }
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();

        $party = SubGroup::where('nature', 'Supplier')->where('propertyid', $this->propertyid)
            ->where('activeyn', 'Y')
            ->orderBy('name', 'ASC')
            ->get();

        $sundrytype = Sundrytype::select('sundrytype.*', 'revmast.field_type')
            ->leftJoin('revmast', function ($join) {
                $join->on('revmast.rev_code', '=', 'sundrytype.revcode')
                    ->where('revmast.propertyid', $this->propertyid);
            })
            ->where('sundrytype.propertyid', $this->propertyid)
            ->where('sundrytype.vtype', 'PURC' . $this->propertyid)
            ->groupBy('sundrytype.nature')
            ->orderBy('sundrytype.sno', 'ASC')->get();
        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();

        $cashmrentry = DB::table('gin')
            ->Join('stock', 'stock.docid', '=', 'gin.docid')
            ->where('gin.propertyid', $this->propertyid)
            ->where('stock.contradocid', '')
            ->where('gin.vtype', 'MRCH')
            ->groupBy('stock.docid')
            ->get();

        return view('property.purchasebill', [
            'data' => $data,
            'ncurdate' => $this->ncurdate,
            'mrno' => $mrno,
            'godown' => $godown,
            'items' => $items,
            'party' => $party,
            'sundrytype' => $sundrytype,
            'superwiser' => $superwiser,
            'enviroinv' => $enviroinv,
            'propertyid' => $this->propertyid,
            'cashmrentry' => $cashmrentry
        ]);
    }

    public function purchasebillno(Request $request)
    {
        $vtype = $request->input('vtype');
        $maxvno = Gin::where('propertyid', $this->propertyid)->where('vtype', $vtype)->max('vno');

        $cashmrentry = DB::table('gin')
            ->Join('stock', 'stock.docid', '=', 'gin.docid')
            ->where('gin.propertyid', $this->propertyid)
            ->where('stock.contradocid', '')
            ->where('gin.vtype', 'MRCH')
            ->groupBy('stock.docid')
            ->orderByDesc('gin.vno')
            ->get();

        if (isset($maxvno)) {
            $mrno = $maxvno + 1;
        } else {
            $mrno = 1;
        }

        $data = [
            'mrno' => $mrno,
            'vtype' => $vtype,
            'cashmrentry' => $cashmrentry
        ];

        return json_encode($data);
    }

    public function partydata(Request $request)
    {
        $partycode = $request->input('partycode');

        $gin = DB::table('gin')
            ->Join('stock', 'stock.docid', '=', 'gin.docid')
            ->where('gin.propertyid', $this->propertyid)
            ->where('stock.contradocid', '')
            ->where('gin.partycode', $partycode)
            ->groupBy('stock.docid')
            ->get();

        $data = [
            'gin' => $gin,
        ];

        return json_encode($data);
    }

    public function getpurchvno(Request $request)
    {
        $invtype =  $request->invtype;

        $maxvno = Purch1::where('propertyid', $this->propertyid)->where('invoicetype', $invtype)->max('invoiceno');

        if ($maxvno == null) {
            return $vno = 1;
        } else {
            return $vno = $maxvno + 1;
        }
    }

    public function mritems(Request $request)
    {
        $docid = $request->input('docid');

        $gin = Gin::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        $stockitems = Stock::select(
            'stock.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            // DB::raw('SUM(taxstru.rate) as taxrate')
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'stock.item')
                    ->where('itemmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('stock.propertyid', $this->propertyid)
            ->where('stock.docid', $docid)
            ->where('stock.contradocid', '=', '')
            ->groupBy('stock.item')
            ->orderBy('stock.sno', 'ASC')
            ->get();

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $taxstrudata = TaxStructure::select(
            'taxstru.*',
            // DB::raw('SUM(taxstru.rate) as taxratesum')
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->where('taxstru.propertyid', $this->propertyid)
            ->groupBy('taxstru.name')
            ->orderBy('taxstru.name', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('group_code', ['23' . $this->propertyid, '10' . $this->propertyid, '14' . $this->propertyid,])->orderBy('name', 'ASC')->get();

        $data = [
            'stockitems' => $stockitems,
            'items' => $items,
            'godown' => $godown,
            'units' => $units,
            'taxstrudata' => $taxstrudata,
            'ledgerdata' => $ledgerdata,
            'gin' => $gin
        ];

        return json_encode($data);
    }

    public function partywiserate(Request $request)
    {
        $partycode = $request->partycode;
        $itemcode = $request->itemcode;

        $chk = Stock::where('propertyid', $this->propertyid)->where('partycode', $partycode)->where('item', $itemcode)->orderBy('vdate', 'DESC')->first();

        if ($chk) {
            $data = [
                'stock' => $chk
            ];
            return response()->json($data);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Not Found For Party'
            ]);
        }
    }

    public function purchaseitems(Request $request)
    {
        $items = ItemMast::select(
            'itemmast.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->orderBy('itemmast.Name', 'ASC')
            ->get();
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $taxstrudata = TaxStructure::select(
            'taxstru.*',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->where('taxstru.propertyid', $this->propertyid)
            ->groupBy('taxstru.name')
            ->orderBy('taxstru.name', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('group_code', ['23' . $this->propertyid, '10' . $this->propertyid, '14' . $this->propertyid,])->orderBy('name', 'ASC')->get();
        $envinventory = EnviroInventory::where('propertyid', $this->propertyid)->first();

        $data = [
            'items' => $items,
            'godown' => $godown,
            'units' => $units,
            'taxstrudata' => $taxstrudata,
            'ledgerdata' => $ledgerdata,
            'envinventory' => $envinventory
        ];

        return json_encode($data);
    }

    public function deletepurchbill(Request $request)
    {
        $permission = revokeopen(161115);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = $request->input('docid');
        // return $docid;
        $doc = Purch1::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
        if ($doc == null) {
            return back()->with('error', 'Purchase Bill not found');
        }
        $doc->update(['delflag' => 'Y']);
        $docitem = Purch2::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        foreach ($docitem as $item) {
            $item->update(['delflag' => 'Y']);
        }
        $sale2 = Sale2::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        foreach ($sale2 as $item) {
            $item->update(['delflag' => 'Y']);
        }
        $stockItems = Stock::where('propertyid', $this->propertyid)
            ->where('contradocid', $docid)
            ->get();

        $affectedRows = Stock::where('propertyid', $this->propertyid)
            ->where('contradocid', $docid)
            ->update([
                'contradocid' => null,
                'contrasno'   => null
            ]);

        if ($affectedRows === 0) {
            Stock::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->delete();
        }


        $ledger = Ledger::where('propertyid', $this->propertyid)->where('docid', $docid)->get();
        if ($ledger) {
            foreach ($ledger as $item) {
                $item->delete();
            }
        }
        return back()->with('success', 'Purchase Bill deleted successfully');
    }

    public function updatepurchasebill(Request $request)
    {
        $permission = revokeopen(161115);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = $request->input('docid');

        // return $docid;

        $doc = Purch1::select('purch1.*', 'purch2.mrno')
            ->leftJoin('purch2', function ($join) {
                $join->on('purch2.docid', '=', 'purch1.docid')
                    ->where('purch2.propertyid', $this->propertyid);
            })
            ->where('purch1.propertyid', $this->propertyid)->where('purch1.docid', $docid)->first();
        if ($doc == null) {
            return back()->with('error', 'Purchase Bill not found');
        }

        
        $stockgin = Stock::where('propertyid', $this->propertyid)->where('docid', $doc->docid)->first();
        
        // return $stockgin;
        // return $doc->docid;
        $gin = Gin::where('propertyid', $this->propertyid)->where('docid', $stockgin->docid)->first();

        $maxvno = Gin::where('propertyid', $this->propertyid)->max('vno');
        if (isset($maxvno)) {
            $mrno = $maxvno + 1;
        } else {
            $mrno = 1;
        }
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $items = ItemMast::select(
            'itemmast.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            // DB::raw('SUM(taxstru.rate) as taxrate')
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->orderBy('itemmast.Name', 'ASC')
            ->get();

        $party = SubGroup::where('nature', 'Supplier')->where('propertyid', $this->propertyid)
            ->where('activeyn', 'Y')
            ->orderBy('name', 'ASC')
            ->get();

        $stockdata = Purch2::select(
            'purch2.*',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'purch2.taxstru')
            ->where('purch2.propertyid', $this->propertyid)
            ->where('purch2.docid', $docid)
            ->groupBy('purch2.item')
            ->orderBy('purch2.sno', 'ASC')
            ->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $taxstrudata = TaxStructure::select(
            'taxstru.*',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate'),
            DB::raw('SUM(taxstru.rate) as taxratesum')
        )
            ->where('taxstru.propertyid', $this->propertyid)
            ->groupBy('taxstru.name')
            ->orderBy('taxstru.name', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')->where('propertyid', $this->propertyid)
            ->whereIn('group_code', ['23' . $this->propertyid, '10' . $this->propertyid, '14' . $this->propertyid,])
            ->orderBy('name', 'ASC')->get();

        $suntrandata = Suntran::select('suntran.*', 'suntran.dispname as disp_name', 'sundrytype.nature')
            ->leftJoin('sundrytype', function ($join) {
                $join->on('suntran.suncode', '=', 'sundrytype.sundry_code')
                    ->where('sundrytype.vtype', '=', 'PURC' . $this->propertyid)
                    ->where('sundrytype.propertyid', $this->propertyid);
            })
            ->where('suntran.propertyid', $this->propertyid)->where('suntran.docid', $docid)->orderBy('suntran.sno', 'ASC')->get();

        // echo '<pre>';
        // print_r($suntrandata);
        // echo '</pre>';
        // die();

        return view('property.purchasebillupdate', [
            'ncurdate' => $this->ncurdate,
            'mrno' => $mrno,
            'godown' => $godown,
            'items' => $items,
            'party' => $party,
            'data' => $doc,
            'stockdata' => $stockdata,
            'units' => $units,
            'taxstrudata' => $taxstrudata,
            'ledgerdata' => $ledgerdata,
            'suntrandata' => $suntrandata,
            'gin' => $gin
        ]);
    }

    public function purchasebillupdate(Request $request)
    {
        $permission = revokeopen(161115);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'olddocid' => 'required'
        ]);

        $docid = $request->input('olddocid');
        $vno = $request->input('oldvno');
        $vprefix = $request->input('oldvprefix');
        $vtype = $request->input('oldvtype');
        $subcodee = $request->input('partycode');
        $gstin = $request->input('partygstin');
        $vdate = $request->input('oldvdate');

        if ($vtype == 'PBPC') {
            $chkinv = EnviroInventory::where('propertyid', $this->propertyid)->first();
            if (is_null($chkinv)) {
                return back()->with('error', 'Please define Cash Payment Type From Enviro');
            }
            // if ($request->input('partycode') == '') {
            $subcodee = $chkinv->cashpurchaseac;
            $gstin = $chkinv->gstin;
            // }
        }

        // echo $subcodee;
        // exit;
        try {
            DB::beginTransaction();
            $exmrno = $request->input('exmrno');

            $doc = Purch1::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
            if ($doc == null) {
                return back()->with('error', 'Purchase Bill not found');
            }
            Purch1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Purch2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Sale2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Suntran::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Ledger::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            $taxable = $request->input('taxableamt');
            $totalamt = $request->input('totalamount');
            $discamt = $request->input('discountsundry');
            $cgstamount = $request->input('cgstamount') ?? 0.00;
            $sgstamount = $request->input('sgstamount') ?? 0.00;
            $igstamount = $request->input('igstamount') ?? 0.00;
            $servicechargeamount = $request->input('servicechargeamount') ?? 0.00;
            $additionamount = $request->input('additionamount') ?? 0.00;
            $deductionamount = $request->input('deductionamount') ?? 0.00;
            $roundoffamount = $request->input('roundoffamount') ?? 0.00;
            $netamount = $request->input('netamount') ?? 0.00;
            $nontaxable = $totalamt - $taxable;
            $totalitem = $request->input('totalitem');
            $stockdocid = $request->input('gindocid');
            $discper = $request->input('discountfix');
            $sundrycount = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)->count();
            $servicechargefix = 0.00;
            $mritemyn = $request->input('mritemyn');

            $firstsno = null;
            $lastsno = null;

            for ($i = 1; $i <= $sundrycount; $i++) {
                if ($i == 1) {
                    $firstsno = $i;
                }
                $lastsno = $i;
                $fstype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)->where('sno', $i)->first();
                $svalue = $fstype->svalue;
                $baseamount = 0.00;
                $amount = 0.00;
                if (strtolower($fstype->nature) == 'discount') {
                    $amount = $discamt;
                    $svalue = $discper;
                    $baseamount = $totalamt;
                }
                if (strtolower($fstype->nature) == 'service charge') {
                    $amount = $servicechargeamount;
                    $svalue = $servicechargefix;
                }
                if (strtolower($fstype->nature) == 'amount') {
                    $amount = $totalamt;
                }
                if (strtolower($fstype->nature) == 'cgst') {
                    $amount = $cgstamount;
                }
                if (strtolower($fstype->nature) == 'sgst') {
                    $amount = $sgstamount;
                }
                if (strtolower($fstype->nature) == 'igst') {
                    $amount = $igstamount;
                }
                if (strtolower($fstype->nature) == 'addition') {
                    $amount = $additionamount;
                }
                if (strtolower($fstype->nature) == 'deduction') {
                    $amount = $deductionamount;
                }
                if (strtolower($fstype->nature) == 'round off') {
                    $amount = $roundoffamount;
                    $baseamount = $netamount - $roundoffamount;
                }
                if (strtolower($fstype->nature) == 'net amount') {
                    $amount = $netamount;
                }

                $suntrandata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'sno' => $i,
                    'vno' => $vno,
                    'vtype' => $vtype,
                    'vdate' => $request->input('vdate'),
                    'dispname' => $fstype->disp_name,
                    'suncode' => $fstype->sundry_code,
                    'calcformula' => $fstype->calcformula,
                    'svalue' => $svalue,
                    'amount' => $amount,
                    'baseamount' => $baseamount,
                    'revcode' => $fstype->revcode,
                    'restcode' => 'PURC' . $this->propertyid,
                    'sunappdate' => $request->input('vdate'),
                    'delflag' => 'N',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                Suntran::insert($suntrandata);
            }

            $snolist = "$firstsno,$lastsno";

            $sundrytp = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)
                ->orderBy('sno', 'DESC')->first();

            $ledgers = DB::table('suntran AS S')
                ->select([
                    DB::raw('SUM(S.amount) AS RevAmt'),
                    'S.revcode',
                    DB::raw('MAX(S.vdate) AS VDate'),
                    DB::raw('MAX(R.name) AS Revenue'),
                    DB::raw('MAX(S.suncode) AS SundryCode'),
                    DB::raw('MAX(R.ac_code) AS ACode'),
                    DB::raw('MAX(R.payable_ac) AS PCode'),
                    DB::raw('MAX(R.unregistered_ac) AS UCode'),
                    DB::raw('MAX(R.field_type) AS FieldType'),
                    DB::raw('MAX(ST.calcsign) AS CSign')
                ])
                ->leftJoin('revmast AS R', 'S.revcode', '=', 'R.rev_code')
                ->leftJoin('depart AS D', 'S.restcode', '=', 'D.dcode')
                ->leftJoin('sundrytype AS ST', function ($join) {
                    $join->on('S.sunappdate', '=', 'ST.appdate')
                        ->on('ST.vtype', '=', 'S.restcode')
                        ->on('S.suncode', '=', 'ST.sundry_code');
                })
                ->whereNotNull('S.revcode')
                ->where('S.revcode', '<>', '')
                ->where('S.suncode', '!=', $sundrytp->sundry_code)
                ->where('S.docid', '=', $docid)
                ->groupBy('S.revcode')
                ->orderBy('S.restcode')
                ->get();

            $n = 1;
            foreach ($ledgers as $row) {
                if ($row->RevAmt > 0) {
                    $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $row->ACode)->first();
                    $ldata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $vno,
                        'vdate' => $vdate,
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($vdate)),
                        'contrasub' => '',
                        'subcode' => $row->ACode,
                        'amtcr' => 0.00,
                        'amtdr' => $row->RevAmt,
                        'chqno' => $request->input('billno'),
                        'chqdate' => $request->input('billdate'),
                        'clgdate' => $vdate,
                        'groupcode' => $subgroup->group_code,
                        'groupnature' => $subgroup->nature,
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];
                    Ledger::insert($ldata);
                    $n++;
                }
            }

            $netledger = Suntran::select(
                'suntran.dispname',
                DB::raw('SUM(suntran.amount) AS RevAmt'),
                DB::raw('MAX(suncode) as SundryCode'),
                'subgroup.sub_code AS subcode',
                'subgroup.name as subname',
                'subgroup.group_code as accode',
                'subgroup.nature as subnature'
            )
                ->leftJoin('revmast', 'suntran.revcode', '=', 'revmast.rev_code')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', DB::raw($subcodee))
                ->leftJoin('depart', 'suntran.restcode', '=', 'depart.dcode')
                ->where('suncode', '=', '10' . $this->propertyid)
                ->where('docid', '=', $docid)
                ->where('suntran.propertyid', $this->propertyid)
                ->groupBy('restcode', 'revcode')
                ->orderBy('restcode')
                ->first();

            if ($vtype == 'PBPB' || $vtype == 'PBPC') {
                $amtcr = $netledger->RevAmt;
                $amtdr = 0.00;
            } else {
                $amtcr = 0.00;
                $amtdr = $netledger->RevAmt;
            }

            $lndata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vsno' => $n,
                'vno' => $vno,
                'vdate' => $request->input('vdate'),
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($request->input('vdate'))),
                'contrasub' => '',
                'subcode' => $netledger->subcode,
                'amtcr' => $amtcr,
                'amtdr' => $amtdr,
                'chqno' => $request->input('billno'),
                'chqdate' => $request->input('billdate'),
                'clgdate' => $request->input('vdate'),
                'groupcode' => $netledger->accode,
                'groupnature' => $netledger->subnature,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
            ];
            Ledger::insert($lndata);

            $dir = 'public/property/purchasebill';
            $newfilename = '';
            if (!Storage::exists($dir)) {
                Storage::makeDirectory($dir);
                $path = storage_path('app/' . $dir);
                File::chmod($path, 0777);
            }

            if ($request->hasFile('billimage')) {
                $file = $request->file('billimage');
                $extension = $file->getClientOriginalExtension();
                $filesize = round($file->getSize() / 1000 / 1000, 3);
                $newfilename = 'bill_' . $this->propertyid . Auth::user()->name . '_' . date('dmY', strtotime($this->ncurdate))  . $vno . $vtype . $extension;
                if (Storage::exists($dir . '/' . $newfilename)) {
                    Storage::delete($dir . '/' . $newfilename);
                }
                Storage::putFileAs($dir, $file, $newfilename);

                Artisan::call('storage:link');
            }

            $maxvno = Purch1::where('propertyid', $this->propertyid)->where('invoicetype', $request->input('invtype') ?? 'otherinvoice')->max('invoiceno');
            if ($maxvno == null) {
                $invoiceno = 1;
            } else {
                $invoiceno = $maxvno + 1;
            }

            $purch1 = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $vno,
                'vdate' => $request->input('vdate'),
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'restcode' => 'PURC' . $this->propertyid,
                'Party' => $subcodee,
                'total' => $request->input('totalamount'),
                'discper' => $request->input('discountfix'),
                'discamt' => $request->input('discountsundry'),
                'nontaxable' => $nontaxable,
                'taxable' => $request->input('totalamount'),
                'tax' => $cgstamount + $sgstamount + $igstamount,
                'servicecharge' => $servicechargeamount,
                'addamt' => $additionamount,
                'dedamt' => $deductionamount,
                'roundoff' => $roundoffamount,
                'netamt' => $netamount,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'e',
                'delflag' => 'N',
                'partybillno' => $request->input('billno'),
                'partybilldt' => $request->input('billdate'),
                'cashparty' => '',
                'gstno' => $gstin ?? '',
                'remark' => '',
                'invoicetype' => $request->input('invtype') ?? 'otherinvoice',
                'invoiceno' => $invoiceno,
                'cgst' => $cgstamount,
                'sgst' => $sgstamount,
                'igst' => $igstamount,
                'payable' => 0,
                'billimagepath' => $newfilename,
            ];

            Purch1::insert($purch1);

            $totalitemqty = 0;
            for ($i = 1; $i <= $totalitem; $i++) {
                $totalitemqty += $request->input('qtyiss' . $i);
            }

            for ($i = 1; $i <= $totalitem; $i++) {
                $itemmast = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)
                    ->where('Code', $request->input('item' . $i))->first();
                if ($mritemyn == 'N') {
                    $discamt = $request->input('amount' . $i) - $request->input('discamt' . $i);
                    $stockdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'sno' => $i,
                        'vno' => $vno,
                        'vtype' => $vtype,
                        'vdate' => $request->input('vdate'),
                        'vtime' => date('H:i:s'),
                        'vprefix' => $vprefix,
                        'restcode' => 'PURC' . $this->propertyid,
                        'partycode' => $subcodee,
                        'roomno' => '',
                        'roomcat' => '',
                        'roomtype' => 'Purchase',
                        'contradocid' => '',
                        'contrasno' => '',
                        'item' => $request->input('item' . $i),
                        'qtyiss' => '0',
                        'qtyrec' => $request->input('qtyiss' . $i),
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('itemrate' . $i),
                        'amount' => $request->input('amount' . $i),
                        'taxper' => $request->input('taxrate' . $i) ?? '0',
                        'taxamt' => $request->input('taxamt' . $i),
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('specification' . $i) ?? '',
                        'voidyn' => '',
                        'remarks' => '',
                        'kotdocid' => '',
                        'kotsno' => '',
                        'total' => $request->input('amount' . $i) - $discamt + $request->input('taxamt' . $i),
                        'discapp' => $request->input('discountfix') > 0 ? 'Y' : 'N',
                        'roundoff' => '0.00',
                        'departcode' => $itemmast->RestCode ?? '',
                        'godowncode' => $request->input('godown' . $i),
                        'chalqty' => $request->input('chalqty' . $i) ?? $request->input('qtyiss' . $i),
                        'recdqty' => $request->input('recdqty' . $i) ?? $request->input('qtyiss' . $i),
                        'accqty' => $request->input('accqty' . $i) ?? $request->input('qtyiss' . $i),
                        'rejqty' => $request->input('rejqty' . $i) ?? 0.00,
                        'recdunit' => $request->input('wtunithidden' . $i) ?? '',
                        'specification' => $request->input('specification' . $i) ?? '',
                        'itemrate' => 0.00,
                        'delflag' => 'N',
                        'landval' => 0,
                        'convratio' => $itemmast->ConvRatio,
                        'indentdocid' => '',
                        'indentsno' => 0,
                        'issqty' => '0',
                        'issueunit' => '0',
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
                        'u_ae' => 'e',
                    ];

                    Stock::insert($stockdata);
                } else {
                    $exmrdocid = $request->exmrdocid;

                    $mrupdate = [
                        'contradocid' => $docid,
                        'contrasno' => $request->input('issno' . $i),
                        'u_updatedt' => $this->currenttime
                    ];

                    Stock::where('docid', $exmrdocid)->where('propertyid', $this->propertyid)->where('sno', $request->input('issno' . $i))->update($mrupdate);
                }
                $stock = Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('restcode', 'PURC' . $this->propertyid)
                    ->where('item', $request->input('item' . $i))->first();

                $itemmastup = [
                    'LPurDate' => $vdate,
                    'LPurRate' => $request->input('itemrate' . $i),
                    'u_updaedt' => $this->currenttime,
                    'U_AE' => 'e'
                ];

                ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)
                    ->where('Code', $request->input('item' . $i))->update($itemmastup);
                $discamt = $request->input('amount' . $i) - $request->input('discamt' . $i);
                $suntransum = Suntran::where('suntran.propertyid', $this->propertyid)
                    ->leftJoin('sundrytype', function ($join) {
                        $join->on('sundrytype.vtype', '=', 'suntran.restcode')
                            ->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                            ->where('sundrytype.propertyid', $this->propertyid);
                    })
                    ->whereNot('sundrytype.nature', 'Discount')
                    ->whereNotIn('suntran.sno', explode(',', $snolist))
                    ->where('suntran.revcode', '')
                    ->sum('suntran.amount');

                $tamt = $suntransum / $totalitemqty;
                $itotal = $request->input('amount' . $i) - $discamt;
                $sumforitemqty = ($tamt * $request->input('qtyiss' . $i)) + $itotal;
                $purch2 = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $vno,
                    'vdate' => $request->input('vdate'),
                    'vtype' => $vtype,
                    'sno' => $i,
                    'vprefix' => $vprefix,
                    'partycode' => $subcodee,
                    'restcode' => 'PURC' . $this->propertyid,
                    'mrno' => $request->input('exmrno') ?? '',
                    'contradocid' => $stock->docid ?? '',
                    'contrasno' => $stock->sno ?? '',
                    'item' => $request->input('item' . $i),
                    'qtyiss' => '0',
                    'qtyrec' => $request->input('qtyiss' . $i),
                    'unit' => $request->input('unit' . $i),
                    // 'rate' => $request->input("itemrate$i"),
                    'rate' => 0,
                    'amount' => $request->input('amount' . $i),
                    'taxper' => $request->input('taxrate' . $i) ?? '0',
                    'taxamt' => $request->input('taxamt' . $i),
                    'discper' => $request->input('discountfix'),
                    'discamt' => $discamt,
                    'remarks' => $request->input('specification' . $i) ?? '',
                    'u_name' => Auth::user()->name,
                    'u_entdt' => $this->currenttime,
                    'u_ae' => 'e',
                    'total' => $request->input('amount' . $i) - $discamt + $request->input('taxamt' . $i),
                    'discapp' => $request->input('discountfix') > 0 ? 'Y' : 'N',
                    'roundoff' => 0,
                    'departcode' => '',
                    'godcode' => $request->input('godown' . $i),
                    'chalqty' => $request->input('chalqty' . $i) ?? 0.00,
                    'recdqty' => $request->input('recdqty' . $i) ?? 0.00,
                    'accqty' => $request->input('accqty' . $i) ?? 0.00,
                    'rejqty' => $request->input('rejqty' . $i) ?? 0.00,
                    'recdunit' => $request->input('wtunithidden' . $i) ?? '',
                    'specification' => $request->input('specification' . $i) ?? '',
                    'itemrate' => $request->input('itemrate' . $i),
                    'delflag' => 'N',
                    'convratio' => $itemmast->ConvRatio,
                    'postval' => $sumforitemqty,
                    'landval' => $sumforitemqty,
                    'issqty' => 0,
                    'issuunit' => '',
                    'taxstru' => $request->input('taxstructure' . $i),
                    'accode' => $request->input('ledger' . $i),
                ];

                Purch2::insert($purch2);

                if ($request->input('taxrate' . $i) > 0) {
                    $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $request->input('taxstructure' . $i))->get();
                    foreach ($fetchtaxes as $taxesrow) {
                        $titemqty = $request->input('qtyiss' . $i);
                        $titemratetmp = $request->input('amount' . $i);
                        $titemamt = $request->input('discamt' . $i);
                        $taxperr = $taxesrow->rate;
                        $taxamt = ($titemamt * $taxperr) / 100;

                        $sale2 = [
                            'propertyid' => $this->propertyid,
                            'docid' => $docid,
                            'sno' => $i,
                            'sno1' => $taxesrow->sno,
                            'vno' => $vno,
                            'vtype' => $vtype,
                            'vdate' => $request->input('vdate'),
                            'vtime' => date('H:i:s'),
                            'vprefix' => $vprefix,
                            'restcode' => 'PURC' . $this->propertyid,
                            'taxcode' => $request->input('taxstructure' . $i),
                            'basevalue' => $titemamt,
                            'taxper' => $taxperr,
                            'taxamt' => $taxamt,
                            'delflag' => 'N',
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                        ];

                        Sale2::insert($sale2);
                    }
                }
            }

            $itemledger = DB::table('purch2')
                ->selectRaw('SUM(purch2.postval) as RevAmt, subgroup.sub_code, subgroup.name as subname, subgroup.nature, subgroup.group_code')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'purch2.accode')
                ->where('purch2.docid', $docid)
                ->where('purch2.propertyid', $this->propertyid)
                ->groupBy('purch2.accode')
                ->get();

            $n = $n + 1;
            foreach ($itemledger as $row) {
                if ($row->RevAmt > 0) {
                    $lidata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $vno,
                        'vdate' => $request->input('vdate'),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($request->input('vdate'))),
                        'contrasub' => '',
                        'subcode' => $row->sub_code,
                        'amtcr' => 0.00,
                        'amtdr' => $row->RevAmt,
                        'chqno' => $request->input('billno'),
                        'chqdate' => $request->input('billdate'),
                        'clgdate' => $request->input('vdate'),
                        'groupcode' => $row->group_code,
                        'groupnature' => $row->nature,
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];
                    Ledger::insert($lidata);
                    $n++;
                }
            }

            DB::commit();
            return redirect('purchasebill')->with('success', 'Purchase Bill Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function purchasebillsubmit(Request $request)
    {
        $permission = revokeopen(161115);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'totalitem' => 'required|int',
            'vtype' => 'required',
            'mritemyn' => 'required|string'
        ]);
        $vtype = $request->input('vtype');
        $subcodee = $request->input('partycode');
        $gstin = $request->input('partygstin');

        try {
            DB::beginTransaction();
            if ($vtype == 'PBPC') {
                $chkinv = EnviroInventory::where('propertyid', $this->propertyid)->first();
                if (is_null($chkinv)) {
                    return back()->with('error', 'Please define Cash Payment Type From Enviro');
                }
                // if ($request->input('partycode') == '') {
                $subcodee = $chkinv->cashpurchaseac;
                $gstin = $chkinv->gstin;
                // }
            }

            // return $subcodee;

            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $request->input('vdate'))
                ->whereDate('date_to', '>=', $request->input('vdate'))
                ->first();

            if ($chkvpf == null) {
                return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
            }


            $vno = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;

            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

            Purch1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Purch2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Sale2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Suntran::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Ledger::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            $taxable = $request->input('taxableamt');
            $totalamt = $request->input('totalamount');
            $discamt = $request->input('discountamount');
            $cgstamount = $request->input('cgstamount') ?? 0.00;
            $sgstamount = $request->input('sgstamount') ?? 0.00;
            $igstamount = $request->input('igstamount') ?? 0.00;
            $servicechargeamount = $request->input('servicechargeamount') ?? 0.00;
            $additionamount = $request->input('additionamount') ?? 0.00;
            $deductionamount = $request->input('deductionamount') ?? 0.00;
            $roundoffamount = $request->input('roundoffamount') ?? 0.00;
            $netamount = $request->input('netamount') ?? 0.00;
            $nontaxable = $totalamt - $taxable;
            $totalitem = $request->input('totalitem');
            $stockdocid = $request->input('gindocid');
            $discper = $request->input('discountfix');
            $servicechargefix = 0.00;
            $mritemyn = $request->input('mritemyn');

            $sundrytypes = Sundrytype::select('sundrytype.*', 'revmast.field_type')
                ->leftJoin('revmast', function ($join) {
                    $join->on('revmast.rev_code', '=', 'sundrytype.revcode')
                        ->where('revmast.propertyid', $this->propertyid);
                })
                ->where('sundrytype.propertyid', $this->propertyid)
                ->where('sundrytype.vtype', 'PURC' . $this->propertyid)
                ->groupBy('sundrytype.nature')
                ->orderBy('sundrytype.sno', 'ASC')->get();

            $sundrycount = $sundrytypes->count();
            $firstsno = $sundrytypes->first()->sno ?? null;
            $lastsno = $sundrytypes->last()->sno ?? null;

            $inputData = [
                'discount' => [
                    'amount' => $discamt,
                    'svalue' => $discper,
                    'baseamount' => $totalamt
                ],
                'servicecharge' => [
                    'amount' => $servicechargeamount,
                    'svalue' => $servicechargefix
                ],
                'amount' => [
                    'amount' => $totalamt
                ],
                'cgst' => [
                    'amount' => $cgstamount
                ],
                'sgst' => [
                    'amount' => $sgstamount
                ],
                'igst' => [
                    'amount' => $igstamount
                ],
                'addition' => [
                    'amount' => $additionamount
                ],
                'deduction' => [
                    'amount' => $deductionamount
                ],
                'roundoff' => [
                    'amount' => $roundoffamount,
                    'baseamount' => $netamount - $roundoffamount
                ],
                'netamount' => [
                    'amount' => $netamount
                ]
            ];

            foreach ($sundrytypes as $item) {
                $natureKey = strtolower(str_replace(' ', '', $item->nature));
                $svalue = $item->svalue;
                $baseamount = 0.00;
                $amount = 0.00;

                if (isset($inputData[$natureKey])) {
                    $amount = $inputData[$natureKey]['amount'] ?? 0.00;
                    $svalue = $inputData[$natureKey]['svalue'] ?? $svalue;
                    $baseamount = $inputData[$natureKey]['baseamount'] ?? $baseamount;
                }

                $suntrandata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'sno' => $item->sno,
                    'vno' => $vno,
                    'vtype' => $vtype,
                    'vdate' => $request->input('vdate'),
                    'dispname' => $item->disp_name,
                    'suncode' => $item->sundry_code,
                    'calcformula' => $item->calcformula,
                    'svalue' => $svalue,
                    'amount' => $amount,
                    'baseamount' => $baseamount,
                    'revcode' => $item->revcode,
                    'restcode' => 'PURC' . $this->propertyid,
                    'sunappdate' => $request->input('vdate'),
                    'delflag' => 'N',
                    'u_entdt' => $this->currenttime,
                    'u_name' => Auth::user()->u_name,
                    'u_ae' => 'a',
                ];

                Suntran::insert($suntrandata);
            }

            // exit;

            $snolist = "$firstsno,$lastsno";

            $sundrytp = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)
                ->orderBy('sno', 'DESC')->first();

            $ledgers = DB::table('suntran AS S')
                ->select([
                    DB::raw('SUM(S.amount) AS RevAmt'),
                    'S.revcode',
                    DB::raw('MAX(S.vdate) AS VDate'),
                    DB::raw('MAX(R.name) AS Revenue'),
                    DB::raw('MAX(S.suncode) AS SundryCode'),
                    DB::raw('MAX(R.ac_code) AS ACode'),
                    DB::raw('MAX(R.payable_ac) AS PCode'),
                    DB::raw('MAX(R.unregistered_ac) AS UCode'),
                    DB::raw('MAX(R.field_type) AS FieldType'),
                    DB::raw('MAX(ST.calcsign) AS CSign')
                ])
                ->leftJoin('revmast AS R', 'S.revcode', '=', 'R.rev_code')
                ->leftJoin('depart AS D', 'S.restcode', '=', 'D.dcode')
                ->leftJoin('sundrytype AS ST', function ($join) {
                    $join->on('S.sunappdate', '=', 'ST.appdate')
                        ->on('ST.vtype', '=', 'S.restcode')
                        ->on('S.suncode', '=', 'ST.sundry_code');
                })
                ->whereNotNull('S.revcode')
                ->where('S.revcode', '<>', '')
                ->where('S.suncode', '!=', $sundrytp->sundry_code)
                ->where('S.docid', '=', $docid)
                ->groupBy('S.revcode')
                ->orderBy('S.restcode')
                ->get();

            $n = 1;
            foreach ($ledgers as $row) {
                if ($row->RevAmt > 0) {
                    $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $row->ACode)->first();

                    if ($vtype == 'PBPB' || $vtype == 'PBPC') {
                        $amtcr = 0.00;
                        $amtdr = $row->RevAmt;
                    } else {
                        $amtcr = $row->RevAmt;
                        $amtdr = 0.00;
                    }

                    $ldata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $vno,
                        'vdate' => $request->input('vdate'),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($request->input('vdate'))),
                        'contrasub' => '',
                        'subcode' => $row->ACode,
                        'amtcr' => $amtcr,
                        'amtdr' => $amtdr,
                        'chqno' => $request->input('billno'),
                        'chqdate' => $request->input('billdate'),
                        'clgdate' => $request->input('vdate'),
                        'groupcode' => $subgroup->group_code,
                        'groupnature' => $subgroup->nature,
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];
                    Ledger::insert($ldata);
                    $n++;
                }
            }

            // if()
            $netledger = Suntran::select(
                'suntran.dispname',
                DB::raw('SUM(suntran.amount) AS RevAmt'),
                DB::raw('MAX(suncode) as SundryCode'),
                'subgroup.sub_code AS subcode',
                'subgroup.name as subname',
                'subgroup.group_code as accode',
                'subgroup.nature as subnature'
            )
                ->leftJoin('revmast', 'suntran.revcode', '=', 'revmast.rev_code')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', DB::raw($subcodee))
                ->leftJoin('depart', 'suntran.restcode', '=', 'depart.dcode')
                ->where('suncode', '=', '10' . $this->propertyid)
                ->where('docid', '=', $docid)
                ->where('suntran.propertyid', $this->propertyid)
                ->groupBy('restcode', 'revcode')
                ->orderBy('restcode')
                ->first();

            if ($vtype == 'PBPB' || $vtype == 'PBPC') {
                $amtcr = $netledger->RevAmt;
                $amtdr = 0.00;
            } else {
                $amtcr = 0.00;
                $amtdr = $netledger->RevAmt;
            }

            $lndata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vsno' => $n,
                'vno' => $vno,
                'vdate' => $request->input('vdate'),
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($request->input('vdate'))),
                'contrasub' => '',
                'subcode' => $netledger->subcode,
                'amtcr' => $amtcr,
                'amtdr' => $amtdr,
                'chqno' => $request->input('billno'),
                'chqdate' => $request->input('billdate'),
                'clgdate' => $request->input('vdate'),
                'groupcode' => $netledger->accode,
                'groupnature' => $netledger->subnature,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
            ];
            Ledger::insert($lndata);

            $dir = 'public/property/purchasebill';
            $newfilename = '';
            if (!Storage::exists($dir)) {
                Storage::makeDirectory($dir);
                $path = storage_path('app/' . $dir);
                File::chmod($path, 0777);
            }

            if ($request->hasFile('billimage')) {
                $file = $request->file('billimage');
                $extension = $file->getClientOriginalExtension();
                $filesize = round($file->getSize() / 1000 / 1000, 3);
                $newfilename = 'bill_' . $this->propertyid . Auth::user()->name . '_' . date('dmY', strtotime($this->ncurdate))  . $vno . $vtype . $extension;
                if (Storage::exists($dir . '/' . $newfilename)) {
                    Storage::delete($dir . '/' . $newfilename);
                }
                Storage::putFileAs($dir, $file, $newfilename);

                Artisan::call('storage:link');
            }

            $maxvno = Purch1::where('propertyid', $this->propertyid)->where('invoicetype', $request->input('invtype') ?? 'otherinvoice')->max('invoiceno');
            if ($maxvno == null) {
                $invoiceno = 1;
            } else {
                $invoiceno = $maxvno + 1;
            }

            $purch1 = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $vno,
                'vdate' => $request->input('vdate'),
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'restcode' => 'PURC' . $this->propertyid,
                'Party' => $subcodee,
                'total' => $request->input('totalamount'),
                'discper' => $request->input('discountfix'),
                'discamt' => $request->input('discountamount'),
                'nontaxable' => $nontaxable,
                'taxable' => $request->input('totalamount'),
                'tax' => $cgstamount + $sgstamount + $igstamount,
                'servicecharge' => $servicechargeamount,
                'addamt' => $additionamount,
                'dedamt' => $deductionamount,
                'roundoff' => $roundoffamount,
                'netamt' => $netamount,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
                'delflag' => 'N',
                'partybillno' => $request->input('billno'),
                'partybilldt' => $request->input('billdate'),
                'cashparty' => '',
                'gstno' => $gstin ?? '',
                'remark' => '',
                'invoicetype' => $request->input('invtype') ?? 'otherinvoice',
                'invoiceno' => $invoiceno,
                'cgst' => $cgstamount,
                'sgst' => $sgstamount,
                'igst' => $igstamount,
                'payable' => 0,
                'billimagepath' => $newfilename,
            ];

            Purch1::insert($purch1);

            $totalitemqty = 0;
            for ($i = 1; $i <= $totalitem; $i++) {
                $totalitemqty += $request->input('qtyiss' . $i);
            }

            for ($i = 1; $i <= $totalitem; $i++) {
                $itemmast = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)
                    ->where('Code', $request->input('item' . $i))->first();
                if ($mritemyn == 'N') {
                    $discamt = $request->input('amount' . $i) - $request->input('discamt' . $i);
                    $stockdata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'sno' => $i,
                        'vno' => $vno,
                        'vtype' => $vtype,
                        'vdate' => $request->input('vdate'),
                        'vtime' => date('H:i:s'),
                        'vprefix' => $vprefix,
                        'restcode' => 'PURC' . $this->propertyid,
                        'partycode' => $subcodee,
                        'roomno' => '',
                        'roomcat' => '',
                        'roomtype' => 'Purchase',
                        'contradocid' => '',
                        'contrasno' => '',
                        'item' => $request->input('item' . $i),
                        'qtyiss' => '0',
                        'qtyrec' => $request->input('qtyiss' . $i),
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $request->input('itemrate' . $i),
                        'amount' => $request->input('amount' . $i),
                        'taxper' => $request->input('taxrate' . $i) ?? '0.00',
                        'taxamt' => $request->input('taxamt' . $i) ?? '0.00',
                        'discper' => $discper ?? 0.00,
                        'discamt' => $discamt,
                        'description' => $request->input('specification' . $i) ?? '',
                        'voidyn' => '',
                        'remarks' => '',
                        'kotdocid' => '',
                        'kotsno' => '',
                        'total' => $request->input('amount' . $i) - $discamt + $request->input('taxamt' . $i),
                        'discapp' => $request->input('discountfix') > 0 ? 'Y' : 'N',
                        'roundoff' => '0.00',
                        'departcode' => $itemmast->RestCode ?? '',
                        'godowncode' => $request->input('godown' . $i),
                        'chalqty' => $request->input('chalqty' . $i) ?? $request->input('qtyiss' . $i),
                        'recdqty' => $request->input('recdqty' . $i) ?? $request->input('qtyiss' . $i),
                        'accqty' => $request->input('accqty' . $i) ?? $request->input('qtyiss' . $i),
                        'rejqty' => $request->input('rejqty' . $i) ?? 0.00,
                        'recdunit' => $request->input('wtunithidden' . $i) ?? '',
                        'specification' => $request->input('specification' . $i) ?? '',
                        'itemrate' => 0.00,
                        'delflag' => 'N',
                        'landval' => 0,
                        'convratio' => $request->input('convratio' . $i),
                        'indentdocid' => '',
                        'indentsno' => 0,
                        'issqty' => '0',
                        'issueunit' => '0',
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

                    Stock::insert($stockdata);
                } else {
                    $exmrdocid = $request->exmrdocid;

                    $mrupdate = [
                        'contradocid' => $docid,
                        'contrasno' => $request->input('issno' . $i),
                        'u_updatedt' => $this->currenttime
                    ];

                    Stock::where('docid', $exmrdocid)->where('propertyid', $this->propertyid)->where('sno', $request->input('issno' . $i))->update($mrupdate);
                }
                $stock = Stock::where('propertyid', $this->propertyid)->where('docid', $stockdocid)->where('restcode', 'PURC' . $this->propertyid)
                    ->where('item', $request->input('item' . $i))->first();

                $itemmastup = [
                    'LPurDate' => $request->input('vdate'),
                    'LPurRate' => $request->input('itemrate' . $i),
                    'u_updaedt' => $this->currenttime,
                    'U_AE' => 'e'
                ];

                ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)
                    ->where('Code', $request->input('item' . $i))->update($itemmastup);
                $discamt = $request->input('amount' . $i) - $request->input('discamt' . $i);
                $suntransum = Suntran::where('suntran.propertyid', $this->propertyid)
                    ->leftJoin('sundrytype', function ($join) {
                        $join->on('sundrytype.vtype', '=', 'suntran.restcode')
                            ->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                            ->where('sundrytype.propertyid', $this->propertyid);
                    })
                    ->whereNot('sundrytype.nature', 'Discount')
                    ->whereNotIn('suntran.sno', explode(',', $snolist))
                    ->where('suntran.revcode', '')
                    ->sum('suntran.amount');

                $tamt = $suntransum / $totalitemqty;
                $itotal = $request->input('amount' . $i) - $discamt;
                $sumforitemqty = ($tamt * $request->input('qtyiss' . $i)) + $itotal;

                $purch2 = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $vno,
                    'vdate' => $request->input('vdate'),
                    'vtype' => $vtype,
                    'sno' => $i,
                    'vprefix' => $vprefix,
                    'partycode' => $subcodee,
                    'restcode' => 'PURC' . $this->propertyid,
                    'mrno' => $request->input('exmrno') ?? '',
                    'contradocid' => $stock->docid ?? '',
                    'contrasno' => $stock->sno ?? '',
                    'item' => $request->input('item' . $i),
                    'qtyiss' => '0',
                    'qtyrec' => $request->input('qtyiss' . $i),
                    'unit' => $request->input('unit' . $i),
                    // 'rate' => $request->input("itemrate$i"),
                    'rate' => 0,
                    'amount' => $request->input('amount' . $i),
                    'taxper' => $request->input('taxrate' . $i) ?? '0.00',
                    'taxamt' => $request->input('taxamt' . $i) ?? '0.00',
                    'discper' => $request->input('discountfix'),
                    'discamt' => $discamt,
                    'remarks' => $request->input('specification' . $i) ?? '',
                    'u_name' => Auth::user()->name,
                    'u_entdt' => $this->currenttime,
                    'u_ae' => 'a',
                    'total' => $request->input('amount' . $i) - $discamt + $request->input('taxamt' . $i),
                    'discapp' => $request->input('discountfix') > 0 ? 'Y' : 'N',
                    'roundoff' => 0,
                    'departcode' => '',
                    'godcode' => $request->input('godown' . $i),
                    'chalqty' => $request->input('chalqty' . $i) ?? 0.00,
                    'recdqty' => $request->input('recdqty' . $i) ?? 0.00,
                    'accqty' => $request->input('accqty' . $i) ?? 0.00,
                    'rejqty' => $request->input('rejqty' . $i) ?? 0.00,
                    'recdunit' => $request->input('wtunithidden' . $i) ?? '',
                    'specification' => $request->input('specification' . $i) ?? '',
                    'itemrate' => $request->input('itemrate' . $i),
                    'delflag' => 'N',
                    'convratio' => $request->input('convratio' . $i),
                    'postval' => $sumforitemqty,
                    'landval' => $sumforitemqty,
                    'issqty' => 0,
                    'issuunit' => '',
                    'taxstru' => $request->input('taxstructure' . $i),
                    'accode' => $request->input('ledger' . $i),
                ];

                Purch2::insert($purch2);

                if ($request->input('taxrate' . $i) > 0) {

                    $fetchtaxes = DB::table('taxstru')->where('propertyid', $this->propertyid)->where('str_code', $request->input('taxstructure' . $i))->get();
                    foreach ($fetchtaxes as $taxesrow) {
                        $titemqty = $request->input('qtyiss' . $i);
                        $titemratetmp = $request->input('amount' . $i);
                        $titemamt = $request->input('discamt' . $i);
                        $taxperr = $taxesrow->rate;
                        $taxamt = ($titemamt * $taxperr) / 100;

                        $sale2 = [
                            'propertyid' => $this->propertyid,
                            'docid' => $docid,
                            'sno' => $i,
                            'sno1' => $taxesrow->sno,
                            'vno' => $vno,
                            'vtype' => $vtype,
                            'vdate' => $request->input('vdate'),
                            'vtime' => date('H:i:s'),
                            'vprefix' => $vprefix,
                            'restcode' => 'PURC' . $this->propertyid,
                            'taxcode' => $request->input('taxstructure' . $i),
                            'basevalue' => $titemamt,
                            'taxper' => $taxperr,
                            'taxamt' => $taxamt,
                            'delflag' => 'N',
                            'u_entdt' => $this->currenttime,
                            'u_name' => Auth::user()->u_name,
                            'u_ae' => 'a',
                        ];

                        Sale2::insert($sale2);
                    }
                }
            }

            $itemledger = DB::table('purch2')
                ->selectRaw('SUM(purch2.postval) as RevAmt, subgroup.sub_code, subgroup.name as subname, subgroup.nature, subgroup.group_code')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'purch2.accode')
                ->where('purch2.docid', $docid)
                ->where('purch2.propertyid', $this->propertyid)
                ->groupBy('purch2.accode')
                ->get();

            $n = $n + 1;
            foreach ($itemledger as $row) {
                if ($row->RevAmt > 0) {
                    $lidata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $vno,
                        'vdate' => $request->input('vdate'),
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Purchase Bill: ' . $vno . ' ' . date('d-m-Y', strtotime($request->input('vdate'))),
                        'contrasub' => '',
                        'subcode' => $row->sub_code,
                        'amtcr' => 0.00,
                        'amtdr' => $row->RevAmt,
                        'chqno' => $request->input('billno'),
                        'chqdate' => $request->input('billdate'),
                        'clgdate' => $request->input('vdate'),
                        'groupcode' => $row->group_code,
                        'groupnature' => $row->nature,
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];
                    Ledger::insert($lidata);
                    $n++;
                }
            }

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            DB::commit();
            return back()->with('success', 'Purchase Bill Submitted');
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage() . ' On Line: ' . $e->getLine();
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function invparameter(Request $request)
    {
        $permission = revokeopen(121611);

        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $cash = SubGroup::where('propertyid', $this->propertyid)->where('nature', 'Cash')->orderBy('name', 'ASC')->get();
        if ($cash->isEmpty()) {
            return back()->with('error', 'No records found.');
        }
        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        if ($godown->isEmpty()) {
            return back()->with('error', 'No records found.');
        }

        $rowexist = EnviroInventory::where('propertyid', $this->propertyid)->first();
        if (is_null($rowexist)) {
            $exdata = [
                'propertyid' => $this->propertyid,
                'cashpurchaseac' => '',
                'purchasegodown' => '',
                'modifyaccountfield' => '',
                'itemratemrbasedon' => '',
                'itemratepbillbasedon' => '',
                'blockdays' => '0',
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
            ];

            EnviroInventory::insert($exdata);
        }

        $data = EnviroInventory::select('enviro_inventory.*', 'subgroup.name as subname', 'godown_mast.name as godownname')
            ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'enviro_inventory.cashpurchaseac')
            ->leftJoin('godown_mast', 'godown_mast.scode', '=', 'enviro_inventory.purchasegodown')
            ->where('enviro_inventory.propertyid', $this->propertyid)
            ->first();

        if (is_null($data)) {
            return back()->with('error', 'Enviro Not Defined');
        }
        $enviro_general = EnviroGeneral::where('propertyid', $this->propertyid)->first();
        return view('property.invparameter', [
            'cash' => $cash,
            'godown' => $godown,
            'data' => $data,
            'enviro_general' => $enviro_general
        ]);
    }

    public function enviroentrysubmit(Request $request)
    {
        $permission = revokeopen(121611);

        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'cashpurchaseac' => 'required',
            'purchasegodown' => 'required'
        ]);

        $envgen = EnviroGeneral::where('propertyid', $this->propertyid)->first();
        $envgen->cashpurcheffect = $request->cashpurcheffect;
        $envgen->save();

        $data = [
            'propertyid' => $this->propertyid,
            'cashpurchaseac' => $request->input('cashpurchaseac'),
            'purchasegodown' => $request->input('purchasegodown'),
            'modifyaccountfield' => $request->input('modifyaccountfield'),
            'itemratemrbasedon' => $request->input('itemratemrbasedon'),
            'itemratepbillbasedon' => $request->input('itemratepbillbasedon'),
            'blockdays' => $request->input('blockdays') ?? '0',
            'storeissuerequistion' => $request->storeissuerequistion,
            'roundofftype' => $request->roundofftype,
            'u_name' => Auth::user()->name,
            'u_entdt' => $this->currenttime,
            'u_ae' => 'a',
        ];

        EnviroInventory::where('propertyid', $this->propertyid)->update($data);

        return back()->with('success', 'Enviro Inventory Submitted');
    }

    public function deleteinv(Request $request)
    {
        $permission = revokeopen(121611);

        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $sn = base64_decode($request->query('sn'));

        EnviroInventory::where('sn', $sn)->where('propertyid', $this->propertyid)->delete();
        return back()->with('success', 'Enviro Deleted Successfully');
    }

    public function stocktransfer(Request $request)
    {
        $permission = revokeopen(161116);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $vno = Stock::where('propertyid', $this->propertyid)->where('vtype', 'KSREC')->max('vno') + 1;

        $vtype1 = 'KSREC';
        $vtype2 = 'KSISS';

        // $data = Stock::select('stock.*', 'g1.name as stockfrom', 'g2.name as stockto')
        //     ->leftJoin('godown_mast as g1', function ($join) {
        //         $join->on('stock.departcode', '=', 'g1.scode')
        //             ->where('g1.propertyid', $this->propertyid);
        //     })
        //     ->leftJoin('godown_mast as g2', function ($join) {
        //         $join->on('stock.godowncode', '=', 'g2.scode')
        //             ->where('g2.propertyid', $this->propertyid);
        //     })
        //     ->where('stock.propertyid', $this->propertyid)
        //     ->whereIn('stock.vtype', [$vtype1, $vtype2])
        //     ->groupBy('stock.docid')
        //     ->where('stock.delflag', 'N')
        //     ->get();


        $data = DB::table('stock as rec')
            ->join('stock as iss', function ($join) {
                $join->on('rec.vno', '=', 'iss.vno')
                    ->on('rec.propertyid', '=', 'iss.propertyid')
                    ->where('rec.vtype', '=', 'KSREC')
                    ->where('iss.vtype', '=', 'KSISS');
            })
            ->leftJoin('godown_mast as gfrom', function ($join) {
                $join->on('iss.departcode', '=', 'gfrom.scode')
                    ->where('gfrom.propertyid', '=', $this->propertyid);
            })
            ->leftJoin('godown_mast as gto', function ($join) {
                $join->on('rec.godowncode', '=', 'gto.scode')
                    ->where('gto.propertyid', '=', $this->propertyid);
            })
            ->where('rec.propertyid', '=', $this->propertyid)
            ->where('rec.delflag', '=', 'N')
            ->where('iss.delflag', '=', 'N')
            ->select([
                'gfrom.name as stockfrom',
                'gto.name as stockto',
                'rec.vno',
                'rec.vdate',
                'iss.item as item_issued',
                'iss.qtyiss',
                'rec.item as item_received',
                'rec.qtyrec',
                'rec.unit',
                'rec.rate',
                'rec.amount'
            ])
            ->groupBy('iss.vno')
            ->orderByDesc('iss.vno')
            ->get();


        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();

        return view('property.stocktransfer', [
            'departs' => $departs,
            'godown' => $godown,
            'vno' => $vno,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser
        ]);
    }

    public function stocktransfersubmit(Request $request)
    {
        $permission = revokeopen(161116);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vdate' => 'required',
        ]);

        $totalitem = $request->totalitem;
        $vtype1 = 'KSREC';

        $chkvpf1 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype1)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf1 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno1 = $chkvpf1->start_srl_no + 1;
        $vprefix1 = $chkvpf1->prefix;

        $docid1 = $this->propertyid . $vtype1 . '‎ ‎ ' . $vprefix1 . '‎ ‎ ‎ ‎ ' . $vno1;

        $vtype2 = 'KSISS';

        $chkvpf2 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype2)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf2 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno2 = $chkvpf2->start_srl_no + 1;
        $vprefix2 = $chkvpf2->prefix;

        $docid2 = $this->propertyid . $vtype2 . '‎ ‎ ' . $vprefix2 . '‎ ‎ ‎ ‎ ' . $vno2;

        try {
            for ($i = 1; $i <= $totalitem; $i++) {
                $itemmast = ItemMast::where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();
                $stockdata1 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid1,
                    'sno'          => $i,
                    'vprefix'      => $vprefix1,
                    'vtype'        => $vtype1,
                    'vno'          => $vno1,
                    'vdate'        => $request->vdate,
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $docid2,
                    'contrasno'    => $i,
                    'departcode'   => $request->tolocation,
                    'godowncode'   => $request->tolocation,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => $request->input('qtyiss' . $i),
                    'accqty'       => $request->input('qtyiss' . $i),
                    'qtyiss'       => 0,
                    'qtyrec'       => $request->input('qtyiss' . $i),
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'total'        => 0,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                    'unit'         => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->remarks ?? '',
                    'delflag' => 'N'
                ];

                Stock::insert($stockdata1);

                $stockdata2 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid2,
                    'sno'          => $i,
                    'vprefix'      => $vprefix2,
                    'vtype'        => $vtype2,
                    'vno'          => $vno2,
                    'vdate'        => $request->vdate,
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $docid1,
                    'contrasno'    => $i,
                    'departcode'   => $request->fromlocation,
                    'godowncode'   => $request->fromlocation,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => '0',
                    'accqty'       => '0',
                    'qtyiss'       => $request->input('qtyiss' . $i),
                    'issqty'       => $request->input('qtyiss' . $i),
                    'qtyrec'       => '0',
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'total'        => 0,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                    'unit'         => $request->input('unit' . $i),
                    'issueunit'    => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->remarks ?? '',
                    'delflag' => 'N'
                ];

                Stock::insert($stockdata2);
            }
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype1)
            ->where('prefix', $vprefix1)
            ->increment('start_srl_no');

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype2)
            ->where('prefix', $vprefix2)
            ->increment('start_srl_no');

        return back()->with('success', 'Stock Transfer Submitted Successfully');
    }

    public function itemstockval(Request $request)
    {
        $icode = $request->input('icode');
        $departcode = $request->input('departcode');
        $qty1 = DB::table('stock as S')
            ->selectRaw('SUM(S.RecdQty) AS QTY')
            ->leftJoin('itemmast as I', function ($join) {
                $join->on('S.Item', '=', 'I.Code');
            })
            ->leftJoin('voucher_type as VT', function ($join) {
                $join->on('S.VType', '=', 'VT.V_Type');
            })
            ->whereIn('VT.NCAT', ['PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC'])
            ->where('S.recdqty', '>', 0)
            ->where('I.Code', $icode)
            ->where('VT.propertyid', $this->propertyid)
            ->where('S.departcode', $departcode)
            ->where('S.propertyid', $this->propertyid)
            ->value('QTY');

        // $qty2 = DB::table('stock as S')
        //     ->selectRaw('SUM(S.issqty) AS QTY')
        //     ->leftJoin('itemmast as I', function ($join) {
        //         $join->on('S.Item', '=', 'I.Code');
        //     })
        //     ->leftJoin('voucher_type as VT', function ($join) {
        //         $join->on('S.VType', '=', 'VT.V_Type');
        //     })
        //     ->whereIn('VT.NCAT', ['PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC'])
        //     ->where('S.issqty', '>', 0)
        //     ->where('I.Code', $icode)
        //     ->where('VT.propertyi', $this->propertyid)
        //     ->where('S.departcode', $departcode)
        //     ->where('S.propertyid', $this->propertyid)
        //     ->value('QTY');

        $qty2 = DB::table('stock as S')
            ->leftJoin('itemmast as I', 'S.Item', '=', 'I.Code')
            ->leftJoin('voucher_type as VT', 'S.VType', '=', 'VT.V_Type')
            ->whereIn('VT.NCAT', ['PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC', 'KSISS'])
            ->where('I.Code', $icode)
            ->where('VT.propertyid', $this->propertyid)
            ->where('S.departcode', $departcode)
            ->where('S.propertyid', $this->propertyid)
            ->select(DB::raw('SUM(S.qtyiss) AS QTY'))
            ->value('QTY');

        $sum = $qty1 - $qty2;

        return response()->json(['qty' => $sum]);
    }

    public function deletestocktransfer(Request $request, $vno)
    {
        $permission = revokeopen(161116);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        if (empty($vno)) {
            return redirect()->back()->with('error', 'Vno ID is required.');
        }

        $vtype1 = 'KSREC';
        $vtype2 = 'KSISS';

        $updt = [
            'u_ae' => 'e',
            'u_name' => Auth::user()->u_name,
            'u_updatedt' => $this->currenttime,
            'delflag' => 'Y'
        ];

        Stock::where('propertyid', $this->propertyid)->whereIn('vtype', [$vtype1, $vtype2])->where('vno', $vno)->update($updt);

        return back()->with('success', 'Stock Bill Deleted Successfully');
    }

    public function updatestocktransfer(Request $request, $vno)
    {
        $permission = revokeopen(161116);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        if (empty($vno)) {
            return redirect()->back()->with('error', 'Vno ID is required.');
        }

        $vtype1 = 'KSREC';
        $vtype2 = 'KSISS';

        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $data = Stock::select('stock.*', 'g1.name as stockfrom', 'g2.name as stockto')
            ->leftJoin('godown_mast as g1', function ($join) {
                $join->on('stock.departcode', '=', 'g1.scode')
                    ->where('g1.propertyid', $this->propertyid);
            })
            ->leftJoin('godown_mast as g2', function ($join) {
                $join->on('stock.godowncode', '=', 'g2.scode')
                    ->where('g2.propertyid', $this->propertyid);
            })
            ->where('stock.propertyid', $this->propertyid)
            ->whereIn('stock.vtype', [$vtype1])
            ->where('stock.vno', $vno)
            ->get();


        $from = Stock::where('propertyid', $this->propertyid)->where('vtype', $vtype2)->where('vno', $vno)->first();
        $to  = Stock::where('propertyid', $this->propertyid)->where('vtype', $vtype1)->where('vno', $vno)->first();

        $fromrows = Stock::where('propertyid', $this->propertyid)->where('vtype', $vtype2)->where('vno', $vno)->get();

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();

        $sundrytype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'PURC' . $this->propertyid)->orderBy('sno', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.stocktransferupdate', [
            'data' => $data,
            'departs' => $departs,
            'godown' => $godown,
            'from' => $from,
            'to' => $to,
            'items' => $items,
            'sundrytype' => $sundrytype,
            'units' => $units,
            'fromrows' => $fromrows
        ]);
    }

    public function stocktransferupdate(Request $request)
    {
        $permission = revokeopen(161116);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vdate' => 'required',
            'vno' => 'required'
        ]);

        $totalitem = $request->totalitem;
        $totalamount = $request->netamount;
        $vtype1 = 'KSREC';

        $chkvpf1 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype1)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf1 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        // $vno1 = $chkvpf1->start_srl_no + 1;
        $vno1 = $request->vno;
        $vprefix1 = $chkvpf1->prefix;

        $docid1 = $this->propertyid . $vtype1 . '‎ ‎ ' . $vprefix1 . '‎ ‎ ‎ ‎ ' . $vno1;

        $vtype2 = 'KSISS';

        $chkvpf2 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype2)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf2 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        // $vno2 = $chkvpf2->start_srl_no + 1;
        $vno2 = $request->vno;
        $vprefix2 = $chkvpf2->prefix;

        $docid2 = $this->propertyid . $vtype2 . '‎ ‎ ' . $vprefix2 . '‎ ‎ ‎ ‎ ' . $vno2;

        // echo $totalitem;
        // exit;

        Stock::where('vno', $request->vno)->whereIn('vtype', [$vtype1, $vtype2])->delete();

        try {
            for ($i = 1; $i <= $totalitem; $i++) {
                $itemmast = ItemMast::where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();

                $stockdata1 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid1,
                    'sno'          => $i,
                    'vprefix'      => $vprefix1,
                    'vtype'        => $vtype1,
                    'vno'          => $vno1,
                    'vdate'        => $request->vdate,
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $docid2,
                    'contrasno'    => $i,
                    'departcode'   => $request->tolocation,
                    'godowncode'   => $request->tolocation,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => $request->input('qtyiss' . $i),
                    'accqty'       => $request->input('qtyiss' . $i),
                    'qtyiss'       => 0,
                    'qtyrec'       => $request->input('qtyiss' . $i),
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'total'        => 0,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'e',
                    'unit'         => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->remarks ?? '',
                    'delflag' => 'N'
                ];


                Stock::insert($stockdata1);

                $stockdata2 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid2,
                    'sno'          => $i,
                    'vprefix'      => $vprefix2,
                    'vtype'        => $vtype2,
                    'vno'          => $vno2,
                    'vdate'        => $request->vdate,
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $docid1,
                    'contrasno'    => $i,
                    'departcode'   => $request->fromlocation,
                    'godowncode'   => $request->fromlocation,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => '0',
                    'accqty'       => '0',
                    'qtyiss'       => $request->input('qtyiss' . $i),
                    'issqty'       => $request->input('qtyiss' . $i),
                    'qtyrec'       => '0',
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'total'        => 0,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'e',
                    'unit'         => $request->input('unit' . $i),
                    'issueunit'    => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->remarks ?? '',
                    'delflag' => 'N'
                ];

                Stock::insert($stockdata2);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

        // VoucherPrefix::where('propertyid', $this->propertyid)
        //     ->where('v_type', $vtype1)
        //     ->where('prefix', $vprefix1)
        //     ->increment('start_srl_no');

        // VoucherPrefix::where('propertyid', $this->propertyid)
        //     ->where('v_type', $vtype2)
        //     ->where('prefix', $vprefix2)
        //     ->increment('start_srl_no');

        return redirect('stocktransfer')->with('success', 'Stock Transfer Updated Successfully');
    }


    public function openingstock(Request $request)
    {
        $permission = revokeopen(121618);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $data = Stock::select(
            'stock.*',
            'godown_mast.name as subname',
            DB::raw('COUNT(stock.item) as totalitem'),
            'itemmast.name as itemname'
        )
            ->leftJoin('godown_mast', 'godown_mast.scode', 'stock.godowncode')
            ->leftJoin('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'stock.item')
                    ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
                    ->where('itemmast.Property_ID', $this->propertyid);
            })
            ->where('stock.propertyid', $this->propertyid)->where('stock.vtype', 'STOP')
            ->groupBy('stock.docid')
            ->groupBy('stock.item')
            ->get();
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();
        $superwiser = Auth::user()->superwiser;

        return view('property.openingstock', [
            'departs' => $departs,
            'godown' => $godown,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser
        ]);
    }

    public function openingstocksubmit(Request $request)
    {
        $permission = revokeopen(121618);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vdate' => 'required',
            'department' => 'required',
        ]);

        $totalitem = $request->totalitem;
        $totalamount = $request->netamount;
        $vtype = 'STOP';

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno = $chkvpf->start_srl_no + 1;
        $vprefix = $chkvpf->prefix;

        $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

        for ($i = 1; $i <= $totalitem; $i++) {
            $itemmast = ItemMast::where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();
            $stockdata = [
                'propertyid'   => $this->propertyid,
                'docid'        => $docid,
                'sno'          => $i,
                'vprefix'      => $vprefix,
                'vtype'        => $vtype,
                'vno'          => $vno,
                'vdate'        => $request->vdate,
                'item'         => $request->input('item' . $i),
                'specification' => 'Opening Stock',
                'contradocid'  => '',
                'contrasno'    => 0,
                'departcode'   => $request->department,
                'godowncode'   => $request->department,
                'rate'         => $request->input('itemrate' . $i),
                'chalqty'      => $request->input('qtyiss' . $i),
                'recdqty'      => $request->input('qtyiss' . $i),
                'accqty'       => $request->input('qtyiss' . $i),
                'qtyiss'       => 0,
                'qtyrec'       => $request->input('qtyiss' . $i),
                'recdunit'     => $request->input('unit' . $i),
                'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                'convratio'    => $itemmast->ConvRatio,
                'total'        => 0,
                'u_name'       => Auth::user()->u_name,
                'u_entdt'      => $this->currenttime,
                'u_ae'         => 'a',
                'unit'         => $request->input('unit' . $i),
                'itemrestcode' => $itemmast->RestCode,
                'remarks'      => $request->remarks ?? '',
                'delflag' => 'N'
            ];

            Stock::insert($stockdata);
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->where('prefix', $vprefix)
            ->increment('start_srl_no');

        return back()->with('success', 'Opening Stock Submitted Successfully');
    }

    public function updateopeningstock(Request $request)
    {
        $permission = revokeopen(121618);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $docid = $request->docid;
        $chk = Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        if (!$chk) {
            return back()->with('error', 'Stock not found');
        }

        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $items = ItemMast::select(
            'itemmast.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('SUM(taxstru.rate) as taxrate')
        )
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->orderBy('itemmast.Name', 'ASC')
            ->get();

        $stockdata = Stock::where('stock.propertyid', $this->propertyid)
            ->where('stock.docid', $docid)
            ->groupBy('stock.item')
            ->orderBy('stock.sno', 'ASC')
            ->get();

        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();
        $superwiser = Auth::user()->superwiser;
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.openingstockupdate', [
            'departs' => $departs,
            'units' => $units,
            'chk' => $chk,
            'godown' => $godown,
            'stockdata' => $stockdata,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser,
            'items' => $items
        ]);
    }

    public function openingstockupdatesubmit(Request $request)
    {
        $permission = revokeopen(121618);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'vdate' => 'required',
            'olddepartment' => 'required',
            'olddocid' => 'required',
            'oldvno' => 'required',
            'oldvprefix' => 'required'
        ]);

        $totalitem = $request->totalitem;
        $vtype = 'STOP';

        $docid = $request->olddocid;
        $chk = Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        if (!$chk) {
            return back()->with('error', 'Opening Stock not found');
        }

        Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

        $vno = $request->oldvno;
        $vprefix = $request->oldvprefix;

        for ($i = 1; $i <= $totalitem; $i++) {
            $itemmast = ItemMast::where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();
            $stockdata = [
                'propertyid'   => $this->propertyid,
                'docid'        => $docid,
                'sno'          => $i,
                'vprefix'      => $vprefix,
                'vtype'        => $vtype,
                'vno'          => $vno,
                'vdate'        => $request->vdate,
                'item'         => $request->input('item' . $i),
                'contradocid'  => '',
                'contrasno'    => 0,
                'specification' => 'Opening Stock',
                'departcode'   => $request->olddepartment,
                'godowncode'   => $request->olddepartment,
                'rate'         => $request->input('itemrate' . $i),
                'chalqty'      => $request->input('qtyiss' . $i),
                'recdqty'      => $request->input('qtyiss' . $i),
                'accqty'       => $request->input('qtyiss' . $i),
                'qtyiss'       => 0,
                'qtyrec'       => $request->input('qtyiss' . $i),
                'recdunit'     => $request->input('unit' . $i),
                'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                'convratio'    => $itemmast->ConvRatio,
                'total'        => 0,
                'u_name'       => Auth::user()->u_name,
                'u_entdt'      => $this->currenttime,
                'u_updatedt'   => $this->currenttime,
                'u_ae'         => 'e',
                'unit'         => $request->input('unit' . $i),
                'itemrestcode' => $itemmast->RestCode,
                'remarks'      => $request->remarks ?? '',
                'delflag' => 'N'
            ];

            Stock::insert($stockdata);
        }

        return redirect('openingstock')->with('success', 'Opening Stock Updated Successfully');
    }

    public function deleteopeningstock(Request $request)
    {
        $docid = $request->docid;
        $chk = Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        if (!$chk) {
            return back()->with('error', 'Stock not found');
        }

        Stock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

        return back()->with('success', 'Opening stock deleted successfully');
    }

    public function departmentwise(Request $request)
    {
        $department = $request->department;
        $itemcode = $request->itemcode;

        $chk = Stock::where('propertyid', $this->propertyid)
            ->where('godowncode', $department)
            ->where('item', $itemcode)
            ->first();

        if ($chk) {
            $data = [
                'stock' => $chk
            ];
            return json_encode($data);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Not Found For Department'
            ]);
        }
    }

    public function requisitionslip(Request $request)
    {
        $permission = revokeopen(161117);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $vtype = 'RQ';
        $vno = Indent::where('propertyid', $this->propertyid)->where('vtype', $vtype)->max('vno') + 1;

        $data = Indent::select('indent.*', 'g1.name as godown', 'd2.name as departname')
            ->leftJoin('godown_mast as g1', function ($join) {
                $join->on('indent.godown', '=', 'g1.scode')
                    ->where('g1.propertyid', $this->propertyid);
            })
            ->leftJoin('depart as d2', function ($join) {
                $join->on('indent.department', '=', 'd2.dcode')
                    ->where('d2.propertyid', $this->propertyid);
            })
            ->where('indent.propertyid', $this->propertyid)
            ->where('indent.vtype', $vtype)
            ->orderByDesc('indent.vno')
            ->get();

        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();

        return view('property.requistionslip', [
            'departs' => $departs,
            'godown' => $godown,
            'vno' => $vno,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser,
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function requisitionslipsubmit(Request $request)
    {
        $permission = revokeopen(161117);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validator = $request->validate([
            'transferno' => 'required',
            'vdate' => 'required',
        ]);

        $vtype = 'RQ';

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno = $chkvpf->start_srl_no + 1;
        $vprefix = $chkvpf->prefix;

        $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

        $indent = new Indent();
        $indent->propertyid = $this->propertyid;
        $indent->docid = $docid;
        $indent->vtype = $vtype;
        $indent->vno = $vno;
        $indent->vprefix = $vprefix;
        $indent->vdate = $request->input('vdate');
        $indent->vtime = date('H:i:s');
        $indent->department = $request->input('department');
        $indent->godown = $request->input('godown');
        $indent->remarks = $request->input('remarks') ?? '';
        $indent->veryfiuser = '';
        $indent->veryfidate = null;
        $indent->veryfiremark = '';
        $indent->company = '';
        $indent->u_name = Auth::user()->u_name;
        $indent->u_entdt = $this->currenttime;
        $indent->u_updatedt = null;
        $indent->u_ae = 'a';
        $indent->save();

        $totalitem = $request->input('totalitem');
        for ($i = 1; $i <= $totalitem; $i++) {
            $item = new Indent1();
            $item->propertyid = $this->propertyid;
            $item->docid = $docid;
            $item->vno = $vno;
            $item->vprefix = $vprefix;
            $item->vtype = $vtype;
            $item->vdate = $request->input('vdate');
            $item->sno = $i;
            $item->item = $request->input('item' . $i);
            $item->qty = $request->input('qtyiss' . $i);
            $item->vqty = $request->input('qtyiss' . $i);
            $item->unit = $request->input('unit' . $i);
            $item->balqty = $request->input('stockvali' . $i);
            $item->rate = $request->input('itemrate' . $i);
            $item->amount = $request->input('amount' . $i);
            $item->wtunit = $request->input('wtunit' . $i) ?? '';
            $item->u_name = Auth::user()->u_name;
            $item->u_entdt = $this->currenttime;
            $item->u_updatedt = null;
            $item->u_ae = 'a';
            $item->save();
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->where('prefix', $vprefix)
            ->increment('start_srl_no');

        return back()->with('success', 'Requisition Slip Submitted Successfully');
    }

    public function updaterequisitionslip(Request $request, $vno, $vprefix, $vtype)
    {
        $permission = revokeopen(161117);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $data = Indent::where('indent.propertyid', $this->propertyid)
            ->where('indent.vno', $vno)
            ->where('indent.vtype', $vtype)
            ->where('vprefix', $vprefix)
            ->first();

        if ($data->clearyn == 'Y') {
            return back()->with('error', 'Stock Transfer already done can not edit');
        }

        if (Auth::user()->superwiser != '1') {
            return back()->with('error', 'This voucher already verified cannot edit');
        }

        $datarows = Indent1::where('propertyid', $this->propertyid)->where('vno', $vno)->where('vprefix', $vprefix)
            ->where('vtype', $vtype)->get();

        // return $datarows;

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.requistionslipupdate', [
            'departs' => $departs,
            'godown' => $godown,
            'data' => $data,
            'datarows' => $datarows,
            'items' => $items,
            'units' => $units
        ]);
    }

    public function requisitionslipupdate(Request $request)
    {
        $permission = revokeopen(161117);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validator = $request->validate([
            'transferno' => 'required',
            'vdate' => 'required',
            'docid' => 'required'
        ]);

        $docid = $request->docid;


        if ($docid == '') {
            return back()->with('error', 'Docid Not Found');
        }

        try {
            DB::beginTransaction();

            $indent = Indent::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
            $indent->department = $request->input('department');
            $indent->godown = $request->input('godown');
            $indent->remarks = $request->input('remarks') ?? '';
            $indent->veryfiuser = '';
            $indent->veryfidate = null;
            $indent->veryfiremark = '';
            $indent->company = '';
            $indent->u_name = Auth::user()->u_name;
            $indent->u_updatedt = $this->currenttime;
            $indent->u_ae = 'e';
            $indent->save();

            Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            $totalitem = $request->input('totalitem');
            for ($i = 1; $i <= $totalitem; $i++) {
                $item = new Indent1();
                $item->propertyid = $this->propertyid;
                $item->docid = $docid;
                $item->vno = $indent->vno;
                $item->vprefix = $indent->vprefix;
                $item->vtype = $indent->vtype;
                $item->vdate = $indent->vdate;
                $item->sno = $i;
                $item->item = $request->input('item' . $i);
                $item->qty = $request->input('qtyiss' . $i);
                $item->vqty = $request->input('qtyiss' . $i);
                $item->unit = $request->input('unit' . $i);
                $item->balqty = $request->input('stockvali' . $i) ?? 0;
                $item->rate = $request->input('itemrate' . $i);
                $item->amount = $request->input('amount' . $i);
                $item->wtunit = $request->input('wtunit' . $i) ?? '';
                $item->u_name = Auth::user()->u_name;
                $item->u_entdt = $this->currenttime;
                $item->u_updatedt = $this->currenttime;
                $item->u_ae = 'e';
                $item->save();
            }
            DB::commit();
            return redirect('requisitionslip')->with('success', 'Requisition Slip Submitted Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function requisitionslipdelete(Request $request, $docid)
    {
        $permission = revokeopen(161117);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        if ($docid == '') {
            return back()->with('error', 'Docid Not Found');
        }

        try {
            DB::beginTransaction();

            Indent::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            DB::commit();
            return redirect('requisitionslip')->with('success', 'Requisition Slip Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function verifyrequisition(Request $request)
    {
        $permission = revokeopen(161118);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $vtype = 'RQ';
        $vno = Indent::where('propertyid', $this->propertyid)->where('vtype', $vtype)->max('vno') + 1;

        $data = Indent::select('indent.*', 'g1.name as godown', 'd2.name as departname')
            ->leftJoin('godown_mast as g1', function ($join) {
                $join->on('indent.godown', '=', 'g1.scode')
                    ->where('g1.propertyid', $this->propertyid);
            })
            ->leftJoin('depart as d2', function ($join) {
                $join->on('indent.department', '=', 'd2.dcode')
                    ->where('d2.propertyid', $this->propertyid);
            })
            ->where('indent.propertyid', $this->propertyid)
            ->orderByDesc('indent.vno')
            ->get();

        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();

        return view('property.requistionslipverify', [
            'departs' => $departs,
            'godown' => $godown,
            'vno' => $vno,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser,
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function requisitionslipverify(Request $request, $docid)
    {
        $permission = revokeopen(161118);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $data = Indent::where('indent.propertyid', $this->propertyid)
            ->where('indent.docid', $docid)
            ->first();

        $datarows = Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->get();

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.requistionslipverifypage', [
            'departs' => $departs,
            'godown' => $godown,
            'data' => $data,
            'datarows' => $datarows,
            'items' => $items,
            'units' => $units
        ]);
    }

    public function requisitionslipverifysub(Request $request)
    {
        $permission = revokeopen(161118);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validator = $request->validate([
            'transferno' => 'required',
            'vdate' => 'required',
            'docid' => 'required'
        ]);

        $docid = $request->docid;

        if ($docid == '') {
            return back()->with('error', 'Docid Not Found');
        }

        try {
            DB::beginTransaction();

            $indent = Indent::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
            $indent->department = $request->input('department');
            $indent->godown = $request->input('godown');
            $indent->remarks = $request->input('remarks') ?? '';
            $indent->veryfiuser = Auth::user()->u_name;
            $indent->veryfidate = $this->ncurdate;
            $indent->veryfiremark = $request->veryfiremark ?? '';
            $indent->company = '';
            $indent->u_updatedt = $this->currenttime;
            $indent->u_ae = 'e';
            $indent->save();

            Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            $totalitem = $request->input('totalitem');
            for ($i = 1; $i <= $totalitem; $i++) {
                $item = new Indent1();
                $item->propertyid = $this->propertyid;
                $item->docid = $docid;
                $item->vno = $indent->vno;
                $item->vprefix = $indent->vprefix;
                $item->vtype = $indent->vtype;
                $item->vdate = $indent->vdate;
                $item->sno = $i;
                $item->item = $request->input('item' . $i);
                $item->qty = $request->input('qtyiss' . $i);
                $item->vqty = $request->input('vqty' . $i);
                $item->unit = $request->input('unit' . $i);
                $item->balqty = $request->input('stockvali' . $i) ?? 0;
                $item->rate = $request->input('itemrate' . $i);
                $item->amount = $request->input('amount' . $i);
                $item->wtunit = $request->input('wtunit' . $i) ?? '';
                $item->u_name = Auth::user()->u_name;
                $item->u_entdt = $this->currenttime;
                $item->u_updatedt = $this->currenttime;
                $item->u_ae = 'e';
                $item->save();
            }
            DB::commit();
            return redirect('verifyrequisition')->with('success', 'Requisition Slip Verified Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function stockissuerequisition(Request $request)
    {
        $permission = revokeopen(161119);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $vtype1 = 'RQI';
        $vtype2 = 'RQR';
        $vno = Indent::where('propertyid', $this->propertyid)->where('vtype', $vtype1)->max('vno') + 1;

        // $data = Stock::select('stock.*', 'g1.name as stockfrom', 'g2.name as stockto')
        //     ->leftJoin('godown_mast as g1', function ($join) {
        //         $join->on('stock.departcode', '=', 'g1.scode')
        //             ->where('g1.propertyid', $this->propertyid);
        //     })
        //     ->leftJoin('godown_mast as g2', function ($join) {
        //         $join->on('stock.godowncode', '=', 'g2.scode')
        //             ->where('g2.propertyid', $this->propertyid);
        //     })
        //     ->where('stock.propertyid', $this->propertyid)
        //     ->whereIn('stock.vtype', [$vtype2])
        //     ->groupBy('stock.docid')
        //     ->where('stock.delflag', 'N')
        //     ->get();

        $data = $data = DB::table('stock as rqr')
            ->leftJoin('stock as rqi', function ($join) {
                $join->on('rqi.vno', '=', 'rqr.vno')
                    ->on('rqi.propertyid', '=', 'rqr.propertyid')
                    ->where('rqi.vtype', '=', 'RQI')
                    ->where('rqi.delflag', '=', 'N');
            })
            ->leftJoin('godown_mast as gfrom', function ($join) {
                $join->on('rqr.departcode', '=', 'gfrom.scode')
                    ->on('gfrom.propertyid', '=', 'rqr.propertyid');
            })
            ->leftJoin('godown_mast as gto', function ($join) {
                $join->on('rqi.godowncode', '=', 'gto.scode')
                    ->on('gto.propertyid', '=', 'rqi.propertyid');
            })
            ->select([
                'rqr.docid as rqr_docid',
                'rqi.docid as rqi_docid',
                'rqr.vno as rqr_vno',
                'rqi.vno as rqi_vno',
                'gfrom.name as stockfrom',
                'gto.name as stockto',
                'rqr.remarks as rqr_remarks',
                'rqr.vprefix as rqr_vprefix',
                'rqr.item',
                'rqr.qtyiss',
                'rqi.qtyrec',
                'rqr.unit',
                'rqr.rate',
                'rqr.amount',
                'rqr.vdate as requisition_date',
                'rqi.vdate as issue_date'
            ])
            ->where('rqr.vtype', '=', 'RQR')
            ->where('rqr.propertyid', '=', $this->propertyid)
            ->where('rqr.delflag', '=', 'N')
            ->groupBy('rqr.vno')
            ->orderBy('rqr.docid')
            ->orderByDesc('rqr.vno')
            ->get();

        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();


        $pendingindent = Indent::select('indent.*', 'godown_mast.name as departname')
            ->leftJoin('godown_mast', function ($join) {
                $join->on('godown_mast.scode', '=', 'indent.department');
            })
            ->where('indent.propertyid', $this->propertyid)
            ->where('indent.clearyn', '')
            ->get();


        return view('property.requisitionstockissue', [
            'departs' => $departs,
            'godown' => $godown,
            'vno' => $vno,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser,
            'ncurdate' => $this->ncurdate,
            'pendingindent' => $pendingindent
        ]);
    }

    public function indentitems(Request $request)
    {

        $department = $request->department;
        $indent = Indent::where('propertyid', $this->propertyid)->where('department', $department)->where('clearyn', '')->first();

        if (is_null($indent)) {
            return response()->json([
                'success' => false,
                'message' => 'Department Not Found'
            ], 401);
        }

        $groupdocid = Indent::select('docid')->where('propertyid', $this->propertyid)->where('department', $department)->where('clearyn', '')->groupBy('docid')->get();

        $envinventory = EnviroInventory::where('propertyid', $this->propertyid)->first();

        $itemstmp = ItemMast::select(
            'itemmast.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate'),
            'indent1.item as indentitem',
            'indent1.rate',
            'indent1.amount',
            'indent1.qty',
            'indent1.unit as itemunit',
            'indent1.balqty',
            'unitmast.name as unitname',
            'indent1.docid',
            'indent1.sno as indentsno'
        )
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'PURC' . $this->propertyid);
            })
            ->leftJoin('stock', function ($join) {
                $join->on('stock.item', '=', 'itemmast.Code');
            })
            ->leftJoin('indent1', function ($join) {
                $join->on('indent1.item', '=', 'itemmast.Code')
                    #->whereColumn('indent1.docid', '!=', 'stock.contradocid')
                    ->where('indent1.propertyid', $this->propertyid);
            })
            ->leftJoin('indent', function ($join) {
                $join->on('indent.docid', '=', 'indent1.docid')
                    ->where('indent.propertyid', $this->propertyid);
            })
            ->leftJoin('unitmast', function ($join) {
                $join->on('unitmast.ucode', '=', 'indent1.unit')
                    ->where('unitmast.propertyid', $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->whereIn('indent1.docid', $groupdocid->pluck('docid'))
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'PURC' . $this->propertyid)
            ->where('indent.clearyn', '')
            ->groupBy('indent1.item')
            ->groupBy('indent1.sno')
            ->orderBy('itemmast.Name', 'ASC');

        if ($envinventory->storeissuerequistion == 'Y') {
            $itemstmp = $itemstmp->where('indent.veryfiuser', '!=', '');
            $items = $itemstmp->get();
        } else {
            $items = $itemstmp->get();
        }

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();
        $taxstrudata = TaxStructure::select(
            'taxstru.*',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->where('taxstru.propertyid', $this->propertyid)
            ->groupBy('taxstru.name')
            ->orderBy('taxstru.name', 'ASC')
            ->get();

        $ledgerdata = DB::table('subgroup')->where('propertyid', $this->propertyid)->whereIn('group_code', ['23' . $this->propertyid, '10' . $this->propertyid, '14' . $this->propertyid,])->orderBy('name', 'ASC')->get();

        $data = [
            'items' => $items,
            'godown' => $godown,
            'units' => $units,
            'taxstrudata' => $taxstrudata,
            'ledgerdata' => $ledgerdata,
            'envinventory' => $envinventory,
            'indent' => $indent
        ];

        return json_encode($data);
    }

    public function requisitionstocksubmit(Request $request)
    {
        $permission = revokeopen(161119);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validate = $request->validate([
            'totalitem' => 'required',
            'netamount' => 'required',
            'transferno' => 'required',
            'indentdocid' => 'required'
        ]);

        $indentdocid = $request->indentdocid;

        // return $indentdocid;

        $totalitem = $request->totalitem;
        $vtype1 = 'RQI';

        $chkvpf1 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype1)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf1 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno1 = $chkvpf1->start_srl_no + 1;
        $vprefix1 = $chkvpf1->prefix;

        $docid1 = $this->propertyid . $vtype1 . '‎ ‎ ' . $vprefix1 . '‎ ‎ ‎ ‎ ' . $vno1;

        $vtype2 = 'RQR';

        $chkvpf2 = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype2)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf2 == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno2 = $chkvpf2->start_srl_no + 1;
        $vprefix2 = $chkvpf2->prefix;

        $docid2 = $this->propertyid . $vtype2 . '‎ ‎ ' . $vprefix2 . '‎ ‎ ‎ ‎ ' . $vno2;

        try {
            for ($i = 1; $i <= $totalitem; $i++) {
                $itemmast = ItemMast::where('Code', $request->input('item' . $i))->where('RestCode', 'PURC' . $this->propertyid)->first();
                $stockdata1 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid1,
                    'sno'          => $i,
                    'vprefix'      => $vprefix1,
                    'vtype'        => $vtype1,
                    'vno'          => $vno1,
                    'vtime'        => date('H:i:s'),
                    'vdate'        => $request->vdate,
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $request->input('docid' . $i),
                    'contrasno'    => $request->input('indentsno' . $i),
                    'departcode'   => $request->department,
                    'godowncode'   => $request->department,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => $request->input('qtyiss' . $i),
                    'accqty'       => $request->input('qtyiss' . $i),
                    'qtyiss'       => 0,
                    'qtyrec'       => $request->input('qtyiss' . $i),
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'total'        => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                    'unit'         => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->issueremarks ?? '',
                    'delflag' => 'N'
                ];

                Stock::insert($stockdata1);

                $stockdata2 = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid2,
                    'sno'          => $i,
                    'vprefix'      => $vprefix2,
                    'vtype'        => $vtype2,
                    'vno'          => $vno2,
                    'vdate'        => $request->vdate,
                    'vtime'        => date('H:i:s'),
                    'item'         => $request->input('item' . $i),
                    'contradocid'  => $request->input('docid' . $i),
                    'contrasno'    => $request->input('indentsno' . $i),
                    'departcode'   => $request->godown,
                    'godowncode'   => $request->godown,
                    'rate'         => $request->input('itemrate' . $i),
                    'recdqty'      => '0',
                    'accqty'       => '0',
                    'qtyiss'       => $request->input('qtyiss' . $i),
                    'issqty'       => $request->input('qtyiss' . $i),
                    'qtyrec'       => '0',
                    'recdunit'     => $request->input('unit' . $i),
                    'amount'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'total'       => $request->input('qtyiss' . $i) * $request->input('itemrate' . $i),
                    'convratio'    => $itemmast->ConvRatio,
                    'u_name'       => Auth::user()->u_name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                    'unit'         => $request->input('unit' . $i),
                    'issueunit'    => $request->input('unit' . $i),
                    'itemrestcode' => $itemmast->RestCode,
                    'remarks'      => $request->issueremarks ?? '',
                    'delflag' => 'N'
                ];

                Stock::insert($stockdata2);
            }

            Indent::where('propertyid', $this->propertyid)->where('docid', $indentdocid)->update(['clearyn' => 'Y']);

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype1)
                ->where('prefix', $vprefix1)
                ->increment('start_srl_no');

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype2)
                ->where('prefix', $vprefix2)
                ->increment('start_srl_no');

            return back()->with('success', 'Requistion Stock Issue Submitted');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updaterequisitionstockissue(Request $request, $vno, $vprefix)
    {
        $permission = revokeopen(161119);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $data = DB::table('stock as SH')
            ->select(
                'SH.*',
                'D.name as DEPARTNAME',
                'VT.description',
                'VT.ncat',
                'itemmast.Name as ItemName',
                'I1.qty as qtyreq',
                'unitmast.name as unitname',
                'I1.balqty'
            )
            ->leftJoin('godown_mast as D', 'D.scode', '=', 'SH.godowncode')
            ->leftJoin('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'SH.item')
                    ->where('itemmast.ItemType', '=', 'Store');
            })
            ->leftJoin('unitmast', function ($join) {
                $join->on('unitmast.ucode', '=', 'SH.unit')
                    ->where('unitmast.propertyid', $this->propertyid);
            })
            ->leftJoin('indent1 as I1', function ($join) {
                $join->on('I1.docid', '=', 'SH.contradocid')
                    ->on('I1.sno', '=', 'SH.contrasno');
            })
            ->leftJoin('voucher_type as VT', 'VT.v_type', '=', 'SH.vtype')
            ->where('VT.propertyid', $this->propertyid)
            ->where('SH.vtype', 'RQR')
            ->where('SH.vno', $vno)
            ->orderBy('SH.Sno')
            ->get();

        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $rqi = Stock::where('propertyid', $this->propertyid)->where('vtype', 'RQI')
            ->where('vno', $vno)->first();
        $rqr = Stock::where('propertyid', $this->propertyid)->where('vtype', 'RQR')
            ->where('vno', $vno)->first();

        return view('property.requisitionstockissueupdate', [
            'departs' => $departs,
            'godown' => $godown,
            'data' => $data,
            'ncurdate' => $this->ncurdate,
            'rqr' => $rqr,
            'rqi' => $rqi
        ]);
    }

    public function requisitionstockisuedelete(Request $request, $vno, $vprefix)
    {
        $permission = revokeopen(161119);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        try {
            $stock = Stock::where('propertyid', $this->propertyid)->whereIN('vtype', ['RQI', 'RQR'])->where('vno', $vno)->where('vprefix', $vprefix)->first();
            Indent::where('propertyid', $this->propertyid)->where('docid', $stock->contradocid)->update(['clearyn' => '']);
            Stock::where('propertyid', $this->propertyid)->whereIN('vtype', ['RQI', 'RQR'])->where('vno', $vno)->where('vprefix', $vprefix)->delete();

            return back()->with('success', 'Requistion Stock Issue Deleted');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function indent(Request $request)
    {
        $permission = revokeopen(161112);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $vtype = 'PIND';
        $vno = Indent::where('propertyid', $this->propertyid)->where('vtype', $vtype)->max('vno') + 1;

        $data = Indent::select('indent.*', 'd2.name as departname')
            ->leftJoin('depart as d2', function ($join) {
                $join->on('indent.department', '=', 'd2.dcode')
                    ->where('d2.propertyid', $this->propertyid);
            })
            ->where('indent.vtype', $vtype)
            ->where('indent.propertyid', $this->propertyid)
            ->get();

        $superwiser = Auth::user()->superwiser;
        $enviroinv = EnviroInventory::where('propertyid', $this->propertyid)->first();

        return view('property.indent', [
            'departs' => $departs,
            'godown' => $godown,
            'vno' => $vno,
            'data' => $data,
            'enviroinv' => $enviroinv,
            'superwiser' => $superwiser,
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function indentsubmit(Request $request)
    {
        $permission = revokeopen(161112);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validator = $request->validate([
            'transferno' => 'required',
            'vdate' => 'required',
        ]);

        $vtype = 'PIND';

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->whereDate('date_from', '<=', $request->input('vdate'))
            ->whereDate('date_to', '>=', $request->input('vdate'))
            ->first();

        if ($chkvpf == null) {
            return back()->with('error', 'Voucher Prefix not found for the selected date: ' . date('d-m-Y', strtotime($request->input('vdate'))));
        }

        $vno = $chkvpf->start_srl_no + 1;
        $vprefix = $chkvpf->prefix;

        $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

        $indent = new Indent();
        $indent->propertyid = $this->propertyid;
        $indent->docid = $docid;
        $indent->vtype = $vtype;
        $indent->vno = $vno;
        $indent->vprefix = $vprefix;
        $indent->vdate = $request->input('vdate');
        $indent->vtime = date('H:i:s');
        $indent->department = $request->input('department');
        $indent->godown = '';
        $indent->remarks = $request->input('remarks') ?? '';
        $indent->veryfiuser = '';
        $indent->veryfidate = null;
        $indent->veryfiremark = '';
        $indent->company = '';
        $indent->u_name = Auth::user()->u_name;
        $indent->u_entdt = $this->currenttime;
        $indent->u_updatedt = null;
        $indent->u_ae = 'a';
        $indent->save();

        $totalitem = $request->input('totalitem');
        for ($i = 1; $i <= $totalitem; $i++) {
            $item = new Indent1();
            $item->propertyid = $this->propertyid;
            $item->docid = $docid;
            $item->vtype = $vtype;
            $item->vno = $vno;
            $item->vprefix = $vprefix;
            $item->vdate = $request->input('vdate');
            $item->sno = $i;
            $item->item = $request->input('item' . $i);
            $item->qty = $request->input('qtyiss' . $i);
            $item->vqty = $request->input('qtyiss' . $i);
            $item->unit = $request->input('unit' . $i);
            $item->balqty = '';
            $item->rate = $request->input('itemrate' . $i);
            $item->amount = $request->input('amount' . $i);
            $item->wtunit = $request->input('wtunit' . $i) ?? '';
            $item->u_name = Auth::user()->u_name;
            $item->u_entdt = $this->currenttime;
            $item->u_updatedt = null;
            $item->u_ae = 'a';
            $item->save();
        }

        VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $vtype)
            ->where('prefix', $vprefix)
            ->increment('start_srl_no');

        return back()->with('success', 'Indent Submitted Successfully');
    }

    public function updateindent(Request $request, $docid)
    {
        $departs = Depart::where('propertyid', $this->propertyid)
            ->whereIn('nature', ['Outlet', 'Room Service'])->orderBy('name', 'ASC')->get();

        $godown = GodownMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        $data = Indent::where('indent.propertyid', $this->propertyid)
            ->where('indent.docid', $docid)
            ->first();

        if ($data->clearyn == 'Y') {
            return back()->with('error', 'Stock Transfer already done can not edit');
        }

        if (Auth::user()->superwiser != '1') {
            return back()->with('error', 'This voucher already verified cannot edit');
        }

        $datarows = Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->get();

        $items = ItemMast::where('Property_ID', $this->propertyid)->where('RestCode', 'PURC' . $this->propertyid)->orderBy('Name', 'ASC')->get();
        $units = UnitMast::where('propertyid', $this->propertyid)->orderBy('name', 'ASC')->get();

        return view('property.indentupdate', [
            'departs' => $departs,
            'godown' => $godown,
            'data' => $data,
            'datarows' => $datarows,
            'items' => $items,
            'units' => $units
        ]);
    }

    public function indentupdate(Request $request)
    {
        $permission = revokeopen(161112);
        if (is_null($permission) || $permission->edit == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        $validator = $request->validate([
            'transferno' => 'required',
            'vdate' => 'required',
            'docid' => 'required'
        ]);

        $docid = $request->docid;


        if ($docid == '') {
            return back()->with('error', 'Docid Not Found');
        }

        try {
            DB::beginTransaction();

            $indent = Indent::where('propertyid', $this->propertyid)->where('docid', $docid)->first();
            $indent->department = $request->input('department');
            $indent->godown = $request->input('godown');
            $indent->remarks = $request->input('remarks') ?? '';
            $indent->veryfiuser = '';
            $indent->veryfidate = null;
            $indent->veryfiremark = '';
            $indent->company = '';
            $indent->u_name = Auth::user()->u_name;
            $indent->u_updatedt = $this->currenttime;
            $indent->u_ae = 'e';
            $indent->save();

            Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            $totalitem = $request->input('totalitem');
            for ($i = 1; $i <= $totalitem; $i++) {
                $item = new Indent1();
                $item->propertyid = $this->propertyid;
                $item->docid = $docid;
                $item->vno = $indent->vno;
                $item->vprefix = $indent->vprefix;
                $item->vtype = $indent->vtype;
                $item->vdate = $indent->vdate;
                $item->sno = $i;
                $item->item = $request->input('item' . $i);
                $item->qty = $request->input('qtyiss' . $i);
                $item->vqty = $request->input('qtyiss' . $i);
                $item->unit = $request->input('unit' . $i);
                $item->balqty = $request->input('stockvali' . $i) ?? 0;
                $item->rate = $request->input('itemrate' . $i);
                $item->amount = $request->input('amount' . $i);
                $item->wtunit = $request->input('wtunit' . $i) ?? '';
                $item->u_name = Auth::user()->u_name;
                $item->u_entdt = $this->currenttime;
                $item->u_updatedt = $this->currenttime;
                $item->u_ae = 'e';
                $item->save();
            }
            DB::commit();
            return redirect('indent')->with('success', 'Indent Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function deleteindent(Request $request, $docid)
    {
        $permission = revokeopen(161112);
        if (is_null($permission) || $permission->del == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        if ($docid == '') {
            return back()->with('error', 'Docid Not Found');
        }

        try {
            DB::beginTransaction();

            Indent::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            Indent1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            DB::commit();
            return redirect('indent')->with('success', 'Indent Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error: ' . $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }
}
