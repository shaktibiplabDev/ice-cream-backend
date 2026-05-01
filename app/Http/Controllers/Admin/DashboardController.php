<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Inquiry;

class DashboardController extends Controller
{
    /**
     * Calculate percentage safely, returning 0 if denominator is 0
     */
    private function safePercentage(int $numerator, int $denominator): int
    {
        return $denominator > 0 ? (int) round(($numerator / $denominator) * 100) : 0;
    }

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
        $responseRate = $this->safePercentage($repliedInquiries, $totalInquiries);

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
                'percentage' => $this->safePercentage($newInquiries, $totalInquiries),
                'class' => 'if-ambl',
            ],
            [
                'name' => 'Read inquiries',
                'count' => $readInquiries,
                'percentage' => $this->safePercentage($readInquiries, $totalInquiries),
                'class' => 'if-sky',
            ],
            [
                'name' => 'Replied inquiries',
                'count' => $repliedInquiries,
                'percentage' => $this->safePercentage($repliedInquiries, $totalInquiries),
                'class' => 'if-mint',
            ],
            [
                'name' => 'Active distributors',
                'count' => $activeDistributors,
                'percentage' => $this->safePercentage($activeDistributors, $totalDistributors),
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
