<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Video;
use App\Services\AdminActivityLogger;
use App\Services\EditLockService;
use App\Services\TvBroadcastService;
use App\Services\TvRevisionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct(
        protected TvRevisionService $tvRevisionService,
        protected TvBroadcastService $tvBroadcastService,
        protected AdminActivityLogger $activityLogger,
        protected EditLockService $editLockService,
    ) {}

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
        $videoTitle = $request->filled('title')
            ? $request->string('title')->toString()
            : pathinfo($request->file('video')->getClientOriginalName(), PATHINFO_FILENAME);

        if (Schema::hasTable('videos')) {
            $hasActiveVideo = Video::query()->where('is_active', true)->exists();

            $attributes = [
                'title' => Str::limit($videoTitle, 255, ''),
                'is_active' => ! $hasActiveVideo,
            ];

            if (Schema::hasColumn('videos', 'created_by')) {
                $attributes['created_by'] = $request->user()?->id;
            }

            if (Schema::hasColumn('videos', 'updated_by')) {
                $attributes['updated_by'] = $request->user()?->id;
            }

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
                $attributes['source_path'] = 'storage/'.ltrim($path, '/');
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

            $video = null;
        }

        $this->activityLogger->log('User menambah video', [
            'video_id' => $video?->id,
            'video_title' => $video?->title ?? $videoTitle,
            'video_path' => $path,
            'video_is_active' => $video?->is_active,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

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

        $video = Video::with(['locker', 'updater'])->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse($request, $video, 'Video')) {
            return $lockResponse;
        }

        if ($conflictResponse = $this->versionConflictResponse($request, $video, 'Video')) {
            return $conflictResponse;
        }

        $payload = [
            'title' => $request->string('title')->toString(),
        ];

        if (Schema::hasColumn('videos', 'updated_by')) {
            $payload['updated_by'] = $request->user()?->id;
        }

        $video->fill($payload);

        if (! $video->isDirty()) {
            if ($request->ajax()) {
                return $this->videoAjaxResponse('Tidak ada perubahan pada data video.');
            }

            return back()->with('info', 'Tidak ada perubahan pada data video.');
        }

        $video->save();
        $this->editLockService->release($video, $request->user(), true);

        $this->activityLogger->log('User mengubah data video', [
            'video_id' => $video->id,
            'video_title' => $video->title,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Nama video berhasil diperbarui.');
        }

        return back()->with('success', 'Nama video berhasil diperbarui.');
    }

    public function toggle(Request $request, int $id)
    {
        abort_unless(Schema::hasTable('videos'), 404);

        $video = Video::with('locker')->findOrFail($id);

        if ($lockResponse = $this->lockViolationResponse($request, $video, 'Video')) {
            return $lockResponse;
        }

        if ($video->is_active) {
            $payload = ['is_active' => false];

            if (Schema::hasColumn('videos', 'updated_by')) {
                $payload['updated_by'] = $request->user()?->id;
            }

            $video->update($payload);
            Setting::where('key', 'video')->delete();

            $this->activityLogger->log('User menonaktifkan video', [
                'video_id' => $video->id,
                'video_title' => $video->title,
            ]);
            $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

            if ($request->ajax()) {
                return $this->videoAjaxResponse('Video berhasil dinonaktifkan.');
            }

            return back()->with('success', 'Video berhasil dinonaktifkan.');
        }

        Video::query()->update(['is_active' => false]);
        $payload = ['is_active' => true];

        if (Schema::hasColumn('videos', 'updated_by')) {
            $payload['updated_by'] = $request->user()?->id;
        }

        $video->update($payload);
        Setting::updateOrCreate(['key' => 'video'], ['value' => $this->resolveVideoPath($video)]);

        $this->activityLogger->log('User mengaktifkan video', [
            'video_id' => $video->id,
            'video_title' => $video->title,
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

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

                if (Schema::hasColumn('videos', 'updated_by')) {
                    $updatePayload['updated_by'] = $request->user()?->id;
                }

                if ($updatePayload !== []) {
                    Video::query()
                        ->where('id', $videoId)
                        ->update($updatePayload);
                }
            }
        });

        $this->activityLogger->log('User mengubah urutan video', [
            'ordered_ids' => $validated['ordered_ids'],
        ]);
        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        return $this->videoAjaxResponse('Urutan video berhasil diperbarui.');
    }

    public function delete(Request $request, int $id)
    {
        if (Schema::hasTable('videos')) {
            $video = Video::with('locker')->findOrFail($id);

            if ($lockResponse = $this->lockViolationResponse($request, $video, 'Video')) {
                return $lockResponse;
            }

            $wasActive = $video->is_active;
            $videoPath = $this->resolveVideoPath($video);

            $this->activityLogger->log('User menghapus video', [
                'video_id' => $video->id,
                'video_title' => $video->title,
                'video_path' => $videoPath,
                'video_was_active' => $wasActive,
            ]);

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

            $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

            if ($request->ajax()) {
                return $this->videoAjaxResponse('Video berhasil dihapus.');
            }

            return back()->with('success', 'Video berhasil dihapus');
        }

        $videoPath = Setting::where('key', 'video')->value('value');

        if ($videoPath) {
            $this->activityLogger->log('User menghapus video legacy', [
                'video_path' => $videoPath,
            ]);
            Storage::disk('public')->delete($videoPath);
            Setting::where('key', 'video')->delete();
        }

        $this->tvBroadcastService->dispatchUpdate($this->tvRevisionService->current());

        if ($request->ajax()) {
            return $this->videoAjaxResponse('Video berhasil dihapus.');
        }

        return back()->with('success', 'Video berhasil dihapus');
    }

    public function lock(Request $request, int $id): JsonResponse
    {
        $video = Video::with(['locker', 'updater'])->findOrFail($id);
        $result = $this->editLockService->acquire($video, $request->user());

        if (! $result['acquired']) {
            return response()->json([
                'message' => $this->editLockService->lockMessage($video),
                'lock' => $result['lock'],
            ], 423);
        }

        return response()->json([
            'message' => 'Lock edit berhasil diambil untuk video ini.',
            'lock' => $result['lock'],
        ]);
    }

    public function unlock(Request $request, int $id): JsonResponse
    {
        $video = Video::findOrFail($id);
        $this->editLockService->release($video, $request->user());

        return response()->json(['released' => true]);
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
            ->with(['updater', 'locker'])
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
            ? $videos->count().' video tersimpan di sistem. Geser baris untuk mengatur urutan.'
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

        return number_format($value, $power === 0 ? 0 : 2).' '.$units[$power];
    }

    protected function lockViolationResponse(Request $request, Video $video, string $entityLabel)
    {
        $userId = $request->user()?->id;

        if (! $this->editLockService->isLockedByAnother($video, $userId)) {
            return null;
        }

        return response()->json([
            'message' => $this->editLockService->lockMessage($video),
            'lock' => $this->editLockService->payload($video, $userId),
        ], 423);
    }

    protected function versionConflictResponse(Request $request, Video $video, string $entityLabel)
    {
        $submittedVersion = trim((string) $request->input('updated_at_version'));
        $currentVersion = $video->updated_at?->toIso8601String() ?? '';

        if ($submittedVersion === '' || $submittedVersion === $currentVersion) {
            return null;
        }

        $updatedBy = $video->updater?->name ?: 'admin lain';
        $updatedAt = $video->updated_at
            ? Carbon::parse($video->updated_at)->locale('id')->translatedFormat('d F Y H:i')
            : 'waktu yang tidak diketahui';

        return response()->json([
            'message' => "{$entityLabel} ini sudah diubah oleh {$updatedBy} pada {$updatedAt}. Muat ulang data sebelum menyimpan lagi.",
            'lock' => $this->editLockService->payload($video, $request->user()?->id),
        ], 409);
    }
}
