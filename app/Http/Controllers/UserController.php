<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        return view('users', ['users' => User::orderBy('id')->get()]);
    }

    public function create()
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('users')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        $request->validate(['avatar' => ['nullable', 'image', 'max:2048']]);
        $oldAvatar = $user->avatar;
        $path = null;

        if ($request->hasFile('avatar')) {
            try {
                $image = ImageManager::gd()->read($request->file('avatar')->getPathname())
                    ->cover(256, 256)->toJpeg(quality: 85);
            } catch (DecoderException $exception) {
                throw ValidationException::withMessages(['avatar' => 'The avatar could not be read as an image.']);
            }

            $path = 'avatars/'.Str::uuid().'.jpg';
            abort_unless(Storage::disk('public')->put($path, (string) $image), 500, 'Unable to save avatar.');
            $data['avatar'] = $path;
        }

        try {
            $user->update($data);
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        if ($path && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('users')->with('status', 'User updated.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user)],
            'full_name' => ['required', 'string', 'max:100'],
            'role' => ['sometimes', 'required', 'string', 'max:20'],
        ]);
    }
}
