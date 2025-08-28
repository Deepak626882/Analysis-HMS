@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="icon-speedometer menu-icon"></i>
                            Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)"><i class="fa-regular fa-user"></i>
                            User Master Register</a></li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" name="usemasterform" id="usemasterform"
                                action="{{ route('usermasterstore') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="fullname">Name</label>
                                        <input type="text" name="fullname" maxlength="25" id="fullname" class="form-control"
                                            required>
                                        <span id="fullname_error" class="text-danger"></span>
                                        @error('fullname')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                        <span id="email_error" class="text-danger"></span>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="designation">Super Wiser</label>
                                        <select name="designation" id="designation" class="form-control" required>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>
                                        <span id="designation_error" class="text-danger"></span>
                                        @error('designation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-lg-4 col-form-label" for="pin">Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" minlength="4" maxlength="12"
                                            class="form-control" placeholder="Password">
                                        <span id="password_error" class="text-danger"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="backdate">Back Date Entry</label>
                                        <select name="backdate" id="backdate" class="form-control" required>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>
                                        <span id="backdate_error" class="text-danger"></span>
                                        @error('backdate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="usermastertable" class="table table-hover table-striped">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Sn.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Super Wiser</th>
                                    <th>Back Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sn = 1; @endphp

                                @foreach ($userdata as $user)
                                    <tr>
                                        @if ($user->comprole != 'Property')
                                            <td>{{ $sn }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->superwiser == '1' ? 'Yes' : 'No' }}</td>
                                            <td>{{ $user->backdate == '1' ? 'Yes' : 'No' }}</td>
                                            <td class="ins">
                                                <a href="updateusermaster?u_name={{ base64_encode($user->u_name) }}">
                                                    <button class="btn-success btn-sm btn">Update <i
                                                            class="fa-regular fa-pen-to-square"></i></button>
                                                </a>
                                                @php
                                                    if ($user->status == 1) {
                                                        $encodedId = base64_encode($user->id);
                                                        echo '<a href="#"
                                        onclick="return confirmBanUserMaster(\'' .
                                                            $encodedId .
                                                            '\')"><button
                                            class="btn-danger btn-sm text-white btn">InActive <i
                                                class="fa-solid fa-ban"></i></button></a>';
                                                    } else {
                                                        $encodedId = base64_encode($user->id);
                                                        echo '<a href="#"
                                        onclick="return confirmUnbanUserMaster(\'' .
                                                            $encodedId .
                                                            '\')"><button
                                            class="btn-info btn-sm text-white btn">Active <i
                                                class="fa-solid fa-user-check"></i></button></a>';
                                                    }
                                                @endphp
                                            </td>
                                        @endif
                                    </tr>
                                    @php $sn++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myloader').removeClass('none');
            setTimeout(() => {
                $('#myloader').addClass('none');
            }, 500);
        });
    </script>
@endsection
