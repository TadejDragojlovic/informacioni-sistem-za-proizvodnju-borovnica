<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Narudzbina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function prijava_vodi_na_dashboard_sa_akcijama_prilagodjenim_ulozi(): void
    {
        $kupac = User::factory()->create(['role' => UserRole::KUPAC]);
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->post(route('login'), ['email' => $kupac->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pakovanja u korpi')
            ->assertSee('Moje narudžbine')
            ->assertDontSee('Finansije');

        $this->actingAs($zaposleni)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Narudžbine za obradu')
            ->assertDontSee('Finansije');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Finansije');
    }

    #[Test]
    public function nalog_povezan_sa_poslovnom_istorijom_ne_moze_biti_obrisan(): void
    {
        $kupac = User::factory()->create(['role' => UserRole::KUPAC]);
        Narudzbina::factory()->create(['user_id' => $kupac->id]);

        $this->actingAs($kupac)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('password', null, 'userDeletion');

        $this->assertAuthenticatedAs($kupac);
        $this->assertDatabaseHas('users', ['id' => $kupac->id]);
    }
}
