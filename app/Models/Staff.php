<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'active'
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'staff_id' => 'integer'
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
        return $this->hasMany(Rental::class, 'staff_id');
    }
    
    /**
     * Relación con Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'staff_id');
    }
    
    /**
     * Obtener nombre completo
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    /**
     * Scope para empleados activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope por tienda
     */
    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}
