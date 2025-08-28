<style>
    .popup-guide {
        position: fixed;
        top: 20px;
        right: 100px;
        text-align: center;
        z-index: 1000;
    }

    .arrow-up {
        color: #007bff;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-20px);
        }

        60% {
            transform: translateY(-10px);
        }
    }

    .instruction-box {
        background: rgba(0, 123, 255, 0.1);
        border: 2px solid #007bff;
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        max-width: 300px;
    }
</style>

<div class="popup-guide">
    <i class="fa-solid fa-arrow-up fa-3x arrow-up"></i>
    <div class="instruction-box">
        <p class="mb-2"><i class="fa-solid fa-info-circle"></i> Please allow popups to continue</p>
        <small class="text-muted">Look for the popup blocker icon in your browser's address bar</small>
    </div>
</div>

<!--**********************************
            Footer start
        ***********************************-->
<div class="footer">
    <div class="copyright text-center text-bg-secondary rounded-1">
        &copy; Copyright <strong><span>{{ config('app.name', 'Analysis') }} {{ date('Y') }}</span></strong>
        Analysis HMS</a></a></p>
    </div>
</div>
<!--**********************************
            Footer end
        ***********************************-->
</div>
<!--**********************************
        Main wrapper end
    ***********************************-->

<!--**********************************
        Scripts
    ***********************************-->
<script>
    function tryPopup() {
        if (localStorage.getItem('popupChecked')) {
            $('.popup-guide').hide();
            $('.content-body').fadeIn();
            return;
        }

        const popup = window.open('about:blank', 'PopupTest',
            'width=300,height=300,left=100,top=100');

        if (popup === null || typeof popup === 'undefined') {
            console.log('Popup blocked');
            $('.popup-guide').show();
            $('.content-body').hide();
        } else {
            popup.close();
            $('.popup-guide').hide();
            $('.content-body').fadeIn();
            localStorage.setItem('popupChecked', 'true');
        }
    }

    $(document).ready(function() {
        tryPopup();
    });


    $(document).ready(function() {
        $('.content-body').hide();

        tryPopup();
    });
    $(document).ready(function() {
        $('#myloader').removeClass('none');
        setTimeout(() => {
            $('#myloader').addClass('none');
        }, 500);
    });
</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- Notify JS -->
<script src="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.min.js"></script>
<script src="{{ asset('admin/plugins/common/common.min.js') }}"></script>
<script src="{{ asset('admin/js/publicval.js') }}"></script>
<script src="{{ asset('admin/js/custom.min.js') }}"></script>
<script src="{{ asset('admin/js/settings.js') }}"></script>
<script src="{{ asset('admin/js/gleek.js') }}"></script>
<script src="{{ asset('admin/js/chart.js') }}"></script>
<script src="{{ asset('admin/js/styleSwitcher.js') }}"></script>
<script src="{{ asset('admin/js/dashboard/dashboard-1.js') }}"></script>

<script src="{{ asset('admin/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('admin/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
<!-- Clock Plugin JavaScript -->
<script src="{{ asset('admin/plugins/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
<!-- Date Picker Plugin JavaScript -->
<script src="{{ asset('admin/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<!-- Date range Plugin JavaScript -->
<script src="{{ asset('admin/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins-init/form-pickers-init.js') }}"></script>

<!-- Color Picker Plugin JavaScript -->
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('admin/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

</body>

</html>

<script>
    $(document).ready(function() {
        $(document).keydown(function(event) {
            // Check if the active element is not an input, textarea, or select
            if (!$('input, textarea, select').is(':focus')) {
                if (event.shiftKey && event.key === 'S') {
                    window.location.href = "roomstatus";
                } else if (event.shiftKey && event.key === 'W') {
                    window.location.href = "walkincheckin";
                } else if (event.shiftKey && event.key === 'C') {
                    window.location.href = "openchargeposting";
                } else if (event.shiftKey && event.key === 'H') {
                    window.location.href = "company";
                } else if (event.shiftKey && event.key === 'N') {
                    window.location.href = "opennightaudit";
                } else if (event.shiftKey && event.key === 'Q') {
                    window.location.href = "reservation";
                } else if (event.shiftKey && event.key === 'F') {
                    window.location.href = "fomparameter";
                } else if (event.shiftKey && event.key === 'E') {
                    $('#main-wrapper').toggleClass("menu-toggle");
                    $(".hamburger").toggleClass("is-active");
                } else if (event.shiftKey && event.key === 'I') {
                    window.location.href = "inhoseroomstatus";
                }
            }
        });
    });

    // $(document).ready(function() {
    //     let depname = '';
    //     let compdetailxhr = new XMLHttpRequest();
    //     compdetailxhr.open('GET', '/getcompdetail', true);
    //     compdetailxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //     compdetailxhr.onreadystatechange = function() {
    //         if (compdetailxhr.readyState === 4 && compdetailxhr.status === 200) {
    //             let results = JSON.parse(compdetailxhr.responseText);
    //             let compdt = results.comp;
    //             let menuhelp = results.menuhelp;
    //             let compname = compdt.comp_name.split(' ')[0];
    //             let namelength = compname.length;
    //             let curroute = window.location.href.split('/');
    //             curroute = curroute[curroute.length - 1];
    //             let chceckpos = menuhelp.find(x => x.opt1 == 17 && x.route == curroute);
    //             if (chceckpos) {
    //                 let dcode = chceckpos.outletcode;
    //                 let departxhr = new XMLHttpRequest();
    //                 departxhr.open('POST', '/departxhr', true);
    //                 departxhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    //                 departxhr.onreadystatechange = function() {
    //                     if (departxhr.status === 200 && departxhr.readyState === 4) {
    //                         let resultd = JSON.parse(departxhr.responseText);
    //                         depname = resultd ?? '';
    //                         updateUI();
    //                     }
    //                 }
    //                 departxhr.send(`dcode=${dcode}&_token={{ csrf_token() }}`);
    //             } else {
    //                 updateUI();
    //             }

    //             function updateUI() {
    //                 let matchname = menuhelp.find(x => x.route == curroute);
    //                 let div = `<div class="heading-container">
    //                             <h4 class="heading">
    //                             </h4>
    //                         </div>`;
    //                 let span = `${matchname.module} ${depname}`;
    //                 for (let i = 0; i < namelength; i++) {
    //                     span += `<span class="bubble bubble-${i}">${compname.charAt(i)}🫧</span>`;
    //                 }
    //                 $('.container-fluid').before(div);
    //                 $('.heading').append(span);
    //             }
    //         }
    //     }
    //     compdetailxhr.send();
    // });
</script>

<script>
    $(document).ready(function() {
        $("#updateLogModal").on("show.bs.modal", function() {
            $.getJSON("/getUpdateLogs")
                .done(function(data) {
                    let content = "";
                    if (data.length > 0) {
                        content = "<ul class='list-group'>";
                        $.each(data, function(index, log) {
                            content += `<li class='list-group-item'>${log.summary}</li>`;
                        });
                        content += "</ul>";
                    } else {
                        content =
                            "<p class='text-muted text-center'>No updates available at the moment.</p>";
                    }
                    $("#updateLogContent").html(content);
                })
                .fail(function() {
                    $("#updateLogContent").html(
                        "<p class='text-danger text-center'>Failed to load updates.</p>");
                });
        });
    });
</script>
