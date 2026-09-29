@extends('layouts.app')

@section('title', 'Список кошек — CatTree')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Список кошек</h1>
            <p class="mt-1 text-sm text-gray-500">Всего записей: {{ $cats->total() }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('cats.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Добавить кошку
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <form method="GET" action="{{ route('cats.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Поиск по кличке</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Например: Барсик" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Пол</label>
                    <select name="gender" id="gender" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Все</option>
                        <option value="male" {{ ($filters['gender'] ?? '') === 'male' ? 'selected' : '' }}>Самец (male)</option>
                        <option value="female" {{ ($filters['gender'] ?? '') === 'female' ? 'selected' : '' }}>Самка (female)</option>
                    </select>
                </div>

                <!-- Exact Age -->
                <div>
                    <label for="age" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Точный возраст (лет)</label>
                    <input type="number" min="0" max="100" name="age" id="age" value="{{ $filters['age'] ?? '' }}" placeholder="Например: 3" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Age Range -->
                <div class="flex space-x-2">
                    <div class="w-1/2">
                        <label for="min_age" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Возраст от</label>
                        <input type="number" min="0" max="100" name="min_age" id="min_age" value="{{ $filters['min_age'] ?? '' }}" placeholder="От" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-1/2">
                        <label for="max_age" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Возраст до</label>
                        <input type="number" min="0" max="100" name="max_age" id="max_age" value="{{ $filters['max_age'] ?? '' }}" placeholder="До" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <div class="text-xs text-gray-400">
                    Фильтрация по критериям ТЗ
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('cats.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Сбросить
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        Применить фильтры
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Cats Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($cats->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Кличка</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Пол</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Возраст</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Мать</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Возможные отцы</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($cats as $cat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-lg mr-2">{{ $cat->gender === 'female' ? '🌸' : '⚡' }}</span>
                                        <a href="{{ route('cats.show', $cat->id) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">
                                            {{ $cat->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($cat->gender === 'female')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            ♀ Самка
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            ♂ Самец
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cat->age }} {{ $cat->age === 1 ? 'год' : ($cat->age >= 2 && $cat->age <= 4 ? 'года' : 'лет') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if ($cat->mother)
                                        <a href="{{ route('cats.show', $cat->mother->id) }}" class="text-indigo-600 hover:underline">
                                            {{ $cat->mother->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if (!empty($cat->possibleFathers) && $cat->possibleFathers->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($cat->possibleFathers as $father)
                                                <a href="{{ route('cats.show', $father->id) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                                                    {{ $father->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('cats.show', $cat->id) }}" class="text-gray-600 hover:text-gray-900" title="Просмотр">
                                        Просмотр
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('cats.edit', $cat->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        Изменить
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('cats.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить кошку {{ $cat->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($cats->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $cats->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <span class="text-4xl">🐱</span>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Кошки не найдены</h3>
                <p class="mt-1 text-sm text-gray-500">Попробуйте изменить параметры фильтрации или добавить новую кошку.</p>
                <div class="mt-6">
                    <a href="{{ route('cats.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                        Добавить кошку
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
