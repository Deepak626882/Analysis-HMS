@extends('property.layouts.main')
@section('main-container')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script> --}}
    <style>
        .modal-content {
            box-shadow: 0 0 15px #e5e8e9, 0px 0px 20px pink, inset 0px 0px 5px #1ba5dd, 0px 0px 20px #17c5ff;
            border-radius: 10px;
            backdrop-filter: drop-shadow(2px 4px 6px black);
            background: #bbb5b517;
            margin-top: 25px;
        }

        .modal.show .modal-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="reservationlist" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sn</th>
                                            <th>Book No.</th>
                                            <th>Reg. On</th>
                                            <th>Guest Name</th>
                                            <th>ArrDate</th>
                                            <th>DepDate</th>
                                            <th>Room No</th>
                                            <th>Advance</th>
                                            <th>Cancel</th>
                                            <th>Ref. Id</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sn = 1;
                                        @endphp
                                        @foreach ($data as $checkin)
                                            <tr>
                                                <td>{{ $sn++ }}</td>
                                                <td>{{ $checkin->BookNo }}</td>
                                                <td>{{ date('d-M-Y', strtotime($checkin->vdate)) }}</td>
                                                <td>{{ $checkin->GuestName }}</td>
                                                <td style="white-space: nowrap;">{{ date('d-M-Y', strtotime($checkin->ArrDate)) }}</td>
                                                <td style="white-space: nowrap;">{{ date('d-M-Y', strtotime($checkin->DepDate)) }}</td>
                                                <td>{{ $checkin->RoomNo }}</td>
                                                <td>{{ $checkin->amtcr }}</td>
                                                <td>{{ $checkin->Cancel }}</td>
                                                <td>{{ $checkin->RefBookNo }}</td>
                                                <td class="ins">
                                                    @if ($checkin->U_Name == 'Web' && $checkin->ContraDocId != '')
                                                        <a href="updatereservation?DocId={{ base64_encode($checkin->BookingDocid) }}&sno={{ base64_encode($checkin->Sno) }}">
                                                            <button class="btn btn-success btn-sm"><i class="far fa-edit"></i>Checkin</button>
                                                        </a>
                                                    @elseif ($checkin->U_Name == 'Web')
                                                        <a href="updatereservation?DocId={{ base64_encode($checkin->BookingDocid) }}&sno={{ base64_encode($checkin->Sno) }}">
                                                            <button class="btn btn-success btn-sm"><i class="far fa-edit"></i>Verify</button>
                                                        </a>
                                                    @elseif ($checkin->U_Name == 'NOSHOW')
                                                        <a href="updatereservation?DocId={{ base64_encode($checkin->BookingDocid) }}&sno={{ base64_encode($checkin->Sno) }}">
                                                            <button class="btn btn-success btn-sm"><i class="far fa-edit"></i>No Show</button>
                                                        </a>
                                                    @elseif (!in_array($checkin->U_Name, ['Web', 'NOSHOW']) && $checkin->ContraDocId == '')
                                                        <a href="updatereservation?DocId={{ base64_encode($checkin->BookingDocid) }}&sno={{ base64_encode($checkin->Sno) }}">
                                                            <button class="btn btn-success btn-sm"><i class="far fa-edit"></i>Edit</button>
                                                        </a>
                                                    @elseif ($checkin->ContraDocId != '')
                                                        <a href="updatereservation?DocId={{ base64_encode($checkin->BookingDocid) }}&sno={{ base64_encode($checkin->Sno) }}">
                                                            <button class="btn btn-success btn-sm"><i class="far fa-edit"></i>Checkin</button>
                                                        </a>
                                                    @endif
                                                    @if ($checkin->Cancel == 'N')
                                                        <a href="updatecancel?DocId={{ base64_encode($checkin->BookingDocid) }}"
                                                            onclick="return confirm('Do You want to cancel this reservation?');">
                                                            <button class="btn btn-secondary btn-sm"><i class="fa-solid fa-ban"></i>
                                                                Cancel</button>
                                                        </a>
                                                    @elseif($checkin->Cancel == 'Y')
                                                        <a href="revcancel?DocId={{ base64_encode($checkin->BookingDocid) }}"
                                                            onclick="return confirm('Do You want to Confirm this reservation?');">
                                                            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i>
                                                                Rev_Cancel</button>
                                                        </a>
                                                    @endif

                                                    @if ($checkin->Cancel == 'N')
                                                        <a>
                                                            <button data-toggle="modal" data-target="#reslettermodal-{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}"
                                                                class="btn btn-warning btn-sm"><i class="fa-solid fa-ban"></i> Res Letter</button>
                                                        </a>
                                                        <div class="modal fade" id="reslettermodal-{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <div class="text-center mb-3">
                                                                            <button docid="{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}" style="width: 7rem;" type="button"
                                                                                class="btn mailbtn btn-facebook mr-2">Mail</button>
                                                                            <button docid="{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}" style="width: 7rem;" type="button"
                                                                                class="btn printbtn btn-dribbble">Print</button>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <button style="width: 7rem;" type="button" class="btn btn-danger"
                                                                                data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($checkin->Cancel == 'Y')
                                                        <a>
                                                            <button data-toggle="modal" data-target="#cancletter-{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}"
                                                                class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i>
                                                                Canc Letter</button>
                                                        </a>
                                                        <div class="modal fade" id="cancletter-{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <div class="text-center mb-3">
                                                                            <button docid="{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}" style="width: 7rem;" type="button"
                                                                                class="btn mailbtnc btn-facebook mr-2">Mail</button>
                                                                            <button docid="{{ $checkin->BookNo }}-Sno-{{ $checkin->Sno }}-vprefix-{{ $checkin->Vprefix }}" style="width: 7rem;" type="button"
                                                                                class="btn printbtnc btn-dribbble">Print</button>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <button style="width: 7rem;" type="button" class="btn btn-danger"
                                                                                data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($checkin->Cancel == 'N')
                                                        <a onclick="return confirm('Do you want to register card?');">
                                                            <button class="btn btn-info btn-sm"><i
                                                                    class="fa-regular fa-address-card"></i>
                                                                Reg Card</button>
                                                        </a>
                                                    @endif

                                                    <a href="advancedeposit?DocId={{ base64_encode($checkin->BookingDocid) }}&Sno={{ base64_encode($checkin->Sno) }}">
                                                        <button class="btn btn-info btn-sm"><i
                                                                class="fa-solid fa-money-bill-transfer"></i>
                                                            Deposit</button>
                                                    </a>

                                                    @if ($channelenviro->checkyn == 'N')
                                                        <a
                                                            href="deletereservation?DocId={{ base64_encode($checkin->BookingDocid) }}">
                                                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>
                                                                Delete</button>
                                                        </a>
                                                    @endif
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

        <!-- #/ container -->
    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#reservationlist', {
                "pageLength": 15
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            let csrf = "{{ csrf_token() }}";
            $(document).on('click', '.printbtn', function() {
                let docid = $(this).attr('docid');
                let fix = docid.split('-');
                let bookno = fix[0];
                let sno = fix[2];
                let year = fix[4];
                window.open(`resletter?bookno=${bookno}&sno=${sno}&year=${year}`, '_blank');
                $(`#reslettermodal-${docid}`).modal('hide');
            });

            $(document).on('click', '.printbtnc', function() {
                let docid = $(this).attr('docid');
                let fix = docid.split('-');
                let bookno = fix[0];
                let sno = fix[2];
                let year = fix[4];
                window.open(`cancletter?bookno=${bookno}&sno=${sno}&year=${year}`, '_blank');
                $(`#cancletter-${docid}`).modal('hide');
            });

            $(document).on('click', '.mailbtn', function() {
                let docid = $(this).attr('docid');
                let fix = docid.split('-');
                let bookno = fix[0];
                let sno = fix[2];
                let postdata = {
                    'bookno': bookno,
                    'sno': sno
                };
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(postdata)
                };

                fetch('/resmailposting', options)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        pushNotify('success', 'Reservation', data.message, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                    })
                    .catch(error => {
                        pushNotify('error', 'Reservation', error, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                    })

            });
        });
        // document.addEventListener('DOMContentLoaded', function() {
        //     initializeDataTable('reservationlist', {
        //         customSearchColumns: [3, 6],
        //         buttons: ['excel', 'print']
        //     });
        // });

        // function initializeDataTable(tableId, options = {}) {
        //     const {
        //         columns = [0, 1, 2, 3, 4],
        //             customSearchColumns = [],
        //             exportColumns = null,
        //             buttons = ['copy', 'csv', 'excel', 'pdf', 'print']
        //     } = options;

        //     const tableConfig = {
        //         pageLength: 10,
        //         lengthMenu: [
        //             [10, 25, 50, -1],
        //             [10, 25, 50, "All"]
        //         ],
        //         dom: 'Bfrtip',
        //         buttons: buttons.map(button => ({
        //             extend: button,
        //             exportOptions: {
        //                 columns: exportColumns || ':visible'
        //             },
        //             className: 'custom-dt-button'
        //         })),
        //         initComplete: function() {
        //             const api = this.api();

        //             if (customSearchColumns.length > 0) {
        //                 customSearchColumns.forEach((index) => {
        //                     let columnHeader = $(`#reservationlist thead th:nth-child(${index + 1})`).text();

        //                     const input = $('<input type="text" placeholder="Search ' + columnHeader + '">')
        //                         .appendTo($(api.column(index).header()).empty())
        //                         .on('keyup change', function() {
        //                             api.column(index).search($(this).val()).draw();
        //                         });
        //                 });
        //             }
        //         }
        //     };

        //     return new DataTable('#' + tableId, tableConfig);
        // }
    </script>
@endsection
