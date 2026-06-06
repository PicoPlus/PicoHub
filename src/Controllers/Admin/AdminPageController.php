<?php

namespace App\Controllers\Admin;

class AdminPageController
{
    public static function kanban(): string
    {
        return view('admin.pages.placeholder', ['title' => 'کانبان']);
    }

    public static function contacts(): string
    {
        return view('admin.pages.placeholder', ['title' => 'مخاطبین']);
    }

    public static function deals(): string
    {
        return view('admin.pages.placeholder', ['title' => 'معاملات']);
    }

    public static function tickets(): string
    {
        return view('admin.pages.placeholder', ['title' => 'تیکت‌ها']);
    }

    public static function analytics(): string
    {
        return view('admin.pages.placeholder', ['title' => 'تحلیل‌ها']);
    }

    public static function settings(): string
    {
        return view('admin.pages.placeholder', ['title' => 'تنظیمات']);
    }
}
