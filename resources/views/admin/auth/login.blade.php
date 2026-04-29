<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celesty Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-purple-400 to-pink-400">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Celesty Admin</h2>
                <p class="text-gray-600 mt-2">Login to your account</p>
            </div>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                </div>
                
                <button type="submit" class="w-full bg-purple-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-700 transition">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
