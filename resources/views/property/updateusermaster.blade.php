@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="icon-speedometer menu-icon"></i>
                            Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)"><i class="fa-regular fa-user"></i>
                            User Master Update</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" name="userupdateform" id="userupdateform"
                                action="{{ route('update_usermaster') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="fullname">Name</label>
                                        <input type="text" value="{{ $userdata->name }}" name="fullname" id="fullname"
                                            class="form-control" required>
                                        <span id="fullname_error" class="text-danger"></span>
                                        @error('fullname')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <input type="hidden" name="userid" value="{{ $userdata->u_name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="email">Email</label>
                                        <input type="email" value="{{ $userdata->email }}" name="email" id="email"
                                            class="form-control" required>
                                        <span id="email_error" class="text-danger"></span>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="designation">Super Wiser</label>
                                        <select name="designation" id="designation" class="form-control" required>
                                            @if (empty($userdata->superwiser))
                                                <option value="" selected>Select</option>
                                            @else
                                                <option value="">Select</option>
                                            @endif
                                            <option value="1" {{ $userdata->superwiser == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $userdata->superwiser == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                        <span id="designation_error" class="text-danger"></span>
                                        @error('designation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="backdate">Back Date Entry</label>
                                        <select name="backdate" id="backdate" class="form-control" required>
                                            <option value="1" {{ $userdata->backdate == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $userdata->backdate == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                        <span id="backdate_error" class="text-danger"></span>
                                        @error('backdate')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-8 mt-4 ml-auto">
                                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-wrench"></i>
                                            Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
    </div>
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
