<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:student,teacher',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User Account created successfully');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:student,teacher',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')){
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User Account updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()){
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User Account deleted successfully');
    }
}
