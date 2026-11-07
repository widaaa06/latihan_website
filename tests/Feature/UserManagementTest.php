<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

    public function test_admin_can_create_a_user(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Pengguna Baru',
                'email' => 'baru@example.com',
                'password' => 'secure-password',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
        ]);

        $user = User::query()->where('email', 'baru@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_authenticated_user_with_another_email_domain_can_access_the_admin_panel(): void
    {
        $this->get('/admin/branches/create')->assertOk();
    }

    public function test_production_panel_access_requires_the_company_email_domain(): void
    {
        config(['app.env' => 'production']);

        $panel = Filament::getCurrentPanel();

        $this->assertTrue((new User(['email' => 'staff@tokobajuserbaada.com']))->canAccessPanel($panel));
        $this->assertFalse((new User(['email' => 'staff@example.com']))->canAccessPanel($panel));
    }

    public function test_editing_a_user_without_a_password_keeps_the_existing_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'staff@example.com',
            'password' => 'existing-password',
        ]);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => 'Nama Baru',
                'email' => $user->email,
                'password' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertTrue(Hash::check('existing-password', $user->password));
    }
}
