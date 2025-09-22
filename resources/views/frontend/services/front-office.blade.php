@extends('frontend.layouts.main')
@section('title', 'Front Office Management - Analysis HMS | Hotel Management Software')

@section('meta')
<meta name="description" content="Front Office module in Analysis HMS streamlines guest check-in, check-out, reservations, billing, and room management for hotels.">
<meta name="keywords" content="Front Office Software, Hotel Front Desk, Check-in System, Hotel Reservation Software, Analysis HMS">
<meta name="author" content="Analysis Softwares Solutions">
@endsection

@section('main-container')
<style>
.hero-section {
    background: url('/images/front-office-hero.jpg') center/cover no-repeat;
    padding: 150px 0;
    color: #fff;
    text-align: center;
}
.hero-section h1 {
    font-family: 'Montserrat', sans-serif;
    font-size: 3rem;
    font-weight: 700;
}
.section-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 30px;
}
.card-feature {
    border: none;
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    transition: 0.3s;
}
.card-feature i {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 15px;
}
.card-feature:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.cta-section {
    background: linear-gradient(90deg, #007bff, #0056b3);
    padding: 60px 0;
    color: #fff;
    text-align: center;
}
.cta-section h2 {
    font-size: 2rem;
    margin-bottom: 20px;
}
.cta-section a {
    background: #fff;
    color: #007bff;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
}
.cta-section a:hover {
    background: #f1f1f1;
}
</style>

<!-- Hero -->
<div class="hero-section" data-aos="fade-down">
    <h1>Front Office Management</h1>
    <p>Seamless guest experiences from check-in to check-out</p>
</div>

<div class="container my-5">
    <!-- Overview -->
    <section data-aos="fade-up">
        <h2 class="section-title">Overview</h2>
        <p>The Front Office module of <strong>Analysis HMS</strong> provides a complete set of tools to manage your hotel’s reception efficiently. From reservations and guest check-ins to billing and reporting, everything is centralized and automated.</p>
    </section>

    <!-- Features -->
    <section class="mt-5" data-aos="fade-up">
        <h2 class="section-title">Key Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-feature">
                    <i class="fas fa-sign-in-alt"></i>
                    <h5>Quick Check-In / Check-Out</h5>
                    <p>Fast, seamless process for walk-in and reserved guests.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature">
                    <i class="fas fa-calendar-check"></i>
                    <h5>Reservations</h5>
                    <p>Manage bookings, cancellations, and availability in real time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature">
                    <i class="fas fa-bed"></i>
                    <h5>Room Management</h5>
                    <p>Track room status, housekeeping, and occupancy instantly.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature">
                    <i class="fas fa-file-invoice"></i>
                    <h5>Billing & Folio</h5>
                    <p>Integrated billing, re-settlement, and invoice generation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-feature">
                    <i class="fas fa-chart-line"></i>
                    <h5>Reports</h5>
                    <p>Check-in/out registers, occupancy, expected check-outs, and MIS.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="mt-5" data-aos="fade-up">
        <h2 class="section-title">Benefits</h2>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Improved guest satisfaction with fast processes</li>
            <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Centralized reservation & room management</li>
            <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Reduced errors in billing and reporting</li>
            <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Real-time access to occupancy and status</li>
        </ul>
    </section>
</div>

<!-- CTA -->
<div class="cta-section" data-aos="zoom-in">
    <h2>Transform Your Front Office Operations Today</h2>
    <a href="/contact">Request a Demo</a>
</div>
@endsection
