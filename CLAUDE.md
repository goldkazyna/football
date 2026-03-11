# Football Doctors Tournament Platform

## Stack
- Laravel 11, PHP 8.3, MySQL 8
- Blade + Alpine.js + Tailwind CSS
- Queue: database | Storage: local
- Design: custom CSS (public/css/style.css) — dark theme, mobile-first

## Commands
- `php artisan serve` — dev server
- `npm run dev` — Vite
- `php artisan migrate:fresh --seed` — reset DB
- `php artisan test` — run tests

## Key Architecture
- Roles: superadmin, tournament_admin, captain, player
- Registration is 5-step flow, stored in session
- Whitelist check happens BEFORE any registration
- Subscription check via middleware CheckSubscription
- Match score is calculated from match_events (not stored directly)
- Standings are calculated on-the-fly via StandingsService

## Important Rules
- All validation messages must be in Russian
- Mobile-first: test every UI change at 375px
- Admin panel at /admin, protected by role:superadmin middleware
- Never expose documents folder to public directly
- Always use eager loading (with()) to avoid N+1
- Design CSS uses custom CSS variables, not Tailwind utilities

## Directory Structure
- Design HTML files: C:\projects\design\football\pages\
- Design CSS: public/css/style.css (copied from design)
- Verification documents stored in storage/app/verification_documents (not public)
