<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Payment;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * Mostrar dashboard de rentas
     */
    public function index(): View
    {
        // Estadísticas principales
        $stats = [
            'active_rentals' => Rental::active()->count(),
            'overdue_rentals' => Rental::overdue()->count(),
            'total_rentals_today' => Rental::whereDate('rental_date', today())->count(),
            'available_inventory' => Inventory::available()->count(),
            'total_customers' => Customer::where('active', true)->count(),
            'total_films' => Film::count(),
            'total_inventory' => Inventory::count(),
            'revenue_today' => Payment::whereDate('payment_date', today())->sum('amount'),
            'revenue_month' => Payment::whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('amount'),
        ];

        // Rentas recientes (últimas 10)
        $recent_rentals = Rental::with(['customer', 'inventory.film', 'staff'])
            ->whereHas('customer')
            ->whereHas('inventory.film')
            ->whereHas('staff')
            ->latest('rental_date')
            ->take(10)
            ->get();

        // Películas más rentadas (este mes)
        $popular_films = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->whereMonth('rental.rental_date', now()->month)
            ->whereYear('rental.rental_date', now()->year)
            ->select('film.title', 'film.film_id', DB::raw('COUNT(*) as rental_count'))
            ->groupBy('film.film_id', 'film.title')
            ->orderBy('rental_count', 'desc')
            ->limit(5)
            ->get();

        // Clientes más activos (este mes)
        $top_customers = DB::table('rental')
            ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
            ->whereMonth('rental.rental_date', now()->month)
            ->whereYear('rental.rental_date', now()->year)
            ->select('customer.first_name', 'customer.last_name', 'customer.customer_id', 
                    DB::raw('COUNT(*) as rental_count'))
            ->groupBy('customer.customer_id', 'customer.first_name', 'customer.last_name')
            ->orderBy('rental_count', 'desc')
            ->limit(5)
            ->get();

        // Inventario por película
        $inventory_status = DB::table('film')
            ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
            ->leftJoin('rental', function($join) {
                $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                     ->whereNull('rental.return_date');
            })
            ->select('film.title', 'film.film_id',
                    DB::raw('COUNT(inventory.inventory_id) as total_copies'),
                    DB::raw('COUNT(rental.rental_id) as rented_copies'),
                    DB::raw('COUNT(inventory.inventory_id) - COUNT(rental.rental_id) as available_copies'))
            ->groupBy('film.film_id', 'film.title')
            ->having('total_copies', '>', 0)
            ->orderBy('film.title')
            ->get();

        return view('rentals.index', compact('stats', 'recent_rentals', 'popular_films', 'top_customers', 'inventory_status'));
    }

    /**
     * Mostrar formulario para nueva renta
     */
    public function create(): View
    {
        $customers = Customer::where('active', true)->orderBy('last_name')->get();
        $staff = Staff::where('active', true)->orderBy('first_name')->get();
        
        // Obtener inventario disponible con películas
        $available_films = Inventory::with('film')
            ->available()
            ->get()
            ->groupBy('film_id')
            ->map(function($group) {
                $inventory = $group->first();
                return [
                    'film_id' => $inventory->film_id,
                    'title' => $inventory->film->title,
                    'rental_rate' => '$' . number_format($inventory->film->rental_rate, 2),
                    'rental_duration' => $inventory->film->rental_duration,
                    'available_copies' => $group->count(),
                    'inventory_items' => $group->pluck('inventory_id', 'store_id')
                ];
            })->values();
        
        return view('rentals.create', compact('customers', 'staff', 'available_films'));
    }

    /**
     * Buscar inventario disponible por película
     */
    public function searchInventory(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $inventory = Inventory::with('film')
            ->whereHas('film', function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%');
            })
            ->available()
            ->take(10)
            ->get();

        return response()->json($inventory->map(function($item) {
            return [
                'inventory_id' => $item->inventory_id,
                'title' => $item->film->title,
                'store_id' => $item->store_id,
                'rental_rate' => '$' . number_format($item->film->rental_rate, 2),
                'rental_duration' => $item->film->rental_duration
            ];
        }));
    }

    /**
     * Procesar nueva renta
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customer,customer_id',
            'inventory_id' => 'required|exists:inventory,inventory_id',
            'staff_id' => 'required|exists:staff,staff_id',
            'rental_date' => 'nullable|date',
            'payment_method' => 'nullable|string|in:cash,card,transfer',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Verificar que el inventario esté disponible
            $inventory = Inventory::with('film')->findOrFail($validated['inventory_id']);
            
            if (!$inventory->isAvailable()) {
                return back()->withErrors(['inventory_id' => 'Este ítem ya está rentado.']);
            }

            // Calcular monto final
            $baseAmount = $inventory->film->rental_rate;
            $discount = $validated['discount_amount'] ?? 0;
            $finalAmount = max(0, $baseAmount - $discount);

            // Crear la renta
            $rental = Rental::create([
                'rental_date' => $validated['rental_date'] ? Carbon::parse($validated['rental_date']) : now(),
                'inventory_id' => $validated['inventory_id'],
                'customer_id' => $validated['customer_id'],
                'staff_id' => $validated['staff_id']
            ]);

            // Crear el pago
            Payment::create([
                'customer_id' => $validated['customer_id'],
                'staff_id' => $validated['staff_id'],
                'rental_id' => $rental->rental_id,
                'amount' => $finalAmount,
                'payment_date' => $rental->rental_date
            ]);

            DB::commit();

            return redirect()->route('rentals.show', $rental->rental_id)
                           ->with('success', 'Renta procesada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al procesar la renta: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalles de una renta
     */
    public function show(Rental $rental): View
    {
        $rental->load(['customer.address', 'inventory.film', 'staff', 'payment']);
        
        return view('rentals.show', compact('rental'));
    }

    /**
     * Mostrar formulario de devolución
     */
    public function returnForm(Request $request): View
    {
        $activeRentals = Rental::with(['customer', 'inventory.film', 'staff'])
            ->active()
            ->orderBy('rental_date', 'desc')
            ->paginate(20);

        $staff = Staff::where('active', true)->get();
        $selectedRental = null;

        // Si se especifica un rental_id, destacarlo
        if ($request->filled('rental')) {
            $selectedRental = Rental::with(['customer', 'inventory.film', 'staff'])
                ->where('rental_id', $request->get('rental'))
                ->active()
                ->first();
        }

        // Si hay búsqueda
        if ($request->filled('search')) {
            $query = $request->get('search');
            $activeRentals = Rental::with(['customer', 'inventory.film', 'staff'])
                ->active()
                ->where(function($q) use ($query) {
                    $q->where('rental_id', 'LIKE', '%' . $query . '%')
                      ->orWhereHas('customer', function($subQ) use ($query) {
                          $subQ->where('first_name', 'LIKE', '%' . $query . '%')
                               ->orWhere('last_name', 'LIKE', '%' . $query . '%');
                      })
                      ->orWhereHas('inventory.film', function($subQ) use ($query) {
                          $subQ->where('title', 'LIKE', '%' . $query . '%');
                      });
                })
                ->orderBy('rental_date', 'desc')
                ->paginate(20);
        }

        return view('rentals.return', compact('activeRentals', 'staff', 'selectedRental'));
    }

    /**
     * Buscar renta para devolución
     */
    public function searchRental(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $rentals = Rental::with(['customer', 'inventory.film'])
            ->active()
            ->where(function($q) use ($query) {
                $q->where('rental_id', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('customer', function($subQ) use ($query) {
                      $subQ->where('first_name', 'LIKE', '%' . $query . '%')
                           ->orWhere('last_name', 'LIKE', '%' . $query . '%');
                  })
                  ->orWhereHas('inventory.film', function($subQ) use ($query) {
                      $subQ->where('title', 'LIKE', '%' . $query . '%');
                  });
            })
            ->take(10)
            ->get();

        return response()->json($rentals->map(function($rental) {
            return [
                'rental_id' => $rental->rental_id,
                'customer_name' => $rental->customer->full_name,
                'film_title' => $rental->inventory->film->title,
                'rental_date' => $rental->rental_date->format('d/m/Y H:i'),
                'due_date' => $rental->due_date->format('d/m/Y'),
                'is_overdue' => $rental->isOverdue(),
                'days_overdue' => $rental->daysOverdue()
            ];
        }));
    }

    /**
     * Procesar devolución rápida (AJAX)
     */
    public function quickReturn(Request $request)
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rental,rental_id'
        ]);

        DB::beginTransaction();

        try {
            $rental = Rental::with(['inventory.film', 'customer'])->findOrFail($validated['rental_id']);

            if (!$rental->isActive()) {
                return response()->json(['error' => 'Esta renta ya fue devuelta.'], 400);
            }

            // Actualizar fecha de devolución
            $rental->update(['return_date' => now()]);

            $overdueFee = 0;
            // Si está atrasada, calcular multa
            if ($rental->isOverdue()) {
                $overdueFee = $rental->daysOverdue() * 1.50; // $1.50 por día de atraso
                
                Payment::create([
                    'customer_id' => $rental->customer_id,
                    'staff_id' => 1, // Staff por defecto
                    'rental_id' => $rental->rental_id,
                    'amount' => $overdueFee,
                    'payment_date' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución procesada exitosamente.',
                'overdue_fee' => $overdueFee,
                'was_overdue' => $rental->isOverdue()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al procesar la devolución: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Procesar devolución
     */
    public function processReturn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rental,rental_id'
        ]);

        DB::beginTransaction();

        try {
            $rental = Rental::with(['inventory.film', 'customer'])->findOrFail($validated['rental_id']);

            if (!$rental->isActive()) {
                return back()->withErrors(['rental_id' => 'Esta renta ya fue devuelta.']);
            }

            // Actualizar fecha de devolución
            $rental->update(['return_date' => now()]);

            // Si está atrasada, calcular multa
            if ($rental->isOverdue()) {
                $overdueFee = $rental->daysOverdue() * 1.50; // $1.50 por día de atraso
                
                Payment::create([
                    'customer_id' => $rental->customer_id,
                    'staff_id' => auth()->user()->staff_id ?? 1, // Usar staff logueado o default
                    'rental_id' => $rental->rental_id,
                    'amount' => $overdueFee,
                    'payment_date' => now()
                ]);
            }

            DB::commit();

            $message = 'Devolución procesada exitosamente.';
            if ($rental->isOverdue()) {
                $message .= ' Se aplicó una multa por atraso.';
            }

            return redirect()->route('rentals.show', $rental->rental_id)
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al procesar la devolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Calcular detalles de devolución antes de procesar
     */
    public function calculateReturnDetails(Rental $rental)
    {
        if (!$rental->isActive()) {
            return response()->json(['error' => 'Esta renta ya fue devuelta'], 400);
        }

        $rental->load(['customer', 'inventory.film']);

        $isOverdue = $rental->isOverdue();
        $daysOverdue = $rental->daysOverdue();
        $overdueFee = $isOverdue ? $daysOverdue * 1.50 : 0;

        return response()->json([
            'rental_id' => $rental->rental_id,
            'customer_name' => $rental->customer->first_name . ' ' . $rental->customer->last_name,
            'film_title' => $rental->inventory->film->title,
            'rental_date' => $rental->rental_date->format('d/m/Y H:i'),
            'expected_return_date' => $rental->expected_return_date->format('d/m/Y'),
            'is_overdue' => $isOverdue,
            'days_overdue' => $daysOverdue,
            'overdue_fee' => $overdueFee,
            'store_id' => $rental->inventory->store_id,
            'rental_rate' => $rental->inventory->film->rental_rate,
            'total_amount' => $overdueFee
        ]);
    }

    /**
     * Obtener detalles de una renta para devolución (AJAX)
     */
    public function getReturnDetails(Rental $rental)
    {
        if (!$rental->isActive()) {
            return response()->json(['error' => 'Esta renta ya fue devuelta'], 400);
        }

        return response()->json([
            'rental_id' => $rental->rental_id,
            'customer_name' => $rental->customer->first_name . ' ' . $rental->customer->last_name,
            'film_title' => $rental->inventory->film->title,
            'rental_date' => $rental->rental_date->format('d/m/Y H:i'),
            'expected_return_date' => $rental->expected_return_date->format('d/m/Y'),
            'is_overdue' => $rental->isOverdue(),
            'days_overdue' => $rental->daysOverdue(),
            'store_id' => $rental->inventory->store_id,
            'rental_rate' => $rental->inventory->film->rental_rate
        ]);
    }

    /**
     * Listar rentas activas
     */
    public function active(): View
    {
        $rentals = Rental::with(['customer', 'inventory.film', 'staff'])
            ->whereHas('customer')
            ->whereHas('inventory.film')  
            ->whereHas('staff')
            ->active()
            ->orderBy('rental_date', 'desc')
            ->paginate(20);

        // Estadísticas de rentas activas
        $stats = [
            'total_active' => $rentals->total(),
            'overdue_count' => Rental::overdue()->count(),
            'due_today' => Rental::active()
                ->whereHas('inventory.film', function($q) {
                    $q->whereRaw('DATE_ADD(rental.rental_date, INTERVAL film.rental_duration DAY) = CURDATE()');
                })->count(),
            'due_tomorrow' => Rental::active()
                ->whereHas('inventory.film', function($q) {
                    $q->whereRaw('DATE_ADD(rental.rental_date, INTERVAL film.rental_duration DAY) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)');
                })->count(),
        ];

        return view('rentals.active', compact('rentals', 'stats'));
    }

    /**
     * Listar rentas atrasadas
     */
    public function overdue(): View
    {
        $rentals = Rental::with(['customer.address', 'inventory.film'])
            ->overdue()
            ->orderBy('rental_date')
            ->get();

        return view('rentals.overdue', compact('rentals'));
    }

    /**
     * Reporte de rentas
     */
    public function report(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $reportData = [
            'total_rentals' => Rental::whereBetween('rental_date', [$startDate, $endDate])->count(),
            'total_revenue' => Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'active_rentals' => Rental::active()->count(),
            'overdue_rentals' => Rental::overdue()->count(),
        ];

        $daily_rentals = Rental::selectRaw('DATE(rental_date) as date, COUNT(*) as count')
            ->whereBetween('rental_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('rentals.report', compact('reportData', 'daily_rentals', 'startDate', 'endDate'));
    }
}
