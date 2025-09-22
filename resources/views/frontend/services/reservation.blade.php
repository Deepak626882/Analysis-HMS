@extends('frontend.layouts.main')
@section('title', 'Reservation Management - Analysis HMS')
@section('meta')
    <meta name="description" content="Analysis HMS Reservation Management software helps hotels handle bookings, cancellations, room lookups, and guest check-ins with ease.">
    <meta name="keywords" content="Hotel Reservation Software, Room Booking System, Guest Check-in, Reservation Management, Analysis HMS">
    <meta name="author" content="Analysis Softwares Solutions">
@endsection

@section('main-container')
<section class="service-hero text-white text-center d-flex align-items-center justify-content-center" 
         style="background: url('/images/reservation-hero.jpg') center/cover no-repeat; min-height: 400px;">
    <div data-aos="fade-up">
        <h1 class="display-4 fw-bold">Reservation Management</h1>
        <p class="lead">Smart Room Booking & Guest Reservation System for Hotels</p>
    </div>
</section>

<section class="py-5 container">
    <div class="row align-items-center">
        <div class="col-md-6" data-aos="fade-right">
            <img src="/images/reservation-dashboard.png" alt="Reservation Management Dashboard" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6" data-aos="fade-left">
            <h2 class="fw-bold mb-3">Seamless Guest Reservations</h2>
            <p>
                The <strong>Analysis HMS Reservation Management module</strong> makes it simple 
                to handle bookings, cancellations, and room allocations. Whether it’s a walk-in 
                guest or an online booking, our system keeps everything synchronized in real time.
            </p>
            <ul class="list-unstyled mt-3">
                <li class="mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i> Manage reservations & cancellations</li>
                <li class="mb-2"><i class="fas fa-bed text-success me-2"></i> Real-time room availability lookup</li>
                <li class="mb-2"><i class="fas fa-user-check text-warning me-2"></i> Streamlined guest check-in & check-out</li>
                <li class="mb-2"><i class="fas fa-credit-card text-danger me-2"></i> Advance payments & deposit tracking</li>
                <li><i class="fas fa-chart-line text-info me-2"></i> Reservation & occupancy analytics</li>
            </ul>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="fw-bold mb-4">Key Features of Reservation Module</h2>
        <p class="mb-5">
            Simplify your hotel’s reservation process with powerful tools to manage 
            bookings efficiently and deliver exceptional guest experiences.
        </p>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                    <h5 class="fw-bold">Advance Booking</h5>
                    <p>Guests can book rooms in advance with instant availability confirmation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="fas fa-door-open fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">Room Allocation</h5>
                    <p>Automatically assign rooms based on category, availability, and preferences.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="fas fa-file-invoice-dollar fa-2x text-danger mb-3"></i>
                    <h5 class="fw-bold">Integrated Billing</h5>
                    <p>Link reservation payments directly to billing & financial modules.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 container">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold">Reservation Management in Action</h2>
        <p>See how our software makes handling bookings and guest management effortless.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4" data-aos="zoom-in">
            <a data-fancybox="gallery" href="/images/reservation1.jpg">
                <img src="/images/reservation1-thumb.jpg" class="img-fluid rounded shadow" alt="Reservation Screenshot 1">
            </a>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
            <a data-fancybox="gallery" href="/images/reservation2.jpg">
                <img src="/images/reservation2-thumb.jpg" class="img-fluid rounded shadow" alt="Reservation Screenshot 2">
            </a>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="400">
            <a data-fancybox="gallery" href="/images/reservation3.jpg">
                <img src="/images/reservation3-thumb.jpg" class="img-fluid rounded shadow" alt="Reservation Screenshot 3">
            </a>
        </div>
    </div>
</section>
@endsection
