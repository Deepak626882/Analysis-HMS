@extends('property.layouts.main')

@section('main-container')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

    <!-- Frontend Content -->
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form>
                                <div class="text-center titlep mb-4">
                                    <h3>{{ companydata()->comp_name }}</h3>
                                    <p class="mb-1">{{ companydata()->address1 }}</p>
                                    <p class="mb-1">{{ $statename . ' - ' . companydata()->city . ' - ' . companydata()->pin }}</p>
                                    <p class="mb-0 font-weight-bold">Stock Register Report</p>
                                </div>

                                <div class="row justify-content-around">

                                    <input type="hidden" value="{{ companydata()->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ companydata()->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ ncurdate() }}" name="ncurdatef" id="ncurdatef">
                                    <div class="">
                                        <div class="form-group">
                                            <label for="fromdate" class="col-form-label">From Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ ncurdate() }}" class="form-control"
                                                name="fromdate" id="fromdate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="todate" class="col-form-label">To Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ ncurdate() }}" class="form-control"
                                                name="todate" id="todate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="type" class="col-form-label">Type</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="All" selected>All</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <label for="valuation" class="col-form-label">Valuation</label>
                                        <select class="form-control" name="valuation" id="valuation">
                                            <option value="Actual" selected>Actual</option>
                                            <option value="LastPurchaseRate">Last Purchase Rate</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <label for="storetype" class="col-form-label">Store Type</label>
                                        <select class="form-control" name="storetype" id="storetype">
                                            <option value="main_store" selected>Main Store</option>
                                            <option value="sub_store">Sub Store</option>
                                            <option value="house_keeping">House Keeping</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <label for="godownDropdown">Godown</label>
                                        <select class="form-control" name="godownDropdown" id="godownDropdown">
                                            <option value="">Select</option>
                                            @foreach ($godown as $item)
                                                <option value="{{ $item->dcode }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-primary" name="itemgrplistbtn"
                                            id="itemgrplistbtn">Item Group <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listeditemgrp" style="display:none;">
                                            <li> <input type="checkbox" id="checkallitemgrps">
                                                <span>Select All <span class="tcount">{{ count($itemgrp) }}</span></span>
                                                @foreach ($itemgrp as $item)
                                            <li data-groupname="{{ $item->name }}" class="groupnameli">
                                                <input class="groupcheckbox" value="{{ $item->code }}" type="checkbox">
                                                <span>{{ $item->name }}</span>
                                            </li>
                                            @endforeach
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-3">
                                        <button style="width: -webkit-fill-available;" type="button"
                                            class="btn rhead btn-outline-secondary" name="itemlistbtn"
                                            id="itemlistbtn">Items <i class="fa-solid fa-angle-down"></i></button>
                                        <ul class="checkul" id="listeditems" style="display:none;">
                                            <li> <input type="checkbox" id="checkallitems">
                                                <span>Select All <span class="tcount"></span></span>
                                            </li>
                                        </ul>
                                    </div>

                                </div>

                                <div class="text-center mt-2 mb-2">
                                    <button type="button" id="refreshbutton" class="btn-refresh btn btn-success btn-sm">Refresh</button>
                                </div>

                                <div class="mt-4">
                                    <div class="custom-header" id="stockTableHeader">Stock Register</div>
                                    <div class="mt-3" id="stockTable"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#checkallitemgrps').change(function() {
                let isChecked = $(this).is(':checked');
                $('.groupcheckbox').prop('checked', isChecked);
                fetchitembygroup();
            });

            $('#checkallitems').change(function() {
                let isChecked = $(this).is(':checked');
                $('.itemcheckbox').prop('checked', isChecked);
            });

            dynamicSearch('.groupsearch', 'itemgroup', '.itemgroupli');
            dynamicSearch('.itemsearch', 'itemname', '.itemnameli');

            toggleList("#itemgrplistbtn", "#listeditemgrp");
            checkAllCheckboxes("#checkallitemgrps", ".groupcheckbox");

            toggleList("#itemlistbtn", "#listeditems");
            checkAllCheckboxes("#checkallitems", ".itemcheckbox");

            function getcheckgroupcode() {
                return $('.groupcheckbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            $(document).on('change', '.groupcheckbox', function() {
                fetchitembygroup();
            });

            function fetchitembygroup() {
                let checkedgroupcode = getcheckgroupcode();

                $('#listeditems li:not(:first-child)').remove();
                if (checkedgroupcode.length > 0) {
                    $.ajax({
                        method: 'POST',
                        url: 'getitemsbygroup',
                        data: {
                            checkedgroupcode: checkedgroupcode
                        },
                        success: function(response) {
                            let items = response;
                            $('#listeditems .tcount').text(items.length);
                            items.forEach((idata, index) => {
                                $('#listeditems').append(`
                                    <li data-itemname="${idata.Name}" class="itemnameli">
                                        <input class="itemcheckbox" value="${idata.Code}" type="checkbox" checked>
                                        <span>${idata.Name}</span>
                                    </li>
                            `);
                            });
                        },
                        error: function(errorres) {
                            console.log(errorres);
                        }
                    })
                }
            }

            const defaultStoreType = $('input[name="storeType"]:checked').val();

            // Refresh button click
            $('#refreshbutton').click(function() {
                const fromdate = $('#fromdate').val();
                const todate = $('#todate').val();
                const type = $('#type').val();
                const valuation = $('#valuation').val();
                const storeType = $('input[name="storeType"]:checked').val();
                const godown = $('#godownDropdown').val();

                let allitems = $('.itemcheckbox').map(function() {
                    if ($(this).is(':checked')) {
                        return $(this).val();
                    }
                }).get();

                if (allitems.length === 0) {
                    pushNotify('error', 'Item Wise Sale', 'Please Select Item', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                    return;
                }

                $.ajax({
                    url: '/fetchValuationData',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        fromdate,
                        todate,
                        type,
                        valuation,
                        storeType,
                        godown,
                        items: allitems
                    },
                    success: function(response) {
                        $('#stockTable').html('');

                        const rawData = response.reportdata;
                        const finalData = [];

                        rawData.forEach(item => {
                            const opQty = Number(item.opqty || 0).toFixed(2);
                            const opAmt = Number(item.opamt || 0).toFixed(2);
                            const opIssQty = Number(item.opissuedqty || 0).toFixed(2);
                            const opIssAmt = Number(item.opissuedamt || 0).toFixed(2);

                            let balQty = Number(opQty) - Number(opIssQty);
                            let balVal = Number(opAmt) - Number(opIssAmt);

                            // Push opening row
                            finalData.push({
                                group: item.itemname,
                                item_name: `${item.itemname} (${item.unitname})`,
                                type: 'OPENING',
                                vdate: '',
                                voucher_no: '',
                                particulars: '',
                                rec_qty: opQty,
                                rec_value: opAmt,
                                iss_qty: opIssQty,
                                iss_value: opIssAmt,
                                bal_qty: balQty.toFixed(2),
                                bal_value: balVal.toFixed(2)
                            });

                            let totalRecQty = Number(opQty);
                            let totalRecAmt = Number(opAmt);
                            let totalIssQty = Number(opIssQty);
                            let totalIssAmt = Number(opIssAmt);

                            if (item.transactions && item.transactions.length) {
                                item.transactions.forEach(txn => {
                                    const recQty = Number(txn.qtyrec || 0);
                                    const issQty = Number(txn.qtyiss || 0);
                                    const amt = Number(txn.amount || 0);
                                    let recVal = 0,
                                        issVal = 0;

                                    if (recQty > 0) {
                                        recVal = amt;
                                        totalRecQty += recQty;
                                        totalRecAmt += recVal;
                                    }

                                    if (issQty > 0) {
                                        issVal = amt;
                                        totalIssQty += issQty;
                                        totalIssAmt += issVal;
                                    }

                                    balQty += recQty - issQty;
                                    balVal += recVal - issVal;

                                    finalData.push({
                                        group: item.itemname,
                                        item_name: txn.vdate,
                                        type: 'TRANSACTION',
                                        vdate: txn.vdate,
                                        voucher_no: txn.vtype + '-' + txn.vno,
                                        particulars: txn.particular,
                                        rec_qty: recQty.toFixed(2),
                                        rec_value: recVal.toFixed(2),
                                        iss_qty: issQty.toFixed(2),
                                        iss_value: issVal.toFixed(2),
                                        bal_qty: balQty.toFixed(2),
                                        bal_value: balVal.toFixed(2)
                                    });
                                });
                            }

                            // Push total row for item
                            finalData.push({
                                group: item.itemname,
                                item_name: 'Total',
                                type: '',
                                vdate: '',
                                voucher_no: '',
                                particulars: '',
                                rec_qty: totalRecQty.toFixed(2),
                                rec_value: totalRecAmt.toFixed(2),
                                iss_qty: totalIssQty.toFixed(2),
                                iss_value: totalIssAmt.toFixed(2),
                                bal_qty: balQty.toFixed(2),
                                bal_value: balVal.toFixed(2)
                            });
                        });

                        // Tabulator rendering
                        const table = new Tabulator("#stockTable", {
                            data: finalData,
                            layout: "fitColumns",
                            groupBy: "group",
                            columns: [{
                                    title: "Item Name / Date",
                                    field: "item_name",
                                    widthGrow: 2
                                },
                                {
                                    title: "Type",
                                    field: "type"
                                },
                                {
                                    title: "Vou No.",
                                    field: "voucher_no"
                                },
                                {
                                    title: "Particulars / Pur Unit",
                                    field: "particulars"
                                },
                                {
                                    title: "Rec. Qty",
                                    field: "rec_qty",
                                    hozAlign: "right"
                                },
                                {
                                    title: "Rec. Value",
                                    field: "rec_value",
                                    hozAlign: "right"
                                },
                                {
                                    title: "Iss. Qty",
                                    field: "iss_qty",
                                    hozAlign: "right"
                                },
                                {
                                    title: "Iss. Value",
                                    field: "iss_value",
                                    hozAlign: "right"
                                },
                                {
                                    title: "Bal. Qty",
                                    field: "bal_qty",
                                    hozAlign: "right"
                                },
                                {
                                    title: "Bal. Value",
                                    field: "bal_value",
                                    hozAlign: "right"
                                }
                            ]
                        });
                    }


                });
            });
        });
    </script>
@endsection
