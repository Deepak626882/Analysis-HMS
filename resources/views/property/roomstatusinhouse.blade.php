@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body inhouse">
                            <input style="display: none;" type="date" id="sss"
                                value="{{ date('Y-m-d', strtotime('-1 day', strtotime(ncurdate()))) }}">
                            <div class="modal fade" id="changeprofilemodal" tabindex="-1"
                                aria-labelledby="changeprofilemodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div style="width: 57rem;" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changeprofilemodalLabel">Profile Change For: <span
                                                    class="ADA" id="profilechangespan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="changeprofilemodalLabel">Folio No.:
                                                <span class="BANX" id="profilechangecode"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="changeprofileframe" src="" frameborder="0"
                                                style="width: 100%; height: 60rem;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="ammendstaymodal" tabindex="-1"
                                aria-labelledby="ammendstaymodalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ammendstaymodalLabel">Ammend Stay For: <span
                                                    class="ADA" id="ammendstayspan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="ammendstaymodalLabel">Folio No.:
                                                <span class="BANX" id="guestcode1"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="ammenstayiframe" src="" frameborder="0"
                                                style="width: 100%; height: 15em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="guestledgermodal" tabindex="-1"
                                aria-labelledby="guestledgermodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="guestledgermodalLabel">Guest Ledger For: <span
                                                    class="ADA" id="guestledgerspan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="guestledgermodalLabel">Folio No.:
                                                <span class="BANX" id="guestcode2"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="guestledgeriframe" src="" frameborder="0"
                                                style="width: 100%; height: 35em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="roomchangemodal" tabindex="-1"
                                aria-labelledby="roomchangemodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="roomchangemodalLabel">Room Change For: <span
                                                    class="ADA" id="roomchangespan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="roomchangemodalLabel">Folio No.:
                                                <span style="display: none;" id="docidd"></span>
                                                <span class="BANX" id="guestcode3"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="roomchangeiframe" src="" frameborder="0"
                                                style="width: 100%; height: 37em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="advchargemodal" tabindex="-1" aria-labelledby="advchargemodalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="advchargemodalLabel">Advance Change For: <span
                                                    class="ADA" id="advchargespan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="advchargemodalLabel">Folio No.:
                                                <span style="display: none;" id="docidd"></span>
                                                <span class="BANX" id="guestcode4"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="advchargeiframe" src="" frameborder="0"
                                                style="width: 100%; height: 37em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="billsettlemodal" tabindex="-1"
                                aria-labelledby="billsettlemodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="billsettlemodalLabel">Bill Settlement For: <span
                                                    class="ADA" id="billsettlespan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="billsettlemodalLabel">Folio No.:
                                                <span style="display: none;" id="docidd"></span>
                                                <span class="BANX" id="guestcode6"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="billsettleiframe" src="" frameborder="0"
                                                style="width: 100%; height: 37em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="billprintmodal" tabindex="-1" aria-labelledby="billprintmodalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="billprintmodalLabel">Bill Print For: <span
                                                    class="ADA" id="billprintspan"></span></h5>
                                            <h5 style="right: 3rem;" class="modal-title absolute-element"
                                                id="billprintmodalLabel">Folio No.:
                                                <span class="BANX" id="guestcode5"></span>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe id="billprintiframe" src="" frameborder="0"
                                                style="width: 100%; height: 35em;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Button Groups -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="" role="group">
                                        <button disabled type="button" class="btn isbtn btn-change" data-toggle="modal"
                                            data-target="#changeprofilemodal">
                                            Change Guest Profile
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-amend" data-toggle="modal"
                                            data-target="#ammendstaymodal">
                                            Amend Stay
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-guestledger" data-toggle="modal"
                                            data-target="#guestledgermodal">
                                            Guest Ledger
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-room" data-toggle="modal"
                                            data-target="#roomchangemodal">
                                            Room Change
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-extra" data-toggle="modal"
                                            data-target="#advchargemodal">
                                            Advance Charge / Paid Out
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-billprint" data-toggle="modal"
                                            data-target="#billprintmodal" id="billprintbtn">
                                            Bill Print 📄
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-billcancel" id="billCancelBtn">
                                            Bill Cancel
                                        </button>
                                        <button disabled type="button" class="btn isbtn btn-billsettle" data-toggle="modal"
                                            data-target="#billsettlemodal" id="billSettleBtn">
                                            Bill Settle
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Container -->
                            <div class="table-container">
                                <table id="guestinhouse" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Folio No</th>
                                            <th>Room No</th>
                                            <th>Guest Name</th>
                                            <th>Arrival<br>Date</th>
                                            <th>Arr.<br>Time</th>
                                            <th>Departure<br>Date</th>
                                            <th>Guest<br>Status</th>
                                            <th>Company/Travel Agent</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Country</th>
                                            <th>Plan</th>
                                            <th>Pax</th>
                                            <th>Leader</th>
                                            <th>SN</th>
                                            <th>Bill No</th>
                                        </tr>
                                    </thead>
                                    <tbody id="guestTableBody">
                                        <!-- Dynamic rows will be inserted here -->
                                    </tbody>
                                </table>
                            </div>


                            <div class="d-flex text-center">
                                <div class="flex-fill p-2 text-white"
                                    style="background-color: #ffffff; color:black !important;">In House Guest</div>
                                <div class="flex-fill p-2 text-white" style="background-color: #90ee90;">Guest Bill Printed
                                </div>
                                <div class="flex-fill p-2 text-white" style="background-color: #FFC0CB;">Current Guest
                                    Selected</div>
                                <div class="flex-fill p-2 text-white" style="background-color: #add8e6;">Plan/Package Guest
                                </div>
                                <div class="flex-fill p-2 text-white" style="background-color: #f4d35e;">Over Stay</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('admin/css/inhousedt.css') }}">
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script>
        let dataTableInstance;

        let globalname = '';
        let folioNo = '';
        let globaldocid = '';
        let globalsno1 = '';
        let globalsno = '';
        let globalroomno = '';
        let selectedRow = null;

        // Function to fetch data from API
        function fetchGuestData() {
            $.ajax({
                url: 'inhoseroomstatusfetch',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    let guestdata = data;
                    populateTable(guestdata);
                    if ($.fn.DataTable.isDataTable('#guestinhouse')) {
                        $('#guestinhouse').DataTable().destroy();
                    }
                    dataTableInstance = new DataTable('#guestinhouse', {
                        pageLength: 100,
                        order: [[1, 'asc']]
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        // Function to populate table with data
        function populateTable(data) {
            const tbody = $('#guestTableBody');
            tbody.empty();
            let options = {
                timeZone: 'Asia/Kolkata',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };

            let currentTime = new Date().toLocaleString('en-US', options);
            let tmpncurdate = document.getElementById('sss').value;

            data.forEach((guest, index) => {
                const row = $(`
                            <tr style="background-color: ${guest.billno != '0' ? '#90ee90' : ''}" data-roomno="${guest.RoomNo}" data-docid="${guest.docid}" data-folio="${guest.FolioNo}" data-sno1="${guest.SN}" data-sno="${guest.sno}" data-billno="${guest.billno || ''}" data-guest-name="${guest.GuestName}">
                                <td>${guest.FolioNo}</td>
                                <td>${guest.RoomNo}</td>
                                <td>${guest.GuestName}</td>
                                <td>${guest.ChkInDate}</td>
                                <td>${guest.ChkTime}</td>
                                <td>${guest.DepDate}</td>
                                <td>${guest.GuestStatus || ''}</td>
                                <td>${guest.CompanyName || ''}</td>
                                <td>${guest.Adress || ''}</td>
                                <td>${guest.City || ''}</td>
                                <td>${guest.Country || ''}</td>
                                <td>${guest.Plan || ''}</td>
                                <td>${guest.Pax}</td>
                                <td>${guest.Leader || ''}</td>
                                <td>${guest.SN || ''}</td>
                                <td>${guest.billno}</td>
                            </tr>
                        `);

                if (guest.plancode != '' && guest.plancode != null) {
                    row.removeClass('selected').addClass('plantd');
                }

                if (currentTime > guest.envcheck && guest.billno == '0' && guest.depdate_minus_one <= tmpncurdate) {
                    row.removeClass('selected, plantd').addClass('delaytd');
                }

                if (guest.billno != '0') {
                    row.removeClass('selected, plantd').addClass('billprinted');
                }

                tbody.append(row);
            });
        }

        function handleRowSelection(row) {
            $('.table tbody tr').removeClass('selected');

            $('.isbtn').prop('disabled', false);

            row.addClass('selected');
            selectedRow = row;

            folioNo = row.data('folio');
            globalname = row.data('guest-name');
            let billno = row.data('billno');

            globaldocid = row.data('docid');
            globalsno1 = row.data('sno1');
            globalsno = row.data('sno');
            globalroomno = row.data('roomno');

            localStorage.setItem('idocid', globaldocid);
            localStorage.setItem('isno1', globalsno1);
            localStorage.setItem('iroomno', globalroomno);

            if (billno != '0') {
                $('#billCancelBtn').removeClass('hidden');
                $('#billSettleBtn').removeClass('hidden');
                $('#billprintbtn').addClass('hidden');
            } else {
                $('#billCancelBtn').addClass('hidden');
                $('#billSettleBtn').addClass('hidden');
                $('#billprintbtn').removeClass('hidden');
            }
        }

        $(document).on('click', '#billprintbtn', function () {
            $('#myloader').removeClass('none');
            $('#myloader').find('img').attr('src', 'admin/icons/custom/typewriter.gif');
            $('#myloader').find('.loader-text').html('Getting Bill Details');
            let docid = localStorage.getItem('idocid');
            let sno1 = localStorage.getItem('isno1');
            
            setTimeout(() => {
                $('#myloader').addClass('none');
                let checkpostxhr = new XMLHttpRequest();
                checkpostxhr.open('POST', '/checkchargecount', true);
                checkpostxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                checkpostxhr.onreadystatechange = function () {
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
                                    startposting.onreadystatechange = function () {
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
                                    startposting.send(`docid=${docid}&roomno=${globalroomno}&sno1=${sno1}&_token={{ csrf_token() }}`);
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
                checkpostxhr.onreadystatechange = function () {
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
                                    startposting.onreadystatechange = function () {
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

        $(document).on('click', '#billCancelBtn', function () {
            let docid = localStorage.getItem('idocid');
            let sno1 = localStorage.getItem('isno1');

            Swal.fire({
                icon: 'question',
                title: 'Cancel Bill',
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
                    let reason = result.value;

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/billcancel',
                        data: {
                            docid: docid,
                            sno1: sno1,
                            reason: reason
                        },
                        success: function (response) {
                            Swal.fire('Cancelled!', response.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });


        // Event handlers
        $(document).ready(function () {
            // Fetch data on page load
            fetchGuestData();

            // Handle row click
            $(document).on('click', '.table tbody tr', function () {
                handleRowSelection($(this));
            });

            // Modal event handlers
            $('#changeprofilemodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("changeprofileframe");
                let profilechangespan = document.getElementById('profilechangespan');
                let profilechangecode = document.getElementById('profilechangecode');
                profilechangespan.textContent = globalname;
                profilechangecode.textContent = folioNo;
                iframe.src = "changeprofile?docid=" + globaldocid + "&sno1=" + globalsno1;
            });

            $('#ammendstaymodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("ammenstayiframe");
                let profilechangespan = document.getElementById('ammendstayspan');
                let guestcode1 = document.getElementById('guestcode1');
                profilechangespan.textContent = globalname;
                guestcode1.textContent = folioNo;
                iframe.src = "ammendstay?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
            });

            $('#guestledgermodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("guestledgeriframe");
                let guestledgerspan = document.getElementById('guestledgerspan');
                let guestcode1 = document.getElementById('guestcode2');
                guestledgerspan.textContent = globalname;
                guestcode1.textContent = folioNo;
                iframe.src = "guestledger?docid=" + globaldocid + "&sno1=" + globalsno1;
            });

            $('#roomchangemodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("roomchangeiframe");
                let profilechangespan = document.getElementById('roomchangespan');
                profilechangespan.textContent = globalname;
                let guestcode3 = document.getElementById('guestcode3');
                guestcode3.textContent = folioNo;
                iframe.src = "roomchange?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
            });

            $('#advchargemodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("advchargeiframe");
                let profilechangespan = document.getElementById('advchargespan');
                profilechangespan.textContent = globalname;
                let guestcode4 = document.getElementById('guestcode4');
                guestcode4.textContent = folioNo;
                iframe.src = "advcharge?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
            });

            $('#billprintmodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("billprintiframe");
                let billprintspan = document.getElementById('billprintspan');
                let guestcode5 = document.getElementById('guestcode5');
                billprintspan.textContent = globalname;
                guestcode5.textContent = folioNo;
                iframe.src = "billprint?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
            });

            $('#billsettlemodal').on('show.bs.modal', function (event) {
                var iframe = document.getElementById("billsettleiframe");
                let profilechangespan = document.getElementById('billsettlespan');
                profilechangespan.textContent = globalname;
                let guestcode6 = document.getElementById('guestcode6');
                guestcode6.textContent = folioNo;
                iframe.src = "billsettle?docid=" + globaldocid + "&sno1=" + globalsno1 + "&sno=" + globalsno;
            });

            $(document).on('click', '#billCancelBtn', function () {

            });
        });

        // Refresh data function (can be called periodically)
        function refreshData() {
            fetchGuestData();
        }

        // Auto-refresh every 30 seconds (optional)
        setInterval(refreshData, 30000);
    </script>
@endsection