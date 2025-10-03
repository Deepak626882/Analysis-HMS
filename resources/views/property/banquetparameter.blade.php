@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tabs">
                                <div class="tabby-tab">
                                    <input type="radio" id="tab-1" name="tabby-tabs" checked>
                                    <label class="tabby" for="tab-1">Banquet Parameter</label>
                                    <div class="tabby-content">
                                        <form class="form" name="banquetparam" id="banquetparam"
                                            action="{{ route('submitbanquetparameter') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="outdoorcatering" class="col-form-label">Outdoor Catering</label>
                                                    <select id="outdoorcatering" name="outdoorcatering" class="form-control">
                                                        @if (empty(banquetparameter()->outdoorcatering))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ banquetparameter()->outdoorcatering }}">
                                                                {{ $value = banquetparameter()->outdoorcatering == 'Y' ? 'Yes' : 'No' }}
                                                            </option>
                                                        @endif
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                    </select>
                                                    @error('outdoorcatering')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label for="cataloglimit" class="col-form-label">Catelog With Item Limit</label>
                                                    <select id="cataloglimit" name="cataloglimit" class="form-control">
                                                        @if (empty(banquetparameter()->cataloglimit))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ banquetparameter()->cataloglimit }}">
                                                                {{ $value = banquetparameter()->cataloglimit == 'Y' ? 'Yes' : 'No' }}
                                                            </option>
                                                        @endif
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                    </select>
                                                    @error('cataloglimit')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label for="discountac" class="col-form-label">Discount Account</label>
                                                    <select id="discountac" name="discountac" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach (subgroupall() as $item)
                                                            <option value="{{ $item->sub_code }}"
                                                                {{ (banquetparameter()->discountac ?? '') == $item->sub_code ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('discountac')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                     <label for="banquet_edit_date" class="col-form-label">Banquet Edit Date</label>
                                                    <select id="banquet_edit_date" name="banquet_edit_date" class="form-control">
                                                        <option value="0" {{ (banquetparameter()->banquet_edit_date ?? '') == 0 ? 'selected' : '' }}>Yes</option>
                                                        <option value="1" {{ (banquetparameter()->banquet_edit_date ?? '') == 1 ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('banquet_edit_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                </div>

                                                <div class="col-md-6">

                                                    <label for="roundoffac" class="col-form-label">Round Off Account</label>
                                                    <select id="roundoffac" name="roundoffac" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach (subgroupall() as $item)
                                                            <option value="{{ $item->sub_code }}"
                                                                {{ (banquetparameter()->roundoffac ?? '') == $item->sub_code ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('roundoffac')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label for="indoorsaleac" class="col-form-label">In Door Sale Account</label>
                                                    <select id="indoorsaleac" name="indoorsaleac" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach (subgroupall() as $item)
                                                            <option value="{{ $item->sub_code }}"
                                                                {{ (banquetparameter()->indoorsaleac ?? '') == $item->sub_code ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('indoorsaleac')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label for="indoorpartyac" class="col-form-label">In Door Party Account</label>
                                                    <select id="indoorpartyac" name="indoorpartyac" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach (subgroupall() as $item)
                                                            <option value="{{ $item->sub_code }}"
                                                                {{ (banquetparameter()->indoorpartyac ?? '') == $item->sub_code ? 'selected' : '' }}>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('indoorpartyac')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label for="panrequiredyn" class="col-form-label">Pan Required</label>
                                                    <select id="panrequiredyn" name="panrequiredyn" class="form-control">
                                                        @if (empty(banquetparameter()->panrequiredyn))
                                                            <option value="">Select</option>
                                                        @else
                                                            <option value="{{ banquetparameter()->panrequiredyn }}">
                                                                {{ $value = banquetparameter()->panrequiredyn == 'Y' ? 'Yes' : 'No' }}
                                                            </option>
                                                        @endif
                                                        <option value="Y">Yes</option>
                                                        <option value="N">No</option>
                                                    </select>
                                                    @error('panrequiredyn')
                                                        <span class="text-danger"> {{ $message }} </span>
                                                    @enderror

                                                    <label class="col-form-label" for="roundofftype">Round Of Type</label>
                                                    <select id="roundofftype" name="roundofftype" class="form-control">
                                                        @if (empty(banquetparameter()->roundofftype))
                                                            <option value="Standard">Standard</option>
                                                        @endif
                                                        <option value="Upper" {{ banquetparameter()->roundofftype == 'Upper' ? 'selected' : '' }}>Upper</option>
                                                        <option value="Standard" {{ banquetparameter()->roundofftype == 'Standard' ? 'selected' : '' }}>Standard</option>
                                                    </select>

                                                    @error('roundofftype')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                </div>
                                            </div>

                                            <div class="col-7 mt-4 ml-auto">
                                                <button type="submit" class="btn btn-primary">Submit <i
                                                        class="fa-solid fa-file-export"></i></button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
