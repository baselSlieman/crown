<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable=[
        "id",
        "username",
        "first_name",
        "last_name",
        "info",
        "balance",
        "affiliate_code"
    ];

    public function ichancies():HasMany
    {
        return $this->hasMany(Ichancy::class);
    }
    public function ichTransactions():HasMany
    {
        return $this->hasMany(IchTransaction::class);
    }
    public function affiliates():HasMany
    {
        return $this->hasMany(Affiliate::class);
    }



    public function affiliatedChats()
    {
        // كل السجلات التي تُشير إلى هذا id كـ affiliate_code
        return $this->hasMany(Chat::class, 'affiliate_code', 'id');
    }


    public function charges()
    {
        return $this->hasMany(Charge::class, 'chat_id', 'id');
    }
}
