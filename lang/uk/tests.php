<?php

return [

    'singular' => 'Тест',
    'plural' => 'Тести',

    'fields' => [
        'title' => 'Назва тесту',
        'description' => 'Опис',
        'is_published' => 'Опубліковано',
        'author' => 'Автор',
        'questions_count' => 'Кількість питань',
        'created_at' => 'Створено',
    ],

    'status' => [
        'published' => 'Опубліковано',
        'draft' => 'Чернетка',
    ],

    'actions' => [
        'create' => 'Створити тест',
        'edit' => 'Редагувати тест',
        'delete' => 'Видалити тест',
        'view' => 'Переглянути',
        'start' => 'Розпочати тест',
        'finish' => 'Завершити тест',
        'add_question' => 'Додати питання',
    ],

    'questions' => [
        'singular' => 'Питання',
        'plural' => 'Питання',
        'fields' => [
            'text' => 'Текст питання',
            'order' => 'Порядок',
        ],
        'add' => 'Додати питання',
        'edit' => 'Редагувати питання',
    ],

    'answers' => [
        'singular' => 'Відповідь',
        'plural' => 'Варіанти відповіді',
        'fields' => [
            'text' => 'Текст відповіді',
            'is_correct' => 'Правильна',
        ],
        'add' => 'Додати варіант',
        'select_correct' => 'Позначте правильну відповідь',
    ],

    'attempts' => [
        'singular' => 'Спроба',
        'plural' => 'Спроби',
        'fields' => [
            'student' => 'Студент',
            'test' => 'Тест',
            'score' => 'Бали',
            'total' => 'Усього питань',
            'completed_at' => 'Завершено',
            'result' => 'Результат',
        ],
        'in_progress' => 'У процесі',
        'completed' => 'Завершено',
        'score_format' => ':score з :total',
    ],

    'messages' => [
        'no_tests' => 'Поки немає жодного тесту.',
        'no_published_tests' => 'Опублікованих тестів поки немає.',
        'no_attempts' => 'Ви ще не проходили жодного тесту.',
        'no_admin_attempts' => 'Жоден студент ще не проходив тестів.',
        'created' => 'Тест створено.',
        'updated' => 'Тест оновлено.',
        'deleted' => 'Тест видалено.',
        'question_created' => 'Питання додано.',
        'question_updated' => 'Питання оновлено.',
        'question_deleted' => 'Питання видалено.',
        'attempt_finished' => 'Тест завершено. Ваш результат: :score з :total.',
        'confirm_delete' => 'Видалити тест ":title"? Усі питання й варіанти відповідей буде втрачено.',
        'no_questions' => 'У цьому тесті ще немає питань.',
    ],

    'student' => [
        'index_title' => 'Доступні тести',
        'index_lead' => 'Обери тест зі списку та натисни «Почати», коли будеш готовий.',
        'attempts_index_title' => 'Мої спроби',
        'attempts_index_lead' => 'Історія твоїх проходжень із результатами.',
        'admin_attempts_title' => 'Усі спроби',
        'admin_attempts_lead' => 'Історія проходжень усіх студентів.',
        'questions_count' => 'Питань у тесті',
        'start_warning_in_progress' => 'У тебе вже є незавершена спроба цього тесту — повертаємо до неї.',
        'start_warning_completed' => 'Ти вже проходив цей тест. Можеш пройти знову — попередня спроба не зміниться.',
        'previous_score' => 'Попередній результат: :score з :total',
        'taking_progress' => 'Питання :index з :total',
        'take_intro' => 'Обери одну відповідь у кожному питанні. Можеш переглянути результат після завершення.',
        'finish_button' => 'Завершити тест',
        'unanswered_warning' => 'Ти ще не обрав відповідь у деяких питаннях.',
        'result_title' => 'Результат тесту',
        'result_score' => ':score з :total',
        'result_percent' => ':percent%',
        'your_answer' => 'Твоя відповідь',
        'correct_answer' => 'Правильна відповідь',
        'answer_correct' => 'Правильно',
        'answer_wrong' => 'Неправильно',
        'attempt_status_in_progress' => 'У процесі',
        'attempt_status_completed' => 'Завершено',
        'duration' => 'Тривалість',
        'started' => 'Початок',
        'finished' => 'Завершення',
    ],

    'admin' => [
        'index_title' => 'Керування тестами',
        'index_lead' => 'Перегляд, створення, редагування та видалення тестів.',
        'create_title' => 'Новий тест',
        'edit_title' => 'Редагування тесту',
        'show_title' => 'Тест: :title',
        'questions_block' => 'Питання тесту',
        'placeholder_title' => 'Наприклад: Основи PHP',
        'placeholder_description' => 'Короткий опис того, що перевіряє цей тест…',
        'question_create_title' => 'Нове питання',
        'question_edit_title' => 'Редагування питання',
        'question_text_placeholder' => 'Сформулюйте питання…',
        'answers_block' => 'Варіанти відповіді',
        'answer_text_placeholder' => 'Текст варіанту',
        'answer_create_title' => 'Новий варіант відповіді',
        'answer_edit_title' => 'Редагування варіанту',
        'mark_correct' => 'Позначити як правильну',
        'is_correct_label' => 'Це правильна відповідь',
        'confirm_delete_question' => 'Видалити це питання? Усі варіанти відповідей теж буде видалено.',
        'confirm_delete_answer' => 'Видалити цей варіант відповіді?',
        'order_optional' => 'Порядок (необов\'язково)',
        'add_answer' => 'Додати ще варіант',
    ],

];
