<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->orderBy('created_at', 'desc');

        if (! $this->isSuperadmin($request)) {
            $query->where('disposition', $request->user()->disposition)
                ->where('role', '!=', 'superadmin');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validated($request);
        $validated['email'] = $validated['nip'].'@agenda.test';
        $validated['unit'] = $validated['disposition'];
        $validated['name'] = 'Admin '.$validated['nip'];

        if (! $this->isSuperadmin($request)) {
            $validated['disposition'] = $request->user()->disposition;
            $validated['unit'] = $request->user()->disposition;
            $validated['role'] = 'admin';
        }

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);

        return response()->json($user);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);
        $validated = $this->validated($request, $user);
        $validated['email'] = $validated['nip'].'@agenda.test';
        $validated['unit'] = $validated['disposition'];
        $validated['name'] = 'Admin '.$validated['nip'];

        if (! $this->isSuperadmin($request)) {
            $validated['disposition'] = $request->user()->disposition;
            $validated['unit'] = $request->user()->disposition;
            $validated['role'] = 'admin';
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->fresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeUser($request, $user);
        $user->delete();

        return response()->json(status: 204);
    }

    private function authorizeUser(Request $request, User $user): void
    {
        if (! $this->isSuperadmin($request) && ($user->disposition !== $request->user()->disposition || $user->role === 'superadmin')) {
            abort(403, 'Anda tidak punya akses ke pengguna ini.');
        }
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'nip' => ['required', 'digits:18', Rule::unique('users', 'nip')->ignore($user?->id)],
            'role' => ['required', 'in:superadmin,admin'],
            'disposition' => ['required', 'string', 'in:tu,data'],
            'password' => $passwordRules,
        ]);
    }

    private function isSuperadmin(Request $request): bool
    {
        return $request->user()?->role === 'superadmin';
    }
}
