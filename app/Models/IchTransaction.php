<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IchTransaction extends Model
{
    protected $fillable = [
        "chat_id",
        "amount",
        "type",
        "ichancy_id",
        "status"
    ];

    public function ichancy():BelongsTo
    {
        return $this->belongsTo(Ichancy::class);
    }

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
