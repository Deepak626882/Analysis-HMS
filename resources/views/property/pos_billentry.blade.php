@extends('property.layouts.main')
@section('main-container')
    <style>
        .kotentry input.amount {
            height: auto;
            width: 7em;
            min-width: auto;
            min-height: auto !important;
        }

        .kotentry input.discountfix {
            height: auto;
            width: 4em;
            min-width: auto;
            min-height: auto !important;
        }

        .kotentry input.discountsundry {
            height: auto;
            width: 7em;
            min-width: auto;
            min-height: auto !important;
        }

        input.sevenem {
            height: auto;
            width: 7em;
            min-width: auto;
            min-height: auto !important;
        }

        tfoot.salebilltfoot {}

        tfoot.salebilltfoot tr {}

        tfoot.salebilltfoot td {
            padding: 2px;
        }
    </style>
    <div class="content-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body box animate__animated animate__bounceIn">

                            <div class="row">
                                <div class="col-md-3 d-flex">
                                    <h5>{{ $depdata->name }}<span class="badge"><i class="fa-brands fa-galactic-senate"></i></span></h5>
                                    <button disabled class="btn mt-1 btn-sm btn-success" name="billprint" id="billprint"
                                        type="button">Bill Print</button>
                                </div>
                                <div class="col-md-8 d-flex justify-content-between">
                                    <p id="roomno"></p>
                                    <p id="vdatetime"></p>
                                    <p id="companyname"></p>
                                    <p id="waitername"></p>
                                </div>
                            </div>
                        </div>
                        <form class="mt-3 mb-3" action="">
                            <input type="hidden" value="{{ $printsetup->description }}" name="printdescription" id="printdescription">
                            <input type="hidden" value="{{ $depdata->dcode }}" name="dcode" id="dcode">
                            <input type="hidden" value="{{ $depdata->name }}" name="departname" id="departname">
                            <input type="hidden" value="{{ $depdata->nature }}" name="departnature" id="departnature">
                            <input type="hidden" name="sale1docid" id="sale1docid">
                            <input type="hidden" name="vnoup" id="vnoup" value="">
                            <input type="hidden" name="kotno" id="kotno" value="">
                            <input type="hidden" name="waitersname" id="waitersname" value="">
                            <input type="hidden" name="vdatesale1" id="vdatesale1" value="">
                            <input type="hidden" class="form-control" name="vtype" id="vtype" value="{{ 'B' . $depdata->short_name }}">
                            <input type="hidden" name="roomno" id="roomno">
                            <input type="hidden" name="addeddocid" id="addeddocid">
                            <div class="row p-3">
                                <div class="col-md-3">
                                    <label for="vprefix">For Year</label>
                                    <select class="form-control" name="vprefix" id="vprefix">
                                        @foreach ($years as $item)
                                            <option value="{{ $item->prefix }}">{{ $item->prefix }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="billno" class="col-form-label">Bill No</label>
                                    <input autocomplete="off" aria-autocomplete="list" placeholder="Enter Bill No..." type="text" class="form-control" name="billno" id="billno">
                                    <ul id="suggestions1" class="list-group suggestions-list mt-1"></ul>
                                </div>
                            </div>
                        </form>
                        <div style="display: grid;place-content: center;" class="table-container">
                            <table style="width: 50%;" id="itemsdata" class="table table-hover">
                                <thead>
                                    <tr style="border-top: 1px solid #0000000f;">
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th>Kot No.</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="bg-gallery salebilltfoot">
                                </tfoot>
                            </table>
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
            let billnos = [];
            let dcode = $('#dcode').val();
            let allbillxhr = new XMLHttpRequest();
            allbillxhr.open('POST', '/allbillxhrsale', true);
            allbillxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            allbillxhr.onreadystatechange = function() {
                if (allbillxhr.status === 200 && allbillxhr.readyState === 4) {
                    let results = JSON.parse(allbillxhr.responseText);
                    let sale1 = results.sale1;
                    sale1.forEach((data) => {
                        billnos.push(data.vno.toString());
                    });

                    initAutoSuggest('billno', 'suggestions1', billnos);
                    if (results.length == 0 || results == null) {
                        pushNotify('info', 'No Data Found', 'No Data Found');
                        return;
                    }
                }
            }
            allbillxhr.send(`dcode=${dcode}&_token={{ csrf_token() }}`);

            let inputTimer;
            $('#billno').on('input', function() {
                let itemsdata = $('#itemsdata tbody');
                itemsdata.empty();
                clearTimeout(inputTimer);
                inputTimer = setTimeout(() => {
                    let billno = $(this).val();
                    if (billno == '') {
                        $('#invalidbill').text('');
                    }
                    let vprefix = $('#vprefix').val();
                    $('#orderno').text('');
                    let manualid = $('#rest').val();
                    let itemnamexhr = new XMLHttpRequest();
                    itemnamexhr.open('POST', '/fetchitemoldroomno', true);
                    itemnamexhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    itemnamexhr.onreadystatechange = function() {
                        if (itemnamexhr.readyState === 4 && itemnamexhr.status === 200) {
                            let results = JSON.parse(itemnamexhr.responseText);
                            let location = new XMLHttpRequest();
                            if (results === 'false') {
                                $('#submitBtn').text('Submit');
                                $('#salebillform').prop('action', '{{ route('salebillsubmit') }}');
                                $('#invalidbill').text(`Invalid Bill No. ${billno}`);
                                pushNotify('error', 'Sale Bill Entry', `Invalid Bill No. ${billno}`, 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'left top');
                                $('#roomnumbers').text('');
                                $('#itemsdata tbody').empty();
                                $('#itemsdata tfoot').empty();
                                $('#companyname').html('');
                                $('#company').val('');
                                $('#roomno').val('');
                                $('#pax').val('');
                                $('#billprint').prop('disabled', true);
                                $('#roomno').text('');
                            } else {
                                $('#billprint').prop('disabled', false);
                                let totalitems = results.items.length;
                                totaladditems = totalitems;
                                $('#addeditems').text(totalitems);
                                $('#addeditems').css('font-size', 'large');
                                let printnum = totalitems.toString();
                                pushNotify('success', 'Sale Bill Entry', printnum + ' Item Added', 'fade', 300, '', '', true, true, true, 3000, 20, 20, 'outline', 'left top');
                                setTimeout(() => {
                                    $('#addeditems').css('font-size', 'small');
                                }, 1000);
                                $('#vnoup').val(results.sale1.vno ?? '');
                                $('#kotno').val(results.sale1.kotno ?? '');
                                $('#vdatesale1').val(results.sale1.vdate ?? '');
                                $('#vdatetime').text(`Date: ${dmy(results.sale1.vdate)} ${results.sale1.vtime}`);
                                $('#roomno').val(results.sale1.roomno ?? '');
                                $('#waitername').html(`${results.waitername == null ? '' : `Waiter: ${results.waitername}`}`);
                                $('#sale1docid').val(results.sale1.docid);
                                let subgroup = results.subgroup;
                                // console.log(subgroup);
                                if (subgroup != null) {
                                    // $('#company').val(subgroup.sub_code);
                                    // $('#compgst').text(subgroup.gstin);
                                    $('#companyname').html(`${subgroup.name == null ? '' : `Company: ${subgroup.name}`} ${subgroup.gstin == null ? '' : `(${subgroup.gstin})`}`);
                                }
                                let guestdetails = results.guestdt;
                                if (guestdetails != null) {
                                    $('#addeddocid').val(guestdetails.docid);
                                    // $('#companyname').html(`${guestdetails.subname == null ? '' : `Company: ${guestdetails.subname}`} ${guestdetails.gstin == null ? '' : `(${guestdetails.gstin})`}`);
                                }
                                if (results.chkguestprof != null) {
                                    $('#addeddocid').val(results.chkguestprof.docid);
                                }

                                $('#compdiv').removeClass('none');
                                $('#invalidbill').text('');
                                $('#submitBtn').text('Update');
                                $('#salebillform').prop('action', '{{ route('salebillupdate') }}');
                                let items = results.items;
                                $('#roomnumbers').text('Room: ' + items[0].roomno);
                                $('#orderno').text('Previous Order');
                                $('#roomno').prop('disabled', true);
                                $('#itemsdata tbody').empty();
                                $('#itemsdata tfoot').empty();
                                let sundrytype = results.sundrytype;
                                let suntransdata = results.suntransdata;
                                let tbodyData = '';
                                let tfootData = '';
                                let currentrowcount = $('#itemsdata tbody tr').length;
                                let currentrowcounttfoot = $('#itemsdata tfoot tr').length;
                                let ajaxRequestsCompleted = 0;
                                suntransdata.forEach((sunitem, index, array) => {
                                    if (index === 0) {
                                        tfootData = `<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">${sunitem.dispname}</td>
                                <td id="totalamount"></td>
                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'discount') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                    <input type="text" readonly class="form-control discountfix" name="discountfix" id="discountfix" value="${sunitem.svalue}">
                                                </td>
                                                <td><input type="text" readonly class="form-control discountsundry" name="discountsundry" id="discountsundry" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'service charge') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                    <input type="text" readonly class="form-control servicechargefix" name="servicechargefix" id="servicechargefix" value="${sunitem.svalue}">
                                                </td>
                                                <td><input type="text" readonly class="form-control servicechargeamount" name="servicechargeamount" id="servicechargeamount" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'cgst') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                </td>
                                                <td><input type="text" readonly class="form-control sevenem cgstamount" name="cgstamount" id="cgstamount" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'sgst') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                </td>
                                                <td><input type="text" readonly class="form-control sevenem sgstamount" name="sgstamount" id="sgstamount" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'round off') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                </td>
                                                <td><input type="text" readonly class="form-control sevenem roundoffamount" name="roundoffamount" id="roundoffamount" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    if (sunitem.dispname.toLowerCase() == 'net amount') {
                                        tfootData += `<tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="d-flex ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">
                                                    <span class="mt-2 mr-1">${sunitem.dispname}</span>
                                                </td>
                                                <td><input type="text" readonly class="form-control sevenem netamount" name="netamount" id="netamount" value="${sunitem.amount}"></td>
                                            </tr>`;
                                    }

                                    // if (sunitem.peroramt == 'A' && index !== 0) {
                                    //     tfootData += `<tr>
                                //     <td></td>
                                //     <td></td>
                                //     <td></td>
                                //     <td></td>
                                //     <td data-value="${sunitem.revcode}" class="taxcodes ${sunitem.bold == 'Y' ? 'font-weight-bold' : ''}">${sunitem.dispname}</td>
                                //     <td data-value="${sunitem.revcode}">
                                //         ${index !== array.length - 1 ?
                                //             `<input value="${sunitem.svalue}" type="text" class="form-control sevenem taxvaluesinput" name="taxvalues${sunitem.sno}${sunitem.revcode.slice(0, -3)}" id="taxvalues${sunitem.sno}" readonly>` :
                                //             `<input value="${sunitem.svalue}" type="text" readonly class="form-control sevenem taxvaluesinput" name="netamount" id="netamount" readonly>`
                                //         }
                                //     </td>
                                // </tr>`;
                                    // }
                                });
                                $('#itemsdata tfoot').append(tfootData);
                                let label = results.label;
                                items.forEach((item, index) => {
                                    $('#waiter').val(item.waiter);
                                    $('#kotdocid').val(item.kotdocid);
                                    $('#stockdocid').val(item.docid);
                                    $('#vnostock').val(item.vno);
                                    $('#previousroomno').val(item.roomno);
                                    $('#roomno').text(`${label}: ${item.roomno}`);
                                    $('#pax').val(item.guaratt);
                                    let rowIndex = currentrowcount + index + 1;
                                    let taxcode = item.TaxStru;
                                    let discapp = item.discapp;
                                    let tax_rate = item.taxper;
                                    if (item.kot_yn == 'Y') {
                                        rateedit = (item.RateEdit == 'N') ? 'readonly' : '';
                                    } else {
                                        rateedit = (item.RateEdit == 'Y') ? '' : 'readonly';
                                    }
                                    tbodyData += `<tr>
                          <td style="white-space: nowrap;">
                              <span style="${item.kot_yn == 'Y' ? 'display: none;' : ''}"><button type="button" class="removeItem"><i class="fa-regular fa-circle-xmark"></i></button></span>
                              <input name="itemcode${rowIndex}" id="itemcode${rowIndex}" value="${item.item}" type="hidden">
                              <input name="itemname${rowIndex}" class="itemnameclass" id="itemname${rowIndex}" value="${item.Name}" type="hidden">
                              <input name="discapp${rowIndex}" id="discapp${rowIndex}" value="${item.discapp}" type="hidden">
                              <input name="kotsno${rowIndex}" id="kotsno${rowIndex}" value="${item.kotsno}" type="hidden">
                              <input name="itemnumber${rowIndex}" class="itemnumber" id="itemnumber${rowIndex}" value="${rowIndex}" type="hidden">
                              ${item.Name}
                          </td>
                          <td><input readonly name="description${rowIndex}" value="${item.description}" placeholder="Enter" id="description${rowIndex}" class="form-control description inone" type="text"></td>
                          <td class="text-center">${item.kotvno}</td>
                          <td>
                            <div class="panelinc">
                                <button type="button" style="${item.kot_yn == 'Y' ? 'display: none;' : ''}" class="decrement btn">-</button>
                                <input name="quantity${rowIndex}" id="quantity${rowIndex}" class="form-control eighteem qtyitem" type="text" readonly value="${item.qtyiss}"}>
                                <button type="button" style="${item.kot_yn == 'Y' ? 'display: none;' : ''}" class="increment btn">+</button>
                            </div>
                          </td>
                          <td><input oninput="checkNumMax(this, 7); handleDecimalInput(event);" ${rateedit} class="rateclass form-control eighteem" name="rate${rowIndex}" id="rate${rowIndex}" value="${item.rate}" type="text" readonly></td>
                          <td>
                              <input type="text" name="amount${rowIndex}" id="amount${rowIndex}" value="${item.amount}" class="form-control amount" readonly>
                              <input type="hidden" name="fixamount${rowIndex}" id="fixamount${rowIndex}" value="${item.amount}" class="form-control fixamount" readonly>
                          </td>
                          <td class="none"><input type="text" name="taxrate_sum${rowIndex}" id="taxrate_sum${rowIndex}" value="${item.taxper}" class="form-control taxrate_sum" readonly></td>
                          <td class="none"><input type="text" name="tax_code${rowIndex}" id="tax_code${rowIndex}" value="${item.tax_code}" class="form-control tax_code" readonly></td>
                         </tr>`;
                                    ajaxRequestsCompleted++;
                                    if (ajaxRequestsCompleted === items.length) {
                                        $('#itemsdata tbody').append(tbodyData);
                                    }
                                });
                            }
                        } else if (itemnamexhr.status === 204) {
                            pushNotify('error', 'Bill Print', 'Invalid Vno', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                        }
                    }
                    itemnamexhr.send(`vprefix=${vprefix}&billno=${billno}&dcode=${dcode}&_token={{ csrf_token() }}`);
                    // setTimeout(() => {
                    //     calculatetaxes();
                    //     calculateDiscount();
                    // }, 1500);
                }, 1000);
            });



            function calcper(amount, percentage) {
                return ((amount * percentage) / 100).toFixed(2);
            }

            function calcitemper(amount, disc) {
                return ((amount - (amount * disc) / 100).toFixed(2));
            }

            function calculatetaxes() {
                taxableamt = 0;
                $('#taxableamt').val('0.00');
                index = 1;
                let ncs = 0;
                let nss = 0;
                let tbodyLength = $('#itemsdata tbody tr').length;
                let totalamount = 0;
                let discountinput = parseFloat($('input[name^="discountfix"]').val());
                for (let i = 1; i <= tbodyLength; i++) {
                    let itemrate = parseFloat($('#fixamount' + i).val()) ?? 0.00;
                    if (isNaN(itemrate)) {
                        console.error("Item rate is NaN for input field #" + i);
                        continue;
                    }
                    let taxeditemrate = parseFloat($('#fixamount' + i).val());
                    // console.log(taxeditemrate);
                    let discapp = $('#discapp' + i).val();
                    if (discapp == 'Y') {
                        let newitemrate = (itemrate - (itemrate * discountinput) / 100);
                        // $('#fixamount' + i).val(newitemrate.toFixed(2));
                        taxeditemrate = newitemrate.toFixed(2);
                        $(`#discedamount${i}`).val(taxeditemrate);
                    }
                    itemrate = Math.floor(itemrate * 100) / 100;
                    totalamount += parseFloat(itemrate);
                    // let trate = $('#taxrate_sum' + i).val();
                    let trate = $('#tax_rate' + i).val();
                    // console.log(trate);
                    let tableid = $('#itemsdata tbody');
                    let amts = tableid.find('tr td .amount');
                    let ttaxcodetmp = $('#tax_code' + i).val() ?? '';
                    let [cgst = '', sgst = ''] = ttaxcodetmp.split(',').map(value => typeof value !== 'undefined' ? value : '');
                    if (trate != 0) {
                        let newtaxvalue = calcper(taxeditemrate, trate);
                        taxableamt += itemrate;
                        let fixtaxval = newtaxvalue / 2;
                        if (cgst.startsWith('CGSS')) {
                            ncs += parseFloat(fixtaxval);
                        }

                        if (sgst.startsWith('SGSS')) {
                            nss += parseFloat(fixtaxval);
                        }
                        if ($('#cgstamount')) {
                            $('#cgstamount').val(ncs.toFixed(2));
                            $('#cgstamt').val(ncs.toFixed(2));
                        }
                        if ($('#sgstamount')) {
                            $('#sgstamount').val(nss.toFixed(2));
                            $('#sgstamt').val(ncs.toFixed(2));
                        }
                        index++;
                    }
                }

                fixtaxableamt = taxableamt.toFixed(2);
                $('#taxableamt').val(fixtaxableamt);
                fixtotlamt = totalamount.toFixed(2);
                $('#totalamount').text(fixtotlamt);
                $('#totalamt').val(fixtotlamt);
                $('#totalitemsum').val(tbodyLength);
            }

            function calculateDiscountPercentage() {
                let discountamount = parseFloat($('input[name^="discountsundry"]').val());
                let amountval = fixtotlamt;
                let amount = parseFloat(amountval);

                if (!isNaN(discountamount) && !isNaN(amount)) {
                    let discountPercentage = (discountamount / amount) * 100;
                    $('input[name^="discountfix"]').val(discountPercentage.toFixed(2));

                    // Optionally, you can update the net amount and other calculations
                    // let newamount = amount - discountamount;
                    // let cgssamt = $('#cgstamount').val();
                    // let sgssamt = $('#sgstamount').val();
                    // let servicecharge = $('#servicechargeamount').val() || 0.00;
                    // let fixnetamount = parseFloat(cgssamt) + parseFloat(sgssamt) + parseFloat(newamount) + parseFloat(servicecharge);
                    // let decimalvalue = (fixnetamount - Math.floor(fixnetamount)) * 100;
                    // decimalvalue = 100 - parseFloat(decimalvalue.toFixed(2));
                    // let integerValue = Math.ceil(fixnetamount);
                    // $('input[name$="RSRO"]').val('0.' + decimalvalue);
                    // $('#roundoff').val('0.' + decimalvalue);
                    // $('#netamount').val(integerValue.toFixed(2));
                    // $('#netamount').val(500);
                } else {
                    // console.log("Invalid input. Please enter valid numbers.");
                }
            }

            function calculateDiscount() {
                let discountinput = $('input[name^="discountfix"]').val();
                let amountval = fixtotlamt;
                let discount = parseFloat(discountinput);
                let amount = parseFloat(amountval);
                let totalAmount = parseFloat($('#totalamount').text());
                let restamt = 0.00;
                $('#itemsdata tbody tr').each(function() {
                    let SChrgApp = $(this).find('input[id^="SChrgApp"]').val();
                    if (SChrgApp === 'Y') {
                        let amount = parseFloat($(this).find('input.discedamount').val()) || 0;
                        totalAmount += amount;
                    }
                });

                let servicechargefix = $('#servicechargefix').val();
                console.log(totalAmount);
                if (totalAmount > 0 && servicechargefix) {
                    restamt = totalAmount * parseFloat(servicechargefix) / 100;
                    $('#servicechargeamount').val(restamt.toFixed(2));
                }

                if (!isNaN(discount) && !isNaN(amount)) {
                    let deductedamount = amount * (discount / 100);
                    let newamount = amount - deductedamount;
                    $('input[name^="discountsundry"]').val(deductedamount.toFixed(2));

                    let cgssamt = $('#cgstamount').val();
                    let sgssamt = $('#sgstamount').val();
                    let serviceamt = $('#servicechargeamount').val() || 0.00;
                    let fixnetamount = parseFloat(cgssamt) + parseFloat(sgssamt) + parseFloat(serviceamt) + parseFloat(newamount);
                    fixnetamount = fixnetamount.toFixed(2);
                    let integerValue = Math.ceil(fixnetamount);

                    let decimalvalue = integerValue - fixnetamount;
                    $('#roundoffamount').val(decimalvalue.toFixed(2));
                    $('#netamount').val((integerValue).toFixed(2));
                }
            }

            $('#billprint').click(function() {
                let tbody = $('#itemsdata tbody');
                let rowcount = tbody.find('tr').length;
                let roomno = $('#roomno').val();
                if (roomno === '' || roomno === null) {
                    pushNotify('error', 'Salebill Entry', 'Please Select Room No.!');
                    return;
                }
                if (rowcount === 0) {
                    pushNotify('error', 'Salebill Entry', 'Please Add Some Item First!');
                    return;
                }

                let vnoup = $('#vnoup').val();
                let vdatesale1 = $('#vdatesale1').val();
                let vtype = $('#vtype').val();
                let departname = $('#departname').val();
                let filetoopen;
                if ($('#printdescription').val() == 'Bill Windows Plain Paper') {
                    filetoopen = 'salebillprint';
                } else if ($('#printdescription').val() == '3 Inch Running Paper Windows Print') {
                    filetoopen = 'salebillprint2';
                }
                let kotno = $('#kotno').val();
                let waitersname = $('#waitersname').val();
                let outletcode = $('#dcode').val();
                let departnature = $('#departnature').val();
                let addeddocid = $('#addeddocid').val();

                let openfile = window.open(filetoopen, '_blank');
                openfile.onload = function() {
                    $('#roomno', openfile.document).text(roomno);
                    $('#vdate', openfile.document).text(vdatesale1);
                    $('#billno', openfile.document).text(vnoup);
                    $('#vtype', openfile.document).text(vtype);
                    $('#departname', openfile.document).text(departname);
                    $('#departnature', openfile.document).text(departnature);
                    $('#kotno', openfile.document).text(kotno);
                    $('#waiter', openfile.document).text(waitersname);
                    $('#outletcode', openfile.document).text(outletcode);
                    $('#addeddocid', openfile.document).text(addeddocid);
                }
            });
        });
    </script>
@endsection
