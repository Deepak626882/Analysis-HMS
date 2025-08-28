@extends('property.layouts.main')
@section('main-container')
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
                            Tax Structure Update</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <form class="form" name="taxform" id="taxform" action="{{ route('taxstrustoreupdate') }}"
                            method="POST">
                            @csrf
                            <div class="col-md-4 text-center offset-4">
                                <label class="col-form-label" for="name">Tax Structure Name</label>
                                <div class="row">
                                    <div class="col">
                                        <?php
                                        if (isset($taxdata[0]->name)) {
                                            $name = $taxdata[0]->name;
                                        } else {
                                            header('Location: /taxstructure');
                                            exit();
                                        }
                                        ?>
                                        <input class="form-control-sm" value="{{ $name }}" type="text"
                                            name="name" id="name" required>
                                        <input type="hidden" name="oldtaxstruname" value="{{ $taxdata[0]->name }}">
                                        <input type="hidden" value="{{ $taxdata[0]->str_code }}" name="oldstr_code" id="oldstr_code">
                                        <input type="hidden" id="maxsn" name="maxsn"
                                            value="{{ $taxdata->max('sno') }}">
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
                                        {{-- @foreach ($taxdata as $data) --}}
                                        @foreach ($taxdata->sortBy('sno') as $data)
                                            <tr>
                                                <td id="serial">{{ $data->sno }}</td>
                                                <td>
                                                    <select id="tax_code" name="tax_code{{ $data->sno }}"
                                                        class="form-control slup" required>
                                                        @if (empty($data->tax_code))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ $data->tax_code }}">{{ $data->revname }}
                                                            </option>
                                                        @endif
                                                        @foreach ($taxdatamain as $list)
                                                            <option value="{{ $list->rev_code }}">{{ $list->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <input name="rate{{ $data->sno }}"
                                                        class="decimal-input form-visible" step="0.01" min="0.00"
                                                        max="9999.99" value="{{ $data->rate }}" placeholder="0.00"
                                                        oninput="checkNumMax(this, 7);handleDecimalInput(event);"
                                                        type="text">
                                                </td>

                                                <td>
                                                    <select id="applyon1" name="applyon{{ $data->sno }}"
                                                        class="form-control slup">
                                                        @if (empty($data->nature))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ $data->nature }}">{{ $data->nature }}
                                                            </option>
                                                        @endif
                                                        <option value="On Base Amount">On Base Amount</option>
                                                        <option value="On Running Total">On Running Total</option>
                                                        <option value="On Previous Tax Amount">On Previous Tax Amount
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input name="limits{{ $data->sno }}"
                                                        class="decimal-input form-visible" step="0.01" min="0.00"
                                                        max="9999.99" value="{{ $data->limits }}" placeholder="0.00"
                                                        oninput="checkNumMax(this, 7);handleDecimalInput(event);"
                                                        type="text" onkeydown="addNewRow(event)">
                                                </td>
                                                <td><select id="comparison1" name="comparison{{ $data->sno }}"
                                                        class="form-control slup">
                                                        @if (empty($data->comp_operator))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ $data->comp_operator }}">
                                                                {{ $data->comp_operator }}</option>
                                                        @endif
                                                        <option value="<">
                                                            &lt;</option>
                                                        <option
                                                            value="<=">
                                                            &lt;=</option>
                                                        <option value=">
                                                            ">></option>
                                                        <option
                                                            value=">=">>=</option>
                                                        <option value="=">=</option>
                                                        <option value="Between">Between</option>
                                                    </select></td>
                                                <td>
                                                    <input name="limit{{ $data->sno }}"
                                                        class="decimal-input form-visible" step="0.01" min="0.00"
                                                        max="9999.99"value="{{ $data->limit1 }}" placeholder="0.00"
                                                        min="0.00" max="9999.99" placeholder="0.00"
                                                        oninput="checkNumMax(this, 7);handleDecimalInput(event);"
                                                        type="text">
                                                </td>
                                                <td>
                                                    <select id="condition" name="condition{{ $data->sno }}"
                                                        class="form-control slup">
                                                        @if (empty($data->condapp))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ $data->condapp }}">
                                                                {{ $data->condapp }}</option>
                                                        @endif
                                                        <option value="Room Rate">Room Rate</option>
                                                        <option value="Rack Rate">Rack Rate</option>
                                                        <option value="Room Tarrif">Room Tarrif</option>
                                                        <option value="Declared Tarrif">Declared Tarrif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                        {{ $data->sno++ }}
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-4 mb-4">
                                <button type="submit" class="btn btn-primary">Update <i
                                        class="fa-regular fa-pen-to-square"></i></button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        $(document).ready(function() {
            $('#myloader').removeClass('none');
            setTimeout(() => {
                $('#myloader').addClass('none');
            }, 500);
        });
        var taxRowCounter = parseInt(document.getElementById("maxsn").value) + 1;

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
                const rowNumber = taxRowCounter;

                cell1.innerHTML = rowNumber;
                cell2.innerHTML = `<select id="tax_code${rowNumber}" name="tax_code${rowNumber}" class="form-control slup" required>
    <option value="">Select</option>
    @foreach ($taxdatamain as $list)
        <option value="{{ $list->rev_code }}">{{ $list->name }}</option>
    @endforeach
</select>`;

                cell3.innerHTML =
                    `<input type="text" name="rate${rowNumber}" class="decimal-input form-visible"
            step="0.01" min="0.00" max="9999.99" placeholder="0.00" oninput="checkNumMax(this, 7);handleDecimalInput(event);">`;
                cell4.innerHTML = `<select id="applyon1" name="applyon${rowNumber}" class="form-control slup">
                        <option value="">Select</option>
                        <option value="On Base Amount">On Base Amount</option>
                        <option value="On Running Total">On Running Total</option>
                        <option value="On Previous Tax Amount">On Previous Tax Amount
                        </option>
                    </select>`;
                cell5.innerHTML = `<input type="text" name="limits${rowNumber}" class="decimal-input form-visible" step="0.01"
                        min="0.00" max="9999.99" placeholder="0.00"
                        oninput="checkNumMax(this, 7);handleDecimalInput(event);" onkeydown="addNewRow(event)">`;
                cell6.innerHTML = `<select id="comparison" name="comparison${rowNumber}" class="form-control slup">
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
                cell8.innerHTML = `<select id="condition${rowNumber}" name="condition${rowNumber}" class="form-control slup">
                        <option value="">Select</option>
                        <option value="Room Rate">Room Rate</option>
                        <option value="Rack Rate">Rack Rate</option>
                        <option value="Room Tarrif">Room Tarrif</option>
                        <option value="Declared Tarrif">Declared Tarrif</option>
                    </select>`;
                taxRowCounter++;

            }
        }
    </script>
@endsection
