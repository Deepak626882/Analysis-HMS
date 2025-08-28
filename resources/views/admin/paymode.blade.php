@extends('admin.layouts.main')
@section('main-container')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('property.updateExpiry') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Property ID</label>
                                    <select name="propertyid" class="form-control" required>
                                        <option value="">-- Select Property --</option>
                                        @foreach (allproperties() as $property)
                                            <option value="{{ $property->propertyid }}">{{ $property->comp_name }} - ({{ $property->propertyid }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" name="amount" step="0.01" class="form-control" placeholder="Enter amount" required>
                                </div>

                                <!-- Expiry Date -->
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" name="expdate" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Property</th>
                                            <th>Ncur</th>
                                            <th>Exp. Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            use Carbon\Carbon;
                                            use Illuminate\Support\Facades\Crypt;

                                            // Sort collection by expiry date ASC
                                            $sorted = $envgeneral
                                                ->filter(function ($i) {
                                                    return $i->expdate != '';
                                                })
                                                ->sortBy(function ($i) {
                                                    return Carbon::parse(Crypt::decryptString($i->expdate));
                                                });
                                        @endphp

                                        @foreach ($sorted as $item)
                                            @php
                                                $expCarbon = Carbon::parse(Crypt::decryptString($item->expdate));
                                                $expdate = $expCarbon->format('d-m-Y');
                                                $expamount = number_format((float) Crypt::decryptString($item->amount), 2);
                                                $ncurCarbon = Carbon::parse($item->ncur);

                                                // highlight row if expired
                                                $rowClass = $expCarbon->lt($ncurCarbon) ? 'table-danger' : '';
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>{{ $item->comp_name }} ({{ $item->propertyid }})</td>
                                                <td>{{ $ncurCarbon->format('d-m-Y') }}</td>
                                                <td>{{ $expdate }}</td>
                                                <td>{{ $expamount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
