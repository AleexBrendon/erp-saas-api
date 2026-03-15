<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financeiro extends Model
{
    use HasFactory;

     protected $table = 'financeiro';

    protected $fillable = [
        'empresa_id',
        'tipo',
        'descricao',
        'valor',
        'data',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}