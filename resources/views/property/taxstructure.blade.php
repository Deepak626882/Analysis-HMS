@extends('property.layouts.main')
@section('main-container')
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <div class="content-body">

        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="icon-speedometer menu-icon"></i>
                            Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="fa-solid fa-screwdriver-wrench"></i>
                            Main Setup</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="fa-solid fa-toolbox"></i> General
                            Setup</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)"><i class="fa-solid fa-folder-tree"></i>
                            Tax Structure</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <form class="form" name="taxform" id="taxform" action="{{ route('taxstrustore') }}"
                            method="POST">
                            @csrf
                            <div class="col-md-4 text-center offset-4">
                                <label class="col-form-label" for="stru_name">Tax Structure Name</label>
                                <div class="row">
                                    <div class="col">
                                        <input class="form-control-sm" type="text" name="stru_name" id="stru_name"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table-hover" id="gridtaxstructure">
                                    <thead>
                                        <tr>
                                            <th>Sno</th>
                                            <th>Tax Name</th>
                                            <th>Rate</th>
                                            <th>Apply On</th>
                                            <th>L Limit</th>
                                            <th>Comparison</th>
                                            <th>U Limit</th>
                                            <th>Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="serial">1</td>
                                            <td>
                                                <select id="tax_code1" name="tax_code1" class="form-control sl" required>
                                                    <option value="">Select</option>
                                                    @foreach ($taxdatamain as $list)
                                                        <option value="{{ $list->rev_code }}">{{ $list->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input name="rate1" class="decimal-input form-visible" step="0.01"
                                                    min="0.00" max="9999.99" placeholder="0.00"
                                                    oninput="checkNumMax(this, 7);handleDecimalInput(event);"
                                                    type="text">
                                            </td>

                                            <td>
                                                <select id="applyon1" name="applyon1" class="form-control sl">
                                                    <option value="">Select</option>
                                                    <option value="On Base Amount">On Base Amount</option>
                                                    <option value="On Running Total">On Running Total</option>
                                                    <option value="On Previous Tax Amount">On Previous Tax Amount
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input name="limits1" class="decimal-input form-visible" step="0.01"
                                                    min="0.00" max="9999.99" placeholder="0.00"
                                                    oninput="checkNumMax(this, 7);handleDecimalInput(event);" type="text"
                                                    onkeydown="addNewRow(event)">
                                            </td>
                                            <td><select id="comparison1" name="comparison1" class="form-control sl">
                                                    <option value="">Select</option>
                                                    <option value="<">
                                                        &lt;</option>
                                                    <option value="<=">
                                                        &lt;=</option>
                                                    <option value=">">></option>
                                                    <option value=">=">>=</option>
                                                    <option value="=">=</option>
                                                    <option value="Between">Between</option>
                                                </select></td>
                                            <td>
                                                <input name="limit1" class="decimal-input form-visible" step="0.01"
                                                    min="0.00" max="9999.99" placeholder="0.00"
                                                    oninput="checkNumMax(this, 7);handleDecimalInput(event);"
                                                    type="text">
                                            </td>
                                            <td>
                                                <select id="condition1" name="condition1" class="form-control sl">
                                                    <option value="">Select</option>
                                                    <option value="Room Rate">Room Rate</option>
                                                    <option value="Rack Rate">Rack Rate</option>
                                                    <option value="Room Tarrif">Room Tarrif</option>
                                                    <option value="Declared Tarrif">Declared Tarrif</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="taxstructure"
                                class="table table-hover table-download-with-search table-hover table-striped">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>Sn.</th>
                                        <th>Name</th>
                                        <th>Property Id</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sn = 1; @endphp

                                    @foreach ($taxdata as $data)
                                        <tr>
                                            <td>{{ $sn }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $propertyid }}</td>
                                            <td class="ins">
                                                <a
                                                    href="updatetaxstructure?name={{ base64_encode($data->name) }}&propertyid={{ base64_encode($propertyid) }}">
                                                    <button class="btn btn-success btn-sm"><i
                                                            class="fa-regular fa-pen-to-square"></i>Edit
                                                    </button>
                                                </a>
                                                <a
                                                    href="deletetaxstructure?name={{ base64_encode($data->name) }}&propertyid={{ base64_encode($propertyid) }}">
                                                    <button class="btn btn-danger btn-sm"><i
                                                            class="fa-solid fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </a>
                                            </td>
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
    </div>
    <script src=""></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        new Datatable('#taxstructure');
    </script>
@endsection
<script>
    $(document).ready(function() {
        $('#myloader').removeClass('none');
        setTimeout(() => {
            $('#myloader').addClass('none');
        }, 500);
    });

    function addNewRow(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const table = document.getElementById("gridtaxstructure");
            const newRow = table.insertRow(table.rows.length);
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
            var cell7 = newRow.insertCell(6);
            var cell8 = newRow.insertCell(7);
            const rowNumber = rowCount + 1;
            cell1.innerHTML = rowNumber;
            cell2.innerHTML = `<select id="tax_code${rowNumber}" name="tax_code${rowNumber}" class="form-control sl" required>
    <option value="">Select</option>
    @foreach ($taxdatamain as $list)
        <option value="{{ $list->rev_code }}">{{ $list->name }}</option>
    @endforeach
</select>`;

            cell3.innerHTML =
                `<input type="text" name="rate${rowNumber}" class="decimal-input form-visible"
            step="0.01" min="0.00" max="9999.99" placeholder="0.00" oninput="checkNumMax(this, 7);handleDecimalInput(event);">`;
            cell4.innerHTML = `<select id="applyon1" name="applyon${rowNumber}" class="form-control sl">
                        <option value="">Select</option>
                        <option value="On Base Amount">On Base Amount</option>
                        <option value="On Running Total">On Running Total</option>
                        <option value="On Previous Tax Amount">On Previous Tax Amount
                        </option>
                    </select>`;
            cell5.innerHTML = `<input type="text" name="limits${rowNumber}" class="decimal-input form-visible" step="0.01"
                        min="0.00" max="9999.99" placeholder="0.00"
                        oninput="checkNumMax(this, 7);handleDecimalInput(event);" onkeydown="addNewRow(event)">`;
            cell6.innerHTML = `<select id="comparison" name="comparison${rowNumber}" class="form-control sl">
                        <option value="">Select</option>
                        <option value="<">
                            <</option>
                        <option value="<=">
                            <=</option>
                        <option value=">">></option>
                        <option value=">=">>=</option>
                        <option value="=">=</option>
                        <option value="Between">Between</option>
                    </select>`;
            cell7.innerHTML = `<input type="text" name="limit${rowNumber}" class="decimal-input form-visible" step="0.01"
min="0.00" max="9999.99" placeholder="0.00"
oninput="checkNumMax(this, 7);handleDecimalInput(event);">`;
            cell8.innerHTML = `<select id="condition${rowNumber}" name="condition${rowNumber}" class="form-control sl">
                        <option value="">Select</option>
                        <option value="Room Rate">Room Rate</option>
                        <option value="Rack Rate">Rack Rate</option>
                        <option value="Room Tarrif">Room Tarrif</option>
                        <option value="Declared Tarrif">Declared Tarrif</option>
                    </select>`;
            rowCount++;
        }
    }
</script>
