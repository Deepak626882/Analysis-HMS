@extends('property.layouts.main')
@section('main-container')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>

    <div class="content-body possalereg">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body box animate__animated animate__bounceIn">
                            <form action="">
                                <div class="row justify-content-around">
                                    <input type="hidden" value="{{ $comp->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ $comp->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ $fromdate }}" name="ncurdatef" id="ncurdatef">
                                    <input type="hidden" value="{{ $comp->propertyid }}" id="propertyid" name="propertyid">

                                    <div class="text-center titlep">
                                        <h3>{{ $comp->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">Arrival List Report</p>
                                        <p style="text-align:left;margin-top:-10px; font-size:16px;">From Date: <span id="fromdatep"></span> To Date: <span id="todatep"></span></p>
                                    </div>

                                    <div class="">
                                        <div class="form-group">
                                            <label for="fromdate" class="col-form-label">From Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $fromdate }}" class="form-control" name="fromdate" id="fromdate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="todate" class="col-form-label">To Date <i class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $fromdate }}" class="form-control" name="todate" id="todate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="pendingyn" class="col-form-label">Pending Y/N</label>
                                        <select class="form-control" name="pendingyn" id="pendingyn">
                                            <option value="pending">Pending</option>
                                            <option value="all" selected>All</option>
                                        </select>
                                    </div>

                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" name="fetchbutton" type="button" class="btn btn-success">Refresh <i class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-3 table-responsive">
                                <table id="arrival-list-table" class="table -table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Res. No</th>
                                            <th>Guest Name</th>
                                            <th>MobNo</th>
                                            <th>Company</th>
                                            <th>Travel</th>
                                            <th>Room Details</th>
                                            <th>Arrival Date</th>
                                            <th>Pax</th>
                                            <th>Child</th>
                                            <th>Departure Date</th>
                                            <th>Plan Name</th>
                                            <th>Room No</th>
                                            <th>Room Type</th>
                                            <th>Booked By</th>
                                            <th>Reservation Status</th>
                                            <th>Remarks</th>
                                            <th>Tarrif</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let dataTable;

            $(document).on('click', '#fetchbutton', function() {
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                let pendingyn = $('#pendingyn').val();

                if (fromdate == '' || todate == '') {
                    pushNotify('error', fromdate == '' ? 'Please Select From Date' : 'Please Select To Date');
                    return;
                }

                $('#myloader').removeClass('none');

                $.ajax({
                    url: '/arrivallistfetch',
                    method: 'POST',
                    data: {
                        fromdate: fromdate,
                        todate: todate,
                        pendingyn: pendingyn,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(results) {
                        $('#myloader').addClass('none');
                        $('#fromdatep').text(formatDate(fromdate));
                        $('#todatep').text(formatDate(todate));

                        if (dataTable) {
                            dataTable.destroy();
                        }

                        let tbody = $('#arrival-list-table tbody');
                        tbody.empty();

                        if (results && results.data && Array.isArray(results.data)) {
                            results.data.forEach(function(item) {
                                tbody.append(`
                                <tr>
                                    <td>${item.ResNo || ''}</td>
                                    <td>${item.GuestName || ''}</td>
                                    <td>${item.MobNo || ''}</td>
                                    <td>${item.Company || ''}</td>
                                    <td>${item.travelname || ''}</td>
                                    <td>${item.RoomDet || ''}</td>
                                    <td>${item.ArrDate || ''}</td>
                                    <td>${item.Pax || ''}</td>
                                    <td>${item.Child || ''}</td>
                                    <td>${item.DepDate || ''}</td>
                                    <td>${item.PlanName || ''}</td>
                                    <td>${item.RoomNo || ''}</td>
                                    <td>${item.RoomType || ''}</td>
                                    <td>${item.BookedBy || ''}</td>
                                    <td>${item.ResStatus || ''}</td>
                                    <td>${item.Remarks || ''}</td>
                                    <td>${item.tarrifamount || '0.00'}</td>
                                </tr>
                            `);
                            });
                        }

                        dataTable = $('#arrival-list-table').DataTable({
                            scrollX: true,
                            dom: 'Bfrtip',
                            buttons: [{
                                    extend: 'print',
                                    title: '',
                                    messageTop: function() {
                                        return $('.titlep').html();
                                    },
                                    // customize: function(win) {
                                    //     $(win.document.body)
                                    //         .css('font-size', '10pt')
                                    //         .prepend($('.titlep').html());
                                    //     $(win.document.body).find('table')
                                    //         .addClass('compact')
                                    //         .css('font-size', 'inherit');
                                    // }
                                },
                                'excel',
                                'csv',
                                'pdf'
                            ]
                        });
                    },
                    error: function() {
                        $('#myloader').addClass('none');
                        pushNotify('error', 'Failed to fetch data');
                    }
                });
            });

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return
                date.toLocaleDateString('en-US', options);
            }
           
        });
    </script>
@endsection
