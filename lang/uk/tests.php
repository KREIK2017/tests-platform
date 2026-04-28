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

    'admin' => [
        'index_title' => 'Керування тестами',
        'index_lead' => 'Перегляд, створення, редагування та видалення тестів.',
        'create_title' => 'Новий тест',
        'edit_title' => 'Редагування тесту',
        'show_title' => 'Тест: :title',
        'questions_block' => 'Питання тесту',
        'placeholder_title' => 'Наприклад: Основи PHP',
        'placeholder_description' => 'Короткий опис того, що перевіряє цей тест…',
    ],

];
