<?php

namespace App\Http\Controllers;

use App\Http\Requests\SedeStoreRequest;
use App\Http\Requests\SedeUpdateRequest;
use App\Models\Sede;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SedeController extends Controller
{
    public function index(Request $request): Response
    {
        $sedes = Sede::all();

        return view('sede.index', compact('sedes'));
    }

    public function create(Request $request): Response
    {
        return view('sede.create');
    }

    public function store(SedeStoreRequest $request): Response
    {
        $sede = Sede::create($request->validated());

        $request->session()->flash('sede.id', $sede->id);

        return redirect()->route('sede.index');
    }

    public function show(Request $request, Sede $sede): Response
    {
        return view('sede.show', compact('sede'));
    }

    public function edit(Request $request, Sede $sede): Response
    {
        return view('sede.edit', compact('sede'));
    }

    public function update(SedeUpdateRequest $request, Sede $sede): Response
    {
        $sede->update($request->validated());

        $request->session()->flash('sede.id', $sede->id);

        return redirect()->route('sede.index');
    }

    public function destroy(Request $request, Sede $sede): Response
    {
        $sede->delete();

        return redirect()->route('sede.index');
    }
}
