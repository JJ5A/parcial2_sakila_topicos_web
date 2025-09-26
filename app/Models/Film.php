<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;
    
    protected $table = 'film';
    protected $primaryKey = 'film_id';
    
    protected $fillable = [
        'title',
        'description',
        'release_year',
        'language_id',
        'original_language_id',
        'rental_duration',
        'rental_rate',
        'length',
        'replacement_cost',
        'rating',
        'special_features'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
        'rental_rate' => 'decimal:2',
        'replacement_cost' => 'decimal:2',
        'release_year' => 'integer',
        'rental_duration' => 'integer',
        'length' => 'integer',
        'language_id' => 'integer',
        'original_language_id' => 'integer',
    ];
    
    // Desactivar timestamps automáticos
    public $timestamps = false;
    const UPDATED_AT = 'last_update';
    
    // Constantes para valores ENUM
    const RATING_G = 'G';
    const RATING_PG = 'PG';
    const RATING_PG13 = 'PG-13';
    const RATING_R = 'R';
    const RATING_NC17 = 'NC-17';
    
    // Constantes para special_features SET
    const FEATURE_TRAILERS = 'Trailers';
    const FEATURE_COMMENTARIES = 'Commentaries';
    const FEATURE_DELETED_SCENES = 'Deleted Scenes';
    const FEATURE_BEHIND_SCENES = 'Behind the Scenes';
    
    /**
     * Obtener todos los ratings posibles
     */
    public static function getRatings()
    {
        return [
            self::RATING_G => 'G - General Audiences',
            self::RATING_PG => 'PG - Parental Guidance',
            self::RATING_PG13 => 'PG-13 - Parents Strongly Cautioned',
            self::RATING_R => 'R - Restricted',
            self::RATING_NC17 => 'NC-17 - No One 17 and Under'
        ];
    }
    
    /**
     * Obtener todas las características especiales posibles
     */
    public static function getSpecialFeatures()
    {
        return [
            self::FEATURE_TRAILERS => 'Trailers',
            self::FEATURE_COMMENTARIES => 'Commentaries',
            self::FEATURE_DELETED_SCENES => 'Deleted Scenes',
            self::FEATURE_BEHIND_SCENES => 'Behind the Scenes'
        ];
    }
    
    /**
     * Relación con Language (idioma principal)
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    
    /**
     * Relación con Language (idioma original)
     */
    public function originalLanguage()
    {
        return $this->belongsTo(Language::class, 'original_language_id');
    }
    
    /**
     * Relación con Inventory
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'film_id');
    }
    
    /**
     * Relación con Actor a través de film_actor
     */
    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'film_actor', 'film_id', 'actor_id')
            ->withTimestamps();
    }
    
    /**
     * Relación con Category a través de film_category
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'film_category', 'film_id', 'category_id')
            ->withTimestamps();
    }
    
    /**
     * Relación con FilmText
     */
    public function filmText()
    {
        return $this->hasOne(FilmText::class, 'film_id');
    }
    
    /**
     * Accessor para formatear el precio de alquiler
     */
    public function getFormattedRentalRateAttribute()
    {
        return '$' . number_format($this->rental_rate, 2);
    }
    
    /**
     * Accessor para formatear el costo de reemplazo
     */
    public function getFormattedReplacementCostAttribute()
    {
        return '$' . number_format($this->replacement_cost, 2);
    }
    
    /**
     * Accessor para formatear la duración
     */
    public function getFormattedLengthAttribute()
    {
        if (!$this->length) return 'N/A';
        
        $hours = floor($this->length / 60);
        $minutes = $this->length % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }
}
