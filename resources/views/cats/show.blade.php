@extends('layouts.app')

@section('title', 'Кошка ' . $cat->name . ' — CatTree')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center text-sm text-gray-500 mb-1 space-x-2">
                <a href="{{ route('cats.index') }}" class="hover:text-indigo-600">Кошки</a>
                <span>&rsaquo;</span>
                <span class="text-gray-900 font-medium">{{ $cat->name }}</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <span>{{ $cat->gender === 'female' ? '🌸' : '⚡' }}</span>
                <span>{{ $cat->name }}</span>
            </h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('cats.edit', $cat->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Изменить
            </a>
            <form action="{{ route('cats.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить кошку {{ $cat->name }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-200 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition">
                    <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Удалить
                </button>
            </form>
        </div>
    </div>

    <!-- Main Cat Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Пол</span>
                <div class="mt-1">
                    @if ($cat->gender === 'female')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-pink-100 text-pink-800">
                            ♀ Самка (female)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                            ♂ Самец (male)
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Возраст</span>
                <span class="mt-1 text-2xl font-bold text-gray-900 block">
                    {{ $cat->age }} {{ $cat->age === 1 ? 'год' : ($cat->age >= 2 && $cat->age <= 4 ? 'года' : 'лет') }}
                </span>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Дата регистрации</span>
                <span class="mt-1 text-sm font-medium text-gray-700 block">
                    {{ $cat->createdAt ? \Carbon\Carbon::parse($cat->createdAt)->translatedFormat('d.m.Y H:i') : '—' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Family Tree & Pedigree Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Mother Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-2 pb-3 border-b border-gray-100">
                <span class="text-xl">🌸</span>
                <h2 class="text-lg font-bold text-gray-900">Мать</h2>
            </div>
            <div class="mt-4">
                @if ($cat->mother)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-pink-50 border border-pink-100">
                        <div>
                            <a href="{{ route('cats.show', $cat->mother->id) }}" class="font-semibold text-pink-900 hover:underline">
                                {{ $cat->mother->name }}
                            </a>
                            <p class="text-xs text-pink-700">Возраст: {{ $cat->mother->age }} г.</p>
                        </div>
                        <a href="{{ route('cats.show', $cat->mother->id) }}" class="text-xs font-medium text-pink-700 hover:text-pink-900">
                            Перейти &rarr;
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Мать не указана</p>
                @endif
            </div>
        </div>

        <!-- Possible Fathers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-2 pb-3 border-b border-gray-100">
                <span class="text-xl">⚡</span>
                <h2 class="text-lg font-bold text-gray-900">Возможные отцы</h2>
            </div>
            <div class="mt-4 space-y-2">
                @if (!empty($cat->possibleFathers) && $cat->possibleFathers->count() > 0)
                    @foreach ($cat->possibleFathers as $father)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50 border border-blue-100">
                            <div>
                                <a href="{{ route('cats.show', $father->id) }}" class="font-semibold text-blue-900 hover:underline">
                                    {{ $father->name }}
                                </a>
                                <p class="text-xs text-blue-700">Возраст: {{ $father->age }} г.</p>
                            </div>
                            <a href="{{ route('cats.show', $father->id) }}" class="text-xs font-medium text-blue-700 hover:text-blue-900">
                                Перейти &rarr;
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400 italic">Возможные отцы не указаны</p>
                @endif
            </div>
        </div>

    </div>

    <!-- Offspring (Потомство) Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <span class="text-xl">🐾</span>
                <h2 class="text-lg font-bold text-gray-900">Потомство (котята)</h2>
            </div>
            <span class="text-sm text-gray-500">Всего: {{ $children->count() }}</span>
        </div>

        <div class="mt-4">
            @if ($children->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($children as $child)
                        <a href="{{ route('cats.show', $child->id) }}" class="block p-3 rounded-lg border border-gray-200 hover:border-indigo-500 hover:shadow-sm transition bg-gray-50 hover:bg-white">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">{{ $child->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $child->gender->value === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $child->gender->value === 'female' ? '♀ Самка' : '♂ Самец' }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Возраст: {{ $child->age }} г.</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Потомство отсутствует</p>
            @endif
        </div>
    </div>

</div>
@endsection
