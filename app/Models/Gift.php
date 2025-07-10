<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "chat_id",
        "amount",
        "code",
        "status",
        "type"
    ];


    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
