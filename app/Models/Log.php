<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['status'];

    public function getStatusAttribute($value)
    {
        if ($value == 3) {
            return ["Zakończono", 'text-success'];
        } else {
            return ["Wystąpił błąd", "text-danger"];
        }
    }

    public function details()
    {
        return $this->hasMany(LogDetail::class, 'log_id', 'id');
    }
}
