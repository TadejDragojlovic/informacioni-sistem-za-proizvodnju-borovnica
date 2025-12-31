<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Skladiste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SkladisteController
 */
final class SkladisteControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $skladistes = Skladiste::factory()->count(3)->create();

        $response = $this->get(route('skladistes.index'));

        $response->assertOk();
        $response->assertViewIs('skladiste.index');
        $response->assertViewHas('skladistes', $skladistes);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('skladistes.create'));

        $response->assertOk();
        $response->assertViewIs('skladiste.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SkladisteController::class,
            'store',
            \App\Http\Requests\SkladisteStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $lokacija = fake()->word();
        $kapacitet = fake()->numberBetween(-10000, 10000);
        $temperatura = fake()->randomFloat(/** decimal_attributes **/);
        $trosak = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->post(route('skladistes.store'), [
            'lokacija' => $lokacija,
            'kapacitet' => $kapacitet,
            'temperatura' => $temperatura,
            'trosak' => $trosak,
        ]);

        $skladistes = Skladiste::query()
            ->where('lokacija', $lokacija)
            ->where('kapacitet', $kapacitet)
            ->where('temperatura', $temperatura)
            ->where('trosak', $trosak)
            ->get();
        $this->assertCount(1, $skladistes);
        $skladiste = $skladistes->first();

        $response->assertRedirect(route('skladistes.index'));
        $response->assertSessionHas('skladiste.id', $skladiste->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $skladiste = Skladiste::factory()->create();

        $response = $this->get(route('skladistes.show', $skladiste));

        $response->assertOk();
        $response->assertViewIs('skladiste.show');
        $response->assertViewHas('skladiste', $skladiste);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $skladiste = Skladiste::factory()->create();

        $response = $this->get(route('skladistes.edit', $skladiste));

        $response->assertOk();
        $response->assertViewIs('skladiste.edit');
        $response->assertViewHas('skladiste', $skladiste);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SkladisteController::class,
            'update',
            \App\Http\Requests\SkladisteUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $skladiste = Skladiste::factory()->create();
        $lokacija = fake()->word();
        $kapacitet = fake()->numberBetween(-10000, 10000);
        $temperatura = fake()->randomFloat(/** decimal_attributes **/);
        $trosak = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->put(route('skladistes.update', $skladiste), [
            'lokacija' => $lokacija,
            'kapacitet' => $kapacitet,
            'temperatura' => $temperatura,
            'trosak' => $trosak,
        ]);

        $skladiste->refresh();

        $response->assertRedirect(route('skladistes.index'));
        $response->assertSessionHas('skladiste.id', $skladiste->id);

        $this->assertEquals($lokacija, $skladiste->lokacija);
        $this->assertEquals($kapacitet, $skladiste->kapacitet);
        $this->assertEquals($temperatura, $skladiste->temperatura);
        $this->assertEquals($trosak, $skladiste->trosak);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $skladiste = Skladiste::factory()->create();

        $response = $this->delete(route('skladistes.destroy', $skladiste));

        $response->assertRedirect(route('skladistes.index'));

        $this->assertModelMissing($skladiste);
    }
}
