<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
    ];
    
    // Desactivar timestamps automáticos
    public $timestamps = false;
    const UPDATED_AT = 'last_update';
    
    /**
     * Relación con Film a través de film_category
     */
    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_category', 'category_id', 'film_id');
    }
}
