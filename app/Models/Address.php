<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    
    protected $table = 'address';
    protected $primaryKey = 'address_id';
    
    protected $fillable = [
        'address',
        'address2',
        'district',
        'city_id',
        'postal_code',
        'phone'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
        'address_id' => 'integer',
        'city_id' => 'integer'
    ];
    
    public $timestamps = false;
    const UPDATED_AT = 'last_update';
    
    /**
     * Relación con City
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    /**
     * Obtener dirección completa formateada
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address;
        
        if ($this->address2) {
            $address .= ', ' . $this->address2;
        }
        
        $address .= ', ' . $this->district;
        
        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }
        
        return $address;
    }
}
