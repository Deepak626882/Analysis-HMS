@extends('property.layouts.main')
@section('main-container')
    <style>
        .occupied {
            background-color: {{ $depdata->occupied }};
        }

        .vacant {
            background-color: {{ $depdata->vacant }};
        }

        .billed {
            background-color: {{ $depdata->billed }};
        }

        .booked {
            background-color: {{ $depdata->booked }};
        }

        .maintenance {
            background-color: #ff5964;
        }
    </style>

    @php
        function isDarkColor($hexColor)
        {
            $r = hexdec(substr($hexColor, 1, 2));
            $g = hexdec(substr($hexColor, 3, 2));
            $b = hexdec(substr($hexColor, 5, 2));
            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
            $threshold = 128;
            return $brightness < $threshold;
        }
    @endphp
    <div class="content-body pos_display">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card pos_display">
                        <div class="card-body box animate__animated animate__bounceIn">
                            <span class="none" id="roomnokot"></span>
                            <span class="none" id="vnofix"></span>
                            <div class="menucover none">
                                <div class="menudiv">
                                    <div class="btn-group-vertical">
                                        <button id="kotbutton" onclick="openkot()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/plus.gif') }}" alt=""> KOT</button>
                                        <button id="salebillbutton" onclick="opensalebill()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/plus.gif') }}" alt=""> Sale Bill</button>
                                        <button id="viewbutton" onclick="openviewitem()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/eye.gif') }}" alt=""> View Item</button>
                                        <button id="closediv" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/cancel.gif') }}" alt=""> Canel</button>
                                    </div>
                                </div>
                            </div>
                            </h5>
                            <p id="updatemsg" class="p-2 updatemsg bg-light">Pos Updated</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="head">
                                        <p class="p-2 box animate__animated animate__bounceInLeft" data-status="occupied"
                                            style="background: {{ $depdata->occupied }}; color: {{ isDarkColor($depdata->occupied) ? 'white' : 'black' }}"
                                            onclick="openColorPicker('occupied')">Occupied</p>
                                        <p class="p-2 box animate__animated animate__bounceInDown" data-status="vacant"
                                            style="background: {{ $depdata->vacant }}; color: {{ isDarkColor($depdata->vacant) ? 'white' : 'black' }}"
                                            onclick="openColorPicker('vacant')">Vacant</p>
                                        <p class="p-2 box animate__animated animate__bounceInRight" data-status="billed"
                                            style="background: {{ $depdata->billed }}; color: {{ isDarkColor($depdata->billed) ? 'white' : 'black' }}"
                                            onclick="openColorPicker('billed')">Billed</p>
                                        <p class="p-2 box animate__animated animate__bounceInLeft" data-status="booked"
                                            style="background: {{ $depdata->booked }}; color: {{ isDarkColor($depdata->booked) ? 'white' : 'black' }}"
                                            onclick="openColorPicker('booked')">Booked</p>
                                    </div>
                                </div>
                                <div id="details" class="col-md-6">
                                    <div class="head2 d-flex bubble-text stylish-border">
                                        <p id="waitername"></p>
                                        <p id="roomnot"></p>
                                        <p id="sessionmast"></p>
                                        <p id="kottime"></p>
                                    </div>
                                </div>
                            </div>
                            <form name="posdispform" id="posdispform" action="{{ route('posdisplaysubmit') }}" method="POST"
                                enctype="multipart/form-data">
                                <input type="hidden" value="{{ $depdata->dcode }}" name="dcode" id="dcode">
                                <input type="hidden" value="{{ $depdata->nature }}" name="nature" id="nature">
                                <input type="hidden" value="{{ $label }}" name="label" id="label">
                                <input type="color" id="occupiedColor" name="occupied" value="{{ $depdata->occupied }}"
                                    style="display: none;">
                                <input type="color" id="vacantColor" name="vacant" value="{{ $depdata->vacant }}"
                                    style="display: none;">
                                <input type="color" id="billedColor" name="billed" value="{{ $depdata->billed }}"
                                    style="display: none;">
                                <input type="color" id="bookedColor" name="booked" value="{{ $depdata->booked }}"
                                    style="display: none;">

                                <div class="room-grid">
                                    @foreach ($roomocc as $item)
                                        <div class="card-container">
                                            <div class="card-flip">
                                                <div class="front">
                                                    <div class="box animate__animated animate__pulse room-boxdisp {{ $item['status'] }}"
                                                        data-value="{{ $item['docid'] ?? '' }}" data-id="{{ $item['roomno'] ?? $item['rcode'] }}">
                                                        <div class="room-number" style="color: {{ isDarkColor($depdata->occupied) ? 'white' : 'black' }};">
                                                            {{ $item['roomno'] ?? $item['rcode'] }}
                                                        </div>
                                                        <div class="room-statusdisp text-dark text-uppercase">
                                                            {{ $item['waitername'] == null ? $item['status'] : $item['waitername'] }}
                                                        </div>
                                                        <input type="hidden" value="{{ $item['roomno'] ?? $item['rcode'] }}" name="roomcode" id="roomcode">
                                                    </div>
                                                </div>
                                                <div class="back">
                                                    <div class="box animate__animated animate__pulse room-boxdisp {{ $item['status'] }}"
                                                        data-value="{{ $item['docid'] ?? '' }}" data-id="{{ $item['roomno'] ?? $item['rcode'] }}">
                                                        <div class="room-number" style="color: {{ isDarkColor($depdata->occupied) ? 'white' : 'black' }};">
                                                            {{ $item['roomno'] ?? $item['rcode'] }}
                                                        </div>
                                                        <div class="room-statusdisp text-dark text-uppercase">
                                                            {{ $item['waitername'] == null ? $item['status'] : $item['waitername'] }}
                                                        </div>
                                                        <input type="hidden" value="{{ $item['roomno'] ?? $item['rcode'] }}" name="roomcode" id="roomcode">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
            let flippedCards = [];

            setInterval(function() {
                let cards = $('.card-container');
                let numberOfCardsToFlip = Math.floor(Math.random() * 1) + 1;
                let indices = [];

                while (indices.length < numberOfCardsToFlip) {
                    let randomIndex = Math.floor(Math.random() * cards.length);
                    if (!indices.includes(randomIndex)) {
                        indices.push(randomIndex);
                    }
                }

                indices.forEach(index => {
                    let card = $(cards[index]);
                    card.addClass('flip');
                    flippedCards.push(card);

                    for (let i = 0; i < 10; i++) {
                        let star = $('<div class="star"></div>').appendTo(card);
                        let randomX = Math.random() * 200 - 100;
                        let randomY = Math.random() * 200 - 100;

                        star.css({
                            transform: `translate(${randomX}px, ${randomY}px)`
                        });

                        setTimeout(() => {
                            star.remove();
                        }, 100);
                    }

                    setTimeout(() => {
                        card.removeClass('flip');
                        flippedCards = flippedCards.filter(c => c !== card);
                    }, 2000);
                });
            }, 25000);
        });

        function openkot() {
            let dcode = $('#dcode').val();
            let roomno = $('#roomnokot').text();
            window.location.href = `kotentry?dcode=${dcode}&roomno=${roomno}`;
        }

        function opensalebill() {
            let dcode = $('#dcode').val();
            let roomno = $('#roomnokot').text();
            window.location.href = `salebillentry?dcode=${dcode}&roomno=${roomno}`;
        }

        function openchangetable() {
            let dcode = $('#dcode').val();
            let roomno = $('#roomnokot').text();
            window.location.href = `pos_tablechangedynamic?dcode=${dcode}&roomno=${roomno}`;
        }

        function openviewitem() {
            let dcode = $('#dcode').val();
            let roomno = $('#roomnokot').text();
            window.location.href = `billlockup?dcode=${dcode}&tableno=${roomno}`;
        }

        function opensettlement() {
            let dcode = $('#dcode').val();
            let roomno = $('#roomnokot').text();
            let vno = $('#vnofix').text();
            window.location.href = `settlemententry?dcode=${dcode}&tableno=${roomno}&vno=${vno}`;
        }

        $(document).ready(function() {
            let kotdata;
            let sessionmast;
            let firstkot;
            let sale1;
            let dcode = $('#dcode').val();
            const nature = $('#nature').val();
            if (nature === 'Outlet') {
                const settlebutton = `<button onclick="window.location.href='settlemententry?dcode=${dcode}'" id="settlementbuttn" class="box animate__animated animate__bounceInRight btn ml-4 btn-warning btn-outline-danger"><i class="fa-solid fa-hammer"></i> Settlement</button>`;
                $('.head').append(settlebutton);
            }

            var occupiedColor = $('#occupiedColor').val();
            var vacantColor = $('#vacantColor').val();
            var billedColor = $('#billedColor').val();
            let tbody = $('#posdisp tbody tr');

            let roomnos = [];
            $(tbody).each(function() {
                let rooms = $(this).find('td').data('id');
                roomnos.push(rooms);
            });

            let colorfillxhr = new XMLHttpRequest();
            colorfillxhr.open('POST', '/colorfill', true);
            colorfillxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            colorfillxhr.onreadystatechange = function() {
                if (colorfillxhr.status === 200 && colorfillxhr.readyState === 4) {
                    let results = JSON.parse(colorfillxhr.responseText);
                    kotdata = results.kot;
                    sessionmast = results.sessionmast;
                    firstkot = results.firstkot;
                    sale1 = results.sale1;
                }
            }
            colorfillxhr.send(`dcode=${dcode}&_token={{ csrf_token() }}`);
            
            let counter = 1;
            $('.room-boxdisp').on('click', function() {
                $('#kotbutton').prop('disabled', false);
                let chkbilledcls = $(this).hasClass('billed');
                if (chkbilledcls == true) {
                    let fbtn = $('.menudiv').find('button#settlementbutton');
                    if (fbtn.length) {
                        fbtn.remove();
                    }
                    setTimeout(() => {
                        $('#kotbutton').prop('disabled', true);
                        const settlementbutton = `<button id="settlementbutton" onclick="opensettlement()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/corruption.gif') }}" alt=""> Settlement</button>`;
                        $('#bookingbutton').after(settlementbutton);
                    }, 500);
                } else {
                    let fbtn = $('.menudiv').find('button#settlementbutton');
                    if (fbtn.length) {
                        fbtn.remove();
                    }
                }

                let roomno = $(this).data('id');
                const nature = $('#nature').val();
                if (nature.toLowerCase() == 'outlet') {
                    $('#roomnot').html(`<b>Table: </b>${roomno}`);
                    if (counter == 1) {
                        const changebtn = `<button id="changebutton" onclick="openchangetable()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/change.gif') }}" alt=""> Change</button>`;
                        const bookingbutton = `<button id="bookingbutton" onclick="opentablebooking()" type="button" class="btn btn-outline-primary"><img style="height: 25px;mix-blend-mode: darken;" src="{{ asset('admin/icons/custom/table.gif') }}" alt=""> Table Booking</button>`;
                        $('#salebillbutton').after(changebtn);
                        $('#changebutton').after(viewbutton);
                        $('#viewbutton').after(bookingbutton);
                    }

                } else if (nature.toLowerCase() == 'room service') {
                    $('#roomnot').html(`<b>Room: </b>${roomno}`);
                }
                counter++;
                let m = kotdata.filter(x => x.roomno == roomno);
                let fx = sale1.find(x => x.roomno == roomno && x.status == 'Pending') ?? '';
                $('#vnofix').text(fx.vno);
                $('#roomnokot').text(`${roomno}`);
                let curtime = curtimesec();
                sessionmast.forEach(data => {
                    if (curtime > data.from_time && curtime < data.to_time) {
                        $('#sessionmast').html(`<b>Session: </b>${data.name}`);
                    }
                });
                let label = $('#label').val();
                if (m.length > 0) {
                    $('#waitername').html(`<b>Waiter: </b>${m[0].waitername ?? ''}`);
                    $('#roomnot').html(`<b>${label}: </b>${roomno}`);
                    $('#waitername').closest('div').addClass('br');
                    let c = firstkot.find(x => x.roomno == roomno);
                    $('#kottime').html(`<b>Time: </b>${c.vtime ?? ''}`);
                    $('#salebillbutton').prop('disabled', false);
                    $('#changebutton').prop('disabled', false);
                    $('#viewbutton').prop('disabled', false);
                    $('#bookingbutton').prop('disabled', false);
                } else {
                    $('#waitername').html('');
                    $('#kottime').html('');
                    $('#salebillbutton').prop('disabled', true);
                    $('#changebutton').prop('disabled', true);
                    $('#viewbutton').prop('disabled', true);
                    $('#bookingbutton').prop('disabled', true);
                }

                $('.menucover').removeClass('none');
                $('.menucover').addClass('block, box animate__animated animate__bounceInDown');
            });

            let docid = $('#docid').val();


            $(document).on('click', '#closediv', closeMenuCover);
            
            $(document).on('keydown', function(event) {
                if (event.key === "Escape" || event.keyCode === 27) {
                    closeMenuCover();
                }
            });

            function closeMenuCover() {
                $('.menucover').addClass('none');
                $('.menucover').removeClass('block box animate__animated animate__bounceInDown');
                $('#waitername').html('');
                $('#roomnot').html('');
                $('#kottime').html('');
                $('#salebillbutton').prop('disabled', true);
            }

            function openkot() {
                window.location.href = `koteentry?dcode=${dcode}&roomno=${roomno}`;
            }

            function submitForm() {
                $.ajax({
                    type: "POST",
                    url: $("#posdispform").attr("action"),
                    data: $("#posdispform").serialize() + "&_token={{ csrf_token() }}",
                    success: function(response) {
                        if (response == 'success') {
                            // pushNotify('success', 'Display Table', 'Pos Updated', 'fade', 300, '', '', true, true, true, 500, 20, 20, 'outline', 'right top');
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            let c = 0;
            $(document).on('input', '#occupiedColor, #vacantColor', function() {
                let occolor = $('#occupiedColor').val();
                let occdivs = $('.room-grid').find('div.occupied');
                occdivs.css('background-color', occolor);
                let chkdark = isDarkColor(occolor);
                if (chkdark === true) {
                    occdivs.find('div.room-number').css('color', 'white');
                } else {
                    occdivs.find('div.room-number').css('color', 'black');
                }
                let vccolor = $('#vacantColor').val();
                let vcdivs = $('.room-grid').find('div.vacant');
                vcdivs.css('background-color', vccolor);
                let chkdark2 = isDarkColor(vccolor);
                if (chkdark2 === true) {
                    vcdivs.find('div.room-number').css('color', 'white');
                } else {
                    vcdivs.find('div.room-number').css('color', 'black');
                }
                c++;
                if (c === 1) {
                    pushNotify('success', 'Display Table', 'Press Enter To Save', 'fade', 300, '', '', true, true, true, 2000, 20, 20, 'outline', 'right top');
                }
            });

            $(document).on('change', '#occupiedColor', function() {
                let occolor = $(this).val();
                let occdivs = $('.room-grid').find('div.occupied');
                occdivs.css('background-color', occolor);
                let chkdark = isDarkColor(occolor);
                if (chkdark === true) {
                    occdivs.find('div.room-number').css('color', 'white');
                } else {
                    occdivs.find('div.room-number').css('color', 'black');
                }
                setTimeout(() => {
                    submitForm();
                }, 2000);
            });

            $(document).on('input', '#vacantColor', function() {
                let vccolor = $(this).val();
                let vcdivs = $('.room-grid').find('div.vacant');
                vcdivs.css('background-color', vccolor);
                let chkdark = isDarkColor(vccolor);
                if (chkdark === true) {
                    vcdivs.find('div.room-number').css('color', 'white');
                } else {
                    vcdivs.find('div.room-number').css('color', 'black');
                }
                setTimeout(() => {
                    submitForm();
                }, 2000);
            });

            $(document).on('input', '#billedColor', function() {
                let blcolor = $(this).val();
                let bcdivs = $('.room-grid').find('div.billed');
                bcdivs.css('background-color', blcolor);
                let chkdark = isDarkColor(blcolor);
                if (chkdark === true) {
                    bcdivs.find('div.room-number').css('color', 'white');
                } else {
                    bcdivs.find('div.room-number').css('color', 'black');
                }
                setTimeout(() => {
                    submitForm();
                }, 2000);
            });

            $(document).on('input', '#bookedColor', function() {
                let bookcolor = $(this).val();
                let bkdivs = $('.room-grid').find('div.booked');
                bkdivs.css('background-color', bookcolor);
                let chkdark = isDarkColor(bookcolor);
                if (chkdark === true) {
                    bkdivs.find('div.room-number').css('color', 'white');
                } else {
                    bkdivs.find('div.room-number').css('color', 'black');
                }
                setTimeout(() => {
                    submitForm();
                }, 2000);
            });
        });
    </script>

    <script src="{{ asset('admin/js/posdisp.js') }}"></script>
@endsection
