<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SkladisnaLokacijaControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function zaposleni_moze_kreirati_i_azurirati_skladisnu_lokaciju(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $skladiste = Skladiste::factory()->create();

        $this->actingAs($zaposleni)
            ->post(route('skladisne-lokacije.store', $skladiste), [
                'naziv' => 'Nova komora',
                'opis' => 'Komora za kontrolisano čuvanje lotova.',
                'aktivna' => true,
            ])
            ->assertRedirect(route('skladiste.show', $skladiste))
            ->assertSessionHas('success');

        $lokacija = SkladisnaLokacija::query()->sole();

        $this->actingAs($zaposleni)
            ->put(route('skladisne-lokacije.update', [$skladiste, $lokacija]), [
                'naziv' => 'Nova komora 1',
                'opis' => 'Komora privremeno van upotrebe.',
                'aktivna' => false,
            ])
            ->assertRedirect(route('skladiste.show', $skladiste))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skladisna_lokacija', [
            'id' => $lokacija->id,
            'skladiste_id' => $skladiste->id,
            'naziv' => 'Nova komora 1',
            'aktivna' => false,
        ]);
    }
}
