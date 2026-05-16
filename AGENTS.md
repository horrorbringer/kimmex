# Repository Guidelines

## Project Structure & Module Organization
This is a Laravel 13 application with a Filament 5 admin panel. Core backend code lives in `app/`, with domain models in `app/Models`, controllers in `app/Http/Controllers`, services in `app/Services`, and admin resources in `app/Filament`. Public Blade views are in `resources/views`, assets are in `resources/css` and `resources/js`, and static files live under `public/`. Database migrations, seeders, and factories are in `database/`, while tests are in `tests/`.

## Build, Test, and Development Commands
- `composer install`: install PHP dependencies.
- `npm install`: install frontend dependencies.
- `composer run dev`: start the local dev workflow with the queue listener and Vite.
- `npm run dev`: run the Vite dev server only.
- `npm run build`: compile production assets.
- `php artisan migrate`: run database migrations.
- `php artisan db:seed`: load seed data.
- `composer test` or `php artisan test`: run the test suite.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF line endings, 4-space indentation for PHP/JS, and 2 spaces for YAML. Use PSR-4 namespaces and Laravel conventions for class names (`ProjectResource`, `ManageOrgChart`, `NewsArticle`). Keep Blade templates descriptive and grouped by feature, such as `resources/views/pages/services/show.blade.php`. For Filament, keep resource classes paired with `Pages/`, `Schemas/`, and `Tables/` subdirectories.

## Testing Guidelines
Use PHPUnit through Laravel’s test runner. Feature tests belong in `tests/Feature`, unit tests in `tests/Unit`, and follow the `*Test.php` naming pattern. Add tests for new routes, service behavior, and model logic. Run targeted tests with `php artisan test --filter=Name` when iterating on one area.

## Commit & Pull Request Guidelines
Recent commits use short, imperative summaries such as `update ux/ui news page` or `feat: add AI stats dashboard component`. Keep commit messages concise and task-focused. Pull requests should include a short description, linked issue or task when available, and screenshots for UI changes. Mention any migration, seeder, or environment steps reviewers must run.

## Security & Configuration Tips
Do not commit `.env`, secrets, generated build output, or local runtime artifacts. For schema changes, add a new migration instead of editing old ones. If you change admin resources or translations, verify the affected Filament pages and localized content paths before merging.
