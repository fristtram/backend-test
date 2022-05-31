<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'users_id',
        'gains_id',
        'amount',
        'date'
    ];

    /**
     * Get the user that owns the Investment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the gain that owns the Investment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function gain(): BelongsTo
    {
        return $this->belongsTo(Gain::class);
    }
}
