@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="container-fluid mt-5">
            <div class="row justify-content-center">
                <div style="width: 100%;" class="card">
                    <div class="card-header text-center bg-success text-white">
                        Welcome, {{ $user->u_name }}
                    </div>
                    <div class="card-body">
                        <marquee id="wpmsgerror" class="text-capitalize text-dpink font-weight-bold" behavior="" direction="right"></marquee>
                        @php
                            use Carbon\Carbon;
                            use Illuminate\Support\Facades\Crypt;

                            $expdate = null;
                            $daysLeft = null;

                            if (envirogeneral() && envirogeneral()->expdate && envirogeneral()->propertyid != '103') {
                                try {
                                    $expCarbon = Carbon::parse(Crypt::decryptString(envirogeneral()->expdate));
                                    $expdate = $expCarbon->format('d-m-Y');
                                    $expamount = number_format((float) Crypt::decryptString(envirogeneral()->amount), 2);

                                    $ncurdate = Carbon::parse(ncurdate());
                                    $daysLeft = $ncurdate->diffInDays($expCarbon, false); // false keeps negative if expired
                                } catch (\Exception $e) {
                                    $expdate = 'Invalid date';
                                }
                            }
                        @endphp

                        @if ($expdate && $expdate !== 'Invalid date')
                            {{-- <p><strong>Expiry Date:</strong> {{ $expdate }}</p> --}}

                            @if ($daysLeft >= 0 && $daysLeft <= 30)
                                <div class="alert alert-warning mt-2">
                                    ⚠️ Your account will be expiring on <strong>{{ $expdate }}</strong>
                                    (in {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}). and due amount is: {{ $expamount }}
                                </div>
                            @endif
                        @else
                            {{-- <p><strong>Expiry Date:</strong> Not Set</p> --}}
                        @endif

                        {{-- <div style="overflow: hidden;">
                            <h3 class="animate-charcter" style="float: left;">{{ $user->comp_name }}</h3>
                            <button id="edit-btn" onclick="Enableformshow()" class="btn btn-dark" style="float: right;"><i
                                    class="fa fa-edit" aria-hidden="true"></i>Edit</button>
                        </div> --}}
                        <form class="companychangeform" id="companychangeform" action="{{ route('changecompanydetail') }}"
                            method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Property ID</th>
                                                <td>{{ $user->propertyid }}</td>
                                            </tr>
                                            <tr>
                                                <th>Company Code</th>
                                                <td>{{ $user->comp_code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Serial Number</th>
                                                <td>{{ $user->sn_num }}</td>
                                            </tr>
                                            <tr>
                                                <th>Start Date</th>
                                                <td id="start_dtdd"></td>
                                            </tr>
                                            <tr>
                                                <th>End Date</th>
                                                <td id="end_dtdd"></td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>{{ $user->address1 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Country</th>
                                                <td>{{ $user->country }}</td>
                                            </tr>
                                            <tr>
                                                <th>State</th>
                                                <td>{{ $user->state }}</td>
                                            </tr>
                                            <tr>
                                                <th>City</th>
                                                <td>{{ $user->city }}</td>
                                            </tr>
                                            <tr>
                                                <th>Name</th>
                                                <td><input class="form-invisible" type="text"
                                                        value="{{ $user->legal_name }}" name="legal_name" disabled></td>
                                                @error('legal_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </tr>
                                            <tr>
                                                <th>Mobile</th>
                                                <td><input class="form-invisible" type="text" value="{{ $user->mobile }}"
                                                        minlength="10" maxlength="10" name="mobile" disabled></td>
                                                @error('mobile')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><input class="form-invisible" type="email" value="{{ $user->email }}"
                                                        name="email" disabled></td>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="submitbtnindex" class="form-group row">
                                <div class="col-7 mt-4 ml-auto">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if ($datearr['roomstatusview'] == 1)
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <h3 class="col">Number of Guests Day Wise</h3>
                            <div class="form-group">
                                <label for="startdate">From</label>
                                <input type="date" max="{{ $datearr['ncurdate'] }}" value="{{ $datearr['last30days'] }}" class="form-control" name="startdate" id="startdate">
                            </div>
                            <div class="form-group">
                                <label for="enddate">To</label>
                                <input max="{{ $datearr['ncurdate'] }}" type="date" value="{{ $datearr['ncurdate'] }}" class="form-control" name="enddate" id="enddate">
                            </div>
                        </div>

                        <div class="chart-container">
                            <canvas id="hotelGuestsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-1">
                        <div class="card-body">
                            <h3 class="card-title text-white">Products Sold</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">4565</h2>
                                <p class="text-white mb-0">Jan - March 2019</p>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-2">
                        <div class="card-body">
                            <h3 class="card-title text-white">Net Profit</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">$ 8541</h2>
                                <p class="text-white mb-0">Jan - March 2019</p>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-3">
                        <div class="card-body">
                            <h3 class="card-title text-white">New Customers</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">4565</h2>
                                <p class="text-white mb-0">Jan - March 2019</p>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-users"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-4">
                        <div class="card-body">
                            <h3 class="card-title text-white">Customer Satisfaction</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">99%</h2>
                                <p class="text-white mb-0">Jan - March 2019</p>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <script>
        $(document).ready(function() {

            let csrftoken = "{{ csrf_token() }}";
            let hotelGuestsChartInstance = null;

            $(document).on('change', '#startdate, #enddate', function() {
                let startdate = $('#startdate').val();
                let enddate = $('#enddate').val();

                const postdata = {
                    'startdate': startdate,
                    'enddate': enddate
                };

                const options = {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrftoken
                    },
                    body: JSON.stringify(postdata)
                };

                fetch('/getindex', options)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            let gueststayduration = result.data.gueststayduration;

                            const guestcount = gueststayduration.map(x => x.guest_count);
                            const chkindate = gueststayduration.map(x => new Date(x.chkindate).getDate());

                            const guestStayData = {
                                labels: chkindate,
                                datasets: [{
                                    label: 'Number of Guests this Day',
                                    data: guestcount,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    tension: 0.4,
                                    fill: false
                                }]
                            };

                            const guestStayOptions = {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Number of Guests Day Wise'
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Day of the Month'
                                        }
                                    },
                                }
                            };

                            if (hotelGuestsChartInstance) {
                                hotelGuestsChartInstance.destroy();
                            }

                            const ctx1 = document.getElementById('hotelGuestsChart').getContext('2d');
                            hotelGuestsChartInstance = new Chart(ctx1, {
                                type: 'line',
                                data: guestStayData,
                                options: guestStayOptions
                            });
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            });
            $('#startdate').trigger('change');
        });
    </script>
@endsection
