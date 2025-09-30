<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class StaffUserProvider extends EloquentUserProvider
{
    /**
     * Validar las credenciales del usuario contra las proporcionadas.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        
        // Usar MD5 en lugar de Hash::check para compatibilidad con VARCHAR(40)
        return $user->password === md5($plain);
    }
}