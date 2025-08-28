<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blank GRC</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        ul.blangrcul {
            list-style: decimal;
            margin-left: 12px;
            padding: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0%;
        }

        .mybox {
            border: 1px solid;
        }

        .form-control {
            max-height: 1.6rem;
            min-height: 1rem !important;
        }

        .form-group {
            margin-bottom: auto;
        }

        #end {
            text-align: center;
        }

        p {
            margin: 0;
            font-weight: 500;
        }

        label {
            margin: 0;
        }

        img {
            position: absolute;
        }

        .logoimg img {
            height: 10vh;
        }

        .sign {
            float: inline-end;
        }

        @media print {
            .col-md-4 {
                float: left;
                width: 33%;
            }

            .sign {
                float: inline-end;
            }

            img {
                position: absolute;
            }

            .logoimg img {
                height: 100px !important;
            }

            ul.blangrcul {
                list-style: decimal;
                margin-left: 12px;
                padding: 0;
            }


            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin: 0%;
            }

            .form-control {
                max-height: 1.6rem;
                min-height: 1rem !important;
            }

            .form-group {
                margin-bottom: auto;
            }

            #end {
                text-align: center;
            }

            p {
                margin: 0;
                font-weight: 500;
            }

            label {
                margin: 0;
            }
        }
    </style>
</head>

<div>
    <div class="container-fluid">
        <h5>Registration Card <span style="float: inline-end;">Reg. No.<span id="rid"></span></span></h5>
        <div class="logoimg">
            <img alt="analysishms" class="" id="complogo" src="">
        </div>
        <h5 id="compname" class="text-center"></h5>
        <div id="end">
            <p class="text-center">Address: <span id="address"></span></p>
            <p>E-mail: <span id="email"></span></p>
            <p>Mobile: <span id="phone"></span></p>
        </div>
        <h6 class="text-center">FIll By Guest</h6>
        <form>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name1">Name 1:</label>
                        <input type="text" class="form-control" id="name1" name="name1">
                    </div>
                    <div class="form-group">
                        <label for="name2">Name 2:</label>
                        <input type="text" class="form-control" id="name2" name="name2">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="designation">Designation:</label>
                        <input type="text" class="form-control" id="designation" name="designation">
                    </div>
                    <div class="form-group">
                        <label for="company">Company:</label>
                        <input type="text" class="form-control" id="company" name="company">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="names" name="names">
                    </div>
                    <div class="form-group">
                        <label for="numPersons">Number of Persons:</label>
                        <input type="number" class="form-control" id="numPersons" name="numPersons">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="form-group">
                        <label for="dueStay">Due of Stay:</label>
                        <input type="text" class="form-control" id="dueStay" name="dueStay">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" class="form-control" id="city" name="city">
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationality:</label>
                        <input type="text" class="form-control" id="nationality" name="nationality">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" class="form-control" id="age" name="age">
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile No:</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="text" class="form-control" id="dob" name="dob">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="anniversary">Anniversary:</label>
                        <input type="text" class="form-control" id="anniversary" name="anniversary">
                    </div>
                    <div class="form-group">
                        <label for="purpose">Purpose of Visit:</label>
                        <input type="text" class="form-control" id="purpose" name="purpose">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="modeoftravel">Mode of Travel:</label>
                        <input type="text" class="form-control" name="modeoftravel" id="modeoftravel">
                    </div>
                    <div class="form-group">
                        <label for="pan">PAN No:</label>
                        <input type="text" class="form-control" id="pan" name="pan">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="remark">Remark:</label>
                        <input type="text" class="form-control" name="remark" id="remark">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-form-label" for="paymentinstr">Payments Instruction</label>
                        <div class="row">
                            <div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" value="Cash" name="paymentinstr"
                                        id="cash">
                                    <label class="form-check-label" for="cash">Cash</label>
                                </div>
                            </div>
                            <div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" value="Credit Card" name="paymentinstr"
                                        id="creditcard">
                                    <label class="form-check-label" for="creditcard">Credit Card</label>
                                </div>
                            </div>
                            <div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" value="Bill To Company"
                                        name="paymentinstr" id="billtocompany">
                                    <label class="form-check-label" for="billtocompany">Bill To Company</label>
                                </div>
                            </div>
                            <div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" value="UPI"
                                        name="paymentinstr" id="upi">
                                    <label class="form-check-label" for="upi">UPI</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <p class="text-center">Passport and other Details</p>
        <div class="mybox">
            <div class="row">
                <div class="col-md-4">
                    <p>Arrival From :</p>
                    <p>Passport No. :</p>
                    <p>Registration No. :</p>
                </div>
                <div class="col-md-4">
                    <p>Proceeding To :</p>
                    <p>Date of issue :</p>
                    <p>Date of issue :</p>
                </div>
                <div class="col-md-4">
                    <p>Nationality :</p>
                    <p>Place :</p>
                    <p>Place :</p>
                </div>
            </div>
        </div>
        <p class="text-center">Fill By Front Desk</p>
        <div class="mybox">
            <div class="row">
                <div class="col-md-4">
                    <p>Chk In Date : <span id="ncur"></span></p>
                    <p>Room No. :</p>
                    <p>Plan/Package :</p>
                </div>
                <div class="col-md-4">
                    <p>Chk In Time : <span id="curtime"></span></p>
                    <p>Room Type :</p>
                    <p>Plan Amount :</p>
                </div>
                <div class="col-md-4">
                    <p>Chk Out Date :</p>
                </div>
            </div>
        </div>
        <h5 class="text-center text-dark">Terms & Conditions</h5>
        <ul class="blangrcul">
            <li><b>Guest:</b> Guest shall and include the person occupying the room any co-occupant or any visitor to
                the
                Hotel. </li>
            <li><b>Tarrif:</b> Trale on the registration card is only per night exclusive of taxes unless specified
                otherwise;
                Resident Guests should obtain the okay cardo from the Reception.</li>
            <li><b>Settlement Of The Bills:</b> Bills ust be settled on presentation, cheques are not accepted.</li>
            <li><b>Company line on vieitoris luggage and Belongings:</b> In the case of default payments of dues by the
                guest,
                the management shall be entitled a lien on the luggage and belongings and to detain after the date of
                departure without reference to the party and appropriate the net sale proceeds provides the amounts due
                by
                the guests.</li>
            <li><b>Departure:</b> Check out time <span id="checkouttime"></span>. If you wish avail late check out, the same can be extended up
                to
                18
                hours with 50% room charges, and beyond that 100% charges.</li>
            <li><b>Visitors Belongings:</b> Visitors are requested to lock the doors of their rooms when going out or
                when
                going to bed and not leave the key in the key whole. The company will not, in any way and any other
                property not interested to the management or for damage their of whether due to neglect of Hotel staff
                or
                any other calls over include ding theft or pilferage.</li>
            <li><b>Hazardous Goods:</b> A Storing of cinema films, Raw and Exposed any other articles of a combustible
                or
                hazardous nature in resident rooms or store is strictly prohibited.</li>
            <li><b>Damage to Property:</b> A Guest will be held responsible for any loss or damage to the hotel property
                caused by themselves, their friends or any person for whom they are responsible.</li>
            <li><b>Management Right:</b> The management reverse to the itself the absolute right of admission to any
                person
                in the hotel premises and to request any guest to vacate his or her room at whatsover and the guests
                shall
                be wound to vocate when requests to do so. In default management will be entitled to remove the luggage
                of
                the visitors from the room accepted by him/her.</li>
            <li><b>Relations between company and visitors:</b> Nothing herein above shall always be deemed to certitude
                any
                tenancy or sub tenancy or any right of tenancy or sub lenancy and any right throw of in fever of any
                guest
                or resident or visitors and the company shall always be deemed to be in full and capsulate position and
                controlled of the whole of hotel premises.</li>
            <li><b>Government Rules and regulation:</b> Guest are requested to observe the government rule and
                regulation in
                force time to time in the respect of registration alcoholic drinks firearть.</li>
            <li><b>Governing law:</b> Substantive laws of the republic of India.</li>
            <li><b>Arbitration:</b> In the event of any dispute arising out of or in connection with the subject matter
                of
                this agreement, parties/guests agree to refer such dispute to arbitration to be conducted international
                arbitration, India Arbitration proceeding shall be conduct before a soul arbitrator to
                mutually appointed by the parties, If parties are unable to agree on ass provided under the laic India
                Rules. Seats of such arbitration shall be in <span class="compcity"></span> and proceedings shall be conducted in English. The
                decision of the arbitrator shall be final and binding.</li>
            <li><b>Jurisdiction:</b> Exclusive jurisdiction of the Courts in <span class="compcity"></span>.</li>
            <li><b>Amendment Rules:</b> The management reserves to itself the right to add to or amend any of above
                terms,
                conditions and rules.</li>
        </ul>
        <div class="sign font-weight-bold">
            <p>Guest Signature</p>
        </div>
    </div>
</div>
</body>

</html>
