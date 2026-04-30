@php
    $adminName = auth('admin')->user()->name ?? 'Admin User';
    $nameParts = preg_split('/\s+/', trim($adminName));
    $firstName = $nameParts[0] ?? 'Admin';
    $initials = collect($nameParts)
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('') ?: 'AD';
    $navNewInquiries = \App\Models\Inquiry::where('status', 'new')->count();
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Celesty Admin - @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-dark: #0a0c10;
            --bg-elevated: #111316;
            --bg-card: rgba(22, 26, 32, 0.8);
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-medium: rgba(255, 255, 255, 0.1);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.4);
            --accent-primary: #4f46e5;
            --accent-primary-light: #6366f1;
            --accent-secondary: #a855f7;
            --accent-gradient: linear-gradient(135deg, #4f46e5, #7c3aed);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --sidebar-w: 260px;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 18px;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 20px rgba(79, 70, 229, 0.15);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background orbs */
        .bg-orb {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .orb-1 {
            position: absolute;
            top: -20%;
            left: -10%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.25) 0%, rgba(79, 70, 229, 0) 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: float 25s ease-in-out infinite;
        }

        .orb-2 {
            position: absolute;
            bottom: -15%;
            right: -10%;
            width: 55%;
            height: 55%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, rgba(168, 85, 247, 0) 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: float 20s ease-in-out infinite reverse;
        }

        .orb-3 {
            position: absolute;
            top: 40%;
            left: 30%;
            width: 40%;
            height: 40%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(59, 130, 246, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            animation: float 30s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -40px) scale(1.08); }
            66% { transform: translate(-30px, 30px) scale(0.95); }
        }

        /* Grid overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 1;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: rgba(17, 19, 22, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .logo {
            padding: 1.5rem 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }

        .logo a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--accent-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        .nav-section {
            flex: 1;
            padding: 0 0.75rem;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 0.75rem 0.75rem 0.5rem;
            display: block;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: var(--radius-md);
            margin-bottom: 0.25rem;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: rgba(79, 70, 229, 0.15);
            color: var(--text-primary);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 28px;
            background: var(--accent-gradient);
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 28px;
            font-size: 1.125rem;
            text-align: center;
        }

        .nav-badge {
            margin-left: auto;
            background: linear-gradient(135deg, #4f46e5, #a855f7);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.125rem 0.5rem;
            border-radius: 20px;
            min-width: 22px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid var(--border-subtle);
            margin-top: 1rem;
        }

        .user-card {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: all 0.2s;
            color: inherit;
            text-align: left;
        }

        .user-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--border-medium);
        }

        .user-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 0.8125rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        .logout-text {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Main content */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(10, 12, 16, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.875rem 1.75rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .topbar-title {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .topbar-title span {
            color: white;
            font-weight: 600;
        }

        .search-bar {
            flex: 1;
            max-width: 320px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: 40px;
            font-size: 0.8125rem;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s;
        }

        .search-bar input:focus {
            border-color: var(--accent-primary);
            background: rgba(255, 255, 255, 0.08);
        }

        .search-bar input::placeholder {
            color: var(--text-muted);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
            color: var(--text-secondary);
            text-decoration: none;
            position: relative;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--border-medium);
            transform: scale(1.02);
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: var(--accent-secondary);
            border-radius: 50%;
            border: 1px solid var(--bg-dark);
        }

        .profile-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: 40px;
        }

        .profile-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .profile-name {
            font-size: 0.8125rem;
            font-weight: 500;
        }

        /* Mobile nav */
        .mobile-nav {
            display: none;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 1rem;
            background: rgba(17, 19, 22, 0.95);
            border-bottom: 1px solid var(--border-subtle);
        }

        .mobile-nav .nav-item {
            white-space: nowrap;
            margin-bottom: 0;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            padding: 0.5rem 1rem;
        }

        /* Content */
        .content {
            padding: 1.75rem;
            flex: 1;
        }

        /* Flash messages */
        .flash-alert {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 0.875rem 1.125rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
        }

        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.875rem 1.125rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
        }

        /* Glass card - for all panels */
        .glass-card {
            background: rgba(22, 26, 32, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* Form styles */
        .form-panel {
            background: rgba(22, 26, 32, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }

        .form-panel-head {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .form-panel-head h2 {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .form-panel-body {
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .form-grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-field.full {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .required-label {
            color: #f87171;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }

        .optional-label {
            color: var(--text-muted);
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .form-input[readonly] {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .form-help {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .form-error {
            font-size: 0.7rem;
            color: #f87171;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-subtle);
        }

        /* Buttons - Global text-decoration reset for links styled as buttons */
        a[class^="btn-"], a[class*=" btn-"] {
            text-decoration: none !important;
        }

        .btn-primary {
            background: var(--accent-gradient);
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-glow);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            color: #f87171;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #f87171;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: rgba(22, 26, 32, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 1.25rem;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(79, 70, 229, 0.3);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            background: rgba(79, 70, 229, 0.15);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .stat-trend {
            font-size: 0.7rem;
            padding: 0.125rem 0.5rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-trend.up {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }

        .stat-trend.down {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        /* Page header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .page-header h1 small {
            display: block;
            font-size: 0.8125rem;
            font-weight: 400;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .date-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: 40px;
            font-size: 0.8125rem;
            color: var(--text-secondary);
        }

        /* Tables - for inquiries & distributors */
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius-lg);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 0.875rem 1rem;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-subtle);
        }

        .data-table td {
            padding: 0.875rem 1rem;
            font-size: 0.8125rem;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-secondary);
        }

        .data-table tbody tr {
            transition: background 0.2s;
            cursor: pointer;
        }

        .data-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 20px;
            font-size: 0.6875rem;
            font-weight: 600;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-new {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }
        .status-new::before { background: #3b82f6; }

        .status-in-progress {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }
        .status-in-progress::before { background: #f59e0b; }

        .status-resolved {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .status-resolved::before { background: #10b981; }

        .status-closed {
            background: rgba(107, 114, 128, 0.15);
            color: #9ca3af;
        }
        .status-closed::before { background: #6b7280; }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .status-active::before { background: #10b981; }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }
        .status-inactive::before { background: #ef4444; }

        /* Action buttons in tables */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .action-view {
            color: #60a5fa;
        }
        .action-view:hover {
            color: #93c5fd;
        }

        .action-edit {
            color: #fbbf24;
        }
        .action-edit:hover {
            color: #fcd34d;
        }

        .action-delete {
            color: #f87171;
        }
        .action-delete:hover {
            color: #fca5a5;
        }

        /* Filter bar */
        .filter-bar {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-select {
            padding: 0.5rem 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 0.8125rem;
            cursor: pointer;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Detail view styles */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .detail-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .detail-value {
            font-size: 0.9375rem;
            color: var(--text-primary);
        }

        /* Conversation thread for inquiries */
        .conversation-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.25rem;
        }

        .thread-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .thread-message {
            background: rgba(22, 26, 32, 0.6);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 1rem;
        }

        .thread-message.outbound {
            background: rgba(79, 70, 229, 0.1);
            border-color: rgba(79, 70, 229, 0.3);
        }

        .thread-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        .thread-subject {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .thread-body {
            font-size: 0.8125rem;
            color: var(--text-secondary);
            line-height: 1.5;
            white-space: pre-wrap;
        }

        .reply-form {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-subtle);
        }

        /* Mid / Bottom rows for dashboard */
        .mid-row, .bottom-row {
            display: grid;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .mid-row { grid-template-columns: 1fr 360px; }
        .bottom-row { grid-template-columns: 1fr 320px; }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-subtle);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .card-head h2 {
            font-size: 1rem;
            font-weight: 600;
        }

        .card-head p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Chart */
        .chart-wrap {
            padding: 1rem 1.25rem;
        }

        .chart-legend {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .leg-item {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .leg-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .leg-dot.sky { background: #4f46e5; }
        .leg-dot.mint { background: #10b981; }
        .leg-dot.blush { background: #a855f7; }

        .chart-area {
            height: 160px;
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .bar-group {
            flex: 1;
            display: flex;
            gap: 0.125rem;
            align-items: flex-end;
            height: 100%;
        }

        .bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            transition: all 0.2s;
            min-height: 4px;
        }

        .bar.sky { background: linear-gradient(to top, #4f46e5, #818cf8); }
        .bar.mint { background: linear-gradient(to top, #10b981, #34d399); }
        .bar.blush { background: linear-gradient(to top, #a855f7, #c084fc); }

        .chart-labels {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .chart-labels span {
            flex: 1;
            text-align: center;
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        /* Seller items */
        .sellers-inner, .activity-inner {
            padding: 1rem;
        }

        .seller-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 0.5rem;
        }

        .seller-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .seller-img {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .si-1 { background: linear-gradient(135deg, #4f46e5, #818cf8); }
        .si-2 { background: linear-gradient(135deg, #10b981, #34d399); }
        .si-3 { background: linear-gradient(135deg, #a855f7, #c084fc); }
        .si-4 { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .si-5 { background: linear-gradient(135deg, #ef4444, #f87171); }

        .seller-info {
            flex: 1;
        }

        .seller-name {
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .seller-cat {
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        .seller-sales {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .seller-rank {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6875rem;
            font-weight: 700;
        }

        .r1, .r2, .r3 { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .r4, .r5 { background: rgba(255, 255, 255, 0.1); color: var(--text-muted); }

        /* Activity items */
        .activity-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.625rem 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .act-dot {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .ad-blue { background: rgba(59, 130, 246, 0.15); }
        .ad-mint { background: rgba(16, 185, 129, 0.15); }
        .ad-blush { background: rgba(168, 85, 247, 0.15); }

        .act-content {
            flex: 1;
        }

        .act-title {
            font-size: 0.8125rem;
        }

        .act-time {
            font-size: 0.6875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* Location tools */
        .location-tools {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 0.75rem;
            align-items: end;
        }

        .map-canvas {
            height: 320px;
            width: 100%;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-subtle);
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
        }

        /* Inventory bars */
        .inv-bars {
            padding: 1rem;
        }

        .inv-item {
            margin-bottom: 1rem;
        }

        .inv-head {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.375rem;
            font-size: 0.75rem;
        }

        .inv-name {
            color: var(--text-secondary);
        }

        .inv-pct {
            color: var(--text-primary);
            font-weight: 600;
        }

        .inv-track {
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .inv-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }

        .if-sky { background: linear-gradient(90deg, #4f46e5, #818cf8); }
        .if-mint { background: linear-gradient(90deg, #10b981, #34d399); }
        .if-blush { background: linear-gradient(90deg, #a855f7, #c084fc); }

        /* Chip & Action Links - Remove underlines */
        .chip, a.chip, .action-link, a.action-link, .seller-item, a.seller-item {
            text-decoration: none !important;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            font-size: 0.75rem;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .chip:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .mid-row, .bottom-row, .conversation-layout { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .mobile-nav { display: flex; }
            .stats-grid { grid-template-columns: 1fr; }
            .form-grid, .form-grid.three { grid-template-columns: 1fr; }
            .topbar { padding: 0.75rem 1rem; }
            .search-bar { max-width: 100%; order: 3; margin-top: 0.5rem; }
            .profile-name { display: none; }
            .content { padding: 1rem; }
            .location-tools { grid-template-columns: 1fr; }
            .filter-bar { flex-direction: column; align-items: stretch; }
        }

        @media print {
            .sidebar, .topbar, .mobile-nav, .bg-orb, .grid-overlay { display: none; }
            .main { margin-left: 0; }
            .content { padding: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="bg-orb">
        <div class="orb-1"></div>
        <div class="orb-2"></div>
        <div class="orb-3"></div>
    </div>
    <div class="grid-overlay"></div>

    <aside class="sidebar">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <div class="logo-icon">✨</div>
                <span class="logo-text">Celesty</span>
            </a>
        </div>

        <nav class="nav-section">
            <span class="nav-label">Overview</span>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                Dashboard
            </a>
            <a href="{{ route('admin.map.index') }}" class="nav-item {{ request()->routeIs('admin.map.*') ? 'active' : '' }}">
                <span class="nav-icon">🗺️</span>
                Territory Map
            </a>

            <span class="nav-label" style="margin-top: 1rem;">Leads & Partners</span>
            <a href="{{ route('admin.inquiries.index') }}" class="nav-item {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                <span class="nav-icon">📩</span>
                Inquiries
                @if($navNewInquiries > 0)
                    <span class="nav-badge">{{ $navNewInquiries }}</span>
                @endif
            </a>
            <a href="{{ route('admin.distributors.index') }}" class="nav-item {{ request()->routeIs('admin.distributors.*') ? 'active' : '' }}">
                <span class="nav-icon">🚚</span>
                Distributors
            </a>

            <span class="nav-label" style="margin-top: 1rem;">Products & Inventory</span>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span class="nav-icon">📦</span>
                Products
            </a>
            <a href="{{ route('admin.warehouses.index') }}" class="nav-item {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                <span class="nav-icon">🏭</span>
                Warehouses
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="nav-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                Inventory
            </a>

            <span class="nav-label" style="margin-top: 1rem;">Quick Actions</span>
            <a href="{{ route('admin.inventory.low-stock') }}" class="nav-item {{ request()->routeIs('admin.inventory.low-stock') ? 'active' : '' }}">
                <span class="nav-icon">⚠️</span>
                Low Stock Alert
            </a>
            <a href="{{ route('admin.inventory.history') }}" class="nav-item {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                <span class="nav-icon">📜</span>
                Stock History
            </a>
            <a href="{{ route('admin.warehouses.create') }}" class="nav-item {{ request()->routeIs('admin.warehouses.create') ? 'active' : '' }}">
                <span class="nav-icon">➕</span>
                Add Warehouse
            </a>

            <span class="nav-label" style="margin-top: 1rem;">External</span>
            <a href="{{ url('/') }}" class="nav-item" target="_blank">
                <span class="nav-icon">🏠</span>
                Public Site
            </a>
        </nav>

        <form method="POST" action="{{ route('admin.logout') }}" class="sidebar-footer">
            @csrf
            <button type="submit" class="user-card">
                <div class="user-avatar-sm">{{ $initials }}</div>
                <div class="user-info">
                    <div class="user-name">{{ $adminName }}</div>
                    <div class="user-role">Admin Console</div>
                </div>
                <span class="logout-text">Logout</span>
            </button>
        </form>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">{{ $greeting }}, <span>{{ $firstName }}</span></div>

            <form class="search-bar" action="{{ route('admin.search') }}" method="GET">
                <span class="search-icon">🔍</span>
                <input type="search" name="q" placeholder="Search inquiries, distributors..." value="{{ request('q') }}">
            </form>

            <div class="topbar-right">
                <a class="icon-btn" href="{{ route('admin.inquiries.index') }}">
                    📩
                    @if($navNewInquiries > 0)
                        <span class="notif-dot"></span>
                    @endif
                </a>
                <a class="icon-btn" href="{{ route('admin.distributors.create') }}">➕</a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                    @csrf
                    <button class="icon-btn" type="submit" style="background: transparent;">⏻</button>
                </form>
                <div class="profile-chip">
                    <div class="profile-avatar">{{ $initials }}</div>
                    <span class="profile-name">{{ $firstName }}</span>
                </div>
            </div>
        </header>

        <nav class="mobile-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.map.index') }}" class="nav-item {{ request()->routeIs('admin.map.*') ? 'active' : '' }}">Map</a>
            <a href="{{ route('admin.inquiries.index') }}" class="nav-item {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">Inquiries</a>
            <a href="{{ route('admin.distributors.index') }}" class="nav-item {{ request()->routeIs('admin.distributors.*') ? 'active' : '' }}">Distributors</a>
            <a href="{{ route('admin.inventory.index') }}" class="nav-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">Inventory</a>
            <a href="{{ route('admin.warehouses.index') }}" class="nav-item {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">Warehouses</a>
        </nav>

        <main class="content">
            @if(session('success'))
                <div class="flash-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>