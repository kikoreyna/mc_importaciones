<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'email',
        'phone',
        'website',
        'address',
        'logo_path',
        'favicon_path',
    ];
}