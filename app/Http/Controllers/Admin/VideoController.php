<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->baseQuery($request)->orderBy('display_order')->latest()->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $video = Video::create($this->validated($request));

        return response()->json($video, 201);
    }

    public function show(Request $request, Video $video): JsonResponse
    {
        $this->authorizeItem($request, $video->unit);

        return response()->json($video);
    }

    public function update(Request $request, Video $video): JsonResponse
    {
        $this->authorizeItem($request, $video->unit);
        $video->update($this->validated($request));

        return response()->json($video->fresh());
    }

    public function destroy(Request $request, Video $video): JsonResponse
    {
        $this->authorizeItem($request, $video->unit);
        $video->delete();

        return response()->json(status: 204);
    }

    private function baseQuery(Request $request)
    {
        $query = Video::query();

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
            'title' => ['required', 'string', 'max:255'],
            'source_type' => ['required', 'in:url,upload'],
            'source_path' => ['nullable', 'string', 'max:2048', 'required_if:source_type,url'],
            'source_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm', 'max:102400'],
            'unit' => ['nullable', 'string', 'in:tu,data'],
            'is_active' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        if (($validated['source_type'] ?? null) === 'upload' && ! $request->hasFile('source_file') && empty($validated['source_path'])) {
            throw ValidationException::withMessages([
                'source_file' => 'Silakan pilih file video dari komputer Anda.',
            ]);
        }

        $validated['unit'] = $this->isSuperadmin($request)
            ? ($validated['unit'] ?? $request->user()->disposition)
            : $request->user()->disposition;

        if (($validated['source_type'] ?? null) === 'upload' && $request->hasFile('source_file')) {
            $validated['source_path'] = $this->storeUploadedFile($request->file('source_file'), 'videos');
        }

        unset($validated['source_file']);

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
