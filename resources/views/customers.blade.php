@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<h1>Customer Accounts</h1>
<a class="btn" href="{{ route('customers.create') }}">Create Customer</a>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->full_name }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->phone }}</td>
            <td><a class="btn" href="{{ route('customers.edit', $customer) }}" aria-label="Edit {{ $customer->full_name }}">Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
