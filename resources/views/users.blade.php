@extends('layouts.app')
@section('title', 'Users')
@section('content')
<h1>User Accounts</h1>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user['id'] }}</td>
            <td>{{ $user['name'] }}</td>
            <td>{{ $user['email'] }}</td>
            <td>{{ $user['role'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection