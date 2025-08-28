@extends('property.layouts.main')
@section('main-container')
    @include('cdns.select')
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <style>
        .kotentry input.amount {
            height: auto;
            width: 7em;
            min-width: auto;
            min-height: auto !important;
        }

        input.sevenem {
            height: auto;
            width: 7em;
            min-width: auto;
            min-height: auto !important;
        }

        tfoot.salebilltfoot td {
            padding: 2px;
        }
    </style>

    <div id="salebillpage" class="content-body kotentry">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <img src="{{ asset('admin/images/baloon2.png') }}" class="balloon" id="balloon1">
                    <img src="{{ asset('admin/images/baloon1.png') }}" class="balloon" id="balloon2">
                    <img src="{{ asset('admin/images/blue.png') }}" class="balloon" id="balloon3">
                    <img src="{{ asset('admin/images/rangeen.png') }}" class="balloon" id="balloon3">
                    <img src="{{ asset('admin/images/baloon3.png') }}" class="balloon" id="balloon3">
                    <div class="birthday-message mt-4">
                        <span id="birthdaytext"></span>
                        <span id="dobtext"></span>
                        <div class="sparkles"></div>
                        <span id="clsbtnoc" class="float-lg-right"><i class="fa-regular fa-rectangle-xmark"></i></span>
                    </div>
                    <div class="p-3">
                        <form class="form" action=" {{ route('salebillsubmit') }} " name="salebillform" id="salebillform"
                            method="POST">
                            @csrf
                            <input type="hidden" name="addeddocid" id="addeddocid">
                            <div class="modal fade" id="customerModal">
                                <div class="modal-dialog">
                                    <div style="transform: translate(-58%, 10px);" class="modal-content">

                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h3 class="modal-title">Customer Information</h3>
                                            <button type="button" class="close modalclosebtn"
                                                data-dismiss="modal">&times;</button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body">
                                            <div class="form-group row">
                                                <label for="phoneno" class="col-sm-4 col-form-label">Phone No</label>
                                                <div id="phonediv" class="col-sm-8">
                                                    <input type="text" autocomplete="off" aria-autocomplete="none"
                                                        class="form-control" name="phoneno" id="phoneno"
                                                        placeholder="Enter phone number">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="customername" class="col-sm-4 col-form-label">Customer
                                                    Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" autocomplete="off" aria-autocomplete="none"
                                                        class="form-control" name="customername" id="customername"
                                                        placeholder="Enter customer name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-4 col-form-label">Address</label>
                                                <div class="col-sm-8">
                                                    <input type="text" autocomplete="off" aria-autocomplete="none"
                                                        class="form-control" name="address" id="address"
                                                        placeholder="Enter address">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="customercity" class="col-sm-4 col-form-label">City</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="customercity" id="customercity">
                                                        <option value="">Select</option>
                                                        @foreach ($citydata as $item)
                                                            <option value="{{ $item->city_code }}">{{ $item->cityname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="like" class="col-sm-4 col-form-label">Like</label>
                                                <div class="col-sm-8">
                                                    <input type="text" autocomplete="off" aria-autocomplete="none"
                                                        class="form-control" name="like" id="like" placeholder="Enter like">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="dislike" class="col-sm-4 col-form-label">Dislike</label>
                                                <div class="col-sm-8">
                                                    <input type="text" autocomplete="off" aria-autocomplete="none"
                                                        class="form-control" name="dislike" id="dislike"
                                                        placeholder="Enter dislike">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="birthdate" class="col-sm-4 col-form-label">Birth Date</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" name="birthdate" id="birthdate">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="anniversary" class="col-sm-4 col-form-label">Anniversary</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" name="anniversary"
                                                        id="anniversary">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <button type="button" id="customerdetailsave"
                                                class="btn btn-success">Save</button>
                                            <button type="button" class="btn btn-secondary modalclosebtn"
                                                data-dismiss="modal">Close</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="fixrestcode" id="fixrestcode"
                                value="{{ $depart->dcode }}">
                            <input type="hidden" class="form-control" name="departname" id="departname"
                                value="{{ $depart->name }}">
                            <input type="hidden" name="departnature" id="departnature" value="{{ $depart->nature }}">
                            <input type="hidden" class="form-control" name="oldvnopendingkot" id="oldvnopendingkot"
                                value="">
                            <input type="hidden" class="form-control" name="olddocidpendingkot" id="olddocidpendingkot"
                                value="">
                            <input type="hidden" value="" name="billprinty" id="billprinty">
                            <input type="hidden" class="form-control" name="roundoff" id="roundoff" value="">
                            <input type="hidden" class="form-control" name="waiter" id="waiter" value="">
                            <input type="hidden" class="form-control" name="kotdocid" id="kotdocid" value="">
                            <input type="hidden" name="kotdocidfix" id="kotdocidfix" value="">
                            <input type="hidden" class="form-control" name="stockdocid" id="stockdocid" value="">
                            <input type="hidden" class="form-control" name="vnostock" id="vnostock" value="">
                            <input type="hidden" class="form-control" name="previousroomno" id="previousroomno" value="">
                            <input type="hidden" class="form-control" name="totalitemsum" id="totalitemsum" value="">
                            <input type="hidden" class="form-control" name="guestname" id="guestname" value="">
                            <input type="hidden" class="form-control" name="guestadd" id="guestadd" value="">
                            <input type="hidden" class="form-control" name="guestmobile" id="guestmobile" value="">
                            <input type="hidden" class="form-control" name="guestcity" id="guestcity" value="">
                            <input type="hidden" class="form-control" name="compstatename" id="compstatename" value="">
                            <input type="hidden" class="form-control" name="compstatecode" id="compstatecode" value="">
                            <input type="hidden" class="form-control" name="companygst" id="companygst" value="">
                            <input type="hidden" class="form-control" name="compcityname" id="compcityname" value="">
                            <input type="hidden" name="sale1docid" id="sale1docid">
                            <input type="hidden" name="vnoup" id="vnoup" value="">
                            <input type="hidden" name="kotno" id="kotno" value="">
                            <input type="hidden" value="N" name="oldroomyn" id="oldroomyn">
                            <input type="hidden" name="waitersname" id="waitersname" value="">
                            <input type="hidden" name="vdatesale1" id="vdatesale1" value="">
                            <input type="hidden" class="form-control" name="vtype" id="vtype"
                                value="{{ 'B' . $depart->short_name }}">
                            <input type="hidden" class="form-control" name="restcode" id="restcode"
                                value="{{ $depart->dcode }}">
                            {{-- <input type="hidden" class="form-control" name="sundrycount" id="sundrycount"
                                value="{{ $sundrycount }}"> --}}
                            <input type="hidden" value="{{ $roomnoone }}" name="posroomno" id="posroomno">
                            <input type="hidden" value="{{ $label }}" name="label" id="label">
                            <input type="hidden" value="{{ $printsetup->description }}" name="printdescription"
                                id="printdescription">
                            <input type="hidden" name="totalitems" id="totalitems">
                            <div style="background: aquamarine;" class="row mb-1">
                                <input type="hidden" value="{{ $envpos->kotoutletselection }}" name="kotoutletselection"
                                    id="kotoutletselection">
                                <div class="col-md-12">
                                    <div class="row ptags">
                                        <div class="col-md-2">
                                            <p style="cursor: pointer;" id="outletchangebtn" class="m-1">{{ $depart->name }}
                                            </p>
                                            <ul id="listoutlets" style="display:none;">
                                                @foreach ($outletdata as $item)
                                                    <li class="outletcls" data-value="{{ $item->dcode }}">
                                                        {{ $item->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="salebillmodal" tabindex="-1" role="dialog"
                                            aria-labelledby="salebillmodalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="salebillmodalLabel">Settlement <span
                                                                class="ADA" id="jghj"></span></h5>
                                                        <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                            id="changeprofilemodalLabel">Deposit No.:
                                                            <span class="BANX" id="vnomodal"></span> &nbsp;&nbsp;&nbsp;
                                                            Deposit Date:
                                                            <span class="BANX" id="depdate"></span>
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe id="salebillsettleiframe" src="" frameborder="0"
                                                            style="width: 100%; height: 37em;"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 d-flex">
                                            <p class="m-1" id="ncurdate2"></p>
                                            <p class="m-1" id="curtime"></p>
                                            <p style="text-indent: 8px;" class="m-1 text-dpink" id="krsno"> </p>
                                        </div>

                                        <div class="col-md-2">
                                            {{-- <input type="text" autocomplete="off" aria-autocomplete="list"
                                                placeholder="&#128269; Bill No" name="oldroomno" id="oldroomno"
                                                class="form-control sevenem" disabled>
                                            <div id="invalidbill" class="position-absolute alert-link text-danger"></div>
                                            --}}
                                            {{-- <select class="form-control" name="oldroomno" id="oldroomno">
                                                <option value="">Old Bills</option>
                                                @foreach ($oldroomno as $item)
                                                <option data-vprefix="{{ $item->vprefix }}" value="{{ $item->vno }}">Bill
                                                    No: {{ $item->vno }} {{ $label }}: {{ $item->roomno }} Waiter: {{
                                                    $item->waitername }}
                                                </option>
                                                @endforeach
                                            </select> --}}
                                            <label for="" class="none">Old Bill No.</label>
                                            <select class="form-control select2-multiple" name="oldroomno" id="oldroomno">
                                                <option value="">Old Bill No.</option>
                                                @foreach ($oldroomno as $item)
                                                    <option data-vprefix="{{ $item->vprefix }}" value="{{ $item->vno }}">Bill
                                                        No: {{ $item->vno }} {{ $label }}: {{ $item->roomno }} Waiter:
                                                        {{ $item->waitername }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-1 text-center">
                                            <button disabled class="btn mt-1 btn-sm btn-success" name="submitBtn"
                                                id="submitBtn" type="submit">Submit</button>
                                        </div>

                                        @if ($envpos->reportingonsalebill == 'N' && $curusername != $adminuname->u_name)
                                        @else
                                            <div class="col-md-1 text-center">
                                                <button disabled class="btn mt-1 btn-sm btn-success" name="billprint"
                                                    id="billprint" type="button">Bill Print</button>
                                            </div>
                                        @endif

                                        <div class="col-md-1 text-center">
                                            <button disabled data-toggle="modal" data-target=""
                                                class="btn mt-1 btn-sm btn-success" name="settlement" id="settlement"
                                                type="button">Settlement</button>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button disabled type="button" id="customerbutton" name="customerbutton"
                                                class="btn mt-1 btn-sm btn-primary" data-toggle="modal"
                                                data-target="#customerModal">
                                                Customer
                                            </button>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button disabled class="btn mt-1 btn-sm btn-danger" name="delete" id="delete"
                                                type="button">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="">
                                                <div class="form-group">
                                                    <select name="roomno" id="roomno" class="form-control">
                                                        <option value="">{{ $label }}</option>
                                                        @foreach ($roomno as $item)
                                                            <option value="{{ $item->roomno }}" {{ $roomnoone == $item->roomno ? 'selected' : '' }}>{{ $item->roomno }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span style="text-transform: capitalize;" id="guestdt"
                                                        class="position-absolute text-nowrap"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="">
                                                <div class="form-group">
                                                    <select name="pax" id="pax" class="form-control" required>
                                                        <option value="">Pax</option>
                                                        <option value="1" selected>1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="custom">Custom</option>
                                                    </select>
                                                    <input type="text" class="form-control" id="custompaxinput"
                                                        name="custompaxinput" style="display: none;"
                                                        placeholder="Enter Number">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="compdiv" class="col-md-3">
                                            <div class="">
                                                <div class="form-group">
                                                    <select class="form-control" name="company" id="company">
                                                        <option value="">Company</option>
                                                        @foreach ($company as $item)
                                                            <option value="{{ $item->sub_code }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="position-absolute ADA" id="compgst"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" placeholder="&#128269; Enter Name" name="searchname"
                                                id="searchname" class="form-control mb-2">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" placeholder="&#128269; Enter Bar Code" name="searchbar"
                                                id="searchbar" class="form-control mb-2">
                                        </div>
                                        <div class="col-md-3 px-lg-0">
                                            <div class="tablecontainermenunames">
                                                <table id="menunames" class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="border-top: 1px solid #0000000f;">Group</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td data-value="favourite" class="menugrpitem" id="favourite">
                                                                Favourite
                                                            </td>
                                                        </tr>
                                                        @foreach ($menudata as $item)
                                                            <tr>
                                                                <td data-value="{{ $item->code }}" class="menugrpitem">
                                                                    {{ $item->name }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="divitemnames">
                                                <table id="itemnames" class="table table-hover">
                                                    <thead>
                                                        <tr style="border: 1px solid #0000000f;">
                                                            <th>Item Name</th>
                                                            <th colspan="3">Total Added Items: <span class="text-info"
                                                                    id="addeditems">0</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 px-lg-0">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <span class="text BCN font-weight-bold" id="roomnumbers"></span>
                                            <span class="text font-weight-bold ARK" id="settleddt"></span>
                                            <span class="text font-weight-bold BRK" id="settledroomno"></span>
                                        </div>
                                        <div class="mb-4 d-flex">
                                            {{-- <button disabled style="width: -webkit-fill-available;" type="button"
                                                class="btn ml-1 rhead btn-sm btn-warning" name="Complete Order"
                                                id="Complete Order">Complete order</button> --}}
                                            <button onclick="Simongoback()" style="width: -webkit-fill-available;"
                                                type="button" class="btn none ml-1 rhead btn-sm btn-info" name="goback"
                                                id="goback">Go Back</button>
                                        </div>

                                        <div class="col">
                                            <div class="table-container">
                                                <div class="cancel-animation" id="cancelAnimation"></div>
                                                <table id="itemsdata" class="table table-hover">
                                                    <thead>
                                                        <tr style="border-top: 1px solid #0000000f;">
                                                            <th>Item</th>
                                                            <th>Description</th>
                                                            <th>Kot No.</th>
                                                            <th>Qty</th>
                                                            <th>Rate</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot class="bg-gallery salebilltfoot">
                                                        <tr>
                                                            <td colspan="6">
                                                                <div class="row">
                                                                    <div id="{{ $outletname[0]->dcode }}" class="col-md-6">
                                                                        <p class="h4 text-danger ">
                                                                            {{ $outletname[0]->name }}
                                                                        </p>
                                                                        @foreach ($sundrytype1 as $index => $item)
                                                                            @if ($index === 0)
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="{{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        {{ $item->disp_name }}
                                                                                    </div>
                                                                                    <div id="{{ $item->vtype }}totalamount"></div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->disp_name) == 'discount')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        <input value="0.00" type="text"
                                                                                            class="form-control discountfix"
                                                                                            value="{{ $item->svalue }}"
                                                                                            name="{{ $item->vtype }}discountfix"
                                                                                            id="{{ $item->vtype }}discountfix" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input value="0.00" type="text"
                                                                                            class="form-control discountsundry"
                                                                                            name="{{ $item->vtype }}discountsundry"
                                                                                            id="{{ $item->vtype }}discountsundry" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->disp_name) == 'service charge')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        <input type="text"
                                                                                            class="form-control servicechargefix"
                                                                                            value="{{ $item->svalue }}"
                                                                                            name="{{ $item->vtype }}servicechargefix"
                                                                                            id="{{ $item->vtype }}servicechargefix"
                                                                                            {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control servicechargeamount"
                                                                                            name="{{ $item->vtype }}servicechargeamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}servicechargeamount"
                                                                                            {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->nature) == 'cgst')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control sevenem cgstamount"
                                                                                            name="{{ $item->vtype }}cgstamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}cgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->nature) == 'sgst')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control sevenem sgstamount"
                                                                                            name="{{ $item->vtype }}sgstamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}sgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->nature) == 'sale tax')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control sevenem vatamount"
                                                                                            name="{{ $item->vtype }}vatamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}vatamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->disp_name) == 'round off')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control sevenem roundoffamount"
                                                                                            name="{{ $item->vtype }}roundoffamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}roundoffamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            @if (strtolower($item->disp_name) == 'net amount')
                                                                                <div class="d-flex justify-content-between mb-2">
                                                                                    <div
                                                                                        class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                        <span
                                                                                            class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="text"
                                                                                            class="form-control sevenem netamount"
                                                                                            name="{{ $item->vtype }}netamount"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}netamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        <input type="hidden"
                                                                                            class="form-control sevenem totalamount"
                                                                                            name="{{ $item->vtype }}totalamountoutlet"
                                                                                            value="0.00"
                                                                                            id="{{ $item->vtype }}totalamountoutlet">
                                                                                        <input type="hidden"
                                                                                            value="{{ count($sundrytype1) }}"
                                                                                            name="{{ $item->vtype }}sundrycount"
                                                                                            id="{{ $item->vtype }}sundrycount">
                                                                                        <input type="hidden"
                                                                                            name="{{ $item->vtype }}totaltaxable"
                                                                                            id="{{ $item->vtype }}totaltaxable"
                                                                                            value="0.00">
                                                                                        <input type="hidden"
                                                                                            name="{{ $item->vtype }}totalnontaxable"
                                                                                            id="{{ $item->vtype }}totalnontaxable"
                                                                                            value="0.00">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>

                                                                    @if (count($sundrytype2) > 1)
                                                                        <div id="{{ $outletname[1]->dcode }}" class="col-md-6">
                                                                            <p class="h4 text-danger ">
                                                                                {{ count($outletname) > 1 ? $outletname[1]->name : '' }}
                                                                            </p>
                                                                            @foreach ($sundrytype2 as $index => $item)
                                                                                @if ($index === 0)
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="{{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            {{ $item->disp_name }}
                                                                                        </div>
                                                                                        <div id="{{ $item->vtype }}totalamount"></div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->disp_name) == 'discount')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                            <input value="0.00" type="text"
                                                                                                class="form-control discountfix"
                                                                                                value="{{ $item->svalue }}"
                                                                                                name="{{ $item->vtype }}discountfix"
                                                                                                id="{{ $item->vtype }}discountfix" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input value="0.00" type="text"
                                                                                                class="form-control discountsundry"
                                                                                                name="{{ $item->vtype }}discountsundry"
                                                                                                id="{{ $item->vtype }}discountsundry" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->disp_name) == 'service charge')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                            <input type="text"
                                                                                                class="form-control servicechargefix"
                                                                                                value="{{ $item->svalue }}"
                                                                                                name="{{ $item->vtype }}servicechargefix"
                                                                                                id="{{ $item->vtype }}servicechargefix"
                                                                                                {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control servicechargeamount"
                                                                                                name="{{ $item->vtype }}servicechargeamount"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}servicechargeamount"
                                                                                                {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->nature) == 'cgst')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control sevenem cgstamount"
                                                                                                name="{{ $item->vtype }}cgstamount"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}cgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->nature) == 'sgst')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control sevenem sgstamount"
                                                                                                name="{{ $item->vtype }}sgstamount"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}sgstamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->nature) == 'sale tax')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control sevenem vatamount"
                                                                                                name="{{ $item->vtype }}vatamount"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}vatamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->disp_name) == 'round off')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control sevenem roundoffamount"
                                                                                                name="{{ $item->vtype }}roundoffamount"
                                                                                                value="0.00" id="{{ $item->vtype }}roundoffamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                @if (strtolower($item->disp_name) == 'net amount')
                                                                                    <div class="d-flex justify-content-between mb-2">
                                                                                        <div
                                                                                            class="d-flex {{ $item->bold == 'Y' ? 'font-weight-bold' : '' }}">
                                                                                            <span
                                                                                                class="mt-2 mr-1">{{ $item->disp_name }}</span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <input type="text"
                                                                                                class="form-control sevenem netamount"
                                                                                                name="{{ $item->vtype }}netamount"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}netamount" {{ $item->automanual == 'A' ? 'readonly' : '' }}>
                                                                                            <input type="hidden"
                                                                                                class="form-control sevenem totalamount"
                                                                                                name="{{ $item->vtype }}totalamountoutlet"
                                                                                                value="0.00"
                                                                                                id="{{ $item->vtype }}totalamountoutlet">
                                                                                            <input type="hidden"
                                                                                                value="{{ count($sundrytype2) }}"
                                                                                                name="{{ $item->vtype }}sundrycount"
                                                                                                id="{{ $item->vtype }}sundrycount">
                                                                                            <input type="hidden"
                                                                                                name="{{ $item->vtype }}totaltaxable"
                                                                                                id="{{ $item->vtype }}totaltaxable"
                                                                                                value="0.00">
                                                                                            <input type="hidden"
                                                                                                name="{{ $item->vtype }}totalnontaxable"
                                                                                                id="{{ $item->vtype }}totalnontaxable"
                                                                                                value="0.00">
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            @if (count($sundrytype2) > 1)
                                                                <td colspan="5">Total Amount: </td>
                                                                <td id="totalamttext" class="text-right h5"></td>
                                                            @endif
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="guesttable" style="display: none;">
                                            <h5 class="d-flex justify-content-center align-items-center">
                                                <span class="flex-grow-1 text-center">Guest History</span>
                                                <span id="closeguestdiv" class="ml-auto text-danger">
                                                    <i class="fa-regular fa-rectangle-xmark"></i>
                                                </span>
                                            </h5>

                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Visit DateTime</th>
                                                        <th>Item Name</th>
                                                        <th>Qty</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            <div id="resizeHandle" class="resizeHandle"></div>
                                        </div>
                                        <div id="guestdetailsoverlay">
                                            <span id="guestDetails"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function Simongoback() {
            window.location.href = `displaytable?dcode=${$('#fixrestcode').val()}`;
        }

        $(document).ready(function () {
            if ($('#departnature').val() == 'Room Service') {
                $('#customerbutton').css('display', 'none');

            }
            $(document).on('click', '#submitBtn', function () {
                setTimeout(() => {
                    $(this).prop('disabled', true);
                    $('#billprint').prop('disabled', true);
                }, 10);
            });

            let posroomno = $('#posroomno').val();
            if (posroomno != '') {
                $('#goback').removeClass('none');
            } else {
                $('#goback').addClass('none');
            }

            setTimeout(() => {
                if (posroomno != '') {
                    $('#roomno').trigger('change');
                }
            }, 1000);

            $('#salebillmodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("salebillsettleiframe");
                let vno = $('#oldroomno').val();
                let sale1docid = $('#sale1docid').val();
                let vdatesale1 = $('#vdatesale1').val();
                $('#vnomodal').text(vno);
                $('#depdate').text(dmy(vdatesale1));
                iframe.src = "{{ url('/salebillsettle') }}" + "?vno=" + vno + "&sale1docid=" + sale1docid;
            });

            $('#pax').on('change', function () {
                var selectedOption = $(this).val();
                var inputField = $('#custompaxinput');
                if (selectedOption === "custom") {
                    inputField.show().focus();
                } else {
                    inputField.hide();
                }
            });

            $('#custompaxinput').on('keypress blur', function (event) {
                if (event.which === 13 || event.type === 'blur') {
                    var inputVal = $(this).val();
                    var selectBox = $('#pax');

                    var existingOption = selectBox.find('option[value="' + inputVal + '"]');
                    if (existingOption.length > 0) {
                        existingOption.remove();
                    }
                    selectBox.append('<option value="' + inputVal + '" selected>' + inputVal + '</option>');
                    var customOption = selectBox.find('option[value="custom"]');
                    if (customOption.is(':selected')) {
                        customOption.remove();
                        selectBox.append('<option value="custom" selected>custom</option>');
                    }
                    $(this).hide();
                }
            });

            $('#roomno').on('change', function () {
                let roomno = $(this).val();
                let guestdtxhr = new XMLHttpRequest();
                guestdtxhr.open('POST', '/guestdtfetchkot', true);
                guestdtxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                guestdtxhr.onreadystatechange = function () {
                    if (guestdtxhr.readyState === 4 && guestdtxhr.status === 200) {
                        let results = JSON.parse(guestdtxhr.responseText);
                        $('#guestdt').text(results.concat);
                        $('#pax').val(results.pax);
                        let guestdetails = results.guestdetails;
                        if (guestdetails != '' && guestdetails != null) {
                            $('#guestname').val(guestdetails.name);
                            $('#company').val(guestdetails.company);
                            $('#compgst').text(guestdetails.gstin);
                            $('#guestadd').val(`${guestdetails.add1} ${guestdetails.add2}`);
                            $('#guestmobile').val(guestdetails.guestmobile);
                            $('#guestcity').val(guestdetails.guestcityname);
                            $('#guestcompany').val(guestdetails.companyname);
                            $('#compstatename').val(guestdetails.compstatename);
                            $('#compstatecode').val(guestdetails.compstatecode);
                            $('#companygst').val(guestdetails.gstin);
                            $('#compcityname').val(guestdetails.compcityname);
                        }
                        $('#compdiv').removeClass('none');
                    } else {
                        $('#compgst').text('');
                        $('#compdiv').addClass('none');
                        $('#company').val('');
                        $('#pax').val('');
                    }
                }
                guestdtxhr.send(`roomno=${roomno}&_token={{ csrf_token() }}`);
            });

            $("#outletchangebtn").click(function () {
                let kotoutletselection = $('#kotoutletselection').val();
                if (kotoutletselection == 'Y') {
                    $("#listoutlets").toggle();
                }
            });

            $('.outletcls').click(function () {
                $("#listoutlets").toggle();
                let dcode = $(this).data('value');
                $('#restcode').val(dcode);

                // Creating XMLHttpRequest for department name fetch
                let departnamexhr = new XMLHttpRequest();
                departnamexhr.open('POST', '/departnamefetch', true);
                departnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                departnamexhr.onreadystatechange = function () {
                    if (departnamexhr.readyState === 4 && departnamexhr.status === 200) {
                        let results = JSON.parse(departnamexhr.responseText);
                        let buttonid = $('#outletchangebtn');
                        buttonid.text(results.name);
                        let shortname = 'B' + results.short_name;
                        $('#vtype').val(shortname);
                        krsno(shortname);
                    }
                }
                departnamexhr.send(`dcode=${dcode}&_token={{ csrf_token() }}`);

                // Clearing previous data
                $('#menunames tbody').find('tr:not(:first)').remove();
                $('#itemnames tbody').empty();

                // Creating XMLHttpRequest for menu names fetch
                let menunamexhr = new XMLHttpRequest();
                menunamexhr.open('POST', '/fetchmenunames', true);
                menunamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                menunamexhr.onreadystatechange = function () {
                    if (menunamexhr.readyState === 4 && menunamexhr.status === 200) {
                        let results = JSON.parse(menunamexhr.responseText);
                        $('#favourite').trigger('click');
                        let menunametbody = $('#menunames tbody');
                        results.forEach(function (item, index) {
                            let row = $('<tr>');
                            row.append(`<td class="menugrpitem" data-value="${item.code}">${item.name}</td>`);
                            menunametbody.append(row);
                            $('.menugrpitem').click(function () {
                                let grpid = $(this).data('value');
                                let dcode = $('#restcode').val();
                                $('#searchname').val('');
                                $('#searchbar').val('');
                                fetchItemNames(`grpid=${grpid}&dcode=${dcode}&_token={{ csrf_token() }}`);
                            });
                        });
                    }
                }
                menunamexhr.send(`dcode=${dcode}&_token={{ csrf_token() }}`);
            });

            function scrollToBottom() {
                var container = $('.table-container');
                container.animate({
                    scrollTop: container.prop("scrollHeight")
                }, 'slow');
            }

            $('#vtype').on('input', function () {
                let value = $(this).val();
                krsno(value);
            });

            function krsno(vtype) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "{{ route('getmaxvtype') }}");
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        $("#krsno").text(data);
                    }
                };
                xhr.send(`vtype=${vtype}&_token={{ csrf_token() }}`);
            }

            $('#menunames td').click(function () {
                if ($(this).hasClass('bgmenutd')) {
                    $(this).removeClass('bgmenutd').find('.fas.fa-arrow-right').remove();
                } else {
                    $('#menunames td').removeClass('bgmenutd').find('.fas.fa-arrow-right').remove();
                    $(this).addClass('bgmenutd').append('<i class="fas fa-arrow-right ml-2"></i>');
                }
            });
            let addedItemCodes = [];

            function fetchItemNames(data) {
                let itemnamexhr = new XMLHttpRequest();
                itemnamexhr.open('POST', '/fetchitemnames', true);
                itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                itemnamexhr.onreadystatechange = function () {
                    if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {
                        let results = JSON.parse(itemnamexhr.responseText);
                        let tbody = $('#itemnames tbody');
                        tbody.empty();
                        let row;
                        results.forEach(function (item, index) {
                            if (index % 4 === 0) {
                                row = $('<tr>');
                            }
                            let itemname = item.Name;
                            let itemcde = item.Code;
                            let itemimage = item.iempic;
                            let itemdir = '';
                            if (itemimage !== '') {
                                itemdir = `<img src="storage/property/itempicture/${item.iempic}" alt="${itemname}" style="width: 100%; height: 100%;" onerror="this.style.display='none';">`;
                            }
                            let bordercolor = (item.dishtype == 1) ? 'green' : (item.dishtype == 2) ? 'red' : (item.dishtype == 3) ? 'yellow' : 'green';
                            row.append(`<td style="position: relative; border-left: 3px solid ${bordercolor};" data-id="${item.rateofitem}" data-itemrestcode="${item.RestCode}" class="tditemname" data-value="${itemcde}">
                                                            ${itemdir}
                                                            <span class="itemnamespan">${itemname}</span>
                                                        </td>`);

                            if ((index + 1) % 4 === 0 || index === results.length - 1) {
                                if ((index + 1) % 4 !== 0) {
                                    let emptyTdCount = 4 - ((index + 1) % 4);
                                    for (let i = 0; i < emptyTdCount; i++) {
                                        row.append('<td></td>');
                                    }
                                }
                                tbody.append(row);
                                $('#roomno').prop('disabled', false);
                                $('#oldroomno').prop('disabled', false);
                            }
                        });
                    }
                }
                itemnamexhr.send(data);
            }

            $('.menugrpitem').click(function () {
                let grpid = $(this).data('value');
                let dcode = $('#restcode').val();
                $('#searchname').val('');
                $('#searchbar').val('');
                fetchItemNames(`grpid=${grpid}&dcode=${dcode}&_token={{ csrf_token() }}`);
            });

            $('#searchname').on('input', function () {
                let nameinput = $(this).val();
                let dcode = $('#restcode').val();
                $('#searchbar').val('');
                fetchItemNames(`name=${nameinput}&dcode=${dcode}&_token={{ csrf_token() }}`);
            });

            $('#searchbar').on('input', function () {
                let barcodeinput = $(this).val();
                let dcode = $('#restcode').val();
                $('#searchname').val('');
                fetchItemNames(`barcodeinput=${barcodeinput}&dcode=${dcode}&_token={{ csrf_token() }}`);
            });

            // Fetch Item details by clicking itemname list grid
            let temptotaladditems = $('#addeditems').text();
            let totaladditems = (temptotaladditems == 0) ? 0 : temptotaladditems;

            $('tbody').on('click', '.tditemname', function () {
                let itemcode = $(this).data('value');
                let itemrestcode = $(this).data('itemrestcode');
                let existingItem = $('#itemsdata tbody tr').filter(function () {
                    return $(this).find('.tditemname').data('value') === itemcode;
                });
                scrollToBottom();
                if (existingItem.length) {
                    let quantityInput = existingItem.find('.el');
                    let quantity = parseInt(quantityInput.val());
                    quantityInput.val(quantity + 1);
                    updateTotal();
                } else {
                    let itemsdata = $('#itemsdata tbody');
                    itemnamexhr = new XMLHttpRequest();
                    itemnamexhr.open('POST', '/fetchitemdetails', true);
                    itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    itemnamexhr.onreadystatechange = function () {
                        if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {
                            let results = JSON.parse(itemnamexhr.responseText);
                            let tbodyLength = $('#itemsdata tbody tr').length;
                            let index = tbodyLength > 0 ? tbodyLength + 1 : 1;
                            totaladditems++;
                            $('#addeditems').text(totaladditems);
                            $('#totalitems').val(totaladditems);
                            $('#addeditems').css('font-size', 'large');
                            setTimeout(() => {
                                $('#addeditems').css('font-size', 'small');
                            }, 1000);
                            let printnum = totaladditems.toString();

                            let fixedrate = parseFloat(results.Rate);
                            if (results.RateIncTax === 'Y') {
                                const taxrate = parseFloat(results.tax_rate);
                                fixedrate = fixedrate / (1 + taxrate / 100);
                            }

                            pushNotify('success', 'Sale Bill Entry', printnum + ' Item Added', 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');

                            let data = `<tr>
                                                                <td style="white-space: nowrap;">
                                                                        <span><button type="button" class="removeItem"><i class="fa-regular fa-circle-xmark"></i></button></span>
                                                                        <input name="itemcode${index}" id="itemcode${index}" value="${results.Code}" type="hidden">
                                                                        <input name="discapp${index}" id="discapp${index}" value="${results.DiscApp}" type="hidden">
                                                                        <input class="itemnumber" name="itemnumber${index}" id="itemnumber${index}" value="${index}" type="hidden">
                                                                        <input name="itemname${index}" class="itemnameclass" id="itemname${index}" value="${results.Name}" type="hidden">
                                                                        <input name="itemrestcode${index}" id="itemrestcode${index}" value="${results.RestCode}" type="hidden">
                                                                        ${results.Name}</td>
                                                                        <td><input readonly name="description${index}" placeholder="Enter" id="description${index}" class="form-control description inone" type="text"></td>
                                                                <td class="text-center"></td>
                                                                <td>
                                                                        <div class="panelinc">
                                                                        <button type="button" class="decrement btn">-</button>
                                                                        <input name="quantity${index}" id="quantity${index}" class="form-control qtyitem" type="text" value="1">
                                                                        <button type="button" class="increment btn">+</button>
                                                                        </div>
                                                                </td>
                                                                <td><input class="rateclass form-control sevenem" ${results.RateEdit == 'N' ? 'readonly' : ''} oninput="checkNumMax(this, 7); handleDecimalInput(event);" name="rate${index}" id="rate${index}" value="${results.Rate}" type="text">
                                                                    <input type="hidden" value="${results.Rate}" name="taxedrate${index}" id="taxedrate${index}" readonly>
                                                                </td>
                                                                <td><input type="text" name="amount${index}" id="amount${index}" value="${results.Rate}" class="form-control amount" readonly>
                                                                    <input type="hidden" name="fixamount${index}" id="fixamount${index}" value="${fixedrate.toFixed(2)}" class="form-control fixamount" readonly></td>
                                                                <td class="none"><input type="text" name="taxrate_sum${index}" id="taxrate_sum${index}" value="${results.tax_rate}" class="form-control taxrate_sum" readonly>
                                                                    <input name="tax_rate${index}" id="tax_rate${index}" value="${results.tax_rate}" type="hidden">
                                                                </td>
                                                                <td class="none"><input type="text" name="tax_code${index}" id="tax_code${index}" value="${results.tax_code}" class="form-control tax_code" readonly></td>
                                                            </tr>`;
                            itemsdata.append(data);
                            addedItemCodes.push(itemcode);
                            setTimeout(() => {
                                calculatetaxes();
                                // calculateDiscount();
                            }, 500);
                        }
                    }
                    itemnamexhr.send(`itemcode=${itemcode}&itemrestcode=${itemrestcode}&_token={{ csrf_token() }}`);
                }
            });

            // Description input
            $(document).on('click', '.description', function () {
                var inputElement = $(this);
                let currow = inputElement.closest('tr');
                let itemnameelement = currow.find('.itemnameclass');
                let itemname = itemnameelement.val();
                let newitemname = itemname.replace(/%20/g, ' ');
                let title = `Enter Description For ${newitemname}`;
                var currentValue = inputElement.val();
                // console.log(currentValue);

                Swal.fire({
                    title: title,
                    input: 'text',
                    inputValue: currentValue,
                    inputPlaceholder: 'Enter your value here',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'OK',
                    denyButtonText: 'Clear',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        return null;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var newValue = result.value;
                        inputElement.val(newValue);
                        inputElement.prop('readonly', true);
                    } else if (result.isDenied) {
                        inputElement.val('');
                        inputElement.prop('readonly', true);
                    }
                });
            });

            function calcper(amount, percentage) {
                return ((amount * percentage) / 100).toFixed(2);
            }

            function calcitemper(amount, disc) {
                return ((amount - (amount * disc) / 100).toFixed(2));
            }

            // Load Items on Select Room
            let firstSelection = true;
            let previousRoomNo = null;
            let selectedRooms = [];
            let totalamount;
            $('#roomno').on('change', function () {
                $('#oldroomno').prop('disabled', true);
                let label = $('#label').val();
                let currentRoomNo = $(this).val();
                if (currentRoomNo === previousRoomNo) {
                    return;
                }
                if (selectedRooms.includes(currentRoomNo)) {
                    alert(`You have already selected this ${label}.`);
                    $(this).val(previousRoomNo);
                    return;
                }
                if (firstSelection) {
                    firstSelection = false;
                } else {
                    var confirmation = confirm(`Do you want to select ${label}: ${currentRoomNo} ?`);
                    if (!confirmation) {
                        $(this).val(previousRoomNo);
                        return;
                    }
                }

                selectedRooms.push(currentRoomNo);
                previousRoomNo = currentRoomNo;
                let dcode = $('#restcode').val();
                $('#roomnumbers').text(label + '. ' + selectedRooms.join(', '));
                $('#orderno').text('Modify Order');
                scrollToBottom();
                let itemnamexhr = new XMLHttpRequest();
                itemnamexhr.open('POST', '/fetchitemroomchange', true);
                itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                itemnamexhr.onreadystatechange = function () {
                    if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {

                        let results = JSON.parse(itemnamexhr.responseText);
                        let items = results.items;
                        let sundrytype = results.sundrytype;
                        let tbodyempty = $('#itemsdata tbody').empty();
                        let tbodyData = '';
                        let currentrowcount = $('#itemsdata tbody tr').length;
                        let ajaxRequestsCompleted = 0;
                        $('#vnoup').val(results.items.vno ?? '');
                        $('#kotno').val(results.items.vno ?? '');
                        $('#vdatesale1').val(results.items.vdate ?? '');
                        $('#waitersname').val(results.waitername ?? '');
                        let totalitems = results.items.length;
                        totaladditems = totalitems;
                        $('#addeditems').text(totalitems);
                        $('#totalitems').val(totalitems);
                        $('#addeditems').css('font-size', 'large');
                        let printnum = totalitems.toString();
                        pushNotify('success', 'Sale Bill Entry', printnum + ' Item Added', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                        setTimeout(() => {
                            $('#addeditems').css('font-size', 'small');
                        }, 1000);
                        let uniquedocid = new Set();
                        let outletTaxSums = {
                            [results.outlet1code]: 0,
                            [results.outlet2code]: 0
                        };
                        items.forEach((item, index) => {

                            const restcode = item.restcode ?? '';
                            const taxamt = parseFloat(item.taxamt) || 0;

                            if (restcode === results.outlet1code) {
                                outletTaxSums[results.outlet1code] += taxamt;
                            } else if (restcode === results.outlet2code) {
                                outletTaxSums[results.outlet2code] += taxamt;
                            }

                            $('#waiter').val(item.waiter);
                            $('#kotdocidfix').val(item.docid);
                            uniquedocid.add(item.docid);
                            let rowIndex = currentrowcount + index + 1;
                            let taxcode = item.TaxStru;
                            let discapp = item.DiscApp;
                            let tax_rate = parseFloat(item.tax_rate);
                            let taxincyn = item.RateIncTax;
                            let itemrate = parseFloat(item.rate);
                            let qty = parseFloat(item.qty);
                            let taxedrate = item.taxedrate;
                            // console.log(taxedrate);

                            tbodyData += `<tr>
                                                        <td style="white-space: nowrap;">
                                                            <input name="itemcode${rowIndex}" id="itemcode${rowIndex}" value="${item.item}" type="hidden">
                                                            <input name="itemname${rowIndex}" class="itemnameclass" id="itemname${rowIndex}" value="${item.Name}" type="hidden">
                                                            <input name="discapp${rowIndex}" id="discapp${rowIndex}" value="${item.DiscApp}" type="hidden">
                                                            <input name="SChrgApp${rowIndex}" id="SChrgApp${rowIndex}" value="${item.SChrgApp}" type="hidden">
                                                            <input name="kotsno${rowIndex}" id="kotsno${rowIndex}" value="${item.sno}" type="hidden">
                                                            <input name="kotsdocid${rowIndex}" id="kotsdocid${rowIndex}" value="${item.docid}" type="hidden">
                                                            <input name="outletfirst${rowIndex}" id="outletfirst${rowIndex}" value="${results.outlet1code}" type="hidden">
                                                            <input name="outletsecond${rowIndex}" id="outletsecond${rowIndex}" value="${results.outlet2code}" type="hidden">
                                                            <input name="mergedwith${rowIndex}" id="mergedwith${rowIndex}" value="${item.mergedwith}" type="hidden">
                                                            <input name="itemrestcode${rowIndex}" id="itemrestcode${rowIndex}" value="${item.restcode}" type="hidden">
                                                            <input class="itemnumber" name="itemnumber${rowIndex}" id="itemnumber${rowIndex}" value="${rowIndex}" type="hidden">
                                                            ${item.Name}
                                                        </td>
                                                        <td><input readonly name="description${rowIndex}" value="${item.description}" placeholder="Enter" id="description${rowIndex}" class="form-control description inone" type="text"></td>
                                                        <td class="text-center">${item.vno}</td>
                                                        <td>
                                                            <div class="panelinc">
                                                                <input name="quantity${rowIndex}" id="quantity${rowIndex}" class="form-control qtyitem" type="text" value="${item.qty}" readonly>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input oninput="checkNumMax(this, 7); handleDecimalInput(event);" ${item.RateEdit === 'N' ? 'readonly' : ''} class="rateclass form-control sevenem" name="rate${rowIndex}" id="rate${rowIndex}" value="${item.rate}" type="text">
                                                            <input type="hidden" value="${taxedrate != 0 ? taxedrate : item.rate}" name="taxedrate${rowIndex}" id="taxedrate${rowIndex}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="amount${rowIndex}" id="amount${rowIndex}" value="${item.amount}" class="form-control amount" readonly>
                                                            <input type="hidden" name="discedamount${rowIndex}" id="discedamount${rowIndex}" value="${item.amount}" class="form-control discedamount" readonly>
                                                            <input type="hidden" name="fixamount${rowIndex}" id="fixamount${rowIndex}" value="${item.fixamount != 0 ? item.fixamount.toFixed(2) : item.amount}" class="form-control fixamount" readonly>
                                                            <input type="hidden" value="${item.RateIncTax}" class="RateIncTax" id="RateIncTax${rowIndex}" name="RateIncTax${rowIndex}" readonly>
                                                        </td>
                                                        <td class="none"><input type="text" name="taxrate_sum${rowIndex}" id="taxrate_sum${rowIndex}" value="${item.taxrate_sum}" class="form-control taxrate_sum" readonly>
                                                        <input name="tax_rate${rowIndex}" id="tax_rate${rowIndex}" value="${item.tax_rate}" type="hidden"></td>
                                                        <td class="none"><input type="text" name="tax_code${rowIndex}" id="tax_code${rowIndex}" value="${item.tax_code}" class="form-control tax_code" readonly></td>
                                                        </tr>`;

                            ajaxRequestsCompleted++;
                            if (ajaxRequestsCompleted === items.length) {
                                $('#itemsdata tbody').append(tbodyData);
                            }
                        });
                        $('#kotdocid').val([...uniquedocid].toString());
                    }
                }
                itemnamexhr.send(`roomno=${currentRoomNo}&dcode=${dcode}&_token={{ csrf_token() }}`);
                setTimeout(() => {
                    calculatetaxes();
                    // calculateDiscount();
                    // calculateDiscountPercentage();
                    $('#submitBtn').prop('disabled', false);
                    $('#customerbutton').prop('disabled', false);
                    $('#billprint').prop('disabled', false);
                }, 1000);
            });
            // Old Room No Fetch
            let inputTimer;
            $('#oldroomno').on('input', function () {
                let itemsdata = $('#itemsdata tbody');
                itemsdata.empty();
                clearTimeout(inputTimer);
                inputTimer = setTimeout(() => {
                    let dcode = $('#restcode').val();
                    let billno = $(this).val();
                    if (billno == '') {
                        $('#invalidbill').text('');
                    }
                    let vprefix = $(this).find('option:selected').data('vprefix');
                    scrollToBottom();
                    $('#orderno').text('');
                    let itemnamexhr = new XMLHttpRequest();
                    itemnamexhr.open('POST', '/fetchitemoldroomno', true);
                    itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    itemnamexhr.onreadystatechange = function () {
                        if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {
                            let results = JSON.parse(itemnamexhr.responseText);
                            if (results === 'false') {
                                $('#submitBtn').text('Submit');
                                $('#salebillform').prop('action', '{{ route('salebillsubmit') }}');
                                $('#invalidbill').text(`Invalid Bill No. ${billno}`);

                                pushNotify('error', 'Sale Bill Entry', `Invalid Bill No. ${billno}`, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                                $('#roomnumbers').text('');
                                $('#itemsdata tbody').empty();
                                $('#itemsdata tfoot').empty();
                                $('#compgst').text('');
                                $('#compdiv').addClass('none');
                                $('#company').val('');
                                $('#roomno').val('');
                                $('#pax').val('');
                                $('#oldroomyn').val('N');
                            } else {
                                let paychargerows = results.paychargerows;
                                if (paychargerows == 0) {
                                    $('#delete').prop('disabled', false);
                                } else {
                                    $('#delete').prop('disabled', true);
                                }
                                $('#oldroomyn').val('Y');
                                let totalitems = results.items.length;
                                let chkoutrowcount = results.chkoutrowcount;
                                if (chkoutrowcount > 0) {
                                    let paychargerowsd = results.paychargerowsd;
                                    let toutrow = results.toutrow;
                                    let paynames = [];
                                    let payamounts = [];
                                    if (Array.isArray(paychargerowsd)) {
                                        paynames = paychargerowsd.map(row => row.paytype);
                                        payamounts = paychargerowsd.map(row => row.amtcr);
                                    }

                                    let paydetails = paynames.map((name, index) => `${name}: ${payamounts[index]}`).join(', ');
                                    $('#settleddt').text(`${paydetails},`);
                                    if (toutrow) {
                                        $('#settledroomno').text(`Room No: ${toutrow.roomno}`);
                                    }

                                    $('#settlement').prop('disabled', true);
                                    $('#settlement').removeAttr('data-target');
                                    $('#submitBtn').prop('disabled', true);
                                    $('#billprint').prop('disabled', false);
                                } else {
                                    $('#settlement').prop('disabled', false);
                                    $('#settlement').attr('data-target', '#salebillmodal');
                                    $('#submitBtn').prop('disabled', false);
                                    $('#billprint').prop('disabled', false);
                                }
                                $('#customerbutton').prop('disabled', false);
                                totaladditems = totalitems;
                                let concat = results.concat;

                                if (concat != '') {
                                    $('#guestdt').text(concat);
                                }

                                if (results.chkguestprof) {
                                    $('#addeddocid').val(results.chkguestprof.docid);
                                }

                                if (results.guestdt != null && results.guestdt != '') {
                                    $('#addeddocid').val(results.guestdt.docid);
                                }

                                $('#addeditems').text(totalitems);
                                $('#totalitems').val(totalitems);
                                $('#addeditems').css('font-size', 'large');
                                let printnum = totalitems.toString();
                                pushNotify('success', 'Sale Bill Entry', printnum + ' Item Added', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                                setTimeout(() => {
                                    $('#addeditems').css('font-size', 'small');
                                }, 1000);
                                if (results.dep == 'Outlet') {
                                    let guestprof = results.chkguestprof;
                                    if (guestprof != null) {
                                        $('#phoneno').val(guestprof.mobile_no);
                                        $('#customername').val(guestprof.name ?? '');
                                        $('#address').val(guestprof.add1 ?? '');
                                        $('#customercity').val(guestprof.city ?? '');
                                        $('#like').val(guestprof.likes ?? '');
                                        $('#dislike').val(guestprof.dislikes ?? '');
                                        $('#birthdate').val(guestprof.dob ?? '');
                                        $('#anniversary').val(guestprof.anniversary ?? '');
                                    } else {
                                        $('#customername').val('');
                                        $('#address').val('');
                                        $('#customercity').val('');
                                        $('#like').val('');
                                        $('#dislike').val('');
                                        $('#birthdate').val('');
                                        $('#anniversary').val('');
                                    }
                                } else {
                                    $('#customername').val('');
                                    $('#address').val('');
                                    $('#customercity').val('');
                                    $('#like').val('');
                                    $('#dislike').val('');
                                    $('#birthdate').val('');
                                    $('#anniversary').val('');
                                }

                                $('#vnoup').val(results.sale1.vno ?? '');
                                $('#kotno').val(results.sale1.kotno ?? '');
                                $('#vdatesale1').val(results.sale1.vdate ?? '');
                                $('#ncurdate2').text(dmy(results.sale1.vdate));
                                $('#curtime').text(results.sale1.vtime);
                                $('#waitersname').val(results.waitername ?? '');
                                $('#sale1docid').val(results.sale1.docid);
                                $('#company').val(results.sale1.party);
                                let optroomno = `<option value="${results.roomno}" selected>${results.roomno}</option>`;
                                let guestdetails = results.guestdt;

                                if (results.sale1.delflag == 'Y') {
                                    $('#submitBtn').prop('disabled', true);
                                    $('#settlement').prop('disabled', true);
                                    $('#delete').prop('disabled', true);
                                    let animationRunning = false;
                                    const animationButton = document.getElementById('animationButton');
                                    const cancelAnimation = document.getElementById('cancelAnimation');

                                    function createCancelText() {
                                        const cancelText = document.createElement('div');
                                        cancelText.className = 'cancel-text';
                                        cancelText.textContent = 'Cancelled';
                                        return cancelText;
                                    }

                                    function animateCancelText() {
                                        const containerWidth = cancelAnimation.offsetWidth;
                                        const containerHeight = cancelAnimation.offsetHeight;

                                        cancelAnimation.innerHTML = '';
                                        for (let i = 0; i < 10; i++) {
                                            const text = createCancelText();
                                            text.style.left = `${Math.random() * containerWidth}px`;
                                            text.style.top = `${Math.random() * containerHeight}px`;
                                            text.dataset.speedX = Math.random() * 0.2 - 0.1;
                                            text.dataset.speedY = Math.random() * 0.2 - 0.1;
                                            cancelAnimation.appendChild(text);
                                        }

                                        function animate() {
                                            if (!animationRunning) return;

                                            const texts = cancelAnimation.getElementsByClassName('cancel-text');
                                            for (let text of texts) {
                                                let left = parseFloat(text.style.left);
                                                let top = parseFloat(text.style.top);
                                                let speedX = parseFloat(text.dataset.speedX);
                                                let speedY = parseFloat(text.dataset.speedY);

                                                left += speedX;
                                                top += speedY;

                                                // Bounce off the edges
                                                if (left < 0 || left > containerWidth - text.offsetWidth) {
                                                    speedX *= -1;
                                                    text.dataset.speedX = speedX;
                                                }
                                                if (top < 0 || top > containerHeight - text.offsetHeight) {
                                                    speedY *= -1;
                                                    text.dataset.speedY = speedY;
                                                }

                                                text.style.left = `${left}px`;
                                                text.style.top = `${top}px`;

                                                // Subtle rotation and scaling
                                                text.style.transform = `rotate(${-45 + Math.sin(Date.now() / 5000) * 5}deg) scale(${0.95 + Math.sin(Date.now() / 3000) * 0.05})`;
                                            }

                                            requestAnimationFrame(animate);
                                        }

                                        animate();
                                    }

                                    if (animationRunning) {
                                        animationRunning = false;
                                        cancelAnimation.style.display = 'none';
                                    } else {
                                        animationRunning = true;
                                        cancelAnimation.style.display = 'block';
                                        animateCancelText();
                                    }

                                } else {
                                    animationRunning = false;
                                    cancelAnimation.style.display = 'none';
                                    // $('#submitBtn').prop('disabled', false);
                                    // $('#settlement').prop('disabled', false);
                                    // $('#delete').prop('disabled', false);
                                }
                                // let subgroup = results.subgroup;
                                // if (subgroup != null) {
                                //     $('#company').val(subgroup.sub_code);
                                //     $('#compgst').text(subgroup.gstin);
                                // }
                                $('#roomno').html(optroomno);
                                // if (guestdetails != null) {
                                //     $('#company').val(guestdetails.company);
                                //     $('#compgst').text(guestdetails.gstin);
                                // }
                                // console.log(guestdetails);
                                $('#compdiv').removeClass('none');
                                $('#invalidbill').text('');
                                $('#submitBtn').text('Update');
                                $('#salebillform').prop('action', '{{ route('salebillupdate') }}');
                                let items = results.items;
                                let label = $('#label').val();
                                $('#roomnumbers').text(label + '. ' + items[0].roomno);
                                $('#orderno').text('Previous Order');
                                $('#roomno').prop('disabled', true);
                                $('#itemsdata tbody').empty();
                                $('#itemsdata tfoot').empty();

                                let sundrytype = results.sundrytype;
                                let suntransdata = results.suntransdata;
                                let outlet2code = results.outlet2code;
                                let tbodyData = '';
                                let tfootData = '';
                                let currentrowcount = $('#itemsdata tbody tr').length;
                                let currentrowcounttfoot = $('#itemsdata tfoot tr').length;
                                let ajaxRequestsCompleted = 0;
                                let groupedByRestcode = {};
                                let totalnetamount = 0.00;
                                suntransdata.forEach((sunitem) => {
                                    if (!groupedByRestcode[sunitem.restcode]) {
                                        groupedByRestcode[sunitem.restcode] = [];
                                    }
                                    groupedByRestcode[sunitem.restcode].push(sunitem);
                                });

                                let tfootHTML = `<tfoot class="bg-gallery salebilltfoot">
                                                                    <tr><td colspan="6"><div class="row">`;

                                for (const [restcode, items] of Object.entries(groupedByRestcode)) {
                                    tfootHTML += `<div id="${restcode}" class="col-md-6">
                                                                     <p class="h4 text-danger">${items[0].restname ?? 'Name Not Found'} (<span>${items[0].vno}</span>)</p>`;

                                    items.forEach((item, index) => {
                                        const disp = item.dispname ? item.dispname.trim().toLowerCase() : '';
                                        const nature = item.nature ? item.nature.trim().toLowerCase() : '';
                                        const bold = item.bold === 'Y' ? 'font-weight-bold' : '';

                                        if (index === 0) {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="${bold}">${item.dispname}</div>
                                                                            <div id="${item.vtype}totalamount">${item.amount}</div>
                                                                        </div>`;
                                        }

                                        if (disp === 'discount') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                                <input type="text" class="form-control discountfix" value="${item.baseamount}" name="${item.restcode}discountfix" id="${item.restcode}discountfix" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control discountsundry" value="${item.amount}" name="${item.restcode}discountsundry" id="${item.restcode}discountsundry" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (disp === 'service charge') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                                <input type="text" class="form-control servicechargefix" value="${item.svalue}" name="${item.restcode}servicechargefix" id="${item.restcode}servicechargefix" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control servicechargeamount" value="${item.amount}" name="${item.restcode}servicechargeamount" id="${item.restcode}servicechargeamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (nature === 'cgst') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control sevenem cgstamount" value="${item.amount}" name="${item.restcode}cgstamount" id="${item.restcode}cgstamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (nature === 'sgst') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control sevenem sgstamount" value="${item.amount}" name="${item.restcode}sgstamount" id="${item.restcode}sgstamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (nature === 'sale tax') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control sevenem vatamount" value="${item.amount}" name="${item.restcode}vatamount" id="${item.restcode}vatamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (disp === 'round off') {
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control sevenem roundoffamount" value="${item.amount}" name="${item.restcode}roundoffamount" id="${item.restcode}roundoffamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                            </div>
                                                                        </div>`;
                                        }

                                        if (disp === 'net amount') {
                                            totalnetamount += parseFloat(item.amount);
                                            tfootHTML += `<div class="d-flex justify-content-between mb-2">
                                                                            <div class="d-flex ${bold}">
                                                                                <span class="mt-2 mr-1">${item.dispname}</span>
                                                                            </div>
                                                                            <div>
                                                                                <input type="text" class="form-control sevenem netamount" value="${item.amount}" name="${item.restcode}netamount" id="${item.restcode}netamount" ${item.automanual == 'A' ? 'readonly' : ''}>
                                                                                <input type="hidden" class="form-control totalamount" name="${item.restcode}totalamountoutlet" id="${item.restcode}totalamountoutlet" value="0.00">
                                                                                <input type="hidden" name="${item.restcode}sundrycount" id="${item.restcode}sundrycount" value="${items.length}">
                                                                                <input type="hidden" name="${item.restcode}totaltaxable" id="${item.restcode}totaltaxable" value="0.00">
                                                                                <input type="hidden" name="${item.restcode}totalnontaxable" id="${item.restcode}totalnontaxable" value="0.00">
                                                                            </div>
                                                                        </div>`;
                                        }
                                    });

                                    tfootHTML += `</div>`;
                                }

                                if (outlet2code != null) {
                                    tfootHTML += `</div></td></tr>
                                                                <tr><td colspan="5">Total Amount:</td><td id="totalamttext" class="text-right h5">${totalnetamount.toFixed(2)}</td></tr>
                                                                </tfoot>`;
                                }

                                // Clear and append the updated <tfoot>
                                $('#itemsdata tfoot').remove();
                                $('#itemsdata').append(tfootHTML);

                                items.forEach((item, index) => {
                                    $('#waiter').val(item.waiter);
                                    $('#kotdocid').val(item.kotdocid);
                                    $('#stockdocid').val(item.docid);
                                    $('#vnostock').val(item.vno);
                                    $('#previousroomno').val(item.roomno);
                                    $('#pax').val(item.guaratt);
                                    let rowIndex = currentrowcount + index + 1;
                                    let taxcode = item.TaxStru;
                                    let discapp = item.discapp;
                                    let tax_rate = item.taxper;
                                    if (item.kot_yn == 'Y') {
                                        rateedit = (item.RateEdit == 'N') ? 'readonly' : '';
                                    } else {
                                        rateedit = (item.RateEdit == 'Y') ? '' : 'readonly';
                                    }
                                    let mainrate = item.actualrate * item.qtyiss;
                                    const loggedInUser = "{{ auth()->user()->name }}";

                                    let removeButtonHTML = '';
                                    if (loggedInUser === 'sa') {
                                        removeButtonHTML = `<span><button type="button" class="removeItem"><i class="fa-regular fa-circle-xmark"></i></button></span> `;
                                    }
                                    tbodyData += `<tr>
                                                                        <td style="white-space: nowrap;">
                                                                            ${removeButtonHTML}
                                                                            <input name="itemcode${rowIndex}" id="itemcode${rowIndex}" value="${item.item}" type="hidden">
                                                                            <input name="itemname${rowIndex}" class="itemnameclass" id="itemname${rowIndex}" value="${item.Name}" type="hidden">
                                                                            <input name="discapp${rowIndex}" id="discapp${rowIndex}" value="${item.discapp}" type="hidden">
                                                                            <input name="kotsno${rowIndex}" id="kotsno${rowIndex}" value="${item.kotsno}" type="hidden">
                                                                            <input name="kotsdocid${rowIndex}" id="kotsdocid${rowIndex}" value="${item.kotdocid}" type="hidden">
                                                                            <input name="outletfirst${rowIndex}" id="outletfirst${rowIndex}" value="${results.outlet1code}" type="hidden">
                                                                            <input name="outletsecond${rowIndex}" id="outletsecond${rowIndex}" value="${results.outlet2code}" type="hidden">
                                                                            <input name="mergedwith${rowIndex}" id="mergedwith${rowIndex}" value="${item.mergedwith}" type="hidden">
                                                                            <input name="itemrestcode${rowIndex}" id="itemrestcode${rowIndex}" value="${item.restcode}" type="hidden">
                                                                            <input name="itemnumber${rowIndex}" class="itemnumber" id="itemnumber${rowIndex}" value="${rowIndex}" type="hidden">
                                                                            ${item.Name}
                                                                        </td>
                                                                        <td><input readonly name="description${rowIndex}" value="${item.description}" placeholder="Enter" id="description${rowIndex}" class="form-control description inone" type="text"></td>
                                                                        <td class="text-center">${item.kotvno ?? ''}</td>
                                                                        <td>
                                                                            <div class="panelinc">
                                                                                <button type="button" style="${item.kot_yn == 'Y' ? 'display: none;' : ''}" class="decrement btn">-</button>
                                                                                <input name="quantity${rowIndex}" id="quantity${rowIndex}" class="form-control qtyitem" type="text" value="${item.qtyiss}" ${item.kot_yn == 'Y' ? 'readonly' : ''}>
                                                                                <button type="button" style="${item.kot_yn == 'Y' ? 'display: none;' : ''}" class="increment btn">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td><input oninput="checkNumMax(this, 7); handleDecimalInput(event);" ${rateedit} class="rateclass form-control sevenem" name="rate${rowIndex}" id="rate${rowIndex}" value="${item.actualrate}" type="text">
                                                                            <input type="hidden" value="${item.rate}" name="taxedrate${rowIndex}" id="taxedrate${rowIndex}" readonly>
                                                                            </td>
                                                                        <td>
                                                                            <input type="text" name="amount${rowIndex}" id="amount${rowIndex}" value="${mainrate.toFixed(2)}" class="form-control amount" readonly>
                                                                            <input type="hidden" name="discedamount${rowIndex}" id="discedamount${rowIndex}" value="${item.amount}" class="form-control discedamount" readonly>
                                                                            <input type="hidden" name="fixamount${rowIndex}" id="fixamount${rowIndex}" value="${item.amount}" class="form-control fixamount" readonly>
                                                                            <input type="hidden" value="${item.RateIncTax}" class="RateIncTax" id="RateIncTax${rowIndex}" name="RateIncTax${rowIndex}" readonly>
                                                                        </td>
                                                                        <td class="none"><input type="text" name="taxrate_sum${rowIndex}" id="taxrate_sum${rowIndex}" value="${item.taxper}" class="form-control taxrate_sum" readonly>
                                                                            <input name="tax_rate${rowIndex}" id="tax_rate${rowIndex}" value="${item.tax_rate ?? item.taxper}" type="hidden"></td>
                                                                        <td class="none"><input type="text" name="tax_code${rowIndex}" id="tax_code${rowIndex}" value="${item.tax_code}" class="form-control tax_code" readonly></td>
                                                                    </tr>`;
                                    ajaxRequestsCompleted++;
                                    if (ajaxRequestsCompleted === items.length) {
                                        $('#itemsdata tbody').append(tbodyData);
                                    }
                                });
                            }
                        }
                    }
                    itemnamexhr.send(`billno=${billno}&vprefix=${vprefix}&dcode=${dcode}&_token={{ csrf_token() }}`);
                    setTimeout(() => {
                        calculatetaxes();
                        // calculateDiscount();
                    }, 1500);
                }, 1000);
            });

            $(document).on('change', '#company', function () {
                let sub_code = $(this).val();
                let compxhr = new XMLHttpRequest();
                compxhr.open('POST', '/fetchcompdetail', true);
                compxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                compxhr.onreadystatechange = function () {
                    if (compxhr.readyState === 4 && compxhr.status === 200) {
                        let results = JSON.parse(compxhr.responseText);
                        $('#compgst').text(results == null ? '' : results);
                    }
                }
                compxhr.send(`sub_code=${sub_code}&_token={{ csrf_token() }}`);
            });

            $(document).on('input', '.discountfix', function () {
                let discvalue = parseFloat($(this).val());
                let discountmaxxhr = new XMLHttpRequest();
                discountmaxxhr.open('GET', '/discountmaxxhr', true);
                discountmaxxhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                discountmaxxhr.onreadystatechange = function () {
                    if (discountmaxxhr.status === 200 && discountmaxxhr.readyState === 4) {
                        let response = JSON.parse(discountmaxxhr.responseText);
                        if (typeof response.message !== 'undefined') {
                            // console.log("It's an property");
                        } else {
                            let maxvalue = parseFloat(response[0].posdiscountallowupto);
                            if (discvalue > maxvalue) {
                                pushNotify('error', 'Sale Bill Entry', `You Have Been Allow To Give Maximum ${response[0].posdiscountallowupto} % Discount Only.`, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                                $('#discountfix').val('0.00');
                            }
                        }

                    }
                }
                discountmaxxhr.send();
                setTimeout(() => {
                    let value = $(this).val();
                    if (value === '' || value === 0) {
                        $('#discountfix').val('0.00');
                        $('[id^="#taxvalues"]').val('0.00');
                    } else {
                        calculatetaxes();
                        // calculateDiscount();
                    }
                }, 500);
            });

            $(document).on('input', '.discountsundry', function () {
                setTimeout(() => {
                    let value = $(this).val();
                    if (value === '') {
                        $('#discountsundry').val('0.00');
                    } else {
                        calculatetaxes();
                        // calculateDiscountPercentage();
                    }
                }, 500);
            });

            $(document).on('input', '.servicechargefix', function () {
                setTimeout(() => {
                    let value = $(this).val();
                    if (value == '') {
                        $('#servicechargefix').val('0.00');
                    } else {
                        calculatetaxes();
                        // calculateDiscountPercentage();
                        // calculateDiscount();
                    }
                }, 500);
            });

            $(document).keypress(function (event) {
                if (event.which === 13) {
                    event.preventDefault();
                    console.log("Enter key pressed!");
                }
            });


            // Fun calculatetaxes
            let fixtotlamt;
            var outlet1;
            var outlet2;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            function calculatetaxes() {
                let tbodyLength = $('#itemsdata tbody tr').length;
                let outletData = {};

                for (let i = 1; i <= tbodyLength; i++) {
                    let itemrate = parseFloat($(`#fixamount${i}`).val()) || 0;
                    let taxeditemrate = itemrate;
                    let restcode = $(`#itemrestcode${i}`).val()?.trim() ?? '';
                    let trate = parseFloat($(`#tax_rate${i}`).val()) || 0;
                    let taxcode = $(`#tax_code${i}`).val()?.trim() ?? '';
                    let discapp = $(`#discapp${i}`).val()?.trim() ?? 'N';

                    let serviceApplicable = $(`#SChrgApp${i}`).val()?.trim() === 'Y';
                    if (!restcode) continue;

                    if (!outletData[restcode]) {
                        outletData[restcode] = {
                            total: 0,
                            taxable: 0,
                            nontaxable: 0,
                            vat: 0,
                            cgst: 0,
                            sgst: 0,
                            serviceableAmt: 0,
                            discountfix: parseFloat($(`#${restcode}discountfix`).val()) || 0,
                            servicefix: parseFloat($(`#${restcode}servicechargefix`).val()) || 0,
                            discountsundry: 0,
                            serviceamt: 0,
                            net: 0,
                            roundoff: 0,
                            fixnetamount: 0
                        };
                    }

                    if (discapp == 'Y') {
                        taxeditemrate -= (itemrate * outletData[restcode].discountfix / 100);
                        taxeditemrate = parseFloat(taxeditemrate.toFixed(2));
                        $(`#discedamount${i}`).val(taxeditemrate);
                    }

                    let srvrate = parseFloat($(`#serv_rate${i}`).val()) || 0;
                    let srvamt = srvrate ? (taxeditemrate * srvrate / 100) : (parseFloat($(`#serv_amt${i}`).val()) || 0);
                    srvamt = parseFloat(srvamt) || 0;

                    let taxvalue = parseFloat(calcper(taxeditemrate, trate)) || 0;

                    outletData[restcode].total += itemrate;

                    if (trate > 0) {
                        outletData[restcode].taxable += itemrate;
                    } else {
                        outletData[restcode].nontaxable += itemrate;
                    }

                    if (discapp === 'Y' && serviceApplicable) {
                        outletData[restcode].serviceableAmt += taxeditemrate;
                    }

                    if (taxcode.endsWith('VAAT')) {
                        outletData[restcode].vat += taxvalue;
                    } else {
                        let [cgstCode, sgstCode] = taxcode.split(',').map(v => v.trim());
                        if (cgstCode?.startsWith('CGSS')) outletData[restcode].cgst += taxvalue / 2;
                        if (sgstCode?.startsWith('SGSS')) outletData[restcode].sgst += taxvalue / 2;
                    }
                }

                // now calculate fixnetamount per outlet
                for (const outlet in outletData) {
                    const data = outletData[outlet];

                    if (data.serviceableAmt > 0 && data.servicefix > 0) {
                        data.serviceamt = (data.serviceableAmt * data.servicefix) / 100;
                    }

                    data.discountsundry = (data.total * data.discountfix) / 100;
                    let afterDiscount = data.total - data.discountsundry;

                    let fixnetamount = afterDiscount +
                        parseFloat(data.cgst.toFixed(2)) +
                        parseFloat(data.sgst.toFixed(2)) +
                        parseFloat(data.vat.toFixed(2)) +
                        parseFloat(data.serviceamt.toFixed(2));

                    data.fixnetamount = parseFloat(fixnetamount.toFixed(2));
                }

                // === single ajax request for all outlets ===
                let amounts = [];
                for (const outlet in outletData) {
                    amounts.push({ outlet: outlet, amount: outletData[outlet].fixnetamount });
                }

                $.ajax({
                    url: "{{ url('calculateroundoffpos') }}",
                    method: "POST",
                    data: { amounts: amounts },
                    success: function (response) {
                        let totalnetamt = 0.00;

                        response.forEach(r => {
                            let data = outletData[r.outlet];
                            if (!data) return;

                            data.roundoff = r.roundoff;
                            data.net = r.billamt;
                            totalnetamt += data.net;

                            $(`#${r.outlet}vatamount`).val(data.vat.toFixed(2));
                            $(`#${r.outlet}cgstamount`).val(data.cgst.toFixed(2));
                            $(`#${r.outlet}sgstamount`).val(data.sgst.toFixed(2));
                            $(`#${r.outlet}serviceamount`).val(data.serviceamt.toFixed(2));
                            $(`#${r.outlet}discountsundry`).val(data.discountsundry.toFixed(2));
                            $(`#${r.outlet}totalamount`).text(data.total.toFixed(2));
                            $(`#${r.outlet}totalamountoutlet`).val(data.total.toFixed(2));
                            $(`#${r.outlet}netamount`).val(data.net.toFixed(2));

                            $(`#${r.outlet}roundoffamount`).val(parseFloat(data.roundoff).toFixed(2));

                            $(`#${r.outlet}totaltaxable`).val(data.taxable.toFixed(2));
                            $(`#${r.outlet}totalnontaxable`).val(data.nontaxable.toFixed(2));
                        });

                        $('#totalitemsum').val(totalnetamt.toFixed(2));
                        $('#totalamttext').text(`Rs. ${totalnetamt.toFixed(2)}`);
                    },
                    error: function (err) {
                        console.error("Roundoff error:", err);
                    }
                });
            }


            $('#itemsdata tbody').on('click', '.removeItem', function () {
                let row = $(this).closest('tr');
                let rowIndex = row.index();
                row.remove();
                totaladditems--;
                $('#addeditems').text(totaladditems);
                $('#totalitems').val(totaladditems);
                pushNotify('success', 'Sale Bill Entry', totaladditems + ' Item Left', 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');
                $('#addeditems').css('font-size', 'large');
                setTimeout(() => {
                    $('#addeditems').css('font-size', 'small');
                }, 1000);

                // Adjust indices of subsequent rows
                $('#itemsdata tbody tr').each(function (index) {
                    let adjustedIndex = index + 1;
                    $(this).find('select, input').each(function () {
                        let originalName = $(this).attr('name');
                        let originalId = $(this).attr('id');
                        let newName = originalName.replace(/\d+$/, adjustedIndex);
                        let newId = originalId.replace(/\d+$/, adjustedIndex);
                        $(this).attr('name', newName);
                        $(this).attr('id', newId);
                    });
                    // Adjust the itemnumber value
                    $(this).find('.itemnumber').val(adjustedIndex);
                    setTimeout(() => {
                        calculatetaxes();
                        // calculateDiscount();
                    }, 500);
                });
            });

            $('#delete').on('click', function () {
                let docid = $("#sale1docid").val();
                if (docid == '' || typeof docid == 'undefined') {
                    pushNotify('error', 'Sale Bill Entry', 'Unknown Vno', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                    return;
                }

                Swal.fire({
                    icon: 'info',
                    title: 'Are you sure?',
                    text: 'Enter the reason for deleting:',
                    input: 'text',
                    inputPlaceholder: 'Reason',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        var reason = result.value;
                        if (reason) {
                            let updatedelflagxhr = new XMLHttpRequest();
                            updatedelflagxhr.open('post', 'updatedelflagxhr', true);
                            updatedelflagxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            updatedelflagxhr.onreadystatechange = function () {
                                if (updatedelflagxhr.status === 200 && updatedelflagxhr.readyState === 4) {
                                    let results = JSON.parse(updatedelflagxhr.responseText);
                                    pushNotify('success', 'Sale Bill Entry', results, 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                }
                            }
                            updatedelflagxhr.send(`docid=${docid}&reason=${reason}&_token={{ csrf_token() }}`);
                        }
                    }
                });

            });

            // Increment and Decrement functionality
            $(document).on('click', '.increment', function () {
                let counter = $(this).siblings('.qtyitem');
                let value = parseFloat(counter.val());
                let valueincr = value + 1;
                counter.val(valueincr);
                let trindex = $(this).closest('tr').index() + 1;
                let rate = parseFloat($(`#rate${trindex}`).val());
                let newamt = valueincr * rate;
                $(`#amount${trindex}`).val(newamt.toFixed(2));
                $(`#fixamount${trindex}`).val(newamt.toFixed(2));
                setTimeout(() => {
                    calculatetaxes();
                    // calculateDiscount();
                }, 500);
            });

            $(document).on('click', '.decrement', function () {
                let counter = $(this).siblings('.qtyitem');
                let value = parseFloat(counter.val());
                if (value > 1) {
                    let valuedcr = value - 1;
                    counter.val(valuedcr);
                    let trindex = $(this).closest('tr').index() + 1;
                    let rate = parseFloat($(`#rate${trindex}`).val());
                    let newamt = valuedcr * rate;
                    $(`#amount${trindex}`).val(newamt.toFixed(2));
                    $(`#fixamount${trindex}`).val(newamt.toFixed(2));
                    setTimeout(() => {
                        calculatetaxes();
                        // calculateDiscount();
                    }, 500);
                }
            });

            // Item Quantity Input
            $(document).on('input', '.qtyitem', function () {
                let value = parseFloat($(this).val());
                let trindex = $(this).closest('tr').index() + 1;
                let rate = parseFloat($(`#rate${trindex}`).val());
                let newamt = value * rate;
                $(`#amount${trindex}`).val(newamt.toFixed(2));
                $(`#fixamount${trindex}`).val(newamt.toFixed(2));
                setTimeout(() => {
                    calculatetaxes();
                    // calculateDiscount();
                }, 500);
            });

            // Input Rate Change
            $(document).on('input', '.rateclass', function () {
                const $row = $(this).closest('tr');
                const trindex = $row.index() + 1;
                const value = parseFloat($(this).val()) || 0;
                const qty = parseFloat($(`#quantity${trindex}`).val()) || 0;
                const rateinctax = $(`#RateIncTax${trindex}`).val();
                const tax_rate = parseFloat($(`#tax_rate${trindex}`).val()) || 0;

                let newamt = value * qty;

                if (rateinctax === 'Y') {
                    newamt = (value * qty * 100) / (100 + tax_rate);
                }

                const formattedAmount = newamt.toFixed(2);

                $(`#amount${trindex}, #fixamount${trindex}`).val(formattedAmount);

                requestAnimationFrame(() => {
                    calculatetaxes();
                    // calculateDiscount();
                    // calculateDiscountPercentage();
                });
            });

            setTimeout(function () {
                $('#favourite').trigger('click');
                $('#vtype').trigger('input')
            }, 100);

            $('.modalclosebtn').click(function () {
                $('#phoneno').val('');
                $('#customername').val('');
                $('#address').val('');
                $('#city').val('');
                $('#like').val('');
                $('#dislike').val('');
                $('#birthdate').val('');
                $('#anniversary').val('');
            });

            $(document).on('click', '#customerdetailsave', function () {
                if ($('#phoneno').val().length < 10) {
                    let errspan = `<span id="errorphone" class="position-absolute text-danger">Phone Length Should Be Equal To 10</span>`;
                    $('#phonediv').append(errspan);
                    $('#customerModal').data('dismiss', false);
                    return;
                } else {
                    $('#errorphone').remove();
                    $('#customerModal').modal('hide');
                }
            });
            let timerphone;
            $(document).on('input', '#phoneno', function () {
                clearTimeout(timerphone);
                var phoneno = $(this);
                if (phoneno.val().length == 10) {
                    $('#errorphone').remove();
                    let phonefindxhr = new XMLHttpRequest();
                    phonefindxhr.open('post', '/phonefindxhr', true);
                    phonefindxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    phonefindxhr.onreadystatechange = function () {
                        if (phonefindxhr.readyState === 4) {
                            if (phonefindxhr.status === 200) {
                                let result = JSON.parse(phonefindxhr.responseText);
                                if (result != 'Not Found') {
                                    let data = result.data;
                                    if (data.length > 0) {
                                        let customername;
                                        let address;
                                        let city;
                                        let like;
                                        let dislike;
                                        let birthdate;
                                        let anniversary;
                                        var row;
                                        let previousVisitTime = '';
                                        data.forEach((tdata, index) => {
                                            let rowClass = '';
                                            customername = tdata.customername;
                                            address = tdata.add1;
                                            city = tdata.city;
                                            likes = tdata.likes;
                                            dislike = tdata.dislikes;
                                            birthdate = tdata.dob;
                                            anniversary = tdata.anniversary;
                                            if (tdata.visittime !== previousVisitTime) {
                                                rowClass = (index % 3 === 0) ? 'table-success' :
                                                    (index % 3 === 1) ? 'table-info' : 'table-danger';
                                                previousVisitTime = tdata.visittime;
                                            }
                                            row += `<tr class="${rowClass}">
                                                                                    <td>${tdata.visittime ?? ''}</td>
                                                                                    <td>${tdata.itemname ?? ''}</td>
                                                                                    <td>${tdata.qtyiss ?? ''}</td>
                                                                                    <td>${tdata.rate ?? ''}</td>
                                                                                    <td>${tdata.amount ?? ''}</td>
                                                                                </tr>`;
                                        });

                                        if (birthdate != null) {
                                            let chkbirthday = GetBirthday(birthdate, customername, 'Birthday');
                                            if (typeof chkbirthday != 'undefined') {
                                                $('#birthdaytext').text(chkbirthday);
                                                $('.birthday-message').fadeIn();
                                                startBalloons();

                                                for (let i = 0; i < 50; i++) {
                                                    let sparkle = $('<div class="sparkle"></div>');
                                                    sparkle.css({
                                                        top: Math.random() * 100 + '%',
                                                        left: Math.random() * 100 + '%',
                                                        animationDelay: Math.random() * 1.5 + 's'
                                                    });
                                                    $('.sparkles').append(sparkle);
                                                }

                                                // Bind the close button click event
                                                $(document).one('click', '#clsbtnoc', function () {
                                                    $('.birthday-message').fadeOut(function () {
                                                        $('#hideBtn').fadeOut();
                                                        $('.sparkles').empty();
                                                        stopBalloons();

                                                        // Check for anniversary message after closing the birthday message
                                                        if (anniversary != null) {
                                                            let chkaniversary = GetBirthday(anniversary, customername, 'Aniversary');
                                                            if (chkaniversary != '') {
                                                                $('#birthdaytext').text(chkaniversary);
                                                                $('.birthday-message').fadeIn();
                                                                startBalloons();

                                                                for (let i = 0; i < 50; i++) {
                                                                    let sparkle = $('<div class="sparkle"></div>');
                                                                    sparkle.css({
                                                                        top: Math.random() * 100 + '%',
                                                                        left: Math.random() * 100 + '%',
                                                                        animationDelay: Math.random() * 1.5 + 's'
                                                                    });
                                                                    $('.sparkles').append(sparkle);
                                                                }

                                                                // Bind the close button click event for the anniversary message
                                                                $(document).one('click', '#clsbtnoc', function () {
                                                                    $('.birthday-message').fadeOut();
                                                                    $('#hideBtn').fadeOut();
                                                                    $('.sparkles').empty();
                                                                    stopBalloons();
                                                                });
                                                            }
                                                        }
                                                    });
                                                });
                                            }
                                        } else if (anniversary != null) {
                                            console.log('anniversarytrue');
                                            let chkaniversary = GetBirthday(anniversary, customername, 'Aniversary');
                                            if (chkaniversary != '') {
                                                $('#birthdaytext').text(chkaniversary);
                                                $('.birthday-message').fadeIn();
                                                startBalloons();

                                                for (let i = 0; i < 50; i++) {
                                                    let sparkle = $('<div class="sparkle"></div>');
                                                    sparkle.css({
                                                        top: Math.random() * 100 + '%',
                                                        left: Math.random() * 100 + '%',
                                                        animationDelay: Math.random() * 1.5 + 's'
                                                    });
                                                    $('.sparkles').append(sparkle);
                                                }

                                                // Bind the close button click event for the anniversary message
                                                $(document).one('click', '#clsbtnoc', function () {
                                                    $('.birthday-message').fadeOut();
                                                    $('#hideBtn').fadeOut();
                                                    $('.sparkles').empty();
                                                    stopBalloons();
                                                });
                                            }
                                        }


                                        $('#customername').val(customername);
                                        $('#address').val(address);
                                        $('#customercity').val(city);
                                        $('#like').val(likes);
                                        $('#dislike').val(dislike);
                                        $('#birthdate').val(ymd(birthdate));
                                        $('#anniversary').val(anniversary != null ? ymd(anniversary) : '');
                                        pushNotify('success', 'Sale Bill Entry', `Previous Details Found For Phone ${phoneno.val()}`, 'fade', 300, '', '', true, true, true, 5000, 20, 20, 'outline', 'right top');
                                        $('#guesttable table tbody').append(row);
                                        $('#guesttable').css('display', 'block');
                                        $('#guesttable').addClass('animation box animate__bounceIn');
                                    }
                                } else {
                                    $('#customername').val('');
                                    $('#address').val('');
                                    $('#customercity').val('');
                                    $('#like').val('');
                                    $('#dislike').val('');
                                    $('#birthdate').val('');
                                    $('#anniversary').val('');
                                }
                            }
                        }
                    }

                    timerphone = setTimeout(() => {
                        phonefindxhr.send(`phoneno=${phoneno.val()}&_token={{ csrf_token() }}`);
                    }, 500);
                } else {
                    $('#customername').val('');
                    $('#address').val('');
                    $('#customercity').val('');
                    $('#like').val('');
                    $('#dislike').val('');
                    $('#birthdate').val('');
                    $('#anniversary').val('');
                }
            });

            $(document).on('click', '#closeguestdiv', function () {
                $('#guesttable table tbody').empty();
                $('#guesttable').css('display', 'none');
                $('#guesttable').removeClass('animation box animate__bounceIn');
            });
        });

        $(document).on('change', '#birthdate', function () {
            setTimeout(() => {
                let chkbirthday = GetBirthday(dmy($(this).val()), $('#customername').val() ?? '', 'Birthday');
                if (typeof chkbirthday != 'undefined') {
                    $('#birthdaytext').text(chkbirthday);
                    $('.birthday-message').fadeIn();
                    startBalloons();

                    for (let i = 0; i < 50; i++) {
                        let sparkle = $('<div class="sparkle"></div>');
                        sparkle.css({
                            top: Math.random() * 100 + '%',
                            left: Math.random() * 100 + '%',
                            animationDelay: Math.random() * 1.5 + 's'
                        });
                        $('.sparkles').append(sparkle);
                    }
                }
            }, 2000);
        });

        $(document).on('change', '#anniversary', function () {
            let chkbirthday = GetBirthday(dmy($(this).val()), $('#customername').val() ?? '', 'Anniversary');
            if (typeof chkbirthday != 'undefined') {
                $('#birthdaytext').text(chkbirthday);
                $('.birthday-message').fadeIn();
                startBalloons();

                for (let i = 0; i < 50; i++) {
                    let sparkle = $('<div class="sparkle"></div>');
                    sparkle.css({
                        top: Math.random() * 100 + '%',
                        left: Math.random() * 100 + '%',
                        animationDelay: Math.random() * 1.5 + 's'
                    });
                    $('.sparkles').append(sparkle);
                }
            }
        });

        makeDraggable('guesttable');
        makeResizable('guesttable', 'resizeHandle');

        let isResizing = false; // Flag to track if resizing is in progress

        // Function to enable dragging
        function makeDraggable(elementId) {
            const element = document.getElementById(elementId);
            let offsetX = 0,
                offsetY = 0,
                initialX = 0,
                initialY = 0;

            element.addEventListener('mousedown', startDrag);

            function startDrag(e) {
                if (isResizing) return; // Prevent dragging if resizing
                e.preventDefault();
                initialX = e.clientX;
                initialY = e.clientY;
                document.addEventListener('mousemove', dragElement);
                document.addEventListener('mouseup', stopDrag);
            }

            function dragElement(e) {
                e.preventDefault();
                offsetX = initialX - e.clientX;
                offsetY = initialY - e.clientY;
                initialX = e.clientX;
                initialY = e.clientY;
                element.style.top = (element.offsetTop - offsetY) + "px";
                element.style.left = (element.offsetLeft - offsetX) + "px";
            }

            function stopDrag() {
                document.removeEventListener('mousemove', dragElement);
                document.removeEventListener('mouseup', stopDrag);
            }
        }

        // Function to enable resizing
        function makeResizable(elementId, handleId) {
            const element = document.getElementById(elementId);
            const handle = document.getElementById(handleId);
            let startX = 0,
                startY = 0,
                startWidth = 0,
                startHeight = 0;

            handle.addEventListener('mousedown', startResize);

            function startResize(e) {
                e.preventDefault();
                isResizing = true; // Set the flag to true
                startX = e.clientX;
                startY = e.clientY;
                startWidth = parseInt(document.defaultView.getComputedStyle(element).width, 10);
                startHeight = parseInt(document.defaultView.getComputedStyle(element).height, 10);
                document.addEventListener('mousemove', doResize);
                document.addEventListener('mouseup', stopResize);
            }

            function doResize(e) {
                e.preventDefault();
                element.style.width = startWidth + e.clientX - startX + 'px';
                element.style.height = startHeight + e.clientY - startY + 'px';
            }

            function stopResize() {
                isResizing = false; // Reset the flag
                document.removeEventListener('mousemove', doResize);
                document.removeEventListener('mouseup', stopResize);
            }
        }

        function GetBirthday(birthdate, birthdayboy, occation) {
            let today = new Date();
            let curMonth = today.getMonth() + 1;
            let curDate = today.getDate();
            let fmtdob = new Date(ymd(birthdate));
            let birthdayMonth = fmtdob.getMonth() + 1;
            let birthdayDate = fmtdob.getDate();
            let guestBirthdayThisYear = new Date(today.getFullYear(), birthdayMonth - 1, birthdayDate);
            if (guestBirthdayThisYear < today) {
                guestBirthdayThisYear.setFullYear(today.getFullYear() + 1);
            }
            let timeDifference = guestBirthdayThisYear - today;
            let daysUntilBirthday = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
            if (daysUntilBirthday === 365) {
                return `Happy ${occation}, ${birthdayboy} Today is your ${occation}!`;
            } else if (daysUntilBirthday <= 30) {
                return `Happy ${occation}, ${birthdayboy} Your ${occation} is on ${guestBirthdayThisYear.toDateString()}.`;
            }
        }

        let animationInterval;

        function startBalloons() {
            $('.balloon').each(function () {
                $(this).css({
                    display: 'block',
                    bottom: '-100px',
                    left: Math.random() * 100 + '%'
                });

                animateBalloon(this);
            });
        }

        function animateBalloon(balloon) {
            $(balloon).animate({
                bottom: '100%'
            }, {
                duration: 10000,
                easing: 'linear',
                complete: function () {
                    $(this).css({
                        bottom: '-100px',
                        left: Math.random() * 100 + '%'
                    });
                    animateBalloon(this);
                }
            });
        }

        function stopBalloons() {
            $('.balloon').stop(true, true).css('display', 'none');
        }

        let element2 = document.getElementById('ncurdate2');
        fetchncur(element2);

        function updateTime() {
            let options = {
                timeZone: 'Asia/Kolkata',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            let currentTime = new Date().toLocaleString('en-US', options);
            let curTimeElement = document.getElementById('curtime');
            curTimeElement.textContent = currentTime;
        }
        $('input[name^="discountfix"]').on('click', function () {
            $(this).val('0');
            $(this).trigger('input');
        });
        $('input[name^="discountfix"]').on('input', function () {
            $(this).val($(this).val().replace(/[^0-9.]/g, ''));
            var val = parseFloat($(this).val());
            if (isNaN(val) || val > 99.99) {
                $(this).val('');
            }
        });

        updateTime();
        // setInterval(() => {
        //     updateTime();
        // }, 1000);

        $('#billprint').click(function () {
            let oldroomyn = $('#oldroomyn').val();
            $('#billprinty').val('Y');
            if (oldroomyn == 'N') {
                $('#submitBtn').click();
                setTimeout(() => {
                    $(this).prop('disabled', true);
                    $('#submitBtn').prop('disabled', true);
                }, 10);
            } else {
                let tbody = $('#itemsdata tbody');
                let rowcount = tbody.find('tr').length;
                let roomno = $('#roomno').val();
                if (roomno === '' || roomno === null) {
                    pushNotify('error', 'Salebill Entry', 'Please Select Room No.!');
                    return;
                }
                if (rowcount === 0) {
                    pushNotify('error', 'Salebill Entry', 'Please Add Some Item First!');
                    return;
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                let vnoup = $('#vnoup').val();
                let vdatesale1 = $('#vdatesale1').val();
                let sale1docid = $('#sale1docid').val();
                let vtype = $('#vtype').val();
                let departname = $('#departname').val();
                let filetoopen;
                if ($('#printdescription').val() == 'Bill Windows Plain Paper') {
                    filetoopen = 'salebillprint';
                    let kotno = $('#kotno').val();
                    let waitersname = $('#waitersname').val();
                    let outletcode = $('#fixrestcode').val();
                    let departnature = $('#departnature').val();
                    let addeddocid = $('#addeddocid').val() ?? '';

                    let openfile = window.open(filetoopen, '_blank');
                    openfile.onload = function () {
                        $('#roomno', openfile.document).text(roomno);
                        $('#vdate', openfile.document).text(vdatesale1);
                        $('#billno', openfile.document).text(vnoup);
                        $('#vtype', openfile.document).text(vtype);
                        $('#departname', openfile.document).text(departname);
                        $('#kotno', openfile.document).text(kotno);
                        $('#waiter', openfile.document).text(waitersname);
                        $('#outletcode', openfile.document).text(outletcode);
                        $('#departnature', openfile.document).text(departnature);
                        $('#addeddocid', openfile.document).text(addeddocid);
                    }
                } else if ($('#printdescription').val() == '3 Inch Running Paper Windows Print') {
                    filetoopen = 'salebillprint2';
                    let kotno = $('#kotno').val();
                    let waitersname = $('#waitersname').val();
                    let outletcode = $('#fixrestcode').val();
                    let departnature = $('#departnature').val();
                    let addeddocid = $('#addeddocid').val() ?? '';

                    let openfile = window.open(filetoopen, '_blank');
                    openfile.onload = function () {
                        $('#roomno', openfile.document).text(roomno);
                        $('#vdate', openfile.document).text(vdatesale1);
                        $('#billno', openfile.document).text(vnoup);
                        $('#vtype', openfile.document).text(vtype);
                        $('#departname', openfile.document).text(departname);
                        $('#kotno', openfile.document).text(kotno);
                        $('#waiter', openfile.document).text(waitersname);
                        $('#outletcode', openfile.document).text(outletcode);
                        $('#departnature', openfile.document).text(departnature);
                        $('#addeddocid', openfile.document).text(addeddocid);
                    }

                } else if ($('#printdescription').val() == '3 Inch Running Paper DOS Print') {
                    $.ajax({
                        url: 'salebillprintthermal',
                        data: {
                            docid: sale1docid
                        },
                        method: "POST",
                        success: function (response) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    })
                }

            }
        });
    </script>
@endsection