<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Resurs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResursControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function zaposleni_evidentira_i_azurira_resurs_vezan_za_lot(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lot = Lot::factory()->create(['oznaka' => 'BL-2026-099']);

        $this->actingAs($zaposleni)
            ->post(route('resurs.store'), [
                'lot_id' => $lot->id,
                'naziv' => 'Ambalažne kutije',
                'kolicina' => 10,
                'jedinica_mere' => 'kom',
                'cena_po_jedinici' => 150,
                'datum_upotrebe' => '2026-08-15',
            ])
            ->assertRedirect(route('resurs.index'))
            ->assertSessionHas('success');

        $resurs = Resurs::query()->sole();

        $this->actingAs($zaposleni)
            ->get(route('resurs.show', $resurs))
            ->assertOk()
            ->assertSee('BL-2026-099')
            ->assertSee('1.500,00 RSD');

        $this->actingAs($zaposleni)
            ->put(route('resurs.update', $resurs), [
                'lot_id' => $lot->id,
                'naziv' => 'Ambalažne kutije 500 g',
                'kolicina' => 12,
                'jedinica_mere' => 'kom',
                'cena_po_jedinici' => 160,
                'datum_upotrebe' => '2026-08-16',
            ])
            ->assertRedirect(route('resurs.show', $resurs))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('resurs', [
            'id' => $resurs->id,
            'lot_id' => $lot->id,
            'naziv' => 'Ambalažne kutije 500 g',
            'kolicina' => 12,
            'cena_po_jedinici' => 160,
            'evidentirao_user_id' => $zaposleni->id,
        ]);
    }
}
