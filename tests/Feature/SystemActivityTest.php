<?php

namespace Tests\Feature;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class SystemActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create([
            'email' => 'admin@example.com',
        ]));
    }

    public function test_login_and_logout_are_recorded(): void
    {
        $user = User::factory()->create();

        Auth::login($user);
        Auth::logout();

        $this->assertDatabaseHas(ActivityLog::class, [
            'user_id' => $user->id,
            'event' => 'login',
        ]);
        $this->assertDatabaseHas(ActivityLog::class, [
            'user_id' => $user->id,
            'event' => 'logout',
        ]);
    }

    public function test_model_changes_are_recorded_without_passwords(): void
    {
        $branch = Branch::query()->create([
            'nama' => 'Cabang Lama',
            'alamat' => 'Alamat Cabang',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);
        $branch->update(['nama' => 'Cabang Baru']);
        Category::query()->create(['nama' => 'Suku Cadang']);
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->assertDatabaseHas(ActivityLog::class, [
            'event' => 'created',
            'subject_type' => Branch::class,
            'subject_id' => $branch->id,
        ]);
        $this->assertDatabaseHas(ActivityLog::class, [
            'event' => 'updated',
            'subject_type' => Branch::class,
            'subject_id' => $branch->id,
        ]);
        $this->assertDatabaseHas(ActivityLog::class, [
            'event' => 'created',
            'subject_type' => Category::class,
        ]);

        $userActivity = ActivityLog::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->firstOrFail();

        $this->assertArrayNotHasKey('password', $userActivity->properties);
    }

    public function test_activity_log_page_is_readable_in_the_admin_panel(): void
    {
        Livewire::test(ListActivityLogs::class)->assertOk();

        $this->get('/admin/activity-logs')->assertOk();
    }
}
