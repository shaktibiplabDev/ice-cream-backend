@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
    <div class="page-header">
        <h1>
            <small>Found {{ $count }} result{{ $count === 1 ? '' : 's' }}</small>
            Search: "{{ $query }}"
        </h1>
    </div>

    @if($count > 0)
        <div class="glass-card">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            @php
                                $typeColors = [
                                    'Inquiry' => 'sky',
                                    'Distributor' => 'blush',
                                    'Product' => 'mint',
                                    'Warehouse' => 'lavender',
                                ];
                                $color = $typeColors[$result['type']] ?? 'sky';

                                $statusClass = match(strtolower($result['status'])) {
                                    'active', 'new' => 'status-active',
                                    'inactive' => 'status-inactive',
                                    'read' => 'status-in-progress',
                                    'replied' => 'status-resolved',
                                    default => 'status-active',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="status-badge" style="background: var(--accent-gradient); font-size: 0.6875rem;">
                                        {{ $result['type'] }}
                                    </span>
                                </td>
                                <td style="font-weight: 500;">{{ $result['title'] }}</td>
                                <td style="color: var(--text-muted);">{{ $result['subtitle'] }}</td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ ucfirst($result['status']) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8125rem;">{{ $result['date']->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ $result['url'] }}" style="text-decoration: none;">
                                        <span class="action-btn action-view">View →</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="glass-card" style="text-align: center; padding: 4rem;">
            <div class="empty-state-icon">🔍</div>
            <h2 style="margin-bottom: 1rem;">No Results Found</h2>
            <p style="color: var(--text-muted);">
                We couldn't find anything matching "{{ $query }}". Try searching with different keywords.
            </p>
        </div>
    @endif
@endsection
