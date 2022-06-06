<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'investment_id',
        'taxation',
        'amount_withdrawn'
    ];

    /**
     * Get the user that owns the Investment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }
}
