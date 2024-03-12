<?php

namespace App\Http\Controllers;

use App\Http\Requests\AulaStoreRequest;
use App\Http\Requests\AulaUpdateRequest;
use App\Models\Aula;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AulaController extends Controller
{
    public function index(Request $request): Response
    {
        $aulas = Aula::all();

        return view('aula.index', compact('aulas'));
    }

    public function create(Request $request): Response
    {
        return view('aula.create');
    }

    public function store(AulaStoreRequest $request): Response
    {
        $aula = Aula::create($request->validated());

        $request->session()->flash('aula.id', $aula->id);

        return redirect()->route('aula.index');
    }

    public function show(Request $request, Aula $aula): Response
    {
        return view('aula.show', compact('aula'));
    }

    public function edit(Request $request, Aula $aula): Response
    {
        return view('aula.edit', compact('aula'));
    }

    public function update(AulaUpdateRequest $request, Aula $aula): Response
    {
        $aula->update($request->validated());

        $request->session()->flash('aula.id', $aula->id);

        return redirect()->route('aula.index');
    }

    public function destroy(Request $request, Aula $aula): Response
    {
        $aula->delete();

        return redirect()->route('aula.index');
    }
}
