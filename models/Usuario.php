<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model {
    protected $table = 'usuario';
    protected $fillable = [
        'nome', 
        'email',
        'senha',
        'idade',
        'peso',
        'altura',
        'academia',
        'cidade',
        'estado',
        'base',
        'competidor',
        'iniciouEm',
        'fotoPerfil',
        'assinatura'
    ];
    public $timestamps = true;

    /**
     * Obtém os tokens associados a este usuário
     */
    public function tokens()
    {
        return $this->hasMany(UserTokens::class, 'user_id');
    }
}