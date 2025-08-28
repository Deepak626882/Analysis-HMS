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
                            {{-- action="{{ route('reservationsubmit') }}" --}}
                            <form class="walkin-form" id="reservationform"
                                name="reservationform" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $totalroom }}" name="totalroom" id="totalroom">
                                <input type="hidden" name="totalrooms" value="1" id="totalrooms">
                                <div class="row">
                                    <table class="table walkin-table table-responsive">
                                        <thead>
                                            <th>Total Room</th>
                                            <th>Ref. Booking Id</th>
                                            <th>Remarks</th>
                                            <th>Pick Up/Drop <i class="fa-solid fa-truck-pickup"></i></th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input max="{{ $totalroom }}" value="1" id="rooms" style="text-align: center;" type="number" name="rooms"
                                                        class="form-control low fiveem" placeholder="1">
                                                </td>
                                                <td><input type="text" name="ref_booking_id" class="form-control low"
                                                        placeholder="Ref. Booking Id"></td>
                                                <td><input placeholder="Remarks" class="form-control" name="remarkmain" id="remarkmain" type="text"></td>
                                                <td><input placeholder="Pickup/Drop" class="form-control" type="text" name="pickupdrop" id="pickupdrop"></td>
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
                                                <th>Booked By</th>
                                                <th>Reservation Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select id="company" name="company" class="form-control low">
                                                        <option value="">Select</option>
                                                        @foreach ($company as $list)
                                                            <option value="{{ $list->sub_code }}" data-gst="{{ $list->gstin }}">
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
                                                        <option value="">Select</option>
                                                        <option value="Booking Engine" selected>Booking Engine</option>
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
                                                            <option value="{{ $list->sub_code }}">{{ $list->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="booked_by" class="form-control low"
                                                        placeholder="Booked By">
                                                </td>
                                                <td>
                                                    <select id="reservation_status" name="reservation_status"
                                                        class="form-control low" required>
                                                        <option value="">Select</option>
                                                        <option value="Confirm" selected>Confirm</option>
                                                        <option value="Tentative">Tentative</option>
                                                        <option value="Waiting">Waiting</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="complimentry"
                                            id="complimentry">
                                        <label class="form-check-label" for="complimentry">Complimentry Room</label>
                                    </div>

                                    <table class="table-hover reservation-multi table-responsive" id="gridtaxstructure">
                                        <thead>
                                            <th>Arr. Date</th>
                                            <th>Time</th>
                                            <th>Days</th>
                                            <th>Dep. Date</th>
                                            <th>Time</th>
                                            <th>Room Type</th>
                                            <th>No Of Rooms</th>
                                            <th>Plans</th>
                                            <th>Room</th>
                                            <th>Adult</th>
                                            <th>Child</th>
                                            <th>Rate Rs.</th>
                                            <th>Tax Inc.</th>
                                            <th id="thlast">Action</th>
                                        </thead>
                                        <tbody>
                                            <tr class="data-row">
                                                <td>
                                                    <input type="date" onchange="validateDates2()"
                                                        onfocus="this.showPicker()" name="arrivaldate1"
                                                        class="form-control arrivaldate low alibaba" value="{{ $ncurdate }}"
                                                        id="arrivaldate1" required>
                                                </td>
                                                <td>
                                                    <input style="width: 5.9em;" onfocus="this.showPicker()"
                                                        value="{{ $enviro_formdata->checkout }}" type="time"
                                                        id="arrivaltime1" name="arrivaltime1"
                                                        class="form-control arrivaltime low" required>
                                                </td>
                                                <td>
                                                    <input style="width: 4rem;" type="number"
                                                        oninput="ValidateNum(this, '1', '100', '3')" name="stay_days1"
                                                        id="stay_days1" class="form-control staydays stays" value="1"
                                                        required>
                                                </td>
                                                <td>
                                                    <input onchange="validateDates2()" onfocus="this.showPicker()"
                                                        type="date" value="{{ $checkoutdate }}" name="checkoutdate1"
                                                        class="form-control low alibaba" id="checkoutdate1" required>
                                                    <span class="text-danger alert-light checkoutdate absolute-element"
                                                        id="date-error1"></span>
                                                </td>

                                                <td>
                                                    <input style="width: 5.9em;" onfocus="this.showPicker()" type="time"
                                                        value="{{ $enviro_formdata->checkout }}" id="checkouttime1"
                                                        name="checkouttime1" class="form-control low" required>
                                                </td>
                                                <td>
                                                    <select id="cat_code1" name="cat_code1"
                                                        class="form-control sl cat_code_class" required>
                                                        <option value="">Select</option>
                                                        @foreach ($roomcat as $list)
                                                            <option data-maxroom="{{ $list->norooms }}" value="{{ $list->cat_code }}">{{ $list->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" class="form-control" name="planedit1" id="planedit1" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" autocomplete="off" aria-autocomplete="none" class="form-control foureem roomcount" value="1" name="roomcount1" id="roomcount1">
                                                </td>
                                                <td><select id="planmaster1" name="planmaster1" class="form-control planmastclass sl" {{ $channelenviro->checkyn == 'Y' ? 'required' : '' }}>
                                                        <option value="">Select</option>
                                                    </select></td>
                                                <td><select id="roommast1" name="roommast1" class="form-control roommastr sl">
                                                        <option value="">Select</option>
                                                    </select></td>
                                                <td><select style="width: 3.5em;" id="adult1" name="adult1"
                                                        class="form-control sl" required>
                                                        <option value="">Select</option>
                                                        <option value="1">1</option>
                                                        <option selected value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select></td>
                                                <td><select style="width: 3.5em;" id="child1" name="child1"
                                                        class="form-control sl" required>
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                    </select></td>
                                                <td><input style="width:8em;" placeholder="Enter Rate" type="number"
                                                        name="rate1" id="rate1"
                                                        oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                                                        class="form-control ratechk sp" required></td>
                                                <td><select style="width: 4em;" class="form-control taxchk sl"
                                                        name="tax_inc1" id="tax_inc1">
                                                        <option value="">Select</option>
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                </td>
                                                <td>
                                                    <img src="admin/icons/flaticon/copy.gif" alt="copy icon"
                                                        class="copy-icon">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="button-container custom-range">
                                        <button type="button" name="add_room" id="add_room"
                                            class="btn radiusbtn mb-1 btn-outline-success">Add Room <i
                                                class="fa-solid fa-building"></i></button>
                                    </div>
                                </div>

                                {{-- <div class="form-group mt-4 form-check">
                                <input type="checkbox" onchange="HandleGuestList('guestlist')" class="form-check-input"
                                    name="guestlist" id="guestlist">
                                <label class="form-check-label" for="guestlist">Guest List</label>
                            </div> --}}

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
                                                <input type="text" name="name" placeholder="Full Name" maxlength="25"
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
                                                                                src="admin/images/preview.gif"
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
                                                                                <input
                                                                                    minlength="10" maxlength="10"
                                                                                    type="tel" class="form-control"
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
                                                                                            src="admin/images/preview.gif"
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
                                                                                                <option
                                                                                                    value="Voter Id">
                                                                                                    Voter Id
                                                                                                </option>
                                                                                                <option
                                                                                                    value="Green Card">
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
                                                                                                {{ $list->name }}</option>
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
                                            <input type="tel"
                                                name="mobile" placeholder="Mobile" minlength="10" maxlength="10" id="mobile"
                                                class="form-control" {{ $enviro_formdata->grcmandatory == 'Y' ? 'required' : '' }}>
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
                                                    <input type="text" placeholder="Enter Address 1" class="form-control" name="address1" id="address1">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="Enter Address 2" class="form-control" name="address2" id="address2">
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

                                <div class="form-group form-check">
                                    <input type="checkbox" checked class="form-check-input" name="advdeposit" id="advdeposit">
                                    <label class="form-check-label" for="advdeposit"><i class="fa-solid fa-money-bill-transfer"></i> Advance Deposit?</label>
                                </div>

                                <div class="col-7 mt-4 mb-4 ml-auto">
                                    <button type="button" class="btn btn-danger" onclick="location.reload()">Cancel <i
                                            class="fa-solid fa-xmark"></i></button>
                                    <button type="submit" name="reservesubmit" id="reservesubmit"
                                        class="btn btn-primary">Reserve <i class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
    <script>
        $(document).ready(function() {

            $('#reservationform').on('submit', function(event) {
                event.preventDefault();
                formsubmit('#reservationform', '#reservesubmit', 'Reservation', 'reservationlist', 'reservationsubmit');
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
                                    let wholedata = `<div id=plandiv${rowindex} class="hidedisp">
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
            $(document).on('change', `#planmaster1`, function() {
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

            $("#cat_code1").on('change', function() {
                var cid = this.value;
                document.getElementById('roommast1').value = '';
                document.getElementById('planmaster1').value = '';
                let checkindate = $('#arrivaldate1').val();
                let checkoutdate = $('#checkoutdate1').val();
                var xhrRooms = new XMLHttpRequest();
                xhrRooms.open('POST', '/getrooms', true);
                xhrRooms.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhrRooms.onreadystatechange = function() {
                    if (xhrRooms.readyState === 4 && xhrRooms.status === 200) {
                        var result = xhrRooms.responseText;
                        var roomSelect = document.getElementById('roommast1');
                        roomSelect.innerHTML = result;
                    }
                };
                xhrRooms.send(`cid=${cid}&checkindate=${checkindate}&checkoutdate=${checkoutdate}&_token={{ csrf_token() }}`);
                var xhrPlans = new XMLHttpRequest();
                xhrPlans.open('POST', '/getplans', true);
                xhrPlans.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhrPlans.onreadystatechange = function() {
                    if (xhrPlans.readyState === 4 && xhrPlans.status === 200) {
                        var result = xhrPlans.responseText;
                        var planSelect = document.getElementById(`planmaster1`);
                        planSelect.innerHTML = result;
                    }
                };
                xhrPlans.send(`cid=${cid}&_token={{ csrf_token() }}`);
            });

            let previouslySelectedRooms = {};

            $(document).on('change', '.cat_code_class', function() {
                var cid = this.value;
                var rowNumber = this.id.replace('cat_code', '');

                $(`#roommast${rowNumber}`).val('');
                $(`#planmaster${rowNumber}`).val('');

                let checkindate = $(`#arrivaldate${rowNumber}`).val();
                let checkoutdate = $(`#checkoutdate${rowNumber}`).val();

                var $select = $(this);

                var xhrRooms = new XMLHttpRequest();
                xhrRooms.open('POST', '/getrooms', true);
                xhrRooms.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhrRooms.onreadystatechange = function() {
                    if (xhrRooms.readyState === 4 && xhrRooms.status === 200) {
                        var result = xhrRooms.responseText;
                        var tempDiv = $('<div>').html(result);
                        var totalrooms = tempDiv.find('option').length - 1;
                        $select.find('option:selected').attr('data-maxroom', totalrooms);

                        tempDiv.find('option').each(function() {
                            var roomNo = $(this).val();
                            var catCode = $(this).data('catcode');

                            if (previouslySelectedRooms[catCode] && previouslySelectedRooms[catCode].some(item => item.roomNo === roomNo)) {
                                $(this).remove();
                            }
                        });

                        var roomSelect = $(`#roommast${rowNumber}`);
                        roomSelect.html(tempDiv.html());
                    }
                };

                xhrRooms.send(`cid=${cid}&checkindate=${checkindate}&checkoutdate=${checkoutdate}&_token={{ csrf_token() }}`);

                var xhrPlans = new XMLHttpRequest();
                xhrPlans.open('POST', '/getplans', true);
                xhrPlans.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhrPlans.onreadystatechange = function() {
                    if (xhrPlans.readyState === 4 && xhrPlans.status === 200) {
                        var result = xhrPlans.responseText;
                        var planSelect = $(`#planmaster${rowNumber}`);
                        planSelect.html(result);
                    }
                };
                xhrPlans.send(`cid=${cid}&_token={{ csrf_token() }}`);
            });

            $(document).on('change', '[id^=roommast]', function() {
                var roomNo = $(this).val();
                var catCode = $(this).find('option:selected').data('catcode');
                var rowNumber = this.id.replace('roommast', '');

                if (!previouslySelectedRooms[catCode]) {
                    previouslySelectedRooms[catCode] = [];
                }

                previouslySelectedRooms[catCode] = previouslySelectedRooms[catCode].filter(item =>
                    item.rowNumber !== rowNumber);

                if (roomNo) {
                    previouslySelectedRooms[catCode].push({
                        roomNo: roomNo,
                        rowNumber: rowNumber
                    });
                }
            });

            $(document).on('input', '#rooms', function() {
                let totalroom = parseInt($('#totalroom').val(), 10) || 0;
                let currentValue = parseInt($(this).val(), 10) || 0;

                if (currentValue > totalroom) {
                    $(this).val('1');
                    pushNotify('info', 'Reservation', 'Value is greater than total rooms', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                    return;
                }

                let rooms = currentValue;
                let tableBody = $('#gridtaxstructure tbody');
                tableBody.find('tr:gt(0)').remove();

                for (let i = 1; i < rooms; i++) {
                    $('#add_room').trigger('click');
                }
            });

            $(document).on('input', '.roomcount', function() {
                let currow = $(this);
                let index = currow.closest('tr').index() + 1;
                let maxallow = $(`#cat_code${index}`).find('option:selected').data('maxroom');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.post('/getmaxroomallow', {
                    catcode: $(`#cat_code${index}`).val(),
                    checkindate: $(`#arrivaldate${index}`).val(),
                    checkoutdate: $(`#checkoutdate${index}`).val()
                }, function(response) {
                    maxallow = response.maxallow;
                }).fail(function(error) {
                    console.error('Error:', error);
                });

                if (currow.val() > maxallow) {
                    currow.val('1');
                    pushNotify('info', 'Reservation', 'Value is greater than total rooms of category', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
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
                var cell10 = newRow.insertCell(9);
                var cell11 = newRow.insertCell(10);
                var cell12 = newRow.insertCell(11);
                var cell13 = newRow.insertCell(12);
                var cell14 = newRow.insertCell(13);
                var cell15 = newRow.insertCell(14);
                const rowNumber = table.rows.length - 1;
                cell15.setAttribute("id", "remaktd" + rowNumber);

                document.getElementById('rooms').value = rowNumber;

                let totalrooms = parseInt($('#totalrooms').val());

                $('#totalrooms').val(totalrooms + 1);

                cell1.innerHTML = `<input onfocus="this.showPicker()" type="date" name="arrivaldate${rowNumber}"
                class="form-control arrival-date low alibaba"
                value="{{ $ncurdate }}" id="arrivaldate${rowNumber}" required>`;

                cell2.innerHTML = `<input style="width: 5.9em;" onfocus="this.showPicker()" value="{{ $enviro_formdata->checkout }}" type="time" id="arrivaltime${rowNumber}"
                name="arrivaltime${rowNumber}" class="form-control arrivaltime low" required>`;

                cell3.innerHTML = `<input onfocus="this.showPicker()" style="width: 4rem;" type="number"
                oninput="ValidateNum(this, '1', '100', '3')" name="stay_days${rowNumber}"
                id="stay_days${rowNumber}" class="form-control stays" value="1" required>`;

                cell4.innerHTML = `<input onfocus="this.showPicker()" type="date" value="{{ $checkoutdate }}" name="checkoutdate${rowNumber}"
                class="form-control low alibaba" placeholder="2023-10-26"
                id="checkoutdate${rowNumber}" required>
                <span class="text-danger alert-light absolute-element" id="date-error${rowNumber}"></span>`;

                cell5.innerHTML = `<input style="width: 5.9em;" onfocus="this.showPicker()" type="time" value="{{ $enviro_formdata->checkout }}" id="checkouttime${rowNumber}" name="checkouttime${rowNumber}"
                class="form-control low" required>`;

                cell6.innerHTML = `
            <select id="cat_code${rowNumber}" name="cat_code${rowNumber}" class="form-control sl cat_code_class" required>
                    <option value="">Select</option>
                    @foreach ($roomcat as $list)
                        <option data-maxroom="{{ $list->norooms }}" value="{{ $list->cat_code }}">{{ $list->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" class="form-control" name="planedit${rowNumber}" id="planedit${rowNumber}" readonly>`;

                cell7.innerHTML = `<input type="text" class="form-control foureem roomcount" value="1" name="roomcount${rowNumber}" id="roomcount${rowNumber}">`;

                cell8.innerHTML = `
                <select id="planmaster${rowNumber}" name="planmaster${rowNumber}" class="form-control planmastclass sl" {{ $channelenviro->checkyn == 'Y' ? 'required' : '' }}>
                    <option value="">Select</option>
                </select> `;

                cell9.innerHTML = `
                <select id="roommast${rowNumber}" name="roommast${rowNumber}" class="form-control roommastr sl">
                    <option value="">Select</option>
                </select>`;

                cell10.innerHTML = `
                <select style="width: 3.5em;" id="adult${rowNumber}" name="adult${rowNumber}" class="form-control sl" required>
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option selected value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>`;

                cell11.innerHTML = `
                <select style="width: 3.5em;" id="child${rowNumber}" name="child${rowNumber}" class="form-control sl" required>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>`;

                cell12.innerHTML = `
                <input placeholder="Enter Rate" style="width:8em;" type="number" name="rate${rowNumber}" id="rate${rowNumber}"
                oninput="checkNumMax(this, 10); handleDecimalInput(event);"
                class="form-control ratechk sp" required>`;
                cell13.innerHTML = `<select style="width:4em;" class="form-control taxchk sl" name="tax_inc${rowNumber}" id="tax_inc${rowNumber}">
            <option value="">Select</option>
            <option value="Y">Yes</option>
            <option value="N">No</option>`;
                cell14.innerHTML = `<img src="admin/icons/flaticon/remove.gif" alt="remove icon" class="remove-icon">
                <img src="admin/icons/flaticon/copy.gif" alt="copy icon" class="copy-icon">`;
                cell15.innerHTML = ``;
                $(document).on('change', `#planmaster${rowNumber}`, function() {
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

                $(document).on('change', `#arrivaldate${rowNumber}, #checkoutdate${rowNumber}`, function() {
                    const checkinDateInput = $(`#arrivaldate${rowNumber}`);
                    const checkoutDateInput = $(`#checkoutdate${rowNumber}`);
                    const dayDifferenceInput = $(`#stay_days${rowNumber}`);
                    const dateError = $(`#date-error${rowNumber}`);

                    const checkinDate = new Date(checkinDateInput.val());

                    let checkoutDate = new Date(checkinDate);
                    checkoutDate.setDate(checkinDate.getDate() + 1);

                    checkoutDateInput.val(formatDate(checkoutDate));

                    const timeDifference = Math.abs(checkoutDate - checkinDate);
                    const dayDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                    dayDifferenceInput.val(dayDifference);

                    dateError.text("");
                });

                $(`#stay_days${rowNumber}`).on('input', function() {
                    const checkinDate = new Date($(`#arrivaldate${rowNumber}`).val());
                    let stayDays = parseInt($(`#stay_days${rowNumber}`).val(), 10);
                    const checkoutDateInput = $(`#checkoutdate${rowNumber}`);

                    if (isNaN(stayDays)) stayDays = 1;

                    checkinDate.setDate(checkinDate.getDate() + stayDays);
                    checkoutDateInput.val(checkinDate.toISOString().split('T')[0]);
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

                        if (result.rrinctaxdefault === 'Y') {
                            var elements = document.getElementsByClassName('taxchk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].value = 'Y';
                            }
                        } else if (result.rrinctaxdefault === 'N') {
                            var elements = document.getElementsByClassName('taxchk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].value = 'N';
                            }
                        } else {
                            var elements = document.getElementsByClassName('taxchk');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].value = '';
                            }
                        }

                    }
                };
                xhr.send();

            });
        });

        $(document).on('click', '.copy-icon', function() {
            var row = $(this).closest('tr');
            var iindex = row.index() + 1;
            var nextRow = row.next('tr');

            if (nextRow.length > 0) {

                let plandiv = $(document).find(`div#plandiv${iindex}`);
                if (plandiv.length > 0) {
                    let newIndex = iindex + 1;

                    let clonedPlandiv = $('<div>').attr('id', `plandiv${newIndex}`).html(plandiv.html());

                    clonedPlandiv.find('table').attr('id', `planmasttable${newIndex}`);

                    clonedPlandiv.find('input, select, textarea').each(function() {
                        let $this = $(this);

                        let currentName = $this.attr('name');
                        let currentId = $this.attr('id');

                        if (currentName) {
                            let newName = currentName.replace(/\d+$/, newIndex);
                            $this.attr('name', newName);
                        }

                        if (currentId) {
                            let newId = currentId.replace(/\d+$/, newIndex);
                            $this.attr('id', newId);
                        }

                        if ($this.is('input[type="text"], input[type="number"], textarea, select')) {
                            $this.val($this.attr('value'));
                        }

                        if ($this.is('input[type="checkbox"], input[type="radio"]')) {
                            $this.prop('checked', $this.prop('checked'));
                        }
                    });

                    clonedPlandiv.find('div[id^="okbtnlabel"]').attr('id', `okbtnlabel${newIndex}`);
                    clonedPlandiv.find('button[id^="okbtnplan"]').attr('id', `okbtnplan${newIndex}`).attr('name', `okbtnplan${newIndex}`);
                    clonedPlandiv.find('button[id^="closebtnplan"]').attr('id', `closebtnplan${newIndex}`).attr('name', `closebtnplan${newIndex}`);
                    clonedPlandiv.find('div[id^="resizeHandle"]').attr('id', `resizeHandle${newIndex}`);

                    nextRow.append(clonedPlandiv);
                }

                row.find('td').each(function(index) {
                    if (index < 15) {
                        var cell = $(this);
                        var nextCell = nextRow.find('td').eq(index);

                        var input = cell.find('input');
                        var nextInput = nextCell.find('input');
                        if (input.length > 0 && nextInput.length > 0) {
                            nextInput.val(input.val());
                        }

                        var select = cell.find('select');
                        var nextSelect = nextCell.find('select');
                        if (select.length > 0 && nextSelect.length > 0) {
                            nextSelect.html(select.html());

                            var selectedOption = select.find('option:selected');

                            if (selectedOption.length > 0) {
                                var selectedValue = selectedOption.val();
                                var selectedText = selectedOption.text();

                                var matchedOption = nextSelect.find('option[value="' + selectedValue + '"]');

                                if (matchedOption.length === 0) {
                                    matchedOption = nextSelect.find('option:contains("' + selectedText + '")');
                                }

                                if (matchedOption.length > 0) {
                                    matchedOption.prop('selected', true);
                                }
                            }
                        }
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

            row.remove();

            totalClonedCount--;
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

                if (result.arrdatetimeedit === 'N') {
                    document.getElementsByClassName('arrival-date').readOnly = true;
                    document.getElementsByClassName('arrivaltime').readOnly = true;
                } else {
                    document.getElementsByClassName('arrival-date').readOnly = false;
                    document.getElementsByClassName('arrivaltime').readOnly = false;
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

                if (result.rrinctaxdefault === 'Y') {
                    var elements = document.getElementsByClassName('taxchk');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].value = 'Y';
                    }
                } else if (result.rrinctaxdefault === 'N') {
                    var elements = document.getElementsByClassName('taxchk');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].value = 'N';
                    }
                } else {
                    var elements = document.getElementsByClassName('taxchk');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].value = '';
                    }
                }

            }
        };
        xhr.send();

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

        $(document).ready(function() {
            $('#stay_days1').on('input', function() {
                const checkinDate = new Date($('#arrivaldate1').val());
                const stayDays = +$('#stay_days1').val();
                const checkoutDateInput = $('#checkoutdate1');

                if (!isNaN(stayDays)) {
                    checkinDate.setDate(checkinDate.getDate() + stayDays);
                    checkoutDateInput.val(checkinDate.toISOString().split('T')[0]);
                }
            });
        });

        function validateDates2() {
            const checkinDateInput = document.getElementsByName('arrivaldate1')[0];
            const checkoutDateInput = document.getElementsByName('checkoutdate1')[0];
            const dayDifferenceInput = document.getElementsByName('stay_days1')[0];
            const dateError = document.getElementById('date-error1');

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
