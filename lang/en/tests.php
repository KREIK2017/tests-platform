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
        'confirm_delete' => 'Delete test ":title"? All questions and answers will be lost.',
        'no_questions' => 'This test has no questions yet.',
    ],

    'student' => [
        'index_title' => 'Available tests',
        'index_lead' => 'Pick a test from the list and click Start when you are ready.',
        'attempts_index_title' => 'My attempts',
        'attempts_index_lead' => 'Your past attempts and their results.',
        'admin_attempts_title' => 'All attempts',
        'admin_attempts_lead' => 'Attempt history across every student.',
        'questions_count' => 'Questions in this test',
        'start_warning_in_progress' => 'You have an unfinished attempt on this test — taking you back to it.',
        'start_warning_completed' => 'You have already taken this test. You can retake it — the previous attempt stays untouched.',
        'previous_score' => 'Previous score: :score of :total',
        'taking_progress' => 'Question :index of :total',
        'take_intro' => 'Pick one answer for each question. You can review your result once finished.',
        'finish_button' => 'Finish test',
        'unanswered_warning' => 'You have not picked an answer for every question.',
        'result_title' => 'Test result',
        'result_score' => ':score of :total',
        'result_percent' => ':percent%',
        'your_answer' => 'Your answer',
        'correct_answer' => 'Correct answer',
        'answer_correct' => 'Correct',
        'answer_wrong' => 'Wrong',
        'attempt_status_in_progress' => 'In progress',
        'attempt_status_completed' => 'Completed',
        'duration' => 'Duration',
        'started' => 'Started',
        'finished' => 'Finished',
    ],

    'admin' => [
        'index_title' => 'Manage tests',
        'index_lead' => 'Browse, create, edit and delete tests.',
        'create_title' => 'New test',
        'edit_title' => 'Edit test',
        'show_title' => 'Test: :title',
        'questions_block' => 'Test questions',
        'placeholder_title' => 'For example: PHP Basics',
        'placeholder_description' => 'A short description of what this test covers…',
        'question_create_title' => 'New question',
        'question_edit_title' => 'Edit question',
        'question_text_placeholder' => 'Type the question…',
        'answers_block' => 'Answer options',
        'answer_text_placeholder' => 'Option text',
        'answer_create_title' => 'New answer option',
        'answer_edit_title' => 'Edit answer option',
        'mark_correct' => 'Mark as correct',
        'is_correct_label' => 'This is the correct answer',
        'confirm_delete_question' => 'Delete this question? All answer options will be removed too.',
        'confirm_delete_answer' => 'Delete this answer option?',
        'order_optional' => 'Order (optional)',
        'add_answer' => 'Add another option',
    ],

];
