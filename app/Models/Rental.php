<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;
    
    protected $table = 'rental';
    public $timestamps = false;
    protected $primaryKey = 'rental_id';
    
    protected $fillable = [
        'rental_date',
        'inventory_id', 
        'customer_id',
        'return_date',
        'staff_id'
    ];
    
    protected $dates = [
        'rental_date',
        'return_date',
        'last_update'
    ];    protected $casts = [
        'rental_date' => 'datetime',
        'return_date' => 'datetime',
        'customer_id' => 'integer',
        'inventory_id' => 'integer',
        'staff_id' => 'integer'
    ];
    
    /**
     * Relación con Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    /**
     * Relación con Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
    
    /**
     * Relación con Staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    
    /**
     * Relación con Payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'rental_id');
    }
    
    /**
     * Verificar si la renta está activa (no devuelta)
     */
    public function isActive()
    {
        return is_null($this->return_date);
    }
    
    /**
     * Verificar si la renta está atrasada
     */
    public function isOverdue()
    {
        if (!$this->isActive()) {
            return false;
        }
        
        $rentalDuration = $this->inventory->film->rental_duration;
        $dueDate = $this->rental_date->addDays($rentalDuration);
        
        return Carbon::now()->isAfter($dueDate);
    }
    
    /**
     * Calcular días de atraso
     */
    public function daysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        $rentalDuration = $this->inventory->film->rental_duration;
        $dueDate = $this->rental_date->copy()->addDays($rentalDuration);
        
        return Carbon::now()->diffInDays($dueDate);
    }
    
    /**
     * Obtener fecha de vencimiento
     */
    public function getDueDateAttribute()
    {
        $rentalDuration = $this->inventory->film->rental_duration ?? 3;
        return $this->rental_date->addDays($rentalDuration);
    }
    
    /**
     * Scope para rentas activas
     */
    public function scopeActive($query)
    {
        return $query->whereNull('return_date');
    }
    
    /**
     * Scope para rentas atrasadas
     */
    public function scopeOverdue($query)
    {
        return $query->active()
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->whereRaw('DATE_ADD(rental.rental_date, INTERVAL film.rental_duration DAY) < CURDATE()');
    }
}
