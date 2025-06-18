<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class UserTokens extends Model {
    protected $table = 'user_tokens';
    protected $fillable = [
        'user_id', 
        'token'
    ];
    public $timestamps = true;
    
    /**
     * Obtém o usuário associado a este token
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}