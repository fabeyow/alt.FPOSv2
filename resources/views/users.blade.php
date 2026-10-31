@extends('layouts.app')
@section('title', 'Users')
@section('content')
<h1>User Accounts</h1>
<table>
    <thead><tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th></tr></thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->role }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
