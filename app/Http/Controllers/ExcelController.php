<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Companyreg;
use App\Models\Paycharge;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends Controller
{

    protected $username;
    protected $email;
    protected $propertyid;
    protected $currenttime;
    protected $ptlngth;
    protected $prpid;
    protected $ncurdate;
    protected $datemanage;
    protected $company;

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
            $this->datemanage = DateHelper::calculateDateRanges($this->ncurdate);
            $this->company = Companyreg::where('propertyid', $this->propertyid)->first();
            return $next($request);
        });
    }

    public function gstr1(Request $request)
    {
        return view('property.gstexcel', [
            'ncurdate' => $this->ncurdate
        ]);
    }

    private function findFirstEmptyRow($worksheet, $column = 'A', $startRow = 6)
    {
        $row = $startRow;
        while (true) {
            $cellValue = $worksheet->getCell($column . $row)->getValue();
            if (is_null($cellValue) || trim($cellValue) === '') {
                return $row;
            }
            $row++;
        }
    }


    // public function submitgstr1()
    // {
    //     $filePath = storage_path('app/public/files/GSTR1_Excel_Workbook_Template_V2.1.xlsx');

    //     if (!file_exists($filePath)) {
    //         return response()->json(['error' => 'File not found'], 404);
    //     }

    //     $reader = IOFactory::createReaderForFile($filePath);
    //     $spreadsheet = $reader->load($filePath);
    //     $sheetNames = $spreadsheet->getSheetNames();

    //     // Print or return sheet names
    //     foreach ($sheetNames as $sheetName) {
    //         echo $sheetName . "<br>";
    //     }

    //     // Or return as JSON
    //     // return response()->json($sheetNames);
    // }

    public function submitgstr1(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = $request->todate;
        $yt = $this->datemanage['hf']['start'] . '-' . $this->datemanage['hf']['end'];

        $repdata = $this->getGSTR1Data($fromdate, $todate);
        $repdata2 = $this->getGSTR1DataPOS($fromdate, $todate);
        $company = $this->company;
        $division_code = $company->division_code;

        $docdata = [];

        // 1. From sale1 table
        $docdata = array_merge(
            $docdata,
            DB::table('sale1')
                ->select(
                    'vtype',
                    DB::raw('MIN(vno) AS startsrlno'),
                    DB::raw('MAX(vno) AS endsrlno'),
                    DB::raw("COUNT(CASE WHEN delflag = 'Y' THEN 1 ELSE NULL END) AS totalcancelledbill")
                )
                ->whereBetween('vdate', [$fromdate, $todate])
                ->where('propertyid', $this->propertyid)
                ->groupBy('vtype')
                ->get()
                ->toArray()
        );

        // 2. From fombilldetails table
        $docdata = array_merge(
            $docdata,
            DB::table('fombilldetails')
                ->select(
                    DB::raw("'BCNT' as vtype"),
                    DB::raw('MIN(billno) AS startsrlno'),
                    DB::raw('MAX(billno) AS endsrlno'),
                    DB::raw("COUNT(CASE WHEN status = 'Cancel' THEN 1 ELSE NULL END) AS totalcancelledbill")
                )
                ->where('propertyid', $this->propertyid)
                ->whereBetween('billdate', [$fromdate, $todate])
                ->get()
                ->toArray()
        );


        $excelData = [];
        $b2bdatajson = [];
        $b2csdata = [];
        $b2csjson = [];
        $docrows = [];
        $docrowsjson = [];
        $hsnrowsb2b = [];
        $hsnrowsjson = [];
        $hsnrowsb2c = [];
        $count = 0;
        $count2 = 0;
        $count3 = 0;
        $count4 = 0;
        $count5 = 0;

        $taxGrouped = [];

        // return $repdata;

        foreach ($repdata as $row) {
            if (!empty($row->GSTIN)) {
                $invoiceno = empty($division_code)
                    ? 'BCNT/' . $yt . '/' . $row->Bill_No
                    : $division_code . $yt . '/' . $row->Bill_No;

                $fdate = DateTime::createFromFormat('Y-m-d', $row->Bill_Date);

                $excelData[] = [
                    $row->GSTIN,
                    $row->CompanyName,
                    $invoiceno,
                    $fdate ? $fdate->format('d-M-y') : '',
                    $row->BillTotal,
                    $company->state_code . '-' . $company->state,
                    'N',
                    "",
                    'Regular B2B',
                    $row->EGSTIN,
                    $row->TAXPER,
                    $row->BASEVALUE,
                    0.00
                ];

                $b2bdatajson[] = [
                    "ctin" =>  $row->GSTIN,
                    "inv" => [
                        "inum" => $invoiceno,
                        "idt" => $fdate,
                        "val" => $row->BillTotal,
                        "pos" => $company->state_code,
                        "rchrg" => "N",
                        "inv_typ" => "R",
                        "itms" => [
                            "num" => $count,
                            "itm_det" => [
                                "txval" => $row->BASEVALUE,
                                "rt" => $row->TAXPER,
                                "camt" => calculateTax($row->BillTotal, $row->TAXPER),
                                "samt" => calculateTax($row->BillTotal, $row->TAXPER),
                                "csamt" => 0
                            ]
                        ]
                    ],
                ];
                $count++;
            } else {
                if (!empty($row->EGSTIN)) {
                    // Keep EGSTIN rows separate
                    $b2csdata[] = [
                        'OE',
                        $company->state_code . '-' . $company->state,
                        '',
                        $row->TAXPER,
                        $row->BASEVALUE,
                        '0.00',
                        $row->EGSTIN
                    ];

                    $b2csjson[] = [
                        "sply_ty" => "INTRA",
                        "rt" => $row->TAXPER,
                        "typ" => "OE",
                        "pos" => $company->state_code,
                        "txval" => $row->BASEVALUE,
                        "camt" => calculateTax($row->BillTotal, $row->TAXPER),
                        "samt" => calculateTax($row->BillTotal, $row->TAXPER),
                        "csamt" => 0
                    ];

                    $count2++;
                } else {
                    // Group by TAXPER if EGSTIN is empty
                    $taxPer = $row->TAXPER;
                    if (!isset($taxGrouped[$taxPer])) {
                        $taxGrouped[$taxPer] = 0;
                    }
                    $taxGrouped[$taxPer] += $row->BASEVALUE;
                }
            }
        }

        foreach ($repdata2 as $row) {
            if (!empty($row->GSTIN)) {
                $invoiceno = empty($division_code)
                    ? 'BCNT/' . $yt . '/' . $row->BillNo
                    : $division_code . $yt . '/' . $row->BillNo;

                $fdate = DateTime::createFromFormat('Y-m-d', $row->Bill_Date);

                $excelData[] = [
                    $row->GSTIN,
                    $row->CompanyName,
                    $invoiceno,
                    $fdate ? $fdate->format('d-M-y') : '',
                    $row->BillTotal,
                    $company->state_code . '-' . $company->state,
                    'N',
                    "",
                    'Regular B2B',
                    $row->EGSTIN,
                    $row->TAXPER,
                    $row->BASEVALUE,
                    0.00
                ];

                $b2bdatajson[] = [
                    "ctin" =>  $row->GSTIN,
                    "inv" => [
                        "inum" => $invoiceno,
                        "idt" => $fdate,
                        "val" => $row->BillTotal,
                        "pos" => $company->state_code,
                        "rchrg" => "N",
                        "inv_typ" => "R",
                        "itms" => [
                            "num" => $count,
                            "itm_det" => [
                                "txval" => $row->BASEVALUE,
                                "rt" => $row->TAXPER,
                                "camt" => calculateTax($row->BillTotal, $row->TAXPER),
                                "samt" => calculateTax($row->BillTotal, $row->TAXPER),
                                "csamt" => 0
                            ]
                        ]
                    ],
                ];
                $count++;
            } else {
                if (!empty($row->EGSTIN)) {
                    // Keep EGSTIN rows separate
                    $b2csdata[] = [
                        'OE',
                        $company->state_code . '-' . $company->state,
                        '',
                        $row->TAXPER,
                        $row->BASEVALUE,
                        '0.00',
                        $row->EGSTIN
                    ];

                    $b2csjson[] = [
                        "sply_ty" => "INTRA",
                        "rt" => $row->TAXPER,
                        "typ" => "OE",
                        "pos" => $company->state_code,
                        "txval" => $row->BASEVALUE,
                        "camt" => calculateTax($row->BillTotal, $row->TAXPER),
                        "samt" => calculateTax($row->BillTotal, $row->TAXPER),
                        "csamt" => 0
                    ];

                    $count2++;
                } else {
                    // Group by TAXPER if EGSTIN is empty
                    $taxPer = $row->TAXPER * 2;
                    if (!isset($taxGrouped[$taxPer])) {
                        $taxGrouped[$taxPer] = 0;
                    }
                    $taxGrouped[$taxPer] += $row->BASEVALUE;
                }
            }
        }

        // Add grouped rows to $b2csdata
        foreach ($taxGrouped as $taxPer => $baseValueSum) {
            $b2csdata[] = [
                'OE',
                $company->state_code . '-' . $company->state,
                '',
                $taxPer,
                number_format($baseValueSum, 2, '.', ''),
                '0.00',
                ''
            ];

            $b2csjson[] = [
                "csamt" => 0,
                "samt" => 25309.43,
                "rt" => 12,
                "flag" => "N",
                "pos" => $company->state_code,
                "txval" => number_format($baseValueSum, 2, '.', ''),
                "typ" => "OE",
                "camt" => 25309.43,
                "chksum" => "3d6651776a9b747b1a9c4ed471571a1ce68bf9faee257e68ef3ccd0aa634a5f9",
                "iamt" => 0,
                "sply_ty" => "INTRA"
            ];

            $b2csjson[] = [
                "sply_ty" => "INTRA",
                "rt" => $taxPer,
                "typ" => "OE",
                "pos" => $company->state_code,
                "txval" => $baseValueSum,
                "camt" => calculateTax($baseValueSum, $taxPer),
                "samt" => calculateTax($baseValueSum, $taxPer),
                "csamt" => 0
            ];
            $count2++;
        }

        $n = 1;
        foreach ($docdata as $row) {
            $vtype = $row->vtype;
            $startsrlno = (int) $row->startsrlno;
            $endsrlno   = (int) $row->endsrlno;

            if ($vtype == 'BCNT') {
                $startcode = empty($division_code)
                    ? "BCNT/$yt/$startsrlno"
                    : "$division_code/$yt/$startsrlno";

                $endcode = empty($division_code)
                    ? "BCNT/$yt/$endsrlno"
                    : "$division_code/$yt/$endsrlno";
            } else {
                $startcode = "$vtype/$yt/$startsrlno";
                $endcode = "$vtype/$yt/$endsrlno";
            }

            $docrows[] = [
                'Invoice for outward supply',
                $startcode,
                $endcode,
                $endsrlno - $startsrlno,
                $row->totalcancelledbill ?? '0'
            ];

            $docrowsjson[] = [
                "flag" => "N",
                "doc_det" => [
                    "docs" => [
                        "cancel" => 0,
                        "num" => 2,
                        "totnum" => 1650,
                        "from" => "BMM/25-26/1135",
                        "to" => "BMM/25-26/2784",
                        "net_issue" => 1650
                    ],
                    "doc_num" => $n++
                ]
            ];

            $count3++;
        }

        // $hsnquery = DB::table('stock')
        //     ->select(
        //         'items.hsncode',
        //         'depart.name',
        //         DB::raw('SUM(stock.amount) AS totalvalue'),
        //         DB::raw('stock.taxper / 2 as taxper'),
        //         DB::raw('SUM(sale1.cgst) as cgst'),
        //         DB::raw('SUM(sale1.sgst) as sgst'),
        //         'sale1.party'
        //     )
        //     ->leftJoin('items', 'items.icode', '=', 'stock.item')
        //     ->leftJoin('depart', 'depart.dcode', '=', 'stock.restcode')
        //     ->leftJoin('sale1', 'sale1.docid', '=', 'stock.docid')
        //     ->where('stock.propertyid', $this->propertyid)
        //     ->whereBetween('stock.vdate', [$fromdate, $todate])
        //     ->where('stock.delflag', '<>', 'Y')
        //     ->groupBy('stock.taxper', 'stock.restcode', 'items.hsncode')
        //     ->get();

        $innerQuery = DB::table('sale1 as s1')
            ->select(
                's1.restcode',
                'd.name',
                'im.HSNCode as hsncode',
                's2.taxper',
                DB::raw('s1.total / hsn_count.total_hsn as total'),
                DB::raw('s1.netamt / hsn_count.total_hsn as netamt'),
                DB::raw('s1.cgst / hsn_count.total_hsn as cgst'),
                DB::raw('s1.sgst / hsn_count.total_hsn as sgst')
            )
            ->join('stock as st', 'st.docid', '=', 's1.docid')
            ->join('depart as d', 'd.dcode', '=', 's1.restcode')
            ->join('itemmast as im', function ($join) {
                $join->on('im.Code', '=', 'st.item')
                    ->on('im.RestCode', '=', 'st.restcode');
            })
            ->join('sale2 as s2', function ($join) {
                $join->on('s2.docid', '=', 's1.docid')
                    ->where('s2.taxper', '>', 0);
            })
            ->join(DB::raw('(
                    SELECT s.docid, COUNT(DISTINCT im.HSNCode) as total_hsn
                    FROM stock s
                    JOIN itemmast im ON im.Code = s.item AND im.RestCode = s.restcode
                    GROUP BY s.docid
                ) as hsn_count'), 'hsn_count.docid', '=', 's1.docid')
            ->where('s1.propertyid', $this->propertyid)
            ->whereBetween('s1.vdate', [$fromdate, $todate])
            ->groupBy('s1.docid', 'im.HSNCode', 's2.taxper', 's1.restcode', 'd.name');

        $hsnquery = DB::table(DB::raw("({$innerQuery->toSql()}) as hsndata"))
            ->mergeBindings($innerQuery)
            ->select(
                'hsndata.restcode',
                'hsndata.name',
                'hsndata.hsncode',
                'hsndata.taxper',
                DB::raw('SUM(hsndata.total) as total'),
                DB::raw('SUM(hsndata.netamt) as netamt'),
                DB::raw('SUM(hsndata.cgst) as cgst'),
                DB::raw('SUM(hsndata.sgst) as sgst')
            )
            ->groupBy('hsndata.restcode', 'hsndata.hsncode', 'hsndata.taxper')
            ->get();


        foreach ($hsnquery as $row) {

            if (!empty($row->party)) {
                $hsnrowsb2b[] = [
                    $row->hsncode,
                    $row->name,
                    'LOT-LOTS',
                    0.00,
                    round((int)$row->total + ((int)$row->cgst + (int)$row->sgst), 2),
                    round($row->taxper, 2) * 2,
                    round($row->total, 2),
                    0.00,
                    $row->cgst,
                    $row->sgst,
                    0.00
                ];

                $hsnrowsjson[] = [
                    "num"   => $count4,
                    "hsn_sc" => $row->hsncode,
                    "desc" => $row->name,
                    "uqc" => "NA",
                    "qty" => 0,
                    "rt" => $row->taxpe * 2,
                    "txval" => $row->total,
                    "iamt" => 0,
                    round($row->cgst, 2),
                    round($row->sgst, 2),
                    "csamt" => 0
                ];

                $count4++;
            } else {
                $hsnrowsb2c[] = [
                    $row->hsncode,
                    $row->name,
                    'LOT-LOTS',
                    0.00,
                    round((float)$row->total + ((float)$row->cgst + (float)$row->sgst), 2),
                    round($row->taxper, 2) * 2,
                    round($row->total, 2),
                    0.00,
                    round($row->cgst, 2),
                    round($row->sgst, 2),
                    0.00
                ];

                $hsnrowsjson[] = [
                    "num"   => $count5,
                    "hsn_sc" => $row->hsncode,
                    "desc" => $row->name,
                    "uqc" => "NA",
                    "qty" => 0,
                    "rt" => $row->taxper * 2,
                    "txval" => $row->total,
                    "iamt" => 0,
                    "samt" => $row->sgst,
                    "camt" => $row->cgst,
                    "csamt" => 0
                ];
                $count5++;
            }
        }

        // $hsnfom = DB::table('paycharge as P')
        //     ->select([
        //         'P.sno1',
        //         'P.sno',
        //         'P.FolioNoDocId',
        //         'P.DocId',
        //         'SM.Nature',
        //         'P.PayCode',
        //         DB::raw("'996311' as hsncode"),
        //         'P.Vdate',
        //         'P.FolioNo',
        //         'P.SettleDate',
        //         'P.billno',
        //         DB::raw('SUM(P.amtdr) AS taxsum'),
        //         DB::raw('SUM(P.billamount) AS taxableamount'),
        //         'P.taxper',
        //         DB::raw('CASE WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt ELSE - P.OnAmt END AS BaseValue'),
        //         DB::raw("MAX(guestfolio.company) as company")
        //     ])
        //     ->leftJoin('revmast', 'P.PayCode', '=', 'revmast.rev_code')
        //     ->leftJoin('sundrymast as SM', 'revmast.Sundry', '=', 'SM.sundry_code')
        //     ->leftJoin('guestfolio', 'guestfolio.docid', '=', 'P.folionodocid')
        //     ->whereIn('SM.Nature', ['CGST', 'SGST', 'IGST'])
        //     ->whereBetween('P.SettleDate', [$fromdate, $todate])
        //     ->where('P.FolioNo', '<>', 0)
        //     ->whereNotNull('P.SettleDate')
        //     ->where('P.billno', '<>', 0)
        //     ->where('P.propertyid', $this->propertyid)
        //     // ->groupBy('revmast.hsn_code', 'P.PayCode')
        //     ->groupBy('revmast.hsn_code', 'P.taxper')
        //     ->get();

        $hsnfom = DB::table('paycharge as P')
            ->select(
                'P.sno1',
                'P.sno',
                'P.FolioNoDocId',
                'P.DocId',
                'SM.Nature',
                'P.PayCode',
                DB::raw("'996311' as hsncode"),
                'P.Vdate',
                'P.FolioNo',
                'P.SettleDate',
                'P.billno',
                DB::raw('SUM(P.amtdr) as taxsum'),
                DB::raw('SUM(P.billamount) as taxableamount'),
                'P.taxper',
                DB::raw("CASE WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt ELSE - P.OnAmt END as BaseValue"),
                'subgroup.name as company',
                'subgroup.gstin',
                DB::raw("SUM(CASE WHEN SM.nature = 'CGST' THEN P.amtdr ELSE 0 END) cgst"),
                DB::raw("SUM(CASE WHEN SM.nature = 'SGST' THEN P.amtdr ELSE 0 END) sgst"),
                'SM.nature as Nature',
                DB::raw("SUM(P.amtdr) + SUM(P.billamount) + SUM(P.amtdr) as netamount")
            )
            ->leftJoin('revmast', function ($join) {
                $join->on('P.PayCode', '=', 'revmast.rev_code')
                    ->where('revmast.propertyid', $this->propertyid);
            })
            ->leftJoin('sundrymast as SM', function ($join) {
                $join->on('revmast.Sundry', '=', 'SM.sundry_code')
                    ->where('SM.propertyid', $this->propertyid);
            })
            ->leftJoin('guestfolio', function ($join) {
                $join->on('guestfolio.docid', '=', 'P.folionodocid')
                    ->where('guestfolio.propertyid', $this->propertyid);
            })
            ->leftJoin('subgroup', function ($join) {
                $join->on('subgroup.sub_code', '=', 'guestfolio.company')
                    ->where('subgroup.propertyid', $this->propertyid);
            })
            ->whereIn('SM.Nature', ['CGST', 'SGST', 'IGST'])
            ->whereBetween('P.SettleDate', [$fromdate, $todate])
            ->where('P.FolioNo', '<>', 0)
            ->whereNotNull('P.SettleDate')
            ->where('P.billno', '<>', 0)
            ->where('P.propertyid', $this->propertyid)
            ->groupBy('revmast.hsn_code', 'P.taxper', 'guestfolio.company', 'P.paycode')
            ->get();

        $grouped = [];

        foreach ($hsnfom as $row) {
            $key = (!empty($row->gstin) ? 'b2b' : 'b2c') . '_' . $row->hsncode . '_' . number_format((float)$row->taxper, 2, '.', '');

            if (!isset($grouped[$key])) {

                $grouped[$key] = [
                    'docid' => $row->FolioNoDocId,
                    'hsncode'        => $row->hsncode,
                    'taxper'         => (float)$row->taxper,
                    'netamount'         => 0.00,
                    'taxableamount'  => 0.0,
                    'cgst'           => 0.0,
                    'sgst'           => 0.0,
                    'igst'           => 0.0,
                    'gstin'          => $row->gstin
                ];
            }

            if (strtoupper($row->Nature) === 'CGST' || strtoupper($row->Nature) === 'IGST') {
                $grouped[$key]['taxableamount'] += (float)$row->taxableamount;
                $grouped[$key]['netamount'] += (float)$row->netamount;
            }

            $grouped[$key]['cgst'] += (float)$row->cgst;
            $grouped[$key]['sgst'] += (float)$row->sgst;
        }

        // $totalValue = 0;
        foreach ($grouped as $key => $g) {

            $rateStr = number_format($g['taxper'], 2);

            $totalValue = $g['netamount'];
            if (!empty($g['gstin'])) {

                $hsnrowsb2b[] = [
                    $g['hsncode'],
                    'LODGING',
                    'LOT-LOTS',
                    0,
                    $totalValue,
                    $rateStr * 2,
                    round($g['taxableamount'], 2),
                    0.00,
                    round($g['cgst'], 2),
                    round($g['sgst'], 2),
                    0.00
                ];

                $hsnrowsjson[] = [
                    "num"   => $count4++,
                    "hsn_sc" => $g['hsncode'],
                    "desc"  => "LODGING",
                    "uqc"   => "NA",
                    "qty"   => 0,
                    "rt"    => $g['taxper'] * 2,
                    "txval" => round($g['taxableamount'], 2),
                    "iamt"  => 0,
                    "samt"  => round($g['sgst'], 2),
                    "camt"  => round($g['cgst'], 2),
                    "csamt" => 0
                ];
            } else {
                $hsnrowsb2c[] = [
                    $g['hsncode'],
                    'LODGING',
                    'LOT-LOTS',
                    0,
                    $totalValue,
                    $rateStr * 2,
                    round($g['taxableamount'], 2),
                    0.00,
                    round($g['cgst'], 2),
                    round($g['sgst'], 2),
                    0.00
                ];

                $hsnrowsjson[] = [
                    "num"   => $count5++,
                    "hsn_sc" => $g['hsncode'],
                    "desc"  => "LODGING",
                    "uqc"   => "NA",
                    "qty"   => 0,
                    "rt"    => $g['taxper'] * 2,
                    "txval" => round($g['taxableamount'], 2),
                    "iamt"  => 0,
                    "samt"  => round($g['sgst'], 2),
                    "camt"  => round($g['cgst'], 2),
                    "csamt" => 0
                ];
            }
        }

        // LOG::info('HSN B2B: ' . json_encode($hsnrowsb2b));
        // LOG::info('HSN B2C: ' . json_encode($hsnrowsb2c));

        // return;

        $datajson = [
            "gstin" => $company->gstin,
            "fp" => getMonthYearCode($this->ncurdate),
            "version" => "GST3.1.7",
            "hash" => "hash",
            "b2b" => $b2bdatajson,
            "b2cs" => $b2csjson,
            "hsn" => $hsnrowsjson,
            "doc_issue" => $docrowsjson,
            "fil_dt" => date('d-m-Y')
        ];

        $directory = storage_path('app/public/files/newfile/');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (file_exists('storage/files/newfile/gstr1_data.json')) {
            unlink('storage/files/newfile/gstr1_data.json');
        }

        $filePath = $directory . 'gstr1_data.json';
        File::put($filePath, json_encode($datajson, JSON_PRETTY_PRINT));

        // Log::info('HSNROWS B2B: ' . json_encode($hsnrowsb2b));
        // Log::info('HSNROWS B2C: ' . json_encode($hsnrowsb2c));
        // return;

        try {
            // $templatePath = storage_path('app/public/files/GSTR1_Excel_Workbook_Template_V2.1.xlsx');
            $templatePath = storage_path('app/public/files/gstr1.xlsx');
            $newDir = storage_path('app/public/files/newfile/');
            // $newFile = $newDir . 'GSTR1_Excel_Workbook_Template_V2.1.xlsx';
            $newFile = $newDir . 'gstr1.xlsx';

            // if (file_exists('storage/files/newfile/GSTR1_Excel_Workbook_Template_V2.1.xlsx')) {
            //     unlink('storage/files/newfile/GSTR1_Excel_Workbook_Template_V2.1.xlsx');
            // }
            if (file_exists('storage/files/newfile/gstr1.xlsx')) {
                unlink('storage/files/newfile/gstr1.xlsx');
            }

            // Ensure the target directory exists
            if (!file_exists($newDir)) {
                mkdir($newDir, 0755, true);
            }

            if (!copy($templatePath, $newFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to copy template file.'
                ], 500);
            }

            // Log::info('Excel File: ' . $newFile);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($newFile);

            // === B2B Sheet ===
            $worksheetB2B = $spreadsheet->getSheetByName('b2b,sez,de');
            if (!$worksheetB2B) {
                return response()->json(['success' => false, 'message' => "Sheet 'b2b' not found."], 500);
            }

            // Log::info('Excel File Sheet: ' . json_encode($excelData));
            // return true;

            $emptyRowStartB2B = $this->findFirstEmptyRow($worksheetB2B, 'A', 5);
            // return $excelData;
            foreach ($excelData as $i => $row) {
                $worksheetB2B->fromArray($row, null, 'A' . ($emptyRowStartB2B + $i));
            }

            // === B2CS Sheet ===
            $worksheetB2CS = $spreadsheet->getSheetByName('b2cs');
            if (!$worksheetB2CS) {
                return response()->json(['success' => false, 'message' => "Sheet 'b2cs' not found."], 500);
            }

            $emptyRowStartB2CS = $this->findFirstEmptyRow($worksheetB2CS, 'A', 5);
            foreach ($b2csdata as $i => $row) {
                $worksheetB2CS->fromArray($row, null, 'A' . ($emptyRowStartB2CS + $i));
            }

            // === DOCS Sheet ===
            $worksheetdocs = $spreadsheet->getSheetByName('docs');
            if (!$worksheetdocs) {
                return response()->json(['success' => false, 'message' => "Sheet 'docs' not found."], 500);
            }

            $emptyrowstartdocs = $this->findFirstEmptyRow($worksheetdocs, 'A', 5);
            foreach ($docrows as $i => $row) {
                $worksheetdocs->fromArray($row, null, 'A' . ($emptyrowstartdocs + $i));
            }

            // === HSN Sheet B2B ===
            $worksheethsnb2b = $spreadsheet->getSheetByName('hsn(b2b)');
            if (!$worksheethsnb2b) {
                return response()->json(['success' => false, 'message' => "Sheet 'HSN B2B' not found."], 500);
            }

            $emptyrowstarthsnb2b = $this->findFirstEmptyRow($worksheethsnb2b, 'A', 5);
            foreach ($hsnrowsb2b as $i => $row) {
                $worksheethsnb2b->fromArray($row, null, 'A' . ($emptyrowstarthsnb2b + $i));
            }

            // === HSN Sheet B2C ===
            $worksheethsnb2c = $spreadsheet->getSheetByName('hsn(b2c)');
            if (!$worksheethsnb2c) {
                return response()->json(['success' => false, 'message' => "Sheet 'HSN B2C' not found."], 500);
            }

            $emptyrowstarthsnb2c = $this->findFirstEmptyRow($worksheethsnb2c, 'A', 5);
            foreach ($hsnrowsb2c as $i => $row) {
                $worksheethsnb2c->fromArray($row, null, 'A' . ($emptyrowstarthsnb2c + $i));
            }

            // Save to new path
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($newFile);

            return response()->json([
                'success' => true,
                // 'message' => "{$count} B2B rows and {$count2} B2CS and {$count3} DOCS and {$count4} HSN B2B and {$count5} HSN B2C rows inserted successfully. Saved to: newfile/"
                'message' => 'GSTR1 Generated Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' On Line: ' . $e->getLine()
            ], 500);
        }
    }

    public function getGSTR1Data($fromdate, $todate)
    {
        return DB::table(DB::raw("(
            SELECT 
                R.FolionoDocid, 
                R.Foliono, 
                R.Bill_Date, 
                R.Bill_No, 
                R.AmtDr, 
                R.BASEVALUE, 
                R.TAXPER, 
                0 AS TaxAmtOther, 
                0 AS TaxPerOther, 
                0 AS BaseValueOther, 
                0 AS NilRated 
            FROM (
                SELECT 
                    MAX(Q.FolionoDocid) AS FolionoDocid, 
                    MAX(Q.Foliono) AS Foliono, 
                    MAX(Q.SettleDate) AS Bill_Date, 
                    MAX(Q.Bill_No) AS Bill_No, 
                    SUM(Q.AmtDr) AS AmtDr, 
                    SUM(Q.BASEVALUE) AS BASEVALUE, 
                    MAX(Q.TAXPER) AS TAXPER 
                FROM (
                    SELECT 
                        P.FolionoDocid, 
                        P.FolioNo, 
                        P.SettleDate, 
                        MAX(P.billno) AS Bill_No, 
                        SUM(P.AmtDr - P.AmtCr) AS AmtDr, 
                        MAX(CASE WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt ELSE -P.OnAmt END) AS BASEVALUE, 
                        SUM(P.TaxPer) AS TAXPER 
                    FROM paycharge P 
                    LEFT JOIN revmast ON P.PayCode = revmast.rev_code 
                    LEFT JOIN sundrymast ON revmast.sundry = sundrymast.sundry_code 
                    WHERE sundrymast.nature IN('CGST', 'SGST', 'IGST') 
                        AND P.roomtype = 'RO' 
                        AND P.propertyid = {$this->propertyid} 
                        AND P.settledate BETWEEN ? AND ? 
                        AND (P.amtdr - P.amtcr) <> 0 
                        AND P.foliono <> 0 
                        AND P.settledate IS NOT NULL 
                        AND IFNULL(P.billno, '') <> '' 
                    GROUP BY P.docid, P.folionodocid, P.foliono, P.settledate, P.taxper
                ) Q 
                GROUP BY folionodocid, taxper
            ) R
        ) as T"))
            ->join(DB::raw("(
            SELECT 
                FolionoDocid, 
                SUM(amtdr - amtcr) AS BillTotal 
            FROM paycharge 
            WHERE (
                DocId IN (SELECT docid FROM paycharge WHERE paycode = 'DISC{$this->propertyid}') 
                OR (AmtDr <> 0 AND IFNULL(Modeset, '') <> 'S') 
                OR (Modeset = 'S' AND PayCode = 'ROFF{$this->propertyid}')
            ) 
            AND RoomType = 'RO' 
            AND propertyid = {$this->propertyid} 
            AND settledate BETWEEN ? AND ? 
            AND FolioNo <> 0 
            GROUP BY FolioNoDocId
        ) as S"), 'T.folionodocid', '=', 'S.FolionoDocId')
            ->join('guestfolio as GF', 'T.folionodocid', '=', 'GF.DocId')
            ->leftJoin('subgroup as SG', 'GF.Company', '=', 'SG.sub_code')
            ->leftJoin('subgroup as TA', 'GF.TravelAgent', '=', 'TA.sub_code')
            ->select([
                'T.*',
                DB::raw('0 AS Exempted'),
                DB::raw('0 AS NonGST'),
                'S.BillTotal',
                DB::raw("CONCAT('BCNT/24-25/', T.Bill_No) AS BillNo"),
                DB::raw("TRIM(IFNULL(SG.GSTIN, '')) AS GSTIN"),
                DB::raw("TRIM(IFNULL(SG.Name, '')) AS CompanyName"),
                DB::raw("IFNULL(GF.Company, '') AS Company"),
                DB::raw("TRIM(IFNULL(TA.GSTIN, '')) AS EGSTIN"),
                DB::raw("IFNULL(GF.TravelAgent, '') AS TravelAgent"),
                DB::raw("CASE WHEN (T.BaseValue <> 0 OR T.TaxAmtOther <> 0) THEN 1 ELSE 0 END AS Cond1"),
                DB::raw("CASE WHEN (T.NilRated <> 0) THEN 1 ELSE 0 END AS Cond2")
            ])
            ->orderByRaw("CAST(T.Bill_No AS UNSIGNED), T.FolionoDocid, T.TAXPER DESC")
            ->setBindings([$fromdate, $todate, $fromdate, $todate])
            ->get();
    }

    public function getGSTR1DataPOS($fromdate, $todate)
    {

        // Subquery: sale2 + suntran tax records (combined)
        $taxUnion = DB::table(DB::raw("(

    SELECT sale2.DocId, sale2.Taxcode, 
    sale2.TaxPer,
           SUM(sale2.TaxAmt) AS TaxAmt,
           SUM(sale2.BaseValue) AS BaseValue
    FROM sale2
    INNER JOIN depart ON depart.dcode = sale2.restcode
    WHERE sale2.delflag = 'N'
      AND sale2.propertyid = {$this->propertyid}
      AND sale2.vdate BETWEEN '{$fromdate}' AND '{$todate}'
      AND depart.rest_type IN ('Outlet', 'Room Service')
    GROUP BY sale2.DocId, sale2.Taxcode, sale2.TaxPer

    UNION ALL

    SELECT suntran.DocId, suntran.RevCode AS Taxcode, suntran.SValue AS TaxPer,
           SUM((suntran.BaseAmount * suntran.SValue / 100)) AS TaxAmt,
           SUM(suntran.BaseAmount) AS BaseValue
    FROM suntran
    INNER JOIN depart ON depart.dcode = suntran.restcode
    WHERE suntran.delflag = 'N'
      AND suntran.propertyid = {$this->propertyid}
      AND suntran.vdate BETWEEN '{$fromdate}' AND '{$todate}'
      AND depart.rest_type IN ('Outlet', 'Room Service')
      AND suntran.revcode <> ''
      AND suntran.svalue > 0
      AND suntran.amount > 0
    GROUP BY suntran.DocId, suntran.RevCode, suntran.SValue

) AS T"))
            ->join('revmast', function ($join) {
                $join->on('revmast.rev_code', '=', 'T.Taxcode')
                    ->where('revmast.propertyid', $this->propertyid);
            })
            ->leftJoin('sundrymast AS SM', function ($join) {
                $join->on('revmast.sundry', '=', 'SM.sundry_code')
                    ->where('SM.propertyid', $this->propertyid);
            })
            ->where('revmast.field_type', 'T')
            ->selectRaw("
    T.DocId,
    SM.Nature,
    T.Taxcode,
    T.TaxPer,
    T.TaxAmt,
    T.BaseValue
");

        // Grouped tax records
        $groupedTax = DB::table(DB::raw("({$taxUnion->toSql()}) AS SL"))
            ->mergeBindings($taxUnion)
            ->selectRaw("
        SL.DocId,
        SL.TaxPer AS TAXPER,
        SUM(CASE WHEN SL.Nature IN ('CGST','SGST','IGST') THEN SL.TaxAmt ELSE 0 END) AS TAXAMT,
        SUM(CASE WHEN SL.Nature IN ('Luxury Tax','Sale Tax','Service Tax') THEN SL.TaxAmt ELSE 0 END) AS TaxAmtOther,
        SUM(CASE WHEN SL.Nature IN ('Luxury Tax','Sale Tax','Service Tax') THEN SL.BaseValue ELSE 0 END) AS BaseValueOther,
        SUM(CASE WHEN SL.Nature IN ('Luxury Tax','Sale Tax','Service Tax') THEN SL.TaxPer ELSE 0 END) AS TaxPerOther,
        0 AS Exempted,
        0 AS NonGST
    ")
            ->groupBy('SL.DocId', 'SL.TaxPer');

        // Exempted items
        $exempted = DB::table('stock')
            ->join('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'stock.item')
                    ->on('itemmast.RestCode', '=', 'stock.itemrestcode');
            })
            ->join('itemcatmast', 'itemcatmast.Code', '=', 'itemmast.ItemCatCode')
            ->join('depart', 'depart.dcode', '=', 'stock.restcode')
            ->where('stock.delflag', 'N')
            ->where('stock.propertyid', $this->propertyid)
            ->whereBetween('stock.vdate', [$fromdate, $todate])
            ->where('stock.taxper', 0)
            ->where('itemcatmast.CatType', '<>', 'Liquor')
            ->whereIn('depart.rest_type', ['Outlet', 'Room Service'])
            ->selectRaw("
        stock.DocId,
        0 AS TAXPER,
        0 AS TAXAMT,
        0 AS TaxAmtOther,
        0 AS BaseValueOther,
        0 AS TaxPerOther,
        SUM(stock.Amount - stock.DiscAmt) AS Exempted,
        0 AS NonGST
    ")
            ->groupBy('stock.DocId');

        // NonGST items
        $nongst = DB::table('stock')
            ->join('itemmast', function ($join) {
                $join->on('itemmast.Code', '=', 'stock.item')
                    ->on('itemmast.RestCode', '=', 'stock.itemrestcode');
            })
            ->join('itemcatmast', 'itemcatmast.Code', '=', 'itemmast.ItemCatCode')
            ->join('depart', 'depart.dcode', '=', 'stock.restcode')
            ->where('stock.delflag', 'N')
            ->where('stock.propertyid', $this->propertyid)
            ->whereBetween('stock.vdate', [$fromdate, $todate])
            ->where('stock.taxper', 0)
            ->where('itemcatmast.CatType', '=', 'Liquor')
            ->whereIn('depart.rest_type', ['Outlet', 'Room Service'])
            ->selectRaw("
        stock.DocId,
        0 AS TAXPER,
        0 AS TAXAMT,
        0 AS TaxAmtOther,
        0 AS BaseValueOther,
        0 AS TaxPerOther,
        0 AS Exempted,
        SUM(stock.Amount - stock.DiscAmt) AS NonGST
    ")
            ->groupBy('stock.DocId');

        // Full union T1
        $t1 = $groupedTax->unionAll($exempted)->unionAll($nongst);

        // Final grouping from T1
        $Q = DB::table(DB::raw("({$t1->toSql()}) AS T1"))
            ->mergeBindings($t1)
            ->selectRaw("
        T1.DocId,
        T1.TAXPER,
        SUM(T1.TAXAMT) AS TAXAMT,
        SUM(T1.TaxAmtOther) AS TaxAmtOther,
        SUM(T1.BaseValueOther) AS BaseValueOther,
        SUM(T1.TaxPerOther) AS TaxPerOther,
        SUM(T1.Exempted) AS Exempted,
        SUM(T1.NonGST) AS NonGST
    ")
            ->groupBy('T1.DocId', 'T1.TAXPER');

        // Final main query
        $finalResult = DB::table(DB::raw("({$Q->toSql()}) AS Q"))
            ->mergeBindings($Q)
            ->join('sale1 AS S1', 'S1.DocId', '=', 'Q.DocId')
            ->leftJoin('subgroup AS SG', 'SG.sub_code', '=', 'S1.party')
            ->selectRaw("
        S1.DocId AS FolioNoDocId,
        S1.Vdate AS Bill_Date,
        CONCAT(IFNULL(RTRIM(LTRIM(S1.VType)), ''), '/24-25/', CAST(S1.VNo AS CHAR)) AS BillNo,
        IFNULL(SG.ConPerson, '') AS GuestName,
        S1.NetAmt AS BillTotal,
        S1.Total AS BASEVALUE,
        S1.cgst AS cgstvalue,
        S1.sgst AS sgstvalue,
        Q.TAXPER,
        IFNULL(Q.TaxAmtOther, 0) AS TaxAmtOther,
        IFNULL(Q.BaseValueOther, 0) AS BaseValueOther,
        IFNULL(Q.TaxPerOther, 0) AS TaxPerOther,
        IFNULL(Q.Exempted, 0) AS Exempted,
        IFNULL(Q.NonGST, 0) AS NonGST,
        0 AS NilRated,
        TRIM(IFNULL(SG.GSTIN, '')) AS GSTIN,
        TRIM(IFNULL(SG.Name, '')) AS CompanyName,
        IFNULL(S1.Party, '') AS Company,
        '' AS EGSTIN,
        CASE WHEN (IFNULL(S1.Total, 0) <> 0 OR IFNULL(Q.TaxAmtOther, 0) <> 0) THEN 1 ELSE 0 END AS Cond1,
        CASE WHEN (IFNULL(Q.Exempted, 0) <> 0 OR IFNULL(Q.NonGST, 0) <> 0) THEN 1 ELSE 0 END AS Cond2
    ")
            ->orderByRaw('S1.VType, S1.VNo, Q.TAXPER DESC')
            ->get();

        return $finalResult;
    }

    // Print sheet content
    // public function viewexcel()
    // {

    //     $file = storage_path('app/public/files/file.xlsx');
    //     $reader = new Xlsx();
    //     $reader->setReadDataOnly(true);

    //     $spreadsheet = $reader->load($file);

    //     $sheet = $spreadsheet->getSheetByName('sagar');
    //     if (!$sheet) {
    //         echo "Sheet 'b2b' not found.";
    //         return;
    //     }

    //     $data = $sheet->toArray();

    //     echo "<table border='1' cellpadding='5'>";
    //     foreach ($data as $row) {
    //         echo "<tr>";
    //         foreach ($row as $cell) {
    //             echo "<td>" . htmlspecialchars($cell) . "</td>";
    //         }
    //         echo "</tr>";
    //     }
    //     echo "</table>";
    // }

    // public function download()
    // {
    //     $file1 = storage_path('app/public/files/newfile/gstr1.xlsx');
    //     $file2 = storage_path('app/public/files/newfile/gstr1_data.json');

    //     $files = [
    //         'file1' => $file1,
    //         'file2' => $file2
    //     ];

    //     return response()->download($files);
    // }

    public function download()
    {
        $file1 = storage_path('app/public/files/newfile/gstr1.xlsx');
        $file2 = storage_path('app/public/files/newfile/gstr1_data.json');

        $zipFileName = 'gstr1_.zip';
        $zipPath = storage_path('app/public/files/newfile/' . $zipFileName);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($file1, 'gstr1.xlsx');
            $zip->addFile($file2, 'gstr1_data.json');
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP file.'], 500);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
