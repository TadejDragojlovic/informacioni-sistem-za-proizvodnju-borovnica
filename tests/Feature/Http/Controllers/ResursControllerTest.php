<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Proizvod;
use App\Models\Resur;
use App\Models\Resurs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ResursController
 */
final class ResursControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $resurs = Resurs::factory()->count(3)->create();

        $response = $this->get(route('resurs.index'));

        $response->assertOk();
        $response->assertViewIs('resur.index');
        $response->assertViewHas('resurs', $resurs);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('resurs.create'));

        $response->assertOk();
        $response->assertViewIs('resur.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ResursController::class,
            'store',
            \App\Http\Requests\ResursStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $naziv = fake()->word();
        $kolicina = fake()->randomFloat(/** decimal_attributes **/);
        $trosak = fake()->randomFloat(/** decimal_attributes **/);
        $proizvod = Proizvod::factory()->create();

        $response = $this->post(route('resurs.store'), [
            'naziv' => $naziv,
            'kolicina' => $kolicina,
            'trosak' => $trosak,
            'proizvod_id' => $proizvod->id,
        ]);

        $resurs = Resur::query()
            ->where('naziv', $naziv)
            ->where('kolicina', $kolicina)
            ->where('trosak', $trosak)
            ->where('proizvod_id', $proizvod->id)
            ->get();
        $this->assertCount(1, $resurs);
        $resur = $resurs->first();

        $response->assertRedirect(route('resurs.index'));
        $response->assertSessionHas('resur.id', $resur->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $resur = Resurs::factory()->create();

        $response = $this->get(route('resurs.show', $resur));

        $response->assertOk();
        $response->assertViewIs('resur.show');
        $response->assertViewHas('resur', $resur);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $resur = Resurs::factory()->create();

        $response = $this->get(route('resurs.edit', $resur));

        $response->assertOk();
        $response->assertViewIs('resur.edit');
        $response->assertViewHas('resur', $resur);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ResursController::class,
            'update',
            \App\Http\Requests\ResursUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $resur = Resurs::factory()->create();
        $naziv = fake()->word();
        $kolicina = fake()->randomFloat(/** decimal_attributes **/);
        $trosak = fake()->randomFloat(/** decimal_attributes **/);
        $proizvod = Proizvod::factory()->create();

        $response = $this->put(route('resurs.update', $resur), [
            'naziv' => $naziv,
            'kolicina' => $kolicina,
            'trosak' => $trosak,
            'proizvod_id' => $proizvod->id,
        ]);

        $resur->refresh();

        $response->assertRedirect(route('resurs.index'));
        $response->assertSessionHas('resur.id', $resur->id);

        $this->assertEquals($naziv, $resur->naziv);
        $this->assertEquals($kolicina, $resur->kolicina);
        $this->assertEquals($trosak, $resur->trosak);
        $this->assertEquals($proizvod->id, $resur->proizvod_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $resur = Resurs::factory()->create();
        $resur = Resur::factory()->create();

        $response = $this->delete(route('resurs.destroy', $resur));

        $response->assertRedirect(route('resurs.index'));

        $this->assertModelMissing($resur);
    }
}
