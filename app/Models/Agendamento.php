<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'servico_id',
        'usuario_id',
        'data',
        'hora',
        'status',
        'observacao',
    ];

    protected $casts = [
        'data' => 'date',
        'hora' => 'time',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}