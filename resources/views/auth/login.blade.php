<x-guest-layout>

    {{-- 🖼️ ВАШ ЛОГОТИП --}}
    <div class="mb-8 md:mb-16 flex justify-center w-full">
        <x-logo class="w-16 h-10 md:w-[89px] md:h-[59px]" />
    </div>

    {{-- Заголовок формы. Добавлены классы ширины для центрирования --}}
    <h1 class="text-3xl md:text-4xl font-bold text-black/90 mb-8 text-center w-full max-w-md mx-auto">
        {{ __('Войти в аккаунт') }}
    </h1>

    {{-- Сообщения о статусе. Добавлены классы ширины для центрирования --}}
    <x-auth-session-status class="mb-4 p-4 border-2 border-green-300 rounded-[12px] bg-green-50 text-green-700 w-full max-w-md mx-auto" :status="session('status')" />

    {{-- ФОРМА: Является карточкой --}}
    <form method="POST" action="{{ route('login') }}" class="rounded-[20px] border-2 border-gray-300 bg-white p-8 md:p-10 w-full max-w-md mx-auto">
        @csrf

        <div class="mb-5">
            <label for="email" class="block font-medium text-sm text-black/80 mb-1">{{ __('Email') }}</label>
            <input id="email"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <div class="mb-5">
            <label for="password" class="block font-medium text-sm text-black/80 mb-1">{{ __('Пароль') }}</label>
            <input id="password"
                   class="block w-full h-12 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/90 focus:border-black focus:ring-black transition"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-black shadow-sm focus:ring-black" name="remember">
                <span class="ms-2 text-sm text-black/70">{{ __('Запомнить меня') }}</span>
            </label>
        </div>

        {{-- 🔥 ИСПРАВЛЕНО: justify-end заменен на justify-center --}}
        <div class="flex items-center justify-center mt-8">
            <button type="submit"
                    class="h-12 px-6 rounded-[14px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center font-semibold">
                {{ __('Войти') }}
            </button>
        </div>

        @if (Route::has('register'))
            <div class="mt-6 text-center text-sm">
                <span class="text-black/70">{{ __('Нет аккаунта?') }}</span>
                <a href="{{ route('register') }}" class="font-semibold text-black hover:underline ml-1 transition">
                    {{ __('Зарегистрироваться') }}
                </a>
            </div>
        @endif
    </form>

</x-guest-layout>
