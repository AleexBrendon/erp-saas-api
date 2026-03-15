<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj',
        'email',
        'plano',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function financeiro()
    {
        return $this->hasMany(Financeiro::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}