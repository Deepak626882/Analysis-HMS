@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ route('member.categorystore') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Member Category</label>
                                        <input type="text" name="title" id="title" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="short_name" class="form-label">Short Name</label>
                                        <input type="text" name="short_name" id="short_name" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="subscription" class="form-label">Subscription</label>
                                        <select name="subscription" id="subscription" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="yes" selected>Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="surcharge" class="form-label">Surcharge</label>
                                        <select name="surcharge" id="surcharge" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="yes">Yes</option>
                                            <option value="no" selected>No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="facility_billing" class="form-label">Facility Billing</label>
                                        <select name="facility_billing" id="facility_billing" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="yes">Yes</option>
                                            <option value="no" selected>No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
