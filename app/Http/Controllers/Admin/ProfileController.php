<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->baseQuery($request)->orderBy('display_order')->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $profile = Profile::create($this->validated($request));

        return response()->json($profile, 201);
    }

    public function show(Request $request, Profile $profile): JsonResponse
    {
        $this->authorizeItem($request, $profile->unit);

        return response()->json($profile);
    }

    public function update(Request $request, Profile $profile): JsonResponse
    {
        $this->authorizeItem($request, $profile->unit);
        $profile->update($this->validated($request));

        return response()->json($profile->fresh());
    }

    public function destroy(Request $request, Profile $profile): JsonResponse
    {
        $this->authorizeItem($request, $profile->unit);
        $profile->delete();

        return response()->json(status: 204);
    }

    private function baseQuery(Request $request)
    {
        $query = Profile::query();

        if (! $this->isSuperadmin($request)) {
            $query->where('unit', $request->user()->disposition);
        }

        return $query;
    }

    private function authorizeItem(Request $request, string $unit): void
    {
        if (! $this->isSuperadmin($request) && $unit !== $request->user()->disposition) {
            abort(403, 'Anda tidak punya akses ke data ini.');
        }
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'photo_file' => ['nullable', 'image', 'max:5120'],
            'unit' => ['nullable', 'string', 'in:tu,data'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $validated['unit'] = $this->isSuperadmin($request)
            ? ($validated['unit'] ?? $request->user()->disposition)
            : $request->user()->disposition;

        if ($request->hasFile('photo_file')) {
            $validated['photo_path'] = $this->storeUploadedFile($request->file('photo_file'), 'profiles');
        }

        unset($validated['photo_file']);

        return $validated;
    }

    private function storeUploadedFile(UploadedFile $file, string $directory): string
    {
        $targetDirectory = public_path('uploads/'.$directory);

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $filename = now()->format('YmdHis').'-'.Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($targetDirectory, $filename);

        return 'uploads/'.$directory.'/'.$filename;
    }

    private function isSuperadmin(Request $request): bool
    {
        return $request->user()?->role === 'superadmin';
    }
}
