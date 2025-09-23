<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Убедитесь, что модель User импортирована
use App\Models\Track; // Убедитесь, что модель Track импортирована
use App\Models\Note;  // Убедитесь, что модель Note импортирована

class DefaultTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Получаем всех пользователей, у которых нет треков
        // Или конкретного пользователя, если вы вызываете сидер для одного пользователя
        $users = User::doesntHave('tracks')->get();

        foreach ($users as $user) {
            $this->createDefaultTrackForUser($user);
        }
    }

    /**
     * Создает трек-пример и заметки-примеры для указанного пользователя.
     *
     * @param User $user
     * @return void
     */
    public function createDefaultTrackForUser(User $user)
    {
        // Создаем трек-пример
        $defaultTrack = Track::create([
            'user_id' => $user->id,
            'name' => 'Мой первый учебный трек',
            'description' => 'Добро пожаловать! Этот трек создан для демонстрации возможностей SkillRise. Здесь вы найдете несколько заметок-примеров, чтобы начать работу.',
        ]);

        // Создаем заметки-примеры для этого трека
        Note::create([
            'track_id' => $defaultTrack->id,
            // Содержимое заметки в формате чистого HTML
            // Списки заменены на параграфы для лучшей совместимости с парсером Quill
            'content' => '<h1>Как создавать заметки</h1>' .
                '<p>Привет! Это ваша первая заметка. Вы можете редактировать ее, нажимая на кнопку "Редактировать заметку".</p>' .
                '<p>Используйте панель инструментов сверху, чтобы форматировать текст, добавлять заголовки, списки и ссылки.</p>' . '<p></p>' .
                '<h2>Попробуйте это:</h2>' .
                '<p>• Создать новый заголовок</p>' . // Изменено с <li> на <p>
                '<p>• Добавить <strong>жирный текст</strong></p>' . // Изменено с <li> на <p>
                '<p>• Вставить <a href="https://example.com" target="_blank" rel="noopener noreferrer">ссылку на внешний ресурс</a></p>' . // Изменено с <li> на <p>
                '<p><em>Удачной работы!</em></p>',
        ]);

        Note::create([
            'track_id' => $defaultTrack->id,

            // Содержимое заметки в формате чистого HTML
            // Списки заменены на параграфы для лучшей совместимости с парсером Quill
            'content' => '<h1>Изучение нового языка программирования</h1>' . '<p></p>' .
                '<h2>План изучения Python:</h2>' .
                '<p>1. Основы синтаксиса и типы данных.</p>' .
                '<p>2. Условные операторы и циклы.</p>' .
                '<p>3. Функции и модули.</p>' .
                '<p>4. Объектно-ориентированное программирование.</p>' . '<p></p>' .
                '<h2>Ресурсы:</h2>' .
                '<p>• <a href="https://docs.python.org/3/" target="_blank" rel="noopener noreferrer">Официальная документация Python</a></p>' . // Изменено с <li> на <p>
                '<p>• Курсы на Coursera/Udemy</p>', // Изменено с <li> на <p>
        ]);
    }
}
