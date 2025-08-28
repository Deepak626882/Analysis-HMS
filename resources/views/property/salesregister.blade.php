@extends('property.layouts.main')
@section('main-container')
    {{-- ✅ DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

    {{-- ✅ jQuery + DataTables JS --}}
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


                                    <div class="text-center titlep mb-3">
                                        <h3>{{ $comp->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">
                                            {{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}
                                        </p>
                                        <p style="margin-top:-10px; font-size:16px;">Sales Register Report</p>
                                        <p style="text-align:left;margin-top:-10px; font-size:16px;">
                                            From Date: <span id="fromdatep"></span> To Date: <span id="todatep"></span>
                                        </p>
                                    </div>


                                    <div>
                                        <div class="form-group">
                                            <label for="fromdate" class="col-form-label">From Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $fromdate }}" class="form-control"
                                                name="fromdate" id="fromdate">
                                        </div>
                                    </div>


                                    <div>
                                        <div class="form-group">
                                            <label for="todate" class="col-form-label">To Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $todate }}" class="form-control"
                                                name="todate" id="todate">
                                        </div>
                                    </div>


                                    <div>
                                        <label for="itemwise" class="col-form-label">Item Wise</label>
                                        <select class="form-control" name="itemwise" id="itemwise">
                                            <option value="yes">YES</option>
                                            <option value="no" selected>NO</option>
                                        </select>
                                    </div>


                                    <div style="margin-top: 30px;">
                                        <button id="fetchbutton" name="fetchbutton" type="button"
                                            class="btn btn-success">Refresh <i class="fa-solid fa-arrows-rotate"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>


                            <div id="myloader" class="none text-center my-3">Loading...</div>


                            <div class="mt-3 table-responsive">
                                <table id="arrival-list-table" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>SNo.</th>
                                            <th>Bill No</th>
                                            <th>Bill Date</th>
                                            <th>Party Name</th>
                                            <th>No. Of Pax</th>
                                            <th>Total Per Cover</th>
                                            <th>Discount</th>
                                            <th>Taxable</th>
                                            <th>NonTaxable</th>
                                            <th>Tax</th>
                                            <th>SRV. Charge</th>
                                            <th>Round Off</th>
                                            <th>Net Amount</th>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            function loadSalesRegister(fromdate, todate, itemwise) {
                $('#myloader').removeClass('none');

                $.ajax({
                    url: "{{ route('fetchsalesregister') }}",
                    method: 'POST',
                    data: {
                        fromdate,
                        todate,
                        itemwise
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
                            let sno = 1;
                            results.data.forEach(function(item) {
                                tbody.append(`
                            <tr>
                                <td>${sno++}</td>
                                <td>${item.vno || ''}</td>
                                <td>${item.vdate || ''}</td>
                                <td>${item.party || ''}</td>
                                <td>${item.noofpax || ''}</td>
                                <td>${item.TotalPerCover || ''}</td>
                                <td>${item.discamt || ''}</td>
                                <td>${item.taxable || ''}</td>
                                <td>${item.nontaxable || ''}</td>
                                <td>0</td> <!-- Tax placeholder -->
                                <td>0</td> <!-- Service Charge placeholder -->
                                <td>${item.roundoff || ''}</td>
                                <td>${item.Amount || ''}</td>
                            </tr>
                        `);
                            });
                        }

                    },
                    error: function() {
                        $('#myloader').addClass('none');
                        pushNotify('error', 'Failed to fetch data');
                    }
                });
            }

            $('#fetchbutton').on('click', function() {
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                let itemwise = $('#itemwise').val();

                if (fromdate === '' || todate === '') {
                    pushNotify('error', fromdate === '' ? 'Please Select From Date' :
                        'Please Select To Date');
                    return;
                }
                loadSalesRegister(fromdate, todate, itemwise);
            });

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function pushNotify(type, message) {
                alert(type.toUpperCase() + ': ' + message);
            }

        });
    </script>
@endsection