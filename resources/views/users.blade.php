@extends('layouts.pos')
@section('title', 'Users')
@section('content')
<h1>User Accounts</h1>
<a class="btn" href="{{ route('users.create') }}">Create User</a>
<table>
    <thead><tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th><th>Avatar</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->role }}</td>
            <td><img class="avatar" src="{{ $user->avatar ? Storage::disk('public')->url($user->avatar) : asset('images/avatar-placeholder.svg') }}" alt="Profile picture for {{ $user->full_name }}" width="64" height="64"></td>
            <td><a class="btn" href="{{ route('users.edit', $user) }}" aria-label="Edit {{ $user->full_name }}">Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
