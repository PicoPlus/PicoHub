<?php

namespace App\Http\Middleware;

use App\Http\Request;

class AdminAuth
{
    public static function handle(Request $request): void
    {
        if (!session('login_state') || session('user_role') !== 'Admin') {
            redirect(url('/admin/login'));
        }
    }
}
