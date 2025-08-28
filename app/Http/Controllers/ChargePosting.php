<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\EnviroFom;
use App\Models\Ledger;
use App\Models\Paycharge;
use App\Models\Revmast;
use App\Models\Stock;
use App\Models\Suntran;
use App\Models\VoucherPrefix;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChargePosting extends Controller
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

    public function accountposting(Request $request)
    {
         $permission = revokeopen(191114);
        if (is_null($permission) || $permission->view == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }
        return view('property.accountposting', [
            'ncurdate' => $this->ncurdate
        ]);
    }

    public function accountpoststore(Request $request)
    {
         $permission = revokeopen(191114);
        if (is_null($permission) || $permission->ins == 0) { 
            return redirect()->back()->with('error', 'You have no permission to execute this functionality!');
        }

        try {

            DB::beginTransaction();
            $fromdate = $request->fromdate;
            $todate = $request->todate;

            $frmdatetmp = new DateTime($fromdate);
            $todatetmp = new DateTime($todate);
            $financialYearStart = (int) $frmdatetmp->format('Y');

            $fyStart = new DateTime("1 April $financialYearStart");
            $fyEnd = new DateTime("31 March " . ($financialYearStart + 1));

            if ($todatetmp > $fyEnd) {
                return back()->with('error', "To date exceeds the financial year limit (1 April $financialYearStart - 31 March " . ($financialYearStart + 1) . ").");
            }

            $checkdatec = VoucherPrefix::where('propertyid', $this->propertyid)
                ->whereDate('date_from', '<=', $fromdate)
                ->whereDate('date_to', '>=', $todate)
                ->first();

            if ($checkdatec === null || $checkdatec === '0') {
                return back()->with('error', 'You are not eligible to post charges for this date: ' . date('d-m-Y', strtotime($request->input('charge_date'))));
            }

            // Ledger Posting
            $ledgerdata = DB::table('paycharge')
                ->select(
                    'paycharge.paycode',
                    DB::raw('MAX(revmast.ac_posting) as ACPosting'),
                    DB::raw('MAX(paycharge.settledate) AS settledate'),
                    DB::raw('MAX(paycharge.vprefix) AS vprefix'),
                    'revmast.ac_code'
                )
                ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                ->where('paycharge.propertyid', $this->propertyid)
                ->whereBetween('paycharge.settledate', [$fromdate, $todate])
                ->whereIn('revmast.field_type', ['C', 'T'])
                ->whereRaw('(paycharge.amtdr - paycharge.amtcr) <> 0')
                ->where('paycharge.restcode', 'FOM' . $this->propertyid)
                ->groupBy('paycharge.paycode')
                ->get();

            $envirofom = EnviroFom::where('propertyid', $this->propertyid)->first();

            $l = 1;
            Ledger::where('propertyid', $this->propertyid)->where('vtype', 'HPOST')->whereBetween('vdate', [$fromdate, $todate])->delete();
            if ($ledgerdata->isNotEmpty()) {

                foreach ($ledgerdata as $row) {
                    $amountdata = DB::table('paycharge')
                        ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                        ->select([
                            DB::raw('SUM(paycharge.amtdr) - SUM(paycharge.amtcr) as DebitAmt'),
                            'paycharge.paycode',
                            DB::raw('MAX(revmast.ac_code) as ACCode'),
                            DB::raw('MAX(revmast.name) as RevenueName'),
                        ])
                        ->where('paycharge.propertyid', $this->propertyid)
                        ->where('paycharge.settledate', $row->settledate)
                        ->whereIn('revmast.field_type', ['C', 'T'])
                        ->whereRaw('(paycharge.amtdr - paycharge.amtcr) <> 0')
                        ->where('paycharge.restcode', 'FOM' . $this->propertyid)
                        ->where('paycharge.paycode', $row->paycode)
                        ->groupBy('paycharge.paycode')
                        ->first();

                    $billnos = DB::table('paycharge')
                        ->select('billno')
                        ->distinct()
                        ->where('paycharge.propertyid', $this->propertyid)
                        ->where('settledate', $row->settledate)
                        ->whereRaw('paycharge.amtdr - paycharge.amtcr <> 0')
                        ->where('paycharge.restcode', 'FOM' . $this->propertyid)
                        ->where('paycharge.paycode', $row->paycode)
                        ->pluck('billno')
                        ->implode(', ');

                    $vno = $row->vprefix . date('dm', strtotime($row->settledate));
                    $vtype = 'HPOST';
                    $docid = $this->propertyid . $vtype . '‎ ‎ ' . $row->vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                    $hpostdata1 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $l++,
                        'vno' => $vno,
                        'vdate' => $row->settledate,
                        'vtype' => $vtype,
                        'vprefix' => $row->vprefix,
                        'narration' => 'Against Guest Bill No(s) : ' . $billnos,
                        'contrasub' => $amountdata->ACCode ?? '',
                        'subcode' => $envirofom->roomchrgdueac,
                        'amtcr' => $amountdata->DebitAmt,
                        'amtdr' => '0.00',
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => $row->settledate,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    $hpostdata2 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $l++,
                        'vno' => $vno,
                        'vdate' => $row->settledate,
                        'vtype' => $vtype,
                        'vprefix' => $row->vprefix,
                        'narration' => 'Against Guest Bill No(s) : ' . $billnos,
                        'contrasub' => $envirofom->roomchrgdueac,
                        'subcode' => $amountdata->ACCode ?? '',
                        'amtcr' => '0.00',
                        'amtdr' => $amountdata->DebitAmt,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => $row->settledate,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($hpostdata1);
                    Ledger::insert($hpostdata2);
                }
            }

            // Pos Submit Ledger
            $posledger = DB::table('paycharge')
                ->select([
                    DB::raw('SUM(paycharge.amtdr) - SUM(paycharge.amtcr) AS DebitAmt'),
                    DB::raw('MAX(paycharge.comments) as Comments'),
                    DB::raw('paycharge.paycode'),
                    DB::raw('MAX(revmast.ac_code) AS ACCode'),
                    DB::raw('MAX(depart.name) as Department'),
                    DB::raw('MAX(revmast.name) as RevenueName'),
                    DB::raw('MAX(paycharge.vdate) as vdate'),
                    DB::raw('MAX(paycharge.vprefix) as vprefix')
                ])
                ->leftJoin('revmast', 'paycharge.paycode', '=', 'revmast.rev_code')
                ->leftJoin('depart', 'paycharge.restcode', '=', 'depart.dcode')
                ->where('paycharge.roomtype', '<>', 'RO')
                ->whereBetween('paycharge.vdate', [$fromdate, $todate])
                ->where('paycharge.propertyid', $this->propertyid)
                ->where('paycharge.restcode', '<>', 'FOM' . $this->propertyid)
                ->groupBy('paycharge.paycode', 'paycharge.restcode')
                ->get();

            if ($posledger->isNotEmpty()) {
                foreach ($posledger as $row) {

                    $billnos = DB::table('paycharge')
                        ->select('vno')
                        ->distinct()
                        ->where('paycharge.propertyid', $this->propertyid)
                        ->where('vdate', $row->vdate)
                        ->whereRaw('paycharge.amtdr - paycharge.amtcr <> 0')
                        ->where('paycharge.restcode', '<>', 'FOM' . $this->propertyid)
                        ->where('paycharge.paycode', $row->paycode)
                        ->pluck('vno')
                        ->implode(', ');

                    $vno = $row->vprefix . date('dm', strtotime($row->vdate));
                    $vtype = 'HPOST';
                    $docid = $this->propertyid . $vtype . '‎ ‎ ' . $row->vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                    $posledgerdata1 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $l++,
                        'vno' => $vno,
                        'vdate' => $row->vdate,
                        'vtype' => $vtype,
                        'vprefix' => $row->vprefix,
                        'narration' => 'Bill No(s) : ' . $billnos,
                        'contrasub' => $envirofom->roomchrgdueac,
                        'subcode' => $amountdata->ACCode ?? '',
                        'amtcr' => '0.00',
                        'amtdr' => $amountdata->DebitAmt,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => $row->vdate,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    $posledgerdata2 = [
                        'propertyid' => $this->propertyid,
                        'docid' => $docid,
                        'vsno' => $l++,
                        'vno' => $vno,
                        'vdate' => $row->vdate,
                        'vtype' => $vtype,
                        'vprefix' => $row->vprefix,
                        'narration' => 'Bill No(s) : ' . $billnos,
                        'contrasub' => $amountdata->ACCode ?? '',
                        'subcode' => $envirofom->roomchrgdueac,
                        'amtcr' => '0.00',
                        'amtdr' => $amountdata->DebitAmt,
                        'chqno' => '',
                        'chqdate' => null,
                        'clgdate' => $row->vdate,
                        'groupcode' => '',
                        'groupnature' => '',
                        'u_name' => Auth::user()->name,
                        'u_entdt' => $this->currenttime,
                        'u_ae' => 'a',
                    ];

                    Ledger::insert($posledgerdata1);
                    Ledger::insert($posledgerdata2);
                }
            }

            $revmastpaypost = DB::table('revmast')
                ->where('propertyid', $this->propertyid)
                ->where('field_type', 'P')
                ->whereIn('pay_type', ['Cash', 'UPI', 'Credit Card', 'Hold'])
                ->get();

            if ($revmastpaypost->isNotEmpty()) {

                foreach ($revmastpaypost as $rows) {
                    $datarevpost = DB::table('paycharge')
                        ->leftJoin('revmast', 'revmast.rev_code', '=', 'paycharge.paycode')
                        ->select([
                            DB::raw('(paycharge.amtcr - paycharge.amtdr) as CreditAmt'),
                            'paycharge.paycode',
                            'revmast.ac_code',
                            'revmast.name as RevenueName',
                            'paycharge.comments',
                            'paycharge.vdate',
                            'paycharge.vprefix'
                        ])
                        ->where('paycharge.propertyid', $this->propertyid)
                        ->whereBetween('paycharge.vdate', [$fromdate, $todate])
                        ->whereIn('paycharge.vtype', ['ARRES', 'ADRES'])
                        ->whereNotIn('paycharge.docid', function ($query) {
                            $query->select(DB::raw('distinct contraid'))
                                ->from('paycharge')
                                ->whereNotNull('contraid')
                                ->where('contraid', '<>', '');
                        })
                        ->where('paycharge.paytype', $rows->pay_type)
                        ->where('paycharge.restcode', 'FOM' . $this->propertyid)
                        ->get();

                    if ($datarevpost->isNotEmpty()) {

                        foreach ($datarevpost as $row) {

                            $billnos = DB::table('paycharge')
                                ->select('vno')
                                ->distinct()
                                ->where('paycharge.propertyid', $this->propertyid)
                                ->where('vdate', $row->vdate)
                                ->where('paycharge.restcode', 'FOM' . $this->propertyid)
                                ->where('paycharge.paycode', $row->paycode)
                                ->pluck('vno')
                                ->implode(', ');

                            $vno = $row->vprefix . date('dm', strtotime($row->vdate));
                            $vtype = 'HPOST';
                            $docid = $this->propertyid . $vtype . '‎ ‎ ' . $row->vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                            $revmastpaypost1 = [
                                'propertyid' => $this->propertyid,
                                'docid' => $docid,
                                'vsno' => $l++,
                                'vno' => $vno,
                                'vdate' => $row->vdate,
                                'vtype' => $vtype,
                                'vprefix' => $row->vprefix,
                                'narration' => 'Adv. Agst. Res.No. : ' . $billnos,
                                'contrasub' => $row->ac_code ?? '',
                                'subcode' => $envirofom->roomchrgdueac,
                                'amtcr' => $row->CreditAmt,
                                'amtdr' => '0.00',
                                'chqno' => '',
                                'chqdate' => null,
                                'clgdate' => $row->vdate,
                                'groupcode' => '',
                                'groupnature' => '',
                                'u_name' => Auth::user()->name,
                                'u_entdt' => $this->currenttime,
                                'u_ae' => 'a',
                            ];

                            $revmastpaypost2 = [
                                'propertyid' => $this->propertyid,
                                'docid' => $docid,
                                'vsno' => $l++,
                                'vno' => $vno,
                                'vdate' => $row->vdate,
                                'vtype' => $vtype,
                                'vprefix' => $row->vprefix,
                                'narration' => 'Adv. Agst. Res.No. : ' . $billnos,
                                'contrasub' => $envirofom->roomchrgdueac,
                                'subcode' => $row->ac_code ?? '',
                                'amtcr' => '0.00',
                                'amtdr' => $row->CreditAmt,
                                'chqno' => '',
                                'chqdate' => null,
                                'clgdate' => $row->vdate,
                                'groupcode' => '',
                                'groupnature' => '',
                                'u_name' => Auth::user()->name,
                                'u_entdt' => $this->currenttime,
                                'u_ae' => 'a',
                            ];

                            Ledger::insert($revmastpaypost1);
                            Ledger::insert($revmastpaypost2);
                        }
                    }
                }
            }

            Paycharge::whereBetween('vdate', [$fromdate, $todate])->whereIn('vtype', ['PPOS', 'IPOS'])->where('propertyid', $this->propertyid)->delete();

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
                ->whereBetween('suntran.vdate', [$fromdate, $todate])
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
                    ->whereDate('date_from', '<=', $fromdate)
                    ->whereDate('date_to', '>=', $todate)
                    ->first();

                $vno = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtypeac)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');

                $docid = $this->propertyid . $vtypeac . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vno;

                $indata = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $vno,
                    'vdate' => $row->vdate,
                    'sno' => '1',
                    'sno1' => '1',
                    'vtype' => $vtypeac,
                    'vtime' => date('H:i:s'),
                    'vprefix' => $vprefix,
                    'comments' => $row->revenue . 'Bill No: ' . $vno,
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
                ->whereBetween('stock.vdate', [$fromdate, $todate])
                ->where('stock.propertyid', $this->propertyid)
                ->where('stock.delflag', '<>', 'Y')
                ->whereRaw("stock.vtype = CONCAT('B', COALESCE(depart.short_name, ''))")
                ->whereIn('depart.rest_type', ['Outlet', 'Room Service'])
                ->groupBy('stock.restcode', 'stock.vdate', 'itemcatmast.RevCode', 'itemcatmast.AcCode')
                ->get();

            foreach ($ipospost as $row) {
                $vnos = explode(',', $row->vno_group);
                $billNoRange = DateHelper::generateBillNoRange($vnos);

                $comment = $row->DShortName . ' Bill No: ' . $billNoRange;
                $vtypeipos = 'IPOS';

                $chkvpf = VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtypeipos)
                    ->whereDate('date_from', '<=', $fromdate)
                    ->whereDate('date_to', '>=', $todate)
                    ->first();

                $vnoipos = $chkvpf->start_srl_no + 1;
                $vprefix = $chkvpf->prefix;

                VoucherPrefix::where('propertyid', $this->propertyid)
                    ->where('v_type', $vtypeipos)
                    ->where('prefix', $vprefix)
                    ->increment('start_srl_no');

                $docid = $this->propertyid . $vtypeipos . '‎ ‎ ' . $vprefix . '‎ ‎ ‎ ‎ ' . $vnoipos;

                $iposin = [
                    'propertyid' => $this->propertyid,
                    'docid' => $docid,
                    'vno' => $vnoipos,
                    'vdate' => $row->vdate,
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

            DB::commit();

            return back()->with('success', 'Account Posting Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Unknown error occured: ' . $e->getMessage() . 'On Line: ' . $e->getLine());
        }
    }
}
