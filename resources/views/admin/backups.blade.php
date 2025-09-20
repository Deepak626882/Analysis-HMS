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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
$(document).ready(function() {
    let timerInterval;

    function startTimer() {
        let startTime = Date.now();
        
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            Swal.update({
                text: `Please wait... Elapsed time: ${timeString}`
            });
        }, 1000);
    }

    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    // Storage Backup
    $('#downloadStorageBtn').click(function() {
        Swal.fire({
            title: 'Creating Storage Backup...',
            text: 'Please wait... Elapsed time: 00:00',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                startTimer();
            }
        });

        $.ajax({
            url: "{{ route('superadmin.storagefdownload') }}",
            method: 'GET',
            success: function(response) {
                stopTimer();
                Swal.close();
                if(response.status === 'success') {
                    const link = document.createElement('a');
                    link.href = response.url;
                    link.download = '';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    Swal.fire({
                        icon: 'success',
                        title: 'Storage Backup Ready!',
                        text: 'Download should start shortly.'
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function() {
                stopTimer();
                Swal.close();
                Swal.fire('Error!', 'Something went wrong while creating the backup.', 'error');
            }
        });
    });

    // Database Backup
$('#downloadDatabaseBtn').click(function() {
    Swal.fire({
        title: 'Enter Database Password',
        input: 'password',
        inputLabel: 'Database Password',
        inputPlaceholder: 'Enter your DB password',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            let dbPassword = result.value;

            let timerInterval;
            Swal.fire({
                title: 'Creating Database Backup...',
                html: 'Please wait... Elapsed time: <b>00:00</b>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    let seconds = 0;
                    timerInterval = setInterval(() => {
                        seconds++;
                        let mins = Math.floor(seconds / 60);
                        let secs = seconds % 60;
                        let formatted = (mins < 10 ? '0' + mins : mins) + ':' + (secs < 10 ? '0' + secs : secs);
                        Swal.getHtmlContainer().querySelector('b').textContent = formatted;
                    }, 1000);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            });

            $.ajax({
                url: "{{ route('superadmin.database-backup') }}",
                method: 'POST',
                data: {
                    password: dbPassword,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    clearInterval(timerInterval);
                    Swal.close();

                    if(response.status === 'success') {
                        const link = document.createElement('a');
                        link.href = response.url;
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        Swal.fire({
                            icon: 'success',
                            title: 'Database Backup Ready!',
                            text: 'Download should start shortly.'
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    clearInterval(timerInterval);
                    Swal.close();
                    Swal.fire('Error!', 'Something went wrong while creating the backup.', 'error');
                }
            });
        }
    });
});

});
</script>
@endsection
