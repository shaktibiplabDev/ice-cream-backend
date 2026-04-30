<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Services\EmailFetcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Show cron job management page
     */
    public function index()
    {
        $settings = CompanySetting::getSettings();

        // Generate cron URL token if not exists
        if (!$settings->cron_token) {
            $settings->update(['cron_token' => bin2hex(random_bytes(16))]);
        }

        $cronUrl = route('cron.run', ['token' => $settings->cron_token]);

        // Get last run times
        $lastRuns = [
            'email_fetch' => $settings->last_email_fetch_at?->diffForHumans() ?? 'Never',
            'cache_cleanup' => cache('last_cache_cleanup')?->diffForHumans() ?? 'Never',
            'backup' => cache('last_backup')?->diffForHumans() ?? 'Never',
        ];

        // Enabled jobs
        $enabledJobs = [
            'email_fetch' => $settings->email_fetching_enabled,
            'cache_cleanup' => true,
            'backup' => false, // TODO: implement backup
        ];

        return view('admin.cron.index', compact('cronUrl', 'lastRuns', 'enabledJobs'));
    }

    /**
     * Public cron endpoint - can be called by external cron services
     */
    public function run(Request $request, string $token)
    {
        $settings = CompanySetting::getSettings();

        // Verify token
        if ($settings->cron_token !== $token) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        // Log start
        Log::info('Cron job started', ['ip' => $request->ip()]);

        $results = [];
        $startTime = microtime(true);

        // 1. Fetch Emails (if enabled)
        if ($settings->email_fetching_enabled) {
            try {
                $fetcher = app(EmailFetcher::class);
                $result = $fetcher->fetch();
                $results['email_fetch'] = $result;
            } catch (\Exception $e) {
                Log::error('Email fetch failed in cron', ['error' => $e->getMessage()]);
                $results['email_fetch'] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        // 2. Clear old cache
        try {
            Artisan::call('cache:prune-stale');
            cache(['last_cache_cleanup' => now()], now()->addDays(7));
            $results['cache_cleanup'] = ['status' => 'success'];
        } catch (\Exception $e) {
            $results['cache_cleanup'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 3. Clear expired sessions
        try {
            Artisan::call('session:gc');
            $results['session_cleanup'] = ['status' => 'success'];
        } catch (\Exception $e) {
            $results['session_cleanup'] = ['status' => 'error'];
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('Cron job completed', ['duration_ms' => $duration, 'results' => $results]);

        return response()->json([
            'status' => 'success',
            'duration_ms' => $duration,
            'timestamp' => now()->toISOString(),
            'results' => $results,
        ]);
    }

    /**
     * Manual trigger for testing
     */
    public function trigger(Request $request, string $job)
    {
        $settings = CompanySetting::getSettings();
        $results = [];

        switch ($job) {
            case 'email':
                if ($settings->email_fetching_enabled) {
                    $fetcher = app(EmailFetcher::class);
                    $results = $fetcher->fetch();
                } else {
                    $results = ['status' => 'disabled', 'message' => 'Email fetching is disabled'];
                }
                break;

            case 'cache':
                Artisan::call('cache:clear');
                $results = ['status' => 'success', 'message' => 'Cache cleared'];
                break;

            case 'config':
                Artisan::call('config:cache');
                $results = ['status' => 'success', 'message' => 'Config cached'];
                break;

            case 'view':
                Artisan::call('view:clear');
                $results = ['status' => 'success', 'message' => 'Views cleared'];
                break;

            default:
                return response()->json(['error' => 'Unknown job'], 400);
        }

        return response()->json($results);
    }

    /**
     * Get cron status for AJAX polling
     */
    public function status()
    {
        $settings = CompanySetting::getSettings();

        return response()->json([
            'last_email_fetch' => $settings->last_email_fetch_at?->toISOString(),
            'email_fetching_enabled' => $settings->email_fetching_enabled,
            'cache_cleanup' => cache('last_cache_cleanup')?->toISOString(),
        ]);
    }

    /**
     * Regenerate cron token
     */
    public function regenerateToken()
    {
        $settings = CompanySetting::getSettings();
        $newToken = bin2hex(random_bytes(16));
        $settings->update(['cron_token' => $newToken]);

        return redirect()->route('admin.cron.index')
            ->with('success', 'Cron URL token regenerated. Update your cron job URL.');
    }
}
