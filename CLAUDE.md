# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Kimmex is a Laravel 13 application with a Filament 5 admin panel for managing corporate content including projects, services, employees, documents, news, job postings, and organizational structure.

## Commands

### Development

```bash
composer install                    # Install PHP dependencies
npm install                         # Install JS dependencies
npm run dev                         # Start Vite dev server
npm run build                       # Build assets for production
composer run dev                    # Start dev servers (app, queue, logs, Vite)
php artisan migrate                 # Run database migrations
php artisan db:seed                 # Run database seeders
```

### Testing

```bash
composer test                       # Run all tests
php artisan test                    # Run all tests
php artisan test --filter=TestName  # Run specific test
php artisan test --path=Feature     # Run feature tests only
php artisan test --path=Unit        # Run unit tests only
```

### Filament Commands

```bash
php artisan filament:make-resource  # Create Filament resource
php artisan filament:make-widget    # Create Filament widget
php artisan filament:make-form      # Create Filament form
```

## Architecture

### Tech Stack
- **Backend**: Laravel 13, PHP 8.3+
- **Admin Panel**: Filament 5
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS 4
- **Database**: MySQL/PostgreSQL with Eloquent ORM
- **Build**: Vite (JS/CSS bundling)

### Directory Structure

```
app/
├── Filament/           # Admin panel resources, pages, widgets
│   ├── Resources/      # CRUD resources (Employees, Projects, Documents, etc.)
│   └── Pages/          # Custom admin pages (ManageOrgChart, etc.)
├── Models/             # Eloquent models
├── Enums/              # PHP enums (e.g., ProjectStatus)
└── Livewire/           # Livewire components (if any)

resources/
├── views/
│   ├── filament/       # Admin panel views
│   ├── pages/          # Public page views
│   ├── components/     # Blade components
│   └── layouts/        # Layout templates
```

### Key Patterns

**Filament Resources** follow a consistent structure:
- `Resource.php` - Main resource class with Eloquent model binding
- `Pages/` - Create, Edit, List page classes
- `Schemas/` - Form schema definitions
- `Tables/` - Table column and filter definitions

**Translations**: The app uses `spatie/laravel-translatable` for multi-language content. Translatable models use the `HasTranslations` trait and store translations as JSON.

**Activity Logging**: Uses `spatie/laravel-activitylog` and `pxlrbt/filament-activity-log` for audit trails on model changes.

**Organization Chart**: Custom implementation in `app/Filament/Pages/ManageOrgChart.php` with tree-based `OrgUnit` model supporting parent-child relationships.

### Models

Core domain models:
- `Project`, `ProjectCategory`, `ProjectImage` - Project portfolio
- `Service` - Company services
- `Employee`, `Department`, `OrgUnit` - Organizational structure
- `Document`, `DocumentCategory` - Document management
- `NewsArticle` - Company news
- `JobPosting`, `JobApplication` - Careers
- `Partner`, `Testimonial` - Business partners and testimonials
- `Inquiry` - Contact form submissions
- `Policy` - Company policies
- `SystemSetting` - Application configuration

## Livewire Guidelines

This project uses Livewire (vendor/livewire/livewire). Key conventions:

- JavaScript directives use `wire:` prefix (e.g., `wire:click`, `wire:model`)
- Component hooks extend `ComponentHook` class
- Use `store($this)` for component state management
- Directives registered in `js/directives/index.js`
- Features registered in `src/LivewireServiceProvider.php`

## Important Notes

- Never commit built assets (`dist/` or `public/build/`)
- Always verify current branch before committing: `git branch`
- For database changes, create new migrations: `php artisan make:migration`
- Filament resources should use the schema pattern in `Schemas/` directory
- When working with translatable fields, use `->translatable()` in Filament forms
