<?php

namespace Tests\Feature;

use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Narudzbina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ZaposleniNarudzbinaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_zaposleni_moze_da_oznaci_narudzbinu_kao_otpremljenu()
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI->value]);

        $narudzbina = Narudzbina::create([
            'user_id' => User::factory()->create()->id,
            'status' => NarudzbinaStatus::POTVRDJENA,
            'adresa_isporuke' => 'Kneza Miloša 5, Beograd',
        ]);

        $response = $this->actingAs($zaposleni)
            ->patch(route('narudzbine.update', $narudzbina), [
                'status' => NarudzbinaStatus::OTPREMLJENA->value,
            ]);

        // provera novog statusa
        $this->assertDatabaseHas('narudzbinas', [
            'id' => $narudzbina->id,
            'status' => NarudzbinaStatus::OTPREMLJENA->value,
        ]);

        $response->assertRedirect();
    }
}
