<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function application()
    {
        return view('frontend.application');
    }

    public function about()
    {
        return view('frontend.aboutus');
    }

    public function frontofficeservices()
    {
        return view('frontend.services.front-office');
    }

    public function pointofsaleservices()
    {
        return view('frontend.services.pointofsale');
    }

    public function banquetservices()
    {
        return view('frontend.services.banquet');
    }

    public function inventoryservices()
    {
        return view('frontend.services.inventory');
    }

    public function reservationservices()
    {
        return view('frontend.services.reservation');
    }

    public function contact() {
        return view('frontend.contactus');
    }

    // public function login()
    // {
    //     return view('frontend.login');
    // }

}
