<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celesty Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-purple-600">Celesty Admin</h1>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-700' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.inquiries.*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-700' }}">
                                Inquiries
                            </a>
                            <a href="{{ route('admin.distributors.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.distributors.*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-700' }}">
                                Distributors
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
