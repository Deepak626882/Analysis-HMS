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
                                    <h3>{{ $company->comp_name }}</h3>
                                    <p class="mb-1">{{ $company->address1 }}</p>
                                    <p class="mb-1">{{ $statename . ' - ' . $company->city . ' - ' . $company->pin }}</p>
                                    <p class="mb-0 font-weight-bold">Stock Register Report</p>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-2">
                                        <label>From Date</label>
                                        <input type="date" id="fromdate" class="form-control"
                                            value="{{ $ncurdate }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>To Date</label>
                                        <input type="date" id="todate" class="form-control"
                                            value="{{ $ncurdate }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Type</label>
                                        <select id="type" class="form-control">
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Valuation</label>
                                        <select id="valuation" class="form-control">
                                            <option value="Actual">Actual</option>
                                            <option value="LastPurchaseRate">Last Purchase Rate</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Store Type</label>
                                        <div class="radio-group">
                                            <label><input type="radio" name="storeType" value="Main Store" checked> Main
                                                Store</label>
                                            <label><input type="radio" name="storeType" value="Sub Store"> Sub
                                                Store</label>
                                            <label><input type="radio" name="storeType" value="House Keeping"> House
                                                Keeping</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Godown</label>
                                        <select id="godownDropdown" class="form-control">
                                            <option value="">Select Godown</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" id="refreshbutton" class="btn-refresh btn btn-success btn-sm">Refresh</button>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="section-header">Item Groups</div>
                                        <div class="table-wrapper">
                                            <table id="itemGroupTable"
                                                class="table table-sm table-bordered hover-highlight">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAllGroups">All</th>
                                                        <th>Group Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="section-header">Items</div>
                                        <div class="table-wrapper">
                                            <table id="itemTable" class="table table-sm table-bordered hover-highlight">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAllItems">All</th>
                                                        <th>Item Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
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
            const defaultStoreType = $('input[name="storeType"]:checked').val();
            loadGodowns(defaultStoreType);

            // Load Godowns
            function loadGodowns(storeType) {
                $.get(`/get-godowns?storeType=${storeType}`, function(data) {
                    const dropdown = $('#godownDropdown');
                    dropdown.empty();
                    if (data.length > 1) {
                        dropdown.append(`<option value="">All</option>`);
                    }
                    data.forEach(g => {
                        dropdown.append(`<option value="${g.dcode}">${g.name}</option>`);
                    });

                    loadItemGroupsAndItems();
                });
            }

            let allItems = [];

            function loadItemGroupsAndItems() {
                const godown = $('#godownDropdown').val();
                const type = $('#type').val();
                const valuation = $('#valuation').val();

                if (!godown) return;

                $.get('/get-items-and-groups', {
                    godown,
                    item_type: type,
                    valuation
                }, function(res) {
                    const groupBody = $('#itemGroupTable tbody');
                    groupBody.empty();
                    $('#itemTable tbody').empty();

                    res.groups.forEach(group => {
                        groupBody.append(`
                        <tr>
                            <td><input type="checkbox" class="groupCheckbox" data-group-id="${group.id}" checked></td>
                            <td>${group.name}</td>
                        </tr>
                    `);
                    });

                    allItems = res.items;

                    $('#selectAllGroups').prop('checked', res.groups.length > 0);
                    $('#selectAllItems').prop('checked', res.items.length > 0);

                    bindGroupCheckboxEvent();
                    filterItemsBySelectedGroups();
                });
            }

            function filterItemsBySelectedGroups() {
                const selectedGroupIds = $('.groupCheckbox:checked').map(function() {
                    return $(this).data('group-id');
                }).get();

                const filteredItems = allItems.filter(item =>
                    selectedGroupIds.includes(item.group_id || item.ItemGroup || item.item_group || item.group)
                );

                const itemBody = $('#itemTable tbody');
                itemBody.empty();

                filteredItems.forEach(item => {
                    itemBody.append(`
                    <tr>
                        <td><input type="checkbox" class="itemCheckbox" data-item-id="${item.id}" checked></td>
                        <td>${item.iname}</td>
                    </tr>
                `);
                });

                $('#selectAllItems').prop('checked',
                    $('.itemCheckbox').length > 0 &&
                    $('.itemCheckbox:checked').length === $('.itemCheckbox').length
                );
            }

            $(document).on('change', '.itemCheckbox', function() {
                const total = $('.itemCheckbox').length;
                const checked = $('.itemCheckbox:checked').length;
                $('#selectAllItems').prop('checked', total > 0 && total === checked);
            });

            function bindGroupCheckboxEvent() {
                $('.groupCheckbox').on('change', function() {
                    const total = $('.groupCheckbox').length;
                    const checked = $('.groupCheckbox:checked').length;
                    $('#selectAllGroups').prop('checked', total > 0 && total === checked);
                    filterItemsBySelectedGroups();
                });

                $('#selectAllGroups').on('change', function() {
                    $('.groupCheckbox').prop('checked', this.checked);
                    filterItemsBySelectedGroups();
                });
            }

            // Refresh button click
            $('#refreshbutton').click(function() {
                const fromdate = $('#fromdate').val();
                const todate = $('#todate').val();
                const type = $('#type').val();
                const valuation = $('#valuation').val();
                const storeType = $('input[name="storeType"]:checked').val();
                const godown = $('#godownDropdown').val();

                const selectedItemIds = $('.itemCheckbox:checked').map(function() {
                    return $(this).data('item-id');
                }).get();

                if (selectedItemIds.length === 0) {
                    alert("Please select at least one item.");
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
                        items: selectedItemIds
                    },
                    success: function(response) {
                        $('#stockTable').html('');

                        const rawData = response.reportdata;
                        const finalData = [];

                        rawData.forEach(item => {
                            // Set default values with proper .toFixed(2)
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

            // Store Type Change → Load Godowns
            $('input[name="storeType"]').change(function() {
                const storeType = $(this).val();
                loadGodowns(storeType);
            });

            // Dropdown changes → load data
            $('#godownDropdown, #type, #valuation').change(loadItemGroupsAndItems);

            // Select All Items Checkbox Logic
            $('#selectAllItems').on('change', function() {
                const checked = $(this).is(':checked');
                $('.itemCheckbox').prop('checked', checked);
            });
        });
    </script>
@endsection
