<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;
    protected $table = 'actor';
    protected $primaryKey = 'actor_id';
    
    protected $fillable = [
        'first_name',
        'last_name'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
    ];
    
    // Deshabilitar timestamps automáticos ya que usamos last_update personalizado
    public $timestamps = false;
    
    // Definir el campo de timestamp personalizado
    const UPDATED_AT = 'last_update';
}
