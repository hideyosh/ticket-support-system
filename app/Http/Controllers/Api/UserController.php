<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List semua user -- admin only (dicek via UserPolicy yang sudah ada).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return response()->json(
            User::with('role:id,role_name')
                ->select('id', 'name', 'email', 'role_id', 'team_id')
                ->get()
        );
    }

    /**
     * Profil user yang sedang login (lewat token).
     */
    public function showProfile(Request $request)
    {
        return response()->json($request->user()->load('role', 'team'));
    }

    /**
     * Update profil sendiri (nama, email, opsional ganti password).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['sometimes', 'confirmed', 'min:8'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->fresh());
    }
}
