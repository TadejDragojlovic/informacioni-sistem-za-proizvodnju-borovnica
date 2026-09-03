<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\Sorta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProizvodControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ne_moze_promeni_sortu_proizvoda_koji_je_vec_narucen(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $postojecaSorta = Sorta::factory()->create();
        $novaSorta = Sorta::factory()->create();
        $proizvod = Proizvod::factory()->create(['sorta_id' => $postojecaSorta->id]);
        NarudzbinaStavka::factory()->create(['proizvod_id' => $proizvod->id]);

        $this->actingAs($zaposleni)
            ->from(route('proizvod.edit', $proizvod))
            ->put(route('proizvod.update', $proizvod), [
                'naziv' => $proizvod->naziv,
                'opis' => $proizvod->opis,
                'sorta_id' => $novaSorta->id,
                'neto_kolicina_g' => $proizvod->neto_kolicina_g,
                'cena' => $proizvod->cena,
                'aktivan' => $proizvod->aktivan,
            ])
            ->assertRedirect(route('proizvod.edit', $proizvod))
            ->assertSessionHasErrors('sorta_id');

        $this->assertSame($postojecaSorta->id, $proizvod->fresh()->sorta_id);
    }
}
