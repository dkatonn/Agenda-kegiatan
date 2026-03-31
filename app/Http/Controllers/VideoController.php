<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index()
    {
        $videos = $this->buildVideoCollection();

        return view('admin.video', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:512000'],
        ]);

        if (! $request->hasFile('video')) {
            return back()->with('error', 'File video tidak ditemukan.');
        }

        $path = $request->file('video')->store('video', 'public');

        if (Schema::hasTable('videos')) {
            $hasActiveVideo = Video::query()->where('is_active', true)->exists();
            $title = $request->filled('title')
                ? $request->string('title')->toString()
                : pathinfo($request->file('video')->getClientOriginalName(), PATHINFO_FILENAME);

            $attributes = [
                'title' => Str::limit($title, 255, ''),
                'is_active' => ! $hasActiveVideo,
            ];

            if ($this->hasSortOrderColumn()) {
                $attributes['sort_order'] = $this->nextSortOrder();
            }

            if ($this->hasDisplayOrderColumn()) {
                $attributes['display_order'] = $this->nextDisplayOrder();
            }

            if ($this->hasFilePathColumn()) {
                $attributes['file_path'] = $path;
            }

            if ($this->hasSourcePathColumn()) {
                $attributes['source_type'] = 'upload';
                $attributes['source_path'] = 'storage/' . ltrim($path, '/');
                $attributes['unit'] = $attributes['unit'] ?? 'data';
            }

            $video = Video::create($attributes);

            if ($video->is_active) {
                Setting::updateOrCreate(['key' => 'video'], ['value' => $this->resolveVideoPath($video)]);
            }
        } else {
            $oldVideoPath = Setting::where('key', 'video')->value('value');

            if ($oldVideoPath) {
                Storage::disk('public')->delete($oldVideoPath);
            }

            Setting::updateOrCreate(
                ['key' => 'video'],
                ['value' => $path]
            );
        }

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Video berhasil diupload');
        }

        return back()->with('success', 'Video berhasil diupload');
    }

    public function update(Request $request, int $id)
    {
        abort_unless(Schema::hasTable('videos'), 404);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $video = Video::findOrFail($id);
        $video->update([
            'title' => $request->string('title')->toString(),
        ]);

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Nama video berhasil diperbarui.');
        }

        return back()->with('success', 'Nama video berhasil diperbarui.');
    }

    public function toggle(Request $request, int $id)
    {
        abort_unless(Schema::hasTable('videos'), 404);

        $video = Video::findOrFail($id);

        if ($video->is_active) {
            $video->update(['is_active' => false]);
            Setting::where('key', 'video')->delete();

            if ($request->ajax()) {
                return $this->videoAjaxResponse('Video berhasil dinonaktifkan.');
            }

            return back()->with('success', 'Video berhasil dinonaktifkan.');
        }

        Video::query()->update(['is_active' => false]);
        $video->update(['is_active' => true]);
        Setting::updateOrCreate(['key' => 'video'], ['value' => $this->resolveVideoPath($video)]);

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Video berhasil diaktifkan.');
        }

        return back()->with('success', 'Video berhasil diaktifkan.');
    }

    public function reorder(Request $request)
    {
        abort_unless(Schema::hasTable('videos') && $this->hasSortOrderColumn(), 404);

        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:videos,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['ordered_ids'] as $index => $videoId) {
                $updatePayload = [];

                if ($this->hasSortOrderColumn()) {
                    $updatePayload['sort_order'] = $index + 1;
                }

                if ($this->hasDisplayOrderColumn()) {
                    $updatePayload['display_order'] = $index + 1;
                }

                if ($updatePayload !== []) {
                    Video::query()
                        ->where('id', $videoId)
                        ->update($updatePayload);
                }
            }
        });

        return $this->videoAjaxResponse('Urutan video berhasil diperbarui.');
    }

    public function delete(Request $request, int $id)
    {
        if (Schema::hasTable('videos')) {
            $video = Video::findOrFail($id);
            $wasActive = $video->is_active;
            $videoPath = $this->resolveVideoPath($video);

            $this->deleteVideoFile($videoPath);
            $video->delete();

            if ($wasActive) {
                $nextActiveVideo = $this->orderedVideosQuery()->first();

                if ($nextActiveVideo) {
                    Video::query()->update(['is_active' => false]);
                    $nextActiveVideo->update(['is_active' => true]);
                    Setting::updateOrCreate(['key' => 'video'], ['value' => $this->resolveVideoPath($nextActiveVideo)]);
                } else {
                    Setting::where('key', 'video')->delete();
                }
            }

            if ($request->ajax()) {
                return $this->videoAjaxResponse('Video berhasil dihapus.');
            }

            return back()->with('success', 'Video berhasil dihapus');
        }

        $videoPath = Setting::where('key', 'video')->value('value');

        if ($videoPath) {
            Storage::disk('public')->delete($videoPath);
            Setting::where('key', 'video')->delete();
        }

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Video berhasil dihapus.');
        }

        return back()->with('success', 'Video berhasil dihapus');
    }

    protected function syncLegacyVideoSetting(): void
    {
        $legacyVideoPath = Setting::where('key', 'video')->value('value');

        if (! $legacyVideoPath || Video::query()->exists()) {
            return;
        }

        Video::create([
            'title' => pathinfo($legacyVideoPath, PATHINFO_FILENAME),
            'is_active' => true,
            'sort_order' => $this->hasSortOrderColumn() ? 1 : 0,
            'display_order' => $this->hasDisplayOrderColumn() ? 1 : 0,
            'file_path' => $this->hasFilePathColumn() ? $legacyVideoPath : null,
            'source_type' => $this->hasSourcePathColumn() ? 'upload' : null,
            'source_path' => $this->hasSourcePathColumn() ? $legacyVideoPath : null,
            'unit' => $this->hasSourcePathColumn() ? 'data' : null,
        ]);
    }

    protected function hasFilePathColumn(): bool
    {
        return Schema::hasTable('videos') && Schema::hasColumn('videos', 'file_path');
    }

    protected function hasSourcePathColumn(): bool
    {
        return Schema::hasTable('videos') && Schema::hasColumn('videos', 'source_path');
    }

    protected function hasSortOrderColumn(): bool
    {
        return Schema::hasTable('videos') && Schema::hasColumn('videos', 'sort_order');
    }

    protected function hasDisplayOrderColumn(): bool
    {
        return Schema::hasTable('videos') && Schema::hasColumn('videos', 'display_order');
    }

    protected function nextSortOrder(): int
    {
        return ((int) Video::query()->max('sort_order')) + 1;
    }

    protected function nextDisplayOrder(): int
    {
        return ((int) Video::query()->max('display_order')) + 1;
    }

    protected function orderedVideosQuery()
    {
        $query = Video::query();

        if ($this->hasSortOrderColumn()) {
            $query->orderBy('sort_order');
        }

        if ($this->hasDisplayOrderColumn()) {
            $query->orderBy('display_order');
        }

        return $query->orderByDesc('id');
    }

    protected function buildVideoCollection()
    {
        if (! Schema::hasTable('videos')) {
            return collect();
        }

        $this->syncLegacyVideoSetting();

        return $this->orderedVideosQuery()
            ->get()
            ->map(function (Video $video) {
                $resolvedPath = $this->resolveVideoPath($video);

                $video->file_size_label = $this->formatBytes(
                    ($absolutePath = $this->resolveAbsoluteVideoPath($resolvedPath)) && is_file($absolutePath)
                        ? (int) filesize($absolutePath)
                        : 0
                );
                $video->resolved_video_url = $this->resolveVideoUrl($resolvedPath);

                return $video;
            });
    }

    protected function videoPanelMeta($videos): string
    {
        return $videos->count()
            ? $videos->count() . ' video tersimpan di sistem. Geser baris untuk mengatur urutan.'
            : 'Belum ada video yang diupload.';
    }

    protected function renderVideoRows($videos): View
    {
        return view('admin.partials.video-table-rows', compact('videos'));
    }

    protected function renderVideoEditModals($videos): View
    {
        return view('admin.partials.video-edit-modals', compact('videos'));
    }

    protected function videoAjaxResponse(string $message, int $status = 200)
    {
        $videos = $this->buildVideoCollection();

        return response()->json([
            'message' => $message,
            'panelMeta' => $this->videoPanelMeta($videos),
            'tableRowsHtml' => $this->renderVideoRows($videos)->render(),
            'editModalsHtml' => $this->renderVideoEditModals($videos)->render(),
        ], $status);
    }

    protected function resolveVideoPath(Video $video): string
    {
        $path = '';

        if ($this->hasFilePathColumn()) {
            $path = is_string($video->file_path) ? trim($video->file_path) : '';
        }

        if ($path === '' && $this->hasSourcePathColumn()) {
            $path = is_string($video->source_path) ? trim($video->source_path) : '';
        }

        return $path;
    }

    protected function resolveVideoUrl(string $path): ?string
    {
        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $publicPrefixedPath = preg_replace('#^storage/#', '', ltrim($path, '/'));
        if (is_string($publicPrefixedPath) && Storage::disk('public')->exists($publicPrefixedPath)) {
            return Storage::disk('public')->url($publicPrefixedPath);
        }

        return asset(ltrim($path, '/'));
    }

    protected function resolveAbsoluteVideoPath(string $path): ?string
    {
        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        $publicPrefixedPath = preg_replace('#^storage/#', '', ltrim($path, '/'));
        if (is_string($publicPrefixedPath) && Storage::disk('public')->exists($publicPrefixedPath)) {
            return Storage::disk('public')->path($publicPrefixedPath);
        }

        $absolutePublicPath = public_path(ltrim($path, '/'));

        return is_file($absolutePublicPath) ? $absolutePublicPath : null;
    }

    protected function deleteVideoFile(string $path): void
    {
        $path = trim($path);

        if ($path === '') {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return;
        }

        $publicPrefixedPath = preg_replace('#^storage/#', '', ltrim($path, '/'));
        if (is_string($publicPrefixedPath) && Storage::disk('public')->exists($publicPrefixedPath)) {
            Storage::disk('public')->delete($publicPrefixedPath);
            return;
        }

        $absolutePublicPath = public_path(ltrim($path, '/'));
        if (is_file($absolutePublicPath)) {
            @unlink($absolutePublicPath);
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}
