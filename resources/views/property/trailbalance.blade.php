@extends('property.layouts.main')

@section('main-container')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <style>
        h1.report-title {
            text-align: center;
            font-size: 2rem;
            margin: 20px 0;
        }

        .dt-buttons {
            margin-bottom: 10px;
        }

        tfoot tr th {
            background-color: #f8f9fa;
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Trial Balance</h5>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-around">
                                <div class="">
                                    <div class="form-group">
                                        <label for="fromdate" class="col-form-label">From Date <i
                                                class="fa-regular fa-calendar mb-1"></i></label>
                                        <input type="date"
                                            class="form-control fromdate" name="fromdate"
                                            id="fromdate">
                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group">
                                        <label for="todate" class="col-form-label">To Date <i
                                                class="fa-regular fa-calendar mb-1"></i></label>
                                        <input type="date"
                                            class="form-control todate" name="todate"
                                            id="todate">
                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group">
                                        <input type="checkbox" name="openingbalance" id="openingbalance" class="form-check-input" value="openingbalance" checked>
                                        <label for="openingbalance" class="col-form-check">Opening Balance</label>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group">
                                        <input type="checkbox" class="form-check-input amtreflex" value="debit" checked>
                                        <label for="debitshow" class="col-form-check">Debit Show</label>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group">
                                        <input type="checkbox" class="form-check-input amtreflex" value="credit" checked>
                                        <label for="debitshow" class="col-form-check">Credit Show</label>
                                    </div>
                                </div>
                            </div>
                            <p class="unassigned-room p-1 rounded-left font-weight-bold">From Date <span id="startdate"></span> To <span id="enddate"></span></p>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <button id="backbutton" class="btn btn-sm btn-danger">Back</button>
                                    <button id="nextbutton" class="btn btn-sm btn-danger">Next</button>
                                </div>
                                <div class="col-md-3 offset-5">
                                    <span id="companyname" class="text-success font-weight-bold"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="table-responsive">

                                        <table id="main-table" class="table table-bordered table-striped table-hover">
                                            <thead class="bg-black">
                                                <tr>
                                                    <th>AC Name</th>
                                                    <th class="debitcell">Debit</th>
                                                    <th class="creditcell">Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">

                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td>Total</td>
                                                    <td class="debitcell" id="total-debit" class="text-end">0.00</td>
                                                    <td class="creditcell" id="total-credit" class="text-end">0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div style="display: none;" id="secondtablediv" class="table-responsive">
                                        <div class="mb-3 d-flex align-items-center flex-wrap gap-2 justify-content-around">
                                            <div class="form-group">
                                                <label for="fromdate" class="col-form-label">From Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                                <input type="date" class="form-control fromdate" name="fromdatem" id="fromdatem">
                                            </div>
                                            <div class="form-group">
                                                <label for="todate" class="col-form-label">To Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                                <input type="date" class="form-control todate" name="todatem" id="todatem">
                                            </div>
                                        </div>
                                        <table id="second-table" class="table table-bordered table-striped table-hover">
                                            <thead class="bg-black">
                                                <tr>
                                                    <th>Month</th>
                                                    <th class="debitcell">Debit</th>
                                                    <th class="creditcell">Credit</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td>Total</td>
                                                    <td class="debitcell text-end" id="total-debit2">0.00</td>
                                                    <td class="creditcell text-end" id="total-credit2">0.00</td>
                                                    <td id="total-balance" class="text-end">0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div style="display: none;" id="thirdtablediv" class="table-responsive">
                                        <div class="mb-3 d-flex align-items-center flex-wrap gap-2 justify-content-around">
                                            <div class="form-group">
                                                <label for="fromdate" class="col-form-label">From Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                                <input type="date" class="form-control fromdate" name="fromdatemr" id="fromdatemr">
                                            </div>
                                            <div class="form-group">
                                                <label for="todate" class="col-form-label">To Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                                <input type="date" class="form-control todate" name="todatemr" id="todatemr">
                                            </div>
                                        </div>
                                        <table id="third-table" class="table table-bordered table-striped table-hover">
                                            <thead class="bg-black">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Vr. No.</th>
                                                    <th>Name</th>
                                                    <th class="debitcell">Debit</th>
                                                    <th class="creditcell">Credit</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td colspan="3">Total</td>
                                                    <td class="debitcell text-end" id="total-debit3">0.00</td>
                                                    <td class="creditcell text-end" id="total-credit3">0.00</td>
                                                    <td id="total-balance" class="text-end">0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $.ajax({
                url: '/yearmanage',
                method: 'GET',
                success: function(response) {
                    $('#startdate').text(dmy(response.finyearreal.start));
                    $('#enddate').text(dmy(response.mtd.end));
                    $('.fromdate').val(response.finyearreal.start);
                    $('.todate').val(response.mtd.end);
                },
                error: function(xhr) {
                    console.log("Error fetching year:", xhr.responseText);
                }
            });

            $.ajaxSetup({
                headers: {
                    'X_CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            let dataTableInitialized = false;
            $(document).on('change', '#fromdate, #todate, #openingbalance', function() {
                showLoader();
                $('#main-table').DataTable().destroy();
                $('#startdate').text(dmy($('#fromdate').val()));
                $('#enddate').text(dmy($('#todate').val()));
                $('#companyname').text('');
                $('#main-table tbody').html('');
                $('#total-debit').text('0.00');
                $('#total-credit').text('0.00');
                let openbal = $('#openingbalance');

                $('#fromdatem, #fromdatemr').val($('#fromdate').val());
                $('#todatem, #todatemr').val($('#todate').val());

                let openingbalance = false;

                if (openbal.is(':checked')) {
                    openingbalance = 'checked';
                } else {
                    openingbalance = 'not checked';
                }

                $.ajax({
                    url: '{{ route('trialmainquery') }}',
                    type: 'POST',
                    data: {
                        'fromdate': $('#fromdate').val(),
                        'todate': $('#todate').val(),
                        'openingbalance': openingbalance
                    },
                    success: function(response) {
                        setTimeout(hideLoader, 1000);
                        if (response.success === false) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Data Found',
                                text: response.message
                            });
                            return;
                        }

                        $('#second-table tbody').empty();
                        $('#secondtablediv').fadeOut('500');
                        $('#third-table tbody').empty();
                        $('#thirdtablediv').fadeOut(500);

                        let rows = '';
                        let totalDebit = 0;
                        let totalCredit = 0;
                        response.forEach(row => {
                            let debit = 0;
                            let credit = 0;
                            let bal = parseFloat(row.balance);

                            if (bal < 0) {
                                credit = Math.abs(bal).toFixed(2);
                                totalCredit += Math.abs(bal);
                            } else {
                                debit = bal.toFixed(2);
                                totalDebit += bal;
                            }

                            rows += `<tr class="clickable-row"
                            data-docid="${row.docid}" 
                            data-vtype="${row.vtype}" 
                            data-vdate="${row.vdate}"
                            data-sub_code="${row.subcode}"
                            data-compname="${row.name}">
                            <td>${row.name}</td>
                            <td class="text-end debitcell">${debit}</td>
                            <td class="text-end creditcell">${credit}</td>
                        </tr>`;
                        });

                        $('#main-table tbody').html(rows);
                        $('#total-debit').text(totalDebit.toFixed(2));
                        $('#total-credit').text(totalCredit.toFixed(2));

                        if (!dataTableInitialized) {
                            let totalbalance = totalDebit - totalCredit;
                            let drcr = totalbalance < 0 ? 'Cr' : 'Dr';
                            totalbalance = `${Math.abs(totalbalance).toFixed(2)} ${drcr}`;

                            $('#main-table').DataTable({
                                dom: 'Bfrtip',
                                footerCallback: function(row, data, start, end, display) {
                                    $(this.api().column(2).footer()).html();
                                },
                                buttons: [{
                                        extend: 'excelHtml5',
                                        title: 'Trial Balance',
                                        exportOptions: {
                                            columns: ':visible'
                                        },
                                        customize: function(xlsx) {
                                            const sheet = xlsx.xl.worksheets['sheet1.xml'];
                                            const rows = $('row', sheet);
                                            const lastRow = rows.last();
                                            const totalRowIndex = parseInt(lastRow.attr('r')) + 1;

                                            const debitTotal = totalDebit.toFixed(2);
                                            const creditTotal = totalCredit.toFixed(2);
                                            const netBalance = `${Math.abs(totalDebit - totalCredit).toFixed(2)} ${totalDebit - totalCredit < 0 ? 'Cr' : 'Dr'}`;

                                            const newRowXml = `
                                                        <row r="${totalRowIndex}">
                                                            <c r="A${totalRowIndex}" t="inlineStr"><is><t></t></is></c>
                                                            <c r="B${totalRowIndex}" t="inlineStr"><is><t><b>Total</b></t></is></c>
                                                            <c r="C${totalRowIndex}" t="inlineStr"><is><t>${debitTotal}</t></is></c>
                                                            <c r="D${totalRowIndex}" t="inlineStr"><is><t>${creditTotal}</t></is></c>
                                                            <c r="E${totalRowIndex}" t="inlineStr"><is><t>${netBalance}</t></is></c>
                                                        </row>
                                                    `;

                                            const sheetData = $('sheetData', sheet);
                                            sheetData.append($.parseXML(newRowXml).documentElement);
                                        }
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        title: '',
                                        orientation: 'landscape',
                                        pageSize: 'A4',
                                        customize: function(doc) {
                                            doc.content.splice(0, 0, {
                                                margin: [0, 0, 0, 12],
                                                alignment: 'center',
                                                fontSize: 12,
                                                text: [{
                                                        text: '{{ companydata()->comp_name }}\n',
                                                        bold: true,
                                                        fontSize: 14
                                                    },
                                                    {
                                                        text: '{{ companydata()->address1 }}\n{{ companydata()->address2 }} - {{ companydata()->state }}-{{ companydata()->city }}-{{ companydata()->pin }}\nTrial Balance\n',
                                                        fontSize: 12
                                                    },
                                                    {
                                                        text: `From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}\n\n`,
                                                        italics: true,
                                                        fontSize: 11
                                                    }
                                                ]
                                            });

                                            const tableBody = doc.content.find(c => c.table);
                                            if (tableBody && tableBody.table) {
                                                const colCount = tableBody.table.body[0].length;
                                                tableBody.table.widths = Array(colCount).fill('*');
                                            }

                                            const dateStr = new Date().toLocaleDateString('en-GB');
                                            doc.header = function() {
                                                return {
                                                    columns: [{
                                                            text: ''
                                                        },
                                                        {
                                                            text: '',
                                                            alignment: 'center'
                                                        },
                                                        {
                                                            text: `Date: ${dateStr}\nPage No: 1`,
                                                            alignment: 'right',
                                                            margin: [0, 10, 10, 0],
                                                            fontSize: 9
                                                        }
                                                    ],
                                                    margin: [10, 10, 10, 0]
                                                };
                                            };

                                            doc.footer = function(currentPage, pageCount) {
                                                if (currentPage === pageCount) {
                                                    return {
                                                        margin: [10, 0, 10, 20],
                                                        columns: [{
                                                                text: `Net Balance: ₹${totalbalance}`,
                                                                bold: true,
                                                                alignment: 'left'
                                                            },
                                                            {
                                                                text: 'Generated By {{ Auth::user()->name }}',
                                                                alignment: 'right',
                                                                fontSize: 9
                                                            }
                                                        ]
                                                    };
                                                }
                                                return {};
                                            };
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        title: '',
                                        customize: function(win) {
                                            $(win.document.body)
                                                .css('font-size', '12px')
                                                .prepend(`
                                                    <div style="text-align:center; margin-bottom:20px;">
                                                        <h3>{{ companydata()->comp_name }}</h3>
                                                        <div>{{ companydata()->address1 }} {{ companydata()->address2 }}</div>
                                                        <div>{{ companydata()->city }} - {{ companydata()->state }}-{{ companydata()->pin }}</div>
                                                        <div><strong>Trial Balance</strong></div>
                                                        <div><em>From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}</em></div>
                                                    </div>
                                                `);

                                            $(win.document.body).append(`
                                                <div style="margin-top:30px;">
                                                    <strong>Total Balance: ₹${totalbalance}</strong><br/>
                                                    <span style="font-size:12px;">Generated by {{ Auth::user()->name }}</span>
                                                </div>
                                            `);

                                            $(win.document.body).find('table')
                                                .addClass('compact')
                                                .css({
                                                    'width': '100%',
                                                    'font-size': 'inherit'
                                                });
                                        }
                                    }
                                ]
                            });
                        }

                    },
                    error: function(error) {
                        setTimeout(hideLoader, 1000);
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.responseJSON.message
                        });
                    }
                });
            });

            setTimeout(() => {
                $('#fromdate').trigger('change');
            }, 1500);

            var dataTableInitialized2 = false;

            function fetchTrialData(fromdateId, todateId) {
                const selectedRow = $('.clickable-row.table-success');
                if (!selectedRow.length) return;

                const sub_code = selectedRow.data('sub_code');
                const companyname = selectedRow.data('compname');

                $('#companyname').text(companyname);
                $('#third-table tbody').empty();
                $('#thirdtablediv').fadeOut(500);
                showLoader();

                $('#second-table').DataTable().destroy();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('monthwisetrialfetch') }}',
                    data: {
                        sub_code: sub_code,
                        fromdate: $(`#${fromdateId}`).val(),
                        todate: $(`#${todateId}`).val()
                    },
                    success: function(response) {
                        setTimeout(hideLoader, 1000);

                        if (response.data.length < 1) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: 'No Data Found'
                            });
                            return;
                        }

                        let tr = '';
                        let totaldebit2 = 0;
                        let totalcredit2 = 0;
                        let openingbalance = parseFloat(response.openingbalance);

                        let amtdr = openingbalance >= 0 ? Math.abs(openingbalance) : 0.00;
                        let amtcr = openingbalance < 0 ? Math.abs(openingbalance) : 0.00;

                        if (amtdr || amtcr) {
                            let openingbal = amtdr ? `${amtdr.toFixed(2)} Dr` : `${amtcr.toFixed(2)} Cr`;
                            tr += `<tr>
                                <td>Opening Balance</td>
                                <td class="text-end debitcell">${amtdr.toFixed(2)}</td>
                                <td class="text-end creditcell">${amtcr.toFixed(2)}</td>
                                <td class="text-end">${openingbal}</td>
                            </tr>`;
                        }

                        let runningBalance = openingbalance;

                        response.data.forEach(row => {
                            let dr = parseFloat(row.totalamtdr) || 0.00;
                            let cr = parseFloat(row.totalamtcr) || 0.00;
                            runningBalance += dr - cr;

                            let balanceLabel = runningBalance > 0 ? `${Math.abs(runningBalance).toFixed(2)} Dr` :
                                runningBalance < 0 ? `${Math.abs(runningBalance).toFixed(2)} Cr` : '0.00';

                            tr += `<tr class="secondtr" data-month_number="${row.month_number}" data-sub_code="${row.subcode}" data-vprefix="${row.vprefix}">
                                    <td>${row.month_year}</td>
                                    <td class="text-end debitcell">${dr.toFixed(2)}</td>
                                    <td class="text-end creditcell">${cr.toFixed(2)}</td>
                                    <td class="text-end">${balanceLabel}</td>
                                </tr>`;

                            totaldebit2 += dr;
                            totalcredit2 += cr;
                        });

                        let finalBalance = totaldebit2 - totalcredit2;
                        let totalBalanceLabel = finalBalance > 0 ? `${Math.abs(finalBalance).toFixed(2)} Dr` :
                            finalBalance < 0 ? `${Math.abs(finalBalance).toFixed(2)} Cr` : '0.00';

                        $('#second-table tbody').html(tr);
                        $('#total-debit2').text(totaldebit2.toFixed(2));
                        $('#total-credit2').text(totalcredit2.toFixed(2));
                        $('#total-balance').text(totalBalanceLabel);
                        $('#secondtablediv').hide().removeClass('d-none').fadeIn(300);

                        if (!dataTableInitialized2) {
                            let totalbalance = totaldebit2 - totalcredit2;
                            let drcr = totalbalance < 0 ? 'Cr' : 'Dr';
                            totalbalance = `${Math.abs(totalbalance).toFixed(2)} ${drcr}`;

                            $('#second-table').DataTable({
                                dom: 'Bfrtip',
                                footerCallback: function(row, data, start, end, display) {
                                    $(this.api().column(2).footer()).html();
                                },
                                buttons: [{
                                        extend: 'excelHtml5',
                                        title: 'Trial Balance (Ledger)',
                                        exportOptions: {
                                            columns: ':visible'
                                        },
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        title: '',
                                        orientation: 'landscape',
                                        pageSize: 'A4',
                                        customize: function(doc) {
                                            doc.content.splice(0, 0, {
                                                margin: [0, 0, 0, 12],
                                                alignment: 'center',
                                                fontSize: 12,
                                                text: [{
                                                        text: '{{ companydata()->comp_name }}\n',
                                                        bold: true,
                                                        fontSize: 14
                                                    },
                                                    {
                                                        text: '{{ companydata()->address1 }}\n{{ companydata()->address2 }} - {{ companydata()->state }}-{{ companydata()->city }}-{{ companydata()->pin }}\nTrial Balance\n',
                                                        fontSize: 12
                                                    },
                                                    {
                                                        text: `From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}\n\n`,
                                                        italics: true,
                                                        fontSize: 11
                                                    }
                                                ]
                                            });

                                            const tableBody = doc.content.find(c => c.table);
                                            if (tableBody && tableBody.table) {
                                                const colCount = tableBody.table.body[0].length;
                                                tableBody.table.widths = Array(colCount).fill('*');
                                            }

                                            const dateStr = new Date().toLocaleDateString('en-GB');
                                            doc.header = function() {
                                                return {
                                                    columns: [{
                                                            text: ''
                                                        },
                                                        {
                                                            text: '',
                                                            alignment: 'center'
                                                        },
                                                        {
                                                            text: `Date: ${dateStr}\nPage No: 1`,
                                                            alignment: 'right',
                                                            margin: [0, 10, 10, 0],
                                                            fontSize: 9
                                                        }
                                                    ],
                                                    margin: [10, 10, 10, 0]
                                                };
                                            };

                                            doc.footer = function(currentPage, pageCount) {
                                                if (currentPage === pageCount) {
                                                    return {
                                                        margin: [10, 0, 10, 20],
                                                        columns: [{
                                                                text: `Net Balance: ₹${totalbalance}`,
                                                                bold: true,
                                                                alignment: 'left'
                                                            },
                                                            {
                                                                text: 'Generated By {{ Auth::user()->name }}',
                                                                alignment: 'right',
                                                                fontSize: 9
                                                            }
                                                        ]
                                                    };
                                                }
                                                return {};
                                            };
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        title: '',
                                        customize: function(win) {
                                            $(win.document.body)
                                                .css('font-size', '12px')
                                                .prepend(`
                                                    <div style="text-align:center; margin-bottom:20px;">
                                                        <h3>{{ companydata()->comp_name }}</h3>
                                                        <div>{{ companydata()->address1 }} {{ companydata()->address2 }}</div>
                                                        <div>{{ companydata()->city }} - {{ companydata()->state }}-{{ companydata()->pin }}</div>
                                                        <div><strong>Trial Balance</strong></div>
                                                        <div><em>From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}</em></div>
                                                    </div>
                                                `);

                                            $(win.document.body).append(`
                                                <div style="margin-top:30px;">
                                                    <strong>Total Balance: ₹${totalbalance}</strong><br/>
                                                    <span style="font-size:12px;">Generated by {{ Auth::user()->name }}</span>
                                                </div>
                                            `);

                                            $(win.document.body).find('table')
                                                .addClass('compact')
                                                .css({
                                                    'width': '100%',
                                                    'font-size': 'inherit'
                                                });
                                        }
                                    }
                                ]
                            });
                        }
                    },
                    error: function(error) {
                        setTimeout(hideLoader, 1000);
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.responseJSON.message
                        });
                    }
                });
            }

            $('#fromdatem, #todatem').on('change', function() {
                fetchTrialData('fromdatem', 'todatem');
            });

            $(document).on('click', '.clickable-row', function() {
                $('.clickable-row').removeClass('table-success');
                $(this).addClass('table-success');
                fetchTrialData('fromdatem', 'todatem');
            });

            function fetchdocrodata(fromdateId, todateId, condition) {
                const selectedRow = $('.secondtr.table-success');
                if (!selectedRow.length) return;

                const sub_code = selectedRow.data('sub_code');
                const vprefix = selectedRow.data('vprefix');
                const month_number = selectedRow.data('month_number');

                const fromdate = $(`#${fromdateId}`).val();
                const todate = $(`#${todateId}`).val();
                if (condition != 1) {
                    let day = '01';
                    let month = String(month_number).padStart(2, '0');
                    let formattedDate = `${vprefix}-${month}-${day}`;
                    $('#fromdatemr').val(formattedDate);
                    $('#todatemr').val($('#todatem').val());
                }

                showLoader();

                $('#third-table').DataTable().destroy();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('monthrowfetch') }}",
                    data: {
                        sub_code: sub_code,
                        vprefix: vprefix,
                        fromdate: fromdate,
                        todate: todate,
                        month_number: month_number,
                        condition: condition
                    },
                    success: function(response) {
                        setTimeout(hideLoader, 1000);
                        if (response.data.length < 1) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: 'No Data Found'
                            });
                            return;
                        }

                        $('#third-table tbody').empty();
                        $('#thirdtablediv').fadeIn(500);

                        let rows = response.data;
                        let openingBalance = parseFloat(response.opening_balance || 0);
                        let totalDebit = 0;
                        let totalCredit = 0;
                        let runningBalance = openingBalance;

                        if (openingBalance !== 0) {
                            $('#third-table tbody').append(`
                                <tr class="table-warning">
                                    <td colspan="5"><strong>(Opening Balance)</strong></td>
                                    <td>${Math.abs(runningBalance).toFixed(2)} ${runningBalance >= 0 ? 'Dr' : 'Cr'}</td>
                                </tr>
                            `);
                        }

                        rows.forEach(row => {
                            let debit = parseFloat(row.amtdr || 0);
                            let credit = parseFloat(row.amtcr || 0);
                            runningBalance += debit - credit;
                            totalDebit += debit;
                            totalCredit += credit;

                            $('#third-table tbody').append(`
                                <tr class="docrow" data-docid="${row.docid}" data-vtype="${row.vtype}">
                                    <td>${row.vdate}</td>
                                    <td>${row.docid}</td>
                                    <td>${row.narration || ''}</td>
                                    <td class="debitcell text-end">${debit ? debit.toFixed(2) : ''}</td>
                                    <td class="creditcell text-end">${credit ? credit.toFixed(2) : ''}</td>
                                    <td class="text-end">${Math.abs(runningBalance).toFixed(2)} ${runningBalance >= 0 ? 'Dr' : 'Cr'}</td>
                                </tr>
                            `);
                        });

                        $('#total-debit3').text(totalDebit.toFixed(2));
                        $('#total-credit3').text(totalCredit.toFixed(2));
                        $('#total-balance').text(`${Math.abs(runningBalance).toFixed(2)} ${runningBalance >= 0 ? 'Dr' : 'Cr'}`);

                        if (!dataTableInitialized2) {
                            let totalbalance = totalDebit - totalCredit;
                            let drcr = totalbalance < 0 ? 'Cr' : 'Dr';
                            totalbalance = `${Math.abs(totalbalance).toFixed(2)} ${drcr}`;

                            $('#third-table').DataTable({
                                dom: 'Bfrtip',
                                footerCallback: function(row, data, start, end, display) {
                                    $(this.api().column(2).footer()).html();
                                },
                                buttons: [{
                                        extend: 'excelHtml5',
                                        title: '(Ledger)',
                                        exportOptions: {
                                            columns: ':visible'
                                        },
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        title: '',
                                        orientation: 'landscape',
                                        pageSize: 'A4',
                                        customize: function(doc) {
                                            doc.content.splice(0, 0, {
                                                margin: [0, 0, 0, 12],
                                                alignment: 'center',
                                                fontSize: 12,
                                                text: [{
                                                        text: '{{ companydata()->comp_name }}\n',
                                                        bold: true,
                                                        fontSize: 14
                                                    },
                                                    {
                                                        text: '{{ companydata()->address1 }}\n{{ companydata()->address2 }} - {{ companydata()->state }}-{{ companydata()->city }}-{{ companydata()->pin }}\nTrial Balance\n',
                                                        fontSize: 12
                                                    },
                                                    {
                                                        text: `From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}\n\n`,
                                                        italics: true,
                                                        fontSize: 11
                                                    }
                                                ]
                                            });

                                            const tableBody = doc.content.find(c => c.table);
                                            if (tableBody && tableBody.table) {
                                                const colCount = tableBody.table.body[0].length;
                                                tableBody.table.widths = Array(colCount).fill('*');
                                            }

                                            const dateStr = new Date().toLocaleDateString('en-GB');
                                            doc.header = function() {
                                                return {
                                                    columns: [{
                                                            text: ''
                                                        },
                                                        {
                                                            text: '',
                                                            alignment: 'center'
                                                        },
                                                        {
                                                            text: `Date: ${dateStr}\nPage No: 1`,
                                                            alignment: 'right',
                                                            margin: [0, 10, 10, 0],
                                                            fontSize: 9
                                                        }
                                                    ],
                                                    margin: [10, 10, 10, 0]
                                                };
                                            };

                                            doc.footer = function(currentPage, pageCount) {
                                                if (currentPage === pageCount) {
                                                    return {
                                                        margin: [10, 0, 10, 20],
                                                        columns: [{
                                                                text: `Net Balance: ₹${totalbalance}`,
                                                                bold: true,
                                                                alignment: 'left'
                                                            },
                                                            {
                                                                text: 'Generated By {{ Auth::user()->name }}',
                                                                alignment: 'right',
                                                                fontSize: 9
                                                            }
                                                        ]
                                                    };
                                                }
                                                return {};
                                            };
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        title: '',
                                        customize: function(win) {
                                            $(win.document.body)
                                                .css('font-size', '12px')
                                                .prepend(`
                                                    <div style="text-align:center; margin-bottom:20px;">
                                                        <h3>{{ companydata()->comp_name }}</h3>
                                                        <div>{{ companydata()->address1 }} {{ companydata()->address2 }}</div>
                                                        <div>{{ companydata()->city }} - {{ companydata()->state }}-{{ companydata()->pin }}</div>
                                                        <div><strong>Trial Balance</strong></div>
                                                        <div><em>From ${dmy($('#fromdate').val())} To ${dmy($('#todate').val())}</em></div>
                                                    </div>
                                                `);

                                            $(win.document.body).append(`
                                                <div style="margin-top:30px;">
                                                    <strong>Total Balance: ₹${totalbalance}</strong><br/>
                                                    <span style="font-size:12px;">Generated by {{ Auth::user()->name }}</span>
                                                </div>
                                            `);

                                            $(win.document.body).find('table')
                                                .addClass('compact')
                                                .css({
                                                    'width': '100%',
                                                    'font-size': 'inherit'
                                                });
                                        }
                                    }
                                ]
                            });
                        }
                    },
                    error: function(error) {
                        setTimeout(hideLoader, 1000);
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.responseJSON.message
                        });
                    }
                });
            }

            $(document).on('click', '.secondtr', function() {
                $('.secondtr').removeClass('table-success');
                $(this).addClass('table-success');
                fetchdocrodata('fromdatem', 'todatem', 0);
            });

            $('#fromdatemr, #todatemr').on('change', function() {
                fetchdocrodata('fromdatemr', 'todatemr', 1);
            });

            $(document).on('change', '.amtreflex', function() {
                const debitChecked = $('.amtreflex[value="debit"]').is(':checked');
                const creditChecked = $('.amtreflex[value="credit"]').is(':checked');

                if (debitChecked && creditChecked) {
                    $('.debitcell').fadeIn('500');
                    $('.creditcell').fadeIn('500');
                } else if (debitChecked) {
                    $('.debitcell').fadeIn('500');
                    $('.creditcell').fadeOut('500');
                } else if (creditChecked) {
                    $('.debitcell').fadeOut('500');
                    $('.creditcell').fadeIn('500');
                } else {
                    $('.debitcell').fadeOut('500');
                    $('.creditcell').fadeOut('500');
                }
            });

            $(document).on('click', '#backbutton', function() {
                $('#secondtablediv').fadeIn('500');
            });

            $(document).on('click', '#nextbutton', function() {
                $('#secondtablediv').fadeOut('500');
            });

            $(document).on('click', '.excelfirst', function() {
                let table = $('#main-table');
            });

            $(document).on('click', '.docrow', function() {
                let docd = $(this).data('docid');
                let vtype = $(this).data('vtype');

                if (docd != '' && vtype != 'HPOST') {
                    window.open(`updatepurchasebill?docid=${docd}`);
                }
            });

        });

        function initTableExporter({
            tableId,
            buttons,
            columns,
            headerHTML = '',
            footerHTML = '',
            excelSheetName = 'Sheet1'
        }) {
            const $table = $('#' + tableId);

            $(buttons.excel).on('click', function() {
                let wb = XLSX.utils.book_new();
                let ws = XLSX.utils.table_to_sheet($table[0], {
                    raw: true
                });
                XLSX.utils.book_append_sheet(wb, ws, excelSheetName);
                XLSX.writeFile(wb, 'table_export.xlsx');
            });

            $(buttons.pdf).on('click', function() {
                const data = [columns];

                $table.find('tbody tr').each(function() {
                    const row = [];
                    $(this).find('td').each(function() {
                        row.push($(this).text().trim());
                    });
                    data.push(row);
                });

                const $tfoot = $table.find('tfoot tr');
                if ($tfoot.length) {
                    const row = [];
                    $tfoot.find('td').each(function() {
                        row.push({
                            text: $(this).text().trim(),
                            bold: true
                        });
                    });
                    data.push(row);
                }

                const headerLines = headerHTML.split('\n').map(line => ({
                    text: line.trim(),
                    alignment: 'center',
                    fontSize: 12,
                    margin: [0, 2]
                }));

                const footerLines = footerHTML.split('\n').map(line => ({
                    text: line.trim(),
                    alignment: 'right',
                    fontSize: 10,
                    margin: [0, 2]
                }));

                const docDefinition = {
                    content: [
                        ...headerLines,
                        {
                            text: '\n'
                        },
                        {
                            table: {
                                headerRows: 1,
                                widths: Array(columns.length).fill('*'),
                                body: data
                            }
                        },
                        {
                            text: '\n'
                        },
                        ...footerLines
                    ]
                };

                pdfMake.createPdf(docDefinition).download('table_export.pdf');
            });


            $(buttons.print).on('click', function() {
                let html = `
                        <html>
                        <head>
                            <title>Print</title>
                            <style>
                                table { border-collapse: collapse; width: 100%; }
                                th, td { border: 1px solid #000; padding: 6px; text-align: right; }
                                td:first-child, th:first-child { text-align: left; }
                                th { background-color: #f0f0f0; }
                            </style>
                        </head>
                        <body>
                            ${headerHTML}
                            <br>
                            ${$table[0].outerHTML}
                            <br>
                            ${footerHTML}
                        </body>
                        </html>
                    `;

                const printWindow = window.open('', '', 'width=900,height=700');
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => printWindow.print(), 500);
            });
        }

        $(document).ready(function() {

            initTableExporter({
                tableId: 'main-table',
                buttons: {
                    excel: '#export-excel',
                    pdf: '#export-pdf',
                    print: '#export-print'
                },
                columns: ['AC Name', 'Debit', 'Credit'],
                headerHTML: `{{ companydata()->comp_name }}
                {{ companydata()->address2 }}
                {{ companydata()->state }} - {{ companydata()->city }} - {{ companydata()->pin }}
                Ledger`,

                footerHTML: `Prepared By: {{ Auth::user()->name }}
                            Date: {{ ncurdate() }}`,
                excelSheetName: 'Trial Balance Report'
            });

            initTableExporter({
                tableId: 'second-table',
                buttons: {
                    excel: '#export-excel2',
                    pdf: '#export-pdf2',
                    print: '#export-print2'
                },
                columns: ['Month', 'Debit', 'Credit', 'Balance'],
                headerHTML: `{{ companydata()->comp_name }}
                {{ companydata()->address2 }}
                {{ companydata()->state }} - {{ companydata()->city }} - {{ companydata()->pin }}
                Trial Balance Ledger`,

                footerHTML: `Prepared By: {{ Auth::user()->name }}
                            Date: {{ ncurdate() }}`,
                excelSheetName: 'Trial Balance Report'
            });

            initTableExporter({
                tableId: 'third-table',
                buttons: {
                    excel: '#export-excel3',
                    pdf: '#export-pdf3',
                    print: '#export-print3'
                },
                columns: ['Date', 'Vr. No.', 'Name', 'Debit', 'Credit', 'Balance'],
                headerHTML: `{{ companydata()->comp_name }}
                {{ companydata()->address2 }}
                {{ companydata()->state }} - {{ companydata()->city }} - {{ companydata()->pin }}
                Trial Balance Ledger`,

                footerHTML: `Prepared By: {{ Auth::user()->name }}
                            Date: {{ ncurdate() }}`,
                excelSheetName: 'Trial Balance Report'
            });
        });
    </script>
@endsection
