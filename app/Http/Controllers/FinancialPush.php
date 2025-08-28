<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Companyreg;
use App\Models\MenuHelp;
use App\Models\VoucherPrefix;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialPush extends Controller
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

    public function yearandupdation(Request $request)
    {
        $permission = revokeopen(122022);
        if (is_null($permission) || $permission->view == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        return view('property.yearandupdation', [
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function yearupdatesubmit(Request $request)
    {
        $permission = revokeopen(122022);
        if (is_null($permission) || $permission->ins == 0) {
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        // $sis = 'ANA';
        // $maxSid = Companyreg::max('comp_code');
        // $maxSidNum = intval(substr($maxSid, strlen($sis)));
        // $sidNum = $maxSidNum + 1;
        // $sidNumStr = str_pad($sidNum, 3, '0', STR_PAD_LEFT);
        // $comp_code = $sis . $sidNumStr;

        $company = Companyreg::where('propertyid', $this->propertyid)->orderBy('comp_code', 'DESC')->first();
        $start_dt = $company->start_dt;
        $start_dtplus = new DateTime($start_dt);
        $start_dtplus->modify('+1 year');

        $end_dt = $company->end_dt;
        $end_dtplus = new DateTime($end_dt);
        $end_dtplus->modify('+1 year');

        // $data = [
        //     'propertyid' => $company->propertyid,
        //     'comp_code' => $company->comp_code,
        //     'comp_name' => $company->comp_name,
        //     'role' => $company->role,
        //     'email' => $company->email,
        //     'mobile' => $company->mobile,
        //     'address1' => $company->address1,
        //     'address2' => $company->address2,
        //     'start_dt' => $start_dtplus->format('Y-m-d'),
        //     'end_dt' => $end_dtplus->format('Y-m-d'),
        //     'country' => $company->country,
        //     'state' => $company->state,
        //     'city' => $company->city,
        //     'state_code' => $company->state_code,
        //     'sn_num' => $company->sn_num,
        //     'password' => $company->password,
        //     'acname' => $company->acname,
        //     'acnum' => $company->acnum,
        //     'ifsccode' => $company->ifsccode,
        //     'bankname' => $company->bankname,
        //     'branchname' => $company->branchname,
        //     'cfyear' => $start_dtplus->format('Y') . '-' . substr($end_dtplus->format('Y'), 2),
        //     'pfyear' => $start_dtplus->format('Y') - 1 . '-' . substr($end_dtplus->format('Y'), 2) - 1,
        //     'u_name' => $company->u_name,
        //     'u_ae' => $company->u_ae,
        //     'u_entdt' => $company->u_entdt,
        //     'pan_no' => $company->pan_no,
        //     'nationality' => $company->nationality,
        //     'gstin' => $company->gstin,
        //     'division_code' => $company->division_code,
        //     'legal_name' => $company->legal_name,
        //     'trade_name' => $company->trade_name,
        //     'logo' => $company->logo,
        //     'website' => $company->website,
        //     'status' => $company->status
        // ];

        // Companyreg::insert($data);

        // $menuhelp = MenuHelp::where('propertyid', $this->propertyid)->where('compcode', $company->comp_code)->get();

        // foreach ($menuhelp as $row) {
        //     $menumodule = new MenuHelp;
        //     $menumodule->propertyid = $this->propertyid;
        //     $menumodule->username = $row->username;
        //     $menumodule->compcode = $comp_code;
        //     $menumodule->opt1 = $row->opt1;
        //     $menumodule->opt2 = $row->opt2;
        //     $menumodule->opt3 = $row->opt3;
        //     $menumodule->code = $row->code;
        //     $menumodule->route = $row->route;
        //     $menumodule->module = $row->module;
        //     $menumodule->module_name = $row->module_name;
        //     $menumodule->view = 1;
        //     $menumodule->ins = 1;
        //     $menumodule->edit = 1;
        //     $menumodule->del = 1;
        //     $menumodule->print = 1;
        //     $menumodule->flag = $row->flag;
        //     $menumodule->outletcode = $row->outletcode;
        //     $menumodule->u_entdt = $this->currenttime;
        //     $menumodule->u_updatedt = null;
        //     $menumodule->u_name = $row->u_name;
            // $menumodule->save();
        // }

        $voucher = VoucherPrefix::where('propertyid', $this->propertyid)->where('date_from', $company->start_dt)->get();

        foreach ($voucher as $row) {
            $vouchers = new VoucherPrefix;
            $vouchers->propertyid = $this->propertyid;
            $vouchers->short_name = $row->short_name;
            $vouchers->v_type = $row->v_type;
            $vouchers->start_srl_no = 0;
            $vouchers->date_from = $start_dtplus->format('Y-m-d');
            $vouchers->date_to = $end_dtplus->format('Y-m-d');
            $vouchers->prefix = $row->prefix + 1;
            $vouchers->u_ae = 'a';
            $vouchers->u_entdt = $this->currenttime;
            $vouchers->sysYN = $row->sysYN;
            $vouchers->save();
        }

        $updatencr = Carbon::parse($this->ncurdate)->addDays(1)->format('Y-m-d');

        DB::table('enviro_general')
            ->where('propertyid', $this->propertyid)
            ->update([
                'ncur' => $updatencr,
                'u_updatedt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'e',
            ]);


        return response()->json([
            'redirecturl' => 'logout',
            'status' => 'success',
            'message' => 'Year Update Processed Successfully',
        ]);
    }
}
