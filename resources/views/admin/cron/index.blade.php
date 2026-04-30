@extends('layouts.admin')

@section('title', 'Cron Jobs')

@section('content')
    <div class="page-header">
        <h1>
            <small>Automated Tasks</small>
            Cron Job Manager
        </h1>
    </div>

    <div class="settings-grid">
        <!-- Cron URL Card -->
        <div class="glass-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <h2>🔗 Cron URL (No Server Setup Required!)</h2>
            </div>
            <div class="form-body" style="padding: 1.25rem;">
                <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid #34d399; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0 0 0.5rem 0; color: var(--text-muted);">Copy this URL and set it up with any free cron service:</p>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <code id="cron-url" style="flex: 1; background: rgba(0,0,0,0.3); padding: 0.75rem; border-radius: 6px; font-family: monospace; word-break: break-all;">{{ $cronUrl }}</code>
                        <button onclick="copyCronUrl()" class="btn-primary" style="padding: 0.75rem 1rem;">📋 Copy</button>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    <a href="https://cron-job.org" target="_blank" class="btn-primary" style="text-decoration: none;">🌐 Use cron-job.org (Free)</a>
                    <a href="https://easycron.com" target="_blank" class="btn-primary" style="text-decoration: none;">🌐 Use EasyCron (Free)</a>
                </div>

                <div style="background: rgba(251, 191, 36, 0.1); border: 1px solid #fbbf24; border-radius: 8px; padding: 1rem;">
                    <strong>⚠️ Keep this URL secret!</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">Anyone with this URL can trigger your cron jobs. If exposed, <a href="{{ route('admin.cron.regenerate') }}" style="color: #fbbf24;">regenerate it</a>.</p>
                </div>
            </div>
        </div>

        <!-- Job Status -->
        <div class="glass-card">
            <div class="card-head">
                <h2>📊 Job Status</h2>
            </div>
            <div class="form-body" style="padding: 1.25rem;">
                <table class="data-table">
                    <tbody>
                        <tr>
                            <td>📧 Email Fetch</td>
                            <td>
                                @if($enabledJobs['email_fetch'])
                                    <span class="status-badge status-active">Enabled</span>
                                @else
                                    <span class="status-badge status-inactive">Disabled</span>
                                @endif
                            </td>
                            <td style="color: var(--text-muted);">{{ $lastRuns['email_fetch'] }}</td>
                        </tr>
                        <tr>
                            <td>🗑️ Cache Cleanup</td>
                            <td>
                                @if($enabledJobs['cache_cleanup'])
                                    <span class="status-badge status-active">Enabled</span>
                                @else
                                    <span class="status-badge status-inactive">Disabled</span>
                                @endif
                            </td>
                            <td style="color: var(--text-muted);">{{ $lastRuns['cache_cleanup'] }}</td>
                        </tr>
                        <tr>
                            <td>💾 Backup</td>
                            <td>
                                @if($enabledJobs['backup'])
                                    <span class="status-badge status-active">Enabled</span>
                                @else
                                    <span class="status-badge status-inactive">Disabled</span>
                                @endif
                            </td>
                            <td style="color: var(--text-muted);">{{ $lastRuns['backup'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                    <small style="color: var(--text-muted);">Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span></small>
                </div>
            </div>
        </div>

        <!-- Backup Settings -->
        <div class="glass-card">
            <div class="card-head">
                <h2>💾 Backup Settings</h2>
            </div>
            <div class="form-body" style="padding: 1.25rem;">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label>Backup Interval (days)</label>
                        <input type="number" name="backup_days" value="{{ $settings->backup_days ?? 30 }}" class="form-control" min="1" max="365">
                        <small style="color: var(--text-muted);">Database backup runs every {{ $settings->backup_days ?? 30 }} days</small>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top: 0.5rem;">
                        💾 Save Backup Settings
                    </button>
                </form>

                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                    <small style="color: var(--text-muted);">Backups are stored in <code>storage/app/backups/</code></small>
                </div>
            </div>
        </div>

        <!-- Manual Trigger -->
        <div class="glass-card">
            <div class="card-head">
                <h2>🚀 Manual Run</h2>
            </div>
            <div class="form-body" style="padding: 1.25rem;">
                <p style="margin-bottom: 1rem; color: var(--text-muted);">Test cron jobs manually:</p>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <button onclick="triggerJob('email')" class="btn-primary" style="justify-content: flex-start; gap: 0.5rem;" {{ !$enabledJobs['email_fetch'] ? 'disabled' : '' }}>
                        📧 Run Email Fetch
                    </button>
                    <button onclick="triggerJob('cache')" class="btn-primary" style="justify-content: flex-start; gap: 0.5rem; background: rgba(251, 191, 36, 0.2);">
                        🗑️ Clear Cache
                    </button>
                    <button onclick="triggerJob('config')" class="btn-primary" style="justify-content: flex-start; gap: 0.5rem; background: rgba(251, 191, 36, 0.2);">
                        ⚙️ Cache Config
                    </button>
                    <button onclick="triggerJob('view')" class="btn-primary" style="justify-content: flex-start; gap: 0.5rem; background: rgba(251, 191, 36, 0.2);">
                        👁️ Clear Views
                    </button>
                    <button onclick="triggerJob('backup')" class="btn-primary" style="justify-content: flex-start; gap: 0.5rem; background: rgba(52, 211, 153, 0.2);">
                        💾 Run Backup
                    </button>
                </div>

                <div id="trigger-result" style="margin-top: 1rem; padding: 0.75rem; border-radius: 6px; display: none;"></div>
            </div>
        </div>

        <!-- Setup Instructions -->
        <div class="glass-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <h2>📖 Setup Instructions</h2>
            </div>
            <div class="form-body" style="padding: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h4 style="margin-bottom: 0.75rem; color: #34d399;">Option 1: cron-job.org (Recommended)</h4>
                        <ol style="margin: 0; padding-left: 1.25rem; line-height: 1.8;">
                            <li>Go to <a href="https://cron-job.org" target="_blank" style="color: #34d399;">cron-job.org</a></li>
                            <li>Create a free account</li>
                            <li>Click "Create cronjob"</li>
                            <li>Paste the Cron URL above</li>
                            <li>Set schedule to "Every 1 minute"</li>
                            <li>Save and you're done!</li>
                        </ol>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.75rem; color: #34d399;">Option 2: Server Cron (Advanced)</h4>
                        <ol style="margin: 0; padding-left: 1.25rem; line-height: 1.8;">
                            <li>Run <code style="background: rgba(0,0,0,0.3); padding: 0.25rem 0.5rem; border-radius: 4px;">crontab -e</code></li>
                            <li>Add this line:</li>
                        </ol>
                        <code style="display: block; background: rgba(0,0,0,0.3); padding: 0.75rem; border-radius: 4px; margin-top: 0.5rem; word-break: break-all;">* * * * * curl -s "{{ $cronUrl }}" > /dev/null 2>&1</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 100px;
        }
        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-active {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }
        .status-inactive {
            background: rgba(148, 163, 184, 0.2);
            color: #94a3b8;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-subtle);
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
    </style>

    @push('scripts')
    <script>
        function copyCronUrl() {
            const url = document.getElementById('cron-url').textContent;
            navigator.clipboard.writeText(url).then(() => {
                alert('Cron URL copied to clipboard!');
            });
        }

        async function triggerJob(job) {
            const resultDiv = document.getElementById('trigger-result');
            resultDiv.style.display = 'block';
            resultDiv.style.background = 'rgba(251, 191, 36, 0.1)';
            resultDiv.textContent = 'Running...';

            try {
                const response = await fetch(`/admin/cron/trigger/${job}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.status === 'success') {
                    resultDiv.style.background = 'rgba(52, 211, 153, 0.1)';
                    resultDiv.style.color = '#34d399';
                } else if (data.status === 'disabled') {
                    resultDiv.style.background = 'rgba(148, 163, 184, 0.1)';
                    resultDiv.style.color = '#94a3b8';
                } else {
                    resultDiv.style.background = 'rgba(248, 113, 113, 0.1)';
                    resultDiv.style.color = '#f87171';
                }
                resultDiv.textContent = data.message || JSON.stringify(data, null, 2);
            } catch (error) {
                resultDiv.style.background = 'rgba(248, 113, 113, 0.1)';
                resultDiv.style.color = '#f87171';
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }

        // Auto-refresh status every 30 seconds
        setInterval(async () => {
            try {
                const response = await fetch('/admin/cron/status');
                const data = await response.json();
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
            } catch (e) {
                console.log('Status refresh failed');
            }
        }, 30000);
    </script>
    @endpush
@endsection
