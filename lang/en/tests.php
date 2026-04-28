<?php

return [

    'singular' => 'Test',
    'plural' => 'Tests',

    'fields' => [
        'title' => 'Test title',
        'description' => 'Description',
        'is_published' => 'Published',
        'author' => 'Author',
        'questions_count' => 'Questions count',
        'created_at' => 'Created',
    ],

    'status' => [
        'published' => 'Published',
        'draft' => 'Draft',
    ],

    'actions' => [
        'create' => 'Create test',
        'edit' => 'Edit test',
        'delete' => 'Delete test',
        'view' => 'View',
        'start' => 'Start test',
        'finish' => 'Finish test',
        'add_question' => 'Add question',
    ],

    'questions' => [
        'singular' => 'Question',
        'plural' => 'Questions',
        'fields' => [
            'text' => 'Question text',
            'order' => 'Order',
        ],
        'add' => 'Add question',
        'edit' => 'Edit question',
    ],

    'answers' => [
        'singular' => 'Answer',
        'plural' => 'Answer options',
        'fields' => [
            'text' => 'Answer text',
            'is_correct' => 'Correct',
        ],
        'add' => 'Add option',
        'select_correct' => 'Mark the correct answer',
    ],

    'attempts' => [
        'singular' => 'Attempt',
        'plural' => 'Attempts',
        'fields' => [
            'student' => 'Student',
            'test' => 'Test',
            'score' => 'Score',
            'total' => 'Total questions',
            'completed_at' => 'Completed',
            'result' => 'Result',
        ],
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'score_format' => ':score of :total',
    ],

    'messages' => [
        'no_tests' => 'No tests yet.',
        'no_published_tests' => 'No published tests yet.',
        'no_attempts' => 'You have not taken any test yet.',
        'no_admin_attempts' => 'No student has taken any test yet.',
        'created' => 'Test created.',
        'updated' => 'Test updated.',
        'deleted' => 'Test deleted.',
        'question_created' => 'Question added.',
        'question_updated' => 'Question updated.',
        'question_deleted' => 'Question deleted.',
        'attempt_finished' => 'Test finished. Your score: :score of :total.',
    ],

];
