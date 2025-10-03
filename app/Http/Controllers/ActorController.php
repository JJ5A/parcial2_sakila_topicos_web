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
    public function index(Request $request): View
    {
        $query = Actor::query();
        
        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        // Filtro por letra inicial del apellido
        if ($request->filled('letter')) {
            $query->where('last_name', 'LIKE', $request->letter . '%');
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'last_name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['first_name', 'last_name', 'actor_id', 'last_update'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('last_name', 'asc');
        }
        
        // Paginación con parámetros de búsqueda
        $actors = $query->paginate(15)->appends($request->all());
        
        // Obtener letras del alfabeto para filtro rápido
        $letters = Actor::selectRaw('UPPER(LEFT(last_name, 1)) as letter')
                        ->groupBy('letter')
                        ->orderBy('letter')
                        ->pluck('letter')
                        ->toArray();
        
        // Estadísticas
        $totalActors = Actor::count();
        $filteredCount = $actors->total();
        
        return view('actors.index', compact('actors', 'letters', 'totalActors', 'filteredCount'));
    }

    /**
     * Highlight search terms in text
     */
    private function highlightSearch($text, $search)
    {
        if (!$search) return $text;
        
        return preg_replace('/(' . preg_quote($search, '/') . ')/i', '<span class="search-highlight">$1</span>', $text);
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
