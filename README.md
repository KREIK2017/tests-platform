# Tests Platform

Knowledge testing platform built on Laravel 13 with Bootstrap 5 (server-rendered Blade) plus a token-based REST API for an upcoming Next.js SPA.

## Stack

- PHP 8.2+, Laravel 13.x
- MySQL (XAMPP locally), SQLite in-memory for tests
- Bootstrap 5 + Bootstrap Icons via CDN
- Authentication: Laravel Breeze (Blade) for web; Sanctum personal access tokens for API
- Localization: Ukrainian (default) and English; switch at `GET /locale/{locale}`

## Quick start

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The seeder creates two pre-verified accounts:

| Email                 | Password   | Role      |
| --------------------- | ---------- | --------- |
| `admin@test.local`    | `password` | admin     |
| `student@test.local`  | `password` | student   |

It also creates two published tests with 5 questions × 4 answers each.

## Tests

```bash
php artisan test
```

The full suite is 102 tests across:

- Auth: registration with role, role+verified gates, locale switch
- Admin CRUD: tests, questions (with the "exactly 4 answers / exactly 1 correct" invariant), answers (single-correct enforcement on store/update)
- Student flow: index visibility, start/take/finish, score computation, ownership policy
- API: register/login/logout, /me, tests CRUD, attempts start/finish/index, policy gating, hidden `is_correct` for non-admins

## API Endpoints

All endpoints live under `/api/v1/`. Bearer tokens are issued by `register` and `login` and must be sent in the `Authorization: Bearer {token}` header for every protected request.

Errors are always JSON. Codes:

- `401 Unauthorized` — missing or invalid token (or session)
- `403 Forbidden` — auth OK but policy/role/verification denied
- `404 Not Found` — model/route missing
- `422 Unprocessable Entity` — validation errors (`{message, errors: {field: [...]}}`)

CORS is open to `http://localhost:3000` with credentials, so the future Next.js client can call directly.

### Auth

| Method | URL                                       | Auth         | Description                                        |
| ------ | ----------------------------------------- | ------------ | -------------------------------------------------- |
| POST   | `/api/v1/register`                        | guest        | Create account; returns `{user, token}`. Sends a `VerifyEmail` notification. |
| POST   | `/api/v1/login`                           | guest        | Email + password; returns `{user, token}`. 422 on bad credentials. |
| POST   | `/api/v1/logout`                          | sanctum      | Revokes the current bearer token. `204 No Content`. |
| GET    | `/api/v1/me`                              | sanctum      | Current user resource.                             |
| POST   | `/api/v1/email/verification-notification` | sanctum      | Re-sends the verification email. `202 Accepted`.   |
| POST   | `/api/v1/email/verify/{id}/{hash}`        | signed URL   | Marks the user as verified. Used when the email-link arrives at the SPA. |

`register` body:

```json
{
  "name": "Maria",
  "email": "maria@example.com",
  "password": "secret-pass",
  "password_confirmation": "secret-pass",
  "role": "student"
}
```

`login` body:

```json
{ "email": "maria@example.com", "password": "secret-pass" }
```

### Tests

All `tests.*` endpoints require **`auth:sanctum` + `verified`**.

| Method | URL                       | Who           | Notes                                                                 |
| ------ | ------------------------- | ------------- | --------------------------------------------------------------------- |
| GET    | `/api/v1/tests`           | any           | Paginated. Students see only `is_published=true`; admins see all.     |
| POST   | `/api/v1/tests`           | admin         | Creates a test owned by the caller. Returns `201`.                    |
| GET    | `/api/v1/tests/{test}`    | any           | Loads questions and answers. Drafts visible only to the owner.        |
| PUT    | `/api/v1/tests/{test}`    | admin + owner | Updates title/description/is_published.                                |
| DELETE | `/api/v1/tests/{test}`    | admin + owner | Cascades to questions and answers. Returns `204`.                     |

`store` / `update` body:

```json
{ "title": "PHP Basics", "description": "...", "is_published": true }
```

> **Note on `is_correct`** — `AnswerResource` only exposes `is_correct` when the caller is an admin. Students fetching `/api/v1/tests/{id}` see `text` but not `is_correct`, so the SPA can render the test-taking form safely.

### Questions (shallow nested)

| Method | URL                                          | Who           |
| ------ | -------------------------------------------- | ------------- |
| GET    | `/api/v1/tests/{test}/questions`             | view test     |
| POST   | `/api/v1/tests/{test}/questions`             | admin + owner |
| GET    | `/api/v1/questions/{question}`               | admin + owner |
| PUT    | `/api/v1/questions/{question}`               | admin + owner |
| DELETE | `/api/v1/questions/{question}`               | admin + owner |

`store` / `update` body:

```json
{ "text": "What is the correct PHP opening tag?", "order": 1 }
```

`order` is optional on store; if omitted, the question is appended to the end of the test.

### Answers (shallow nested)

| Method | URL                                              | Who           |
| ------ | ------------------------------------------------ | ------------- |
| GET    | `/api/v1/questions/{question}/answers`           | view question |
| POST   | `/api/v1/questions/{question}/answers`           | admin + owner |
| GET    | `/api/v1/answers/{answer}`                       | admin + owner |
| PUT    | `/api/v1/answers/{answer}`                       | admin + owner |
| DELETE | `/api/v1/answers/{answer}`                       | admin + owner |

`store` / `update` body:

```json
{ "text": "<?php", "is_correct": true }
```

If `is_correct: true`, the controller atomically clears the flag from every other answer of the same question, so a question always has at most one correct answer.

### Attempts

| Method | URL                                  | Who                  | Notes                                                                          |
| ------ | ------------------------------------ | -------------------- | ------------------------------------------------------------------------------ |
| POST   | `/api/v1/tests/{test}/attempts`      | any verified user    | Creates a new attempt or returns the existing in-progress one. `201` or `200`. |
| POST   | `/api/v1/attempts/{attempt}/finish`  | attempt owner        | Submits answers, scores, marks completed. Returns the attempt with results.    |
| GET    | `/api/v1/attempts`                   | any verified user    | Students see their own attempts; admins see everyone's.                        |
| GET    | `/api/v1/attempts/{attempt}`         | owner or admin       | Detailed view with picked + correct answers.                                   |

`finish` body — every question of the test must be answered:

```json
{
  "answers": {
    "<question_id>": "<answer_id>",
    "<question_id>": "<answer_id>"
  }
}
```

Each `answer_id` must belong to the question whose id is the key, otherwise the request returns `422`.

### Resource shapes (excerpt)

`UserResource`:

```json
{
  "id": 1, "name": "Maria", "email": "maria@example.com",
  "role": "student", "email_verified_at": "2026-04-29T10:15:00+00:00",
  "created_at": "..."
}
```

`TestResource`:

```json
{
  "id": 1, "title": "...", "description": "...", "is_published": true,
  "user_id": 1, "questions_count": 5,
  "author": { "id": 1, "name": "...", ... },
  "questions": [{ "id": 1, "text": "...", "answers": [...] }]
}
```

`AnswerResource`:

```json
{
  "id": 1, "question_id": 1, "text": "<?php",
  "is_correct": true   // present only when caller is admin
}
```

`AttemptResource`:

```json
{
  "id": 1, "user_id": 2, "test_id": 1,
  "score": 4, "total_questions": 5, "percent": 80,
  "completed_at": "...",
  "student": { ... },
  "test": { ... },
  "attempt_answers": [{ "question_id": 1, "answer_id": 3 }]
}
```

## Mail

Mail is sent through Mailtrap during development. Configure credentials in `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tests-platform.local"
```

The verification email link points to the **web** route (`/verify-email/{id}/{hash}`). API consumers that prefer a dedicated SPA flow can sign their own URLs against the `api.v1.verification.verify` route name and POST them to `/api/v1/email/verify/{id}/{hash}`.

## Project layout

```
app/
├── Http/Controllers/
│   ├── Admin/{Test,Question,Answer}Controller.php   # Blade CRUD
│   ├── Student/TestTakingController.php             # Blade test taking
│   └── Api/V1/{Auth,Test,Question,Answer,Attempt}Controller.php
├── Http/Resources/{User,Test,Question,Answer,Attempt}Resource.php
├── Http/Middleware/{EnsureUserHasRole,SetLocale}.php
├── Models/{User,Test,Question,Answer,Attempt,AttemptAnswer}.php
└── Policies/{Test,Question,Answer,Attempt}Policy.php
lang/{uk,en}/{messages,tests,auth,validation,passwords,pagination}.php
resources/views/{admin,attempts,auth,layouts,profile,tests}/...
routes/{web,api,auth,console}.php
tests/Feature/{Admin,Api,Auth,Student}/...
```
