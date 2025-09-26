<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;
    
    protected $fillable = [
        'customer_id',
        'staff_id',
        'rental_id',
        'amount',
        'payment_date',
        'last_update'
    ];
    
    protected $dates = [
        'payment_date',
        'last_update'
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'payment_id' => 'integer',
        'customer_id' => 'integer',
        'staff_id' => 'integer',
        'rental_id' => 'integer'
    ];
    
    /**
     * Relación con Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    /**
     * Relación con Staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    
    /**
     * Relación con Rental
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id');
    }
    
    /**
     * Formatear cantidad como moneda
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }
    
    /**
     * Scope para pagos por fecha
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('payment_date', $date);
    }
    
    /**
     * Scope para pagos por cliente
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
    
    /**
     * Scope para pagos por empleado
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }
}
