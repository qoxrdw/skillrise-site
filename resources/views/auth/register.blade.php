<x-guest-layout>

    {{-- 🖼️ ВАШ ЛОГОТИП --}}
    <div class="mb-8 md:mb-16 flex justify-center w-full">
        <x-logo class="w-16 h-10 md:w-[89px] md:h-[59px]" />
    </div>

    {{-- Заголовок формы --}}
    <h1 class="text-3xl md:text-4xl font-bold text-black/90 mb-8 text-center w-full max-w-md mx-auto">
        {{ __('Регистрация') }}
    </h1>

    {{-- ФОРМА: Является карточкой, как на странице логина --}}
    <form method="POST" action="{{ route('register') }}" class="rounded-[20px] border-2 border-gray-300 bg-white p-8 md:p-10 w-full max-w-md mx-auto">
        @csrf

        <div class="mb-5">
            <label for="name" class="block font-medium text-sm text-black/80 mb-1">{{ __('Имя') }}</label>
            <input id="name"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600" />
        </div>

        <div class="mt-4 mb-5">
            <label for="email" class="block font-medium text-sm text-black/80 mb-1">{{ __('Email') }}</label>
            <input id="email"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <div class="mt-4 mb-5">
            <label for="password" class="block font-medium text-sm text-black/80 mb-1">{{ __('Пароль') }}</label>
            <input id="password"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="password"
                   name="password"
                   required
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <div class="mt-4 mb-8">
            <label for="password_confirmation" class="block font-medium text-sm text-black/80 mb-1">{{ __('Подтверждение пароля') }}</label>
            <input id="password_confirmation"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        {{-- Кнопка "Зарегистрироваться" по центру --}}
        <div class="flex items-center justify-center">
            <button type="submit"
                    class="h-12 px-6 rounded-[14px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center font-semibold">
                {{ __('Зарегистрироваться') }}
            </button>
        </div>

        {{-- Ссылка "Уже зарегистрированы?" в стилистике сайта --}}
        <div class="mt-6 text-center text-sm">
            <span class="text-black/70">{{ __('Уже зарегистрированы?') }}</span>
            <a href="{{ route('login') }}" class="font-semibold text-black hover:underline ml-1 transition">
                {{ __('Войти') }}
            </a>
        </div>
    </form>
</x-guest-layout>
