<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundEmail extends Model
{
    protected $guarded = [];

    protected $casts = [
        'needs_human' => 'boolean',
        'confidence'  => 'float',
    ];
}
