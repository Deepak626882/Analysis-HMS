<input type="hidden" value="{{ $printdata['billno'] }}" name="vnoup" id="vnoup">
<input type="hidden" value="{{ $printdata['vdate'] }}" name="vdatesale1" id="vdatesale1">
<input type="hidden" value="{{ $printdata['vtype'] }}" name="vtype" id="vtype">
<input type="hidden" value="{{ $printdata['docid'] }}" name="sale1docid" id="sale1docid">
<input type="hidden" value="{{ $printdata['waiter'] }}" name="waiter" id="waiter">
<input type="hidden" value="{{ $printdata['departname'] }}" name="departname" id="departname">
<input type="hidden" value="{{ $printdata['departnature'] }}" name="departnature" id="departnature">
<input type="hidden" value="{{ $printdata['kotno'] }}" name="kotno" id="kotno">
<input type="hidden" value="{{ $printdata['roomno'] }}" name="roomno" id="roomno">
<input type="hidden" value="{{ $printdata['outletcode'] }}" name="outletcode" id="outletcode">
<input type="hidden" value="{{ $page }}" name="page" id="page">
<input type="hidden" value="{{ $printdata['printsetup']->description }}" name="printdescription" id="printdescription">

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    $(document).ready(function() {
        let vnoup = $('#vnoup').val();
        let vdatesale1 = $('#vdatesale1').val();
        let vtype = $('#vtype').val();
        let departname = $('#departname').val();
        let kotno = $('#kotno').val();
        let waiter = $('#waiter').val();
        let outletcode = $('#outletcode').val();
        let departnature = $('#departnature').val();
        let sale1docid = $('#sale1docid').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        let filetoopen;
        if ($('#printdescription').val() == 'Bill Windows Plain Paper') {
            filetoopen = 'salebillprint';
            let openfile = window.open(filetoopen, '_blank');
            openfile.onload = function() {
                $(openfile.document).ready(function() {
                    $('#roomno', openfile.document).text(roomno);
                    $('#vdate', openfile.document).text(vdatesale1);
                    $('#billno', openfile.document).text(vnoup);
                    $('#vtype', openfile.document).text(vtype);
                    $('#departname', openfile.document).text(departname);
                    $('#kotno', openfile.document).text(kotno);
                    $('#waiter', openfile.document).text(waiter);
                    $('#outletcode', openfile.document).text(outletcode);
                    $('#departnature', openfile.document).text(departnature);
                    $('#addeddocid', openfile.document).text(sale1docid);
                });
            };
            setTimeout(() => {
                window.location.href = `${$('#page').val()}?dcode=${outletcode}`;
            }, 2000);
        } else if ($('#printdescription').val() == '3 Inch Running Paper Windows Print') {
            filetoopen = 'salebillprint2';
            let openfile = window.open(filetoopen, '_blank');
            openfile.onload = function() {
                $(openfile.document).ready(function() {
                    $('#roomno', openfile.document).text(roomno);
                    $('#vdate', openfile.document).text(vdatesale1);
                    $('#billno', openfile.document).text(vnoup);
                    $('#vtype', openfile.document).text(vtype);
                    $('#departname', openfile.document).text(departname);
                    $('#kotno', openfile.document).text(kotno);
                    $('#waiter', openfile.document).text(waiter);
                    $('#outletcode', openfile.document).text(outletcode);
                    $('#departnature', openfile.document).text(departnature);
                    $('#addeddocid', openfile.document).text(sale1docid);
                });
            };
            setTimeout(() => {
                window.location.href = `${$('#page').val()}?dcode=${outletcode}`;
            }, 2000);
        } else if ($('#printdescription').val() == '3 Inch Running Paper DOS Print') {
            $.ajax({
                url: 'salebillprintthermal',
                data: {
                    docid: sale1docid
                },
                method: "POST",
                success: function(response) {
                    setTimeout(() => {
                        window.location.href = `${$('#page').val()}?dcode=${outletcode}`;
                    }, 500);
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

    });
</script>
