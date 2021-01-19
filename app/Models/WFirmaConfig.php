<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WFirmaConfig extends Model
{
    protected $fillable = ['name', 'login', 'password', 'company_id'];


    public function series()
    {
        return $this->hasMany(WFirmaInvoiceSerie::class, 'w_firma_config_id', 'id');
    }
}
