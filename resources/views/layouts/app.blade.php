<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CatTree — Учет кошек')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('cats.index') }}" class="flex items-center space-x-2 text-indigo-600 font-bold text-xl hover:text-indigo-700 transition">
                    <span class="text-2xl">🐾</span>
                    <span>CatTree</span>
                </a>
                <nav class="hidden md:flex space-x-4">
                    <a href="{{ route('cats.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('cats.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Список кошек
                    </a>
                    <a href="{{ route('cats.create') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('cats.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Добавить кошку
                    </a>
                </nav>
            </div>

            <div>
                <a href="{{ route('cats.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Новая кошка
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Пожалуйста, исправьте следующие ошибки:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            CatTree &copy; {{ date('Y') }} — Система учета кошек и генеалогических связей
        </div>
    </footer>

</body>
</html>
