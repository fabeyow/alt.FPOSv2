@extends('layouts.pos')
@section('title', $user->exists ? 'Edit User' : 'Create User')
@section('content')
<h1>{{ $user->exists ? 'Edit User' : 'Create User' }}</h1>
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" enctype="multipart/form-data" class="card account-form">
    @csrf
    @if ($user->exists)
        @method('PUT')
    @endif
    <label for="username">Username (required)</label>
    <input id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="50" autocomplete="username">
    <label for="full_name">Full name (required)</label>
    <input id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required maxlength="100" autocomplete="name">
    <label for="role">Role (required)</label>
    <input id="role" name="role" value="{{ old('role', $user->role ?? 'Staff') }}" required maxlength="20">
    @if ($user->exists)
        <img class="avatar" src="{{ $user->avatar ? Storage::disk('public')->url($user->avatar) : asset('images/avatar-placeholder.svg') }}" alt="Current profile picture" width="64" height="64">
        <label for="avatar">Profile picture</label>
        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/gif,image/webp,image/bmp" aria-describedby="avatar-help">
        <p id="avatar-help">Upload an image up to 2MB. It will be cropped to a 256 × 256 JPEG. Leave empty to keep the current picture.</p>
    @endif
    <div>
        <button class="btn" type="submit">Save User</button>
        <a class="btn" href="{{ route('users.index') }}">Cancel</a>
    </div>
</form>
@endsection
