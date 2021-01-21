<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestaProduct extends Model
{
    protected $fillable = ['presta_config_id', 'presta_id', 'name', 'code', 'count', 'status'];
}
