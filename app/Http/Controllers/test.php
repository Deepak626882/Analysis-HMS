<!-- after round yn  -->
 // if ($request->input('phoneno') != '') {
            //     $wpnum = $request->input('phoneno');
            //     $findexist = GuestProf::where('propertyid', $this->propertyid)->where('mobile_no', $request->input('phoneno'))->first();

            //     if ($findexist != null) {
            //         $guestprof = $findexist->guestcode;
            //     } else {
            //         $maxguestprof = GuestProf::where('propertyid', $this->propertyid)->max('guestcode');
            //         $guestprof = ($maxguestprof === null) ? $this->propertyid . '10001' : ($guestprof = $this->propertyid . substr($maxguestprof, $this->ptlngth) + 1);
            //     }

            //     $citycode = $request->input('customercity');
            //     $citydata = '';
            //     $statedata = '';
            //     $countrydata = '';
            //     if (!empty($citycode)) {
            //         $citydata = Cities::where('propertyid', $this->propertyid)->where('city_code', $citycode)->first();
            //         $statedata = DB::table('states')->where('propertyid', $this->propertyid)->where('state_code', $citydata->state)->first();
            //         $countrydata = DB::table('countries')->where('propertyid', $this->propertyid)->where('country_code', $citydata->country)->first();
            //     }

            //     $guestreward = [
            //         'propertyid' => $this->propertyid,
            //         'docid' => $docid,
            //         'custcode' => $guestprof,
            //         'vdate' => $this->ncurdate,
            //         'vtime' => date('H:i:s'),
            //         'restcode' => $restcode,
            //         'departname' => $depart->name,
            //         'billno' => $start_srl_no,
            //         'total' => $totalamt,
            //         'billamt' => $netamount,
            //         'rewardpoint' => 0.00,
            //         'redeempoint' => 0.00,
            //         'mobileno' => $request->input('phoneno'),
            //         'discamt' => $discamt ?? 0.00,
            //         'schemecode' => '',
            //         'saleupto' => 0.00,
            //         'rppointonamt' => 0.00,
            //         'rewardvalue' => 0.00,
            //         'reedemvalue' => 0.00,
            //         'regid' => '',
            //         'discper' => $discper ?? 0.00,
            //         'u_entdt' => $this->currenttime,
            //         'u_name' => Auth::user()->u_name,
            //         'u_ae' => 'a',
            //     ];

            //     $dob = $request->input('birthdate');
            //     $age = Carbon::parse($dob)->age;

            //     $guestproft = [
            //         'propertyid' => $this->propertyid,
            //         'docid' => $docid,
            //         'folio_no' => '0',
            //         'u_entdt' => $this->currenttime,
            //         'u_name' => Auth::user()->u_name,
            //         'u_ae' => 'a',
            //         'complimentry' => '',
            //         'guestcode' => $guestprof,
            //         'name' => $request->input('customername'),
            //         'state_code' => $citydata->state ?? '',
            //         'country_code' => $citydata->country ?? '',
            //         'add1' => $request->input('address') ?? '',
            //         'add2' => '',
            //         'city' => $citycode ?? '',
            //         'type' => $countrydata->Type ?? '',
            //         'mobile_no' => $request->input('phoneno'),
            //         'email_id' => '',
            //         'nationality' => $countrydata->nationality ?? '',
            //         'anniversary' => $request->input('anniversary') ?? null,
            //         'guest_status' => '',
            //         'comments1' => '',
            //         'comments2' => '',
            //         'comments3' => '',
            //         'city_name' => $citydata->cityname ?? '',
            //         'state_name' => $statedata->name ?? '',
            //         'country_name' => $countrydata->name ?? '',
            //         'gender' => '',
            //         'marital_status' => $request->input('anniversary') != '' ? 'Married' : 'Single',
            //         'zip_code' => $citydata->zipcode ?? '',
            //         'con_prefix' => '',
            //         'dob' => $dob ?? null,
            //         'age' => $age ?? '',
            //         'pic_path' => '',
            //         'id_proof' => '',
            //         'idproof_no' => '',
            //         'issuingcitycode' => '',
            //         'issuingcityname' => '',
            //         'issuingcountrycode' => '',
            //         'issuingcountryname' => '',
            //         'expiryDate' => null,
            //         'vipStatus' => '',
            //         'paymentMethod' => '',
            //         'billingAccount' => '',
            //         'idpic_path' => '',
            //         'm_prof' => $guestprof,
            //         'father_name' => '',
            //         'likes' => $request->input('like') ?? '',
            //         'dislikes' => $request->input('dislike') ?? '',
            //         'fom' => 0,
            //         'pos' => 1,
            //     ];
            //     GuestReward::insert($guestreward);
            //     GuestProf::insert($guestproft);
            // }

            // $chkroomserv = DB::table('depart')->where('propertyid', $this->propertyid)->where('dcode', $restcode)->first();
            // if (strtolower($chkroomserv->rest_type) == 'room service') {

            //     $paycode1 = 'ROOM' . $this->propertyid;
            //     $revdata1 = DB::table('revmast')->where('propertyid', $this->propertyid)->where('rev_code', $paycode1)->first();

            //     $paycharge1 = [
            //         'propertyid' => $this->propertyid,
            //         'docid' => $docid,
            //         'vno' => $start_srl_no,
            //         'vtype' => $vtype,
            //         'comp_code' => $request->input('company'),
            //         'sno' => 1,
            //         'sno1' => $roomdata->sno1,
            //         'msno1' => $msno1,
            //         'vdate' => $this->ncurdate,
            //         'vtime' => date('H:i:s'),
            //         'vprefix' => $vprefix,
            //         'paycode' => $paycode1,
            //         'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $start_srl_no,
            //         'paytype' => $revdata1->pay_type,
            //         'roomcat' => 'REST',
            //         'restcode' => $restcode,
            //         'roomno' => $roomno,
            //         'amtcr' => $netamount,
            //         'roomtype' => 'RO',
            //         'foliono' => 0,
            //         'billamount' => $netamount,
            //         'taxcondamt' => 0,
            //         'u_entdt' => $this->currenttime,
            //         'u_name' => Auth::user()->u_name,
            //         'u_ae' => 'a',
            //     ];

            //     $paycode2 = 'TOUT' . $this->propertyid;
            //     $paycharge2 = [
            //         'propertyid' => $this->propertyid,
            //         'docid' => $docid,
            //         'vno' => $start_srl_no,
            //         'vtype' => $vtype,
            //         'comp_code' => $request->input('company'),
            //         'sno' => 2,
            //         'sno1' => $roomdata->sno1 ?? '',
            //         'msno1' => $msno1,
            //         'vdate' => $this->ncurdate,
            //         'vtime' => date('H:i:s'),
            //         'vprefix' => $vprefix,
            //         'paycode' => $paycode2,
            //         'comments' => '(' . $depart->short_name . ')' . ' BILL NO.- ' . $start_srl_no,
            //         'paytype' => $revdata1->pay_type,
            //         'folionodocid' => $roomdata->docid ?? '',
            //         'restcode' => $restcode,
            //         'roomno' => $roomno,
            //         'roomcat' => $roommast->room_cat,
            //         'amtdr' => $netamount,
            //         'roomtype' => $roommast->type,
            //         'foliono' => $roomdata->folioNo ?? '',
            //         'guestprof' => $roomdata->guestprof ?? '',
            //         'billamount' => $netamount,
            //         'taxcondamt' => 0,
            //         'u_entdt' => $this->currenttime,
            //         'u_name' => Auth::user()->u_name,
            //         'u_ae' => 'a',
            //     ];
            //     DB::table($table5)->insert($paycharge1);
            //     DB::table($table5)->insert($paycharge2);
            // }
