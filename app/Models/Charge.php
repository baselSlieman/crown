<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    protected $fillable=[
        "amount",
        "processid",
        "chat_id",
        "status",
        "method"
    ];

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

}
