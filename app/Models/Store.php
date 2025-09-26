<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    
    protected $table = 'store';
    protected $primaryKey = 'store_id';
    
    protected $fillable = [
        'manager_staff_id',
        'address_id'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
        'store_id' => 'integer',
        'manager_staff_id' => 'integer',
        'address_id' => 'integer'
    ];
    
    public $timestamps = false;
    const UPDATED_AT = 'last_update';
    
    /**
     * Relación con Staff (manager)
     */
    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_staff_id');
    }
    
    /**
     * Relación con Address
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    
    /**
     * Relación con Staff (empleados)
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'store_id');
    }
    
    /**
     * Relación con Customers
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'store_id');
    }
    
    /**
     * Relación con Inventory
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'store_id');
    }
}
