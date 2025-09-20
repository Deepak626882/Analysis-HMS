@extends('admin.layouts.main')
@section('main-container')
    <div class="content-body">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="icon-speedometer menu-icon"></i>
                            Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)"><i class="fa-solid fa-list"></i> Company
                            List</a></li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center">Company List <i class="fa-solid fa-flip fa-list"></i></h4>
                            <div class="table-responsive">
                                <table id="companylist"
                                    class="table companylisttable table-download-with-search table-striped table-hover table-bordered">
                                    <thead class="bg-white">
                                        <tr>
                                            <th>Property Id</th>
                                            <th>Company Name</th>
                                            <th>Username</th>
                                            <th>Start Date</th>
                                            <th>End Dt</th>
                                            <th>Country</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Logo</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($companies as $list)
                                            <tr>
                                                <td>{{ $list->propertyid }}</td>
                                                <td>{{ $list->comp_name }}</td>
                                                <td>{{ $list->u_name }}</td>
                                                <td>{{ date('d-m-Y', strtotime($list->start_dt)) }}</td>
                                                <td>{{ date('d-m-Y', strtotime($list->end_dt)) }}</td>
                                                <td>{{ $list->country }}</td>
                                                <td>{{ $list->state }}</td>
                                                <td>{{ $list->city }}</td>
                                                <td><a class="text-dark"
                                                        href="tel:{{ $list->mobile }}">{{ $list->mobile }}</a></td>
                                                <td><a class="text-dark"
                                                        href="mailto:{{ $list->email }}">{{ $list->email }}</a></td>
                                                <td>
                                                    <img
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#logomodal"
                                                        style="width: 10vh; cursor: pointer;"
                                                        onerror="this.src='https://placehold.co/25x25?text=A+S'"
                                                        src="{{ url('/storage/admin/property_logo') }}/{{ $list->logo }}"
                                                        alt="{{ $list->comp_name }}"
                                                        onclick="showLogo('{{ url('/storage/admin/property_logo') }}/{{ $list->logo }}', '{{ $list->comp_name }}', '{{ $list->logo }}')">
                                                </td>
                                                <td class="ins">
                                                    <a
                                                        href="updatepropertyadmin?propertyid={{ base64_encode($list->propertyid) }}">
                                                        <button class="btn-success btn-sm btn">Update <i
                                                                class="fa-regular fa-pen-to-square"></i></button>
                                                    </a>
                                                    @php
                                                        if ($list->status == 1) {
                                                            $encodedPropertyId = base64_encode($list->propertyid);
                                                            echo '<a href="#" onclick="return confirmBan(\'' . $encodedPropertyId . '\')"><button class="btn-danger btn-sm text-white btn">InActive <i class="fa-solid fa-ban"></i></button></a>';
                                                        } else {
                                                            $encodedPropertyId = base64_encode($list->propertyid);
                                                            echo '<a href="#" onclick="return confirmUnban(\'' . $encodedPropertyId . '\')"><button class="btn-info btn-sm text-white btn">Active <i class="fa-solid fa-user-check"></i></button></a>';
                                                        }
                                                    @endphp
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal fade" id="logomodal" tabindex="-1" aria-labelledby="logomodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="logomodalLabel">Company Logo</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img id="companylogo" src="" alt="" class="img-fluid" style="max-width: 100%; height: auto;">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <a id="downloadLogo" href="" download="" class="btn btn-primary">
                                                <i class="fa-solid fa-download"></i> Download Logo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLogo(logoUrl, companyName, logoFilename) {
            $('#companylogo').attr('src', logoUrl);
            $('#companylogo').attr('alt', companyName + ' Logo');
            $('#logomodalLabel').text(companyName + ' Logo');
            $('#downloadLogo').attr('href', logoUrl);
            $('#downloadLogo').attr('download', logoFilename);

            var myModal = new bootstrap.Modal(document.getElementById('logomodal'));
            myModal.show();
        }
    </script>
@endsection
