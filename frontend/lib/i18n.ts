export type Locale = "uk" | "en";

export const SUPPORTED_LOCALES: Locale[] = ["uk", "en"];
export const DEFAULT_LOCALE: Locale = "uk";

type Dict = Record<string, string>;

const uk: Dict = {
  // Navigation / app shell
  "app.name": "Tests Platform",
  "app.tagline": "Платформа для тестування знань",
  "nav.home": "Головна",
  "nav.dashboard": "Кабінет",
  "nav.tests": "Тести",
  "nav.attempts": "Спроби",
  "nav.my_attempts": "Мої спроби",
  "nav.admin_attempts": "Усі спроби",
  "nav.admin_tests": "Керування тестами",
  "nav.login": "Увійти",
  "nav.register": "Реєстрація",
  "nav.logout": "Вийти",
  "nav.profile": "Профіль",

  "common.save": "Зберегти",
  "common.cancel": "Скасувати",
  "common.delete": "Видалити",
  "common.edit": "Редагувати",
  "common.create": "Створити",
  "common.back": "Назад",
  "common.submit": "Надіслати",
  "common.confirm": "Підтвердити",
  "common.loading": "Завантаження…",
  "common.empty": "Поки нічого немає.",
  "common.actions": "Дії",
  "common.error": "Сталася помилка",
  "common.required": "Обов'язкове поле",

  // Welcome
  "welcome.title": "Перевір свої знання",
  "welcome.subtitle":
    "Створюй тести як адміністратор або проходь їх як студент. Усе просто, швидко й українською.",
  "welcome.cta_register": "Зареєструватися",
  "welcome.cta_login": "Увійти",
  "welcome.feat.admin_title": "Адміністратор",
  "welcome.feat.admin_text":
    "Створюй тести, додавай питання та варіанти відповідей, переглядай статистику.",
  "welcome.feat.student_title": "Студент",
  "welcome.feat.student_text":
    "Проходь опубліковані тести й одразу бачиш свій результат.",
  "welcome.feat.i18n_title": "Дві мови",
  "welcome.feat.i18n_text":
    "Інтерфейс доступний українською та англійською — перемикай у будь-який момент.",

  // Auth
  "auth.login_title": "Вхід",
  "auth.register_title": "Реєстрація",
  "auth.name": "Ім'я",
  "auth.email": "Email",
  "auth.password": "Пароль",
  "auth.password_confirmation": "Підтвердження пароля",
  "auth.role": "Роль",
  "auth.role_student": "Студент",
  "auth.role_admin": "Адміністратор",
  "auth.no_account": "Немає облікового запису?",
  "auth.already_registered": "Вже зареєстровані?",
  "auth.verify_title": "Підтвердження email",
  "auth.verify_intro":
    "Дякуємо за реєстрацію! Ми надіслали тобі лист із посиланням для підтвердження пошти. Якщо лист не прийшов — натисни нижче.",
  "auth.verify_resent": "Нове посилання надіслано на твій email.",
  "auth.verify_resend": "Надіслати знову",

  // Dashboard
  "dashboard.greeting": "Привіт, {name}!",
  "dashboard.admin_title": "Панель адміністратора",
  "dashboard.admin_lead": "Керуй тестами та переглядай статистику спроб.",
  "dashboard.student_title": "Кабінет студента",
  "dashboard.student_lead": "Обирай тест зі списку та проходь.",
  "dashboard.create_test": "Створити тест",
  "dashboard.create_test_lead": "Додати новий тест із питаннями.",
  "dashboard.all_tests": "Усі тести",
  "dashboard.all_tests_lead": "Перегляд, редагування та видалення тестів.",
  "dashboard.attempts": "Статистика спроб",
  "dashboard.attempts_lead": "Хто, коли і з яким результатом проходив.",
  "dashboard.available_tests": "Доступні тести",
  "dashboard.available_tests_lead": "Список опублікованих тестів.",
  "dashboard.my_attempts": "Мої спроби",
  "dashboard.my_attempts_lead": "Історія твоїх проходжень і результати.",

  // Tests admin
  "tests.fields.title": "Назва тесту",
  "tests.fields.description": "Опис",
  "tests.fields.is_published": "Опубліковано",
  "tests.fields.author": "Автор",
  "tests.status.published": "Опубліковано",
  "tests.status.draft": "Чернетка",
  "tests.actions.create": "Створити тест",
  "tests.actions.edit": "Редагувати тест",
  "tests.actions.delete": "Видалити тест",
  "tests.actions.view": "Переглянути",
  "tests.actions.start": "Розпочати тест",
  "tests.actions.add_question": "Додати питання",
  "tests.admin.index_title": "Керування тестами",
  "tests.admin.index_lead": "Перегляд, створення, редагування та видалення тестів.",
  "tests.admin.create_title": "Новий тест",
  "tests.admin.edit_title": "Редагування тесту",
  "tests.admin.questions_block": "Питання тесту",
  "tests.admin.placeholder_title": "Наприклад: Основи PHP",
  "tests.admin.placeholder_description": "Короткий опис того, що перевіряє цей тест…",
  "tests.admin.question_create_title": "Нове питання",
  "tests.admin.question_edit_title": "Редагування питання",
  "tests.admin.question_text_placeholder": "Сформулюйте питання…",
  "tests.admin.answers_block": "Варіанти відповіді",
  "tests.admin.answer_text_placeholder": "Текст варіанту",
  "tests.admin.is_correct_label": "Це правильна відповідь",
  "tests.admin.confirm_delete_test":
    "Видалити цей тест? Усі питання та варіанти відповідей буде втрачено.",
  "tests.admin.confirm_delete_question":
    "Видалити це питання? Усі варіанти відповідей теж буде видалено.",
  "tests.admin.order_optional": "Порядок (необов'язково)",

  "tests.questions.fields.text": "Текст питання",
  "tests.questions.fields.order": "Порядок",
  "tests.answers.singular": "Варіант",
  "tests.answers.select_correct": "Позначте правильну відповідь",

  // Student / attempts
  "tests.student.index_title": "Доступні тести",
  "tests.student.index_lead":
    "Обери тест зі списку та натисни «Почати», коли будеш готовий.",
  "tests.student.questions_count": "Питань у тесті",
  "tests.student.attempts_index_title": "Мої спроби",
  "tests.student.attempts_index_lead": "Історія твоїх проходжень із результатами.",
  "tests.student.admin_attempts_title": "Усі спроби",
  "tests.student.admin_attempts_lead": "Історія проходжень усіх студентів.",
  "tests.student.start_warning_in_progress":
    "У тебе вже є незавершена спроба цього тесту — повертаємо до неї.",
  "tests.student.start_warning_completed":
    "Ти вже проходив цей тест. Можеш пройти знову — попередня спроба не зміниться.",
  "tests.student.previous_score": "Попередній результат: {score} з {total}",
  "tests.student.taking_progress": "Питання {index} з {total}",
  "tests.student.take_intro":
    "Обери одну відповідь у кожному питанні. Можеш переглянути результат після завершення.",
  "tests.student.finish_button": "Завершити тест",
  "tests.student.unanswered_warning":
    "Ти ще не обрав відповідь у деяких питаннях.",
  "tests.student.result_title": "Результат тесту",
  "tests.student.result_score": "{score} з {total}",
  "tests.student.your_answer": "Твоя відповідь",
  "tests.student.correct_answer": "Правильна відповідь",
  "tests.student.answer_correct": "Правильно",
  "tests.student.answer_wrong": "Неправильно",
  "tests.student.attempt_status_in_progress": "У процесі",
  "tests.student.attempt_status_completed": "Завершено",

  "tests.messages.no_published_tests": "Опублікованих тестів поки немає.",
  "tests.messages.no_tests": "Поки немає жодного тесту.",
  "tests.messages.no_questions": "У цьому тесті ще немає питань.",
  "tests.messages.no_attempts": "Ви ще не проходили жодного тесту.",
  "tests.messages.no_admin_attempts": "Жоден студент ще не проходив тестів.",
};

const en: Dict = {
  "app.name": "Tests Platform",
  "app.tagline": "Knowledge testing platform",
  "nav.home": "Home",
  "nav.dashboard": "Dashboard",
  "nav.tests": "Tests",
  "nav.attempts": "Attempts",
  "nav.my_attempts": "My attempts",
  "nav.admin_attempts": "All attempts",
  "nav.admin_tests": "Manage tests",
  "nav.login": "Log in",
  "nav.register": "Register",
  "nav.logout": "Log out",
  "nav.profile": "Profile",

  "common.save": "Save",
  "common.cancel": "Cancel",
  "common.delete": "Delete",
  "common.edit": "Edit",
  "common.create": "Create",
  "common.back": "Back",
  "common.submit": "Submit",
  "common.confirm": "Confirm",
  "common.loading": "Loading…",
  "common.empty": "Nothing here yet.",
  "common.actions": "Actions",
  "common.error": "Something went wrong",
  "common.required": "Required",

  "welcome.title": "Test your knowledge",
  "welcome.subtitle":
    "Build tests as an administrator or take them as a student. Simple, fast, bilingual.",
  "welcome.cta_register": "Sign up",
  "welcome.cta_login": "Log in",
  "welcome.feat.admin_title": "Administrator",
  "welcome.feat.admin_text":
    "Create tests, add questions and answer options, review statistics.",
  "welcome.feat.student_title": "Student",
  "welcome.feat.student_text":
    "Take published tests and see your result instantly.",
  "welcome.feat.i18n_title": "Two languages",
  "welcome.feat.i18n_text":
    "The interface is available in Ukrainian and English — switch any time.",

  "auth.login_title": "Log in",
  "auth.register_title": "Sign up",
  "auth.name": "Name",
  "auth.email": "Email",
  "auth.password": "Password",
  "auth.password_confirmation": "Confirm password",
  "auth.role": "Role",
  "auth.role_student": "Student",
  "auth.role_admin": "Administrator",
  "auth.no_account": "No account yet?",
  "auth.already_registered": "Already registered?",
  "auth.verify_title": "Verify your email",
  "auth.verify_intro":
    "Thanks for signing up! We sent you an email with a verification link. If it didn't arrive, click below.",
  "auth.verify_resent": "A new verification link has been sent to your email.",
  "auth.verify_resend": "Resend verification email",

  "dashboard.greeting": "Hello, {name}!",
  "dashboard.admin_title": "Administrator panel",
  "dashboard.admin_lead": "Manage tests and review attempt statistics.",
  "dashboard.student_title": "Student dashboard",
  "dashboard.student_lead": "Pick a test from the list and start.",
  "dashboard.create_test": "Create a test",
  "dashboard.create_test_lead": "Add a new test with questions.",
  "dashboard.all_tests": "All tests",
  "dashboard.all_tests_lead": "View, edit and delete tests.",
  "dashboard.attempts": "Attempt statistics",
  "dashboard.attempts_lead": "Who took what and when, with their score.",
  "dashboard.available_tests": "Available tests",
  "dashboard.available_tests_lead": "List of published tests.",
  "dashboard.my_attempts": "My attempts",
  "dashboard.my_attempts_lead": "Your past attempts and results.",

  "tests.fields.title": "Test title",
  "tests.fields.description": "Description",
  "tests.fields.is_published": "Published",
  "tests.fields.author": "Author",
  "tests.status.published": "Published",
  "tests.status.draft": "Draft",
  "tests.actions.create": "Create test",
  "tests.actions.edit": "Edit test",
  "tests.actions.delete": "Delete test",
  "tests.actions.view": "View",
  "tests.actions.start": "Start test",
  "tests.actions.add_question": "Add question",
  "tests.admin.index_title": "Manage tests",
  "tests.admin.index_lead": "Browse, create, edit and delete tests.",
  "tests.admin.create_title": "New test",
  "tests.admin.edit_title": "Edit test",
  "tests.admin.questions_block": "Test questions",
  "tests.admin.placeholder_title": "For example: PHP Basics",
  "tests.admin.placeholder_description": "A short description of what this test covers…",
  "tests.admin.question_create_title": "New question",
  "tests.admin.question_edit_title": "Edit question",
  "tests.admin.question_text_placeholder": "Type the question…",
  "tests.admin.answers_block": "Answer options",
  "tests.admin.answer_text_placeholder": "Option text",
  "tests.admin.is_correct_label": "This is the correct answer",
  "tests.admin.confirm_delete_test":
    "Delete this test? All questions and answers will be lost.",
  "tests.admin.confirm_delete_question":
    "Delete this question? All answer options will be removed too.",
  "tests.admin.order_optional": "Order (optional)",

  "tests.questions.fields.text": "Question text",
  "tests.questions.fields.order": "Order",
  "tests.answers.singular": "Option",
  "tests.answers.select_correct": "Mark the correct answer",

  "tests.student.index_title": "Available tests",
  "tests.student.index_lead":
    "Pick a test from the list and click Start when you are ready.",
  "tests.student.questions_count": "Questions in this test",
  "tests.student.attempts_index_title": "My attempts",
  "tests.student.attempts_index_lead": "Your past attempts and their results.",
  "tests.student.admin_attempts_title": "All attempts",
  "tests.student.admin_attempts_lead": "Attempt history across every student.",
  "tests.student.start_warning_in_progress":
    "You have an unfinished attempt on this test — taking you back to it.",
  "tests.student.start_warning_completed":
    "You have already taken this test. You can retake it — the previous attempt stays untouched.",
  "tests.student.previous_score": "Previous score: {score} of {total}",
  "tests.student.taking_progress": "Question {index} of {total}",
  "tests.student.take_intro":
    "Pick one answer for each question. You can review your result once finished.",
  "tests.student.finish_button": "Finish test",
  "tests.student.unanswered_warning":
    "You have not picked an answer for every question.",
  "tests.student.result_title": "Test result",
  "tests.student.result_score": "{score} of {total}",
  "tests.student.your_answer": "Your answer",
  "tests.student.correct_answer": "Correct answer",
  "tests.student.answer_correct": "Correct",
  "tests.student.answer_wrong": "Wrong",
  "tests.student.attempt_status_in_progress": "In progress",
  "tests.student.attempt_status_completed": "Completed",

  "tests.messages.no_published_tests": "No published tests yet.",
  "tests.messages.no_tests": "No tests yet.",
  "tests.messages.no_questions": "This test has no questions yet.",
  "tests.messages.no_attempts": "You have not taken any test yet.",
  "tests.messages.no_admin_attempts": "No student has taken any test yet.",
};

const dictionaries: Record<Locale, Dict> = { uk, en };

export function translate(
  locale: Locale,
  key: string,
  params?: Record<string, string | number>,
): string {
  const dict = dictionaries[locale] ?? dictionaries[DEFAULT_LOCALE];
  let value = dict[key] ?? dictionaries[DEFAULT_LOCALE][key] ?? key;

  if (params) {
    for (const [k, v] of Object.entries(params)) {
      value = value.replace(new RegExp(`\\{${k}\\}`, "g"), String(v));
    }
  }
  return value;
}
