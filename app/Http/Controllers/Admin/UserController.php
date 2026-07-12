<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $roleIds = array_filter(explode(',', (string) $request->input('role_id')));

        $query = User::with('role')
            ->select('users.*')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', '!=', auth()->id());

        if (!empty($roleIds)) {
            $query->whereIn('users.role_id', $roleIds);
        }

        $users = $query->orderBy('roles.role_name', 'asc')
            ->orderBy('users.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }


    public function create()
    {
        $roles = Role::all();
        // $teams = Team::select('id', 'name')->get(); // If teams exist
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            $validated = Arr::except($validated, ['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
