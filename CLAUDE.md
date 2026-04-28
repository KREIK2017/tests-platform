# Платформа тестування знань — Контекст проєкту

## ⚠️ Важливо для Claude Code

Цей файл — твій основний контекст. Завжди дотримуйся цих правил і вимог. Перед будь-якою дією перевіряй чи відповідає вона цьому документу.

## Технічний стек

- **PHP:** 8.2+
- **Laravel:** 13.x
- **БД:** MySQL (через XAMPP)
- **Frontend (Blade частина):** Bootstrap 5
- **Frontend (SPA частина):** Next.js 15 (App Router, TypeScript)
- **API:** Laravel Sanctum (token-based)
- **Email:** Mailtrap для розробки, Gmail SMTP для продакшену
- **Локалізація:** українська (uk) + англійська (en)

## Структура папок

```
tests-platform/
├── (Laravel backend - тут зараз працюємо)
└── frontend/  (Next.js додаток - створимо пізніше)
```

База даних: `tests_platform` (MySQL, utf8mb4_unicode_ci)

## Завдання — повне ТЗ

Створити повноцінну платформу для тестування знань з двома частинами:
1. **Full-stack Laravel застосунок** з Blade.php фронтендом
2. **Next.js SPA**, що підключається до Laravel через API і повторює весь функціонал

### Обов'язкові вимоги (з ТЗ)

- ✅ Аутентифікація користувачів (реєстрація, логін, логаут)
- ✅ Email-верифікація на **реальну пошту** під час реєстрації
- ✅ Авторизація за ролями (мінімум 2 ролі)
- ✅ Мінімум 2 сутності з CRUD операціями
- ✅ Зв'язок між сутностями: один-до-багатьох АБО один-до-одного
- ✅ Локалізація українською та англійською мовами
- ✅ API, що дублює весь функціонал Blade-частини
- ✅ Next.js підключається до API і повторює весь функціонал

## Бізнес-логіка — Платформа тестування знань

### Ролі користувачів

1. **admin** — створює, редагує, видаляє тести і питання, бачить статистику всіх спроб
2. **student** — переглядає список тестів, проходить тести, бачить свої результати

### Сутності та зв'язки

```
User (1) ─────< (N) Test           [Один admin створює багато Tests]
Test (1) ─────< (N) Question        [Один Test має багато Questions]
Question (1) ──< (N) Answer         [Одне Question має багато Answers, одна правильна]
User (1) ─────< (N) Attempt         [Один student робить багато Attempts]
Test (1) ─────< (N) Attempt         [Один Test проходять багато разів]
```

**Сутності, що задовольняють вимогу ТЗ "мінімум 2 сутності зі зв'язком 1-до-багатьох":**
- Test → Question (1-до-багатьох)
- Question → Answer (1-до-багатьох)
- User → Attempt (1-до-багатьох)

### Структура таблиць (детально)

**users** (вже є в Laravel за замовчуванням, додаємо `role`)
- id, name, email, password, email_verified_at
- role: ENUM('admin', 'student'), default 'student'
- timestamps

**tests**
- id
- title (string)
- description (text, nullable)
- user_id (FK → users.id, той хто створив — admin)
- is_published (boolean, default false)
- timestamps

**questions**
- id
- test_id (FK → tests.id, ON DELETE CASCADE)
- text (text)
- order (integer, default 0)
- timestamps

**answers**
- id
- question_id (FK → questions.id, ON DELETE CASCADE)
- text (string)
- is_correct (boolean, default false)
- timestamps

**attempts**
- id
- user_id (FK → users.id) — student, що проходив
- test_id (FK → tests.id)
- score (integer) — кількість правильних відповідей
- total_questions (integer)
- completed_at (timestamp, nullable)
- timestamps

**attempt_answers** (зберігає що саме обрав студент — щоб показати результат детально)
- id
- attempt_id (FK → attempts.id, ON DELETE CASCADE)
- question_id (FK → questions.id)
- answer_id (FK → answers.id) — те що обрав студент
- timestamps

### Сторінки (Blade)

**Публічні:**
- `/` — головна, опис платформи + кнопки "Login" / "Register"
- `/login` — форма логіну
- `/register` — форма реєстрації (з полем role: student/admin)
- `/email/verify` — сторінка про необхідність підтвердити email

**Для авторизованих (any role):**
- `/dashboard` — кабінет (різний контент для admin / student)
- `/profile` — редагування профілю
- `/logout` — POST форма логауту

**Тільки admin:**
- `/admin/tests` — список усіх тестів (з кнопками CRUD)
- `/admin/tests/create` — форма створення тесту
- `/admin/tests/{id}` — перегляд тесту з питаннями
- `/admin/tests/{id}/edit` — редагування тесту
- `/admin/tests/{id}/questions/create` — додати питання до тесту
- `/admin/tests/{id}/questions/{qid}/edit` — редагувати питання
- `/admin/attempts` — статистика всіх спроб

**Тільки student:**
- `/tests` — список доступних (опублікованих) тестів
- `/tests/{id}` — перегляд тесту перед початком
- `/tests/{id}/start` — POST, починає спробу
- `/tests/{id}/take` — проходження тесту (форма з питаннями)
- `/tests/{id}/finish` — POST, завершує спробу і рахує результат
- `/attempts` — мої спроби
- `/attempts/{id}` — детальний результат конкретної спроби

### API endpoints (Sanctum)

Префікс: `/api/v1/`

**Auth:**
- POST `/api/v1/register` — реєстрація + відправка email
- POST `/api/v1/login` — логін, повертає токен
- POST `/api/v1/logout` — логаут (auth required)
- GET `/api/v1/me` — поточний користувач
- POST `/api/v1/email/verify/{id}/{hash}` — підтвердження email через посилання
- POST `/api/v1/email/verification-notification` — повторне надсилання

**Tests (admin only для CUD, всі для R):**
- GET `/api/v1/tests` — список (для student — тільки published)
- POST `/api/v1/tests` — створити (admin)
- GET `/api/v1/tests/{id}` — отримати тест з питаннями
- PUT `/api/v1/tests/{id}` — оновити (admin, owner)
- DELETE `/api/v1/tests/{id}` — видалити (admin, owner)

**Questions:**
- POST `/api/v1/tests/{id}/questions` — додати питання (admin)
- PUT `/api/v1/questions/{id}` — оновити (admin)
- DELETE `/api/v1/questions/{id}` — видалити (admin)

**Answers:**
- POST `/api/v1/questions/{id}/answers` — додати відповідь (admin)
- PUT `/api/v1/answers/{id}` — оновити (admin)
- DELETE `/api/v1/answers/{id}` — видалити (admin)

**Attempts:**
- POST `/api/v1/tests/{id}/attempts` — почати спробу (student)
- POST `/api/v1/attempts/{id}/finish` — завершити (student)
- GET `/api/v1/attempts` — мої спроби (student) / усі (admin)
- GET `/api/v1/attempts/{id}` — детальна спроба

## Локалізація

Файли мов: `lang/uk/` і `lang/en/`

Файли:
- `auth.php` — повідомлення авторизації
- `validation.php` — повідомлення валідації
- `messages.php` — UI тексти (заголовки, кнопки)
- `tests.php` — специфічні тексти для тестів

Перемикач мов у navbar: дві кнопки UA / EN, що змінюють `app.locale` через сесію.

## Email-верифікація

Використовуємо стандартний Laravel `MustVerifyEmail`:
1. Модель `User` імплементує `MustVerifyEmail`
2. Маршрути верифікації згідно з документацією Laravel
3. Кастомізований шаблон листа (двома мовами)

**Налаштування:**
- Розробка: `MAIL_MAILER=smtp` + Mailtrap credentials
- Перед здачею: переключаємо на Gmail SMTP

## Стандарти коду

- **Контролери:** тонкі, бізнес-логіка в Service-класах або моделях
- **Валідація:** через Form Requests (`php artisan make:request`)
- **API відповіді:** через API Resources (`php artisan make:resource`)
- **Авторизація:** через Policies (`php artisan make:policy`)
- **Іменування:** PSR-12, моделі в однині (Test, Question), таблиці в множині (tests, questions)

## ❌ Чого НЕ робити

- НЕ використовувати Tailwind (тільки Bootstrap 5 через CDN)
- НЕ використовувати Livewire / Inertia — тільки чистий Blade
- НЕ використовувати Vue/React всередині Laravel — Next.js буде окремим проєктом
- НЕ створювати зайві сутності, яких немає в цьому ТЗ
- НЕ ігнорувати локалізацію — кожен користувацький текст має бути в lang-файлах
- НЕ забувати про авторизацію (Policies) на кожному CRUD-маршруті

## Порядок розробки (важливо!)

Працюємо чітко за етапами. Кожен етап = окремий коміт у git.

1. **Етап 1:** Моделі + міграції + сідери
2. **Етап 2:** Аутентифікація (Breeze) + ролі + email-верифікація
3. **Етап 3:** Layout + Bootstrap 5 + локалізація
4. **Етап 4:** CRUD для тестів (admin)
5. **Етап 5:** CRUD для питань і відповідей (admin)
6. **Етап 6:** Логіка проходження тестів (student)
7. **Етап 7:** API (Sanctum) — повторення усього функціоналу
8. **Етап 8:** Next.js фронтенд

Після завершення кожного етапу — `git add . && git commit -m "Stage X: description"`.
