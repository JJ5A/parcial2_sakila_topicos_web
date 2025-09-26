<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'language';
    protected $primaryKey = 'language_id';
    
    protected $fillable = [
        'name'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
    ];
    
    public $timestamps = false;
    const UPDATED_AT = 'last_update';
    
    /**
     * Relación con Film (películas con este idioma)
     */
    public function films()
    {
        return $this->hasMany(Film::class, 'language_id');
    }
    
    /**
     * Relación con Film (películas con este idioma original)
     */
    public function originalFilms()
    {
        return $this->hasMany(Film::class, 'original_language_id');
    }
}
