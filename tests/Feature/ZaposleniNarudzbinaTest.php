<?php

namespace Tests\Feature;

use App\Models\Narudzbina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ZaposleniNarudzbinaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_zaposleni_moze_da_oznaci_narudzbinu_kao_isporucenu()
    {
        $zaposleni = User::factory()->create(['role' => 'zaposleni']);

        $narudzbina = Narudzbina::create([
            'user_id' => User::factory()->create()->id,
            'datum_narudzbine' => now()->toDateString(),
            'status' => 'kreirana',
        ]);

        $response = $this->actingAs($zaposleni)
            ->patch(route('narudzbine.update', $narudzbina), [
                'status' => 'isporucena',
            ]);

        // provera novog statusa
        $this->assertDatabaseHas('narudzbinas', [
            'id' => $narudzbina->id,
            'status' => 'isporucena',
        ]);

        $response->assertRedirect();
    }
}
