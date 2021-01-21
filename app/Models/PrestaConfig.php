<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestaConfig extends Model
{
    protected $fillable = ['name', 'api_key', 'url', 'status'];
}
