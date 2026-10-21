@extends('layouts.app')
@section('title', 'Home')
@section('content')
<section class="hero">
    <h1>Smarter Sales with alt.FPOS</h1>
    <p>A modern, lightweight Point-of-Sale system built with Laravel. Manage customers, track users, and streamline your retail operations.</p>
    <a class="btn" href="{{ route('customers') }}">👥 View Customers</a>
    <a class="btn" href="{{ route('users') }}">🔑 Manage Users</a>
</section>
<section class="cards">
    <div class="card"><h3>👥 Customer Management</h3><p>Organized contact records with names, emails, and phone numbers.</p></div>
    <div class="card"><h3>🔑 User & Role Control</h3><p>Manage staff accounts and assign Admin, Manager, or Cashier roles.</p></div>
    <div class="card"><h3>⚡ Built on Laravel</h3><p>MVC architecture with routing, controllers, and Blade templating.</p></div>
</section>
@endsection