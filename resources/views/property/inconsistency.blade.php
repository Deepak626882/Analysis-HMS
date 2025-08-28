@extends('property.layouts.main')
@section('main-container')
<div class="content-body">

    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="icon-speedometer menu-icon"></i>
                        Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)"><i
                            class="fa-solid fa-magnifying-glass"></i>
                        Utility</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">
                        Inconsistency Check</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-3">Inconsistency Check <i
                                class="fa-solid fa-magnifying-glass"></i></h4>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">Finance <i
                                    class="fa-solid fa-coins"></i></button>
                            <i class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button" onclick="inconPostRequest('{{ url('accountgrp') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Account Group</button>
                            <button type="button" onclick="inconPostRequest('{{ url('subgroup') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Sub Group</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('loadvoucherprefix') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Voucher Prefix</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('loadvouchertype') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Voucher Type</button>
                            <button type="button" onclick="inconPostRequest('{{ url('loadsundrytype') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Sundry Type</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('loadsundrymaster') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Sundry Master</button>
                            <button type="button" onclick="inconPostRequest('{{ url('loadtaxes') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Tax</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('loadtaxesstructure') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Tax Structure</button>
                            <button type="button" onclick="inconPostRequest('{{ url('loadunitmaster') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-success">Unit</button>
                        </div>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">Inventry <i
                                    class="fa-solid fa-warehouse"></i></button> <i
                                class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('/loadhousekeeping') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-info">House Keeping</button>
                            <button type="button" onclick="inconPostRequest('{{ url('/loadstore') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-info">Store</button>
                            <button type="button" class="btn mb-1 btn-outline-info">Godown</button>
                        </div>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">CSC <i
                                    class="fa-solid fa-globe"></i></button> <i
                                class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button" onclick="inconPostRequest('{{ url('/countryload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-dark">Country</button>
                            <button type="button" onclick="inconPostRequest('{{ url('/stateload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-dark">State</button>
                            <button type="button" onclick="inconPostRequest('{{ url('/cityload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-dark">City</button>
                        </div>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">Front Office <i
                                    class="fa-solid fa-door-open"></i></button> <i
                                class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('/fixchargesload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-warning">Fix Charges</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('/settlementload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-warning">Settlement Type</button>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('/travelagentload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-warning">Travel Agent</button>
                        </div>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">Out Let <i
                                    class="fa-solid fa-o"></i></button> <i class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button"
                                onclick="inconPostRequest('{{ url('/loadroomservice') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-primary">Room Service</button>
                        </div>
                        <div class="general-button inconsistency">
                            <button type="button" class="btn mb-1 btn-primary">Hall <i
                                    class="fa-solid fa-dungeon"></i></button> <i
                                class="fa-solid fa-arrow-right fa-fade"></i>
                            <button type="button" onclick="inconPostRequest('{{ url('/banquetload') }}', 'GET', this)"
                                class="btn mb-1 btn-outline-danger">Catering</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
    </div>
</div>
@endsection
{{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
