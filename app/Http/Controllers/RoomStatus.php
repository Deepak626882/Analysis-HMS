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

use function App\Helpers\endsWith;
use function App\Helpers\removeSuffixIfExists;
use function PHPUnit\Framework\isNull;

class RoomStatus extends Controller
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

    public function inhouseroomstatus(Request $request)
    {
        return view('property.roomstatusinhouse');
    }

    public function inhoseroomstatusfetch(Request $request)
    {
        $data = DB::table('roomocc')
            ->select([
                'guestfolio.folio_no as FolioNo',
                'guestfolio.docid',
                'roomocc.plancode',
                DB::raw('DATE_SUB(roomocc.depdate, INTERVAL 1 DAY) as depdate_minus_one'),
                'enviro_form.checkout as envcheck',
                DB::raw('IFNULL(paycharge.billno, "0") as billno'),
                'room_mast.rcode as RoomNo',
                'guestprof.Name as GuestName',
                DB::raw("DATE_FORMAT(guestfolio.VDate, '%d/%m/%Y') as ChkInDate"),
                'roomocc.chkintime as ChkTime',
                DB::raw("DATE_FORMAT(roomocc.DepDate, '%d/%m/%Y') as DepDate"),
                'gueststats.Name as GuestStatus',
                DB::raw("CONCAT(
                        IFNULL(subgroup.name, ''),
                        IF(IFNULL(TA.name, '') = '' OR IFNULL(subgroup.name, '') = '', '', '/ '),
                        IFNULL(TA.name, '')
                    ) as CompanyName"),
                'guestprof.Add1 as Adress',
                'guestprof.city_name as City',
                'guestprof.country_name as Country',
                'plan_mast.Name as Plan',
                DB::raw("CONCAT(CAST(roomocc.adult AS CHAR), '/', CAST(roomocc.children AS CHAR)) as Pax"),
                DB::raw("CASE WHEN roomocc.leaderyn = 'Y' THEN 'Yes' ELSE 'No' END as Leader"),
                'roomocc.sno1 as SN',
                'roomocc.sno'
            ])
            ->distinct()
            ->join('room_mast', 'room_mast.rcode', '=', 'roomocc.roomno')
            ->leftJoin('plan_mast', 'roomocc.plancode', '=', 'plan_mast.pcode')
            ->join('guestfolio', 'roomocc.docid', '=', 'guestfolio.docid')
            ->leftJoin('enviro_form', 'enviro_form.propertyid', '=', 'roomocc.propertyid')
            ->join('guestprof', 'guestfolio.guestprof', '=', 'guestprof.guestcode')
            ->leftJoin('gueststats', 'gueststats.gcode', '=', 'guestprof.guest_status')
            ->leftJoin('subgroup', 'guestfolio.company', '=', 'subgroup.sub_code')
            ->leftJoin('subgroup as TA', 'guestfolio.TravelAgent', '=', 'TA.sub_code')
            ->join('enviro_general as E', 'guestfolio.propertyid', '=', 'E.propertyid')
            ->leftJoin('paycharge', function ($join) {
                $join->on('paycharge.folionodocid', '=', 'roomocc.docid')
                    ->on('paycharge.sno1', '=', 'roomocc.sno1')
                    ->where('paycharge.billno', '!=', 0);
            })
            ->where('roomocc.propertyid', $this->propertyid)
            ->where('room_mast.propertyid', $this->propertyid)
            ->where(function ($query) {
                $query->whereNotIn('roomocc.type', ['C', 'O'])
                    ->orWhereNull('roomocc.type');
            })
            ->where('room_mast.type', 'RO')
            ->orderBy('room_mast.rcode')
            ->get();

        return response()->json($data);
    }
}
