<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and role filtering.
     */
    public function index(Request $request)
    {
        $query = User::query()->latest('id');

        // Search by name, email, or role
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by specific role
        if ($role = $request->input('role')) {
            $query->role($role);
        }

        $users = $query->paginate(15)->withQueryString();

        // Calculate summary counters for header cards
        $metrics = [
            'total'        => User::count(),
            'super_admins' => User::where('role', 'Super Admin')->count(),
            'admins'       => User::where('role', 'Admin')->count(),
            'sales'        => User::where('role', 'Sales')->count(),
            'accountants'  => User::where('role', 'Accountant')->count(),
            'employees'    => User::where('role', 'Employee')->count(),
        ];

        return view('users.index', compact('users', 'metrics'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = User::ROLES;
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'string', Rule::in(User::ROLES)],
        ]);

        $user = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'is_super_admin' => ($validated['role'] === 'Super Admin'),
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' with role '{$user->role}' created successfully!");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = User::ROLES;
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', 'string', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $userData = [
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'role'           => $validated['role'],
            'is_super_admin' => ($validated['role'] === 'Super Admin'),
        ];

        // Only update password if a new one was provided
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' updated successfully!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Safety: Prevent deleting currently authenticated user if applicable
        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own active account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }
}
