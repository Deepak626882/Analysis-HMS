<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsappSend;
use App\Models\BookingDetail;
use App\Models\BookingInquiry;
use App\Models\HallSale1;
use App\Models\ItemMast;
use App\Models\VenueMast;
use App\Models\VenueOcc;
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
use App\Models\EnviroBanquet;
use App\Models\EnviroWhatsapp;
use App\Models\HallBook;
use App\Models\HallSale2;
use App\Models\HallStock;
use App\Models\SubGroup;
use App\Models\Sale1log;
use App\Models\Sale2log;
use App\Models\Stocklog;
use App\Models\Suntranlog;
use App\Models\Kot as KoTModal;
use App\Models\Ledger;
use App\Models\PaychargeH;
use App\Models\Sundrytype;
use App\Models\SuntranH;
use App\Models\User;
use App\Models\VoucherPrefix;
use App\Models\VoucherType;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Finder\Iterator\VcsIgnoredFilterIterator;

use function App\Helpers\endsWith;
use function App\Helpers\removeSuffixIfExists;
use function App\Helpers\splitByJoin;

class Banquet extends Controller
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

    public function openbanquetbooking(Request $request)
    {
        $clicktime = $request->query('clicktime');
        $venuecode = $request->query('venuecode');
        $fromdate = $request->query('fromdate');
        $bookinginquiry = BookingInquiry::where('propertyid', $this->propertyid)->where('contradocid', '')->orderByDesc('sn')->get();

        return view('property.banquetbooking', [
            'clicktime' => $clicktime,
            'venuecode' => $venuecode,
            'fromdate' => $fromdate,
            'bookinginquiry' => $bookinginquiry
        ]);
    }

    public function checkvenuduplicate(Request $request)
    {
        $bookings = $request->bookings;

        foreach ($bookings as $row) {
            $venue_name = $row['venue_name'];
            $from_date = $row['from_date'];
            $to_date = $row['to_date'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $venuemast = VenueMast::where('propertyid', $this->propertyid)->where('code', $venue_name)->first();

            $start_datetime = date("Y-m-d H:i:s", strtotime("$from_date $from_time"));
            $end_datetime   = date("Y-m-d H:i:s", strtotime("$to_date $to_time"));

            $overlap = VenueOcc::where('propertyid', $this->propertyid)
                ->where('venucode', $venue_name)
                ->where(function ($query) use ($start_datetime, $end_datetime) {
                    $query->where(function ($q) use ($start_datetime, $end_datetime) {
                        $q->whereRaw("CONCAT(fromdate, ' ', dromtime) < ?", [$end_datetime])
                            ->whereRaw("CONCAT(todate, ' ', totime) > ?", [$start_datetime]);
                    });
                })
                ->exists();

            if ($overlap) {
                return response()->json([
                    'error' => '1',
                    'message' => "Booking already exists for venue: $venuemast->name"
                ]);
            }
        }

        return response()->json([
            'error' => '0',
            'message' => 'No conflicts found'
        ]);
    }

    public function checkvenuduplicateup(Request $request)
    {
        $bookings = $request->bookings;

        foreach ($bookings as $row) {
            $docid = $row['docid'];
            $venue_name = $row['venue_name'];
            $from_date = $row['from_date'];
            $to_date = $row['to_date'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $venuemast = VenueMast::where('propertyid', $this->propertyid)->where('code', $venue_name)->first();

            $start_datetime = date("Y-m-d H:i:s", strtotime("$from_date $from_time"));
            $end_datetime   = date("Y-m-d H:i:s", strtotime("$to_date $to_time"));

            $overlap = VenueOcc::where('propertyid', $this->propertyid)
                ->where('venucode', $venue_name)
                ->where(function ($query) use ($start_datetime, $end_datetime) {
                    $query->where(function ($q) use ($start_datetime, $end_datetime) {
                        $q->whereRaw("CONCAT(fromdate, ' ', dromtime) < ?", [$end_datetime])
                            ->whereRaw("CONCAT(todate, ' ', totime) > ?", [$start_datetime]);
                    });
                })
                ->whereNot('fpdocid', $docid)
                ->exists();

            if ($overlap) {
                return response()->json([
                    'error' => '1',
                    'message' => "Booking already exists for venue: $venuemast->name"
                ]);
            }
        }

        return response()->json([
            'error' => '0',
            'message' => 'No conflicts found'
        ]);
    }


    public function banquetparameter(Request $request)
    {

        $data = EnviroBanquet::where('propertyid', Auth::user()->propertyid)->first();

        if (is_null($data)) {
            $insert = new EnviroBanquet();
            $insert->propertyid = Auth::user()->propertyid;
            $insert->save();
        }

        return view('property.banquetparameter');
    }

    public function submitbanquetparameter(Request $request)
    {
        try {
            $data = EnviroBanquet::where('propertyid', $this->propertyid)->first();
            $data->outdoorcatering = $request->outdoorcatering;
            $data->cataloglimit = $request->cataloglimit;
            $data->roundoffac = $request->roundoffac;
            $data->discountac = $request->discountac;
            $data->indoorsaleac = $request->indoorsaleac;
            $data->indoorpartyac = $request->indoorpartyac;
            $data->panrequiredyn = $request->panrequiredyn;
            $data->roundofftype = $request->roundofftype;
            $data->u_ae = 'e';
            $data->banquet_edit_date =  $request->banquet_edit_date;
            $data->save();

            return back()->with('success', 'Banquet Parameter Updated Successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function banquetbookingsubmit(Request $request)
    {
        try {
            $totalrows = $request->totalrows;
            DB::beginTransaction();

            $vtype = "IBOOK";
            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $request->booking_date)
                ->whereDate('date_to', '>=', $request->booking_date)
                ->first();
            if ($chkvpf === null || $chkvpf === '0') {
                return back()->with('error', 'You are not eligible to checkin for this date: ' . date('d-m-Y', strtotime($request->booking_date)));
            }

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;
            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

            $partyname = $request->party;
            if (!empty($request->partysel)) {
                $inquiry = BookingInquiry::where('propertyid', $this->propertyid)->where('inqno', $request->partysel)->first();
                $partyname = $inquiry->partyname;

                BookingInquiry::where('propertyid', $this->propertyid)->where('inqno', $request->partysel)->update(['contradocid' => $docid]);
            }

            $booking = new HallBook();
            $booking->propertyid = $this->propertyid;
            $booking->docid = $docid;
            $booking->vtype = $vtype;
            $booking->vno = $start_srl_no;
            $booking->vtime = date('H:i:s');
            $booking->vprefix = $vprefix;
            $booking->vdate = $request->booking_date;
            $booking->partyname = $partyname;
            $booking->add1 = $request->address ?? '';
            $booking->city = $request->city_name;
            $booking->panno = $request->pan_no ?? '';
            $booking->mobileno = $request->mobile_no ?? '';
            $booking->mobileno1 = $request->mobile_no2 ?? '';
            $booking->func_name = $request->function_type;
            $booking->restcode = 'BANQ' . $this->propertyid;
            $booking->housekeeping = $request->department_instruction1 ?? '';
            $booking->frontoff = $request->department_instruction2 ?? '';
            $booking->engg = $request->department_instruction3 ?? '';
            $booking->security = $request->department_instruction4 ?? '';
            $booking->chef = $request->department_instruction5 ?? '';
            $booking->board = $request->boardtoread ?? '';
            $booking->menuspl1 = $request->special_instruction1 ?? '';
            $booking->menuspl2 = $request->special_instruction2 ?? '';
            $booking->menuspl3 = $request->special_instruction3 ?? '';
            $booking->menuspl4 = $request->special_instruction4 ?? '';
            $booking->menuspl5 = $request->special_instruction5 ?? '';
            $booking->expatt = $request->exp_pax ?? 0;
            $booking->guaratt = $request->gurr_pax ?? 0;
            $booking->coverrate = $request->rate_pax ?? 0;
            $booking->companycode = $request->company_name ?? '';
            $booking->remark = $request->remark ?? '';
            $booking->bookingagent = $request->booking_agent ?? '';
            $booking->u_name = Auth::user()->name;
            $booking->u_entdt = now();
            $booking->u_updatedt = null;
            $booking->u_ae = 'a';

            $booking->save();

            for ($i = 1; $i <= $totalrows; $i++) {
                $venue = new VenueOcc();

                $venue->propertyid = $this->propertyid;
                $venue->fpdocid = $docid;
                $venue->venucode = $request->input("venue_name$i");
                $venue->sno = $i;
                $venue->fromdate = $request->input("from_date$i");
                $venue->dromtime = $request->input("from_time$i");
                $venue->todate = $request->input("to_date$i");
                $venue->totime = $request->input("to_time$i");
                $venue->u_name = Auth::user()->name;
                $venue->u_entdt = now();
                $venue->u_updatedt = null;
                $venue->u_ae = "a";
                $venue->save();
            }

            VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            DB::commit();

            return back()->with('success', 'Booking Subitted Successfully');
            // return $docid;
        } catch (Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function hallbookfetch(Request $request, $docid)
    {
        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        $venues = VenueOcc::where('propertyid', $this->propertyid)->where('fpdocid', $docid)->orderBy('sno')->get();

        $sundrytype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'BANQ' . $this->propertyid)->orderBy('sno')->get();

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->first();
        $paychargeh = PaychargeH::where('propertyid', $this->propertyid)->where('contradocid', $docid)->where('sno', '1')->orderBy('vno')->get();

        $data = [
            'hallbook' => $hallbook,
            'venues' => $venues,
            'sundrytype' => $sundrytype,
            'depart' => $depart,
            'paychargeh' => $paychargeh
        ];

        return response()->json($data);
    }

    public function hallsalefetch(Request $request, $docid)
    {
        $hallsale1 = HallSale1::where('propertyid', $this->propertyid)->where('docId', $docid)->first();
        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $hallsale1->bookdocid)->first();
        $venues = VenueOcc::where('propertyid', $this->propertyid)->where('fpdocid', $hallsale1->bookdocid)->orderBy('sno')->get();

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->first();
        $paychargeh = PaychargeH::where('propertyid', $this->propertyid)->where('contradocid', $hallsale1->bookdocid)->where('sno', '1')->orderBy('vno')->get();

        $sundrytype = SuntranH::select(
            'suntranh.*',
            'sundrytype.nature',
            'sundrytype.disp_name',
            'sundrytype.vtype',
            'sundrytype.automanual'
        )
            ->leftJoin('sundrytype', function ($join) {
                $join->on('sundrytype.sundry_code', '=', 'suntranh.suncode')
                    ->whereColumn('sundrytype.sno', 'suntranh.sno')
                    ->where('sundrytype.vtype', '=', 'BANQ' . $this->propertyid);
            })
            ->where('suntranh.propertyid', $this->propertyid)
            ->where('suntranh.docid', $hallsale1->docId)
            ->orderBy('suntranh.sno')
            ->get();

        $sundrytype2 = Suntran::select(
            'suntran.*',
            'sundrytype.nature',
            'sundrytype.disp_name',
            'sundrytype.vtype',
            'sundrytype.automanual'
        )
            ->leftJoin('sundrytype', function ($join) {
                $join->on('sundrytype.sundry_code', '=', 'suntran.suncode')
                    ->whereColumn('sundrytype.sno', 'suntran.sno')
                    ->where('sundrytype.vtype', '=', 'BANQ' . $this->propertyid);
            })
            ->where('suntran.propertyid', $this->propertyid)
            ->where('suntran.docid', $hallsale1->docId)
            ->orderBy('suntran.sno')
            ->get();

        $items = ItemMast::select(
            'itemmast.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'BANQ' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'BANQ' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->orderBy('itemmast.Name', 'ASC')
            ->get();

        $stockitems = HallStock::select(
            'hallstock.*',
            'taxstru.str_code',
            'itemcatmast.AcCode',
            DB::raw('GROUP_CONCAT(taxstru.tax_code ORDER BY taxstru.sno ASC) as taxcodes'),
            DB::raw('GROUP_CONCAT(taxstru.rate ORDER BY taxstru.sno ASC) as taxrate')
        )
            ->leftJoin('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'hallstock.item')
                    ->where('itemmast.RestCode', 'BANQ' . $this->propertyid);
            })
            ->leftJoin('itemcatmast', function ($join) {
                $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                    ->where('itemcatmast.RestCode', 'BANQ' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('hallstock.propertyid', $this->propertyid)
            ->where('hallstock.docid', $hallsale1->docId)
            ->groupBy('hallstock.item')
            ->orderBy('hallstock.sno', 'ASC')
            ->get();

        $data = [
            'hallbook' => $hallbook,
            'venues' => $venues,
            'sundrytype' => $sundrytype,
            'sundrytype2' => $sundrytype2,
            'depart' => $depart,
            'paychargeh' => $paychargeh,
            'hallsale1' => $hallsale1,
            'stockitems' => $stockitems,
            'items' => $items
        ];

        return response()->json($data);
    }

    public function updatebanquet(Request $request, $docid)
    {
        $hallsale = HallSale1::where('propertyid', $this->propertyid)->where('bookdocid', $docid)->first();

        if (!is_null($hallsale)) {
            return back()->with('error', 'Bill Submitted can not update');
        }

        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        $venues = VenueOcc::where('propertyid', $this->propertyid)->where('fpdocid', $docid)->orderBy('sno')->get();

        $sundrytype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'BANQ' . $this->propertyid)->orderBy('sno')->get();

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', 'BANQ' . $this->propertyid)->first();
        $paychargeh = PaychargeH::where('propertyid', $this->propertyid)->where('sno', '1')->whereNot('amtcr', '0.00')->where('contradocid', $docid)->get();

        return view('property.banquetupdate', [
            'hallbook' => $hallbook,
            'venues' => $venues,
            'sundrytype' => $sundrytype,
            'depart' => $depart,
            'paychargeh' => $paychargeh
        ]);
    }

    public function deletebanquet(Request $request, $docid)
    {
        $hallsale = HallSale1::where('propertyid', $this->propertyid)->where('bookdocid', $docid)->first();

        $inquiry = BookingInquiry::where('propertyid', $this->propertyid)->where('contradocid', $docid)->first();

        if (!is_null($inquiry)) {
            BookingInquiry::where('propertyid', $this->propertyid)->where('contradocid', $docid)->update(['contradocid' => '']);
        }

        if (!is_null($hallsale)) {
            return back()->with('error', 'Bill Submitted can not update');
        }

        try {
            HallBook::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            VenueOcc::where('propertyid', $this->propertyid)->where('fpdocid', $docid)->delete();
            return back()->with('success', 'Booking Deleted Successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function banquetbilling(Request $request)
    {

        $banquet_edit_date = EnviroBanquet::where('propertyid', Auth::user()->propertyid)->first('banquet_edit_date');
        $readonly = ($banquet_edit_date->banquet_edit_date === 1) ? 'readonly' : '';
        return view('property.banquetbilling', compact('banquet_edit_date', 'readonly'));
    }

    public function advanceabanquet(Request $request, $docid)
    {
        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        // if (!$hallbook) {
        //     return back()->with('error', 'Booking Not Found');
        // }

        $companydata = Companyreg::where('propertyid', $this->propertyid)->first();

        $revdata = DB::table('revmast')
            ->select('revmast.name', 'revmast.rev_code', 'revmast.nature', 'revmast.field_type', 'revmast.flag_type', 'depart_pay.pay_code')
            ->leftJoin('depart_pay', 'revmast.rev_code', '=', 'depart_pay.pay_code')
            ->where('revmast.field_type', '=', 'P')
            ->where('revmast.propertyid', $this->propertyid)
            ->get();

        $taxstrudata = DB::table('taxstru')
            ->where('propertyid', $this->propertyid)
            ->orderBy('name', 'ASC')->groupBy('name')->get();

        return view('property.banquetadvance', [
            'data' => $hallbook,
            'companydata' => $companydata,
            'revdata' => $revdata,
            'taxstrudata' => $taxstrudata,
        ]);
    }

    public function deleteadvancebanquet($docid)
    {
        $chk = PaychargeH::where('propertyid', $this->propertyid)->where('docid', $docid)->first();

        if (is_null($chk)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Vno'
            ]);
        }

        PaychargeH::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Advance Deleted successfully'
        ]);
    }

    public function advancebanquetsubmit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'advancetype' => 'required',
                'partyname' => 'required',
                'paytype' => 'required',
                'narration' => 'required',
                'amount' => 'required',
            ]);

            DB::beginTransaction();

            $tablename = 'paychargeh';

            $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $request->docid)->first();

            if (!$hallbook) {
                return back()->with('error', 'Booking Not Found');
            }

            $vtype = $request->input('prevtype');
            $voucherPrefix = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $this->ncurdate)
                ->whereDate('date_to', '>=', $this->ncurdate)
                ->first();

            // return $vtype;

            if (!$voucherPrefix) {
                DB::rollBack();
                return redirect('banquetbooking')->with('error', 'Voucher prefix not found');
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
                return redirect('banquetbooking')->with('error', 'Payment type not found');
            }

            $mainEntryData = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vno' => $vno,
                'sno' => '1',
                'fpno' => $hallbook->vno,
                'vtype' => $vtype,
                'vdate' => $this->ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'paycode' => $request->input('paytype'),
                'paytype' => $paytype->pay_type,
                'comments' => $request->input('narration'),
                'comp_code' => '',
                'roomno' => 0,
                'amtdr' => $amtdr,
                'amtcr' => $amtcr,
                'roomcat' => '',
                'restcode' => 'BANQ' . $this->propertyid,
                'billamount' => $amount,
                'taxper' => 0,
                'onamt' => 0,
                'taxstru' => $request->input('tax_stru') ?? '',
                'contradocid' => $request->input('docid'),
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

                            $comments = $taxName . ', ' . 'Bill No: ' . $hallbook->vno;

                            $taxEntryData = [
                                'propertyid' => $this->propertyid,
                                'docid' => $docid,
                                'vno' => $vno,
                                'sno' => $tax->sno + 1,
                                'fpno' => $hallbook->vno,
                                'vtype' => $vtype,
                                'vdate' => $this->ncurdate,
                                'vtime' => date('H:i:s'),
                                'vprefix' => $vprefix,
                                'paycode' => $tax->tax_code,
                                'comments' => $comments,
                                'roomno' => 0,
                                'amtcr' => $amtcrTaxed,
                                'amtdr' => $amtdrTaxed,
                                'roomcat' => '',
                                'restcode' => 'BANQ' . $this->propertyid,
                                'billamount' => 0.00,
                                'taxper' => $rate,
                                'taxstru' => $taxStru,
                                'onamt' => $amount,
                                'contradocid' => $request->input('docid'),
                                'u_entdt' => $this->currenttime,
                                'u_name' => Auth::user()->u_name,
                                'u_ae' => 'a',
                            ];

                            DB::table($tablename)->insert($taxEntryData);
                        }
                    }
                }
            }

            $indoorpartyac = banquetparameter()->indoorpartyac;
            $subgroup = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $indoorpartyac)->first();

            $commonLedgerData = [
                'propertyid'   => $this->propertyid,
                'docid'        => $docid,
                'vno'          => $vno,
                'vdate'        => $this->ncurdate,
                'vtype'        => $vtype,
                'vprefix'      => $vprefix,
                'narration'    => 'Banquet Booking No. : ' . $vno . ' ' . date('d-m-Y', strtotime($this->ncurdate)),
                'chqno'        => '',
                'chqdate'      => NULL,
                'clgdate'      => $this->ncurdate,
                'u_name'       => Auth::user()->name,
                'u_entdt'      => $this->currenttime,
                'u_ae'         => 'a',
            ];

            $subgroup2 = SubGroup::where('propertyid', $this->propertyid)->where('sub_code', $paytype->ac_code)->first();

            $ledgers = [
                array_merge($commonLedgerData, [
                    'vsno'         => 1,
                    'subcode'      => $paytype->ac_code,
                    'contrasub'    => $indoorpartyac,
                    'amtcr'        => 0.00,
                    'amtdr'        => $amount,
                    'groupcode'    => $subgroup2->group_code,
                    'groupnature'  => $subgroup2->nature,
                ]),
                array_merge($commonLedgerData, [
                    'vsno'         => 2,
                    'subcode'      => $indoorpartyac,
                    'contrasub'    => $paytype->ac_code,
                    'amtcr'        => $amount,
                    'amtdr'        => 0.00,
                    'groupcode'    => $subgroup->group_code,
                    'groupnature'  => $subgroup->nature,
                ])
            ];

            Ledger::insert($ledgers);

            $updatedRows = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->where('prefix', $vprefix)
                ->increment('start_srl_no');

            if (!$updatedRows) {
                DB::rollBack();
                return redirect('banquetbooking')->with('error', 'Failed to update voucher prefix');
            }

            DB::commit();
            return redirect('banquetbooking')->with('success', 'Advance Deposit Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('banquetbooking')->with('error', 'Failed to process advance deposit: ' . $e->getMessage());
        }
    }

    public function banquetbookingupdate(Request $request)
    {
        try {
            $totalrows = $request->totalrows;
            $docid = $request->docid;

            DB::beginTransaction();

            HallBook::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->update([
                    'partyname' => $request->party ?? '',
                    'add1' => $request->address ?? '',
                    'city' => $request->city_name,
                    'panno' => $request->pan_no ?? '',
                    'mobileno' => $request->mobile_no ?? '',
                    'mobileno1' => $request->mobile_no2 ?? '',
                    'func_name' => $request->function_type,
                    'housekeeping' => $request->department_instruction1 ?? '',
                    'frontoff' => $request->department_instruction2 ?? '',
                    'engg' => $request->department_instruction3 ?? '',
                    'security' => $request->department_instruction4 ?? '',
                    'chef' => $request->department_instruction5 ?? '',
                    'board' => $request->boardtoread ?? '',
                    'menuspl1' => $request->special_instruction1 ?? '',
                    'menuspl2' => $request->special_instruction2 ?? '',
                    'menuspl3' => $request->special_instruction3 ?? '',
                    'menuspl4' => $request->special_instruction4 ?? '',
                    'menuspl5' => $request->special_instruction5 ?? '',
                    'expatt' => $request->exp_pax ?? 0,
                    'guaratt' => $request->gurr_pax ?? 0,
                    'coverrate' => $request->rate_pax ?? 0,
                    'companycode' => $request->company_name ?? '',
                    'remark' => $request->remark ?? '',
                    'bookingagent' => $request->booking_agent ?? '',
                    'u_name' => Auth::user()->name,
                    'u_updatedt' => now(),
                    'u_ae' => 'e',
                ]);


            // Clear previous venues
            VenueOcc::where('propertyid', $this->propertyid)->where('fpdocid', $docid)->delete();

            for ($i = 1; $i <= $totalrows; $i++) {
                $venue = new VenueOcc();
                $venue->propertyid = $this->propertyid;
                $venue->fpdocid = $docid;
                $venue->venucode = $request->input("venue_name$i");
                $venue->sno = $i;
                $venue->fromdate = $request->input("from_date$i");
                $venue->dromtime = $request->input("from_time$i");
                $venue->todate = $request->input("to_date$i");
                $venue->totime = $request->input("to_time$i");
                $venue->u_name = Auth::user()->name;
                $venue->u_entdt = now();
                $venue->u_updatedt = now();
                $venue->u_ae = "e";
                $venue->save();
            }

            DB::commit();
            return redirect('banquetbooking')->with("success", "Banquet Booking Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function banquetitems(Request $request)
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
                    ->where('itemcatmast.RestCode', 'BANQ' . $this->propertyid);
            })
            ->leftJoin('taxstru', 'taxstru.str_code', '=', 'itemcatmast.TaxStru')
            ->where('itemmast.Property_ID', $this->propertyid)
            ->where('itemmast.RestCode', 'BANQ' . $this->propertyid)
            ->groupBy('itemmast.Code')
            ->orderBy('itemmast.Name', 'ASC')
            ->get();

        $sundrytype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', 'BANQ' . $this->propertyid)->orderBy('sno')->get();

        $data = [
            'items' => $items,
            'sundrytype' => $sundrytype
        ];

        return json_encode($data);
    }

    public function banquetbillingsubmit(Request $request)
    {

        try {
            // DB::beginTransaction();
            $totalitem = $request->totalitem;

            // return $totalitem;
            $vtype = "IDC";
            $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                ->where('v_type', $vtype)
                ->whereDate('date_from', '<=', $request->booking_date)
                ->whereDate('date_to', '>=', $request->booking_date)
                ->first();
            if ($chkvpf === null || $chkvpf === '0') {
                return back()->with('error', 'You are not eligible to checkin for this date: ' . date('d-m-Y', strtotime($request->booking_date)));
            }

            $start_srl_no = $chkvpf->start_srl_no + 1;
            $vprefix = $chkvpf->prefix;
            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $start_srl_no;

            $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $request->bookingdocid)->first();

            if (!$hallbook) {
                return back()->with('error', 'Hallbook Docid Not Found');
            }

            $rest = 'BANQ' . $this->propertyid;

            $vat      = floatval($request->input($rest . 'vatamount', 0));
            $cgst     = floatval($request->input($rest . 'cgstamount', 0));
            $cgstrate = floatval($request->input($rest . 'cgstrate', 0));
            $sgst     = floatval($request->input($rest . 'sgstamount', 0));
            $sgstrate = floatval($request->input($rest . 'sgstrate', 0));
            $totaltaxable = floatval($request->input($rest . 'totaltaxable', 0));
            $totalnontaxable = floatval($request->input($rest . 'totalnontaxable', 0));
            $service  = floatval($request->input($rest . 'serviceamount', 0));
            $discper = floatval($request->input($rest . 'discountfix', 0));
            $discount = floatval($request->input($rest . 'discountsundry', 0));
            $roundoff = floatval($request->input($rest . 'roundoffamount', 0));
            $netamt   = floatval($request->input($rest . 'netamount', 0));
            $totalamt = floatval($request->input($rest . 'totalamountoutlet', 0));
            $sundryCount = intval($request->input($rest . 'sundrycount', 0));

            if ($totalitem > 0) {
                $vat2      = floatval($request->input('s' . $rest . 'vatamount', 0));
                $cgst2     = floatval($request->input('s' . $rest . 'cgstamount', 0));
                $cgstrate2 = floatval($request->input('s' . $rest . 'cgstrate', 0));
                $sgst2     = floatval($request->input('s' . $rest . 'sgstamount', 0));
                $sgstrate2 = floatval($request->input('s' . $rest . 'sgstrate', 0));
                $totaltaxable2 = floatval($request->input('s' . $rest . 'totaltaxable', 0));
                $totalnontaxable2 = floatval($request->input('s' . $rest . 'totalnontaxable', 0));
                $service2  = floatval($request->input('s' . $rest . 'serviceamount', 0));
                $discper2 = floatval($request->input('s' . $rest . 'discountfix', 0));
                $discount2 = floatval($request->input('s' . $rest . 'discountsundry', 0));
                $roundoff2 = floatval($request->input('s' . $rest . 'roundoffamount', 0));
                $netamt2  = floatval($request->input('s' . $rest . 'netamount', 0));
                $totalamt2 = floatval($request->input('s' . $rest . 'totalamountoutlet', 0));
                $sundryCount2 = intval($request->input('s' . $rest . 'sundrycount', 0));

                for ($s = 1; $s <= $sundryCount2; $s++) {
                    $st = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', $rest)->where('sno', $s)->first();
                    if (!$st) continue;

                    $amt = 0;
                    $base = 0;
                    $svalue = 0;
                    if ($st->disp_name == 'Discount') {
                        $amt = $discount2;
                        $svalue = $discper2;
                    } elseif ($st->disp_name == 'Service Charge') {
                        $amt = $service2;
                    } elseif ($st->disp_name == 'Amount') {
                        $amt = $totalamt2;
                    } elseif ($st->disp_name == 'CGST') {
                        $amt = $cgst2;
                        $svalue = $cgstrate2;
                    } elseif ($st->disp_name == 'SGST') {
                        $amt = $sgst2;
                        $svalue = $sgstrate2;
                    } elseif ($st->disp_name == 'VAT') {
                        $amt = $vat2;
                    } elseif ($st->disp_name == 'Round Off') {
                        $amt = $roundoff2;
                        $base = $netamt2 + $roundoff2;
                    } elseif ($st->disp_name == 'Net Amount') {
                        $amt = $netamt2;
                    }

                    $suntrandata1 = [
                        'propertyid' => $this->propertyid,
                        'docid'       => $docid,
                        'sno'         => $s,
                        'vno'         => $start_srl_no,
                        'vtype'       => $vtype,
                        'vdate'       => $request->booking_date,
                        'dispname'    => $st->disp_name,
                        'suncode'     => $st->sundry_code,
                        'calcformula' => $st->calcformula,
                        'svalue'      => $svalue,
                        'amount'      => $amt,
                        'baseamount'  => $base,
                        'revcode'     => $st->revcode,
                        'restcode'    => $rest,
                        'sunappdate'  => $st->appdate,
                        'delflag'     => 'N',
                        'u_entdt'     => $this->currenttime,
                        'u_name'      => Auth::user()->u_name,
                        'u_ae'        => 'a',
                    ];

                    Suntran::insert($suntrandata1);
                }
            }

            for ($s = 1; $s <= $sundryCount; $s++) {
                $st = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', $rest)->where('sno', $s)->first();
                if (!$st) continue;

                $amt = 0;
                $base = 0;
                $svalue = 0;
                if ($st->disp_name == 'Discount') {
                    $amt = $discount;
                    $svalue = $discper;
                } elseif ($st->disp_name == 'Service Charge') {
                    $amt = $service;
                } elseif ($st->disp_name == 'Amount') {
                    $amt = $totalamt;
                } elseif ($st->disp_name == 'CGST') {
                    $amt = $cgst;
                    $svalue = $cgstrate;
                } elseif ($st->disp_name == 'SGST') {
                    $amt = $sgst;
                    $svalue = $sgstrate;
                } elseif ($st->disp_name == 'VAT') {
                    $amt = $vat;
                } elseif ($st->disp_name == 'Round Off') {
                    $amt = $roundoff;
                    $base = $netamt + $roundoff;
                } elseif ($st->disp_name == 'Net Amount') {
                    $amt = $netamt;
                }

                $suntrandata = [
                    'propertyid' => $this->propertyid,
                    'docid'       => $docid,
                    'sno'         => $s,
                    'vno'         => $start_srl_no,
                    'vtype'       => $vtype,
                    'vdate'       => $request->booking_date,
                    'dispname'    => $st->disp_name,
                    'suncode'     => $st->sundry_code,
                    'calcformula' => $st->calcformula,
                    'svalue'      => $svalue,
                    'amount'      => $amt,
                    'baseamount'  => $base,
                    'revcode'     => $st->revcode,
                    'restcode'    => $rest,
                    'sunappdate'  => $st->appdate,
                    'delflag'     => 'N',
                    'u_entdt'     => $this->currenttime,
                    'u_name'      => Auth::user()->u_name,
                    'u_ae'        => 'a',
                ];

                SuntranH::insert($suntrandata);
            }

            $suntranh = SuntranH::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->get()
                ->keyBy('sno');

            $suntran = Suntran::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->get()
                ->keyBy('sno');

            $allSnos = $suntranh->keys()->merge($suntran->keys())->unique();

            $finalData = [];

            foreach ($allSnos as $sno) {
                $h = $suntranh->get($sno);
                $n = $suntran->get($sno);

                $row = [];

                $row['dispname'] = $h->dispname ?? $n->dispname;
                $row['suncode']  = $h->suncode ?? $n->suncode;
                $row['sunappdate']  = $h->sunappdate ?? $n->sunappdate;
                $row['sno']  = $h->sno ?? $n->sno;
                $row['revcode']  = $h->revcode ?? $n->revcode;
                $row['restcode'] = $h->restcode ?? $n->restcode;
                $row['svalue']     = ($h->svalue ?? 0) + ($n->svalue ?? 0);
                $row['amount']     = ($h->amount ?? 0) + ($n->amount ?? 0);
                $row['baseamount'] = ($h->baseamount ?? 0) + ($n->baseamount ?? 0);

                $finalData[] = $row;
            }

            // return $finalData;

            $n = 1;
            $banqparameter = EnviroBanquet::where('propertyid', $this->propertyid)->first();

            foreach ($finalData as $row) {
                if ($row['amount'] <= 0) {
                    continue;
                }

                $sundrytypev = Sundrytype::where('propertyid', $this->propertyid)
                    ->where('vtype', "BANQ$this->propertyid")
                    ->where('sundry_code', $row['suncode'])
                    ->where('sno', $row['sno'])
                    ->first();

                if (!$sundrytypev || in_array($sundrytypev->nature, ['Amount'])) {
                    continue;
                }

                if ($sundrytypev->nature == 'Discount') {
                    $amtdr = $row['amount'];
                    $amtcr = 0;
                } elseif ($sundrytypev->nature == 'Net Amount') {
                    if (!$banqparameter) {
                        continue; // Skip if config missing
                    }

                    $subgroupp = SubGroup::where('propertyid', $this->propertyid)
                        ->where('sub_code', $banqparameter->indoorpartyac)
                        ->first();

                    if (!$subgroupp) {
                        continue;
                    }

                    $ldata1 = [
                        'propertyid'   => $this->propertyid,
                        'docid'        => $docid,
                        'vsno'         => $n++,
                        'vno'          => $start_srl_no,
                        'vdate'        => $request->booking_date,
                        'vtype'        => $vtype,
                        'vprefix'      => $vprefix,
                        'narration'    => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                        'contrasub'    => '',
                        'subcode'      => $subgroupp->sub_code,
                        'amtcr'        => 0.00,
                        'amtdr'        => $row['amount'],
                        'chqno'        => 0,
                        'chqdate'      => $request->booking_date,
                        'clgdate'      => $request->booking_date,
                        'groupcode'    => $subgroupp->group_code,
                        'groupnature'  => $subgroupp->nature,
                        'u_name'       => Auth::user()->name,
                        'u_entdt'      => $this->currenttime,
                        'u_ae'         => 'a',
                    ];
                    Ledger::insert($ldata1);
                    continue; // Skip to next after Net Amount entry
                } else {
                    $amtdr = 0;
                    $amtcr = $row['amount'];
                }

                $revmastt = Revmast::where('propertyid', $this->propertyid)
                    ->where('rev_code', $row['revcode'])
                    ->first();

                if (!$revmastt) {
                    continue;
                }

                $subgroup = SubGroup::where('propertyid', $this->propertyid)
                    ->where('sub_code', $revmastt->ac_code)
                    ->first();

                if (!$subgroup) {
                    continue;
                }

                $ldata = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid,
                    'vsno'         => $n++,
                    'vno'          => $start_srl_no,
                    'vdate'        => $request->booking_date,
                    'vtype'        => $vtype,
                    'vprefix'      => $vprefix,
                    'narration'    => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                    'contrasub'    => '',
                    'subcode'      => $subgroup->sub_code,
                    'amtcr'        => $amtcr,
                    'amtdr'        => $amtdr,
                    'chqno'        => 0,
                    'chqdate'      => $request->booking_date,
                    'clgdate'      => $request->booking_date,
                    'groupcode'    => $subgroup->group_code,
                    'groupnature'  => $subgroup->nature,
                    'u_name'       => Auth::user()->name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                ];
                Ledger::insert($ldata);
            }


            // return 'sagar';

            $netledger = SuntranH::select(
                'suntranh.dispname',
                DB::raw('SUM(suntranh.amount) AS RevAmt'),
                DB::raw('MAX(suntranh.suncode) AS SundryCode'),
                'subgroup.sub_code AS subcode',
                'subgroup.name AS subname',
                'subgroup.group_code AS accode',
                'subgroup.nature AS subnature'
            )
                ->join('enviro_banquet', 'enviro_banquet.propertyid', '=', 'suntranh.propertyid')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'enviro_banquet.indoorsaleac')
                ->where('suntranh.suncode', '=', '3' . $this->propertyid)
                ->where('suntranh.docid', '=', $docid)
                ->where('suntranh.propertyid', '=', $this->propertyid)
                ->groupBy('suntranh.restcode')
                ->first();

            $amtcr2 = $netledger->RevAmt;
            $amtdr2 = 0.00;

            $lndata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vsno' => $n,
                'vno' => $start_srl_no,
                'vdate' => $request->booking_date,
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'narration' => 'Purchase Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                'contrasub' => '',
                'subcode' => $netledger->subcode,
                'amtcr' => $amtcr2,
                'amtdr' => $amtdr2,
                'chqno' => '',
                'chqdate' => $request->booking_date,
                'clgdate' => $request->booking_date,
                'groupcode' => $netledger->accode,
                'groupnature' => $netledger->subnature,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
            ];
            Ledger::insert($lndata);

            $hallsale = new HallSale1();
            $hallsale->propertyid = $this->propertyid;
            $hallsale->docId = $docid;
            $hallsale->vtype = $vtype;
            $hallsale->vno = $start_srl_no;
            $hallsale->vdate = $request->booking_date;
            $hallsale->vprefix = $vprefix;
            $hallsale->restcode = $rest;
            $hallsale->party = $request->partyname;
            $hallsale->total      = ($totalamt ?? 0) + ($totalamt2 ?? 0);
            $hallsale->discper    = ($discper ?? 0) + ($discper2 ?? 0);
            $hallsale->discamt    = ($discount ?? 0) + ($discount2 ?? 0);
            $hallsale->roundoff   = ($roundoff ?? 0) + ($roundoff2 ?? 0);
            $hallsale->nontaxable = ($totalnontaxable2 ?? 0);
            $hallsale->taxable    = ($totaltaxable ?? 0) + ($totaltaxable2 ?? 0);
            $hallsale->netamt     = ($netamt ?? 0) + ($netamt2 ?? 0);
            $hallsale->u_name = Auth::user()->name;
            $hallsale->u_entdt = now();
            $hallsale->u_updatedt = null;
            $hallsale->u_ae = 'a';
            $hallsale->noofpax = $request->totalpax;
            $hallsale->rateperpax = $request->paxrate;
            $hallsale->totalpercover = $request->totalpax * $request->paxrate;
            $hallsale->advance = $request->paidamt;
            $hallsale->rectno = 0;
            $hallsale->comp_code = $request->company_name ?? '';
            $hallsale->rectdate = null;
            $hallsale->bookdocid = $hallbook->docid;
            $hallsale->remark = $request->remark ?? '';
            $hallsale->narration = $request->particular ?? '';
            $hallsale->narration1 = '';
            $hallsale->cgst = ($cgst ?? 0) + ($cgst2 ?? 0);
            $hallsale->sgst = ($sgst ?? 0) + ($sgst2 ?? 0);

            $hallsale->save();

            if ($totalitem > 0) {
                $sale2Records = [];
                for ($i = 1; $i <= $totalitem; $i++) {
                    $itemqty     = floatval($request->input("qtyiss$i", 0));
                    $itemcamttmp = floatval($request->input("amount$i", 0));
                    $itemcode = $request->input('item' . $i);
                    $itemratetmp = $request->input('taxedrate' . $i);
                    $itemrate = floor($itemratetmp * 100) / 100;
                    $itemtruerate = $request->input('itemrate' . $i);
                    $itemcamt = floor($itemcamttmp * 100) / 100;
                    $taxratepos = $request->input('taxrate_sum' . $i);
                    $tax_rate = $request->input('taxamt' . $i);
                    $discamt = $discper != 0 ? ($itemqty * $itemrate * $discper / 100) : 0.00;
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $netamount = $itemcamt + $taxamt - $discamt;

                    $itemmast = DB::table('itemmast')
                        ->where('Property_ID', $this->propertyid)
                        ->where('RestCode', $rest)
                        ->where('Code', $request->input("item$i"))
                        ->first();

                    if (!$itemmast->RestCode) throw new \Exception("Missing RestCode for $itemcode");

                    $taxStruct = DB::table('itemcatmast')
                        ->where('propertyid', $this->propertyid)
                        ->where('Code', $itemmast->ItemCatCode)
                        ->where('RestCode', $rest)
                        ->value('TaxStru');

                    $taxes = DB::table('taxstru')
                        ->where('propertyid', $this->propertyid)
                        ->where('str_code', $taxStruct)
                        ->get();

                    // return $taxes;

                    // $i = 1;
                    foreach ($taxes as $taxRow) {
                        if (floatval($taxRow->rate) > 0) {
                            $baseVal = $itemqty * ($itemcamttmp / $itemqty);
                            if ($itemmast->DiscApp == 'Y') {
                                $baseVal = $itemcamttmp * (1 - $discper / 100);
                            }
                            $taxAmttmp = $baseVal * $taxRow->rate / 100;

                            $roundedtmp = floor($taxAmttmp * 100) / 100;
                            $taxAmt = str_replace(',', '', number_format($roundedtmp, 2));

                            $sale2Records[] = [
                                'propertyid'  => $this->propertyid,
                                'docid'       => $docid,
                                'sno'         => $i,
                                'sno1'        => $taxRow->sno,
                                'vno'         => $start_srl_no,
                                'vtype'       => $vtype,
                                'vdate'       => $this->ncurdate,
                                'vprefix'     => $vprefix,
                                'restcode'    => $rest,
                                'taxcode'     => $taxRow->tax_code,
                                'basevalue'   => $baseVal,
                                'taxper'      => $taxRow->rate,
                                'taxamt'      => $taxAmt,
                                'u_entdt'     => $this->currenttime,
                                'u_name'      => Auth::user()->u_name,
                                'u_ae'        => 'a',
                            ];
                        }
                    }

                    $lastSno = DB::table('hallstock')
                        ->where('propertyid', $this->propertyid)
                        ->where('docid', $docid)
                        ->max('sno');
                    $sno = $lastSno ? $lastSno + 1 : 1;

                    $stock = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'sno' => $sno,
                        'vno' => $start_srl_no,
                        'vtype' => $vtype,
                        'vdate' => $request->booking_date,
                        'vprefix' => $vprefix,
                        'restcode' => $rest,
                        'item' => $itemcode,
                        'qtyiss' => $itemqty,
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $itemtruerate,
                        'amount' => $itemcamt,
                        'taxper' => $tax_rate ?? 0,
                        'taxamt' => $taxamt,
                        'discper' => $discper,
                        'discamt' => $discamt,
                        'remarks' => $request->input('description' . $i) ?? '',
                        'total' => $netamount,
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    HallStock::insert($stock);
                }

                if ($sale2Records) {
                    HallSale2::insert($sale2Records);
                }
            }

            $itemledger = DB::table('hallstock')
                ->select(
                    DB::raw('SUM(hallstock.amount) as RevAmt'),
                    DB::raw('subgroup.sub_code'),
                    DB::raw('subgroup.name as subname'),
                    DB::raw('subgroup.nature'),
                    DB::raw('subgroup.group_code')
                )
                ->leftJoin('itemmast', function ($join) {
                    $join->on('itemmast.Code', '=', 'hallstock.item')
                        ->where('itemmast.RestCode', '=', "BANQ$this->propertyid");
                })
                ->leftJoin('itemcatmast', function ($join) {
                    $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                        ->where('itemcatmast.RestCode', '=', "BANQ$this->propertyid");
                })
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'itemcatmast.AcCode')
                ->where('hallstock.docid', $docid)
                ->where('hallstock.propertyid', $this->propertyid)
                ->groupBy('itemcatmast.AcCode')
                ->get();

            // return $itemledger;

            $n = $n + 1;
            foreach ($itemledger as $row) {
                if ($row->RevAmt > 0) {
                    $lidata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $start_srl_no,
                        'vdate' => $request->booking_date,
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                        'contrasub' => '',
                        'subcode' => $row->sub_code,
                        'amtcr' => $row->RevAmt,
                        'amtdr' => 0.00,
                        'chqno' => 0,
                        'chqdate' => $request->booking_date,
                        'clgdate' => $request->booking_date,
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
            // return 'Billing Submitted Successfully';
            return back()->with('success', 'Billing Submitted Successfully');
        } catch (Exception $e) {
            // return $e->getMessage() . ' On Line: ' . $e->getLine();
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function deletebanquetbill(Request $request)
    {
        try {
            $docid = $request->input('docid');
            HallSale1::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            HallSale2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            HallStock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            Suntran::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            SuntranH::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bill Deleted Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'unable to delete bill ' . $e->getMessage()
            ]);
        }
    }

    public function banquetbillingupdate(Request $request)
    {

        try {
            DB::beginTransaction();
            $totalitem = $request->totalitem;
            $docid = $request->oldhalldocid;
            $hallsale1 = HallSale1::where('propertyid', $this->propertyid)->where('docId', $request->oldhalldocid)->first();

            if (!$hallsale1) {
                return back()->with('error', 'Unable to find Hall ID');
            }

            $start_srl_no = $hallsale1->vno;
            $vprefix = $hallsale1->vprefix;
            $vtype = $hallsale1->vtype;

            $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $request->bookingdocid)->first();

            if (!$hallbook) {
                return back()->with('error', 'Hallbook Docid Not Found');
            }

            $rest = $hallsale1->restcode;

            $vat      = floatval($request->input($rest . 'vatamount', 0));
            $cgst     = floatval($request->input($rest . 'cgstamount', 0));
            $cgstrate = floatval($request->input($rest . 'cgstrate', 0));
            $sgst     = floatval($request->input($rest . 'sgstamount', 0));
            $sgstrate = floatval($request->input($rest . 'sgstrate', 0));
            $totaltaxable = floatval($request->input($rest . 'totaltaxable', 0));
            $totalnontaxable = floatval($request->input($rest . 'totalnontaxable', 0));
            $service  = floatval($request->input($rest . 'serviceamount', 0));
            $discper = floatval($request->input($rest . 'discountfix', 0));
            $discount = floatval($request->input($rest . 'discountsundry', 0));
            $roundoff = floatval($request->input($rest . 'roundoffamount', 0));
            $netamt   = floatval($request->input($rest . 'netamount', 0));
            $totalamt = floatval($request->input($rest . 'totalamountoutlet', 0));
            $sundryCount = intval($request->input($rest . 'sundrycount', 0));

            Suntran::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            if ($totalitem > 0) {
                $vat2      = floatval($request->input('s' . $rest . 'vatamount', 0));
                $cgst2     = floatval($request->input('s' . $rest . 'cgstamount', 0));
                $cgstrate2 = floatval($request->input('s' . $rest . 'cgstrate', 0));
                $sgst2     = floatval($request->input('s' . $rest . 'sgstamount', 0));
                $sgstrate2 = floatval($request->input('s' . $rest . 'sgstrate', 0));
                $totaltaxable2 = floatval($request->input('s' . $rest . 'totaltaxable', 0));
                $totalnontaxable2 = floatval($request->input('s' . $rest . 'totalnontaxable', 0));
                $service2  = floatval($request->input('s' . $rest . 'serviceamount', 0));
                $discper2 = floatval($request->input('s' . $rest . 'discountfix', 0));
                $discount2 = floatval($request->input('s' . $rest . 'discountsundry', 0));
                $roundoff2 = floatval($request->input('s' . $rest . 'roundoffamount', 0));
                $netamt2  = floatval($request->input('s' . $rest . 'netamount', 0));
                $totalamt2 = floatval($request->input('s' . $rest . 'totalamountoutlet', 0));
                $sundryCount2 = intval($request->input('s' . $rest . 'sundrycount', 0));



                for ($s = 1; $s <= $sundryCount2; $s++) {
                    $st = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', $rest)->where('sno', $s)->first();
                    if (!$st) continue;

                    $amt = 0;
                    $base = 0;
                    $svalue = 0;
                    if ($st->disp_name == 'Discount') {
                        $amt = $discount2;
                        $svalue = $discper2;
                    } elseif ($st->disp_name == 'Service Charge') {
                        $amt = $service2;
                    } elseif ($st->disp_name == 'Amount') {
                        $amt = $totalamt2;
                    } elseif ($st->disp_name == 'CGST') {
                        $amt = $cgst2;
                        $svalue = $cgstrate2;
                    } elseif ($st->disp_name == 'SGST') {
                        $amt = $sgst2;
                        $svalue = $sgstrate2;
                    } elseif ($st->disp_name == 'VAT') {
                        $amt = $vat2;
                    } elseif ($st->disp_name == 'Round Off') {
                        $amt = $roundoff2;
                        $base = $netamt2 + $roundoff2;
                    } elseif ($st->disp_name == 'Net Amount') {
                        $amt = $netamt2;
                    }

                    $suntrandata1 = [
                        'propertyid' => $this->propertyid,
                        'docid'       => $docid,
                        'sno'         => $s,
                        'vno'         => $start_srl_no,
                        'vtype'       => $vtype,
                        'vdate'       => $request->booking_date,
                        'dispname'    => $st->disp_name,
                        'suncode'     => $st->sundry_code,
                        'calcformula' => $st->calcformula,
                        'svalue'      => $svalue,
                        'amount'      => $amt,
                        'baseamount'  => $base,
                        'revcode'     => $st->revcode,
                        'restcode'    => $rest,
                        'sunappdate'  => $request->booking_date,
                        'delflag'     => 'N',
                        'u_entdt'     => $this->currenttime,
                        'u_name'      => Auth::user()->u_name,
                        'u_ae'        => 'a',
                    ];

                    Suntran::insert($suntrandata1);
                }
            }

            // return $discount;

            SuntranH::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            for ($s = 1; $s <= $sundryCount; $s++) {
                $st = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', $rest)->where('sno', $s)->first();
                if (!$st) continue;

                $amt = 0;
                $base = 0;
                $svalue = 0;
                if ($st->disp_name == 'Discount') {
                    $amt = $discount;
                    $svalue = $discper;
                } elseif ($st->disp_name == 'Service Charge') {
                    $amt = $service;
                } elseif ($st->disp_name == 'Amount') {
                    $amt = $totalamt;
                } elseif ($st->disp_name == 'CGST') {
                    $amt = $cgst;
                    $svalue = $cgstrate;
                } elseif ($st->disp_name == 'SGST') {
                    $amt = $sgst;
                    $svalue = $sgstrate;
                } elseif ($st->disp_name == 'VAT') {
                    $amt = $vat;
                } elseif ($st->disp_name == 'Round Off') {
                    $amt = $roundoff;
                    $base = $netamt + $roundoff;
                } elseif ($st->disp_name == 'Net Amount') {
                    $amt = $netamt;
                }

                $suntrandata = [
                    'propertyid' => $this->propertyid,
                    'docid'       => $docid,
                    'sno'         => $s,
                    'vno'         => $start_srl_no,
                    'vtype'       => $vtype,
                    'vdate'       => $request->booking_date,
                    'dispname'    => $st->disp_name,
                    'suncode'     => $st->sundry_code,
                    'calcformula' => $st->calcformula,
                    'svalue'      => $svalue,
                    'amount'      => $amt,
                    'baseamount'  => $base,
                    'revcode'     => $st->revcode,
                    'restcode'    => $rest,
                    'sunappdate'  => $request->booking_date,
                    'delflag'     => 'N',
                    'u_entdt'     => $this->currenttime,
                    'u_name'      => Auth::user()->u_name,
                    'u_ae'        => 'a',
                ];

                SuntranH::insert($suntrandata);
            }

            Ledger::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            $suntranh = SuntranH::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->get()
                ->keyBy('sno');

            $suntran = Suntran::where('propertyid', $this->propertyid)
                ->where('docid', $docid)
                ->get()
                ->keyBy('sno');

            $allSnos = $suntranh->keys()->merge($suntran->keys())->unique();

            $finalData = [];

            foreach ($allSnos as $sno) {
                $h = $suntranh->get($sno);
                $n = $suntran->get($sno);

                $row = [];

                $row['dispname'] = $h->dispname ?? $n->dispname;
                $row['suncode']  = $h->suncode ?? $n->suncode;
                $row['sunappdate']  = $h->sunappdate ?? $n->sunappdate;
                $row['sno']  = $h->sno ?? $n->sno;
                $row['revcode']  = $h->revcode ?? $n->revcode;
                $row['restcode'] = $h->restcode ?? $n->restcode;
                $row['svalue']     = ($h->svalue ?? 0) + ($n->svalue ?? 0);
                $row['amount']     = ($h->amount ?? 0) + ($n->amount ?? 0);
                $row['baseamount'] = ($h->baseamount ?? 0) + ($n->baseamount ?? 0);

                $finalData[] = $row;
            }

            // return $finalData;

            $n = 1;
            $banqparameter = EnviroBanquet::where('propertyid', $this->propertyid)->first();

            foreach ($finalData as $row) {
                if ($row['amount'] <= 0) {
                    continue;
                }

                $sundrytypev = Sundrytype::where('propertyid', $this->propertyid)
                    ->where('vtype', "BANQ$this->propertyid")
                    ->where('sundry_code', $row['suncode'])
                    ->where('sno', $row['sno'])
                    ->first();

                if (!$sundrytypev || in_array($sundrytypev->nature, ['Amount'])) {
                    continue;
                }

                if ($sundrytypev->nature == 'Discount') {
                    $amtdr = $row['amount'];
                    $amtcr = 0;
                } elseif ($sundrytypev->nature == 'Net Amount') {
                    if (!$banqparameter) {
                        continue; // Skip if config missing
                    }

                    $subgroupp = SubGroup::where('propertyid', $this->propertyid)
                        ->where('sub_code', $banqparameter->indoorpartyac)
                        ->first();

                    if (!$subgroupp) {
                        continue;
                    }

                    $ldata1 = [
                        'propertyid'   => $this->propertyid,
                        'docid'        => $docid,
                        'vsno'         => $n++,
                        'vno'          => $start_srl_no,
                        'vdate'        => $request->booking_date,
                        'vtype'        => $vtype,
                        'vprefix'      => $vprefix,
                        'narration'    => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                        'contrasub'    => '',
                        'subcode'      => $subgroupp->sub_code,
                        'amtcr'        => 0.00,
                        'amtdr'        => $row['amount'],
                        'chqno'        => 0,
                        'chqdate'      => $request->booking_date,
                        'clgdate'      => $request->booking_date,
                        'groupcode'    => $subgroupp->group_code,
                        'groupnature'  => $subgroupp->nature,
                        'u_name'       => Auth::user()->name,
                        'u_entdt'      => $this->currenttime,
                        'u_ae'         => 'a',
                    ];
                    Ledger::insert($ldata1);
                    continue; // Skip to next after Net Amount entry
                } else {
                    $amtdr = 0;
                    $amtcr = $row['amount'];
                }

                $revmastt = Revmast::where('propertyid', $this->propertyid)
                    ->where('rev_code', $row['revcode'])
                    ->first();

                if (!$revmastt) {
                    continue;
                }

                $subgroup = SubGroup::where('propertyid', $this->propertyid)
                    ->where('sub_code', $revmastt->ac_code)
                    ->first();

                if (!$subgroup) {
                    continue;
                }

                $ldata = [
                    'propertyid'   => $this->propertyid,
                    'docid'        => $docid,
                    'vsno'         => $n++,
                    'vno'          => $start_srl_no,
                    'vdate'        => $request->booking_date,
                    'vtype'        => $vtype,
                    'vprefix'      => $vprefix,
                    'narration'    => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                    'contrasub'    => '',
                    'subcode'      => $subgroup->sub_code,
                    'amtcr'        => $amtcr,
                    'amtdr'        => $amtdr,
                    'chqno'        => 0,
                    'chqdate'      => $request->booking_date,
                    'clgdate'      => $request->booking_date,
                    'groupcode'    => $subgroup->group_code,
                    'groupnature'  => $subgroup->nature,
                    'u_name'       => Auth::user()->name,
                    'u_entdt'      => $this->currenttime,
                    'u_ae'         => 'a',
                ];
                Ledger::insert($ldata);
            }


            // return 'sagar';

            $netledger = SuntranH::select(
                'suntranh.dispname',
                DB::raw('SUM(suntranh.amount) AS RevAmt'),
                DB::raw('MAX(suntranh.suncode) AS SundryCode'),
                'subgroup.sub_code AS subcode',
                'subgroup.name AS subname',
                'subgroup.group_code AS accode',
                'subgroup.nature AS subnature'
            )
                ->join('enviro_banquet', 'enviro_banquet.propertyid', '=', 'suntranh.propertyid')
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'enviro_banquet.indoorsaleac')
                ->where('suntranh.suncode', '=', '3' . $this->propertyid)
                ->where('suntranh.docid', '=', $docid)
                ->where('suntranh.propertyid', '=', $this->propertyid)
                ->groupBy('suntranh.restcode')
                ->first();

            $amtcr2 = $netledger->RevAmt;
            $amtdr2 = 0.00;

            $lndata = [
                'propertyid' => $this->propertyid,
                'docid' => $docid,
                'vsno' => $n,
                'vno' => $start_srl_no,
                'vdate' => $request->booking_date,
                'vtype' => $vtype,
                'vprefix' => $vprefix,
                'narration' => 'Purchase Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                'contrasub' => '',
                'subcode' => $netledger->subcode,
                'amtcr' => $amtcr2,
                'amtdr' => $amtdr2,
                'chqno' => '',
                'chqdate' => $request->booking_date,
                'clgdate' => $request->booking_date,
                'groupcode' => $netledger->accode,
                'groupnature' => $netledger->subnature,
                'u_name' => Auth::user()->name,
                'u_entdt' => $this->currenttime,
                'u_ae' => 'a',
            ];
            Ledger::insert($lndata);

            $hallsale1up = [
                'party' => $request->partyname,
                'total'      => ($totalamt ?? 0) + ($totalamt2 ?? 0),
                'discper'    => ($discper ?? 0) + ($discper2 ?? 0),
                'discamt'    => ($discount ?? 0) + ($discount2 ?? 0),
                'roundoff'   => ($roundoff ?? 0) + ($roundoff2 ?? 0),
                'nontaxable' => ($totalnontaxable2 ?? 0),
                'taxable'    => ($totaltaxable ?? 0) + ($totaltaxable2 ?? 0),
                'netamt'     => ($netamt ?? 0) + ($netamt2 ?? 0),
                'u_name' => Auth::user()->name,
                'u_updatedt' => now(),
                'u_ae' => 'e',
                'noofpax' => $request->totalpax,
                'rateperpax' => $request->paxrate,
                'totalpercover' => $request->totalpax * $request->paxrate,
                'advance' => $request->paidamt,
                'comp_code' => $request->company_name ?? '',
                'rectno' => 0,
                'rectdate' => null,
                'bookdocid' => $hallbook->docid,
                'remark' => $request->remark ?? '',
                'narration' => $request->particular ?? '',
                'narration1' => '',
                'cgst' => ($cgst ?? 0) + ($cgst2 ?? 0),
                'sgst' => ($sgst ?? 0) + ($sgst2 ?? 0),
            ];

            HallSale1::where('propertyid', $this->propertyid)->where('docid', $request->oldhalldocid)->update($hallsale1up);


            HallSale2::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();
            HallStock::where('propertyid', $this->propertyid)->where('docid', $docid)->delete();

            if ($totalitem > 0) {
                $sale2Records = [];
                for ($i = 1; $i <= $totalitem; $i++) {
                    $itemqty     = floatval($request->input("qtyiss$i", 0));
                    $itemcamttmp = floatval($request->input("amount$i", 0));
                    $itemcode = $request->input('item' . $i);
                    $itemratetmp = $request->input('taxedrate' . $i);
                    $itemrate = floor($itemratetmp * 100) / 100;
                    $itemtruerate = $request->input('itemrate' . $i);
                    $itemcamt = floor($itemcamttmp * 100) / 100;
                    $taxratepos = $request->input('taxrate_sum' . $i);
                    $tax_rate = $request->input('taxamt' . $i);
                    $discamt = $discper != 0 ? ($itemqty * $itemrate * $discper / 100) : 0.00;
                    $taxamt = ($itemcamt * $taxratepos) / 100;
                    $netamount = $itemcamt + $taxamt - $discamt;

                    $itemmast = DB::table('itemmast')
                        ->where('Property_ID', $this->propertyid)
                        ->where('RestCode', $rest)
                        ->where('Code', $request->input("item$i"))
                        ->first();

                    if (!$itemmast->RestCode) throw new \Exception("Missing RestCode for $itemcode");

                    $taxStruct = DB::table('itemcatmast')
                        ->where('propertyid', $this->propertyid)
                        ->where('Code', $itemmast->ItemCatCode)
                        ->where('RestCode', $rest)
                        ->value('TaxStru');

                    $taxes = DB::table('taxstru')
                        ->where('propertyid', $this->propertyid)
                        ->where('str_code', $taxStruct)
                        ->get();

                    // return $taxes;

                    // $i = 1;
                    foreach ($taxes as $taxRow) {
                        if (floatval($taxRow->rate) > 0) {
                            $baseVal = $itemqty * ($itemcamttmp / $itemqty);
                            if ($itemmast->DiscApp == 'Y') {
                                $baseVal = $itemcamttmp * (1 - $discper / 100);
                            }
                            $taxAmttmp = $baseVal * $taxRow->rate / 100;

                            $roundedtmp = floor($taxAmttmp * 100) / 100;

                            $taxAmt = str_replace(',', '', number_format($roundedtmp, 2));

                            $sale2Records[] = [
                                'propertyid'  => $this->propertyid,
                                'docid'       => $docid,
                                'sno'         => $i,
                                'sno1'        => $taxRow->sno,
                                'vno'         => $start_srl_no,
                                'vtype'       => $vtype,
                                'vdate'       => $this->ncurdate,
                                'vprefix'     => $vprefix,
                                'restcode'    => $rest,
                                'taxcode'     => $taxRow->tax_code,
                                'basevalue'   => $baseVal,
                                'taxper'      => $taxRow->rate,
                                'taxamt'      => $taxAmt,
                                'u_entdt'     => $this->currenttime,
                                'u_name'      => Auth::user()->u_name,
                                'u_ae'        => 'a',
                            ];
                        }
                    }

                    $lastSno = DB::table('hallstock')
                        ->where('propertyid', $this->propertyid)
                        ->where('docid', $docid)
                        ->max('sno');
                    $sno = $lastSno ? $lastSno + 1 : 1;

                    $stock = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'sno' => $sno,
                        'vno' => $start_srl_no,
                        'vtype' => $vtype,
                        'vdate' => $this->ncurdate,
                        'vprefix' => $vprefix,
                        'restcode' => $rest,
                        'item' => $itemcode,
                        'qtyiss' => $itemqty,
                        'unit' => $itemmast->Unit ?? '',
                        'rate' => $itemtruerate,
                        'amount' => $itemcamt,
                        'taxper' => $tax_rate ?? 0,
                        'taxamt' => $taxamt,
                        'discper' => $discper,
                        'discamt' => $discamt,
                        'remarks' => $request->input('description' . $i) ?? '',
                        'total' => $netamount,
                        'u_entdt' => $this->currenttime,
                        'u_name' => Auth::user()->u_name,
                        'u_ae' => 'a',
                    ];

                    HallStock::insert($stock);
                }

                if ($sale2Records) {
                    HallSale2::insert($sale2Records);
                }
            }

            $itemledger = DB::table('hallstock')
                ->select(
                    DB::raw('SUM(hallstock.amount) as RevAmt'),
                    DB::raw('subgroup.sub_code'),
                    DB::raw('subgroup.name as subname'),
                    DB::raw('subgroup.nature'),
                    DB::raw('subgroup.group_code')
                )
                ->leftJoin('itemmast', function ($join) {
                    $join->on('itemmast.Code', '=', 'hallstock.item')
                        ->where('itemmast.RestCode', '=', "BANQ$this->propertyid");
                })
                ->leftJoin('itemcatmast', function ($join) {
                    $join->on('itemcatmast.Code', '=', 'itemmast.ItemCatCode')
                        ->where('itemcatmast.RestCode', '=', "BANQ$this->propertyid");
                })
                ->leftJoin('subgroup', 'subgroup.sub_code', '=', 'itemcatmast.AcCode')
                ->where('hallstock.docid', $docid)
                ->where('hallstock.propertyid', $this->propertyid)
                ->groupBy('itemcatmast.AcCode')
                ->get();

            // return $itemledger;

            $n = $n + 1;
            foreach ($itemledger as $row) {
                if ($row->RevAmt > 0) {
                    $lidata = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $n,
                        'vno' => $start_srl_no,
                        'vdate' => $request->booking_date,
                        'vtype' => $vtype,
                        'vprefix' => $vprefix,
                        'narration' => 'Banquet Bill: ' . $start_srl_no . ' ' . date('d-m-Y', strtotime($request->booking_date)),
                        'contrasub' => '',
                        'subcode' => $row->sub_code,
                        'amtcr' => $row->RevAmt,
                        'amtdr' => 0.00,
                        'chqno' => 0,
                        'chqdate' => $request->booking_date,
                        'clgdate' => $request->booking_date,
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

            return back()->with('success', 'Billing Updated Successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage() . ' On Line: ' . $e->getLine());
        }
    }

    public function banquetbillprint(Request $request, $docid)
    {

        $hallsale1 = HallSale1::select(
            'hallsale1.*',
            'functiontype.name as functionname',
            'hallbook.panno',
            'hallbook.add1',
            'hallbook.add2',
            'cities.cityname',
        )
            ->leftJoin('hallbook', 'hallbook.docid', '=', 'hallsale1.bookdocid')
            ->leftJoin('functiontype', 'functiontype.code', '=', 'hallbook.func_name')
            ->leftJoin('cities', 'cities.city_code', '=', 'hallbook.city')
            ->where('hallsale1.propertyid', $this->propertyid)->where('hallsale1.docId', $docid)->first();

        if (!$hallsale1) {
            return back()->with('error', 'Unable to find Hall ID');
        }

        $venueocc = VenueOcc::select(
            'venueocc.*',
            'venuemast.name as venuename'
        )
            ->leftJoin('venuemast', 'venuemast.code', '=', 'venueocc.venucode')
            ->where('venueocc.propertyid', $this->propertyid)->where('venueocc.fpdocid', $hallsale1->bookdocid)->orderBy('venueocc.sno')->get();

        $paidrows = PaychargeH::where('propertyid', $this->propertyid)->where('docid', $docid)->whereNot('amtcr', 0.00)->get();

        $advancerows = PaychargeH::where('propertyid', $this->propertyid)->where('contradocid', $hallsale1->bookdocid)->where('sno', '1')->whereNot('amtcr', 0.00)->get();
        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $hallsale1->bookdocid)->first();

        $docId = $hallsale1->docId;
        $propertyId = $this->propertyid;
        $suntranh = SuntranH::where('propertyid', $propertyId)
            ->where('docid', $docId)
            ->get()
            ->keyBy('sno');

        $suntran = Suntran::where('propertyid', $propertyId)
            ->where('docid', $docId)
            ->get()
            ->keyBy('sno');

        $allSnos = $suntranh->keys()->merge($suntran->keys())->unique();

        $finalData = [];

        foreach ($allSnos as $sno) {
            $h = $suntranh->get($sno);
            $n = $suntran->get($sno);

            $row = [];

            $row['dispname'] = $h->dispname ?? $n->dispname;
            $row['suncode']  = $h->suncode ?? $n->suncode;
            $row['revcode']  = $h->revcode ?? $n->revcode;
            $row['restcode'] = $h->restcode ?? $n->restcode;
            $row['svalue']     = ($h->svalue ?? 0) + ($n->svalue ?? 0);
            $row['amount']     = ($h->amount ?? 0) + ($n->amount ?? 0);
            $row['baseamount'] = ($h->baseamount ?? 0) + ($n->baseamount ?? 0);

            $finalData[] = $row;
        }


        $stockitems = HallStock::select('hallstock.*', 'itemmast.Name')
            ->leftJoin('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'hallstock.item')
                    ->where('itemmast.RestCode', "BANQ$this->propertyid");
            })
            ->where('hallstock.propertyid', $this->propertyid)->where('hallstock.docid', $docid)->orderBy('hallstock.sno')->get();

        $hallsale2 = HallSale2::select('hallsale2.*', 'revmast.name')
            ->leftJoin('revmast', function ($join) {
                $join->on('revmast.rev_code', '=', 'hallsale2.taxcode')
                    ->where('revmast.propertyid', $this->propertyid);
            })
            ->where('hallsale2.propertyid', $this->propertyid)
            ->where('hallsale2.docid', $docid)
            ->groupBy('hallsale2.taxper', 'hallsale2.sno', 'hallsale2.sno1')
            ->get();

        $sundrytype = Sundrytype::where('propertyid', $this->propertyid)->where('vtype', "BANQ$this->propertyid")->whereIn('nature', ['CGST', 'SGST'])->get();

        return view('property.banquetbillprint', [
            'hallsale1' => $hallsale1,
            'venueocc' => $venueocc,
            'paidrows' => $paidrows,
            'advancerows' => $advancerows,
            'hallbook' => $hallbook,
            'finalData' => $finalData,
            'stockitems' => $stockitems,
            'hallsale2' => $hallsale2,
            'sundrytype' => $sundrytype
        ]);
    }

    public function hallbillsettle(Request $request, $docid)
    {
        $vno = $request->query('vno');

        $hallsale1 = HallSale1::where('propertyid', $this->propertyid)->where('docId', $docid)->first();

        if (!$hallsale1) {
            return back()->with('error', 'Unable to find Hall ID');
        }

        $start_srl_no = $hallsale1->vno;
        $vprefix = $hallsale1->vprefix;
        $vtype = $hallsale1->vtype;

        $hallbook = HallBook::where('propertyid', $this->propertyid)->where('docid', $hallsale1->bookdocid)->first();

        if (!$hallbook) {
            return back()->with('error', 'Hallbook Docid Not Found');
        }

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

        $paidamt = PaychargeH::where('propertyid', $this->propertyid)->where('docid', $docid)->whereNot('amtcr', 0.00)->sum('amtcr');
        $balance = $hallsale1->netamt - $paidamt;

        $paidrows = PaychargeH::where('propertyid', $this->propertyid)->where('docid', $docid)->whereNot('amtcr', 0.00)->get();

        return view('property.banquetbillsettle', [
            'vno' => $vno,
            'company' => $company,
            'paidamt' => $paidamt,
            'balance' => $balance,
            'revdata' => $records,
            'paidrows' => $paidrows,
            'hallsale1' => $hallsale1
        ]);
    }

    public function fetchadvamtpayhall(Request $request)
    {
        $docid = $request->input('docid');

        $paydata = DB::table('paycharge')->where('propertyid', $this->propertyid)->where('folionodocid', $docid)->get();
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

    public function banquetbillsubmit(Request $request)
    {

        $hallsale1docid = $request->input('hallsale1docid');
        $rowcount = $request->input('rowcount') + 1;
        // return $rowcount;

        for ($i = 1; $i <= $rowcount; $i++) {
            $chargetype[] = $request->input('chargetype' . $i);
        }

        $string = ['ROOM SETTLEMENT', 'Room'];

        $hallsale1 = HallSale1::where('propertyid', $this->propertyid)->where('docid', $hallsale1docid)->first();

        // return $hallsale1;

        $depart = Depart::where('propertyid', $this->propertyid)->where('dcode', $hallsale1->restcode)->first();
        $paycode1 = 'ROOM' . $this->propertyid;
        $netamount = $request->input('netamount');
        $revdata1 = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $paycode1)->first();
        $roomno = $request->input('roomno') ?? '';

        $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
            ->where('v_type', $hallsale1->vtype)
            ->whereDate('date_from', '<=', $this->ncurdate)
            ->whereDate('date_to', '>=', $this->ncurdate)
            ->first();

        $vprefix = $chkvpf->prefix;

        PaychargeH::where('propertyid', $this->propertyid)->where('docid', $hallsale1->docId)->delete();


        if (array_intersect($string, $chargetype)) {
            $roommast = RoomMast::where('propertyid', $this->propertyid)->where('rcode', $roomno)->first();
            $paycode2 = 'TOUT' . $this->propertyid;
            $paycharge2 = [
                'propertyid' => $this->propertyid,
                'docid' => $hallsale1->docId,
                'vno' => $hallsale1->vno,
                'vtype' => $hallsale1->vtype,
                'sno' => 100,
                'vdate' => $hallsale1->vdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'paycode' => $paycode2,
                'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $hallsale1->vno,
                'paytype' => $revdata1->pay_type,
                'contradocid' => '',
                'restcode' => $hallsale1->restcode,
                'roomno' => $roomno,
                'roomcat' => $roommast->room_cat ?? '',
                'amtdr' => $netamount,
                'billamount' => $netamount,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a',
            ];
            PaychargeH::insert($paycharge2);
        }

        $snos = 1;
        for ($i = 1; $i <= $rowcount; $i++) {

            $paycodes = Revmast::where('propertyid', $this->propertyid)->where('rev_code', $request->input('chargecode' . $i))->first();

            $insertdata = [
                'propertyid' => $this->propertyid,
                'docid' => $hallsale1->docId,
                'vno' => $hallsale1->vno,
                'vtype' => $hallsale1->vtype,
                'sno' => $snos,
                'chqno' => $request->input('checkno') ? $request->input('checkno') : $request->input('referencenoupi'),
                'cardno' => $request->input('crnumber'),
                'cardholder' => $request->input('holdername'),
                'expdate' => $request->input('expdatecr'),
                'vdate' => $this->ncurdate,
                'vtime' => date('H:i:s'),
                'vprefix' => $vprefix,
                'comp_code' => $request->input('compcode' . $i) ?? '',
                'paycode' => $request->input('chargecode' . $i),
                'paytype' => $paycodes->pay_type ?? '',
                'comments' => $request->input('chargenarration' . $i),
                'roomno' => $roomno,
                'amtcr' => $request->input('amtrow' . $i),
                'roomcat' => $result->roomcat ?? '',
                'restcode' => $hallsale1->restcode,
                'billamount' => $netamount,
                'taxper' => 0,
                'onamt' => 0.00,
                'u_entdt' => $this->currenttime,
                'u_name' => Auth::user()->u_name,
                'u_ae' => 'a'
            ];

            PaychargeH::insert($insertdata);
            $snos++;
        }
        return redirect('autorefreshmain');
    }

    public function banqsettlefetch(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = $request->todate;

        $users = User::where('propertyid', $this->propertyid)->get();
        $revheading = Revmast::where('propertyid', $this->propertyid)->where('field_type', 'P')->get();
        $paychargeh = DB::table('paychargeh as P')
            ->select([
                'P.docid',
                DB::raw('P.vdate AS BillDate'),
                'P.vtype',
                'P.vno',
                'H.partyname as PartyName',
                'P.vdate',
                'P.paytype',
                'P.billamount',
                'P.amtcr as Amount',
                'P.comments as Narration',
                'P.u_name as Name',
                'P.vtype'
            ])
            ->leftJoin('hallbook as H', 'P.contradocid', '=', 'H.docid')
            ->where('P.restcode', 'BANQ' . $this->propertyid)
            ->where('P.propertyid', $this->propertyid)
            ->where('P.sno', 1)
            ->whereBetween('P.vdate', [$fromdate, $todate])
            ->groupBy('P.paytype', 'P.docid')
            ->orderBy('P.vdate')
            ->orderBy('P.vno')
            ->get();

        $data = [
            'revheading' => $revheading,
            'report' => $paychargeh
        ];

        return json_encode($data);
    }

    public function venueavailability(Request $request)
    {
        return view('property.banquetavailability');
    }

    public function venueavailabilitydaywise(Request $request)
    {
        $hallbook = HallBook::where('propertyid', $this->propertyid)
            ->groupBy('vprefix')
            ->get();
        return view('property.banquetavailabilitydaywise', [
            'hallbook' => $hallbook
        ]);
    }

    public function availablitybanquet(Request $request)
    {
        $fromdate = request('fromdate');
        $venuemast = VenueMast::where('propertyid', $this->propertyid)->orderBy('name')->get();

        $repdata = DB::table('venueocc')
            ->select(
                'venueocc.venucode',
                'venueocc.fromdate',
                'venueocc.dromtime as fromtime',
                'venueocc.todate',
                'venueocc.totime',
                'hallbook.partyname',
                'hallbook.expatt',
                'hallbook.guaratt',
                'hallbook.coverrate',
                DB::raw('COALESCE(SUM(paychargeh.amtcr), 0) as advancesum')
            )
            ->leftJoin('hallbook', 'hallbook.docid', '=', 'venueocc.fpdocid')
            ->leftJoin('paychargeh', function ($join) {
                $join->on('paychargeh.contradocid', '=', 'hallbook.docid');
            })
            ->where('venueocc.propertyid', $this->propertyid)
            ->where('venueocc.fromdate', $fromdate)
            ->groupBy(
                'venueocc.venucode',
                'venueocc.fromdate',
                'venueocc.dromtime',
                'venueocc.todate',
                'venueocc.totime',
                'hallbook.partyname',
                'hallbook.expatt',
                'hallbook.guaratt',
                'hallbook.coverrate'
            )
            ->get();

        $data = [
            'venuemast' => $venuemast,
            'repdata' => $repdata
        ];

        return json_encode($data);
    }

    public function availablitybanquetdaywise(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $venuemast = VenueMast::where('propertyid', $this->propertyid)
            ->orderBy('name')
            ->get();

        $repdata = DB::table('venueocc')
            ->select(
                'venueocc.venucode',
                'venueocc.fromdate',
                'venueocc.dromtime as fromtime',
                'venueocc.todate',
                'venueocc.totime',
                'hallbook.partyname',
                'hallbook.expatt',
                'hallbook.guaratt',
                'hallbook.coverrate',
                DB::raw('COALESCE(SUM(paychargeh.amtcr), 0) as advancesum')
            )
            ->leftJoin('hallbook', 'hallbook.docid', '=', 'venueocc.fpdocid')
            ->leftJoin('paychargeh', function ($join) {
                $join->on('paychargeh.contradocid', '=', 'hallbook.docid');
            })
            ->where('venueocc.propertyid', $this->propertyid)
            ->whereYear('venueocc.fromdate', $year)
            ->whereMonth('venueocc.fromdate', $month)
            ->groupBy(
                'venueocc.venucode',
                'venueocc.fromdate',
                'venueocc.dromtime',
                'venueocc.todate',
                'venueocc.totime',
                'hallbook.partyname',
                'hallbook.expatt',
                'hallbook.guaratt',
                'hallbook.coverrate'
            )
            ->get();

        return response()->json([
            'venuemast' => $venuemast,
            'repdata'   => $repdata
        ]);
    }

    public function banqenquieryfetch(Request $request)
    {
        $inqno = $request->inqno;

        $inquiry = BookingInquiry::where('propertyid', $this->propertyid)->where('contradocid', '')->where('inqno', $inqno)->first();

        if (!$inquiry) {
            return response()->json(['message' => 'Data Not Found', 'success' => false], 500);
        }

        $bookdetail = BookingDetail::where('propertyid', $this->propertyid)->where('inqno', $inquiry->inqno)->orderBY('sno')->get();

        return response()->json([
            'inquiry' => $inquiry,
            'bookdetail' => $bookdetail
        ]);
    }
}
