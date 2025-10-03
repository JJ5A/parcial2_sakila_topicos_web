<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
    
    protected $fillable = [
        'store_id',
        'first_name',
        'last_name',
        'email',
        'address_id',
        'active',
        'create_date',
        'last_update'
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'customer_id' => 'integer',
        'create_date' => 'datetime',
        'last_update' => 'datetime'
    ];
    
    /**
     * Relación con Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    
    /**
     * Relación con Address
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    
    /**
     * Relación con Rentals
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id');
    }
    
    /**
     * Relación con Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }
    
    /**
     * Rentas activas del cliente
     */
    public function activeRentals()
    {
        return $this->rentals()->active();
    }
    
    /**
     * Rentas atrasadas del cliente
     */
    public function overdueRentals()
    {
        return $this->rentals()->overdue();
    }
    
    /**
     * Obtener nombre completo
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    /**
     * Obtener nombre con formato apellido, nombre
     */
    public function getFormattedNameAttribute()
    {
        return $this->last_name . ', ' . $this->first_name;
    }
    
    /**
     * Verificar si el cliente tiene rentas activas
     */
    public function hasActiveRentals()
    {
        return $this->activeRentals()->exists();
    }
    
    /**
     * Calcular balance del cliente (total de rentas sin devolver)
     */
    public function calculateBalance()
    {
        return $this->activeRentals()
            ->with('inventory.film')
            ->get()
            ->sum(function ($rental) {
                return $rental->inventory->film->rental_rate;
            });
    }
}
