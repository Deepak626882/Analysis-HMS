<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Bill Receipt</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/images/favicon.png') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .none {
            display: none;
        }

        .receipt {
            width: 72mm;
            padding: 10px;
            margin: 0 auto 17px auto;
            background: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
            /* Ensuring relative positioning for the overlay */
        }

        header {
            text-align: center;
            margin-bottom: 10px;
        }

        .line {
            border-bottom: 1px dashed;
            margin: 4px 0 4px 0;
        }

        footer .line {
            border-bottom: 1px dashed;
            margin: 12px 0 0 0 !important;
        }

        header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }

        header h2 {
            font-size: 14px;
            margin: 5px 0;
            font-weight: normal;
        }

        header p {
            margin: 0;
            font-size: 11px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .receipt-details,
        footer {
            margin-bottom: 10px;
        }

        .receipt-details p,
        footer p {
            margin: 0;
            line-height: 1.4;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            font-size: 11px;
            text-align: left;
            padding: 1px 4px 0 3px;
        }

        table.items tbody {
            border-bottom: 1px dashed;
        }

        table.items th {
            border-bottom: 1px dashed;
            font-weight: bold;
        }

        table tfoot td {
            font-weight: bold;
            padding-top: 5px;
        }

        .right-align {
            text-align: right;
        }

        p {
            margin: 0rem;
        }

        #customerdiv h3,
        #companydiv h3 {
            border-bottom: 1px dashed;
            font-size: 13px;
            padding: 0;
            margin: 0 0 2px 0;
        }

        .d-flex {
            display: flex;
        }

        .justify-space-between {
            justify-content: space-between;
        }

        .bold {
            font-weight: 700;
        }

        .cancel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1;
            overflow: hidden;
        }

        .cancel-text {
            font-size: 48px;
            font-weight: bold;
            color: red;
            text-transform: uppercase;
            animation: cancel-animation 1.5s linear infinite;
            white-space: nowrap;
        }

        @keyframes cancel-animation {
            0% {
                transform: translateY(100%);
            }

            100% {
                transform: translateY(-100%);
            }
        }

        @media print {
            body {
                background: none;
            }

            .receipt {
                box-shadow: none;
                position: relative;
            }

            .cancel-text {
                font-size: 36px;
            }

            .none {
                display: none;
            }

            table th,
            table td {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <p class="none" id="roomno"></p>
    <p class="none" id="billno"></p>
    <p class="none" id="vdate"></p>
    <p class="none" id="vtype"></p>
    <p class="none" id="outletcode"></p>
    <p class="none" id="departnature"></p>
    <p class="none" id="addeddocid"></p>
    <div class="receipt">
        <div class="cancel-overlay">
            <div class="cancel-text">Cancelled Cancelled Cancelled</div>
        </div>
        <header>
            <div style="display: flex;justify-content: space-between;">
                <img style="width: 70px;" src="" id="logo" name="logo" alt="Hotel Logo">
                <div>
                    <h1 id="comp_name"></h1>
                    <h2 id="departname"></h2>
                </div>
            </div>
            <p><strong></strong> <span id="address1"></span></p>
            <p><strong></strong> <span id="address2"></span></p>
            <p><strong></strong> <span id="city"></span></p>
            <p><strong>Mob:</strong> <span id="mobile"></span></p>
            <p><strong>Email:</strong> <span id="email"></span></p>
            <p><strong>Website: </strong><span id="website"></span></p>
            <p><strong>GSTIN: </strong><span id="gstin"></span><strong> SAC Code: </strong><span>996332</span></p>
        </header>
        <div class="line"></div>
        <section class="receipt-details">
            <div class="details-row">
                <p><strong>Bill No:</strong> <span id="billnoshow"></span></p>
                <p><strong>Bill Date:</strong> <span id="fixvdate"></span> <span id="curtime"></span></p>
            </div>
            <div class="details-row">
                <p><strong><span id="tableorroom"></span>:</strong> <span id="tableroom"></span></p>
                <p><strong>KOT No.:</strong> <span id="kotno"></span></p>
            </div>
        </section>
        <div id="customerdiv">
            <h3 class="text-center m-0">Customer Details</h3>
            <table id="customerdetail">
                <tr id="guestnameth">
                    <th>Customer Name: </th>
                    <td id="guestname"></td>
                </tr>
                <tr id="guestaddth">
                    <th>Customer Address: </th>
                    <td id="guestadd"></td>
                </tr>
                <tr id="guestmobileth">
                    <th>Customer Mobile: </th>
                    <td id="guestmobile"></td>
                </tr>
                <tr id="guestcityth">
                    <th>Customer City: </th>
                    <td id="guestcity"></td>
                </tr>
            </table>
        </div>
        <div id="companydiv">
            <h3 class="text-center m-0">Company Details</h3>
            <table id="companydetails">
                <tr id="guestcompanyth">
                    <th>Company Name: </th>
                    <td id="guestcompany"></td>
                </tr>
                <tr id="companygstth">
                    <th>Company GSTIN: </th>
                    <td id="companygst"></td>
                </tr>
                <tr id="companyaddressth">
                    <th>Company Address: </th>
                    <td id="companyaddress"></td>
                </tr>
                <tr id="compstatenameth">
                    <th>Company State: </th>
                    <td id="compstatename"></td>
                </tr>
                <tr id="compstatecodeth">
                    <th>Company State Code: </th>
                    <td id="compstatecode"></td>
                </tr>
                <tr id="compcitynameth">
                    <th>Company City: </th>
                    <td id="compcityname"></td>
                </tr>
            </table>
        </div>
        <div class="line"></div>
        <table id="items" class="table">
            <thead style="border-bottom: 1px dashed;margin: 4px 0 4px 0;">
                <tr>
                    <th>Particulars</th>
                    <th class="right-align">Qty</th>
                    <th class="right-align">Rate</th>
                    <th class="right-align">Amount</th>
                </tr>
            </thead>
            <tbody style="border-bottom: 1px dashed;margin: 4px 0 4px 0;">

            </tbody>
            <tfoot>
            </tfoot>
        </table>
        <div class="cancel-overlay">
            <div class="cancel-text">Cancelled Cancelled Cancelled</div>
        </div>
        <div class="line"></div>
        <div id="grouptaxes" style="margin: 4px 0;" class="d-flex text-center">
            <div class="line"></div>
        </div>
        <footer>
            <p><strong>Steward Name:</strong> <span id="waiter"></span></p>
            <p><strong>Cashier:</strong> <span id="cashier"></span></p>
            <p>Analysis Software Services - 9161380170</p>
            <p class="bold">Guest Signature: _________________________</p>
            <div id="slogan">

            </div>
            <div class="line"></div>
        </footer>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<script>
    $(document).ready(function() {
        setTimeout(() => {
            var divcode;
            var sale1docid;
            var yearmanage;
            let outletcode = $('#outletcode').text();
            var outletcompany = '';
            let outletxhr = new XMLHttpRequest();
            outletxhr.open('POST', '/getoutletdetails', true);
            outletxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            outletxhr.onreadystatechange = function() {
                if (outletxhr.readyState === 4 && outletxhr.status === 200) {
                    let results = JSON.parse(outletxhr.responseText);
                    let outlet_title = results.outlet_title;
                    let company_title = results.company_title;
                    divcode = results.divcode;
                    outletcompany = results.companyname;
                    $('#departname').css('display', outlet_title == 'N' ? 'none' : 'block');
                    $('#comp_name').css('display', company_title == 'N' ? 'none' : 'block');
                    $('#comp_name').text(outletcompany);
                    $('#gstin').text(results.gstin);
                    $('#logo').attr('src', `storage/admin/property_logo/${results.logo}`);
                    let slogan1 = results.slogan1;
                    let slogan2 = results.slogan2;

                    let slogans = `<p>${slogan1 ?? ''}</p>
                    <p>${slogan2 ?? ''}</p>`;
                    $('#slogan').append(slogans);
                }
            }
            outletxhr.send(`dcode=${outletcode}&_token={{ csrf_token() }}`);
            let compdetailxhr = new XMLHttpRequest();
            compdetailxhr.open('GET', '/getcompdetail', true);
            compdetailxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            compdetailxhr.onreadystatechange = function() {
                if (compdetailxhr.readyState === 4 && compdetailxhr.status === 200) {
                    let results = JSON.parse(compdetailxhr.responseText);
                    results = results.comp;
                    if (outletcompany == '') {
                        $('#comp_name').text(results.comp_name);
                        $('#gstin').text(results.gstin);
                        $('#logo').attr('src', `storage/admin/property_logo/${results.logo}`);
                    }
                    $('#address1').text(`${results.address1}`);
                    $('#address2').text(`${results.address2 ?? ''}`);
                    $('#city').text(results.city);
                    $('#state').text(results.state);
                    $('#mobile').text(results.mobile);
                    $('#email').text(results.email);
                    $('#website').text(results.website);
                }
            }
            compdetailxhr.send();

            let roomno = $('#roomno').text();

            let billno = $('#billno').text();
            let vdate = $('#vdate').text();
            let vtype = $('#vtype').text();
            let departnature = $('#departnature').text();
            let fetchcompdt = new XMLHttpRequest();
            fetchcompdt.open('POST', '/fetchcompdt', true);
            fetchcompdt.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            fetchcompdt.onreadystatechange = function() {
                if (fetchcompdt.readyState === 4 && fetchcompdt.status === 200) {
                    let result = JSON.parse(fetchcompdt.responseText);
                    if (result != null) {
                        $('#guestcompany').text(result.compname ?? '');
                        if (!result.compname) {
                            $('#guestcompanyth').css('display', 'none');
                        }

                        $('#compstatename').text(result.compstatename ?? '');
                        if (!result.compstatename) {
                            $('#compstatename').css('display', 'none');
                            $('#compstatenameth').css('display', 'none');
                        }

                        $('#compstatecode').text(result.compstatecode ?? '');
                        if (!result.compstatecode) {
                            $('#compstatecodeth').css('display', 'none');
                        }

                        $('#companygst').text(result.gstin ?? '');
                        if (!result.gstin) {
                            $('#companygstth').css('display', 'none');
                        }

                        $('#companyaddress').text(result.address ?? '');
                        if (!result.address) {
                            $('#companyaddressth').css('display', 'none');
                        }

                        $('#compcityname').text(result.compcityname ?? '');
                        if (!result.compcityname) {
                            $('#compcitynameth').css('display', 'none');
                        }
                    } else {
                        $('#companydiv').addClass('none');
                    }
                }
            }
            fetchcompdt.send(`billno=${billno}&vtype=${vtype}&_token={{ csrf_token() }}`);

            $('#fixvdate').text(dmy($('#vdate').text()));
            let str = '';
            $.get("/yearmanage", function(response) {
                yearmanage = response;
                let prefix = vtype;

                if (divcode != '') {
                    prefix = divcode;
                }
                if (departnature.toLowerCase() == 'outlet') {
                    str = `${prefix}/${yearmanage.hf.start}-${parseInt(yearmanage.hf.end)}/${billno}`;
                    billdisplaytext = 'Table';
                    $('#tableroom').text(roomno);
                } else if (departnature.toLowerCase() == 'room service') {
                    str = `${prefix}/${yearmanage.hf.start}-${parseInt(yearmanage.hf.end)}/${billno}`;
                    billdisplaytext = 'Room'
                    $('#tableroom').text(roomno);
                }
                $('#billdisplaytext').text(billdisplaytext);
                $('#billnoshow').text(str);
            }, "json");

            // Fetch Items Row
            let tbody = $('#items tbody');
            tbody.empty();
            let tfoot = $('#items tfoot');
            tfoot.empty();
            let grouptaxesdiv = $('#grouptaxes');
            grouptaxesdiv.empty();
            let itemsxhr = new XMLHttpRequest();
            itemsxhr.open('POST', '/salebillprintitems', true);
            itemsxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            itemsxhr.onreadystatechange = function() {
                if (itemsxhr.readyState === 4 && itemsxhr.status === 200) {
                    let results = JSON.parse(itemsxhr.responseText);
                    if (results.length < 1) {
                        window.location.href = 'company';
                        return;
                    }

                    sale1docid = results.sale1.docid;
                    let addeddocid = $('#addeddocid').text();
                    let guestdtxhr = new XMLHttpRequest();
                    guestdtxhr.open('POST', '/guestdtfetch', true);
                    guestdtxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    guestdtxhr.onreadystatechange = function() {
                        if (guestdtxhr.readyState === 4 && guestdtxhr.status === 200) {
                            let results = JSON.parse(guestdtxhr.responseText);
                            let guestdetails = results.guestdetails;

                            if (guestdetails != null) {
                                $('#guestname').text(guestdetails.name ?? '');
                                if (!guestdetails.name) {
                                    $('#guestnameth').css('display', 'none');
                                }

                                let addstr = `${guestdetails.add1}${guestdetails.add2 !== '' ? ', ' + guestdetails.add2 : ''}`;
                                $('#guestadd').text(addstr);
                                if (!guestdetails.add1 && !guestdetails.add2) {
                                    $('#guestaddth').css('display', 'none');
                                }

                                $('#guestmobile').text(guestdetails.guestmobile ?? '');
                                if (!guestdetails.guestmobile) {
                                    $('#guestmobileth').css('display', 'none');
                                }

                                $('#guestcity').text(guestdetails.guestcityname ?? '');
                                if (!guestdetails.guestcityname) {
                                    $('#guestcityth').css('display', 'none');
                                }
                            } else {
                                let fetchguestprof = new XMLHttpRequest();
                                fetchguestprof.open('POST', '/fetchgguestprof', true);
                                fetchguestprof.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                                fetchguestprof.onreadystatechange = function() {
                                    if (fetchguestprof.status === 200 && fetchguestprof.readyState === 4) {
                                        let result = JSON.parse(fetchguestprof.responseText);
                                        if (result.guestprof != null) {
                                            let guestdetails = result.guestprof;
                                            $('#guestname').text(guestdetails.name ?? '');
                                            if (!guestdetails.name) {
                                                $('#guestnameth').css('display', 'none');
                                            }

                                            let addstr = `${guestdetails.add1}${guestdetails.add2 !== '' ? ', ' + guestdetails.add2 : ''}`;
                                            $('#guestadd').text(addstr);
                                            if (!guestdetails.add1 && !guestdetails.add2) {
                                                $('#guestaddth').css('display', 'none');
                                            }

                                            $('#guestmobile').text(guestdetails.mobile_no ?? '');
                                            if (!guestdetails.mobile_no) {
                                                $('#guestmobileth').css('display', 'none');
                                            }

                                            $('#guestcity').text(guestdetails.nameofcity ?? '');
                                            if (!guestdetails.nameofcity) {
                                                $('#guestcityth').css('display', 'none');
                                            }
                                        } else {
                                            $('#customerdiv').addClass('none');
                                        }
                                    }
                                }
                                fetchguestprof.send(`addeddocid=${addeddocid}&sale1docid=${sale1docid}&_token={{ csrf_token() }}`);
                            }
                        }
                    }
                    guestdtxhr.send(`addeddocid=${addeddocid}&roomno=${roomno}&_token={{ csrf_token() }}`);
                    if (results.sale1.delflag == 'Y') {
                        $('.cancel-overlay').css('display', 'flex');
                    }

                    $('#tableorroom').text(results.tbro);

                    $('#curtime').text(results.sale1.vtime);
                    let rows = '';
                    let items = results.items;
                    let taxes = results.taxes;
                    let suntran = results.suntran;
                    items.forEach((data) => {
                        rows += `<tr>
                            <td>${data.itemname}</td>
                            <td class="right-align">${data.qty}</td>
                            <td class="right-align">${data.rate}</td>
                            <td class="right-align">${data.amt}</td>
                        </tr>`;
                    });
                    tbody.append(rows);
                    let suntranrow = '';
                    let tfootRows = '';
                    suntran.forEach((data, index) => {
                        if (data.amount !== 0.00) {
                            if (data.dispname.toLowerCase() === 'amount') {
                                tfootRows += `
                <tr>
                    <td colspan="3" style="">Amount</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>`;
                            }

                            if (data.dispname.toLowerCase() === 'discount') {
                                tfootRows += `
                <tr>
                    <td colspan="3" style="">Discount ${data.svalue}%</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>`;
                            }
                            if (data.dispname.toLowerCase() === 'sgst') {
                                tfootRows += `
                <tr>
                    <td colspan="3" style="">SGST</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>`;
                            }
                            if (data.dispname.toLowerCase() === 'cgst') {
                                tfootRows += `
                <tr>
                    <td colspan="3" style="">CGST</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>`;
                            }
                            if (data.dispname.toLowerCase() === 'round off') {
                                tfootRows += `
                <tr style="border-bottom: 1px dashed;">
                    <td colspan="3" style="font-weight: bold;">Round Off</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>`;
                            }
                            if (data.dispname.toLowerCase() === 'net amount') {
                                tfootRows += `
                <tr>
                    <td colspan="3" style="">Net Amount</td>
                    <td class="right-align" style="font-weight: bold;">
                        ${data.amount}
                    </td>
                </tr>
                <tr><td colspan="3" style="">In Words</td><td style="font-weight: bold;">
                        ${inWords(parseFloat(data.amount))}
                    </td>
                </tr>
                `;
                            }
                        }
                    });
                    tfoot.append(tfootRows);
                    let taxdata = '';
                    taxes.forEach((data, index) => {
                        taxdata += `<div>
                    <p class="bold">${data.taxname}</p>
                    <p class="bold">${data.taxper}%</p>
                    <p>${data.taxamt}</p>
                    <p>${data.taxableamt}</p>
                </div>`;
                    });
                    grouptaxesdiv.html(taxdata);
                    $('#waiter').text(results.waitername.name);
                    $('#cashier').text(results.sale1.u_name);
                }

            }
            itemsxhr.send(`vdate=${vdate}&vtype=${vtype}&billno=${billno}&_token={{ csrf_token() }}`);


        }, 2000);
    });

    function dmy(dateString) {
        var parts = dateString.split('-');
        var newDate = parts[2] + '-' + parts[1] + '-' + parts[0];
        return newDate;
    }

    function returnvdata(dcode, fcode) {
        let menuxhr = new XMLHttpRequest();
        menuxhr.open('POST', '/menuxhrsearch', true);
        menuxhr.setRequestHeader('Content-Type', '')
    }

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
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    setTimeout(() => {
        window.print();
    }, 3000);
</script>
