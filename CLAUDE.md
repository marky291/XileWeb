# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

XileWeb is a Laravel 11 web application for managing a Ragnarok Online private server. It includes features for player management, donations, War of Emperium (WoE) events, Discord bot integration, and an admin panel built with Filament PHP.

## Tech Stack

- **Backend**: Laravel 11 with PHP 8.1+
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Admin Panel**: Filament 3
- **Database**: Dual database setup (main Laravel DB + Ragnarok game DB)
- **Queue/Cache**: Redis with Laravel Horizon
- **Build Tools**: Vite for frontend assets

## Common Development Commands

### Development Server
```bash
# Start Vite dev server for frontend assets
npm run dev

# Run Laravel development server (if not using Laravel Herd)
php artisan serve
```

### Build & Deployment
```bash
# Build frontend assets for production
npm run build

# Clear all Laravel caches
php artisan optimize:clear

# Run database migrations
php artisan migrate

# Run queue workers (managed by Horizon)
php artisan horizon
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run specific test file
php artisan test tests/Unit/Ragnarok/LoginTest.php
```

### Code Quality
```bash
# Format PHP code with Laravel Pint
./vendor/bin/pint

# Run PHPUnit tests
./vendor/bin/phpunit
```

## Architecture Overview

### Dual Database Architecture
The application uses two database connections:
- **Main Database**: Standard Laravel tables (users, posts, patches, etc.)
- **Ragnarok Database**: Game server tables (login, char, guild, etc.)

Models extending `App\Ragnarok\RagnarokModel` automatically handle the game database connection, with special logic for unit testing environments.

### Key Directories

- `app/Ragnarok/`: Models and logic for Ragnarok game database entities
- `app/Filament/`: Admin panel resources and pages
- `app/Livewire/`: Interactive frontend components
- `app/Actions/`: Laravel Actions for business logic encapsulation
- `app/WoeEvents/`: War of Emperium event handling system
- `app/Discord/scripts/`: Python scripts for Discord bot functionality
- `app/Console/Commands/`: Artisan commands including Discord bot runners

### Discord Bot Integration
The application includes multiple Discord bots implemented in Python:
- Player count monitoring
- Server time display
- WoE event notifications
- Latest player updates
- Uber cost information

These are managed through Laravel commands (e.g., `RunDiscordPlayerCountBot`) that execute the Python scripts.

### War of Emperium (WoE) System
Complex event tracking system for guild battles:
- `GameWoeEvent` and `GameWoeScore` models track event data
- `ProcessWoeEventPoints` action calculates scoring
- `WoeEventScheduleJob` handles Discord notifications
- Configuration in `config/xilero.php`

### Admin Panel (Filament)
Two separate admin panels:
- `/admin`: Main admin panel (`AdminPanelProvider`)
- `/app`: User-facing app panel (`AppPanelProvider`)

Resources follow Filament conventions with:
- Resource classes defining model configurations
- Page classes for Create/Edit/List operations
- Relation managers for related data

## Important Configuration Files

- `config/xilero.php`: Game-specific settings (levels, rates, WoE configuration)
- `config/castles.php`: Castle configuration for WoE
- `config/donation.php`: Donation system settings
- `.env`: Environment-specific configuration (database credentials, API keys)

## Testing Approach

Tests use SQLite in-memory database for isolation. The `RagnarokModel` base class automatically handles connection switching during tests. Factory classes are provided for both main and Ragnarok database models.

## Key Business Logic

1. **User Registration**: Custom registration flow linking game accounts
2. **Donation System**: Uber currency system with configurable conversion rates
3. **WoE Events**: Automated scoring and Discord notifications
4. **Character Management**: Reset positions, inventory management
5. **Vending System**: In-game shop data display