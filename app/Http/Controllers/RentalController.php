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
        $stats = [
            'active_rentals' => Rental::active()->count(),
            'overdue_rentals' => Rental::overdue()->count(),
            'total_rentals_today' => Rental::whereDate('rental_date', today())->count(),
            'available_inventory' => Inventory::available()->count()
        ];

        $recent_rentals = Rental::with(['customer', 'inventory.film', 'staff'])
            ->whereHas('customer')
            ->whereHas('inventory.film')
            ->whereHas('staff')
            ->latest('rental_date')
            ->take(10)
            ->get();

        return view('rentals.index', compact('stats', 'recent_rentals'));
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
    public function returnForm(): View
    {
        return view('rentals.return');
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
     * Listar rentas activas
     */
    public function active(): View
    {
        $rentals = Rental::with(['customer', 'inventory.film', 'staff'])
            ->active()
            ->orderBy('rental_date', 'desc')
            ->simplePaginate(15);

        return view('rentals.active', compact('rentals'));
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
