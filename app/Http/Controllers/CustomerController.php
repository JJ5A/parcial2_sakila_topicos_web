<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Mostrar lista de clientes
     */
    public function index(Request $request): View
    {
        $query = Customer::with(['address.city.country']);
        
        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('active', $request->status == 'active' ? 1 : 0);
        }
        
        $customers = $query->orderBy('last_name')->paginate(20);
        
        // Agregar estadísticas de rentas para cada cliente
        $customers->getCollection()->transform(function($customer) {
            $customer->total_rentals = Rental::where('customer_id', $customer->customer_id)->count();
            $customer->active_rentals = Rental::where('customer_id', $customer->customer_id)
                                             ->whereNull('return_date')->count();
            $customer->overdue_rentals = Rental::where('customer_id', $customer->customer_id)
                                              ->whereNull('return_date')
                                              ->whereHas('inventory.film', function($q) {
                                                  $q->whereRaw('DATE_ADD(rental.rental_date, INTERVAL film.rental_duration DAY) < CURDATE()');
                                              })->count();
            return $customer;
        });
        
        // Estadísticas generales
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('active', true)->count(),
            'inactive_customers' => Customer::where('active', false)->count(),
            'customers_with_rentals' => Customer::whereHas('rentals')->count(),
        ];
        
        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Mostrar detalles de un cliente
     */
    public function show(Customer $customer): View
    {
        $customer->load(['address.city.country']);
        
        // Historial de rentas
        $rentals = Rental::with(['inventory.film', 'staff', 'payment'])
            ->whereHas('inventory.film') // Solo rentas con inventario y película válidos
            ->where('customer_id', $customer->customer_id)
            ->orderBy('rental_date', 'desc')
            ->paginate(10);
        
        // Estadísticas del cliente
        $lastRental = Rental::where('customer_id', $customer->customer_id)
                           ->latest('rental_date')
                           ->first();
        
        $customerStats = [
            'total_rentals' => Rental::where('customer_id', $customer->customer_id)->count(),
            'active_rentals' => Rental::where('customer_id', $customer->customer_id)
                                     ->whereNull('return_date')->count(),
            'overdue_rentals' => Rental::where('customer_id', $customer->customer_id)
                                      ->whereNull('return_date')
                                      ->whereHas('inventory.film', function($q) {
                                          $q->whereRaw('DATE_ADD(rental.rental_date, INTERVAL film.rental_duration DAY) < CURDATE()');
                                      })->count(),
            'total_payments' => DB::table('payment')
                                 ->where('customer_id', $customer->customer_id)
                                 ->sum('amount'),
            'last_rental' => $lastRental ? $lastRental->rental_date : null,
        ];
        
        // Películas más rentadas por este cliente
        $favoriteFilms = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->where('rental.customer_id', $customer->customer_id)
            ->select('film.title', 'film.film_id', DB::raw('COUNT(*) as rental_count'))
            ->groupBy('film.film_id', 'film.title')
            ->orderBy('rental_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('customers.show', compact('customer', 'rentals', 'customerStats', 'favoriteFilms'));
    }

    /**
     * Search customers for AJAX requests
     */
    public function search(Request $request)
    {
        if (!$request->has('q')) {
            return response()->json(['customers' => []]);
        }

        $searchTerm = $request->q;
        
        $customers = Customer::where(function($query) use ($searchTerm) {
                $query->where('first_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            })
            ->where('active', true) // Solo clientes activos para rentas
            ->limit(10)
            ->get()
            ->map(function($customer) {
                return [
                    'customer_id' => $customer->customer_id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'active' => $customer->active,
                    'full_name' => $customer->first_name . ' ' . $customer->last_name
                ];
            });

        return response()->json(['customers' => $customers]);
    }
}
