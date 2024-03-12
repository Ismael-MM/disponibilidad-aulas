<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SedeController
 */
final class SedeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $sedes = Sede::factory()->count(3)->create();

        $response = $this->get(route('sede.index'));

        $response->assertOk();
        $response->assertViewIs('sede.index');
        $response->assertViewHas('sedes');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('sede.create'));

        $response->assertOk();
        $response->assertViewIs('sede.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SedeController::class,
            'store',
            \App\Http\Requests\SedeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $nombre = $this->faker->word();

        $response = $this->post(route('sede.store'), [
            'nombre' => $nombre,
        ]);

        $sedes = Sede::query()
            ->where('nombre', $nombre)
            ->get();
        $this->assertCount(1, $sedes);
        $sede = $sedes->first();

        $response->assertRedirect(route('sede.index'));
        $response->assertSessionHas('sede.id', $sede->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $sede = Sede::factory()->create();

        $response = $this->get(route('sede.show', $sede));

        $response->assertOk();
        $response->assertViewIs('sede.show');
        $response->assertViewHas('sede');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $sede = Sede::factory()->create();

        $response = $this->get(route('sede.edit', $sede));

        $response->assertOk();
        $response->assertViewIs('sede.edit');
        $response->assertViewHas('sede');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SedeController::class,
            'update',
            \App\Http\Requests\SedeUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $sede = Sede::factory()->create();
        $nombre = $this->faker->word();

        $response = $this->put(route('sede.update', $sede), [
            'nombre' => $nombre,
        ]);

        $sede->refresh();

        $response->assertRedirect(route('sede.index'));
        $response->assertSessionHas('sede.id', $sede->id);

        $this->assertEquals($nombre, $sede->nombre);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $sede = Sede::factory()->create();

        $response = $this->delete(route('sede.destroy', $sede));

        $response->assertRedirect(route('sede.index'));

        $this->assertModelMissing($sede);
    }
}
