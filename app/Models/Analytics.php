<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    const TABLE = 'analytics';
    protected $table = self::TABLE;
    protected $fillable = [
        'user_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'response_time',
    ];
}
