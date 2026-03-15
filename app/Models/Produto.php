<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'preco',
        'estoque',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function itensVenda()
    {
        return $this->hasMany(ItemVenda::class);
    }
}