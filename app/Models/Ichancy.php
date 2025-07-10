<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ichancy extends Model
{
    protected $fillable=[
        "chat_id",
        "e_username",
        "e_password",
        "username",
        "password",
        "status",
        "identifier"
    ];

    public function ichTransactions():HasMany
    {
        return $this->hasMany(IchTransaction::class);
    }

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
