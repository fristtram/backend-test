<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gain extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get all of the investment for the Gain
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function investment(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
