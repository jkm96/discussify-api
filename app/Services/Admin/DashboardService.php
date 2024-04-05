<?php

namespace App\Services\Admin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DashboardService
{
    /**
     * @return Application|Factory|View
     */
    public function renderDashboard()
    {
        return view('admin.dashboard.dashboard');
    }
}
