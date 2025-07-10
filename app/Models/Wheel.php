<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wheel extends Model
{
    use HasFactory;

    protected $fillable = [
        "rotation",
        "amount",
        "difference",
        "status",
        "chat_id",
        "canwheel"
    ];
}
