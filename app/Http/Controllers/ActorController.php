<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $actors = Actor::orderBy('last_name')->simplePaginate(15);
        return view('actors.index', compact('actors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('actors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
        ]);

        Actor::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return redirect()->route('actors.index')
                         ->with('success', 'Actor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Actor $actor): View
    {
        return view('actors.show', compact('actor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Actor $actor): View
    {
        return view('actors.edit', compact('actor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Actor $actor): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
        ]);

        $actor->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return redirect()->route('actors.index')
                         ->with('success', 'Actor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Actor $actor): RedirectResponse
    {
        $actor->delete();

        return redirect()->route('actors.index')
                         ->with('success', 'Actor eliminado exitosamente.');
    }
}
