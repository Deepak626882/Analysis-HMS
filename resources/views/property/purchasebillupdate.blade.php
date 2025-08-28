@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" action="{{ url('purchasebillupdate') }}" name="purchaseentryform" id="purchaseentryform" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ count($stockdata) }}" name="totalitem" id="totalitem">
                                <input type="hidden" name="taxableamt" id="taxableamt">
                                <input type="hidden" name="partygstin" id="partygstin">
                                <input type="hidden" name="gindocid" id="gindocid">
                                <input type="hidden" value="{{ $data->mrno == '0' ? 'N' : 'Y' }}" name="mritemyn" id="mritemyn">
                                <input type="hidden" value="{{ $data->docid }}" name="olddocid" id="olddocid">
                                <input type="hidden" value="{{ $data->vno }}" name="oldvno" id="oldvno">
                                <input type="hidden" value="{{ $data->vtype }}" name="oldvtype" id="oldvtype">
                                <input type="hidden" value="{{ $data->Party }}" name="oldpartycode" id="oldpartycode">
                                <input type="hidden" value="{{ $data->vprefix }}" name="oldvprefix" id="oldvprefix">
                                <input type="hidden" value="{{ $data->vdate }}" name="oldvdate" id="oldvdate">
                                <div class="container-fluid">
                                    <div class="row">
                                        <!-- Left small section -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <!-- First Column -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mrno" class="col-form-label">VR.No</label>
                                                        <input type="number" value="{{ $data->vno }}" class="form-control" name="mrno" id="mrno" required readonly>
                                                        @error('mrno')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="vdate" class="col-form-label">Date</label>
                                                        <input type="date" value="{{ $data->vdate }}" class="form-control" name="vdate" id="vdate" required>
                                                        @error('vdate')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                    <div id="partydiv" class="form-group">
                                                        @if ($data->vtype == 'PBPB')
                                                            <label for="partycode" class="col-form-label">Party</label>
                                                            <select class="form-control" id="partycode" name="partycode">
                                                                <option value="">Select Party</option>
                                                                @foreach ($party as $item)
                                                                    <option data-gstin="{{ $item->gstin }}" value="{{ $item->sub_code }}" {{ $data->Party == $item->sub_code ? 'selected' : '' }}>
                                                                        {{ $item->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <label for="partycodet" class="col-form-label mt-2">Party Name</label>
                                                            <input type="text" class="form-control" value="{{ $gin->partyname ?? '' }}" placeholder="Enter Party Name" name="partycode" id="partycodet">
                                                            <span class="text-purple nowrap" id="gstinnumber"></span>
                                                        @endif

                                                    </div>
                                                    <div class="form-group">
                                                        <label for="billno" class="col-form-label">Bill No.</label>
                                                        <input type="text" value="{{ $data->partybillno }}" class="form-control" placeholder="Enter Bill No." name="billno" id="billno" required>
                                                    </div>
                                                </div>
                                                <!-- Second Column -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vtype" class="col-form-label">Type</label>
                                                        <select class="form-control" name="vtype" id="vtype" required>
                                                            <option value="">Select</option>
                                                            <option value="PBPC" {{ $data->vtype == 'PBPC' ? 'selected' : '' }}>Purchase Bill(Cash)</option>
                                                            <option value="PBPB" {{ $data->vtype == 'PBPB' ? 'selected' : '' }}>Purchase Bill(Credit)</option>
                                                            <option value="PRPC" {{ $data->vtype == 'PRPC' ? 'selected' : '' }}>Purchase Return(Cash)</option>
                                                            <option value="PRPB" {{ $data->vtype == 'PRPB' ? 'selected' : '' }}>Purchase Return(Credit)</option>
                                                        </select>
                                                        @error('vtype')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="billdate" class="col-form-label">Bill Date</label>
                                                        <input type="date" value="{{ $data->partybilldt }}" class="form-control" name="billdate" id="billdate" required>
                                                    </div>
                                                    <div id="exmrnodiv" class="form-group">
                                                        <label for="exmrno" class="col-form-label">Mr. No.</label>
                                                        <span id="exmrno" class="font-weight-bold">{{ $data->mrno }}</span>
                                                        {{-- 
                                                        <select class="form-control" name="exmrno" id="exmrno">
                                                            <option value=''>Select</option>
                                                            @if ($data->mrno != '0')
                                                                <option value="{{ $data->mrno }}">{{ $data->mrno }}</option>
                                                            @endif
                                                        </select> 
                                                        --}}
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="billimage" class="col-form-label">Bill Image</label>
                                                        <input type="file" class="form-control" name="billimage" id="billimage">
                                                        <span id="previewbtn" style="display: none;">Preview</span>
                                                    </div>
                                                </div>

                                                <!-- Hidden Field -->
                                                <input type="hidden" class="form-control" name="gstin" id="gstin" placeholder="Enter GSTIN.">

                                                <!-- Modal for Image Preview -->
                                                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img id="imagePreview" src="" alt="Image Preview" class="img-fluid">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!-- inner row ends -->
                                        </div> <!-- left col-md-6 ends -->

                                        <!-- Right Side Empty -->
                                        <div class="col-md-6">
                                            <!-- Empty space for future content -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input invtype" type="radio" value="saleinvoice" name="invtype"
                                                id="saleinvoice" {{ $data->invoicetype == 'saleinvoice' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="saleinvoice">Sale Invoice</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input invtype" type="radio" value="taxinvoice" name="invtype"
                                                id="taxinvoice" {{ $data->invoicetype == 'taxinvoice' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="taxinvoice">Tax Invoice</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input invtype" type="radio" value="otherinvoice" name="invtype"
                                                id="otherinvoice" {{ $data->invoicetype == 'otherinvoice' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="otherinvoice">Other</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="itemshow">
                                    <div class="addbtn text-end  mb-2">
                                        <button id="additem" type="button" class="btn btn-outline-primary">Add Item <i class="fa-solid fa-square-plus"></i></button>

                                    </div>
                                    <table id="itemtable" class="table table-itemshow table-hover">
                                        <thead class="thead-muted">
                                            <tr>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Godown</th>
                                                <th>Tax Structure</th>
                                                <th>A/C Name</th>
                                                <th><i class="fa-solid fa-square-caret-down"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stockdata as $item)
                                                <tr>
                                                    <td>
                                                        <select class='form-control items' name='item{{ $item->sno }}' id='item{{ $item->sno }}' required>
                                                            <option value=''>Select Item</option>
                                                            @foreach ($items as $itemr)
                                                                <option data-accode="{{ $itemr->AcCode }}"
                                                                    {{ $itemr->Code == $item->item ? 'selected' : '' }}
                                                                    data-convratio='{{ $itemr->ConvRatio }}'
                                                                    data-unit='{{ $itemr->Unit }}'
                                                                    data-issueunit='{{ $itemr->IssueUnit }}'
                                                                    data-purchrate='{{ $itemr->PurchRate }}'
                                                                    data-taxcode='{{ $itemr->taxcodes }}'
                                                                    data-strcode='{{ $itemr->str_code }}'
                                                                    data-taxrate='{{ $itemr->taxrate }}'
                                                                    value='{{ $itemr->Code }}'>
                                                                    {{ $itemr->Name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type='hidden' name='unithidden{{ $item->sno }}' id='unithidden{{ $item->sno }}'>
                                                        <input type='hidden' name='wtunithidden{{ $item->sno }}' id='wtunithidden{{ $item->sno }}'>
                                                        <input value='{{ $item->sno }}' type='hidden' name='issno{{ $item->sno }}' id='issno{{ $item->sno }}'>
                                                        <input value='' type='hidden' name='convratio{{ $item->sno }}' id='convratio{{ $item->sno }}'>
                                                        <input value='{{ $item->chalqty }}' type='hidden' class='form-control chalqtys' name='chalqty{{ $item->sno }}' id='chalqty{{ $item->sno }}' placeholder='Chal. Qty.'>
                                                        <input value='{{ $item->recdqty }}' type='hidden' class='form-control recdqtys' name='recdqty{{ $item->sno }}' id='recdqty{{ $item->sno }}' placeholder='Recd. Qty.' readonly>
                                                        <input value='{{ $item->rejqty }}' type='hidden' class='form-control rejqtys' name='rejqty{{ $item->sno }}' id='rejqty{{ $item->sno }}' placeholder='Rej. Qty.'>
                                                        <input value='{{ $item->accqty }}' type='hidden' class='form-control accqtys' name='accqty{{ $item->sno }}' id='accqty{{ $item->sno }}' placeholder='Acc. Qty.' readonly>
                                                        <input value='{{ $item->taxrate }}' type='hidden' class='form-control taxrates' name='taxrate{{ $item->sno }}' id='taxrate{{ $item->sno }}' placeholder='Tax Rate' readonly>
                                                        <input value='{{ $item->taxamt }}' type='hidden' class='form-control taxeamts' name='taxamt{{ $item->sno }}' id='taxamt{{ $item->sno }}' placeholder='Tax Rate' readonly>
                                                        <input value='{{ $item->qtyrec }}' type='hidden' class='form-control fixqtys' name='fixqty{{ $item->sno }}' id='fixqty{{ $item->sno }}' placeholder='Mr. Qty.'>
                                                        <input value='{{ $item->specification }}' type='hidden' class='form-control specification' name='specification{{ $item->sno }}' id='specification{{ $item->sno }}' placeholder='Enter Specification'>
                                                        <input value='{{ $item->taxcodes }}' type='hidden' class='form-control taxcodes' name='taxcode{{ $item->sno }}' id='taxcode{{ $item->sno }}' placeholder='Tax Code' readonly>

                                                        <select class='form-control none wtunits' name='wtunit{{ $item->sno }}' id='wtunit{{ $item->sno }}'>
                                                            <option value=''>Select Wt. Unit</option>
                                                            @foreach ($units as $row)
                                                                <option {{ $row->ucode == $item->issueunit ? 'selected' : '' }} value='{{ $row->ucode }}'>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class='form-control readonly units' name='unit{{ $item->sno }}' id='unit{{ $item->sno }}' required>
                                                            <option value=''>Select Unit</option>
                                                            @foreach ($units as $row)
                                                                <option {{ $row->ucode == $item->unit ? 'selected' : '' }} value='{{ $row->ucode }}'>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input value='{{ $item->qtyrec }}' type='text' class='form-control qtyisss' name='qtyiss{{ $item->sno }}' id='qtyiss{{ $item->sno }}' placeholder='Item. Qty.'>
                                                    </td>
                                                    <td class='none'>
                                                        <input value='{{ $item->recdunit }}' type='hidden' class='form-control wtqtys' name='wtqty{{ $item->sno }}' id='wtqty{{ $item->sno }}' placeholder='Wt. Qty.'>
                                                    </td>
                                                    <td>
                                                        <input value='{{ $item->itemrate }}' type='text' class='form-control rates' name='itemrate{{ $item->sno }}' id='itemrate{{ $item->sno }}' placeholder='Enter Rate'>
                                                    </td>
                                                    <td>
                                                        <input value='{{ $item->amount }}' type='text' class='form-control amounts' name='amount{{ $item->sno }}' id='amount{{ $item->sno }}' placeholder='Amount'>
                                                        <input value='{{ $item->amount - $item->discamt }}' type='hidden' class='form-control discamts' name='discamt{{ $item->sno }}' id='discamt{{ $item->sno }}' placeholder='Amount'>
                                                    </td>
                                                    <td>
                                                        <select class='form-control godowns' name='godown{{ $item->sno }}' id='godown{{ $item->sno }}' required>
                                                            <option value=''>Select Godown</option>
                                                            @foreach ($godown as $row)
                                                                <option {{ $row->scode == $item->godcode ? 'selected' : '' }} value='{{ $row->scode }}'>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class='form-control taxstructures' name='taxstructure{{ $item->sno }}' id='taxstructure{{ $item->sno }}' required>
                                                            <option value=''>Select Tax Structure</option>
                                                            @foreach ($taxstrudata as $row)
                                                                <option data-taxcode="{{ $row->taxcodes }}" data-rate='{{ $row->taxrate ?? '0' }}' {{ $row->str_code == $item->taxstru ? 'selected' : '' }} value='{{ $row->str_code }}'>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class='form-control ledgers' name='ledger{{ $item->sno }}' id='ledger{{ $item->sno }}' required>
                                                            <option value=''>Select Account</option>
                                                            @foreach ($ledgerdata as $row)
                                                                <option value='{{ $row->sub_code }}' {{ $row->sub_code == $item->accode ? 'selected' : '' }}>{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <span class='removerow'><i class="fa-solid fa-eraser"></i></span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="billing-container">

                                    @foreach ($suntrandata as $index => $item)
                                        @if ($index === 0)
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input type="text" class="billing-input" name="totalamount" id="totalamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'discount')
                                            <div class="billing-row">
                                                <div class="d-flex align-items-center">
                                                    <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                    <input type="text" class="billing-input discountfix" id="discountfix" name="discountfix" value="{{ $item->svalue }}">
                                                </div>
                                                <input type="text" class="billing-input discountsundry" id="discountsundry" name="discountsundry" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'service charge')
                                            <div class="billing-row">
                                                <div class="d-flex align-items-center">
                                                    <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                    <input type="text" class="billing-input" id="servicechargefix" name="servicechargefix" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->svalue }}">
                                                </div>
                                                <input type="text" class="billing-input" id="servicechargeamount" name="servicechargeamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'cgst')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input data-revcode="{{ $item->revcode }}" type="text" class="billing-input sevenem" id="cgstamount" name="cgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'sgst')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input data-revcode="{{ $item->revcode }}" type="text" class="billing-input sevenem" id="sgstamount" name="sgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'addition')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input type="text" class="billing-input additionamount sevenem" id="additionamount" name="additionamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'deduction')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input type="text" class="billing-input deductionamount sevenem" id="deductionamount" name="deductionamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'igst')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input data-revcode="{{ $item->revcode }}" type="text" class="billing-input sevenem" id="igstamount" name="igstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'round off')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input type="text" class="billing-input sevenem" id="roundoffamount" name="roundoffamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif

                                        @if (strtolower($item->nature) == 'net amount')
                                            <div class="billing-row">
                                                <span class="billing-label {{ $item->bold == 'Y' ? 'bold-text' : '' }}">{{ $item->disp_name }}</span>
                                                <input type="text" class="billing-input sevenem" id="netamount" name="netamount" {{ $item->automanual == 'A' ? 'readonly' : '' }} value="{{ $item->amount }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-7 mt-4 ml-auto">
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Update <i class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            let exmrno = $('#exmrno').text();
            if (exmrno != '') {
                $('.addbtn').fadeOut('1500');
            }
            var totalqty;
            $('.partycodet').fadeOut();
            $(document).on('change', '#vtype', function() {
                let vtype = $(this).val();
                $('#exmrno').empty('');

                if (vtype == 'PBPC') {
                    $('.partycode').fadeOut('500');
                    $('.partycode').val('');
                    $('#gstinnumber').text('');
                    $('#partygstin').val('');
                    $('#partycodet').val('');
                    setTimeout(() => {
                        $('.partycodet').fadeIn('500');
                    }, 500);
                } else if (vtype == 'PBPB') {
                    $('.partycodet').fadeOut('500');
                    $('.partycode').val('');
                    setTimeout(() => {
                        $('.partycode').fadeIn('500');
                    }, 500);
                }

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
                fetch('purchasebillno', options)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        $('#mrno').val(data.mrno);
                        if (vtype == 'PBPC') {
                            let opt = `<option value=''>Select</option>`;
                            data.cashmrentry.forEach((row, index) => {
                                opt += `<option data-docid='${row.docid}' value='${row.vno}'>${row.vno}</option>`
                            });
                            $('#exmrno').append(opt);
                        } else {
                            let opt = `<option value=''>Select</option>`;
                            $('#exmrno').append(opt);
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });
            });

            let docid;
            $(document).on('change', '#partycode', function() {
                $('#gstin, #exmrno').html('');
                $('#gstin, #exmrno').prop('readonly', false);
                $('#gstinnumber').text('');
                $('#partygstin').val('');
                if ($(this).val() != '') {
                    let gstin = $(this).find('option:selected').data('gstin');
                    docid = $(this).find('option:selected').data('docid');
                    $('#partycodet').val($(this).val());
                    if (gstin != '') {
                        $('#gstin').val(gstin);
                        $('#gstinnumber').text(`GSTIN: ${gstin}`);
                        $('#gstin').prop('readonly', true);
                        $('#partygstin').val(gstin);
                    } else {
                        $('#gstin').val('');
                        $('#gstinnumber').text('');
                        $('#gstin').prop('readonly', false);
                        $('#partygstin').val('');
                        $('.invtype').each(function() {
                            $(this).prop('checked', false);
                        });
                    }

                    let partycode = $(this).val();
                    const postdata = {
                        'partycode': partycode
                    };
                    const options = {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify(postdata)
                    };
                    fetch('partydata', options)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.gin) {
                                let opt = `<option value=''>Select</option>`;
                                data.gin.forEach((row, index) => {
                                    opt += `<option value='${row.vno}'>${row.vno}</option>`
                                });
                                $('#exmrno').append(opt);
                            } else {
                                $('#exmrno').val('');
                            }
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                        });
                }
            });
            let taxstrudata;
            let ledgerdata;
            let envinventory;
            $(document).on('change', '#exmrno', function() {
                if ($(this).val() != '') {
                    let docid = $(this).val();
                    const postdata = {
                        'docid': docid,
                        'partycode': $('#partycode').val()
                    };

                    const options = {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify(postdata)
                    };

                    fetch('mritems', options)
                        .then(response => response.json())
                        .then(data => {
                            if (data.items.length > 0) {
                                $('#gindocid').val(docid);
                                $('#mritemyn').val('Y');
                                let stockitems = data.stockitems;
                                let items = data.items;
                                let godown = data.godown;
                                taxstrudata = data.taxstrudata;
                                ledgerdata = data.ledgerdata;
                                let units = data.units;
                                $('#totalitem').val(stockitems.length);
                                let tr = '';
                                let totalqty = 0;
                                let ratesum = 0.00;
                                let amountsum = 0.00;
                                stockitems.forEach((sitem, index) => {
                                    let newIndex = index + 1;
                                    totalqty += parseFloat(sitem.qtyrec);
                                    ratesum += parseFloat(sitem.rate);
                                    amountsum += parseFloat(sitem.amount);
                                    tr += `<tr>
                                            <td><select class='form-control items' name='item${newIndex}' id='item${newIndex}' required>
                                                <option value=''>Select Item</option>
                                                ${items.map(item => `<option data-accode=${item.AcCode} ${item.Code == sitem.item ? 'selected' : ''} data-convratio='${item.ConvRatio}' data-unit='${item.Unit}' data-issueunit='${item.IssueUnit}' data-purchrate='${item.PurchRate}' value='${item.Code}'>${item.Name}</option>`).join('')}
                                            </select>
                                            <input type='hidden' name='unithidden${newIndex}' id='unithidden${newIndex}'>
                                            <input type='hidden' name='wtunithidden${newIndex}' id='wtunithidden${newIndex}'>
                                            <input value='${sitem.sno}' type='hidden' name='issno${newIndex}' id='issno${newIndex}'>
                                            <input value='${sitem.convratio}' type='hidden' name='convratio${newIndex}' id='convratio${newIndex}'>
                                            <input value='${sitem.chalqty}' type='hidden' class='form-control chalqtys' name='chalqty${newIndex}' id='chalqty${newIndex}' placeholder='Chal. Qty.'>
                                            <input value='${sitem.recdqty}' type='hidden' class='form-control recdqtys' name='recdqty${newIndex}' id='recdqty${newIndex}' placeholder='Recd. Qty.' readonly>
                                            <input value='${sitem.rejqty}' type='hidden' class='form-control rejqtys' name='rejqty${newIndex}' id='rejqty${newIndex}' placeholder='Rej. Qty.'>
                                            <input value='${sitem.accqty}' type='hidden' class='form-control accqtys' name='accqty${newIndex}' id='accqty${newIndex}' placeholder='Acc. Qty.' readonly>
                                            <input value='${sitem.taxrate}' type='hidden' class='form-control taxrates' name='taxrate${newIndex}' id='taxrate${newIndex}' placeholder='Tax Rate' readonly>
                                            <input type='hidden' class='form-control taxeamts' name='taxamt${newIndex}' id='taxamt${newIndex}' placeholder='Tax Rate' readonly>
                                            <input value='${sitem.taxcodes}' type='hidden' class='form-control taxcodes' name='taxcode${newIndex}' id='taxcode${newIndex}' placeholder='Tax Code' readonly>
                                            <input value='${sitem.qtyrec}' type='hidden' class='form-control fixqtys' name='fixqty${newIndex}' id='fixqty${newIndex}' placeholder='Mr. Qty.'>
                                            <input value='${sitem.specification}' type='hidden' class='form-control specification' name='specification${newIndex}' id='specification${newIndex}' placeholder='Enter Specification'>
                                            <select class='form-control none wtunits' name='wtunit${newIndex}' id='wtunit${newIndex}'>
                                                <option value=''>Select Wt. Unit</option>
                                            ${units.map(row => `<option ${row.ucode == sitem.issueunit ? 'selected' : ''} value='${row.ucode}'>${row.name}</option>`).join('')}</select>
                                            </td>
                                            <td><select class='form-control readonly units' name='unit${newIndex}' id='unit${newIndex}' required>
                                                <option value=''>Select Unit</option>
                                            ${units.map(row => `<option ${row.ucode == sitem.unit ? 'selected' : ''} value='${row.ucode}'>${row.name}</option>`).join('')}</select></td>
                                            <td><input value='${sitem.qtyrec}' type='text' class='form-control qtyisss' name='qtyiss${newIndex}' id='qtyiss${newIndex}' placeholder='Item. Qty.'></td>
                                            <td class='none'><input value='${sitem.recdunit}' type='hidden' class='form-control wtqtys' name='wtqty${newIndex}' id='wtqty${newIndex}' placeholder='Wt. Qty.'></td>
                                            <td><input value='${sitem.rate}' type='text' class='form-control rates' name='itemrate${newIndex}' id='itemrate${newIndex}' placeholder='Enter Rate'></td>
                                            <td>
                                                <input value='${sitem.amount}' type='text' class='form-control amounts' name='amount${newIndex}' id='amount${newIndex}' placeholder='Amount'>
                                                <input value='${sitem.amount}' type='hidden' class='form-control discamts' name='discamt${newIndex}' id='discamt${newIndex}' placeholder='Amount'>
                                            </td>
                                            <td><select class='form-control godowns' name='godown${newIndex}' id='godown${newIndex}' required>
                                                <option value=''>Select Godown</option>
                                            ${godown.map(row => `<option ${row.scode == sitem.godowncode ? 'selected' : ''} value='${row.scode}'>${row.name}</option>`).join('')}</select></td>
                                            <td><select class='form-control taxstructures' name='taxstructure${newIndex}' id='taxstructure${newIndex}' required>
                                                <option value=''>Select Tax Structure</option>
                                            ${taxstrudata.map(row => `<option data-rate='${row.taxratesum ?? '0'}' ${row.str_code == sitem.str_code ? 'selected' : ''} value='${row.str_code}'>${row.name}</option>`).join('')}</select></td>
                                            <td><select class='form-control ledgers' name='ledger${newIndex}' id='ledger${newIndex}' required>
                                                <option value=''>Select Account</option>
                                            ${ledgerdata.map(row => `<option value='${row.sub_code}' ${row.sub_code == sitem.AcCode ? 'selected' : ''}>${row.name}</option>`).join('')}</select></td>
                                            <td><span class='removerow'><i class="fa-solid fa-eraser"></i></span></td>
                                        </tr>`;
                                });
                                let discountfix = $('#discountfix').val();
                                calculateamt();
                                $('.addbtn').fadeOut('1500');

                                $('#itemtable tbody').append(tr);
                            } else {
                                pushNotify('error', 'MR Entry', 'Items Not Found', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                                $('.addbtn').fadeIn('1500');
                            }
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                        });
                }
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
                            $('#mritemyn').val('N');
                            let items = data.items;
                            let godown = data.godown;
                            let units = data.units;
                            taxstrudata = data.taxstrudata;
                            ledgerdata = data.ledgerdata;
                            let rowCount = tbody.find('tr').length;
                            let newIndex = rowCount + 1;
                            envinventory = data.envinventory;
                            $('#totalitem').val(newIndex);
                            let tr = `<tr>
                                <td><select class='form-control items' name='item${newIndex}' id='item${newIndex}' required>
                                    <option value=''>Select Item</option>
                                    ${items.map(item => `<option data-accode=${item.AcCode} data-strcode='${item.str_code}' data-taxrate='${item.taxrate}' data-taxcode='${item.taxcodes}' data-convratio='${item.ConvRatio}' data-unit='${item.Unit}' data-issueunit='${item.IssueUnit}' data-purchrate='${item.PurchRate}' value='${item.Code}'>${item.Name}</option>`).join('')}
                                </select>
                                <input type='hidden' name='unithidden${newIndex}' id='unithidden${newIndex}'>
                                <input type='hidden' name='wtunithidden${newIndex}' id='wtunithidden${newIndex}'>
                                <input type='hidden' name='convratio${newIndex}' id='convratio${newIndex}'>
                                <input type='hidden' class='form-control specification' name='specification${newIndex}' id='specification${newIndex}' placeholder='Enter Specification'>
                                <input type='hidden' class='form-control chalqtys' name='chalqty${newIndex}' id='chalqty${newIndex}' placeholder='Chal. Qty.'>
                                <input type='hidden' class='form-control recdqtys' name='recdqty${newIndex}' id='recdqty${newIndex}' placeholder='Recd. Qty.' readonly>
                                <input type='hidden' class='form-control rejqtys' name='rejqty${newIndex}' id='rejqty${newIndex}' placeholder='Rej. Qty.'>
                                <input type='hidden' class='form-control accqtys' name='accqty${newIndex}' id='accqty${newIndex}' placeholder='Acc. Qty.' readonly>
                                <input type='hidden' class='form-control taxrates' name='taxrate${newIndex}' id='taxrate${newIndex}' placeholder='Tax Rate' readonly>
                                <input type='hidden' class='form-control taxeamts' name='taxamt${newIndex}' id='taxamt${newIndex}' placeholder='Tax Rate' readonly>
                                <input type='hidden' class='form-control taxcodes' name='taxcode${newIndex}' id='taxcode${newIndex}' placeholder='Tax Code' readonly>
                                </td>
                                <td><select class='form-control readonly units' name='unit${newIndex}' id='unit${newIndex}' required>
                                    <option value=''>Select Unit</option>
                                ${units.map(row => `<option value='${row.ucode}'>${row.name}</option>`).join('')}</select></td>
                                <td class='none'><select class='form-control wtunits' name='wtunit${newIndex}' id='wtunit${newIndex}'>
                                    <option value=''>Select Wt. Unit</option>
                                ${units.map(row => `<option value='${row.ucode}'>${row.name}</option>`).join('')}</select></td>
                                <td><input value='0' type='text' class='form-control qtyisss' name='qtyiss${newIndex}' id='qtyiss${newIndex}' placeholder='Item. Qty.'></td>
                                <td class='none'><input type='hidden' class='form-control wtqtys' name='wtqty${newIndex}' id='wtqty${newIndex}' placeholder='Wt. Qty.'></td>
                                <td><input type='text' class='form-control rates' name='itemrate${newIndex}' id='itemrate${newIndex}' placeholder='Enter Rate'></td>
                                <td>
                                    <input type='text' class='form-control amounts' name='amount${newIndex}' id='amount${newIndex}' placeholder='Amount'>
                                    <input type='hidden' class='form-control discamts' name='discamt${newIndex}' id='discamt${newIndex}' placeholder='Amount'>
                                </td>
                                <td><select class='form-control godowns' name='godown${newIndex}' id='godown${newIndex}' required>
                                    <option value=''>Select Godown</option>
                                ${godown.map(row => `<option value='${row.scode}' ${envinventory.purchasegodown == row.scode ? 'selected' : ''}>${row.name}</option>`).join('')}</select></td>
                                 <td><select class='form-control taxstructures' name='taxstructure${newIndex}' id='taxstructure${newIndex}' required>
                                    <option value=''>Select Tax Structure</option>
                                ${taxstrudata.map(row => `<option data-rate='${row.taxratesum ?? '0'}' value='${row.str_code}'>${row.name}</option>`).join('')}</select></td>
                                <td><select class='form-control ledgers' name='ledger${newIndex}' id='ledger${newIndex}' required>
                                    <option value=''>Select Account</option>
                                ${ledgerdata.map(row => `<option value='${row.sub_code}'>${row.name}</option>`).join('')}</select></td>
                                <td><span class='removerow'><i class="fa-solid fa-eraser"></i></span></td>
                                </tr>`;
                            $('#itemtable tbody').append(tr);
                            $('#exmrnodiv').fadeOut(1500);
                            calculateamt();
                        } else {
                            pushNotify('error', 'MR Entry', 'Items Not Found', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                            $('#exmrnodiv').fadeIn(1500);
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
                calculateamt();
            });

            // $(document).on('change', '.items', function() {
            //     let index = $(this).closest('tr').index() + 1;
            //     let value = $(this).val();
            //     let unit = $(this).find('option:selected').data('unit');
            //     let issueunit = $(this).find('option:selected').data('issueunit');
            //     let purchrate = $(this).find('option:selected').data('purchrate');
            //     let convratio = $(this).find('option:selected').data('convratio');
            //     let taxcode = $(this).find('option:selected').data('taxcode');
            //     let taxrate = $(this).find('option:selected').data('taxrate');
            //     let strcode = $(this).find('option:selected').data('strcode');
            //     let accode = $(this).find('option:selected').data('accode');
            //     $(`#unit${index}`).val(unit);
            //     $(`#unithidden${index}`).val(unit);
            //     $(`#wtunit${index}`).val(issueunit);
            //     $(`#wtunithidden${index}`).val(issueunit);
            //     $(`#itemrate${index}`).val(purchrate);
            //     $(`#convratio${index}`).val(convratio);
            //     $(`#taxcode${index}`).val(taxcode);
            //     $(`#taxrate${index}`).val(taxrate);
            //     $(`#taxstructure${index}`).val(strcode);
            //     $(`#ledger${index}`).val(accode);
            // });

            $(document).on('change', '.items', async function() {
                let index = $(this).closest('tr').index() + 1;
                let value = $(this).val();
                let unit = $(this).find('option:selected').data('unit');
                let issueunit = $(this).find('option:selected').data('issueunit');
                let purchrate = $(this).find('option:selected').data('purchrate');
                let convratio = $(this).find('option:selected').data('convratio');
                let taxcode = $(this).find('option:selected').data('taxcode');
                let taxrate = $(this).find('option:selected').data('taxrate');
                let strcode = $(this).find('option:selected').data('strcode');
                let accode = $(this).find('option:selected').data('accode');
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
                $(`#taxcode${index}`).val(taxcode);
                $(`#taxrate${index}`).val(taxrate);
                $(`#taxstructure${index}`).val(strcode);
                $(`#ledger${index}`).val(accode);
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
                let wtqty = parseFloat(convratio) * parseFloat(accqty) || 0.00;
                let amount = parseFloat(accqty) * parseFloat(rate) || 0.00;
                $(`#wtqty${index}`).val(wtqty.toFixed(2));
                $(`#amount${index}`).val(amount.toFixed(2));
                $(`#discamt${index}`).val(amount.toFixed(2));
            }

            $(document).on('input', '.rates', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                wtqty($(`#convratio${index}`).val(), $(`#qtyiss${index}`).val(), index, $(`#itemrate${index}`).val());
                calculateamt();
            });

            $(document).on('input', '.amounts', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                let amt = $(this).val() / $(`#qtyiss${index}`).val();
                $(`#itemrate${index}`).val(amt.toFixed(2));
                calculateamt();
            });

            $(document).on('input', '.qtyisss', function() {
                if ($(this).val() < 0) {
                    $(this).val('0.00');
                }
                let index = $(this).closest('tr').index() + 1;
                wtqty($(`#convratio${index}`).val(), $(`#qtyiss${index}`).val(), index, $(`#itemrate${index}`).val());
                calculateamt();
            });

            $(document).on('change', '.taxstructures', function() {
                let index = $(this).closest('tr').index() + 1;
                let rate = $(this).find('option:selected').data('rate');
                let taxcode = $(this).find('option:selected').data('taxcode');
                $(`#taxrate${index}`).val(rate);
                $(`#taxcode${index}`).val(taxcode);
                calculateamt();
            });

            $('#billimage').change(function() {
                var fileInput = $('#billimage')[0].files[0];
                if (fileInput) {
                    $('#previewbtn').show();
                } else {
                    $('#previewbtn').hide();
                }
            });

            $('#previewbtn').click(function() {
                var fileInput = $('#billimage')[0].files[0];
                if (fileInput) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                        $('#imageModal').modal('show');
                    }
                    reader.readAsDataURL(fileInput);
                } else {
                    alert('Please select an image first!');
                }
            });

            const sumofamounts = (selector) => {
                let total = 0;
                $(selector).each(function() {
                    total += parseFloat($(this).val()) || 0.00;
                });
                return total;
            }

            $(document).on('change', '.invtype', function() {
                let invtype = $(this).val();
                $('.billnospan').remove();
                let gstin = $('#gstin').val();
                if (gstin == '') {
                    if ($(this).val() == 'saleinvoice' || $(this).val() == 'taxinvoice') {
                        $(this).prop('checked', false);
                        pushNotify('info', 'Purchase Bill', 'GSTIN Not Found', 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');
                    }
                }

                const pdata = {
                    'invtype': invtype,
                };

                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(pdata),
                };
                fetch('/getpurchvno', options)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            pushNotify('error', 'Purchase Bill', data.error, 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');
                        } else {
                            let span = `<span class="billnospan text-danger font-weight-bold">${data}</span>`;
                            $(this).after(span);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        pushNotify('error', 'Purchase Bill', 'Failed to get Purchase Voucher Number', 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');
                    });
            });

            function handleValue(selector, valueOnClick = '', valueOnBlur = '0.00') {
                $(document).on('click', selector, function() {
                    let value = parseFloat($(this).val()) || 0;
                    if (value <= 0) {
                        $(this).val(valueOnClick);
                    }
                });

                $(document).on('blur', selector, function() {
                    let value = parseFloat($(this).val()) || 0;
                    if (value <= 0) {
                        $(this).val(valueOnBlur);
                    }
                });
            }

            handleValue('.discountfix', '', '0.00');
            handleValue('.additionamount', '', '0.00');
            handleValue('.deductionamount', '', '0.00');
            handleValue('.discountsundry', '', '0.00');

            $(document).on('input', '.discountfix', function() {
                if ($(this).val() < 0 || isNaN($(this).val()) || $(this).val() > 90) {
                    $(this).val('0.00');
                }

                calculateamt();
            });

            let disctime;
            $(document).on('input', '.discountsundry', function() {
                if ($(this).val() < 0 || isNaN($(this).val())) {
                    $(this).val('0.00');
                }
                clearTimeout(disctime);
                disctime = setTimeout(() => {
                    let discountamount = parseFloat($(this).val());
                    let amount = sumofamounts('.discamts') || 0.00;
                    let discountPercentage = (discountamount / amount) * 100;
                    $('#discountfix').val(discountPercentage.toFixed(2));
                    setTimeout(() => {
                        calculateamt();
                    }, 1000);
                }, 2000);
            });

            $(document).on('input', '.additionamount', function() {
                if ($(this).val() < 0 || isNaN($(this).val())) {
                    $(this).val('0.00');
                }
                calculateamt();
            });

            $(document).on('input', '.qtyisss', function() {
                let index = $(this).closest('tr').index() + 1;
                if ($(this).val() < 0 || isNaN($(this).val())) {
                    $(this).val('0.00');
                }
                setTimeout(() => {
                    if (parseFloat($(this).val()) > parseFloat($(`#fixqty${index}`).val())) {
                        $(this).val('0.00');
                    }
                    wtqty($(`#convratio${index}`).val(), $(`#qtyiss${index}`).val(), index, $(`#itemrate${index}`).val());
                    calculateamt();
                }, 100);
            });

            $(document).on('input', '.deductionamount', function() {
                if ($(this).val() < 0 || isNaN($(this).val())) {
                    $(this).val('0.00');
                }
                calculateamt();
            });

            function calcper(amount, percentage) {
                return ((amount * percentage) / 100).toFixed(2);
            }

            function calculateamt() {
                setTimeout(() => {
                    let index = 1;
                    let totalamount = 0;
                    let taxableamt = 0;
                    let totalTaxAmount = 0;

                    $('#taxableamt').val('0.00');
                    let discountinput = parseFloat($('#discountfix').val()) || 0.00;
                    let tbodyLength = $('#itemtable tbody tr').length;

                    // 🔁 Reset all individual item tax amounts and sundry tax inputs
                    $('[id^="taxamt"]').val('0.00');
                    $('input[data-revcode]').each(function() {
                        $(this).val('0.00');
                    });

                    for (let i = 1; i <= tbodyLength; i++) {
                        let itemrate = parseFloat($('#amount' + i).val()) ?? 0.00;
                        if (isNaN(itemrate)) {
                            console.error("Item rate is NaN for input field #" + i);
                            continue;
                        }

                        // Apply discount
                        let newitemrate = itemrate - (itemrate * discountinput / 100);
                        let taxeditemrate = parseFloat(newitemrate.toFixed(2));
                        $(`#discamt${i}`).val(taxeditemrate);

                        totalamount += parseFloat(itemrate);

                        // Tax handling
                        let taxcodes = $('#taxcode' + i).val() ?? '';
                        let taxrates = $('#taxrate' + i).val();
                        let taxcodesArray = taxcodes.split(',');
                        let taxratesArray = taxrates.split(',');

                        let taxMapping = {};
                        for (let j = 0; j < taxcodesArray.length; j++) {
                            let taxCode = taxcodesArray[j]?.trim();
                            let taxRate = parseFloat(taxratesArray[j]?.trim() ?? 0);

                            if (taxCode && !isNaN(taxRate)) {
                                taxMapping[taxCode] = taxRate;
                            }
                        }

                        // Calculate and assign tax per tax code
                        for (let taxCode in taxMapping) {
                            let rate = taxMapping[taxCode];
                            let taxAmount = (taxeditemrate * rate) / 100;
                            totalTaxAmount += taxAmount;

                            $(`#taxamt${i}`).val(taxAmount.toFixed(2));

                            let input = $(`input[data-revcode="${taxCode}"]`);
                            if (input.length) {
                                let existingTax = parseFloat(input.val()) || 0;
                                input.val((existingTax + taxAmount).toFixed(2));
                            }
                        }

                        index++;
                    }

                    // Final calculations
                    let fixtaxableamt = taxableamt.toFixed(2);
                    $('#taxableamt').val(fixtaxableamt);

                    let totalqty = sumofamounts('.qtyisss') || 0.00;
                    let totalrate = sumofamounts('.rates') || 0.00;
                    let totalamounts = sumofamounts('.amounts') || 0.00;

                    let discountper = parseFloat($('#discountfix').val()) || 0.00;
                    let additionamount = parseFloat($('#additionamount').val()) || 0.00;
                    let deductionamount = parseFloat($('#deductionamount').val()) || 0.00;
                    let cgstamount = parseFloat($('#cgstamount').val()) || 0.00;
                    let sgstamount = parseFloat($('#sgstamount').val()) || 0.00;

                    let taxsum = cgstamount + sgstamount;

                    $('#totalamount').val(totalamounts.toFixed(2));

                    let discamount = (totalamounts * discountper) / 100 || 0.00;
                    $('#discountsundry').val(discamount.toFixed(2));

                    let netamount = (totalamounts + additionamount + taxsum) - (discamount + deductionamount) || 0.00;
                    let integervalue = Math.ceil(netamount) || 0.00;
                    let decimalvalue = integervalue - netamount || 0.00;

                    $('#roundoffamount').val(decimalvalue.toFixed(2));
                    $('#netamount').val(integervalue.toFixed(2));

                }, 200);
            }

            $('#purchaseentryform').on('submit', function(e) {
                let netamount = parseFloat($('#netamount').val()) || 0.00;

                if (netamount < 1) {
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Atleast Select 1 Item to Submit!',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    })
                    e.preventDefault();
                    return;
                } else {
                    e.submit();
                }
            });
        });
    </script>
@endsection
