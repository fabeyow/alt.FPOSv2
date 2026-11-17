@extends('layouts.pos')
@section('title', $customer->exists ? 'Edit Customer' : 'Create Customer')
@section('content')
<h1>{{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}</h1>
<form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}" class="card account-form">
    @csrf
    @if ($customer->exists)
        @method('PUT')
    @endif
    <label for="full_name">Full name (required)</label>
    <input id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required maxlength="100" autocomplete="name">
    <label for="email">Email (required)</label>
    <input id="email" name="email" type="email" value="{{ old('email', $customer->email) }}" required maxlength="100" autocomplete="email">
    <label for="phone">Phone</label>
    <input id="phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" maxlength="20" autocomplete="tel">
    <div>
        <button class="btn" type="submit">Save Customer</button>
        <a class="btn" href="{{ route('customers.index') }}">Cancel</a>
    </div>
</form>
@endsection
