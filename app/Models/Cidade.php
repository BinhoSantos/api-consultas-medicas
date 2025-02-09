<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cidade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cidade';

    protected $fillable = ['nome', 'estado'];

    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }
}
