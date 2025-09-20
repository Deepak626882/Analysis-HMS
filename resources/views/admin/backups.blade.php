@extends('admin.layouts.main')
@section('main-container')
<div class="content-body">
    <div class="container mt-3">
        <!-- Storage Backup Button -->
        <button id="downloadStorageBtn" class="btn btn-primary mb-2">
            <i class="fa-solid fa-download"></i> Download Storage Backup
        </button>

        <!-- Database Backup Button -->
        <button id="downloadDatabaseBtn" class="btn btn-success mb-2">
            <i class="fa-solid fa-database"></i> Download Database Backup
        </button>

        <!-- Verify Database Form -->
        <form id="verifyDbForm" enctype="multipart/form-data" class="mt-3">
            @csrf
            <label class="form-label">Upload Database Backup (ZIP):</label>
            <input type="file" name="backup_file" id="backup_file" class="form-control mb-2" accept=".zip" required>
            <button type="submit" class="btn btn-warning">
                <i class="fa-solid fa-check"></i> Verify Database
            </button>
        </form>
    </div>
</div>

<!-- Verify Results Modal -->
<div class="modal fade" id="verifyDbModal" tabindex="-1" aria-labelledby="verifyDbModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- make modal wide -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="verifyDbModalLabel">Database Verification Results</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="verifyDbResults">
        <!-- Table will be injected here -->
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // Verify DB Form
    $('#verifyDbForm').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        Swal.fire({
            title: 'Verifying Database...',
            text: 'Please wait while we check the uploaded file.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('superadmin.verify-database') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                Swal.close();
                if (response.status === 'success') {
                    let rows = response.data;
                    let today = new Date().toISOString().split('T')[0];

                    let tableHtml = `
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Property ID</th>
                                <th>Ncur Date</th>
                                <th>Inserted Rows (paycharge)</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    rows.forEach(row => {
                        let highlight = (row.ncur === today) ? 'table-success' : '';
                        tableHtml += `
                            <tr class="${highlight}">
                                <td>${row.propertyid}</td>
                                <td>${row.ncur}</td>
                                <td>${row.paycharge_count || ''}</td>
                            </tr>`;
                    });

                    tableHtml += `</tbody></table>`;

                    $('#verifyDbResults').html(tableHtml);
                    $('#verifyDbModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function () {
                Swal.close();
                Swal.fire('Error!', 'Something went wrong during verification.', 'error');
            }
        });
    });

    // Storage Backup
    $('#downloadStorageBtn').click(function () {
        Swal.fire({
            title: 'Creating Storage Backup...',
            html: 'Please wait... Elapsed time: <b>00:00</b>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                startTimer();
            },
            willClose: () => stopTimer()
        });

        $.ajax({
            url: "{{ route('superadmin.storagefdownload') }}",
            method: 'GET',
            success: function (response) {
                stopTimer();
                Swal.close();
                if (response.status === 'success') {
                    triggerDownload(response.url);
                    Swal.fire({
                        icon: 'success',
                        title: 'Storage Backup Ready!',
                        text: 'Download should start shortly.'
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function () {
                stopTimer();
                Swal.close();
                Swal.fire('Error!', 'Something went wrong while creating the backup.', 'error');
            }
        });
    });

    // Database Backup
    $('#downloadDatabaseBtn').click(function () {
        Swal.fire({
            title: 'Enter Database Password',
            input: 'password',
            inputLabel: 'Database Password',
            inputPlaceholder: 'Enter your DB password',
            inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                let dbPassword = result.value;

                Swal.fire({
                    title: 'Creating Database Backup...',
                    html: 'Please wait... Elapsed time: <b>00:00</b>',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        startTimer();
                    },
                    willClose: () => stopTimer()
                });

                $.ajax({
                    url: "{{ route('superadmin.database-backup') }}",
                    method: 'POST',
                    data: {
                        password: dbPassword,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        stopTimer();
                        Swal.close();
                        if (response.status === 'success') {
                            triggerDownload(response.url);
                            Swal.fire({
                                icon: 'success',
                                title: 'Database Backup Ready!',
                                text: 'Download should start shortly.'
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        stopTimer();
                        Swal.close();
                        Swal.fire('Error!', 'Something went wrong while creating the backup.', 'error');
                    }
                });
            }
        });
    });

    // Timer utils
    let timerInterval;
    function startTimer() {
        let startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            Swal.update({ html: `Please wait... Elapsed time: <b>${timeString}</b>` });
        }, 1000);
    }
    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    // Trigger download helper
    function triggerDownload(url) {
        const link = document.createElement('a');
        link.href = url;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
</script>
@endsection
