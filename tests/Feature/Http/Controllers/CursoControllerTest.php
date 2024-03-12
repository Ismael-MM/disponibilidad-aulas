<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CursoController
 */
final class CursoControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $cursos = Curso::factory()->count(3)->create();

        $response = $this->get(route('curso.index'));

        $response->assertOk();
        $response->assertViewIs('curso.index');
        $response->assertViewHas('cursos');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('curso.create'));

        $response->assertOk();
        $response->assertViewIs('curso.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CursoController::class,
            'store',
            \App\Http\Requests\CursoStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $titulo = $this->faker->word();
        $turno = $this->faker->randomElement(/** enum_attributes **/);
        $horas = $this->faker->randomNumber();
        $horas_diarias = $this->faker->randomDigitNotNull();

        $response = $this->post(route('curso.store'), [
            'titulo' => $titulo,
            'turno' => $turno,
            'horas' => $horas,
            'horas_diarias' => $horas_diarias,
        ]);

        $cursos = Curso::query()
            ->where('titulo', $titulo)
            ->where('turno', $turno)
            ->where('horas', $horas)
            ->where('horas_diarias', $horas_diarias)
            ->get();
        $this->assertCount(1, $cursos);
        $curso = $cursos->first();

        $response->assertRedirect(route('curso.index'));
        $response->assertSessionHas('curso.id', $curso->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->get(route('curso.show', $curso));

        $response->assertOk();
        $response->assertViewIs('curso.show');
        $response->assertViewHas('curso');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->get(route('curso.edit', $curso));

        $response->assertOk();
        $response->assertViewIs('curso.edit');
        $response->assertViewHas('curso');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CursoController::class,
            'update',
            \App\Http\Requests\CursoUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $curso = Curso::factory()->create();
        $titulo = $this->faker->word();
        $turno = $this->faker->randomElement(/** enum_attributes **/);
        $horas = $this->faker->randomNumber();
        $horas_diarias = $this->faker->randomDigitNotNull();

        $response = $this->put(route('curso.update', $curso), [
            'titulo' => $titulo,
            'turno' => $turno,
            'horas' => $horas,
            'horas_diarias' => $horas_diarias,
        ]);

        $curso->refresh();

        $response->assertRedirect(route('curso.index'));
        $response->assertSessionHas('curso.id', $curso->id);

        $this->assertEquals($titulo, $curso->titulo);
        $this->assertEquals($turno, $curso->turno);
        $this->assertEquals($horas, $curso->horas);
        $this->assertEquals($horas_diarias, $curso->horas_diarias);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->delete(route('curso.destroy', $curso));

        $response->assertRedirect(route('curso.index'));

        $this->assertModelMissing($curso);
    }
}
