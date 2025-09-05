@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            
                            <div class="mb-3">
                                <form method="post" action="">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="member_id">Member ID</label>
                                                <input type="text" class="form-control" name="member_id" id="member_id">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="application_no">Application No</label>
                                                <input type="text" class="form-control" name="application_no" id="application_no">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="application_date">Application Date</label>
                                                <input type="date" class="form-control" name="application_date" id="application_date">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="membership_date">Membership Date</label>
                                                <input type="date" value="{{ ncurdate() }}" class="form-control" name="membership_date" id="membership_date">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="col-form-label" for="reservationtype">Contact Person</label>
                                            <select class="form-control" name="greetings" id="greetings">
                                                <option value="Mr.">Mr.</option>
                                                <option value="Ms.">Ms.</option>
                                                <option value="Mam">Mam</option>
                                                <option value="Dr.">Dr.</option>
                                                <option value="Prof.">Prof.</option>
                                                <option value="Mrs.">Mrs.</option>
                                                <option value="Miss">Miss</option>
                                                <option value="Sir">Sir</option>
                                                <option value="Madam">Madam</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="fullname">Full Name</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
