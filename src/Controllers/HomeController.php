<?php

namespace App\Controllers;

class HomeController
{
    public static function index(): never
    {
        if (session('login_state')) {
            if (session('user_role') === 'Admin') {
                redirect(url('/admin/dashboard'));
            }
            redirect(url('/user/panel'));
        }

        redirect(url('/auth/login'));
    }
}
