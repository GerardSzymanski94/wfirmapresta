<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDetail extends Model
{
    protected $fillable = ['product_id', 'log_id', 'product_code', 'product_name', 'status', 'count_after', 'count_before'];
}
