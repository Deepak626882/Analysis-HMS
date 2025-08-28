@extends('property.layouts.main')
@section('main-container')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <div class="content-body">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" action="{{ url('paytypemasterstore') }}" name="paytypemasterform"
                                id="paytypemasterform" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="name">Payment Name</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                        <div id="namelist"></div>
                                        <span id="name_error" class="text-danger"></span>
                                        @error('name')
                                            <span class="text-danger"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label" for="ac_code">Ledger Account Name</label>
                                        <select id="ac_code" name="ac_code" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($ledgerdata as $list)
                                                <option value="{{ $list->sub_code }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-form-label" for="ac_posting">AC Posting</label>
                                        <select id="ac_posting" name="ac_posting" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Detailed">Detailed</option>
                                            <option value="Summarized">Summarized</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-form-label" for="nature">Nature</label>
                                        <select id="nature" name="nature" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Complementary">Complementary</option>
                                            <option value="Company">Company</option>
                                            <option value="Cash Card">Cash Card</option>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="Hold">Hold</option>
                                            <option value="Member">Member</option>
                                            <option value="Other">Other</option>
                                            <option value="Room">Room</option>
                                            <option value="Staff">Staff</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Void">Void</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-7 mt-4 ml-auto">
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit <i
                                            class="fa-solid fa-file-export"></i></button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table id="revmast"
                                    class="table table-hover table-download-with-search table-hover table-striped">
                                    <thead class="bg-secondary">
                                        <tr>
                                            <th>Sn.</th>
                                            <th>Name</th>
                                            <th>Ac. Name</th>
                                            <th>AC Posting</th>
                                            <th>Nature</th>
                                            <th>Action</th>
                                            <th class="none">code</th>
                                            <th class="none">code</th>
                                            <th class="none">rev_code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sn = 1; @endphp
                                        @foreach ($data as $row)
                                            <tr>
                                                <td>{{ $sn }}</td>
                                                <td id="tdname_{{ $sn }}">{{ $row->name }}</td>
                                                <td>{{ $row->subname }}</td>
                                                <td>{{ $row->ac_posting }}</td>
                                                <td>{{ $row->nature }}</td>
                                                <td class="ins">
                                                    <button id="revedit" data-toggle="modal" data-target="#updateModal"
                                                        class="btn btn-success editBtn update-btn btn-sm">
                                                        <i class="fa-regular fa-pen-to-square"></i>Edit
                                                    </button>

                                                    <a
                                                        href="{{ url('deletepaytype/' . $row->sn . '/' . $row->rev_code) }}">
                                                        <button class="btn btn-danger btn-sm delete-btn">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </button>
                                                    </a>

                                                </td>
                                                <td class="none">{{ $row->sn }}</td>
                                                <td class="none">{{ $row->ac_code }}</td>
                                                <td id="rev_code_original" class="none">{{ $row->rev_code }}</td>
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
    </div>
    <!-- #/ container -->
    <div class="modal fade bd-example-modal-lg" id="updateModal" tabindex="-1" role="dialog"
        aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Edit Pay Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{ url('paymaststoreupdate') }}"
                        name="paytypeupdateform" id="paytypeupdateform">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="col-form-label" for="updatename">Payment Name</label>
                                <input type="text" name="updatename" id="updatename" class="form-control" required>
                                <input type="hidden" name="updatecode" id="updatecode" class="form-control" required>
                                <input type="hidden" name="revcodeup" id="revcodeup" class="form-control" required>
                                <div id="namelist"></div>
                                <span id="name_error" class="text-danger"></span>
                                @error('updatename')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="col-form-label" for="upac_code">Ledger Account Name</label>
                                <select id="upac_code" name="upac_code" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($ledgerdata as $list)
                                        <option value="{{ $list->sub_code }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="col-form-label" for="upac_posting">AC Posting</label>
                                <select id="upac_posting" name="upac_posting" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Detailed">Detailed</option>
                                    <option value="Summarized">Summarized</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="col-form-label" for="upnature">Nature</label>
                                <select id="upnature" name="upnature" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Complementary">Complementary</option>
                                    <option value="Company">Company</option>
                                    <option value="Cash Card">Cash Card</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Hold">Hold</option>
                                    <option value="Member">Member</option>
                                    <option value="Other">Other</option>
                                    <option value="Room">Room</option>
                                    <option value="Staff">Staff</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Void">Void</option>
                                </select>
                            </div>
                        </div>
                        <div id="hide5" class="alert mt-2 hide5 alert-info alert-dismissible fade show"
                            role="alert">Please
                            wait
                            to load the
                            checkboxes
                        </div>

                        <div class="color-facebook bg-gallery pl-lg-5 mt-3 p-2">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between" id="loadcheckbox">
                                </div>
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


    <script>
        // Unit Master Name
        document.addEventListener('DOMContentLoaded', function() {
            var name = document.getElementById('name');
            var namelist = document.getElementById('namelist');
            var currentLiIndex = -1;
            name.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    var liElements = namelist.querySelectorAll('li');
                    currentLiIndex = (currentLiIndex + 1) % liElements.length;
                    if (liElements.length > 0) {
                        name.value = liElements[currentLiIndex].textContent;
                    }
                }
            });
            name.addEventListener('keyup', function() {
                var cid = this.value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/getpaytypenames', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        namelist.innerHTML = xhr.responseText;
                        namelist.style.display = 'block';
                    }
                };
                xhr.send('cid=' + cid + '&_token=' + '{{ csrf_token() }}');

            });
            $(document).on('click', function(event) {
                if (!$(event.target).closest('li').length) {
                    namelist.style.display = 'none';
                }
            });
            $(document).on('click', '#namelist li', function() {
                $('#name').val($(this).text());
                namelist.style.display = 'none';
            });
        });
        $(document).ready(function() {
            //handleFormSubmission('#paytypemasterform', '#submitBtn', 'paytypemasterstore');
            //handleFormSubmission('#paytypeupdateform', '#updateBtn', 'paymaststoreupdate');

            $(".editBtn").click(function() {
                setTimeout(function() {
                    $(".hide5").alert('close');
                }, 5000);

                var name = $(this).closest("tr").find("td:eq(1)").text();
                var ac_code = $(this).closest("tr").find("td:eq(7)").text();
                var ac_posting = $(this).closest("tr").find("td:eq(3)").text();
                var nature = $(this).closest("tr").find("td:eq(4)").text();
                var code = $(this).closest("tr").find("td:eq(6)").text();
                var revcodeup = $(this).closest("tr").find("td:eq(8)").text();
                var revmoti = $(this).closest("tr").find("td:eq(8)").text();

                populateFormWithData5(name, ac_code, ac_posting, nature, code, revcodeup);
                loadcheckboxes('/getcheckboxes');

                async function loadcheckboxes(Endpoint, selectbox) {
                    const loadCheckboxDiv = document.getElementById('loadcheckbox');
                    try {
                        const data = await fetch(Endpoint).then(response => response.json());
                        loadCheckboxDiv.innerHTML = '';

                        const totalCheckboxes = data.length;
                        const checkboxesPerColumn = Math.ceil(totalCheckboxes / 2);

                        let leftColumn = document.createElement('div');
                        let rightColumn = document.createElement('div');

                        for (let i = 0; i < totalCheckboxes; i++) {
                            const item = data[i];
                            const ischecked = await getCheckedStatus(item.dcode, revmoti);
                            const checkbox = createCheckbox(item, ischecked);
                            if (i < checkboxesPerColumn) {
                                leftColumn.appendChild(checkbox);
                            } else {
                                rightColumn.appendChild(checkbox);
                            }
                        }

                        loadCheckboxDiv.appendChild(leftColumn);
                        loadCheckboxDiv.appendChild(rightColumn);
                    } catch (error) {
                        console.error('Error fetching options:', error);
                    }
                }
                // alert(revmoti);
                function getCheckedStatus(dcode, revmoti) {
                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/getperfectcheckrows', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                const data = JSON.parse(xhr.responseText);
                                resolve(data.some(row => row.rest_code === dcode) ? 'checked' :
                                    '');
                            } else if (xhr.readyState === 4) {
                                console.error('Error fetching checked status. Status:', xhr
                                    .status);
                                reject('');
                            }
                        };
                        xhr.send(`cid2=${dcode}&revmoti=${revmoti}&_token={{ csrf_token() }}`);
                    });
                }

                function createCheckbox(item, ischecked) {
                    const checkbox = document.createElement('div');
                    checkbox.className = 'form-check';
                    checkbox.innerHTML = `<input type="checkbox" ${ischecked} class="form-check-input" name="departpay[]" value="${item.dcode}">
        <label class="form-check-label">${item.name}</label>
        <br>`;
                    return checkbox;
                }
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        new Datatable('#revmast');
    </script>
@endsection
