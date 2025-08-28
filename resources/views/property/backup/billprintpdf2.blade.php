@include('property.layouts.loader')
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/images/favicon.png') }}">
    <title>Bill Print {{ $guest->name }} </title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Namdhinggo:wght@400;500;600;700;800&display=swap');

        body {
            font-size: smaller !important;
        }

        .roomchargeval {
            border: none;
            background: inherit;
        }

        .roomchargeval:disabled {
            border: none;
            background: inherit;
            color: black;
        }

        #website {
            text-transform: lowercase;
        }

        .none {
            display: none;
        }

        a {
            text-decoration: none !important;
            color: #020911 !important;
        }

        a:hover {
            text-decoration: none !important;
            color: #020911 !important;
        }

        #compname {
            font-family: namdhinggo-regular;
        }

        table {
            margin-bottom: 1px !important;
        }

        .table td,
        .table th {
            padding: 2px !important;
        }


        .table-bordered td,
        .table-bordered th {
            border: 1px solid black !important;
            /* border-bottom: 1px solid #020911 !important; */
            /* border-top: 1px solid #020911 !important; */
            border-left: 1px solid #020911 !important;
            border-right: 1px solid #020911 !important;
            text-transform: capitalize;
        }

        .table-bordered>:not(caption)>* {
            border-width: 0 !important;
        }

        tbody#billdetails td {
            border: none !important;
        }

        .signimage {
            display: flex;
            justify-content: end;
        }

        tbody#billdetails td {
            border-left: 1px solid #020911 !important;
            border-right: 1px solid #020911 !important;
        }

        tbody#billdetails tr:last-child,
        {
        border-bottom: 1px solid #020911 !important;
        }

        #taxdivision p:last-child {
            border-bottom: 1px solid black;
        }

        /* .table tbody+tbody {
            border: 1px solid black !important;
        } */

        span {
            font-weight: 400 !important;
            /* text-transform: capitalize; */
        }

        .payment-details label {
            margin-right: 10px;
            margin-bottom: 0;
        }

        .payment-details input[type="checkbox"] {
            margin-right: 5px;
        }

        .payment-details p,
        span#netamt,
        span#roomno2 {
            text-indent: 4px;
        }

        table th {
            text-align: inherit;
            font-weight: 600;
        }

        table#lightdark th,
        table#billdetails th {
            background: #cbd0d5;
        }

        img {
            position: absolute;
            width: 142px;
            height: 75px;
        }

        p {
            margin: 0 !important;
            font-weight: 500;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0%;
        }

        #end {
            text-align: center;
        }

        .signnn {
            margin-top: 7em;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body {
                font-size: smaller !important;
            }

            .signimage {
                display: flex;
                justify-content: end;
            }

            body::after {
                content: none !important;
            }

            .table-bordered>:not(caption)>* {
                border-width: 0 !important;
            }

            .signnn {
                margin-top: 7em;
                display: flex;
                justify-content: space-between;
            }

            tbody#billdetails td {
                border: none !important;
            }

            tbody#billdetails td {
                border-left: 1px solid #020911 !important;
                border-right: 1px solid #020911 !important;
            }

            tbody#billdetails tr:last-child,
            {
            border-bottom: 1px solid #020911 !important;
        }

        #taxdivision p:last-child {
            border-bottom: 1px solid black;
        }

        img {
            position: absolute;
        }

        #taxdivision p:last-child {
            border-bottom: 1px solid black;
        }

        tbody#billdetails td {
            border: .1px solid black !important;
        }

        .table tbody+tbody {
            border: 1px solid black !important;
        }

        span {
            font-weight: 400 !important;
            /* text-transform: capitalize; */
        }

        .payment-details label {
            margin-right: 10px;
            margin-bottom: 0;
        }

        .payment-details input[type="checkbox"] {
            margin-right: 5px;
        }

        .payment-details p,
        span#netamt,
        span#roomno2 {
            text-indent: 4px;
        }

        table th {
            text-align: inherit;
            font-weight: 600;
        }

        table#lightdark th,
        table#billdetails th {
            background: #cbd0d5;
        }

        img {
            position: absolute;
        }

        p {
            margin: 0 !important;
            font-weight: 500;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0%;
        }

        #end {
            text-align: center;
        }

        p {
            margin: 0;
            font-weight: 500;
        }

        }

        .payamts {
            border: 2px ridge #676767a1;
            border-radius: 5px;
            padding: 0 1px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{--
    {{--
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> --}}
</head>

<body>
    <p class="none" id="propertyid">{{ $company->propertyid }}</p>
    <p class="none" id="folionodocid">{{ $guest->docid }}</p>
    <p class="none" id="sno1">{{ $guest->sno1 }}</p>
    <p class="none" id="billno">{{ $paycharger->billno }}</p>
    <p class="none" id="totalbalance">{{ $totalbalance }}</p>
    <p class="none" id="totalroomcharge">{{ $totalroomcharge }}</p>
    <p class="none" id="taxsummary">{{ $taxsummary }}</p>
    <p class="none" id="billprintingsummerised">{{ $billprintingsummerised }}</p>
    <p class="none" id="splitval">{{ $paycharger->split }}</p>
    <div class="container-fluid">
        <div class="logoimg">
            <img alt="analysishms" class="" id="complogo" src="storage/admin/property_logo/{{ $company->logo }}">
        </div>
        <h5 id="compname" class="text-center">{{ $company->comp_name }}</h5>

        <div class="d-flex justify-content-between">
            <div class="name">
                ‎ ‎
            </div>
            <div class="text-center" style="margin-left: 11em;">
                <p class="text-center"><span id="address1"></span></p>
                <p class="text-center"><span id="address2"></span></p>
                <p class="text-center"><span id="city"></span></p>
                <p id="tagemail">E-mail: <span id="email"></span></p>
                <p>Mobile: <span id="phone">{{ $company->mobile }}</span></p>
                <p id="tagwebsite">Website: <span id="website"></span></p>
                <p>TAX INVOICE</p>
            </div>
            <div>
                <p>
                <p>GST IN: <span id="gstin"></span></p>
                <p>
                <p>SAC Code: <span id="sascode">996331</span></p>
                <p>SAC Food Code: <span id="sascode">996332</span></p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <th>G.R.C. No.</th>
                    <td id="grcno"></td>
                    <th id="invoicetext">Invoice No.</th>
                    <td id="invoiceno">{{ $invoiceno }}</td>
                    <th>Room No.</th>
                    <td id="roomno"></td>
                    <th>Invoice Date.</th>
                    <td id="invoicedate"></td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <th>Pax</th>
                    <th>Room Disc</th>
                    <th>Room Type</th>
                    <th>Nationality</th>
                    <th>Arrival Date & Time</th>
                    <th>Departure Date & Time</th>
                    <th>Plan/Package</th>
                <tr>
                    <td><span id="adult"></span>/<span id="children"></span></td>
                    <td id="rodisc"></td>
                    <td id="categname"></td>
                    <td id="nationality"></td>
                    <td><span id="arrdate"></span> <span id="arrtime"></span></td>
                    <td><span id="depdate"></span> <span id="deptime"></span></td>
                    <td id="package"></td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <th>Guest Details</th>
                    <th>Company Details</th>
                    <th>Traveller Details</th>
                </tr>
                <tr>
                    <td>
                        <p>Guest Name: <span id="guestname"></span></p>
                        <p>Address: <span id="addressclient"></span></p>
                        <p>City: <span id="cityclient"></span></p>
                        <p>State: <span id="stateclient"></span></p>
                        <p>Mobile No.: <span id="mobno"></span></p>
                    </td>
                    <td>
                        <p>Company: <span id="subname"></span></p>
                        <p>Address: <span id="subaddress"></span></p>
                        <p>GSTIN: <span id="subgstin"></span></p>
                        <p>State: <span id="substatename"></span></p>
                        <p>State Code: <span id="substatecode"></span></p>
                    </td>
                    <td>
                        <p>Agency: <span id="travelname"></span></p>
                        <p>Address: <span id="traveladdress"></span></p>
                        <p>GSTIN: <span id="travelgstin"></span></p>
                        <p>State: <span id="travelstatename"></span></p>
                        <p>State Code: <span id="travelstatecode"></span></p>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Bill/Voucher</th>
                        <th>Description</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>
                </thead>
                <tbody id="billdetails">
                    @foreach ($charged as $ledger)
                        <tr>
                            <td style="white-space: nowrap;">{{ date('d-m-Y', strtotime($ledger['vdate'])) }}</td>
                            <td>{{ $ledger['vtype'] }}/{{ $ledger['vno'] }}</td>
                            <td>{{ $ledger['comments'] }}</td>
                            <td>{{ $ledger['amtdr'] ?? '0' }}</td>
                            <td>{{ $ledger['amtcr'] ?? '0' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <td class="payment-details d-flex">
                        {{-- <p>Payment Mode:
                            <label>Cash</label><input type="checkbox" value="Cash">
                            <label>Credit Card</label><input type="checkbox" value="Credit Card">
                            <label>Bill to Company</label><input type="checkbox" value="Bill to Company">
                        </p> --}}
                        {{-- <p>Room Details: </p>
                        <span id="roomno2"> </span> --}}
                    </td>

            </table>
        </div>

        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <td>
                        <p>In Words: <span id="rupeewords"></span></p>
                        <p>User Name: <span id="username"></span></p>
                        <p style="display: none;" class="bankdetails">Account Name: <span id="acname"></span></p>
                        <p style="display: none;" class="bankdetails">Account No.: <span id="acnum"></span></p>
                        <p style="display: none;" class="bankdetails">Bank Name: <span id="bankname"></span></p>
                        <p style="display: none;" class="bankdetails">IFSC Code: <span id="ifsccode"></span></p>
                        <p style="display: none;" class="bankdetails">Branch Name: <span id="branchname"></span></p>
                    </td>
                    <td>
                        <p>TOTAL: <span id="totalsumdebit">{{ $totalroomcharge }}</span></p>
                        <div id="taxdivision">
                        </div>
                        <p>TOTAL: <span id="totalaftertax"></span></p>
                        <p>ADVANCE & OTHER CREDIT: <span id="totalcredit"></span></p>
                        <p style="border-bottom: 1px solid black;">Round Off: <span id="roundoff"></span></p>
                        <p>NET AMOUNT: <span id="netamount"></span></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <td class="text-center">PLEASE RETURN YOUR KEY ON DEPARTURE</td>
                </tr>
            </table>
        </div>

        {{-- <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <th>Outlet</th>
                    <th>Goods</th>
                    <th>GST%</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>Bill Total</th>

                    <th>Room Charge DETAILS</td>
                    <th>CGST(SALES)</th>
                    <th>SGST(SALES)</th>

                <tr>
                    <td>LITTLE CHEF</td>
                    <td>650.00</td>
                    <td>2.50</td>
                    <td>16.25</td>
                    <td>16.25</td>
                    <td>683.00</td>

                    <td></td>
                    <td>
                        <p>6%</p>
                    </td>
                    <td>
                        <p>6%</p>
                    </td>
                </tr>
                <tr>
                    <td>LAUNDRY</td>
                    <td>135.00</td>
                    <td>9.00</td>
                    <td>12.15</td>
                    <td>12.15</td>
                    <td>160.00</td>

                    <td></td>
                    <td>270.00</td>
                    <td>270.00</td>
                </tr>
                <tr>
                    <td>ROOM SERVICE</td>
                    <td>980.00</td>
                    <td>2.50</td>
                    <td>24.50</td>
                    <td>24.50</td>
                    <td>1,029.00</td>

                    <td></td>
                    <td>4500.00</td>
                    <td>4500.00</td>
                </tr>
                </th>
                </tr>
            </table>
        </div> --}}

        <div class="table-responsive">
            <table id="lightdark" class="table table-bordered">
                <tr>
                    <td>
                        <div class="signimage">

                        </div>
                        <div class="signnn">
                            <p>Cashier's Signature</p>
                            <p>Guest's Signature</p>
                        </div>
                        <p class="text-center">-----------------------------------------Thank You for
                            Honouring us by your
                            visit-----------------------------------------
                        </p>
                        <p style="text-align:center;font-weight: 400">(Subject to <span id="citynamed"></span>
                            Juridiction)</p>
                    </td>
                </tr>
            </table>
        </div>
        <p style="font-weight: 400">Analysis Software Services - <a href="tel:9161380170">9161380170</a></p>

    </div>
</body>

</html>
<script src="{{ asset('admin/js/custom.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#myloader').removeClass('none');

        function updateLoader(imageSrc, text, delay) {
            return new Promise(resolve => {
                setTimeout(() => {
                    $('#myloader').find('img').attr('src', imageSrc);
                    $('#myloader').find('#loader-text').html(text);
                    resolve();
                }, delay);
            });
        }

        async function runLoaderSequence() {
            await updateLoader('admin/icons/custom/notepad.gif', 'Writing Guest Name...', 0);
            await updateLoader('admin/icons/custom/growth.gif', 'Calculating Amount...', 1000);
            await updateLoader('admin/icons/custom/tax.gif', 'Adding Taxes...', 2000);
            await updateLoader('admin/icons/custom/typewriter.gif', 'Bill Generated...', 3000);
        }

        runLoaderSequence();
        setTimeout(() => {
            // $('#billdetails tr').each(function() {
            //     let billprintingsummerised = $('#billprintingsummerised').text();
            //     let secondTd = $(this).find('td:eq(1)');
            //     secondTd.removeClass('none');

            //     $('.reprintrooomval').attr('disabled', true).css({
            //         'background': 'white',
            //         'border': 'none',
            //         'color': 'black'
            //     });

            //     let lastThreeTd = $(this).find('td').slice(-5);
            //     lastThreeTd.remove();
            //     let taxsummary = $('#taxsummary').text();
            //     if ($(this).attr('removable') == 'T' && taxsummary == 'Y') {
            //         $(this).remove();
            //     }
            // });

            let propertyid = $('#propertyid').text();
            let email = $('#email').text();
            let docid = $('#folionodocid').text();
            let sno1 = $('#sno1').text();
            let billno = $('#billno').text();
            let totalsumdebit = $('#totalsumdebit').text();
            let totalbalance = $('#totalbalance').text();
            let totalroomcharge = $('#totalroomcharge').text();
            let splitval = $('#splitval').text();
            if (splitval > 1) {
                $('#invoicetext').text('Reference No.')
            }
            var xhrledger = new XMLHttpRequest();
            xhrledger.open('POST', '/getcompdetails', true);
            xhrledger.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhrledger.onreadystatechange = function() {
                if (xhrledger.readyState === 4 && xhrledger.status === 200) {
                    var result = JSON.parse(xhrledger.responseText);

                    $('#gstin').text(result.gstin);
                    if (result.acnum != '') {
                        $('.bankdetails').css('display', 'block');
                        $('#acname').text(result.acname);
                        $('#acnum').text(result.acnum);
                        $('#bankname').text(result.bankname);
                        $('#ifsccode').text(result.ifsccode);
                        $('#branchname').text(result.branchname);
                    } else {
                        $('.bankdetails').css('display', 'none');
                    }
                    $('#email').text(result.email.toLowerCase());
                    $('#citynamed').text(result.city);
                    $('#website').text(result.website);

                    if (result.logoyn == 'N') {
                        $('#complogo').css('display', 'none');
                    }
                    if (result.websiteyn == 'N') {
                        $('#tagwebsite').css('display', 'none');
                    }
                    if (result.emailyn == 'N') {
                        $('#tagemail').css('display', 'none');
                    }
                }
            }
            xhrledger.send(`propertyid=${propertyid}&_token={{ csrf_token() }}`);

            xhrroomoccdata = new XMLHttpRequest();
            xhrroomoccdata.open('POST', 'getroomoccdata', true);
            xhrroomoccdata.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhrroomoccdata.onreadystatechange = function() {
                if (xhrroomoccdata.readyState === 4 && xhrroomoccdata.status === 200) {
                    let result = JSON.parse(xhrroomoccdata.responseText);
                    var year = result.vprefix;
                    var financialYear = year + '-' + (parseInt(year) + 1).toString().slice(-2);
                    let comp_code = result.companycode;
                    let travelcode = result.guesttravel
                    let chkoutfix;
                    if (result.chkoutdate != null) {
                        chkoutfix = new Date(result.chkoutdate).toLocaleDateString('en-GB').replace(/\//g, '-');
                    } else {
                        chkoutfix = '';
                    }

                    $('#depdate').text(chkoutfix);
                    $('#invoicedate').text(chkoutfix);
                    $('#deptime').text(result.chkouttime);
                    // Sub group Request Start
                    xhrsubgroupdata = new XMLHttpRequest();
                    xhrsubgroupdata.open('POST', 'getsubgroupdata', true);
                    xhrsubgroupdata.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhrsubgroupdata.onreadystatechange = function() {
                        if (xhrsubgroupdata.readyState === 4 && xhrsubgroupdata.status === 200) {
                            let result = JSON.parse(xhrsubgroupdata.responseText);
                            $('#subname').text(result.subname);
                            $('#subaddress').text(result.subaddress);
                            $('#subgstin').text(result.subgstin);
                            $('#substatename').text(result.substatename);
                            $('#substatecode').text(result.substatecode);
                        }
                    }
                    xhrsubgroupdata.send(`comp_code=${comp_code}&_token={{ csrf_token() }}`);
                    // Sub group Request End
                    // Travel Agent Request Start
                    xhrtraveldata = new XMLHttpRequest();
                    xhrtraveldata.open('POST', 'gettraveldata', true);
                    xhrtraveldata.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhrtraveldata.onreadystatechange = function() {
                        if (xhrtraveldata.readyState === 4 && xhrtraveldata.status === 200) {
                            let result = JSON.parse(xhrtraveldata.responseText);
                            $('#travelname').text(result?.travelname ?? '');
                            $('#traveladdress').text(result?.traveladdress ?? '');
                            $('#travelgstin').text(result?.travelgstin ?? '');
                            $('#travelstatename').text(result?.travelstatename ?? '');
                            $('#travelstatecode').text(result?.travelstatecode ?? '');
                        }
                    }
                    xhrtraveldata.send(`travelcode=${travelcode}&_token={{ csrf_token() }}`);
                    // Travel Agent Request End
                    $('#grcno').text(result.folioNo);
                    $('#finyear').text(financialYear);
                    $('#roomno').text(result.roomkanam);
                    // $('#roomno2').text(result.roomkanam);
                    $('#children').text(result.children);
                    $('#adult').text(result.adult);
                    $('#rodisc').text(result.rodisc);

                    $('#categname').text(result.categname);
                    $('#nationality').text(result.nationality);
                    $('#arrdate').text(new Date(result.chkindate).toLocaleDateString('en-GB').replace(/\//g, '-'));
                    $('#arrtime').text(result.chkintime);
                    $('#package').text(result.plankanam ?? 'EP');
                    $('#guestname').text(result.name);
                    let addstr = `${result.add1 ?? ''}${result.add2 != null ? ', ' + result.add2 : ''}`;
                    $('#addressclient').text(addstr);
                    $('#cityclient').text(result.city_name);
                    $('#stateclient').text(result.state_name);
                    $('#mobno').text(result.mobile_no);
                    setTimeout(() => {
                        $('#myloader').removeClass('d-flex');
                        $('#myloader').addClass('none');
                        // window.print();
                    }, 500);
                }
            }
            xhrroomoccdata.send(`docid=${docid}&_token={{ csrf_token() }}`);

            // Amount Fetch Request Start
            xhramountfetch = new XMLHttpRequest();
            xhramountfetch.open('POST', 'getamountfetch2', true);
            xhramountfetch.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhramountfetch.onreadystatechange = function() {
                if (xhramountfetch.readyState === 4 && xhramountfetch.status === 200) {
                    let result = JSON.parse(xhramountfetch.responseText);
                    let paymentname = result.paymentname;
                    let formatpayment = paymentname
                        .map(payment => `<p>${payment.name}</p>: <span class='payamts'> ${payment.amt}</span>`)
                        .join(', ');
                    let rooms = result.rooms;
                    let froom = rooms.map(r => `${r.roomno}`).join(', ');
                    $('.payment-details').prepend(`<p>Payment Mode: </p>${formatpayment} <p>Rooms: </p>${froom}`);
                    let taxdivision = $('#taxdivision');
                    // $('#totalsumdebit').text(result.betotal);
                    $('#totalaftertax').text(result.toalaftertaxadd);
                    $('#totalcredit').text(result.creditsum);
                    $('#netamount').text(Math.ceil(result.netamount).toFixed(2));
                    $('#roundoff').text(result.roundoff);
                    $('#rupeewords').text(inWords(Math.ceil(result.netamount)));
                    $('#username').text(result.u_name);
                    result.taxname.forEach((tax, index) => {
                        let taxedamount = result.taxedamount[index];
                        $('#taxdivision').append(`<p>${tax}:<span>${taxedamount}</span></p>`);
                    });

                }
            }
            xhramountfetch.send(`splitval=${splitval}&docid=${docid}&sno1=${sno1}&totalroomcharge=${totalroomcharge}&totalsumdebit=${totalsumdebit}&totalbalance=${totalbalance}&billno=${billno}&_token={{ csrf_token() }}`);
        }, 3000);
        // Amount Fetch Request End
    });
    var a = ['', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ', 'ten ', 'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ', 'nineteen '];
    var b = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

    function inWords(num) {
        if ((num = num.toString()).length > 9) return 'overflow';
        n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
        if (!n) return;
        var str = '';
        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
        str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
        return str;
    }


    function fetchncur() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/ncurfetch', false);
        xhr.send();
        if (xhr.status === 200) {
            var date = xhr.responseText;
            var formattedDate = new Date(date).toLocaleDateString('en-GB');
            return formattedDate;
        } else {
            console.error('Failed to fetch Ncur Date. Status:', xhr.status);
            return null;
        }
    }
    let ncurdate = fetchncur();
    let curtime = getCurrentTimeIndia();
</script>
