<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('status', 'new')->count();
        $totalDistributors = Distributor::count();
        $recentInquiries = Inquiry::latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalInquiries', 'newInquiries', 'totalDistributors', 'recentInquiries'
        ));
    }
}