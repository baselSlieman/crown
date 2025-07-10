<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Withdraw extends Model
{
    protected $fillable = [
        "amount",
        "code",
        "chat_id",
        "finalAmount",
        "discountAmount",
        "status",
        "subscriber",
        "method"
    ];

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

}
