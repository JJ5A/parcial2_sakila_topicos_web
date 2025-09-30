<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Language;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    /**
     * Display available films for rental
     */
    public function available(Request $request): View
    {
        $query = Film::with(['language', 'originalLanguage']);
        
        // Búsqueda por título
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }
        
        // Obtener todas las películas
        $allFilms = $query->orderBy('title')->get();
        
        // Filtrar solo las que tienen inventario disponible
        $films = $allFilms->map(function($film) {
            $totalCopies = Inventory::where('film_id', $film->film_id)->count();
            $rentedCopies = DB::table('rental')
                ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                ->where('inventory.film_id', $film->film_id)
                ->whereNull('rental.return_date')
                ->count();
            
            $availableCopies = $totalCopies - $rentedCopies;
            
            $film->available_copies = $availableCopies;
            $film->total_copies = $totalCopies;
            
            return $film;
        })->filter(function($film) {
            return $film->total_copies > 0 && $film->available_copies > 0;
        });
        
        // Datos para filtros
        $ratings = Film::getRatings();
        $categories = DB::table('category')->orderBy('name')->get();
        
        return view('films.available', compact('films', 'ratings', 'categories'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Film::with(['language', 'originalLanguage']);
        
        // Búsqueda por título
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        // Filtro por año
        if ($request->filled('year')) {
            $query->where('release_year', $request->year);
        }
        
        $films = $query->orderBy('title')->simplePaginate(12);
        
        // Datos para filtros
        $ratings = Film::getRatings();
        $years = Film::selectRaw('DISTINCT release_year')
                    ->whereNotNull('release_year')
                    ->orderBy('release_year', 'desc')
                    ->pluck('release_year');
        
        return view('films.index', compact('films', 'ratings', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = Language::orderBy('name')->get();
        $ratings = Film::getRatings();
        $specialFeatures = Film::getSpecialFeatures();
        
        return view('films.create', compact('languages', 'ratings', 'specialFeatures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:128',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1888|max:' . (date('Y') + 5),
            'language_id' => 'required|exists:language,language_id',
            'original_language_id' => 'nullable|exists:language,language_id',
            'rental_duration' => 'required|integer|min:1|max:255',
            'rental_rate' => 'required|numeric|min:0|max:99.99',
            'length' => 'nullable|integer|min:1|max:65535',
            'replacement_cost' => 'required|numeric|min:0|max:999.99',
            'rating' => 'nullable|in:' . implode(',', array_keys(Film::getRatings())),
            'special_features' => 'nullable|array',
            'special_features.*' => 'in:' . implode(',', array_keys(Film::getSpecialFeatures())),
        ]);

        // Procesar special_features como SET de MySQL
        if (isset($validated['special_features'])) {
            $validated['special_features'] = implode(',', $validated['special_features']);
        }

        Film::create($validated);

        return redirect()->route('films.index')
                         ->with('success', 'Película creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film): View
    {
        $film->load(['language', 'originalLanguage']);
        return view('films.show', compact('film'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film): View
    {
        $languages = Language::orderBy('name')->get();
        $ratings = Film::getRatings();
        $specialFeatures = Film::getSpecialFeatures();
        
        // Convertir special_features de SET a array
        $selectedFeatures = $film->special_features ? explode(',', $film->special_features) : [];
        
        return view('films.edit', compact('film', 'languages', 'ratings', 'specialFeatures', 'selectedFeatures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Film $film): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:128',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1888|max:' . (date('Y') + 5),
            'language_id' => 'required|exists:language,language_id',
            'original_language_id' => 'nullable|exists:language,language_id',
            'rental_duration' => 'required|integer|min:1|max:255',
            'rental_rate' => 'required|numeric|min:0|max:99.99',
            'length' => 'nullable|integer|min:1|max:65535',
            'replacement_cost' => 'required|numeric|min:0|max:999.99',
            'rating' => 'nullable|in:' . implode(',', array_keys(Film::getRatings())),
            'special_features' => 'nullable|array',
            'special_features.*' => 'in:' . implode(',', array_keys(Film::getSpecialFeatures())),
        ]);

        // Procesar special_features como SET de MySQL
        if (isset($validated['special_features'])) {
            $validated['special_features'] = implode(',', $validated['special_features']);
        } else {
            $validated['special_features'] = null;
        }

        $film->update($validated);

        return redirect()->route('films.index')
                         ->with('success', 'Película actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film): RedirectResponse
    {
        $film->delete();

        return redirect()->route('films.index')
                         ->with('success', 'Película eliminada exitosamente.');
    }
}
