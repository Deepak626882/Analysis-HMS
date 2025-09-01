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
                            <form class="walkin-form" id="walkinform" name="walkinform" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="N" name="guestfetch" id="guestfetch">
                                <input type="hidden" name="guestfetchdocid" id="guestfetchdocid">
                                <input type="hidden" value="" name="planratesum" id="planratesum">
                                <input type="hidden" value="{{ $enviro_formdata->rrinctaxdefault }}" name="rrinctaxdefault"
                                    id="rrinctaxdefault">
                                <input type="hidden" name="totalrooms" value="1" id="totalrooms">
                                <div class="row">
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
                                                        placeholder="2023-10-26" value="<?php echo date('Y-m-d'); ?>"
                                                        id="checkindate" onchange="validateDates()" required>
                                                </td>

                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="time" id="checkintime" name="checkintime"
                                                            class="form-control low" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" onchange="DisplayCheckout()"
                                                        oninput="ValidateNum(this, '1', '100', '3')" name="stay_days"
                                                        id="stay_days" class="form-control stays" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="date" value="{{ $checkoutdate }}" name="checkoutdate"
                                                        class="form-control low alibaba" placeholder="2023-10-26"
                                                        id="checkoutdate" onchange="validateDates()" required>
                                                    <span class="text-danger absolute-element" id="date-error"></span>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="time" id="checkouttime" name="checkouttime"
                                                            class="form-control low" required>
                                                    </div>
                                                </td>
                                                <td><input id="rooms" style="text-align: end;" type="number"
                                                        oninput="ValidateNum(this, '1', '100', '3')" name="rooms"
                                                        class="form-control low fiveem" placeholder="1">
                                                </td>
                                                <td><input placeholder="Remarks" class="form-control" name="remarkmain"
                                                        id="remarkmain" type="text"></td>
                                                <td><input placeholder="Pickup/Drop" class="form-control" type="text"
                                                        name="pickupdrop" id="pickupdrop"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table walkin-table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Booking Source</th>
                                                <th>Business Source</th>
                                                <th style="display: none;" id="trvelth">Travel Agent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select id="company" name="company" class="form-control low">
                                                        <option value="">Select</option>
                                                        @foreach ($company as $list)
                                                            <option value="{{ $list->sub_code }}" data-gst="{{ $list->gstin }}">
                                                                {{ $list->name }}
                                                            </option>
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
                                                        <option value="">Select</option>
                                                        <option value="Booking Engine">Booking Engine</option>
                                                        <option value="OTA">OTA</option>
                                                        <option value="Travel Agent">Travel Agent</option>
                                                        <option value="Direct">Direct</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="bsource" name="bsource" class="form-control low" required>
                                                        <option value="">Select</option>
                                                        @foreach ($bsource as $list)
                                                            <option value="{{ $list->bcode }}">{{ $list->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="display:none;" id="trveltd">
                                                    <select id="travel_agent" name="travel_agent" class="form-control low">
                                                        <option value="">Select</option>
                                                        @foreach ($travel_agent as $list)
                                                            <option value="{{ $list->sub_code }}" data-gst="{{ $list->gstin }}">
                                                                {{ $list->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="position-absolute p-0">
                                                        <p id="gstCodet" class="text-purple"
                                                            style="font-size: small;display: none;">GST
                                                            No.: <span id="gstCodet"></span></p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="complimentry"
                                            id="complimentry">
                                        <label class="form-check-label" for="complimentry">Complimentry Room</label>
                                    </div>

                                    <div class="multirow">
                                        <table class="table-hover walkin-multi table-responsive" id="gridtaxstructure">
                                            <thead>
                                                <th>Room Type</th>
                                                <th>Room</th>
                                                <th>Adult</th>
                                                <th>Child</th>
                                                <th>Plans</th>
                                                <th>Rate Rs.</th>
                                                <th>Tax Inc.</th>
                                                <th>Leader</th>
                                                <th id="thlast">Action</th>
                                            </thead>
                                            <tbody>
                                                <tr class="data-row">
                                                    <td>
                                                        <select id="cat_code1" name="cat_code1"
                                                            class="form-control sl catselect cat_code_class" required>
                                                            <option value="">Select</option>
                                                            @foreach ($roomcat as $list)
                                                                <option value="{{ $list->cat_code }}">{{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" class="form-control" name="planedit1"
                                                            id="planedit1" readonly>
                                                    </td>
                                                    <td><select id="roommast1" name="roommast1"
                                                            class="form-control sl roomselect" required>
                                                            <option value="">Select</option>
                                                        </select></td>
                                                    <td><select id="adult1" name="adult1" class="form-control sl" required>
                                                            <option value="">Select</option>
                                                            <option value="1">1</option>
                                                            <option selected value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select></td>
                                                    <td><select id="child1" name="child1" class="form-control sl" required>
                                                            <option value="0">0</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                        </select></td>
                                                    <td><select id="planmaster1" name="planmaster1"
                                                            class="form-control planmastclass sl">
                                                            <option value="">Select</option>
                                                        </select></td>
                                                    <td><input type="text" name="rate1" id="rate1"
                                                            oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                                                            class="form-control ratechk sp" required></td>
                                                    <td><select class="form-control taxchk sl" name="tax_inc1"
                                                            id="tax_inc1">
                                                            <option value="">Select</option>
                                                            <option value="Y" {{ $enviro_formdata->rrinctaxdefault == 'Y' ? 'selected' : '' }}>Yes</option>
                                                            <option value="N" {{ $enviro_formdata->rrinctaxdefault == 'N' ? 'selected' : '' }}>No</option>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="leadercl" name="leader1" id="leader1">
                                                    </td>
                                                    <td>
                                                        <img src="admin/icons/flaticon/copy.gif" alt="copy icon"
                                                            class="copy-icon">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="button-container custom-range">
                                        <button type="button" name="add_room" id="add_room"
                                            class="btn radiusbtn mb-1 btn-outline-success">Add Room <i
                                                class="fa-solid fa-building"></i></button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mt-4 form-check">
                                            <input type="checkbox" onchange="HandleGuestList('guestlist')"
                                                class="form-check-input" name="guestlist" id="guestlist">
                                            <label class="form-check-label" for="guestlist">Guest List</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">


                                    </div>
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
                                                <option value="Mr.">Mr.</option>
                                                <option value="Ms.">Ms.</option>
                                                <option value="Mam">Mam</option>
                                                <option value="Dr.">Dr.</option>
                                                <option value="Prof.">Prof.</option>
                                                <option value="Mrs.">Mrs.</option>
                                                <option value="Miss">Miss</option>
                                                <option value="Sir">Sir</option>
                                                <option value="Madam">Madam</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label" for="reservationtype">&nbsp;
                                                &NonBreakingSpace;</label>
                                            <div style="display: flex; align-items: center;">
                                                <input type="text" name="name" placeholder="Full Name" maxlength="100"
                                                    id="name" class="form-control" required>
                                                <i class="fa-regular fa-credit-card wcard" data-toggle="modal"
                                                    data-target="#formswipecard" style="margin-left: 5px;"></i>
                                                <i id="guestinfobutton" data-toggle="modal" data-target="#formguestdt"
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
                                                            <div class="row">
                                                                <div class="col">
                                                                    <label for="findname"
                                                                        class="col-form-label">Name</label>
                                                                    <input
                                                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]|^(.{50}).*$/g, '$1')"
                                                                        type="text" class="form-control" name="findname"
                                                                        id="findname">
                                                                </div>
                                                                <div class="col">
                                                                    <label for="findnum"
                                                                        class="col-form-label">Mobile</label>
                                                                    <input
                                                                        oninput="this.value = this.value.replace(/[^0-9]|^(.{11}).*$/g, '$1')"
                                                                        type="text" class="form-control" name="findnum"
                                                                        id="findnum">
                                                                </div>
                                                            </div>
                                                            <div class="text-center mt-3">
                                                                <button class="btn btn-success" type="button" id="findby"
                                                                    name="findby">Find</button>
                                                            </div>
                                                        </div>
                                                        <div style="padding: .5rem;" class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div style="overflow: auto;" class="modal fade" id="formguestdt">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Add Guest</h5>
                                                            <button onclick="DeleteData()" type="button" class="close"
                                                                data-dismiss="modal"><span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mt-2">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="profileimagepreview"></label>
                                                                        <label for="profileimagepreview">
                                                                            <img class="preview prevprofile"
                                                                                id="profileimagepreview"
                                                                                src="{{ asset('admin/images/preview.gif') }}"
                                                                                alt="your image"
                                                                                onclick="openFileInput('profileimage');" />
                                                                            <div style="text-align: center;">
                                                                                {{-- <button
                                                                                    onclick="startWebcam('profileimagepreview', 'openWebcamBtn', 'webcamContainer', 'closeBtn', 'videoElement', 'captureBtn', 'capturedImageCanvas')"
                                                                                    type="button"
                                                                                    class="mt-2 openWebcamBtn p-1"
                                                                                    id="openWebcamBtn">Open
                                                                                    Webcam <i
                                                                                        class="fa-solid fa-camera"></i></button>
                                                                                --}}
                                                                            </div>
                                                                        </label>
                                                                        <input type="hidden" name="existing_profileimage"
                                                                            id="existing_profileimage">
                                                                        <input type="file" name="profileimage"
                                                                            class="profileimage" id="profileimage"
                                                                            onchange="readURL(this, 'profileimagepreview');" />
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
                                                                                    <select style="width: auto;"
                                                                                        class="form-control"
                                                                                        name="greetingsguest"
                                                                                        id="greetingsguest">
                                                                                        <option value="Mr.">Mr.</option>
                                                                                        <option value="Ms.">Ms.</option>
                                                                                        <option value="Ma'am">Ma'am</option>
                                                                                        <option value="Dr.">Dr.</option>
                                                                                        <option value="Prof.">Prof.</option>
                                                                                        <option value="Mrs.">Mrs.</option>
                                                                                        <option value="Miss">Miss</option>
                                                                                        <option value="Sir">Sir</option>
                                                                                        <option value="Madam">Madam</option>
                                                                                    </select>
                                                                                    <input style="width: auto;" type="text"
                                                                                        name="guestname"
                                                                                        placeholder="Full Name"
                                                                                        maxlength="25" id="guestname"
                                                                                        class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <div class="">
                                                                            <div class="form-group">
                                                                                <label for="guestmobile">Mobile</label>
                                                                                <input type="tel" class="form-control"
                                                                                    id="guestmobile" name="guestmobile"
                                                                                    placeholder="Mobile">
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="gender">Gender</label>
                                                                            <select name="genderguest" id="genderguest"
                                                                                class="form-control">
                                                                                <option value="">Select</option>
                                                                                <option value="Male">Male</option>
                                                                                <option value="Female">Female</option>
                                                                                <option value="Other">Other</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="email">Email</label>
                                                                                <input type="email" class="form-control"
                                                                                    id="guestemail" name="guestemail"
                                                                                    placeholder="Email">
                                                                                <small class="form-text text-muted">Use
                                                                                    comma
                                                                                    to
                                                                                    add multiple Email IDs</small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="arrfrom">Arrival From</label>
                                                                                <select name="arrfrom" id="arrfrom"
                                                                                    class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    @foreach ($citydata as $list)
                                                                                        <option value="{{ $list->city_code }}">
                                                                                            {{ $list->cityname }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="destination">Destination</label>
                                                                                <select name="destination" id="destination"
                                                                                    class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    @foreach ($citydata as $list)
                                                                                        <option value="{{ $list->city_code }}">
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
                                                                    <select class="form-control" name="cityguest"
                                                                        id="cityguest">
                                                                        <option value="">Select City</option>
                                                                        @foreach ($citydata as $list)
                                                                            <option value="{{ $list->city_code }}">
                                                                                {{ $list->cityname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="stateguest">State</label>
                                                                        <select class="form-control" name="stateguest"
                                                                            id="stateguest">
                                                                            <option value="">Select State
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="countryguest">Country</label>
                                                                        <select class="form-control" name="countryguest"
                                                                            id="countryguest">
                                                                            <option value="">Select Country
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="zipguest">Zip</label>
                                                                        <input type="text" class="form-control"
                                                                            id="zipguest" name="zipguest"
                                                                            placeholder="Zip Code">
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row">
                                                                <input type="hidden" name="signimage" id="signimage">
                                                                <div class="col-md-6">
                                                                    <h1 class="signature-heading">
                                                                        <i class="fas fa-pen-signature"></i> Guest Signature
                                                                    </h1>
                                                                    <div class="text-center mt-5 mb-3">
                                                                        <button type="button" id="openModalBtn"
                                                                            class="btn btn-primary">Sign Your Name</button>
                                                                    </div>

                                                                    <div id="myModal" class="modal fade signaturemodal"
                                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Guest Signature
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close"
                                                                                        data-bs-dismiss="modal"
                                                                                        aria-label="Close"><i
                                                                                            class="fa-solid fa-xmark"></i></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>Use Pen Tablet For a better
                                                                                        signature: <a target="_blank"
                                                                                            class="text-info font-weight-bold"
                                                                                            href="https://www.amazon.in/HUION-H420-Pressure-Battery-Free-4-17x2-6/dp/B00DM24HNE">HUION
                                                                                            USB PEN TABLET</a></p>
                                                                                    <input type="color" id="colorPicker"
                                                                                        value="#000000">
                                                                                    <canvas id="signatureCanvas" width="400"
                                                                                        height="200"></canvas>
                                                                                    <div class="signature-tips">
                                                                                        Tip: Take your time and sign slowly
                                                                                        for a smooth signature</br>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-4">
                                                                                            <button type="button"
                                                                                                id="clearCanvasBtn"
                                                                                                class="btn btn-secondary mt-3">Clear</button>
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <button type="button"
                                                                                                id="downloadBtn"
                                                                                                class="btn btn-success mt-3">Save</button>
                                                                                        </div>
                                                                                        {{-- <div class="col-4">
                                                                                            <button type="button"
                                                                                                id="previewBtn"
                                                                                                class="btn btn-primary mt-3">Preview</button>
                                                                                        </div> --}}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <img id="imagePreview" alt="Signature Preview" />
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
                                                                                        <img class="preview"
                                                                                            id="identityimagepreview"
                                                                                            src="{{ asset('admin/images/preview.gif') }}"
                                                                                            alt="your image"
                                                                                            onclick="openFileInput2('identityimage');" />
                                                                                    </label>
                                                                                    <div style="text-align: center;">
                                                                                        {{-- <button
                                                                                            onclick="startWebcam('identityimagepreview', 'openWebcamBtn2', 'webcamContainer2', 'closeBtn2', 'videoElement2', 'captureBtn2', 'capturedImageCanvas2')"
                                                                                            type="button"
                                                                                            class="mt-2 openWebcamBtn p-1"
                                                                                            id="openWebcamBtn2">Open
                                                                                            Webcam <i
                                                                                                class="fa-solid fa-camera"></i></button>
                                                                                        --}}
                                                                                    </div>
                                                                                    <input type="hidden"
                                                                                        name="existing_identityimage"
                                                                                        id="existing_identityimage">
                                                                                    <input type="file" name="identityimage"
                                                                                        id="identityimage"
                                                                                        class="identityimage"
                                                                                        onchange="readURL2(this, 'identityimagepreview');" />
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
                                                                                                onchange="validateAadhar('idType', 'idNumber', 'idNumberError');DisplayIssueFields('idType','issuefielda', 'issuefieldb', 'issuefieldc')"
                                                                                                name="idType" id="idType"
                                                                                                class="form-control idTypeSelect">
                                                                                                <option value="">Select
                                                                                                </option>
                                                                                                <option value="Aadhar Card">
                                                                                                    Aadhar Card</option>
                                                                                                <option
                                                                                                    value="Driving Licence">
                                                                                                    Driving Licence</option>
                                                                                                <option value="Passport">
                                                                                                    Passport</option>
                                                                                                <option
                                                                                                    value="National Identity Card">
                                                                                                    National Identity Card
                                                                                                </option>
                                                                                                <option value="Voter Id">
                                                                                                    Voter Id
                                                                                                </option>
                                                                                                <option value="Green Card">
                                                                                                    Green Card
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="idNumber">ID
                                                                                                Number</label>
                                                                                            <input type="text"
                                                                                                oninput="this.value = this.value.toUpperCase()"
                                                                                                class="form-control idNumberInput"
                                                                                                id="idNumber"
                                                                                                name="idNumber"
                                                                                                placeholder="ID Number">
                                                                                            <span class="idNumberError"
                                                                                                id="idNumberError"
                                                                                                style="display:none;color: red; position: fixed;">Aadhar
                                                                                                number must be 12 digits and
                                                                                                contain only numbers</span>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display: none;"
                                                                                        id="issuefielda" class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="issuingCity">Issuing
                                                                                                City</label>
                                                                                            <select id="issuingcity"
                                                                                                name="issuingcity"
                                                                                                class="form-control">
                                                                                                <option value="">Select City
                                                                                                </option>
                                                                                                @foreach ($citydata as $list)
                                                                                                    <option
                                                                                                        value="{{ $list->city_code }}">
                                                                                                        {{ $list->cityname }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display: none;"
                                                                                        id="issuefieldb" class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="issuingcountry">Issuing
                                                                                                Country</label>
                                                                                            <select id="issuingcountry"
                                                                                                class="form-control"
                                                                                                name="issuingcountry">
                                                                                                <option value="">Select
                                                                                                    Country</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div style="display: none;"
                                                                                        id="issuefieldc" class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <label for="expiryDate">Expiry
                                                                                                Date</label>
                                                                                            <input onchange="PastDtNA(this)"
                                                                                                type="date"
                                                                                                class="form-control"
                                                                                                name="expiryDate"
                                                                                                id="expiryDate">
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
                                                                                    <select
                                                                                        onchange="DisplayBillingField('paymentMethod', 'billingfield')"
                                                                                        class="paymentmethodselect form-control"
                                                                                        name="paymentMethod"
                                                                                        id="paymentMethod"
                                                                                        class="form-control">
                                                                                        <option value="">Select</option>
                                                                                        <option value="Cash">Cash</option>
                                                                                        <option value="Bill To Company">Bill
                                                                                            To Company</option>
                                                                                        <option value="UPI">UPI</option>
                                                                                        <option value="Debit Card">Debit
                                                                                            Card</option>
                                                                                        <option value="Credit Card">Credit
                                                                                            Card</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div id="billingfield" style="display: none;"
                                                                                class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="billingAccount">Direct
                                                                                        Billing</label>
                                                                                    <select name="billingAccount"
                                                                                        id="billingAccount"
                                                                                        class="form-control">
                                                                                        <option value="">Select</option>
                                                                                        @foreach ($company as $item)
                                                                                            <option
                                                                                                value="{{ $item->sub_code }}">
                                                                                                {{ $item->name }}
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="birthDate">Birth
                                                                                        Date</label>
                                                                                    <input name="birthDate"
                                                                                        onchange="FutureDtNA(this)"
                                                                                        type="date" class="form-control"
                                                                                        id="birthDate">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="purpofvisit">Purpose Of
                                                                                        Visit</label>
                                                                                    <select class="form-control"
                                                                                        id="purpofvisit" name="purpofvisit">
                                                                                        <option value="">Select</option>
                                                                                        <option value="Official">Official
                                                                                        </option>
                                                                                        <option value="Personal">Personal
                                                                                        </option>
                                                                                        <option value="Business">Business
                                                                                        </option>
                                                                                        <option value="Tourist">Tourist
                                                                                        </option>
                                                                                        <option value="Other">Other</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="nationalityother">Nationality</label>
                                                                                    <select class="form-control"
                                                                                        id="nationalityother"
                                                                                        name="nationalityother">
                                                                                        <option value="">Select</option>
                                                                                        @foreach ($nationalitydata as $item)
                                                                                            <option
                                                                                                value="{{ $item->nationality }}">
                                                                                                {{ $item->nationality }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="vipStatus">Guest
                                                                                        Status</label>
                                                                                    <select name="vipStatus" id="vipStatus"
                                                                                        class="form-control">
                                                                                        <option value="">Select Status
                                                                                        </option>
                                                                                        @foreach ($gueststatus as $list)
                                                                                            <option value="{{ $list->gcode }}">
                                                                                                {{ $list->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="weddingAnniversary">Wedding
                                                                                        Anniversary</label>
                                                                                    <input name="weddingAnniversary"
                                                                                        onchange="FutureDtNA(this)"
                                                                                        type="date" class="form-control"
                                                                                        id="weddingAnniversary">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label for="marital_status">Marital
                                                                                        Status</label>
                                                                                    <select name="marital_status"
                                                                                        id="marital_status"
                                                                                        class="form-control">
                                                                                        <option value="">Select</option>
                                                                                        <option value="Single">Single
                                                                                        </option>
                                                                                        <option value="Married">Married
                                                                                        </option>
                                                                                        <option value="Divorced">Divorced
                                                                                        </option>
                                                                                        <option value="Widowed">Widowed
                                                                                        </option>
                                                                                        <option value="Separated">Separated
                                                                                        </option>
                                                                                        <option value="Other">Other</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                        <div class="row">

                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label"
                                                                                        for="rodisc">Room Discount %</label>
                                                                                    <input type="text" step="0.01"
                                                                                        min="0.00" max="99.99"
                                                                                        placeholder="0.00" name="rodisc"
                                                                                        id="rodisc"
                                                                                        class="form-control percent_value"
                                                                                        oninput="validatePercentage('rodisc')">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label"
                                                                                        for="rsdisc">Rs Disc %</label>
                                                                                    <input type="text" step="0.01"
                                                                                        min="0.00" max="99.99"
                                                                                        placeholder="0.00" name="rsdisc"
                                                                                        id="rsdisc"
                                                                                        class="form-control percent_value"
                                                                                        oninput="validatePercentagers('rsdisc')">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label class="col-form-label"
                                                                                        for="travelmode">Travel Mode</label>
                                                                                    <select
                                                                                        onchange="DisplayVehicleNum('travelmode', 'vehiclediv')"
                                                                                        name="travelmode"
                                                                                        class="form-control"
                                                                                        id="travelmode">
                                                                                        <option value="">Select</option>
                                                                                        <option value="By Road">By Road
                                                                                        </option>
                                                                                        <option value="By Air">By Air
                                                                                        </option>
                                                                                        <option value="By Car">By Car
                                                                                        </option>
                                                                                        <option value="By Bus">By Bus
                                                                                        </option>
                                                                                        <option value="By Train">By Train
                                                                                        </option>
                                                                                        <option value="By Ship">By Ship
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                        <div class="row">
                                                                            <div id="vehiclediv" style="display: none;"
                                                                                class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label for="vehiclenum"
                                                                                        class="col-form-label">Vehicle
                                                                                        Number</label>
                                                                                    <input type="text"
                                                                                        oninput="this.value = this.value.toUpperCase()"
                                                                                        name="vehiclenum" id="vehiclenum"
                                                                                        class="form-control"
                                                                                        placeholder="Enter Vehicle Number">

                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button onclick="DeleteData()" type="button"
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
                                            <input type="tel" name="mobile" placeholder="Mobile" minlength="10"
                                                maxlength="10" id="mobile" class="form-control" {{ $enviro_formdata->grcmandatory == 'Y' ? 'required' : '' }}>
                                            <div style="display: none;" id="error-phone" class="error-phone text-danger">
                                                Invalid Number</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="col-form-label" for="email">Email</label>
                                            <input type="email" name="email" placeholder="Email" maxlength="100" id="email"
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
                                                        <option value="">Select City</option>
                                                        @foreach ($citydata as $list)
                                                            <option value="{{ $list->city_code }}">
                                                                {{ $list->cityname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="">Select State</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="country" id="country">
                                                        <option value="">Select Country</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="nationality" id="nationality">
                                                        <option value="">Select Nationality</option>
                                                    </select>
                                                </td>

                                                <td><input type="text" class="form-control fiveem" name="zipcode"
                                                        id="zipcode">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="Enter Address 1" class="form-control"
                                                        name="address1" id="address1">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="Enter Address 2" class="form-control"
                                                        name="address2" id="address2">
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

                                <div class="col-7 mt-4 mb-4 ml-auto">
                                    <button type="button" class="btn btn-danger" onclick="location.reload()">Cancel <i
                                            class="fa-solid fa-xmark"></i></button>
                                    <button type="submit" name="walkinsubmit" id="walkinsubmit"
                                        class="btn btn-primary">Check In <i class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                            <div class="table-responsive table-findhistory none">
                                <table id="findhistory" class="table">
                                    <thead>
                                        <tr>
                                            <th>Sn</th>
                                            <th>Guest Name</th>
                                            <th>Mobile</th>
                                            <th>Room Type</th>
                                            <th>Room No</th>
                                            <th>Arrival Date</th>
                                            <th>Adult</th>
                                            <th>Days</th>
                                            <th>Inc Tax</th>
                                            <th>Plan</th>
                                            <th>Plan Amt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- #/ container -->

    <script>
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
        $(document).ready(function() {
            $('#myloader').removeClass('none');
            setTimeout(() => {
                $('#myloader').addClass('none');
            }, 500);

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

                    let sum = 0.00;
                    $('.rowdamount').each(function() {
                        sum += parseFloat($(this).val()) || 0;
                    });

                    let roomratenet = sum + newnetroomrate;
                    $(`#totalnetamtplan${number}`).val(roomratenet.toFixed(2));
                }, 500);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            localStorage.setItem('comprateyn', 'N');
            let compdiscdata = [];
            $(document).on('change', '#company', function() {
                let compcode = $(this).val();

                Swal.fire({
                    title: 'Walkin',
                    text: 'Apply Rates As Per Company',
                    icon: 'info',
                    showCancelButton: true,
                    cancelButtonText: 'No',
                    confirmButtonText: 'Yes'
                }).then((success) => {
                    if (success.isConfirmed) {
                        $.ajax({
                            url: "{{ url('walkincompdetail') }}",
                            method: "POST",
                            data: {
                                compcode: compcode
                            },
                            success: function(response) {
                                localStorage.setItem('comprateyn', 'Y');
                                let compdata = response.compdata;
                                compdiscdata = response.compdiscdata;
                                $('#name').val(compdata.conperson ?? '');
                                $('#mobile').val(compdata.mobile ?? '');
                                $('#email').val(compdata.email ?? '');
                                $('#cityname').val(compdata.citycode ?? '').change();
                                $('#address1').val(compdata.address ?? '');
                                $('#booking_source').val('Direct');
                            },
                            error: function(errorres) {

                            }
                        });
                    }
                });
            });

            function triggerRateCalculation(index) {
                const cid = $(`#roommast${index}`).val();
                const adult = +($(`#adult${index}`).val() || 0);
                const child = +($(`#child${index}`).val() || 0);
                const room_category = $(`#cat_code${index}`).val();
                const sumchildadult = adult + child;
                if (localStorage.getItem('comprateyn') == 'N' || localStorage.getItem('comprateyn') == '') {

                    if (!$(`#planedit${index}`).val() || $(`#planedit${index}`).val() === 'N') {
                        $.post('/getrate3', {
                            data: JSON.stringify([room_category, cid, sumchildadult]),
                            _token: '{{ csrf_token() }}'
                        }, function(result) {
                            $(`#rate${index}`).val(result);
                        });
                    }

                }
            }

            $(document).on('change', '.roomselect', function() {
                const index = this.id.match(/\d+$/)[0];
                const roomNo = this.value;
                const catid = $(this).find(':selected').data('catid');

                if (!$(`#cat_code${index}`).val()) {
                    $(`#roommast${index}`).data('preselect', roomNo);
                    $(`#cat_code${index}`).val(catid).trigger('change');
                } else {
                    $(`#roommast${index}`).val(roomNo);
                    triggerRateCalculation(index);
                }
            });

            $(document).on('change', '.cat_code_class', function() {
                const index = this.id.match(/\d+$/)[0];
                const cid = this.value;
                const checkindate = $('#checkindate').val();
                const checkoutdate = $('#checkoutdate').val();
                let adultcount = $(`#adult${index}`).val();

                $(`#roommast${index}`).empty().append('<option value="">Loading…</option>');
                $(`#planmaster${index}`).empty().append('<option value="">Loading…</option>');

                if (localStorage.getItem('comprateyn') == 'Y') {
                    let matchedRow = compdiscdata.find(cdata => cdata.roomcatcode == cid && cdata.adult == adultcount);

                    if (matchedRow) {
                        if (matchedRow.plan != '') {
                            $(`#rate${index}`).val(matchedRow.planamount);
                        } else {
                            $(`#rate${index}`).val(matchedRow.fixrate);
                        }
                    } else {
                        $(`#rate${index}`).val('0.00');
                    }
                }

                $.post('/getroomswalkin', {
                    cid,
                    checkindate,
                    checkoutdate,
                    _token: '{{ csrf_token() }}'
                }, function(result) {
                    $(`#roommast${index}`).html(result);

                    const want = $(`#roommast${index}`).data('preselect');
                    if (want) {
                        $(`#roommast${index}`).val(want);
                        $(`#roommast${index}`).removeData('preselect');

                        triggerRateCalculation(index);
                    }
                });

                $.post('/getplans', {
                    cid,
                    _token: '{{ csrf_token() }}'
                }, function(result) {
                    $(`#planmaster${index}`).html(result);
                });
            });

            $(document).on('change', '[id^="child"], [id^="adult"], [id^="roommast"]', function() {
                const index = this.id.match(/\d+$/)[0];
                triggerRateCalculation(index);
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
                    let sum = 0.00;
                    $('.rowdamount').each(function() {
                        sum += parseFloat($(this).val()) || 0;
                    });

                    let roomratenet = sum + newnetroomrate;
                    $(`#totalnetamtplan${number}`).val(roomratenet.toFixed(2));
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
                                                                                                        <div id="okbtnlabel${rowindex}" class="text-center" okbtnlabel>
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
                            pushNotify('error', 'Walkin Form', 'Please Fill Identity Details', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                            setTimeout(() => {
                                $('#idType').focus();
                                $('#idType').css({
                                    'border-color': 'red',
                                    'border-width': '2px'
                                });
                                $('#idNumber').css('border-color', '');
                            }, 1000);
                        } else if (grcmandatory == 'Y' && idType == '') {
                            $('#guestinfobutton').click();
                            pushNotify('error', 'Walkin Form', 'Please Select ID Type', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                            setTimeout(() => {
                                $('#idType').focus();
                                $('#idType, #idNumber').css({
                                    'border-color': 'red',
                                    'border-width': '2px'
                                });
                            }, 1000);
                        } else if (grcmandatory == 'Y' && idNumber == '') {
                            $('#guestinfobutton').click();
                            pushNotify('error', 'Walkin Form', 'Please Enter ID Number', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                            setTimeout(() => {
                                $('#idNumber').focus();
                                $('#idNumber').css({
                                    'border-color': 'red',
                                    'border-width': '2px'
                                });
                                $('#idType').css('border-color', '');
                            }, 1000);
                        } else if (grcmandatory == 'Y' && idType != '' && idNumber != '') {
                            formsubmit('#walkinform', '#walkinsubmit', 'Walkin', "{{ fomparameter()->pageopenwalkin }}", 'walkinsubmit');
                        } else if (grcmandatory == 'N') {
                            formsubmit('#walkinform', '#walkinsubmit', 'Walkin', "{{ fomparameter()->pageopenwalkin }}", 'walkinsubmit');
                        }
                    }
                };
                enviroxhr.send();
            });

            let offsetX, offsetY, isDragging = false;
            $('.table-findhistory').on('mousedown', function(e) {
                isDragging = true;
                offsetX = e.clientX - $(this).offset().left;
                offsetY = e.clientY - $(this).offset().top;
            });

            $(document).on('mousemove', function(e) {
                if (isDragging) {
                    $('.table-findhistory').css({
                        left: e.clientX - offsetX,
                        top: e.clientY - offsetY
                    });
                }
            });

            $(document).on('mouseup', function() {
                isDragging = false;
            });

            $(document).on('input', '#findname', function() {
                $('#findnum').val('');
                let tbody = $('#findhistory tbody');
                tbody.empty();
                $('.table-findhistory').addClass('none');
            });
            $(document).on('input', '#findnum', function() {
                $('#findname').val('');
                let tbody = $('#findhistory tbody');
                tbody.empty();
                $('.table-findhistory').addClass('none');
            });

            var historydata = [];
            $('#findby').click(function() {
                const findname = $('#findname').val();
                const findnum = $('#findnum').val();
                const sendfor = findname || findnum;
                const nameornum = findname ? 'name' : 'number';
                let tbody = $('#findhistory tbody');
                tbody.empty();
                let guesthistoryxhr = new XMLHttpRequest();
                guesthistoryxhr.open('POST', '/guesthistory', true);
                guesthistoryxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                guesthistoryxhr.onreadystatechange = function() {
                    if (guesthistoryxhr.status === 200 && guesthistoryxhr.readyState === 4) {
                        let results = JSON.parse(guesthistoryxhr.responseText);
                        if (results.error == 'No data found') {
                            $('.table-findhistory').addClass('none');
                            pushNotify('error', 'Guest History', results.error, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                        } else {
                            let sn = 1;
                            historydata = results;
                            $('.table-findhistory').removeClass('none');
                            let docid;
                            let groupedData = {};

                            results.forEach(row => {
                                if (!groupedData[row.docid]) {
                                    groupedData[row.docid] = {
                                        sn: sn++,
                                        name: row.name,
                                        mobile_no: row.mobile_no ?? '',
                                        roomcatname: row.roomcatname,
                                        roomno: row.roomno,
                                        chkindate: dmy(row.chkindate),
                                        adult: row.adult ?? 0,
                                        nodays: row.nodays ?? 0,
                                        rrtaxinc: row.rrtaxinc == 'N' ? 'No' : 'Yes',
                                        planname: row.planname ?? '',
                                        planamt: row.planamt ?? 0.00
                                    };
                                } else {}
                            });

                            let data = '';
                            $('#formswipecard').modal('hide');
                            pushNotify('info', 'Guest History', `${Object.keys(groupedData).length} Rows Found!`, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                            Object.keys(groupedData).forEach(docid => {
                                let row = groupedData[docid];
                                data += `<tr class="guestidhistory" data-id="${docid}">`;
                                data += `<td>${row.sn}</td>`;
                                data += `<td>${row.name}</td>`;
                                data += `<td>${row.mobile_no}</td>`;
                                data += `<td>${row.roomcatname}</td>`;
                                data += `<td>${row.roomno}</td>`;
                                data += `<td>${row.chkindate}</td>`;
                                data += `<td>${row.adult}</td>`;
                                data += `<td>${row.nodays}</td>`;
                                data += `<td>${row.rrtaxinc}</td>`;
                                data += `<td>${row.planname}</td>`;
                                data += `<td>${row.planamt}</td>`;
                                data += `</tr>`;
                            });

                            tbody.append(data);
                        }
                    }
                }
                guesthistoryxhr.send(`sendfor=${sendfor}&nameornum=${nameornum}&_token={{ csrf_token() }}`);
            });

            $('#findhistory tbody').on('click', '.guestidhistory', function() {
                let row = $(this).closest('tr');
                let docid = row.data('id');
                let n = historydata.filter(x => x.docid == docid);
                let tbodyfill = $('#gridtaxstructure tbody');
                tbodyfill.empty();
                let rdata = '';
                n.forEach((rows, index) => {
                    rdata += `<tr class="data-row">`;
                    rdata += `<td><select id="cat_code${rows.sno1}" name="cat_code${rows.sno1}" class="form-control sl cat_code_class" required>
                                                                            <option value="">Select</option>
                                                                            @foreach ($roomcat as $list)
                                                                                <option value="{{ $list->cat_code }}" ${rows.roomcat == '{{ $list->cat_code }}' ? 'selected' : ''}>{{ $list->name }}</option>
                                                                            @endforeach
                                                                            </select>
                                                                            <input type="hidden" class="form-control" name="planedit${rows.sno1}" id="planedit${rows.sno1}" readonly></td>`;
                    rdata += `<td><select id="planmaster${rows.sno1}" name="planmaster${rows.sno1}" class="form-control planmastclass sl">
                                                                                <option value="">Select</option>
                                                                                ${rows.plans ? rows.plans.map(plan =>
                        `<option value="${plan.code}" ${plan.code === rows.plancode ? 'selected' : ''}>${plan.name}</option>`
                    ).join('') : ''}
                                                                            </select></td>`;
                    rdata += `<td><select id="roommast${rows.sno1}" name="roommast${rows.sno1}" class="form-control room_mast sl" required>
                                                                                        <option value="">Select</option>
                                                                                        <option value="${rows.roomno}" ${rows.roomno != null ? 'selected' : ''}>${rows.roomno}</option>
                                                                                    </select></td>`;
                    rdata += `<td><select id="adult${rows.sno1}" name="adult${rows.sno1}" class="form-control sl" required>
                                                                                        <option value="">Select</option>
                                                                                        <option value="1" ${rows.adult == 1 ? 'selected' : ''}>1</option>
                                                                                        <option value="2" ${rows.adult == 2 ? 'selected' : ''}>2</option>
                                                                                        <option value="3" ${rows.adult == 3 ? 'selected' : ''}>3</option>
                                                                                        <option value="4" ${rows.adult == 4 ? 'selected' : ''}>4</option>
                                                                                        <option value="5" ${rows.adult == 5 ? 'selected' : ''}>5</option>
                                                                                    </select></td>`;
                    rdata += `<td><select id="child${rows.sno1}" name="child${rows.sno1}" class="form-control sl" required>
                                                                                        <option value="0">0</option>
                                                                                        <option value="1" ${rows.children == 1 ? 'selected' : ''}>1</option>
                                                                                        <option value="2" ${rows.children == 2 ? 'selected' : ''}>2</option>
                                                                                        <option value="3" ${rows.children == 3 ? 'selected' : ''}>3</option>
                                                                                        <option value="4" ${rows.children == 4 ? 'selected' : ''}>4</option>
                                                                                        <option value="5" ${rows.children == 5 ? 'selected' : ''}>5</option>
                                                                                    </select></td>`;
                    rdata += `<td><input type="number" name="rate${rows.sno1}" id="rate${rows.sno1}"
                                                                                    oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                                                                                    class="form-control ratechk sp" value="${rows.rackrate}" required></td>`;
                    rdata += `<td><select class="form-control taxchk sl" name="tax_inc${rows.sno1}" id="tax_inc${rows.sno1}">
                                                                                    <option value="">Select</option>
                                                                                    <option value="Y" ${rows.rrtaxinc == 'Y' ? 'selected' : ''}>Yes</option>
                                                                                    <option value="N" ${rows.rrtaxinc == 'N' ? 'selected' : ''}>No</option>
                                                                                    </select></td>`;
                    rdata += ` <td><input type="checkbox" class="leadercl" name="leader${row.sno1}" id="leader${row.sno1}">
                                                                                </td>`;
                    if (index > 0) {
                        rdata += `<td><img src="admin/icons/flaticon/remove.gif" alt="remove icon" class="remove-icon">
                                                                                    <img src="admin/icons/flaticon/copy.gif" alt="copy icon" class="copy-icon"></td>`;
                    } else {
                        rdata += '<td><img src="admin/icons/flaticon/copy.gif" alt="copy icon" class="copy-icon"></td>';
                    }
                });
                tbodyfill.append(rdata);
                let m = historydata.find(x => x.docid == docid);
                $('#formswipecard').modal('hide');
                $('.table-findhistory').addClass('none');
                let tbody = $('#findhistory tbody');
                tbody.empty();
                $('#rooms').val(m.roomcount);
                $('#company').val(m.company);
                $('#booking_source').val(m.booking_source);
                $('#bsource').val(m.busssource);
                $('#greetings').val(m.con_prefix);
                $('#greetingsguest').val(m.con_prefix);
                $('#name').val(m.name);
                $('#guestname').val(m.name);
                $('#mobile').val(m.mobile_no);
                $('#guestmobile').val(m.mobile_no);
                $('#email').val(m.email_id);
                $('#guestemail').val(m.email_id);
                $('#genderguest').val(m.gender);
                $('#cityname').val(m.city);
                $('#cityguest').val(m.city);
                const state = `<option value="${m.state_code}">${m.state_name}</option>`;
                $('#state').html(state);
                $('#stateguest').html(state);
                const country = `<option value="${m.country_code}">${m.country_name}</option>`;
                $('#country').html(country);
                $('#countryguest').html(country);
                const nationality = `<option value="${m.nationality}">${m.nationality}</option>`;
                $('#nationality').html(nationality);
                $('#nationalityother').html(nationality);
                $('#zipcode').val(m.zip_code);
                $('#zipguest').val(m.zip_code);
                $('#arrfrom').val(m.arrfrom);
                $('#destination').val(m.destination);
                $('#idType').val(m.id_proof);
                $('#idNumber').val(m.idproof_no);
                if (m.id_proof == 'Passport') {
                    $('#issuefielda').css('display', 'block');
                    $('#issuefieldb').css('display', 'block');
                    $('#issuefieldc').css('display', 'block');
                } else {
                    $('#issuefielda').css('display', 'none');
                    $('#issuefieldb').css('display', 'none');
                    $('#issuefieldc').css('display', 'none');
                }
                const issuingcountry = `<option value="${m.issuingcountrycode}">${m.issuingcountryname}</option>`;
                $('#issuingcity').val(m.issuingcitycode);
                $('#issuingcountry').html(issuingcountry);
                $('#expiryDate').val(m.expirydate);
                if (m.paymentMethod == 'Bill To Company') {
                    $('#billingfield').css('display', 'block');
                } else {
                    $('#billingfield').css('display', 'none');
                }
                $('#paymentMethod').val(m.paymentMethod);
                $('#billingAccount').val(m.billingAccount);
                $('#birthDate').val(m.dob);
                $('#purpofvisit').val(m.purvisit);
                $('#vipStatus').val(m.vipStatus);
                $('#weddingAnniversary').val(m.anniversary);
                $('#marital_status').val(m.marital_status);
                $('#rodisc').val(m.guestrodisc);
                $('#rsdisc').val(m.guestrsdisc);
                if (m.travelmode == 'By Car') {
                    $('#vehiclediv').css('display', 'block');
                } else {
                    $('#vehiclediv').css('display', 'none');
                }
                $('#travelmode').val(m.travelmode);
                $('#vehiclenum').val(m.vehiclenum);
                if (m.pic_path != null) {
                    $('#profileimagepreview').attr('src', `{{ asset('storage/walkin/profileimage/${m.pic_path}') }}`);
                    $('#existing_profileimage').val(m.pic_path);
                }
                if (m.idpic_path != null) {
                    $('#identityimagepreview').attr('src', `{{ asset('storage/walkin/identityimage/${m.idpic_path}') }}`);
                    $('#existing_identityimage').val(m.idpic_path);
                }
                $('#guestfetch').val('Y');
                $('#guestfetchdocid').val(docid);
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            function fetchEmptyRooms(checkindate, checkoutdate, index) {
                $.ajax({
                    url: "{{ url('fetchallemptyrooms') }}",
                    method: "POST",
                    data: {
                        checkindate: checkindate,
                        checkoutdate: checkoutdate
                    },
                    success: function(response) {
                        let roomlist = response;
                        let rdata = '<option value="">Select</option>';
                        response.forEach((tdata) => {
                            rdata += `<option data-catid="${tdata.room_cat}" value="${tdata.rcode}">${tdata.rcode}</option>`;
                        });
                        $(`#roommast${index}`).html(rdata);
                    },
                    error: function(error) {
                        console.error("Error fetching rooms:", error);
                    }
                });
            }

            let checkin = $('#checkindate').val();
            let checkout = $('#checkoutdate').val();

            fetchEmptyRooms(checkin, checkout, 1);

            $(document).on('change', '#checkindate', function() {
                fetchEmptyRooms($(this).val(), $('#checkoutdate').val(), 1);
            });

            $(document).on('change', '#checkoutdate', function() {
                fetchEmptyRooms($('#checkindate').val(), $(this).val(), 1);
            });

            $("#add_room").click(function(event) {
                event.preventDefault();
                const table = document.getElementById("gridtaxstructure");
                const newRow = table.insertRow(table.rows.length);
                newRow.classList.add('data-row');

                let rrinctaxdefault = $('#rrinctaxdefault').val();

                var cell1 = newRow.insertCell(0);
                var cell2 = newRow.insertCell(1);
                var cell3 = newRow.insertCell(2);
                var cell4 = newRow.insertCell(3);
                var cell5 = newRow.insertCell(4);
                var cell6 = newRow.insertCell(5);
                var cell7 = newRow.insertCell(6);
                var cell8 = newRow.insertCell(7);
                var cell9 = newRow.insertCell(8);

                const rowNumber = table.rows.length - 1;

                document.getElementById('rooms').value = rowNumber;

                let totalrooms = parseInt($('#totalrooms').val());

                $('#totalrooms').val(totalrooms + 1);

                fetchEmptyRooms($('#checkindate').val(), $('#checkoutdate').val(), rowNumber);

                cell1.innerHTML = `
                                                                <select id="cat_code${rowNumber}" name="cat_code${rowNumber}" class="form-control sl cat_code_class catselect" required>
                                                                    <option value="">Select</option>
                                                                    @foreach ($roomcat as $list)
                                                                        <option value="{{ $list->cat_code }}">{{ $list->name }}</option>
                                                                    @endforeach
                                                                </select><input type="hidden" class="form-control" name="planedit${rowNumber}" id="planedit${rowNumber}" readonly>`;

                cell2.innerHTML = `
                                                                <select id="roommast${rowNumber}" name="roommast${rowNumber}" class="form-control room_mast sl roomselect" required>
                                                                    <option value="">Select</option>
                                                                </select>`;

                cell3.innerHTML = `
                                                                <select id="adult${rowNumber}" name="adult${rowNumber}" class="form-control sl" required>
                                                                    <option value="">Select</option>
                                                                    <option value="1">1</option>
                                                                    <option selected value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4">4</option>
                                                                    <option value="5">5</option>
                                                                </select>`;

                cell4.innerHTML = `<select id="child${rowNumber}" name="child${rowNumber}" class="form-control sl" required>
                                                                    <option value="0">0</option>
                                                                    <option value="1">1</option>
                                                                    <option value="2">2</option>
                                                                </select>
                                                                `;

                cell5.innerHTML = `<select id="planmaster${rowNumber}" name="planmaster${rowNumber}" class="form-control planmastclass sl">
                                                                    <option value="">Select</option>
                                                                </select>
                                                                `;

                cell6.innerHTML = `
                                                                <input type="number" name="rate${rowNumber}" id="rate${rowNumber}"
                                                                oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                                                                class="form-control ratechk sp" required>`;
                cell7.innerHTML = `<select class="form-control taxchk sl" name="tax_inc${rowNumber}" id="tax_inc${rowNumber}">
                                                                <option value="">Select</option>
                                                                <option value="Y" ${rrinctaxdefault == 'Y' ? 'selected' : ''}>Yes</option>
                                                                <option value="N" ${rrinctaxdefault == 'N' ? 'selected' : ''}>No</option>
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

                        if (result.roominctaxeditable === 'N') {
                            var elements = document.getElementsByClassName('taxchk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].readOnly = true;
                            }
                        }

                        if (result.roominctaxeditable === 'N') {
                            var elements = document.getElementsByClassName('taxchk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].disabled = true;
                            }
                        }

                        // Don't touch it Or you will be in trouble😭

                    }
                };
                xhr.send();
            });
        });

        $(document).on('click', '.copy-icon', function() {
            var row = $(this).closest('tr');
            console.log(row.index() + 1);
            var nextRow = row.next('tr');

            if (nextRow.length > 0) {
                row.find('td').each(function(index) {
                    var cell = $(this);
                    var nextCell = nextRow.find('td').eq(index);
                    var input = cell.find('input');
                    var select = cell.find('option:selected');

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
                // console.log('index', index);
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

                document.getElementById('checkindate').value = result.ncur;
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const currentTime = `${hours}:${minutes}`;
                document.getElementById('checkintime').value = currentTime;
                document.getElementById('checkouttime').value = result.checkout;

                const checkinDate = new Date(result.ncur);
                const checkoutDate = new Date(checkinDate);
                checkoutDate.setDate(checkoutDate.getDate() + 1);
                const checkoutYear = checkoutDate.getFullYear();
                const checkoutMonth = (checkoutDate.getMonth() + 1).toString().padStart(2, '0');
                const checkoutDay = checkoutDate.getDate().toString().padStart(2, '0');
                const checkoutFormattedDate = `${checkoutYear}-${checkoutMonth}-${checkoutDay}`;
                document.getElementById('checkoutdate').value = checkoutFormattedDate;
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
                if (result.roominctaxeditable === 'N') {
                    var elements = document.getElementsByClassName('taxchk');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].disabled = true;
                    }
                }

            }
        };
        xhr.send();

        function updateGSTCode(selector, gstCodepId, gstCodeSpanId) {
            $(selector).on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var gstCodep = $(gstCodepId);
                var gstCodeSpan = $(gstCodeSpanId);

                if (selectedOption.data('gst') !== '') {
                    gstCodep.show();
                    gstCodeSpan.text(selectedOption.data('gst'));
                } else {
                    gstCodep.hide();
                    gstCodeSpan.text('');
                }
            });
        }

        updateGSTCode('#company', '#gstCodep', '#gstCode');
        updateGSTCode('#travel_agent', '#gstCodet', '#gstCodet');

        $(document).ready(function() {
            var canvas = document.getElementById('signatureCanvas');
            var ctx = canvas.getContext('2d');
            var isDrawing = false;
            var points = [];
            var paths = [];
            var colorPicker = document.getElementById('colorPicker');
            var imagePreview = document.getElementById('imagePreview');

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#000';

            function redrawAllPaths() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                paths.forEach(function(path) {
                    ctx.strokeStyle = path.color;
                    ctx.beginPath();
                    ctx.moveTo(path.points[0].x, path.points[0].y);

                    for (var i = 1; i < path.points.length - 2; i++) {
                        var xc = (path.points[i].x + path.points[i + 1].x) / 2;
                        var yc = (path.points[i].y + path.points[i + 1].y) / 2;
                        ctx.quadraticCurveTo(path.points[i].x, path.points[i].y, xc, yc);
                    }

                    if (path.points.length >= 2) {
                        ctx.quadraticCurveTo(
                            path.points[path.points.length - 2].x,
                            path.points[path.points.length - 2].y,
                            path.points[path.points.length - 1].x,
                            path.points[path.points.length - 1].y
                        );
                    }

                    ctx.stroke();
                });
            }

            function getCoordinates(e) {
                var rect = canvas.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var pressure = e.pressure || 0.5;
                return {
                    x: x,
                    y: y,
                    pressure: pressure
                };
            }

            function startDrawing(e) {
                e.preventDefault();
                isDrawing = true;
                points = [];

                var coord = getCoordinates(e);
                points.push(coord);
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();

                var coord = getCoordinates(e);
                points.push(coord);

                redrawAllPaths();

                ctx.strokeStyle = colorPicker.value;
                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);

                for (var i = 1; i < points.length - 2; i++) {
                    var xc = (points[i].x + points[i + 1].x) / 2;
                    var yc = (points[i].y + points[i + 1].y) / 2;
                    ctx.quadraticCurveTo(points[i].x, points[i].y, xc, yc);
                }

                if (points.length >= 2) {
                    ctx.quadraticCurveTo(
                        points[points.length - 2].x,
                        points[points.length - 2].y,
                        points[points.length - 1].x,
                        points[points.length - 1].y
                    );
                }

                ctx.stroke();
            }

            function stopDrawing(e) {
                if (!isDrawing) return;
                isDrawing = false;

                if (points.length > 0) {
                    paths.push({
                        color: colorPicker.value,
                        points: points
                    });
                }
                points = [];
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            canvas.addEventListener('touchstart', function(e) {
                e.preventDefault();
                startDrawing(e.touches[0]);
            }, {
                passive: false
            });

            canvas.addEventListener('touchmove', function(e) {
                e.preventDefault();
                draw(e.touches[0]);
            }, {
                passive: false
            });

            canvas.addEventListener('touchend', stopDrawing);

            canvas.addEventListener('pointerdown', function(e) {
                if (e.pointerType === 'pen') {
                    ctx.lineWidth = e.pressure * 3;
                }
            });

            $('#openModalBtn').click(function() {
                $('#myModal').modal('show');
                $('#imagePreview').hide();
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                paths = [];
            });

            $('.btn-close').click(function() {
                $('#myModal').modal('hide');
            });

            $('#clearCanvasBtn').click(function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                paths = [];
                points = [];
                $('#signimage').val('');
            });

            $('#previewBtn').click(function() {
                var dataUrl = canvas.toDataURL('image/png');
                imagePreview.src = dataUrl;
                $('#imagePreview').show();
                $('#myModal').modal('hide');
            });

            $('#downloadBtn').click(function() {
                var dataUrl = canvas.toDataURL('image/png');
                imagePreview.src = dataUrl;
                $('#imagePreview').show();
                $('#myModal').modal('hide');
                var minX = Math.min(...paths.flatMap(path => path.points.map(point => point.x)));
                var minY = Math.min(...paths.flatMap(path => path.points.map(point => point.y)));
                var maxX = Math.max(...paths.flatMap(path => path.points.map(point => point.x)));
                var maxY = Math.max(...paths.flatMap(path => path.points.map(point => point.y)));

                var width = maxX - minX;
                var height = maxY - minY;
                var newCanvas = document.createElement('canvas');
                newCanvas.width = width;
                newCanvas.height = height;
                var newCtx = newCanvas.getContext('2d');

                var scaleX = width / (maxX - minX);
                var scaleY = height / (maxY - minY);

                newCtx.lineWidth = ctx.lineWidth * Math.max(scaleX, scaleY);

                newCtx.setTransform(scaleX, 0, 0, scaleY, -minX * scaleX, -minY * scaleY);

                paths.forEach(function(path) {
                    newCtx.strokeStyle = path.color;
                    newCtx.beginPath();
                    newCtx.moveTo(path.points[0].x, path.points[0].y);

                    for (var i = 1; i < path.points.length - 2; i++) {
                        var xc = (path.points[i].x + path.points[i + 1].x) / 2;
                        var yc = (path.points[i].y + path.points[i + 1].y) / 2;
                        newCtx.quadraticCurveTo(path.points[i].x, path.points[i].y, xc, yc);
                    }

                    if (path.points.length >= 2) {
                        newCtx.quadraticCurveTo(
                            path.points[path.points.length - 2].x,
                            path.points[path.points.length - 2].y,
                            path.points[path.points.length - 1].x,
                            path.points[path.points.length - 1].y
                        );
                    }

                    newCtx.stroke();
                });

                var dataUrl = newCanvas.toDataURL('image/png');
                $('#signimage').val(dataUrl);
                $('#myModal').modal('hide');

            });
        });


        $(document).ready(function() {
            $('#stay_days').on('input', function() {
                const checkinDate = new Date($('#checkindate').val());
                const stayDays = +$('#stay_days').val();
                const checkoutDateInput = $('#checkoutdate');

                if (!isNaN(stayDays)) {
                    checkinDate.setDate(checkinDate.getDate() + stayDays);
                    checkoutDateInput.val(checkinDate.toISOString().split('T')[0]);
                }
            });
        });

        function validateDates() {
            const checkinDateInput = document.getElementById('checkindate');
            const checkoutDateInput = document.getElementById('checkoutdate');
            const dayDifferenceInput = document.getElementById('stay_days');
            const dateError = document.getElementById('date-error');

            const checkinDate = new Date(checkinDateInput.value);

            const checkoutDate = new Date(checkinDate);
            checkoutDate.setDate(checkinDate.getDate() + 1);

            checkoutDateInput.value = formatDate(checkoutDate);

            const timeDifference = Math.abs(checkoutDate - checkinDate);
            const dayDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
            dayDifferenceInput.value = dayDifference;

            if (checkoutDate > checkinDate) {
                dateError.textContent = "";
            }
        }
    </script>
@endsection
