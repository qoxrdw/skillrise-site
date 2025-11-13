@extends('layouts.app')

@section('content')
    {{-- Основной контейнер с белым фоном и вертикальным отступом --}}
    <div class="py-12 bg-white">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Сфокусированная ширина для формы записи --}}

            {{-- Back link --}}
            <div class="mb-6">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center text-sm font-medium text-black/70 hover:text-black transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            <div class="rounded-[20px] border-2 border-gray-300 bg-white shadow-xl p-8 md:p-10">
                <h1 class="text-2xl md:text-3xl font-bold text-black/90 mb-6">{{ __('Новая голосовая заметка') }}</h1>

                {{-- Сообщения об ошибках и успехе --}}
                @if (session('success'))
                    <div class="mb-4 p-4 border-2 border-green-300 rounded-[12px] bg-green-50 text-green-700">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 border-2 border-red-300 rounded-[12px] bg-red-50 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="voice-note-form" method="POST" action="{{ route('notes.store.voice', $track) }}" enctype="multipart/form-data">
                    @csrf

                    <div id="recorder-ui" class="space-y-6">
                        <div class="text-center p-8 border-2 border-dashed border-gray-300 rounded-[14px] bg-gray-50">
                            {{-- Счетчик времени --}}
                            <p id="timer" class="text-4xl font-mono text-black/90 mb-4">00:00</p>

                            {{-- Статус --}}
                            <p id="status-message" class="text-black/70">{{ __('Нажмите "Запись", чтобы начать') }}</p>

                            {{-- Аудио превью (скрыто по умолчанию) --}}
                            <audio id="audio-preview" controls class="hidden w-full mt-4 bg-white rounded-[10px]"></audio>

                            {{-- ВАЖНО: Ранее здесь был скрытый <input type="file" ... required>.
                                 Его отсутствие исправляет ошибку "An invalid form control..." --}}

                        </div>

                        {{-- Кнопки управления --}}
                        <div class="flex justify-center space-x-4">

                            {{-- Кнопка "Запись" (Основная, зеленая) --}}
                            <button type="button" id="record-button"
                                    class="h-12 px-6 rounded-[14px] border-2 border-green-600 bg-green-600 text-white hover:bg-green-700 hover:border-green-700 transition flex items-center justify-center">
                                <svg id="record-icon" class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span id="record-text">{{ __('Запись') }}</span>
                            </button>

                            {{-- Кнопка "Стоп" (Опасная, красная) --}}
                            <button type="button" id="stop-button" disabled
                                    class="h-12 px-6 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-red-600 hover:border-red-700 hover:text-white transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6l5-3-5-3z"></path></svg>
                                {{ __('Стоп') }}
                            </button>
                        </div>

                        {{-- Кнопка "Сохранить" (Появляется после записи) --}}
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" id="save-button" disabled
                                    class="w-full h-12 px-6 rounded-[14px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('Сохранить голосовую заметку') }}
                            </button>
                        </div>

                        {{-- Сброс (Появляется после записи) --}}
                        <div class="text-center">
                            <button type="button" id="reset-button" disabled class="text-sm text-black/60 hover:text-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('Очистить запись') }}
                            </button>
                        </div>

                    </div>
                </form>

                <div class="mt-8 text-sm text-black/60">
                    <p>{{ __('Совет: Для более надежной работы рекомендуется использовать браузер Chrome или Firefox.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Скрипт для работы с Web Audio API --}}
    <script>
        // Инициализация элементов
        const form = document.getElementById('voice-note-form');
        const recordButton = document.getElementById('record-button');
        const stopButton = document.getElementById('stop-button');
        const saveButton = document.getElementById('save-button');
        const resetButton = document.getElementById('reset-button');
        const audioPreview = document.getElementById('audio-preview');
        // const audioFileInput = document.getElementById('audio-file-input'); // УДАЛЕНО: Этот элемент больше не используется
        const statusMessage = document.getElementById('status-message');
        const timerDisplay = document.getElementById('timer');

        let mediaRecorder;
        let audioChunks = [];
        let audioBlob = null;
        let timerInterval;
        let seconds = 0;

        // --- УПРАВЛЕНИЕ ИНТЕРФЕЙСОМ ---
        function updateUI(state) {
            switch (state) {
                case 'ready':
                    recordButton.disabled = false;
                    stopButton.disabled = true;
                    saveButton.disabled = true;
                    resetButton.disabled = true;
                    audioPreview.classList.add('hidden');
                    audioPreview.src = '';
                    statusMessage.textContent = 'Нажмите "Запись", чтобы начать';
                    timerDisplay.textContent = '00:00';
                    seconds = 0;
                    break;
                case 'recording':
                    recordButton.disabled = true;
                    stopButton.disabled = false;
                    saveButton.disabled = true;
                    resetButton.disabled = true;
                    audioPreview.classList.add('hidden');
                    statusMessage.textContent = 'Запись...';
                    break;
                case 'stopped':
                    recordButton.disabled = false;
                    stopButton.disabled = true;
                    saveButton.disabled = audioBlob === null;
                    resetButton.disabled = audioBlob === null;
                    audioPreview.classList.remove('hidden');
                    statusMessage.textContent = 'Запись готова к сохранению';
                    break;
            }
        }

        // --- ЛОГИКА ТАЙМЕРА ---
        function startTimer() {
            timerDisplay.textContent = '00:00';
            seconds = 0;
            timerInterval = setInterval(() => {
                seconds++;
                const min = String(Math.floor(seconds / 60)).padStart(2, '0');
                const sec = String(seconds % 60).padStart(2, '0');
                timerDisplay.textContent = `${min}:${sec}`;
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
        }

        // --- ОБРАБОТЧИК ЗАПИСИ ---
        recordButton.addEventListener('click', async () => {
            try {
                // Запрос доступа к микрофону
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                // Инициализация MediaRecorder
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', event => {
                    audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    stopTimer();

                    // Создаем Blob
                    const mimeType = mediaRecorder.mimeType || 'audio/webm; codecs=opus';
                    audioBlob = new Blob(audioChunks, { type: mimeType });

                    // Создаем URL для предпросмотра
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;

                    // Обновляем UI
                    updateUI('stopped');

                    // Останавливаем все треки в потоке (чтобы погасить индикатор микрофона)
                    stream.getTracks().forEach(track => track.stop());
                });

                // Начинаем запись
                mediaRecorder.start();
                startTimer();
                updateUI('recording');

            } catch (err) {
                console.error('Ошибка доступа к микрофону:', err);
                statusMessage.textContent = 'Ошибка: Не удалось получить доступ к микрофону.';
                updateUI('ready');
            }
        });

        // --- ОБРАБОТЧИК ОСТАНОВКИ ---
        stopButton.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        });

        // --- ОБРАБОТЧИК СБРОСА ---
        resetButton.addEventListener('click', () => {
            audioBlob = null;
            updateUI('ready');
        });

        // --- ОБРАБОТЧИК ОТПРАВКИ ФОРМЫ (ИСПРАВЛЕННЫЙ ДЛЯ JSON) ---
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!audioBlob) {
                alert('Сначала запишите голосовую заметку!');
                return;
            }

            // Создаем объект File из Blob
            const filename = `voice_note_${Date.now()}.${audioBlob.type.split('/')[1] || 'webm'}`;
            const audioFile = new File([audioBlob], filename, { type: audioBlob.type });

            // Создаем FormData
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('audio', audioFile, filename); // Имя поля 'audio' должно совпадать с валидацией в контроллере

            saveButton.disabled = true;
            statusMessage.textContent = 'Отправка...';

            try {
                // Отправляем данные вручную через fetch API, запрашивая JSON-ответ
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    // !!! КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: Указываем, что ждем JSON
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // !!! КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: Читаем ответ как JSON
                const data = await response.json();

                if (response.ok) {
                    // !!! КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: Используем URL для перенаправления из JSON-ответа контроллера
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        // Запасной вариант на случай, если redirect_url не пришел
                        window.location.href = '{{ route('tracks.show', $track) }}?success=' + encodeURIComponent('Голосовая заметка успешно создана!');
                    }
                } else {
                    // Обработка ошибок сервера из JSON-ответа
                    let errorMessage = data.message || 'Произошла неизвестная ошибка сервера.';

                    // Если это ошибки валидации, Laravel обычно помещает их в data.errors
                    if (data.errors) {
                        // Берем первое сообщение об ошибке из первого поля
                        const firstError = Object.values(data.errors)[0][0];
                        errorMessage = 'Ошибка валидации: ' + firstError;
                    }

                    alert('Ошибка сохранения: ' + errorMessage);
                    console.error('Server error details:', data);
                    updateUI('stopped');
                }
            } catch (error) {
                console.error('Ошибка отправки:', error);
                alert('Ошибка отправки данных. Проверьте соединение или формат ответа сервера.');
                updateUI('stopped');
            }
        });

        // Инициализация при загрузке
        document.addEventListener('DOMContentLoaded', () => {
            updateUI('ready');
        });
    </script>
@endsection
