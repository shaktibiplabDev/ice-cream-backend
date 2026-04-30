<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Inquiry;

class DashboardController extends Controller
{
    public function index()
    {
        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('status', 'new')->count();
        $readInquiries = Inquiry::where('status', 'read')->count();
        $repliedInquiries = Inquiry::where('status', 'replied')->count();
        $totalDistributors = Distributor::count();
        $activeDistributors = Distributor::where('is_active', true)->count();
        $inactiveDistributors = max($totalDistributors - $activeDistributors, 0);
        $recentInquiries = Inquiry::latest()->take(5)->get();
        $recentDistributors = Distributor::latest()->take(5)->get();
        $responseRate = $totalInquiries > 0 ? (int) round(($repliedInquiries / $totalInquiries) * 100) : 0;

        $monthlyInquiryStats = collect(range(3, 0))->map(function ($monthsAgo) {
            $month = now()->subMonthsNoOverflow($monthsAgo);
            $statusCounts = Inquiry::query()
                ->selectRaw('status, COUNT(*) as aggregate')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            return [
                'label' => $month->format('M'),
                'new' => (int) ($statusCounts['new'] ?? 0),
                'read' => (int) ($statusCounts['read'] ?? 0),
                'replied' => (int) ($statusCounts['replied'] ?? 0),
            ];
        });

        $maxChartValue = max(
            1,
            (int) $monthlyInquiryStats
                ->flatMap(fn ($month) => [$month['new'], $month['read'], $month['replied']])
                ->max()
        );

        $pipelineStats = collect([
            [
                'name' => 'New inquiries',
                'count' => $newInquiries,
                'percentage' => $totalInquiries > 0 ? (int) round(($newInquiries / $totalInquiries) * 100) : 0,
                'class' => 'if-ambl',
            ],
            [
                'name' => 'Read inquiries',
                'count' => $readInquiries,
                'percentage' => $totalInquiries > 0 ? (int) round(($readInquiries / $totalInquiries) * 100) : 0,
                'class' => 'if-sky',
            ],
            [
                'name' => 'Replied inquiries',
                'count' => $repliedInquiries,
                'percentage' => $totalInquiries > 0 ? (int) round(($repliedInquiries / $totalInquiries) * 100) : 0,
                'class' => 'if-mint',
            ],
            [
                'name' => 'Active distributors',
                'count' => $activeDistributors,
                'percentage' => $totalDistributors > 0 ? (int) round(($activeDistributors / $totalDistributors) * 100) : 0,
                'class' => 'if-lav',
            ],
        ]);

        return view('admin.dashboard', compact(
            'activeDistributors',
            'inactiveDistributors',
            'maxChartValue',
            'monthlyInquiryStats',
            'newInquiries',
            'pipelineStats',
            'readInquiries',
            'recentDistributors',
            'recentInquiries',
            'repliedInquiries',
            'responseRate',
            'totalDistributors',
            'totalInquiries',
        ));
    }
}
