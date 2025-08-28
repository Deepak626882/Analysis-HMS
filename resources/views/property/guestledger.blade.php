<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Pignose Calender -->
    <link href="{{ asset('admin/plugins/pg-calendar/css/pignose.calendar.min.css') }}" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/chartist/css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom Stylesheet -->
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <link
        href="{{ asset('admin/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
        rel="stylesheet">
    <!-- Color picker plugins css -->
    <link href="{{ asset('admin/plugins/jquery-asColorPicker-master/css/asColorPicker.css') }}" rel="stylesheet">
    <!-- Daterange picker plugins css -->
    <link href="{{ asset('admin/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (isset($message))
    <script>
        Swal.fire({
            icon: '{{ $type }}',
            title: '{{ $type == 'success' ? 'Success' : 'Error' }}',
            text: '{{ $message }}',
            timer: 5000,
            showConfirmButton: true
        });
    </script>
@endif

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
        });
        setTimeout(function() {
            Swal.close();
        }, 5000);
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
        });
        setTimeout(function() {
            Swal.close();
        }, 5000);
    </script>
@endif

<body>
    <div style="font-size: small;" class="col-md-12">
        <h3 class="text-center BCH-alt border-bottom-1">Charge Details</h3>
        {{-- <span style="float: inline-end;">Use <code>Shift + D</code> To Delete Rows</span> --}}
        <div id="alertspandiv" style="display: none;" class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong><span id="alertmsg"></span></strong>
        </div>

        <div class="table-responsive">
            <table style="font-size: small;" id="guestledger"
                class="table table-hover guestledger table-download-with-search table-hover table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                        <th>Dr/Cr</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                        $prevBalance = 0;
                    @endphp
                    @foreach ($data as $ledger)
                        <tr data-value="{{ $ledger->vtype }}" data-id="{{ $ledger->vno }}">
                            <td style="white-space: nowrap;">{{ date('d-m-Y', strtotime($ledger->vdate)) }}</td>
                            <td>{{ $ledger->comments }}</td>
                            <td>{{ $ledger->amtdr ?? '0' }}</td>
                            <td>{{ $ledger->amtcr ?? '0' }}</td>
                            <td class="balance"></td>
                            <td class="dr-cr"></td>
                            <td>{{ $ledger->u_name }}</td>
                        </tr>
                        <?php
                        $totalDebit += $ledger->amtdr;
                        $totalCredit += $ledger->amtcr;
                        ?>
                    @endforeach
                    <tr style="font-weight: 600;">
                        <td><b>Total</b></td>
                        <td>‎ </td>
                        <td id="totalDebit">{{ number_format($totalDebit, 2) }}</td>
                        <td id="totalCredit">{{ number_format($totalCredit, 2) }}</td>
                        <td>{{ number_format(abs($totalDebit - $totalCredit), 2) }}</td>
                        <td>{{ $totalDebit > $totalCredit ? 'Dr' : 'Cr' }}</td>
                        <td>‎ </td>
                    </tr>
                </tbody>
            </table>

            <script>
                $(document).ready(function() {
                    let dataid;
                    let datavalue;
                    $(document).on('click', '#guestledger tbody tr', function() {
                        $('#guestledger tbody tr').removeClass('bgchangegtr');
                        $(this).addClass('bgchangegtr');
                        dataid = $(this).data('id');
                        datavalue = $(this).data('value');
                    });

                    $(document).on('keydown', function(event) {
                        if (event.shiftKey && event.key === 'D') {
                            var selectedRows = $('#guestledger tbody tr[data-id="' + dataid + '"]');
                            if (selectedRows.length > 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Are you sure?',
                                    text: 'Enter the reason for deleting:',
                                    input: 'text',
                                    inputPlaceholder: 'Reason',
                                    inputValue: 'Wrong Entry',
                                    showCancelButton: true,
                                    confirmButtonText: 'Delete',
                                    cancelButtonText: 'Cancel',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        var reason = result.value;
                                        if (reason) {
                                            var xhrledger = new XMLHttpRequest();
                                            xhrledger.open('POST', '/deleteguestledger', true);
                                            xhrledger.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                            xhrledger.onreadystatechange = function() {
                                                if (xhrledger.readyState === 4 && xhrledger.status === 200) {
                                                    var result = xhrledger.responseText;
                                                    $('#alertspandiv').css('display', 'block');
                                                    $('#alertmsg').text('Selected Guest Ledger has been deleted');

                                                    setTimeout(() => {
                                                        $('#alertmsg').text('');
                                                        $('#alertspandiv').css('display', 'none');
                                                        location.reload();
                                                    }, 2000);
                                                    selectedRows.remove();
                                                } else {
                                                    $('#alertspandiv').css('display', 'block');
                                                    $('#alertmsg').text('Unable to delete guest ledger');
                                                }
                                            };
                                            xhrledger.send(`dataid=${dataid}&datavalue=${datavalue}&reason=${reason}&_token={{ csrf_token() }}`);
                                        } else {
                                            Swal.fire('No reason provided', 'You need to enter a reason to proceed.', 'info');
                                        }
                                    }
                                });
                            }
                        }
                    });

                });

                var rows = document.querySelectorAll('tbody tr');
                var prevBalance = 0;
                var totalBalance = 0;

                rows.forEach((row, index) => {
                    var debitCell = row.querySelector('td:nth-child(3)');
                    var creditCell = row.querySelector('td:nth-child(4)');
                    var balanceCell = row.querySelector('.balance');
                    var drCrCell = row.querySelector('.dr-cr');
                    if (debitCell && creditCell && balanceCell && drCrCell) {
                        var debit = parseFloat(debitCell.innerText);
                        var credit = parseFloat(creditCell.innerText);
                        var balance = prevBalance + debit - credit;
                        drCrCell.innerText = balance < 0 ? 'Cr' : 'Dr';
                        balanceCell.innerText = Math.abs(balance.toFixed(2));
                        prevBalance = balance;
                        var absolutebalance = Math.abs(balance.toFixed(2));
                        totalBalance += absolutebalance;
                    } else {
                        // console.log('Cells not found');
                    }
                });
            </script>

            </table>

        </div>
    </div>
</body>

</html>

<script src="{{ asset('admin/plugins/common/common.min.js') }}"></script>
<script src="{{ asset('admin/js/custom.min.js') }}"></script>
<script src="{{ asset('admin/js/settings.js') }}"></script>
<script src="{{ asset('admin/js/gleek.js') }}"></script>
<script src="{{ asset('admin/js/styleSwitcher.js') }}"></script>
<script src="{{ asset('admin/js/dashboard/dashboard-1.js') }}"></script>

<script src="{{ asset('admin/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('admin/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
<!-- Clock Plugin JavaScript -->
<script src="{{ asset('admin/plugins/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
<!-- Date Picker Plugin JavaScript -->
<script src="{{ asset('admin/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<!-- Date range Plugin JavaScript -->
<script src="{{ asset('admin/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins-init/form-pickers-init.js') }}"></script>

<!-- Color Picker Plugin JavaScript -->
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
