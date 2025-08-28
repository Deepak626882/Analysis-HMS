@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" action="{{ url('mrentrysubmit') }}" name="mrentryform" id="mrentryform"
                                method="POST">
                                @csrf
                                <input type="hidden" name="totalitem" id="totalitem">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="mrno" class="col-form-label">MR.No</label>
                                            <input type="number" class="form-control" name="mrno" id="mrno"
                                                placeholder="Enter M.R No." required readonly>
                                            @error('mrno')
                                                <span class="text-danger"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type" class="col-form-label">Type</label>
                                            <select class="form-control" name="vtype" id="vtype" required>
                                                <option value="">Select</option>
                                                <option value="MRCR">M.R. Entry Credit</option>
                                                <option value="MRCH">M.R. Entry Cash</option>
                                            </select>
                                            @error('vtype')
                                                <span class="text-danger"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vdate" class="col-form-label">Date</label>
                                            <input type="date" value="{{ $ncurdate }}" class="form-control"
                                                name="vdate" id="vdate" required>
                                            @error('vdate')
                                                <span class="text-danger"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pono" class="col-form-label">P.O. No.</label>
                                            <input type="text" class="form-control" name="pono" id="pono"
                                                placeholder="P. O. No.">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="partydiv" class="form-group">
                                            <label for="partycode" class="col-form-label">Party</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="chalno" class="col-form-label">Challan No.</label>
                                            <input type="text" class="form-control" name="chalno" id="chalno"
                                                placeholder="Challan No." required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="chaldate" class="col-form-label">Challan Dt.</label>
                                            <input type="date" class="form-control" name="chaldate" id="chaldate"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meminvno" class="col-form-label">Inv. No.</label>
                                            <input type="text" class="form-control" name="meminvno" id="meminvno"
                                                placeholder="Inv. No." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meminvdate" class="col-form-label">Inv. Date</label>
                                            <input type="date" class="form-control" name="meminvdate" id="meminvdate">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="indentno" class="col-form-label">Indent No.</label>
                                            <input type="text" class="form-control" name="indentno" id="indentno"
                                                placeholder="Indent No.">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inspectedby" class="col-form-label">Inspected By</label>
                                            <input type="text" class="form-control" name="inspectedby"
                                                id="inspectedby" placeholder="Inspected By" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="approvedby" class="col-form-label">Approved By</label>
                                            <input type="text" class="form-control" name="approvedby" id="approvedby"
                                                placeholder="Approved By" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="remark" class="col-form-label">Remark</label>
                                            <input type="text" class="form-control" name="remark" id="remark"
                                                placeholder="Remark">
                                        </div>
                                    </div>
                                </div>
                                <div class="itemshow">
                                    <div class="addbtn text-end  mb-2">
                                        <button id="additem" type="button" class="btn btn-outline-primary">Add Item <i
                                                class="fa-solid fa-square-plus"></i></button>

                                    </div>
                                    <table id="itemtable" class="table table-itemshow table-hover">
                                        <thead class="thead-muted">
                                            <tr>
                                                <th>Item</th>
                                                <th>Specification</th>
                                                <th>Unit</th>
                                                <th>Chal. Qty</th>
                                                <th>Recd. Qty</th>
                                                <th>Rej Qty.</th>
                                                <th>Acc. Qty</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Godown</th>
                                                <th><i class="fa-solid fa-square-caret-down"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-7 mt-4 ml-auto">
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit <i
                                            class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table id="menuitem"
                                class="table table-hover table-download-with-search table-hover table-striped">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>Vno</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Party</th>
                                        <th>Chal No.</th>
                                        <th>Chal Date</th>
                                        <th>Item</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $today = date('Y-m-d');
                                        $blockDays = $enviroinv->blockdays;
                                        $laterdays = date('Y-m-d', strtotime("-{$blockDays} days"));
                                    @endphp
                                    @foreach ($data as $row)
                                        <tr>
                                            <td>{{ $row->vno }}</td>
                                            <td>{{ $row->vtype == 'MRCR' ? 'M.R. Entry Credit' : 'M.R. Entry Cash' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($row->vdate)) }}</td>
                                            <td>{{ $row->subname }}</td>
                                            <td>{{ $row->chalno }}</td>
                                            <td>{{ date('d-m-Y', strtotime($row->chaldate)) }}</td>
                                            <td>{{ $row->itemcount }}</td>
                                            <td class="ins">

                                                @if ($superwiser == '1')
                                                    <a href="updatemrentry?docid={{ $row->docid }}">
                                                        <button id="revedit" data-toggle="modal"
                                                            data-target="#updateModal"
                                                            class="btn btn-success editBtn update-btn btn-sm">
                                                            <i class="fa-regular fa-pen-to-square"></i>Edit
                                                        </button>
                                                    </a>
                                                @elseif($row->vdate > $laterdays && $row->contradocid == '' && $superwiser != '0')
                                                    <a href="updatemrentry?docid={{ $row->docid }}">
                                                        <button id="revedit" data-toggle="modal"
                                                            data-target="#updateModal"
                                                            class="btn btn-success editBtn update-btn btn-sm">
                                                            <i class="fa-regular fa-pen-to-square"></i>Edit
                                                        </button>
                                                    </a>
                                                @elseif($row->contradocid != '')
                                                    <a href="#">
                                                        <button id="revedit" data-toggle="modal"
                                                            data-target="#updateModal"
                                                            class="btn btn-success editBtn update-btn btn-sm">
                                                            <i class="fa-regular fa-pen-to-square"></i>Billed
                                                        </button>
                                                    </a>
                                                @endif

                                                <a href="{{ url('mrprinting/' . $row->docid) }}" target="_blank">
                                                    <button class="btn btn-primary btn-sm"><i class="fas fa-print"></i>
                                                        Print</button>
                                                </a>

                                                <a href="deletemrentry?docid={{ $row->docid }}">
                                                    <button class="btn btn-danger btn-sm delete-btn">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    


    <script>
        $(document).ready(function() {
            let envinventory;
            $(document).on('change', '#vtype', function() {
                let vtype = $(this).val();
                $('#partydiv').html('');

                const postdata = {
                    'vtype': vtype
                };
                const options = {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'content-type': 'application/json'
                    },
                    body: JSON.stringify(postdata)
                };
                fetch('mrentryparty', options)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        $('#mrno').val(data.mrno);
                        if (data.subgroup.length > 0) {
                            if (vtype == 'MRCR') {
                                let opt = `<label for="partycode" class="col-form-label">Party</label><select class="form-control" id="partycode" name="partycode" required>
                                    <option value=''>Select Party</option>`;
                                data.subgroup.forEach((row) => {
                                    opt +=
                                        `<option value='${row.sub_code}'>${row.name}</option>`;
                                });
                                opt += '</select>';
                                $('#partydiv').html(opt);
                                $('#chalno').prop('readonly', false);
                                $('#meminvno').prop('readonly', false);
                            } else {
                                let input =
                                    `<label for="partycode" class="col-form-label">Party</label>
                                <input type="text" placeholder="Enter Party Name" class="form-control" id="partycode" name="partycode" required>`;
                                $('#partydiv').html(input);
                                $('#chalno').prop('readonly', false);
                                $('#meminvno').prop('readonly', false);
                            }
                        } else {
                            pushNotify('error', 'MR Entry', 'Party Not Found', 'fade', 300, '', '',
                                true, true, true, 2000, 20, 20, 'outline', 'right top');
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });

            });

            let timer;
            $(document).on('input', '#chalno', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    let chalno = $(this).val();
                    if (chalno != '' && $('#partycode').val() != '') {
                        let postdata = {
                            'chalno': chalno,
                            'partycode': $('#partycode').val()
                        };
                        const options = {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(postdata)
                        };

                        fetch('checkduplicatechalan', options)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.duplicate === true) {
                                    $(this).val('');
                                    pushNotify('error', 'MR Entry', 'Chalan No. Already Exists',
                                        'fade', 300, '', '', true, true, true, 2000, 20, 20,
                                        'outline', 'right top');
                                }
                            })
                            .catch(error => {
                                console.error('There was a problem with the fetch operation:',
                                    error);
                            });
                    }
                }, 1000);
            });

            let timerinv;
            $(document).on('input', '#meminvno', function() {
                clearTimeout(timerinv);
                timerinv = setTimeout(() => {
                    let invoiceno = $(this).val();
                    if (invoiceno != '' && $('#partycode').val() != '') {
                        let postdata = {
                            'invoiceno': invoiceno,
                            'partycode': $('#partycode').val()
                        };
                        const options = {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(postdata)
                        };

                        fetch('checkduplicatememinvno', options)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.duplicate === true) {
                                    $(this).val('');
                                    pushNotify('error', 'MR Entry',
                                        'Invoice No. Already Exists', 'fade', 300, '', '',
                                        true, true, true, 2000, 20, 20, 'outline',
                                        'right top');
                                }
                            })
                            .catch(error => {
                                console.error('There was a problem with the fetch operation:',
                                    error);
                            });
                    }
                }, 1000);
            });

            $(document).on('click', '#additem', function() {
                let tbody = $('#itemtable tbody');
                fetch('purchaseitems')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.items.length > 0) {
                            let items = data.items;
                            let godown = data.godown;
                            let units = data.units;
                            let rowCount = tbody.find('tr').length;
                            let newIndex = rowCount + 1;
                            envinventory = data.envinventory;
                            $('#totalitem').val(newIndex);
                            let tr = `<tr>
                                <td><select class='form-control items' name='item${newIndex}' id='item${newIndex}' required>
                                    <option value=''>Select Item</option>
                                    ${items.map(item => `<option data-lpurrate='${item.LPurRate}' data-convratio='${item.ConvRatio}' data-unit='${item.Unit}' data-issueunit='${item.IssueUnit}' data-purchrate='${item.PurchRate}' value='${item.Code}'>${item.Name}</option>`).join('')}
                                </select>
                                <input type='hidden' name='unithidden${newIndex}' id='unithidden${newIndex}'>
                                <input type='hidden' name='wtunithidden${newIndex}' id='wtunithidden${newIndex}'>
                                <input type='hidden' name='convratio${newIndex}' id='convratio${newIndex}'>
                                </td>
                                <td><input type='text' class='form-control specification' name='specification${newIndex}' id='specification${newIndex}' placeholder='Enter Specification'></td>
                                <td><select class='form-control readonly units' name='unit${newIndex}' id='unit${newIndex}' required>
                                    <option value=''>Select Unit</option>
                                ${units.map(row => `<option value='${row.ucode}'>${row.name}</option>`).join('')}</select></td>
                                <td class='none'><select class='form-control wtunits' name='wtunit${newIndex}' id='wtunit${newIndex}'>
                                    <option value=''>Select Wt. Unit</option>
                                ${units.map(row => `<option value='${row.ucode}'>${row.name}</option>`).join('')}</select></td>
                                <td><input type='text' class='form-control chalqtys' name='chalqty${newIndex}' id='chalqty${newIndex}' placeholder='Chal. Qty.'></td>
                                <td><input type='text' class='form-control recdqtys' name='recdqty${newIndex}' id='recdqty${newIndex}' placeholder='Recd. Qty.' readonly></td>
                                <td><input type='text' class='form-control rejqtys' name='rejqty${newIndex}' id='rejqty${newIndex}' placeholder='Rej. Qty.'></td>
                                <td><input type='text' class='form-control accqtys' name='accqty${newIndex}' id='accqty${newIndex}' placeholder='Acc. Qty.' readonly></td>
                                <td class='none'><input type='hidden' class='form-control wtqtys' name='wtqty${newIndex}' id='wtqty${newIndex}' placeholder='Wt. Qty.'></td>
                                <td><input type='text' class='form-control rates' name='itemrate${newIndex}' id='itemrate${newIndex}' placeholder='Enter Rate'></td>
                                <td><input type='text' class='form-control amounts' name='amount${newIndex}' id='amount${newIndex}' placeholder='Amount'></td>
                                <td><select class='form-control godowns' name='godown${newIndex}' id='godown${newIndex}' required>
                                    <option value=''>Select Godown</option>
                                ${godown.map(row => `<option value='${row.scode}' ${envinventory.purchasegodown == row.scode ? 'selected' : ''}>${row.name}</option>`).join('')}</select></td>
                                <td><span class='removerow'><i class="fa-solid fa-eraser"></i></span></td>
                                </tr>`;
                            $('#itemtable tbody').append(tr);
                        } else {
                            pushNotify('error', 'MR Entry', 'Items Not Found', 'fade', 300, '', '',
                                true, true, true, 2000, 20, 20, 'outline', 'right top');
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });
            });

            $(document).on('click', '.removerow', function() {
                let row = $(this).closest('tr');
                let rowIndex = row.index();
                row.remove();

                $('#itemtable tbody tr').each(function(index) {
                    let adjustedIndex = index + 1;
                    $('#totalitem').val(adjustedIndex);
                    $(this).find('td:first p').text(index + 1);
                    $(this).find('select, input').each(function() {
                        let originalName = $(this).attr('name');
                        let originalId = $(this).attr('id');
                        let newName = originalName.replace(/\d+$/, adjustedIndex);
                        let newId = originalId.replace(/\d+$/, adjustedIndex);
                        $(this).attr('name', newName);
                        $(this).attr('id', newId);
                    });
                });
            });

            $(document).on('change', '.items', async function() {
                let index = $(this).closest('tr').index() + 1;
                let value = $(this).val();
                let unit = $(this).find('option:selected').data('unit');
                let issueunit = $(this).find('option:selected').data('issueunit');
                let purchrate = $(this).find('option:selected').data('purchrate');
                let convratio = $(this).find('option:selected').data('convratio');
                let lpurrate = $(this).find('option:selected').data('lpurrate');

                let itemratep = 0.00;

                if (envinventory.itemratemrbasedon === 'Purchase Rate') {
                    itemratep = purchrate;
                } else if (envinventory.itemratemrbasedon === 'Last Purchase Rate') {
                    itemratep = purchrate;
                } else if (envinventory.itemratemrbasedon === 'Party Wise Last Purchase Rate') {
                    let postdatap = {
                        itemcode: value,
                        partycode: $('#partycode').val()
                    };

                    const postdata = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify(postdatap)
                    };

                    try {
                        let response = await fetch('partywiserate', postdata);
                        let data = await response.json();

                        if (data.status === 'error') {
                            itemratep = purchrate;
                        } else {
                            itemratep = data.stock.rate;
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }

                $(`#unit${index}`).val(unit);
                $(`#unithidden${index}`).val(unit);
                $(`#wtunit${index}`).val(issueunit);
                $(`#wtunithidden${index}`).val(issueunit);
                $(`#itemrate${index}`).val(itemratep);
                $(`#convratio${index}`).val(convratio);
            });

            function sameval(firstinput, secondinput) {
                $(document).on('change', firstinput, function() {
                    let index = $(this).closest('tr').index() + 1;
                    let curval = $(`#${secondinput}${index}`).val();
                    if ($(this).val() != curval) {
                        $(this).val(curval);
                    }
                });
            }

            sameval('.units', 'unithidden');
            sameval('.wtunits', 'wtunithidden');

            function wtqty(convratio, accqty, index, rate) {
                let wtqty = parseFloat(convratio) * parseFloat(accqty);
                let amount = parseFloat(accqty) * parseFloat(rate);
                $(`#wtqty${index}`).val(wtqty.toFixed(2));
                $(`#amount${index}`).val(amount.toFixed(2));
            }

            $(document).on('input', '.chalqtys', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                let chalqty = $(this).val();
                $(`#recdqty${index}`).val(chalqty);
                $(`#accqty${index}`).val(chalqty);
                $(`#rejqty${index}`).val('0');
                wtqty($(`#convratio${index}`).val(), $(`#accqty${index}`).val(), index, $(
                    `#itemrate${index}`).val());
            });

            $(document).on('input', '.rejqtys', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                let rejqty = $(this).val();
                let chalqty = parseFloat($(`#chalqty${index}`))
                let newrecdqty = parseFloat($(`#chalqty${index}`).val()) - parseFloat(rejqty);
                $(`#recdqty${index}`).val(newrecdqty);
                $(`#accqty${index}`).val(newrecdqty);
                wtqty($(`#convratio${index}`).val(), $(`#accqty${index}`).val(), index, $(
                    `#itemrate${index}`).val())
            });

            $(document).on('input', '.rates', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                wtqty($(`#convratio${index}`).val(), $(`#accqty${index}`).val(), index, $(
                    `#itemrate${index}`).val())
            });


            $(document).on('mousedown', '.units, .wtunits', function(e) {
                e.preventDefault();
            });

            $('#mrentryform').on('submit', function(e) {
                e.preventDefault();
                let itemtable = $('#itemtable tbody tr').length;

                if (itemtable < 1) {
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Atleast Select 1 Item to Submit!',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                    return;
                } else {
                    this.submit();
                }
            });


        });
    </script>
@endsection
