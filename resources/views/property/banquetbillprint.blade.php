<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hallsale1->party }} Bill No_{{ $hallsale1->vno }} Banquet Bill Receipt</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/images/favicon.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.2in;
            }

            body {
                margin: 0;
                padding: 0;
                font-size: 11px;
                line-height: 1.2;
            }

            table th {
                background: #adb5bdb5 !important;
            }

            .no-print {
                display: none;
            }

            .print-break {
                page-break-after: always;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 5px;
        }

        .invoice-header {
            border: 2px solid #000;
            padding: 2px;
            margin-bottom: 2px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 10px;
        }

        .duplicate-original {
            text-align: right;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .table-custom {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        table th {
            background: #adb5bdb5 !important;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10px;
            vertical-align: top;
        }

        .table-custom th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .amount-section {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 10px;
        }

        .footer-section {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 8px;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .small-text {
            font-size: 12px;
        }

        .gst-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .function-details {
            background-color: #f8f9fa;
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">

        <!-- Invoice Header -->
        <div class="invoice-header">

            <div class="row align-items-center">
                <!-- Left: Logo + GSTIN/SAC -->
                <div class="col-2 text-start">
                    <div class="logo mb-2">
                        <img src="{{ asset('storage/admin/property_logo/' . companydata()->logo) }}" alt="{{ companydata()->comp_name }}" style="max-width: 100%; height: auto;">
                    </div>
                    <div class="small-text">
                        <strong>GSTIN:</strong> {{ companydata()->gstin }}<br>
                        <strong>SAC Code:</strong> 996334
                    </div>
                </div>

                <!-- Center: Company Info -->
                <div class="col-9 text-center company-info">
                    <div class="text-bold" style="font-size: 16px;">{{ companydata()->comp_name }}</div>
                    <div class="small-text">
                        <strong>Website:</strong> {{ companydata()->website }} &nbsp;&nbsp;&nbsp;
                        <strong>Email:</strong> {{ companydata()->email }}
                    </div>
                    <div class="small-text">
                        <strong>Ph. No.:</strong> {{ companydata()->mobile }}
                    </div>
                    <div class="small-text">
                        <strong>Add:</strong> {{ companydata()->address1 }} {{ companydata()->address2 }} {{ companydata()->city }} {{ companydata()->state }}
                    </div>

                    <div class="mt-5">
                        <b>TAX INVOICE</b>
                    </div>
                </div>

                <div class="duplicate-original text-end mb-2">
                    <label><input type="checkbox" name="duplicate_copy" value="1"> Duplicate</label>
                    <label style="margin-left: 20px;"><input type="checkbox" name="original_copy" value="1"> Original</label>
                </div>
            </div>
        </div>

        <!-- Function Details -->
        <table class="table table-bordered table-sm m-0 p-0" style="font-size: 12px;">
            <!-- Title Row -->
            <thead>
                @if (!empty($hallsale1->remark))
                    <tr class="text-center fw-bold">
                        <th colspan="7" class="text-uppercase" style="font-size: 18px;">{{ $hallsale1->remark }}</th>
                    </tr>
                @endif

                <!-- Header Labels -->
                <tr class="text-center fw-bold">
                    <th>Function</th>
                    <th>PAN No.</th>
                    <th colspan="2">Bill No.</th>
                    <th colspan="3">Bill Date</th>
                </tr>

                <!-- Header Values -->
                <tr class="text-center">
                    <td>{{ $hallsale1->functionname }}</td>
                    <td>{{ $hallsale1->panno }}</td>
                    <td colspan="2">{{ $hallsale1->vtype }} {{ $hallsale1->vno }}</td>
                    <td colspan="3">{{ date('d-m-Y', strtotime($hallsale1->vdate)) }}</td>
                </tr>

                <!-- Subheaders -->
                <tr class="text-center fw-bold">
                    <th colspan="3">Name & Address</th>
                    <th>Venue Name</th>
                    <th colspan="2">From Date & Time</th>
                    <th colspan="2">To Date & Time</th>
                </tr>
            </thead>

            <!-- Data Rows -->
            <tbody>
                <tr>
                    <td colspan="3">
                        {{ $hallsale1->party }} </br>
                        {{ $hallsale1->add1 }} {{ $hallsale1->cityname }}
                    </td>
                    <td>
                        @foreach ($venueocc as $item)
                            {{ strtoupper($item->venuename) }}<br>
                        @endforeach
                    </td>
                    <td colspan="2">
                        @foreach ($venueocc as $item)
                            {{ date('d-m-Y', strtotime($item->fromdate)) }} {{ date('H:i', strtotime($item->dromtime)) }}<br>
                        @endforeach
                    </td>
                    <td colspan="2">
                        @foreach ($venueocc as $item)
                            {{ date('d-m-Y', strtotime($item->todate)) }} {{ date('H:i', strtotime($item->totime)) }}<br>
                        @endforeach
                    </td>
                </tr>

                <!-- Footer Labels -->
                <tr class="text-center fw-bold">
                    <th colspan="4">Company Details</th>
                    <th>GSTIN</th>
                    <th>State</th>
                    <th>State Code</th>
                </tr>

                <!-- Footer Data -->
                <tr>
                    @if (!empty($hallbook->companycode))
                        <td colspan="4">{{ subgroup($hallbook->companycode)->name }}</td>
                        <td>{{ subgroup($hallbook->companycode)->gstin }}</td>
                        <td>{{ subgroup($hallbook->companycode)->statename }}</td>
                        <td>{{ subgroup($hallbook->companycode)->state_code }}</td>
                    @endif
                </tr>
            </tbody>
        </table>


        <!-- Items Table -->
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 5%;">S.No.</th>
                    <th style="width: 50%;">Particular</th>
                    <th style="width: 8%;">Qty.</th>
                    <th style="width: 15%;">Rate</th>
                    <th style="width: 15%;">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><b>1.</b></td>
                    <td>{{ $hallsale1->narration }} </br><i>{{ $hallsale1->remark }}</i></td>
                    <td class="text-right">{{ $hallsale1->noofpax }}</td>
                    <td class="text-right">{{ $hallsale1->rateperpax }}</td>
                    <td class="text-right">{{ $hallsale1->totalpercover }}</td>
                </tr>

                @if ($stockitems->isNotEmpty())
                    @php
                        $index = 2;
                    @endphp
                    @foreach ($stockitems as $item)
                        <tr>
                            <td class="text-center"><b>{{ $index++ }}.</b></td>
                            <td>{{ $item->Name }} </br><i>{{ $item->remarks }}</i></td>
                            <td class="text-right">{{ $item->qtyiss }}</td>
                            <td class="text-right">{{ $item->rate }}</td>
                            <td class="text-right">{{ $item->amount }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="amount-section mt-3">
            <div class="row small-text">

                {{-- Left Column: hallsale2 --}}
                <div class="col-6">
                    <div class="d-flex justify-content-lg-start">
                        @if ($hallsale2->isNotEmpty())
                            @foreach ($hallsale2 as $item)
                                <div style="margin:0 0 0 10px;" class="mb-2">
                                    <b>{{ $item->name }}</b><br>
                                    <b>{{ $item->taxper }}%</b><br>
                                    ₹{{ number_format($item->taxamt, 2) }}<br>
                                    <b>Base</b>: ₹{{ number_format($item->basevalue, 2) }}
                                </div>
                            @endforeach
                            @foreach ($sundrytype as $item)
                                <div style="margin:0 0 0 10px;" class="mb-2">
                                    <b>{{ $item->disp_name }}</b><br>
                                    <b>{{ $item->svalue }}%</b><br>
                                    ₹{{ number_format(($hallsale1->totalpercover * $item->svalue) / 100, 2) }}<br>
                                    <b>Base</b>: ₹{{ number_format($hallsale1->totalpercover, 2) }}
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Settlement Mode --}}
                    <div class="mt-2"><strong>Settlement Mode:</strong><br>
                        @foreach ($paidrows as $item)
                            {{ $item->paytype }} : <b>{{ number_format($item->amtcr, 2) }}</b><br>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: finalData + advance --}}
                <div class="col-6 text-end">
                    <table class="table table-bordered ms-auto" style="width: 350px; font-weight: bold;">
                        @php
                            $netamount = 0.0;
                        @endphp
                        @foreach ($finalData as $item)
                            @if ($item['dispname'] == 'Net Amount')
                                @php
                                    $netamount = $item['amount'];
                                @endphp
                            @endif
                            @if ($item['amount'] > 0)
                                <tr style="background-color: #d3d3d3;">
                                    <th style="width: 200px;">{{ $item['dispname'] }}</th>
                                    <td>:</td>
                                    <td style="text-align: right;">₹{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>

                    {{-- Advance and Total In Words --}}
                    <div class="mt-4 text-end">
                        @if ($advancerows->sum('amtcr') > 0)
                            <div><strong>Advance (-): Rs.{{ number_format($advancerows->sum('amtcr'), 2) }}</strong></div>
                        @endif
                        <div><strong>Net Payable Amount: Rs. {{ number_format($netamount - $advancerows->sum('amtcr'), 2) }}</strong></div>
                        <div class="small-text"><strong>Rs. {{ amountToWords($netamount - $advancerows->sum('amtcr')) }} Only</strong></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Footer -->
        <div class="footer-section">
            <div class="row">
                <div class="col-6 small-text">
                    <div><strong>Company:</strong>
                        @if (!empty($hallbook->companycode))
                            <b>{{ subgroup($hallbook->companycode)->name }}</b>
                        @endif
                    </div>
                    <div class="mt-3">
                        <strong>Terms & Condition:</strong><br>
                        1. Interest @ 24% P.A. will be charged if amount not paid within 3 days.<br>
                        2. All Subject to {{ companydata()->city }} Jurisdiction.
                    </div>
                </div>
                <div class="col-6 text-right small-text">
                    <div><strong>For: ANALYSIS DEMONSTRATION PACKAGE</strong></div>
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <div>Prepared by: <b>{{ strtoupper($hallsale1->u_name) }}</b></div>
                    <div class="mt-3">Checked by: _______________</div>
                    <div class="mt-3">Accountants Deptt./Authorised Signatory</div>
                </div>
            </div>
            <div class="text-center mt-3 small-text">
                <strong>E. & O. E.</strong>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<script>
    setTimeout(() => {
        window.print();
    }, 1000);
</script>
