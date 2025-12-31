<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Narudzbina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NarudzbinaController
 */
final class NarudzbinaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $narudzbinas = Narudzbina::factory()->count(3)->create();

        $response = $this->get(route('narudzbinas.index'));

        $response->assertOk();
        $response->assertViewIs('narudzbina.index');
        $response->assertViewHas('narudzbinas', $narudzbinas);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('narudzbinas.create'));

        $response->assertOk();
        $response->assertViewIs('narudzbina.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NarudzbinaController::class,
            'store',
            \App\Http\Requests\NarudzbinaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $datum_narudzbine = Carbon::parse(fake()->date());
        $ukupna_cena = fake()->randomFloat(/** decimal_attributes **/);
        $status = fake()->randomElement(/** enum_attributes **/);
        $user = User::factory()->create();

        $response = $this->post(route('narudzbinas.store'), [
            'datum_narudzbine' => $datum_narudzbine->toDateString(),
            'ukupna_cena' => $ukupna_cena,
            'status' => $status,
            'user_id' => $user->id,
        ]);

        $narudzbinas = Narudzbina::query()
            ->where('datum_narudzbine', $datum_narudzbine)
            ->where('ukupna_cena', $ukupna_cena)
            ->where('status', $status)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $narudzbinas);
        $narudzbina = $narudzbinas->first();

        $response->assertRedirect(route('narudzbinas.index'));
        $response->assertSessionHas('narudzbina.id', $narudzbina->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $narudzbina = Narudzbina::factory()->create();

        $response = $this->get(route('narudzbinas.show', $narudzbina));

        $response->assertOk();
        $response->assertViewIs('narudzbina.show');
        $response->assertViewHas('narudzbina', $narudzbina);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $narudzbina = Narudzbina::factory()->create();

        $response = $this->get(route('narudzbinas.edit', $narudzbina));

        $response->assertOk();
        $response->assertViewIs('narudzbina.edit');
        $response->assertViewHas('narudzbina', $narudzbina);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NarudzbinaController::class,
            'update',
            \App\Http\Requests\NarudzbinaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $narudzbina = Narudzbina::factory()->create();
        $datum_narudzbine = Carbon::parse(fake()->date());
        $ukupna_cena = fake()->randomFloat(/** decimal_attributes **/);
        $status = fake()->randomElement(/** enum_attributes **/);
        $user = User::factory()->create();

        $response = $this->put(route('narudzbinas.update', $narudzbina), [
            'datum_narudzbine' => $datum_narudzbine->toDateString(),
            'ukupna_cena' => $ukupna_cena,
            'status' => $status,
            'user_id' => $user->id,
        ]);

        $narudzbina->refresh();

        $response->assertRedirect(route('narudzbinas.index'));
        $response->assertSessionHas('narudzbina.id', $narudzbina->id);

        $this->assertEquals($datum_narudzbine, $narudzbina->datum_narudzbine);
        $this->assertEquals($ukupna_cena, $narudzbina->ukupna_cena);
        $this->assertEquals($status, $narudzbina->status);
        $this->assertEquals($user->id, $narudzbina->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $narudzbina = Narudzbina::factory()->create();

        $response = $this->delete(route('narudzbinas.destroy', $narudzbina));

        $response->assertRedirect(route('narudzbinas.index'));

        $this->assertModelMissing($narudzbina);
    }
}
