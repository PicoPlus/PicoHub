<?php

namespace App\Controllers\Auth;

use App\Http\Request;

class AdminLoginController
{
    public static function show(): string
    {
        return view('admin.login');
    }

    public static function login(Request $request): never
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $email = strtolower(trim($data['email']));
        $admins = config('admin_users', []);

        if (!isset($admins[$email]) || $admins[$email] !== $request->input('password')) {
            remember_old($request->all());
            set_errors(['email' => ['اطلاعات ورود نادرست است']]);
            redirect(url('/admin/login'));
        }

        $name = ucfirst(strtok($email, '@'));

        $_SESSION['login_state'] = 1;
        $_SESSION['user_role'] = 'Admin';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;

        if ($request->boolean('remember')) {
            $_SESSION['remember_admin'] = true;
        }

        redirect(url('/admin/owner-select'));
    }

    public static function logout(): never
    {
        session_destroy();
        session_start();
        redirect(url('/admin/login'));
    }
}
