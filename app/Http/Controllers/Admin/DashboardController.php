<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->renderView('dashboard.twig', [
            'title' => 'Dashboard',
            'stats' => [
                'users' => 124,
                'posts' => 56,
                'visits' => 3087
            ]
        ]);
    }
}
