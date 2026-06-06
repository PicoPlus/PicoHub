<?php

namespace App\Http\Middleware;

use App\Http\Request;

class UserAuth
{
    public static function handle(Request $request): void
    {
        if (!session('login_state') || session('user_role') !== 'User') {
            redirect(url('/auth/login'));
        }
    }
}
