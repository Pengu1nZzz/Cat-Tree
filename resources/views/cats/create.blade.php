@extends('layouts.app')

@section('title', 'Добавить кошку — CatTree')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Добавить новую кошку</h1>
            <p class="mt-1 text-sm text-gray-500">Заполните данные о кошке и укажите ее родителей при наличии.</p>
        </div>
        <a href="{{ route('cats.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            &larr; Назад к списку
        </a>
    </div>

    <!-- Create Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('cats.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Кличка <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Например: Мурка" class="mt-1 block w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Пол <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">♀ Самка (female)</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">♂ Самец (male)</span>
                    </label>
                </div>
                @error('gender')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Age -->
            <div>
                <label for="age" class="block text-sm font-medium text-gray-700">Возраст (в годах) <span class="text-red-500">*</span></label>
                <input type="number" name="age" id="age" value="{{ old('age') }}" min="0" max="100" required placeholder="Например: 2" class="mt-1 block w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('age') border-red-300 @enderror">
                @error('age')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mother -->
            <div class="pt-4 border-t border-gray-100">
                <label for="mother_id" class="block text-sm font-medium text-gray-700">Мать котенка</label>
                <p class="text-xs text-gray-500 mb-2">Выберите мать среди зарегистрированных самок (необязательно).</p>
                <select name="mother_id" id="mother_id" class="block w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('mother_id') border-red-300 @enderror">
                    <option value="">-- Не указана --</option>
                    @foreach ($mothers as $mother)
                        <option value="{{ $mother->id }}" {{ old('mother_id') == $mother->id ? 'selected' : '' }}>
                            {{ $mother->name }} (Возраст: {{ $mother->age }} г.)
                        </option>
                    @endforeach
                </select>
                @error('mother_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Possible Fathers -->
            <div class="pt-4 border-t border-gray-100">
                <label class="block text-sm font-medium text-gray-700">Множество возможных отцов</label>
                <p class="text-xs text-gray-500 mb-2">В связи с особенностями поведения кошачьих, можно выбрать нескольких возможных отцов среди самцов.</p>
                
                @if ($fathers->count() > 0)
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 border border-gray-200 rounded-lg bg-gray-50">
                        @foreach ($fathers as $father)
                            <label class="inline-flex items-center p-1.5 rounded hover:bg-white cursor-pointer transition">
                                <input type="checkbox" name="father_ids[]" value="{{ $father->id }}" {{ in_array($father->id, old('father_ids', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $father->name }} <span class="text-xs text-gray-400">({{ $father->age }} г.)</span></span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Самцы в базе отсутствуют.</p>
                @endif
                @error('father_ids')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('cats.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Отмена
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition">
                    Сохранить кошку
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
