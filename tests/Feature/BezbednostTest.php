<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class BezbednostTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function zaposleni_nema_pristup_finansijskim_izvestajima()
    {
        $zaposleni = User::factory()->create(['role' => 'zaposleni']);

        $response = $this->actingAs($zaposleni)->get(route('admin.finansije.create'));

        if ($response->status() === 302) $response->assertRedirect('/');
        else $response->assertStatus(403);
    }

    #[Test]
    public function obican_kupac_ne_moze_da_vidi_resurse()
    {
        $kupac = User::factory()->create(['role' => 'kupac']);

        $response = $this->actingAs($kupac)->get(route('resurs.index'));

        if ($response->status() === 302) $response->assertRedirect('/');
        else $response->assertStatus(403);
    }

    #[Test]
    public function neulogovan_korisnik_ne_moze_da_pristupi_skladistima()
    {
        $response = $this->get(route('skladiste.index'));

        $response->assertRedirect('/login');
    }
}
