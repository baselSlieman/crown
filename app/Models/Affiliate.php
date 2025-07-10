<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client',
        'amount',
        'affiliate_amount',
        'status',
        'chat_id',
        'month_at'
    ];

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
