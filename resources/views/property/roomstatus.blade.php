@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div id="bookingmodal" class="bookingmodal">
            <h3>In House <span class="fa-2xs blinking-text ARDR">Room Status is a color coded indicator that shows
                    the status of the Rooms.</span></h3>
            <div class="booking-status">
                <div class="indicator confirmed">In House</div>
                <div class="indicator confirmreservation">Confirm Reservation</div>
                <div class="indicator billed">Billed</div>
                <div class="indicator delayed">Delayed</div>
                <div class="indicator outoforder">Out of Order</div>
                <div class="indicator maintainence">Maintainence</div>
                <div class="indicator management">Management</div>
                <div class="indicator marriage">Marriage</div>
                {{-- <div class="indicator stayover">Stayover</div>
            <div class="indicator dayusenormal">Dayuse</div>
            <div class="indicator checked-out">Checked Out</div>
            <div class="indicator due-out">Due Out</div>
            <div class="indicator inhouse">Inhouse</div>
            <div class="indicator dayuse">Day Use Reservation</div>
            <div class="indicator maintenance-block">Maintenance Block</div> --}}
            </div>
            {{-- <h3>Booking Indicators</h3>
        <div class="booking-indicators">
            <div class="indicator group-owner"><img alt="Analysishms" src="{{ asset('admin/icons/custom/king.svg') }}">
                Group Owner</div>
            <div class="indicator payment-pending"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/dollar.svg') }}"> Payment Pending</div>
            <div class="indicator single-lady"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/singlelady.svg') }}"> Single Lady</div>
            <div class="indicator split-reservation"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/split.svg') }}"> Split Reservation</div>
            <div class="indicator group-booking"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/groupbooking.svg') }}"> Group Booking</div>
            <div class="indicator stop-room-move"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/stoproom.svg') }}"> Stop Room Move</div>
            <div class="indicator vip-guest"><img alt="Analysishms" src="{{ asset('admin/icons/custom/star.svg') }}">
                VIP Guest</div>
        </div>
        <h3>Room Indicators</h3>
        <div class="room-indicators">
            <div class="indicator no-smoking"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/dhuanotallow.svg') }}"> No Smoking</div>
            <div class="indicator dirty"><img alt="Analysishms" src="{{ asset('admin/icons/custom/dirty.svg') }}"> Dirty
            </div>
            <div class="indicator work-order"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/workorder.svg') }}"> Work Order</div>
            <div class="indicator smoking"><img alt="Analysishms" src="{{ asset('admin/icons/custom/ciggerate.svg') }}">
                Smoking</div>
            <div class="indicator connected-rooms"><img alt="Analysishms"
                    src="{{ asset('admin/icons/custom/connroom.svg') }}"> Connected Rooms</div>
        </div>
        <h3>Inventory</h3>
        <div class="inventory">
            <div class="indicator unassigned-room">Unassigned Room</div>
            <div class="indicator unconfirmed-bookings">Unconfirm Bookings</div>
            <div class="indicator confirmed">Confirmed Reservation</div>
            <div class="indicator inventry">Inventry</div>
        </div> --}}
        </div>
        {{-- {{ $ncurdate }} --}}
        <input style="display: none;" type="date" id="fixncur" value="{{ $ncurdate }}">
        <input style="display: none;" type="date" id="sss" value="{{ date('Y-m-d', strtotime('-1 day', strtotime($ncurdate))) }}">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body roomstatus">

                        {{-- <div class="row"> --}}
                        {{-- <div class="col-md-8"> --}}
                        {{-- <div class="table-responsive"> --}}
                        {{-- {{ $roomtype = $roomcategorydata }} --}}

                        <form action="" method="post">
                            <input type="hidden" name="showList" value="">
                        </form>
                        <table style="background: #ffffff;margin: 0 0 -4px -14px;" class="table table-primary">
                            <thead>
                                <tr>
                                    <th>
                                        <input value="{{ date('Y-m-d', strtotime('-1 day', strtotime($ncurdate))) }}" class="form-control rhead" type="date"
                                            id="fromdate">
                                    </th>
                                    <th>
                                        <select class="form-control rhead" name="housekeepingr"
                                            id="housekeepingr">
                                            <option value="">House Keeping</option>
                                            @foreach ($housekeepingdata as $item)
                                                <option value="{{ $item->dcode }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <input type="search" placeholder="Search..." class="form-control rhead"
                                            name="roomsearch" id="roomsearch">
                                    </th>
                                    <th style="cursor: pointer;" id="infoicon"><i class="fa-regular fa-circle-question"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        {{-- <div
                                    style="position: fixed; top: 0; right: 0; padding: 10px; background-color: #f1f1f1;">
                                    <i class="fa-regular fa-circle-question"></i>
                                </div> --}}
                        {{--
                            </div>
                        </div>
                    </div> --}}

                        <table class="table-responsive table" id="dateTable"></table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeprofilemodal" tabindex="-1" aria-labelledby="changeprofilemodalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div style="width: 57rem;" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeprofilemodalLabel">Profile Change For: <span class="ADA"
                            id="profilechangespan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="changeprofilemodalLabel">Folio No.:
                        <span class="BANX" id="profilechangecode"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="changeprofileframe" src="" frameborder="0" style="width: 100%; height: 60rem;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ammendstaymodal" tabindex="-1" aria-labelledby="ammendstaymodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ammendstaymodalLabel">Ammend Stay For: <span class="ADA"
                            id="ammendstayspan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="ammendstaymodalLabel">Folio No.:
                        <span class="BANX" id="guestcode1"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="ammenstayiframe" src="" frameborder="0" style="width: 100%; height: 15em;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="guestledgermodal" tabindex="-1" aria-labelledby="guestledgermodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guestledgermodalLabel">Guest Ledger For: <span class="ADA"
                            id="guestledgerspan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="guestledgermodalLabel">Folio No.:
                        <span class="BANX" id="guestcode2"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="guestledgeriframe" src="" frameborder="0" style="width: 100%; height: 35em;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="roomchangemodal" tabindex="-1" aria-labelledby="roomchangemodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomchangemodalLabel">Room Change For: <span class="ADA"
                            id="roomchangespan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="roomchangemodalLabel">Folio No.:
                        <span style="display: none;" id="docidd"></span>
                        <span class="BANX" id="guestcode3"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="roomchangeiframe" src="" frameborder="0" style="width: 100%; height: 37em;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="advchargemodal" tabindex="-1" aria-labelledby="advchargemodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="advchargemodalLabel">Advance Change For: <span class="ADA"
                            id="advchargespan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="advchargemodalLabel">Folio No.:
                        <span style="display: none;" id="docidd"></span>
                        <span class="BANX" id="guestcode4"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="advchargeiframe" src="" frameborder="0" style="width: 100%; height: 37em;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="billsettlemodal" tabindex="-1" aria-labelledby="billsettlemodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="billsettlemodalLabel">Bill Settlement For: <span class="ADA"
                            id="billsettlespan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="billsettlemodalLabel">Folio No.:
                        <span style="display: none;" id="docidd"></span>
                        <span class="BANX" id="guestcode6"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="billsettleiframe" src="" frameborder="0" style="width: 100%; height: 37em;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="billprintmodal" tabindex="-1" aria-labelledby="billprintmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="billprintmodalLabel">Bill Print For: <span class="ADA"
                            id="billprintspan"></span></h5>
                    <h5 style="right: 3rem;" class="modal-title absolute-element" id="billprintmodalLabel">Folio No.:
                        <span class="BANX" id="guestcode5"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="billprintiframe" src="" frameborder="0" style="width: 100%; height: 35em;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#myloader').removeClass('none');
        setTimeout(() => {
            $('#myloader').addClass('none');
        }, 2000);
        let activePopup = null;

        $(document).ready(function() {
            function exportdateformat(formatteddate) {
                datearray = formatteddate;
            }

            $(document).on('click', '#roomcategorybtn', function() {
                $('#listroomcat').toggle();
            });

            $(document).on('change', '.roomcatcheckbox', function() {
                if (!$(this).is(':checked')) {
                    let valueToHide = $(this).val();
                    $('#dateTable tbody tr').each(function() {
                        if ($(this).data('value') == valueToHide) {
                            $(this).hide();
                        }
                    });
                } else {
                    let valueToHide = $(this).val();
                    $('#dateTable tbody tr').each(function() {
                        if ($(this).data('value') == valueToHide) {
                            $(this).show();
                        }
                    });
                }
            });

            $(document).on('click', '#infoicon', function() {
                let modal = $('#bookingmodal');
                if (modal.css('display') === 'none') {
                    modal.css('display', 'block');
                } else {
                    modal.css('display', 'none');
                }
            });

            function generateDateRow() {
                // This code only runs in production not in my machine because of the fetch function🗿
                let startDate = document.getElementById('fromdate').value;
                let date = new Date(startDate);
                let table = document.getElementById('dateTable');
                table.innerHTML = '';
                let row = table.insertRow();
                row.classList.add('dateheader');

                let firstCell = row.insertCell();
                let nameSpan = document.createElement('span');
                nameSpan.innerHTML = `
                    <button style="width: -webkit-fill-available;" type="button" class="btn rhead btn-outline-primary"
                            name="roomcategorybtn" id="roomcategorybtn">Room Type <i
                            class="fa-solid fa-angle-down"></i></button>
                        <ul id="listroomcat" style="display:none;">
                            @foreach ($roomcategorydata as $item)
                                <li>
                                    <input class="roomcatcheckbox" value="{{ $item->cat_code }}" type="checkbox" checked>
                                    <span>{{ $item->name }}</span>
                                </li>
                            @endforeach
                        </ul>`;
                firstCell.appendChild(nameSpan);

                for (let i = 1; i <= 30; i++) {
                    let cell = row.insertCell();
                    cell.classList.add('dateheadertd');
                    cell.dataset.nu = i;
                    let today = new Date();
                    // let ymd = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate() + 1).padStart(2, '0')}`;
                    let monthSpan = document.createElement('span');
                    monthSpan.textContent = new Intl.DateTimeFormat('en-US', {
                        month: 'short'
                    }).format(date);
                    cell.appendChild(monthSpan);
                    cell.appendChild(document.createElement('br'));
                    cell.appendChild(document.createElement("strong")).appendChild(document.createTextNode(date.getDate()));
                    let yearSpan = document.createElement('span');
                    yearSpan.style.display = 'none';
                    yearSpan.textContent = date.getFullYear();
                    cell.appendChild(yearSpan);
                    cell.appendChild(document.createElement('br'));
                    let daySpan = document.createElement('span');
                    daySpan.textContent = new Intl.DateTimeFormat('en-US', {
                        weekday: 'short'
                    }).format(date);
                    cell.appendChild(daySpan);
                    date.setDate(date.getDate() + 1);
                    let year = yearSpan.textContent;
                    let month = new Intl.DateTimeFormat('en-US', {
                        month: '2-digit'
                    }).format(date);
                    let day = new Intl.DateTimeFormat('en-US', {
                        day: '2-digit'
                    }).format(date);
                    completedate = `${year}-${month}-${day}`;
                    cell.dataset.date = completedate;
                    exportdateformat(completedate);
                }

                async function fetchRoomCategory() {
                    try {
                        const response = await fetch('/roomcategoryget');
                        const categories = await response.json();

                        let table = document.getElementById('dateTable');

                        for (let category of categories) {
                            let row = table.insertRow();
                            let cell = row.insertCell();
                            $(cell).html(`${category.name} <span class="badge badge-primary">${category.norooms}</span>`);
                            cell.classList.add('rstatuscatname');
                            row.dataset.value = category.cat_code;

                            // for (let i = 1; i <= 30; i++) {
                            //     fetch('/roomcountget', {
                            //             method: 'POST',
                            //             headers: {
                            //                 'Content-Type': 'application/json',
                            //                 'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            //             },
                            //             body: JSON.stringify({
                            //                 categoryCode: category.cat_code,
                            //             }),
                            //         })
                            //         .then(response => {
                            //             if (!response.ok) {
                            //                 throw new Error('Network response was not ok');
                            //             }
                            //             return response.json();
                            //         })
                            //         .then(roomCount => {
                            //             let cell = row.insertCell();
                            //             cell.classList.add('categoryheader');
                            //             cell.dataset.nu = i;
                            //             cell.innerHTML = `% </br> <span>${roomCount}</span>`;
                            //         })
                            //         .catch(error => {
                            //             console.error('Error:', error);
                            //         });
                            // }

                            for (let room of await fetchRooms(category.cat_code)) {
                                row = table.insertRow();
                                cell = row.insertCell();
                                cell.textContent = room.rcode;
                                row.dataset.value = room.room_cat;

                                for (let i = 1; i <= 30; i++) {
                                    cell = row.insertCell();
                                    cell.classList.add('roomstatuscell');
                                    cell.innerHTML = '&nbsp;';
                                    cell.dataset.value = room.rcode;
                                    dataaddedcell = cell.dataset.value;

                                    const querySelector = document.querySelectorAll('.dateheadertd')[i - 1];
                                    const htmlContent = querySelector.innerHTML;
                                    const year = htmlContent.match(/(\d{4})/)[1];
                                    const dateMatch = htmlContent.match(/<strong>(\d+)<\/strong>/);
                                    const date = dateMatch ? dateMatch[1].padStart(2, '0') : null;
                                    const monthName = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/)[1];
                                    const day = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/g)[1];
                                    const monthNumber = (new Date(Date.parse(monthName + " 1, " + year)).getMonth() + 1).toString().padStart(2, '0');
                                    const fromdate = year + '-' + monthNumber + '-' + date;
                                    cell.headers = fromdate;
                                    headeraddedcell = cell.headers;
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }

                async function fetchRooms(categoryCode) {
                    try {
                        const response = await fetch('/roomget', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                categoryCode: categoryCode,
                            }),
                        });

                        const rooms = await response.json();
                        return rooms;
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }

                function popupblockout(roomblockout) {
                    roomblockout.forEach(function(rdata) {
                        const roomno = rdata.roomcode;
                        const fromdate = new Date(rdata.fromdate);
                        const todate = new Date(rdata.todate);
                        const block = rdata.block;
                        const reasons = rdata.reasons;
                        const alltd = $('td[data-value="' + roomno + '"]');

                        alltd.each(function() {
                            const td = $(this);
                            const headers = new Date(td.attr('headers'));

                            if (headers >= fromdate && headers <= todate) {
                                if (rdata.block == 'Out of Order') {
                                    td.addClass('oorder').text(block);
                                    td.append('<div class="opopshow">' + reasons + '</div>');
                                }
                                if (rdata.block == 'Maintainence') {
                                    td.addClass('maintainence').text(block);
                                    td.append('<div class="opopshow">' + reasons + '</div>');
                                }
                                if (rdata.block == 'Management') {
                                    td.addClass('management').text(block);
                                    td.append('<div class="opopshow">' + reasons + '</div>');
                                }
                                if (rdata.block == 'Marriage') {
                                    td.addClass('marriage').text(block);
                                    td.append('<div class="opopshow">' + reasons + '</div>');
                                }
                            }
                        });
                    });
                }

                async function fetchBookedRooms() {
                    try {
                        const response = await fetch('/bookedroomget');

                        if (response.ok) {
                            const datas = await response.json();
                            const data = datas.bookedroomdata;
                            const amountdetails = datas.amountdetails;
                            let roomblockout = datas.roomblockout;
                            if (roomblockout.length > 0) {
                                popupblockout(roomblockout);
                            }
                            if (data.length > 0) {
                                data.forEach(function(booking) {
                                    const name = booking.name;
                                    const docid = booking.docid;
                                    const roomno = booking.roomno;
                                    const con_prefix = booking.con_prefix ?? '';
                                    const [firstName, lastName] = name.split(' ');
                                    const chkindate = new Date(booking.chkindate);
                                    const depdate = new Date(booking.depdate);
                                    let firstBetweenTd = null;
                                    let tmpncurdate = document.getElementById('sss').value;
                                    const allTds = document.querySelectorAll('td[data-value="' + roomno + '"]');
                                    let options = {
                                        timeZone: 'Asia/Kolkata',
                                        hour12: false,
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    };
                                    let currentTime = new Date().toLocaleString('en-US', options);

                                    allTds.forEach(function(td) {
                                        const headers = new Date(td.getAttribute('headers'));
                                        if (headers && headers.getTime() === chkindate.getTime()) {
                                            td.classList.add('fromtd');
                                            if (booking.leaderyn == 'Y') {
                                                td.classList.add('crown');
                                            }
                                            if (booking.billno != '0') {
                                                td.classList.remove('fromtd');
                                                td.classList.add('billedtd');
                                            }
                                            if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate_minus_one <= tmpncurdate) {
                                                td.classList.remove('fromtd');
                                                td.classList.add('delaytd');
                                            }
                                            let prefix = booking.con_prefix ?? '';
                                            td.textContent = prefix + ' ' + firstName;
                                            td.addEventListener('click', function() {
                                                OpenBookingInfoModal(booking, amountdetails);
                                            });
                                        } else if (headers > chkindate && headers < new Date(booking.depdate_minus_one)) {
                                            td.classList.add('betweentd');
                                            if (booking.leaderyn == 'Y') {
                                                td.classList.add('crown');
                                            }
                                            let prefix = booking.con_prefix ?? '';
                                            td.textContent = prefix + ' ' + firstName;
                                            td.addEventListener('click', function() {
                                                OpenBookingInfoModal(booking, amountdetails);
                                            });
                                            if (!firstBetweenTd) {
                                                firstBetweenTd = td;
                                            }
                                            if (booking.billno != '0') {
                                                td.classList.remove('betweentd');
                                                td.classList.add('billedtd');
                                            }
                                            if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate_minus_one <= tmpncurdate) {
                                                td.classList.remove('betweentd');
                                                td.classList.add('delaytd');
                                            }
                                        }
                                    });

                                    const perfecttdto = document.querySelectorAll('td[data-value="' + roomno + '"][headers="' + booking.depdate_minus_one + '"]');
                                    perfecttdto.forEach(function(td) {
                                        td.classList.add('totd');
                                        if (booking.leaderyn == 'Y') {
                                            td.classList.add('crown');
                                        }
                                        if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate_minus_one <= tmpncurdate) {
                                            td.classList.remove('totd');
                                            td.classList.add('delaytd');
                                        }
                                        let prefix = booking.con_prefix ?? '';
                                        td.textContent = prefix + ' ' + firstName;
                                        td.addEventListener('click', function() {
                                            OpenBookingInfoModal(booking, amountdetails);
                                        });
                                        if (!firstBetweenTd) {
                                            let prefix = booking.con_prefix ?? '';
                                            td.textContent = prefix + ' ' + firstName;
                                        }
                                        if (booking.billno != '0') {
                                            td.classList.remove('totd');
                                            td.classList.add('billedtd');
                                        }
                                    });

                                    if (firstBetweenTd) {
                                        let prefix = booking.con_prefix ?? '';
                                        firstBetweenTd.textContent = prefix + ' ' + firstName;
                                    }
                                });
                            } else {
                                pushNotify('info', 'Room Status', 'No Booked Rooms Data Found', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'left top');
                            }
                        } else {
                            console.error('Failed to fetch booked rooms. Status:', response.status);
                        }
                    } catch (error) {
                        console.error('An error occurred during the fetch:', error);
                    }
                }

                async function fetchBookedRoomsres() {
                    try {
                        const response = await fetch('/reservedroomget');

                        if (response.ok) {
                            const data = await response.json();
                            const bookingdata = data.bookedroomdata;
                            const emptycategory = data.emptycategory;
                            const emptyrooms = data.emptyrooms;

                            if (emptycategory.length > 0) {
                                emptycategory.forEach((row) => {
                                    let emptycount = row.emptycategory || 0;
                                    let room_cat = row.room_cat;

                                    $('#dateTable tbody tr').each(function() {
                                        if ($(this).data('value') == room_cat) {
                                            const categorytd = $(this).find('td.rstatuscatname');
                                            const emptyCountText = `<span class="emptyrooms">Und. Room: ${emptycount}</span>`;
                                            categorytd.html(categorytd.text() + emptyCountText);
                                        }
                                    });
                                });
                            }

                            $('#dateTable tbody tr.dateheader').each(function() {
                                const dateFromTable = $(this).find('td.dateheadertd').data('date');

                                const matchingRow = emptyrooms.find((row) => row.ArrDate.includes(dateFromTable));
                                if (matchingRow) {
                                    if ($(this).find('td').data('date') == matchingRow.ArrDate) {
                                        $(this).css('background-color', 'yellow');
                                    }
                                }
                            });

                            if (bookingdata.length > 0) {
                                bookingdata.forEach(function(booking) {
                                    const name = booking.GuestName;
                                    const docid = booking.BookingDocid;
                                    const sno = booking.Sno;
                                    const roomno = booking.RoomNo;
                                    const con_prefix = booking.con_prefix ?? '';
                                    const [firstName, lastName] = name.split(' ');
                                    const chkindate = new Date(booking.ArrDate);
                                    const depdate = new Date(booking.depdate_minus_one);
                                    let firstBetweenTd = null;
                                    const allTds = document.querySelectorAll('td[data-value="' + roomno + '"]');

                                    allTds.forEach(function(td) {
                                        const headers = new Date(td.getAttribute('headers'));
                                        if (headers && headers.getTime() === chkindate.getTime()) {
                                            td.classList.add('fromtd2');
                                            td.textContent = booking.con_prefix + ' ' + firstName;
                                            let arrdate = booking.ArrDate;
                                            let tmpncurdate = document.getElementById('sss').value;
                                            td.addEventListener('click', function() {
                                                openbookngreservemodal(booking);
                                            });
                                            if (arrdate == tmpncurdate) {
                                                // Existing condition, left empty
                                            }
                                        } else if (headers > chkindate && headers < new Date(booking.depdate_minus_one)) {
                                            td.classList.add('betweentd2');
                                            td.textContent = booking.con_prefix + ' ' + firstName;
                                            if (!firstBetweenTd) {
                                                firstBetweenTd = td;
                                            }
                                            td.addEventListener('click', function() {
                                                openbookngreservemodal(booking);
                                            });
                                        }
                                    });

                                    const perfecttdto = document.querySelectorAll('td[data-value="' + roomno + '"][headers="' + booking.depdate_minus_one + '"]');
                                    perfecttdto.forEach(function(td) {
                                        td.textContent = booking.con_prefix + ' ' + firstName;
                                        td.classList.add('totd2');
                                        if (!firstBetweenTd) {
                                            td.textContent = booking.con_prefix + ' ' + firstName;
                                            td.addEventListener('click', function() {
                                                openbookngreservemodal(booking);
                                            });
                                        }
                                    });

                                    if (firstBetweenTd) {
                                        firstBetweenTd.textContent = booking.con_prefix + ' ' + firstName;
                                    }
                                });
                            } else {
                                pushNotify('info', 'Room Status', 'No Reserved Rooms Data Found', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                            }
                        } else {
                            console.error('Failed to fetch booked rooms. Status:', response.status);
                        }
                    } catch (error) {
                        console.error('An error occurred during the fetch:', error);
                    }
                }


                setTimeout(() => {
                    fetchBookedRooms();
                    fetchBookedRoomsres();
                }, 5000);

                fetchRoomCategory();

            }

            document.getElementById('fromdate').addEventListener('change', generateDateRow);

            generateDateRow();
        });
    </script>

    {{-- <script src="{{ asset('admin/js/room.js') }}"></script> --}}


    <script>
        var globalguestcode;
        var globalname;
        var globalfoliono;
        var globaldocid;
        var globalsno1;
        var globalsno;
        var globalroomno;
        var globaldepdata;
        var globalncurdate;
        var globalleaderyn;

        var rglobaldocid
        var rglobalsno1

        function OpenBookingInfoModal(bookingarray, amountdetails) {
            if (!document.querySelector('.modal-packed')) {
                let leaderyn = bookingarray['leaderyn'];
                let paydetail;
                if (leaderyn == 'Y') {
                    paydetail = amountdetails.filter(x => x.docid == bookingarray['docid']);
                } else {
                    paydetail = amountdetails.filter(x => x.docid == bookingarray['docid'] && x.sno1 == bookingarray['sno1']);
                }

                let totalamt = 0.00;
                let paidamt = 0.00;
                let balance = 0.00;
                paydetail.forEach((data) => {
                    totalamt += parseFloat(data.totalamt);
                    paidamt += parseFloat(data.paidamt);
                    balance += parseFloat(data.balance);
                });

                globalguestcode = bookingarray['guestcode'];
                folioNo = bookingarray['folioNo'];
                globaldocid = bookingarray['docid'];
                globalsno1 = bookingarray['sno1'];
                globalsno = bookingarray['sno'];
                globalroomno = bookingarray['roomno'];
                globaldepdata = bookingarray['depdate'];
                globalleaderyn = bookingarray['leaderyn'];
                let crowncls = globalleaderyn == 'Y' ? 'crown' : '';
                globalncurdate = $('#fixncur').val();
                document.getElementById('docidd').value = globaldocid;
                let prefix = bookingarray['con_prefix'] ?? '';
                globalname = prefix + ' ' + bookingarray['name'];
                const div1 = document.createElement('div');
                div1.classList.add('modal-packed');
                const modal = document.createElement('div');
                modal.classList.add('modal-custom');
                let formbtndisplay;
                let billprintbtndisp;
                if (bookingarray['billno'] == '0') {
                    formbtndisplay = 'none';
                    billprintbtndisp = 'block';
                } else {
                    formbtndisplay = 'block';
                    billprintbtndisp = 'none';
                }
                modal.innerHTML = `
                    <div class="modal-content content-packed">
                        <div style="display:contents;">
                            <h3 class="RBT ${crowncls}"><i class="fa-solid fa-hotel"></i> ${bookingarray['con_prefix'] ? bookingarray['con_prefix'] : ''} ${bookingarray['name'] ? bookingarray['name'] : ''}</h3>
                            <img class="close" onclick="deletemodaldiv()" src="{{ asset('admin/icons/custom/close2.svg') }}" alt="Close"></div>
                            <p><i class="fa-solid fa-phone"></i> ${bookingarray['mobile_no'] ? bookingarray['mobile_no'] : ''}</p>
                            <div class="button-group mb-2">
                                <div aria-label="Vertical button group" role="group" class="">
                                    <button style="display:none;" class="btn btn-eight ti-write btn-sm btn-dribbble" type="button"> Edit Reservation</button>
                                    <button data-toggle="modal" data-target="#changeprofilemodal" class="btn btn-eight ti-id-badge btn-sm btn-vimeo" type="button"> Change Profile</button>
                                    <div role="group" class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-eight btn-sm btn-outline-success dropdown-toggle" type="button">More Options</button>
                                        <div class="dropdown-menu .custom-dropdown">
                                        <button style="display:${billprintbtndisp};" data-toggle="modal" data-target="#ammendstaymodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Amend Stay</button> 
                                        <button data-toggle="modal" data-target="#guestledgermodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Guest Ledger</button>
                                        <button style="display:${billprintbtndisp};" data-toggle="modal" data-target="#roomchangemodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Room Change</button> 
                                        <button style="display:${billprintbtndisp};" data-toggle="modal" data-target="#advchargemodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Advance Charge</button> 
                                        <button id="billprint" style="display:${billprintbtndisp};" data-toggle="modal" data-target="#billprintmodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Bill Print</button> 
                                        <form id="cancelForm" method="POST" action="{{ route('billcancel') }}">
                                        @csrf
                                        <input type="hidden" name="docid" value="${bookingarray['docid']}">
                                        <input type="hidden" name="sno1" value="${bookingarray['sno1']}">
                                        <input type="hidden" id="cancelreason"" name="cancelreason" value="">
                                        <button style="display:${formbtndisplay};" id="billcancelbtn" type="button" onclick="openCancelPrompt()" class="btn btn-eight btn-sm btn-outline-primary">Bill Cancel</button>
                                        <button data-toggle="modal" data-target="#billsettlemodal" style="display:${formbtndisplay};" type="button" class="btn btn-eight btn-sm btn-outline-primary">Bill Settle</button>
                                    </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Folio No.:  ${bookingarray['folioNo']}</p>
                                    <p>Check-in Date: ${new Date(bookingarray['chkindate']).toLocaleDateString('en-GB').replace(/\//g,'-')}</p>
                                    <p>Room Type: ${bookingarray['roomcatname']}</p>
                                    <p>Rate Plan: ${bookingarray['planname'] ? bookingarray['planname'] : ''}</p> 
                                    <p>Plan Amount: ${bookingarray['planamt'] ? bookingarray['planamt'] : ''}</p>
                                    <p>Company: ${bookingarray['company'] ? bookingarray['companyname'] : ''}</p>
                                    <p>Booked By: ${bookingarray['BookedBy'] ? bookingarray['BookedBy'] : ''}</p>
                                    <p>Remarks: ${bookingarray['remarks'] ? bookingarray['remarks'] : ''}</p>
                                </div>
                                <div class="col-md-6">
                                    <p>Adults <i class="fa-solid fa-user"></i> : ${bookingarray['adult']} Children <i class="fa-solid fa-child"></i> : ${bookingarray['child'] ? bookingarray['child'] : ''}</p>
                                    <p>Exp. Departure Date: ${new Date(bookingarray['depdate']).toLocaleDateString('en-GB').replace(/\//g,'-')}</p>
                                    <p>Room Number: ${bookingarray['roomno']}</p>
                                    <p>Room Rate: ${bookingarray['roomrate']}</p>
                                    <p>Bill No.: ${bookingarray['billno'] == '0' ? '' : bookingarray['billno']}</p>
                                    <p>Travel: ${bookingarray['travel'] ? bookingarray['travel'] : ''}</p>
                                    <p>Pick Up/Drop <i class="fa-solid fa-truck-pickup"></i>: ${bookingarray['pickupdrop'] ? bookingarray['pickupdrop'] : ''}</p>
                                </div>
                                <div style="position: absolute;bottom: 0;width: -webkit-fill-available;" class="modal-footer">
                                <div style="width: -webkit-fill-available;" class="card shadow-sm">
                                        <div class="card-body p-0">
                                            <table class="table amountshow table-hover mb-0">
                                                <tbody>
                                                    <tr class="hover-row">
                                                        <td class="fw-bold">Total</td>
                                                        <td class="text-end">Rs. ${totalamt.toFixed(2)}</td>
                                                    </tr>
                                                    <tr class="hover-row">
                                                        <td class="fw-bold">Paid</td>
                                                        <td class="text-end">Rs. ${paidamt.toFixed(2)}</td>
                                                    </tr>
                                                    <tr class="hover-row">
                                                        <td class="fw-bold text-danger">Balance</td>
                                                        <td class="text-end text-danger">Rs. ${balance.toFixed(2)}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>`;

                document.body.appendChild(div1);
                div1.appendChild(modal);
            }
        }

        function openbookngreservemodal(bookingarray) {
            if (!document.querySelector('.modal-packed')) {
                let rglobalguestcode = bookingarray['guestcode'];
                let rfolioNo = bookingarray['BookNo'];
                rglobaldocid = bookingarray['BookingDocid'];
                rglobalsno1 = bookingarray['Sno'];
                let rglobalroomno = bookingarray['RoomNo'];
                let rglobalarrdate = bookingarray['ArrDate'];
                let rglobaldepdata = bookingarray['DepDate'];
                globalncurdate = $('#fixncur').val();
                let dispbtn = '';
                let oldchk = 'green';

                if (rglobalarrdate < globalncurdate) {
                    oldchk = 'red';
                }

                if (rglobalarrdate <= globalncurdate) {
                    dispbtn = `<button class="checkinbtnrocc" data-docid="${rglobaldocid}" data-sno="${rglobalsno1}" type="button" style="padding: 5px 8px; background-color: ${oldchk}; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.3s ease; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                            <i class="fa fa-check-circle" style="margin-right: 8px;"></i> Check In
                        </button>`;
                }

                let prefix = bookingarray['con_prefix'] ?? '';
                let rglobalname = prefix + ' ' + bookingarray['GuestName'];
                const div1 = document.createElement('div');
                div1.classList.add('modal-packed');
                const modal = document.createElement('div');
                modal.classList.add('modal-custom');
                let formbtndisplay;
                let billprintbtndisp;
                let adtr = '';
                let totalamt = 0.00;
                let tfoot = '';
                let adthead = '';
                if (bookingarray['advance'].length > 0) {
                    adthead = `<tr>
                                 <th>Paytype</th>
                                 <th>On Date</th>
                                 <th>Amount</th>
                            </tr>`;
                    bookingarray['advance'].forEach((data, index) => {
                        totalamt += parseFloat(data.amtcr);
                        adtr += `<tr class="hover-row">
                                    <td class="font-weight-light">${data.paytype}</td>
                                    <td class="font-weight-light">${dmy(data.vdate)} ${data.vtime}</td>
                                    <td class="font-weight-light text-end">Rs. ${data.amtcr}</td>
                                </tr>`;
                    });
                    tfoot = `<tr>
                                 <td></td>
                                 <td></td>
                                 <td class="font-tiny text-end">Rs. ${totalamt.toFixed(2)}</td>
                                    </tr>`

                }

                modal.innerHTML = `
                    <div class="modal-content content-packed">
                        <div style="display:contents;">
                            <h3 class="RBT"><i class="fa-solid fa-hotel"></i> ${rglobalname} </h3>
                            <img class="close" onclick="deletemodaldiv()" src="{{ asset('admin/icons/custom/close2.svg') }}" alt="Close"></div>
                            <p><i class="fa-solid fa-phone"></i> ${bookingarray['mobile_no'] ? bookingarray['mobile_no'] : ''}</p>
                           <div class="button-group mb-2 ">
                                <div aria-label="Vertical button group" role="group" class="">
                                 ${dispbtn}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Folio No.:  ${bookingarray['BookNo']}</p>
                                    <p>Check-in Date: ${new Date(bookingarray['ArrDate']).toLocaleDateString('en-GB').replace(/\//g,'-')}</p>
                                    <p>Room Type: ${bookingarray['roomcatname']}</p>
                                    <p>Rate Plan: ${bookingarray['planname'] ? bookingarray['planname'] : ''}</p> 
                                    <p>Plan Amount: ${bookingarray['plannetamt'] ? bookingarray['plannetamt'] : ''}</p>
                                    <p>Company: ${bookingarray['company'] ? bookingarray['companyname'] : ''}</p>
                                    <p>Remarks: ${bookingarray['Remarks'] ? bookingarray['Remarks'] : ''}</p>
                                </div>
                                <div class="col-md-6">
                                    <p>Adults <i class="fa-solid fa-user"></i> : ${bookingarray['Adults']} Children <i class="fa-solid fa-child"></i> : ${bookingarray['Childs'] ? bookingarray['Childs'] : ''}</p>
                                    <p>Exp. Departure Date: ${new Date(bookingarray['DepDate']).toLocaleDateString('en-GB').replace(/\//g,'-')}</p>
                                    <p>Room Number: ${bookingarray['RoomNo']}</p>
                                    <p>Room Rate: ${bookingarray['Tarrif']}</p>
                                    <p>Travel: ${bookingarray['travel'] ? bookingarray['travel'] : ''}</p>
                                    <p>Booked By: ${bookingarray['BookedBy'] ? bookingarray['BookedBy'] : ''}</p>
                                    <p>Pick Up/Drop <i class="fa-solid fa-truck-pickup"></i>: ${bookingarray['pickupdrop'] ? bookingarray['pickupdrop'] : ''}</p>
                                </div>
                            </div>
                        </div>
                        <div style="position: absolute;bottom: 0;width: -webkit-fill-available;" class="modal-footer">
                                <div style="width: -webkit-fill-available;" class="card shadow-sm">
                                        <div class="card-body p-0">
                                            <table class="table amountshow table-hover mb-0">
                                                <thead>
                                                    ${adthead}
                                                </thead>
                                                <tbody>
                                                   ${adtr}
                                                </tbody>
                                                <tfoot>
                                                    ${tfoot}
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>`;

                document.body.appendChild(div1);
                div1.appendChild(modal);
            }
        }

        $(document).on('click', '.checkinbtnrocc', function() {
            let docid = $(this).data('docid');
            let sno = $(this).data('sno');
            sendroute(docid, sno);
        });

        $(document).on('click', '#billprint', function() {
            $('#myloader').removeClass('none');
            $('#myloader').find('img').attr('src', 'admin/icons/custom/typewriter.gif');
            $('#myloader').find('.loader-text').html('Getting Bill Details');
            setTimeout(() => {
                $('#myloader').addClass('none');
                let checkpostxhr = new XMLHttpRequest();
                checkpostxhr.open('POST', '/checkchargecount', true);
                checkpostxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                checkpostxhr.onreadystatechange = function() {
                    if (checkpostxhr.readyState === 4 && checkpostxhr.status === 200) {
                        let resultsd = JSON.parse(checkpostxhr.responseText);
                        let results = resultsd.chargecount;
                        let allrooms = resultsd.allrooms;
                        let leaderyn = resultsd.leaderyn;
                        if (results === 0) {
                            Swal.fire({
                                title: "Bill Print",
                                text: `Charge Posting For Room No : ${globalroomno}`,
                                icon: "error",
                                showCancelButton: true,
                                confirmButtonColor: "#1EE01E",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#billprintmodal').modal('hide');
                                    let startposting = new XMLHttpRequest();
                                    startposting.open('POST', '/postchargesone', true);
                                    startposting.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    startposting.onreadystatechange = function() {
                                        if (startposting.status === 200 && startposting.readyState === 4) {
                                            let results = JSON.parse(startposting.responseText);
                                            $('#billprintmodal').modal('hide');
                                            if (results.success == 'Charge Posted') {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Charges have been posted for Room No. : ${globalroomno}. Reopen Bill Print.`,
                                                    icon: "success",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(allrooms, 0);
                                                });
                                            } else if (results.error == 'Unable To Post Charge') {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Unable to Post Charge`,
                                                    icon: "error",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(allrooms, 0);
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Unknown Error Occurred`,
                                                    icon: "error",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(allrooms, 0);
                                                });
                                            }
                                        }
                                    };
                                    startposting.send(`docid=${globaldocid}&roomno=${globalroomno}&sno1=${globalsno1}&_token={{ csrf_token() }}`);
                                } else if (result.isDismissed) {
                                    showAlertForRooms(allrooms, 0);
                                }
                            });
                        } else {
                            showAlertForRooms(allrooms, 0);
                        }
                    }
                };
                checkpostxhr.send(`docid=${globaldocid}&sno1=${globalsno1}&_token={{ csrf_token() }}`);
            }, 1000);
        });


        function showAlertForRooms(rooms, index) {
            if (index >= rooms.length) return;
            if (typeof rooms[index] != 'undefined') {
                let checkpostxhr = new XMLHttpRequest();
                checkpostxhr.open('POST', '/checkchargecount', true);
                checkpostxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                checkpostxhr.onreadystatechange = function() {
                    if (checkpostxhr.readyState === 4 && checkpostxhr.status === 200) {
                        let resultsd = JSON.parse(checkpostxhr.responseText);
                        let results = resultsd.chargecount;
                        let allrooms = resultsd.allrooms;
                        if (results === 0) {
                            Swal.fire({
                                title: "Bill Print",
                                text: `Charge Posting For Room No : ${rooms[index].roomno}`,
                                icon: "error",
                                showCancelButton: true,
                                confirmButtonColor: "#1EE01E",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#billprintmodal').modal('hide');
                                    let startposting = new XMLHttpRequest();
                                    startposting.open('POST', '/postchargesone', true);
                                    startposting.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    startposting.onreadystatechange = function() {
                                        if (startposting.status === 200 && startposting.readyState === 4) {
                                            let results = JSON.parse(startposting.responseText);
                                            $('#billprintmodal').modal('hide');
                                            if (results.success == 'Charge Posted') {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Charges have been posted for Room No. : ${rooms[index].roomno}. Reopen Bill Print.`,
                                                    icon: "success",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(rooms, index + 1);
                                                });
                                            } else if (results.error == 'Unable To Post Charge') {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Unable to Post Charge`,
                                                    icon: "error",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(rooms, index + 1);
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Charge Posting",
                                                    text: `Unknown Error Occurred`,
                                                    icon: "error",
                                                    timer: 3000
                                                }).then(() => {
                                                    showAlertForRooms(rooms, index + 1);
                                                });
                                            }
                                        }
                                    };
                                    startposting.send(`docid=${rooms[index].docid ?? ''}&roomno=${rooms[index].roomno}&sno1=${rooms[index].sno1}&_token={{ csrf_token() }}`);
                                } else if (result.isDismissed) {
                                    showAlertForRooms(rooms, index + 1);
                                }
                            });
                        } else {
                            showAlertForRooms(rooms, index + 1);
                        }
                    }
                }
                checkpostxhr.send(`docid=${rooms[index].docid}&sno1=${rooms[index].sno1}&_token={{ csrf_token() }}`);
            }

        }

        function openCancelPrompt() {
            // var reason = prompt("Why do you want to cancel this bill?");
            Swal.fire({
                icon: 'question',
                title: 'Bill',
                text: 'Why do you want to cancel this bill?',
                input: 'text',
                inputPlaceholder: 'Reason',
                inputValue: 'Wrong Bill Entry',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Reason is required';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("cancelreason").value = result.value;
                    document.getElementById("cancelForm").submit();
                }
            });
        }

        function deletemodaldiv() {
            const modal = document.querySelector('.modal-custom');
            const modalpacked = document.querySelector('.modal-packed');
            modal.remove();
            modalpacked.remove();
        }

        $(document).keydown(function(event) {
            if (event.keyCode === 27) {
                deletemodaldiv();
            }
        });

        $('#changeprofilemodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("changeprofileframe");
            let profilechangespan = document.getElementById('profilechangespan');
            let profilechangecode = document.getElementById('profilechangecode');
            profilechangespan.textContent = globalname;
            profilechangecode.textContent = folioNo;
            iframe.src = "{{ url('/changeprofile') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1;
        });

        $('#ammendstaymodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("ammenstayiframe");
            let profilechangespan = document.getElementById('ammendstayspan');
            let guestcode1 = document.getElementById('guestcode1');
            profilechangespan.textContent = globalname;
            guestcode1.textContent = folioNo;
            iframe.src = "{{ url('/ammendstay') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
        });

        $('#guestledgermodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("guestledgeriframe");
            let guestledgerspan = document.getElementById('guestledgerspan');
            let guestcode1 = document.getElementById('guestcode2');
            guestledgerspan.textContent = globalname;
            guestcode1.textContent = folioNo;
            iframe.src = "{{ url('/guestledger') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1;
        });

        $('#roomchangemodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("roomchangeiframe");
            let profilechangespan = document.getElementById('roomchangespan');
            profilechangespan.textContent = globalname;
            let guestcode3 = document.getElementById('guestcode3');
            guestcode3.textContent = folioNo;
            iframe.src = "{{ url('/roomchange') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
        });

        $('#advchargemodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("advchargeiframe");
            let profilechangespan = document.getElementById('advchargespan');
            profilechangespan.textContent = globalname;
            let guestcode4 = document.getElementById('guestcode4');
            guestcode4.textContent = folioNo;
            iframe.src = "{{ url('/advcharge') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
        });

        $('#billprintmodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("billprintiframe");
            let billprintspan = document.getElementById('billprintspan');
            let guestcode5 = document.getElementById('guestcode5');
            billprintspan.textContent = globalname;
            guestcode5.textContent = folioNo;
            iframe.src = "{{ url('/billprint') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
        });

        $('#billsettlemodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("billsettleiframe");
            let profilechangespan = document.getElementById('billsettlespan');
            profilechangespan.textContent = globalname;
            let guestcode6 = document.getElementById('guestcode6');
            guestcode6.textContent = folioNo;
            iframe.src = "{{ url('/billsettle') }}" + "?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
        });


        function sendroute(docid, sno) {
            Swal.fire({
                title: 'Room Status',
                text: "Mentioned thing(s) are compulsory in order to proceed to check in. Do you want to add this information?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = 'prefilledwalkin?docid=' + encodeURIComponent(docid) + '&sno=' + encodeURIComponent(sno);
                    window.location.href = url;
                }
            });
        }

        function deletemodaldiv2() {
            const modalpacked = document.querySelector('.modal-packed');
            modalpacked.remove();
        }
    </script>
@endsection
