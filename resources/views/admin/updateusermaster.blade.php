@extends('admin.layouts.main')
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
                            <p class="text-dpink">If you encounter an error message indicating that an input already exists
                                when you're making
                                changes, please consider renaming the input with the new input. for same name no need to
                                worry it'll update.</p>
                            <form class="form" name="userupdateform" id="userupdateform"
                                action="{{ route('update_usermaster2') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <label class="col-form-label" for="property_dt">Property</label>
                                        <select id="property_dt" name="property_dt" required class="form-control">
                                            <option value="{{ $userdata->propertyid }}">
                                                {{ $userdata->propertyid }}@php
                                                @endphp
                                                @foreach ($property as $list)
                                            <option value="{{ $list->propertyid }}">{{ $list->comp_name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="property_dt_error" class="text-danger"></span>
                                        @error('property_dt')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label class="col-form-label" for="fullname">Name</label>
                                        <input type="text" value="{{ $userdata->name }}" name="fullname" id="fullname"
                                            class="form-control" required>
                                        <span id="fullname_error" class="text-danger"></span>
                                        @error('fullname')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <input type="hidden" name="userid" value="{{ $userdata->id }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label class="col-form-label" for="email">Email</label>
                                        <input type="email" value="{{ $userdata->email }}" name="email" id="email"
                                            class="form-control" required>
                                        <span id="email_error" class="text-danger"></span>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label class="col-form-label" for="designation">Designation</label>
                                        <select name="designation" id="designation" class="form-control" required>
                                            <option value="{{ $userdata->role }}"{{ $userdata->role }}>@php
                                                if ($userdata->role === 3) {
                                                    echo 'SuperWiser';
                                                }
                                            @endphp
                                            </option>
                                            <option value="3">SuperWiser</option>
                                        </select>
                                        <span id="designation_error" class="text-danger"></span>
                                        @error('designation')
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
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function checkInput(inputElement, endpoint, errorElementId) {
            inputElement.addEventListener('input', function() {
                var inputValue = this.value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', endpoint, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var result = xhr.responseText;
                        var errorElement = document.getElementById(errorElementId);
                        errorElement.textContent = result;
                    }
                };
                xhr.send(inputElement.id + '=' + inputValue + '&_token={{ csrf_token() }}');
            });
        }

        var email = document.getElementById('email');
        checkInput(email, '/check_email', 'email_error');

        var username = document.getElementById('fullname');
        checkInput(username, '/check_username', 'fullname_error');
    });
</script>
