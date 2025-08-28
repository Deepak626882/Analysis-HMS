@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div id="bookingmodal" class="bookingmodal">
            <h3>In House <span class="fa-2xs blinking-text ARDR">Room Status is a color coded indicator that shows
                    the status of the booking.</span></h3>
            <div class="booking-status">
                <div class="indicator confirmed">Confirmed Reservation</div>
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
        <input style="display: none;" type="date" id="sss" value="{{ $ncurdate }}">
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
                                        <input value="{{ $ncurdate }}" class="form-control rhead" type="date"
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
                                    <th id="infoicon"><i class="fa-regular fa-circle-question"></i></th>
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


    <script>
        let activePopup = null;

        function exportdateformat(formatteddate) {
            datearray = formatteddate;
        }

        function generateDateRow() {
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
        <li> <input type="checkbox" id="checkallroomcat" checked>
            <span>Select All</span></li>
        @foreach ($roomcategorydata as $item)
            <li>
                <input class="roomcatcheckbox" value="{{ $item->cat_code }}"
                       type="checkbox" checked>
                <span>{{ $item->name }}</span>
            </li>
        @endforeach
    </ul>
`;
            firstCell.appendChild(nameSpan);

            for (let i = 1; i <= 30; i++) {
                let cell = row.insertCell();
                cell.classList.add('dateheadertd');
                cell.id = 'datheaderid' + i;
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
                        cell.textContent = category.name;
                        cell.classList.add('rstatuscatname');
                        row.dataset.value = category.cat_code;

                        for (let i = 1; i <= 30; i++) {
                            const querySelector = document.querySelectorAll('.dateheadertd')[i - 1];
                            const htmlContent = querySelector.innerHTML;
                            const year = htmlContent.match(/(\d{4})/)[1];
                            const dateMatch = htmlContent.match(/<strong>(\d+)<\/strong>/);
                            const date = dateMatch ? dateMatch[1].padStart(2, '0') : null;
                            const monthName = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/)[1];
                            const day = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/g)[1];
                            const monthNumber = (new Date(Date.parse(monthName + " 1, " + year)).getMonth() + 1).toString().padStart(2, '0');
                            const fromdate = year + '-' + monthNumber + '-' + date;

                            fetch('/roomcountget', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                    },
                                    body: JSON.stringify({
                                        categoryCode: category.cat_code,
                                    }),
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(roomCount => {
                                    let cell = row.insertCell();
                                    cell.classList.add('categoryheader');
                                    cell.dataset.nu = i;
                                    cell.innerHTML = `% </br> <span>${roomCount}</span>`;
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });

                        }

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

                                //     function cellClickListener() {
                                //     cellindex = this.cellIndex;
                                //     if (cellindex) {
                                //         const querySelector = document.querySelectorAll('.dateheadertd')[cellindex - 1];
                                //         const nextcellqueryselector = document.querySelectorAll('.dateheadertd')[cellindex];
                                //         const htmlContent = querySelector.innerHTML;
                                //         const htmlcontent2 = nextcellqueryselector.innerHTML;
                                //         const year = htmlContent.match(/(\d{4})/)[1];
                                //         const nextcellyear = htmlcontent2.match(/(\d{4})/)[1];
                                //         const date = htmlContent.match(/<strong>(\d+)<\/strong>/)[1];
                                //         const nextcelldate = htmlcontent2.match(/<strong>(\d+)<\/strong>/)[1];
                                //         const monthName = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/)[1];
                                //         const nextcellmonthName = htmlcontent2.match(/<span>([a-zA-Z]+)<\/span>/)[1];
                                //         const day = htmlContent.match(/<span>([a-zA-Z]+)<\/span>/g)[1];
                                //         const monthNumber = new Date(Date.parse(monthName + " 1, " + year)).getMonth() + 1;
                                //         const nextmonthNumber = new Date(Date.parse(nextcellmonthName + " 1, " + nextcellyear)).getMonth() + 1;

                                //         if (activePopup) {
                                //             document.body.removeChild(activePopup);
                                //         }
                                //         let popup = createPopup(year, monthNumber, date, day, this.datarow, nextcelldate, nextmonthNumber, nextcellyear);
                                //         let rect = cell.getBoundingClientRect();
                                //         popup.style.top = `${window.scrollY + rect.top - popup.offsetHeight}px`;
                                //         popup.style.left = `revert-layer`;
                                //         document.body.appendChild(popup);
                                //         activePopup = popup;
                                //     }
                                // }
                                // cell.addEventListener('click', cellClickListener);

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

            async function fetchBookedRooms() {
                try {
                    const response = await fetch('/bookedroomget');
                    if (response.ok) {
                        const datas = await response.json();
                        const data = datas.bookedroomdata;
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
                                        if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate <= tmpncurdate) {
                                            td.classList.remove('fromtd');
                                            td.classList.add('delaytd');
                                        }
                                        let prefix = booking.con_prefix ?? '';
                                        td.textContent = prefix + ' ' + firstName;
                                        td.addEventListener('click', function() {
                                            OpenBookingInfoModal(booking, amountdetails);
                                            pushNotify('info', 'Room Status', 'Press ESC Button To Close Details', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'left top');
                                        });
                                    } else if (headers > chkindate && headers < depdate) {
                                        td.classList.add('betweentd');
                                        if (booking.leaderyn == 'Y') {
                                            td.classList.add('crown');
                                        }
                                        let prefix = booking.con_prefix ?? '';
                                        td.textContent = prefix + ' ' + firstName;
                                        td.addEventListener('click', function() {
                                            pushNotify('info', 'Room Status', 'Press ESC Button To Close Details', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'left top');
                                            OpenBookingInfoModal(booking, amountdetails);
                                        });
                                        if (!firstBetweenTd) {
                                            firstBetweenTd = td;
                                        }
                                        if (booking.billno != '0') {
                                            td.classList.remove('betweentd');
                                            td.classList.add('billedtd');
                                        }
                                        if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate <= tmpncurdate) {
                                            td.classList.remove('betweentd');
                                            td.classList.add('delaytd');
                                        }
                                    }
                                });

                                const perfecttdto = document.querySelectorAll('td[data-value="' + roomno + '"][headers="' + booking.depdate + '"]');
                                perfecttdto.forEach(function(td) {
                                    td.classList.add('totd');
                                    if (booking.leaderyn == 'Y') {
                                        td.classList.add('crown');
                                    }
                                    if (currentTime > booking.envcheck && booking.billno == '0' && booking.depdate <= tmpncurdate) {
                                        td.classList.remove('totd');
                                        td.classList.add('delaytd');
                                    }
                                    let prefix = booking.con_prefix ?? '';
                                    td.textContent = prefix + ' ' + firstName;
                                    td.addEventListener('click', function() {
                                        OpenBookingInfoModal(booking, amountdetails);
                                        pushNotify('info', 'Room Status', 'Press ESC Button To Close Details', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'left top');
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
                            console.error('No booked rooms data found.');
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
                            const bookingdata = data.bookedroomdata;
                            bookingdata.forEach(function(booking) {
                                const name = booking.GuestName;
                                const docid = booking.BookingDocid;
                                const roomno = booking.RoomNo;
                                const con_prefix = booking.con_prefix ?? '';
                                const [firstName, lastName] = name.split(' ');
                                const chkindate = new Date(booking.ArrDate);
                                const depdate = new Date(booking.DepDate);
                                let firstBetweenTd = null;

                                const allTds = document.querySelectorAll('td[data-value="' + roomno + '"]');
                                allTds.forEach(function(td) {
                                    const headers = new Date(td.getAttribute('headers'));
                                    if (headers && headers.getTime() === chkindate.getTime()) {
                                        td.classList.add('fromtd2');
                                        td.textContent = booking.con_prefix + ' ' + firstName;

                                    } else if (headers > chkindate && headers < depdate) {
                                        td.classList.add('betweentd2');
                                        td.textContent = booking.con_prefix + ' ' + firstName;
                                        // if (!firstBetweenTd) {
                                        //     firstBetweenTd = td;
                                        // }
                                    }
                                });

                                const perfecttdto = document.querySelectorAll('td[data-value="' + roomno + '"][headers="' + booking.depdate + '"]');
                                perfecttdto.forEach(function(td) {
                                    td.classList.add('totd2');
                                    td.textContent = booking.con_prefix + ' ' + firstName;
                                    if (!firstBetweenTd) {
                                        td.textContent = lastName;
                                    }
                                });

                                if (firstBetweenTd) {
                                    firstBetweenTd.textContent = lastName;
                                }
                            });
                        } else {
                            console.error('No reserved rooms data found.');
                        }
                    } else {
                        console.error('Failed to fetch reserved rooms. Status:', response.status);
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
    </script>

    <script src="{{ asset('admin/js/room.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        var globalguestcode;
        var globalname;
        var globalfoliono;

        function OpenBookingInfoModal(bookingarray) {
            globalguestcode = bookingarray['guestcode'];
            folioNo = bookingarray['folioNo'];
            globalname = bookingarray['con_prefix'] + ' ' + bookingarray['name'];
            const div1 = document.createElement('div');
            div1.classList.add('modal-packed');
            const modal = document.createElement('div');
            modal.classList.add('modal-custom');

            modal.innerHTML = `
        <div class="modal-content content-packed">
            <img class="close" onclick="deletemodaldiv()" src="{{ asset('admin/icons/custom/close.svg') }}" alt="Close">
    <h3 class="RBT"><i class="fa-solid fa-hotel"></i> ${bookingarray['con_prefix'] ? bookingarray['con_prefix'] : ''} ${bookingarray['name'] ? bookingarray['name'] : ''}</h3>
    <p><i class="fa-solid fa-phone"></i> ${bookingarray['mobile_no'] ? bookingarray['mobile_no'] : ''}</p>
    <div class="button-group mb-2">
        <div aria-label="Vertical button group" role="group" class="">
            <button style="display:none;" class="btn btn-eight ti-write btn-sm btn-dribbble" type="button"> Edit Reservation</button>
            <button data-toggle="modal" data-target="#changeprofilemodal" class="btn btn-eight ti-id-badge btn-sm btn-vimeo" type="button"> Change Profile</button>
            <div role="group" class="btn-group">
                <button data-toggle="dropdown" class="btn btn-eight btn-sm btn-outline-success dropdown-toggle" type="button">More Options</button>
                <div class="dropdown-menu .custom-dropdown">
                <button data-toggle="modal" data-target="#ammendstaymodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Amend Stay</button> 
                <button data-toggle="modal" data-target="#guestledgermodal" class="btn btn-eight btn-sm btn-outline-primary" type="button">Guest Ledger</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p>DOC ID: <span class="text-dark"> ${bookingarray['docid']}</span></p>
            <p>Check-in Date: <span class="text-dark">${new Date(bookingarray['chkindate']).toLocaleDateString('en-GB')}</span></p>
            <p>Room Type: <span class="text-dark">${bookingarray['roomcatname']}</span></p>
            <p>Rate Plan: <span class="text-dark">${bookingarray['planname'] ? bookingarray['planname'] : ''}</span></p> 
            <p>Plan Amount: <span class="text-dark">${bookingarray['planamt'] ? bookingarray['planamt'] : ''}</span></p>
        </div>
        <div class="col-md-6">
            <p>Adults <i class="fa-solid fa-user"></i> : <span class="text-dark">${bookingarray['adult']}</span> Children <i class="fa-solid fa-child"></i> : <span class="text-dark">${bookingarray['child'] ? bookingarray['child'] : ''}</span></p>
            <p>Exp. Departure Date: <span class="text-dark">${new Date(bookingarray['depdate']).toLocaleDateString('en-GB')}</span></p>
            <p>Room Number: <span class="text-dark">${bookingarray['roomno']}</span></p>
            <p>Room Rate: <span class="text-dark">${bookingarray['roomrate']}</span></p>
        </div>
    </div>
</div>
        `;

            document.body.appendChild(div1);
            div1.appendChild(modal);
        }

        function deletemodaldiv() {
            const modal = document.querySelector('.modal-custom');
            const modalpacked = document.querySelector('.modal-packed');
            modal.remove();
            modalpacked.remove();
        }

        $('#changeprofilemodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("changeprofileframe");
            let profilechangespan = document.getElementById('profilechangespan');
            let profilechangecode = document.getElementById('profilechangecode');
            profilechangespan.textContent = globalname;
            profilechangecode.textContent = folioNo;
            iframe.src = "{{ url('/changeprofile') }}" + "?guestcode=" + globalguestcode;
        });

        $('#ammendstaymodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("ammenstayiframe");
            let profilechangespan = document.getElementById('ammendstayspan');
            let guestcode1 = document.getElementById('guestcode1');
            profilechangespan.textContent = globalname;
            guestcode1.textContent = folioNo;
            iframe.src = "{{ url('/ammendstay') }}" + "?guestcode=" + globalguestcode;
        });

        $('#guestledgermodal').on('show.bs.modal', function(event) {
            var iframe = document.getElementById("guestledgeriframe");
            let guestledgerspan = document.getElementById('guestledgerspan');
            let guestcode1 = document.getElementById('guestcode2');
            guestledgerspan.textContent = globalname;
            guestcode1.textContent = folioNo;
            iframe.src = "{{ url('/guestledger') }}" + "?guestcode=" + globalguestcode;
        });

        // opening clickmodal where inside a modal only a button of openwalkinform will be shown
        function openclickedmodal(bookingarray) {
            let docid = bookingarray['BookingDocid'];
            let folioNo = bookingarray['folioNo'];
            globalname = bookingarray['con_prefix'] + ' ' + bookingarray['GuestName'];
            const div1 = document.createElement('div');
            div1.classList.add('modal-packed');
            const modal = document.createElement('div');

            modal.innerHTML = `
    <div class="modal-content content-packed">
        <img class="close" onclick="deletemodaldiv2()" src="{{ asset('admin/icons/custom/close.svg') }}" alt="Close">
        <h3 class="RBT"><i class="fa-solid fa-hotel"></i> ${globalname ? globalname : ''}</h3>
        <p><i class="fa-solid fa-phone"></i> ${bookingarray['mobile_no'] ? bookingarray['mobile_no'] : ''}</p>
        <div class="button-group mb-2">
            <button onclick="window.location.href='{{ url('prefilledwalkin?docid=') }}${docid}'" class="btn btn-eight btn-sm btn-outline-primary">Open Walkin Form</button>
        </div>
    </div>
`;


            document.body.appendChild(div1);
            div1.appendChild(modal);
        }

        function deletemodaldiv2() {
            const modalpacked = document.querySelector('.modal-packed');
            modalpacked.remove();
        }
    </script>
@endsection
