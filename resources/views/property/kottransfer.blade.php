@extends('property.layouts.main')
@section('main-container')
    <div class="content-body">
        <div style="padding: 0;" class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body box animate__animated animate__bounceIn">
                            <form action="{{ route('kottransferstore') }}" method="post">
                                @csrf
                                <input type="hidden" value="{{ $dcode }}" name="dcode" id="dcode">
                                <input type="hidden" name="docid" id="docid">
                                <div class="row p-4">
                                    <div class="col-md-6">
                                        <label for="vno" class="col-form-label">Kot No.</label>
                                        <select class="form-control" name="vno" id="vno">
                                            <option value="">Select</option>
                                            @foreach ($vnos as $item)
                                                <option value="{{ $item->vno }}">{{ $item->vno }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-warning position-absolute" id="roomnopicked"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="roomno" class="col-form-label">Room No.</label>
                                        <select class="form-control" name="roomno" id="roomno">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
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
    <!-- #/ container -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            let roomnos = [];
            $(document).on('change', '#vno', function() {
                let vno = $(this).val();
                let dcode = $('#dcode').val();
                let roomnoselect = $('#roomno');
                let vnoxhr = new XMLHttpRequest();
                vnoxhr.open('POST', '/vnoxhr', true);
                vnoxhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                vnoxhr.onreadystatechange = function() {
                    if (vnoxhr.readyState === 4 && vnoxhr.status === 200) {
                        let results = JSON.parse(vnoxhr.responseText);
                        if (results && results.restrooms) {
                            let restrooms = results.restrooms;
                            roomnoselect.empty();
                            roomnoselect.append('<option value="">Select</option>');
                            roomnos = [];
                            restrooms.forEach((data) => {
                                roomnos.push(data.roomno);
                                roomnoselect.append(`<option data-id="${data.docid}" value="${data.roomno}">${data.roomno}</option>`);
                            });
                            if (results.oneroom) {
                                $('#roomnopicked').text(results.oneroom.roomno);
                                $('#docid').val(results.oneroom.docid);
                            }
                        } else {
                            console.error('Unexpected response structure:', results);
                        }
                    }
                }
                vnoxhr.send(`vno=${vno}&dcode=${dcode}&_token={{ csrf_token() }}`);
            });

            $(document).on('change', '#roomno', function() {
                let roomno = $(this).val();
                let m = roomnos.find(x => x === roomno);
                let docid = $(this).find('option:selected').data('id');
                // $('#docid').val(docid);
            });
        });
    </script>
@endsection
