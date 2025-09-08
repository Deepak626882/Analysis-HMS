@extends('property.layouts.main')
@section('main-container')
    @include('cdns.select')
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <form method="post" action="">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="member_id">Member ID</label>
                                                <input type="text" class="form-control" name="member_id" id="member_id">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="application_no">Application No</label>
                                                <input type="text" class="form-control" name="application_no" id="application_no">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="application_date">Application Date</label>
                                                <input type="date" class="form-control" name="application_date" id="application_date">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="membership_date">Membership Date</label>
                                                <input type="date" value="{{ ncurdate() }}" class="form-control" name="membership_date" id="membership_date">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="col-form-label" for="reservationtype">Contact Person</label>
                                                <select class="form-control select2-multiple" name="greetings" id="greetings">
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Ms.">Ms.</option>
                                                    <option value="Mam">Mam</option>
                                                    <option value="Dr.">Dr.</option>
                                                    <option value="Prof.">Prof.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Sir">Sir</option>
                                                    <option value="Madam">Madam</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fullname">Full Name</label>
                                                <input type="text" class="form-control" name="fullname" id="fullname" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fathername">Father Name</label>
                                                <input type="text" class="form-control" name="fathername" id="fathername">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mothername">Mother Name</label>
                                                <input type="text" class="form-control" name="mothername" id="mothername">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="accountname">A/C Name</label>
                                                <select class="form-control  select2-multiple" name="accountname" id="accountname">
                                                    <option value="">Select</option>
                                                    @foreach (subgroupall() as $item)
                                                        <option value="{{ $item->sub_code }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="undergroup">Under Group</label>
                                                <select class="form-control  select2-multiple" name="undergroup" id="undergroup">
                                                    <option value="">Select</option>
                                                    @foreach (subgroupall() as $item)
                                                        <option value="{{ $item->sub_code }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="membercategory">Category</label>
                                                <select class="form-control  select2-multiple" name="membercategory" id="membercategory">
                                                    <option value="">Select</option>
                                                    @foreach (membercategories() as $item)
                                                        <option value="{{ $item->code }}">{{ $item->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="allowcredit">Allow Credit</label>
                                                <select class="form-control  select2-multiple" name="allowcredit" id="allowcredit">
                                                    <option value="">Select</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="creditlimit">Creditlimit</label>
                                                <input type="number" class="form-control" name="creditlimit" id="creditlimit" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="activeyn">Active</label>
                                                <select class="form-control  select2-multiple" name="activeyn" id="activeyn">
                                                    <option value="">Select</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control  select2-multiple" name="gender" id="gender">
                                                    <option value="">Select</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dob">Date Of Birth</label>
                                                <input type="date" class="form-control" name="dob" id="dob">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nationality">Nationality</label>
                                                <select class="form-control select2-multiple" name="nationality" id="nationality">
                                                    <option value="">Select</option>
                                                    @foreach (allcountries() as $item)
                                                        <option value="{{ $item->nationality }}">{{ $item->nationality }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="bloodgroup">Blood Group</label>
                                                <select class="form-control select2-multiple" name="bloodgroup" id="bloodgroup">
                                                    <option value="">Select Blood Group</option>
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="relegion">Religion</label>
                                                <select class="form-control select2-multiple" name="relegion" id="relegion">
                                                    <option value="">Select Religion</option>
                                                    <option value="hindu">Hindu</option>
                                                    <option value="muslim">Muslim</option>
                                                    <option value="christian">Christian</option>
                                                    <option value="sikh">Sikh</option>
                                                    <option value="jain">Jain</option>
                                                    <option value="buddhist">Buddhist</option>
                                                    <option value="judaism">Judaism</option>
                                                    <option value="bahai">Bahá'í Faith</option>
                                                    <option value="confucianism">Confucianism</option>
                                                    <option value="taoism">Taoism</option>
                                                    <option value="shinto">Shinto</option>
                                                    <option value="zoroastrianism">Zoroastrianism</option>
                                                    <option value="animism">Animism</option>
                                                    <option value="atheism">Atheism</option>
                                                    <option value="agnosticism">Agnosticism</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pancard">Pancard</label>
                                                <input type="text" class="form-control" name="pancard" id="pancard">
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(document).on('change', '#allowcredit', function() {
                $('#creditlimit').val($('#allowcredit').val() == 'yes' ? '' : '').prop('readonly', $('#allowcredit').val() == 'yes' ? false : true);
            });
        });
    </script>
@endsection
