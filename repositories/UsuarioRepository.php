<?php

namespace Repositories;

use Models\Usuario;
use Models\UserTokens;

class UsuarioRepository {
    // pegando todos os usuários
    public static function getAll() {
        return Usuario::all();
    }

    // pegando user pelo id
    public static function getById($id) {
        return Usuario::find($id);
    }

    // pegando user pelo email
    public static function getByEmail($email) {
        return Usuario::where('email', $email)->first();
    }

    // atualizando foto de perfil do usuário
    public static function updateProfilePicture($id, $profilePicture) {
        return Usuario::where('id', $id)->update(['fotoPerfil' => $profilePicture]);
    }

    // atualizar senha do usuário
    public static function updatePassword($id, $password) {
        return Usuario::where('id', $id)->update(['senha' => $password]);
    }

    // criando um usuário
    public static function create($data) {
        return Usuario::create($data);
    }

    // atualizando um usuário
    public static function update($id, $data) {
        return Usuario::where('id', $id)->update($data);
    }
    
    // deletando um usuário 
    public static function delete($id) {
        return Usuario::where('id', $id)->delete();
    }

    // puxando tokens e verificando se o token passado é válido
    public static function isTokenValid($token) {
        return UserTokens::where('token', $token)->first();
    }

    // salvando novo token para o usuário
    public static function saveToken($userId, $token) {
        return UserTokens::create([
            'user_id' => $userId,
            'token' => $token
        ]);
    }

    // deletando token do usuário
    public static function deleteToken($token) {
        return UserTokens::where('token', $token)->delete();
    }
}