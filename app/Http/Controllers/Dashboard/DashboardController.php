<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboard,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $stats = $this->dashboard->getStats();
        $pinjamanMenunggu = $this->dashboard->pinjamanMenunggu();

        return view('dashboard.index', compact('user', 'stats', 'pinjamanMenunggu'));
    }
}
