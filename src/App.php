<?php

namespace App;

use App\Http\Request;
use App\Http\Router;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\UserAuth;
use App\Controllers\HomeController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\AdminLoginController;
use App\Controllers\User\PanelController;
use App\Controllers\Deal\SearchController;
use App\Controllers\Admin\OwnerSelectController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\AdminPageController;

class App
{
    public static function run(): void
    {
        $router = new Router();
        $request = Request::capture();

        $router->get('/', [HomeController::class, 'index']);

        $router->get('/auth/login', [LoginController::class, 'show']);
        $router->post('/auth/login', [LoginController::class, 'login']);
        $router->get('/auth/register', [RegisterController::class, 'show']);
        $router->post('/auth/register/verify-identity', [RegisterController::class, 'verifyIdentity']);
        $router->post('/auth/register/send-otp', [RegisterController::class, 'sendOtp']);
        $router->post('/auth/register/verify-otp', [RegisterController::class, 'verifyOtp']);
        $router->post('/logout', [LoginController::class, 'logout']);

        $router->get('/user/panel', [PanelController::class, 'index'], [UserAuth::class]);
        $router->get('/user', static fn () => redirect(url('/user/panel')), [UserAuth::class]);
        $router->get('/Deal/Search', [SearchController::class, 'show'], [UserAuth::class]);
        $router->post('/Deal/Search', [SearchController::class, 'search'], [UserAuth::class]);

        $router->get('/admin/login', [AdminLoginController::class, 'show']);
        $router->post('/admin/login', [AdminLoginController::class, 'login']);
        $router->post('/admin/logout', [AdminLoginController::class, 'logout']);

        $router->get('/admin/owner-select', [OwnerSelectController::class, 'show'], [AdminAuth::class]);
        $router->post('/admin/owner-select', [OwnerSelectController::class, 'store'], [AdminAuth::class]);
        $router->get('/admin/dashboard', [DashboardController::class, 'index'], [AdminAuth::class]);
        $router->get('/admin/kanban', [AdminPageController::class, 'kanban'], [AdminAuth::class]);
        $router->get('/admin/contacts', [AdminPageController::class, 'contacts'], [AdminAuth::class]);
        $router->get('/admin/deals', [AdminPageController::class, 'deals'], [AdminAuth::class]);
        $router->get('/admin/tickets', [AdminPageController::class, 'tickets'], [AdminAuth::class]);
        $router->get('/admin/analytics', [AdminPageController::class, 'analytics'], [AdminAuth::class]);
        $router->get('/admin/settings', [AdminPageController::class, 'settings'], [AdminAuth::class]);

        $router->get('/set-culture/fa-IR', static function () {
            $_SESSION['locale'] = 'fa-IR';
            redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        });
        $router->get('/set-culture/en-US', static function () {
            $_SESSION['locale'] = 'en-US';
            redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        });

        $router->dispatch($request);
    }
}
