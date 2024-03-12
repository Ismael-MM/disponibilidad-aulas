<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Aula;
use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AulaController
 */
final class AulaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $aulas = Aula::factory()->count(3)->create();

        $response = $this->get(route('aula.index'));

        $response->assertOk();
        $response->assertViewIs('aula.index');
        $response->assertViewHas('aulas');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('aula.create'));

        $response->assertOk();
        $response->assertViewIs('aula.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AulaController::class,
            'store',
            \App\Http\Requests\AulaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $nombre = $this->faker->word();
        $sede = Sede::factory()->create();

        $response = $this->post(route('aula.store'), [
            'nombre' => $nombre,
            'sede_id' => $sede->id,
        ]);

        $aulas = Aula::query()
            ->where('nombre', $nombre)
            ->where('sede_id', $sede->id)
            ->get();
        $this->assertCount(1, $aulas);
        $aula = $aulas->first();

        $response->assertRedirect(route('aula.index'));
        $response->assertSessionHas('aula.id', $aula->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $aula = Aula::factory()->create();

        $response = $this->get(route('aula.show', $aula));

        $response->assertOk();
        $response->assertViewIs('aula.show');
        $response->assertViewHas('aula');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $aula = Aula::factory()->create();

        $response = $this->get(route('aula.edit', $aula));

        $response->assertOk();
        $response->assertViewIs('aula.edit');
        $response->assertViewHas('aula');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AulaController::class,
            'update',
            \App\Http\Requests\AulaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $aula = Aula::factory()->create();
        $nombre = $this->faker->word();
        $sede = Sede::factory()->create();

        $response = $this->put(route('aula.update', $aula), [
            'nombre' => $nombre,
            'sede_id' => $sede->id,
        ]);

        $aula->refresh();

        $response->assertRedirect(route('aula.index'));
        $response->assertSessionHas('aula.id', $aula->id);

        $this->assertEquals($nombre, $aula->nombre);
        $this->assertEquals($sede->id, $aula->sede_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $aula = Aula::factory()->create();

        $response = $this->delete(route('aula.destroy', $aula));

        $response->assertRedirect(route('aula.index'));

        $this->assertModelMissing($aula);
    }
}
