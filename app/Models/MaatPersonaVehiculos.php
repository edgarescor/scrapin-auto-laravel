<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaatPersonaVehiculos extends Model
{
    use HasFactory;
    protected $table = 'maat_persona_vehiculo';

    // Identificador
    protected $primaryKey = 'id';

    // Proteger campos
    protected $guarded = ['id'];

    // Deshabilitar timestamps
    public $timestamps = false;
}
