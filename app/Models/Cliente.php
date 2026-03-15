<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'nome',
        'email',
        'telefone',
        'documento',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }
}