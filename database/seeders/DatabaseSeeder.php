<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@test.local',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $testsData = [
            [
                'title' => 'Основи PHP',
                'description' => 'Базовий тест з мови PHP: синтаксис, типи, функції.',
                'questions' => [
                    [
                        'text' => 'Який тег використовується для відкриття PHP-коду?',
                        'answers' => [
                            ['text' => '<?php',     'is_correct' => true],
                            ['text' => '<script>',  'is_correct' => false],
                            ['text' => '<%',        'is_correct' => false],
                            ['text' => '<php>',     'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Який тип даних повертає функція count()?',
                        'answers' => [
                            ['text' => 'int',    'is_correct' => true],
                            ['text' => 'string', 'is_correct' => false],
                            ['text' => 'array',  'is_correct' => false],
                            ['text' => 'bool',   'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Як оголосити константу в PHP?',
                        'answers' => [
                            ['text' => 'define("X", 1)', 'is_correct' => true],
                            ['text' => 'const = 1',      'is_correct' => false],
                            ['text' => 'let X = 1',      'is_correct' => false],
                            ['text' => 'var X = 1',      'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Який оператор перевіряє рівність значення І типу?',
                        'answers' => [
                            ['text' => '===',  'is_correct' => true],
                            ['text' => '==',   'is_correct' => false],
                            ['text' => '=',    'is_correct' => false],
                            ['text' => '<=>',  'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Що повертає var_dump(null)?',
                        'answers' => [
                            ['text' => 'NULL',         'is_correct' => true],
                            ['text' => 'string(0) ""', 'is_correct' => false],
                            ['text' => 'int(0)',       'is_correct' => false],
                            ['text' => 'bool(false)',  'is_correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Основи Laravel',
                'description' => 'Тест з фреймворку Laravel: маршрути, моделі, контролери.',
                'questions' => [
                    [
                        'text' => 'Яка команда створює нову модель Eloquent?',
                        'answers' => [
                            ['text' => 'php artisan make:model', 'is_correct' => true],
                            ['text' => 'php artisan new:model',  'is_correct' => false],
                            ['text' => 'composer make:model',    'is_correct' => false],
                            ['text' => 'php artisan generate',   'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Де описуються маршрути веб-частини у Laravel 13?',
                        'answers' => [
                            ['text' => 'routes/web.php',     'is_correct' => true],
                            ['text' => 'routes/routes.php',  'is_correct' => false],
                            ['text' => 'app/Routes.php',     'is_correct' => false],
                            ['text' => 'config/routes.php',  'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Яка ORM використовується в Laravel за замовчуванням?',
                        'answers' => [
                            ['text' => 'Eloquent',   'is_correct' => true],
                            ['text' => 'Doctrine',   'is_correct' => false],
                            ['text' => 'Propel',     'is_correct' => false],
                            ['text' => 'ActiveRecord', 'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Яка команда запускає міграції?',
                        'answers' => [
                            ['text' => 'php artisan migrate',         'is_correct' => true],
                            ['text' => 'php artisan db:migrate',      'is_correct' => false],
                            ['text' => 'php artisan migration:run',   'is_correct' => false],
                            ['text' => 'composer migrate',            'is_correct' => false],
                        ],
                    ],
                    [
                        'text' => 'Який пакет надає token-based API-аутентифікацію?',
                        'answers' => [
                            ['text' => 'Sanctum',  'is_correct' => true],
                            ['text' => 'Breeze',   'is_correct' => false],
                            ['text' => 'Jetstream','is_correct' => false],
                            ['text' => 'Fortify',  'is_correct' => false],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($testsData as $testData) {
            $test = Test::create([
                'title' => $testData['title'],
                'description' => $testData['description'],
                'user_id' => $admin->id,
                'is_published' => true,
            ]);

            foreach ($testData['questions'] as $i => $questionData) {
                $question = Question::create([
                    'test_id' => $test->id,
                    'text' => $questionData['text'],
                    'order' => $i + 1,
                ]);

                foreach ($questionData['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'text' => $answerData['text'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }
        }
    }
}
