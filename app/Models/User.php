<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'staff';
    
    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'staff_id';
    
    /**
     * Indica si el modelo debe ser timestamped.
     */
    public $timestamps = false;

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'first_name',
        'last_name', 
        'address_id',
        'email',
        'store_id',
        'username',
        'password',
        'active',
        'picture'
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Indica si el modelo debe usar remember tokens.
     */
    protected $rememberTokenName = false;

    /**
     * Get the name of the "remember me" token column.
     *
     * @return string|null
     */
    public function getRememberTokenName()
    {
        return null; // Desactivar remember tokens
    }

    /**
     * Los atributos que deben ser castrados.
     */
    protected $casts = [
        'active' => 'boolean',
        'staff_id' => 'integer',
        'address_id' => 'integer', 
        'store_id' => 'integer',
        'last_update' => 'datetime',
    ];

    /**
     * Obtener el nombre completo del staff.
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Obtener el nombre completo del staff.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Relación con Address.
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Relación con Store.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Relación con las rentas manejadas por este staff.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'staff_id');
    }

    /**
     * Relación con los pagos procesados por este staff.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'staff_id');
    }

    /**
     * Verificar si el staff está activo.
     */
    public function isActive()
    {
        return $this->active == 1;
    }

    /**
     * Verificar contraseña usando MD5 (para compatibilidad con campo VARCHAR(40)).
     */
    public function checkPassword($password)
    {
        return $this->password === md5($password);
    }

    /**
     * Scope para obtener solo staff activo.
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
