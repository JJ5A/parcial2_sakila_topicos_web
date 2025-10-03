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
        $query = Film::with(['language', 'originalLanguage', 'categories'])
            ->whereHas('inventory', function($q) {
                // Solo películas que tienen inventario
                $q->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('rental')
                        ->whereRaw('rental.inventory_id = inventory.inventory_id')
                        ->whereNull('return_date');
                });
            });
        
        // Búsqueda por título
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'LIKE', "%{$searchTerm}%");
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
        
        // Filtro por rango de precio
        if ($request->filled('price_min')) {
            $query->where('rental_rate', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('rental_rate', '<=', $request->price_max);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'title');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['title', 'release_year', 'rental_rate', 'length', 'rating'])) {
            if ($sortBy === 'title') {
                $query->orderBy('title', $sortDirection);
            } elseif ($sortBy === 'release_year') {
                $query->orderBy('release_year', $sortDirection)->orderBy('title', 'asc');
            } elseif ($sortBy === 'rental_rate') {
                $query->orderBy('rental_rate', $sortDirection)->orderBy('title', 'asc');
            } elseif ($sortBy === 'length') {
                $query->orderBy('length', $sortDirection)->orderBy('title', 'asc');
            } elseif ($sortBy === 'rating') {
                $query->orderBy('rating', $sortDirection)->orderBy('title', 'asc');
            }
        } else {
            $query->orderBy('title', 'asc');
        }
        
        // Paginación con parámetros de búsqueda
        $films = $query->paginate(16)->appends($request->all());
        
        // Agregar información de disponibilidad a cada película
        $films->getCollection()->transform(function($film) {
            $totalCopies = Inventory::where('film_id', $film->film_id)->count();
            $rentedCopies = DB::table('rental')
                ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                ->where('inventory.film_id', $film->film_id)
                ->whereNull('rental.return_date')
                ->count();
            
            $film->available_copies = $totalCopies - $rentedCopies;
            $film->total_copies = $totalCopies;
            
            return $film;
        });
        
        // Datos para filtros
        $ratings = Film::getRatings();
        $categories = DB::table('category')->orderBy('name')->get();
        
        // Estadísticas
        $totalAvailable = $films->total();
        $totalFilms = Film::count();
        
        return view('films.available', compact('films', 'ratings', 'categories', 'totalAvailable', 'totalFilms'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Film::with(['language', 'originalLanguage']);
        
        // Búsqueda por título
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'LIKE', "%{$searchTerm}%");
        }
        
        // Filtro por rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        // Filtro por año
        if ($request->filled('year')) {
            $query->where('release_year', $request->year);
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'title');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['title', 'release_year', 'rental_rate', 'length', 'rating'])) {
            if ($sortBy === 'title') {
                $query->orderBy('title', $sortDirection);
            } elseif ($sortBy === 'release_year') {
                $query->orderBy('release_year', $sortDirection)->orderBy('title', 'asc');
            } elseif ($sortBy === 'rental_rate') {
                $query->orderBy('rental_rate', $sortDirection)->orderBy('title', 'asc');
            } elseif ($sortBy === 'length') {
                $query->orderBy('length', $sortDirection)->orderBy('title', 'asc');
            }
        } else {
            $query->orderBy('title', 'asc');
        }
        
        // Paginación con parámetros de búsqueda
        $films = $query->paginate(12)->appends($request->all());
        
        // Datos para filtros
        $ratings = Film::getRatings();
        $years = Film::selectRaw('DISTINCT release_year')
                    ->whereNotNull('release_year')
                    ->orderBy('release_year', 'desc')
                    ->pluck('release_year');
        
        // Estadísticas
        $totalFilms = Film::count();
        $filteredCount = $films->total();
        
        return view('films.index', compact('films', 'ratings', 'years', 'totalFilms', 'filteredCount'));
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

    /**
     * Search available films for AJAX requests
     */
    public function searchAvailable(Request $request)
    {
        if (!$request->has('q')) {
            return response()->json(['films' => []]);
        }

        $searchTerm = $request->q;
        
        $films = Film::with(['language'])
            ->whereHas('inventory', function($q) {
                // Solo películas que tienen inventario disponible
                $q->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('rental')
                        ->whereRaw('rental.inventory_id = inventory.inventory_id')
                        ->whereNull('return_date');
                });
            })
            ->where('title', 'LIKE', "%{$searchTerm}%")
            ->limit(10)
            ->get()
            ->map(function($film) {
                // Obtener inventario disponible por tienda
                $availableInventory = DB::table('inventory')
                    ->where('film_id', $film->film_id)
                    ->whereNotExists(function($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('rental')
                            ->whereRaw('rental.inventory_id = inventory.inventory_id')
                            ->whereNull('return_date');
                    })
                    ->get();

                $totalAvailable = $availableInventory->count();
                $storeAvailability = $availableInventory->groupBy('store_id');
                
                // Seleccionar el primer inventario disponible
                $firstAvailable = $availableInventory->first();
                
                return [
                    'inventory_id' => $firstAvailable ? $firstAvailable->inventory_id : null,
                    'film_id' => $film->film_id,
                    'title' => $film->title,
                    'rating' => $film->rating,
                    'rental_rate' => $film->rental_rate,
                    'rental_duration' => $film->rental_duration,
                    'available_copies' => $totalAvailable,
                    'store_id' => $firstAvailable ? $firstAvailable->store_id : null,
                    'language' => $film->language->name ?? 'N/A'
                ];
            })
            ->filter(function($film) {
                return $film['inventory_id'] !== null;
            });

        return response()->json(['films' => $films->values()]);
    }
}
