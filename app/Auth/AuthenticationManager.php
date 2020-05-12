<?php
declare(strict_types=1);

namespace App\Auth;

use Illuminate\Support\Facades\Auth;

final class AuthenticationManager
{
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function user()
    {

    }
}
