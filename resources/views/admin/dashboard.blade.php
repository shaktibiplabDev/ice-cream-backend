@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1>
            <small>Overview</small>
            Dashboard
        </h1>
        <div class="date-badge">
            <span aria-hidden="true">📅</span>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    <div class="stats-grid" role="list" aria-label="Key metrics">
        <div class="stat-card blue" role="listitem">
            <div class="stat-top">
                <div class="stat-icon blue" aria-hidden="true">📩</div>
                <span class="stat-trend info">{{ $recentInquiries->count() }} recent</span>
            </div>
            <div class="stat-value">{{ number_format($totalInquiries) }}</div>
            <div class="stat-label">Total Inquiries</div>
            <div class="stat-sub">All distributor leads received</div>
        </div>

        <div class="stat-card mint" role="listitem">
            <div class="stat-top">
                <div class="stat-icon mint" aria-hidden="true">🆕</div>
                <span class="stat-trend {{ $newInquiries > 0 ? 'warning' : 'success' }}">
                    {{ $newInquiries > 0 ? $newInquiries . ' needs review' : 'All clear' }}
                </span>
            </div>
            <div class="stat-value">{{ number_format($newInquiries) }}</div>
            <div class="stat-label">New Inquiries</div>
            <div class="stat-sub">{{ number_format($readInquiries) }} read, {{ number_format($repliedInquiries) }} replied</div>
        </div>

        <div class="stat-card blush" role="listitem">
            <div class="stat-top">
                <div class="stat-icon blush" aria-hidden="true">🚚</div>
                <span class="stat-trend success">{{ number_format($activeDistributors) }} active</span>
            </div>
            <div class="stat-value">{{ number_format($totalDistributors) }}</div>
            <div class="stat-label">Distributors</div>
            <div class="stat-sub">{{ number_format($inactiveDistributors) }} inactive distributor{{ $inactiveDistributors === 1 ? '' : 's' }}</div>
        </div>

        <div class="stat-card lavender" role="listitem">
            <div class="stat-top">
                <div class="stat-icon lavender" aria-hidden="true">💬</div>
                <span class="stat-trend success">{{ $responseRate }}%</span>
            </div>
            <div class="stat-value">{{ $responseRate }}%</div>
            <div class="stat-label">Response Rate</div>
            <div class="stat-sub">Inquiries marked as replied</div>
        </div>
    </div>

    <div class="mid-row">
        <div class="glass-card">
            <div class="card-head">
                <div>
                    <h2>Inquiry Flow</h2>
                    <p>Last four months by current status</p>
                </div>
                <a href="{{ route('admin.inquiries.index') }}" class="chip">📬 Open inbox</a>
            </div>
            <div class="chart-wrap">
                <div class="chart-legend" aria-hidden="true">
                    <div class="leg-item"><div class="leg-dot sky"></div> New</div>
                    <div class="leg-item"><div class="leg-dot mint"></div> Read</div>
                    <div class="leg-item"><div class="leg-dot blush"></div> Replied</div>
                </div>

                <div class="chart-area" role="img" aria-label="Bar chart showing inquiries by status for the last four months">
                    @foreach($monthlyInquiryStats as $stat)
                        @php
                            $newHeight = $stat['new'] > 0 ? max(6, (int) round(($stat['new'] / $maxChartValue) * 100)) : 6;
                            $readHeight = $stat['read'] > 0 ? max(6, (int) round(($stat['read'] / $maxChartValue) * 100)) : 6;
                            $repliedHeight = $stat['replied'] > 0 ? max(6, (int) round(($stat['replied'] / $maxChartValue) * 100)) : 6;
                        @endphp
                        <div class="bar-group">
                            <div class="bar sky" style="height: {{ $newHeight }}%" title="New - {{ $stat['label'] }}: {{ $stat['new'] }}"></div>
                            <div class="bar mint" style="height: {{ $readHeight }}%" title="Read - {{ $stat['label'] }}: {{ $stat['read'] }}"></div>
                            <div class="bar blush" style="height: {{ $repliedHeight }}%" title="Replied - {{ $stat['label'] }}: {{ $stat['replied'] }}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="chart-labels" aria-hidden="true">
                    @foreach($monthlyInquiryStats as $stat)
                        <span>{{ $stat['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-head">
                <div>
                    <h2>Distribution Network</h2>
                    <p>Recently added distributors</p>
                </div>
                <a href="{{ route('admin.distributors.index') }}" class="chip">👥 View all</a>
            </div>
            <div class="sellers-inner">
                @forelse($recentDistributors as $distributor)
                    @php
                        $rankClass = 'r' . min($loop->iteration, 5);
                        $tileClass = 'si-' . ((($loop->iteration - 1) % 5) + 1);
                        $distributorInitial = strtoupper(substr($distributor->name, 0, 1));
                    @endphp
                    <a href="{{ route('admin.distributors.show', $distributor->id) }}" class="seller-item">
                        <div class="seller-rank {{ $rankClass }}" aria-label="Rank {{ $loop->iteration }}">{{ $loop->iteration }}</div>
                        <div class="seller-img {{ $tileClass }}" aria-hidden="true">{{ $distributorInitial }}</div>
                        <div class="seller-info">
                            <div class="seller-name">{{ $distributor->name }}</div>
                            <div class="seller-cat">{{ $distributor->service_area ?? Str::limit($distributor->address, 30) }}</div>
                        </div>
                        <div class="seller-right">
                            <div class="seller-sales">{{ $distributor->is_active ? 'Active' : 'Inactive' }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">{{ $distributor->created_at->format('M d, Y') }}</div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">🏪</div>
                        <div>No distributors have been added yet.</div>
                        <a href="{{ route('admin.distributors.create') }}" class="btn-primary" style="margin-top: 1rem; padding: 0.5rem 1rem; font-size: 0.75rem;">+ Add your first distributor</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bottom-row">
        <div class="glass-card">
            <div class="card-head">
                <div>
                    <h2>Recent Inquiries</h2>
                    <p>Latest distributor requests</p>
                </div>
                <a href="{{ route('admin.inquiries.index') }}" class="chip">📋 View all</a>
            </div>
            <div class="table-wrap">
                <table class="data-table" aria-label="Recent inquiries table">
                    <thead>
                        <tr>
                            <th>Inquiry ID</th>
                            <th>Contact</th>
                            <th>Business</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                    </thead>
                    <tbody>
                        @forelse($recentInquiries as $inquiry)
                            @php
                                $statusClass = match($inquiry->status) {
                                    'new' => 'status-new',
                                    'read' => 'status-in-progress',
                                    'replied' => 'status-resolved',
                                    default => 'status-new'
                                };
                                $statusText = ucfirst($inquiry->status);
                                $avatarClass = 'ca' . ((($loop->iteration - 1) % 5) + 1);
                            @endphp
                            <tr>
                                <td class="order-id">{{ $inquiry->displayNumber() }}</td>
                                <td>
                                    <div class="customer">
                                        <div class="cust-av {{ $avatarClass }}">{{ substr($inquiry->name, 0, 1) }}</div>
                                        <span class="cust-name">{{ $inquiry->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $inquiry->business_name ?? 'Not provided' }}</td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td>{{ $inquiry->created_at->format('M d, Y') }}</td>
                                <td><a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="action-link">View →</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <div class="empty-state-icon">📭</div>
                                    <div>No inquiries have arrived yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:18px;">
            <div class="glass-card">
                <div class="card-head">
                    <div>
                        <h2>Pipeline Status</h2>
                        <p>Current inquiry distribution</p>
                    </div>
                </div>
                <div class="inv-bars">
                    @foreach($pipelineStats as $item)
                        <div class="inv-item">
                            <div class="inv-head">
                                <span class="inv-name">{{ $item['name'] }}</span>
                                <span class="inv-pct">{{ $item['count'] }} ({{ $item['percentage'] }}%)</span>
                            </div>
                            <div class="inv-track" role="progressbar" aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $item['name'] }}: {{ $item['percentage'] }}%">
                                <div class="inv-fill {{ $item['class'] }}" style="width: {{ $item['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-card">
                <div class="card-head">
                    <div>
                        <h2>Recent Activity</h2>
                        <p>Latest updates and changes</p>
                    </div>
                </div>
                <div class="activity-inner" aria-label="Recent activity">
                    @forelse($recentActivities ?? $recentInquiries->take(5) as $activity)
                        @php
                            $status = $activity->status ?? ($activity->is_active ? 'active' : 'inactive');
                            $activityClass = match($status) {
                                'new' => 'ad-blush',
                                'read' => 'ad-blue',
                                'replied' => 'ad-mint',
                                'active' => 'ad-mint',
                                'inactive' => 'ad-blush',
                                default => 'ad-blue'
                            };
                            $activityIcon = match($status) {
                                'new' => '📩',
                                'read' => '👀',
                                'replied' => '✅',
                                'active' => '🚚',
                                'inactive' => '⏸️',
                                default => '📌'
                            };
                            $activityTitle = match($status) {
                                'new', 'read', 'replied' => "Inquiry from {$activity->name} is {$activity->status}",
                                'active', 'inactive' => "Distributor {$activity->name} is now {$activity->is_active}",
                                default => "Update on {$activity->name}"
                            };
                        @endphp
                        <div class="activity-item">
                            <div class="act-dot {{ $activityClass }}" aria-hidden="true">{{ $activityIcon }}</div>
                            <div class="act-content">
                                <div class="act-title"><strong>{{ $activity->name ?? $activity->name }}</strong> {{ $activityTitle ?? 'was updated' }}</div>
                                <div class="act-time">{{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <div>No recent activity</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Additional dashboard-specific styles */
    .stat-trend {
        font-size: 0.7rem;
        padding: 0.25rem 0.625rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 600;
    }
    .stat-trend.success {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
    }
    .stat-trend.warning {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }
    .stat-trend.info {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
    }
    .stat-trend.danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
    }
    
    /* Stat card color variations */
    .stat-card.blue {
        border-top: 3px solid #3b82f6;
    }
    .stat-card.mint {
        border-top: 3px solid #10b981;
    }
    .stat-card.blush {
        border-top: 3px solid #a855f7;
    }
    .stat-card.lavender {
        border-top: 3px solid #8b5cf6;
    }
    
    /* Stat icon backgrounds */
    .stat-icon.blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
    .stat-icon.mint { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .stat-icon.blush { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
    .stat-icon.lavender { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
    
    /* Customer avatar colors */
    .cust-av {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        flex-shrink: 0;
    }
    .ca1 { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .ca2 { background: linear-gradient(135deg, #10b981, #34d399); }
    .ca3 { background: linear-gradient(135deg, #a855f7, #c084fc); }
    .ca4 { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .ca5 { background: linear-gradient(135deg, #ef4444, #f87171); }
    
    .customer {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .cust-name {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-primary);
    }
    .order-id {
        font-weight: 600;
        color: var(--accent-primary-light);
    }
    
    /* Pipeline bars */
    .inv-fill.if-sky { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .inv-fill.if-mint { background: linear-gradient(90deg, #10b981, #34d399); }
    .inv-fill.if-blush { background: linear-gradient(90deg, #a855f7, #c084fc); }
    .inv-fill.if-lav { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    
    /* Activity dots */
    .ad-blue { background: rgba(59, 130, 246, 0.15); }
    .ad-mint { background: rgba(16, 185, 129, 0.15); }
    .ad-blush { background: rgba(168, 85, 247, 0.15); }
    
    /* Empty state styling */
    .empty-state-icon {
        font-size: 3rem;
        opacity: 0.4;
        margin-bottom: 0.75rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .stat-value {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush