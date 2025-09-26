<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;
    
    protected $fillable = [
        'country',
        'last_update'
    ];
    
    protected $dates = [
        'last_update'
    ];
    
    /**
     * Relación con Cities
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
    }
}
