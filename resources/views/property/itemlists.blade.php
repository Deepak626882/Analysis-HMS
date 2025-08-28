@extends('property.layouts.main')
@section('main-container')
<div class="content-body">

    <!-- row -->

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="form" action="{{ route('itemliststore') }}" name="itemlistform" id="itemlistform"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-form-label" for="name">Item Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                    @error('name')
                                    <span class="text-danger"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label" for="hsncode">HSN Code</label>
                                    <input type="text" name="hsncode" oninput="this.value = this.value.toUpperCase();"
                                        id="hsncode" class="form-control">

                                </div>
                                <div class="col-md-6">
                                    <label for="barcode" class="col-form-label">Barcode</label>
                                    <input oninput="checkNumMax(this, 10)" type="number"
                                        value="{{ empty($maxicode) ? 1 : substr($maxicode, 0, -$idlength) + 1 }}"
                                        name="barcode" id="barcode" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="itempicture" class="col-form-label">Item Image</label>
                                    <input type="file" name="itempicture"
                                        onchange="checkFile(this, '1mb', ['jpg', 'png', 'jpeg', 'webp'])"
                                        class="form-control secondary-table-bg" accept=".jpg,.png,.jpeg,.webp">
                                </div>

                            </div>

                            <div class="col-7 mt-4 ml-auto">
                                <button id="submitBtn" type="submit" class="btn btn-primary">Submit <i
                                        class="fa-solid fa-file-export"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="itemlistmast"
                            class="table table-hover table-download-with-search table-hover table-striped">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Sn.</th>
                                    <th>Name</th>
                                    <th>Barcode</th>
                                    <th>HSN Code</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                    <th class="none"></th>
                                    <th class="none"></th>
                                    <th class="none"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sn = 1; @endphp
                                @foreach ($data as $row)
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->barcode }}</td>
                                    <td>{{ $row->hsncode }}</td>
                                    <td>
                                        @if ($row->itempic === null || $row->itempic == '')
                                        <span style="color: red;"> Not Uploaded </span>
                                        @else
                                        <img class="view-now-link" style="width: 43px;height: 25px;"
                                            src="{{ asset('storage/property/itempicture/' . $row->itempic) }}"
                                            alt="{{ $row->name }}"
                                            data-image-url="{{ asset('storage/property/itempicture/' . $row->itempic) }}">

                                        @endif
                                    </td>

                                    <td class="ins">
                                        <button data-toggle="modal" data-target="#updateModal"
                                            class="btn btn-success editBtn update-btn btn-sm">
                                            <i class="fa-regular fa-pen-to-square"></i>Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn"
                                            onclick="handleDeleteRequest('deleteitemlist', this, '{{ base64_encode($row->sn) }}', '{{ base64_encode($row->icode) }}')">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </td>
                                    <td class="none">{{ $row->sn }}</td>
                                    <td class="none">{{ $row->icode }}</td>
                                    <td class="none">{{ $row->itempic }}</td>
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
</div>
<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" class="img-fluid" alt="Image Preview">
            </div>
        </div>
    </div>
</div>

<!-- #/ container -->
<div draggable="true" class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Edit Item List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form" method="POST" action="{{ route('itemlistupstore') }}" name="itemlistsupdateform"
                    id="itemlistsupdateform" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="upname">Item Name</label>
                        <input type="text" name="upname" id="upname" class="form-control" required>
                    </div>
                    <input type="hidden" name="upsn" id="upsn" class="form-control" required>
                    <input type="hidden" name="upicode" id="upicode" class="form-control" required>
                    <input type="hidden" name="olditemimage" id="olditemimage" class="form-control">
                    <div class="form-group">
                        <label class="col-form-label" for="uphsncode">HSN Code</label>
                        <input type="text" name="uphsncode" id="uphsncode" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="upbarcode">Barcode</label>
                        <input type="text" name="upbarcode" id="upbarcode" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="upitemimage" class="col-form-label">Item Image</label>
                        <input type="file" name="upitemimage"
                            onchange="checkFile(this, '1mb', ['jpg', 'png', 'jpeg', 'webp'])" id="upitemimage"
                            class="form-control secondary-table-bg" accept=".jpg,.png,.jpeg,.webp">
                    </div>
                    <div class="text-center">
                        <button id="updateBtn" type="submit" class="btn btn-primary">Update <i
                                class="fa-solid fa-file-export"></i></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<script>
    $(".editBtn").click(function () {
            var name = $(this).closest("tr").find("td:eq(1)").text();
            var barcode = $(this).closest("tr").find("td:eq(2)").text();
            var hsncode = $(this).closest("tr").find("td:eq(3)").text();
            var sn = $(this).closest("tr").find("td:eq(6)").text();
            var code = $(this).closest("tr").find("td:eq(7)").text();
            var pic = $(this).closest("tr").find("td:eq(8)").text();
            populateFormWithData7(name, barcode, hsncode, code, sn, pic);
        });
        $(document).on('click', '.view-now-link', function() {
            var imageUrl = $(this).data('image-url');
            $('#imageModal').find('.modal-body img').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });
</script>
@endsection
