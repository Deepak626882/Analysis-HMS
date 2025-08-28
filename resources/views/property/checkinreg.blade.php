@extends('property.layouts.main')
@section('main-container')
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row justify-content-around">
                                    <input type="hidden" value="{{ $company->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ $company->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ $company->propertyid }}" id="propertyid" name="propertyid">
                                    <input type="hidden" value="{{ $company->comp_name }}" id="compname" name="compname">
                                    <input type="hidden" value="{{ $company->address1 }}" id="address" name="address">
                                    <input type="hidden" value="{{ $company->city }}" id="city" name="city">
                                    <input type="hidden" value="{{ $company->mobile }}" id="compmob" name="compmob">
                                    <input type="hidden" value="{{ $statename }}" id="statename" name="statename">
                                    <input type="hidden" value="{{ $company->pin }}" id="pin" name="pin">
                                    <input type="hidden" value="{{ $company->email }}" id="email" name="email">
                                    <input type="hidden" value="{{ $company->logo }}" id="logo" name="logo">
                                    <input type="hidden" value="{{ $company->u_name }}" id="u_name" name="u_name">
                                    <input type="hidden" value="{{ $company->gstin }}" id="gstin" name="gstin">
                                    <div class="text-center titlep">
                                        <h3>{{ $company->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $company->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $statename . ' - ' . $company->city . ' - ' . $company->pin }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">Check In Register</p>
                                        <p style="text-align:left;margin-top:-10px; font-size:16px;">From Date: <span id="fromdatep"></span> To Date:
                                            <span id="todatep"></span>
                                        </p>
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
                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" name="fetchbutton" type="button" class="btn btn-success">
                                            Refresh <i class="fa-solid fa-arrows-rotate"></i>
                                        </button>
                                    </div>
                                </div>

                            </form>

                            <div class="row table-responsive">
                                <table id="checkinregister" class=" table table-border table-hover table striped border rounded">
                                    <thead>
                                        <tr>
                                            <th>GRC No.</th>
                                            <th>Guest Name</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Nationaity</th>
                                            <th>Mobile No.</th>
                                            <th>Room</th>
                                            <th>Occ</th>
                                            <th>R Rate</th>
                                            <th>In Date</th>
                                            <th>Out Date</th>
                                            <th>Arr From/Dest.</th>
                                            <th>Purpose Of Visit</th>
                                            <th>Advance</th>
                                            <th>Travel</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
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
            let dataTableInitialized = false;

            $(document).on('change', '#fromdate', function() {
                validateFinancialYear('#fromdate');
            });
            $(document).on('change', '#todate', function() {
                validateFinancialYear('#todate');
            });

            $(document).on('click', '#fetchbutton', function() {

                pushNotify('info', 'Check In Register', 'Fetching Report, Please Wait...', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                $('#checkinregister').DataTable().destroy();
                let compname = $('#compname').val();
                let fromdate = $('#fromdate').val();
                let todate = $('#todate').val();
                $('#fromdatep').text(dmy(fromdate));
                $('#todatep').text(dmy(todate));
                let tablebody = $('#checkinregister tbody');
                tablebody.empty();
                let tfoot = $('#checkinregister tfoot');
                tfoot.empty();
                let itemnamexhr = new XMLHttpRequest();
                itemnamexhr.open('POST', '/fetchcheckinregdata', true);
                itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                itemnamexhr.onreadystatechange = function() {
                    if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {
                        let results = JSON.parse(itemnamexhr.responseText);
                        if (results.length == 0) {
                            pushNotify('info', 'No Data Found', 'No Data Found for the Selected Dates');
                        }
                        let rows = '';
                        let totalrooms = 0;
                        let totalguests = 0;
                        let totaladvance = 0;
                        let totalroomrate = 0;
                        results.forEach((item, index) => {
                            totalrooms++;
                            rows += `<tr>
                        <td>${item.folio_no == null ? '' : item.folio_no}</td>
                        <td>${item.Name == null ? '' : item.Name}</td>
                        <td>${item.add1 == null ? '' : item.add1} ${item.add2 == null ? '' : item.add2}</td>
                        <td>${item.city == null ? '' : item.city}</td>
                        <td>${item.nationality == null ? '' : item.nationality}</td>
                        <td>${item.mobile_no == null ? '' : item.mobile_no}</td>
                        <td>${item.RoomNo == null ? '' : item.RoomNo}</td>
                        <td>${item.TotalGuest == null ? '0' : item.TotalGuest}</td>
                        <td>${item.RoomRate == null ? '0' : item.RoomRate}</td>
                        <td>${item.ChkinDate == null ? '' : dmy(item.ChkinDate)} ${item.ChkinTime == null ? '' : item.ChkinTime.substr(0, 5)}</td>
                        <td>${item.chkoutdate == null ? '' : dmy(item.chkoutdate)} ${item.chkouttime == null ? '' : item.chkouttime.substr(0, 5)}</td>
                        <td>${item.arrfrom == null ? '' : item.arrfrom}/${item.destination == null ? '' : item.destination}</td>
                        <td>${item.PurVisit == null ? '' : item.PurVisit}</td>
                        <td>${item.advance == null ? '' : item.advance}</td>
                        <td>${item.travelagent== null ? '' : item.travelagent}</td>
                        <td>${item.U_Name == null ? '' : item.U_Name}</td>
                        </tr>`;
                            totalroomrate += parseFloat(item.RoomRate ?? 0.00);
                            totalguests += parseInt(item.TotalGuest ?? 0);
                            totaladvance += parseFloat(item.advance ?? 0.00);
                        });
                        tablebody.append(rows);
                        let tfootdata = `<tr class="font-weight-bold">
                        <td colspan="6">Total</td>
                        <td>${totalrooms}</td>
                        <td>${totalguests}</td>
                        <td>${totalroomrate.toFixed(2)}</td>
                        <td colspan="4"></td>
                        <td>${totaladvance.toFixed(2)}</td>
                        <td colspan="2"></td>
                        </tr>`;
                        tfoot.append(tfootdata);
                        if (!dataTableInitialized) {
                            $('#checkinregister').DataTable({
                                dom: 'Bfrtip',
                                pageLength: 15,
                                buttons: [{
                                        extend: 'excelHtml5',
                                        text: 'Excel <i class="fa fa-file-excel-o"></i>',
                                        title: compname,
                                        filename: 'Checkin Register',
                                        footer: true
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        text: 'Csv <i class="fa-solid fa-file-csv"></i>',
                                        title: compname,
                                        filename: 'Checkin Register',
                                        footer: true,
                                    },
                                    {
                                        extend: 'print',
                                        text: 'Print <i class="fa-solid fa-print"></i>',
                                        title: 'Checkin Register',
                                        filename: 'Checkin Register',
                                        footer: true,
                                        customize: function(win) {
                                            $(win.document.body).find('th').removeClass('sorting sorting_asc sorting_desc');
                                            $(win.document.body).find('table').css('margin-top', '100px');
                                            $(win.document.body).prepend('<div class="titlep">' + $('.titlep').html() + '</div>');
                                        }
                                    }
                                ],
                            });
                        }
                    }
                }
                itemnamexhr.send(`fromdate=${fromdate}&todate=${todate}&_token={{ csrf_token() }}`);
            });
            $('#fromdate').trigger('change');
        });
    </script>
@endsection
