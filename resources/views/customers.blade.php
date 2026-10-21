@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<h1>Customer Accounts</h1>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr></thead>
    <tbody>
        @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer['id'] }}</td>
            <td>{{ $customer['name'] }}</td>
            <td>{{ $customer['email'] }}</td>
            <td>{{ $customer['phone'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection