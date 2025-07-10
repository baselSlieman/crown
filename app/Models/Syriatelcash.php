<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class Syriatelcash extends Model
{
    use HasFactory;
    use SoftDeletes;

     protected $fillable = [
        'phone',
        'code',
        'status',
        'codeOrder',
        'type',
        'username',
        'userid',
        'userHistory',
        'refreshBalance',
        'MaarchentHistory',
        'info',
        'more'
    ];
}
