@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" name="menuitemform" action="{{url('menuitemstore')}}" id="menuitemform" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="itempic" id="itempic" value="">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="restcode">Restaurant Name</label>
                                        <select id="restcode" name="restcode" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($restaurentdata as $list)
                                                <option value="{{ $list->dcode }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="itemgrp">Item Group</label>
                                        <select id="itemgrp" name="itemgrp" class="form-control" required>
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="itemname">Name</label>
                                        <select id="itemname" name="itemname" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($itemnames as $list)
                                                <option value="{{ $list->icode }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="itemcode">Code</label>
                                        <input type="number" name="itemcode" oninput="allmx(this,10),checkNumMax(this, 10);"
                                            id="itemcode" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="hsncode">HSN Code</label>
                                        <input readonly type="text" name="hsncode" oninput="allmx(this,50)" id="hsncode"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="barcode">Bar Code</label>
                                        <input type="text" name="barcode" oninput="allmx(this,50)" id="barcode"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="unit">Unit</label>
                                        <select id="unit" name="unit" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($unit as $list)
                                                <option value="{{ $list->ucode }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="itemcatmast">Item Category</label>
                                        <select id="itemcatmast" name="itemcatmast" class="form-control" required>
                                            <option value="">Select</option>
                                            {{-- @foreach ($itemcatmast as $list)
                                                <option value="{{ $list->Code }}">{{ $list->Name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="rateedit">Rate Edit</label>
                                        <select id="rateedit" name="rateedit" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Y">Yes</option>
                                            <option value="N" selected>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="discappl">Disc Applicable</label>
                                        <select id="discappl" name="discappl" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Y" selected>Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="servicecharge">Service Charge</label>
                                        <select id="servicecharge" name="servicecharge" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Y">Yes</option>
                                            <option value="N" selected>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="salerate">Sale Rate</label>
                                        <input type="text" name="salerate"
                                            oninput="checkNumMax(this, 7); handleDecimalInput(event);" id="salerate"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="rateinctax">Rate Inc. Tax</label>
                                        <select id="rateinctax" name="rateinctax" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Y">Yes</option>
                                            <option value="N" selected>No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="applicabldate">Applicable Date</label>
                                        <input type="date" name="applicabldate" id="applicabldate" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="kitchen">Kitchen</label>
                                        <select id="kitchen" name="kitchen" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($kitchen as $list)
                                                <option value="{{ $list->dcode }}" @if (substr($list->dcode, 0, 2) == 'MK') selected @endif>{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="type">Type</label>
                                        <select id="type" name="type" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Manufactured Item">Manufactured Item</option>
                                            <option value="Trade Item">Trade Item</option>
                                            <option value="Proprietory Item">Proprietory Item</option>
                                            <option value="Other" selected>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="activeyn">Active YN</label>
                                        <select id="activeyn" name="activeyn" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="Y" selected>Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="dishtype">Dish Type</label>
                                        <select id="dishtype" name="dishtype" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="1" selected>Veg</option>
                                            <option value="2">Nonveg</option>
                                            <option value="3">Egg</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label" for="favourite">Favourite</label>
                                        <select id="favourite" name="favourite" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-7 mt-4 ml-auto">
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit <i
                                            class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table id="menuitem"
                                class="table table-hover table-download-with-search table-hover table-striped">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>Sn.</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Group</th>
                                        <th>Category</th>
                                        <th>Disp</th>
                                        <th>Restaurant</th>
                                        <th>Rate</th>
                                        <th>Disc</th>
                                        <th>Redit</th>
                                        <th>Active</th>
                                        <th>Kitchen</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                        <th class="none">code</th>
                                        <th class="none">restcode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sn = 1; @endphp
                                    @foreach ($itemmast as $row)
                                        <tr>
                                            <td>{{ $sn }}</td>
                                            <td>{{ $row->itemname }}</td>
                                            <td>{{ $row->unitname }}</td>
                                            <td>{{ $row->itemgrpname }}</td>
                                            <td>{{ $row->itemcatname }}</td>
                                            <td>{{ $row->DispCode }}</td>
                                            <td>{{ $row->Restaurant }}</td>
                                            <td>{{ $row->Rate }}</td>
                                            <td>{{ $row->DiscApp == 'N' ? 'No' : 'Yes' }}</td>
                                            <td>{{ $row->RateEdit == 'N' ? 'No' : 'Yes' }}</td>
                                            <td>{{ $row->ActiveYN == 'N' ? 'No' : 'Yes' }}</td>
                                            <td>{{ $row->kitchenname }}</td>
                                            <td>{{ $row->NType }}</td>
                                            <td class="ins">
                                                <button id="revedit" data-toggle="modal" data-target="#updateModal"
                                                    class="btn btn-success editBtn update-btn btn-sm">
                                                    <i class="fa-regular fa-pen-to-square"></i>Edit
                                                </button>

                                               <a href="{{ url('deletemenuitem/' . $row->sn . '/' . $row->Code) }}">
                                                    <button class="btn btn-danger btn-sm delete-btn">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </a>
                                            </td>
                                            <td class="none">{{ $row->Code }}</td>
                                            <td class="none">{{ $row->RestCode }}</td>
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
    <!-- #/ container -->
    <div class="modal fade bd-example-modal-lg" id="updateModal" tabindex="-1" role="dialog"
        aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Edit Menu Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{url('menuitemstoreupdate')}}" name="menuitemformupdate" id="menuitemformupdate">
                        @csrf
                        <input type="hidden" name="upcode" id="upcode">
                        <div class="row">
                            <input type="hidden" name="upitempic" id="upitempic" value="">
                            <div class="col-md-3">
                                <label class="col-form-label" for="uprestcode">Restaurant Name</label>
                                <select id="uprestcode" name="uprestcode" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($restaurentdata as $list)
                                        <option value="{{ $list->dcode }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upitemgrp">Item Group</label>
                                <select id="upitemgrp" name="upitemgrp" class="form-control" required>
                                    {{-- <option value="">Select</option>
                                    @foreach ($itemgrp as $list)
                                        <option value="{{ $list->code }}">{{ $list->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upitemname">Name</label>
                                <input type="text" class="form-control" name="upitemname" id="upitemname" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upitemcode">Code</label>
                                <input readonly type="text" name="upitemcode" oninput="allmx(this,50)" id="upitemcode"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="col-form-label" for="uphsncode">HSN Code</label>
                                <input readonly type="text" name="uphsncode" oninput="allmx(this,50)" id="uphsncode"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upbarcode">Bar Code</label>
                                <input type="text" name="upbarcode" oninput="allmx(this,50)" id="upbarcode"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upunit">Unit</label>
                                <select id="upunit" name="upunit" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($unit as $list)
                                        <option value="{{ $list->ucode }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upitemcatmast">Item Category</label>
                                <select id="upitemcatmast" name="upitemcatmast" class="form-control" required>
                                    {{-- <option value="">Select</option> --}}
                                    {{-- @foreach ($itemcatmast as $list)
                                        <option value="{{ $list->Code }}">{{ $list->Name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label class="col-form-label" for="uprateedit">Rate Edit</label>
                                <select id="uprateedit" name="uprateedit" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="updiscappl">Disc Applicable</label>
                                <select id="updiscappl" name="updiscappl" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upservicecharge">Service Charge</label>
                                <select id="upservicecharge" name="upservicecharge" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upsalerate">Sale Rate</label>
                                <input type="text" name="upsalerate"
                                    oninput="checkNumMax(this, 7); handleDecimalInput(event);" id="upsalerate"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label class="col-form-label" for="uprateinctax">Rate Inc. Tax</label>
                                <select id="uprateinctax" name="uprateinctax" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upapplicabldate">Applicable Date</label>
                                <input type="date" name="upapplicabldate" id="upapplicabldate" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upkitchen">Kitchen</label>
                                <select id="upkitchen" name="upkitchen" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($kitchen as $list)
                                        <option value="{{ $list->dcode }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="uptype">Type</label>
                                <select id="uptype" name="uptype" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Manufactured Item">Manufactured Item</option>
                                    <option value="Trade Item">Trade Item</option>
                                    <option value="Proprietory Item">Proprietory Item</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label class="col-form-label" for="upactiveyn">Active YN</label>
                                <select id="upactiveyn" name="upactiveyn" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="updishtype">Dish Type</label>
                                <select id="updishtype" name="updishtype" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="1">Veg</option>
                                    <option value="2">Nonveg</option>
                                    <option value="3">Egg</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="col-form-label" for="upfavourite">Favourite</label>
                                <select id="upfavourite" name="upfavourite" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center">
                            <button id="updateBtn" type="submit" class="btn mt-3 btn-primary">Update <i
                                    class="fa-solid fa-file-export"></i></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#menuitem', {
                "pageLength": 15
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('change', '#restcode', function() {
                let restcode = $(this).val();
                if (restcode != '') {
                    let restxhr = new XMLHttpRequest();
                    restxhr.open('POST', '/restxhr', true);
                    restxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    restxhr.onreadystatechange = function() {
                        if (restxhr.status === 200 && restxhr.readyState === 4) {
                            let result = JSON.parse(restxhr.responseText);
                            let itemgrps = result.itemgrps;
                            let itemcats = result.itemcats;
                            $('#itemgrp').html('');
                            $('#itemcatmast').html('');
                            let opt = `<option value=''>Select</option>`;
                            itemgrps.forEach((data, index) => {
                                opt += `<option value='${data.code}'>${data.name}</option>`;
                            });
                            $('#itemgrp').append(opt);

                            let optitemcat = `<option value=''>Select</option>`;
                            itemcats.forEach((datas, index) => {
                                optitemcat += `<option value='${datas.Code}'>${datas.Name}</option>`;
                            });
                            $('#itemcatmast').append(optitemcat);
                        }
                    }
                    restxhr.send(`restcode=${restcode}&_token={{ csrf_token() }}`);
                }
            })

            //handleFormSubmission('#menuitemform', '#submitBtn', 'menuitemstore');
            //handleFormSubmission('#menuitemformupdate', '#updateBtn', 'menuitemstoreupdate');
            $(document).ready(function() {
                document.getElementById("itemname").addEventListener("change", function() {
                    var icode = this.value;
                    if (icode == "") {
                        $('#itemcode').val("");
                        $('#hsncode').val("");
                        $('#barcode').val("");
                    }
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "{{ route('getitemdata') }}");
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var data = JSON.parse(xhr.responseText);
                            $("#hsncode").val(data.hsncode);
                            $("#barcode").val(data.barcode);
                            $("#itempic").val(data.itempic);
                        }
                    };
                    xhr.send("icode=" + icode);
                });
            });
            $(document).on('click', '.editBtn', function() {
                let iname = $(this).closest("tr").find("td:eq(1)").text();
                $('#upitemname').val(iname);
                var code = $(this).closest("tr").find("td:eq(14)").text();
                var restcode = $(this).closest("tr").find("td:eq(15)").text();
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "{{ route('itemmastupdata') }}");
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var result = JSON.parse(xhr.responseText);
                        let data = result.itemdata;
                        let itemgrps = result.itemgrps;
                        $('#upitemgrp').html('');
                        let opt = `<option value=''>Select</option>`;
                        itemgrps.forEach((data, index) => {
                            opt += `<option value='${data.code}'>${data.name}</option>`;
                        });
                        $('#upitemgrp').append(opt);
                        let itemcats = result.itemcats;
                        $('#upitemcatmast').html('');
                        let optitemcat = `<option value=''>Select</option>`;
                        itemcats.forEach((data, index) => {
                            optitemcat += `<option value='${data.Code}'>${data.Name}</option>`;
                        });
                        $('#upitemcatmast').append(optitemcat);
                        $("#uprestcode").val(data.RestCode);
                        $("#upitemgrp").val(data.ItemGroup);
                        //$("#upitemname").val(data.itemcode);
                        $("#upitemcode").val(data.DispCode);
                        $("#uphsncode").val(data.HSNCode);
                        $("#upbarcode").val(data.BarCode);
                        $("#upunit").val(data.Unit);
                        $("#upitemcatmast").val(data.ItemCatCode);
                        $("#uprateedit").val(data.RateEdit);
                        $("#updiscappl").val(data.DiscApp);
                        $("#updishtype").val(data.dishtype);
                        $("#upfavourite").val(data.favourite);
                        $("#upservicecharge").val(data.SChrgApp);
                        $("#upsalerate").val(data.Rate);
                        $("#uprateinctax").val(data.RateIncTax);
                        $("#upapplicabldate").val(data.AppDate);
                        $("#upkitchen").val(data.Kitchen);
                        $("#uptype").val(data.NType);
                        $("#upactiveyn").val(data.ActiveYN);
                        $("#upcode").val(data.Code);
                        $("#upsn").val(data.sn);
                    }
                };
                xhr.send(`code=${code}&restcode=${restcode}`);
            });
        });


        $(document).ready(function() {
            // setInterval(function () {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "{{ route('getmaxitemcode') }}");
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    $("#itemcode").val(data);
                }
            };
            xhr.send();
            // }, 1000);
        });

        //Fetch Current Financial Year
        $(document).ready(function() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "{{ route('getcurfinyear') }}");
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    $("#applicabldate").val(data);
                }
            };
            xhr.send();
        });
    </script>
@endsection
