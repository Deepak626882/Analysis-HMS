{{-- 
#𝒲𝒜𝑅𝒩𝐼𝒩𝒢⚠️ 𝒟𝒪𝒩'𝒯 𝒰𝒮𝐸 𝒞𝒪𝒟𝐸 𝐹𝒪𝑅𝑀𝒜𝒯𝒯𝐸𝑅 𝒪𝑅 𝒮𝐻𝐼𝐹𝒯+𝒜𝐿𝒯+𝒫 𝒪𝒩 𝒯𝐻𝐼𝒮 𝒫𝒜𝒢𝐸 
𝒞𝒪𝒟𝐸 𝒪𝑅 𝒯𝐻𝐸 𝒫𝑅𝒪𝒟𝒰𝒞𝒯𝐼𝒪𝒩 𝒲𝐼𝐿𝐿 𝐵𝐸 𝐵𝑅𝒪𝒦𝐸𝒩
--}}

@extends('property.layouts.main')
@section('main-container')
    <link href="admin/plugins/clockpicker/dist/jquery-clockpicker.min.css" rel="stylesheet">
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body walkin">
                            {{-- action="{{ route('walkinsubmit') }}" --}}
                            <form class="walkin-form" id="walkinform" name="walkinform"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="docid" value="{{ $data[0]->BookingDocid }}">
                                <input type="hidden" name="bookingsno" value="{{ $data[0]->Sno }}">
                                <input type="hidden" name="folioNo" value="{{ $data[0]->BookNo }}">
                                <input type="hidden" name="guestprof" value="{{ $data[0]->GuestProf }}">
                                <input type="hidden" value="{{ $ncurdate }}" name="curdate" id="curdate">
                                <input type="hidden" value="{{ $companydata->comp_name }}" id="compname" name="compname">
                                <input type="hidden" value="{{ $companydata->address1 }}" id="address" name="address">
                                <input type="hidden" value="{{ $companydata->mobile }}" id="compmob" name="compmob">
                                <input type="hidden" value="{{ $companydata->email }}" id="email" name="email">
                                <input type="hidden" value="{{ $companydata->logo }}" id="logo" name="logo">
                                <input type="hidden" value="{{ $companydata->u_name }}" id="u_name" name="u_name">
                                <input type="hidden" name="totalrooms" value="{{ count($data) }}" id="totalrooms">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table walkin-table table-responsive">
                                            <thead>
                                                <th>Check-In</th>
                                                <th></th>
                                                <th style="text-align: center !important;font-size: x-small;">Nights</th>
                                                <th>Checkout</th>
                                                <th></th>
                                                <th>Room</th>
                                                <th>Remarks</th>
                                                <th>Pick Up/Drop <i class="fa-solid fa-truck-pickup"></i></th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="date" name="checkindate" class="form-control low alibaba"
                                                            placeholder="2023-10-26" value="{{ $data[0]->ArrDate }}"
                                                            id="checkindate" onchange="validateDates()" required>
                                                    </td>

                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="time" value="{{ $data[0]->ArrTime }}" id="checkintime"
                                                                name="checkintime" class="form-control low" required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            onchange="DisplayCheckout()"
                                                            oninput="ValidateNum(this, '1', '100', '3')" name="stay_days"
                                                            id="stay_days" class="form-control stays" value={{ $data[0]->NoDays }} required>
                                                    </td>
                                                    <td>
                                                        <input type="date" value="{{ $data[0]->DepDate }}" name="checkoutdate"
                                                            class="form-control low alibaba" placeholder="2023-10-26"
                                                            id="checkoutdate" onchange="validateDates()" required>
                                                        <span class="text-danger absolute-element" id="date-error"></span>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="time" value="{{ $data[0]->DepTime }}" id="checkouttime"
                                                                name="checkouttime" class="form-control low" required>
                                                        </div>
                                                    </td>
                                                    <td><input id="rooms" value="{{ $data[0]->NoofRooms }}"
                                                            style="text-align: end;" type="number"
                                                            oninput="ValidateNum(this, '1', '100', '3')" name="rooms"
                                                            class="form-control low fiveem" placeholder="1">
                                                    </td>
                                                    <td><input placeholder="Remarks" class="form-control" name="remarkmain" id="remarkmain" type="text" value="{{ $data[0]->Remarks }}"></td>
                                                    <td><input placeholder="Pickup/Drop" class="form-control" name="pickupdrop" id="pickupdrop" type="text" value="{{ $data[0]->pickupdrop }}"></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table walkin-table table-responsive">
                                            <thead>
                                                <tr>
                                                    <th>Company</th>
                                                    <th>Booking Source</th>
                                                    <th>Business Source</th>
                                                    <th style="display: {{ $data[0]->MarketSeg == 'Travel Agent' ? 'block' : 'none' }};"
                                                        id="trvelth">Travel Agent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select id="company" name="company" class="form-control low">
                                                            @if (empty($data[0]->sub_code))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            @foreach ($company as $list)
                                                                <option value="{{ $list->sub_code }}" {{ $data[0]->Company == $list->sub_code ? 'selected' : '' }} data-gst="{{ $list->gstin }}">
                                                                    {{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="position-absolute p-0">
                                                            <p id="gstCodep" class="text-purple"
                                                                style="font-size: small;display: none;">GST
                                                                No.: <span id="gstCode"></span></p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select id="booking_source" name="booking_source"
                                                            class="form-control low" onchange="toggleTravelAgent(this.value)"
                                                            required>
                                                            @if (empty($data[0]->MarketSeg))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            <option value="Booking Engine" {{ $data[0]->MarketSeg == 'Booking Engine' ? 'selected' : '' }}>Booking Engine</option>
                                                            <option value="OTA" {{ $data[0]->MarketSeg == 'OTA' ? 'selected' : '' }}>OTA</option>
                                                            <option value="Travel Agent" {{ $data[0]->MarketSeg == 'Travel Agent' ? 'selected' : '' }}>Travel Agent</option>
                                                            <option value="Direct" {{ $data[0]->MarketSeg == 'Direct' ? 'selected' : '' }}>Direct</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="bsource" name="bsource" class="form-control low" required>
                                                            @if (empty($data[0]->BussSource))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            @foreach ($bsource as $list)
                                                                <option value="{{ $list->bcode }}" {{ $data[0]->BussSource == $list->bcode ? 'selected' : '' }}>{{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td style="display: {{ $data[0]->MarketSeg == 'Travel Agent' ? 'block' : 'none' }};"
                                                        id="trveltd">
                                                        <select id="travel_agent" name="travel_agent" class="form-control low">
                                                            @if (empty($data[0]->TravelAgency))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            @foreach ($travel_agent as $list)
                                                                <option value="{{ $list->sub_code }}" {{ $data[0]->TravelAgency == $list->sub_code ? 'selected' : '' }}>{{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <p class="text-center font-weight-bold">Advance Details <i class="fa-solid fa-money-bill"></i></p>
                                            <table class="table table-hover table-bordered table-payshow">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Paytype</th>
                                                        <th>On Date</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total = 0.0;
                                                    @endphp
                                                    @foreach ($advance as $item)
                                                        @php
                                                            $total += $item->amtcr;
                                                        @endphp
                                                        <tr data-vno="{{ $item->vno }}" data-roomno="{{ $item->roomno }}">
                                                            <td class="paytype">{{ $item->paytype }}</td>
                                                            <td>{{ date('d-M-Y H:i', strtotime($item->u_entdt)) }}</td>
                                                            <td class="amount">{{ $item->amtcr }}</td>
                                                            <td class="btn btn-dark btn-block advanceprint">Print</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td></td>
                                                        <td>{{ str_replace(',', '', number_format($total, 2)) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div>

                                    <div class="form-group form-check">
                                        <input type="checkbox" {{ $data[0]->complimentry == 'Y' ? 'checked' : '' }}
                                            class="form-check-input" name="complimentry"
                                            id="complimentry">
                                        <label class="form-check-label" for="complimentry">Complimentry Room</label>
                                    </div>

                                    <table class="table-hover walkin-multi table-responsive" id="gridtaxstructure">
                                        <thead>
                                            <tr>
                                                <th>Room Type</th>
                                                <th>Room</th>
                                                <th>Adult</th>
                                                <th>Child</th>
                                                <th>Plans</th>
                                                <th>Rate Rs.</th>
                                                <th>Tax Inc.</th>
                                                <th>Leader</th>
                                                <th id="thlast">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $dbrow)
                                                <tr class="data-row">
                                                    <td>
                                                        <select id="cat_code{{ $dbrow->Sno }}" name="cat_code{{ $dbrow->Sno }}" class="form-control sl cat_code_class" required>
                                                            @if (empty($dbrow->RoomCat))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            @foreach ($roomcat as $list)
                                                                <option value="{{ $list->cat_code }}" {{ $dbrow->RoomCat == $list->cat_code ? 'selected' : '' }}>{{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" value="{{ $dbrow->planedit }}" class="form-control" name="planedit{{ $dbrow->Sno }}" id="planedit{{ $dbrow->Sno }}" readonly>
                                                    </td>
                                                    <td>
                                                        <select id="roommast{{ $dbrow->Sno }}" name="roommast{{ $dbrow->Sno }}" class="form-control sl" required>
                                                            @if (empty($dbrow->RoomNo))
                                                            <option value="" selected>Select</option>
                                                            @else
                                                            <option value="">Select</option>
                                                            <option value="{{ $dbrow->RoomNo }}" selected>{{ $dbrow->RoomNo }}</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="adult{{ $dbrow->Sno }}" name="adult{{ $dbrow->Sno }}" class="form-control sl" required>
                                                            @if (empty($dbrow->Adults))
                                                            <option value="" selected>Select</option>
                                                            @else
                                                            <option value="">Select</option>
                                                            @endif
                                                            <option value="1" {{ $dbrow->Adults == '1' ? 'selected' : '' }}>1</option>
                                                            <option value="2" {{ $dbrow->Adults == '2' ? 'selected' : '' }}>2</option>
                                                            <option value="3" {{ $dbrow->Adults == '3' ? 'selected' : '' }}>3</option>
                                                            <option value="4" {{ $dbrow->Adults == '4' ? 'selected' : '' }}>4</option>
                                                            <option value="5" {{ $dbrow->Adults == '5' ? 'selected' : '' }}>5</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="child{{ $dbrow->Sno }}" name="child{{ $dbrow->Sno }}" class="form-control sl" required>
                                                            @if (empty($dbrow->Childs))
                                                            <option value="0" selected>0</option>
                                                            @else
                                                            <option value="0" {{ $dbrow->Childs == '0' ? 'selected' : '' }}>0</option>
                                                            <option value="1" {{ $dbrow->Childs == '1' ? 'selected' : '' }}>1</option>
                                                            <option value="2" {{ $dbrow->Childs == '2' ? 'selected' : '' }}>2</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="planmaster{{ $dbrow->Sno }}" name="planmaster{{ $dbrow->Sno }}" class="form-control planmastclass sl">
                                                            @if (empty($dbrow->Plan_Code))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                                <option value="{{ $dbrow->Plan_Code }}" selected>{{ $dbrow->planname }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($dbrow->planedit == 'Y')
                                                            <span data-sn="{{ $dbrow->Sno }}" class="text-center planviewbtn ARK font-weight-bold">View Plan</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" name="rate{{ $dbrow->Sno }}" id="rate{{ $dbrow->Sno }}" oninput="checkNumMax(this, 10); handleDecimalInput(event);" class="form-control ratechk sp" value="{{ $dbrow->Tarrif }}" required {{ $dbrow->planedit == 'Y' ? 'readonly' : '' }}>
                                                    </td>
                                                    <td>
                                                        <select class="form-control taxchk sl" name="tax_inc{{ $dbrow->Sno }}" id="tax_inc{{ $dbrow->Sno }}">
                                                            @if (empty($dbrow->IncTax))
                                                                <option value="" selected>Select</option>
                                                            @else
                                                                <option value="">Select</option>
                                                            @endif
                                                            <option value="Y" {{ $dbrow->IncTax == 'Y' ? 'selected' : '' }}>Yes</option>
                                                            <option value="N" {{ $dbrow->IncTax == 'N' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="leadercl" name="leader{{ $dbrow->Sno }}" id="leader{{ $dbrow->Sno }}">
                                                    </td>
                                                    <td>
                                                        @if ($dbrow->Sno > 1)
                                                            <img src="admin/icons/flaticon/remove.gif" alt="remove icon" class="remove-icon">
                                                        @endif
                                                        <img src="admin/icons/flaticon/copy.gif" alt="copy icon" class="copy-icon">
                                                    </td>
                                                    <td>
                                                        @if ($dbrow->planedit == 'Y')
                                                            <div class="">
                                                                <div style="display: none;" id="table-planmast{{ $dbrow->Sno }}" class="table-responsive table-planmast">
                                                                    <h3 class="text-center adc">Plan Details</h3>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <label id="plannamelabel" class="col-form-label" for="planname">Plan</label>
                                                                            <input type="text" value="{{ $dbrow->chargename }}" class="form-control" name="planname{{ $dbrow->Sno }}" id="planname{{ $dbrow->Sno }}" readonly>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <label id="plankaamountlabel" class="col-form-label" for="plankaamount">Plan Amount</label>
                                                                            <input autocomplete="off" type="text" value="{{ $dbrow->bnetplanamt }}" class="form-control planrow" name="plankaamount{{ $dbrow->Sno }}" id="plankaamount{{ $dbrow->Sno }}">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <label id="taxincplanroomratelabel" class="col-form-label" for="taxincplanroomrate">Inc. In Room Rate</label>
                                                                            <input type="text" value="{{ $dbrow->btaxinc }}" class="form-control" name="taxincplanroomrate{{ $dbrow->Sno }}" id="taxincplanroomrate{{ $dbrow->Sno }}" readonly>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <label id="roomratelabel" class="col-form-label" for="roomrate">Room Rate</label>
                                                                            <input type="text" value="{{ $dbrow->broom_rate_before_tax }}" class="form-control" name="roomrate{{ $dbrow->Sno }}" id="roomrate{{ $dbrow->Sno }}" readonly>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <label id="netroomratelabel" class="col-form-label" for="netroomrate">Net Room Rate</label>
                                                                            <input type="text" value="{{ $dbrow->bnetplanamt - $dbrow->bamount }}" class="form-control" name="netroomrate{{ $dbrow->Sno }}" id="netroomrate{{ $dbrow->Sno }}" readonly>
                                                                            <input type="hidden" value="{{ $dbrow->btotal_rate }}" class="form-control" name="plansumrate{{ $dbrow->Sno }}" id="plansumrate{{ $dbrow->Sno }}">
                                                                            <input type="hidden" value="{{ $dbrow->btaxstru }}" class="form-control" name="taxstruplan{{ $dbrow->Sno }}" id="taxstruplan{{ $dbrow->Sno }}">
                                                                            <input type="hidden" value="{{ $dbrow->room_perplan }}" class="form-control" name="planpercent{{ $dbrow->Sno }}" id="planpercent{{ $dbrow->Sno }}">
                                                                            <input type="hidden" value="{{ $dbrow->pcode }}" class="form-control" name="plancodeplan{{ $dbrow->Sno }}" id="plancodeplan{{ $dbrow->Sno }}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 d-flex justify-content-around">
                                                                        <table id="planmasttable{{ $dbrow->Sno }}" class="table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Sn</th>
                                                                                    <th>Fixed Charge</th>
                                                                                    <th>Amount</th>
                                                                                    <th>Percentage</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>{{ $dbrow->Sno }}</td>
                                                                                    <td>{{ $dbrow->chargename }}</td>
                                                                                    <td><input autocomplete="off" type="text" value="{{ $dbrow->bamount }}" class="form-control rowdamount" name="rowdamount{{ $dbrow->Sno }}" id="rowdamount{{ $dbrow->Sno }}"></td>
                                                                                    <td>
                                                                                        <input type="text" value="{{ $dbrow->bplanper }}" class="form-control" name="rowdplan_per{{ $dbrow->Sno }}" id="rowdplan_per{{ $dbrow->Sno }}" readonly>
                                                                                        <input type="hidden" value="{{ $dbrow->bfixrate }}" class="form-control" name="rowdplanfixrate{{ $dbrow->Sno }}" id="rowdplanfixrate{{ $dbrow->Sno }}" readonly>
                                                                                        <input type="hidden" value="{{ $dbrow->brev_code }}" class="form-control" name="rowsrev_code{{ $dbrow->Sno }}" id="rowsrev_code{{ $dbrow->Sno }}" readonly>
                                                                                        <input type="hidden" value="{{ $dbrow->btaxstru }}" class="form-control" name="rowstax_stru{{ $dbrow->Sno }}" id="rowstax_stru{{ $dbrow->Sno }}" readonly>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="offset-10">
                                                                            <input type="text" value="{{ $dbrow->bnetplanamt }}" class="form-control" name="totalnetamtplan{{ $dbrow->Sno }}" id="totalnetamtplan{{ $dbrow->Sno }}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div id="okbtnlabel{{ $dbrow->Sno }}" class="text-center">
                                                                        <button id="okbtnplan{{ $dbrow->Sno }}" name="okbtnplan{{ $dbrow->Sno }}" type="button" class="btn okbtncls btn-success btn-sm"><i class="fa-regular fa-circle-check"></i> OK</button>
                                                                        <button id="closebtnplan{{ $dbrow->Sno }}" name="closebtnplan{{ $dbrow->Sno }}" type="button" class="btn closebtncls btn-danger btn-sm"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                                                    </div>
                                                                    <div id="resizeHandle{{ $dbrow->Sno }}" class="resizeHandle"></div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="button-container custom-range">
                                        <button type="button" name="add_room" id="add_room"
                                            class="btn radiusbtn mb-1 btn-outline-success">Add Room <i
                                                class="fa-solid fa-building"></i></button>
                                    </div>
                                </div>

                                <div class="form-group mt-4 form-check">
                                    <input type="checkbox" onchange="HandleGuestList('guestlist')" class="form-check-input"
                                        name="guestlist" id="guestlist">
                                    <label class="form-check-label" for="guestlist">Guest List</label>
                                </div>

                                <div id="cloneit">

                                    <div class="astrogeeksagar">
                                        <div style="display: flex; position: relative; align-items: center;">
                                            <h4>Guest Information</h4>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="col-form-label" for="reservationtype">Guest Name</label>
                                            <select class="form-control" name="greetings" id="greetings">
                                                <option value="" {{ empty($data[0]->con_prefix) ? 'selected' : '' }}>Select</option>
                                                <option value="Mr." {{ $data[0]->con_prefix == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                <option value="Ms." {{ $data[0]->con_prefix == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                                <option value="Mam" {{ $data[0]->con_prefix == 'Mam' ? 'selected' : '' }}>Mam</option>
                                                <option value="Dr." {{ $data[0]->con_prefix == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                                <option value="Prof." {{ $data[0]->con_prefix == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                                <option value="Mrs." {{ $data[0]->con_prefix == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                                <option value="Miss" {{ $data[0]->con_prefix == 'Miss' ? 'selected' : '' }}>Miss</option>
                                                <option value="Sir" {{ $data[0]->con_prefix == 'Sir' ? 'selected' : '' }}>Sir</option>
                                                <option value="Madam" {{ $data[0]->con_prefix == 'Madam' ? 'selected' : '' }}>Madam</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="reservationtype">&nbsp;
                                                &NonBreakingSpace;</label>
                                            <div style="display: flex; align-items: normal;">
                                                <input type="text" name="name" placeholder="Full Name" value="{{ $data[0]->clientname }}" maxlength="25"
                                                    id="name" class="form-control" required>
                                                <i class="fa-regular fa-credit-card wcard" data-toggle="modal"
                                                    data-target="#formswipecard" style="margin-left: 5px;"></i>
                                                <i data-toggle="modal" data-target="#formguestdt"
                                                    class="fas fa-user-plus userplus"></i>
                                            </div>

                                            <div class="modal fade" id="formswipecard">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Guest History</h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal"><span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div>
                                                                <p class="text-center alert-link h5">Please Swipe Your Card
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div style="padding: .5rem;" class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="button" data-dismiss="modal"
                                                                class="btn btn-sm btn-primary">Save
                                                                changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="formguestdt">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Guest</h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal"><span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mt-2">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="profileimagepreview"></label>
                                                                        <label for="profileimagepreview">
                                                                            <img style="height: 180px;width: 180px;" class="preview prevprofile" id="profileimagepreview"
                                                                                @if (empty($data[0]->pic_path)) src="admin/images/preview.gif" @else src="storage/walkin/reservationprofilepic/{{ $data[0]->pic_path }}" @endif
                                                                                alt="{{ $data[0]->clientname }}" onclick="openFileInputpf('profileimage');" />
                                                                            <div style="text-align: center;">
                                                                            </div>
                                                                        </label>
                                                                        <input type="hidden" value="{{ $data[0]->pic_path }}" name="profileimagehidden"
                                                                            id="profileimagehidden">
                                                                        <input type="file" name="profileimage" class="profileimage none" id="profileimage"
                                                                            onchange="readURLp(this, 'profileimagepreview');" />
                                                                    </div>
                                                                </div>
                                                                <canvas id="capturedImageCanvas"
                                                                    style="display: none;"></canvas>

                                                                {{-- <div id="webcamContainer" class="video-container">
                                                                <video autoplay="true" id="videoElement"
                                                                    class="embed-responsive embed-responsive-4by3"></video>
                                                                <button type="button" id="closeBtn" class="btn"><i
                                                                        class="fa-solid fa-xmark"></i></button>
                                                                <button type="button" id="captureBtn" class="btn">
                                                                    <img class="img-fluid captureimg"
                                                                        src="admin/icons/flaticon/camera.svg"
                                                                        alt="camera"></button>
                                                            </div> --}}


                                                                <div class="col-md-9">
                                                                    <div class="row">
                                                                        <div class="">
                                                                            <div class="form-group">
                                                                                <label for="reservationtype">Guest
                                                                                    Name</label>
                                                                                <div class="d-flex">
                                                                                    <select style="width: auto;" class="form-control" name="greetingsguest"
                                                                                        id="greetingsguest">
                                                                                        @if (empty($data[0]->con_prefix))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="Mr." @if ($data[0]->con_prefix == 'Mr.') selected @endif>Mr.</option>
                                                                                            <option value="Ms." @if ($data[0]->con_prefix == 'Ms.') selected @endif>Ms.</option>
                                                                                            <option value="Mam" @if ($data[0]->con_prefix == 'Mam') selected @endif>Mam</option>
                                                                                            <option value="Dr." @if ($data[0]->con_prefix == 'Dr.') selected @endif>Dr.</option>
                                                                                            <option value="Prof." @if ($data[0]->con_prefix == 'Prof.') selected @endif>Prof.
                                                                                            </option>
                                                                                            <option value="Mrs." @if ($data[0]->con_prefix == 'Mrs.') selected @endif>Mrs.</option>
                                                                                            <option value="Miss" @if ($data[0]->con_prefix == 'Miss') selected @endif>Miss</option>
                                                                                            <option value="Sir" @if ($data[0]->con_prefix == 'Sir') selected @endif>Sir</option>
                                                                                            <option value="Madam" @if ($data[0]->con_prefix == 'Madam') selected @endif>Madam
                                                                                            </option>
                                                                                        @endif
                                                                                    </select>

                                                                                    <input style="width: auto;" type="text" name="guestname" placeholder="Full Name"
                                                                                        maxlength="25" value="{{ $data[0]->clientname }}" id="guestname" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="">
                                                                            <div class="form-group">
                                                                                <label for="guestmobile">Mobile</label>
                                                                                <input type="tel" minlength="10" maxlength="10" id="mobile"
                                                                                    class="form-control" id="guestmobile" value="{{ $data[0]->mobile_no }}" name="guestmobile"
                                                                                    placeholder="Mobile">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="genderguest">Gender</label>
                                                                            <select name="genderguest" id="genderguest" class="form-control">
                                                                                @if (empty($data->gender))
                                                                                    <option value="" selected>Select</option>
                                                                                @else
                                                                                    <option value="">Select</option>
                                                                                @endif
                                                                                <option value="Male" @if ($data[0]->gender == 'Male') selected @endif>Male</option>
                                                                                <option value="Female" @if ($data[0]->gender == 'Female') selected @endif>Female</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="guestemail">Email</label>
                                                                                <input value="{{ $data[0]->email_id }}" type="email" class="form-control" id="guestemail"
                                                                                    name="guestemail" placeholder="Email">
                                                                                <small class="form-text text-muted">Use
                                                                                    comma to
                                                                                    add multiple Email IDs</small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="arrfrom">Arrival From</label>
                                                                                <select name="arrfrom" id="arrfrom" class="form-control">
                                                                                    @if (empty($data[0]->ArrFrom))
                                                                                        <option value="" selected>Select</option>
                                                                                    @else
                                                                                        <option value="">Select</option>
                                                                                    @endif
                                                                                    @foreach ($citydata as $list)
                                                                                        <option value="{{ $list->city_code }}" @if ($data[0]->ArrFrom == $list->city_code) selected @endif>
                                                                                            {{ $list->cityname }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="destination">Destination</label>
                                                                                <select name="destination" id="destination" class="form-control">
                                                                                    @if (empty($data[0]->Destination))
                                                                                        <option value="" selected>Select</option>
                                                                                    @else
                                                                                        <option value="">Select</option>
                                                                                    @endif
                                                                                    @foreach ($citydata as $list)
                                                                                        <option value="{{ $list->city_code }}" @if ($data[0]->Destination == $list->city_code) selected @endif>
                                                                                            {{ $list->cityname }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <label for="cityguest">City</label>
                                                                    <select class="form-control"
                                                                        onchange="UpdateCitydata('cityguest', 'stateguest', 'countryguest', 'zipguest', 'nationalityother')"
                                                                        name="cityguest" id="cityguest">
                                                                        @if (empty($data->citycode))
                                                                            <option value="" selected>Select City</option>
                                                                        @else
                                                                            <option value="">Select City</option>
                                                                        @endif
                                                                        @foreach ($citydata as $list)
                                                                            <option value="{{ $list->city_code }}" @if ($data[0]->city == $list->city_code) selected @endif>
                                                                                {{ $list->cityname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="stateguest">State</label>
                                                                        <select class="form-control" name="stateguest" id="stateguest">
                                                                            @if (empty($data[0]->state_code))
                                                                                <option value="">Select State</option>
                                                                            @else
                                                                                <option value="">Select State</option>
                                                                                <option value="{{ $data[0]->state_code }}" selected>{{ $data[0]->nameofstate }}</option>
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="countryguest">Country</label>
                                                                        <select class="form-control" name="countryguest" id="countryguest">
                                                                            @if (empty($data[0]->country_code))
                                                                                <option value="" selected>Select Country</option>
                                                                            @else
                                                                                <option value="">Select Country</option>
                                                                                <option value="{{ $data[0]->country_code }}" selected>{{ $data[0]->nameofcountry }}</option>
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="zipguest">Zip</label>
                                                                        <input type="text" value="{{ $data[0]->cityzipcode }}" class="form-control" id="zipguest" name="zipguest"
                                                                            placeholder="Zip Code">
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div id="accordion-one" class="accordion">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <h5 class="mb-0" data-toggle="collapse"
                                                                            data-target="#collapseOne" aria-expanded="true"
                                                                            aria-controls="collapseOne"><i class="fa"
                                                                                aria-hidden="true"></i>
                                                                            Other Details
                                                                        </h5>
                                                                    </div>

                                                                    <div id="collapseOne" class="collapse show"
                                                                        data-parent="#accordion-one">

                                                                        <div class="row mt-2">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="identityInfo">Identity
                                                                                        Information</label>
                                                                                    <label for="identityimagepreview">
                                                                                        <img style="height: 180px;width: 180px;" class="preview" id="identityimagepreview"
                                                                                            @if (empty($data[0]->idpic_path)) src="admin/images/preview.gif" @else src="storage/walkin/reservationidentitypic/{{ $data[0]->idpic_path }}" @endif
                                                                                            alt="your image" onclick="openidentity('identityimage');" />
                                                                                    </label>
                                                                                    <div style="text-align: center;">
                                                                                    </div>
                                                                                    <input type="hidden" value="{{ $data[0]->idpic_path }}" name="identityimagehidden"
                                                                                        id="identityimagehidden">
                                                                                    <input type="file" name="identityimage" id="identityimage" class="identityimage none"
                                                                                        onchange="readURLp(this, 'identityimagepreview');" />
                                                                                </div>
                                                                            </div>

                                                                            <canvas id="capturedImageCanvas2"
                                                                                style="display: none;"></canvas>

                                                                            {{-- <div id="webcamContainer2"
                                                                            class="video-container">
                                                                            <video autoplay="true" id="videoElement2"
                                                                                class="embed-responsive embed-responsive-4by3"></video>
                                                                            <button type="button" id="closeBtn2"
                                                                                class="btn"><i
                                                                                    class="fa-solid fa-xmark"></i></button>
                                                                            <button type="button" id="captureBtn2"
                                                                                class="btn">
                                                                                <img class="img-fluid captureimg"
                                                                                    src="admin/icons/flaticon/camera.svg"
                                                                                    alt="camera">
                                                                            </button>
                                                                        </div> --}}

                                                                            <div class="col-md-9">
                                                                                <div class="row">
                                                                                    <div class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="idType">ID
                                                                                                Type</label>
                                                                                            <select
                                                                                                onchange="validateAadhar2('idType', 'idNumber', 'idNumberError');DisplayIssueFields2('idType','issuefielda', 'issuefieldb', 'issuefieldc')"
                                                                                                name="idType" id="idType" class="form-control idTypeSelect">
                                                                                                @if (empty($data[0]->id_proof))
                                                                                                    <option value="" selected>Select</option>
                                                                                                @else
                                                                                                    <option value="">Select</option>
                                                                                                @endif
                                                                                                <option value="Aadhar Card" @if ($data[0]->id_proof == 'Aadhar Card') selected @endif>Aadhar Card</option>
                                                                                                <option value="Driving Licence" @if ($data[0]->id_proof == 'Driving Licence') selected @endif>Driving Licence</option>
                                                                                                <option value="Passport" @if ($data[0]->id_proof == 'Passport') selected @endif>Passport</option>
                                                                                                <option value="National Identity Card" @if ($data[0]->id_proof == 'National Identity Card') selected @endif>National Identity Card</option>
                                                                                                <option value="Voter Id" @if ($data[0]->id_proof == 'Voter Id') selected @endif>Voter Id</option>
                                                                                                <option value="Green Card" @if ($data[0]->id_proof == 'Green Card') selected @endif>Green Card</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="idNumber">ID Number</label>
                                                                                            <input type="text" oninput="this.value = this.value.toUpperCase()"
                                                                                                value="{{ $data[0]->idproof_no }}" class="form-control idNumberInput" id="idNumber"
                                                                                                name="idNumber" placeholder="ID Number">
                                                                                            <span class="idNumberError" id="idNumberError"
                                                                                                style="display:none;color: red; position: fixed;">Aadhar
                                                                                                number must be 12 digits and
                                                                                                contain only numbers</span>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display:{{ $data[0]->id_proof == 'Passport' ? 'block' : 'none' }};" id="issuefielda"
                                                                                        class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="issuingcity">Issuing City</label>
                                                                                            <select id="issuingcity" name="issuingcity" class="form-control">
                                                                                                @if (empty($data[0]->issuingcitycode))
                                                                                                    <option value="" selected>Select City</option>
                                                                                                @endif
                                                                                                @foreach ($citydata as $list)
                                                                                                    <option value="">Select City</option>
                                                                                                    <option value="{{ $list->city_code }}" @if ($data[0]->issuingcitycode == $list->city_code) selected @endif>
                                                                                                        {{ $list->cityname }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display:{{ $data[0]->id_proof == 'Passport' ? 'block' : 'none' }};" id="issuefieldb"
                                                                                        class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="issuingcountry">Issuing Country</label>
                                                                                            <select id="issuingcountry" class="form-control" name="issuingcountry">
                                                                                                @if (empty($data[0]->issuingcountrycode))
                                                                                                    <option value="" selected>Select Country</option>
                                                                                                @else
                                                                                                    <option value="{{ $data[0]->issuingcountrycode }}" selected>{{ $data[0]->issuingcountryname }}
                                                                                                    </option>
                                                                                                @endif
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display:{{ $data[0]->id_proof == 'Passport' ? 'block' : 'none' }};" id="issuefieldc"
                                                                                        class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="expiryDate">Expiry
                                                                                                Date</label>
                                                                                            <input value="{{ $data[0]->expiryDate }}" onchange="PastDtNA(this)" type="date"
                                                                                                class="form-control" name="expiryDate" id="expiryDate">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="paymentMethod">Payment
                                                                                        Method</label>
                                                                                    <select onchange="DisplayBillingField2('paymentMethod', 'billingfield')"
                                                                                        class="paymentmethodselect form-control" name="paymentMethod" id="paymentMethod"
                                                                                        class="form-control">
                                                                                        @if (empty($data[0]->paymentMethod))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="">Select</option>
                                                                                        @endif
                                                                                        <option value="Cash" @if ($data[0]->paymentMethod == 'Cash') selected @endif>Cash</option>
                                                                                        <option value="Bill To Company" @if ($data[0]->paymentMethod == 'Bill To Company') selected @endif>Bill
                                                                                            To Company</option>
                                                                                        <option value="UPI" @if ($data[0]->paymentMethod == 'UPI') selected @endif>UPI</option>
                                                                                        <option value="Debit Card" @if ($data[0]->paymentMethod == 'Debit Card') selected @endif>Debit
                                                                                            Card</option>
                                                                                        <option value="Credit Card" @if ($data[0]->paymentMethod == 'Credit Card') selected @endif>Credit
                                                                                            Card</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div id="billingfield" style="display:{{ $data[0]->paymentMethod == ' Bill To Company' ? 'block' : 'none' }} ;"
                                                                                class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="billingAccount">Direct
                                                                                        Billing</label>
                                                                                    <select name="billingAccount" id="billingAccount" class="form-control">
                                                                                        @if (empty($data[0]->billingAccount))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="">Select</option>
                                                                                        @endif
                                                                                        @foreach ($company as $item)
                                                                                            <option value="{{ $item->sub_code }}" @if ($data[0]->billingAccount == $item->sub_code) selected @endif> {{ $item->name }}
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="birthDate">Birth
                                                                                        Date</label>
                                                                                    <input value="{{ $data[0]->dob }}" name="birthDate" onchange="FutureDtNA(this)" type="date"
                                                                                        class="form-control" id="birthDate">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="purpofvisit">Purpose Of
                                                                                        Visit</label>
                                                                                    <select class="form-control" id="purpofvisit" name="purpofvisit">
                                                                                        @if (empty($data[0]->purpofvisit))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="">Select</option>
                                                                                        @endif
                                                                                        <option value="Official" @if ($data[0]->purpofvisit == 'Official') selected @endif>Official
                                                                                        </option>
                                                                                        <option value="Personal" @if ($data[0]->purpofvisit == 'Personal') selected @endif>Personal
                                                                                        </option>
                                                                                        <option value="Business" @if ($data[0]->purpofvisit == 'Business') selected @endif>Business
                                                                                        </option>
                                                                                        <option value="Tourist" @if ($data[0]->purpofvisit == 'Tourist') selected @endif>Tourist
                                                                                        </option>
                                                                                        <option value="Other" @if ($data[0]->purpofvisit == 'Other') selected @endif>Other</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="nationalityother">Nationality</label>
                                                                                    <select class="form-control" id="nationalityother" name="nationalityother">
                                                                                        @if (empty($data[0]->country_code))
                                                                                            <option value="" selected>Select Nationality</option>
                                                                                        @endif
                                                                                        <option value="">Select</option>
                                                                                        <option value="{{ $data[0]->nameofnationality }}" selected>{{ $data[0]->nameofnationality }}</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="vipStatus">Guest
                                                                                        Status</label>
                                                                                    <select name="vipStatus" id="vipStatus" class="form-control">
                                                                                        @if (empty($data[0]->guest_status))
                                                                                            <option value="" selected>Select Status</option>
                                                                                        @else
                                                                                            <option value="">Select Status</option>
                                                                                        @endif
                                                                                        @foreach ($gueststatus as $list)
                                                                                            <option value="{{ $list->gcode }}" @if ($data[0]->guest_status == $list->gcode) selected @endif>
                                                                                                {{ $list->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="weddingAnniversary">Wedding
                                                                                        Anniversary</label>
                                                                                    <input value="{{ $data[0]->anniversary }}" name="weddingAnniversary" onchange="FutureDtNA(this)"
                                                                                        type="date" class="form-control" id="weddingAnniversary">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="marital_status">Marital
                                                                                        Status</label>
                                                                                    <select name="marital_status" id="marital_status" class="form-control">
                                                                                        @if (empty($data[0]->marital_status))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="">Select</option>
                                                                                        @endif
                                                                                        <option value="Single" @if ($data[0]->marital_status == 'Single') selected @endif>Single
                                                                                        </option>
                                                                                        <option value="Married" @if ($data[0]->marital_status == 'Married') selected @endif>Married
                                                                                        </option>
                                                                                        <option value="Divorced" @if ($data[0]->marital_status == 'Divorced') selected @endif>Divorced
                                                                                        </option>
                                                                                        <option value="Widowed" @if ($data[0]->marital_status == 'Widowed') selected @endif>Widowed
                                                                                        </option>
                                                                                        <option value="Separated" @if ($data[0]->marital_status == 'Separated') selected @endif>Separated</option>
                                                                                        <option value="Other" @if ($data[0]->marital_status == 'Other') selected @endif>Other
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label" for="rodisc">Room Discount %</label>
                                                                                    <input value="{{ $data[0]->RDisc }}" type="text" step="0.01" min="0.00" max="99.99" placeholder="0.00"
                                                                                        name="rodisc" id="rodisc" class="form-control percent_value"
                                                                                        oninput="validatePercentage2('rodisc')">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label" for="rsdisc">Rs Disc %</label>
                                                                                    <input value="{{ $data[0]->RSDisc }}" type="text" step="0.01" min="0.00" max="99.99" placeholder="0.00"
                                                                                        name="rsdisc" id="rsdisc" class="form-control percent_value"
                                                                                        oninput="validatePercentagers2('rsdisc')">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label" for="travelmode">Travel Mode</label>
                                                                                    <select onchange="DisplayVehicleNum2('travelmode', 'vehiclediv')" name="travelmode"
                                                                                        class="form-control" id="travelmode">
                                                                                        @if (empty($data[0]->TravelMode))
                                                                                            <option value="" selected>Select</option>
                                                                                        @else
                                                                                            <option value="">Select</option>
                                                                                        @endif
                                                                                        <option value="By Road" @if ($data[0]->TravelMode == 'By Road') selected @endif>By Road
                                                                                        </option>
                                                                                        <option value="By Air" @if ($data[0]->TravelMode == 'By Air') selected @endif>By Air
                                                                                        </option>
                                                                                        <option value="By Car" @if ($data[0]->TravelMode == 'By Car') selected @endif>By Car
                                                                                        </option>
                                                                                        <option value="By Bus" @if ($data[0]->TravelMode == 'By Bus') selected @endif>By Bus
                                                                                        </option>
                                                                                        <option value="By Train" @if ($data[0]->TravelMode == 'By Train') selected @endif>By Train
                                                                                        </option>
                                                                                        <option value="By Ship" @if ($data[0]->TravelMode == 'By Ship') selected @endif>By Ship
                                                                                        </option>
                                                                                    </select>

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div id="vehiclediv" style="display: {{ $data[0]->TravelMode == 'By Car' ? 'block' : 'none' }};"
                                                                                class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label for="vehiclenum" class="col-form-label">Vehicle Number</label>
                                                                                    <input value="{{ $data[0]->vehiclenum }}" type="text" oninput="this.value = this.value.toUpperCase()"
                                                                                        name="vehiclenum" id="vehiclenum" class="form-control" placeholder="Enter Vehicle Number">
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="button" data-dismiss="modal"
                                                                class="btn btn-primary">Save
                                                                changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">
                                            <label class="col-form-label" for="mobile">Mobile</label>
                                            <input type="tel"
                                                name="mobile" placeholder="Mobile" value="{{ $data[0]->mobile_no }}" minlength="10" maxlength="10" id="mobile"
                                                class="form-control" {{ $enviro_formdata->grcmandatory == 'Y' ? 'required' : '' }}>
                                            <div style="display: none;" id="error-phone" class="error-phone text-danger">
                                                Invalid Number</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="col-form-label" for="email">Email</label>
                                            <input type="email" name="email" value="{{ $data[0]->email_id }}" placeholder="Email" maxlength="100" id="email"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <table class="table mt-2 walkin-table">
                                        <thead>
                                            <tr>
                                                <th><label for="city">City:</label></th>
                                                <th><label for="state">State:</label></th>
                                                <th><label for="country">Country:</label></th>
                                                <th><label for="nationality">Nationality:</label></th>
                                                <th><label for="zipcode">Zip Code:</label></th>
                                                <th><label for="address1">Address 1:</label></th>
                                                <th><label for="address2">Address 2:</label></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-control" name="cityname" id="cityname" required>
                                                        <option value="" disabled {{ empty($data[0]->city) ? 'selected' : '' }}>Select City</option>
                                                        @foreach ($citydata as $list)
                                                            <option value="{{ $list->city_code }}" {{ $data[0]->city == $list->city_code ? 'selected' : '' }}>
                                                                {{ $list->cityname }}
                                                            </option>
                                                        @endforeach
                                                    </select>                                                    
                                                <td>
                                                    <select class="form-control" name="state" id="state">
                                                        @if (empty($data[0]->state_code))
                                                            <option value="">Select State</option>
                                                        @else
                                                            <option value="">Select State</option>
                                                            <option value="{{ $data[0]->state_code }}" selected>{{ $data[0]->nameofstate }}</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="country" id="country">
                                                        @if (empty($data[0]->country_code))
                                                            <option value="" selected>Select Country</option>
                                                        @else
                                                            <option value="">Select Country</option>
                                                            <option value="{{ $data[0]->country_code }}" selected>{{ $data[0]->nameofcountry }}</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="nationality" id="nationality">
                                                        @if (empty($data[0]->country_code))
                                                            <option value="" selected>Select Nationality</option>
                                                        @else
                                                            <option value="">Select</option>
                                                            <option value="{{ $data[0]->nameofnationality }}" selected>{{ $data[0]->nameofnationality }}</option>
                                                        @endif
                                                    </select>
                                                </td>

                                                <td><input type="text" value="{{ $data[0]->cityzipcode }}" class="form-control fiveem" name="zipcode"
                                                        id="zipcode">
                                                </td>
                                                <td>
                                                    <input type="text" value="{{ $data[0]->add1 }}" placeholder="Enter Address 1" class="form-control" name="address1" id="address1">
                                                </td>
                                                <td>
                                                    <input type="text" value="{{ $data[0]->add2 }}" placeholder="Enter Address 2" class="form-control" name="address2" id="address2">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                                <div class="astrogeeksagar">
                                    <h4 style="width: 160px;">Other Information</h4>
                                </div>

                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" name="emailcheckout" id="emailcheckout">
                                    <label class="form-check-label" for="emailcheckout">Send Email at
                                        Checkout.</label>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" name="suppressrate" id="suppressrate">
                                    <label class="form-check-label" for="suppressrate">Suppress Rate on
                                        Registration
                                        Card.</label>
                                </div>

                                <div class="text-center mt-4 mb-4 ml-auto">
                                    <button type="button" class="btn ti-back-left btn-danger" onclick="window.location.href='{{ url('roomstatus') }}'">Cancel </button>
                                    <button type="submit" name="walkinsubmit" id="walkinsubmit"
                                        class="btn btn-primary">Check In <i class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        $(document).ready(function() {
            $('#myloader').removeClass('none');
            setTimeout(() => {
                $('#myloader').addClass('none');
            }, 500);

            $(document).on('click', '.planviewbtn', function() {
                let index = $(this).data('sn');
                $(`#table-planmast${index}`).toggle();
            });

            let timer;
            $(document).on('input', '.planrow', function() {
                clearTimeout(timer);
                let element = $(this);

                timer = setTimeout(() => {
                    let currownum = element.attr('id');
                    const regex = /\d+/;
                    const match = currownum.match(regex);
                    const number = parseInt(match[0], 10);

                    let planamount = element.val();
                    let plansumrate = $(`#plansumrate${number}`).val();
                    let sumpercent = $(`#rowdplan_per${number}`).val();

                    let newchargevalue = (planamount * sumpercent) / 100;
                    let newnetroomrate = planamount - newchargevalue;
                    let beforetax = 1 + (plansumrate / 100);
                    let newroomrate = newnetroomrate / beforetax;

                    $(`#netroomrate${number}`).val(newnetroomrate.toFixed(2));
                    $(`#rowdamount${number}`).val(newchargevalue.toFixed(2));
                    $(`#roomrate${number}`).val(newroomrate.toFixed(2));

                    $(`#netroomrate${number}`).attr('value', newnetroomrate.toFixed(2));
                    $(`#rowdamount${number}`).attr('value', newchargevalue.toFixed(2));
                    $(`#roomrate${number}`).attr('value', newroomrate.toFixed(2));

                    let sum = 0.00;
                    $('.rowdamount').each(function() {
                        sum += parseFloat($(this).val()) || 0;
                    });

                    let roomratenet = sum + newnetroomrate;
                    $(`#totalnetamtplan${number}`).val(roomratenet.toFixed(2));
                    $(`#totalnetamtplan${number}`).attr('value', roomratenet.toFixed(2));
                    $(`#plankaamount${number}`).val(planamount).trigger('change');
                    $(`#plankaamount${number}`).attr('value', planamount);
                }, 500);
            });

            $(document).on('input', '.rowdamount', function() {
                clearTimeout(timer);
                let element = $(this);

                timer = setTimeout(() => {
                    let currownum = element.attr('id');
                    const regex = /\d+/;
                    const match = currownum.match(regex);
                    const number = parseInt(match[0], 10);

                    let rowdamount = element.val();
                    let planamount = $(`#plankaamount${number}`).val();
                    let plansumrate = $(`#plansumrate${number}`).val();

                    let newsumpercentval = (rowdamount / planamount) * 100;
                    let newnetroomrate = planamount - rowdamount;
                    let beforetax = 1 + (plansumrate / 100);
                    let newroomrate = newnetroomrate / beforetax;

                    $(`#netroomrate${number}`).val(newnetroomrate.toFixed(2));
                    $(`#rowdplan_per${number}`).val(newsumpercentval.toFixed(2));
                    $(`#roomrate${number}`).val(newroomrate.toFixed(2));

                    $(`#netroomrate${number}`).attr('value', newnetroomrate.toFixed(2));
                    $(`#rowdplan_per${number}`).attr('value', newsumpercentval.toFixed(2));
                    $(`#roomrate${number}`).attr('value', newroomrate.toFixed(2));

                    let sum = 0.00;
                    $('.rowdamount').each(function() {
                        sum += parseFloat($(this).val()) || 0;
                    });

                    let roomratenet = sum + newnetroomrate;
                    $(`#totalnetamtplan${number}`).val(roomratenet.toFixed(2));
                    $(`#totalnetamtplan${number}`).attr('value', roomratenet.toFixed(2));
                }, 500);
            });

            $(document).on('keypress', '.rowdamount, .planrow', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let element = $(this);
                    let num = extractnum(element.attr('id'));
                    $(`#okbtnplan${num}`).trigger('click');
                }
            });


            $(document).on('focus', '.taxincplanroomrate', function() {
                $(this).data('curval', $(this).val());
            });

            $(document).on('change', '.taxincplanroomrate', function() {
                if ($(this).val() != $(this).data('curval')) {
                    $(this).val($(this).data('curval'));
                }
            });

            $(document).on('focus', '.taxchk', function() {
                $(this).data('curval', $(this).val());
            });

            $(document).on('change', '.taxchk', function() {
                let index = $(this).closest('tr').index() + 1;
                if ($(`#planedit${index}`).val() == 'Y') {
                    if ($(this).val() != $(this).data('curval')) {
                        $(this).val($(this).data('curval'));
                    }
                }
            });

            $(document).on('click', '.okbtncls', function() {
                let element = $(this);
                let num = extractnum(element.attr('id'));
                console.log(num);
                let netroomrate = $(`#netroomrate${num}`).val();
                let taxincplanroomrate = $(`#taxincplanroomrate${num}`).val() == 'Y' ? 'Y' : 'N';
                $(`#rate${num}`).val(netroomrate);
                $(`#rate${num}`).prop('readonly', true);
                $(`#tax_inc${num}`).val(taxincplanroomrate);
                let taxparent = $(`#tax_inc${num}`).parent();
                $(`#planedit${num}`).val('Y');
                element.parents('div.table-planmast').css('display', 'none');
                element.parents('div.hidedisp').removeClass('hidedisp');
            });

            $(document).on('click', '.closebtncls', function() {
                let element = $(this);
                let num = extractnum(element.attr('id'));
                let taxparent = $(`#tax_inc${num}`).parent();
                $(`#tax_inc${num}`).remove();
                let newtx = `<select class="form-control taxchk sl" name="tax_inc${num}" id="tax_inc${num}">
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                            </select>`;
                taxparent.append(newtx);
                $(`#rate${num}`).prop('readonly', false);
                $(`#planedit${num}`).val('N');
                element.parents('div.table-planmast').css('display', 'none');
                element.parents('div.hidedisp').removeClass('hidedisp');
            });

            var csrftoken = '{{ csrf_token() }}';
            let outenviroxhr = new XMLHttpRequest();
            outenviroxhr.open('GET', '/enviroform', true);
            outenviroxhr.onreadystatechange = function() {
                if (outenviroxhr.readyState === 4 && outenviroxhr.status === 200) {
                    let envirodataout = JSON.parse(outenviroxhr.responseText);
                    let plancalc = envirodataout.plancalc;
                    $(document).on('change', '.planmastclass', function() {
                        let parenttag = $(this).parents('tr.data-row');
                        let plancode = $(this).val();
                        let rowindex = $(this).closest('tr').index() + 1;
                        let taxparent = $(`#tax_inc${rowindex}`).parent();
                        $(`#tax_inc${rowindex}`).remove();
                        let newtx = `<select class="form-control taxchk sl" name="tax_inc${rowindex}" id="tax_inc${rowindex}">
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                            </select>`;
                        taxparent.append(newtx);
                        $(`#rate${rowindex}`).prop('readonly', false);
                        $(`#planedit${rowindex}`).val('N');
                        if (plancalc == 'Y' && plancode != '') {
                            const plandata = {
                                'plancode': plancode
                            };

                            const options = {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify(plandata)
                            };

                            fetch('/fetchplancacl', options)
                                .then(response => response.json())
                                .then(data => {
                                    let planrows = data.plan1;
                                    let plan_mast = data.plan_mast;
                                    let total_rate = plan_mast.total_rate;
                                    let existingPlanDetails = parenttag.find('.table-planmast');
                                    if (existingPlanDetails.length > 0) {
                                        existingPlanDetails.remove();
                                    }
                                    let wholedata = `<div class="hidedisp">
                                                        <div id="table-planmast${rowindex}" class="table-responsive table-planmast">
                                                        <h3 class="text-center adc">Plan Details</h3>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label id="plannamelabel" class="col-form-label" for="planname">Plan</label>
                                                                <input type="text" value="${plan_mast.name}" class="form-control" name="planname${rowindex}" id="planname${rowindex}" readonly>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label id="plankaamountlabel" class="col-form-label" for="plankaamount">Plan Amount</label>
                                                                <input autocomplete="off" type="text" value=${plan_mast.total} class="form-control planrow" name="plankaamount${rowindex}" id="plankaamount${rowindex}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                 <label id="taxincplanroomratelabel" class="col-form-label" for="taxincplanroomrate">Inc. In Room Rate</label>
                                                                <select class="form-control taxincplanroomrate" name="taxincplanroomrate${rowindex}" id="taxincplanroomrate${rowindex}">
                                                                    <option value="Y" ${plan_mast.rrinc_tax == 'Y' ? 'selected' : ''}>Yes</option>
                                                                    <option value="N" ${plan_mast.rrinc_tax == 'N' ? 'selected' : ''}>No</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label id="roomratelabel" class="col-form-label" for="roomrate">Room Rate</label>
                                                                <input type="text" value="${plan_mast.room_rate_before_tax.toFixed(2)}" class="form-control" name="roomrate${rowindex}" id="roomrate${rowindex}" readonly>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label id="netroomratelabel" class="col-form-label" for="netroomrate">Net Room Rate</label>
                                                                <input type="text" value="${plan_mast.room_rate}" class="form-control" name="netroomrate${rowindex}" id="netroomrate${rowindex}" readonly>
                                                                <input type="hidden" value="${plan_mast.total_rate ?? 0}" class="form-control" name="plansumrate${rowindex}" id="plansumrate${rowindex}">
                                                                <input type="hidden" value="${plan_mast.room_tax_stru}" class="form-control" name="taxstruplan${rowindex}" id="taxstruplan${rowindex}">
                                                                <input type="hidden" value="${plan_mast.room_per}" class="form-control" name="planpercent${rowindex}" id="planpercent${rowindex}">
                                                                <input type="hidden" value="${plan_mast.pcode}" class="form-control" name="plancodeplan${rowindex}" id="plancodeplan${rowindex}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 d-flex justify-content-around">
                                                            <table id="planmasttable${rowindex}" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sn</th>
                                                                        <th>Fixed Charge</th>
                                                                        <th>Amount</th>
                                                                        <th>Percentage</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="row">
                                                            <div class="offset-10">
                                                                <input type="text" class="form-control" name="totalnetamtplan${rowindex}" id="totalnetamtplan${rowindex}" readonly>
                                                            </div>
                                                        </div>
                                                        <div id="okbtnlabel${rowindex}" class="text-center">
                                                            <button id="okbtnplan${rowindex}" name="okbtnplan${rowindex}" type="button" class="btn okbtncls btn-success btn-sm"><i class="fa-regular fa-circle-check"></i> OK</button>
                                                            <button id="closebtnplan${rowindex}" name="closebtnplan${rowindex}" type="button" class="btn closebtncls btn-danger btn-sm"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
                                                        </div>
                                                        <div id="resizeHandle${rowindex}" class="resizeHandle"></div>
                                                        </div>
                                                    </div>`;


                                    parenttag.append(wholedata);

                                    let tbody = $(`#planmasttable${rowindex} tbody`);
                                    let rowdata = '';
                                    let sn = 0;
                                    let roomratenet = parseFloat(plan_mast.room_rate);

                                    planrows.forEach((row, index) => {
                                        sn++;
                                        roomratenet += parseFloat(row.net_amount);
                                        rowdata += `<tr>
                                            <td>${sn}</td>
                                            <td>${row.chargename}</td>
                                            <td><input autocomplete="off" type="text" value="${row.net_amount}" class="form-control rowdamount" name="rowdamount${rowindex}" id="rowdamount${rowindex}"></td>
                                            <td><input type="text" value="${row.plan_per}" class="form-control" name="rowdplan_per${rowindex}" id="rowdplan_per${rowindex}" readonly>
                                            <input type="hidden" value="${row.fix_rate}" class="form-control" name="rowdplanfixrate${rowindex}" id="rowdplanfixrate${rowindex}" readonly>
                                            <input type="hidden" value="${row.rev_code}" class="form-control" name="rowsrev_code${rowindex}" id="rowsrev_code${rowindex}" readonly>
                                            <input type="hidden" value="${row.tax_stru}" class="form-control" name="rowstax_stru${rowindex}" id="rowstax_stru${rowindex}" readonly></td>
                                        </tr>`;
                                    });
                                    $(`#totalnetamtplan${rowindex}`).val(roomratenet.toFixed(2));
                                    tbody.append(rowdata);
                                    // makeDraggable(`table-planmast${rowindex}`);
                                    // makeResizable(`table-planmast${rowindex}`, `resizeHandle${rowindex}`);
                                })
                                .catch(error => {
                                    console.log(error);
                                })
                        }
                    });

                }
            }
            outenviroxhr.send();
        });

        $(document).ready(function() {
            $('#walkinform').on('submit', function(event) {
                event.preventDefault();
                let enviroxhr = new XMLHttpRequest();
                enviroxhr.open('GET', '/enviroform', true);
                enviroxhr.onreadystatechange = function() {
                    if (enviroxhr.readyState === 4 && enviroxhr.status === 200) {
                        let envirodata = JSON.parse(enviroxhr.responseText);
                        let grcmandatory = envirodata.grcmandatory;
                        let idType = $('#idType').val();
                        let idNumber = $('#idNumber').val();
                        if (grcmandatory == 'Y' && idType == '' && idNumber == '') {
                            $('#guestinfobutton').click();
                            setTimeout(() => {
                                $('#idType').focus();
                                $('#idType').css('border-color', 'red');
                                $('#idType').css('border-width', '2px');
                                $('#idNumber').css('border-color', '');
                            }, 1000);
                            pushNotify('error', 'Walkin Form', 'Please Fill Identity Details', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                        } else if (grcmandatory == 'Y' && idType == '') {
                            $('#guestinfobutton').click();
                            setTimeout(() => {
                                $('#idType').focus();
                                $('#idType').css('border-color', 'red');
                                $('#idType').css('border-width', '2px');
                                $('#idNumber').css('border-color', 'red');
                                $('#idNumber').css('border-width', '2px');
                            }, 1000);
                            pushNotify('error', 'Walkin Form', 'Please Select ID Type', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                        } else if (grcmandatory == 'Y' && idNumber == '') {
                            $('#guestinfobutton').click();
                            setTimeout(() => {
                                $('#idNumber').focus();
                                $('#idNumber').css('border-color', 'red');
                                $('#idNumber').css('border-width', '2px');
                                $('#idType').css('border-color', '');
                            }, 1000);
                            pushNotify('error', 'Walkin Form', 'Please Enter ID Number', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                        } else if (grcmandatory == 'Y' && idType != '' && idNumber != '') {
                            formsubmit('#walkinform', '#walkinsubmit', 'Walkin', 'roomstatus', 'walkinsubmit');
                        } else if (grcmandatory == 'N') {
                            formsubmit('#walkinform', '#walkinsubmit', 'Walkin', 'roomstatus', 'walkinsubmit');
                        }
                    }
                };
                enviroxhr.send();
            });
        });
        // Delegate event handling to a static parent element
        $(document).on('change', '[id^="cityname"]', function() {
            var citycode = $(this).val();
            var cityId = $(this).attr('id');
            var stateId = cityId.replace('cityname', 'state');
            var countryId = cityId.replace('cityname', 'country');
            var nationalityId = cityId.replace('cityname', 'nationality');
            var zipcodeId = cityId.replace('cityname', 'zipcode');

            $.ajax({
                type: 'POST',
                url: '/sendcitycode',
                data: {
                    citycode: citycode,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#' + stateId).empty();
                    $('#' + countryId).empty();
                    $('#' + nationalityId).empty();

                    $.each(result.states, function(index, state) {
                        $('<option>').val(state.state_code).text(state.name).appendTo('#' + stateId);
                    });

                    $.each(result.countries, function(index, country) {
                        $('<option>').val(country.country_code).text(country.country_name).appendTo('#' + countryId);
                        $('<option>').val(country.nationality).text(country.nationality).appendTo('#' + nationalityId);
                    });

                    $('#' + zipcodeId).val(result.zipcode);
                }
            });
        });

        // Delegate event handling to a static parent element
        $(document).on('change', '[id^="cityguest"]', function() {
            var citycode = $(this).val();
            var cityId = $(this).attr('id');
            var stateId = cityId.replace('cityguest', 'stateguest');
            var countryId = cityId.replace('cityguest', 'countryguest');
            var nationalityId = cityId.replace('cityguest', 'nationalityother');
            var zipcodeId = cityId.replace('cityguest', 'zipguest');
            var arrivalId = cityId.replace('cityguest', 'arrfrom');
            var destinationId = cityId.replace('cityguest', 'destination');

            $.ajax({
                type: 'POST',
                url: '/sendcitycode',
                data: {
                    citycode: citycode,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#' + stateId).empty();
                    $('#' + countryId).empty();
                    $('#' + nationalityId).empty();

                    $.each(result.states, function(index, state) {
                        $('<option>').val(state.state_code).text(state.name).appendTo('#' + stateId);
                        $('#' + arrivalId).val(citycode);
                        $('#' + destinationId).val(citycode);
                    });

                    $.each(result.countries, function(index, country) {
                        $('<option>').val(country.country_code).text(country.country_name).appendTo('#' + countryId);
                        $('<option>').val(country.nationality).text(country.nationality).appendTo('#' + nationalityId);
                    });

                    $('#' + zipcodeId).val(result.zipcode);
                }
            });
        });

        // Delegate event handling to a static parent element
        $(document).on('change', '[id^="issuingcity"]', function() {
            var citycode = $(this).val();
            var issuingcityId = $(this).attr('id');
            var issuingcountryId = issuingcityId.replace('issuingcity', 'issuingcountry');

            $.ajax({
                type: 'POST',
                url: '/sendcitycode',
                data: {
                    citycode: citycode,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#' + issuingcountryId).empty();

                    $.each(result.countries, function(index, country) {
                        $('<option>').val(country.country_code).text(country.country_name).appendTo('#' + issuingcountryId);
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Event listener for hardcoded select element
            $(document).on('change', `#child1, #adult1, #planmaster1`, function() {
                var cid = $(`#child1`).val();
                var adult = $(`#adult1`).val();
                var room_cat = $(`#cat_code1`).val();
                var planmaster = $(`#planmaster1`).val();
                const data = [room_cat, planmaster, adult, cid];
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/getrate2', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var result = xhr.responseText;
                        var rate = $(`#rate1`);
                        rate.val(result);
                    }
                };
                xhr.send(`data=${JSON.stringify(data)}&_token={{ csrf_token() }}`);
            });

            $(document).on('change', `#child1, #adult1, #roommast1`, function() {
                var cid = $(`#roommast1`).val();
                var adult = $(`#adult1`).val();
                var room_category = $(`#cat_code1`).val();
                var child = $(`#child1`).val();
                var sumchildadult = parseInt(adult) + parseInt(child);
                const data = [room_category, cid, sumchildadult];
                if ($('#planedit1').val() == '' || $('#planedit1').val() == 'N') {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/getrate3', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var result = xhr.responseText;
                            var rate = $(`#rate1`);
                            rate.val(result);
                        }
                    };
                    xhr.send(`data=${JSON.stringify(data)}&_token={{ csrf_token() }}`);
                }
            });


            $(document).ready(function() {
                jQuery(window).on("load", function() {
                    $('.cat_code_class').trigger('change');
                });
            });

            $(document).on('change', '.cat_code_class', function() {
                var cid = this.value;
                let trno = $(this).closest('tr').index() + 1;
                var exrval = $(`#roommast${trno}`).val();
                var expval = $(`#planmaster${trno}`).val();
                var exptext = $(`#planmaster${trno} option:selected`).text();
                let checkindate = $(`#arrivaldate${trno}`).val();
                let checkoutdate = $(`#checkoutdate${trno}`).val();

                var xhrRooms = new XMLHttpRequest();
                xhrRooms.open('POST', '/getrooms', true);
                xhrRooms.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhrRooms.onreadystatechange = function() {
                    if (xhrRooms.readyState === 4 && xhrRooms.status === 200) {
                        var result = xhrRooms.responseText;
                        var roomSelect = document.getElementById(`roommast${trno}`);
                        roomSelect.innerHTML = result;

                        if (exrval != '') {
                            $(`#roommast${trno}`).prepend(`<option value="${exrval}" selected>${exrval}</option>`);
                        }

                        var xhrPlans = new XMLHttpRequest();
                        xhrPlans.open('POST', '/getplans', true);
                        xhrPlans.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhrPlans.onreadystatechange = function() {
                            if (xhrPlans.readyState === 4 && xhrPlans.status === 200) {
                                var resultplan = xhrPlans.responseText;
                                var planSelect = document.getElementById(`planmaster${trno}`);
                                planSelect.innerHTML = resultplan;

                                if (expval != '') {
                                    $(`#planmaster${trno}`).val(expval);
                                }
                            }
                        };
                        xhrPlans.send(`cid=${cid}&_token={{ csrf_token() }}`);
                    }
                };
                xhrRooms.send(`cid=${cid}&checkindate=${checkindate}&checkoutdate=${checkoutdate}&_token={{ csrf_token() }}`);
            });

            $(document).on('change', '.leadercl', function() {
                let curcheck = $(this);
                if (curcheck.is(':checked')) {
                    $('.leadercl').prop('checked', false);
                    $(this).prop('checked', true);
                } else {
                    console.log('not checked');
                }
                let currow = curcheck.closest('tr').siblings().length + 1;
                if (currow == 1) {
                    $('.leadercl').prop('checked', false);
                }
            });

            $("#add_room").click(function(event) {
                event.preventDefault();
                const table = document.getElementById("gridtaxstructure");
                const newRow = table.insertRow(table.rows.length);
                newRow.classList.add('data-row');

                var cell1 = newRow.insertCell(0);
                var cell2 = newRow.insertCell(1);
                var cell3 = newRow.insertCell(2);
                var cell4 = newRow.insertCell(3);
                var cell5 = newRow.insertCell(4);
                var cell6 = newRow.insertCell(5);
                var cell7 = newRow.insertCell(6);
                var cell8 = newRow.insertCell(7);
                var cell9 = newRow.insertCell(8);
                //Warning: Magic in progress. Do not disturb the wizards🧙.
                const rowNumber = table.rows.length - 1;

                document.getElementById('rooms').value = rowNumber;

                let totalrooms = parseInt($('#totalrooms').val());

                $('#totalrooms').val(totalrooms + 1);

                cell1.innerHTML = `
            <select id="cat_code${rowNumber}" name="cat_code${rowNumber}" class="form-control sl cat_code_class" required>
                    <option value="">Select</option>
                    @foreach ($roomcat as $list)
                        <option value="{{ $list->cat_code }}">{{ $list->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" class="form-control" name="planedit${rowNumber}" id="planedit${rowNumber}" readonly>`;

                cell2.innerHTML = `
                <select id="planmaster${rowNumber}" name="planmaster${rowNumber}" class="form-control planmastclass sl">
                    <option value="">Select</option>
                </select>`;

                cell3.innerHTML = `
                <select id="roommast${rowNumber}" name="roommast${rowNumber}" class="form-control room_mast sl" required>
                    <option value="">Select</option>
                </select>`;

                cell4.innerHTML = `
                <select id="adult${rowNumber}" name="adult${rowNumber}" class="form-control sl" required>
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option selected value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>`;

                cell5.innerHTML = `
                <select id="child${rowNumber}" name="child${rowNumber}" class="form-control sl" required>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>`;

                cell6.innerHTML = `
                <input type="number" name="rate${rowNumber}" id="rate${rowNumber}"
                oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                class="form-control ratechk sp" required>`;
                cell7.innerHTML = `<select class="form-control taxchk sl" name="tax_inc${rowNumber}" id="tax_inc${rowNumber}">
                <option value="">Select</option>
                <option value="Y">Yes</option>
                <option value="N">No</option>
                </select>`;
                cell8.innerHTML = `<td><input type="checkbox" class="leadercl" name="leader${rowNumber}" id="leader${rowNumber}">
                </td>`;
                cell9.innerHTML = `<img src="admin/icons/flaticon/remove.gif" alt="remove icon" class="remove-icon">
                    <img src="admin/icons/flaticon/copy.gif" alt="copy icon" class="copy-icon">`;
                $(document).on('change', `#child${rowNumber}, #adult${rowNumber}, #planmaster${rowNumber}`, function() {
                    var cid = $(`#child${rowNumber}`).val();
                    var adult = $(`#adult${rowNumber}`).val();
                    var room_cat = $(`#cat_code${rowNumber}`).val();
                    var planmaster = $(`#planmaster${rowNumber}`).val();
                    const data = [room_cat, planmaster, adult, cid];
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/getrate2', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var result = xhr.responseText;
                            var rate = $(`#rate${rowNumber}`);
                            rate.val(result);
                        }
                    };
                    xhr.send(`data=${JSON.stringify(data)}&_token={{ csrf_token() }}`);
                });

                $(document).on('change', `#child${rowNumber}, #adult${rowNumber}, #roommast${rowNumber}`, function() {
                    var cid = $(`#roommast${rowNumber}`).val();
                    var adult = $(`#adult${rowNumber}`).val();
                    var room_category = $(`#cat_code${rowNumber}`).val();
                    var child = $(`#child${rowNumber}`).val();
                    var sumchildadult = parseInt(adult) + parseInt(child);
                    const data = [room_category, cid, sumchildadult];
                    if ($(`#planedit${rowNumber}`).val() == '' || $(`#planedit${rowNumber}`).val() == 'N') {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '/getrate3', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                var result = xhr.responseText;
                                var rate = $(`#rate${rowNumber}`);
                                rate.val(result);
                            }
                        };
                        xhr.send(`data=${JSON.stringify(data)}&_token={{ csrf_token() }}`);
                    }
                });

                var xhr = new XMLHttpRequest();
                var csrfToken = '{{ csrf_token() }}';
                xhr.open('GET', '{{ route('checkeditarrival') }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = xhr.responseText;
                        var result = JSON.parse(response);

                        if (result.roomrateeditable === 'N') {
                            var elements = document.getElementsByClassName('ratechk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].readOnly = true;
                            }
                        }

                        // if (result.roominctaxeditable === 'N') {
                        //     var elements = document.getElementsByClassName('taxchk');
                        //     for (var i = 0; i < elements.length; i++) {
                        //         elements[i].readOnly = true;
                        //     }
                        // }

                        // if (result.roominctaxeditable === 'N') {
                        //     var elements = document.getElementsByClassName('taxchk');
                        //     for (var i = 0; i < elements.length; i++) {
                        //         elements[i].disabled = true;
                        //     }
                        // }

                        // Don't touch it Or you will be in trouble😭

                        // if (result.rrinctaxdefault === 'Y') {
                        //     var elements = document.getElementsByClassName('taxchk');
                        //     for (var i = 0; i < elements.length; i++) {
                        //         elements[i].value = 'Y';
                        //     }
                        // } else if (result.rrinctaxdefault === 'N') {
                        //     var elements = document.getElementsByClassName('taxchk');
                        //     for (var i = 0; i < elements.length; i++) {
                        //         elements[i].value = 'N';
                        //     }
                        // } else {
                        //     var elements = document.getElementsByClassName('taxchk');
                        //     for (var i = 0; i < elements.length; i++) {
                        //         elements[i].value = '';
                        //     }
                        // }

                    }
                };
                xhr.send();

            });
        });


        $(document).on('click', '.copy-icon', function() {
            var row = $(this).closest('tr');
            var nextRow = row.next('tr');

            if (nextRow.length > 0) {
                row.find('td').each(function(index) {
                    var cell = $(this);
                    var nextCell = nextRow.find('td').eq(index);
                    var input = cell.find('input');
                    var select = cell.find('select');

                    var nextInput = nextCell.find('input');
                    if (input.length > 0 && nextInput.length > 0) {
                        nextInput.val(input.val());
                    }

                    var nextSelect = nextCell.find('select');
                    if (select.length > 0 && nextSelect.length > 0) {
                        nextSelect.val(select.val());
                    }
                });
            }
        });

        $(document).on('click', '.remove-icon', function() {
            var row = $(this).closest('tr');
            var rowIndex = row.index();
            document.getElementById('rooms').value = parseInt(document.getElementById('rooms').value) - 1;
            var clonedDiv = document.getElementById('cloneit' + (rowIndex));
            if (clonedDiv) {
                clonedDiv.remove();
            }

            let totalrooms = parseInt($('#totalrooms').val());
            $('#totalrooms').val(totalrooms - 1);
            row.remove();

            totalClonedCount--;
            let rowCount = $('#gridtaxstructure tbody tr').length;
            if (rowCount == 1) {
                $('.leadercl').prop('checked', false);
            }
            $('#gridtaxstructure tr').each(function(index) {
                console.log('index', index);
                if (index >= rowIndex) {
                    var oldIndex = index + 1;
                    var newIndex = index;

                    var clonedDiv = document.getElementById('cloneit' + oldIndex);
                    if (clonedDiv) {
                        clonedDiv.id = 'cloneit' + newIndex;
                    }

                    $(this).find('select, input').each(function() {
                        var regex = new RegExp(oldIndex + "$");
                        this.id = this.id.replace(regex, newIndex);
                        this.name = this.name.replace(regex, newIndex);
                    });
                }
            });
        });

        let totalClonedCount = 1;

        function HandleGuestList(guestlist) {
            const guestlistCheckbox = document.getElementById(guestlist);
            var table = document.getElementById('gridtaxstructure');
            var rows = table.getElementsByTagName('tr');
            var rowCount = parseInt(rows.length - 1);

            var cloneit = document.getElementById('cloneit');

            if (guestlistCheckbox.checked && rowCount > 1) {
                var diff = rowCount - totalClonedCount;

                if (diff > 0) {
                    let lastClonedDiv = cloneit;
                    for (let i = totalClonedCount; i < rowCount; i++) {
                        var clonedDiv = cloneit.cloneNode(true);
                        clonedDiv.id = 'cloneit' + i;

                        clonedDiv.querySelectorAll('[id]').forEach((element) => {
                            element.id = element.id + i;
                        });
                        clonedDiv.querySelectorAll('[name]').forEach((element) => {
                            element.name = element.name + i;
                        });
                        clonedDiv.querySelectorAll('[data-target]').forEach((element) => {
                            var currentDataTarget = element.getAttribute('data-target');
                            element.setAttribute('data-target', currentDataTarget + i);
                        });
                        lastClonedDiv.insertAdjacentElement('afterend', clonedDiv);
                        const modalInsideClone = clonedDiv.querySelector('#formguestdt' + i);
                        totalClonedCount++;
                        lastClonedDiv = clonedDiv;
                    }
                }

            } else {
                // console.log('Checkbox is not checked or row count is not greater than 0.');
            }
        }

        function fetchData(url, targetElement) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        targetElement.value = response.data;
                    } else {
                        console.error('Request failed with status: ' + xhr.status);
                    }
                }
            };
            xhr.send();
        }
    </script>

    <script>
        var xhr = new XMLHttpRequest();
        var csrfToken = '{{ csrf_token() }}';
        xhr.open('GET', '{{ route('checkeditarrival') }}', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                var result = JSON.parse(response);

                // document.getElementById('checkindate').value = result.ncur;
                // const now = new Date();
                // const hours = now.getHours().toString().padStart(2, '0');
                // const minutes = now.getMinutes().toString().padStart(2, '0');
                // const currentTime = `${hours}:${minutes}`;
                // document.getElementById('checkintime').value = currentTime;
                // document.getElementById('checkouttime').value = result.checkout;

                // const checkinDate = new Date(result.ncur);
                // const checkoutDate = new Date(checkinDate);
                // checkoutDate.setDate(checkoutDate.getDate() + 1);
                // const checkoutYear = checkoutDate.getFullYear();
                // const checkoutMonth = (checkoutDate.getMonth() + 1).toString().padStart(2, '0');
                // const checkoutDay = checkoutDate.getDate().toString().padStart(2, '0');
                // const checkoutFormattedDate = `${checkoutYear}-${checkoutMonth}-${checkoutDay}`;
                // document.getElementById('checkoutdate').value = checkoutFormattedDate;
                if (result.arrdatetimeedit === 'N') {
                    // console.log(result.arrdatetimeedit);
                    document.getElementById('checkindate').readOnly = true;
                    document.getElementById('checkintime').readOnly = true;
                } else {
                    document.getElementById('checkindate').readOnly = false;
                    document.getElementById('checkintime').readOnly = false;
                }

                if (result.roomrateeditable === 'N') {
                    var elements = document.getElementsByClassName('ratechk');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].readOnly = true;
                    }
                }
                // if (result.roominctaxeditable === 'N') {
                //     var elements = document.getElementsByClassName('taxchk');
                //     for (var i = 0; i < elements.length; i++) {
                //         elements[i].disabled = true;
                //     }
                // }

                // if (result.rrinctaxdefault === 'Y') {
                //     var elements = document.getElementsByClassName('taxchk');
                //     for (var i = 0; i < elements.length; i++) {
                //         elements[i].value = 'Y';
                //     }
                // } else if (result.rrinctaxdefault === 'N') {
                //     var elements = document.getElementsByClassName('taxchk');
                //     for (var i = 0; i < elements.length; i++) {
                //         elements[i].value = 'N';
                //     }
                // } else {
                //     var elements = document.getElementsByClassName('taxchk');
                //     for (var i = 0; i < elements.length; i++) {
                //         elements[i].value = '';
                //     }
                // }

            }
        };
        xhr.send();

        // document.addEventListener('DOMContentLoaded', function() {
        //     var mobileNoInput = document.getElementById('mobile');

        //     mobileNoInput.addEventListener('input', function() {
        //         mobileNoInput.value = parseInt(mobileNoInput.value, 10) || '';
        //     });
        // });

        // document.addEventListener('DOMContentLoaded', function() {
        //     var mobileNoInput = document.getElementById('guestmobile');

        //     mobileNoInput.addEventListener('input', function() {
        //         mobileNoInput.value = parseInt(mobileNoInput.value, 10) || '';
        //     });
        // });

        function loadgstspanonbodyload() {
            var companySelect = document.getElementById('company');
            var selectedOption = companySelect.options[companySelect.selectedIndex];
            var gstcodep = document.getElementById('gstCodep');
            var gstCodeSpan = document.getElementById('gstCode');
            if (selectedOption.getAttribute('data-gst') !== '') {
                gstcodep.style.display = 'block';
                gstCodeSpan.textContent = selectedOption.getAttribute('data-gst');
            } else {
                gstcodep.style.display = 'none';
                gstCodeSpan.textContent = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadgstspanonbodyload();
        });

        document.getElementById('company').addEventListener('change', function() {
            var companySelect = this;
            var selectedOption = companySelect.options[companySelect.selectedIndex];
            var gstcodep = document.getElementById('gstCodep');
            var gstCodeSpan = document.getElementById('gstCode');
            if (selectedOption.getAttribute('data-gst') !== '') {
                gstcodep.style.display = 'block';
                gstCodeSpan.textContent = selectedOption.getAttribute('data-gst');
            } else {
                gstcodep.style.display = 'none';
                gstCodeSpan.textContent = '';
            }
        });

        function validateAadhar2(idType, idNumber, iderr) {
            var selectedIdType = document.getElementById(idType).value;
            var idNumberInput = document.getElementById(idNumber);
            var idNumberError = document.getElementById(iderr);

            if (selectedIdType == 'Aadhar Card' && idNumberInput.value.length < 12) {
                idNumberInput.value = '';
                idNumberInput.setAttribute('maxlength', '12');
                idNumberInput.setAttribute('minlength', '12');
                idNumberInput.required = true;
                idNumberError.style.display = 'block';
            } else {
                idNumberInput.removeAttribute('maxlength');
                idNumberInput.removeAttribute('minlength');
                idNumberInput.required = false;
                idNumberError.style.display = 'none';
            }

        }


        function DisplayIssueFields2(idType, issuefielda, issuefieldb, issuefieldc) {
            var selectedIdType = document.getElementById(idType).value;
            var issuefielda = document.getElementById(issuefielda);
            var issuefieldb = document.getElementById(issuefieldb);
            var issuefieldc = document.getElementById(issuefieldc);
            // console.log(selectedIdType);

            if (selectedIdType === 'Passport') {
                issuefielda.style.display = 'block';
                issuefieldb.style.display = 'block';
                issuefieldc.style.display = 'block';
            } else {
                issuefielda.style.display = 'none';
                issuefieldb.style.display = 'none';
                issuefieldc.style.display = 'none';
            }
        }


        function DisplayBillingField2(paymentMethod, billingfield) {
            var selectedPaymentMethod = document.getElementById(paymentMethod).value;
            var billingfield = document.getElementById(billingfield);

            var selectedPaymentMethod = document.getElementById(paymentMethodElements[0].id).value;
            var billingfield = document.getElementById(billingfieldElements[0].id);

            if (selectedPaymentMethod === 'Bill To Company') {
                billingfield.style.display = 'block';
            } else {
                billingfield.style.display = 'none';
            }

        }

        function validatePercentagers2(input) {
            var rodisc = document.getElementById(input).value;

            if (rodisc.value > 100) {
                rodisc.value = '';
            }

            if (isNaN(rodisc.value)) {
                rodisc.value = '';
            }

            if (rodisc.value < 0) {
                rodisc.value = '';
            }

        }

        function validatePercentage2(input) {
            var rodisc = document.getElementById(input).value;

            if (rodisc.value > 100) {
                rodisc.value = '';
            }
            if (isNaN(rodisc.value)) {
                rodisc.value = '';
            }
            if (rodisc.value < 0) {
                rodisc.value = '';
            }
        }

        function DisplayVehicleNum2(travelmode, vehicleNum) {
            var selectedTravelMode = document.getElementById(travelmode).value;
            var vehicleNum = document.getElementById(vehicleNum);

            if (selectedTravelMode == 'By Car') {
                vehicleNum.style.display = 'block';
            } else {
                vehicleNum.style.display = 'none';
            }
        }

        function openFileInputpf(inputId) {
            document.getElementById(inputId).click();
        }

        function openidentity(inputId) {
            document.getElementById(inputId).click();
        }

        function readURLp(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var preview = document.getElementById(previewId);
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            $(document).on('click', '.advanceprint', function() {
                let amount = $(this).closest('tr').find('td.amount').text();
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
                    return str;
                }

                let fixval = Math.abs(amount);
                let textamount = inWords(fixval);
                let paymentmode = $(this).closest('tr').find('td.paytype').text();
                let compname = $('#compname').val();
                let address = $('#address').val();
                let name = $('#name').val();
                let mob = $('#compmob').val();
                let email = $('#email').val();
                let roomno = $(this).closest('tr').data('roomno');
                let nature = $('#nature').val();
                let u_name = $('#u_name').val();
                let logo = 'storage/admin/property_logo/' + $('#logo').val();
                let filetoprint = 'advancereceipt';
                let ncurdate = $('#curdate').val();
                let rectnop = $(this).closest('tr').data('vno');
                let curdate = new Date(ncurdate).toLocaleDateString('en-IN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                let newWindow = window.open(filetoprint, '_blank');
                let recref = 'Received';
                let asadvref = 'As Advance';
                if (amount < 0) {
                    recref = 'Refund'
                    asadvref = 'As Refund';
                }

                newWindow.onload = function() {
                    $('.recpno', newWindow.document).text(rectnop);
                    $('#compname', newWindow.document).text(compname);
                    $('#address', newWindow.document).text(address);
                    $('#recref', newWindow.document).text(recref);
                    $('#asadvref', newWindow.document).text(asadvref);
                    $('#name', newWindow.document).text(name);
                    $('#phone', newWindow.document).text(mob);
                    $('#email', newWindow.document).text(email);
                    $('#roomno', newWindow.document).text(roomno);
                    $('#amount', newWindow.document).text(Math.abs(amount));
                    $('#textamount', newWindow.document).text(textamount);
                    $('#curdate', newWindow.document).text(curdate);
                    $('#nature', newWindow.document).text(paymentmode);
                    $('#u_name', newWindow.document).text(u_name);
                    $('#complogo', newWindow.document).attr('src', logo);
                    $('#compname2', newWindow.document).text(compname);
                    $('#address2', newWindow.document).text(address);
                    $('#recref2', newWindow.document).text(recref);
                    $('#asadvref2', newWindow.document).text(asadvref);
                    $('#name2', newWindow.document).text(name);
                    $('#phone2', newWindow.document).text(mob);
                    $('#email2', newWindow.document).text(email);
                    $('#roomno2', newWindow.document).text(roomno);
                    $('#amount2', newWindow.document).text(Math.abs(amount));
                    $('#textamount2', newWindow.document).text(textamount);
                    $('#curdate2', newWindow.document).text(curdate);
                    $('#nature2', newWindow.document).text(paymentmode);
                    $('#u_name2', newWindow.document).text(u_name);
                    $('#complogo2', newWindow.document).attr('src', logo);

                    setTimeout(function() {
                        newWindow.print();
                        newWindow.close();
                    }, 500);
                };
            });
        });
    </script>

@endsection
