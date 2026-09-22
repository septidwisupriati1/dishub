# Developer Quick Reference - Multi-Version Laravel Support

## Version Support Summary

| Version | Status | PHP | Support Until |
|---------|--------|-----|---|
| Laravel 10 | ✅ Supported | 8.1+ | Jan 2025 |
| Laravel 11 | ✅ Supported (LTS) | 8.2+ | Jan 2027 |
| Laravel 12 | ✅ Supported | 8.3+ | Q3 2025 |
| Laravel 13 | ✅ Supported | 8.3+ | Q3 2026 |

## Quick Commands

```bash
# Check current version
php artisan tinker
App\Helpers\LaravelVersionHelper::getVersion()

# Get detailed version info
App\Helpers\LaravelVersionHelper::getVersionInfo()

# Upgrade to next version
composer update
php artisan migrate
php artisan optimize:clear
php artisan test
```

## Using Version Helper in Code

### In Controllers/Models
```php
use App\Helpers\LaravelVersionHelper;

// Get version
$version = LaravelVersionHelper::getVersion();

// Check major version
if (LaravelVersionHelper::isLaravel13Plus()) {
    // Use Laravel 13+ features
}

// Check specific version
if (LaravelVersionHelper::is(11)) {
    // Use Laravel 11 specific code
}

// Get config for version
$timeout = LaravelVersionHelper::config('queue_timeout', 60);
```

### In Models with Trait
```php
use App\Traits\VersionCompatible;

class Vehicle extends Model {
    use VersionCompatible;

    public function process() {
        return $this->ifLaravel12Plus(function() {
            return $this->processV12();
        }) ?? $this->processLegacy();
    }
}
```

## Version-Specific Features

### Migration Between Versions
```bash
# Before: Update composer.json
# "laravel/framework": "^11.0"

composer update
php artisan migrate
php artisan cache:clear
php artisan optimize:clear
```

## File Structure Added

```
app/
  ├── Helpers/
  │   └── LaravelVersionHelper.php      # Version checking utilities
  ├── Traits/
  │   └── VersionCompatible.php         # Use in models for version checks
  
config/
  └── version.php                        # Version-specific configuration

tests/Feature/
  └── LaravelCompatibilityTest.php      # Tests for version compatibility

# Documentation
├── LARAVEL_COMPATIBILITY.md            # Full compatibility guide
└── VERSION_MIGRATION_GUIDE.md           # Step-by-step upgrade guide
```

## Configuration

### Version-Specific Config
```php
// In config/version.php
'v13' => [
    'queue_timeout' => 60,
    'cache_ttl' => 3600,
    // Add version-specific settings
]
```

### Access Version Config
```php
$timeout = LaravelVersionHelper::config('queue_timeout');
// Or with fallback
$timeout = LaravelVersionHelper::config('queue_timeout', 120);
```

## Testing Compatibility

```bash
# Run all tests
php artisan test

# Run compatibility tests only
php artisan test tests/Feature/LaravelCompatibilityTest.php

# Run with coverage
php artisan test --coverage

# Debug failed tests
php artisan test --verbose
```

## API Response Headers

Version information is logged, not explicitly returned in API headers. Access version via:

```php
// In any API response
$response->header('X-Laravel-Version', LaravelVersionHelper::getVersion());
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Class not found | `composer dump-autoload && php artisan optimize:clear` |
| Version not detected | Restart PHP service / clear cache |
| Middleware issues | Verify `app/Http/Kernel.php` - unchanged across versions |
| Sanctum auth fails | `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"` |
| Database migration error | `php artisan migrate:status` then `php artisan migrate:reset && php artisan migrate` |

## Checking Version in Different Ways

```php
// Method 1: Using helper
use App\Helpers\LaravelVersionHelper;
LaravelVersionHelper::getVersion();

// Method 2: Using Laravel facade
\Illuminate\Foundation\Application::VERSION;

// Method 3: Using app() helper
app()->version();

// Method 4: In Tinker
php artisan tinker
> app()->version()
> \Illuminate\Foundation\Application::VERSION
```

## Key Compatibility Notes

✅ **These work across all versions:**
- Service Providers (unchanged)
- Middleware system (unchanged)
- Eloquent ORM (unchanged)
- API routing (unchanged)
- Authorization/Gates (unchanged)
- Database migrations (unchanged)
- Sanctum authentication (with version updates)

⚠️ **Monitor for changes:**
- New features in Laravel 12/13
- Deprecation warnings during development
- Third-party package compatibility

## Environment Variables

Keep these consistent across versions:
```env
APP_NAME="KIR Antrean"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kir_antrean

SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

## Useful Links

- [Laravel Docs](https://laravel.com/docs)
- [Upgrade Guides](https://laravel.com/docs/releases)
- [Laravel News](https://laravel-news.com)
- [Package Versioning](https://getcomposer.org/doc/articles/versions.md)

## Common Workflows

### Checking Version Before Feature Use
```php
if (LaravelVersionHelper::getMajorVersion() >= 12) {
    // Use new feature
    $data = Model::lazy();
} else {
    // Use traditional approach
    $data = Model::cursor();
}
```

### Conditional Migration
```php
// In migration
Schema::table('users', function (Blueprint $table) {
    // Works in all versions
    if (!Schema::hasColumn('users', 'phone')) {
        $table->string('phone')->nullable();
    }
});
```

### Version-Aware Response
```php
return response()->json([
    'data' => $data,
    'meta' => [
        'laravel_version' => LaravelVersionHelper::getVersion(),
        'total' => count($data),
    ]
]);
```

## Performance Notes

- **Laravel 11+**: ~5-10% faster due to optimizations
- **Laravel 12+**: Additional improvements expected
- Use `php artisan optimize` in production for all versions
- Cache versions via `php artisan config:cache`

## Deployment Checklist

Before deploying to new Laravel version:
- [ ] Test locally with new version
- [ ] Run `php artisan test`
- [ ] Check `php artisan migrate:status`
- [ ] Verify `composer.lock` is updated
- [ ] Run migrations on staging
- [ ] Clear all caches: `php artisan optimize:clear`
- [ ] Monitor logs for errors: `tail -f storage/logs/laravel.log`

---
**Last Updated:** May 25, 2026  
**Current Version:** Laravel 10.10+ (compatible with 11, 12, 13)
