<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WFirmaGood extends Model
{
    protected $fillable = ['good_id', 'w_firma_config_id', 'code', 'unit', 'netto',
        'brutto', 'lumpcode', 'classification', 'description', 'discount', 'notes', 'documents', 'tags', 'count', 'name'];
}
