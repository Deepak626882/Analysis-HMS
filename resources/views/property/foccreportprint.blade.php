<head>
    <title>Analysis FOCC Report Print</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/images/favicon.png') }}">
</head>
<style>
    .none {
        display: none;
    }

    .tabulator-header {
        font-weight: bold;
        border-bottom: 2px solid #333;
    }

    .tabulator-headers {
        display: flex !important;
        background: #c6c7c8;
        font-weight: 600;
        border: 1px solid;
    }

    .tabulator .tabulator-header .tabulator-header-contents {
        overflow: hidden;
        position: relative;
    }

    .custom-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
        text-align: center;
    }

    .tabulator-col-title {
        border-right: 1px solid;
    }

    .tabulator-tableholder .tabulator-selectable {
        display: flex;
    }

    .tabulator-tableholder .tabulator-selectable .tabulator-cell {
        border: 1px ridge #373b3e;
        padding: 5px 5px 0 5px;
    }

    .tabulator-tableholder .tabulator-unselectable {
        display: flex;
    }

    .tabulator-tableholder .tabulator-unselectable .tabulator-cell {
        font-weight: 700;
        border: 1px ridge #373b3e;
        padding: 5px 5px 0 5px;
    }

    .tabulator-group-level-0 {
        font-weight: bolder;
        margin: 0 0 0 6px;
    }

    .tabulator-group-level-0 span {
        display: none;
    }

    #front-office .tabulator-tableholder {
        height: auto !important;
    }

    #front-office .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #front-office .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #pos-outlet .tabulator-tableholder {
        height: auto !important;
    }

    #pos-outlet .tabulator-header .tabulator-headers .tabulator-col-sorter-element {
        min-width: auto !important;
        width: auto !important;
        position: relative !important;
        left: auto !important;
        height: auto !important;
    }

    #pos-outlet .tabulator-header .tabulator-headers {
        display: flex !important;
    }

    #pos-outlet .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
        display: flex !important;
    }

    #pos-outlet .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #pos-outlet .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #misc-collection .tabulator-tableholder {
        height: auto !important;
    }

    #misc-collection .tabulator-header .tabulator-headers .tabulator-col-sorter-element {
        min-width: auto !important;
        width: auto !important;
        position: relative !important;
        left: auto !important;
        height: auto !important;
    }

    #misc-collection .tabulator-header .tabulator-headers {
        display: flex !important;
    }

    #misc-collection .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
        display: flex !important;
    }

    #misc-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #misc-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #misx-collection .tabulator-tableholder {
        height: auto !important;
    }

    #misx-collection .tabulator-header .tabulator-headers .tabulator-col-sorter-element {
        min-width: auto !important;
        width: auto !important;
        position: relative !important;
        left: auto !important;
        height: auto !important;
    }

    #misx-collection .tabulator-header .tabulator-headers {
        display: flex !important;
    }

    #misx-collection .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
        display: flex !important;
    }

    #misx-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #misx-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #bill-tocompany .tabulator-tableholder {
        height: auto !important;
    }

    #bill-tocompany .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #bill-tocompany .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #other-collection .tabulator-tableholder {
        height: auto !important;
    }

    #other-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #other-collection .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    @media print {
        #pos-outlet .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
            display: flex !important;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<p class="none" id="totalamount"></p>
<div class="text-center titlep">
    <h3>{{ $comp->comp_name }}</h3>
    <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
    <p style="margin-top:-10px; font-size:16px;">
        {{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}</p>
    <p style="margin-top:-10px; font-size:16px;">FOCC Report</p>
    <p style="text-align:left;margin-top:-10px; font-size:16px;">For Date: <span
            id="fordatep"></span>
    </p>
</div>

<div id="reportprint">

</div>

<div id="reportprint2">

</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    setTimeout(() => {
        let heighttitle = $('.titlep').outerHeight();
        let heightrep = $('#reportprint').outerHeight();
        let totalheight = heighttitle + heightrep;

        if (totalheight > 100) {
            $('#reportprint2').addClass('page-break');
        }
    }, 500);

    setTimeout(() => {
        window.print();
    }, 1000);
</script>
