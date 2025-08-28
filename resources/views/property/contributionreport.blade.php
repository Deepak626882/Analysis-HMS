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
        .country-rep {
            font-size: 14px;
        }

        .country-rep .parameter-section {
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .country-rep .title-bar {
            background-color: #486b8a;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }

        .country-rep .table th {
            background-color: #5c2d91;
            color: white;
            vertical-align: middle;
            text-align: center;
        }

        .country-rep .company-name {
            font-weight: bold;
            color: #00008b;
        }

        .country-rep .total-column,
        .country-rep .revenue-column {
            background-color: #e6e6fa;
            font-weight: bold;
        }

        .country-rep .footer-info {
            background-color: #d3d3d3;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 3px;
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body checkoutreport country-rep">
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
                                    <div class="text-center titlep">
                                        <h3>{{ $comp->comp_name }}</h3>
                                        <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">
                                            {{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}</p>
                                        <p style="margin-top:-10px; font-size:16px;">Contribuition Report</p>
                                    </div>
                                    <div class="">
                                        <label for="vprefix">For Year</label>
                                        <select class="form-control" name="vprefix" id="vprefix">
                                            @foreach ($years as $item)
                                                <option value="{{ $item->prefix }}">{{ $item->prefix }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @php
                                        $selectedMonth = date('m', strtotime($ncurdate));
                                    @endphp

                                    <div class="">
                                        <div class="form-group">
                                            <label for="formonth" class="col-form-label">For Month <i class="fa-regular fa-calendar mb-1"></i></label>
                                            <select class="form-control" name="formonth" id="formonth">
                                                <option value="01" {{ $selectedMonth == '01' ? 'selected' : '' }}>January</option>
                                                <option value="02" {{ $selectedMonth == '02' ? 'selected' : '' }}>February</option>
                                                <option value="03" {{ $selectedMonth == '03' ? 'selected' : '' }}>March</option>
                                                <option value="04" {{ $selectedMonth == '04' ? 'selected' : '' }}>April</option>
                                                <option value="05" {{ $selectedMonth == '05' ? 'selected' : '' }}>May</option>
                                                <option value="06" {{ $selectedMonth == '06' ? 'selected' : '' }}>June</option>
                                                <option value="07" {{ $selectedMonth == '07' ? 'selected' : '' }}>July</option>
                                                <option value="08" {{ $selectedMonth == '08' ? 'selected' : '' }}>August</option>
                                                <option value="09" {{ $selectedMonth == '09' ? 'selected' : '' }}>September</option>
                                                <option value="10" {{ $selectedMonth == '10' ? 'selected' : '' }}>October</option>
                                                <option value="11" {{ $selectedMonth == '11' ? 'selected' : '' }}>November</option>
                                                <option value="12" {{ $selectedMonth == '12' ? 'selected' : '' }}>December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="">
                                        <div class="form-group">
                                            <label for="type" class="col-form-label">For Type</label>
                                            <select class="form-control" name="type" id="type">
                                                <option value="Corporate">Company</option>
                                                <option value="Travel Agency">Travel Agent</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="margin-top: 30px;" class="">
                                        <button id="fetchbutton" name="fetchbutton" type="button"
                                            class="btn btn-success">Refresh <i
                                                class="fa-solid fa-arrows-rotate"></i></button>
                                    </div>

                                </div>
                            </form>
                            <button id="printBtn" class="btn btn-primary mb-3">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <button id="exportExcelBtn" class="btn btn-success mb-3 ms-2">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                            <div class="table-responsive">
                                <table id="companynights" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>01</th>
                                            <th>02</th>
                                            <th>03</th>
                                            <th>04</th>
                                            <th>05</th>
                                            <th>06</th>
                                            <th>07</th>
                                            <th>08</th>
                                            <th>09</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                            <th>14</th>
                                            <th>15</th>
                                            <th>16</th>
                                            <th>17</th>
                                            <th>18</th>
                                            <th>19</th>
                                            <th>20</th>
                                            <th>21</th>
                                            <th>22</th>
                                            <th>23</th>
                                            <th>24</th>
                                            <th>25</th>
                                            <th>26</th>
                                            <th>27</th>
                                            <th>28</th>
                                            <th>29</th>
                                            <th>30</th>
                                            <th>31</th>
                                            <th class="total-column">Tot</th>
                                            <th class="revenue-column">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('exportExcelBtn').addEventListener('click', function() {
            // Function to convert HTML table to Excel file
            let table = document.querySelector('table');
            let html = table.outerHTML;

            // Add Excel styling
            let excelStyles = '<style>table { border-collapse: collapse; } th, td { border: 1px solid #000; }</style>';

            // Create a blob with the Excel content
            let blob = new Blob([excelStyles + html], {
                type: 'application/vnd.ms-excel'
            });

            // Create a download link
            let downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = 'Company_Wise_Nights_Report.xls';

            // Trigger download
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        });
    </script>

    <script>
        $(document).ready(function() {

            $(document).on('click', '#printBtn', function() {
                let tbody = $('#companynights').html();
                let newWindow = window.open('contribuitionreportprint', '_blank');

                newWindow.onload = function() {
                    $('#contryprint', newWindow.document).html(tbody);
                    $('#formonth', newWindow.document).text(`For Month: ${$('#formonth').find('option:selected').text()}/${$('#vprefix').val()}`)
                    $('#type', newWindow.document).text(`For: ${$('#type').val()}`);
                    $('#details', newWindow.document).html($('.titlep').html());
                }
            });

            $(document).on('click', '#fetchbutton', function() {
                showLoader();
                pushNotify('info', 'Company Wise Nights', 'Fetching Report, Please Wait...', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'right top');
                $('#fomsalesummary').DataTable().destroy();
                let vprefix = $('#vprefix').val();
                let formonth = $('#formonth').val();
                let type = $('#type').val();

                if (vprefix == '' || formonth == '' || type == '') {
                    pushNotify('error', 'Fill Required Fields');
                    return;
                }
                let thead = $('#companynights thead');
                let tbody = $('#companynights tbody');
                let tfoot = $('#companynights tfoot');

                tbody.empty();
                tfoot.empty();

                let compname = $('#compname').val();
                let contribuition = new XMLHttpRequest();
                contribuition.open('POST', '/fetchcontribuition', true);
                contribuition.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                contribuition.onreadystatechange = function() {
                    if (contribuition.readyState === 4 && contribuition.status === 200) {
                        let resulttmp = JSON.parse(contribuition.responseText);
                        let result = resulttmp;
                        if (result.length == 0) {
                            pushNotify('info', 'No Data Found', 'No Data Found for the Selected Month');
                        } else {

                            populateTable(result);

                            setTimeout(hideLoader, 1000);
                        }
                    }
                }
                contribuition.send(`vprefix=${vprefix}&formonth=${formonth}&type=${type}&_token={{ csrf_token() }}`);
            });

        });

        function populateTable(data) {
            let tableRows = '';

            if (data.length === 0) {
                tableRows = '<tr><td colspan="34" class="text-center">No data available for the selected period.</td></tr>';
            } else {
                data.forEach(function(item) {
                    tableRows += '<tr>';
                    tableRows += '<td class="company-name">' + item.company_name + '</td>';

                    for (let day = 1; day <= 31; day++) {
                        const dayStr = day.toString().padStart(2, '0');
                        tableRows += '<td>' + (item[dayStr] > 0 ? item[dayStr] : '0') + '</td>';
                    }

                    tableRows += '<td class="total-column">' + item.total_nights + '</td>';
                    tableRows += '<td class="revenue-column">' + item.revenue + '</td>';
                    tableRows += '</tr>';
                });
            }

            $('#companynights tbody').html(tableRows);
        }
    </script>
@endsection
