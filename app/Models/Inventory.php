<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    
    protected $fillable = [
        'film_id',
        'store_id',
        'last_update'
    ];
    
    protected $dates = [
        'last_update'
    ];
    
    protected $casts = [
        'inventory_id' => 'integer',
        'film_id' => 'integer',
        'store_id' => 'integer'
    ];
    
    public $timestamps = false;
    
    /**
     * Relación con Film
     */
    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id');
    }
    
    /**
     * Relación con Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    
    /**
     * Relación con Rentals
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'inventory_id');
    }
    
    /**
     * Renta activa (si existe)
     */
    public function activeRental()
    {
        return $this->hasOne(Rental::class, 'inventory_id')->whereNull('return_date');
    }
    
    /**
     * Verificar si el ítem está disponible para renta
     */
    public function isAvailable()
    {
        return !$this->activeRental()->exists();
    }
    
    /**
     * Obtener el estado del ítem
     */
    public function getStatusAttribute()
    {
        return $this->isAvailable() ? 'Disponible' : 'Rentado';
    }
    
    /**
     * Scope para ítems disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('activeRental');
    }
    
    /**
     * Scope para ítems rentados
     */
    public function scopeRented($query)
    {
        return $query->whereHas('activeRental');
    }
    
    /**
     * Scope por tienda
     */
    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}
