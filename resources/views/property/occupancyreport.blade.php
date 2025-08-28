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
    <style>
        .details {
            font-weight: 800;
            color: black;
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body box animate__animated animate__bounceIn">

                            <form action="">
                                <div class="row justify-content-around">
                                    <input type="hidden" value="{{ $comp->start_dt }}" name="start_dt" id="start_dt">
                                    <input type="hidden" value="{{ $comp->end_dt }}" name="end_dt" id="end_dt">
                                    <input type="hidden" value="{{ $comp->propertyid }}" id="propertyid" name="propertyid">
                                    <input type="hidden" value="{{ $comp->comp_name }}" id="compname" name="compname">
                                    <input type="hidden" value="{{ $comp->address1 }}" id="address" name="address">
                                    <input type="hidden" value="{{ $comp->city }}" id="city" name="city">
                                    <input type="hidden" value="{{ $comp->mobile }}" id="compmob" name="compmob">
                                    <input type="hidden" value="{{ $statename }}" id="statename" name="statename">
                                    <input type="hidden" value="{{ $comp->pin }}" id="pin" name="pin">
                                    <input type="hidden" value="{{ $comp->email }}" id="email" name="email">
                                    <input type="hidden" value="{{ $comp->logo }}" id="logo" name="logo">
                                    <input type="hidden" value="{{ $comp->u_name }}" id="u_name" name="u_name">
                                    <input type="hidden" value="{{ $comp->gstin }}" id="gstin" name="gstin">
                                    <input class="none" type="date" value="{{ $ncurdate }}" name="ncurdatef" id="ncurdatef">
                                    <div class="">
                                        <div class="form-group">
                                            <label for="fordate" class="col-form-label">From Date <i
                                                    class="fa-regular fa-calendar mb-1"></i></label>
                                            <input type="date" value="{{ $ncurdate }}" class="form-control" name="fordate"
                                                id="fordate">
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="printcondition" class="col-form-label">Print Condition</label>
                                        <select class="form-control" name="printcondition" id="printcondition">
                                            <option value="1">Room Rate With Discount</option>
                                            <option value="2">With Room Rate</option>
                                            <option value="3" selected>Without Room Rate</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <label for="plainbifurcation" class="col-form-label">Plain Bifurcation</label>
                                        <select class="form-control" name="plainbifurcation" id="plainbifurcation">
                                            <option value="">Select</option>
                                            <option value="Y">Yes</option>
                                            <option value="N" selected>No</option>
                                        </select>
                                    </div>
                                    <div class="none">
                                        <label for="sortedby" class="col-form-label">Sorted By</label>
                                        <select class="form-control" name="sortedby" id="sortedby">
                                            <option value="room_mast.rcode" selected>Room Number</option>
                                            <option value="roomocc.name">Guest Name</option>
                                            <option value="companyname">Company Name</option>
                                        </select>
                                    </div>
                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" name="fetchbutton" type="button" class="btn btn-success">Refresh <i class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>
                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" onclick="printTable()" name="fetchbutton" type="button" class="btn btn-info">Print</button>
                                    </div>
                                </div>
                            </form>

                            <div id="table-container">
                                <table id="occupancytable" class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>GRC No.</th>
                                            <th>Room No.</th>
                                            <th>Name</th>
                                            <th>Pax</th>
                                            <th>Nationality</th>
                                            <th>Comp/T.A.</th>
                                            <th>Plan</th>
                                            <th>In Dt <i class="fa-regular fa-calendar mb-1"></i></th>
                                            <th>Dep Dt <i class="fa-regular fa-calendar mb-1"></i></th>
                                            <th>Mobile</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot style="font-size: larger;">

                                    </tfoot>
                                </table>
                            </div>
                            <div id="details" class="details">

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
            $(document).on('change', '#fordate', function() {
                validateFinancialYear('#fordate');
            });
            
            $(document).on('click', '#fetchbutton', function() {
                $('#occupancytable').DataTable().destroy();
                let fordate = $('#fordate').val();
                if (fordate == '' || fordate == null) {
                    pushNotify('error', 'Occupancy Report', 'Please Select For Date', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                    return;
                }
                let sortedby = $('#sortedby').val();
                let printcondition = $('#printcondition').val();
                let throom = $('#occupancytable thead tr').find('th:nth-child(2)');
                if (printcondition == 2) {
                    if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() == 'Room Disc') {
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                    } else if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() != 'Room Disc') {
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                    }
                    let createroomrate = '<th>Room Rate</th>';
                    let createrrtax = '<th>Room Tax</th>';
                    throom.after(createroomrate);
                    $('#occupancytable thead tr').find('th:nth-child(3)').after(createrrtax);
                } else if (printcondition == 3) {
                    if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() == 'Room Disc') {
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                    } else if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() != 'Room Disc') {
                        $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                        $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                    }
                } else if (printcondition == 1) {
                    if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate') {
                        if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() == 'Room Disc') {
                            $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                            $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                            $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                            $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                        } else if ($('#occupancytable thead tr').find('th:nth-child(3)').text() == 'Room Rate' && $('#occupancytable thead tr').find('th:nth-child(4)').text() != 'Room Disc') {
                            $('#occupancytable thead tr').find('th:nth-child(3)').remove();
                            $('#occupancytable tbody tr').find('td:nth-child(3)').remove();
                        }
                    }
                    let createroomrate = '<th>Room Rate</th>';
                    let createroomdisc = '<th>Room Disc</th>';
                    let createrrtax = '<th>Room Tax</th>';
                    throom.after(createroomrate);
                    $('#occupancytable thead tr').find('th:nth-child(3)').after(createroomdisc);
                    $('#occupancytable thead tr').find('th:nth-child(4)').after(createrrtax);
                }
                let fetchoocxhr = new XMLHttpRequest();
                fetchoocxhr.open('POST', '/fetchoocxhr', true);
                fetchoocxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                fetchoocxhr.onreadystatechange = function() {
                    if (fetchoocxhr.readyState === 4 && fetchoocxhr.status === 200) {
                        let result = JSON.parse(fetchoocxhr.responseText);
                        let occdata = result.occdata;
                        if (occdata.length == 0) {
                            pushNotify('info', 'Occupancy Report', `${occdata.length} rows found`, 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                            $('#occupancytable tbody').empty();
                            $('#occupancytable tfoot').empty();
                            $('#details').html('');
                            return;
                        }
                        pushNotify('success', 'Occupancy Report', `${occdata.length} rows found`, 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                        $('#occupancytable tbody').empty();
                        $('#occupancytable tfoot').empty();
                        $('#details').html('');
                        let row = '';
                        let totaladult = 0;
                        let totalchild = 0;
                        let totalplan = [];
                        occdata.forEach((data, index) => {
                            totaladult += parseInt(data.adult);
                            totalchild += parseInt(data.children);
                            row += `<tr>
                                <td>${data.foliono}</td>
                                <td>${data.roomno}</td>`
                            if (printcondition == 2) {
                                row += `<td>${data.roomrate ?? 0.00}</td>`;
                                row += `<td>${data.RRTaxInc == 'N' ? 'No' : 'Yes'}</td>`;
                            } else if (printcondition == 1) {
                                row += `<td>${data.roomrate ?? 0.00}</td>`;
                                row += `<td>${data.roomdisc ?? 0.00}</td>`;
                            }
                            row += `<td>${data.guestname}</td>
                                <td>${data.adult} / ${data.children}</td>
                                <td>${data.nationality}</td>
                               <td>${data.companyname || data.travelname ? `${data.companyname || ''}${data.companyname && data.travelname ? ' / ' : ''}${data.travelname || ''}` : ''}</td>
                                <td>${data.tarrif ?? ''}</td>
                                <td>${data.chkindate}</td>
                                <td>${data.depdate}</td>
                                <td>${data.mobileno ?? ''}</td>`;

                            row += `</tr>`;

                            if (data.tarrif !== null) {
                                totalplan.push(data.tarrif);
                            }
                        });

                        let planscount = {};
                        totalplan.forEach((plan, index) => {
                            planscount[plan] = (planscount[plan] || 0) + 1;
                        });

                        let countplans = Object.entries(planscount).map(([plan, count]) => `${plan}: ${count}`).join(', ');
                        $('#occupancytable tbody').append(row);
                        let tfoot = `<p>No. Of Rooms: ${occdata.length}</p>
                        <p>Dayuse: ${result.dayuse} </p>
                        <p>Total Person: ${totaladult} / ${totalchild}</p>
                        <p>${countplans}</p>`;
                        $('#details').html(tfoot);
                        let compname = $('#compname').val();
                        let details = $('#details').html();
                        if (!dataTableInitialized) {
                            $('#occupancytable').DataTable({
                                dom: 'Bfrtip',
                                pageLength: 15,
                                buttons: [{
                                        extend: 'excelHtml5',
                                        text: 'Excel <i class="fa fa-file-excel-o"></i>',
                                        title: compname,
                                        filename: 'Occupancy Report',
                                        exportOptions: {
                                            rows: function(idx, data, node) {
                                                return !$(node).hasClass('none');

                                            }
                                        },
                                        footer: true
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        text: 'Csv <i class="fa-solid fa-file-csv"></i>',
                                        title: compname,
                                        filename: 'Occupancy Report',
                                        footer: true,
                                        exportOptions: {
                                            rows: function(idx, data, node) {
                                                return !$(node).hasClass('none');
                                            }
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        text: 'Print <i class="fa-solid fa-print"></i>',
                                        title: 'Occupancy Report',
                                        filename: 'Occupancy Report',
                                        footer: true,
                                        exportOptions: {
                                            rows: function(idx, data, node) {
                                                return !$(node).hasClass('none');
                                            }
                                        },
                                        customize: function(win) {
                                            $(win.document.body).find('th').removeClass('sorting sorting_asc sorting_desc');
                                            $(win.document.body).find('table').css('margin-top', '115px');
                                            $(win.document.body).prepend('<div class="titlep">' + $('.titlep').html() + '</div>');
                                            $(win.document.body).append('<div class="details">' + details + '</div>');
                                            $(win.document.body).find('table').addClass('print-landscape');
                                            var css = '@page { size: portrait; }';
                                            var head = win.document.head || win.document.getElementsByTagName('head')[0];
                                            var style = win.document.createElement('style');
                                            style.type = 'text/css';
                                            style.media = 'print';
                                            if (style.styleSheet) {
                                                style.styleSheet.cssText = css;
                                            } else {
                                                style.appendChild(win.document.createTextNode(css));
                                            }
                                            head.appendChild(style);
                                        }
                                    }
                                ],
                            });
                        }
                    }
                };
                fetchoocxhr.send(`fordate=${fordate}&sortedby=${sortedby}&printcondition=${printcondition}&_token={{ csrf_token() }}`);
            });
        });
    </script>
@endsection
