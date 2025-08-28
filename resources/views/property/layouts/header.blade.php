<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Analysis</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/images/favicon.png') }}">
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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Notify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="backdate" content="{{ Auth::user()->backdate }}">
</head>

<body>

    <div class="loader-overlay">
        <div class="sk-cube-grid">
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
        </div>
        <div class="loader-text">Analysis HMS</div>
    </div>

    <!--*******************
        Preloader start
    ********************-->
    {{-- <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3"
                    stroke-miterlimit="10" />
            </svg>
        </div>
    </div> --}}
    <!--*******************
        Preloader end
    ********************-->
    {{-- <div id="myloader" class="loader-overlay none">
        <div class="loader-content">
            <div class="loader-spinner">
                <div class="loader-circle"></div>
                <img src="{{ asset('admin/icons/custom/jogging.gif') }}" alt="Jogging" class="loader-image">
            </div>
            <div class="loader-text">Please wait...</div>
        </div>
    </div> --}}

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <div class="brand-logo">
                <a href="{{ url('/company') }}">
                    <b class="logo-abbr"><img class="rounded-circle" src="{{ env('APP_URL') }}/admin/images/user/letter-a.gif" alt="">
                    </b>
                    <span class="logo-compact"><img src="{{ asset('admin/images/logo-compact.png') }}"
                            alt=""></span>
                    <span class="brand-title">
                        <img src="{{ asset('admin/images/logo-text.png') }}" class="img-fluid" alt="">
                    </span>
                </a>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content clearfix">

                <div class="nav-control">
                    <div class="hamburger">
                        <span class="toggle-icon"><i class="icon-menu"></i></span>
                    </div>
                </div>
                <div class="header-left">
                </div>
                <div class="header-center">
                    <div class="input-group icons">
                        <div class="two alt-two">
                            <h1 class="mainhead"><span id="compnamed"></span>
                                <span id="showfinyear"></span>
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="header-right d-flex align-items-center">

                    @if (count(myproperties()) > 1)
                        <div class="my-properties-wrapper position-relative mr-3">
                            <li class="my-properties-toggle" style="cursor: pointer; list-style: none;">
                                <i class="fa-solid fa-house"></i> My Properties
                            </li>
                            <ul class="submenuproperty cursor-pointer position-absolute shadow p-2"
                                style="display: none; list-style: none; min-width: 200px; z-index: 999;">
                                @foreach (myproperties() as $item)
                                    <li data-propertyid="{{ $item->propertyid }}" data-userid="{{ $item->userid }}" data-username="{{ Auth::user()->u_name }}" class="p-2 propertysllist bg-light font-11 {{ Auth::user()->propertyid == $item->propertyid ? 'bg-dark text-white' : '' }}">
                                        {{ $item->propertyid }} - {{ $item->comp_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Profile Dropdown -->
                    <li class="icons dropdown" style="list-style: none;">
                        <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                            <span class="activity active"></span>
                            <img src="{{ env('APP_URL') }}/admin/images/user/letter-a.gif" height="40" width="40" alt="">
                        </div>
                        <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                            <div class="dropdown-content-body">
                                <ul>
                                    <li>
                                        <a href=""><i class="icon-user"></i><span id="usernameshow"></span></a>
                                    </li>
                                    <li class="update-log">
                                        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#updateLogModal">
                                            Update Log
                                        </button>
                                    </li>
                                    <li>
                                        <a href="{{ route('logout') }}"><i class="icon-key"></i><span>Logout</span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                </div>

            </div>
        </div>

        <div class="modal fade" id="updateLogModal" role="dialog" tabindex="-1"
            aria-labelledby="updateLogModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-lg rounded-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="updateLogModalLabel"><i class="fas fa-history"></i> Update Log
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="update-log-content p-3" id="updateLogContent">
                            <p class="text-muted text-center">Loading updates...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

        <script>
            var yearManageUrl = "{{ url('yearmanage') }}";
            $(document).on('click', '.my-properties-toggle', function(e) {
                e.stopPropagation();
                $('.submenuproperty').slideToggle();
            });

            $(document).on('click', function() {
                $('.submenuproperty').slideUp();
            });

            $(document).on('click', '.propertysllist', function() {
                let userid = $(this).data('userid');
                let username = $(this).data('username');
                let propertyid = $(this).data('propertyid');
                let currentUrl = window.location.href;

                $.ajax({
                    url: '/auto-login',
                    type: 'POST',
                    data: {
                        userid: userid,
                        username: username,
                        propertyid: propertyid,
                        redirect_url: currentUrl,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect;
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        alert('Something went wrong!');
                    }
                });
            });
        </script>

        @if (isset($message))
            <script>
                Swal.fire({
                    icon: '{{ $type }}',
                    title: '{{ $type }}',
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
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif
        @if (session('infosale'))
            <script>
                Swal.fire({
                    icon: 'info',
                    title: "Sale Bill Entry",
                    text: "{{ session('infosale')['text'] }}",
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let printdata = JSON.parse('{!! session('infosale')['printdata'] !!}');
                        let filetoopen;
                        if (printdata.printsetup.description == 'Bill Windows Plain Paper') {
                            filetoopen = 'salebillprint';
                        } else if (printdata.printsetup.description == '3 Inch Running Paper Windows Print') {
                            filetoopen = 'salebillprint2';
                        }
                        let openfile = window.open(filetoopen, '_blank');
                        openfile.onload = function() {
                            $('#roomno', openfile.document).text(printdata.roomno);
                            $('#vdate', openfile.document).text(printdata.vdate);
                            $('#billno', openfile.document).text(printdata.billno);
                            $('#vtype', openfile.document).text(printdata.vtype);
                            $('#departname', openfile.document).text(printdata.departname);
                            $('#kotno', openfile.document).text(printdata.kotno);
                            $('#waiter', openfile.document).text(printdata.waiter);
                            $('#outletcode', openfile.document).text(printdata.outletcode);
                            $('#departnature', openfile.document).text(printdata.departnature);
                        }
                    }
                });
            </script>
        @endif
        @if (session('nightinfo'))
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Night Audit',
                    text: "{{ session('nightinfo')['message'] }}",
                    showConfirmButton: true,
                    confirmButtonText: 'Click To View',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let bills = '{!! session('nightinfo')['bills'] !!}';
                        var rowcode = "{{ session('nightinfo')['row'] }}";
                        var cname, ur;
                        if (rowcode == 1) {
                            let pendingbillskot = new XMLHttpRequest();
                            pendingbillskot.open('POST', '/pendingbillskot', true);
                            pendingbillskot.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            pendingbillskot.onreadystatechange = function() {
                                if (pendingbillskot.readyState === 4 && pendingbillskot.status === 200) {
                                    let results = JSON.parse(pendingbillskot.responseText);
                                    let tbody = $('#pendingkotnightaudit tbody');
                                    tbody.empty();
                                    let tdata = '';
                                    results.forEach((data) => {
                                        if (rowcode == 1) {
                                            cname = 'kotrow';
                                            ur = `salebillentry?dcode=${data.restcode}&roomno=${data.roomno}`;
                                        } else if (rowcode == 2) {
                                            cname = 'salerow';
                                            ur =
                                                `settlemententry?dcode=${data.restcode}&tableno=${data.roomno}&vno=${data.vno}`;
                                        }
                                        tdata += `<tr class="${cname}" data-vno="${data.vno}" data-id="${data.restcode}" data-value="${data.roomno}">
                                    <td>${data.vno}</td>
                                    <td>${data.roomno}</td>
                                    <td>${data.waitername}</td>
                                    <td>${data.depname}</td>
                                    <td>Pending</td>
                                </tr>`;
                                    });
                                    tbody.append(tdata);
                                    $('.pendingkotnightaudit').removeClass('none');
                                }
                            }
                            pendingbillskot.send(`bills=${bills}&_token={{ csrf_token() }}`);
                        } else if (rowcode == 2) {
                            let salewarnxhr = new XMLHttpRequest();
                            salewarnxhr.open('GET', '/salewarnxhr', true);
                            salewarnxhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            salewarnxhr.onreadystatechange = function() {
                                if (salewarnxhr.status === 200 && salewarnxhr.readyState === 4) {
                                    let result = JSON.parse(salewarnxhr.responseText);
                                    let count = result.count;
                                    if (count > 0) {
                                        if (result.msg.length > 0) {
                                            $('#headingth').text('Bill No.');
                                            let msg = result.msg;
                                            let salerows = result.salerows;
                                            let tdata = '';
                                            salerows.forEach((data, index) => {
                                                tdata += `<tr class="salerow" data-vno="${data.vno}" data-id="${data.restcode}" data-value="${data.roomno}">
                                                <td>${data.vno}</td>
                                                <td>${data.roomno}</td>
                                                <td>${data.waitername}</td>
                                                <td>${data.depname}</td>
                                                <td>Pending</td>
                                            </tr>`;
                                            });
                                            let tfoot = `<tr>
                                            <td>Maxed</td>
                                            </tr>`;
                                            $('#pendingkotnightaudit tfoot').append(tfoot);
                                            $('#pendingkotnightaudit tbody').append(tdata);
                                            $('.pendingkotnightaudit').removeClass('none');
                                            $(document).on('click', '.salerow', function() {
                                                let roomno = $(this).data('value');
                                                let restcode = $(this).data('id');
                                                let vno = $(this).data('vno');
                                                window.location.href =
                                                    `settlemententry?dcode=${restcode}&tableno=${roomno}&vno=${vno}`;
                                            });
                                        }
                                    }
                                }
                            }
                            salewarnxhr.send();
                        }

                    }
                });

                $(document).on('click', '.kotrow, .salerow', function() {
                    let roomno = $(this).data('value');
                    let restcode = $(this).data('id');
                    let vno = $(this).data('vno');
                    let rowcode = "{{ session('nightinfo')['row'] }}";
                    let ur;
                    if (rowcode == 1) {
                        ur = `salebillentry?dcode=${restcode}&roomno=${roomno}`;
                    } else if (rowcode == 2) {
                        ur = `settlemententry?dcode=${restcode}&tableno=${roomno}&vno=${vno}`;
                    }
                    window.location.href = ur;
                });
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                fetch('/getcompdt', {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(response => response.json())
                    .then(results => {
                        // let count = 0;

                        // function showAlertRepeatedly() {
                        //     if (count < 2) {
                        //         Swal.fire({
                        //             title: 'Pending Payment',
                        //             text: 'Your Payment is due. Software will be closed soon. Please make the payment now.',
                        //             icon: 'info',
                        //             confirmButtonText: 'OK'
                        //         }).then(() => {
                        //             count++;
                        //             showAlertRepeatedly();
                        //         });
                        //     }
                        // }

                        // if (results.company.propertyid == '122') {
                        //     showAlertRepeatedly();
                        // }

                        const datemanage = results.datemanage;
                        $('#usernameshow').text(results.user.name);
                        document.getElementById('compnamed').textContent = results.company.comp_name + " (" +
                            results.company.propertyid + ")";
                        document.getElementById('showfinyear').textContent = `${datemanage.finyear.current}-${datemanage.hf.end}`;
                        $('#start_dtdd').text(`01-04-${datemanage.finyear.current}`);
                        $('#end_dtdd').text(`31-03-${datemanage.finyear.nextyear}`);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });

            $.ajax({
                url: '{{ route('getwpenviro') }}',
                type: 'GET',
                success: function(response) {
                    if (response === true) {
                        $('#wpmsgerror').text('⚠️ Your WhatsApp balance is low. Please recharge. to send automatic messages');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        </script>
