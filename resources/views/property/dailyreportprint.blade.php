<style>
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

    #bill-to-company .tabulator-tableholder {
        height: auto !important;
    }

    #bill-to-company .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #bill-to-company .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #occupancy-table .tabulator-tableholder {
        height: auto !important;
    }

    #occupancy-table .tabulator-header .tabulator-headers .tabulator-col-sorter-element {
        min-width: auto !important;
        width: auto !important;
        position: relative !important;
        left: auto !important;
        height: auto !important;
    }

    #occupancy-table .tabulator-header .tabulator-headers {
        display: flex !important;
    }

    #occupancy-table .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
        display: flex !important;
    }

    #occupancy-table .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #occupancy-table .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    #occupancy-revenue .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom {
        display: flex;
        font-weight: bold;
    }

    #occupancy-revenue .tabulator-footer .tabulator-calcs-holder .tabulator-calcs-bottom .tabulator-cell {
        border: 1px solid;
    }

    @media print {
        #occupancy-table .tabulator-header .tabulator-headers .tabulator-col-group .tabulator-col-group-cols {
            display: flex !important;
        }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="text-center titlep">
    <h3>{{ $comp->comp_name }}</h3>
    <p style="margin-top:-10px; font-size:16px;">{{ $comp->address1 }}</p>
    <p style="margin-top:-10px; font-size:16px;">
        {{ $statename . ' - ' . $comp->city . ' - ' . $comp->pin }}</p>
    <p style="margin-top:-10px; font-size:16px;">Daily Register Report</p>
    <p style="text-align:left;margin-top:-10px; font-size:16px;">For Date: <span
            id="fordatep"></span>
    </p>
</div>

<div id="reportprint">

</div>


<script>
    setTimeout(() => {
        window.print();
    }, 1000);
</script>