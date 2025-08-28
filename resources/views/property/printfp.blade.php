<!DOCTYPE html>
<html>

<head>
    <title>Function Prospectus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        .header,
        .section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header td,
        .section td {
            padding: 4px;
            vertical-align: top;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }

        .company-name {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }

        .company-address {
            text-align: center;
            font-size: 12px;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        .table-border {
            border: 1px solid #000;
            width: 100%;
            border-collapse: collapse;
        }

        .table-border td {
            border: 1px solid #000;
            padding: 5px;
        }

        ol {
            margin: 5px 0 0 15px;
            padding: 0;
        }

        .signature {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="company-name">{{ $company->comp_name ?? 'COMPANY NAME' }}</div>
    <div class="company-address">{{ $company->address1 ?? '' }}</div>
    <div class="company-address">{{ $company->address2 ?? '' }}</div>
    <div class="company-address">{{ $statename ?? '' }}</div>
    <div class="title">FUNCTION PROSPECTUS</div>
    <div style="text-align:right;"><b>FP No:</b> {{ $hallbookData->docid ?? '-' }}</div>
    <div class="line"></div>

    <!-- Event Info -->
    <table class="header">
        <tr>
            <td class="bold">DATE</td>
            <td>{{ $hallbookData->vdate ? \Carbon\Carbon::parse($hallbookData->vdate)->format('d/M/Y') : '-' }}</td>
            <td class="bold">DAY</td>
            <td>{{ $hallbookData->vdate ? \Carbon\Carbon::parse($hallbookData->vdate)->format('l') : '-' }}</td>
            <td class="bold">TIME</td>
            <td>{{ $hallbookData->vtime ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bold">TYPE OF FUNCTION</td>
            <td>{{ $hallbookData->func_name ?? '-' }}</td>
            <td class="bold">VENUE</td>
            <td colspan="3">
                @if ($venueData->count() > 0)
                    {{ $venueData->pluck('VenuName')->implode(', ') }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>
    <hr style="border: 1px solid black; width: 100%;">
    <!-- Party Info -->
    <table class="section">
        <tr>
            <td class="bold">NAME OF THE GROUP/PERSON</td>
            <td>{{ $hallbookData->partyname ?? '-' }}</td>
            <td class="bold">PAN NO.</td>
            <td>{{ $hallbookData->panno ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bold">ADDRESS</td>
            <td colspan="3">{{ trim($hallbookData->add1 . ' ' . $hallbookData->add2) }}</td>
        </tr>
        <tr>
            <td class="bold">CONTACT NO.</td>
            <td colspan="3">{{ $hallbookData->mobileno ?? '-' }}</td>
        </tr>
    </table>
    <hr style="border: 1px solid black; width: 100%;">

    <!-- Payment & PAX -->
    <table class="section">
        <tr>
            <td class="bold">MODE OF PAYMENT</td>
            <td>{{ $hallbookData->PaymentMode ?? 'Cash' }}</td>
            <td class="bold">ADVANCE AMOUNT</td>
            <td>{{ number_format($advanceData->sum('Adv'), 2) }}</td>
            <td class="bold">R.T.NO. & DATE</td>
            <td>{{ $hallbookData->RTNo ?? '-' }} & {{ $hallbookData->vdate ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bold">GUARANTEED PAX</td>
            <td>{{ $hallbookData->guaratt ?? '-' }}</td>
            <td class="bold">EXPECTED PAX</td>
            <td>{{ $hallbookData->expatt ?? '-' }}</td>
            <td class="bold">VARIANCE</td>
            <td>{{ ($hallbookData->expatt ?? 0) - ($hallbookData->guaratt ?? 0) }}</td>
        </tr>
        <tr>
            <td class="bold">RATE PER PAX</td>
            <td colspan="5">{{ number_format($hallbookData->coverrate ?? 0, 2) }}</td>
        </tr>
    </table>
    <hr style="border: 1px solid black; width: 100%;">

    <!-- Menu Section -->
    <div class="bold">MENU :</div>
    <table class="table-border">
        <tr>
            <td class="bold">Item Name</td>
            <td class="bold">Remarks</td>
        </tr>
        <tr>
            <td colspan="2">remark</td>
        </tr>
    </table>

    <!-- Special Instructions -->
    <hr style="border: 1px solid black; width: 100%;">
    @if ($hallbookData->menuspl1 || $hallbookData->menuspl2 || $hallbookData->menuspl3)
        <div class="bold">SPECIAL INSTRUCTIONS :</div>
        <ul>
            @foreach (range(1, 7) as $i)
                @php $field = 'menuspl'.$i; @endphp
                @if (!empty($hallbookData->$field))
                    <li>{{ $hallbookData->$field }}</li>
                @endif
            @endforeach
        </ul>
    @endif

    <hr style="border: 1px solid black; width: 100%;">


    <!-- Board to Read -->
    <p class="bold">BOARD TO READ: <span style="font-weight:normal;">{{ $hallbookData->board ?? '-' }}</span></p>

    <!-- Terms -->
    <div class="bold">TERMS & CONDITIONS :</div>
    <ol>
        <li>NO OUTSTATION CHEQUE WILL BE ACCEPTED.</li>
        <li>WE AGREE TO SETTLE THE BILLS IMMEDIATELY AFTER THE CLOSING OF THE FUNCTION AS AGREED UPON.</li>
        <li>WE AGREE TO DEPOSIT ADVANCE OF THE GUARANTEED NO. OF PERSONS BEFORE THE DATE OF THE FUNCTION.</li>
        <li>THE HOTEL IS NOT RESPONSIBLE FOR VARIATION OF MORE THAN 10% OF NO. OF EXPECTED PAX IF NOT INFORMED WELL IN ADVANCE.</li>
        <li>E. & O.E.</li>
    </ol>

    <hr style="border: 1px solid black; width: 100%;">

    <p class="bold">NET RATE PER PAX : RATES, TERMS & CONDITIONS, READ & ACCEPTED</p>

    <!-- Signature -->
    <div class="signature">
        <span>(SIG. OF GUEST)</span>
        <span style="float:right;">BANQUET MANAGER</span>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>

</html>
