@extends('property.layouts.main')
@section('main-container')
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <style>
        @media print {
            div.titlep {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background-color: white;
                text-align: center;
            }

            #bulkpaycharge {
                margin-top: 250px;
            }

            table#bulkpaycharge tbody td.name,
            table#bulkpaycharge tbody td.billdate {
                white-space: nowrap;
            }
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body checkoutreport">
                            <form action="">
                                <div class="row justify-content-around">
                                    <input type="hidden" value="{{ $company->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ $company->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ $company->propertyid }}" id="propertyid" name="propertyid">
                                    <input type="hidden" value="{{ $company->comp_name }}" id="compname" name="compname">
                                    <input type="hidden" value="{{ $company->address1 }}" id="address" name="address">
                                    <input type="hidden" value="{{ $company->city }}" id="city" name="city">
                                    <input type="hidden" value="{{ $company->mobile }}" id="compmob" name="compmob">
                                    <input type="hidden" value="{{ $statename }}" id="statename" name="statename">
                                    <input type="hidden" value="{{ $company->pin }}" id="pin" name="pin">
                                    <input type="hidden" value="{{ $company->email }}" id="email" name="email">
                                    <input type="hidden" value="{{ $company->logo }}" id="logo" name="logo">
                                    <input type="hidden" value="{{ $company->u_name }}" id="u_name" name="u_name">
                                    <input type="hidden" value="{{ $company->gstin }}" id="gstin" name="gstin">
                                    <div class="text-center titlep">
                                        <h3>{{ $company->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $company->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $statename . ' - ' . $company->city . ' - ' . $company->pin }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">Check Out Register</p>
                                        <p style="text-align:left;margin-top:-10px; font-size:16px;">From Date: <span id="fromdatep"></span> To Date:
                                            <span id="todatep"></span>
                                        </p>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="fromdate" class="col-form-label">From Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $fromdate }}" class="form-control" name="fromdate"
                                                id="fromdate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="todate" class="col-form-label">To Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $fromdate }}" class="form-control" name="todate" id="todate">
                                        </div>
                                    </div>

                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" name="fetchbutton" type="button"
                                            class="btn btn-success">Refresh <i
                                                class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-primary" name="settlemodebbtn"
                                            id="settlemodebbtn"><input type="checkbox" class="custom-check" name="settleyn" id="settleyn"> Settlement <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listedsettles" style="display:none;">
                                            <li> <input type="checkbox" id="checkallsettle" checked>
                                                <span>Select All <span class="tcount">10</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Settlement Name..." class="form-control settlesearch"></li>
                                            @foreach ($uniqpay as $item)
                                                <li data-settlename="{{ $item->paytype }}" class="settlenameli">
                                                    <input class="settlecheckbox" value="{{ $item->paytype }}" type="checkbox" checked>
                                                    <span>{{ $item->paytype }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-primary" name="companylistbtn"
                                            id="companylistbtn"><input type="checkbox" class="custom-check" name="companyyn" id="companyyn"> Company <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listedcompany" style="display:none;">
                                            <li> <input type="checkbox" id="checkallcompanies" checked>
                                                <span>Select All <span class="tcount">{{ count($companysub) }}</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Company Name..." class="form-control companysearch"></li>
                                            @foreach ($companysub as $item)
                                                <li data-companyname="{{ $item->name }}" class="companynameli">
                                                    <input class="companycheckbox" value="{{ $item->sub_code }}"
                                                        type="checkbox" checked>
                                                    <span>{{ $item->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-primary" name="travelagentbtn"
                                            id="travelagentbtn"><input type="checkbox" class="custom-check" name="travelyn" id="travelyn"> Travel Agent <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listedtravelagent" style="display:none;">
                                            <li> <input type="checkbox" id="checkalltravelagent" checked>
                                                <span>Select All <span class="tcount">{{ count($companysub) }}</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Agent Name..." class="form-control travelagentsearch"></li>
                                            @foreach ($travelagents as $item)
                                                <li data-travelname="{{ $item->name }}" class="travelnameli">
                                                    <input class="travelagentcheckbox" value="{{ $item->sub_code }}"
                                                        type="checkbox" checked>
                                                    <span>{{ $item->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-primary" name="bussourcebtn"
                                            id="bussourcebtn"><input type="checkbox" class="custom-check" name="bussyn" id="bussyn"> Bus Source <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listedbussource" style="display:none;">
                                            <li> <input type="checkbox" id="checkallbussource" checked>
                                                <span>Select All <span class="tcount">{{ count($bussdata) }}</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Buss Name..." class="form-control bussourcesearch"></li>
                                            @foreach ($bussdata as $item)
                                                <li data-bussourcename="{{ $item->name }}" class="bussourcenameli">
                                                    <input class="bussourcecheckbox" value="{{ $item->bcode }}"
                                                        type="checkbox" checked>
                                                    <span>{{ $item->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>

                                <div id="paygroup" class="none">
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table id="bulkpaycharge"
                                    class="table table-hover table-download-with-search table-hover table-striped">
                                    <thead>
                                        <th id="billnoth">Bill No.</th>
                                        <th>Folio No.</th>
                                        <th>Guest Name</th>
                                        <th>Bill Date</th>
                                        <th>Mobile</th>
                                        <th>In Date</th>
                                        <th>Out Date</th>
                                        <th>Room No.</th>
                                        <th>Occ</th>
                                        <th>NIS</th>
                                        <th>Goods</th>
                                        @foreach ($revmast as $item)
                                            <th>{{ $item->name }}</th>
                                        @endforeach
                                        <th>CGST</th>
                                        <th>SGST</th>
                                        <th>TTL Tax</th>
                                        <th>Discount</th>
                                        <th>Round Off</th>
                                        <th>Advance</th>
                                        <th>Bill Amt</th>
                                        <th>Mode</th>
                                        <th>Payment</th>
                                        <th>Company/Travel</th>
                                        <th>Book No</th>
                                        <th>Ref. Book Id.</th>
                                        <th>Bus Source</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="compnames" class="none">
                            </div>
                            <div id="busssource" class="none">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function counting(arr) {
            if (!Array.isArray(arr)) {
                throw new Error("Input must be an array");
            }
            return arr.length;
        }
        $(document).ready(function() {

            $(document).on('change', '#fromdate', function() {
                validateFinancialYear('#fromdate');
            });
            $(document).on('change', '#todate', function() {
                validateFinancialYear('#todate');
            });

            dynamicSearch('.companysearch', 'companyname', '.companynameli');
            toggleList("#companylistbtn", "#listedcompany");
            checkAllCheckboxes("#checkallcompanies", ".companycheckbox");

            dynamicSearch('.settlesearch', 'settlename', '.settlenameli');
            toggleList("#settlemodebbtn", "#listedsettles");
            checkAllCheckboxes("#checkallsettle", ".settlecheckbox");

            dynamicSearch('.travelagentsearch', 'travelname', '.travelnameli');
            toggleList("#travelagentbtn", "#listedtravelagent");
            checkAllCheckboxes("#checkalltravelagent", ".travelagentcheckbox");

            dynamicSearch('.bussourcesearch', 'bussourcename', '.bussourcenameli');
            toggleList("#bussourcebtn", "#listedbussource");
            checkAllCheckboxes("#checkallbussource", ".bussourcecheckbox");

            let dataTableInitialized = false;
            $(document).on('click', '#fetchbutton', function() {
                showLoader();
                pushNotify('info', 'Check Out Register', 'Fetching Report, Please Wait...', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                $('#bulkpaycharge').DataTable().destroy();
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                $('#fromdatep').text(dmy(fromdate));
                $('#todatep').text(dmy(todate));
                if (fromdate > todate) {
                    pushNotify('error', 'From Date should not be greater than To Date');
                    $('#fromdate').val(todate);
                    return;
                }

                let settleyn = $('#settleyn').is(':checked') == true ? 'Y' : 'N';
                let companyyn = $('#companyyn').is(':checked') == true ? 'Y' : 'N';
                let travelyn = $('#travelyn').is(':checked') == true ? 'Y' : 'N';
                let bussyn = $('#bussyn').is(':checked') == true ? 'Y' : 'N';

                let allsettlement = $('.settlecheckbox').map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).val();
                    }
                }).get();

                let allcompany = $('.companycheckbox').map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).val();
                    }
                }).get();

                let alltravelagent = $('.travelagentcheckbox').map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).val();
                    }
                }).get();

                let allbusssource = $('.bussourcecheckbox').map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).val();
                    }
                }).get();

                let compname = $('#compname').val();

                let tbody = $('#bulkpaycharge tbody');
                let tfoot = $('#bulkpaycharge tfoot');
                tbody.empty();
                tfoot.empty();
                let chargexhr = new XMLHttpRequest();
                chargexhr.open('POST', '/fetchpaydata', true);
                chargexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                chargexhr.onreadystatechange = function() {
                    if (chargexhr.readyState === 4 && chargexhr.status === 200) {
                        let resulttmp = JSON.parse(chargexhr.responseText);
                        let result = resulttmp.report;
                        let revmast = resulttmp.revmast;
                        if (result.length == 0) {
                            pushNotify('info', 'No Data Found', 'No Data Found for the Selected Dates');
                        }
                        if (result == '1') {
                            pushNotify('error', `From Date should not be less than: ${dmy($('#start_dt').val())}`);
                            $('#fromdate').val($('#start_dt').val());
                        } else if (result == '2') {
                            pushNotify('error', `To Date should not be greater than: ${dmy($('#end_dt').val())}`);
                            $('#todate').val($('#start_dt').val());
                        } else {
                            let goodssum = 0.00;
                            let cgstsum = 0.00;
                            let sgstsum = 0.00;
                            let rmchsum = 0.00;
                            let extrabedsum = 0.00;
                            let discsum = 0.00;
                            let mealsum = 0.00;
                            let totaltax = 0.00;
                            let totaldiscount = 0.00;
                            let totalroundoff = 0.00;
                            let totaladvance = 0.00;
                            let totalbill = 0.00;
                            let paymentsum = 0.00;
                            let totalroomcount = 0;
                            let totalguests = 0;
                            let totalnight = 0;
                            let revSums = {};
                            revmast.forEach((rdata) => {
                                revSums[rdata.rev_code.substring(0, 4).toLowerCase()] = 0.00;
                            });
                            let data = '';
                            let paymentarray = [];
                            result.forEach((item, index) => {
                                totalroomcount++;
                                // filtering paymentmode with their row adding payment and doing total of each paymeode payment then making array of that
                                let paymentmode = item.paytype;
                                let payment = item.payment;
                                // if (paymentmode != null) {
                                //     if (paymentmode.includes(',')) {
                                //         paymentmode = paymentmode.split(',');
                                //         payment = payment.split(',');
                                //         paymentmode.forEach((item, index) => {
                                //             paymentarray.push({
                                //                 paymentmode: item,
                                //                 payment: payment[index]
                                //             });
                                //         });
                                //     } else {
                                //         paymentarray.push({
                                //             paymentmode: paymentmode,
                                //             payment: payment
                                //         });
                                //     }
                                // }

                                data = `<tr bcode="${item.bcode}" paymode="${item.paymentmode}" compcode="${item.compcode}" travelcode="${item.travelcode}">
                                            <td class="bg-warning-rgba1 font-tiny">
                                                ${item.billno == null ? '' : `<a href="billreprint?billno=${item.billno}&year=${item.vprefix}">${item.billno}</a>`}
                                            </td>
                                            <td>${item.foliono}</td>
                                            <td>${item.guestname == null ? '' : item.guestname}</td>
                                            <td class="billdate">${dmy(item.settledate)}</td>
                                            <td>${item.mobile_no == null ? '' : `<a class="text-info" href="tel: ${item.mobile_no}">${item.mobile_no}`}</a></td>
                                            <td class="nowrap">${item.checkindate == null ? '' : dmy(item.checkindate)} ${item.checkintime == null ? '' : item.checkintime.substr(0, 5)}</td>
                                            <td>${item.chkoutdate == null ? '' : dmy(item.chkoutdate)} ${item.chkouttime == null ? '' : item.chkouttime.substr(0, 5)}</td>
                                            <td>${item.roomno == null ? '' : item.roomno}</td>
                                            <td>${item.occ == null ? '' : item.occ}</td>
                                            <td>${item.nights == null ? '' : item.nights}</td>
                                            <td>${item.goods1 == null ? '0.00' : item.goods1}</td>`
                                revmast.forEach((rdata) => {
                                    // console.log(`sum_${rdata.rev_code.substring(0, 4).toLowerCase()}`);
                                    let value = parseFloat(item["sum_" + rdata.rev_code.substring(0, 4).toLowerCase()] ?? 0.00);
                                    revSums[rdata.rev_code.substring(0, 4).toLowerCase()] += value;
                                    data += `<td>${value.toFixed(2)}</td>`;
                                });
                                data += `<td>${item.cgstsum == null ? '0.00' : item.cgstsum}</td>
                                            <td>${item.sgstsum == null ? '0.00' : item.sgstsum}</td>
                                            <td>${item.total_tax == null ? '0.00' : item.total_tax}</td>
                                            <td>${item.discount == null ? '0.00' : item.discount}</td>
                                            <td>${item.roundoff == null ? '0.00' : item.roundoff}</td>
                                            <td>${item.advance}</td>
                                            <td>${item.billamt == null ? '0.00' : item.billamt}</td>
                                            <td>${item.paytype == null ? '' : item.paytype}</td>
                                            <td>${item.payment == null ? '0.00' : item.payment}</td>
                                            <td>${item.company == null ? '' : item.company} ${item.compgstin == null ? '' : `(${item.compgstin})`} / ${item.travelcompany == null ? '' : item.travelcompany} ${item.travelgstin == null ? '' : `(${item.travelgstin})`}</td>
                                            <td>${item.bookno == null ? '' : item.bookno}</td>
                                            <td>${item.refbookingid == null ? '' : item.refbookingid}</td>
                                            <td>${item.busssource == null ? '' : item.busssource}</td>
                                         </tr>`;
                                tbody.append(data);

                                totalguests += parseInt(item.occ ?? 0);
                                totalnight += parseInt(item.nights ?? 0);
                                goodssum += parseFloat(item.goods1 ?? 0.00);
                                cgstsum += parseFloat(item.cgstsum ?? 0.00);
                                sgstsum += parseFloat(item.sgstsum ?? 0.00);
                                rmchsum += parseFloat(item.rmchsum ?? 0.00);
                                extrabedsum += parseFloat(item.extrabed ?? 0.00);
                                mealsum += parseFloat(item.mealcharge ?? 0.00);
                                totaltax += parseFloat(item.total_tax ?? 0.00);
                                totaldiscount += parseFloat(item.discount ?? 0.00);
                                totalroundoff += parseFloat(item.roundoff ?? 0.00);
                                totaladvance += parseFloat(item.advance ?? 0.00);
                                totalbill += parseFloat(item.billamt ?? 0.00);
                            });
                            let grouped = paymentarray.reduce((acc, item) => {
                                if (!acc[item.paymentmode]) {
                                    acc[item.paymentmode] = 0;
                                }
                                acc[item.paymentmode] += parseFloat(item.payment);
                                return acc;
                            }, {});
                            let paygroup = $('#paygroup');
                            paygroup.empty();
                            paygroup.removeClass('none');
                            let paydata = '';
                            let total = 0;
                            for (const [key, value] of Object.entries(grouped)) {
                                paydata += `<ul><li>${key} : ${value.toFixed(2)}</li></ul>`;
                                total += value;
                            }
                            // paydata += `<ul><li><b>Total:</b> ${total.toFixed(2)}</li></ul>`;
                            // paygroup.append(paydata);
                            $('#billnoth').click();
                            let explancode = 'CGSS: CGST (SALES), SGSS: SGST (SALES), RMCH: ROOM CHARGE, TOUT: TRANSFER FROM OUTLET, ROFF: ROUND OFF A/C';
                            let footdata = `<tr class="font-weight-bold"><td><b>Total:</b></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        <td id="roomcount">${totalroomcount}</td>
                        <td id="totalguests">${totalguests}</td>
                        <td id="totalnights">${totalnight}</td>
                        <td id="goodssum">${goodssum.toFixed(2)}</td>`;

                            revmast.forEach((rdata) => {
                                footdata += `<td id="${rdata.rev_code.substring(0, 4).toLowerCase()}-sum">${revSums[rdata.rev_code.substring(0, 4).toLowerCase()].toFixed(2)}</td>`;
                            });

                            footdata += `<td id="sgstsum">${sgstsum.toFixed(2)}</td>
                        <td id="cgstsum">${cgstsum.toFixed(2)}</td>
                        <td id="totaltax">${totaltax.toFixed(2)}</td>
                        <td id="totaldiscount">${totaldiscount.toFixed(2)}</td>
                        <td id="totalroundoff">${totalroundoff.toFixed(2)}</td>
                        <td id="totaladvance">${totaladvance.toFixed(2)}</td>
                        <td id="totalbill">${totalbill.toFixed(2)}</td>
                        <td></td>
                        <td></td><td></td><td></td><td></td></td>
                    </tr>`;
                            tfoot.append(footdata);

                            setTimeout(hideLoader, 1000);
                            if (!dataTableInitialized) {
                                $('#bulkpaycharge').DataTable({
                                    dom: 'Bfrtip',
                                    pageLength: 15,
                                    buttons: [{
                                            extend: 'excelHtml5',
                                            text: 'Excel <i class="fa fa-file-excel-o"></i>',
                                            title: compname,
                                            filename: 'Checkout Register',
                                            exportOptions: {
                                                rows: function(idx, data, node) {
                                                    return !$(node).hasClass('none');
                                                }
                                            },
                                            footer: true
                                        },
                                        {
                                            extend: 'csvHtml5',
                                            text: 'Csv <i class="fa-solid fa-file-csv"></i>',
                                            title: compname,
                                            filename: 'Checkout Register',
                                            footer: true,
                                            exportOptions: {
                                                rows: function(idx, data, node) {
                                                    return !$(node).hasClass('none');
                                                }
                                            }
                                        },
                                        {
                                            extend: 'print',
                                            text: 'Print <i class="fa-solid fa-print"></i>',
                                            title: 'Checkout Register',
                                            filename: 'Checkout Register',
                                            footer: true,
                                            exportOptions: {
                                                rows: function(idx, data, node) {
                                                    return !$(node).hasClass('none');
                                                }
                                            },
                                            customize: function(win) {
                                                $(win.document.body).find('th').removeClass('sorting sorting_asc sorting_desc');
                                                $(win.document.body).find('table').css('margin-top', '115px');
                                                $(win.document.body).prepend('<div class="titlep">' + $('.titlep').html() + '</div>');
                                                $(win.document.body).append('<div class="paygroup">' + paygroup.html() + '</div>');
                                                $(win.document.body).append('<div class="explancode">' + explancode + '</div>');
                                                $(win.document.body).find('table').addClass('print-landscape');
                                                var css = '@page { size: landscape; }';
                                                var head = win.document.head || win.document.getElementsByTagName('head')[0];
                                                var style = win.document.createElement('style');
                                                style.type = 'text/css';
                                                style.media = 'print';
                                                if (style.styleSheet) {
                                                    style.styleSheet.cssText = css;
                                                } else {
                                                    style.appendChild(win.document.createTextNode(css));
                                                }
                                                head.appendChild(style);
                                            }
                                        }
                                    ],
                                });
                            }
                        }
                    } else if (chargexhr.readyState === 4 && chargexhr.status === 500) {
                        pushNotify('error', 'Error Fetching Data', 'Error Fetching Data, Please Try Again Later');
                        console.error(chargexhr.responseText);

                        setTimeout(hideLoader, 1000);
                    }
                }
                chargexhr.send(`settleyn=${settleyn}&companyyn=${companyyn}&travelyn=${travelyn}&bussyn=${bussyn}&fromdate=${fromdate}&todate=${todate}&allsettlement=${allsettlement}&allcompany=${allcompany}&alltravelagent=${alltravelagent}
                &allbusssource=${allbusssource}&_token={{ csrf_token() }}`);
            });
            $('#todate').trigger('change');

        });
    </script>
@endsection
