<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculosPRT extends Model
{
    use HasFactory;
    protected $table = 'prt_vehiculos';

    // Identificador
    protected $primaryKey = 'id';

    // Proteger campos
    protected $guarded = ['id'];

    // Deshabilitar timestamps
    public $timestamps = false;
}
