@extends('property.layouts.main')
@section('main-container')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
    <style>
    </style>

    <div class="content-body possalereg">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body box animate__animated animate__bounceIn">
                            <form action="">
                                <div class="row justify-content-around">
                                    <input type="hidden" value="{{ $comp->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ $comp->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ $fromdate }}" name="ncurdatef" id="ncurdatef">
                                    <input type="hidden" value="{{ $comp->propertyid }}" id="propertyid" name="propertyid">
                                    <input type="hidden" value="{{ $comp->comp_name }}" id="compname" name="compname">
                                    <input type="hidden" value="{{ $comp->address1 }}" id="address" name="address">
                                    <input type="hidden" value="{{ $comp->city }}" id="city" name="city">
                                    <input type="hidden" value="{{ $comp->mobile }}" id="compmob" name="compmob">
                                    <input type="hidden" value="{{ $statename }}" id="statename" name="statename">
                                    <input type="hidden" value="{{ $comp->pin }}" id="pin" name="pin">
                                    <input type="hidden" value="{{ $comp->email }}" id="email" name="email">
                                    <input type="hidden" value="{{ $comp->logo }}" id="logo" name="logo">
                                    <input type="hidden" value="{{ $comp->u_name }}" id="u_name" name="u_name">
                                    <input type="hidden" value="{{ $comp->gstin }}" id="gstin" name="gstin">
                                    <div class="text-center titlep">
                                        <h3>{{ $comp->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">
                                            {{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">Sale Register Report</p>
                                        <p style="text-align:left;margin-top:-10px; font-size:16px;">From Date: <span
                                                id="fromdatep"></span> To Date:
                                            <span id="todatep"></span>
                                        </p>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="fromdate" class="col-form-label">From Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date"
                                                value="{{ $fromdate }}" class="form-control" name="fromdate"
                                                id="fromdate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="todate" class="col-form-label">To Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date"
                                                value="{{ $fromdate }}" class="form-control" name="todate"
                                                id="todate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="itemdetails" class="col-form-label">Item Details</label>
                                        <select class="form-control" name="itemdetails" id="itemdetails">
                                            <option value="N">No</option>
                                            <option value="Y">Yes</option>
                                        </select>
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
                                            class="btn rhead btn-outline-primary" name="companylistbtn"
                                            id="companylistbtn">Company <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listedcompany" style="display:none;">
                                            <li> <input type="checkbox" id="checkallcompanies" checked>
                                                <span>Select All <span class="tcount">{{ count($company) }}</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Company Name..." class="form-control companysearch"></li>
                                            @foreach ($company as $item)
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
                                            class="btn rhead btn-outline-success" name="departlistbtn"
                                            id="departlistbtn">Outlet <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listeddepart" style="display:none;">
                                            <li> <input type="checkbox" id="checkalldepart">
                                                <span>Select All <span class="tcount">{{ count($departs) }}</span></span>
                                            </li>
                                            <li><input type="text" placeholder="Enter Outlet Name..." class="form-control outletsearch"></li>
                                            @foreach ($departs as $item)
                                                <li data-outletname="{{ $item->name }}" class="outletnameli">
                                                    <input class="departcheckbox" value="{{ $item->dcode }}"
                                                        type="checkbox">
                                                    <span>{{ $item->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-secondary" name="itemlistbtn"
                                            id="itemlistbtn">Items <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listeditems" style="display:none;">
                                            <li><input type="checkbox" id="checkallitems" checked>
                                                <span>Select All <span class="tcount"></span></span>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-3">
                                <button id="print-table" class="btn btn-primary">Print <i
                                        class="fa-solid fa-print"></i></button>
                                <button id="download-xlsx" class="btn btn-success">Excel <i
                                        class="fa fa-file-excel-o"></i></button>
                            </div>

                            <div class="mt-3" id="sale-register-table"></div>

                            <div class="paygroup">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table;
            let csrftoken = "{{ csrf_token() }}";

            $(document).on('change', '#fromdate', function() {
                validateFinancialYear('#fromdate');
            });
            $(document).on('change', '#todate', function() {
                validateFinancialYear('#todate');
            });

            function getCheckedOutletCodes() {
                return $('.departcheckbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function populateItemLists(data) {
                let itemmast = data.itemmast;

                $('#listeditems li:not(:first-child)').remove();

                $('#listeditems').append('<li><input type="text" placeholder="Enter Item Name..." class="form-control itemsearch"></li>');

                $('#listeditems').find('span.tcount').text(itemmast.length);

                itemmast.forEach((element) => {
                    $('#listeditems').append(`
                <li data-itemname="${element.Name}" class="itemnameli">
                    <input class="itemcheckbox" value="${element.Code}" type="checkbox" checked>
                    <span>${element.Name}</span>
                </li>
            `);
                });

            }

            $(document).on('change', '.departcheckbox', function() {
                let checkedOutletCodes = getCheckedOutletCodes();

                if (checkedOutletCodes.length > 0) {
                    const otpostdata = {
                        'outletcodes': checkedOutletCodes
                    };

                    const optionsot = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrftoken
                        },
                        body: JSON.stringify(otpostdata)
                    };

                    fetch('/outletitems', optionsot)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            populateItemLists(data);
                        })
                        .catch(error => {
                            console.error('Error fetching outlet items:', error);
                        })
                        .finally(() => {

                        });
                } else {
                    $('#listeditems li:not(:first-child)').remove();

                    $('#listeditems').append('<li><input type="text" placeholder="Enter Item Name..." class="form-control itemsearch"></li>');

                    $('#listeditems').find('span.tcount').text('0');

                }
            });

            $('#checkalldepart').change(function() {
                let isChecked = $(this).is(':checked');
                $('.departcheckbox').prop('checked', isChecked).trigger('change');
            });

            $('#checkallitems').change(function() {
                let isChecked = $(this).is(':checked');
                $('.itemcheckbox').prop('checked', isChecked);
            });

            dynamicSearch('.itemsearch', 'itemname', '.itemnameli');
            dynamicSearch('.outletsearch', 'outletname', '.outletnameli');
            dynamicSearch('.companysearch', 'companyname', '.companynameli');

            toggleList("#companylistbtn", "#listedcompany");
            checkAllCheckboxes("#checkallcompanies", ".companycheckbox");

            toggleList("#itemlistbtn", "#listeditems");
            checkAllCheckboxes("#checkallitems", ".itemcheckbox");

            toggleList("#departlistbtn", "#listeddepart");
            checkAllCheckboxes("#checkalldepart", ".departcheckbox");

            $(document).on('click', '#fetchbutton', function() {
                let comps = $('#company').val();
                let compname = $('#compname').val();
                let taxdetail = $('#taxdetail').val();
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                
                if (fromdate == '') {
                    pushNotify('error', 'Sale Register', 'Please Select From Date', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                    $('#fromdate').addClass('invalid');
                }
                if (todate == '') {
                    pushNotify('error', 'Sale Register', 'Pleasee Select To Date', 'fade', 300, '', '',
                    true, true, true, 2000, 20, 20, 'outline', 'right top');
                    $('#todate').addClass('invalid');
                }
                
                if (fromdate != '' && todate != '') {
                    let alloutlets = $('.departcheckbox').map(function() {
                        if ($(this).is(':checked')) {
                            return $(this).val();
                        }
                    }).get();
                    let allitems = $('.itemcheckbox').map(function() {
                        if ($(this).is(':checked')) {
                            return $(this).val();
                        }
                    }).get();
                    let allcompany = $('.companycheckbox').map(function() {
                        if ($(this).is(':checked')) {
                            return $(this).val();
                        }
                    }).get();
                    
                    if (alloutlets.length == 0) {
                        pushNotify('error', 'Sale Register', 'Please Select Outlets', 'fade', 300, '', '',
                        true, true, true, 2000, 20, 20, 'outline', 'right top');
                        return;
                    }
                    
                    if (allitems.length == 0) {
                        pushNotify('error', 'Sale Register', 'Please Select Item', 'fade', 300, '', '',
                        true, true, true, 2000, 20, 20, 'outline', 'right top');
                        return;
                    }
                    
                    showLoader();
                    
                    let fdata = new XMLHttpRequest();
                    fdata.open('POST', '/saleregfetch', true);
                    fdata.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    fdata.onreadystatechange = function() {
                        if (fdata.status === 200 && fdata.readyState === 4) {
                            let itemdetails = $('#itemdetails').val();
                            setTimeout(hideLoader, 1000);
                            let results = JSON.parse(fdata.responseText);
                            let tableData = processData(results);
                            let paygrouped = results.paygrouped;
                            
                            let paygdata = '';
                            $('.paygroup').html('');
                            paygrouped.forEach((data, index) => {
                                let dpayment = parseFloat(data.payment);
                                if (data.payment > 0.00) {
                                    paygdata +=
                                        `<p>${data.paytype} : ${dpayment.toFixed(2)}</p>`;
                                }
                            });
                            $('.paygroup').append(paygdata);
                            if (table) {
                                table.setData(tableData);
                            } else {
                                let columns = [{
                                        title: "Date",
                                        field: "date",
                                        headerWordWrap: true,
                                        minWidth: 70
                                    },
                                    {
                                        title: "Bill No",
                                        field: "billNo",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Room/Table",
                                        field: "roomTable",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Outlet Name",
                                        field: "outletName",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Goods Amt",
                                        field: "goodsAmt",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Disc",
                                        field: "disc",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Non Taxable",
                                        field: "nonTaxable",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Taxable",
                                        field: "taxable",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Tax Amt",
                                        field: "taxAmt",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Service Charge",
                                        field: "serviceCharge",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "CGST",
                                        field: "cgst",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "SGST",
                                        field: "sgst",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Addition",
                                        field: "addition",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Deduction",
                                        field: "deduction",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Round Off",
                                        field: "roundOff",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Bill Amt",
                                        field: "billAmt",
                                        hozAlign: "right",
                                        bottomCalc: "sum",
                                        bottomCalcFormatter: "money",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Bill Time",
                                        field: "billTime",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "UserName",
                                        field: "userName",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Company",
                                        field: "company",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Payment Mode",
                                        field: "paymentMode",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    },
                                    {
                                        title: "Payments",
                                        field: "payments",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    }
                                ];

                                if (itemdetails == 'Y') {
                                    columns.splice(4, 0, {
                                        title: "Item Name",
                                        field: "itemName",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    }, {
                                        title: "Qty",
                                        field: "qty",
                                        hozAlign: "right",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    }, {
                                        title: "Rate",
                                        field: "rate",
                                        hozAlign: "right",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    }, {
                                        title: "Amount",
                                        field: "amount",
                                        hozAlign: "right",
                                        headerWordWrap: true,
                                        minWidth: 100
                                    });
                                }

                                $('#fromdatep').text(dmy(fromdate));
                                $('#todatep').text(dmy(todate));

                                table = new Tabulator("#sale-register-table", {
                                    data: tableData,
                                    layout: "fitColumns",
                                    groupBy: "outletName",
                                    printHeader: $('.titlep').html(),
                                    printFooter: "<h2>Copyright @Analysis</h2>",
                                    columns: columns,
                                    rowFormatter: function(row) {
                                        if (row.getData().type === "item-detail") {
                                            row.getElement().classList.add("item-detail");
                                        }
                                    },
                                    groupHeader: function(value, count, data, group) {
                                        return value + " - " + count + " bills";
                                    },
                                    groupToggleElement: "header",
                                    groupStartOpen: true,
                                    columnCalcs: "both",
                                });
                            }

                            // Update payment group display
                            updatePaymentGroupDisplay(results.paygrouped);

                        } else {
                            setTimeout(hideLoader, 1000);
                        }
                    }
                    fdata.send(
                        `fromdate=${fromdate}&todate=${todate}&allcompany=${allcompany}&alloutlets=${alloutlets}&allitems=${allitems}&_token={{ csrf_token() }}`
                    );
                }
            });

            $("#print-table").on("click", function() {
                table.print(false, true);
            });

            $("#download-xlsx").on("click", function() {
                table.download("xlsx", "sale_register_report.xlsx", {
                    sheetName: "Sale Register"
                });
            });

            function processData(results) {
                let tableData = [];
                let groupedSales = {};
                let itemDetailsEnabled = $('#itemdetails').val() == 'Y';

                // Group sales by outlet and vno
                results.firstcond.forEach(sale => {
                    if (!groupedSales[sale.OutletName]) {
                        groupedSales[sale.OutletName] = {};
                    }
                    if (!groupedSales[sale.OutletName][sale.vno]) {
                        groupedSales[sale.OutletName][sale.vno] = [];
                    }
                    groupedSales[sale.OutletName][sale.vno].push(sale);
                });

                // Process grouped data
                for (let outletName in groupedSales) {
                    let outletSales = groupedSales[outletName];

                    for (let vno in outletSales) {
                        let saleGroup = outletSales[vno];
                        let sale = saleGroup[0];
                        let saleRow = {
                            date: dmy(sale.vdate),
                            billNo: sale.vno + (sale.delflag === 'Y' ? ' (deleted)' : ''),
                            roomTable: sale.TABLENo,
                            outletName: sale.OutletName,
                            goodsAmt: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.GoodsAmt).toFixed(2),
                            disc: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.discamt).toFixed(2),
                            nonTaxable: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.nontaxable).toFixed(2),
                            taxable: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.taxable).toFixed(2),
                            taxAmt: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.taxamount).toFixed(2),
                            serviceCharge: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.servicecharge).toFixed(2),
                            cgst: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.cgst).toFixed(2),
                            sgst: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.sgst).toFixed(2),
                            addition: parseFloat(sale.addamt).toFixed(2),
                            deduction: "0.00",
                            roundOff: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.roundoff).toFixed(2),
                            billAmt: sale.delflag === 'Y' ? "0.00" : parseFloat(sale.netamt).toFixed(2),
                            billTime: sale.vtime,
                            userName: sale.UserName,
                            company: sale.subgroupname || '',
                            paymentMode: sale.paymentmode || 'Unset',
                            payments: sale.payments
                        };

                        if (itemDetailsEnabled) {
                            saleRow.itemName = '';
                            saleRow.qty = '';
                            saleRow.rate = '';
                            saleRow.amount = '';
                        }

                        tableData.push(saleRow);

                        // Add item details if itemdetails is 'Y'
                        if (itemDetailsEnabled) {
                            saleGroup.forEach(item => {
                                let itemRow = {
                                    outletName: sale.OutletName,
                                    itemName: item.ItemName,
                                    qty: parseFloat(item.qtyiss).toFixed(2),
                                    rate: parseFloat(item.rate).toFixed(2),
                                    amount: parseFloat(item.amount).toFixed(2),
                                    type: "item-detail"
                                };
                                // Fill other fields with empty strings
                                for (let key in saleRow) {
                                    if (!(key in itemRow)) {
                                        itemRow[key] = '';
                                    }
                                }
                                tableData.push(itemRow);
                            });
                        }
                    }
                }

                return tableData;
            }

            function updatePaymentGroupDisplay(paygrouped) {
                let paygdata = '';
                paygrouped.forEach((data, index) => {
                    let dpayment = parseFloat(data.payment);
                    if (data.payment > 0.00) {
                        paygdata += `<p>${data.paytype} : ${dpayment.toFixed(2)}</p>`;
                    }
                });
                $('.paygroup').html(paygdata);
            }

            $('#checkalldepart, .departcheckbox').prop('checked', true).trigger('change');

        });
    </script>
@endsection
