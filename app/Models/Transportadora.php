<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasActivityLog;

class Transportadora extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'nombre',
        'web',
        'telefono',
    ];
}
