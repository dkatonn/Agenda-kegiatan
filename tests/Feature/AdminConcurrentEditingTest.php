<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConcurrentEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_update_rejects_stale_version(): void
    {
        $firstAdmin = User::factory()->create([
            'nip' => '100000000000000001',
        ]);
        $secondAdmin = User::factory()->create([
            'nip' => '100000000000000002',
        ]);

        $agenda = Agenda::query()->create([
            'date' => '2026-04-22',
            'time' => '08:00',
            'name' => 'Rapat awal',
            'location' => 'Ruang rapat',
            'disposition' => 'Sekretariat',
            'created_by' => $firstAdmin->id,
            'updated_by' => $firstAdmin->id,
        ]);

        $staleVersion = $agenda->updated_at?->toIso8601String();

        Carbon::setTestNow(now()->addMinute());
        $agenda->update([
            'name' => 'Rapat final',
            'updated_by' => $firstAdmin->id,
        ]);
        Carbon::setTestNow();

        $response = $this
            ->actingAs($secondAdmin)
            ->from(route('admin.agenda'))
            ->put(route('admin.agenda.update', $agenda->id), [
                'date' => '2026-04-22',
                'time' => '09:00',
                'name' => 'Rapat tertimpa',
                'location' => 'Ruang utama',
                'disposition' => 'Pimpinan',
                'updated_at_version' => $staleVersion,
            ]);

        $response
            ->assertRedirect(route('admin.agenda'))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'name' => 'Rapat final',
        ]);
    }

    public function test_agenda_lock_blocks_other_admin(): void
    {
        $firstAdmin = User::factory()->create([
            'nip' => '100000000000000003',
        ]);
        $secondAdmin = User::factory()->create([
            'nip' => '100000000000000004',
        ]);

        $agenda = Agenda::query()->create([
            'date' => '2026-04-22',
            'time' => '10:00',
            'name' => 'Koordinasi',
            'location' => 'Aula',
            'disposition' => 'TU',
            'created_by' => $firstAdmin->id,
            'updated_by' => $firstAdmin->id,
        ]);

        $this
            ->actingAs($firstAdmin)
            ->postJson(route('admin.agenda.lock', $agenda->id))
            ->assertOk()
            ->assertJsonPath('lock.is_mine', true);

        $this
            ->actingAs($secondAdmin)
            ->postJson(route('admin.agenda.lock', $agenda->id))
            ->assertStatus(423);
    }

    public function test_video_update_returns_conflict_for_stale_version(): void
    {
        $firstAdmin = User::factory()->create([
            'nip' => '100000000000000005',
        ]);
        $secondAdmin = User::factory()->create([
            'nip' => '100000000000000006',
        ]);

        $video = Video::query()->create([
            'title' => 'Profil 1',
            'file_path' => 'video/profil-1.mp4',
            'is_active' => true,
            'sort_order' => 1,
            'created_by' => $firstAdmin->id,
            'updated_by' => $firstAdmin->id,
        ]);

        $staleVersion = $video->updated_at?->toIso8601String();

        Carbon::setTestNow(now()->addMinute());
        $video->update([
            'title' => 'Profil 2',
            'updated_by' => $firstAdmin->id,
        ]);
        Carbon::setTestNow();

        $this
            ->actingAs($secondAdmin)
            ->putJson(route('admin.video.update', $video->id), [
                'title' => 'Profil 3',
                'updated_at_version' => $staleVersion,
            ])
            ->assertStatus(409)
            ->assertJsonFragment([
                'message' => "Video ini sudah diubah oleh {$firstAdmin->name} pada ".$video->updated_at->locale('id')->translatedFormat('d F Y H:i').'. Muat ulang data sebelum menyimpan lagi.',
            ]);
    }

    public function test_agenda_changes_are_written_to_activity_log(): void
    {
        $admin = User::factory()->create([
            'nip' => '100000000000000007',
        ]);

        $agenda = Agenda::query()->create([
            'date' => '2026-04-22',
            'time' => '13:00',
            'name' => 'Briefing',
            'location' => 'Studio',
            'disposition' => 'Tim TV',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this
            ->actingAs($admin)
            ->from(route('admin.agenda'))
            ->put(route('admin.agenda.update', $agenda->id), [
                'date' => '2026-04-22',
                'time' => '14:00',
                'name' => 'Briefing sore',
                'location' => 'Studio',
                'disposition' => 'Tim TV',
                'updated_at_version' => $agenda->updated_at?->toIso8601String(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'agenda',
            'event' => 'updated',
            'subject_type' => Agenda::class,
            'subject_id' => $agenda->id,
            'causer_type' => User::class,
            'causer_id' => $admin->id,
        ]);
    }

    public function test_employee_update_rejects_stale_version(): void
    {
        $firstAdmin = User::factory()->create([
            'nip' => '100000000000000008',
        ]);
        $secondAdmin = User::factory()->create([
            'nip' => '100000000000000009',
        ]);

        $employee = Employee::query()->create([
            'name' => 'Dewi',
            'nip' => '198901012010122001',
            'role' => 'Analis',
            'created_by' => $firstAdmin->id,
            'updated_by' => $firstAdmin->id,
        ]);

        $staleVersion = $employee->updated_at?->toIso8601String();

        Carbon::setTestNow(now()->addMinute());
        $employee->update([
            'role' => 'Kepala Analis',
            'updated_by' => $firstAdmin->id,
        ]);
        Carbon::setTestNow();

        $response = $this
            ->actingAs($secondAdmin)
            ->from(route('admin.employee'))
            ->put(route('admin.employee.update', $employee->id), [
                'name' => 'Dewi',
                'nip' => '198901012010122001',
                'role' => 'Staf',
                'updated_at_version' => $staleVersion,
            ]);

        $response
            ->assertRedirect(route('admin.employee'))
            ->assertSessionHasErrors();
    }

    public function test_admin_lock_blocks_other_admin(): void
    {
        $firstAdmin = User::factory()->create([
            'nip' => '100000000000000010',
        ]);
        $secondAdmin = User::factory()->create([
            'nip' => '100000000000000011',
        ]);
        $targetAdmin = User::factory()->create([
            'nip' => '100000000000000012',
            'created_by' => $firstAdmin->id,
            'updated_by' => $firstAdmin->id,
        ]);

        $this
            ->actingAs($firstAdmin)
            ->postJson(route('admin.user-settings.lock', $targetAdmin->id))
            ->assertOk()
            ->assertJsonPath('lock.is_mine', true);

        $this
            ->actingAs($secondAdmin)
            ->postJson(route('admin.user-settings.lock', $targetAdmin->id))
            ->assertStatus(423);
    }
}
