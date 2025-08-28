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
        .refresh-button-container {
            position: absolute;
            top: 70px;
            right: 50px;
        }

        @media print {
            .none {
                display: none !important;
            }

            .titlep {
                display: block !important;
                text-align: center !important;
            }

            #fomtax thead th.none {
                display: none !important;
            }

            #fomtax tbody td.none {
                display: none !important;
            }
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body fomtaxdetail">
                            <form action="" method="post">
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
                                    <p style="margin-top:-10px; font-size:16px;">FOM Tax Details</p>
                                    <p style="text-align:left;margin-top:-10px; font-size:16px;">From Date: <span id="fromdatep"></span> To Date:
                                        <span id="todatep"></span>
                                    </p>
                                </div>
                                <div class="row">
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
                                    <div class="">
                                        <label for="taxnamebtn" class="col-form-label">‎ </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-outline-success btn-success" name="taxnamebtn" id="taxnamebtn">Taxes</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="refresh-button-container">
                                    <button type="button" id="refreshbutton" class="btn btn-primary">Refresh</button>
                                </div>

                            </form>

                            <div id="tableshowdiv" class="row table-responsive">
                                <table id="fomtax" class=" table table-border table-hover table striped border rounded">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Guest Name</th>
                                            <th>Folio No.</th>
                                            <th id="billnoth">Bill No.</th>
                                            <th>Room No.</th>
                                            <th>Bill Amt</th>
                                            <th>Goods 6%</th>
                                            <th>Goods 9%</th>
                                            <th>CGST 6%</th>
                                            <th>SGST 6%</th>
                                            <th>CGST 9%</th>
                                            <th>SGST 9%</th>
                                            <th>Till Tax Amt</th>
                                            <th>Company</th>
                                            <th>GSTIN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="none" id="taxesnames"></div>
    </div>

    <script>
        $(document).ready(function() {
            let dataTableInitialized = false;
            $(document).on('click', '#refreshbutton', function() {
                showLoader();
                pushNotify('info', 'FOM Tax Details', 'Fetching Report, Please Wait...', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                $('#fomtax').DataTable().destroy();
                let compname = $('#compname').val();
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                if (fromdate > todate) {
                    pushNotify('error', 'From Date should not be greater than To Date');
                    $('#fromdate').val($('#start_dt').val());
                    $('#todate').val($('#end_dt').val());
                    return;
                }
                $('#fromdatep').text(dmy(fromdate));
                $('#todatep').text(dmy(todate));
                let tablehead = $('#fomtax thead');
                let tablebody = $('#fomtax tbody');
                tablebody.empty();
                let tfoot = $('#fomtax tfoot');
                tfoot.empty();
                let fomtaxxhr = new XMLHttpRequest();
                fomtaxxhr.open('POST', '/fetchfomtaxdata', true);
                fomtaxxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                fomtaxxhr.onreadystatechange = function() {

                    if (fomtaxxhr.readyState === 4 && fomtaxxhr.status === 200) {
                        let results = JSON.parse(fomtaxxhr.responseText);
                        let taxdetail = results.taxdetail;
                        if (taxdetail.length === 0) {
                            setTimeout(hideLoader, 1000);
                            pushNotify('info', 'No Data Found', 'No Data Found for the Selected Dates');
                            tablebody.append('<tr><td colspan="15" class="text-center dataTables_empty">No Data Available</td></tr>');
                            return;
                        }
                        if (taxdetail == '1') {
                            setTimeout(hideLoader, 1000);
                            pushNotify('error', `From Date should not be less than: ${dmy($('#start_dt').val())}`);
                            $('#fromdate').val($('#start_dt').val());
                        } else if (taxdetail == '2') {
                            setTimeout(hideLoader, 1000);
                            pushNotify('error', `To Date should not be greater than: ${dmy($('#end_dt').val())}`);
                            $('#todate').val($('#start_dt').val());
                        }
                        if (taxdetail.length == 0) {
                            setTimeout(hideLoader, 1000);
                            pushNotify('info', 'No Data Found', 'No Data Found for the Selected Dates');
                        }
                        taxdetail.sort((a, b) => {
                            const billnoA = parseFloat(a.billno) || 0;
                            const billnoB = parseFloat(b.billno) || 0;
                            return billnoA - billnoB;
                        });
                        setTimeout(hideLoader, 1000);
                        let rows = '';
                        let totalcgst6 = 0.00;
                        let totalsgst6 = 0.00;
                        let totalcgst9 = 0.00;
                        let totalsgst9 = 0.00;
                        let totalbillamt = 0.00;
                        let totalbase1 = 0.00;
                        let totalbase2 = 0.00;
                        let totaltilltax = 0.00;

                        let thead = `<tr>
                                        <th>Date</th>
                                        <th>Guest Name</th>
                                        <th>Folio No.</th>
                                        <th id="billnoth">Bill No.</th>
                                        <th>Room No.</th>
                                        <th>Bill Amt</th>
                                    `;

                        taxdetail.forEach((item) => {
                            if (item.BILL_NO == 0) {
                                return;
                            }
                            rows += `<tr>
                                    <td>${item.settledate == null ? '' : dmy(item.settledate)}</td>
                                    <td>${item.GuestName == null ? '' : item.GuestName}</td>
                                    <td>${item.foliono == null ? '' : item.foliono}</td>
                                    <td>${item.BILL_NO == null ? '' : item.BILL_NO}</td>
                                    <td>${item.RoomNo == null ? '' : item.RoomNo}</td>
                                    <td>${item.billamount}</td>
                                    <td id="base1">${item.BASEVALUE1}</td>
                                    <td id="base2">${item.BASEVALUE2}</td>
                                    <td id="cgst6">${item.TAXAMT1}</td>
                                    <td id="sgst6">${item.TAXAMT3}</td>
                                    <td id="cgst9">${item.TAXAMT2}</td>
                                    <td id="sgst9">${item.TAXAMT4}</td>
                                    <td>${item.ETAXAMT}</td>
                                    <td>${item.companyname == null ? '' : item.companyname}</td>
                                    <td>${item.companygstin == null ? '' : item.companygstin}</td>
                                </tr>`;

                            totalcgst6 += parseFloat(item.TAXAMT1 ?? 0.00);
                            totalsgst6 += parseFloat(item.TAXAMT3 ?? 0.00);
                            totalcgst9 += parseFloat(item.TAXAMT2 ?? 0.00);
                            totalsgst9 += parseFloat(item.TAXAMT4 ?? 0.00);
                            totalbillamt += parseFloat(item.billamount ?? 0.00);
                            totalbase1 += parseFloat(item.BASEVALUE1 ?? 0.00);
                            totalbase2 += parseFloat(item.BASEVALUE2 ?? 0.00);
                            totaltilltax += parseFloat(item.ETAXAMT ?? 0.00);
                        });
                        tablebody.append(rows);

                        let tfootdata = `<tr class="font-weight-bold">
                                <td colspan="5">Total</td>
                                <td>${totalbillamt.toFixed(2)}</td>
                                <td id="base1tf">${totalbase1.toFixed(2)}</td>
                                <td id="base2tf">${totalbase2.toFixed(2)}</td>
                                <td id="cgst6tf">${totalcgst6.toFixed(2)}</td>
                                <td id="sgst6tf" class="${totalsgst6.toFixed(2) === '0.00' ? 'none' : ''}">${totalsgst6.toFixed(2)}</td>
                                <td id="cgst9tf">${totalcgst9.toFixed(2)}</td>
                                <td id="sgst9tf">${totalsgst9.toFixed(2)}</td>
                                <td>${totaltilltax.toFixed(2)}</td>
                                <td colspan="2"></td>
                            </tr>`;
                        tfoot.append(tfootdata);

                        if (totalbase1 > 0) {
                            thead += `<th>Goods 6%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#base1').remove();
                            $('#fomtax tfoot tr').find('#base1tf').remove();
                        }
                        if (totalbase2 > 0) {
                            thead += `<th>Goods 9%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#base2').remove();
                            $('#fomtax tfoot tr').find('#base2tf').remove();
                        }
                        if (totalcgst6 > 0) {
                            thead += `<th>CGST 6%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#cgst6').remove();
                            $('#fomtax tfoot tr').find('#cgst6tf').remove();
                        }
                        if (totalsgst6 > 0) {
                            thead += `<th>SGST 6%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#sgst6').remove();
                            $('#fomtax tfoot tr').find('sgst6tf').remove();
                        }
                        if (totalcgst9 > 0) {
                            thead += `<th>CGST 9%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#cgst9').remove();
                            $('#fomtax tfoot tr').find('#cgst9tf').remove();
                        }
                        if (totalsgst9 > 0) {
                            thead += `<th>SGST 9%</th>`;
                        } else {
                            $('#fomtax tbody tr').find('#sgst9').remove();
                            $('#fomtax tfoot tr').find('#sgst9tf').remove();
                        }

                        thead += `<th>Till Tax Amt</th><th>Company</th><th>GSTIN</th></tr>`;
                        tablehead.empty();

                        tablehead.append(thead);

                        setTimeout(() => {
                            $('#billnoth').trigger('click');
                        }, 1000);

                        if (!dataTableInitialized) {
                            $('#fomtax').DataTable({
                                dom: 'Bfrtip',
                                pageLength: 15,
                                buttons: [{
                                        extend: 'excelHtml5',
                                        text: 'Excel <i class="fa fa-file-excel-o"></i>',
                                        title: compname,
                                        filename: 'Fom Tax Report',
                                        footer: true
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        text: 'Csv <i class="fa-solid fa-file-csv"></i>',
                                        title: compname,
                                        filename: 'Fom Tax Report',
                                        footer: true,
                                    },
                                    {
                                        extend: 'print',
                                        text: 'Print <i class="fa-solid fa-print"></i>',
                                        title: 'Fom Tax Report',
                                        filename: 'Fom Tax Report',
                                        footer: true,
                                        customize: function(win) {
                                            $(win.document.body).find('th').removeClass('sorting sorting_asc sorting_desc');
                                            $(win.document.body).find('table').css('margin-top', '100px');
                                            $(win.document.body).prepend('<div class="titlep">' + $('.titlep').html() + '</div>');
                                            var style = '<style>';
                                            style += '.none { display: none !important; }';
                                            style += '</style>';
                                            $(win.document.head).append(style);
                                        }
                                    }

                                ],
                            });
                        }
                    }
                }
                fomtaxxhr.send(`fromdate=${fromdate}&todate=${todate}&_token={{ csrf_token() }}`);
            });

            // Fetch Tax Names
            let divbus = `<div class=""></div>`;
            $(document).on('click', '#taxnamebtn', function() {
                let divbus = $('#taxesnames');
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                divbus.html('');
                let setforxhr = new XMLHttpRequest();
                setforxhr.open('POST', '/fetchtaxesnames', true);
                setforxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                setforxhr.onreadystatechange = function() {
                    if (setforxhr.readyState === 4 && setforxhr.status === 200) {
                        let results = JSON.parse(setforxhr.responseText);
                        if (results.length < 1) {
                            divbus.addClass('none');
                            pushNotify('error', 'No data found');
                        } else {
                            divbus.removeClass('none');
                            let data = `<ul id="taxnameul"><li class="text-center movableli">Taxes<button style="top:2px;" class="btn btn-sm btn-danger" id="closeBtn"><i class="fa-regular fa-circle-xmark"></i></button></li><li><input class="menucheckbox" id="allcheckbox" checked value="All" type="checkbox"> All</li>`;
                            results.forEach((item, index) => {
                                data += `
                                    <li data-id="${item.paycode}"><input class="menucheckbox" checked value="${item.paycode}" type="checkbox"> ${item.name}</li>
                                `;
                            });
                            data += '</ul>';
                            divbus.html(data);
                        }
                    }
                };
                setforxhr.send(`fromdate=${fromdate}&todate=${todate}&_token={{ csrf_token() }}`);
            });
            $('#fromdate').trigger('change');
            let offsetX, offsetY;
            let isDragging = false;

            $(document).on('mousedown', '.movableli', function(e) {
                isDragging = true;
                offsetX = e.clientX - $(this).offset().left;
                offsetY = e.clientY - $(this).offset().top;
            });

            $(document).on('mouseup', function() {
                isDragging = false;
            });

            $(document).on('mousemove', function(e) {
                if (isDragging) {
                    $('#taxesnames').css({
                        left: e.clientX - offsetX,
                        top: e.clientY - offsetY
                    });
                }
            });



            $(document).on('change', '#allcheckbox', function() {
                let checkbox = $(this);
                let checked = checkbox.prop('checked');
                let checkboxes = $('.menucheckbox');
                checkboxes.prop('checked', checked);
                checkboxes.trigger('change');
            });

            $(document).on('change', '.menucheckbox', function() {
                let checkox = $(this);
                let table = $('#fomtax');
                if (checkox.prop('checked')) {
                    $(`#fomtax thead th#${checkox.val()}`).remove();
                    $(`#fomtax thead th#${checkox.val()}`).removeClass('none');
                    $(`#fomtax tbody td#${checkox.val()}`).removeClass('none');
                } else {
                    $(`#fomtax thead th#${checkox.val()}`).addClass('none');
                    $(`#fomtax tbody td#${checkox.val()}`).addClass('none');
                }
            });

            $(document).on('click', '#closeBtn', function() {
                $('#taxesnames').addClass('none');
            });
        });
    </script>
@endsection
