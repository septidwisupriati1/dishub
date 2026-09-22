# Multi-Version Laravel Setup Guide

## Overview

This application is designed to work with multiple Laravel versions (10, 11, 12, and 13). It uses a compatibility layer to ensure seamless operation across different framework versions.

## Initial Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd kir-antrean
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# If you need to install for a specific Laravel version:
# Edit composer.json to specify desired version first
# "laravel/framework": "^11.0"
composer install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update .env with your database credentials
nano .env
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Check Laravel Version
```bash
# Run the version checker command
php artisan version:check

# Or in Tinker
php artisan tinker
App\Helpers\LaravelVersionHelper::getVersion()
```

### 6. Start Development Server
```bash
# Start Laravel dev server
php artisan serve

# In another terminal, start Vite for assets
npm run dev
```

## Version Detection

### Automatic Detection
The application automatically detects your Laravel version:

```bash
php artisan version:check
php artisan version:check --verbose
```

### Programmatic Access
```php
use App\Helpers\LaravelVersionHelper;

echo LaravelVersionHelper::getVersion();
echo LaravelVersionHelper::getMajorVersion();
```

## Working with Different Laravel Versions

### Laravel 10
- **PHP Requirement**: 8.1+
- **Status**: Supported until January 2025
- **Status Code**: Full backward compatibility

```bash
composer require "laravel/framework:^10.10"
composer install
php artisan migrate
php artisan test
```

### Laravel 11 (Recommended)
- **PHP Requirement**: 8.2+
- **Status**: LTS (Long Term Support) until January 2027
- **Status Code**: Recommended for new projects

```bash
composer require "laravel/framework:^11.0"
composer install
php artisan migrate
php artisan test
```

### Laravel 12 (Future)
- **PHP Requirement**: 8.3+
- **Status**: Supported
- **Status Code**: Full compatibility

```bash
composer require "laravel/framework:^12.0"
composer install
php artisan migrate
php artisan test
```

### Laravel 13 (Future)
- **PHP Requirement**: 8.3+
- **Status**: Supported
- **Status Code**: Full compatibility

```bash
composer require "laravel/framework:^13.0"
composer install
php artisan migrate
php artisan test
```

## File Structure

```
kir-antrean/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckVersionCompatibility.php    ← Version checker command
│   ├── Helpers/
│   │   └── LaravelVersionHelper.php             ← Version utilities
│   ├── Traits/
│   │   └── VersionCompatible.php                ← Version checks in models
│   ├── Http/
│   ├── Models/
│   └── Providers/
│
├── config/
│   └── version.php                              ← Version-specific config
│
├── tests/
│   └── Feature/
│       └── LaravelCompatibilityTest.php         ← Version tests
│
├── LARAVEL_COMPATIBILITY.md                     ← Full compatibility guide
├── VERSION_MIGRATION_GUIDE.md                   ← Upgrade guide
├── DEVELOPER_QUICK_REFERENCE.md                 ← Developer reference
├── composer.json                                ← Updated for multi-version
└── [other standard Laravel files]
```

## Key Features

### Version Helper
```php
use App\Helpers\LaravelVersionHelper;

// Get version
$version = LaravelVersionHelper::getVersion();

// Check version
if (LaravelVersionHelper::isLaravel11Plus()) {
    // Use Laravel 11+ features
}

// Get version-specific config
$timeout = LaravelVersionHelper::config('queue_timeout', 60);
```

### Version Trait for Models
```php
use App\Traits\VersionCompatible;

class MyModel extends Model {
    use VersionCompatible;

    public function process() {
        return $this->ifLaravel13Plus(function() {
            // Laravel 13+ specific code
        });
    }
}
```

### Artisan Command
```bash
# Check version and compatibility
php artisan version:check

# Verbose output with system info
php artisan version:check --verbose
```

## Configuration

### Version-Specific Settings
Edit `config/version.php` to add version-specific configurations:

```php
'v13' => [
    'queue_timeout' => 60,
    'cache_ttl' => 3600,
    // Add your version-specific settings
]
```

### Access in Code
```php
use App\Helpers\LaravelVersionHelper;

$timeout = LaravelVersionHelper::config('queue_timeout', 60);
```

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Compatibility Tests Only
```bash
php artisan test tests/Feature/LaravelCompatibilityTest.php
```

### Test Specific Feature
```bash
php artisan test tests/Feature/AuthControllerTest.php
```

### With Coverage
```bash
php artisan test --coverage
```

## Upgrading Laravel Version

### Full Upgrade Process
See [VERSION_MIGRATION_GUIDE.md](VERSION_MIGRATION_GUIDE.md) for detailed steps.

### Quick Upgrade
```bash
# 1. Update composer.json
# Edit and change: "laravel/framework": "^11.0"

# 2. Update dependencies
composer update

# 3. Run migrations
php artisan migrate

# 4. Clear caches
php artisan optimize:clear

# 5. Test
php artisan test

# 6. Check version
php artisan version:check
```

## Common Development Tasks

### Database Management
```bash
# Create migration
php artisan make:migration create_users_table

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database
php artisan migrate:refresh

# Seed database
php artisan db:seed
```

### API Development
```bash
# Make controller
php artisan make:controller Api/UserController

# Make model with migration and controller
php artisan make:model User -mc

# Make request class
php artisan make:request StoreUserRequest
```

### Testing
```bash
# Make test
php artisan make:test Feature/UserTest

# Run single test file
php artisan test tests/Feature/UserTest.php

# Run with verbose output
php artisan test --verbose
```

## Troubleshooting

### Issue: "Class not found" errors
```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue: Version not detected correctly
```bash
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

### Issue: Migration fails
```bash
php artisan migrate:status
php artisan migrate:reset
php artisan migrate
```

### Issue: Sanctum authentication not working
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
php artisan migrate
php artisan cache:clear
```

## Documentation

- **[LARAVEL_COMPATIBILITY.md](LARAVEL_COMPATIBILITY.md)** - Comprehensive compatibility guide
- **[VERSION_MIGRATION_GUIDE.md](VERSION_MIGRATION_GUIDE.md)** - Step-by-step upgrade instructions
- **[DEVELOPER_QUICK_REFERENCE.md](DEVELOPER_QUICK_REFERENCE.md)** - Quick reference for developers
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - API endpoint documentation

## Performance Optimization

### Enable Production Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Optimize loader
composer install --optimize-autoloader --no-dev
```

### Monitor Performance
```bash
# Check logs
tail -f storage/logs/laravel.log

# In Tinker
php artisan tinker
> \Illuminate\Support\Facades\Log::info('test')
```

## Version Support Timeline

| Version | PHP | Release | Support Until | Status |
|---------|-----|---------|---|---------|
| Laravel 10 | 8.1+ | Feb 7, 2023 | Jan 2025 | ✅ Supported |
| Laravel 11 | 8.2+ | Mar 12, 2024 | Jan 2027 | ✅ LTS |
| Laravel 12 | 8.3+ | Sep 3, 2024 | Q3 2025 | ✅ Supported |
| Laravel 13 | 8.3+ | Mar 12, 2025 | Q3 2026 | ✅ Supported |

## Getting Help

1. **Check Documentation**
   - Read `LARAVEL_COMPATIBILITY.md`
   - Review `DEVELOPER_QUICK_REFERENCE.md`

2. **Check Version**
   ```bash
   php artisan version:check --verbose
   ```

3. **Review Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Run Tests**
   ```bash
   php artisan test --verbose
   ```

5. **Laravel Documentation**
   - [Laravel Docs](https://laravel.com/docs)
   - [Upgrade Guides](https://laravel.com/docs/releases)

## Best Practices

✅ **Do:**
- Keep dependencies updated
- Test after version upgrades
- Use the version helper in version-specific code
- Check compatibility before using new features
- Run tests regularly

❌ **Don't:**
- Hardcode Laravel version checks
- Ignore deprecation warnings
- Skip database backups before upgrades
- Use version-specific hacks when not necessary
- Forget to test after changes

## Next Steps

1. Read [LARAVEL_COMPATIBILITY.md](LARAVEL_COMPATIBILITY.md) for full details
2. Run `php artisan version:check` to verify your setup
3. Review [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for API endpoints
4. Start development!

---
**Last Updated**: May 25, 2026  
**Current Status**: Compatible with Laravel 10, 11, 12, and 13
